<?php
/*
* @version v5.0
*/


//Postbox definition
$postBoxTitle = __('Informations G&eacute;n&eacute;rales', 'evarisk');
$postBoxId = 'postBoxGeneralInformation';
$postBoxCallbackFunction = 'getSimpleActivityGeneralInformationPostBoxBody';
add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_ACTIVITE, 'rightSide', 'default');
require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/activite/evaActivity.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayInput.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'methode/methodeEvaluation.class.php' );

function getSimpleActivityGeneralInformationPostBoxBody($arguments)
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
		$saveOrUpdate = 'update-FAC';
	}
	else
	{
		$contenuInputTitre = '';
		$contenuInputDescription = '';
		$contenuInputDateDebut = date('Y-m-d');
		$contenuInputDateFin = date('Y-m-d');
		$contenuInputRealisateur = '';
		$contenuInputResponsable = '';
		$ProgressionStatus = 'Done';
		$contenuInputAvancement = 100;
		$idPere = $arguments['idPere'];
		$grise = true;
		$saveOrUpdate = 'addAction-FAC';
	}

	$idForm = 'informationGeneralesActivite';
	$idBouttonEnregistrer = 'save_activite';
	$activite_new = EvaDisplayInput::ouvrirForm('POST', $idForm, $idForm);
	{//Champs cach�s
		$activite_new .= EvaDisplayInput::afficherInput('hidden', 'act_activite', $saveOrUpdate, '', null, 'act', false, false);
		$activite_new .= EvaDisplayInput::afficherInput('hidden', 'affichage_activite', $arguments['affichage'], '', null, 'affichage', false, false);
		$activite_new .= EvaDisplayInput::afficherInput('hidden', 'table_activite', TABLE_ACTIVITE, '', null, 'table', false, false);
		$activite_new .= EvaDisplayInput::afficherInput('hidden', 'id_activite', $postId, '', null, 'id', false, false);
		$activite_new .= EvaDisplayInput::afficherInput('hidden', 'idPere_activite', $idPere, '', null, 'idPere', false, false);
		$activite_new .= EvaDisplayInput::afficherInput('hidden', 'idsFilAriane_activite', $arguments['idsFilAriane'], '', null, 'idsFilAriane', false, false);
		$activite_new .= EvaDisplayInput::afficherInput('hidden', 'idProvenance_activite', $idProvenance, '', null, 'idProvenance', false, false);
		$activite_new .= EvaDisplayInput::afficherInput('hidden', 'tableProvenance_activite', $tableProvenance, '', null, 'tableProvenance', false, false);
	}
	{//Nom de l'action
		$contenuAideTitre = "";
		$labelInput = ucfirst(sprintf(__("nom %s", 'evarisk'), __("de l'action",'evarisk'))) . '&nbsp;<span class="fieldInfo required" >' . __('(obligatoire)', 'evarisk') . '</span> :';
		$nomChamps = "nom_activite";
		$idTitre = "nom_activite";
		$activite_new .= EvaDisplayInput::afficherInput('text', $idTitre, $contenuInputTitre, $contenuAideTitre, $labelInput, $nomChamps, $grise, true, 255, 'titleInput');
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
		$activite_new .= 
'<div id="avancement" >' .
EvaDisplayInput::afficherInput('text', $id, $contenuInputAvancement, $contenuAideDescription, $labelInput, $nomChamps, true, true, 3, '', 'number', '10%') . '<div id="sliderAvancement" ></div>
<script type="text/javascript" >
	evarisk(document).ready(function(){
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
		evarisk("#avancement").hide();
	});
</script>
</div>';
	}
	{//Responsable
		$contenuAideDescription = "";
		$labelInput = __("Responsable", 'evarisk');
		if(digirisk_options::getOptionValue('responsable_Action_Obligatoire') == 'oui')
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
		$activite_new .= EvaDisplayInput::afficherInput('textarea', $id, $contenuInputDescription, $contenuAideDescription, $labelInput, $nomChamps, $grise, true, $rows);
	}
	{//Reevaluation du risque
		$activite_new .= '<fieldset style="display:table;margin:18px 0px;" >
		<legend class="bold" >' . __('Nouvelle &Eacute;valuation', 'evarisk') . ' :</legend>
		<script type="text/javascript">
				evarisk(document).ready(function(){
					setTimeout(function(){
					evarisk("#divVariablesFormRisque-simpleFAC").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
						"post":"true", 
						"table":"' . TABLE_METHODE . '", 
						"act":"reloadVariables-FAC",
						"idRisque":evarisk("#idProvenance_activite").val()
					});
					}, 700);
				})
			</script>
			<div id="divVariablesFormRisque-simpleFAC" ></div>
			</fieldset>';
	}
	{//Photos
		$activite_new .= '<div id="photosActionsCorrectives" >&nbsp;</div>';
		$addPictureButton = 
			'<input type="button" name="addFACPicture" id="addFACPicture" class="button-primary alignleft" value="' . __('Enregistrer puis Ajouter des photos', 'evarisk') . '" />
			<script type="text/javascript">
				evarisk(document).ready(function(){
					evarisk("#addFACPicture").click(function(){
						evarisk("#act_activite").val("addActionPhoto");
						evarisk("#' . $idBouttonEnregistrer . '").click();
					});
				});
			</script>';
	}
	{//Bouton Enregistrer
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

				evarisk("#' . $idBouttonEnregistrer . '").unbind("click");
				evarisk("#' . $idBouttonEnregistrer . '").click(function(){
					var variables = new Array;';
					$allVariables = MethodeEvaluation::getAllVariables();
					foreach($allVariables as $variable)
					{
						$scriptEnregistrementSave .= '
					variables["' . $variable->id . '"] = evarisk("#var' . $variable->id . 'FormRisque-FAC").val();';
					}
					$scriptEnregistrementSave .= '
					if(evarisk("#' . $idTitre . '").is(".form-input-tip"))
					{
						evarisk("#' . $idTitre . '").value = "";
						evarisk("#' . $idTitre . '").removeClass("form-input-tip");
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
						evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
						{
							"post": "true", 							
							"nom": evarisk("#act_activite").val(),
							"nom_activite": evarisk("#nom_activite").val(),
							"date_debut": evarisk("#date_debut_activite").val(),
							"date_fin": evarisk("#date_fin_activite").val(),
							"idPere": 1,
							"description": evarisk("#description_activite").val(),
							"cout": evarisk("#cout_activite").val(),
							"avancement": evarisk("#avancement_activite").val(),
							"responsable_activite": evarisk("#responsable_activite").val(),
							"variables":variables,
							"idProvenance": evarisk("#idProvenance_activite").val(),
							"tableProvenance": evarisk("#tableProvenance_activite").val()
						});
					}
				});
			});
			</script>';
		$activite_new .= 
			'<div class="alignright" id="ActionSaveButton" >' . 
				$addPictureButton . 
				EvaDisplayInput::afficherInput('button', $idBouttonEnregistrer, __('Enregistrer et Terminer', 'evarisk'), null, '', $idBouttonEnregistrer, false, true, '', 'button-primary', '', '', $scriptEnregistrementSave, 'left') . 
			'</div>
			<script type="text/javascript" >evarisk("#ActionSaveButton").children("div").children("br").remove();evarisk("#idBouttonEnregistrer").children("br").remove();var removeSlideShowViewer = true;</script>';
	}

	$activite_new .= EvaDisplayInput::fermerForm($idForm);

	echo '<div style="margin:12px 0px;" >' . $activite_new . '</div>';
}

?>