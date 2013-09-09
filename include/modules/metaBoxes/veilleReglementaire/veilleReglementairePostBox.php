<?php
	//Postbox definition
	$postBoxTitle = __('Veille R&egrave;glementaire', 'evarisk') . (!empty($_REQUEST['table']) && !empty($_REQUEST['id']) ? Arborescence::display_element_main_infos( $_REQUEST['table'], $_REQUEST['id'] ) : '');
	$postBoxId = 'postBoxVeilleReglementaire';
	$postBoxCallbackFunction = 'getReglementairyWatchBoxBody';
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_UNITES_DE_TRAVAIL, 'rightSide', 'default');
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_GROUPEMENTS, 'rightSide', 'default');

	function getReglementairyWatchBoxBody($element) {
		$idElement = $element['idElement'];
		$tableElement = $element['tableElement'];
		if($idElement != null)
		{
			require_once(EVA_METABOXES_PLUGIN_DIR . 'veilleReglementaire/formulaireReponse.php');
			$corpsPostBoxVeille =  '<div id="plotLocation"></div><div id="interractionVeille">' . getFormulaireReponse($idElement, $tableElement) . '</div>';
		}
		else
		{
			switch($tableElement)
			{
				case TABLE_GROUPEMENT:
					$element = __('le groupement', 'evarisk');
					break;
				case TABLE_UNITE_TRAVAIL:
					$element = __('l\'unit&eacute; de travail', 'evarisk');
					break;
				default :
					$element = __('l\'&eacute;l&eacute;ment', 'evarisk');
			}
			$corpsPostBoxVeille = sprintf(__("Veuillez d'abord enregistrer %s.", 'evarisk'), $element);
		}
		echo $corpsPostBoxVeille;
	}
?>