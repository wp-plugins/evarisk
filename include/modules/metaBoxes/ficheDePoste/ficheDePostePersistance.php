<?php
require_once(EVA_CONFIG );
require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/documentUnique/documentUnique.class.php' );
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

if(($_POST['act'] == 'saveFichePoste') || ($_POST['act'] == 'saveWorkUnitSheetForGroupement'))
{
	$tableElement = digirisk_tools::IsValid_Variable($_POST['tableElement']);
	$idElement = digirisk_tools::IsValid_Variable($_POST['idElement']);

	$workUnitSheetInfos['nomDuDocument'] = digirisk_tools::IsValid_Variable($_POST['nomDuDocument']);
	$workUnitSheetInfos['nomEntreprise'] = digirisk_tools::IsValid_Variable($_POST['nomEntreprise']);
	$workUnitSheetInfos['dateCreation'] = date('Ymd');
	$workUnitSheetInfos['id_model'] = digirisk_tools::IsValid_Variable($_POST['id_model']);
	$workUnitSheetInfos['description'] = (isset($_POST['description']) && ($_POST['description'] != '') && ($_POST['description'] != NULL)) ? digirisk_tools::IsValid_Variable($_POST['description']) : __('NC', 'evarisk');
	$workUnitSheetInfos['adresse'] = (isset($_POST['adresse']) && ($_POST['adresse'] != '') && ($_POST['adresse'] != NULL)) ? digirisk_tools::IsValid_Variable($_POST['adresse']) : __('NC', 'evarisk');
	$workUnitSheetInfos['telephone'] = (isset($_POST['telephone']) && ($_POST['telephone'] != '') && ($_POST['telephone'] != NULL)) ? digirisk_tools::IsValid_Variable($_POST['telephone']) : __('NC', 'evarisk');

	$messageInfo = $moremessageInfo = '';
	$sauvegardeFicheDePoste = eva_WorkUnitSheet::saveWorkUnitSheet($tableElement, $idElement, $workUnitSheetInfos);

	if($_POST['act'] != 'saveWorkUnitSheetForGroupement')
	{
		if($sauvegardeFicheDePoste['result'] != 'error')
		{
			$messageToOutput = "<img src='" . EVA_MESSAGE_SUCCESS . "' alt='success' class='messageIcone' />" . __('La fiche de poste &agrave; bien &eacute;t&eacute; sauvegard&eacute;e.', 'evarisk');
			$moremessageInfo = 'digirisk("#ongletHistoriqueFicheDePoste").click();';
		}
		else
		{
			$messageToOutput = "<img src='" . EVA_MESSAGE_ERROR . "' alt='error' class='messageIcone' />" . __('La fiche de poste n\'a pas pu &ecirc;tre sauvegard&eacute;e', 'evarisk');
		}

		$messageInfo = '
			<script type="text/javascript">
				digirisk(document).ready(function(){
					actionMessageShow("#message' . TABLE_FP . '", "' . $messageToOutput . '");
					setTimeout(\'actionMessageHide("#message' . TABLE_FP . '")\',5000);
					' . $moremessageInfo . '
				});
			</script>';
	}

	echo $messageInfo;
}