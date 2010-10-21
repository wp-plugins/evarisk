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
	$informationDocumentUnique['sources'] = ($_POST['sources']);

	$lienVersDUER = '';
	$sauvegardeDocumentUnique = documentUnique::saveNewDocumentUnique($tableElement, $idElement, $informationDocumentUnique);
	$messageInfo = '<script type="text/javascript">
							$(document).ready(function(){
								$("#message' . TABLE_DUER . '").addClass("updated");';
	if($sauvegardeDocumentUnique['result'] != 'error')
	{
		$messageInfo = $messageInfo . '
				$("#message' . TABLE_DUER . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'veille-reponse.gif" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le document unique &agrave; bien &eacute;t&eacute; sauvegard&eacute;.', 'evarisk') . '</strong></p>') . '");';

		$lienVersDUER = '<a href="' . EVA_INC_PLUGIN_URL . 'modules/evaluationDesRisques/documentUnique.php?id=' . $_POST['idElement'] . '&table=' . $_POST['tableElement'] . '" target="evaDUER" >' . __('Voir le document unique', 'evarisk') . '</a>';
	}
	else
	{
		$messageInfo = $messageInfo . '
				$("#message' . TABLE_DUER . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'veille-no-reponse.gif" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le document unique n\'&agrave; pas pu &ecirc;tre sauvegard&eacute;', 'evarisk') . '</strong></p>') . '");';
	}

	$messageInfo = $messageInfo . '
				$("#message' . TABLE_DUER . '").show();
				setTimeout(function(){
					$("#message' . TABLE_DUER . '").removeClass("updated");
					$("#message' . TABLE_DUER . '").hide();
				},7500);
			});
		</script>' .$lienVersDUER;
	echo $messageInfo;
}
if($_POST['act'] == 'delete')
{

}