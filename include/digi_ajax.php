<?php
/**
 * Ajax request management file
 *
 * @author Evarisk <dev@evarisk.com>
 * @version 5.1.6.6
 * @package evarisk
 * @subpackage include
 */

/*	Check if file is include. No direct access possible with file url	*/
if ( !defined( 'EVA_PLUGIN_VERSION' ) ) {
	die( __('Access is not allowed by this way', 'evarisk') );
}

function digi_ajax_repair_db() {
	check_ajax_referer( 'digi_repair_db_per_version', 'digi_ajax_nonce' );
	$bool = false;
	$version_id = isset($_POST['version_id']) ? intval(digirisk_tools::IsValid_Variable($_POST['version_id'])) : null;

	if ( !empty($version_id) ) {
		$bool = digirisk_install::repair_database( $version_id );
	}

	echo json_encode(array($bool, $version_id));
	die();
}
add_action('wp_ajax_digi_ajax_repair_db', 'digi_ajax_repair_db');

function digi_ajax_repair_db_datas() {
	check_ajax_referer( 'digi_repair_db_per_version', 'digi_ajax_nonce' );
	$bool = false;
	$version_id = isset($_POST['version_id']) ? intval(digirisk_tools::IsValid_Variable($_POST['version_id'])) : null;

	if ( !empty($version_id) ) {
		digirisk_install::insert_data_for_version( $version_id );
		digirisk_install::make_specific_operation_on_update( $version_id );

		$bool = true;
	}

	echo json_encode(array($bool, $version_id));
	die();
}
add_action('wp_ajax_digi_ajax_repair_db_datas', 'digi_ajax_repair_db_datas');


/**
 *
 *
 * Follow up / Comment / Notes
 *
 *
 */
/**
 * Save modification on a comment
 */
