<?php
/*
 * @version v5.0
 */
 
 
//Postbox definition
$postBoxTitle = __('Informations G&eacute;n&eacute;rales', 'evarisk');
$postBoxId = 'postBoxGeneralInformation';
$postBoxCallbackFunction = 'getCategorieDangersGeneralInformationPostBoxBody';
add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_CATEGORIES_DANGERS, 'rightSide', 'default');
require_once(EVA_LIB_PLUGIN_DIR . 'danger/categorieDangers/categorieDangers.class.php' ); 
require_once(EVA_LIB_PLUGIN_DIR . 'arborescence.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayInput.class.php' );

function getCategorieDangersGeneralInformationPostBoxBody($arguments)
{
	$postId = '';
	if($arguments['idElement'] != null)
	{
		$postId = $arguments['idElement'];
		$categorie_danger = categorieDangers::getCategorieDanger($postId);
		$contenuInputTitre = $categorie_danger->nom;
		$contenuInputDescription = $categorie_danger->description;
		$grise = false;
		$catMere = Arborescence::getPere(TABLE_CATEGORIE_DANGER, $categorie_danger);
	}
	else
	{
		$contenuInputTitre = '';
		$contenuInputDescription = '';
		$catMere = categorieDangers::getCategorieDanger($arguments['idPere']);
		$grise = true;
	}
	$categorieDanger_new = EvaDisplayInput::ouvrirForm('POST', 'informationGeneralesCategorieDangers', 'informationGeneralesCategorieDangers');
	{//Champs cachés
		$categorieDanger_new = $categorieDanger_new . EvaDisplayInput::afficherInput('hidden', 'act', '', '', null, 'act', false, false);
		$categorieDanger_new = $categorieDanger_new . EvaDisplayInput::afficherInput('hidden', 'affichage', $arguments['affichage'], '', null, 'affichage', false, false);
		$categorieDanger_new = $categorieDanger_new . EvaDisplayInput::afficherInput('hidden', 'table', TABLE_CATEGORIE_DANGER, '', null, 'table', false, false);
		$categorieDanger_new = $categorieDanger_new . EvaDisplayInput::afficherInput('hidden', 'id', $postId, '', null, 'id', false, false);
		$categorieDanger_new = $categorieDanger_new . EvaDisplayInput::afficherInput('hidden', 'idsFilAriane', $arguments['idsFilAriane'], '', null, 'idsFilAriane', false, false);
	}
	{//Nom de la catégorie de dangers
		$contenuAideTitre = "";
		$labelInput = ucfirst(sprintf(__("nom %s", 'evarisk'), __("de la cat&eacute;gorie de dangers",'evarisk'))) . " :";
		$nomChamps = "nom_categorie";
		$idTitre = "nom_categorie";
		$categorieDanger_new = $categorieDanger_new . EvaDisplayInput::afficherInput('text', $idTitre, $contenuInputTitre, $contenuAideTitre, $labelInput, $nomChamps, $grise, true, 255, 'titleInput');
	}
	{//Catégorie de dangers mère
		$search = "`Status`='Valid' AND nom<>'Categorie Racine'";
		$order = "nom ASC";
		if((isset($categorie_danger)))
		{
			 $search = $search . " AND NOT (limiteGauche >= " . $categorie_danger->limiteGauche . " AND limiteDroite <= " . $categorie_danger->limiteDroite . ") ";
		}
		$selection = $catMere->id;
		$nameSelect = "categorieMere";
		$idSelect = "categorieMere";
		$labelSelect = __("Cat&eacute;gorie de dangers m&egrave;re", 'evarisk') . ' : ';
		$valeurDefaut = "Aucune";
		$nomRacine = "Categorie Racine";
		$categorieRacine = categorieDangers::getCategorieDangerByName($nomRacine);
		$categorieDanger_new = $categorieDanger_new .  '<div style="display:none">';
		$categorieDanger_new = $categorieDanger_new . EvaDisplayInput::afficherComboBoxArborescente($categorieRacine, TABLE_CATEGORIE_DANGER, $idSelect, $labelSelect, $nameSelect, $valeurDefaut, $selection);
		$categorieDanger_new = $categorieDanger_new .  '</div>';
	}
	{//Description
		$contenuAideDescription = "";
		$labelInput = __("Description", 'evarisk') . ' : ';
		$id = "description";
		$nomChamps = "description";
		$rows = 5;
		$categorieDanger_new = $categorieDanger_new . EvaDisplayInput::afficherInput('textarea', $id, $contenuInputDescription, $contenuAideDescription, $labelInput, $nomChamps, $grise, DESCRIPTION_CATEGORIE_DANGER_OBLIGATOIRE, $rows);
	}
	{//Bouton Enregistrer
		/*	We check if the are no danger category with the same name*/
		$saufCategorie = $listeCategorieExistante = $actionValue = '';
		if($postId!=null)
		{
			$saufCategorie = $categorie_danger->nom;
			$actionValue = 'evarisk("#act").val("update")';
		}
		else
		{
			$actionValue = 'evarisk("#act").val("save")';
		}
		$categories = categorieDangers::getCategoriesName($saufCategorie);
		if(count($categories) != 0)
		{
			$listeCategorieExistante = "valeurActuelle in {";
			foreach($categories as $categorie)
			{
				$listeCategorieExistante .= "'" . addslashes($categorie) . "':'', ";
			}
			$listeCategorieExistante .= "}";
		}
		else
		{
			$listeCategorieExistante .= "false";
		}

		$idBouttonEnregistrer = 'save';
		$scriptEnregistrement = '<script type="text/javascript">
			function isSomeName(){
				valeurActuelle = evarisk("#nom_categorie").val();
				if(valeurActuelle == "")
				{
					alert("' . __("Vous n'avez pas donne de nom a la categorie", 'evarisk') . '");
				}
				else
				{
					if(' . $listeCategorieExistante . ')
					{
						alert("' . __("Une categorie porte deja ce nom", 'evarisk') . '");
					}
					else
					{
						'. $actionValue . '
						evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
							"table": "' . TABLE_CATEGORIE_DANGER . '",
							"act": evarisk("#act").val(),
							"id": evarisk("#id").val(),
							"nom_categorie": evarisk("#nom_categorie").val(),
							"categorieMere": evarisk("#categorieMere :selected").val(),
							"description": evarisk("#description").val(),
							"affichage": evarisk("#affichage").val(),
							"idsFilAriane": evarisk("#idsFilAriane").val()
						});
					}
				}
			}
			evarisk(document).ready(function() {
				evarisk(\'#' . $idBouttonEnregistrer . '\').click(function() {
					if(evarisk(\'#' . $idTitre . '\').is(".form-input-tip"))
					{
						document.getElementById(\'' . $idTitre . '\').value=\'\';
						evarisk(\'#' . $idTitre . '\').removeClass(\'form-input-tip\');
					}
					isSomeName(\'' . $idTitre . '\');
				});
			});
			</script>';
		if(current_user_can('digi_add_danger_category') || current_user_can('digi_edit_danger_category'))
		{
			$categorieDanger_new .= EvaDisplayInput::afficherInput('button', $idBouttonEnregistrer, __('Enregistrer', 'evarisk'), null, '', 'save', false, true, '', 'button-primary alignright', '', '', $scriptEnregistrement);
		}
	}
	$categorieDanger_new = $categorieDanger_new . EvaDisplayInput::fermerForm('informationGeneralesCategorieDangers');
	echo $categorieDanger_new;
}

