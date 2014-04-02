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








function digi_ajax_delete_user_affectation() {
	global $wpdb;

	$deletion = $wpdb->update( TABLE_LIAISON_USER_ELEMENT, array( 'status' => 'deleted', 'date_desAffectation' => current_time( 'mysql', 0 ), 'id_desAttributeur' => get_current_user_id(), ), array( 'id' => $_POST[ 'affectation-id' ], ) );
	$message = __( 'Affectation supprim&eacute;e avec succ&eacute;s', 'digirisk' );
	if ( false === $deletion ) {
		$message = __( 'Une erreur est survenue lors de la suppression de l\'affectation', 'digirisk' );
	}
	wp_die( json_encode( array( 'status' => $deletion, 'link_id' => $_POST[ 'affectation-id' ], 'message' => $message, ) ) );
}
add_action('wp_ajax_digi_ajax_delete_user_affectation', 'digi_ajax_delete_user_affectation');

function digi_affect_users_to_evaluation() {
	global $wpdb;
	if ( !empty( $_POST ) && !empty( $_POST[ 'digi_user' ] ) && is_array( $_POST[ 'digi_user' ] ) ) {
		$has_error = true;
		foreach ( $_POST[ 'digi_user' ] as $user_id ) {
			$date_addition = null;
			if ( !empty( $_POST[ 'digi_user_time' ] ) || !empty( $_POST[ 'digi_user_time' ][ $user_id ] ) ) {
				$date_addition[] = !empty( $_POST[ 'digi_user_time' ][ $user_id ][ 'hour' ] ) ? ' ' . $_POST[ 'digi_user_time' ][ $user_id ][ 'hour' ] . ' hours' : '';
				$date_addition[] = !empty( $_POST[ 'digi_user_time' ][ $user_id ][ 'minutes' ] ) ? ' ' . $_POST[ 'digi_user_time' ][ $user_id ][ 'minutes' ] . ' minutes' : '';
			}
			else if ( !empty( $_POST[ 'digi-users-affectation-default-duration-hour' ] ) || !empty( $_POST[ 'digi-users-affectation-default-duration-minutes' ] ) ) {
				$date_addition[] = !empty( $_POST[ 'digi-users-affectation-default-duration-hour' ] ) ? ' ' . $_POST[ 'digi-users-affectation-default-duration-hour' ] . ' hours' : '';
				$date_addition[] = !empty( $_POST[ 'digi-users-affectation-default-duration-minutes' ] ) ? ' ' . $_POST[ 'digi-users-affectation-default-duration-minutes' ] . ' minutes' : '';
			}
			$date_desAffectation_reelle = !empty( $date_addition ) ? date( 'Y-m-d H:i:s', strtotime( '+' . implode( ' ', $date_addition ), strtotime( $_POST[ 'date_affectation_reelle' ] ) ) ) : '';
			if ( !empty( $date_desAffectation_reelle ) ) {
				$new_link = $wpdb->insert( TABLE_LIAISON_USER_ELEMENT, array(
					'status' => 'valid',
					'date_affectation' => current_time( 'mysql', 0 ),
					'id_attributeur' => get_current_user_id(),
					'id_user' => $user_id,
					'id_element' => $_POST[ 'idElement' ],
					'table_element' => $_POST[ 'tableElement' ],
					'date_affectation_reelle' => $_POST[ 'date_affectation_reelle' ],
					'date_desaffectation_reelle' => $date_desAffectation_reelle,
				) );
				$has_error = $has_error && (false === $new_link) ? $new_link : $has_error;
			}
		}
	}

	require_once(EVA_METABOXES_PLUGIN_DIR . 'utilisateurs/liaisonUtilisateursElement.php');
	getParticipantPostBoxBody( array( 'idElement' => $_POST[ 'idElement' ], 'tableElement' => $_POST[ 'tableElement' ] ), array( 'action_success' => $has_error, ) );
	wp_die();
}
add_action('wp_ajax_digi_affect_users_to_evaluation', 'digi_affect_users_to_evaluation');


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
function display_form_mas_updater( $tableELement, $idElement ) {
	return '<form method="post" id="form_mass_updater" action="' . admin_url('admin-ajax.php') . '" >
		<input type="hidden" name="action" value="digi_ajax_save_mass_modification" />
		<input type="hidden" name="tableElement" value="' . $tableELement . '" />
		<input type="hidden" name="idElement" value="' . $idElement . '" />
		' . eva_documentUnique::bilanRisque($tableELement, $idElement, 'ligne', 'massUpdater') . '
	</form>';
}
/**
 * Load mass updater interface
 */
