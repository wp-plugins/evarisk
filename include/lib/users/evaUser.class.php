<?php
/**
*	The different utilities to manage users in evarisk
*
*	@package 		Evarisk
*	@subpackage Users
* @author			Evarisk team <contact@evarisk.com>
*/

class evaUser
{
	/**
	*	Set the current attribute group regarding the 
	*/
	protected $_currentAttributeGroup = '';
	/**
	*	Set the current attribute group regarding the 
	*/
	protected $_userToUpdate = '';

	/**
	*	Create an instance for a user. Initialize the entity id related to the user
	*/
	function evaUser($AttributeGroupName)
	{
		global $eav_attribute;

		$eav_attribute->setCurrentEntityTypeId($eav_attribute->getEntityInformation('eva_users'));

		$this->_currentAttributeGroup = $AttributeGroupName;
	}

	/**
	*	Get the query that will be launch to update different attribute
	*
	*	@return mixed $this->_attributesValueQuery The query we will launch to update information
	*/
	function setUserToUpdate($userId)
	{
		$this->_userToUpdate = $userId;
	}

	/**
	*	Get the list of attribute available for a given group
	*
	*	@return mixed $attributeForm The different element to output
	*/
	function evaUserAttributeForm()
	{
		global $eav_attribute;

		$attributeForm = $eav_attribute->attributeFormOutput($this->_currentAttributeGroup, $this->_userToUpdate);

		return $this->evaUserAttributeFormTemplate($attributeForm);
	}

	/**
	*	Output a container with the different element for user
	*
	*	@param mixed $content The different element to output in the template
	*/
	function evaUserAttributeFormTemplate($content)
	{
		if(trim($content) != '')
		{
?>
<h3><?php _e('Informations compl&eacute;mentaires evarisk'); ?></h3>
<div class="form-table" id="evaUserInformation" >
	<?php echo $content; ?>
</div>
<?php
		}
	}

	/**
	*	Update the different information for an user profile. First we check that there is an user update form that were send, then if it's the case we do the job
	*/
	function evaUserUpdateProfile()
	{
		/* Check if we send the user update form, if it is not the case we return to the normal output */
		if(!isset($_POST['user_id'])) return;

		global $eav_attribute;

		if(isset($_REQUEST['userAttributes']) && is_array($_REQUEST['userAttributes']) && (count($_REQUEST['userAttributes']) > 0))
		{
			foreach ($_REQUEST['userAttributes'] as $attributeType => $attributeContent) :
					$eav_attribute->createAttributesValueHeaderQuery($attributeType);
				foreach ($attributeContent as $attributeCode => $attributeValue) :
					$eav_attribute->createAttributesValueQuery($attributeCode, $_POST['user_id'], $attributeValue);
				endforeach;
					$eav_attribute->attributesValueQueryTrimmer();
					$eav_attribute->setAttributesValue();
					$eav_attribute->attributesValueQueryTrimmer(true);
			endforeach;
		}
	}


	/**
	*	Get the wordpress' user list
	*
	*	@return array $userlist An object containing the different subscriber
	*/
	function getUserList()
	{
		global $wpdb;

		$query = 
			"SELECT USERS.ID
			FROM wp_users AS USERS";
		$userList = $wpdb->get_results($query);

		return $userList;
	}
	/**
	*	Get the wordpress' user list
	*
	*	@return array $userlist An object containing the different subscriber
	*/
	function getCompleteUserList()
	{
		$listeComplete = array();

		$listeUtilisateurs = evaUser::getUserList();
		foreach($listeUtilisateurs as $utilisateurs)
		{
			if($utilisateurs->ID != 1)
			{
				$user_info = get_userdata($utilisateurs->ID);

				unset($valeurs);
				$valeurs['user_id'] = $user_info->ID;
				$valeurs['user_registered'] = $user_info->user_registered;
				if( (isset($user_info->user_lastname) && ($user_info->user_lastname != '')) )
				{
					$valeurs['user_lastname'] = $user_info->user_lastname;
				}
				else
				{
					$valeurs['user_lastname'] = '';
				}
				if( (isset($user_info->user_firstname) && ($user_info->user_firstname != '')) )
				{
					$valeurs['user_firstname'] = $user_info->user_firstname;
				}
				else
				{
					$valeurs['user_firstname'] = $user_info->user_nicename;
				}

				$listeComplete[$user_info->ID] = $valeurs;
			}
		}

		return $listeComplete;
	}
	/**
	*	Get the wordpress' user list
	*
	*	@return array $userlist An object containing the different subscriber
	*/
	function getUserInformation($userId)
	{
		$listeComplete = array();

		$user_info = get_userdata($userId);

		unset($valeurs);
		$valeurs['user_id'] = $user_info->ID;
		if( (isset($user_info->user_lastname) && ($user_info->user_lastname != '')) )
		{
			$valeurs['user_lastname'] = $user_info->user_lastname;
		}
		else
		{
			$valeurs['user_lastname'] = '';
		}
		if( (isset($user_info->user_firstname) && ($user_info->user_firstname != '')) )
		{
			$valeurs['user_firstname'] = $user_info->user_firstname;
		}
		else
		{
			$valeurs['user_firstname'] = $user_info->user_nicename;
		}

		$listeComplete[$user_info->ID] = $valeurs;

		return $listeComplete;
	}


	/**
	*	Get the identifier of the groups bind with an element
	*
	*	@param integer $elementId The identifier of the element (in its table) we want to bind
	*	@param string $elementTable The table of the element we want to bind
	*
	*	@return array An array containing the groups identifiers
	*/
	function getBindUsers($elementId, $elementTable)
	{
		global $wpdb;
		
		$elementId = mysql_real_escape_string($elementId);
		$elementTable = mysql_real_escape_string($elementTable);
		
		$queryCleanGroupBind = $wpdb->prepare("SELECT id_user FROM " . TABLE_LIAISON_USER_EVALUATION . " WHERE table_element = '%s' AND id_element = %d and status='valid'", $elementTable, $elementId);
		
		return $wpdb->get_results($queryCleanGroupBind);
	}


	/**
	*	Return different informations about users
	*/
	function dashBoardStats()
	{
		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT 
				(
					SELECT count(USERS.ID)
					FROM " . $wpdb->prefix . "users AS USERS
					WHERE USERS.ID != 1
				) AS TOTAL_USER, 
				(
					SELECT COUNT( DISTINCT( USER_LINK_EVALUATION.id_user ) )
					FROM " . TABLE_LIAISON_USER_ELEMENT . " AS USER_LINK_EVALUATION
					WHERE USER_LINK_EVALUATION.table_element = '" . TABLE_UNITE_TRAVAIL . "_evaluation'
						AND status = 'valid'
					GROUP BY USER_LINK_EVALUATION.table_element
				) AS EVALUATED_USER
			LIMIT 1"
		);

		return $wpdb->get_row($query);
	}

}