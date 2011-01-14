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
	*	@param boolean $noScript Define if all datatable options must be shown or just the table
	*
	*	@return mixed $utilisateursMetaBox The entire html code to output
	*/
	function afficheListeUtilisateur($tableElement, $idElement, $noScript = false)
	{
		$utilisateursMetaBox = '';

		$utilisateursMetaBox .= '<div id="message' . TABLE_LIAISON_USER_ELEMENT . '" class="updated fade" style="cursor:pointer; display:none;"></div>';
		{//Création dataTable
			$idTable = 'listeIndividus' . $tableElement . $idElement;
			$titres = array( __('&nbsp;', 'evarisk'), __('Affect&eacute;', 'evarisk'), ucfirst(strtolower(sprintf(__('Nom %s', 'evarisk'), __('de l\'utilisateur', 'evarisk')))), ucfirst(strtolower(sprintf(__('Pr&eacute;nom %s', 'evarisk'), __('de l\'utilisateur', 'evarisk')))));
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
					$valeurs[] = array('value'=>evaDisplayInput::afficherInput('checkbox', $idCbLigne, null, null, null, 'cb_' . $idLigne));
					$affectedValue = __('non', 'evarisk');
					if(isset($listeUtilisateursLies[$utilisateur['user_id']]))
					{
						$script = $script . '<script type="text/javascript">
							evarisk(document).ready(function() {
								evarisk(\'#' . $idCbLigne . '\').attr("checked", "checked");
							});
						</script>';
						$affectedValue = __('oui', 'evarisk');
					}
					$valeurs[] = array('value'=>$affectedValue);
					$valeurs[] = array('value'=>$utilisateur['user_lastname']);
					$valeurs[] = array('value'=>$utilisateur['user_firstname']);
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
				$lignesDeValeurs[] = $valeurs;
				$idLignes[] = $tableElement . $idElement . 'listeUtilisateursVide';
			}

			$classes = array('cbColumnLarge','','','cbNbUserGroup');
			if(!$noScript)
			{
			$script = $script . '<script type="text/javascript">
				evarisk(document).ready(function() {
					evarisk(\'#' . $idTable . '\').dataTable({
						"bAutoWidth": false,
						"bInfo": false,
						"aoColumns": 
						[
							{ "bSortable": false },
							{ "bSortable": true },
							{ "bSortable": true },
							{ "bSortable": true }
						],
						"aaSorting": [[1,\'desc\']]});
					evarisk(\'#' . $idTable . '\').children("tfoot").remove();
				});
			</script>';
			}
			$utilisateursMetaBox .= evaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script);
		}
		{//Bouton Enregistrer
			$idBoutonEnregistrer = 'save_group';
			$scriptEnregistrement = '<script type="text/javascript">
				evarisk(document).ready(function() {				
					evarisk(\'#' . $idBoutonEnregistrer . '\').click(function() {		
						var listeUtilisateurs = new Array();
						evarisk(\'#' . $idTable . ' input\').each(function(){
							if(evarisk(this).attr("checked"))
							{
								listeUtilisateurs.push(evarisk(this).attr("id").replace(new RegExp(/[A-Za-z_\-]+[\d]+[A-Za-z_\-]+/), ""));
							}
						});
						evarisk("#userList' . $tableElement . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
							"table": "' . TABLE_LIAISON_USER_ELEMENT . '",
							"act": "save",
							"utilisateurs": listeUtilisateurs,
							"tableElement": "' . $tableElement . '",
							"idElement": "' . $idElement . '"
						});
					});
				});
				</script>';
			$boutonEnregistrer = EvaDisplayInput::afficherInput('button', $idBoutonEnregistrer, __('Enregistrer', 'evarisk'), null, '', 'save', false, true, '', 'button-primary alignright', '', '', $scriptEnregistrement);

			$displayButton = true;
			$displayedMessage = '';
			switch($tableElement)
			{
				case TABLE_TACHE:

					$displayedMessage = '<div class="alignright button-primary" id="TaskSaveUserListButton" >' . __('Cette t&acirc;che est sold&eacute;e, vous ne pouvez pas la modifier', 'evarisk') . '</div>';

					$currentTask = new EvaTask($idElement);
					$currentTask->load();
					$ProgressionStatus = $currentTask->getProgressionStatus();
					if( ($ProgressionStatus != '') && ($ProgressionStatus != 'inProgress') && (options::getOptionValue('possibilite_Modifier_Tache_Soldee') == 'non') )
					{
						$displayButton = false;
					}
				break;
				case TABLE_ACTIVITE:

					$displayedMessage = '<div class="alignright button-primary" id="TaskSaveUserListButton" >' . __('Cette action est sold&eacute;e, vous ne pouvez pas la modifier', 'evarisk') . '</div>';

					$currentActivity = new EvaActivity($idElement);
					$currentActivity->load();
					$ProgressionStatus = $currentActivity->getProgressionStatus();
					if( ($ProgressionStatus != '') && ($ProgressionStatus != 'inProgress') && (options::getOptionValue('possibilite_Modifier_Action_Soldee') == 'non') )
					{
						$displayButton = false;
					}
				break;
			}

			if($displayButton)
			{
				$utilisateursMetaBox .= $boutonEnregistrer;
			}
			else
			{
				$utilisateursMetaBox .= $displayedMessage;
			}
		}

		return $utilisateursMetaBox;
	}

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

		$deleteAlluser = true;
		if(is_array($userIdList) && (count($userIdList) > 0))
		{
			$deleteAlluser = false;
			foreach($userIdList as $userId)
			{
				if(!array_key_exists($userId, $listeUtilisateursLies))
				{
					$userToTreat .= "('', 'valid', NOW(), '" . $current_user->ID . "', '0000-00-00 00:00:00', '', '" . $userId . "', '" . $idElement . "', '" . $tableElement . "'), ";
				}
			}
		}

		foreach($listeUtilisateursLies as $utilisateurs)
		{
			if(is_array($userIdList) && !in_array($utilisateurs->id_user, $userIdList))
			{
				$userToTreat .= "('" . $utilisateurs->id . "', 'deleted', '" . $utilisateurs->date_affectation . "', '" . $utilisateurs->id_attributeur . "', NOW(), '" . $current_user->ID . "', '" . $utilisateurs->id_user . "', '" . $idElement . "', '" . $tableElement . "'), ";
			}
			elseif($deleteAlluser)
			{
				$userToTreat .= "('" . $utilisateurs->id . "', 'deleted', '" . $utilisateurs->date_affectation . "', '" . $utilisateurs->id_attributeur . "', NOW(), '" . $current_user->ID . "', '" . $utilisateurs->id_user . "', '" . $idElement . "', '" . $tableElement . "'), ";
			}
		}

		$messageInfo = 
