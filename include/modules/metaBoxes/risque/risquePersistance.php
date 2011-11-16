<?php
require_once(EVA_CONFIG );
require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'risque/Risque.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/tache/evaTask.class.php' );
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

if($_REQUEST['act'] == 'save'){
	$idRisque = eva_tools::IsValid_Variable($_REQUEST['idRisque']);

	/*	Check if there are correctiv actions to link with this risk	*/
	$actionsCorrectives = eva_tools::IsValid_Variable($_REQUEST['actionsCorrectives']);
	if(($actionsCorrectives != '') && ($idRisque > 0)){
		Risque::update_risk_rating_link_with_task($idRisque, $actionsCorrectives, array('before', 'after'));
		$task = new EvaTask();
		$task->setId($actionsCorrectives);
		$task->load();
		$task->setEfficacite($_REQUEST['action_efficiency']);
		$task->save();
	}
	else{
		$idDanger = eva_tools::IsValid_Variable($_REQUEST['idDanger']);
		$idMethode = eva_tools::IsValid_Variable($_REQUEST['idMethode']);
		$tableElement = eva_tools::IsValid_Variable($_REQUEST['tableElement']);
		$idElement = eva_tools::IsValid_Variable($_REQUEST['idElement']);
		$variables = $_REQUEST['variables'];
		$description = eva_tools::IsValid_Variable($_REQUEST['description_risque']);
		$histo = eva_tools::IsValid_Variable($_REQUEST['histo']);
		$idRisque = Risque::saveNewRisk($idRisque, $idDanger, $idMethode, $tableElement, $idElement, $variables, $description, $histo);
	}

	/*	Check if there are recommendation to link with this risk	*/
	$preconisationRisque = eva_tools::IsValid_Variable($_REQUEST['preconisationRisque']);
	if($preconisationRisque != ''){
		$infosDanger = EvaDanger::getDanger($idDanger);
		$_POST['nom_activite'] = substr($preconisationRisque, 0, 50);
		$_POST['description'] = $preconisationRisque;
		$_POST['cout'] = '';
		$_POST['idProvenance'] = $idRisque;
		$_POST['tableProvenance'] = TABLE_RISQUE;
		$_POST['responsable_activite'] = '';
		$_POST['date_debut'] = date('Y-m-d');
		$_POST['date_fin'] = date('Y-m-d', mktime(0, 0, 0, date("m")+1, date("d"), date("Y")));
		$_POST['avancement'] = '0';
		// $_POST['hasPriority'] = 'yes';

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
		$_POST['parentTaskId'] = evaTask::saveNewTask();
		evaTask::liaisonTacheElement(TABLE_AVOIR_VALEUR, $evaluation->id_evaluation, $_POST['parentTaskId'], 'demand');
		/*	Create automatically a sub-task for the priority task	*/
		evaActivity::saveNewActivity();
	}

	if($_REQUEST['act'] == 'save'){
		echo '<script type="text/javascript" >goTo("#postBoxRisques");</script>';
	}
}

if($_REQUEST['act'] == 'delete')
{
	$table = eva_tools::IsValid_Variable($_REQUEST['table']);
	$idRisque = eva_tools::IsValid_Variable($_REQUEST['idRisque']);
	$tableElement = eva_tools::IsValid_Variable($_REQUEST['tableElement']);
	$idElement = eva_tools::IsValid_Variable($_REQUEST['idElement']);
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