function digi_ajax_save_activite_follow() {
	check_ajax_referer( 'digi_ajax_save_activite_follow', 'digi_ajax_nonce' );
	global $wpdb;

	$follow_up_details = null;
	if ( !empty($_POST['specific_follow_up']) ) {
		$follow_up_details = suivi_activite::getSuiviActivite($_POST['tableElement'], $_POST['idElement'], $_POST[TABLE_ACTIVITE_SUIVI]['follow_up_type'], $_POST['specific_follow_up']);
	}

	if ( !empty( $_POST['sub_action'] ) ) {
		$update_result = $wpdb->update(TABLE_ACTIVITE_SUIVI, array('status' => $_POST['sub_action']), array('id' => $_POST['specific_follow_up']));
		$save_activite_result = (($update_result === false)) ? 'error' : 'ok';

		digirisk_user_notification::log_element_modification($_POST['tableElement'], $_POST['idElement'], 'delete_follow_up', $_POST['specific_follow_up'], '');
	}
	else {
		if ( empty( $_POST[TABLE_ACTIVITE_SUIVI] ) ) {
			foreach ( $_POST as $key => $value ) {
				if ( substr($key, 0, strlen( TABLE_ACTIVITE_SUIVI) ) == TABLE_ACTIVITE_SUIVI ) {
					$_POST[TABLE_ACTIVITE_SUIVI][substr($key, strlen( TABLE_ACTIVITE_SUIVI) + 1 )] = $value;
				}
			}
		}

		$save_activite_result = suivi_activite::save_suivi_activite( $_POST['specific_follow_up'], $_POST['tableElement'], $_POST['idElement'], $_POST[TABLE_ACTIVITE_SUIVI] );

		if ( !empty( $_POST['specific_follow_up']) ) {
			digirisk_user_notification::log_element_modification($_POST['tableElement'], $_POST['idElement'], 'update_follow_up', $follow_up_details, $_POST[TABLE_ACTIVITE_SUIVI]);
		}
		else {
			digirisk_user_notification::log_element_modification($_POST['tableElement'], $_POST['idElement'], 'add_follow_up', $follow_up_details, $_POST[TABLE_ACTIVITE_SUIVI]);
		}
	}

	$text_response = '';
	$activity_current_progression_status = $activite_status_output = '';
	switch ( $_POST['tableElement'] ) {
		case TABLE_ACTIVITE :
			$activity_follow_up_list = suivi_activite::getSuiviActivite($_POST['tableElement'], $_POST['idElement'], 'follow_up');
			$elapsed_time = $global_cost = 0;
			$min_start_date = $max_end_date = 0000-00-00;
			$activity_current_progression_status = 'notStarted';
			foreach ( $activity_follow_up_list as $activity_follow_up ) {
				if ( !empty($activity_follow_up->elapsed_time) ) {
					$elapsed_time += $activity_follow_up->elapsed_time;
				}
				if ( !empty($activity_follow_up->cost) ) {
					$global_cost += $activity_follow_up->cost;
				}
				if ( empty($min_start_date) || ($min_start_date == '0000-00-00') || ( $min_start_date > $activity_follow_up->date_ajout ) ) {
					$min_start_date = $activity_follow_up->date_ajout;
				}
				if ( empty($max_end_date) || ($max_end_date == '0000-00-00') || ( $max_end_date < $activity_follow_up->date_ajout ) ) {
					$max_end_date = $activity_follow_up->date_ajout;
				}
			}
			if ( !empty($elapsed_time) || !empty($global_cost) ) {
				$activity = new EvaActivity( $_POST['idElement'] );
				$activity->load();
				$activity->setelapsed_time( $elapsed_time );
				$activity_current_progression_status = $activity->getProgressionStatus();
				if ( !empty($elapsed_time) && ($activity_current_progression_status == 'notStarted') ) {
					$activity->setProgressionStatus( 'inProgress' );
				}
				$activity->setcout_reel( $global_cost );
				$activity->setreal_start_date( $min_start_date );
				$activity->setreal_end_date( $max_end_date );

				$activity->save();

				$tache = new EvaTask( $activity->getRelatedTaskId() );
				$tache->load();
				$tache->getTimeWindow();
				$tache->computeProgression();
				$tache->save();

				ob_start();
				suivi_activite::digi_postbox_project( $_POST );
				$text_response = ob_get_contents();
				ob_end_clean();
				$activite_status_output = $activity->getProgression() . '%&nbsp;(' . actionsCorrectives::check_progression_status_for_output($activity->getProgressionStatus()) . ')';
			}
		break;
	}

	if ( (!empty($_POST[TABLE_ACTIVITE_SUIVI]['follow_up_type']) && ($_POST[TABLE_ACTIVITE_SUIVI]['follow_up_type'] == 'note')) || ( !empty( $_POST['sub_action'] ) && ($_POST['sub_action'] == 'delete')) ) {
		$text_response = suivi_activite::formulaireAjoutSuivi($_POST['tableElement'], $_POST['idElement'], true, '', TABLE_ACTIVITE_SUIVI);
	}

	echo json_encode( array($save_activite_result, $text_response, $activity_current_progression_status, $activite_status_output, ) );
	die();
}
add_action('wp_ajax_digi_ajax_save_activite_follow', 'digi_ajax_save_activite_follow');

/**
 * Output the list of comment for a given element
 */
function digi_ajax_load_activite_follow() {
	check_ajax_referer( 'digi_ajax_load_activite_follow', 'digi_ajax_nonce' );

	echo suivi_activite::tableauSuiviActivite($_POST['tableElement'], $_POST['idElement'], $_POST['follow_up_type']);

	die();
}
add_action('wp_ajax_digi_ajax_load_activite_follow', 'digi_ajax_load_activite_follow');


/**
 *
 *
 *	Risk mass updater interface
 *
 *
 */
/**
 * Load mass updater interface
 */
