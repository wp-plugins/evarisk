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
	*	Save the new bind between a users group and an element
	*
	*	@param integer $groupId The identifier of the group we want to bind
	*	@param integer $elementId The identifier of the element (in its table) we want to bind
	*	@param string $elementTable The table of the element we want to bind
	*
	*	@return array $status An array containing the result of the method
	*/
	function saveUserEvaluationBind($userId, $elementId, $elementTable)
	{
		global $wpdb;
		$status = array();

		$query = $wpdb->prepare("REPLACE INTO " . TABLE_LIAISON_USER_EVALUATION . " 
				(id_user, table_element, id_element, date)
			VALUES 
				(%d, '%s', %d, '" . date('Y-m-d H:i:s') . "')", $userId, $elementTable, $elementId);
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

	function deleteUserEvaluationBind($userId, $elementId, $elementTable)
	{
		global $wpdb;
		$status = array();

		$query = $wpdb->prepare("UPDATE " . TABLE_LIAISON_USER_EVALUATION . " 
				SET status = 'deleted' 
				WHERE id_user = '%d' AND table_element = '%s' AND id_element = '%d' ", $userId, $elementTable, $elementId);
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
	function getBindUsers($elementId, $elementTable)
	{
		global $wpdb;
		
		$elementId = mysql_real_escape_string($elementId);
		$elementTable = mysql_real_escape_string($elementTable);
		
		$queryCleanGroupBind = $wpdb->prepare("SELECT id_user FROM " . TABLE_LIAISON_USER_EVALUATION . " WHERE table_element = '%s' AND id_element = %d and status='valid'", $elementTable, $elementId);
		
		return $wpdb->get_results($queryCleanGroupBind);
	}

	/**
	*	Get the list of users' who take part in an evaluation
	*
	*	@param integer $elementId The identifier of the element we are working on
	*	@param mixed $elementTable The table identifying the object we are working on
	*
	*	@return mixed The html code for the dropdown menu with the different users who are not affected to the object we are working on
	*/
	function afficheListeUtilisateurNeParticipantPasEvaluation($elementId, $elementTable)
	{
		$listeUtilisateurs = array();
		$listeUtilisateurs = evaUser::getCompleteUserList();
		$listeUtilisateurs[0] = '';

		$listeUtilisateursAffectes = evaUser::getBindUsers($elementId, $elementTable);
		foreach($listeUtilisateursAffectes as $index => $affectedUser)
		{
			if( isset($listeUtilisateurs[$affectedUser->id_user]) )
			{
				unset($listeUtilisateurs[$affectedUser->id_user]);
			}
		}

		$tabValue[0] = '0';
		$tabDisplay[0] = __('Cliquez ici pour ajouter un participant', 'evarisk');

		$i=1;
		foreach($listeUtilisateurs as $idUtilisateur => $informationsUtilisateurs)
		{
			if($idUtilisateur > 0)
			{
				$tabValue[$i] = $idUtilisateur;
				$tabDisplay[$i] = $informationsUtilisateurs['user_lastname'] . ' ' . $informationsUtilisateurs['user_firstname'];
				$i++;
			}
		}

		return EvaDisplayInput::afficherComboBox($listeUtilisateurs, 'utilisateursEvalues', __('Utilisateurs', 'evarisk') . '&nbsp;:', 'utilisateursEvalues', '', '', $tabValue, $tabDisplay);
	}

	/**
	*	Return the output for the box containing the different users' who take part in the evaluation
	*
	*	@param array $arguments An array wiht the different element (the object type and te object id)
	*	@return mixed The html code of the box
	*/
	function boxUtilisateursEvalues($arguments)
	{
		$tableElement = $arguments['tableElement'];
		$idElement = $arguments['idElement'];

		$listeUtilisateur = evaUser::afficheListeUtilisateurNeParticipantPasEvaluation($idElement, $tableElement);

		$scriptEnregistrement = 
		'<script type="text/javascript">
			$(document).ready(function() {
				$(\'#utilisateursEvalues\').change(function() {
					$("#chargementBox' . TABLE_LIAISON_USER_EVALUATION . '").html(\'<img src="' . EVA_IMG_DIVERS_PLUGIN_URL . 'loading.gif" />\');
					utilisateursEvaluesAAjouter = $(\'#utilisateursEvalues\').val();

					$("#listeEmployesEvalues").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
						"post": "true", 
						"table": "' . TABLE_LIAISON_USER_EVALUATION . '",
						"act": "save",
						"idsUsers": utilisateursEvaluesAAjouter,
						"tableElement": "' . $tableElement . '",
						"idElement": "' . $idElement . '"
					});
				});
			});

			function deleteUserEvaluationBind(idUser)
			{
				$("#chargementBox' . TABLE_LIAISON_USER_EVALUATION . '").html(\'<img src="' . EVA_IMG_DIVERS_PLUGIN_URL . 'loading.gif" />\');
				$("#listeEmployesEvalues").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
					"post": "true", 
					"table": "' . TABLE_LIAISON_USER_EVALUATION . '",
					"act": "delete",
					"idsUsers": idUser,
					"tableElement": "' . $tableElement . '",
					"idElement": "' . $idElement . '"
				});
			}
		</script>';

		{//Création dataTable liste utilisateur affectés à cette unité pour l'évaluation des risques
			$idTable = 'listesIndividus' . $tableElement . $idElement;
			$titres = array(__('Action', 'evarisk'), ucfirst(strtolower(__('Nom', 'evarisk'))), ucfirst(strtolower(__('Pr&eacute;nom', 'evarisk'))));
			unset($lignesDeValeurs);
			//on récupère les utilisateurs.
			$listeUtilisateurs = evaUser::getBindUsers($idElement, $tableElement);
			foreach($listeUtilisateurs as $utilisateurs)
			{
				if($utilisateurs->id_user != 1)
				{
					$user_info = get_userdata($utilisateurs->id_user);

					unset($valeurs);
					$idLigne = $tableElement . $idElement . 'utilisateursAEvaluer' . $user_info->ID;
					$idCbLigne = 'cb_' . $idLigne;
					$valeurs[] = array('value'=>'<img src="' . PICTO_DELETE . '" id="' . $idCbLigne . '" onclick="javascript:deleteUserEvaluationBind(' . $user_info->ID . ');" />');
					if( (isset($user_info->user_lastname) && ($user_info->user_lastname != '')) )
					{
						$valeurs[] = array('value'=>$user_info->user_lastname);
					}
					else
					{
						$valeurs[] = '';
					}
					if( (isset($user_info->user_firstname) && ($user_info->user_firstname != '')) )
					{
						$valeurs[] = array('value'=>$user_info->user_firstname);
					}
					else
					{
						$valeurs[] = array('value'=>$user_info->user_nicename);
					}

					$lignesDeValeurs[] = $valeurs;
					$idLignes[] = $idLigne;
				}
			}

			$classes = array('cbColumnLarge','','');
			$script = '<script type="text/javascript">
				$(document).ready(function() {
					$(\'#' . $idTable . '\').dataTable({
						"bLengthChange": false,
						"bFilter": false,
						"bInfo": false,
						"bAutoWidth": false, 
						"aoColumns": 
						[
							{ "bSortable": false },
							{ "bSortable": true },
							{ "bSortable": true }
						],
						"aaSorting": [[1,\'asc\']]});
				});
			</script>';
			$utilisateursEvaluesDataTable = evaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script);
		}

		return '<div id="chargementBox' . TABLE_LIAISON_USER_EVALUATION . '" ></div>' . $scriptEnregistrement . '<div style="float:right;margin:10px;" >' . $listeUtilisateur . '<div ><a href="' . get_bloginfo('siteurl') . '/wp-admin/user-new.php" >' . __('Ajouter un nouvel utilisateur', 'evarisk') . '</a></div></div>' . $utilisateursEvaluesDataTable;
	}

}