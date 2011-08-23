<?php
require_once(EVA_CONFIG );
require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/documentUnique/documentUnique.class.php' );
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

if($_POST['act'] == 'saveDocumentUnique')
{
	$tableElement = eva_tools::IsValid_Variable($_POST['tableElement']);
	$idElement = eva_tools::IsValid_Variable($_POST['idElement']);

	$informationDocumentUnique['dateCreation'] = eva_tools::IsValid_Variable($_POST['dateCreation']);
	$informationDocumentUnique['dateDebutAudit'] = eva_tools::IsValid_Variable($_POST['dateDebutAudit']);
	$informationDocumentUnique['dateFinAudit'] = eva_tools::IsValid_Variable($_POST['dateFinAudit']);
	$informationDocumentUnique['nomEntreprise'] = eva_tools::IsValid_Variable($_POST['nomEntreprise']);

	$informationDocumentUnique['telephoneFixe'] = eva_tools::IsValid_Variable($_POST['telephoneFixe']);
	$informationDocumentUnique['telephonePortable'] = eva_tools::IsValid_Variable($_POST['telephonePortable']);
	$informationDocumentUnique['numeroFax'] = eva_tools::IsValid_Variable($_POST['numeroFax']);

	$informationDocumentUnique['emetteur'] = eva_tools::IsValid_Variable($_POST['emetteur']);
	$informationDocumentUnique['destinataire'] = eva_tools::IsValid_Variable($_POST['destinataire']);
	$informationDocumentUnique['nomDuDocument'] = eva_tools::IsValid_Variable($_POST['nomDuDocument']);
	$informationDocumentUnique['methodologie'] = ($_POST['methodologie']);
	$informationDocumentUnique['sources'] = eva_tools::IsValid_Variable($_POST['sources']);
	$informationDocumentUnique['alerte'] = eva_tools::IsValid_Variable($_POST['alerte']);
	$informationDocumentUnique['localisation'] = eva_tools::IsValid_Variable($_POST['localisation']);
	$informationDocumentUnique['id_model'] = ($_POST['id_model']);

	$messageInfo = $moremessageInfo = '';
	$sauvegardeDocumentUnique = eva_documentUnique::saveNewDocumentUnique($tableElement, $idElement, $informationDocumentUnique);
	$messageInfo = '<script type="text/javascript">
			evarisk(document).ready(function(){
				evarisk("#message' . TABLE_DUER . '").addClass("updated");';

	if($sauvegardeDocumentUnique['result'] != 'error')
	{
		$messageToOutput = "<img src='" . EVA_MESSAGE_SUCCESS . "' alt='success' class='messageIcone' />" . __('Le document unique &agrave; bien &eacute;t&eacute; sauvegard&eacute;.', 'evarisk');
		$moremessageInfo = 'evarisk("#subTabSelector").val("DUER");
				evarisk("#ongletHistoriqueDocument").click();';
	}
	else
	{
		$messageToOutput = "<img src='" . EVA_MESSAGE_ERROR . "' alt='error' class='messageIcone' />" . __('Le document unique n\'&agrave; pas pu &ecirc;tre sauvegard&eacute;', 'evarisk');
	}

	$messageInfo = '
		<script type="text/javascript">
			evarisk(document).ready(function(){
				actionMessageShow("#message' . TABLE_DUER . '", "' . $messageToOutput . '");
				setTimeout(\'actionMessageHide("#message' . TABLE_DUER . '")\',5000);
				' . $moremessageInfo . '
			});
		</script>';

	echo $messageInfo;
}