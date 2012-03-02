<?php
/**
*	The different utilities to manage users in evarisk
*
*	@package 		Evarisk
*	@subpackage Users
* @author			Evarisk <contact@evarisk.com>
*/

class evaUserLinkElement
{

	/**
	*	Output a table with the different users binded to an element
	*
	*	@param mixed $tableElement The element type we want to get the user list for
	*	@param integer $idElement The element identifier we want to get the user list for
	*
	*	@return mixed $utilisateursMetaBox The entire html code to output
	*/
	function afficheListeUtilisateurTable($tableElement, $idElement)
	{
		$utilisateursMetaBox = '';
		$idBoutonEnregistrer = 'save_group' . $tableElement;

		$idTable = 'listeIndividus' . $tableElement . $idElement;
		$titres = array( '', ucfirst(strtolower(__('Id.', 'evarisk'))), ucfirst(strtolower(__('Nom', 'evarisk'))), ucfirst(strtolower(__('Pr&eacute;nom', 'evarisk'))), ucfirst(strtolower(__('Inscription', 'evarisk'))));
		unset($lignesDeValeurs);

		//on récupère les utilisateurs déjà affectés à l'élément en cours.
		$listeUtilisateursLies = array();
		$utilisateursLies = evaUserLinkElement::getAffectedUser($tableElement, $idElement);
		if(is_array($utilisateursLies ) && (count($utilisateursLies) > 0))
		{
			foreach($utilisateursLies as $utilisateur)
			{
				$listeUtilisateursLies[$utilisateur->id_user] = $utilisateur;
			}
		}

		$listeUtilisateurs = evaUser::getCompleteUserList();
		if(is_array($listeUtilisateurs) && (count($listeUtilisateurs) > 0))
		{
			foreach($listeUtilisateurs as $utilisateur)
			{
				unset($valeurs);
				$idLigne = $tableElement . $idElement . 'listeUtilisateurs' . $utilisateur['user_id'];
				$idCbLigne = 'cb_' . $idLigne;
				$moreLineClass = 'userIsNotLinked';
				if(isset($listeUtilisateursLies[$utilisateur['user_id']])){
					$moreLineClass = 'userIsLinked';
				}
				$valeurs[] = array('value'=>'<span id="actionButton' . $tableElement . 'UserLink' . $utilisateur['user_id'] . '" class="buttonActionUserLinkList ' . $moreLineClass . '  ui-icon pointer" >&nbsp;</span>');
				$valeurs[] = array('value'=>ELEMENT_IDENTIFIER_U . $utilisateur['user_id']);
				$valeurs[] = array('value'=>$utilisateur['user_lastname']);
				$valeurs[] = array('value'=>$utilisateur['user_firstname']);
				$valeurs[] = array('value'=>mysql2date('d M Y', $utilisateur['user_registered'], true));
				$lignesDeValeurs[] = $valeurs;
				$idLignes[] = $idLigne;
			}
		}
		else
		{
			$valeurs[] = array('value'=>'');
			$valeurs[] = array('value'=>'');
			$valeurs[] = array('value'=>__('Aucun r&eacute;sultat trouv&eacute;', 'evarisk'));
			$valeurs[] = array('value'=>'');
			$valeurs[] = array('value'=>'');
			$lignesDeValeurs[] = $valeurs;
			$idLignes[] = $tableElement . $idElement . 'listeUtilisateursVide';
		}

		$classes = array('addUserButtonDTable','userIdentifierColumn','','','');
		$script = 
'<script type="text/javascript">
	evarisk(document).ready(function(){
		evarisk("#' . $idTable . '").dataTable({
			"bAutoWidth": false,
			"bInfo": false,
			"bPaginate": false,
			"bFilter": false,
			"aaSorting": [[4,"desc"]]
		});
		evarisk("#' . $idTable . '").children("tfoot").remove();
		evarisk("#' . $idTable . '").removeClass("dataTables_wrapper");
		evarisk(".buttonActionUserLinkList").click(function(){
			if(evarisk(this).hasClass("addUserToLinkList")){
				var currentId = evarisk(this).attr("id").replace("actionButton' . $tableElement . 'UserLink", "");
				cleanUserIdFiedList(currentId, "' . $tableElement . '");

				var lastname = evarisk(this).parent("td").next().next().html();
				var firstname = evarisk(this).parent("td").next().next().next().html();

				addUserIdFieldList(' . ELEMENT_IDENTIFIER_U . 'currentId + " - " + lastname + " " + firstname, currentId, "' . $tableElement . '");
			}
			else if(evarisk(this).hasClass("deleteUserToLinkList")){
				deleteUserIdFiedList(evarisk(this).attr("id").replace("actionButton' . $tableElement . 'UserLink", ""), "' . $tableElement . '");
			}
			checkUserListModification("' . $tableElement . '", "' . $idBoutonEnregistrer . '");
		});
		evarisk("#completeUserList' . $tableElement . ' .odd, #completeUserList' . $tableElement . ' .even").click(function(){
			if(evarisk(this).children("td:first").children("span").hasClass("userIsNotLinked")){
				var currentId = evarisk(this).attr("id").replace("' . $tableElement . $idElement . 'listeUtilisateurs", "");
				cleanUserIdFiedList(currentId, "' . $tableElement . '");

				var lastname = evarisk(this).children("td:nth-child(3)").html();
				var firstname = evarisk(this).children("td:nth-child(4)").html();

				addUserIdFieldList("' . ELEMENT_IDENTIFIER_U . '" + currentId + " - " + lastname + " " + firstname, currentId, "' . $tableElement . '");
			}
			else{
				deleteUserIdFiedList(evarisk(this).attr("id").replace("' . $tableElement . $idElement . 'listeUtilisateurs", ""), "' . $tableElement . '");
			}
			checkUserListModification("' . $tableElement . '", "' . $idBoutonEnregistrer . '");
		});
	});
</script>';

		$utilisateursMetaBox .= evaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script);