function digi_ajax_load_mass_modification() {
	$output = '
<div id="ajax-response-massUpdater" class="hide" >&nbsp;</div>
<div id="messageRisqMassUpdater" class="evaMessage hide fade updated" >&nbsp;</div>
<div class="massUpdaterListing" >
	<form method="post" id="form_mass_updater" action="' . admin_url('admin-ajax.php') . '" >
		<input type="hidden" name="action" value="digi_ajax_save_mass_modification" />
		<input type="hidden" name="tableElement" value="' . $_POST['tableElement'] . '" />
		<input type="hidden" name="idElement" value="' . $_POST['idElement'] . '" />
		' . eva_documentUnique::bilanRisque($_POST['tableElement'], $_POST['idElement'], 'ligne', 'massUpdater') . '
	</form>
</div>

<div id="mass_update_button_pane_helper" >
	<div class="clear alignright" ><span id="checkAllBoxMassUpdater" class="massUpdaterChecbkoxAction" >' . __('Tout cocher', 'evarisk') . '</span>&nbsp;/&nbsp;<span id="uncheckAllBoxMassUpdater" class="massUpdaterChecbkoxAction" >' . __('Tout d&eacutecocher', 'evarisk') . '</span>&nbsp;/&nbsp;<span id="reverseSelectionBoxMassUpdater" class="reverseSelectionBoxMassUpdater massUpdaterChecbkoxAction" >' . __('Inverser la s&eacute;lection', 'evarisk') . '</span><img src="' . EVA_ARROW_TOP . '" alt="arrow_top" class="checkboxRisqMassUpdaterSelector_bottom" /></div>
	<div class="clear alignright risqMassUpdaterChooserExplanation" >' . __('Cochez les cases pour prendre en compte les modifications', 'evarisk') . '</div>
</div>

<script type="text/javascript" >
	digirisk("#risqMassUpdater textarea").keypress(function(){
		var checkbox_for_current_line = jQuery(this).closest("tr").children(".columnCBRisqueMassUpdater").children("input[type=checkbox]").attr("id");
		jQuery("#" + checkbox_for_current_line).prop("checked", "checked");
	});
	digirisk("#risqMassUpdater textarea").mousedown(function(){
		var checkbox_for_current_line = jQuery(this).closest("tr").children(".columnCBRisqueMassUpdater").children("input[type=checkbox]").attr("id");
		jQuery("#" + checkbox_for_current_line).prop("checked", "checked");
	});

	digirisk("#checkAllBoxMassUpdater").unbind("click");
	digirisk("#checkAllBoxMassUpdater").live("click", function(){
		digirisk(".checkboxRisqMassUpdater").each(function(){
			digirisk(this).prop("checked", "checked");
		});
	});
	digirisk("#uncheckAllBoxMassUpdater").unbind("click");
	digirisk("#uncheckAllBoxMassUpdater").live("click", function(){
		digirisk(".checkboxRisqMassUpdater").each(function(){
			digirisk(this).prop("checked", "");
		});
	});
	digirisk(".reverseSelectionBoxMassUpdater").click(function(){
		digirisk(".checkboxRisqMassUpdater").each(function(){
			console.log(digirisk(this).prop("checked"));
			if(digirisk(this).is(":checked")){
				digirisk(this).prop("checked", "");
			}
			else{
				digirisk(this).prop("checked", "checked");
			}
		});
	});
	jQuery(document).ready(function(){
		jQuery("#form_mass_updater").ajaxForm({
			success: function(responseText, statusText, xhr, $form) {
				jQuery("#messageRisqMassUpdater").html( responseText );
				jQuery("#messageRisqMassUpdater").show();
			}
		});
	});
</script>';

	echo $output;
	die();
}
add_action('wp_ajax_digi_ajax_load_mass_modification', 'digi_ajax_load_mass_modification');

/**
 * Save mass modification
 */
