<?php
/*
 * @version v5.0
 */
 
 
//Postbox definition
$postBoxTitle = __('Informations G&eacute;n&eacute;rales', 'evarisk');
$postBoxId = 'postBoxGeneralInformation';
$postBoxCallbackFunction = 'getDangerGeneralInformationPostBoxBody';
add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_DANGERS, 'rightSide', 'default');

function getDangerGeneralInformationPostBoxBody($arguments)
{
	require_once(EVA_LIB_PLUGIN_DIR . 'danger/categorieDangers/categorieDangers.class.php' ); 
	require_once(EVA_LIB_PLUGIN_DIR . 'danger/danger/evaDanger.class.php' ); 
	require_once(EVA_LIB_PLUGIN_DIR . 'arborescence.class.php');
	require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayInput.class.php');

	$postId = '';
	if($arguments['idElement']!=null)
	{	
		$postId = $arguments['idElement'];
		$danger = EvaDanger::getDanger($postId);
		$contenuInputTitre = $danger->nom;
		$contenuInputDescription = $danger->description;
		$grise = false;
		$catMere = categorieDangers::getCategorieDanger($danger->id_categorie);
	}
	else
	{	
		$contenuInputTitre = '';
		$contenuInputDescription = '';
		$catMere = categorieDangers::getCategorieDanger($arguments['idPere']);
		$grise = true;
	}

	$danger_new = EvaDisplayInput::ouvrirForm('POST', 'informationGeneralesDanger', 'informationGeneralesDanger');
	{//Champs cachés
		$danger_new = $danger_new . EvaDisplayInput::afficherInput('hidden', 'act', '', '', null, 'act', false, false);
		$danger_new = $danger_new . EvaDisplayInput::afficherInput('hidden', 'affichage', $arguments['affichage'], '', null, 'affichage', false, false);
		$danger_new = $danger_new . EvaDisplayInput::afficherInput('hidden', 'table', TABLE_DANGER, '', null, 'table', false, false);
		$danger_new = $danger_new . EvaDisplayInput::afficherInput('hidden', 'id', $postId, '', null, 'id', false, false);
		$danger_new = $danger_new . EvaDisplayInput::afficherInput('hidden', 'idsFilAriane', $arguments['idsFilAriane'], '', null, 'idsFilAriane', false, false);
	}
	{//Nom du dangers
		$contenuAideTitre = "";
		$labelInput = ucfirst(sprintf(__("nom %s", 'evarisk'), __("du danger",'evarisk'))) . " :";
		$nomChamps = "nom_danger";
		$idTitre = "nom_danger";
		$danger_new = $danger_new . EvaDisplayInput::afficherInput('text', $idTitre, $contenuInputTitre, $contenuAideTitre, $labelInput, $nomChamps, $grise, true, 255, 'titleInput');
	}
	{//Catégorie de dangers mère		
		$selection = $catMere->id;
		$nameSelect = "categorieMere";
		$idSelect = "categorieMere";
		$labelSelect = __("Cat&eacute;gorie de dangers m&egrave;re", 'evarisk') . ' : ';
		$valeurDefaut = "Aucune";
		$nomRacine = "Categorie Racine";
		$categorieRacine = categorieDangers::getCategorieDangerByName($nomRacine);
		$danger_new = $danger_new .  '<div style="display:none">';
		$danger_new = $danger_new . EvaDisplayInput::afficherComboBoxArborescente($categorieRacine, TABLE_CATEGORIE_DANGER, $idSelect, $labelSelect, $nameSelect, $valeurDefaut, $selection);
		$danger_new = $danger_new .  '</div>';
	}
	{//Description
		$contenuAideDescription = "";
		$labelInput = __("Description", 'evarisk') . ' : ';
		$id = "description";
		$nomChamps = "description";
		$rows = 5;
		$danger_new = $danger_new . EvaDisplayInput::afficherInput('textarea', $id, $contenuInputDescription, $contenuAideDescription, $labelInput, $nomChamps, $grise, DESCRIPTION_DANGER_OBLIGATOIRE, $rows);
	}
	{//Bouton Enregistrer
		/*	We check if there are no danger with the same name	*/
		$saufDanger = $listeDangersExistants = $actionValue = '';
		if(isset($danger)) 
		{
			$saufDanger = $danger->nom;
			$actionValue = 'digirisk("#act").val("update")';
		}
		else
		{
			$actionValue = 'digirisk("#act").val("save")';
		}
		$dangersName = EvaDanger::getDangersName($saufDanger);
		if(count($dangersName) != 0)
		{
			$listeDangersExistants = "valeurActuelle in {";
			foreach($dangersName as $dangerName)
			{
				$listeDangersExistants .= "'" . addslashes($dangerName) . "':'', ";
			}
			$listeDangersExistants .= "}";
		}
		else
		{
			$listeDangersExistants = "false";
		}
		$idBouttonEnregistrer = 'save';
		$scriptEnregistrement = '<script type="text/javascript">
			function isSomeName()
			{				
				valeurActuelle = digirisk("#nom_danger").val();
				if(valeurActuelle == "")
				{
					alert("' . __("Vous n'avez pas donne de nom au danger", 'evarisk') . '");
				}
				else
				{
					if(' . $listeDangersExistants . ')
					{
						alert("' . __("Un danger porte deja ce nom", 'evarisk') . '");
					}
					else
					{
						'. $actionValue . '
						digirisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
							"table": "' . TABLE_DANGER . '",
							"act": digirisk("#act").val(),
							"id": digirisk("#id").val(),
							"nom_danger": digirisk("#nom_danger").val(),
							"categorieMere": digirisk("#categorieMere :selected").val(),
							"description": digirisk("#description").val(),
							"affichage": digirisk("#affichage").val(),
							"idsFilAriane": digirisk("#idsFilAriane").val()
						});
					}
				}
			}
			digirisk(document).ready(function() {				
				digirisk(\'#' . $idBouttonEnregistrer . '\').click(function() {
					if(digirisk(\'#' . $idTitre . '\').is(".form-input-tip"))
					{
						document.getElementById(\'' . $idTitre . '\').value=\'\';
						digirisk(\'#' . $idTitre . '\').removeClass(\'form-input-tip\');
					}
					isSomeName(\'' . $idTitre . '\');
				});
			});
			</script>';
		if(current_user_can('digi_add_danger') || current_user_can('digi_edit_danger'))
		{
			$danger_new .= EvaDisplayInput::afficherInput('button', $idBouttonEnregistrer, __('Enregistrer', 'evarisk'), null, '', 'save', false, false, '', 'button-primary alignright', '', '', $scriptEnregistrement);
		}
	}
	$danger_new = $danger_new . EvaDisplayInput::fermerForm('informationGeneralesDanger');
	echo $danger_new;
}
?>