		return $utilisateursMetaBox;
	}

	/**
	*	Output a table with the different users binded to an element
	*
	*	@param mixed $tableElement The element type we want to get the user list for
	*	@param integer $idElement The element identifier we want to get the user list for
	*
	*	@return mixed $utilisateursMetaBox The entire html code to output
	*/
	function afficheListeUtilisateur($tableElement, $idElement, $showButton = true)
	{
		$utilisateursMetaBox = '';
		$alreadyLinkedUserId = $alreadyLinkedUser = '';
		$idBoutonEnregistrer = 'save_group' . $tableElement;

		//on récupère les utilisateurs déjà affectés à l'élément en cours.
		$listeUtilisateursLies = array();
		$utilisateursLies = evaUserLinkElement::getAffectedUser($tableElement, $idElement);
		if(is_array($utilisateursLies ) && (count($utilisateursLies) > 0))
		{
			foreach($utilisateursLies as $utilisateur)
			{
				$listeUtilisateursLies[$utilisateur->id_user] = $utilisateur;
				$alreadyLinkedUserId .= $utilisateur->id_user . ', ';
				$currentUser = evaUser::getUserInformation($utilisateur->id_user);
				$alreadyLinkedUser .= '<div class="selecteduserOP" id="affectedUser' . $tableElement . $utilisateur->id_user . '" title="' . __('Cliquez pour supprimer', 'evarisk') . '" >' . ELEMENT_IDENTIFIER_U . $utilisateur->id_user . '&nbsp;-&nbsp;' . $currentUser[$utilisateur->id_user]['user_lastname'] . ' ' . $currentUser[$utilisateur->id_user]['user_firstname'] . '<div class="ui-icon deleteUserFromList" >&nbsp;</div></div>';
			}
		}
		else
		{
			$alreadyLinkedUser = '<span id="noUserSelected' . $tableElement . '" style="margin:5px 10px;color:#646464;" >' . __('Aucun utilisateur affect&eacute;', 'evarisk') . '</span>';
		}

		$utilisateursMetaBox = '
<input type="hidden" name="actuallyAffectedUserIdList' . $tableElement . '" id="actuallyAffectedUserIdList' . $tableElement . '" value="' . $alreadyLinkedUserId . '" />
<input type="hidden" name="affectedUserIdList' . $tableElement . '" id="affectedUserIdList' . $tableElement . '" value="' . $alreadyLinkedUserId . '" />

<div class="alignleft" style="width:40%;" >
	<div id="userListOutput' . $tableElement . '" class="userListOutput ui-widget-content clear" >' . $alreadyLinkedUser . '</div>
</div>

<div class="alignright" style="width:55%;" >';
	if(current_user_can('add_users'))
	{
	$utilisateursMetaBox .= '
	<span class="alignright" ><a href="' . get_option('siteurl') . '/wp-admin/user-new.php">' . __('Ajouter des utilisateurs', 'evarisk') . '</a></span>';
	}
	$utilisateursMetaBox .= '
	<div class="clear addLinkUserElement" >
		<div class="clear" >
			<span class="searchUserInput ui-icon" >&nbsp;</span>
			<input class="searchUserToAffect" type="text" name="affectedUser' . $tableElement . '" id="searchUser' . $tableElement . '" value="' . __('Rechercher dans la liste des utilisateurs', 'evarisk') . '" />
		</div>
		<div id="completeUserList' . $tableElement . '" class="completeUserList clear" >' . evaUserLinkElement::afficheListeUtilisateurTable($tableElement, $idElement) . '</div>
	</div>
	<div id="massAction' . $tableElement . '" ><span class="checkAll" >' . __('cochez tout', 'evarisk') . '</span>&nbsp;/&nbsp;<span class="uncheckAll" >' . __('d&eacute;cochez tout', 'evarisk') . '</span></div>
</div>
<div id="userBlocContainer" class="clear hide" ><div onclick="javascript:userDeletion(evarisk(this).attr(\'id\'), \'' . $tableElement . '\');" class="selecteduserOP" title="' . __('Cliquez pour supprimer', 'evarisk') . '" >#USERNAME#<span class="ui-icon deleteUserFromList" >&nbsp;</span></div></div>

<script type="text/javascript" >
	evarisk(document).ready(function(){
		/*	Mass action : check / uncheck all	*/
		jQuery("#massAction' . $tableElement . ' .checkAll").unbind("click");
		jQuery("#massAction' . $tableElement . ' .checkAll").click(function(){
			jQuery("#completeUserList' . $tableElement . ' .buttonActionUserLinkList").each(function(){
				if(jQuery(this).hasClass("userIsNotLinked")){
					jQuery(this).click();
				}
			});
		});
		jQuery("#massAction' . $tableElement . ' .uncheckAll").unbind("click");
		jQuery("#massAction' . $tableElement . ' .uncheckAll").click(function(){
			jQuery("#completeUserList' . $tableElement . ' .buttonActionUserLinkList").each(function(){
				if(jQuery(this).hasClass("userIsLinked")){
					jQuery(this).click();
				}
			});
		});

		/*	Action when click on delete button	*/
		jQuery(".selecteduserOP").click(function(){
			userDivId = jQuery(this).attr("id").replace("affectedUser' . $tableElement . '", "");
			deleteUserIdFiedList(userDivId, "' . $tableElement . '");
			checkUserListModification("' . $tableElement . '", "' . $idBoutonEnregistrer . '");
		});

		/*	User Search autocompletion	*/
		jQuery("#searchUser' . $tableElement . '").click(function(){
			jQuery(this).val("");
		});
		jQuery("#searchUser' . $tableElement . '").blur(function(){
			jQuery(this).val("' . __('Rechercher dans la liste des utilisateurs', 'evarisk') . '");
		});
		jQuery("#searchUser' . $tableElement . '").autocomplete("' . EVA_INC_PLUGIN_URL . 'liveSearch/searchUsers.php");
		jQuery("#searchUser' . $tableElement . '").result(function(event, data, formatted){
			cleanUserIdFiedList(data[1], "' . $tableElement . '");
			addUserIdFieldList(data[0], data[1], "' . $tableElement . '");

			checkUserListModification("' . $tableElement . '", "' . $idBoutonEnregistrer . '");

			jQuery("#searchUser' . $tableElement . '").val("' . __('Rechercher dans la liste des utilisateurs', 'evarisk') . '");
		});
	});
</script>';

		if($showButton){
			$alternate_button = '';
			switch($tableElement)
			{
				case TABLE_GROUPEMENT:
				case TABLE_GROUPEMENT . '_evaluation':
					if(!current_user_can('digi_edit_groupement') && !current_user_can('digi_edit_groupement_' . $idElement)){
						$showButton = false;
					}
				break;
				case TABLE_UNITE_TRAVAIL:
				case TABLE_UNITE_TRAVAIL . '_evaluation':
					if(!current_user_can('digi_edit_unite') && !current_user_can('digi_edit_unite_' . $idElement)){
						$showButton = false;
					}
				break;

				case TABLE_TACHE:
					if(!current_user_can('digi_edit_task')){
						$showButton = false;
					}
					else{
						$currentTask = new EvaTask($idElement);
						$currentTask->load();
						$ProgressionStatus = $currentTask->getProgressionStatus();

						if((($ProgressionStatus == 'Done') || ($ProgressionStatus == 'DoneByChief')) && (digirisk_options::getOptionValue('possibilite_Modifier_Tache_Soldee') == 'non') ){
							$alternate_button = '
				<br class="clear" />
				<div class="alignright button-primary" id="TaskSaveButton" >
					' . __('Cette t&acirc;che est sold&eacute;e, vous ne pouvez pas modifier les utilisateurs', 'evarisk') . '
				</div>';
						}
					}
				break;
				case TABLE_ACTIVITE:
					if(!current_user_can('digi_edit_action')){
						$showButton = false;
					}
					else{
						$current_action = new EvaActivity($idElement);
						$current_action->load();
						$ProgressionStatus = $current_action->getProgressionStatus();

						if((($ProgressionStatus == 'Done') || ($ProgressionStatus == 'DoneByChief')) && (digirisk_options::getOptionValue('possibilite_Modifier_Action_Soldee') == 'non') ){
							$alternate_button = '
				<br class="clear" />
				<div class="alignright button-primary" id="TaskSaveButton" >
					' . __('Cette t&acirc;che est sold&eacute;e, vous ne pouvez pas modifier les utilisateurs', 'evarisk') . '
				</div>';
						}
					}
				break;
			}
		}

		if($showButton){//Bouton Enregistrer
			$scriptEnregistrement = '<script type="text/javascript">
				evarisk(document).ready(function() {
					checkUserListModification("' . $tableElement . '", "' . $idBoutonEnregistrer . '");
					evarisk("#' . $idBoutonEnregistrer . '").click(function(){
						evarisk("#saveButtonLoading' . $tableElement . '").show();
						evarisk("#saveButtonContainer' . $tableElement . '").hide();
						evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
							"table": "' . TABLE_LIAISON_USER_ELEMENT . '",
							"act": "save",
							"utilisateurs": evarisk("#affectedUserIdList' . $tableElement . '").val(),
							"tableElement": "' . $tableElement . '",
							"idElement": "' . $idElement . '"
						});
					});
				});
				</script>';

			if($alternate_button != ''){
				$utilisateursMetaBox .= $alternate_button;
			}
			else{
				$utilisateursMetaBox .= '<div class="clear" ><div id="saveButtonLoading' . $tableElement . '" style="display:none;" class="clear alignright" ><img src="' . PICTO_LOADING_ROUND . '" alt="loading in progress" /></div><div id="saveButtonContainer' . $tableElement . '" >' . EvaDisplayInput::afficherInput('button', $idBoutonEnregistrer, __('Enregistrer', 'evarisk'), null, '', 'save', false, true, '', 'button-primary alignright', '', '', $scriptEnregistrement) . '</div></div>';
			}
		}

		return $utilisateursMetaBox;
	}

	/**
	*	Get the user linked to an element
	*
	*	@param mixed $tableElement The element type we want to get the user list for
	*	@param integer $idElement The element identifier we want to get the user list for
	*
	*	@return object A wordpress object with the user list affected to the given element
	*/
	function getAffectedUser($tableElement, $idElement)
	{
		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT id_user, id, date_affectation, id_attributeur
			FROM " . TABLE_LIAISON_USER_ELEMENT . "
			WHERE id_element = '%s'
				AND table_element = '%s'
				AND status = 'valid' "
			, $idElement, $tableElement
		);
		
		return $wpdb->get_results($query);
	}

	/**
	*	Create a link betwwen an element and a user
	*
	*	@param mixed $tableElement The element type we want to create a link to
	*	@param integer $idElement The element identifier we want to create a link to
	*	@param array $userIdList An user list id to create link with the selected element
	*
	*	@return mixed $messageInfo An html output that contain the result message
	*/
	function setLinkUserElement($tableElement, $idElement, $userIdList, $outputMessage = true){
		global $wpdb;
		global $current_user;
		$userToTreat = "  ";

		//on récupère les utilisateurs déjà affectés à l'élément en cours.
		$listeUtilisateursLies = array();
		$utilisateursLies = evaUserLinkElement::getAffectedUser($tableElement, $idElement);
		if(is_array($utilisateursLies ) && (count($utilisateursLies) > 0)){
			foreach($utilisateursLies as $utilisateur){
				$listeUtilisateursLies[$utilisateur->id_user] = $utilisateur;
			}
		}

		/*	Transform the new element list to affect into an array	*/
		$newUserList = explode(", ", $userIdList);

		$message = addslashes('<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Il n\'a aucune modification a apporter', 'evarisk') . '</strong>');

		/*	Read the product list already linked for checking if they are again into the list or if we have to delete them form the list	*/
		$done_element = 0;
		$deleted_user_list = array();
		foreach($listeUtilisateursLies as $utilisateurs){
			if(is_array($newUserList) && !in_array($utilisateurs->id_user, $newUserList)){
				$query = $wpdb->prepare(
"UPDATE " . TABLE_LIAISON_USER_ELEMENT . " 
SET status = 'deleted', 
	date_desAffectation = NOW(), 
	id_desAttributeur = %d 
WHERE id = %d", 
$current_user->ID, $utilisateurs->id);
				$done_element += $wpdb->query($query);
				if(($tableElement == TABLE_TACHE) || ($tableElement == TABLE_ACTIVITE)){
					$wpdb->update(DIGI_DBT_LIAISON_USER_NOTIFICATION_ELEMENT, array('status' => 'deleted', 'date_desAffectation' => current_time('mysql', 0), 'id_desAttributeur' => $current_user->ID), array('id_user' => $utilisateurs->id_user, 'id_element' => $idElement, 'table_element' => $tableElement));
				}
				$deleted_user_list[] = $utilisateurs->id;

				/*	Log modification on element and notify user if user subscribe	*/
				digirisk_user_notification::log_element_modification($tableElement, $idElement, 'delete_user_from_affectation_list', $utilisateursLies, $deleted_user_list);
			}
		}
		if($done_element > 0){
			$message = addslashes('<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Les utilisateurs ont bien &eacute;t&eacute; supprim&eacute; de la liste des utilisateurs affect&eacute;s', 'evarisk') . '</strong>');
		}

		if(is_array($newUserList) && (count($newUserList) > 0)){
			foreach($newUserList as $userId){
				if((trim($userId) != '') && !array_key_exists($userId, $listeUtilisateursLies)){
					$userToTreat .= "('', 'valid', NOW(), '" . $current_user->ID . "', '0000-00-00 00:00:00', '', '" . $userId . "', '" . $idElement . "', '" . $tableElement . "'), ";
				}
			}
		}

		$endOfQuery = trim(substr($userToTreat, 0, -2));
		if($endOfQuery != ""){
			$query = $wpdb->prepare(
				"REPLACE INTO " . TABLE_LIAISON_USER_ELEMENT . "
					(id, status ,date_affectation ,id_attributeur ,date_desAffectation ,id_desAttributeur ,id_user ,id_element ,table_element)
				VALUES 
					" . $endOfQuery
			);
			if($wpdb->query($query)){
				$message = addslashes('<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Les modifications ont correctement &eacute;t&eacute enregistr&eacute;es', 'evarisk') . '</strong>');
				switch($tableElement){
					case TABLE_ACTIVITE:
					case TABLE_TACHE:
						/*	Log modification on element and notify user if user subscribe	*/
						digirisk_user_notification::log_element_modification($tableElement, $idElement, 'user_affectation_update', $utilisateursLies, $newUserList);
					break;
				}
			}
			else{
				$message = addslashes('<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Les modifications n\'ont pas toutes &eacute;t&eacute correctement enregistr&eacute;es', 'evarisk') . '</strong>"');
			}
		}

		$script = '';
		if($outputMessage){
			$script .= '
		actionMessageShow("#messageInfo_' . $tableElement . $idElement . '_affectUser", "' . $message . '");
		setTimeout(\'actionMessageHide("#messageInfo_' . $tableElement . $idElement . '_affectUser")\',7500);
		jQuery("#saveButtonLoading' . $tableElement . '").hide();
		jQuery("#saveButtonContainer' . $tableElement . '").show();
		jQuery("#actuallyAffectedUserIdList' . $tableElement . '").val(jQuery("#affectedUserIdList' . $tableElement . '").val());
		checkUserListModification("' . $tableElement . '", "save_group' . $tableElement . '");';
		}
		if(($tableElement == TABLE_TACHE) || ($tableElement == TABLE_ACTIVITE)){
			$script .= '
		jQuery("#userNotificationContainerBox").html(jQuery("#loadingImg").html());
		jQuery("#userNotificationContainerBox").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",{
			post:"true",
			"table": "' . DIGI_DBT_ELEMENT_NOTIFICATION . '",
			"act": "reload_user_notification_box",
			"tableElement": "' . $tableElement . '",
			"idElement": "' . $idElement . '"
		});';
		}

		if($script != ''){
			echo 
'<script type="text/javascript">
	evarisk(document).ready(function(){
		' . $script . '
	});
</script>';
		}
	}

}