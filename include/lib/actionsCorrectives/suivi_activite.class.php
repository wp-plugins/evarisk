<?php

class suivi_activite {

	function formulaireAjoutSuivi($tableElement, $idElement, $complete_interface = true, $specific_follow_up = "") {
		$saveButtonOuput = 'no';
		global $wpdb;

		switch ($tableElement) {
			case TABLE_TACHE:
				$currentTask = new EvaTask($idElement);
				$currentTask->load();
				$ProgressionStatus = $currentTask->getProgressionStatus();

				if( ($ProgressionStatus == 'inProgress') || ($ProgressionStatus == 'notStarted') || (digirisk_options::getOptionValue('possibilite_Modifier_Tache_Soldee')== 'oui') ){
					$saveButtonOuput = 'yes';
				}
				$document_type_to_print = __('Plan d\'action', 'evarisk');
			break;
			case TABLE_ACTIVITE:
				$current_action = new EvaActivity($idElement);
				$current_action->load();
				$ProgressionStatus = $current_action->getProgressionStatus();

				if( ($ProgressionStatus == 'inProgress') || ($ProgressionStatus == 'notStarted') || (digirisk_options::getOptionValue('possibilite_Modifier_Action_Soldee')== 'oui') ){
					$saveButtonOuput = 'yes';
				}
				$document_type_to_print = __('Plan d\'action', 'evarisk');
			break;

			case TABLE_UNITE_TRAVAIL:
			case TABLE_UNITE_TRAVAIL:
			case TABLE_AVOIR_VALEUR:
				$saveButtonOuput = 'yes';
				$document_type_to_print = __('DUER', 'evarisk');
				break;
		}

		$output = '';
		if($saveButtonOuput == 'yes'){
			$idBouttonEnregistrer = 'saveActionFollow';
			$scriptEnregistrement =
				'<script type="text/javascript">
					digirisk(document).ready(function() {
						digirisk("#' . $idBouttonEnregistrer . '").click(function() {
							if (digirisk("#commentaire' . $tableElement . $idElement . '").val() != "") {
								digirisk("#load' . $idBouttonEnregistrer . '").html(\'<img src="' . PICTO_LOADING_ROUND . '" />\');
								digirisk("#bttn' . $idBouttonEnregistrer . '").hide();
								digirisk("#load' . $idBouttonEnregistrer . '").show();

								var print_in_do = "no";
								if ( digirisk("#digi_print_comment_in_doc' . $tableElement . $idElement . '_").is(":checked") ) {
									print_in_do = "yes";
								}
								digirisk("#load' . $tableElement . $idElement . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
									"post": "true",
									"table": "' . TABLE_ACTIVITE_SUIVI . '",
									"act": "save",
									"idElement": "' . $idElement . '",
									"tableElement": "' . $tableElement . '",
									"date_ajout": digirisk("#date_ajout' . $tableElement . $idElement . '_").val(),
									"export": print_in_do,
									"commentaire": digirisk("#commentaire' . $tableElement . $idElement . '_").val(),
								});
							}
							else{
								alert(digi_html_accent_for_js("' . __('Vous ne pouvez pas ajouter de commentaire vide', 'evarisk') . '"));
							}
						});
					});
				</script>';

			$selected_date = current_time('mysql', 0);
			$export_state = '';
			$comment = '';
			if ( !empty($specific_follow_up) ) {
				$query = $wpdb->prepare("SELECT * FROM " . TABLE_ACTIVITE_SUIVI . " WHERE id = %d", $specific_follow_up);
				$follow_up_infos = $wpdb->get_row($query);
				$selected_date = $follow_up_infos->date_ajout;
				$export_state = (!empty($follow_up_infos->export) && ($follow_up_infos->export == 'yes')) ? ' checked="checked"' : '';
				$comment =  $follow_up_infos->commentaire;
			}

			$output .= '
<table summary="" cellpadding="0" cellspacing="0" style="width:100%;" >
	<tr>
		<td style="width:10%;" >&nbsp;</td>
		<td style="width:80%;" >' . __('Commentaire', 'evarisk') . '</td>
		<td style="width:10%;" ></td>
	</tr>
	<tr>
		<td style="vertical-align:top; "  >
			<input id="date_ajout' . $tableElement . $idElement . '_' . $specific_follow_up . '" type="text" value="' . $selected_date . '" name="date_ajout"><br/><br/>
			<input' . $export_state . ' type="checkbox" name="export" value="yes" id="digi_print_comment_in_doc' . $tableElement . $idElement .'_' . $specific_follow_up .  '" /> <label for="digi_print_comment_in_doc' . $tableElement . $idElement .'_' . $specific_follow_up .  '" >' . sprintf( __('Imprimer dans %s', 'evarisk'), $document_type_to_print ) . '</label>
		</td>
		<td >' . EvaDisplayInput::afficherInput('textarea', 'commentaire' . $tableElement . $idElement . '_' . $specific_follow_up, $comment, '', '', 'commentaire', false, true, 3) . '</td>
		<td style="vertical-align:top; " >';

			if ( $complete_interface ) {
				$output .=
			'<div id="bttn' . $idBouttonEnregistrer . '" >' . EvaDisplayInput::afficherInput('button', $idBouttonEnregistrer, __('Ajouter', 'evarisk'), null, '', $idBouttonEnregistrer, false, true, '', 'button-primary alignright', '', '', $scriptEnregistrement) . '</div><div style="float:right;display:none;" id="load' . $idBouttonEnregistrer . '" ></div>';
			}
			else {
				$output .= '';
			}

			$output .=
		'</td>
	</tr>
</table>
<div title="' . __('Modification d\'un commentaire', 'evarisk') . '" id="digi_follow_up_update_box" ></div>
<input type="hidden" name="" value="' . $tableElement . '" id="" />
<script type="text/javascript" >
	digirisk(document).ready(function(){
		jQuery("#date_ajout' . $tableElement . $idElement .'_' . $specific_follow_up .  '").datetimepicker({
			dateFormat: "yy-mm-dd",
			timeFormat: "hh:mm",
			regional: "fr_FR",
		});

		jQuery("#digi_follow_up_update_box").dialog({
			autoOpen: false,
			modal: true,
			width: 850,
			buttons: {
				"' . __('Enregistrer', 'evarisk') . '": function(){
					var print_in_do = "no";
					if ( digirisk("#digi_follow_up_update_box #digi_print_comment_in_doc' . $tableElement . $idElement .'_' . $specific_follow_up .  '").is(":checked") ) {
						print_in_do = "yes";
					}
					digirisk("#load' . $tableElement . $idElement . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
						"post": "true",
						"table": "' . TABLE_ACTIVITE_SUIVI . '",
						"act": "update",
						"id_follow_up": "' . $specific_follow_up . '",
						"idElement": "' . $idElement . '",
						"tableElement": "' . $tableElement . '",
						"date_ajout": digirisk("#digi_follow_up_update_box #date_ajout' . $tableElement . $idElement .'_' . $specific_follow_up .  '").val(),
						"export": print_in_do,
						"commentaire": digirisk("#digi_follow_up_update_box #commentaire' . $tableElement . $idElement .'_' . $specific_follow_up .  '").val(),
					});
					jQuery(this).dialog("close");
				},
				"' . __('Annuler', 'evarisk') . '": function(){
					jQuery(this).dialog("close");
				}
			},
			close: function(){
				jQuery("#digi_follow_up_update_box").html("");
			},
		});
	});
</script>';
		}
		else {
			$output .= '<div class="alignright button-primary" id="TaskSaveButton" >' .
					__('Vous ne pouvez pas ajouter de commentaire', 'evarisk') .
				'</div>';
		}

