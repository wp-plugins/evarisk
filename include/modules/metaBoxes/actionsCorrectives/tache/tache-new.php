<?php
/*
 * @version v5.0
 */

//Postbox definition
$postBoxTitle = __('Informations G&eacute;n&eacute;rales', 'evarisk');
$postBoxId = 'postBoxGeneralInformation';
$postBoxCallbackFunction = 'getTaskGeneralInformationPostBoxBody';
add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_TACHE, 'rightSide', 'default');
require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/tache/evaTask.class.php' ); 
require_once(EVA_LIB_PLUGIN_DIR . 'arborescence.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayInput.class.php' );

function getTaskGeneralInformationPostBoxBody($arguments)
{
	$postId = '';
	if($arguments['idElement'] != null)
	{
		$postId = $arguments['idElement'];
    $tache = new EvaTask($postId);
		$tache->load();
		$contenuInputTitre = $tache->getName();
		$contenuInputDescription = $tache->getDescription();
		$idProvenance = $tache->getIdFrom();
		$tableProvenance = $tache->getTableFrom();
		$contenuInputResponsable = $tache->getidResponsable();
		$contenuInputRealisateur = $tache->getidSoldeur();
		$ProgressionStatus = $tache->getProgressionStatus();
		$grise = false;
		$tacheMere = Arborescence::getPere(TABLE_TACHE, $tache->convertToWpdb());
		$idPere = $tacheMere->id;
    $saveOrUpdate = 'update';
	}
	else
	{
		$contenuInputTitre = '';
		$contenuInputDescription = '';
		$contenuInputResponsable = '';
		$contenuInputRealisateur = '';
		$ProgressionStatus = '';
		$idProvenance = 0;
		$tableProvenance = '';
		$idPere = $arguments['idPere'];
		$grise = true;
    $saveOrUpdate = 'save';
	}
  
  $idForm = 'informationGeneralesTache';
	$tache_new = EvaDisplayInput::ouvrirForm('POST', $idForm, $idForm);
	{//Champs cachés
		$tache_new = $tache_new . EvaDisplayInput::afficherInput('hidden', 'actTache', $saveOrUpdate, '', null, 'act', false, false);
		$tache_new = $tache_new . EvaDisplayInput::afficherInput('hidden', 'affichageTache', $arguments['affichage'], '', null, 'affichage', false, false);
		$tache_new = $tache_new . EvaDisplayInput::afficherInput('hidden', 'tableTache', TABLE_TACHE, '', null, 'table', false, false);
		$tache_new = $tache_new . EvaDisplayInput::afficherInput('hidden', 'idTache', $postId, '', null, 'id', false, false);
		$tache_new = $tache_new . EvaDisplayInput::afficherInput('hidden', 'idPereTache', $idPere, '', null, 'idPere', false, false);
		$tache_new = $tache_new . EvaDisplayInput::afficherInput('hidden', 'idsFilArianeTache', $arguments['idsFilAriane'], '', null, 'idsFilAriane', false, false);
		$tache_new = $tache_new . EvaDisplayInput::afficherInput('hidden', 'idProvenanceTache', $idProvenance, '', null, 'idProvenance', false, false);
		$tache_new = $tache_new . EvaDisplayInput::afficherInput('hidden', 'tableProvenanceTache', $tableProvenance, '', null, 'tableProvenance', false, false);
	}
	{//Nom de la tâche
		$contenuAideTitre = "";
		$labelInput = ucfirst(sprintf(__("nom %s", 'evarisk'), __("de la t&acirc;che",'evarisk'))) . " :";
		$nomChamps = "nom_tache";
		$idTitre = "nom_tache";
		$tache_new = $tache_new . EvaDisplayInput::afficherInput('text', $idTitre, $contenuInputTitre, $contenuAideTitre, $labelInput, $nomChamps, $grise, true, 255, 'titleInput');
	}
	{//Responsable
		$contenuAideDescription = "";
		$labelInput = __("Responsable", 'evarisk') . ' : ';
		$id = "responsable_tache";
		$nomChamps = "responsable_tache";		

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
		$tache_new .= '<br/>' . EvaDisplayInput::afficherComboBox($listeUtilisateurs, $id, $labelInput, $nomChamps, '', $selectedUser, $tabValue, $tabDisplay);
	}
	{//Description
		$contenuAideDescription = "";
		$labelInput = __("Description", 'evarisk') . ' : ';
		$id = "descriptionTache";
		$nomChamps = "description";
		$rows = 5;
		$tache_new = $tache_new . EvaDisplayInput::afficherInput('textarea', $id, $contenuInputDescription, $contenuAideDescription, $labelInput, $nomChamps, $grise, DESCRIPTION_TACHE_OBLIGATOIRE, $rows);
	}
	{//Bouton Enregistrer
		$idBouttonEnregistrer = 'saveTache';

		/*	Check if the user in charge of the action and the maker are mandatory */
		$idResponsableIsMandatory = options::getOptionValue('responsable_Tache_Obligatoire');

		$scriptEnregistrementSave = '<script type="text/javascript">
			evarisk(document).ready(function() {				
				evarisk("#' . $idBouttonEnregistrer . '").click(function() {
					if(evarisk("#' . $idTitre . '").is(".form-input-tip"))
					{
						document.getElementById("' . $idTitre . '").value="";
						evarisk("#' . $idTitre . '").removeClass("form-input-tip");
					}

					idResponsable = evarisk("#responsable_tache").val();
					idResponsableIsMandatory = "false";
					idResponsableIsMandatory = "' . $idResponsableIsMandatory . '";

					valeurActuelle = evarisk("#' . $idTitre . '").val();
          if(valeurActuelle == "")
          {
            alert(convertAccentToJS("' . __("Vous n\'avez pas donne de nom a la t&acirc;che", 'evarisk') . '"));
          }
					else if(((idResponsable <= "0") ||(idResponsable == "")) && (idResponsableIsMandatory == "oui"))
					{
						alert(convertAccentToJS("' . __("Vous devez choisir une personne en charge de la t&acirc;che", 'evarisk') . '"));
					}
          else
          {
            evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
              "table": "' . TABLE_TACHE . '",
              "act": evarisk("#actTache").val(),
              "id": evarisk("#idTache").val(),
              "nom_tache": evarisk("#' . $idTitre . '").val(),
              "idPere": evarisk("#idPereTache").val(),
              "responsable_tache": evarisk("#responsable_tache").val(),
              "description": evarisk("#descriptionTache").val(),
              "affichage": evarisk("#affichageTache").val(),
              "idsFilAriane": evarisk("#idsFilArianeTache").val(),
              "idProvenance": evarisk("#idProvenanceTache").val(),
              "tableProvenance": evarisk("#tableProvenanceTache").val()
            });
          }
				});
			});
			</script>';
	}	
	{//Bouton Solder
		$idBoutton = 'taskDone';

		$scriptEnregistrementDone = '<script type="text/javascript">
			evarisk(document).ready(function() {				
				evarisk("#' . $idBoutton . '").click(function() {
					if(evarisk("#' . $idTitre . '").is(".form-input-tip"))
					{
						document.getElementById("' . $idTitre . '").value="";
						evarisk("#' . $idTitre . '").removeClass("form-input-tip");
					}

					idResponsable = evarisk("#responsable_tache").val();
					idResponsableIsMandatory = "false";
					idResponsableIsMandatory = "' . $idResponsableIsMandatory . '";

					valeurActuelle = evarisk("#' . $idTitre . '").val();
          if(valeurActuelle == "")
          {
            alert(convertAccentToJS("' . __("Vous n\'avez pas donne de nom a la t&acirc;che", 'evarisk') . '"));
          }
					else if(((idResponsable <= "0") ||(idResponsable == "")) && (idResponsableIsMandatory == "oui"))
					{
						alert(convertAccentToJS("' . __("Vous devez choisir une personne en charge de la t&acirc;che", 'evarisk') . '"));
					}
          else if(confirm(convertAccentToJS("' . __('En soldant cette t&acirc;che, vous solderez tous les &eacute;l&eacute;ments \'en-dessous\'. Etes vous sur?', 'evarisk') . '")))
          {
						evarisk("#actionDone").html(\'<img src="' . PICTO_LOADING_ROUND . '" alt="loading" />\');
            evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
              "table": "' . TABLE_TACHE . '",
              "act": "taskDone",
              "id": evarisk("#idTache").val(),
              "nom_tache": evarisk("#' . $idTitre . '").val(),
              "idPere": evarisk("#idPereTache").val(),
              "responsable_tache": evarisk("#responsable_tache").val(),
              "description": evarisk("#descriptionTache").val(),
              "affichage": evarisk("#affichageTache").val(),
              "idsFilAriane": evarisk("#idsFilArianeTache").val(),
              "idProvenance": evarisk("#idProvenanceTache").val(),
              "tableProvenance": evarisk("#tableProvenanceTache").val()
            });
          }
				});
			});
			</script>';
	}
	{//Boutons
		if(($saveOrUpdate == 'save') || ($ProgressionStatus == '') || ($ProgressionStatus == 'inProgress') || (options::getOptionValue('possibilite_Modifier_Tache_Soldee')== 'oui'))
		{
			$tache_new .= 
				'<div class="alignright" id="TaskSaveButton" >';

			if(($saveOrUpdate != 'save') && (($ProgressionStatus == '') || ($ProgressionStatus == 'inProgress')))
			{
			$tache_new .= 
					EvaDisplayInput::afficherInput('button', $idBoutton, __('Solder la tache', 'evarisk'), null, '', $idBoutton, false, true, '', 'button-primary', '', '', $scriptEnregistrementDone, 'left');
			}
			elseif($saveOrUpdate == 'update')
			{
			$tache_new .= 
					'<div style="float:left;" id="TaskSaveButton" >
						<br/>
						<div class="alignright button-primary" >' . 
							__('Cette t&acirc;che est sold&eacute;e', 'evarisk') . 
						'</div>
					</div>';
			}

			$tache_new .= 
					EvaDisplayInput::afficherInput('button', $idBouttonEnregistrer, __('Enregistrer', 'evarisk'), null, '', 'saveTache', false, true, '', 'button-primary', '', '', $scriptEnregistrementSave, 'left') . 
				'</div>
				<script type="text/javascript" >evarisk("#TaskSaveButton").children("br").remove();</script>';
		}
		else
		{
			$tache_new .= 
				'<div class="alignright button-primary" id="TaskSaveButton" >' . 
					__('Cette t&acirc;che est sold&eacute;e, vous ne pouvez pas la modifier', 'evarisk') . 
				'</div>';
		}
	}
	$tache_new .= EvaDisplayInput::fermerForm($idForm);
	echo $tache_new;
}

?>