function digi_ajax_save_mass_modification() {
	global $wpdb;

	$actions_message = '';
	$correctiv_action_no_error = $risk_comment_no_error = '';
	if ( !empty( $_POST['checkboxRisqMassUpdater'] ) ) {
		$correctiv_action_no_error = $risk_comment_no_error = true;
		foreach ( $_POST['checkboxRisqMassUpdater'] as $risk_id ) {

			/**	Save risk comment	*/
			if ( !empty( $_POST['risqComment'] ) && !empty( $_POST['risqComment'][$risk_id] ) ) {
				foreach ( $_POST['risqComment'][$risk_id] as $comment_id => $risk_comment_content) {
					$query = $wpdb->prepare("SELECT id_element FROM " . TABLE_ACTIVITE_SUIVI . " WHERE id = %s", $comment_id);
					$element_id = $wpdb->get_var( $query );
					if ( empty( $risk_comment_content['export'] ) ) {
						$risk_comment_content['export'] = 'no';
					}
					$save_activite_result = suivi_activite::save_suivi_activite( $comment_id, TABLE_AVOIR_VALEUR, $element_id, $risk_comment_content );
					if ( $save_activite_result != 'ok' ) {
						$risk_comment_no_error = false;
					}
				}
			}

			/**	Save correctiv actions	*/
			if ( !empty( $_POST['risqPrioritaryCA'] ) && !empty( $_POST['risqPrioritaryCA'][$risk_id] ) ) {
				foreach ( $_POST['risqPrioritaryCA'][$risk_id] as $correctiv_action_id => $correctiv_action_content) {
					$correctiv_action_no_error = $wpdb->update(TABLE_TACHE, array('description' => $correctiv_action_content), array('id' => $correctiv_action_id));
				}
			}

		}
	}

	if ( $risk_comment_no_error === false ) {
		$actions_message .= '<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="response" style="vertical-align:middle;" />' . __('Une ou plusieurs erreurs sont survenues lors de l\'enregistrement des corrections pour les risques.', 'evarisk');
	}
	else if ( $risk_comment_no_error === true ) {
		$actions_message .= '<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />' . __('Tous les risques ont &eacute;t&eacute; mis &agrave; jour', 'evarisk');
	}

	if ( $risk_comment_no_error === false ) {
		$actions_message .= '<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="response" style="vertical-align:middle;" />' . __('Une ou plusieurs erreurs sont survenues lors de l\'enregistrement des corrections pour les actions prioritaires.', 'evarisk');
	}
	else if ( $risk_comment_no_error === true ) {
		$actions_message .= '<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />' . __('Tous les actions prioritaire ont &eacute;t&eacute; mise &agrave; jour', 'evarisk');
	}

	echo $actions_message;
	die();
}
add_action('wp_ajax_digi_ajax_save_mass_modification', 'digi_ajax_save_mass_modification');


/**
 *
 *
 * Correctiv actions
 *
 *
 */
/**
 * Save creation or modification on a task of correctiv actions
 */
