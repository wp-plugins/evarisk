<?php
/**
*	The different utilities to manage roles in evarisk
*
*	@package 		Evarisk
*	@subpackage Roles
* @author			Evarisk team <contact@evarisk.com>
*/

class evaRole
{

	/**
	*	Get the list of role defined for evarisk
	*
	*	@return array $resultList The list of role
	*/
	function getList($id = null)
	{
		global $wpdb;
		$resultList = array();
		$whatToSelect = "";

		$query = 
			"SELECT ROLE.* " . $whatToSelect . "
			FROM " . TABLE_EVA_ROLES . " AS ROLE
			WHERE ROLE.eva_role_status != 'deleted' ";
		if($id != null)
		{
			$query .= " AND eva_role_id = '%d' ";
		}
		$query = $wpdb->prepare($query,$id);

		/*	Execute the query	*/
		$resultList = $wpdb->get_results($query);

		return $resultList;
	}


	/**
	* Output a row in the main grid
	*/
	function RowOutput()
	{
		global $evariskCapability;
		$rowToOutput = $this->getList();

		$i=0;
		foreach ($rowToOutput as $rowInformations )
		{
?>
	<tr id="ut-<?php echo $rowInformations->eva_role_id . '"'; if(($i%2) == 0) {echo ' class="alternate"';} ?> valign="top">
		<th class="check-column" scope="row">
			<!--<input type="checkbox" value="<?php echo $rowInformations->eva_role_id; ?>" name="attribute[]"/>-->
		</th> 
		<td><strong><a onclick="javascript:$('#act').val('mod');$('#id').val('<?php echo $rowInformations->eva_role_id; ?>');$('#evaRoleForm').submit();" style="cursor:pointer;" ><?php echo stripcslashes($rowInformations->eva_role_name); ?></a></strong></td>
		<td><strong><?php echo $rowInformations->eva_role_description ?></strong></td>
		<td><?php echo str_replace(',',', ',str_replace('_',' ',$rowInformations->eva_role_capabilities)); ?></td>
	</tr>
<?php
			$i++;
		}

		if($i <= 0)
		{
?>
	<tr id="ut-0" valign="top" >
		<th colspan="4" class="check-column" style="text-align:center;" scope="row"><?php echo __('Aucun r&eacute;sultat','evarisk'); ?></th> 
	</tr>
<?php
		}

		return null;
	}

	/**
	*	Prepare the different field before use them in the query
	*
	*	@param array $prm An array containing the fields to prepare
	*	@param mixed $operation The type of query we are preparing the vars for
	*
	*	@return mixed $preparedFields The fields ready to be injected in the query
	*/
	function prepareQuery($prm, $operation = 'creation')
	{
		$preparedFields = array();

		foreach($prm as $field => $value)
		{
			if($field != 'id')
			{
				if($operation == 'creation')
				{
					$preparedFields['fields'][] = $field;
					$preparedFields['values'][] = "'" . mysql_real_escape_string($value) . "'";
				}
				elseif($operation == 'update')
				{
					$preparedFields['values'][] = $field . " = '" . mysql_real_escape_string($value) . "'";
				}
			}
		}

		return $preparedFields;
	}

