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
		$contenuInputAvancement = 0;
		$idPere = $arguments['idPere'];
		$grise = true;
		$saveOrUpdate = 'save';
	}

	$idForm = 'informationGeneralesActivite';
	$activite_new = EvaDisplayInput::ouvrirForm('POST', $idForm, $idForm);
	{//Champs cach�s
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
		$activite_new = $activite_new . EvaDisplayInput::afficherInput('text', $idTitre, $contenuInputTitre, $contenuAideTitre, $labelInput, $nomChamps, $grise, true, 255, 'titleInput');
	}
	{//Date de d�but de l'action
		$contenuAideTitre = "";
		$id = "date_debut_activite";
		$label = '<label for="' . $id . '" >' . ucfirst(sprintf(__("Date de d&eacute;but %s", 'evarisk'), __("de l'action",'evarisk'))) . '</label> : <span class="fieldInfo pointer" id="putTodayActionStart" >' . __('Aujourd\'hui', 'evarisk') . '</span>';
		$labelInput = '';
		$nomChamps = "date_debut";
		$activite_new .= $label . EvaDisplayInput::afficherInput('text', $id, $contenuInputDateDebut, $contenuAideTitre, $labelInput, $nomChamps, $grise, true, 255, '', 'date') . '';
	}
	{//Date de d�but de l'action
		$contenuAideTitre = "test";
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
	$(function() {
		$( "#sliderAvancement" ).slider({
			value:' . $contenuInputAvancement . ',
			min: 0,
			max: 100,
			step: 1,
			slide: function( event, ui ) {
				$( "#' . $id . '" ).val( ui.value );
			}
		});
		$( "#' . $id . '" ).val( $( "#sliderAvancement" ).slider( "value" ) );
		$( "#' . $id . '" ).attr("style",$( "#' . $id . '" ).attr("style") + "border:0px solid #000000;");
;
	});
	</script>';
	}
	{//Responsable
		$contenuAideDescription = "";		
		$labelInput = __("Responsable", 'evarisk');
		if(options::getOptionValue('responsable_Action_Obligatoire') == 'oui')
		{
			$labelInput .= '&nbsp;<span class="fieldInfo required" >' . __('(obligatoire)', 'evarisk') . '</span>';
		}
		$labelInput .= ' : <span class="fieldInfo" >' . sprintf(__('(vous pouvez d&eacute;finir si ce champs est obligatoire ou non dans le menu %s du plugin)', 'evarisk'), '<a href="' . get_bloginfo('siteurl') . '/wp-admin/admin.php?page=' . EVA_PLUGIN_DIR .'/include/modules/options/options.php" target="optionPage" >' . __('Options', 'evarisk') . '</a>') . '</span>'; 
		$id = "responsable_activite";
		$nomChamps = "responsable_activite";

		$tmpListeUtilisateurs = $listeUtilisateurs = $tabValue = $tabDisplay = $selectedUser = array();
		$tmpListeUtilisateurs = evaUser::getCompleteUserList();
		$listeUtilisateurs[0] = '';
		$tabValue[0] = '0';
		$tabDisplay[0] = __('Choisissez...', 'evarisk');
		$i=1;
		foreach($tmpListeUtilisateurs as $idUtilisateur => $informationsUtilisateurs)
		{
			$listeUtilisateurs[] = $informationsUtilisateurs;
			if($idUtilisateur > 0)
			{
				$tabValue[$i] = $idUtilisateur;
				$tabDisplay[$i] = $informationsUtilisateurs['user_lastname'] . ' ' . $informationsUtilisateurs['user_firstname'];
				if($idUtilisateur == $contenuInputResponsable)
				{
					$selectedUser = $informationsUtilisateurs;
				}
				$i++;
			}
		}
		$activite_new .= '<br/>' . EvaDisplayInput::afficherComboBox($listeUtilisateurs, $id, $labelInput, $nomChamps, '', $selectedUser, $tabValue, $tabDisplay);
	}
	{//Description
		$contenuAideDescription = "";
		$labelInput = __("Description", 'evarisk') . ' : ';
		$id = "description_activite";
		$nomChamps = "description";
		$rows = 5;
		$activite_new = $activite_new . EvaDisplayInput::afficherInput('textarea', $id, $contenuInputDescription, $contenuAideDescription, $labelInput, $nomChamps, $grise, true, $rows);
	}
	{//Bouton Enregistrer
		$idBouttonEnregistrer = 'save_activite';

		/*	Check if the user in charge of the action are mandatory */
		$idResponsableIsMandatory = options::getOptionValue('responsable_Action_Obligatoire');

		$scriptEnregistrementSave = '<script type="text/javascript">
			$(document).ready(function() {
				$("#putTodayActionStart").click(function(){
					$("#date_debut_activite").val("' . date('Y-m-d') . '");
				});
				$("#putTodayActionEnd").click(function(){
					$("#date_fin_activite").val("' . date('Y-m-d') . '");
				});

				$(\'#' . $idBouttonEnregistrer . '\').click(function() {
					if($(\'#' . $idTitre . '\').is(".form-input-tip"))
					{
						document.getElementById(\'' . $idTitre . '\').value=\'\';
						$(\'#' . $idTitre . '\').removeClass(\'form-input-tip\');
					}

					idResponsable = $("#responsable_activite").val();
					idResponsableIsMandatory = "false";
					idResponsableIsMandatory = "' . $idResponsableIsMandatory . '";

					valeurActuelle = $("#' . $idTitre . '").val();
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
						$("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
							"table": "' . TABLE_ACTIVITE . '",
							"act": $("#act_activite").val(),
							"id": $("#id_activite").val(),
							"nom_activite": $("#nom_activite").val(),
							"date_debut": $("#date_debut_activite").val(),
							"date_fin": $("#date_fin_activite").val(),
							"idPere": $("#idPere_activite").val(),
							"description": $("#description_activite").val(),
							"affichage": $("#affichage_activite").val(),
							"cout": $("#cout_activite").val(),
							"avancement": $("#avancement_activite").val(),
							"responsable_activite": $("#responsable_activite").val(),
							"idsFilAriane": $("#idsFilAriane_activite").val(),
							"idProvenance": $("#idProvenance_activite").val(),
							"tableProvenance": $("#tableProvenance_activite").val()
						});
					}
				});
			});
			</script>';
	}
	{//Bouton Solder
		$idBoutton = 'actionDone';

		/*	Check if the user in charge of the action are mandatory */
		$alertWhenMarkActionAsDone = options::getOptionValue('avertir_Solde_Action_Non_100');

		$scriptEnregistrementDone = '<script type="text/javascript">
			$(document).ready(function() {				
				$(\'#' . $idBoutton . '\').click(function() {
					if($(\'#' . $idTitre . '\').is(".form-input-tip"))
					{
						document.getElementById(\'' . $idTitre . '\').value=\'\';
						$(\'#' . $idTitre . '\').removeClass(\'form-input-tip\');
					}

					idResponsable = $("#responsable_activite").val();
					idResponsableIsMandatory = "false";
					idResponsableIsMandatory = "' . $idResponsableIsMandatory . '";
					alertWhenMarkActionAsDone = "non";
					alertWhenMarkActionAsDone = "' . $alertWhenMarkActionAsDone . '";

					valeurActuelle = $("#' . $idTitre . '").val();
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
							($("#avancement_activite").val() == "100") && (' . $contenuInputAvancement . ' == "100") 
						)
						||
							(alertWhenMarkActionAsDone == "non")
						||
						(
							(alertWhenMarkActionAsDone == "oui")
							&&
							confirm((convertAccentToJS("' . __("Vous &eacute;tes sur le point de solder une action dont l\'avancement est de #avancement#%.#retour#Etes vous sur de vouloir continuer ?", 'evarisk') . '").replace("#avancement#", $("#avancement_activite").val())).replace("#retour#", "\r\n"))
						)
					)
					{
						$("#actionDone").html(\'<img src="' . PICTO_LOADING_ROUND . '" alt="loading" />\');
						$("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
							"table": "' . TABLE_ACTIVITE . '",
							"act": "actionDone",
							"id": $("#id_activite").val(),
							"nom_activite": $("#nom_activite").val(),
							"date_debut": $("#date_debut_activite").val(),
							"date_fin": $("#date_fin_activite").val(),
							"idPere": $("#idPere_activite").val(),
							"description": $("#description_activite").val(),
							"affichage": $("#affichage_activite").val(),
							"cout": $("#cout_activite").val(),
							"avancement": $("#avancement_activite").val(),
							"responsable_activite": $("#responsable_activite").val(),
							"idsFilAriane": $("#idsFilAriane_activite").val(),
							"idProvenance": $("#idProvenance_activite").val(),
							"tableProvenance": $("#tableProvenance_activite").val()
						});
					}
				});
			});
			</script>';
	}
	{//Boutons
		if(($saveOrUpdate == 'save') || ($ProgressionStatus == '') || ($ProgressionStatus == 'inProgress') || (options::getOptionValue('possibilite_Modifier_Action_Soldee')== 'oui'))
		{
			$activite_new .= 
				'<div class="alignright" id="ActionSaveButton" >';

			if(($saveOrUpdate == 'update') && (($ProgressionStatus == '') || ($ProgressionStatus == 'inProgress')))
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
				<script type="text/javascript" >$("#ActionSaveButton").children("br").remove();</script>';
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