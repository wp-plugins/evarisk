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
		$contenuInputAvancement = $activite->getProgression();
		$grise = false;
		$idPere = $activite->getRelatedTaskId();
		$saveOrUpdate = 'update';
	}
	else
	{
		$contenuInputTitre = '';
		$contenuInputDescription = '';
		$contenuInputDateDebut = '';
		$contenuInputDateFin = '';
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
		$labelInput = ucfirst(sprintf(__("nom %s", 'evarisk'), __("de l'action",'evarisk'))) . " :";
		$nomChamps = "nom_activite";
		$idTitre = "nom_activite";
		$activite_new = $activite_new . EvaDisplayInput::afficherInput('text', $idTitre, $contenuInputTitre, $contenuAideTitre, $labelInput, $nomChamps, $grise, true, 255, 'titleInput');
	}
	{//Date de début de l'action
		$contenuAideTitre = "";
		$labelInput = ucfirst(sprintf(__("Date de d&eacute;but %s", 'evarisk'), __("de l'action",'evarisk'))) . " :";
		$nomChamps = "date_debut";
		$id = "date_debut_activite";
		$activite_new = $activite_new . EvaDisplayInput::afficherInput('text', $id, $contenuInputDateDebut, $contenuAideTitre, $labelInput, $nomChamps, $grise, true, 255, '', 'date');
	}
	{//Date de début de l'action
		$contenuAideTitre = "";
		$labelInput = ucfirst(sprintf(__("Date de fin %s", 'evarisk'), __("de l'action",'evarisk'))) . " :";
		$nomChamps = "date_fin";
		$id = "date_fin_activite";
		$activite_new = $activite_new . EvaDisplayInput::afficherInput('text', $id, $contenuInputDateFin, $contenuAideTitre, $labelInput, $nomChamps, $grise, true, 255, '', 'date');
	}
	{//Avancement
		$contenuAideDescription = "";
		$labelInput = __("Co&ucirc;t", 'evarisk') . ' : ';
		$id = "cout_activite";
		$nomChamps = "cout";
		$activite_new = $activite_new . EvaDisplayInput::afficherInput('text', $id, $contenuInputAvancement, $contenuAideDescription, $labelInput, $nomChamps, $grise, true, 255, '100%', 'float');
	}
	{//Avancement
		$contenuAideDescription = "";
		$labelInput = __("Avancement", 'evarisk') . ' : ';
		$id = "avancement_activite";
		$nomChamps = "avancement";
		$activite_new = $activite_new . EvaDisplayInput::afficherInput('text', $id, $contenuInputAvancement, $contenuAideDescription, $labelInput, $nomChamps, $grise, true, 255, '100%', 'number');
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
		$scriptEnregistrement = '<script type="text/javascript">
			$(document).ready(function() {				
				$(\'#' . $idBouttonEnregistrer . '\').click(function() {
					if($(\'#' . $idTitre . '\').is(".form-input-tip"))
					{
						document.getElementById(\'' . $idTitre . '\').value=\'\';
						$(\'#' . $idTitre . '\').removeClass(\'form-input-tip\');
					}
					valeurActuelle = $("#' . $idTitre . '").val();
					if(valeurActuelle == "")
					{
						alert(convertAccentToJS("' . __("Vous n\'avez pas donne de nom a l'action", 'evarisk') . '"));
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
							"idsFilAriane": $("#idsFilAriane_activite").val(),
							"idProvenance": $("#idProvenance_activite").val(),
							"tableProvenance": $("#tableProvenance_activite").val()
						});
					}
				});
			});
			</script>';
		$activite_new = $activite_new . EvaDisplayInput::afficherInput('button', $idBouttonEnregistrer, __('Enregistrer', 'evarisk'), null, '', 'save', false, true, '', 'button-primary alignright', '', '', $scriptEnregistrement);
	}
	$activite_new = $activite_new . EvaDisplayInput::fermerForm($idForm);
	echo $activite_new;
}
?>