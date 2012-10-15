<?php
/**
* Plugin installer
*
* Define the different methods to install digirisk plugin
* @author Evarisk <dev@evarisk.com>
* @version 5.1.3.5
* @package Digirisk
* @subpackage librairies
*/

/**
* Define the different methods to install digirisk plugin
* @package Digirisk
* @subpackage librairies
*/
class digirisk_install
{
	/**
	*	Define the main installation form
	*/
	function installation_form(){
		global $evaluation_method_operator, $evaluation_main_vars;
		$installation_form = '';

		/*	Create an ouput with the defined method operator	*/
		$basic_operator_list = '     ';
		foreach($evaluation_method_operator as $operator){
			$basic_operator_list .= $operator['symbole'] . '  ';
		}
		$basic_operator_list = (trim(substr($basic_operator_list, 2, -2)) !=  '')  ? '  (' . trim(substr($basic_operator_list, 2, -2)) . ')' : '';
		/*	Create an ouput with the defined method vars	*/
		$basic_vars_list = '      ';
		foreach($evaluation_main_vars as $operator){
			$basic_vars_list .= $operator['nom'] . ' / ';
		}
		$basic_vars_list = (trim(substr($basic_vars_list, 3, -3)) !=  '')  ? '  (' . trim(substr($basic_vars_list, 2, -2)) . ')' : '';
		/*	Create an output with the defined danger categories	*/
		$basic_danger_cat_list = '';
		$inrs_danger_categories=unserialize(DIGI_INRS_DANGER_LIST);
		foreach($inrs_danger_categories as $version_number => $category){
			if ( is_file(EVA_HOME_DIR . $category['picture']) ) {
				$picture = EVA_HOME_URL . $category['picture'];
			}
			else {
				$picture = EVA_CATEGORIE_DANGER_ICON;
			}
			$basic_danger_cat_list .= '<div class="alignleft inrs_picto_container_install" ><img src="' . $picture . '" alt="' . $category['nom'] . '" /><br/>' . $category['nom'] . '</div>';
		}

		$installation_form .= digirisk_display::start_page(__('Installation du logiciel d\'aide &agrave; l\'&eacute;valuation des risques', 'evarisk'), EVA_OPTIONS_ICON, __('Installation du logiciel d\'aide &agrave; l\'&eacute;valuation des risques', 'evarisk'), __('Installation du logiciel D\'aide &agrave; l\'&eacute;valuation des risques', 'evarisk'), TABLE_OPTION, false, '', false, false) . '
<form method="post" id="digi_install_form" action="' . EVA_INC_PLUGIN_URL . 'ajax.php" >
	<input type="hidden" name="post" value="true" />
	<input type="hidden" name="nom" value="digirisk_install" />

	<fieldset>
		<legend>' . __('M&eacute;thode d\'&eacute;valuation', 'evarisk')  . '</legend>
		<input type="checkbox" name="insert_basic_operator" id="insert_basic_operator" value="yes" checked="checked" disabled="disabled" />&nbsp;<label for="insert_basic_operator" >' . __('Ins&eacute;rer les op&eacute;rateurs de base', 'evarisk') . '</label>' . $basic_operator_list . '<br/>
		<input type="checkbox" name="insert_basic_vars" id="insert_basic_vars" value="yes" checked="checked" disabled="disabled" />&nbsp;<label for="insert_basic_vars" >' . __('Ins&eacute;rer les variables de base', 'evarisk') . '</label>' . $basic_vars_list . '<br/><br/><br/>
		<input type="checkbox" name="insert_evarisk_main_method" id="insert_evarisk_main_method" value="yes" checked="checked" />&nbsp;<label for="insert_evarisk_main_method" >' . __('Ins&eacute;rer la m&eacute;thode d\'&eacute;valuation d\'Evarisk', 'evarisk') . '</label><br/>
		<img src="' . EVA_HOME_URL . 'medias/uploads/wp_eva__methode/1/tabcoeff.gif" alt="' . __('Explication de la m&eacute;thode Evarisk', 'evarisk') . '" title="' . __('Explication de la m&eacute;thode Evarisk', 'evarisk') . '" />
	</fieldset>

	<fieldset>
		<legend>' . __('Cat&eacute;gories de danger', 'evarisk')  . '</legend>
		<input type="checkbox" name="insert_inrs_danger_cat" id="insert_inrs_danger_cat" value="yes" checked="checked" />&nbsp;<label for="insert_inrs_danger_cat" >' . __('Ins&eacute;rer les cat&eacute;gories de danger d&eacute;finies par l\'INRS', 'evarisk') . '</label> (' . __('Un danger sera ins&eacute;r&eacute; dans chaque cat&eacute;gorie. Sauf pour la cat&eacute;gorie "Autres" o&ugrave; plusieurs dangers seront ins&eacute;r&eacute;s', 'evarisk') . ')<br/>' . $basic_danger_cat_list . '
	</fieldset>

	<fieldset>
		<legend>' . __('Th&egrave;me pour le portail', 'evarisk')  . '</legend>
		<input type="checkbox" name="activate_evarisk_theme" id="activate_evarisk_theme" value="yes" checked="checked" />&nbsp;<label for="activate_evarisk_theme" >' . __('Activer le th&egrave;me Evarisk. (NB: vous pourrez activer le th&egrave;me ult&eacute;rieurement)', 'evarisk') . '</label>
	</fieldset>

	<div class="digirisk_install_button_container" >
		<div id="load_picture_container" >&nbsp;</div>
		<div id="button_container" ><input type="submit" class="button-primary" name="digirisk_install_button" value="' . __('Installer le logiciel', 'evarisk') . '" /></div>
	</div>
</form>
<script type="text/javascript" >
	digirisk(document).ready(function(){
		var autoInstall = false;';
		if(STANDALONEVERSION){
			$installation_form .=
		'var autoInstall = true;';
		}

		$installation_form .= '
		jQuery("#insert_evarisk_main_method").click(function(){
			if(jQuery(this).is(":checked")){
				jQuery("#insert_basic_operator").prop("checked", true);
				jQuery("#insert_basic_operator").prop("disabled", true);
				jQuery("#insert_basic_vars").prop("checked", true);
				jQuery("#insert_basic_vars").prop("disabled", true);
			}
			else{
				jQuery("#insert_basic_operator").prop("disabled", false);
				jQuery("#insert_basic_vars").prop("disabled", false);
			}
		});

		jQuery("#digi_install_form").ajaxForm({
			target: "ajax-response",
			dataType:  "json",
			success: function(responseText, statusText, xhr, $form){
				if(responseText["status"]){
					jQuery("#load_picture_container").html(digi_html_accent_for_js("' . sprintf(__('Installation termin&eacute;e. Vous allez &ecirc;tre redirig&eacute; dans quelque secondes. Si ce n\'est pas le cas %s', 'evarisk'), '<a href=\'' . admin_url("options-general.php?page=digirisk_options") . '\'>' . __('Cliquez ici', 'evarisk') . '</a>') . '"));
					setTimeout(function(){window.top.location.href = "' . admin_url("options-general.php?page=digirisk_options") . '";}, 5000);
				}
			},
			beforeSubmit: function(formData, jqForm, options){
				var check_if_install_could_be_launch = false;
				if(!jQuery("#activate_evarisk_theme").is(":checked") || autoInstall){
					check_if_install_could_be_launch = true;
				}
				else if(jQuery("#activate_evarisk_theme").is(":checked") && confirm(digi_html_accent_for_js("' . __('&Ecirc;tes vous s&ucirc;r de vouloir activer le th&egrave;me Evarisk pour votre Blog?\nNB: Si vous avez un th&egrave;me personnalis&eacute; celui sera remplac&eacute; par le th&egrave;me Evarisk. Il restera disponible dans la liste des th&egrave;mes.', 'evarisk') . '"))){
					check_if_install_could_be_launch = true;
				}

				if(check_if_install_could_be_launch){
					jQuery("#load_picture_container").html(jQuery("#round_loading_img div.round_loading_img").html() + "   " + digi_html_accent_for_js("' . __('Installation en cours. Merci de patienter.', 'evarisk') . '"));
					jQuery("#button_container").hide();
				}
				else{
					return false;
				}
			}
		});';

		if(STANDALONEVERSION){
			$installation_form .=
		'jQuery("#digi_install_form").submit();';
		}

		$installation_form .= '
	});
