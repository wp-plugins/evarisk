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

		echo '<div class="wrap">';
		$idForm = 'formulaireInstallation';
		$formulaireInstallation = '<div id="wrap' . $idForm . '"><div class="hide" id="digirisk_loading_picto" ><img src="' . PICTO_LOADING_ROUND . '" alt="loading..." /></div>';
		$formulaireInstallation = $formulaireInstallation . EvaDisplayInput::ouvrirForm('POST', $idForm, $idForm);

		$idTable = 'evariskSetup';
		$insertions[0]['class'] = 'setupTitle';
		$insertions[1]['class'] = 'setupAction';
		$scriptCB = '
			<script type="text/javascript">
				evarisk(document).ready(function(){
					evarisk(\'#%1$s\').prop("checked", "checked");
					evarisk(\'#%1$s\').addClass("cbSetup");
				});
			</script>';
		{//Dangers
			$insertions[0]['value'] = '<p class="titreSetup">' . __('Dangers', 'evarisk') . '</p>';
			$insertions[1]['value'] = '';
			{//Ins&eacute;rer les cat&eacute;gories INRS
				$labelInput = ucfirst(strtolower(sprintf(__("Ins&eacute;rer %s", 'evarisk'),__("les cat&eacute;gories INRS", 'evarisk'))));
				$labelInput[1] = ($labelInput[0] == "&")?ucfirst($labelInput[1]):$labelInput[1];
				$id = "insertionCategorie";
				$nomChamps = "insertionCategorie";
				$script = sprintf($scriptCB, $id);
				$insertions[1]['value'] = $insertions[1]['value'] . EvaDisplayInput::afficherInput('checkbox', $id, '', '', $labelInput, $nomChamps, false, false, 1, '', '', '', $script);
			}
			{//Ins&eacute;rer un danger par d&eacute;faut dans chaque cat&eacute;gorie
				$labelInput = ucfirst(strtolower(sprintf(__("Ins&eacute;rer %s", 'evarisk'),__("un danger par d&eacute;faut dans chaque cat&eacute;gorie", 'evarisk'))));
				$labelInput[1] = ($labelInput[0] == "&")?ucfirst($labelInput[1]):$labelInput[1];
				$id = "insertionDanger";
				$nomChamps = "insertionDanger";
				$script = sprintf($scriptCB, $id);
				$insertions[1]['value'] = $insertions[1]['value'] . EvaDisplayInput::afficherInput('checkbox', $id, '', '', $labelInput, $nomChamps, false, false, 1, '', '', '', $script);
			}
			$lignesDeValeurs[] = $insertions;
			$idLignes[] = 'digi_install_danger';
		}
		{//M&eacute;thode
			$insertions[0]['value'] = '<p class="titreSetup">' . __('M&eacute;thodes', 'evarisk') . '</p>';
			$insertions[1]['value'] = '';
			{//Ins&eacute;rer la m&eacute;thode Evarisk
				$labelInput = ucfirst(strtolower(sprintf(__("Ins&eacute;rer %s", 'evarisk'),__("la m&eacute;thode Evarisk", 'evarisk'))));
				$labelInput[1] = ($labelInput[0] == "&")?ucfirst($labelInput[1]):$labelInput[1];
				$id = "insertionMethodeEvarisk";
				$nomChamps = "insertionMethodeEvarisk";
				$script = sprintf($scriptCB, $id);
				$insertions[1]['value'] = $insertions[1]['value'] . EvaDisplayInput::afficherInput('checkbox', $id, '', '', $labelInput, $nomChamps, false, false, 1, '', '', '', $script);
			}
			$lignesDeValeurs[] = $insertions;
			$idLignes[] = 'digi_install_method';
		}
		{//EPI 																										/!\	PLUS UTILISE/!\
			//DELETE FROM VERSION 44
		}
		{//Theme Evarisk
			$insertions[0]['value'] = '<p class="titreSetup">' . __('Th&egrave;me', 'evarisk') . '</p>';
			$insertions[1]['value'] = '';
			{//Ins&eacute;rer la m&eacute;thode Evarisk
				$labelInput = ucfirst(strtolower(__("Activer le th&egrave;me Evarisk. (NB: vous pourrez activer le th&egrave;me ult&eacute;rieurement)", 'evarisk')));
				$labelInput[1] = ($labelInput[0] == "&")?ucfirst($labelInput[1]):$labelInput[1];
				$id = "activationThemeEvarisk";
				$nomChamps = "activationThemeEvarisk";
				$script = sprintf($scriptCB, $id);
				$insertions[1]['value'] .= EvaDisplayInput::afficherInput('checkbox', $id, '', '', $labelInput, $nomChamps, false, false, 1, '', '', '', $script);
			}
			$lignesDeValeurs[] = $insertions;
			$idLignes[] = 'digi_install_theme';
		}


		/*	Add the autoinstall option for the executable version of the soft 	*/
		$autoLaunch = '';


		$classes = null;
		// $idLignes = null;
		$formulaireInstallation .= EvaDisplayDesign::getTable($idTable, array(__('Nom du module', 'evarisk'), __('Actions', 'evarisk')), $lignesDeValeurs, $classes, $idLignes, '');
		{//Bouton installer Evarisk
			$allVariables = MethodeEvaluation::getAllVariables();
			$idBouttonEnregistrer = 'installerEvarisk';

		/*	Autoinstall at launch if the good parameter is passe din the url	*/
			$autoLaunch =  
		'var autoInstall = false;';
		if(STANDALONEVERSION)
		{
			$autoLaunch =  
		'var autoInstall = true;';
		}

			$scriptEnregistrement = '<script type="text/javascript">
				evarisk(document).ready(function() {

					' . $autoLaunch . '
				
					evarisk("#' . $idTable . ' tfoot").remove();
					evarisk("#' . $idTable . ' thead").remove();
					evarisk("#' . $idBouttonEnregistrer . '").click(function() {
						var methodes = new Array();
						methodes[0] = new Array();
						methodes[0][0] = evarisk(\'#insertionMethodeEvarisk\').prop("checked");
						methodes[0][1] = "evarisk";
						var goOnInstall = false;
						if( !evarisk("#activationThemeEvarisk").is(":checked") || (autoInstall))
						{
							goOnInstall = true;
						}
						else if(evarisk("#activationThemeEvarisk").is(":checked") && confirm("' . __('Etes vous sur de vouloir activer le theme Evarisk pour votre Blog?\nNB: Si vous avez un theme personnalise celui sera remplace par le theme Evarisk. Il restera disponible dans la liste des themes.', 'evarisk') . '"))
						{
							goOnInstall = true;
						}

						if(goOnInstall)
						{
							evarisk("#installLoading").html(evarisk("#digirisk_loading_picto").html());
							evarisk("#installButton").html("' . __('Installation en cours. Merci de patienter', 'evarisk') . '");
							evarisk("#wrap' . $idForm . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
								"post": "true", 
								"nom": "installerEvarisk", 
								"categorieDangers": evarisk("#insertionCategorie").prop("checked"),
								"danger": evarisk("#insertionDanger").prop("checked"),
								"methodes": methodes,
								"theme": evarisk("#activationThemeEvarisk").prop("checked")
							});
						}
					});
				});
				</script>';
			$formulaireInstallation .= 
		'	<div id="installLoading" class="alignright" >&nbsp;</div>
			<div id="installButton" class="alignright" >' . EvaDisplayInput::afficherInput('button', $idBouttonEnregistrer, __('Installer Evarisk', 'evarisk'), null, '', 'save', false, false, '255', 'button-primary alignright', '', '', $scriptEnregistrement) . '</div>';
		}
			
		$formulaireInstallation .= EvaDisplayInput::fermerForm($idForm);
		echo $formulaireInstallation . '
		</div>
		</div>';
		/*	Autoinstall at launch if the good parameter is passe din the url	*/
		if(STANDALONEVERSION)
		{
			echo
		'<script type="text/javascript">
				evarisk(document).ready(function() {
					evarisk("#' . $idBouttonEnregistrer . '").click();
				});
		</script>';
		}

	}

	
	/**
	*	Method called when plugin is loaded for database update. This method allows to update the database structure, insert default content.
	*/
	function update_digirisk(){
		global $wpdb, $digirisk_db_table, $digirisk_db_table_list, $digirisk_update_way, $digirisk_db_content_add, $digirisk_db_content_update, $digirisk_db_options_add;

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$current_db_version = get_option('digirisk_db_options', 0);
		$current_db_version = $current_db_version['base_evarisk'];

		$current_def_max_version = max(array_keys($digirisk_update_way));
		$new_version = $current_def_max_version + 1;
		$version_nb_delta = $current_def_max_version - $current_db_version;

		$do_changes = false;

		/*	Check if there are modification to do	*/
		if($current_def_max_version >= $current_db_version){
			/*	Check the lowest version of db to execute	*/
			$lowest_version_to_execute = $current_def_max_version - $version_nb_delta;

			for($i = $lowest_version_to_execute; $i <= $current_def_max_version; $i++){
				/*	Check if there are modification to do	*/
				if(isset($digirisk_update_way[$i])){
					/*	Check if there are modification to make on table	*/
					if(isset($digirisk_db_table_list[$i])){
						foreach($digirisk_db_table_list[$i] as $table_name){
							dbDelta($digirisk_db_table[$table_name]);
						}
						$do_changes = true;
					}


			/********************/
			/*		Insert data		*/
			/********************/
					/*	Options content	*/
					if(is_array($digirisk_db_options_add) && is_array($digirisk_db_options_add[$i]) && (count($digirisk_db_options_add[$i]) > 0)){
						foreach($digirisk_db_options_add[$i] as $option_name => $option_content){
							add_option($option_name, $option_content, '', 'yes');
						}
					}
					if(is_array($digirisk_db_options_update) && is_array($digirisk_db_options_update[$i]) && (count($digirisk_db_options_update[$i]) > 0)){
						foreach($digirisk_db_options_update[$i] as $option_name => $option_content){
							update_option($option_name, $option_content);
						}
					}

					/*	Add datas	*/
					if(is_array($digirisk_db_content_add) && is_array($digirisk_db_content_add[$i]) && (count($digirisk_db_content_add[$i]) > 0)){
						foreach($digirisk_db_content_add[$i] as $table_name => $def){
							foreach($def as $information_index => $table_information){
								$wpdb->insert($table_name, $table_information, '%s');
								$do_changes = true;
							}
						}
					}

					/*	Update datas	*/
					if(is_array($digirisk_db_content_update) && is_array($digirisk_db_content_update[$i]) && (count($digirisk_db_content_update[$i]) > 0)){
						foreach($digirisk_db_content_update[$i] as $table_name => $def){
							foreach($def as $information_index => $table_information){
								$wpdb->update($table_name, $table_information['datas'], $table_information['where'], '%s', '%s');
								$do_changes = true;
							}
						}
					}
					/*	Update datas	*/
					if(is_array($digirisk_db_update) && is_array($digirisk_db_update[$i]) && (count($digirisk_db_update[$i]) > 0)){
						foreach($digirisk_db_update[$i] as $table_name => $def){
							foreach($def as $information_index => $table_information){
								$query = $wpdb->prepare($table_information);
								$wpdb->query($table_information);
								$do_changes = true;
							}
						}
					}


			/******************************************************/
			/*		Make special operation on database structure		*/
			/******************************************************/
					if(is_array($digirisk_table_structure_change) && is_array($digirisk_table_structure_change[$i]) && (count($digirisk_table_structure_change[$i]) > 0)){
						foreach($digirisk_table_structure_change[$i] as $table_name => $operations_to_make){
							foreach($operations_to_make as $operation_index => $operation){
								$query = $wpdb->prepare($operation['MAIN_ACTION'] . " TABLE " . $table_name . " " . $operation['ACTION'] . " " . $operation['ACTION_CONTENT']);
								$wpdb->query($query);
							}
						}
					}
				}

				self::make_specific_operation_on_update($i);
			}
		}

		/*	Update the db version option value	*/
		// $do_changes = false;
		if($do_changes){
			$digirisk_db_options = array();
			$digirisk_db_options['base_evarisk'] = $new_version;
			update_option('digirisk_db_options', $digirisk_db_options);
		}
	}

	/**
	*
	*/
	function make_specific_operation_on_update($version){
		global $wpdb;
		switch($version){
			
		}
	}

}