function digi_ajax_save_correctiv_actions_task() {
	global $wpdb;

	switch ( $_POST['act'] ) {
		case 'save':
			$action = __('sauvegard&eacute;e', 'evarisk');
			break;
		case 'update':
			$action = __('mise &agrave; jour', 'evarisk');
			break;
		case 'taskDone':
			$action = __('sold&eacute;e', 'evarisk');
			break;
	}

	$tache = new EvaTask($_POST['id']);
	$tache->load();
	$old_tache = new EvaTask($_POST['id']);
	$old_tache->load();
	$tache->setName($_POST['nom_tache']);
	$tache->setDescription($_POST['description']);
	$tache->setIdFrom($_POST['idProvenance']);
	$tache->setTableFrom($_POST['tableProvenance']);
	$tache->setnom_exportable_plan_action(!empty($_POST['nom_exportable_plan_action'])?$_POST['nom_exportable_plan_action']:'no');
	$tache->setdescription_exportable_plan_action(!empty($_POST['description_exportable_plan_action'])?$_POST['description_exportable_plan_action']:'no');
	$tache->setProgressionStatus('notStarted');

	if ( !empty( $_POST['avancement'] ) || ( $tache->getProgressionStatus() == 'inProgress' ) ) {
		$tache->setProgressionStatus('inProgress');
	}

	$tache->setidResponsable($_POST['responsable_tache']);
	$tache->setEfficacite($_POST['correctiv_action_efficiency_control']);
	if ( $_POST['act'] == 'taskDone' ) {
		global $current_user;
		$tache->setidSoldeur($current_user->ID);
		$tache->setProgression($_POST['avancement']);
// 		$tache->setStartDate($_POST['date_debut']);
// 		$tache->setFinishDate($_POST['date_fin']);
		$tache->setProgressionStatus('Done');
		$tache->setdateSolde(current_time('mysql', 0));

		/*	Get the task subelement to set the progression status to DoneByChief	*/
		if ( $_POST['markAllSubElementAsDone'] == 'true' )
			$tache->markAllSubElementAsDone($_POST['avancement'], $_POST['date_fin'], $_POST['date_debut']);
	}
	if ( $tache->getLeftLimit() == 0 ) {
		$racine = new EvaTask(1);
		$racine->load();
		$tache->setLeftLimit($racine->getRightLimit());
		$tache->setRightLimit(($racine->getRightLimit()) + 1);
		$racine->setRightLimit(($racine->getRightLimit()) + 2);
		$racine->save();
	}

	if ( $_POST['act'] != 'taskDone' )
		$tache->computeProgression();

	$tache->save();
	$tacheMere = new EvaTask();
	$tacheMere->convertWpdb(Arborescence::getPere(TABLE_TACHE, $tache->convertToWpdb()));
	if ( $_POST['idPere'] != $tacheMere->getId() ) {
		$tache->transfert($_POST['idPere']);
	}

	if ( $tache->getStatus() != 'error' ) {
		/*	Reload the task content for future action	*/
		$tache->load();

		/*	Check the state of export checkboxes in order to update sub element of current element	*/
		if ( $tache->getnom_exportable_plan_action() == 'no' ) {

			/*	Set subtask exportble status	*/
			$query = $wpdb->prepare( "UPDATE " . TABLE_ACTIVITE . " SET nom_exportable_plan_action = 'no', description_exportable_plan_action = 'no' WHERE id_tache = %d", $tache->getId() );
			$wpdb->query( $query );

			/*	Change the sub task exportable status if no is selected for the current element	*/
			$task_children = $tache->getDescendants();
			if ( !empty ( $task_children->tasks ) ) {
				foreach ( $task_children->tasks as $task_id => $task_detail ) {
					$sub_task = new EvaTask($task_id);
					$sub_task->load();

					$sub_task->setnom_exportable_plan_action('no');
					$query = $wpdb->prepare( "UPDATE " . TABLE_ACTIVITE . " SET nom_exportable_plan_action = 'no', description_exportable_plan_action = 'no' WHERE id_tache = %d", $task_id );
					$wpdb->query( $query );
					$sub_task->save();
				}
			}

		}

		/*	Log the different modification	*/
		switch ( $_POST['act'] ) {
			case 'save':
				/*	Log modification on element and notify user if user subscribe	*/
				digirisk_user_notification::log_element_modification(TABLE_TACHE, $_POST['idPere'], 'add_new_subtask', '', array(TABLE_TACHE, $tache->getId(), $_POST['nom_tache'], $_POST['description']));
				break;
			case 'update':
				/*	Log modification on element and notify user if user subscribe	*/
				digirisk_user_notification::log_element_modification(TABLE_TACHE, $_POST['id'], 'update', $old_tache, $tache);
				unset($old_tache);
				break;
			case 'taskDone':
				/*	Log modification on element and notify user if user subscribe	*/
				digirisk_user_notification::log_element_modification(TABLE_TACHE, $_POST['id'], 'mark_done', '', '');
				break;
		}
	}

	$tache->load();
	$the_task_id = $tache->getId();
	$task_id = !empty($the_task_id) ? $the_task_id : $_POST['id'];

	echo json_encode( array($tache->getStatus(), stripslashes($_POST['nom_tache']), $task_id) );
	die();
}
add_action('wp_ajax_digi_ajax_save_correctiv_actions_task', 'digi_ajax_save_correctiv_actions_task');