	/**
	*	Create a role
	*
	*	@param array $prm An array containing the different information needed for the role
	*
	*	@return array $status An array containing the result of the method
	*/
	function create($prm)
	{
		global $wpdb;
		$status = array();

		$preparedFields = $this->prepareQuery($prm['evaRole'],'creation');

		if( !isset($prm['evaRole']['eva_role_name']) || (trim($prm['evaRole']['eva_role_name']) == '') )
		{
			$status['result'] = 'error'; 
			$status['errors']['mandatory_field'] = __('Le champs Nom est obligatoire');
		}
		else
		{
			$query = 
				$wpdb->prepare("INSERT INTO " . TABLE_EVA_ROLES . "
					(" . implode(', ', $preparedFields['fields']) . ")
				VALUES
					(" . implode(', ', $preparedFields['values']) . ")");
			if($wpdb->query($query))
			{
				$status['result'] = 'ok';
				$status['id'] = $wpdb->insert_id;

				/*	Create the new role with the good name	*/
				add_role($prm['evaRole']['eva_role_label'],$prm['evaRole']['eva_role_name']);

				$this->addCapabilitiesToRole($status['id'], $prm['evaRole']['eva_role_capabilities']);
			}
			else
			{
				$status['result'] = 'error'; 
				$status['errors']['query_error'] = __('Une erreur est survenue lors de l\'enregistrement', 'evarisk');
			}
		}

		return $status;
	}

	/**
	*	Update a role
	*
	*	@param array $prm An array containing the different information needed for the role
	*
	*	@return array $status An array containing the result of the method
	*/
	function update($prm)
	{
		global $wpdb;
		$status = array();

		$preparedFields = $this->prepareQuery($prm['evaRole'],'update');

		if( !isset($prm['evaRole']['eva_role_name']) || (trim($prm['evaRole']['eva_role_name']) == '') )
		{
			$status['result'] = 'error';
			$status['errors']['mandatory_field'] = __('Le champs Nom est obligatoire', 'evarisk');
		}
		else
		{
			$query = 
				$wpdb->prepare(
				"UPDATE " . TABLE_EVA_ROLES . " 
				SET " . implode(', ', $preparedFields['values']) . " 
				WHERE eva_role_id = '%s' ",
				$prm['evaRole']['id']);
			$wpdb->query($query);

			/*	Make sure that the role name will be updated in user interface, so we delete the role before any operation	*/
			// remove_role($fieldForAttributes['evaRole']['eva_role_label']);

			$this->addCapabilitiesToRole($prm['evaRole']['id'], $prm['evaRole']['eva_role_capabilities']);

			$status['result'] = 'ok';
			$status['id'] = $prm['evaRole']['id'];
		}

		return $status;
	}

	/**
	*	Add capabilities to a group
	*
	*	@param integer $roleId The group id we want to get the role name to assign
	*	@param array $roleCapabilities THe list of capabilities we want to affect to the group
	*/
	function addCapabilitiesToRole($roleId, $roleCapabilities)
	{
		$role = get_role($this->getRoleName($roleId));
		$roleCapabilities = explode(',', $roleCapabilities);
		if(($role !== NULL) && is_array($roleCapabilities))
		{
			$evariskCapabilities = getDroitEvarisk();
			foreach($evariskCapabilities as $capability => $capabilityName)
			{
				if($role->has_cap($capability))
				{
					$role->remove_cap($capability);
				}
			}

			foreach($roleCapabilities as $key => $capability)
			{
				if(!$role->has_cap($capability))
				{
					$role->add_cap($capability);
				}
			}
		}
	}

	/**
	*	Manage the different roles affected to users affected to a group
	*
	*	@param integer $userId The user identifier we want to change the role
	*	@param mixed $roleName The role name
	*	@param mixed $action The action we want to do.
	*/
	function manageRole($userId, $roleName, $action = 'add')
	{
		global $wpdb;
		$userNewRole = '';

		$query = 
			$wpdb->prepare("SELECT meta_value
			FROM " . $wpdb->prefix . "usermeta
			WHERE user_id = '%s'
				AND meta_key = 'wp_capabilities' ",
				$userId);
		$userRole = $wpdb->get_col($query);
		$userActualRole = unserialize($userRole[0]);

		if($action == 'add')
		{
			if(!is_array($userActualRole) || ( is_array($userActualRole) && !array_key_exists($roleName, $userActualRole)))
			{
				$userActualRole[$roleName] = 1;
			}
		}
		elseif($action == 'remove')
		{
			if(is_array($userActualRole))
			{
				foreach($userActualRole as $role => $value)
				{
					if($role == $roleName)
					{
						unset($userActualRole[$role]);
					}
				}
			}
		}

		$userNewRole = serialize($userActualRole);

		if($userNewRole != '')
		{
			$query = 
				$wpdb->prepare("UPDATE " . $wpdb->prefix . "usermeta 
					SET meta_value = '%s' 
					WHERE user_id = '%s'
					AND meta_key = 'wp_capabilities' ",
					$userNewRole, $userId);
			$wpdb->query($query);
		}
	}

	/**
	*	Get the role affected to a group
	*
	*	@param integer $roleId The role we want to get the name
	*
	*	@return mixed $role The role affected to the group
	*/
	function getRoleName($roleId)
	{
		global $wpdb;
		$role = '';

		$query = 
			$wpdb->prepare(
			"SELECT eva_role_label 
			FROM " . TABLE_EVA_ROLES . "
			WHERE eva_role_id = '%s' ",
			$roleId);
		$roleResult = $wpdb->get_col($query);

		$role = $roleResult[0];

		return $role;
	}

	/**
	*	Remove a role from wordpress, and update all the user with this role
	*
	*	@param mixed $roleName The role name we have to destroy
	*/
	function removeRole($roleName)
	{
		global $wpdb;
		include_once(EVA_LIB_PLUGIN_DIR . 'users/evaUsergroup.class.php' );
		$evaUserGroup = new evaUserGroup();

		/*	Remove the role from wordpress	*/
		remove_role($roleName);

		/*	Get the different groups and users having the role we want to delete	*/
		$groupHavingThisRole = $evaUserGroup->getGroupBelongingRole($roleName);
		foreach($groupHavingThisRole as $key => $group)
		{
			/*	Get the user list affected to a group	*/
			$userList[] = $evaUserGroup->getUserInGroup($group->user_group_id);

			/*	Delete the relation between groups and the role we are deleting	*/
			$query = $wpdb->prepare("DELETE FROM " . TABLE_EVA_USER_GROUP_ROLES_DETAILS . " WHERE eva_role_label = '%s' ",$roleName);
			$wpdb->query($query);
		}
		foreach($userList as $groupKey => $groupContent)
		{
			foreach($groupContent as $key => $user)
			{
				$this->manageRole($user->user_id, $roleName, 'remove');
			}
		}
	}

}