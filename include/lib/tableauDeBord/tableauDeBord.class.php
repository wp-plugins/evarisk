<?php
require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/tache/evaTask.class.php');

class tableauDeBord {

	const boxNumber = 0;

	/**
	* The global function that call the different functions defined to build the dashboard
	*/
	function genereTableauDeBord()
	{
		/*	Get the not finished and passed end date corrective actions 	*/
		tableauDeBord::actionCorrectivesNonTermineesDateDepassees_Box();

		/*	Get the finished actions with parent task not mark as done 	*/
		tableauDeBord::actionCorrectivesTermineesTachesASolder_Box();

		/*	Get the finished corrective actions to reevaluate the associated element 	*/
		tableauDeBord::actionCorrectivesTermineesAReEvaluer_Box();

		/*	Get different stats 	*/
		tableauDeBord::vracStats_Box();
	}

/*	Start - correctives actions	*/


	/**
	*	Return the meta-box with the different actions with the date already passed but not finished
	*	@see actionCorrectivesNonTermineesDateDepassees_Check
	*/
	function actionCorrectivesNonTermineesDateDepassees_Box()
	{
		add_meta_box("evaDashboard_ac_passed", __("Actions correctives non termin&eacute;es dont la date est d&eacute;pass&eacute;e", "evarisk"), array("tableauDeBord", "actionCorrectivesNonTermineesDateDepassees_Check"), "evaDashboard", "leftSide");
	}
	/**
	*	Return the content of the meta-box of not finished but passed actions
	*/
	function actionCorrectivesNonTermineesDateDepassees_Check()
	{
		$tasks = evaTask::getTaskForDashBoard('passed');
		echo $tasks;
	}

	/**
	*	Return the meta-box with the different task to sold because the sub-actions are done
	*	@see actionsCorrectivesTermineesTachesASolder_Check
	*/
	function actionCorrectivesTermineesTachesASolder_Box()
	{
		add_meta_box("evaDashboard_task_toDone", __("T&acirc;ches &agrave; solder dont toutes les actions sont termin&eacute;es", "evarisk"), array("tableauDeBord", "actionsCorrectivesTermineesTachesASolder_Check"), "evaDashboard", "leftSide");
	}
	/**
	*	Return the content of the meta-box of done actions but not done task
	*/
	function actionsCorrectivesTermineesTachesASolder_Check()
	{
		$tasks = evaTask::getTaskForDashBoard('taskToMarkAsDone');
		echo $tasks;
	}

	/**
	*	Return the meta-box with the different actions that are already done but the associed risk have not been reevaluated
	*	@see actionCorrectivesTermineesAReEvaluer_Check
	*/
	function actionCorrectivesTermineesAReEvaluer_Box()
	{
		add_meta_box("evaDashboard_ac_done", __("Risques &agrave; re-&eacute;valuer suite &agrave; une action corrective", "evarisk"), array("tableauDeBord", "actionCorrectivesTermineesAReEvaluer_Check"), "evaDashboard", "leftSide");
	}
	/**
	*	Return the content of the meta-box of done task but risk to reevaluate
	*/
	function actionCorrectivesTermineesAReEvaluer_Check()
	{
		$tasks = evaTask::getTaskForDashBoard('toEvaluate');
		echo $tasks;
	}


/*	End - correctives actions	*/



/*	Start - Stats en vrac	*/

	/**
	*	Return the meta-box with the different actions that are already done but the associed risk have not been reevaluated
	*	@see actionCorrectivesTermineesAReEvaluer_Check
	*/
	function vracStats_Box()
	{
		add_meta_box("evaDashboard_vracStats", __("Statistiques", "evarisk"), array("tableauDeBord", "vracStats_Check"), "evaDashboard", "rightSide");
	}
	/**
	*	Return the content of the meta-box of done task but risk to reevaluate
	*/
	function vracStats_Check()
	{
		$output = 
			'<div class="hide" id="loadingPicContainer" ><img src="' . PICTO_LOADING_ROUND . '" alt="loading ..." /></div>
			<div id="vracStatsTabs" >
				<ul>
					<li><a href="' . EVA_INC_PLUGIN_URL . 'ajax.php?nom=dashboardStats&amp;tab=user" title="vracStatsContent" >' . __('Personnel', 'evarisk') . '</a></li>
					<li><a href="' . EVA_INC_PLUGIN_URL . 'ajax.php?nom=dashboardStats&amp;tab=risk" title="vracStatsContent" >' . __('Risques', 'evarisk') . '</a></li>
					<!-- <li><a href="' . EVA_INC_PLUGIN_URL . 'ajax.php?nom=dashboardStats&amp;tab=danger" title="vracStatsContent" >' . __('Dangers', 'evarisk') . '</a></li> -->
				</ul>
				<div id="vracStatsContent" ></div>
			</div>
			<script type="text/javascript" >
				evarisk("#vracStatsTabs").tabs();
				evarisk("#vracStatsTabs ul li a").click(function(){
					evarisk("#vracStatsContent").html(evarisk("#loadingPicContainer").html());
				});
			</script>';

		echo $output;
	}

/*	End - Stats en vrac	*/


}