/**
 *
 *
 * Risks
 *
 *
 */
function digi_ajax_reload_unassociated_risk_to_pics() {
	echo Risque::getRisqueNonAssociePhoto($_POST['tableElement'], $_POST['idElement']);
	die();
}
add_action('wp_ajax_digi_ajax_reload_unassociated_risk_to_pics', 'digi_ajax_reload_unassociated_risk_to_pics');

/**
 *
 *	Dashboard Stats
 *
 */
function digi_ajax_load_field_for_export() {
	$output = '';
	$available_fields = unserialize( DIGI_AVAILABLE_FIELDS_FOR_EXPORT );

	$export_type = digirisk_tools::IsValid_Variable( $_POST['export_type'] );
	$more_output = '';
	$selected['user'] = $selected['tree_element'] = $selected['global'] = '';
	switch ( $export_type ) {
		case 'user':
			unset($available_fields['user_lastname']);
			unset($available_fields['user_firstname']);
			$more_output = '
	<div id="digi_user_summary_zipfile_container" >
		' . eva_GroupSheet::getGroupSheetCollectionHistory('all', 0, 'user_summary_file', ELEMENT_IDENTIFIER_GUS) . '
	</div>';
			$selected['user'] = ' checked="checked"';
		break;
		case 'tree_element':
			unset($available_fields['ref_elt']);
			unset($available_fields['name_elt']);
			$selected['tree_element'] = ' checked="checked"';
		break;
		case 'global':
			$selected['global'] = ' checked="checked"';
		break;
	}

	$output .= '<div id="digi_export_csv_file_result" ></div>' . __('S&eacute;lectionnez les colonnes que vous souhaitez avoir dans le fichier export&eacute;.', 'evarisk') . '<br/>' . __('Vous pouvez &eacute;galement ordonner les colonnes en glissant/d&eacute;posant les diff&eacute;rents &eacute;l&eacute;ments.', 'evarisk') . '
			<div style="width:70%; margin:0 auto;" >
				<input type="radio"' . $selected['user'] . ' class="export_csv_file_type_choice" id="export_csv_file_per_user" name="export_csv_file" value="user" /> <label style="vertical-align: baseline;" for="export_csv_file_per_user" >' . __('Un fichier par personne', 'evarisk') . '</label>
				<input type="radio"' . $selected['global'] . ' class="export_csv_file_type_choice" id="export_csv_file_global" name="export_csv_file" value="global" /> <label style="vertical-align: baseline;" for="export_csv_file_global" >' . __('Un fichier global', 'evarisk') . '</label>
			</div>
			<ul id="digi_field_list_for_export" >';
	foreach ( $available_fields as $field_key => $field_title ) {
		$output .= '<li><input type="checkbox" value="' . $field_key . '" id="' . $field_key . '" name="digi_column_to_export[]" class="digi_column_to_export_input" > <label for="' . $field_key . '" >' . $field_title . '</label></li>';
	}
	$output .= '</ul>
	<button id="digi_export_csv_file" class="alignright button-primary" >' . __('Exporter le fichier', 'evarisk') . '</button>' . $more_output . '
<style type="text/css" >
#digi_field_list_for_export li {
	background-color: #CCCCCC;
    border: 1px solid #000000;
    border-radius: 13px 13px 13px 13px;
    padding: 0 12px;
	cursor: move;
	margin: 3px auto;
    width: 40%;
}
#digi_field_list_for_export li label {
	padding: 5px 0 0;
}
#digi_field_list_for_export li input[type=checkbox] {
	margin: 5px 0;
}
</style>
<script type="text/javascript" >
	digirisk(document).ready(function(){
		jQuery(".export_csv_file_type_choice").click(function(){
			jQuery("#digi_stats_user_dialog").html( jQuery("#loadingPicContainer").html() );
			jQuery.post("' . admin_url('admin-ajax.php') . '", {action: "digi_ajax_load_field_for_export", export_type: jQuery(this).val(),}, function(response){
				jQuery("#digi_stats_user_dialog").html( response );
			});
		});

		jQuery("#digi_field_list_for_export").sortable({});
		jQuery("#digi_export_csv_file").click(function(){
			var column_to_export = new Array;
			jQuery(".digi_column_to_export_input").each(function(){
				if ( jQuery(this).is(":checked") ) {
					column_to_export.push( jQuery(this).val() );
				}
			});

			var data = {
				action: "digi_ajax_export_csv_file",
				export_type: "' . $export_type . '",
				column_to_export: column_to_export,
			};
			jQuery.post("' . admin_url( 'admin-ajax.php' ) . '", data, function (response) {
				jQuery("#digi_export_csv_file_result").html( digi_html_accent_for_js(response[0]) );
				jQuery("#digi_user_summary_zipfile_container").html( digi_html_accent_for_js(response[1]) );
			}, "json");
		});
	});