</script>
		' . digirisk_display::end_page();

		echo $installation_form;
	}

	/**
	*	Method called when plugin is loaded for database update. This method allows to update the database structure, insert default content.
	*/
	function update_digirisk($version_to_launch = -1){
		global $wpdb, $digirisk_db_table, $digirisk_db_table_list, $digirisk_update_way, $digirisk_db_content_add, $digirisk_db_content_update, $digirisk_db_options_add, $digirisk_table_structure_change, $digirisk_db_update, $standard_message_subject_to_send, $standard_message_to_send, $digirisk_db_table_operation_list;

		/*
		 * 	Initialisation des permissions (Lancement a chaque chargement du plugin)
		 */
		digirisk_permission::digirisk_init_permission();

		/*
		 * Copie du dossier contenant les fichiers de modeles/images (Lancement a chaque chargement du plugin)
		 */
		digirisk_tools::copyEntireDirectory(EVA_UPLOADS_PLUGIN_OLD_DIR, EVA_UPLOADS_PLUGIN_DIR);

		/*
		 * Copie/creation du dossier devant contenir les fichiers crees par le biais du logiciel DUER/Fiches de poste/... (Lancement a chaque chargement du plugin)
		 */
		digirisk_tools::copyEntireDirectory(EVA_RESULTATS_PLUGIN_OLD_DIR, EVA_RESULTATS_PLUGIN_DIR);

		/*
		 *	Creation du dossier temporatire permettant de creer les documents au format odt (Lancement a chaque chargement du plugin)
		 */
		if(!is_dir(EVA_RESULTATS_PLUGIN_DIR . 'tmp')){
			digirisk_tools::make_recursiv_dir(EVA_RESULTATS_PLUGIN_DIR . 'tmp');
		}

		$current_db_version = digirisk_options::getDbOption('base_evarisk');

		$current_def_max_version = (string)max(array_keys($digirisk_update_way));
		if($version_to_launch >= 0){
			$current_def_max_version = $version_to_launch;
		}
		$new_version = $current_def_max_version + 1;
		$version_nb_delta = $current_def_max_version - $current_db_version;

		$do_changes = false;

		/*	Check if there are modification to do	*/
		if($current_def_max_version >= $current_db_version){
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			/*	Check the lowest version of db to execute	*/
			$lowest_version_to_execute = (($current_def_max_version - $version_nb_delta) < 0) ? 0 : ($current_def_max_version - $version_nb_delta);

			for($i = $lowest_version_to_execute; $i <= $current_def_max_version; $i++){
				/*	Check if there are modification to do	*/
				if(isset($digirisk_update_way[$i])){
					$dependance_list = '';
					$dependance_to_make = false;

					/*	Check if there are modification to make on table	*/
					if(isset($digirisk_db_table_list[$i])){
						foreach($digirisk_db_table_list[$i] as $table_name){
							if(!empty($digirisk_db_table[$table_name]))
								$table_update_result = dbDelta($digirisk_db_table[$table_name]);
						}
						$do_changes = true;
					}

			/********************/
			/*		Insert data	*/
			/********************/
					$do_changes = self::insert_data_for_version($i, $do_changes);

			/********************/
			/*		Update		*/
			/********************/
					if(is_array($digirisk_db_update) && !empty($digirisk_db_update[$i]) && (count($digirisk_db_update[$i]) > 0)){
						foreach($digirisk_db_update[$i] as $table_name => $def){
							foreach($def as $information_index => $table_information){
								$query = $wpdb->prepare($table_information);
								$wpdb->query($table_information);
								$do_changes = true;
							}
						}
					}

			/*****************************************************/
			/*	Call specific data insertion for current version */
			/*****************************************************/
					self::make_specific_operation_on_update($i);

			/******************************************************/
			/*		Make special operation on database structure		*/
			/******************************************************/
					if(is_array($digirisk_table_structure_change) && !empty($digirisk_table_structure_change[$i]) && (count($digirisk_table_structure_change[$i]) > 0)){
						foreach($digirisk_table_structure_change[$i] as $table_name => $operations_to_make){
							foreach($operations_to_make as $operation_index => $operation){
								$query = $wpdb->prepare($operation['MAIN_ACTION'] . " TABLE " . $table_name . " " . $operation['ACTION'] . " " . $operation['ACTION_CONTENT']);
								$wpdb->query($query);
							}
						}
						$do_changes = true;
					}
				}
			}
		}

// 			self::make_specific_operation_on_update('dev');

		/*	Update the db version option value	*/
		// $do_changes = false;
		if($do_changes){
			$digirisk_db_options = array();
			$digirisk_db_options['base_evarisk'] = $new_version;
			update_option('digirisk_db_option', $digirisk_db_options);
		}
	}

	/**
	*
	*/
	function insert_data_for_version($i, $do_changes = ''){
		global $wpdb, $digirisk_db_table, $digirisk_db_table_list, $digirisk_update_way, $digirisk_db_content_add, $digirisk_db_content_update, $digirisk_db_options_add, $digirisk_db_options_update, $standard_message_subject_to_send, $standard_message_to_send;
		$dependance_to_make = false;
		$dependance_list=array();

		/*	Options content	*/
		if(is_array($digirisk_db_options_add) && !empty($digirisk_db_options_add[$i]) && (count($digirisk_db_options_add[$i]) > 0)){
			foreach($digirisk_db_options_add[$i] as $option_name => $option_content){
				add_option($option_name, $option_content, '', 'yes');
			}
		}
		if(is_array($digirisk_db_options_update) && !empty($digirisk_db_options_update[$i]) && (count($digirisk_db_options_update[$i]) > 0)){
			foreach($digirisk_db_options_update[$i] as $option_name => $option_content){
				$option_current_content = get_option($option_name);
				foreach($option_content as $option_key => $option_value){
					$option_current_content[$option_key] = $option_value;
				}
				update_option($option_name, $option_current_content);
			}
		}

		/*	Add datas	*/
		if(is_array($digirisk_db_content_add) && !empty($digirisk_db_content_add[$i]) && (count($digirisk_db_content_add[$i]) > 0)){
			foreach($digirisk_db_content_add[$i] as $table_name => $def){
				foreach($def as $information_index => $table_information){
					if(isset($table_information['dependance'])){
						$dependance_list[] = $table_information['dependance'];
						$dependance_to_make = true;
						unset($table_information['dependance']);
					}
					if(isset($table_information['parent_element'])){
						$parent = self::get_parent_element_identifier($table_name, $table_information['parent_element']);
						unset($table_information['parent_element']);
						$table_information[$parent['name']] = $parent['id'];
					}
					$wpdb->insert($table_name, $table_information, '%s');
					$do_changes = true;
				}
			}
		}

		/*	Update datas	*/
		if(is_array($digirisk_db_content_update) && !empty($digirisk_db_content_update[$i]) && (count($digirisk_db_content_update[$i]) > 0)){
			foreach($digirisk_db_content_update[$i] as $table_name => $def){
				foreach($def as $information_index => $table_information){
					if(isset($table_information['dependance'])){
						$dependance_list[] = $table_information['dependance'];
						$dependance_to_make = true;
						unset($table_information['dependance']);
					}
					if(isset($table_information['parent_element'])){
						$parent = self::get_parent_element_identifier($table_name, $table_information['parent_element']);
						unset($table_information['parent_element']);
						$table_information['datas'][$parent['name']] = $parent['id'];
					}
					$wpdb->update($table_name, $table_information['datas'], $table_information['where'], '%s', '%s');
					$do_changes = true;
				}
			}
		}

		/*	Make dependance actions	*/
		if($dependance_to_make && (count($dependance_list) > 0)){
			foreach($dependance_list as $dependances){
				foreach($dependances as $dependance_table => $dependance){
					switch($dependance_table){
						case TABLE_PHOTO:{
							switch($dependance[2]){
								case TABLE_CATEGORIE_PRECONISATION:
									$element_identifier = evaRecommandationCategory::get_recommandation_category_id(array('id'), ' AND nom = "' . $dependance[3] . '"');
								break;
								case TABLE_PRECONISATION:
									$element_identifier = evaRecommandation::get_recommandation_id(array('id'), ' AND nom = "' . $dependance[3] . '"');
								break;
							}
							$new_cat_pict_id = EvaPhoto::saveNewPicture($dependance[2], $element_identifier, $dependance[0]);
							if($dependance[1] && ($dependance[1] == 'yes')){
								EvaPhoto::setMainPhoto($dependance[2], $element_identifier, $new_cat_pict_id, 'yes');
							}
						}
						break;
					}
				}
			}
		}

		return $do_changes;
	}

	/**
	*
	*/
	function get_parent_element_identifier($element_type, $parent_data){
		$parent_infos = array();

		switch($element_type){
			case TABLE_PRECONISATION:{
				$parent_infos['id'] = evaRecommandationCategory::get_recommandation_category_id(array('id'), ' AND nom = "' . $parent_data . '"');
				$parent_infos['name'] = 'id_categorie_preconisation';
			}
			break;
		}

		return $parent_infos;
	}

	/**
	*
	*/
	function make_specific_operation_on_update($version){
		global $wpdb, $standard_message_subject_to_send, $standard_message_to_send;

		switch($version){/*	Check different version for specific action	*/
			case 17:{
				$sql = "UPDATE " . TABLE_DUER . " SET groupesUtilisateursAffectes = groupesUtilisateurs ";
				$wpdb->query($sql);
			}break;
			case 19:{
				$sql = "UPDATE " . TABLE_AVOIR_VALEUR . " SET idEvaluateur = '1' ";
				$wpdb->query($sql);
			}break;
			case 20:{
				$sql = "UPDATE " . TABLE_AVOIR_VALEUR . " SET Status = 'Moderated' WHERE Status != 'Valid' ";
				$wpdb->query($sql);
			}break;
			case 21:{
				$sql = "SELECT GROUP_CONCAT( id ) as LIST FROM " . TABLE_AVOIR_VALEUR . " GROUP BY id_risque, DATE";
				$listToUpdate = $wpdb->get_results($sql);
				foreach($listToUpdate as $listID){
					$sql = "SELECT MAX(id_evaluation) + 1 AS newId FROM " . TABLE_AVOIR_VALEUR;
					$newId = $wpdb->get_row($sql);

					$sql = $wpdb->prepare("UPDATE " . TABLE_AVOIR_VALEUR . " SET id_evaluation = '%d' WHERE id IN (" . $listID->LIST . ")", $newId->newId);
					$wpdb->query($sql);
				}
			}break;
			case 25:{
				$sql = $wpdb->prepare("INSERT INTO " . TABLE_LIAISON_USER_ELEMENT . " SELECT '', status, date, 1, '', 0, id_user, id_element, table_element FROM " . TABLE_LIAISON_USER_EVALUATION);
				$wpdb->query($sql);
			}break;
			case 26:{
				$sql = "UPDATE " . TABLE_DUER . " SET id_model = '1'";
				$wpdb->query($sql);
				/*	Move the directory containing the different models	*/
				if(is_dir(EVA_MODELES_PLUGIN_OLD_DIR) && !is_dir(EVA_MODELES_PLUGIN_DIR)){
					rename(EVA_MODELES_PLUGIN_OLD_DIR, EVA_MODELES_PLUGIN_DIR);
				}
			}break;
			case 29:{
				$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " SELECT '', status, isMainPicture, id, idDestination, tableDestination FROM " . TABLE_PHOTO . ";";
				$wpdb->query($sql);
			}break;
			case 35:{
				digirisk_tools::make_recursiv_dir(EVA_GENERATED_DOC_DIR);
				/*	Move the directory containing the different models	*/
				if(is_dir(EVA_UPLOADS_PLUGIN_OLD_DIR) && !is_dir(EVA_UPLOADS_PLUGIN_DIR)){
					digirisk_tools::copyEntireDirectory(EVA_UPLOADS_PLUGIN_OLD_DIR, EVA_UPLOADS_PLUGIN_DIR);
				}
				/*	Move the directory containing the different models	*/
				if(is_dir(EVA_RESULTATS_PLUGIN_OLD_DIR) && !is_dir(EVA_RESULTATS_PLUGIN_DIR)){
					digirisk_tools::copyEntireDirectory(EVA_RESULTATS_PLUGIN_OLD_DIR, EVA_RESULTATS_PLUGIN_DIR);
				}
			}break;
			case 41:{
				$sql = "SELECT * FROM " . TABLE_UTILISE_EPI;
				$epi_utilise_results = $wpdb->get_results($sql);

				$query = "  ";
				foreach($epi_utilise_results as $epi_utilise){
					switch($epi_utilise->ppeId){
						case 1:
							$newEpiId = 5;
						break;
						case 2:
							$newEpiId = 4;
						break;
						case 3:
							$newEpiId = 7;
						break;
						case 4:
							$newEpiId = 9;
						break;
						case 5:
							$newEpiId = 8;
						break;
						case 6:
							$newEpiId = 11;
						break;
						case 7:
							$newEpiId = 3;
						break;
						case 8:
							$newEpiId = 6;
						break;
					}
					$wpdb->insert(TABLE_LIAISON_PRECONISATION_ELEMENT, array('status' => 'valid', 'id_preconisation' => $newEpiId, 'id_element' => $epi_utilise->elementId, 'table_element' => $epi_utilise->elementTable));
				}
			}break;
			case 44:{
				if( $wpdb->get_var("show tables like '" . TABLE_VERSION . "'") == TABLE_VERSION ){/*	Deplacement de la gestion des version	*/
					add_option('digirisk_db_option', array('base_evarisk' => digirisk_options::getDbOption('base_evarisk')));
				}
				if($wpdb->get_var("show tables like '" . TABLE_OPTION . "'") == TABLE_OPTION ){/*	Deplacement de la gestion des options	*/
					$optionToStore = array();

					/*	R�cup�ration de la liste des options existantes pour le transfert	*/
					$query = $wpdb->prepare("
						SELECT *
						FROM " . TABLE_OPTION);
					$optionsList = $wpdb->get_results($query);
					foreach($optionsList as $option){
						$optionToStore[$option->nom] = $option->valeur;
					}
					/*	Ajout de l'entr�e dans la table option avec toutes les valeurs des options	*/
					add_option('digirisk_options', $optionToStore);
				}

				if(($wpdb->get_var("show tables like '" . TABLE_EVA_USER_GROUP . "'") == TABLE_EVA_USER_GROUP) && ($wpdb->get_var("show tables like '" . TABLE_EVA_EVALUATOR_GROUP . "'") == TABLE_EVA_EVALUATOR_GROUP))
				{/*	Transfert des anciens groupes dans la nouvelle table	*/
					/*	Groupes d'employ�	*/
					$query = $wpdb->prepare("SELECT * FROM " . TABLE_EVA_USER_GROUP);
					$employeeGroups = $wpdb->get_results($query);
					$subQuery = "  ";
					foreach($employeeGroups as $employeeGroup)
					{
						$subQuery .= "('', '" . $wpdb->escape($employeeGroup->user_group_id) . "', '" . $wpdb->escape($employeeGroup->user_group_status) . "', 'employee', '" . current_time('mysql', 0) . "', 1, '', '', '" . $wpdb->escape($employeeGroup->user_group_name) . "', '" . $wpdb->escape($employeeGroup->user_group_description) . "'), ";
					}
					/*	Groupes	d'evaluateur	*/
					$query = $wpdb->prepare("SELECT * FROM " . TABLE_EVA_EVALUATOR_GROUP);
					$employeeGroups = $wpdb->get_results($query);
					foreach($employeeGroups as $employeeGroup)
					{
						$subQuery .= "('', '" . $wpdb->escape($employeeGroup->evaluator_group_id) . "', '" . $wpdb->escape($employeeGroup->evaluator_group_status) . "', 'evaluator', '" . $wpdb->escape($employeeGroup->evaluator_group_creation_date) . "', '" . $wpdb->escape($employeeGroup->evaluator_group_creation_user_id) . "', '" . $wpdb->escape($employeeGroup->evaluator_group_deletion_date) . "', '" . $wpdb->escape($employeeGroup->evaluator_deletion_user_id) . "', '" . $wpdb->escape($employeeGroup->evaluator_group_name) . "', '" . $wpdb->escape($employeeGroup->evaluator_group_description) . "'), ";
					}
					/*	Transfert dans la nouvelle table	*/
					$subQuery = trim(substr($subQuery, 0, -2));
					if($subQuery != "")
					{
						$query =
							"INSERT INTO " . DIGI_DBT_USER_GROUP . "
								(id, old_id, status, group_type, creation_date, creation_user_id, deletion_date, deletion_user_id, name, description)
							VALUES
								" . $subQuery;
						$wpdb->query($query);
					}

					/*	Transfert les tables non utilis�es vers la "trash" section	*/
					$query = $wpdb->prepare("RENAME TABLE " . TABLE_EVA_USER_GROUP . " TO " . TRASH_DIGI_DBT_USER_GROUP);
					$wpdb->query($query);
					$query = $wpdb->prepare("RENAME TABLE " . TABLE_EVA_EVALUATOR_GROUP . " TO " . TRASH_DIGI_DBT_EVALUATOR_GROUP);
					$wpdb->query($query);
				}

				if(($wpdb->get_var("show tables like '" . TABLE_LIAISON_USER_GROUPS . "'") == TABLE_LIAISON_USER_GROUPS) && ($wpdb->get_var("show tables like '" . TABLE_EVA_EVALUATOR_GROUP_BIND . "'") == TABLE_EVA_EVALUATOR_GROUP_BIND))
				{/*	Transfert la liaison des anciens groupes vers les nouveaux	*/
					/*	Groupes d'employ�	*/
					$query = $wpdb->prepare("SELECT * FROM " . TABLE_LIAISON_USER_GROUPS);
					$employeeGroupsLink = $wpdb->get_results($query);
					$subQuery = "  ";
					foreach($employeeGroupsLink as $employeeGroupLink)
					{
						$query = $wpdb->prepare("SELECT id FROM " . DIGI_DBT_USER_GROUP . " WHERE old_id = %d AND group_type = %s", $employeeGroupLink->id_group, 'employee');
						$newGroupId = $wpdb->get_row($query);
						$subQuery .= "('', '" . $wpdb->escape($employeeGroupLink->Status) . "', '" . $wpdb->escape($employeeGroupLink->date) . "', 1, '', '', '" . $wpdb->escape($newGroupId->id) . "', '" . $wpdb->escape($employeeGroupLink->id_element) . "', '" . $wpdb->escape($employeeGroupLink->table_element) . "_employee'), ";
					}
					/*	Groupes d'evaluateurs	*/
					$query = $wpdb->prepare("SELECT * FROM " . TABLE_EVA_EVALUATOR_GROUP_BIND);
					$evaluatorsGroupsLink = $wpdb->get_results($query);
					foreach($evaluatorsGroupsLink as $evaluatorsGroupLink)
					{
						$query = $wpdb->prepare("SELECT id FROM " . DIGI_DBT_USER_GROUP . " WHERE old_id = %d AND group_type = %s", $evaluatorsGroupLink->id_group, 'evaluator');
						$newGroupId = $wpdb->get_row($query);
						$subQuery .= "('', '" . $wpdb->escape($evaluatorsGroupLink->Status) . "', '" . $wpdb->escape($evaluatorsGroupLink->dateAffectation) . "', '" . $wpdb->escape($evaluatorsGroupLink->affectationUserId) . "', '" . $wpdb->escape($evaluatorsGroupLink->dateDesaffectation) . "', '" . $wpdb->escape($evaluatorsGroupLink->desaffectationUserId) . "', '" . $wpdb->escape($newGroupId->id) . "', '" . $wpdb->escape($evaluatorsGroupLink->id_element) . "', '" . $wpdb->escape($evaluatorsGroupLink->table_element) . "_evaluator'), ";
					}
					/*	Transfert dans la nouvelle table	*/
					$subQuery = trim(substr($subQuery, 0, -2));
					if($subQuery != "")
					{
						$query =
							"INSERT INTO " . DIGI_DBT_LIAISON_USER_GROUP . "
								(id, status, date_affectation, id_attributeur, date_desAffectation, id_desAttributeur, id_group, id_element, table_element)
							VALUES
								" . $subQuery;
						$wpdb->query($query);
					}

					/*	Transfert les tables non utilis�es vers la "trash" section	*/
					$query = $wpdb->prepare("RENAME TABLE " . TABLE_LIAISON_USER_GROUPS . " TO " . TRASH_DIGI_DBT_LIAISON_USER_GROUPS);
					$wpdb->query($query);
					$query = $wpdb->prepare("RENAME TABLE " . TABLE_EVA_EVALUATOR_GROUP_BIND . " TO " . TRASH_DIGI_DBT_EVALUATOR_GROUP_BIND);
					$wpdb->query($query);
				}

				if(($wpdb->get_var("show tables like '" . TABLE_EVA_USER_GROUP_DETAILS . "'") == TABLE_EVA_USER_GROUP_DETAILS) && ($wpdb->get_var("show tables like '" . TABLE_EVA_EVALUATOR_GROUP_DETAILS . "'") == TABLE_EVA_EVALUATOR_GROUP_DETAILS))
				{/*	Transfert des utilisateurs vers la table de liaison utilisateur element	*/
					/*	Groupes d'employ�	*/
					$query = $wpdb->prepare("SELECT * FROM " . TABLE_EVA_USER_GROUP_DETAILS);
					$employeeGroupsDetail = $wpdb->get_results($query);
					$subQuery = "  ";
					foreach($employeeGroupsDetail as $employeeGroupDetail)
					{
						$query = $wpdb->prepare("SELECT id FROM " . DIGI_DBT_USER_GROUP . " WHERE old_id = %d AND group_type = %s", $employeeGroupDetail->user_group_id, 'employee');
						$newGroupId = $wpdb->get_row($query);
						$subQuery .= "('', 'valid', '" . current_time('mysql', 0) . "', 1, '', '', '" . $wpdb->escape($employeeGroupDetail->user_id) . "', '" . $wpdb->escape($newGroupId->id) . "', '" . DIGI_DBT_USER_GROUP . "'), ";
					}
					/*	Groupes d'evaluateurs	*/
					$query = $wpdb->prepare("SELECT * FROM " . TABLE_EVA_EVALUATOR_GROUP_DETAILS);
					$evaluatorsGroupsDetail = $wpdb->get_results($query);
					foreach($evaluatorsGroupsDetail as $evaluatorsGroupDetail)
					{
						$query = $wpdb->prepare("SELECT id FROM " . DIGI_DBT_USER_GROUP . " WHERE old_id = %d AND group_type = %s", $evaluatorsGroupDetail->evaluator_group_id, 'evaluator');
						$newGroupId = $wpdb->get_row($query);
						$subQuery .= "('', '" . $wpdb->escape($evaluatorsGroupDetail->Status) . "', '" . $wpdb->escape($evaluatorsGroupDetail->dateEntree) . "', '" . $wpdb->escape($evaluatorsGroupDetail->affectationUserId) . "', '" . $wpdb->escape($evaluatorsGroupDetail->dateSortie) . "', '" . $wpdb->escape($evaluatorsGroupDetail->desaffectationUserId) . "', '" . $wpdb->escape($evaluatorsGroupDetail->user_id) . "', '" . $wpdb->escape($newGroupId->id) . "', '" . DIGI_DBT_USER_GROUP . "'), ";
					}
					/*	Transfert dans la nouvelle table	*/
					$subQuery = trim(substr($subQuery, 0, -2));
					if($subQuery != "")
					{
						$query =
							"INSERT INTO " . TABLE_LIAISON_USER_ELEMENT . "
								(id, status, date_affectation, id_attributeur, date_desAffectation, id_desAttributeur, id_user, id_element, table_element)
							VALUES
								" . $subQuery;
						$wpdb->query($query);
					}

					/*	Transfert les tables non utilis�es vers la "trash" section	*/
					$query = $wpdb->prepare("RENAME TABLE " . TABLE_EVA_USER_GROUP_DETAILS . " TO " . TRASH_DIGI_DBT_USER_GROUP_DETAILS);
					$wpdb->query($query);
					$query = $wpdb->prepare("RENAME TABLE " . TABLE_EVA_EVALUATOR_GROUP_DETAILS . " TO " . TRASH_DIGI_DBT_EVALUATOR_GROUP_DETAILS);
					$wpdb->query($query);
				}
			}break;
			case 66:{
				$deleted_grpt_list = EvaGroupement::getGroupements("Status = 'deleted'");

				if(is_array($deleted_grpt_list)){
					foreach($deleted_grpt_list as $deleted_grpt){
						$listeUnitesDeTravail = EvaGroupement::getUnitesEtGroupementDescendants($deleted_grpt->id);
						if(is_array($listeUnitesDeTravail)){
							foreach($listeUnitesDeTravail as $key => $uniteDefinition){
								switch($uniteDefinition['table']){
									case TABLE_GROUPEMENT:{
										EvaGroupement::deleteGroupement($uniteDefinition['value']->id);
									}break;
									case TABLE_UNITE_TRAVAIL:{
										eva_UniteDeTravail::deleteWorkingUnit($uniteDefinition['value']->id);
									}break;
								}
							}
						}
					}
				}
			}break;
			case 67:{
				$query = $wpdb->prepare("SELECT action FROM " . DIGI_DBT_ELEMENT_NOTIFICATION);
				$action_list = $wpdb->get_results($query);
				foreach($action_list as $action){
					$wpdb->update(DIGI_DBT_ELEMENT_NOTIFICATION, array('last_update_date' => current_time('mysql', 0), 'action_title' => __($action->action, 'evarisk'), 'message_to_send' => $standard_message_to_send, 'message_subject' => $standard_message_subject_to_send), array('action' => $action->action));
				}
			}break;
			case 69:{
				$user_list = evaUser::getUserList();
				foreach($user_list as $user){
					$user = new WP_User($user->ID);
					if($user->has_cap('digi_add_action')){
						$user->has_cap('digi_ask_action_front');
					}
				}
			}break;

			case '73':
		/* Mise à jour des sous elements de l'arbre des taches supprimees pour cohérence avec la corbeille des GP et UT */
				$query = $wpdb->prepare("SELECT id FROM " . TABLE_TACHE . " WHERE Status = %s", 'Deleted');
				$task_list = $wpdb->get_results($query);
				foreach ($task_list as $deleted_task) {
					$tache = new EvaTask($deleted_task->id);
					$tache->load();
					$task_children = $tache->getDescendants();
					foreach ($task_children->tasks as $index => $task) {
						$wpdb->update(TABLE_TACHE, array('Status'=>'Deleted'), array('id'=>$task->id));
						$sub_tasks = EvaTask::getChildren($task->id);
						foreach ($sub_tasks as $sub_task) {
							$wpdb->update(TABLE_ACTIVITE, array('Status'=>'Deleted'), array('id'=>$sub_task->id));
						}
					}
				}

				/*	Ajout des méthodes d'evaluation pour la pénibilité	*/
				$wpdb->insert(TABLE_METHODE, array('nom' => __('Manutention manuelle femme', 'evarisk'), 'Status' => 'Valid'));
				$methode_manutention_femme = $wpdb->insert_id;
				$wpdb->insert(TABLE_METHODE, array('nom' => __('Manutention manuelle homme', 'evarisk'), 'Status' => 'Valid'));
				$methode_manutention_homme = $wpdb->insert_id;
				$wpdb->insert(TABLE_METHODE, array('nom' => __('Vibration m&eacute;canique mains/bras', 'evarisk'), 'Status' => 'Valid'));
				$methode_vibration_main_bras = $wpdb->insert_id;
				$wpdb->insert(TABLE_METHODE, array('nom' => __('Vibration m&eacute;canique ensemble du corps', 'evarisk'), 'Status' => 'Valid'));
				$methode_vibration_corps = $wpdb->insert_id;
				$wpdb->insert(TABLE_METHODE, array('nom' => __('Posture p&eacute;nibles', 'evarisk'), 'Status' => 'Valid'));
				$methode_posture = $wpdb->insert_id;
				$wpdb->insert(TABLE_METHODE, array('nom' => __('Temp&eacute;ratures extr&ecirc;mes', 'evarisk'), 'Status' => 'Valid'));
				$methode_temperature = $wpdb->insert_id;
				$wpdb->insert(TABLE_METHODE, array('nom' => __('Bruit', 'evarisk'), 'Status' => 'Valid'));
				$methode_bruit = $wpdb->insert_id;

				/*	Changement d'image pour la catégorie	*/
				$activite_physique_cat_id_query = $wpdb->prepare("SELECT id FROM " . TABLE_CATEGORIE_DANGER . " WHERE nom = %s", strtolower( __('Activit&eacute; physique', 'evarisk')));
				$activite_physique_cat_id = $wpdb->get_var($activite_physique_cat_id_query);
				$activite_physique_cat_id_pict = EvaPhoto::saveNewPicture(TABLE_CATEGORIE_DANGER, $activite_physique_cat_id, 'medias/images/Pictos/categorieDangers/activitePhysique.png');
				EvaPhoto::setMainPhoto(TABLE_CATEGORIE_DANGER, $activite_physique_cat_id, $activite_physique_cat_id_pict, 'yes');

				/*	Create the new danger categories	*/
				$inrs_danger_categories = unserialize(DIGI_INRS_DANGER_LIST);
				foreach ($inrs_danger_categories as $danger_cat) {
					if (!empty($danger_cat['version']) && ($danger_cat['version'] == $version)) {
						$new_danger_cat_id = categorieDangers::saveNewCategorie($danger_cat['nom']);

						/*	If user ask to add danger in categories	*/
						$wpdb->insert(TABLE_DANGER, array('nom' => __('Divers', 'evarisk') . ' ' . strtolower($danger_cat['nom']), 'id_categorie' => $new_danger_cat_id));
						if ( !empty($danger_cat['risks']) && is_array($danger_cat['risks']) ) {
							foreach ( $danger_cat['risks'] as $risk_to_create ) {
								$wpdb->insert(TABLE_DANGER, array('nom' => $risk_to_create, 'id_categorie' => $new_danger_cat_id));
							}
						}

						/*	Insert picture for danger categories	*/
						if ( !empty($danger_cat['picture']) ) {
							$new_cat_pict_id = EvaPhoto::saveNewPicture(TABLE_CATEGORIE_DANGER, $new_danger_cat_id, $danger_cat['picture']);
							EvaPhoto::setMainPhoto(TABLE_CATEGORIE_DANGER, $new_danger_cat_id, $new_cat_pict_id, 'yes');
						}
					}
				}

		/* Ajout des variables permettant l'evaluation de la pénibilité */
				/*	Manutention pour les hommes	*/
				$question_manutention_homme[] = array('question'=>__('Charges < 10Kg ou 0,5 tonnes par jour', 'evarisk'), 'seuil'=>0);
				$question_manutention_homme[] = array('question'=>__('Charges < 15Kg ou 1 tonne par jour', 'evarisk'), 'seuil'=>48);
				$question_manutention_homme[] = array('question'=>__('Charges > 35Kg ou 2 tonnes par jour', 'evarisk'), 'seuil'=>51);
				$question_manutention_homme[] = array('question'=>__('Charges > 49Kg ou 5 tonnes par jour', 'evarisk'), 'seuil'=>80);
				$wpdb->insert(TABLE_VARIABLE, array('nom' => __('Port de charge (Homme)', 'evarisk'), 'Status' => 'Valid', 'min'=>1, 'max'=>4, 'annotation'=>__('Risque noir si des charges sont > &agrave; 49Kg ou 5 tonnes par jour\nRisque rouge si les charges sont > &agrave; 35Kg ou 2 tonnes par jour\nRisque orange si les charges sont < &agrave; 15Kg et 1 tonne par jour\nRisque blanc si les charges sont < 10Kg et 0,5 tonne par jour', 'evarisk'), 'affichageVar'=>'checkbox', 'questionVar'=>serialize($question_manutention_homme), 'questionTitre'=>__('Poids total transport&eacute; sur une journ&eacute;e de travail de 7 heures', 'evarisk')));
				$variable_manutention_homme = $wpdb->insert_id;
				/* Liaison entre variable et methode */
				$liaison_methode_manutention_variable = $wpdb->insert(TABLE_AVOIR_VARIABLE, array('id_methode'=>$methode_manutention_homme, 'id_variable'=>$variable_manutention_homme, 'ordre'=>1, 'date'=>current_time('mysql',0), 'Status'=>'Valid'));

				/*	Manutention pour les femmes	*/
				$question_manutention_femme[] = array('question'=>__('Charges < 5Kg ou 0,25 tonnes par jour', 'evarisk'), 'seuil'=>0);
				$question_manutention_femme[] = array('question'=>__('Charges < 7,5Kg ou 0,5 tonne par jour', 'evarisk'), 'seuil'=>48);
				$question_manutention_femme[] = array('question'=>__('Charges > 17,5Kg ou 1 tonnes par jour', 'evarisk'), 'seuil'=>51);
				$question_manutention_femme[] = array('question'=>__('Charges > 25Kg ou 2,5 tonnes par jour', 'evarisk'), 'seuil'=>80);
				$wpdb->insert(TABLE_VARIABLE, array('nom' => __('Port de charge (Femme)', 'evarisk'), 'Status' => 'Valid', 'min'=>1, 'max'=>4, 'annotation'=>__('Risque noir si des charges sont > &agrave; 25Kg ou 2,5 tonnes par jour\nRisque rouge si les charges sont > &agrave; 17,5Kg ou 1 tonne par jour\nRisque orange si les charges sont < &agrave; 7,5Kg et 0,5 tonne par jour\nRisque blanc si les charges sont < 5Kg et 0,25 tonne par jour', 'evarisk'), 'affichageVar'=>'checkbox', 'questionVar'=>serialize($question_manutention_femme), 'questionTitre'=>__('Poids total transport&eacute; sur une journ&eacute;e de travail de 7 heures', 'evarisk')));
				$variable_manutention_femme = $wpdb->insert_id;
				/* Liaison entre variable et methode */
				$liaison_methode_manutention_variable = $wpdb->insert(TABLE_AVOIR_VARIABLE, array('id_methode'=>$methode_manutention_femme, 'id_variable'=>$variable_manutention_femme, 'ordre'=>1, 'date'=>current_time('mysql',0), 'Status'=>'Valid'));

				/*	Postures penibles */
				$question_postures_penibles[] = array('question'=>__('Somme calcul&eacute; &agrave; partir de la check-list OSHA est < 3', 'evarisk'), 'seuil'=>0);
				$question_postures_penibles[] = array('question'=>__('Somme calcul&eacute; &agrave; partir de la check-list OSHA est < 5', 'evarisk'), 'seuil'=>48);
				$question_postures_penibles[] = array('question'=>__('Somme calcul&eacute; &agrave; partir de la check-list OSHA est >= 5', 'evarisk'), 'seuil'=>51);
				$question_postures_penibles[] = array('question'=>__('Somme calcul&eacute; &agrave; partir de la check-list OSHA est > 9', 'evarisk'), 'seuil'=>80);
				$wpdb->insert(TABLE_VARIABLE, array('nom' => __('Postures p&eacute;nibles', 'evarisk'), 'Status' => 'Valid', 'min'=>1, 'max'=>4, 'annotation'=>__('Risque noir si la somme est > 9 (choix arbitraire Evarisk)\nRisque rouge si la somme est >= 5 (choix OSHA)\nRisque orange si la somme est < 5 (choix arbitraire Evarisk)\nRisque blanc si la somme est < 3 (choix arbitraire Evarisk)', 'evarisk'), 'affichageVar'=>'checkbox', 'questionVar'=>serialize($question_postures_penibles), 'questionTitre'=>__('&Eacute;valuation des facteurs de risque relatifs aux membres sup&eacute;rieurs', 'evarisk')));
				$variable_postures_penible = $wpdb->insert_id;
				/* Liaison entre variable et methode */
				$liaison_methode_posture_variable = $wpdb->insert(TABLE_AVOIR_VARIABLE, array('id_methode'=>$methode_posture, 'id_variable'=>$variable_postures_penible, 'ordre'=>1, 'date'=>current_time('mysql',0), 'Status'=>'Valid'));

				/*	Postures penibles Mains/bras */
				$question_vibrations[] = array('question'=>__('Vibrations < 1,25 m/s&sup2;', 'evarisk'), 'seuil'=>0);
				$question_vibrations[] = array('question'=>__('Vibrations < 2,5 m/s&sup2;', 'evarisk'), 'seuil'=>48);
				$question_vibrations[] = array('question'=>__('Vibrations > 2,5 m/s&sup2;', 'evarisk'), 'seuil'=>51);
				$question_vibrations[] = array('question'=>__('Vibrations > 5 m/s&sup2;', 'evarisk'), 'seuil'=>80);
				$wpdb->insert(TABLE_VARIABLE, array('nom' => __('Vibrations des mains/bras', 'evarisk'), 'Status' => 'Valid', 'min'=>1, 'max'=>4, 'annotation'=>__('Risque noir > 5 M/s&sup2;\nRisque rouge > 2,5 M/S&sup2;\nRisque orange < 2,5 m/s&sup2;\Risque Blanc < 1,25 m/s&sup2; (choix arbitraire Evarisk)', 'evarisk'), 'affichageVar'=>'checkbox', 'questionVar'=>serialize($question_vibrations), 'questionTitre'=>__('Pour une exposition quotidienne (8h)', 'evarisk')));
				$variable_vibrations_main_bras = $wpdb->insert_id;
				/* Liaison entre variable et methode */
				$liaison_methode_posture_variable = $wpdb->insert(TABLE_AVOIR_VARIABLE, array('id_methode'=>$methode_vibration_main_bras, 'id_variable'=>$variable_vibrations_main_bras, 'ordre'=>1, 'date'=>current_time('mysql',0), 'Status'=>'Valid'));

				/*	Postures penibles Corps */
				$question_vibrations_corps[] = array('question'=>__('Vibrations < 1,25 m/s&sup2;', 'evarisk'), 'seuil'=>0);
				$question_vibrations_corps[] = array('question'=>__('Vibrations < 2,5 m/s&sup2;', 'evarisk'), 'seuil'=>48);
				$question_vibrations_corps[] = array('question'=>__('Vibrations > 2,5 m/s&sup2;', 'evarisk'), 'seuil'=>51);
				$question_vibrations_corps[] = array('question'=>__('Vibrations > 5 m/s&sup2;', 'evarisk'), 'seuil'=>80);
				$wpdb->insert(TABLE_VARIABLE, array('nom' => __('Vibration de l\'ensemble du corps', 'evarisk'), 'Status' => 'Valid', 'min'=>1, 'max'=>4, 'annotation'=>__('Risque noir > 1.15 m/s&sup2;\nRisque rouge > 0,5 m/s&sup2;\nRisque orange < 0,5 m/s&sup2;\nRisque Blanc < 0,25 m/s&sup2; (choix arbitraire Evarisk)', 'evarisk'), 'affichageVar'=>'checkbox', 'questionVar'=>serialize($question_vibrations_corps), 'questionTitre'=>__('Pour une exposition quotidienne (8h)', 'evarisk')));
				$variable_vibrations_corps = $wpdb->insert_id;
				/* Liaison entre variable et methode */
				$liaison_methode_posture_variable = $wpdb->insert(TABLE_AVOIR_VARIABLE, array('id_methode'=>$methode_vibration_corps, 'id_variable'=>$variable_vibrations_corps, 'ordre'=>1, 'date'=>current_time('mysql',0), 'Status'=>'Valid'));

				/*	Temperature */
				$question_temperatures[] = array('question'=>__('20&deg;C < T > 24&deg;C', 'evarisk'), 'seuil'=>0);
				$question_temperatures[] = array('question'=>__('15&deg;C < T > 30&deg;C', 'evarisk'), 'seuil'=>48);
				$question_temperatures[] = array('question'=>__('T > 30&deg;C ou < 10&deg;C', 'evarisk'), 'seuil'=>51);
				$question_temperatures[] = array('question'=>__('T > 33&deg;C ou < 5&deg;C', 'evarisk'), 'seuil'=>80);
				$wpdb->insert(TABLE_VARIABLE, array('nom' => __('Exposition &agrave; des temp&eacute;ratures extr&ecirc;mes', 'evarisk'), 'Status' => 'Valid', 'min'=>1, 'max'=>4, 'annotation'=>__('Risque noir si T&deg; > 33&deg;C ou si T&deg; < 5&deg;C (seuil INRS ed 931)\nRisque rouge si T&deg; > 30&deg;C ou si T&deg; < 10&deg;C (travailler mieux)\nRisque orange si  15&deg;C < T&deg;< 30&deg;C (Choix arbitraire Evarisk)\nRisque Blanc si 20&deg;C < T&deg;< 24&deg;C (Choix INRS)', 'evarisk'), 'affichageVar'=>'checkbox', 'questionVar'=>serialize($question_temperatures), 'questionTitre'=>__('Exposition aux temp&eacute;ratures extr&ecirc;mes (Dur&eacute;e de 6 heures par jour)', 'evarisk')));
				$variable_temperature = $wpdb->insert_id;
				/* Liaison entre variable et methode */
				$liaison_methode_temperature_variable = $wpdb->insert(TABLE_AVOIR_VARIABLE, array('id_methode'=>$methode_temperature, 'id_variable'=>$variable_temperature, 'ordre'=>1, 'date'=>current_time('mysql',0), 'Status'=>'Valid'));

				/*	Bruit */
				$question_bruit[] = array('question'=>__('Lex, 6h < 75dB(a) ou < 120 dB(c)', 'evarisk'), 'seuil'=>0);
				$question_bruit[] = array('question'=>__('Lex, 8h < 80dB(a) ou < 112 dB(c)', 'evarisk'), 'seuil'=>48);
				$question_bruit[] = array('question'=>__('Lex, 8h > 85dB(a) ou > 137 dB(c)', 'evarisk'), 'seuil'=>51);
				$question_bruit[] = array('question'=>__('Lex, 8h > 87dB(a) ou > 140 dB(c)', 'evarisk'), 'seuil'=>80);
				$wpdb->insert(TABLE_VARIABLE, array('nom' => __('Exposition au bruit', 'evarisk'), 'Status' => 'Valid', 'min'=>1, 'max'=>4, 'annotation'=>__('Risque noir Lex, 8h > 87 dB(a) ou > 140 dB(c) seuil r&eacute;glementaire (France et europe)\nRisque rouge Lex, 8h > 85 dB(a) ou > 137 dB(c) seuil r&eacute;glementaire (France et europe)\nRisque orange Lex, 8h  < 80 dB(a) ou < 112 dB(c) seuil europ&eacute;en\nRisque Blanc Lex, 6h < 75 dB(a) ou < 120 dB(c) seuil travailler mieux', 'evarisk'), 'affichageVar'=>'checkbox', 'questionVar'=>serialize($question_bruit), 'questionTitre'=>__('Exposition au bruit', 'evarisk')));
				$variable_bruit = $wpdb->insert_id;
				/* Liaison entre variable et methode */
				$liaison_methode_bruit_variable = $wpdb->insert(TABLE_AVOIR_VARIABLE, array('id_methode'=>$methode_bruit, 'id_variable'=>$variable_bruit, 'ordre'=>1, 'date'=>current_time('mysql',0), 'Status'=>'Valid'));


		/* Equivalence variable etalon */
				/*	Manutention manuelle femme	*/
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_manutention_femme, 'id_valeur_etalon'=>0, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>1, 'Status'=>'Valid'));
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_manutention_femme, 'id_valeur_etalon'=>48, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>2, 'Status'=>'Valid'));
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_manutention_femme, 'id_valeur_etalon'=>51, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>3, 'Status'=>'Valid'));
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_manutention_femme, 'id_valeur_etalon'=>100, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>4, 'Status'=>'Valid'));

				/*	Manutention manuelle homme	*/
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_manutention_homme, 'id_valeur_etalon'=>0, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>1, 'Status'=>'Valid'));
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_manutention_homme, 'id_valeur_etalon'=>48, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>2, 'Status'=>'Valid'));
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_manutention_homme, 'id_valeur_etalon'=>51, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>3, 'Status'=>'Valid'));
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_manutention_homme, 'id_valeur_etalon'=>100, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>4, 'Status'=>'Valid'));

				/*	Postures penibles 	*/
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_posture, 'id_valeur_etalon'=>0, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>1, 'Status'=>'Valid'));
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_posture, 'id_valeur_etalon'=>48, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>2, 'Status'=>'Valid'));
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_posture, 'id_valeur_etalon'=>51, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>3, 'Status'=>'Valid'));
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_posture, 'id_valeur_etalon'=>100, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>4, 'Status'=>'Valid'));

				/*	Vibrations mécaniques mains/bras 	*/
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_vibration_main_bras, 'id_valeur_etalon'=>0, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>1, 'Status'=>'Valid'));
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_vibration_main_bras, 'id_valeur_etalon'=>48, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>2, 'Status'=>'Valid'));
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_vibration_main_bras, 'id_valeur_etalon'=>51, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>3, 'Status'=>'Valid'));
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_vibration_main_bras, 'id_valeur_etalon'=>100, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>4, 'Status'=>'Valid'));

				/*	Vibrations mécaniques ensemble du corps 	*/
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_vibration_corps, 'id_valeur_etalon'=>0, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>1, 'Status'=>'Valid'));
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_vibration_corps, 'id_valeur_etalon'=>48, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>2, 'Status'=>'Valid'));
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_vibration_corps, 'id_valeur_etalon'=>51, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>3, 'Status'=>'Valid'));
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_vibration_corps, 'id_valeur_etalon'=>100, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>4, 'Status'=>'Valid'));

				/*	Temperature 	*/
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_temperature, 'id_valeur_etalon'=>0, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>1, 'Status'=>'Valid'));
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_temperature, 'id_valeur_etalon'=>48, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>2, 'Status'=>'Valid'));
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_temperature, 'id_valeur_etalon'=>51, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>3, 'Status'=>'Valid'));
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_temperature, 'id_valeur_etalon'=>100, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>4, 'Status'=>'Valid'));

				/*	Bruit 	*/
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_bruit, 'id_valeur_etalon'=>0, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>1, 'Status'=>'Valid'));
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_bruit, 'id_valeur_etalon'=>48, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>2, 'Status'=>'Valid'));
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_bruit, 'id_valeur_etalon'=>51, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>3, 'Status'=>'Valid'));
				$wpdb->insert(TABLE_EQUIVALENCE_ETALON, array('id_methode'=>$methode_bruit, 'id_valeur_etalon'=>100, 'date'=>current_time('mysql', 0), 'valeurMaxMethode'=>4, 'Status'=>'Valid'));

		/* Affectation du statut "penible" aux risques concernés */
				$query = $wpdb->prepare("SELECT id FROM " . TABLE_DANGER . " WHERE nom = %s ", __('Divers', 'evarisk') . ' ' . strtolower(__('Nuisances sonores', 'evarisk')));
				$risque_nuissance_sonore = $wpdb->get_var($query);
				$wpdb->update(TABLE_DANGER, array('choix_danger' => serialize(array('penibilite')), 'methode_eva_defaut' => $methode_bruit), array('id' => $risque_nuissance_sonore));

				$query = $wpdb->prepare("SELECT id FROM " . TABLE_DANGER . " WHERE nom = %s ", __('Divers', 'evarisk') . ' ' . strtolower(__('Ambiances climatiques', 'evarisk')));
				$risque_temperature = $wpdb->get_var($query);
				$wpdb->update(TABLE_DANGER, array('choix_danger' => serialize(array('penibilite')), 'methode_eva_defaut' => $methode_temperature), array('id' => $risque_temperature));

				$query = $wpdb->prepare("SELECT id FROM " . TABLE_DANGER . " WHERE nom = %s ", __('Divers', 'evarisk') . ' ' . strtolower(__('Manutention manuelle', 'evarisk')));
				$risque_manutention_manuelle = $wpdb->get_var($query);
				$wpdb->update(TABLE_DANGER, array('choix_danger' => serialize(array('penibilite')), 'methode_eva_defaut' => $methode_manutention_homme), array('id' => $risque_manutention_manuelle));

				$query = $wpdb->prepare("SELECT id FROM " . TABLE_DANGER . " WHERE nom = %s ", __('Divers', 'evarisk') . ' ' . strtolower(__('Postures penibles', 'evarisk')));
				$risque_posture = $wpdb->get_var($query);
				$wpdb->update(TABLE_DANGER, array('choix_danger' => serialize(array('penibilite')), 'methode_eva_defaut' => $methode_posture), array('id' => $risque_posture));

				$query = $wpdb->prepare("SELECT id FROM " . TABLE_DANGER . " WHERE nom = %s ", __('Divers', 'evarisk') . ' ' . strtolower(__('Vibrations', 'evarisk')));
				$risque_vibrations = $wpdb->get_var($query);
				$wpdb->update(TABLE_DANGER, array('choix_danger' => serialize(array('penibilite')), 'methode_eva_defaut' => $methode_vibration_main_bras), array('id' => $risque_vibrations));
			break;

			case 74:
				/*	Check if default categories exist	*/
				$inrs_danger_categories = unserialize(DIGI_INRS_DANGER_LIST);
				foreach ($inrs_danger_categories as $danger_cat) {
					$query = $wpdb->prepare("SELECT id FROM " . TABLE_CATEGORIE_DANGER . " WHERE nom = %s", $danger_cat['nom']);
					$category_id = $wpdb->get_var($query);
					if ( empty($category_id) ) {
						$new_danger_cat_id = categorieDangers::saveNewCategorie($danger_cat['nom']);

						/*	If user ask to add danger in categories	*/
						$query = $wpdb->prepare("SELECT D.id FROM " . TABLE_DANGER . " AS D WHERE D.nom = %s AND D.Status = %s AND D.id_categorie NOT IN (SELECT id FROM " . TABLE_CATEGORIE_DANGER . ")", __('Divers', 'evarisk') . ' ' . strtolower($danger_cat['nom']), 'Valid');
						$danger_id = $wpdb->get_var($query);
						if ( empty($danger_id) ) {
							$wpdb->insert(TABLE_DANGER, array('nom' => __('Divers', 'evarisk') . ' ' . strtolower($danger_cat['nom']), 'id_categorie' => $new_danger_cat_id));
						}
						else {
							$wpdb->update(TABLE_DANGER, array('id_categorie' => $new_danger_cat_id), array('id' => $danger_id));
						}

						/*	Insert picture for danger categories	*/
						if ( !empty($danger_cat['picture']) ) {
							$new_cat_pict_id = EvaPhoto::saveNewPicture(TABLE_CATEGORIE_DANGER, $new_danger_cat_id, $danger_cat['picture']);
							EvaPhoto::setMainPhoto(TABLE_CATEGORIE_DANGER, $new_danger_cat_id, $new_cat_pict_id, 'yes');
						}
					}
				}
			break;
		}
	}

}
