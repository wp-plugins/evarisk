<?php
/**
*	The different utilities to manage users' group in evarisk
*
*	@package 		Evarisk
*	@subpackage Users' group
* @author			Evarisk team <contact@evarisk.com>
*/

class evaUserGroup
{

	/**
	*	Get the different groups
	*
	*	@param integer $groupId The identifier for the group we want to have information about (if we are on a group page and not on the main list)
	*
	*	@return array $resultList The list of existing groups in database
	*/
	function getUserGroup($groupId = null)
	{
		global $wpdb;
		$resultList = array();

		/*	Build the query regarding to the different parameters	*/		
		$whatToSelect = ", COUNT( DISTINCT(GRPDETAILS.user_id) ) AS TOTALUSERNUMBER";
		if($groupId != null)
		{
			$whatToSelect .= ", GROUP_CONCAT( 'user', GRPDETAILS.user_id , ',' SEPARATOR '') AS ELEMENT";
		}
		$query = 
			"SELECT GRP.* " . $whatToSelect . "
			FROM " . TABLE_EVA_USER_GROUP . " AS GRP
				LEFT JOIN " . TABLE_EVA_USER_GROUP_DETAILS . " AS GRPDETAILS ON (GRPDETAILS.user_group_id = GRP.user_group_id)
			WHERE GRP.user_group_status != 'deleted' ";
		if($groupId != null)
		{
			$query = $wpdb->prepare($query . " AND GRP.user_group_id = '%s' ", $groupId);
		}
		$query .= "GROUP BY GRP.user_group_id";

		/*	Execute the query	*/
		$resultList = $wpdb->get_results($query);

		return $resultList;
	}


