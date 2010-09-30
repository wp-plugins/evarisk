<?php
require_once(EVA_CONFIG );
require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'risque/Risque.class.php' );
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

if($_POST['act'] == 'save')
{
	$idRisque = eva_tools::IsValid_Variable($_POST['idRisque']);
	$idDanger = eva_tools::IsValid_Variable($_POST['idDanger']);
	$idMethode = eva_tools::IsValid_Variable($_POST['idMethode']);
	$tableElement = eva_tools::IsValid_Variable($_POST['tableElement']);
	$idElement = eva_tools::IsValid_Variable($_POST['idElement']);
	$variables = $_POST['variables'];
	$description = eva_tools::IsValid_Variable($_POST['description']);
	Risque::saveNewRisk($idRisque, $idDanger, $idMethode, $tableElement, $idElement, $variables, $description);
}
if($_POST['act'] == 'delete')
{
	$table = eva_tools::IsValid_Variable($_POST['table']);
	$idRisque = eva_tools::IsValid_Variable($_POST['idRisque']);
	$tableElement = eva_tools::IsValid_Variable($_POST['tableElement']);
	$idElement = eva_tools::IsValid_Variable($_POST['idElement']);
	$deleteResult = Risque::deleteRisk($idRisque, $tableElement, $idElement);

	$messageInfo = 
	'<script type="text/javascript">
		$(document).ready(function(){
			$("#message' . $table . '").addClass("updated");';
		if($deleteResult != 'error')
		{
			$messageInfo .= '
			$("#message' . $table . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'veille-reponse.gif" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le risque a &eacute;t&eacute; supprim&eacute;.', 'evarisk') . '</strong></p>') . '");';
		}
		else
		{
			$messageInfo .= '
			$("#message' . $table . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'veille-no-reponse.gif" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le risque n\'a pas pu &ecirc;tre supprim&eacute;.', 'evarisk') . '</strong></p>') . '");';
		}
		$messageInfo .= '
			$("#message' . $table . '").show();
			setTimeout(function(){
				$("#message' . $table . '").removeClass("updated");
				$("#message' . $table . '").hide();
			},5000);

			$("#ongletVoirLesRisques").click();
		});
	</script>';
	echo $messageInfo;
}