		if ( $complete_interface ) {
			$output .= suivi_activite::tableauSuiviActivite($tableElement, $idElement);
		}

		return $output;
	}

	function saveSuiviActivite($tableElement, $idElement, $commentaire, $date_ajout, $export) {
		global $wpdb;
		global $current_user;
		$result = array();

		$request = $wpdb->insert(TABLE_ACTIVITE_SUIVI, array('id' => null, 'status' => 'valid', 'date' => current_time('mysql', 0), 'id_user' => $current_user->ID, 'id_element' => $idElement, 'table_element' => $tableElement, 'commentaire' => str_replace("�","'", $commentaire), 'date_ajout' => $date_ajout, 'export' => $export));
		$last_comment = $wpdb->insert_id;
		if ($request && !empty($last_comment) && is_int($last_comment)) {
			$result = 'ok';
		}
		else {
			$result = 'error';
		}

		return $result;
	}

	function getSuiviActivite($tableElement, $idElement) {
		global $wpdb;

		$more_query = '';
		switch ($tableElement) {
			case TABLE_AVOIR_VALEUR:
				$query = $wpdb->prepare("SELECT GROUP_CONCAT(id_evaluation) as risk_eval_list FROM " . TABLE_AVOIR_VALEUR . " WHERE id_risque = (SELECT DISTINCT(id_risque) FROM " . TABLE_AVOIR_VALEUR . " WHERE id_evaluation = %d) GROUP BY id_risque", $idElement);
				$risk_eval_list = $wpdb->get_var($query);
				$query = $wpdb->prepare(
					"SELECT *
			FROM " . TABLE_ACTIVITE_SUIVI . "
			WHERE id_element IN (" . $risk_eval_list . ")
				AND table_element = '%s'
				AND status = 'valid'
			ORDER BY date_ajout DESC",
					$tableElement
				);
				break;
			default:
				$query = $wpdb->prepare(
					"SELECT *
					FROM " . TABLE_ACTIVITE_SUIVI . "
					WHERE id_element = '%s'
						AND table_element = '%s'
						AND status = 'valid'
					ORDER BY date DESC",
					$idElement, $tableElement
				);
				break;
		}

		return $wpdb->get_results($query);
	}

	function tableauSuiviActivite($tableElement, $idElement) {
		$listSuivi = suivi_activite::getSuiviActivite($tableElement, $idElement);
		$outputSuivi = '';

		if( !empty($listSuivi) ) {
			switch ($tableElement) {
				case TABLE_TACHE:
				case TABLE_ACTIVITE:
					$document_type_to_print = __('le plan d\'action', 'evarisk');
					break;
				case TABLE_UNITE_TRAVAIL:
				case TABLE_UNITE_TRAVAIL:
				case TABLE_AVOIR_VALEUR:
					$saveButtonOuput = 'yes';
					$document_type_to_print = __('Le DUER', 'evarisk');
					break;
			}

			$idTable = 'tableauSuiviModification' . $tableElement . $idElement;
			$titres = array( '', __('Suivi modifications', 'evarisk'), sprintf( __('Publier dans %s', 'evarisk'), $document_type_to_print ), __('Actions', 'evarisk') );
			$classes = array( 'digi_suivi_modif_date_ajout_unformated', '', 'digi_suivi_modif_export_col', 'digi_suivi_modif_action_col');

			unset($lignesDeValeurs);
			foreach ($listSuivi as $suivi) {
				unset($valeurs);
				$user_info = get_userdata($suivi->id_user);
				$user_lastname = '';
				if ( (isset($user_info->user_lastname) && ($user_info->user_lastname != '')) ) {
					$user_lastname = $user_info->user_lastname;
				}
				$user_firstname = $user_info->user_nicename;
				if ( (isset($user_info->user_firstname) && ($user_info->user_firstname != '')) ) {
					$user_firstname = $user_info->user_firstname;
				}

				$valeurs[] = array('value' => $suivi->date_ajout);
				$valeurs[] = array('value' => sprintf(__('Le <b>%s</b>, <b>%s</b> dit <i>%s</i>', 'evarisk'), mysql2date('d M Y (H:i:s)', $suivi->date_ajout, true), $user_lastname . ' ' . $user_firstname, $suivi->commentaire));
				$valeurs[] = array('value' => (!empty($suivi->export) && ($suivi->export == 'yes')) ? __('Oui', 'evarisk')/* '<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'veille-reponse.gif" alt="publish_in_doc" title="' . sprintf( __('Ce commentaire sera publi&eacute; dans %s', 'evarisk'), $document_type_to_print ) . '" />' */ : __('Non', 'evarisk'));
				$valeurs[] = array('value' => '<img src="' . PICTO_EDIT . '" alt="' . __('Edit this comment', 'evarisk') . '" class="digi_edit_follow_up_line" id="digi_edit_follow_up_line_' . $suivi->id . '" /><img src="' . PICTO_DELETE . '" alt="' . __('Delete this comment', 'evarisk') . '" class="digi_delete_follow_up_line" id="digi_delete_follow_up_line_' . $suivi->id . '" />');
				$lignesDeValeurs[] = $valeurs;
				$idLignes[] = $tableElement . $idElement . 'suiviModification';
			}

			$scriptTableauSuiviModification =
			'<script type="text/javascript">
				digirisk(document).ready(function() {
					digirisk("#' . $idTable . '").dataTable({
						"bInfo": false,
						"aaSorting": [[ 0, "desc" ]],
						"oLanguage":{
							"sUrl": "' . EVA_INC_PLUGIN_URL . 'js/dataTable/jquery.dataTables.common_translation.txt"
						},
						"aoColumnDefs": [
	                        { "bSearchable": false, "bVisible": false, "aTargets": [ 0 ] },
	                    ],
					});
					digirisk("#' . $idTable . '").children("tfoot").remove();

					jQuery(".digi_delete_follow_up_line").click(function(){
						if ( confirm(digi_html_accent_for_js("' . __('Etes vous s&ucirc;r de vouloir supprimer ce commentaire?', 'evarisk') . '")) ) {
							jQuery("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
								"post":"true",
								"table":"' . TABLE_ACTIVITE_SUIVI . '",
								"act":"delete",
								"follow_up_2_del": jQuery(this).attr("id")
							});
						}
					});
					jQuery(".digi_edit_follow_up_line").click(function(){
						jQuery("#digi_follow_up_update_box").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
							"post":"true",
							"table":"' . TABLE_ACTIVITE_SUIVI . '",
							"act":"load_follow_up_edition_form",
							"table_element": "' . $tableElement . '",
							"id_element": "' . $idElement . '",
							"follow_up_2_edit": jQuery(this).attr("id").replace("digi_edit_follow_up_line_", ""),
						});
						jQuery("#digi_follow_up_update_box").dialog("open");
					});
				});
			</script>';

			$outputSuivi .= evaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $scriptTableauSuiviModification);
		}

		return $outputSuivi;
	}

}