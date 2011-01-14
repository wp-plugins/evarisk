<?php
require_once(EVA_CONFIG );
require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'risque/Risque.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/tache/evaTask.class.php' );
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

if(($_POST['act'] == 'save') || ($_POST['act'] == 'saveAdvanced'))
{
	$idRisque = eva_tools::IsValid_Variable($_POST['idRisque']);
	$idDanger = eva_tools::IsValid_Variable($_POST['idDanger']);
	$idMethode = eva_tools::IsValid_Variable($_POST['idMethode']);
	$tableElement = eva_tools::IsValid_Variable($_POST['tableElement']);
	$idElement = eva_tools::IsValid_Variable($_POST['idElement']);
	$histo = eva_tools::IsValid_Variable($_POST['histo']);
	$variables = $_POST['variables'];
	$description = eva_tools::IsValid_Variable($_POST['description']);
	$idRisque = Risque::saveNewRisk($idRisque, $idDanger, $idMethode, $tableElement, $idElement, $variables, $description, $histo);

	/*	Check if there are correctiv actions to link with this risk	*/
	$actionsCorrectives = eva_tools::IsValid_Variable($_POST['actionsCorrectives']);
	if($actionsCorrectives != '')
	{
		/*	Make the link between a corrective action and a risk evaluation	*/
		$query = 
			$wpdb->prepare(
				"SELECT id_evaluation 
				FROM " . TABLE_AVOIR_VALEUR . " 
				WHERE id_risque = '%d' 
					AND Status = 'Valid' 
				ORDER BY id DESC 
				LIMIT 1", 
				$idRisque
			);
		$evaluation = $wpdb->get_row($query);
		evaTask::liaisonTacheElement(TABLE_AVOIR_VALEUR, $evaluation->id_evaluation, $actionsCorrectives, 'after');
	}
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
		evarisk(document).ready(function(){
			evarisk("#message' . $table . '").addClass("updated");';
		if($deleteResult != 'error')
		{
			$messageInfo .= '
			evarisk("#message' . $table . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le risque a &eacute;t&eacute; supprim&eacute;.', 'evarisk') . '</strong></p>') . '");';
		}
		else
		{
			$messageInfo .= '
			evarisk("#message' . $table . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le risque n\'a pas pu &ecirc;tre supprim&eacute;.', 'evarisk') . '</strong></p>') . '");';
		}
		$messageInfo .= '
			evarisk("#message' . $table . '").show();
			setTimeout(function(){
				evarisk("#message' . $table . '").removeClass("updated");
				evarisk("#message' . $table . '").hide();
			},5000);

			evarisk("#ongletVoirLesRisques").click();
		});
	</script>';
	echo $messageInfo;
}