<?php
/**
*	The different utilities to manage users in evarisk
*
*	@package 		Evarisk
*	@subpackage Users
* @author			Evarisk team <contact@evarisk.com>
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
		$titres = array( '', ucfirst(strtolower(__('Nom', 'evarisk'))), ucfirst(strtolower(__('Pr&eacute;nom', 'evarisk'))), ucfirst(strtolower(__('Inscription', 'evarisk'))));
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
				if(isset($listeUtilisateursLies[$utilisateur['user_id']]))
				{
					$moreLineClass = 'userIsLinked';
				}
				$valeurs[] = array('value'=>'<span id="actionButton' . $tableElement . 'UserLink' . $utilisateur['user_id'] . '" class="buttonActionUserLinkList ' . $moreLineClass . '  ui-icon pointer" >&nbsp;</span>');
				$valeurs[] = array('value'=>$utilisateur['user_lastname']);
				$valeurs[] = array('value'=>$utilisateur['user_firstname']);
				$valeurs[] = array('value'=>eva_tools::transformeDate($utilisateur['user_registered']));
				$lignesDeValeurs[] = $valeurs;
				$idLignes[] = $idLigne;
			}
		}
		else
		{
			$valeurs[] = array('value'=>'');
			$valeurs[] = array('value'=>__('Aucun r&eacute;sultat trouv&eacute;', 'evarisk'));
			$valeurs[] = array('value'=>'');
			$valeurs[] = array('value'=>'');
			$lignesDeValeurs[] = $valeurs;
			$idLignes[] = $tableElement . $idElement . 'listeUtilisateursVide';
		}

		$classes = array('addUserButtonDTable','','','');
		$script = 
'<script type="text/javascript">
	evarisk(document).ready(function(){
		evarisk("#' . $idTable . '").dataTable({
			"bAutoWidth": false,
			"bInfo": false,
			"bPaginate": false,
			"bFilter": false,
			"aaSorting": [[3,"desc"]],
		});
		evarisk("#' . $idTable . '").children("tfoot").remove();
		evarisk(".buttonActionUserLinkList").click(function(){
			if(evarisk(this).hasClass("addUserToLinkList")){
				var currentId = evarisk(this).attr("id").replace("actionButton' . $tableElement . 'UserLink", "");
				cleanUserIdFiedList(currentId, "' . $tableElement . '");
				
				var lastname = evarisk(this).parent("td").next().html();
				var firstname = evarisk(this).parent("td").next().next().html();

				addUserIdFieldList(lastname + " " + firstname, currentId, "' . $tableElement . '");
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
				
				var lastname = evarisk(this).children("td:nth-child(2)").html();
				var firstname = evarisk(this).children("td:nth-child(3)").html();

				addUserIdFieldList(lastname + " " + firstname, currentId, "' . $tableElement . '");
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
	function afficheListeUtilisateur($tableElement, $idElement)
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
				$alreadyLinkedUser .= '<div class="selecteduserOP" id="affectedUser' . $tableElement . $utilisateur->id_user . '" title="' . __('Cliquez pour supprimer', 'evarisk') . '" >' . $currentUser[$utilisateur->id_user]['user_lastname'] . ' ' . $currentUser[$utilisateur->id_user]['user_firstname'] . '<div class="ui-icon deleteUserFromList" >&nbsp;</div></div>';
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

<div class="alignright" style="width:55%;" >
	<span class="alignright" ><a href="' . get_option('siteurl') . '/wp-admin/user-new.php">' . __('Ajouter des utilisateurs', 'evarisk') . '</a></span>
	<div class="clear addLinkUserElement" >
		<div class="clear" >
			<span class="searchUserInput ui-icon" ></span>
			<input class="searchUserToAffect" type="text" name="affectedUser' . $tableElement . '" id="affectedUser' . $tableElement . '" value="' . __('Rechercher dans la liste des utilisateurs', 'evarisk') . '" />
		</div>
		<div id="completeUserList' . $tableElement . '" class="completeUserList clear" >' . evaUserLinkElement::afficheListeUtilisateurTable($tableElement, $idElement) . '</div>
	</div>
	<div id="massAction' . $tableElement . '" ><span class="checkAll" >' . __('cochez tout', 'evarisk') . '</span>&nbsp;/&nbsp;<span class="uncheckAll" >' . __('d&eacute;cochez tout', 'evarisk') . '</span></div>
</div>

<script type="text/javascript" >
	function checkUserListModification(tableElement, idButton){
		var actualUserList = evarisk("#actuallyAffectedUserIdList" + tableElement).val();
		var userList = evarisk("#affectedUserIdList" + tableElement).val();

		if(actualUserList == userList){
			evarisk("#" + idButton).attr("disabled", "disabled");
			evarisk("#" + idButton).addClass("button-secondary");
			evarisk("#" + idButton).removeClass("button-primary");
		}
		else{
			evarisk("#" + idButton).attr("disabled", "");
			evarisk("#" + idButton).removeClass("button-secondary");
			evarisk("#" + idButton).addClass("button-primary");
		}
	}
	function cleanUserIdFiedList(id, tableElement)
	{
		var actualAffectedUserList = evarisk("#affectedUserIdList" + tableElement).val().replace(id + ", ", "");
		evarisk("#affectedUserIdList" + tableElement).val( actualAffectedUserList + id + ", ");

		if(evarisk("#affectedUser" + tableElement + id)){
			evarisk("#affectedUser" + tableElement + id).remove();
		}

		evarisk("#actionButton" + tableElement + "UserLink" + id).addClass("userIsLinked");
		evarisk("#actionButton" + tableElement + "UserLink" + id).removeClass("userIsNotLinked");
	}
	function deleteUserIdFiedList(id, tableElement)
	{
		var actualAffectedUserList = evarisk("#affectedUserIdList" + tableElement).val().replace(id + ", ", "");
		evarisk("#affectedUserIdList" + tableElement).val( actualAffectedUserList );
		evarisk("#affectedUser" + tableElement + id).remove();

		evarisk("#actionButton" + tableElement + "UserLink" + id).removeClass("userIsLinked");
		evarisk("#actionButton" + tableElement + "UserLink" + id).addClass("userIsNotLinked");
	}
	function addUserIdFieldList(name, id, tableElement)
	{
		evarisk(\'<div class="selecteduserOP" onclick="javascript:deleteUserIdFiedList(evarisk(this).attr(\\\'id\\\').replace(\\\'affectedUser\\\' + evarisk(\\\'.userListOutput\\\').attr(\\\'id\\\').replace(\\\'userListOutput\\\', \\\'\\\'), \\\'\\\'), evarisk(\\\'.userListOutput\\\').attr(\\\'id\\\').replace(\\\'userListOutput\\\', \\\'\\\'));" title="' . __('Cliquez pour supprimer', 'evarisk') . '" />\').text(name).prependTo("#userListOutput" + tableElement);

		evarisk("#noUserSelected" + tableElement).remove();
		evarisk("#userListOutput" + tableElement + " div:first").append(\'<div  onclick="javascript:deleteUserIdFiedList(evarisk(this).parent().attr(\\\'id\\\').replace(\\\'affectedUser\\\' + evarisk(\\\'.userListOutput\\\').attr(\\\'id\\\').replace(\\\'userListOutput\\\', \\\'\\\'), \\\'\\\'), evarisk(\\\'.userListOutput\\\').attr(\\\'id\\\').replace(\\\'userListOutput\\\', \\\'\\\'));" class="ui-icon deleteUserFromList" >&nbsp;</div>\');

		evarisk("#userListOutput" + tableElement + " div:first").attr("id", "affectedUser" + tableElement + id);
		evarisk("#userListOutput" + tableElement).attr("scrollTop",0);
	}

	evarisk(document).ready(function(){
		evarisk("#massAction' . $tableElement . ' .checkAll").unbind("click");
		evarisk("#massAction' . $tableElement . ' .checkAll").click(function(){
			evarisk("#completeUserList' . $tableElement . ' .buttonActionUserLinkList").each(function(){
				if(evarisk(this).hasClass("userIsNotLinked")){
					evarisk(this).click();
				}
			});
		});
		evarisk("#massAction' . $tableElement . ' .uncheckAll").unbind("click");
		evarisk("#massAction' . $tableElement . ' .uncheckAll").click(function(){
			evarisk("#completeUserList' . $tableElement . ' .buttonActionUserLinkList").each(function(){
				if(evarisk(this).hasClass("userIsLinked")){
					evarisk(this).click();
				}
			});
		});
		evarisk("#affectedUser' . $tableElement . '").click(function(){
			evarisk(this).val("");
		});
		evarisk("#affectedUser' . $tableElement . '").blur(function(){
			evarisk(this).val("' . __('Rechercher dans la liste des utilisateurs', 'evarisk') . '");
		});

		evarisk(".selecteduserOP").click(function(){
			id = evarisk(this).attr("id").replace("affectedUser' . $tableElement . '", "");
			deleteUserIdFiedList(id, "' . $tableElement . '");

			checkUserListModification("' . $tableElement . '", "' . $idBoutonEnregistrer . '");
		});

		evarisk("#affectedUser' . $tableElement . '").autocomplete("' . EVA_INC_PLUGIN_URL . 'searchUsers.php");
		evarisk("#affectedUser' . $tableElement . '").result(function(event, data, formatted){
			cleanUserIdFiedList(data[1], "' . $tableElement . '");
			addUserIdFieldList(data[0], data[1], "' . $tableElement . '");

			checkUserListModification("' . $tableElement . '", "' . $idBoutonEnregistrer . '");

			evarisk("#affectedUser' . $tableElement . '").val("' . __('Rechercher dans la liste des utilisateurs', 'evarisk') . '");
		});
	});
</script>';

		{//Bouton Enregistrer
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

			$utilisateursMetaBox .= '<div class="clear" ><div id="saveButtonLoading' . $tableElement . '" style="display:none;" class="clear alignright" ><img src="' . PICTO_LOADING_ROUND . '" alt="loading in progress" /></div><div id="saveButtonContainer' . $tableElement . '" >' . EvaDisplayInput::afficherInput('button', $idBoutonEnregistrer, __('Enregistrer', 'evarisk'), null, '', 'save', false, true, '', 'button-primary alignright', '', '', $scriptEnregistrement) . '</div></div>';
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
	function setLinkUserElement($tableElement, $idElement, $userIdList)
	{
		global $wpdb;
		global $current_user;
		$userToTreat = "  ";

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

		$newUserList = explode(", ", $userIdList);

		foreach($listeUtilisateursLies as $utilisateurs)
		{
			if(is_array($newUserList) && !in_array($utilisateurs->id_user, $newUserList))
			{
				$userToTreat .= "('" . $utilisateurs->id . "', 'deleted', '" . $utilisateurs->date_affectation . "', '" . $utilisateurs->id_attributeur . "', NOW(), '" . $current_user->ID . "', '" . $utilisateurs->id_user . "', '" . $idElement . "', '" . $tableElement . "'), ";
			}
		}
		if(is_array($newUserList) && (count($newUserList) > 0))
		{
			foreach($newUserList as $userId)
			{
				if((trim($userId) != '') && !array_key_exists($userId, $listeUtilisateursLies))
				{
					$userToTreat .= "('', 'valid', NOW(), '" . $current_user->ID . "', '0000-00-00 00:00:00', '', '" . $userId . "', '" . $idElement . "', '" . $tableElement . "'), ";
				}
			}
		}

		$message = addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Il n\'a aucune modification a apporter', 'evarisk') . '</strong></p>');
		$endOfQuery = trim(substr($userToTreat, 0, -2));
		if($endOfQuery != "")
		{
			$query = $wpdb->prepare(
				"REPLACE INTO " . TABLE_LIAISON_USER_ELEMENT . "
					(id, status ,date_affectation ,id_attributeur ,date_desAffectation ,id_desAttributeur ,id_user ,id_element ,table_element)
				VALUES 
					" . $endOfQuery
			);
			if($wpdb->query($query))
			{
				$message = addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Les modifications ont correctement &eacute;t&eacute enregistr&eacute;es', 'evarisk') . '</strong></p>');
			}
			else
			{
				$message = addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Les modifications n\'ont pas toutes &eacute;t&eacute correctement enregistr&eacute;es', 'evarisk') . '</strong></p>"');
			}
		}

		echo 
'<script type="text/javascript">
	evarisk(document).ready(function(){
		actionMessageShow("#messageInfo_' . $tableElement . $idElement . '_affectUser", "' . $message . '");
		setTimeout(\'actionMessageHide("#messageInfo_' . $tableElement . $idElement . '_affectUser")\',7500);
		evarisk("#saveButtonLoading' . $tableElement . '").hide();
		evarisk("#saveButtonContainer' . $tableElement . '").show();
		evarisk("#actuallyAffectedUserIdList' . $tableElement . '").val(evarisk("#affectedUserIdList' . $tableElement . '").val());
		checkUserListModification("' . $tableElement . '", "save_group' . $tableElement . '");
	});
</script>';
	}

}