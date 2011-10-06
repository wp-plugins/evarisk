<?php
/*
 * @version v5.0
 */
 
 
//Postbox definition
$postBoxTitle = __('Informations G&eacute;n&eacute;rales', 'evarisk');
$postBoxId = 'postBoxGeneralInformation';
$postBoxCallbackFunction = 'getActivityGeneralInformationPostBoxBody';
add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_ACTIVITE, 'rightSide', 'default');
require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/activite/evaActivity.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayInput.class.php' );

function getActivityGeneralInformationPostBoxBody($arguments)
{
	$postId = '';
	if($arguments['idElement'] != null)
	{
		$postId = $arguments['idElement'];
		$activite = new EvaActivity($postId);
		$activite->load();
		$contenuInputTitre = $activite->getName();
		$contenuInputDescription = $activite->getDescription();
		$contenuInputDateDebut = $activite->getStartDate();
		$contenuInputDateFin = $activite->getFinishDate();
		$contenuInputCout = $activite->getCout();
		$contenuInputAvancement = $activite->getProgression();
		$contenuInputResponsable = $activite->getidResponsable();
		$contenuInputRealisateur = $activite->getidSoldeur();
		$ProgressionStatus = $activite->getProgressionStatus();
		$firstInsert = $activite->getFirstInsert();
		$grise = false;
		$idPere = $activite->getRelatedTaskId();
		$saveOrUpdate = 'update';
	}
	else
	{
		$contenuInputTitre = '';
		$contenuInputDescription = '';
		$contenuInputDateDebut = date('Y-m-d');
		$contenuInputDateFin = date('Y-m-d');
		$contenuInputRealisateur = '';
		$contenuInputResponsable = '';
		$ProgressionStatus = '';
		$firstInsert = '';
		$contenuInputAvancement = 0;
		$idPere = $arguments['idPere'];
		$grise = true;
		$saveOrUpdate = 'save';
	}

	$idForm = 'informationGeneralesActivite';
	$activite_new = EvaDisplayInput::ouvrirForm('POST', $idForm, $idForm);
	{//Champs cachés
		$activite_new = $activite_new . EvaDisplayInput::afficherInput('hidden', 'act_activite', $saveOrUpdate, '', null, 'act', false, false);
		$activite_new = $activite_new . EvaDisplayInput::afficherInput('hidden', 'affichage_activite', $arguments['affichage'], '', null, 'affichage', false, false);
		$activite_new = $activite_new . EvaDisplayInput::afficherInput('hidden', 'table_activite', TABLE_ACTIVITE, '', null, 'table', false, false);
		$activite_new = $activite_new . EvaDisplayInput::afficherInput('hidden', 'id_activite', $postId, '', null, 'id', false, false);
		$activite_new = $activite_new . EvaDisplayInput::afficherInput('hidden', 'idPere_activite', $idPere, '', null, 'idPere', false, false);
		$activite_new = $activite_new . EvaDisplayInput::afficherInput('hidden', 'idsFilAriane_activite', $arguments['idsFilAriane'], '', null, 'idsFilAriane', false, false);
		$activite_new = $activite_new . EvaDisplayInput::afficherInput('hidden', 'idProvenance_activite', $idProvenance, '', null, 'idProvenance', false, false);
		$activite_new = $activite_new . EvaDisplayInput::afficherInput('hidden', 'tableProvenance_activite', $tableProvenance, '', null, 'tableProvenance', false, false);
	}
	{//Nom de l'action
		$contenuAideTitre = "";
		$labelInput = ucfirst(sprintf(__("nom %s", 'evarisk'), __("de l'action",'evarisk'))) . '&nbsp;<span class="fieldInfo required" >' . __('(obligatoire)', 'evarisk') . '</span> :';
		$nomChamps = "nom_activite";
		$idTitre = "nom_activite";
		$activite_new = $activite_new . EvaDisplayInput::afficherInput('text', $idTitre, html_entity_decode($contenuInputTitre, ENT_NOQUOTES, 'UTF-8'), $contenuAideTitre, $labelInput, $nomChamps, $grise, true, 255, 'titleInput');
	}
	{//Date de début de l'action
		if($firstInsert != ''){
			$activite_new .='<br/>' . __('Ajout&eacute;e le', 'evarisk') . '&nbsp;' . mysql2date('d M Y', $firstInsert, true) . '<br/><br/>';
		}
		$contenuAideTitre = "";
		$id = "date_debut_activite";
		$label = '<label for="' . $id . '" >' . ucfirst(sprintf(__("Date de d&eacute;but %s", 'evarisk'), __("de l'action",'evarisk'))) . '</label> : <span class="fieldInfo pointer" id="putTodayActionStart" >' . __('Aujourd\'hui', 'evarisk') . '</span>';
		$labelInput = '';
		$nomChamps = "date_debut";
		$activite_new .= $label . EvaDisplayInput::afficherInput('text', $id, $contenuInputDateDebut, $contenuAideTitre, $labelInput, $nomChamps, $grise, true, 255, '', 'date') . '';
	}
	{//Date de début de l'action
		$contenuAideTitre = "";
		$id = "date_fin_activite";
		$label = '<label for="' . $id . '" >' . ucfirst(sprintf(__("Date de fin %s", 'evarisk'), __("de l'action",'evarisk'))) . '</label> : <span class="fieldInfo pointer" id="putTodayActionEnd" >' . __('Aujourd\'hui', 'evarisk') . '</span>';
		$labelInput = '';
		$nomChamps = "date_fin";
		$activite_new .= $label . EvaDisplayInput::afficherInput('text', $id, $contenuInputDateFin, $contenuAideTitre, $labelInput, $nomChamps, $grise, true, 255, '', 'date') . '';
	}
	{//Cout
		$contenuAideDescription = "";
		$labelInput = __("Co&ucirc;t", 'evarisk') . ' : ';
		$id = "cout_activite";
		$nomChamps = "cout";
		$activite_new .= EvaDisplayInput::afficherInput('text', $id, $contenuInputCout, $contenuAideDescription, $labelInput, $nomChamps, $grise, true, 255, '100%', 'float');
	}
	{//Avancement
		$contenuAideDescription = "";
		$labelInput = __("Avancement", 'evarisk') . ' : ';
		$id = "avancement_activite";
		$nomChamps = "avancement";
		$activite_new .= EvaDisplayInput::afficherInput('text', $id, $contenuInputAvancement, $contenuAideDescription, $labelInput, $nomChamps, true, true, 3, '', 'number', '10%') . '<div id="sliderAvancement" ></div><script type="text/javascript" >
	evarisk(function() {
		evarisk( "#sliderAvancement" ).slider({
			value:' . $contenuInputAvancement . ',
			min: 0,
			max: 100,
			step: 1,
			slide: function( event, ui ) {
				evarisk( "#' . $id . '" ).val( ui.value );
			}
		});
		evarisk( "#' . $id . '" ).val( evarisk( "#sliderAvancement" ).slider( "value" ) );
		evarisk( "#' . $id . '" ).attr("style",evarisk( "#' . $id . '" ).attr("style") + "border:0px solid #000000;");
;
	});
	</script>';
	}
	{//Responsable
		$contenuAideDescription = "";		
		$labelInput = __("Responsable", 'evarisk');
		if(digirisk_options::getOptionValue('responsable_Action_Obligatoire') == 'oui')
		{
			$labelInput .= '&nbsp;<span class="fieldInfo required" >' . __('(obligatoire)', 'evarisk') . '</span>';
		}
		$labelInput .= ' : <span class="fieldInfo" >' . sprintf(__('(vous pouvez d&eacute;finir si ce champs est obligatoire ou non dans le menu %s du plugin)', 'evarisk'), '<a href="' . get_bloginfo('siteurl') . '/wp-admin/options-general.php?page=' . DIGI_URL_SLUG_MAIN_OPTION . '#digirisk_options_correctivaction" target="optionPage" >' . __('Options', 'evarisk') . '</a>') . '</span>'; 
		$id = "responsable_activite";
		$nomChamps = "responsable_activite";

		// $tmpListeUtilisateurs = $listeUtilisateurs = $tabValue = $tabDisplay = $selectedUser = array();
		// $tmpListeUtilisateurs = evaUser::getCompleteUserList();
		// $listeUtilisateurs[0] = '';
		// $tabValue[0] = '0';
		// $tabDisplay[0] = __('Choisissez...', 'evarisk');
		// $i=1;
		// foreach($tmpListeUtilisateurs as $idUtilisateur => $informationsUtilisateurs)
		// {
			// $listeUtilisateurs[] = $informationsUtilisateurs;
			// if($idUtilisateur > 0)
			// {
				// $tabValue[$i] = $idUtilisateur;
				// $tabDisplay[$i] = $informationsUtilisateurs['user_lastname'] . ' ' . $informationsUtilisateurs['user_firstname'];
				// if($idUtilisateur == $contenuInputResponsable)
				// {
					// $selectedUser = $informationsUtilisateurs;
				// }
				// $i++;
			// }
		// }
		// EvaDisplayInput::afficherComboBox($listeUtilisateurs, $id, $labelInput, $nomChamps, '', $selectedUser, $tabValue, $tabDisplay);

		$activite_new .= '<br/><label for="search_user_responsable_' . $arguments['tableElement'] . '" >' . $labelInput . '</label>' . EvaDisplayInput::afficherInput('hidden', $id, $contenuInputResponsable, '', null, $nomChamps, false, false) . '<div id="responsible_name" >';
		$search_input_state = '';
		$change_input_state = 'hide';
		if($contenuInputResponsable > 0){
			$search_input_state = 'hide';
			$change_input_state = '';
			$responsible = evaUser::getUserInformation($contenuInputResponsable);
			$activite_new .= ELEMENT_IDENTIFIER_U . $contenuInputResponsable . '&nbsp;-&nbsp;' . $responsible[$contenuInputResponsable]['user_lastname'] . ' ' . $responsible[$contenuInputResponsable]['user_firstname'];
		}
		else{
			$activite_new .= '&nbsp;';
		}
		$activite_new .= '</div>&nbsp;<span id="change_responsible_' . $arguments['tableElement'] . 'responsible" class="' . $change_input_state . ' change_ac_responsible" >' . __('Changer', 'evarisk') . '</span><input class="searchUserToAffect ac_responsable ' . $search_input_state . '" type="text" name="responsable_name_' . $arguments['tableElement'] . '" id="search_user_responsable_' . $arguments['tableElement'] . '" value="' . __('Rechercher dans la liste des utilisateurs', 'evarisk') . '" /><div id="completeUserList' . $arguments['tableElement'] . 'responsible" class="completeUserList completeUserListActionResponsible hide clear" >' . evaUser::afficheListeUtilisateurTable_SimpleSelection($arguments['tableElement'] . 'responsible', $arguments['idElement']) . '</div>
<script type="text/javascript" >
	evarisk(document).ready(function(){
		jQuery("#search_user_responsable_' . $arguments['tableElement'] . '").click(function(){
			jQuery(this).val("");
			jQuery(".completeUserListActionResponsible").show();
		});
		jQuery("#search_user_responsable_' . $arguments['tableElement'] . '").blur(function(){
			jQuery(this).val(convertAccentToJS("' . __('Rechercher dans la liste des utilisateurs', 'evarisk') . '"));
		});
		jQuery("#search_user_responsable_' . $arguments['tableElement'] . '").autocomplete("' . EVA_INC_PLUGIN_URL . 'liveSearch/searchUsers.php?table_element=' . $tableElement . '&id_element=' . $arguments['idElement'] . '");
		jQuery("#search_user_responsable_' . $arguments['tableElement'] . '").result(function(event, data, formatted){
			jQuery("#responsable_activite").val(data[1]);
			jQuery("#responsible_name").html(data[0]);
			jQuery("#search_user_responsable_' . $arguments['tableElement'] . '").val(convertAccentToJS("' . __('Rechercher dans la liste des utilisateurs', 'evarisk') . '"));
			jQuery(".completeUserListActionResponsible").hide();
			jQuery(".searchUserToAffect").hide();
			jQuery("#change_responsible_' . $arguments['tableElement'] . 'responsible").show();
		});
		jQuery("#change_responsible_' . $arguments['tableElement'] . 'responsible").click(function(){
			jQuery("#search_user_responsable_' . $arguments['tableElement'] . '").show();
			jQuery("#completeUserList' . $arguments['tableElement'] . 'responsible").show();
			jQuery(this).hide();
		});
	});
</script><br class="clear" /><br/><br/><br/>';
	}
	{//Description
		$contenuAideDescription = "";
		$labelInput = __("Description", 'evarisk') . ' : ';
		$id = "description_activite";
		$nomChamps = "description";
		$rows = 5;
		$activite_new .= '<br/>' . EvaDisplayInput::afficherInput('textarea', $id, $contenuInputDescription, $contenuAideDescription, $labelInput, $nomChamps, $grise, true, $rows);
	}
	{//Bouton Mettre en cours
		$idBouttonSetInProgress = 'setActivityInProgress';
		$scriptEnregistrementInProgress = '<script type="text/javascript">
			evarisk("#' . $idBouttonSetInProgress . '").click(function(){
				evarisk("#inProgressButtonContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",
				{
					"post": "true", 
					"table": "' . TABLE_ACTIVITE . '",
					"act": "setActivityInProgress",
					"id": evarisk("#id_activite").val()
				});
			});
		</script>';
	}
	{//Bouton Enregistrer
		$idBouttonEnregistrer = 'save_activite';

		/*	Check if the user in charge of the action are mandatory */
		$idResponsableIsMandatory = digirisk_options::getOptionValue('responsable_Action_Obligatoire');

		$scriptEnregistrementSave = '<script type="text/javascript">
			evarisk(document).ready(function() {
				evarisk("#putTodayActionStart").click(function(){
					evarisk("#date_debut_activite").val("' . date('Y-m-d') . '");
				});
				evarisk("#putTodayActionEnd").click(function(){
					evarisk("#date_fin_activite").val("' . date('Y-m-d') . '");
				});

				evarisk(\'#' . $idBouttonEnregistrer . '\').click(function() {
					if(evarisk(\'#' . $idTitre . '\').is(".form-input-tip"))
					{
						document.getElementById(\'' . $idTitre . '\').value=\'\';
						evarisk(\'#' . $idTitre . '\').removeClass(\'form-input-tip\');
					}

					idResponsable = evarisk("#responsable_activite").val();
					idResponsableIsMandatory = "false";
					idResponsableIsMandatory = "' . $idResponsableIsMandatory . '";

					valeurActuelle = evarisk("#' . $idTitre . '").val();
					if(valeurActuelle == "")
					{
						alert(convertAccentToJS("' . __("Vous n\'avez pas donne de nom a l'action", 'evarisk') . '"));
					}
					else if(((idResponsable <= "0") ||(idResponsable == "")) && (idResponsableIsMandatory == "oui"))
					{
						alert(convertAccentToJS("' . __("Vous devez choisir une personne en charge de l\'action", 'evarisk') . '"));
					}
					else
					{
						evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
							"table": "' . TABLE_ACTIVITE . '",
							"act": evarisk("#act_activite").val(),
							"id": evarisk("#id_activite").val(),
							"nom_activite": evarisk("#nom_activite").val(),
							"date_debut": evarisk("#date_debut_activite").val(),
							"date_fin": evarisk("#date_fin_activite").val(),
							"idPere": evarisk("#idPere_activite").val(),
							"description": evarisk("#description_activite").val(),
							"affichage": evarisk("#affichage_activite").val(),
							"cout": evarisk("#cout_activite").val(),
							"avancement": evarisk("#avancement_activite").val(),
							"responsable_activite": evarisk("#responsable_activite").val(),
							"idsFilAriane": evarisk("#idsFilAriane_activite").val(),
							"idProvenance": evarisk("#idProvenance_activite").val(),
							"tableProvenance": evarisk("#tableProvenance_activite").val()
						});
					}
				});
			});
			</script>';
	}
	{//Bouton Solder
		$idBoutton = 'actionDone';

		/*	Check if the user in charge of the action are mandatory */
		$alertWhenMarkActionAsDone = digirisk_options::getOptionValue('avertir_Solde_Action_Non_100');

		$scriptEnregistrementDone = '<script type="text/javascript">
			evarisk(document).ready(function() {				
				evarisk(\'#' . $idBoutton . '\').click(function() {
					if(evarisk(\'#' . $idTitre . '\').is(".form-input-tip"))
					{
						document.getElementById(\'' . $idTitre . '\').value=\'\';
						evarisk(\'#' . $idTitre . '\').removeClass(\'form-input-tip\');
					}

					idResponsable = evarisk("#responsable_activite").val();
					idResponsableIsMandatory = "false";
					idResponsableIsMandatory = "' . $idResponsableIsMandatory . '";
					alertWhenMarkActionAsDone = "non";
					alertWhenMarkActionAsDone = "' . $alertWhenMarkActionAsDone . '";

					valeurActuelle = evarisk("#' . $idTitre . '").val();
					if(valeurActuelle == "")
					{
						alert(convertAccentToJS("' . __("Vous n\'avez pas donne de nom a l'action", 'evarisk') . '"));
					}
					else if(((idResponsable <= "0") ||(idResponsable == "")) && (idResponsableIsMandatory == "oui"))
					{
						alert(convertAccentToJS("' . __("Vous devez choisir une personne en charge de l\'action", 'evarisk') . '"));
					}
					else if(
						(
							(evarisk("#avancement_activite").val() == "100") && (' . $contenuInputAvancement . ' == "100") 
						)
						||
							(alertWhenMarkActionAsDone == "non")
						||
						(
							(alertWhenMarkActionAsDone == "oui")
							&&
							confirm((convertAccentToJS("' . __("Vous &eacute;tes sur le point de solder une action dont l\'avancement est de #avancement#%.#retour#Etes vous sur de vouloir continuer ?", 'evarisk') . '").replace("#avancement#", evarisk("#avancement_activite").val())).replace("#retour#", "\r\n"))
						)
					)
					{
						evarisk("#actionDone").html(\'<img src="' . PICTO_LOADING_ROUND . '" alt="loading" />\');
						evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
							"table": "' . TABLE_ACTIVITE . '",
							"act": "actionDone",
							"id": evarisk("#id_activite").val(),
							"nom_activite": evarisk("#nom_activite").val(),
							"date_debut": evarisk("#date_debut_activite").val(),
							"date_fin": evarisk("#date_fin_activite").val(),
							"idPere": evarisk("#idPere_activite").val(),
							"description": evarisk("#description_activite").val(),
							"affichage": evarisk("#affichage_activite").val(),
							"cout": evarisk("#cout_activite").val(),
							"avancement": evarisk("#avancement_activite").val(),
							"responsable_activite": evarisk("#responsable_activite").val(),
							"idsFilAriane": evarisk("#idsFilAriane_activite").val(),
							"idProvenance": evarisk("#idProvenance_activite").val(),
							"tableProvenance": evarisk("#tableProvenance_activite").val()
						});
					}
				});
			});
			</script>';
	}
	{//Boutons
		$inProgressButton = '';
		if(($saveOrUpdate == 'update') && ($ProgressionStatus != '') && ($ProgressionStatus != 'inProgress') && ($contenuInputAvancement != '100'))
		{
			$inProgressButton = '<span id="inProgressButtonContainer" class="alignleft" >' . EvaDisplayInput::afficherInput('button', $idBouttonSetInProgress, __('Passer en cours', 'evarisk'), null, '', $idBouttonSetInProgress, false, true, '', 'button-primary', '', '', $scriptEnregistrementInProgress, 'left') . '</span>';
		}
		if(($saveOrUpdate == 'save') || ($ProgressionStatus == '') || ($ProgressionStatus == 'inProgress') || ($ProgressionStatus == 'notStarted') || (digirisk_options::getOptionValue('possibilite_Modifier_Action_Soldee') == 'oui'))
		{
			$activite_new .= 
				'<div class="alignright" id="ActionSaveButton" >' . $inProgressButton;

			if(($saveOrUpdate == 'update') && (($ProgressionStatus == '') || ($ProgressionStatus == 'notStarted') || ($ProgressionStatus == 'inProgress')))
			{
			$activite_new .= 
					EvaDisplayInput::afficherInput('button', $idBoutton, __('Solder l\'action', 'evarisk'), null, '', $idBoutton, false, true, '', 'button-primary', '', '', $scriptEnregistrementDone, 'left');
			}
			elseif($saveOrUpdate == 'update')
			{
			$activite_new .= 
					'<div style="float:left;" id="ActionSaveButton" >
						<br/>
						<div class="alignright button-primary" >' . 
							__('Cette action est sold&eacute;e', 'evarisk') . 
						'</div>
					</div>';
			}

			$activite_new .= 
					EvaDisplayInput::afficherInput('button', $idBouttonEnregistrer, __('Enregistrer', 'evarisk'), null, '', $idBouttonEnregistrer, false, true, '', 'button-primary', '', '', $scriptEnregistrementSave, 'left') . 
				'</div>
				<script type="text/javascript" >evarisk("#ActionSaveButton").children("br").remove();</script>';
		}
		else
		{
			$activite_new .= 
				'<div class="alignright button-primary" id="ActionSaveButton" >' . 
					__('Cette action est sold&eacute;e, vous ne pouvez pas la modifier', 'evarisk') . 
				'</div>';
		}
	}
	
	$activite_new .= EvaDisplayInput::fermerForm($idForm);

	echo $activite_new;
}

?>