function digi_ajax_load_mass_modification() {
	$output = '
<div id="ajax-response-massUpdater" class="hide" >&nbsp;</div>
<div id="messageRisqMassUpdater" class="evaMessage hide fade updated" >&nbsp;</div>
<div class="massUpdaterListing" >
	' . display_form_mas_updater($_POST['tableElement'], $_POST['idElement']) . '
</div>

<div id="mass_update_button_pane_helper" >
	<div class="clear alignright" ><span id="checkAllBoxMassUpdater" class="massUpdaterChecbkoxAction" >' . __('Tout cocher', 'evarisk') . '</span>&nbsp;/&nbsp;<span id="uncheckAllBoxMassUpdater" class="massUpdaterChecbkoxAction" >' . __('Tout d&eacutecocher', 'evarisk') . '</span>&nbsp;/&nbsp;<span id="reverseSelectionBoxMassUpdater" class="reverseSelectionBoxMassUpdater massUpdaterChecbkoxAction" >' . __('Inverser la s&eacute;lection', 'evarisk') . '</span><img src="' . EVA_ARROW_TOP . '" alt="arrow_top" class="checkboxRisqMassUpdaterSelector_bottom" /></div>
	<div style="margin:9px 0 0; float:left; width:51%; text-align:right; cursor:pointer;  font-size:11px;" id="digi_use_parent_date_for_all" >' . __('Utiliser la date de l\'employeur pour les dates de d&eacute;but', 'evarisk') . '</div>
	<div class="clear alignright risqMassUpdaterChooserExplanation" >' . __('Cochez les cases pour prendre en compte les modifications', 'evarisk') . '</div>
</div>