	/**
	* Output a row in the main grid
	*/
	function RowOutput()
	{
		$rowToOutput = $this->getUserGroup();

		$i=0;
		foreach ($rowToOutput as $rowInformations ) 
		{
?>
	<tr id="ut-<?php echo $rowInformations->user_group_id . '"'; if(($i%2) == 0) {echo ' class="alternate"';} ?> valign="top">
		<th class="check-column" scope="row">
			<!--<input type="checkbox" value="<?php echo $rowInformations->user_group_id; ?>" name="attribute[]"/>-->
		</th> 
		<td><strong><a onclick="javascript:$('#act').val('mod');$('#id').val('<?php echo $rowInformations->user_group_id; ?>');$('#evaUserGroupManagementForm').submit();" style="cursor:pointer;" ><?php echo stripcslashes($rowInformations->user_group_name); ?></a></strong></td>
		<td><strong><?php echo $rowInformations->user_group_description ?></strong></td>
		<td><strong><?php echo $rowInformations->TOTALUSERNUMBER ?></strong></td>
	</tr>
<?php
			$i++;
		}

		if($i <= 0)
		{
?>
	<tr id="ut-<?php echo $rowInformations->user_group_id . '"'; if(($i%2) == 0) {echo ' class="alternate"';} ?> valign="top" >
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
	*	Create an user group
	*
	*	@param array $prm An array containing the different information needed for the group
	*
	*	@return array $status An array containing the result of the method
	*/
	function createUserGroup($prm)
	{
		global $wpdb;
		$status = array();

		$preparedFields = $this->prepareQuery($prm['evaUserGroup'],'creation');

		if( !isset($prm['evaUserGroup']['user_group_name']) || (trim($prm['evaUserGroup']['user_group_name']) == '') )
		{
			$status['result'] = 'error'; 
			$status['errors']['mandatory_field'] = __('Le champs Nom est obligatoire');
		}
		else
		{
			$query = 
				$wpdb->prepare("INSERT INTO " . TABLE_EVA_USER_GROUP . "
					(" . implode(', ', $preparedFields['fields']) . ")
				VALUES
					(" . implode(', ', $preparedFields['values']) . ")");
			if($wpdb->query($query))
			{
				$status['result'] = 'ok';
				$status['id'] = $wpdb->insert_id;

				$this->addUserToGroup($status['id'], $_POST['groupUserList']);

				$this->affectRoleToGroup($status['id'], $prm['evaUserGroupRole']['groupRole']);
			}
			else
			{
				$status['result'] = 'error'; 
				$status['errors']['query_error'] = __('Une erreur est survenue lors de l\'enregistrement ', 'evarisk');
			}
		}

		return $status;
	}

	/**
	*	Update an user group
	*
	*	@param array $prm An array containing the different information needed for the group
	*
	*	@return array $status An array containing the result of the method
	*/
	function updateUsergroup($prm)
	{
		global $wpdb;
		$status = array();

		$preparedFields = $this->prepareQuery($prm['evaUserGroup'],'update');

		if( !isset($prm['evaUserGroup']['user_group_name']) || (trim($prm['evaUserGroup']['user_group_name']) == '') )
		{
			$status['result'] = 'error';
			$status['errors']['mandatory_field'] = __('Le champs Nom est obligatoire');
		}
		else
		{
			$query = 
				$wpdb->prepare(
				"UPDATE " . TABLE_EVA_USER_GROUP . " 
				SET " . implode(', ', $preparedFields['values']) . " 
				WHERE user_group_id = '%s' ",
				$prm['evaUserGroup']['id']);
			$wpdb->query($query);

			$status['result'] = 'ok';
			$status['id'] = $prm['evaUserGroup']['id'];

			$this->addUserToGroup($status['id'], $_POST['groupUserList']);

			$this->affectRoleToGroup($status['id'], $prm['evaUserGroupRole']['groupRole']);
		}

		return $status;
	}

	/**
	*	Add some user to a group
	*
	*	@param integer $groupId The group identifier we want to add the user to
	*	@param array $newUserList An array with the user list we want to add to the group
	*
	*	@return array $result An array containing the result of the query. With the errors' message in case of errors append
	*/
	function addUserToGroup($groupId, $newUserList)
	{
		global $wpdb;
		$list = "  ";
		$result = true;

		$userToAdd = explode(',', $newUserList);

		$oldUserList = $this->getUserInGroup($groupId);
		foreach($oldUserList as $key => $user)
		{
			$user->user_id = 'user' . $user->user_id;
			if(!in_array($user->user_id, $userToAdd))
			{
				include_once(EVA_LIB_PLUGIN_DIR . 'evaRole/evaRole.class.php' );
				$evaRole = new evaRole();

				$user->user_id = str_replace('user', '', $user->user_id);

				$roleListToDelete = $this->getRoleAffectedToGroup($groupId);
				if(is_array($roleListToDelete))
				{
					foreach($roleListToDelete as $key => $roleName)
					{
						$evaRole->manageRole($user->user_id, $roleName, 'remove');
					}
				}
			}
		}

		foreach($userToAdd as $key => $userId)
		{
			if($userId != '')
			{
				$userGoodId = str_replace('user', '', $userId);
				$list .= $wpdb->prepare("('" . $groupId . "', '" . $userGoodId . "'), ");
			}
		}
		$list = substr($list, 0, -2);

		/*	First we clean the group content by deleting all the members of this group	*/
		$query = 
			$wpdb->prepare(
				"DELETE FROM " . TABLE_EVA_USER_GROUP_DETAILS . " 
				WHERE user_group_id = '%s' ", 
				$groupId
			);
		$wpdb->query($query);

		/*	Add the choosen users to the group	*/
		if( trim($list) != "" )
		{
			$query = 
				"REPLACE INTO " . TABLE_EVA_USER_GROUP_DETAILS . "
					(user_group_id, user_id)
				VALUES
					" . $list;
			if(!$wpdb->query($query))
			{
				$result = false;
			}
		}

		return $result;
	}

	/**
	*	Get the list of users from a group
	*
	*	@param integer $groupId The group identifier we want to get the users' list
	*
	*	@return array $userList The different information about users that are in the group
	*/
	function getUserInGroup($groupId)
	{
		global $wpdb;
		$userList = array();

		$query = $wpdb->prepare(
		"SELECT GRPDETAILS.user_id
			FROM " . TABLE_EVA_USER_GROUP . " AS GRP
				LEFT JOIN " . TABLE_EVA_USER_GROUP_DETAILS . " AS GRPDETAILS ON (GRPDETAILS.user_group_id = GRP.user_group_id)
			WHERE GRP.user_group_status != 'deleted' 
				AND GRP.user_group_id = '%s' ",
		$groupId);

		/*	Execute the query	*/
		$userList = $wpdb->get_results($query);

		return $userList;
	}


	/**
	*	Do the link between the different role affected to a group and the users. check if a role must be added or removed for a given user
	*
	*	@param integer $groupId The identifier of the group
	*	@param array $roleList An array with the role we have to affect to a group
	*/
	function affectRoleToGroup($groupId, $roleList)
	{
		global $wpdb;

		include_once(EVA_LIB_PLUGIN_DIR . 'evaRole/evaRole.class.php' );
		$evaRole = new evaRole();

		$roleToDelete = array();

		$oldRole = $this->getRoleAffectedToGroup($groupId);

		/*	Clean the group role list by deleting	*/
		$query = $wpdb->prepare("DELETE FROM " . TABLE_EVA_USER_GROUP_ROLES_DETAILS . " WHERE user_group_id = '%s' ", $groupId);
		$wpdb->query($query);

		/*	Complete the list with the different role affected to this group	*/
		if(is_array($roleList))
		{
			/*	Start the role affectation to a group	*/
			$list = "  ";
			foreach($roleList as $key => $roleName)
			{
				$list .= $wpdb->prepare("('%d', '%s'), ", $groupId, $roleName);

				$userList = $this->getUserInGroup($groupId);
				foreach($userList as $key => $user)
				{
					$evaRole->manageRole($user->user_id, $roleName, 'add');
				}
			}
			/*	Reaffect the role to the group	*/
			$list = substr($list, 0, -2);
			if($list != "")
			{
				$query = 	
					"REPLACE INTO " . TABLE_EVA_USER_GROUP_ROLES_DETAILS . "
						(user_group_id, eva_role_label)
					VALUES 
					" . $list;
				$wpdb->query($query);
			}
		}

		if(is_array($oldRole))
		{
			/*	Start the role affectation to a group	*/
			foreach($oldRole as $key => $roleName)
			{
				if(!is_array($roleList) || !in_array($roleName,$roleList))
				{
					$roleToDelete[] = $roleName;
				}
			}

			/*	We check all the role affected to the other group to delete the good one to the user	*/
			foreach($roleToDelete as $key => $roleNameToDelete)
			{
				$groupWithThoseRole = $this->getGroupBelongingRole($roleNameToDelete, $groupId);

				foreach($groupWithThoseRole as $key => $groupIdToNotDelete)
				{
					$tmpUserListToSave = $this->getUserInGroup($groupIdToNotDelete->user_group_id);
				}

				$userListToSave = array();
				if(is_array($tmpUserListToSave))
				{
					foreach($tmpUserListToSave as $key => $user)
					{
						$userListToSave[] = $user->user_id;
					}
				}

				$userList = $this->getUserInGroup($groupId);
				foreach($userList as $key => $user)
				{
					if(!in_array($user->user_id, $userListToSave))
					{
						$evaRole->manageRole($user->user_id, $roleNameToDelete, 'remove');
					}
				}
			}
		}
	}

	/**
	*	Retrieve the role list affected to a group
	*
	*	@param intger $groupId The identifier of the group we want to get the role list
	*
	*	@return array $affectedRole An array with the different role already affected to a group
	*/
	function getRoleAffectedToGroup($groupId)
	{
		global $wpdb;
		$affectedRole = '';

		$query = 
			$wpdb->prepare("SELECT eva_role_label
			FROM " . TABLE_EVA_USER_GROUP_ROLES_DETAILS . "
			WHERE user_group_id = '%d' ", $groupId);
		$affectedRoleObject = $wpdb->get_results($query);

		foreach($affectedRoleObject as $key => $role)
		{
			foreach($role as $roleKey => $infos)
			{
				$affectedRole[] = $infos;
			}
		}

		return $affectedRole;
	}

	/**
	*	Get the list of group that have a given role
	*
	*	@param mixed $roleName The role we want to get the group list
	*	@param integer $groupId The identifier of the group we want to exclude from the list
	*
	*	@return array $groupList An object with the different group that have a given role, exluding the group we are working on
	*/
	function getGroupBelongingRole($roleName, $groupId = null)
	{
		global $wpdb;
		$groupList = '';

		$query = 
			"SELECT user_group_id
			FROM " . TABLE_EVA_USER_GROUP_ROLES_DETAILS . "
			WHERE eva_role_label = '%s'";
		if($groupId != null)
		{
			$query .= " AND user_group_id != '%d' ";
		}
			
		$query = $wpdb->prepare($query, $roleName, $groupId);
		$groupList = $wpdb->get_results($query);

		return $groupList;
	}

	/**
	*	Save the new bind between a users group and an element
	*
	*	@param integer $groupId The identifier of the group we want to bind
	*	@param integer $elementId The identifier of the element (in its table) we want to bind
	*	@param string $elementTable The table of the element we want to bind
	*
	*	@return array $status An array containing the result of the method
	*/
	function saveBind($groupId, $elementId, $elementTable)
	{
		global $wpdb;
		$status = array();

		$query = 
			$wpdb->prepare("REPLACE INTO " . TABLE_LIAISON_USER_GROUPS . " 
				(id_group, table_element, id_element, date)
			VALUES 
				(%d, '%s', %d, '" . date('Y-m-d H:i:s') . "')", $groupId, $elementTable, $elementId);
		if($wpdb->query($query))
		{
			$status['result'] = 'ok';
		}
		else
		{
			$status['result'] = 'error'; 
			$status['errors']['query_error'] = __('Une erreur est survenue lors de l\'enregistrement', 'evarisk');
		}

		return $status;
	}

	/**
	*	Delete a bind between a users group and an element
	*
	*	@param integer $groupId The identifier of the group we want to unbind
	*	@param integer $elementId The identifier of the element (in its table) we want to unbind
	*	@param string $elementTable The table of the element we want to unbind
	*
	*	@return array $status An array containing the result of the method
	*/
	function deleteBind($groupId, $elementId, $elementTable)
	{
		global $wpdb;
		$status = array();

		$query = 
			$wpdb->prepare("UPDATE " . TABLE_LIAISON_USER_GROUPS . " 
				SET Status = 'Deleted' 
				WHERE id_group = '%d'
					AND table_element = '%s'
					AND id_element = '%d'", $groupId, $elementTable, $elementId);
		if($wpdb->query($query))
		{
			$status['result'] = 'ok';
		}
		else
		{
			$status['result'] = 'error'; 
			$status['errors']['query_error'] = __('Une erreur est survenue lors de l\'enregistrement', 'evarisk');
		}

		return $status;
	}

	/**
	*	Get the identifier of the groups bind with an element
	*
	*	@param integer $elementId The identifier of the element (in its table) we want to bind
	*	@param string $elementTable The table of the element we want to bind
	*
	*	@return array An array containing the groups identifiers
	*/
	function getBindGroups($elementId, $elementTable)
	{
		global $wpdb;
		
		$elementId = mysql_real_escape_string($elementId);
		$elementTable = mysql_real_escape_string($elementTable);
		
		$queryCleanGroupBind = $wpdb->prepare(
		"SELECT id_group 
		FROM " . TABLE_LIAISON_USER_GROUPS . " 
		WHERE table_element = '%s' 
			AND id_element = %d 
			AND Status='Valid'", $elementTable, $elementId);
		
		return $wpdb->get_results($queryCleanGroupBind);
	}

	/**
	*	Get informations about the different group binded to an element
	*
	*	@param integer $elementId The identifier of the element (in its table) we want to bind
	*	@param string $elementTable The table of the element we want to bind
	*
	*	@return array $groups An array containing the groups informations
	*/
	function getBindGroupsWithInformations($elementId, $elementTable)
	{
		$groups = array();

		$bindGroups = evaUserGroup::getBindGroups($elementId, $elementTable);
		foreach($bindGroups as $indexBind => $groupDefinition)
		{
			$groupeInfos = evaUserGroup::getUserGroup($groupDefinition->id_group);
			foreach($groupeInfos as $index => $groupe)
			{
				$groups[$groupDefinition->id_group]['name'] = $groupe->user_group_name;
				$groups[$groupDefinition->id_group]['description'] = $groupe->user_group_description;
				$groups[$groupDefinition->id_group]['userNumber'] = $groupe->TOTALUSERNUMBER;
			}
		}

		return $groups;
	}


	/**
	*	Get the user number affected to a work unit or a group
	*
	*	@param integer $elementId The identifier of the element (in its table) we want to bind
	*	@param string $elementTable The table of the element we want to bind
	*
	*	@return intger The number of employees of the work unit or the group
	*/
	function getUserNumberInWorkUnit($elementId, $elementTable)
	{
		$totalUserInWorkUnit = 0;

		$groupesLies = evaUserGroup::getBindGroups($elementId, $elementTable);
		if($groupesLies != null and count($groupesLies) > 0)
		{
			foreach($groupesLies as $groupeLie)
			{
				$userInGroup = evaUserGroup::getUserInGroup($groupeLie->id_group);
		// echo '<pre>';print_r($userInGroup);echo '</pre>';
				foreach($userInGroup as $users)
				{
					if($users->user_id > 0)
					{
						$userInUnit[$users->user_id] = 1;
					}
				}
			}
		}

		$totalUserInWorkUnit = count($userInUnit);

		return $totalUserInWorkUnit;
	}

	/**
	*	Output a combo box with the different groups that are available to be binded to an element
	*
	*	@param mixed $tableElement The element type we want to get the group list for
	*	@param integer $idElement The element identifier we want to get the group list for
	*
	*	@return mixed The entire html code to output
	*/
	function afficheListeGroupeUtilisateurNonAffecte($tableElement, $idElement)
	{
		$listeGroupes = array();
		$listeGroupes = evaUserGroup::getUserGroup();
		$tmpListeGroupes = array();
		foreach($listeGroupes as $key => $groupeInformation)
		{
			$tmpListeGroupes[$groupeInformation->user_group_id] = $groupeInformation;
		}
		$listeGroupes = $tmpListeGroupes;
		$listeGroupes[0] = '';

		$listeGroupesAffectes = evaUserGroup::getBindGroups($idElement, $tableElement);
		foreach($listeGroupesAffectes as $index => $affectedGroup)
		{
			if( isset($listeGroupes[$affectedGroup->id_group]) )
			{
				unset($listeGroupes[$affectedGroup->id_group]);
			}
		}

		$tabValue[0] = '0';
		$tabDisplay[0] = __('Cliquez ici pour ajouter', 'evarisk');
		$i=1;
		foreach($listeGroupes as $key => $groupeInformation)
		{
			$tabValue[$i] = $groupeInformation->user_group_id;
			$tabDisplay[$i] = $groupeInformation->user_group_name;
			$i++;
		}

		return EvaDisplayInput::afficherComboBox($listeGroupes, 'groupesUtilisateursNonAssocies', __('Groupes', 'evarisk') . '&nbsp;:', 'groupesUtilisateursNonAssocies', '', '', $tabValue, $tabDisplay);
	}

	/**
	*	Output a table with the different groups binded to an element
	*
	*	@param mixed $tableElement The element type we want to get the group list for
	*	@param integer $idElement The element identifier we want to get the group list for
	*	@param boolean $noScript Define if all datatable options must be shown or just the table
	*
	*	@return mixed $groupesUtilisateursMetaBox The entire html code to output
	*/
	function afficheListeGroupe($tableElement, $idElement, $noScript = false)
	{
		require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayDesign.class.php');
		require_once(EVA_LIB_PLUGIN_DIR . 'users/evaUserGroup.class.php');

		$groupesUtilisateursMetaBox = '';
		$groupesUtilisateursMetaBox = $groupesUtilisateursMetaBox . '<div id="message' . TABLE_LIAISON_USER_GROUPS . '" class="updated fade" style="cursor:pointer; display:none;"></div>';
		{//Création dataTable
			$idTable = 'groupesIndividus' . $tableElement . $idElement;
			$titres = array(__('Affect&eacute;', 'evarisk'), ucfirst(strtolower(sprintf(__('Nom %s', 'evarisk'), __('du groupe d\'utilisateurs', 'evarisk')))), ucfirst(strtolower(sprintf(__('Description %s', 'evarisk'), __('du groupe d\'utilisateurs', 'evarisk')))), __('Nombre d\'utilisateurs', 'evarisk'));
			unset($lignesDeValeurs);
			//on récupère les groupes.
			$groupesUtilisateurs = evaUserGroup::getUserGroup();
			$groupesLies = evaUserGroup::getBindGroups($idElement, $tableElement);
			if($groupesLies != null and count($groupesLies) > 0)
			{
				foreach($groupesLies as $groupeLie)
				{	
					$groupesLiesIds[$groupeLie->id_group] = $groupeLie;
				}
			}		
			foreach($groupesUtilisateurs as $groupeUtilisateurs)
			{
				unset($valeurs);
				$idLigne = $tableElement . $idElement . 'groupeUtilisateurs' . $groupeUtilisateurs->user_group_id;
				$idCbLigne = 'cb_' . $idLigne;
				$valeurs[] = array('value'=>evaDisplayInput::afficherInput('checkbox', $idCbLigne, null, null, null, 'cb_' . $idLigne));
				$valeurs[] = array('value'=>$groupeUtilisateurs->user_group_name);
				$valeurs[] = array('value'=>$groupeUtilisateurs->user_group_description);
				$valeurs[] = array('value'=>$groupeUtilisateurs->TOTALUSERNUMBER);
				$lignesDeValeurs[] = $valeurs;
				if(isset($groupesLiesIds[$groupeUtilisateurs->user_group_id]))
				{
					$script = $script . '<script type="text/javascript">
						$(document).ready(function() {
							$(\'#' . $idCbLigne . '\').attr("checked", "checked");
						});
					</script>';
				}
				$idLignes[] = $idLigne;
			}
			$classes = array('cbColumnLarge','','','cbNbUserGroup');
			if(!$noScript)
			{
			$script = $script . '<script type="text/javascript">
				$(document).ready(function() {
					$(\'#' . $idTable . '\').dataTable({
						"bLengthChange": false,
						"bAutoWidth": false,
						"bFilter": false,
						"bInfo": false,
						"aoColumns": 
						[
							{ "bSortable": false },
							{ "bSortable": true },
							{ "bSortable": false },
							{ "bSortable": true }
						],
						"aaSorting": [[1,\'asc\']]});
				});
			</script>';
			}
			$groupesUtilisateursDataTable = evaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script);
			
			$groupesUtilisateursMetaBox = $groupesUtilisateursMetaBox . $groupesUtilisateursDataTable;
		}
		{//Bouton Enregistrer
			$idBoutonEnregistrer = 'save_group';
			$scriptEnregistrement = '<script type="text/javascript">
				$(document).ready(function() {				
					$(\'#' . $idBoutonEnregistrer . '\').click(function() {		
						var groupeUtilisateurs = new Array();
						$(\'#' . $idTable . ' input\').each(function(){
							if($(this).attr("checked"))
							{
								groupeUtilisateurs.push($(this).attr("id").replace(new RegExp(/[A-Za-z_\-]+[\d]+[A-Za-z_\-]+/), ""));
							}
						});
						$("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
							"table": "' . TABLE_LIAISON_USER_GROUPS . '",
							"act": "save",
							"idsGroupes": groupeUtilisateurs,
							"tableElement": "' . $tableElement . '",
							"idElement": "' . $idElement . '"
						});
					});
				});
				</script>';
			$boutonEnregistrer = EvaDisplayInput::afficherInput('button', $idBoutonEnregistrer, __('Enregistrer', 'evarisk'), null, '', 'save', false, true, '', 'button-primary alignright', '', '', $scriptEnregistrement);
			$groupesUtilisateursMetaBox = $groupesUtilisateursMetaBox . $boutonEnregistrer;
		}
		
		return $groupesUtilisateursMetaBox;
	}

	/**
	*	Create an output for the user group box
	*
	*	@param mixed $tableElement The element type we want to get the group list for
	*	@param integer $idElement The element identifier we want to get the group list for
	*
	*	@return mixed The entire html code to output
	*/
	function boxGroupesUtilisateursEvaluation($tableElement, $idElement)
	{
		/*	Recuperation des groupes non affectes	*/
		$listeGroupesNonAffectes = evaUserGroup::afficheListeGroupeUtilisateurNonAffecte($tableElement, $idElement);

		$scriptEnregistrement = 
		'<script type="text/javascript">
			$(document).ready(function() {
				$(\'#groupesUtilisateursNonAssocies\').change(function() {
					$("#chargementBox' . TABLE_LIAISON_USER_GROUPS . '").html(\'<img src="' . EVA_IMG_DIVERS_PLUGIN_URL . 'loading.gif" />\');
					groupeAAssocier = $(\'#groupesUtilisateursNonAssocies\').val();

					$("#listeGroupesUtilisateurs").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
						"post": "true", 
						"table": "' . TABLE_LIAISON_USER_GROUPS . '",
						"act": "save",
						"idGroupe": groupeAAssocier,
						"tableElement": "' . $tableElement . '",
						"idElement": "' . $idElement . '"
					});
				});
			});

			function deleteGroupeBind(idGroupe)
			{
				$("#chargementBox' . TABLE_LIAISON_USER_GROUPS . '").html(\'<img src="' . EVA_IMG_DIVERS_PLUGIN_URL . 'loading.gif" />\');
				$("#listeGroupesUtilisateurs").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
					"post": "true", 
					"table": "' . TABLE_LIAISON_USER_GROUPS . '",
					"act": "delete",
					"idGroupe": idGroupe,
					"tableElement": "' . $tableElement . '",
					"idElement": "' . $idElement . '"
				});
			}
		</script>';

		/*	Recuperation des groupes deja affectes	*/
		$idTable = 'groupesIndividus' . $tableElement . $idElement;
		$titres = array(__('Actions', 'evarisk'), ucfirst(strtolower(sprintf(__('Nom %s', 'evarisk'), __('du groupe d\'utilisateurs', 'evarisk')))), ucfirst(strtolower(sprintf(__('Description %s', 'evarisk'), __('du groupe d\'utilisateurs', 'evarisk')))), __('Nombre d\'utilisateurs', 'evarisk'));
		$listeGroupesAffectes = evaUserGroup::getBindGroups($idElement, $tableElement);
		foreach($listeGroupesAffectes as $groupeAffecte)
		{
			$groupInformation = array();
			$groupInformation = evaUserGroup::getUserGroup($groupeAffecte->id_group);

			unset($valeurs);
			$idLigne = $tableElement . $idElement . 'groupeUtilisateurs' . $groupeAffecte->id_group;
			$idCbLigne = 'cb_' . $idLigne;
			$valeurs[] = array('value'=>'<img src="' . PICTO_DELETE . '" id="' . $idCbLigne . '" onclick="javascript:deleteGroupeBind(' . $groupeAffecte->id_group . ');" />');
			$valeurs[] = array('value'=>$groupInformation[0]->user_group_name);
			$valeurs[] = array('value'=>$groupInformation[0]->user_group_description);
			$valeurs[] = array('value'=>$groupInformation[0]->TOTALUSERNUMBER);

			$lignesDeValeurs[] = $valeurs;
			$idLignes[] = $idLigne;
		}
		$classes = array('cbColumnLarge','','','cbNbUserGroup');
		$script = $scriptEnregistrement . '<script type="text/javascript">
			$(document).ready(function() {
				$(\'#' . $idTable . '\').dataTable({
					"bLengthChange": false,
					"bAutoWidth": false,
					"bFilter": false,
					"bInfo": false,
					"aoColumns": 
					[
						{ "bSortable": false },
						{ "bSortable": true },
						{ "bSortable": false },
						{ "bSortable": true }
					],
					"aaSorting": [[1,\'asc\']]});
			});
		</script>';
		$groupesUtilisateursDataTable = evaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script);

		return '<div id="chargementBox' . TABLE_LIAISON_USER_GROUPS . '" ></div><div style="float:right;margin:10px;" >' . $listeGroupesNonAffectes . '</div>' . $groupesUtilisateursDataTable;
	}

	function afficheListeGroupeDU($tableElement, $idElement)
	{
		require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayDesign.class.php');
		require_once(EVA_LIB_PLUGIN_DIR . 'users/evaUserGroup.class.php');

		$groupesUtilisateursMetaBox = '';
		$groupesUtilisateursMetaBox = $groupesUtilisateursMetaBox . '<div id="message' . TABLE_LIAISON_USER_GROUPS . '" class="updated fade" style="cursor:pointer; display:none;"></div>';
		{//Création dataTable
			$idTable = 'groupesIndividus' . $tableElement . $idElement;
			$titres = array(ucfirst(strtolower(sprintf(__('Nom %s', 'evarisk'), __('du groupe d\'utilisateurs', 'evarisk')))), ucfirst(strtolower(sprintf(__('Description %s', 'evarisk'), __('du groupe d\'utilisateurs', 'evarisk')))), __('Nombre d\'utilisateurs', 'evarisk'));
			unset($lignesDeValeurs);
			//on récupère les groupes.
			$groupesUtilisateurs = evaUserGroup::getUserGroup();
			$groupesLies = evaUserGroup::getBindGroups($idElement, $tableElement);
			if($groupesLies != null and count($groupesLies) > 0)
			{
				foreach($groupesLies as $groupeLie)
				{	
					$groupesLiesIds[$groupeLie->id_group] = $groupeLie;
				}
			}
			foreach($groupesUtilisateurs as $groupeUtilisateurs)
			{
				unset($valeurs);
				$idLigne = $tableElement . $idElement . 'groupeUtilisateurs' . $groupeUtilisateurs->user_group_id;
				$idCbLigne = 'cb_' . $idLigne;
				$valeurs[] = array('value'=>$groupeUtilisateurs->user_group_name);
				$valeurs[] = array('value'=>$groupeUtilisateurs->user_group_description);
				$valeurs[] = array('value'=>$groupeUtilisateurs->TOTALUSERNUMBER);
				if(isset($groupesLiesIds[$groupeUtilisateurs->user_group_id]))
				{
					$lignesDeValeurs[] = $valeurs;
				}
				$idLignes[] = $idLigne;
			}

			$groupesUtilisateursDataTable = evaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, '');
			
			$groupesUtilisateursMetaBox = $groupesUtilisateursMetaBox . $groupesUtilisateursDataTable;
		}
		
		return $groupesUtilisateursMetaBox;
	}
}