</script>';

	echo $output;
	die();
}
add_action( 'wp_ajax_digi_ajax_load_field_for_export', 'digi_ajax_load_field_for_export' );
add_action( 'wp_ajax_digi_ajax_export_csv_file', array('eva_gestionDoc', 'digi_ajax_export_csv_file') );

/**	User stats tab on dashboard	*/
add_action( 'wp_ajax_digi_ajax_stats_user', array('evaUser', 'digi_ajax_stats_user') );
add_action( 'wp_ajax_digi_ajax_load_user_stat', array('evaUser', 'digi_ajax_load_user_stat') );
add_action( 'wp_ajax_digi_ajax_load_user_stat_mouvement_between_dates', array('evaUser', 'digi_ajax_mouvement_between_dates') );
/**	Risk stats tab on dashboard*/
add_action( 'wp_ajax_digi_ajax_risk_stats', array('Risque', 'digi_ajax_risk_stats') );

/**	Penibility file export*/
add_action( 'wp_ajax_digi_ajax_save_document', array('eva_gestionDoc', 'digi_ajax_save_document') );
add_action( 'wp_ajax_digi_ajax_duplicate_document', array('eva_gestionDoc', 'duplicate_document') );

function digi_ajax_save_task_for_risk() {
	/**	Check if there are recommendation to link with this risk	*/
	$preconisationRisqueTitle = !empty($_POST['title']) ? digirisk_tools::IsValid_Variable($_POST['title']) : '';
	$preconisationRisque = !empty($_POST['description']) ? digirisk_tools::IsValid_Variable($_POST['description']) : '';
	if ( !empty($preconisationRisque) || !empty($preconisationRisqueTitle ) ) {
		EvaTask::save_task_from_risk_form($preconisationRisqueTitle, $preconisationRisque, $_POST['id_risque']);
	}

	die();
}
add_action( 'wp_ajax_digi_ajax_save_task_for_risk', 'digi_ajax_save_task_for_risk' );
/**
 * Affect a recommandation to an element
 */