<script type="text/javascript" >
	digirisk("#risqMassUpdater textarea, #risqMassUpdater input").keypress(function(){
		var checkbox_for_current_line = jQuery(this).closest("tr").children(".columnCBRisqueMassUpdater").children("input[type=checkbox]").attr("id");
		jQuery("#" + checkbox_for_current_line).prop("checked", "checked");
	});
	digirisk("#risqMassUpdater textarea, #risqMassUpdater input").mousedown(function(){
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
		jQuery("#digi_use_parent_date_for_all").click(function(){
			jQuery( ".digi_use_parent_date_for_risk" ).each( function(){
				jQuery(this).click();
			});
		});

		jQuery("#form_mass_updater").ajaxForm({
			dataType: "json",
			success: function(response) {
				jQuery("#messageRisqMassUpdater").html( response["message"] );
				jQuery("#messageRisqMassUpdater").show();
				jQuery("#ongletVoirLesRisques").click();
				jQuery(".checkboxRisqMassUpdater").each(function(){
					jQuery(this).prop("checked", false);
				});
			},
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

			/**	Save risk dates	*/
			if ( !empty( $_POST['risqDate'] ) && !empty( $_POST['risqDate'][$risk_id] ) ) {
				$params = array();
				$dateDebutRisque = $dateFinRisque = null;
				if ( !empty($_POST['risqDate'][$risk_id]['dateDebutRisque']) && ($_POST['risqDate'][$risk_id]['dateDebutRisque'] != '0000-00-00 00:00') ) {
					$params['dateDebutRisque'] = $_POST['risqDate'][$risk_id]['dateDebutRisque'] ;
				}
				if ( !empty($_POST['risqDate'][$risk_id]['dateFinRisque']) && ($_POST['risqDate'][$risk_id]['dateFinRisque'] != '0000-00-00 00:00') ) {
					$params['dateFinRisque'] = $_POST['risqDate'][$risk_id]['dateFinRisque'] ;
					$params['risk_status'] = 'closed' ;
				}
				if ( !empty($params) ) {
					$query = $wpdb->prepare( "SELECT dateDebutRisque, dateFinRisque FROM " . TABLE_RISQUE . " WHERE id = %d", $risk_id );
					$current_date = $wpdb->get_row( $query );
					$wpdb->insert( TABLE_RISQUE_HISTO, array('id_risque' => $risk_id, 'date' => current_time( 'mysql', 0), 'field' => 'dateDebutRisque', 'value' => $current_date->dateDebutRisque) );
					if ( !empty($current_date->dateFinRisque) && ($current_date->dateFinRisque != '0000-00-00 00:00:00') ) {
						$wpdb->insert( TABLE_RISQUE_HISTO, array('id_risque' => $risk_id, 'date' => current_time( 'mysql', 0), 'field' => 'dateFinRisque', 'value' => $current_date->dateFinRisque) );
					}

					$wpdb->update( TABLE_RISQUE, $params, array('id' => $risk_id) );
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
		$actions_message .= '<br/><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="response" style="vertical-align:middle;" />' . __('Une ou plusieurs erreurs sont survenues lors de l\'enregistrement des corrections pour les actions prioritaires.', 'evarisk');
	}
	else if ( $risk_comment_no_error === true ) {
		$actions_message .= '<br/><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />' . __('Tous les actions prioritaire ont &eacute;t&eacute; mise &agrave; jour', 'evarisk');
	}

	echo json_encode( array( "message" => $actions_message, "response" => display_form_mas_updater( $_POST['tableElement'], $_POST['idElement'] ), ));
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

function digi_ajax_save_correctiv_action_sheet() {
	$tableElement = $_POST['tableElement'];
	$idElement = $_POST['idElement'];

	$response = actionsCorrectives::save_action_sheet( $tableElement, $idElement );
	if ( !empty( $_POST['create_recursiv_sheet'] ) && ('true' == $_POST['create_recursiv_sheet']) ) {
		$error = 0;
		$file_to_zip = array();

		if ( true === $response['status'] ) {
			$file_to_zip[] = EVA_RESULTATS_PLUGIN_DIR . $response[ 'reponse' ];
		}
		else {
			$error++;
		}

		$tache = new EvaTask( $idElement );
		$tache->load();
		$TasksAndSubTasks = $tache->getDescendants();
		$TasksAndSubTasks->addTask($tache);
		$TasksAndSubTasks = $TasksAndSubTasks->getTasks();
		if ( $TasksAndSubTasks != null AND count($TasksAndSubTasks) > 0 ) {
			foreach ( $TasksAndSubTasks as $task ) {
				if ( $task->id != $tache->id ) {
					$response = actionsCorrectives::save_action_sheet( TABLE_TACHE, $task->id );
					if ( true === $response['status'] ) {
						$file_to_zip[] = EVA_RESULTATS_PLUGIN_DIR . $response[ 'reponse' ];
					}
					else {
						$error++;
					}
				}
				$activities = $task->getActivitiesDependOn();
				$activities = $activities->getActivities();
				if ( ($activities != null) AND (count($activities) > 0) ) {
					foreach ( $activities as $activity ) {
						$response = actionsCorrectives::save_action_sheet( TABLE_ACTIVITE, $activity->id );
						if ( true === $response['status'] ) {
							$file_to_zip[] = EVA_RESULTATS_PLUGIN_DIR . $response[ 'reponse' ];
						}
						else {
							$error++;
						}
					}
				}
			}
		}

		/*	ZIP THE FILE	*/
		if ( !empty($file_to_zip) ) {
			$pathToZip = EVA_RESULTATS_PLUGIN_DIR . 'planDActions/' . $tableElement . '/' . $idElement. '/';
			if ( !is_dir($pathToZip) ) {
				mkdir( $pathToZip, 0755, true);
			}

			$zipFileName = date('Ymd') . '_' . ELEMENT_IDENTIFIER_T . $idElement . '_fichesPlanDAction.zip';
			$archive = new eva_Zip($zipFileName);
			$archive->setFiles($file_to_zip);
			$archive->compressToPath($pathToZip);
			eva_gestionDoc::saveNewDoc('printed_fiche_action', $tableElement, $idElement, str_replace(EVA_RESULTATS_PLUGIN_DIR, '', $pathToZip . $zipFileName));
		}
	}

	echo eva_gestionDoc::get_associated_document_list($tableElement, $idElement, 'printed_fiche_action', "dateCreation DESC, id DESC", EVA_RESULTATS_PLUGIN_DIR);
	die();
}
add_action( 'wp_ajax_digi_ajax_save_correctiv_action_sheet', 'digi_ajax_save_correctiv_action_sheet' );


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

function digi_ajax_reload_current_risk_cotation(){
	$score_risque = digirisk_tools::IsValid_Variable($_POST['score_risque']);
	$date = digirisk_tools::IsValid_Variable($_REQUEST['date']);
	$idMethode = digirisk_tools::IsValid_Variable($_REQUEST['idMethode']);

	$response['cotation'] = Risque::getEquivalenceEtalon($idMethode, $score_risque, $date);
	$response['seuil'] = (int)Risque::getSeuil( $response['cotation'] );

	echo json_encode( $response );
	die();
}
add_action('wp_ajax_digi_ajax_reload_current_risk_cotation', 'digi_ajax_reload_current_risk_cotation');

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
	$input_display_state = '';
	$required_fields = array();
	switch ( $export_type ) {
		case 'user':
			unset($available_fields['user_identifier']);
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
			$required_fields = array('user_identifier', 'user_lastname', 'user_firstname');
			$more_output = '
	<div id="digi_user_summary_zipfile_container" >
		' . eva_gestionDoc::getGeneratedDocument('all', 0, 'list', '', 'user_global_export', '0') . '
	</div>';
// 			$input_display_state = ' digirisk_hide';
		break;
	}

	$output .= '<div id="digi_export_csv_file_result" ></div>' . __('S&eacute;lectionnez les colonnes que vous souhaitez avoir dans le fichier export&eacute;.', 'evarisk') . '<br/>' . __('Vous pouvez &eacute;galement ordonner les colonnes en glissant/d&eacute;posant les diff&eacute;rents &eacute;l&eacute;ments.', 'evarisk') . '
			<div style="width:70%; margin:0 auto;" >
				<input type="radio"' . $selected['global'] . ' class="export_csv_file_type_choice" id="export_csv_file_global" name="export_csv_file" value="global" /> <label style="vertical-align: baseline;" for="export_csv_file_global" >' . __('Un fichier global', 'evarisk') . '</label>
				<input type="radio"' . $selected['user'] . ' class="export_csv_file_type_choice" id="export_csv_file_per_user" name="export_csv_file" value="user" /> <label style="vertical-align: baseline;" for="export_csv_file_per_user" >' . __('Un fichier par personne', 'evarisk') . '</label>
			</div>
			<ul id="digi_field_list_for_export" >';
	foreach ( $available_fields as $field_key => $field_title ) {
		$options = (!empty($required_fields) && in_array($field_key, $required_fields)) ? ' disabled="disabled"' : '';
		$output .= '<li><input type="checkbox" value="' . $field_key . '" id="' . $field_key . '" name="digi_column_to_export[]" class="digi_column_to_export_input' . $input_display_state . '" checked="checked"' . $options . ' > <label for="' . $field_key . '" >' . $field_title . '</label></li>';
	}
	$output .= '</ul>
	<button id="digi_export_csv_file" class="alignright button-primary" >' . __('Exporter le fichier', 'evarisk') . '</button>' . $more_output . '
<style type="text/css" >
#digi_field_list_for_export li {
	background-color: #CCCCCC;
    border: 1px solid #000000;
    border-radius: 13px 13px 13px 13px;
    padding: 0 12px;
// 	cursor: move;
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

		//jQuery("#digi_field_list_for_export").sortable({});
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
	$ids = (isset($_POST['recommandation_ids']) && ($_POST['recommandation_ids'] != '') && ($_POST['recommandation_ids'] != '0')) ? $_POST['recommandation_ids'] : '';
	$recommandationEfficiency = (isset($_POST['recommandation_efficiency']) && ($_POST['recommandation_efficiency'] != '') && ($_POST['recommandation_efficiency'] != '0')) ? digirisk_tools::IsValid_Variable($_POST['recommandation_efficiency']) : '0';
	$recommandationComment = (isset($_POST['recommandation_comment']) && ($_POST['recommandation_comment'] != '')) ? digirisk_tools::IsValid_Variable($_POST['recommandation_comment']) : '';
	$id_element = (isset($_POST['id_element']) && ($_POST['id_element'] != '') && ($_POST['id_element'] != '0')) ? digirisk_tools::IsValid_Variable($_POST['id_element']) : '';
	$table_element = (isset($_POST['table_element']) && ($_POST['table_element'] != '')) ? digirisk_tools::IsValid_Variable($_POST['table_element']) : '';
	$preconisation_type = (isset($_POST['recommandation_type']) && ($_POST['recommandation_type'] != '')) ? digirisk_tools::IsValid_Variable($_POST['recommandation_type']) : '';

	$recommandation_link_action = (isset($_POST['recommandation_action']) && ($_POST['recommandation_action'] != '')) ? digirisk_tools::IsValid_Variable($_POST['recommandation_action']) : '';
	$recommandation_link_id = (isset($_POST['recommandation_to_update']) && ($_POST['recommandation_to_update'] != '')) ? digirisk_tools::IsValid_Variable($_POST['recommandation_to_update']) : '';

	$has_error = false;
	if ( is_array($ids) && !empty($ids) ) {
		foreach ( $ids as $id ) {
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

			if ( $recommandationActionResult == 'error' ) {
				$has_error = true;
			}
		}
	}

	$response[] = '#message' . TABLE_PRECONISATION . '-' . $table_element;
	if ( $has_error ) {
		$response[] = false;
		$response[] ='<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" class="messageIcone" alt="error" />' . __('Une erreur est survenue lors de l\'enregistrement de la(des) pr&eacute;conisation(s). Merci de r&eacute;essayer.', 'evarisk');
	}
	else {
		$response[] = true;
		$response[] =  '<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" class="messageIcone" alt="success" />' . __('La(Les) pr&eacute;conisation(s) a(ont) correctement &eacute;t&eacute; enregistr&eacute;e(s).', 'evarisk');
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
	$response[] = evaRecommandation::recommandationAssociation('pictos', '', array('idElement' => $id_element, 'table_element' => $table_element, 'hide_save_button' => true, 'form_container' => (!empty($table_element) && !empty($id_element) ? 'digi_risk_eval_' . $table_element . '_' . $id_element . '_reco_container' : 'single_preco'))) . evaRecommandation::getRecommandationListForElementOutput($table_element, $id_element, false);

	echo json_encode( $response );
	die();
}
add_action( 'wp_ajax_digi_ajax_load_recommandation_form', 'digi_ajax_load_recommandation_form' );

function digi_ajax_load_recommandation_from_category() {
	$outputMode = (isset($_POST['outputMode']) && ($_POST['outputMode'] != '')) ? digirisk_tools::IsValid_Variable($_POST['outputMode']) : 'pictos';
	$id_categorie_preconisation = (isset($_POST['id_categorie_preconisation']) && ($_POST['id_categorie_preconisation'] != '') && ($_POST['id_categorie_preconisation'] != '0')) ? digirisk_tools::IsValid_Variable($_POST['id_categorie_preconisation']) : '';
	$arguments['form_container'] = !empty($_POST['specific_container']) ? digirisk_tools::IsValid_Variable( $_POST['specific_container'] ) : '';

	echo json_encode( array(evaRecommandation::getRecommandationListByCategory($id_categorie_preconisation, $outputMode, '', $arguments)) );
	die();
}
add_action( 'wp_ajax_digi_ajax_load_recommandation_from_category', 'digi_ajax_load_recommandation_from_category' );


function digi_ajax_regenerate_file() {
	$tableElement = $_POST['table_element'];
	$idElement = $_POST['id_element'];
	$idDocument = $_POST['doc_id'];
	$document_type = $_POST['document_type'];

	$result = array(false, $tableElement . '_' . $idElement . '_' . $idDocument, '<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'warning_vs.gif" alt="' . __('warning', 'evarisk') . '" />' . __('Une erreur est survenue lors de la génération', 'evarisk'));

	/**	Generate the document	*/
	$last_file = eva_gestionDoc::generateSummaryDocument($tableElement, $idElement, 'odt', $idDocument);

	/**	Check if document now exists	*/
	if ( is_file($last_file) ) {
		$result = array(true, $tableElement . '_' . $idElement . '_' . $idDocument, '<a href="' . str_replace(EVA_RESULTATS_PLUGIN_DIR, EVA_RESULTATS_PLUGIN_URL, $last_file) . '" target="evaFPOdt" >' . __('Odt', 'evarisk') . '</a>');
	}

	echo json_encode( $result );
	die();
}
add_action( 'wp_ajax_digi_ajax_regenerate_file', 'digi_ajax_regenerate_file' );

function digi_mass_change_user_informations() {
	$mass_user_form_content = '';
	global $wpdb;

	$query = $wpdb->prepare(
	"SELECT U.ID
	FROM {$wpdb->users} AS U
		LEFT JOIN {$wpdb->usermeta} AS UMETA ON (UMETA.user_id = U.ID)
	WHERE UMETA.meta_key = 'last_name'
	ORDER BY UMETA.meta_value", '');
	$userList = $wpdb->get_results($query);

	foreach($userList as $utilisateurs){
		if( $utilisateurs->ID != 1 ){

			$user_info = get_userdata($utilisateurs->ID);

			unset($valeurs);
			$valeurs['user_id'] = $user_info->ID;
			$valeurs['user_registered'] = $user_info->user_registered;
			if( (isset($user_info->user_lastname) && ($user_info->user_lastname != '')) ){
				$valeurs['user_lastname'] = $user_info->user_lastname;
			}
			else{
				$valeurs['user_lastname'] = '';
			}
			if( (isset($user_info->user_firstname) && ($user_info->user_firstname != '')) ){
				$valeurs['user_firstname'] = $user_info->user_firstname;
			}
			else{
				$valeurs['user_firstname'] = $user_info->user_nicename;
			}

			$listeUtilisateurs[$user_info->ID] = $valeurs;
		}
	}

	if(is_array($listeUtilisateurs) && (count($listeUtilisateurs) > 0)){
		$mass_user_form_content .= '
<table style="width: 100%;" >
	<tr style="background-color: #DDD;" >
		<th style="border:1px solid #CCC; padding: 5px;" >' . __('Id.', 'evarisk') . '</th>
		<th style="border:1px solid #CCC; padding: 5px;" >' . __('Nom', 'evarisk') . '</th>
		<th style="border:1px solid #CCC; padding: 5px;" >' . __('Prénom', 'evarisk') . '</th>
		<th style="border:1px solid #CCC; padding: 5px; width: 2%;" >' . __('Forcer MAJ', 'evarisk') . '</th>
		<th style="border:1px solid #CCC; padding: 5px;" >
			' . __('Date d\'embauche', 'evarisk') . '
			<br/><span style="font-size: 9px;" >' . __('YYYY-MM-DD ou DD/MM/YYYY', 'evarisk') . '</span>
		</th>
		<th style="border:1px solid #CCC; padding: 5px;" >
			' . __('Date de sortie', 'evarisk') . '
			<br/><span style="font-size: 9px;" >' . __('YYYY-MM-DD ou DD/MM/YYYY', 'evarisk') . '</span>
		</th>
	</tr>';
		foreach($listeUtilisateurs as $utilisateur){
			$user_meta = get_user_meta($utilisateur['user_id'], 'digirisk_information', false);
			$user_meta_hiring_date = get_user_meta($utilisateur['user_id'], 'digi_hiring_date', true);
			$user_meta_unhiring_date = get_user_meta($utilisateur['user_id'], 'digi_unhiring_date', true);
			$mass_user_form_content .= '
	<tr>
		<td style="border-bottom:1px solid #CCC; text-align: center;" >' . ELEMENT_IDENTIFIER_U . $utilisateur['user_id'] . '</td>
		<td style="border-bottom:1px solid #CCC;" >' . $utilisateur['user_lastname'] . '</td>
		<td style="border-bottom:1px solid #CCC;" >' . $utilisateur['user_firstname'] . '</td>
		<td style="border-bottom:1px solid #CCC; text-align: center;" ><input type="checkbox" value="' . $utilisateur['user_id'] . '" name="digi_user_force_update[]" /></td>
		<td style="border-bottom:1px solid #CCC; width: 165px;" >
			<input type="text" style="text-align: center;" id="user_date_input_digi_hiring_date_' . $utilisateur['user_id'] . '" name="digi_user_single_meta[' . $utilisateur['user_id'] . '][digi_hiring_date]" value="' . $user_meta_hiring_date . '" />
			<script type="text/javascript" >
				jQuery(document).ready(function(){
					jQuery("#user_date_input_digi_hiring_date_' . $utilisateur['user_id'] . '").datepicker({
						dateFormat: "yy-mm-dd",
						changeMonth: true,
						changeYear: true,
						navigationAsDateFormat: true,
					});
				});
				jQuery("#user_date_input_digi_hiring_date_' . $utilisateur['user_id'] . '").val("' . $user_meta_hiring_date . '");
			</script>
		</td>
		<td style="border-bottom:1px solid #CCC; width: 165px;" >
			<input type="text" style="text-align: center;" class="user_date_input" name="digi_user_single_meta[' . $utilisateur['user_id'] . '][digi_unhiring_date]" value="' . $user_meta_unhiring_date . '" />
		</td>
	</tr>';
		}
		$mass_user_form_content .= '
</table>';
	}

	echo '
<div id="digi_user_mass_updater_message" class="updated digirisk_hide" style="width: 30%; margin: 0 auto; position: fixed; text-align:center;"></div>
<form action="' . admin_url('admin-ajax.php') . '" id="digi-mass-user-updater-form" method="POST" style="margin-top: 30px;" >
	<input type="hidden" name="action" value="digi-mass-user-update" />
	' . $mass_user_form_content . '
</form>
<script type="text/javascript" >
	jQuery(document).ready(function(){
		jQuery("#digi-mass-user-updater-form").ajaxForm({
			dataType:  "json",
			success: function( response){
				jQuery("#digi_user_mass_updater_message").html( response["message"] );
				jQuery("#digi_user_mass_updater_message").show();
				setTimeout(function(){
					jQuery("#digi_user_mass_updater_message").html( "" );
					jQuery("#digi_user_mass_updater_message").hide();
					jQuery("#TB_closeWindowButton").click();
				}, "1500");
			},
		});
		jQuery(".user_date_input").datepicker({

			changeMonth: true,
			changeYear: true,
			navigationAsDateFormat: true,
		});
	});
</script>';
	die();
}
add_action( 'wp_ajax_digi-mass-change-user-informations', 'digi_mass_change_user_informations' );

function digi_mass_user_update() {
	$response = array(
		'status' => true,
		'message' => __('Les utilisateurs ont bien été mis à jour', 'evarisk'),
	);
	if ( !empty( $_POST['digi_user_single_meta'] ) ) {
		foreach ( $_POST['digi_user_single_meta'] as $user_id => $user_informations_in_single_meta ) {
			foreach ( $user_informations_in_single_meta as $meta_key => $meta_value ) {
				if ( !empty($meta_value) || (!empty($_POST['digi_user_force_update']) && in_array( $user_id, $_POST['digi_user_force_update'] )) ) {
					if ( ( ( 'digi_hiring_date' == $meta_key ) || ( 'digi_unhiring_date' == $meta_key ) ) && ( $meta_value != date( 'Y-m-d', strtotime( $meta_value ) ) ) ) {
						$date_components = explode( '/', $meta_value );
						$meta_value = date( 'Y-m-d', strtotime( $date_components[ 2 ] . '-' . $date_components[ 1 ] . '-' . $date_components[ 0 ] ) );
					}
					$meta_update_result = update_user_meta($user_id, $meta_key, $meta_value);
// 					if ( false === $meta_update_result ) {
// 						$response = array(
// 							'status' => false,
// 							'message' => __('Au moins une erreur a eu lieu lors de l\'enregistrement des utilisateurs, merci de vérifier vos modifications', 'evarisk'),
// 						);
// 					}
				}
			}
		}
	}

	echo json_encode( $response );
	die();
}
add_action( 'wp_ajax_digi-mass-user-update', 'digi_mass_user_update' );


/***
 *
 *
 * SUrevy management
 *
 *
 */
/**
 * Create new evaluation form a survey
 */
add_action( 'wp_ajax_digi-ajax-final-survey-evaluation-result-view', 'digi_ajax_evaluation_answers_view' );
function digi_ajax_evaluation_answers_view() {
	check_ajax_referer( 'wpes-ajax-view-survey-results', 'wpes-ajax-survey-final-result-view-nonce' );
	global $wpdb,
				$wpes_survey;
	$output = '';

	$query = $wpdb->prepare( "SELECT * FROM " . TABLE_FORMULAIRE_LIAISON . " WHERE tableElement = %s AND idElement = %d AND survey_id = %d AND state = %s ORDER BY date_started DESC LIMIT 1" , array( $_REQUEST[ 'tableElement' ], $_REQUEST[ 'idElement' ], $_REQUEST['final_survey_id'], 'closed') );
	$element_evaluation[ 'closed' ] = $wpdb->get_row( $query, ARRAY_A );
	if ( !empty($element_evaluation) && !empty($element_evaluation[ 'closed' ]) ) {
		$preview = false;
		$survey_id = $element_evaluation[ 'closed' ]['survey_id'];

		$associated_item = $wpes_survey->issues->get_issues( $survey_id, '', $preview );
		if ( $associated_item->have_posts() ) {
			/**	Add each issue to display	*/
			$sub_output = '';
			foreach ( $associated_item->posts as $item ) {
				$sub_output .= $wpes_survey->display_final_issue( $item, $_REQUEST[ 'post_id' ], $survey_id );
			}
		}

		if ( !empty($sub_output) ) {
			$output = $wpes_survey->display_final_survey_evaluation_result( $element_evaluation[ 'closed' ], $sub_output );
		}
		else {
			$has_content = false;
			$output = sprintf( __('There are no issues in this survey for the moment. %s', 'wp_easy_survey'), '<a href="' . admin_url('post.php') . '?post=' . $survey_id . '&amp;action=edit" >' . __('Edit survey', 'wp_easy_survey') . '</a>' );
		}
	}

	echo $output;
	die();
}

add_action( 'wp_ajax_digi-close-evaluation', 'digi_close_evaluation' );
function digi_close_evaluation() {
	check_ajax_referer( 'wpes-ajax-close-evaluation', 'wpes-ajax-close-evaluation' );
	global $wpdb,
				$wpes_survey;
	$ancestors = get_post_ancestors( $_POST['survey_id'] );
	$response = array(
		'status'    => false,
		'survey_id' => $ancestors[0],
		'post_ID'   => $_POST['post_ID'],
		'output'    => '',
	);
	$query = $wpdb->prepare( "SELECT * FROM " . TABLE_FORMULAIRE_LIAISON . " WHERE idFormulaire = %d AND state = %s LIMIT 1", $ancestors[0], "started" );
	$current_evaluation = $wpdb->get_row( $query );
	$wpdb->update( TABLE_FORMULAIRE_LIAISON, array( 'date_closed' => current_time( 'mysql', 0 ), 'user_closed' => get_current_user_id(), 'state' => 'closed'), array( 'id' => $current_evaluation->id, ) );

	$response['status'] = true;
	$query = $wpdb->prepare( "SELECT * FROM " . TABLE_FORMULAIRE_LIAISON . " WHERE tableElement = %s AND idElement = %d AND idFormulaire = %d AND state = %s ORDER BY date_started LIMIT 1" , array( $current_evaluation->tableElement, $current_evaluation->idElement, $current_evaluation->idFormulaire, 'started') );
	$current_element_evaluation[ 'in_progress' ] = $wpdb->get_row( $query, ARRAY_A );
	$query = $wpdb->prepare( "SELECT * FROM " . TABLE_FORMULAIRE_LIAISON . " WHERE tableElement = %s AND idElement = %d AND idFormulaire = %d AND state = %s ORDER BY date_started DESC" , array( $current_evaluation->tableElement, $current_evaluation->idElement, $current_evaluation->idFormulaire, 'closed') );

	$current_element_evaluation[ 'closed' ] = $wpdb->get_results( $query, ARRAY_A );
	$current_element_evaluation[ 'ajax_action' ] = "digi-ajax-final-survey-evaluation-result-view&amp;tableElement=" . $current_evaluation->tableElement . "&amp;idElement=" . $current_evaluation->idElement;
	$final_survey = $wpes_survey->final_survey_display( $current_evaluation->idElement, $current_evaluation->idFormulaire, $current_element_evaluation );
	$response['output'] = $final_survey[ 'content' ];

	echo json_encode( $response );
	die();
}

add_action( 'wp_ajax_digi-start-new-evaluation-for-wpes', 'digi_start_new_evaluation' );
function digi_start_new_evaluation() {
	check_ajax_referer( 'wpes-new-evaluation-start', 'wpes-ajax-new-evaluation-start' );
	global $wpdb,
				$wpes_survey;
	$response = array(
		'status'    => false,
		'survey_id' => $_POST['survey_id'],
		'post_ID'   => $_POST['post_ID'],
		'output'    => '',
	);

	/**	Save the survey content for the current evaluation */
	$new_survey_id = $wpes_survey->create_post_revision( $_POST['survey_id'] );

	if ( is_int($new_survey_id) ) {
		$wpes_survey->create_final_survey( $_POST['survey_id'], $new_survey_id );

		$evaluation_id = $wpdb->insert( TABLE_FORMULAIRE_LIAISON, array(
			'id' 			=> NULL,
			'status' 		=> 'valid',
			'date_started' 	=> current_time('mysql', 0),
			'date_closed' 	=> '',
			'user' 			=> get_current_user_id(),
			'user_closed' 	=> '',
			'idFormulaire' 	=> $_POST['survey_id'],
			'survey_id' 	=> $new_survey_id,
			'idELement' 	=> $_POST[ 'post_ID' ],
			'tableElement' 	=> $_POST[ 'post_type' ],
			'state' 		=> 'started',
		) ) ;

		if ( is_int( $evaluation_id ) ) {
			$response['status'] = true;

			$sub_output = '';
			$has_content = true;

			$preview = true;
			$current_evaluation = null;
			$parent_survey_id = $_POST['survey_id'];

			$query = $wpdb->prepare( "SELECT * FROM " . TABLE_FORMULAIRE_LIAISON . " WHERE tableElement = %s AND idElement = %d AND idFormulaire = %d AND state = %s ORDER BY date_started LIMIT 1" , array( $_POST[ 'post_type' ], $_POST[ 'post_ID' ], $_POST['survey_id'], 'started') );
			$current_element_evaluation[ 'in_progress' ] = $wpdb->get_row( $query, ARRAY_A );
			$query = $wpdb->prepare( "SELECT * FROM " . TABLE_FORMULAIRE_LIAISON . " WHERE tableElement = %s AND idElement = %d AND idFormulaire = %d AND state = %s ORDER BY date_started DESC" , array( $_POST[ 'post_type' ], $_POST[ 'post_ID' ], $_POST['survey_id'], 'closed') );
			$current_element_evaluation[ 'closed' ] = $wpdb->get_results( $query, ARRAY_A );
			$current_element_evaluation[ 'ajax_action' ] = "digi-ajax-final-survey-evaluation-result-view&amp;tableElement=" . $_POST[ 'post_type' ] . "&amp;idElement=" . $_POST[ 'post_ID' ];
			$final_survey = $wpes_survey->final_survey_display( $_POST[ 'post_ID' ], $_POST['survey_id'], $current_element_evaluation );
			$response['output'] = $final_survey[ 'content' ];
		}
	}

	echo json_encode( $response );
	die();
}

?>