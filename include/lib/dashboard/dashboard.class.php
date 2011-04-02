<?php
require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/tache/evaTask.class.php');

class dashboard {

	const boxNumber = 0;

	function dashboardMainPage()
	{
?>
	<div class="wrap">
	<div id="evaDashboardIntro" >
		<h2><b>EVARISK </b>: Progiciel gratuit d'aide &agrave; l'&eacute;valuation des risques professionnels</h2>
		<p><b>Evarisk est specialis&eacute;e dans l'&eacute;tude m&eacute;thodologique de la pr&eacute;vention des risques.</b></p>
		<p>Il d&eacute;coule de cette activit&eacute; la r&eacute;daction du document unique et les d&eacute;marches de pr&eacute;vention n&eacute;cessaires &agrave; la r&eacute;duction des risques.</p>
		<p><b>Evarisk</b> vous propose le premier logiciel libre d'aide &agrave; la r&eacute;daction du document unique. Gr&acirc;ce &agrave; ce logiciel gratuit en Open Source, vous pourrez g&eacute;rer la totalit&eacute; des acteurs, des unit&eacute;s, et des dangers que vous devez recenser dans votre document unique.</p>
	</div>
	<div class="metabox-holder clear">
		<script type="text/javascript">
			evarisk(document).ready( function() {
				evarisk('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				evarisk('.postbox h3, .postbox .handlediv').each(function(){evarisk(this).unbind("click");});
				postboxes.add_postbox_toggles("evaDashboard");
				postboxes.init("evaDashboard");
				postboxes.save_state("evaDashboard");
				postboxes.save_order("evaDashboard");
			});
		</script>
<?php

	dashboard::getDashBoardContent();

?>
		<div style="float:left;width:49%;" id="evaDashboard_LeftSide" >
			<?php
				/*	Output the different Meta Box define for the dashboard page	*/
				do_meta_boxes("evaDashboard", "leftSide", null);
			?>
		</div>
		<div style="float:right;width:49%;" id="evaDashboard_RightSide" >
			<?php
				/*	Output the different Meta Box define for the dashboard page	*/
				do_meta_boxes("evaDashboard", "rightSide", null);
				//To preserve postBoxes order
				wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false);
				//To preserve postBoxes closure state
				wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false);
			?>
		</div>
	</div>
</div>
<?php
	}

	/**
	* The global function that call the different functions defined to build the dashboard
	*/
	function getDashBoardContent()
	{
		/*	Get the not finished and passed end date corrective actions 	*/
		dashboard::actionCorrectivesNonTermineesDateDepassees_Box();

		/*	Get the finished actions with parent task not mark as done 	*/
		dashboard::actionCorrectivesTermineesTachesASolder_Box();

		/*	Get the finished corrective actions to reevaluate the associated element 	*/
		dashboard::actionCorrectivesTermineesAReEvaluer_Box();

		/*	Get different stats 	*/
		dashboard::vracStats_Box();
	}

/*	Start - correctives actions	*/


	/**
	*	Return the meta-box with the different actions with the date already passed but not finished
	*	@see actionCorrectivesNonTermineesDateDepassees_Check
	*/
	function actionCorrectivesNonTermineesDateDepassees_Box()
	{
		add_meta_box("evaDashboard_ac_passed", __("Actions correctives non termin&eacute;es dont la date est d&eacute;pass&eacute;e", "evarisk"), array("dashboard", "actionCorrectivesNonTermineesDateDepassees_Check"), "evaDashboard", "leftSide");
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
		add_meta_box("evaDashboard_task_toDone", __("T&acirc;ches &agrave; solder dont toutes les actions sont termin&eacute;es", "evarisk"), array("dashboard", "actionsCorrectivesTermineesTachesASolder_Check"), "evaDashboard", "leftSide");
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
		add_meta_box("evaDashboard_ac_done", __("Risques &agrave; re-&eacute;valuer suite &agrave; une action corrective", "evarisk"), array("dashboard", "actionCorrectivesTermineesAReEvaluer_Check"), "evaDashboard", "leftSide");
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
		add_meta_box("evaDashboard_vracStats", __("Statistiques", "evarisk"), array("dashboard", "vracStats_Check"), "evaDashboard", "rightSide");
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