function digi_ajax_save_single_recommandation() {
	$response = '';

	$id = (isset($_POST['recommandation_id']) && ($_POST['recommandation_id'] != '') && ($_POST['recommandation_id'] != '0')) ? digirisk_tools::IsValid_Variable($_POST['recommandation_id']) : '';
	$recommandationEfficiency = (isset($_POST['recommandation_efficiency']) && ($_POST['recommandation_efficiency'] != '') && ($_POST['recommandation_efficiency'] != '0')) ? digirisk_tools::IsValid_Variable($_POST['recommandation_efficiency']) : '0';
	$recommandationComment = (isset($_POST['recommandation_comment']) && ($_POST['recommandation_comment'] != '')) ? digirisk_tools::IsValid_Variable($_POST['recommandation_comment']) : '';
	$id_element = (isset($_POST['id_element']) && ($_POST['id_element'] != '') && ($_POST['id_element'] != '0')) ? digirisk_tools::IsValid_Variable($_POST['id_element']) : '';
	$table_element = (isset($_POST['table_element']) && ($_POST['table_element'] != '')) ? digirisk_tools::IsValid_Variable($_POST['table_element']) : '';
	$preconisation_type = (isset($_POST['recommandation_type']) && ($_POST['recommandation_type'] != '')) ? digirisk_tools::IsValid_Variable($_POST['recommandation_type']) : '';

	$recommandation_link_action = (isset($_POST['recommandation_action']) && ($_POST['recommandation_action'] != '')) ? digirisk_tools::IsValid_Variable($_POST['recommandation_action']) : '';
	$recommandation_link_id = (isset($_POST['recommandation_to_update']) && ($_POST['recommandation_to_update'] != '')) ? digirisk_tools::IsValid_Variable($_POST['recommandation_to_update']) : '';

	$recommandationsinformations = array();
	$recommandationsinformations['id_preconisation'] = $id;
	$recommandationsinformations['efficacite'] = $recommandationEfficiency;
	$recommandationsinformations['commentaire'] = $recommandationComment;
	$recommandationsinformations['preconisation_type'] = $preconisation_type;

	if ( $recommandation_link_action == 'update' ) {
		$recommandationsinformations['date_update_affectation'] = current_time('mysql', 0);
		$recommandationActionResult = evaRecommandation::updateRecommandationAssociation($recommandationsinformations, $recommandation_link_id);
	}
	else {
		$recommandationsinformations['id_element'] = $id_element;
		$recommandationsinformations['table_element'] = $table_element;
		$recommandationsinformations['status'] = 'valid';
		$recommandationsinformations['date_affectation'] = current_time('mysql', 0);
		$recommandationActionResult = evaRecommandation::saveRecommandationAssociation($recommandationsinformations);
	}

	$response[] = '#message' . TABLE_PRECONISATION . '-' . $table_element;
	if ( $recommandationActionResult == 'error' ) {
		$response[] = false;
		$response[] ='<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" class="messageIcone" alt="error" />' . __('Une erreur est survenue lors de l\'enregistrement de la pr&eacute;conisation. Merci de r&eacute;essayer.', 'evarisk');
	}
	else {
		$response[] = true;
		$response[] =  '<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" class="messageIcone" alt="success" />' . __('La pr&eacute;conisation a correctement &eacute;t&eacute; enregistr&eacute;e.', 'evarisk');
	}
	$response[] = $id_element;
	$response[] = $table_element;

	echo json_encode( $response );
	die();
}
add_action( 'wp_ajax_digi_ajax_save_single_recommandation', 'digi_ajax_save_single_recommandation' );

function digi_ajax_load_recommandation_form() {
	$response = '';

	$id_element = !empty( $_POST['id_element'] ) ? $_POST['id_element'] : 0;
	$table_element = !empty( $_POST['table_element'] ) ? $_POST['table_element'] : '';

	$response[] = $table_element;
	$response[] = $id_element;
	$response[] = evaRecommandation::recommandationAssociation('pictos', '', array('idElement' => $id_element, 'table_element' => $table_element, 'hide_save_button' => true)) . evaRecommandation::getRecommandationListForElementOutput($table_element, $id_element, false);

	echo json_encode( $response );
	die();
}
add_action( 'wp_ajax_digi_ajax_load_recommandation_form', 'digi_ajax_load_recommandation_form' );
