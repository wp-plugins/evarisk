<?php
require_once(EVA_CONFIG );
require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/documentUnique/documentUnique.class.php' );
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

if(($_POST['act'] == 'saveFichePoste') || ($_POST['act'] == 'saveWorkUnitSheetForGroupement'))
{
	$tableElement = eva_tools::IsValid_Variable($_POST['tableElement']);
	$idElement = eva_tools::IsValid_Variable($_POST['idElement']);

	$workUnitSheetInfos['nomDuDocument'] = eva_tools::IsValid_Variable($_POST['nomDuDocument']);
	$workUnitSheetInfos['nomEntreprise'] = eva_tools::IsValid_Variable($_POST['nomEntreprise']);
	$workUnitSheetInfos['dateCreation'] = date('Ymd');

	$lienVersDUER = '';
	$sauvegardeDocumentUnique = eva_WorkUnitSheet::saveWorkUnitSheet($tableElement, $idElement, $workUnitSheetInfos);
	$messageInfo = $moremessageInfo = '';

	if($sauvegardeDocumentUnique['result'] != 'error')
	{
		$messageToOutput = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png\' alt=\'success\' />' . __('La fiche de poste &agrave; bien &eacute;t&eacute; sauvegard&eacute;.', 'evarisk');

		$lastDocumentUnique = eva_documentUnique::getDernierDocumentUnique($tableElement, $idElement);
		/*	Check if an odt file exist to be downloaded	*/
		$outputOdtFile = '';
		$odtFile = 'ficheDePoste/' . $tableElement . '/' . $idElement . '/' . $lastDocumentUnique->nomDUER . '_V' . $lastDocumentUnique->revisionDUER . '.odt';
		if( is_file(EVA_RESULTATS_PLUGIN_DIR . $odtFile) )
		{
			$outputOdtFile = '<br/><br/><br/><a href=\'' . EVA_RESULTATS_PLUGIN_URL . $odtFile . '\' target=\'evaDUEROdt\' >' . __('T&eacute;l&eacute;charger au format odt', 'evarisk') . '</a><br/><br/><br/><br/><a href=\'' . LINK_TO_DOWNLOAD_OPEN_OFFICE . '\' target=\'OOffice\' >' . __('T&eacute;l&eacute;charger Open Office', 'evarisk') . '</a>';
		}
		$lienVersDUER = 'evarisk("#documentUniqueResultContainer").html("<div style=\'float:right;width:80%;\' >' . $outputOdtFile . '</div>");';
	}
	else
	{
		$messageToOutput = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png\' alt=\'error\' />' . __('La fiche de poste n\'a pas pu &ecirc;tre sauvegard&eacute;e', 'evarisk');
		echo $sauvegardeDocumentUnique['errors']['query'];
	}

	$messageInfo .= '
		<script type="text/javascript">
			evarisk(document).ready(function(){
				actionMessageShow("#message' . TABLE_FP . '", "' . $messageToOutput . '");
				setTimeout(\'actionMessageHide("#message' . TABLE_FP . '")\',5000);
				' . $moremessageInfo . '
			});
		</script>';
	echo $messageInfo;
}
if($_POST['act'] == 'delete')
{

}