'<script type="text/javascript">
	evarisk(document).ready(function(){
		evarisk("#messageInfo_' . $tableElement . $idElement . '_affectUser").addClass("updated");';

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
				$messageInfo .= '
		evarisk("#messageInfo_' . $tableElement . $idElement . '_affectUser").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Les modifications ont correctement &eacute;t&eacute enregistr&eacute;es', 'evarisk') . '</strong></p>') . '");';
			}
			else
			{
				$messageInfo .= '
		evarisk("#messageInfo_' . $tableElement . $idElement . '_affectUser").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Les modifications n\'ont pas toutes &eacute;t&eacute correctement enregistr&eacute;es', 'evarisk') . '</strong></p>"') . '");';
			}
		}
		else
		{
			$messageInfo .= '
		evarisk("#messageInfo_' . $tableElement . $idElement . '_affectUser").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Il n\'a aucune modification a apporter', 'evarisk') . '</strong></p>') . '");';
		}

		$messageInfo .= '
		evarisk("#messageInfo_' . $tableElement . $idElement . '_affectUser").show();
		setTimeout(function(){
			evarisk("#messageInfo_' . $tableElement . $idElement . '_affectUser").removeClass("updated");
			evarisk("#messageInfo_' . $tableElement . $idElement . '_affectUser").hide();
		},7500);
	});
</script>';
		echo $messageInfo;
	}

}