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
		$grise = false;
		$tacheMere = Arborescence::getPere(TABLE_TACHE, $tache->convertToWpdb());
		$idPere = $tacheMere->id;
    $saveOrUpdate = 'update';
	}
	else
	{
		$contenuInputTitre = '';
		$contenuInputDescription = '';
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
            alert(convertAccentToJS("' . __("Vous n\'avez pas donne de nom a la t&acirc;che", 'evarisk') . '"));
          }
          else
          {            
            $(\'#ajax-response\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {\'post\': \'true\', 
              \'table\': \'' . TABLE_TACHE . '\',
              \'act\': $(\'#actTache\').val(),
              \'id\': $(\'#idTache\').val(),
              \'nom_tache\': $(\'#' . $idTitre . '\').val(),
              \'idPere\': $(\'#idPereTache\').val(),
              \'description\': $(\'#descriptionTache\').val(),
              \'affichage\': $(\'#affichageTache\').val(),
              \'idsFilAriane\': $(\'#idsFilArianeTache\').val(),
              \'idProvenance\': $(\'#idProvenanceTache\').val(),
              \'tableProvenance\': $(\'#tableProvenanceTache\').val()
            });
          }
				});
			});
			</script>';
		$tache_new = $tache_new . EvaDisplayInput::afficherInput('button', $idBouttonEnregistrer, __('Enregistrer', 'evarisk'), null, '', 'saveTache', false, true, '', 'button-primary alignright', '', '', $scriptEnregistrement);
	}
	$tache_new = $tache_new . EvaDisplayInput::fermerForm($idForm);
	echo $tache_new;
}
?>