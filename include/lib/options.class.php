<?php
/**
* Plugin options' management
* 
* Define the settings page, with the different field to output and field's validators
* @author Eoxia <dev@eoxia.com>
* @version 1.0
* @package evarisk
* @subpackage librairies
*/

/**
* Define the settings page, with the different field to output and field's validators
* @package evarisk
* @subpackage librairies
*/
class digirisk_options
{
	/**
	*	Declare the different options for the plugin	
	*/
	function evarisk_add_options(){
		register_setting('digirisk_options', 'digirisk_options', array('digirisk_options', 'digirisk_options_validator'));
		register_setting('digirisk_options', 'digirisk_tree_options', array('digirisk_options', 'digirisk_tree_options_validator'));
		register_setting('digirisk_options', 'digirisk_product_options', array('digirisk_options', 'digirisk_product_options_validator'));
		register_setting('digirisk_db_option', 'digirisk_db_option');

		{/* Declare the general options	*/
			add_settings_section('digi_main_options', null, array('digirisk_options', 'main_options_output'), 'digirisk_options_general');
			/*	Add the different field for current section	*/
			add_settings_field('digi_activ_trash', __('Activer la corbeille', 'evarisk'), array('digirisk_options', 'digi_activ_trash'), 'digirisk_options_general', 'digi_main_options');
		}

		{/* Declare the different options for the correctiv actions	*/
			add_settings_section('digi_options_ac', null, array('digirisk_options', 'options_output_ac'), 'digirisk_options_correctivaction');
			/*	Add the different field for current section	*/
			add_settings_field('digi_ac_advancedCA_field', __('Activer les actions correctives avanc&eacute;es', 'evarisk'), array('digirisk_options', 'digi_ac_advancedCA_field'), 'digirisk_options_correctivaction', 'digi_options_ac');
			add_settings_field('digi_ac_supervisormandatory_field', __('Responsable des t&acirc;ches obligatoire', 'evarisk'), array('digirisk_options', 'digi_ac_supervisormandatory_field'), 'digirisk_options_correctivaction', 'digi_options_ac');
			add_settings_field('digi_ac_subsupervisormandatory_field', __('Responsable des sous-t&acirc;ches obligatoire', 'evarisk'), array('digirisk_options', 'digi_ac_subsupervisormandatory_field'), 'digirisk_options_correctivaction', 'digi_options_ac');
			add_settings_field('digi_ac_changesold_field', __('Possibilit&eacute; de modifier une t&acirc;che sold&eacute;e', 'evarisk'), array('digirisk_options', 'digi_ac_changesold_field'), 'digirisk_options_correctivaction', 'digi_options_ac');
			add_settings_field('digi_ac_changesubsold_field', __('Possibilit&eacute; de modifier une sous-t&acirc;che sold&eacute;e', 'evarisk'), array('digirisk_options', 'digi_ac_changesubsold_field'), 'digirisk_options_correctivaction', 'digi_options_ac');
			add_settings_field('digi_ac_alertsoldnot100_field', __('Avertir lorsqu\'on tente de solder une t&acirc;che qui n\'a pas atteint les 100%', 'evarisk'), array('digirisk_options', 'digi_ac_alertsoldnot100_field'), 'digirisk_options_correctivaction', 'digi_options_ac');
			add_settings_field('digi_ac_alertundersoldnot100_field', __('Avertir lorsqu\'on tente de solder une t&acirc;che ayant des sous-t&acirc;ches qui n\'ont pas atteint les 100%', 'evarisk'), array('digirisk_options', 'digi_ac_alertundersoldnot100_field'), 'digirisk_options_correctivaction', 'digi_options_ac');
			add_settings_field('digi_ac_displayonlysoldtaskinrisk_field', __('Affecter uniquement les t&acirc;ches sold&eacute;es aux risques', 'evarisk'), array('digirisk_options', 'digi_ac_displayonlysoldtaskinrisk_field'), 'digirisk_options_correctivaction', 'digi_options_ac');
			add_settings_field('digi_ac_exportprioritytaskonly_field', __('Exporter uniquement les t&acirc;ches prioritaires', 'evarisk'), array('digirisk_options', 'digi_ac_exportprioritytaskonly_field'), 'digirisk_options_correctivaction', 'digi_options_ac');
			add_settings_field('digi_ac_taskexport_field', __('Afficher le bouton d\'export des actions correctives au format texte', 'evarisk'), array('digirisk_options', 'digi_ac_taskexport_field'), 'digirisk_options_correctivaction', 'digi_options_ac');
			add_settings_field('digi_ac_allowed_ext', __('Liste des extensions autoris&eacute;es pour les documents', 'evarisk'), array('digirisk_options', 'digi_ac_allowed_ext'), 'digirisk_options_correctivaction', 'digi_options_ac');
			add_settings_field('digi_ac_allow_front_ask', __('Autoriser la demande d\'actions correctives depuis la partie front du portail', 'evarisk'), array('digirisk_options', 'digi_ac_allow_front_ask'), 'digirisk_options_correctivaction', 'digi_options_ac');
			add_settings_field('digi_ac_front_ask_parent_task_id', '', array('digirisk_options', 'digi_ac_front_ask_parent_task_id'), 'digirisk_options_correctivaction', 'digi_options_ac');
		}

		{/*	Declare the different options for the risks	*/
			add_settings_section('digi_risk_options', null, array('digirisk_options', 'options_output_risk'), 'digirisk_options_risk');
			/*	Add the different field for current section	*/
			add_settings_field('digi_risk_advancedrisk_field', __('Activer l\'&eacute;valuation des risques avanc&eacute;e', 'evarisk'), array('digirisk_options', 'digi_risk_advancedrisk_field'), 'digirisk_options_risk', 'digi_risk_options');
		}

		{/*	Declare the different options for the work unit sheet	*/
			add_settings_section('digi_fp_options', null, array('digirisk_options', 'options_output_fp'), 'digirisk_options_worksheet');
			/*	Add the different field for current section	*/
			add_settings_field('digi_fp_picsize_field', __('Taille de la photo dans la fiche de poste (cm)', 'evarisk'), array('digirisk_options', 'digi_fp_picsize_field'), 'digirisk_options_worksheet', 'digi_fp_options');
		}

		{/*	Declare the different options for the recommandation	*/
			add_settings_section('digi_recommandation_options', null, array('digirisk_options', 'options_output_recommandation'), 'digirisk_options_recommandation');
			/*	Add the different field for current section	*/
			add_settings_field('digi_recommandation_efficiency_field', __('Activer l\'efficacit&eacute; des pr&eacute;conisations', 'evarisk'), array('digirisk_options', 'digi_recommandation_efficiency_field'), 'digirisk_options_recommandation', 'digi_recommandation_options');
		}

		{/*	Declare the different options for the users	*/
			add_settings_section('digi_users_options', null, array('digirisk_options', 'options_output_users'), 'digirisk_options_user');
			/*	Add the different field for current section	*/
			add_settings_field('digi_users_emaildomain_field', __('Domaine par d&eacute;faut pour les e-mail utilisateurs (sans @)', 'evarisk'), array('digirisk_options', 'digi_users_emaildomain_field'), 'digirisk_options_user', 'digi_users_options');
			add_settings_field('digi_users_access_field', __('Permettre l\'acc&egrave;s &agrave; tous les utilisateurs : ', 'evarisk'), array('digirisk_options', 'digi_users_access_field'), 'digirisk_options_user', 'digi_users_options');
			// add_settings_field('digi_users_digirisk_extra_field', __('Champs suppl&eacute;mentaires pour le logiciel Digirisk', 'evarisk'), array('digirisk_options', 'digi_users_digirisk_extra_field'), 'digirisk_options_user', 'digi_users_options');
		}

		{/*	Declare the different options for the products if plugin exists and is active	*/
			if(is_plugin_active(DIGI_WPSHOP_PLUGIN_MAINFILE))
			{
				add_settings_section('digi_product_options', null, array('digirisk_options', 'options_output_products'), 'digirisk_options_product');
			/*	Add the different field for current section	*/
				add_settings_field('digi_product_categories_field', __('Cat&eacute;gorie(s) de produits &agrave; afficher pour affectation aux &eacute;l&eacute;ments', 'evarisk'), array('digirisk_options', 'digi_product_categories_field'), 'digirisk_options_product', 'digi_product_options');
				add_settings_field('digi_product_status_field', __('Statuts des produits affich&eacute;s', 'evarisk'), array('digirisk_options', 'digi_product_status_field'), 'digirisk_options_product', 'digi_product_options');
				add_settings_field('digi_product_uncategorized_field', __('Afficher les produits non affect&eacute;s aux cat&eacute;gories', 'evarisk'), array('digirisk_options', 'digi_product_uncategorized_field'), 'digirisk_options_product', 'digi_product_options');
			}
		}

		{/*	Declare the different options for tree management	*/
			add_settings_section('digi_tree_options', null, array('digirisk_options', 'options_output_tree'), 'digirisk_options_arbo');
			/*	Add the different field for current section	*/
			add_settings_field('digi_tree_recreation_dialog', __('Afficher la bo&icirc;te de dialogue lorsqu\'on tente de cr&eacute;er un &eacute;l&eacute;ment d&eacute;j&agrave; existant mais supprim&eacute;', 'evarisk'), array('digirisk_options', 'digi_tree_recreation_dialog'), 'digirisk_options_arbo', 'digi_tree_options');
			add_settings_field('digi_tree_recreation_default', __('Choix par d&eacute;fault lorsqu\'on tente de cr&eacute;er un &eacute;l&eacute;ment d&eacute;j&agrave; existant mais supprim&eacute;', 'evarisk'), array('digirisk_options', 'digi_tree_recreation_default'), 'digirisk_options_arbo', 'digi_tree_options');
			add_settings_field('digi_tree_element_identifier', __('Identifiants pour les diff&eacute;rents &eacute;l&eacute;ments dans les arbres', 'evarisk'), array('digirisk_options', 'digi_tree_element_identifier'), 'digirisk_options_arbo', 'digi_tree_options');
			// add_settings_field('digi_groupement_extra_field', __('Champs suppl&eacute;mentaires pour les groupements', 'evarisk'), array('digirisk_options', 'digi_groupement_extra_field'), 'digirisk_options_arbo', 'digi_tree_options');
			// add_settings_field('digi_workunit_extra_field', __('Champs suppl&eacute;mentaires pour les unit&eacute;s de travail', 'evarisk'), array('digirisk_options', 'digi_workunit_extra_field'), 'digirisk_options_arbo', 'digi_tree_options');
		}
	}

	/**
	*	Create the html ouput code for the options page
	*
	*	@return The html code to output for option page
	*/
	function optionMainPage()
	{
		echo EvaDisplayDesign::afficherDebutPage(__('Options du logiciel Digirisk', 'evarisk'), EVA_OPTIONS_ICON, __('options du logiciel', 'evarisk'), __('options du logiciel', 'evarisk'), TABLE_OPTION, false, '', false);
?>
<div id="digirisk_options_container" >
	<form action="options.php" method="post" id="option_form" >
	<div id="options_tabs" >
		<ul>
			<li><a href="#digirisk_options_general" title="optionsContent" id="tabOptions_General" ><?php _e('G&eacute;n&eacute;ral', 'evarisk'); ?></a></li>
			<li><a href="#digirisk_options_user" title="optionsContent" id="tabOptions_User" ><?php _e('Utilisateurs', 'evarisk'); ?></a></li>
			<li><a href="#digirisk_options_arbo" title="optionsContent" id="tabOptions_Arbo" ><?php _e('Arborescence', 'evarisk'); ?></a></li>
			<li><a href="#digirisk_options_risk" title="optionsContent" id="tabOptions_Risk" ><?php _e('Risques', 'evarisk'); ?></a></li>
			<li><a href="#digirisk_options_worksheet" title="optionsContent" id="tabOptions_WorkSheet" ><?php _e('Fiches de postes', 'evarisk'); ?></a></li>
			<li><a href="#digirisk_options_correctivaction" title="optionsContent" id="tabOptions_CActions" ><?php _e('Actions correctives', 'evarisk'); ?></a></li>
			<li><a href="#digirisk_options_recommandation" title="optionsContent" id="tabOptions_Recommandation" ><?php _e('Pr&eacute;conisations', 'evarisk'); ?></a></li>
<?php
if(is_plugin_active(DIGI_WPSHOP_PLUGIN_MAINFILE)):
?>
			<li><a href="#digirisk_options_product" title="optionsContent" id="tabOptions_Product" ><?php _e('Produits', 'evarisk'); ?></a></li>
<?php
endif;
?>
		</ul>
		<div id="digirisk_options_general" ><?php do_settings_sections('digirisk_options_general'); ?></div>
		<div id="digirisk_options_user" ><?php do_settings_sections('digirisk_options_user'); ?></div>
		<div id="digirisk_options_arbo" ><?php do_settings_sections('digirisk_options_arbo'); ?></div>
		<div id="digirisk_options_risk" ><?php do_settings_sections('digirisk_options_risk'); ?></div>
		<div id="digirisk_options_worksheet" ><?php do_settings_sections('digirisk_options_worksheet'); ?></div>
		<div id="digirisk_options_correctivaction" ><?php do_settings_sections('digirisk_options_correctivaction'); ?></div>
		<div id="digirisk_options_recommandation" ><?php do_settings_sections('digirisk_options_recommandation'); ?></div>
<?php
if(is_plugin_active(DIGI_WPSHOP_PLUGIN_MAINFILE)):
?>
		<div id="digirisk_options_product" ><?php do_settings_sections('digirisk_options_product'); ?></div>
<?php
endif;
?>
	</div>
<?php
		settings_fields('digirisk_options');
if(current_user_can('digi_edit_option'))
{
?>
	<input class="button-primary alignright" name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
<?php
}
?>
	</form>
</div>
<script type="text/javascript" >
	evarisk(document).ready(function(){
		jQuery("#options_tabs").tabs();
		jQuery("#options_tabs ul li a").click(function(){
			jQuery("#option_form").attr("action", "options.php" + jQuery(this).attr("href"));
		});

		/*	Add support for option allowed_extension for AC deletion	*/
		jQuery(".delete_allowed_extension_for_ac_docs").live('click', function(){
			jQuery(this).closest("div").remove();
		});
		/*	Add support for option allowed_extension for AC addition	*/
		jQuery(".add_new_extension_to_allow").click(function(){
			if(jQuery("#new_allowed_extension").val() != ""){
				jQuery("#allowed_ext_list").append("<div><input type='text' value='" + jQuery("#new_allowed_extension").val() + "' name='digirisk_options[digi_ac_allowed_ext][]' /><img src='<?php echo EVA_IMG_ICONES_PLUGIN_URL; ?>delete_vs.png' alt='<?php _e('Supprimer cette extension de la liste', 'evarisk'); ?>' title='<?php _e('Supprimer cette extension de la liste', 'evarisk'); ?>' class='delete_allowed_extension_for_ac_docs' /></div>");
				jQuery("#new_allowed_extension").val("");
			}
			else{
				alert(convertAccentToJS("<?php _e('Vous n\'avez pas entr&eacute; d\'extension', 'evarisk'); ?>"));
			}
		});
	});
</script>
<?php
		echo EvaDisplayDesign::afficherFinPage();
	}

	/**
	*	Validate the different data sent for the option
	*
	*	@param array $input An array which will receive the values sent by the user with the form
	*
	*	@return array $newinput An array with the send values cleaned for more secure usage
	*/
	function digirisk_tree_options_validator($input)
	{
		$newinput['digi_tree_recreation_dialog'] = $input['digi_tree_recreation_dialog'];
		$newinput['digi_tree_recreation_default'] = $input['digi_tree_recreation_default'];
		$newinput['digi_tree_element_identifier'] = serialize($input['digi_tree_element_identifier']);
		$newinput['digi_groupement_extra_field'] = serialize($input['digi_groupement_extra_field']);
		$newinput['digi_workunit_extra_field'] = serialize($input['digi_workunit_extra_field']);

		return $newinput;
	}

	/**
	*	Validate the different data sent for the option
	*
	*	@param array $input An array which will receive the values sent by the user with the form
	*
	*	@return array $newinput An array with the send values cleaned for more secure usage
	*/
	function digirisk_product_options_validator($input)
	{

		$newinput['product_categories'] = serialize($input['product_categories']);
		$newinput['product_status'] = serialize($input['product_status']);
		$newinput['digi_product_uncategorized_field'] = $input['digi_product_uncategorized_field'];

		return $newinput;
	}

	/**
	*	Validate the different data sent for the option
	*
	*	@param array $input An array which will receive the values sent by the user with the form
	*
	*	@return array $newinput An array with the send values cleaned for more secure usage
	*/
	function digirisk_options_validator($input){
		global $wpdb;
		$newinput['digi_activ_trash'] = trim($input['digi_activ_trash']);

		$newinput['responsable_Tache_Obligatoire'] = trim($input['responsable_Tache_Obligatoire']);
		$newinput['responsable_Action_Obligatoire'] = trim($input['responsable_Action_Obligatoire']);
		$newinput['possibilite_Modifier_Tache_Soldee'] = trim($input['possibilite_Modifier_Tache_Soldee']);
		$newinput['possibilite_Modifier_Action_Soldee'] = trim($input['possibilite_Modifier_Action_Soldee']);
		$newinput['avertir_Solde_Action_Non_100'] = trim($input['avertir_Solde_Action_Non_100']);
		$newinput['avertir_Solde_Tache_Ayant_Action_Non_100'] = trim($input['avertir_Solde_Tache_Ayant_Action_Non_100']);
		$newinput['affecter_uniquement_tache_soldee_a_un_risque'] = trim($input['affecter_uniquement_tache_soldee_a_un_risque']);
		$newinput['action_correctives_avancees'] = trim($input['action_correctives_avancees']);
		$newinput['export_only_priority_task'] = trim($input['export_only_priority_task']);
		$newinput['export_tasks'] = trim($input['export_tasks']);
		$newinput['digi_ac_allowed_ext'] = $input['digi_ac_allowed_ext'];
		$newinput['digi_ac_allow_front_ask'] = $input['digi_ac_allow_front_ask'];
		$newinput['digi_ac_front_ask_parent_task_id'] = $input['digi_ac_front_ask_parent_task_id'];

		$newinput['risques_avances'] = trim($input['risques_avances']);

		$newinput['taille_photo_poste_fiche_de_poste'] = trim($input['taille_photo_poste_fiche_de_poste']);

		$newinput['recommandation_efficiency_activ'] = trim($input['recommandation_efficiency_activ']);

		$newinput['emailDomain'] = trim(str_replace('@', '', $input['emailDomain']));
		$newinput['digi_users_access_field'] = trim($input['digi_users_access_field']);
		$newinput['identifiant_htpasswd'] = trim($input['identifiant_htpasswd']);
		$newinput['password_htpasswd'] = trim($input['password_htpasswd']);
		
		digirisk_options::create_files($newinput['digi_users_access_field'],$newinput['identifiant_htpasswd'],$newinput['password_htpasswd']);
		
		$newinput['digi_users_digirisk_extra_field'] = serialize($input['digi_users_digirisk_extra_field']);

		return $newinput;
	}

	/**
	*	Function allowing to set a explication area for the settings section
	*/
	function main_options_output()
	{
		
	}
	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_activ_trash()
	{
		global $optionYesNoList;
		$options = get_option('digirisk_options');
		if(current_user_can('digi_edit_option'))
		{
			echo EvaDisplayInput::createComboBox('digi_activ_trash', 'digirisk_options[digi_activ_trash]', $optionYesNoList, $options['digi_activ_trash']);
		}
		else
		{
			echo $options['digi_activ_trash'];
		}
	}	

	/**
	*	Function allowing to set a explication area for the settings section
	*/
	function options_output_ac()
	{
		
	}
	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_ac_supervisormandatory_field()
	{
		global $optionYesNoList;
		$options = get_option('digirisk_options');
		if(current_user_can('digi_edit_option'))
		{
			echo EvaDisplayInput::createComboBox('digi_ac_supervisormandatory_field', 'digirisk_options[responsable_Tache_Obligatoire]', $optionYesNoList, $options['responsable_Tache_Obligatoire']);
		}
		else
		{
			echo $options['responsable_Tache_Obligatoire'];
		}
	}
	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_ac_subsupervisormandatory_field()
	{
		global $optionYesNoList;
		$options = get_option('digirisk_options');
		if(current_user_can('digi_edit_option'))
		{
			echo EvaDisplayInput::createComboBox('digi_ac_subsupervisormandatory_field', 'digirisk_options[responsable_Action_Obligatoire]', $optionYesNoList, $options['responsable_Action_Obligatoire']);
		}
		else
		{
			echo $options['responsable_Action_Obligatoire'];
		}
	}
	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_ac_changesold_field()
	{
		global $optionYesNoList;
		$options = get_option('digirisk_options');
		if(current_user_can('digi_edit_option'))
		{
			echo EvaDisplayInput::createComboBox('digi_ac_changesold_field', 'digirisk_options[possibilite_Modifier_Tache_Soldee]', $optionYesNoList, $options['possibilite_Modifier_Tache_Soldee']);
		}
		else
		{
			echo $options['possibilite_Modifier_Tache_Soldee'];
		}
	}
	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_ac_changesubsold_field()
	{
		global $optionYesNoList;
		$options = get_option('digirisk_options');
		if(current_user_can('digi_edit_option'))
		{
			echo EvaDisplayInput::createComboBox('digi_ac_changesubsold_field', 'digirisk_options[possibilite_Modifier_Action_Soldee]', $optionYesNoList, $options['possibilite_Modifier_Action_Soldee']);
		}
		else
		{
			echo $options['possibilite_Modifier_Action_Soldee'];
		}
	}
	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_ac_alertsoldnot100_field()
	{
		global $optionYesNoList;
		$options = get_option('digirisk_options');
		if(current_user_can('digi_edit_option'))
		{
			echo EvaDisplayInput::createComboBox('digi_ac_alertsoldnot100_field', 'digirisk_options[avertir_Solde_Action_Non_100]', $optionYesNoList, $options['avertir_Solde_Action_Non_100']);
		}
		else
		{
			echo $options['avertir_Solde_Action_Non_100'];
		}
	}
	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_ac_alertundersoldnot100_field()
	{
		global $optionYesNoList;
		$options = get_option('digirisk_options');
		if(current_user_can('digi_edit_option'))
		{
			echo EvaDisplayInput::createComboBox('digi_ac_alertundersoldnot100_field', 'digirisk_options[avertir_Solde_Tache_Ayant_Action_Non_100]', $optionYesNoList, $options['avertir_Solde_Tache_Ayant_Action_Non_100']);
		}
		else
		{
			echo $options['avertir_Solde_Tache_Ayant_Action_Non_100'];
		}
	}
	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_ac_displayonlysoldtaskinrisk_field()
	{
		global $optionYesNoList;
		$options = get_option('digirisk_options');
		if(current_user_can('digi_edit_option'))
		{
			echo EvaDisplayInput::createComboBox('digi_ac_displayonlysoldtaskinrisk_field', 'digirisk_options[affecter_uniquement_tache_soldee_a_un_risque]', $optionYesNoList, $options['affecter_uniquement_tache_soldee_a_un_risque']);
		}
		else
		{
			echo $options['affecter_uniquement_tache_soldee_a_un_risque'];
		}
	}
	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_ac_advancedCA_field()
	{
		global $optionYesNoList;
		$options = get_option('digirisk_options');
		if(current_user_can('digi_edit_option'))
		{
			echo EvaDisplayInput::createComboBox('digi_ac_advancedCA_field', 'digirisk_options[action_correctives_avancees]', $optionYesNoList, $options['action_correctives_avancees']);
		}
		else
		{
			echo $options['action_correctives_avancees'];
		}
	}
	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_ac_exportprioritytaskonly_field()
	{
		global $optionYesNoList;
		$options = get_option('digirisk_options');
		if(current_user_can('digi_edit_option'))
		{
			echo EvaDisplayInput::createComboBox('digi_ac_exportprioritytaskonly_field', 'digirisk_options[export_only_priority_task]', $optionYesNoList, $options['export_only_priority_task']);
		}
		else
		{
			echo $options['export_only_priority_task'];
		}
	}
	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_ac_taskexport_field()
	{
		global $optionYesNoList;
		$options = get_option('digirisk_options');
		if(current_user_can('digi_edit_option'))
		{
			echo EvaDisplayInput::createComboBox('digi_ac_taskexport_field', 'digirisk_options[export_tasks]', $optionYesNoList, $options['export_tasks']);
		}
		else
		{
			echo $options['export_tasks'];
		}
	}	

	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_ac_front_ask_parent_task_id(){
	
	}
	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_ac_allow_front_ask(){
		global $optionYesNoList;
		$options = get_option('digirisk_options');
		$load_class = ' class="digirisk_hide" ';
		if($options['digi_ac_allow_front_ask'] == 'oui'){
			$load_class = ' ';
		}
		if(current_user_can('digi_edit_option')){
			echo EvaDisplayInput::createComboBox('digi_ac_allow_front_ask', 'digirisk_options[digi_ac_allow_front_ask]', $optionYesNoList, $options['digi_ac_allow_front_ask']) . '<div id="associated_task_container" ' . $load_class . ' >&nbsp;&nbsp;&nbsp;' . __('Code &agrave; ins&eacute;rer dans votre page', 'evarisk') . '&nbsp;:[digirisk_correctiv_action]<br/>' . __('Identifiant de la t&acirc;che a associer', 'evarisk') . '&nbsp;:&nbsp;<input type="text" value="' . $options['digi_ac_front_ask_parent_task_id'] . '" name="digirisk_options[digi_ac_front_ask_parent_task_id]" id="digi_ac_front_ask_parent_task_id" /></div>
<script type="text/javascript" >
	evarisk(document).ready(function(){
		jQuery("#digi_ac_allow_front_ask").change(function(){
			if(jQuery(this).val() == "oui"){
				jQuery("#associated_task_container").show();
			}
			else{
				jQuery("#associated_task_container").hide();
			}
		});	
	});
</script>';
		}
		else{
			echo $options['digi_ac_allow_front_ask'] . '<div id="associated_task_container" ' . $load_class . ' >' . __('Identifiant de la t&acirc;che a associer', 'evarisk') . '&nbsp;:&nbsp;<input type="hidden" value="' . $options['digi_ac_front_ask_parent_task_id'] . '" name="digirisk_options[digi_ac_front_ask_parent_task_id]" id="digi_ac_front_ask_parent_task_id" /></div>';
		}
	}
	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_ac_allowed_ext(){
		$digi_ac_allowed_ext_out = '';
		$digi_ac_allowed_ext = self::getOptionValue('digi_ac_allowed_ext', 'digirisk_options');

		if(is_array($digi_ac_allowed_ext)){
			foreach($digi_ac_allowed_ext as $index => $extension){
				if(current_user_can('digi_edit_option') && ($extension != '')){
					$digi_ac_allowed_ext_out .= '<div id="digi_allowed_AC_ext_' . $index . '" ><input type="text" value="' . $extension . '" name="digirisk_options[digi_ac_allowed_ext][]" /><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'delete_vs.png" alt="' . __('Ne plus autoriser cette extension', 'eobackup') . '" title="' . __('Ne plus autoriser cette extension', 'eobackup') . '" class="delete_allowed_extension_for_ac_docs" /></div>';
				}
				else{
					$digi_ac_allowed_ext_out .= $extension . '<br/>';
				}
			}
		}

		$add_extension = '';
		if(current_user_can('digi_edit_option')){
			$add_extension = '<input type="text" value="" name="new_allowed_extension" id="new_allowed_extension" /><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'add_vs.png" alt="' . __('Ajouter cette extension dans la liste des extensions autoris&eacute;es', 'eobackup') . '" title="' . __('Ajouter cette extension dans la liste des extensions autoris&eacute;es', 'eobackup') . '" class="add_new_extension_to_allow" />';
		}

		$digi_ac_allowed_ext_out = $add_extension . '
<fieldset class="digi_allowed_extension_for_AC_fieldset" >
	<legend>' . __('Liste des extensions autoris&eacute;es pour les documents associ&eacute;s aux t&acirc;ches et sous-t&acirc;ches', 'evarisk') . '</legend>
	<div id="allowed_ext_list" >' . $digi_ac_allowed_ext_out . '</div>
</fieldset>';

		echo $digi_ac_allowed_ext_out;
	}

	/**
	*	Function allowing to set a explication area for the settings section
	*/
	function options_output_risk()
	{
		
	}
	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_risk_advancedrisk_field()
	{
		global $optionYesNoList;
		$options = get_option('digirisk_options');
		if(current_user_can('digi_edit_option'))
		{
			echo EvaDisplayInput::createComboBox('digi_risk_advancedrisk_field', 'digirisk_options[risques_avances]', $optionYesNoList, $options['risques_avances']);
		}
		else
		{
			echo $options['risques_avances'];
		}
	}

	/**
	*	Function allowing to set a explication area for the settings section
	*/
	function options_output_fp()
	{
		
	}
	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_fp_picsize_field()
	{
		$options = get_option('digirisk_options');
		if(current_user_can('digi_edit_option'))
		{
			echo "<input id='taille_photo_poste_fiche_de_poste' name='digirisk_options[taille_photo_poste_fiche_de_poste]' size='40' type='text' value='{$options['taille_photo_poste_fiche_de_poste']}' />";
		}
		else
		{
			echo $options['taille_photo_poste_fiche_de_poste'];
		}
	}

	/**
	*	Function allowing to set a explication area for the settings section
	*/
	function options_output_recommandation()
	{
		
	}
	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_recommandation_efficiency_field()
	{
		global $optionYesNoList;
		$options = get_option('digirisk_options');
		if(current_user_can('digi_edit_option'))
		{
			echo EvaDisplayInput::createComboBox('digi_recommandation_efficiency_field', 'digirisk_options[recommandation_efficiency_activ]', $optionYesNoList, $options['recommandation_efficiency_activ']);
		}
		else
		{
			echo $options['recommandation_efficiency_activ'];
		}
	}

	/**
	*	Function allowing to set a explication area for the settings section
	*/
	function options_output_users()
	{
		
	}
	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_users_emaildomain_field(){
		$options = get_option('digirisk_options');
		if(current_user_can('digi_edit_option')){
			echo "<input id='emailDomain' name='digirisk_options[emailDomain]' size='40' type='text' value='{$options['emailDomain']}' />";
		}
		else{
			echo $options['emailDomain'];
		}
	}
	/**
	*
	*/
	function digi_users_access_field(){
		global $optionYesNoList;
		$options = get_option('digirisk_options');
		$digi_users_access_field = '';
		if(current_user_can('digi_edit_option')){
			$digi_users_access_field = "
<input id='last_value_of_user_access' name='last_value_of_user_access' size='20' type='text' value='{$options['digi_users_access_field']}' />
	" . EvaDisplayInput::createComboBox('digi_users_access_field', 'digirisk_options[digi_users_access_field]', $optionYesNoList, $options['digi_users_access_field']) . "
	<br/><label for='identifiant_htpasswd'>" . __('Idenfiant', 'evarisk') . "&nbsp;:</label><input id='identifiant_htpasswd' name='digirisk_options[identifiant_htpasswd]' type='text' value='{$options['identifiant_htpasswd']}' />
	<br/><label for='password_htpasswd'>" . __('Mot de passe', 'evarisk') . "&nbsp;:</label><input id='password_htpasswd' name='digirisk_options[password_htpasswd]' type='text' value='{$options['password_htpasswd']}' />";
			echo $digi_users_access_field;
		}
		else{
			echo $options['digi_users_access_field'];
		}
	}	
	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_users_digirisk_extra_field()
	{
		$userField = '';
		global $userWorkAccidentMandatoryFields;
		$options = get_option('digirisk_options');
		// $userField .= __('Liste des champs obligatoires pour les utilisateurs', 'evarisk') . '<div class="clear" >';
		// foreach($userWorkAccidentMandatoryFields as $accident_mandatory_fields){
			// $userField .= '&nbsp;-&nbsp;<span class="required" >' . __($accident_mandatory_fields, 'evarisk') . '</span>';
		// }
		// $userField .= '</div>';
		if(current_user_can('digi_edit_option'))
		{
			$user_extra_fields = unserialize($options['digi_users_digirisk_extra_field']);
			if(is_array($user_extra_fields) && (count($user_extra_fields) > 0)){
				$userField .= __('Liste des champs suppl&eacute;mentaires pour les utilisateurs', 'evarisk') . '
				<div class="clear user_extra_fields" >' . implode(', ', $user_extra_fields) . '</div>';
			}
			$userField .= '
				<div id="digi_user_extra_field_container" >
					<div id="digi_user_extra_field" class="digi_user_extra_field" >'
						. EvaDisplayInput::afficherInput('text', '', '', '', null, 'digirisk_options[digi_users_digirisk_extra_field][]', false, false, 61, '', '', '100%', '', 'left', true) . '
					</div>
					<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'add_vs.png" alt="' . __('Ajouter un champs', 'evarisk') . '" id="add_new_user_field" />
				</div>
				<div id="delete_digi_user_extra_field_container" ><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'delete_vs.png" alt="' . __('Supprimez ce champs', 'evarisk') . '" id="delete_selected_digi_user_extra_field" class="delete_selected_digi_user_extra_field" /></div>
				<div id="digi_user_extra_field_details" >&nbsp;</div>
				<script type="text/javascript" >
					evarisk(document).ready(function(){
						jQuery("#add_new_user_field").click(function(){
							lineNumber = jQuery("#digi_user_extra_field_details div.digi_user_extra_field").length;
							jQuery("#digi_user_extra_field_details").append(jQuery("#digi_user_extra_field_container").html());
							jQuery("#digi_user_extra_field_details #add_new_user_field").remove();
							jQuery("#digi_user_extra_field_details #digi_user_extra_field").attr("id", "user_field_" + lineNumber);
							jQuery("#digi_user_extra_field_details").append(jQuery("#delete_digi_user_extra_field_container").html());
							jQuery("#digi_user_extra_field_details #delete_selected_digi_user_extra_field").attr("onclick", "remove_current_user_field_line(" + lineNumber + ");");
							jQuery("#digi_user_extra_field_details #delete_selected_digi_user_extra_field").attr("id", "delete_selected_digi_user_extra_field_" + lineNumber);
						});
					});

					function remove_current_user_field_line(line_number){
						jQuery("#delete_selected_digi_user_extra_field_" + line_number).remove();
						jQuery("#user_field_" + line_number).remove();
					}
				</script>';
		}
		else
		{
			$userField .= $options['digi_users_digirisk_extra_field'];
		}

		echo $userField;
	}

	/**
	*	Function allowing to set a explication area for the settings section
	*/
	function options_output_products()
	{
		_e('D&eacute;finissez les options permettant de g&eacute;rer les produits provenant du plugin WP Shop pour les associer aux diff&eacute;rents &eacute;l&eacute;ments de Digirisk.', 'evarisk');
	}
	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_product_categories_field()
	{
		$tableContent = '<span class="evarisk_options_explanation" > ' . __('Vous pouvez choisir les cat&eacute;gories &agrave; afficher (si aucune n\'est s&eacute;lectionn&eacute;e, elles seront toutes affich&eacute;es)', 'evarisk') . '</span>' . digirisk_product_categories::options_category_tree_output(0);

		echo $tableContent;
	}
	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_product_status_field()
	{
		global $posts_status;

		if(is_array($posts_status) && (count($posts_status) > 0)){
			$tableContent = '<span class="evarisk_options_explanation" > ' . __('Vous pouvez d&eacute;finir le statut des produits que vous voulez afficher (si aucun n\'est s&eacute;lectionn&eacute; seul les produits avec le statut "publi&eacute;" seront affich&eacute;s dans Digirisk)', 'evarisk') . '</span><br/>';
			$options = get_option('digirisk_product_options');
			$choosenStatus = unserialize($options['product_status']);
			foreach($posts_status as $status){
				$checked = (is_array($choosenStatus) && in_array($status, $choosenStatus)) ? ' checked="checked" ' : '';
				$tableContent .= '
<ul class="digirisk_options_product_status_list" >
	<li><input ' . $checked . ' type="checkbox" name="digirisk_product_options[product_status][]" value="' . $status . '" id="wpshop_product_categories_' . $status . '" /><label for="wpshop_product_categories_' . $status . '" >' . __($status) . '</label>
	</li>
</ul>';
			}
		}
		else{
			$tableContent = '<span class="evarisk_options_explanation" > ' . __('Une erreur est survenue lors de la r&eacute;cup&eacute;ration des statuts des produits. Seuls les produits "publi&eacute;s" seront affich&eacute;s', 'evarisk') . '</span><br/>';
		}		

		echo $tableContent;
	}
	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_product_uncategorized_field()
	{
		global $optionYesNoList;
		$options = get_option('digirisk_product_options');
		if(current_user_can('digi_edit_option'))
		{
			echo EvaDisplayInput::createComboBox('digi_product_uncategorized_field', 'digirisk_product_options[digi_product_uncategorized_field]', $optionYesNoList, $options['digi_product_uncategorized_field']);
		}
		else
		{
			echo $options['digi_product_uncategorized_field'];
		}
	}

	/**
	*	Function allowing to set a explication area for the settings section
	*/
	function options_output_tree()
	{
		
	}
	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_tree_recreation_dialog()
	{
		global $optionYesNoList;
		$options = get_option('digirisk_tree_options');
		if(current_user_can('digi_edit_option'))
		{
			echo EvaDisplayInput::createComboBox('digi_tree_recreation_dialog', 'digirisk_tree_options[digi_tree_recreation_dialog]', $optionYesNoList, $options['digi_tree_recreation_dialog']);
		}
		else
		{
			echo $options['digi_tree_recreation_dialog'];
		}
	}
	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_tree_recreation_default()
	{
		global $optionExistingTreeElementList;
		$options = get_option('digirisk_tree_options');
		if(current_user_can('digi_edit_option'))
		{
			echo EvaDisplayInput::createComboBox('digi_tree_recreation_default', 'digirisk_tree_options[digi_tree_recreation_default]', $optionExistingTreeElementList, $options['digi_tree_recreation_default']);
		}
		else
		{
			echo $options['digi_tree_recreation_default'];
		}
	}
	/**
	*	Define the ouput for the different element identifier (one output per identifier)
	*/
	function digi_tree_element_identifier()
	{
		global $treeElementList;

		$options = get_option('digirisk_tree_options');
		$identifierList = unserialize($options['digi_tree_element_identifier']);
		$optionOutput = '
<table summary="element identifier definer" cellpadding="0" cellspacing="0" >';
		$i = 0;
		foreach($treeElementList as $elementName => $elementDefault){
			if($i == 0){
				$optionOutput .= '
	<tr>';
			}
			$optionValue = $elementDefault;
			if(isset($identifierList[$elementDefault]) && (trim($identifierList[$elementDefault]) != '')){
				$optionValue = $identifierList[$elementDefault];
			}
			$optionOutput .= '
		<td><label for="digi_tree_element_identifier' . $elementDefault . '" >' . $elementName . '</label></td>
		<td>';
			if(current_user_can('digi_edit_option'))
			{
				$optionOutput .= '<input type="text" size="3" name="digirisk_tree_options[digi_tree_element_identifier][' . $elementDefault . ']" value="' . $optionValue . '" id="digi_tree_element_identifier' . $elementDefault . '" />';
			}
			else
			{
				echo $optionValue;
			}
			$optionOutput .= '
		</td>';
			$i++;
			if($i >= 3){
				$optionOutput .= '
	</tr>';
				$i = 0;
			}
		}
		if(($i > 0) && ($i < 3)){
			$optionOutput .= '
	</tr>';
		}
		$optionOutput .= '</table>';

		echo $optionOutput;
	}
	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_groupement_extra_field()
	{
		$groupementField = '';
		global $userWorkAccidentMandatoryFields;
		$options = get_option('digirisk_tree_options');
		if(current_user_can('digi_edit_option'))
		{
			$user_extra_fields = unserialize($options['digi_groupement_extra_field']);
			if(is_array($user_extra_fields) && (count($user_extra_fields) > 0)){
				$groupementField .= __('Liste des champs suppl&eacute;mentaires pour les groupements', 'evarisk') . '
				<div class="clear user_extra_fields" >' . implode(', ', $user_extra_fields) . '</div>';
			}
			$groupementField .= '
				<div id="digi_gpt_extra_field_container" >
					<div id="digi_gpt_extra_field" class="digi_gpt_extra_field" >'
						. EvaDisplayInput::afficherInput('text', '', '', '', null, 'digirisk_tree_options[digi_groupement_extra_field][]', false, false, 61, '', '', '100%', '', 'left', true) . '
					</div>
					<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'add_vs.png" alt="' . __('Ajouter un champs', 'evarisk') . '" id="add_gpt_new_field" />
				</div>
				<div id="delete_digi_gpt_extra_field_container" class="hide" ><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'delete_vs.png" alt="' . __('Supprimez ce champs', 'evarisk') . '" id="delete_selected_digi_gpt_extra_field" class="delete_selected_digi_gpt_extra_field" /></div>
				<div id="digi_gpt_extra_field_details" >&nbsp;</div>
				<script type="text/javascript" >
					evarisk(document).ready(function(){
						jQuery("#add_gpt_new_field").click(function(){
							lineNumber = jQuery("#digi_gpt_extra_field_details div.digi_gpt_extra_field").length;
							jQuery("#digi_gpt_extra_field_details").append(jQuery("#digi_gpt_extra_field_container").html());
							jQuery("#digi_gpt_extra_field_details #add_gpt_new_field").remove();
							jQuery("#digi_gpt_extra_field_details #digi_gpt_extra_field").attr("id", "gpt_field_" + lineNumber);
							jQuery("#digi_gpt_extra_field_details").append(jQuery("#delete_digi_gpt_extra_field_container").html());
							jQuery("#digi_gpt_extra_field_details #delete_selected_digi_gpt_extra_field").attr("onclick", "remove_current_user_field_line(" + lineNumber + ");");
							jQuery("#digi_gpt_extra_field_details #delete_selected_digi_gpt_extra_field").attr("id", "delete_selected_digi_gpt_extra_field_" + lineNumber);
						});
					});

					function remove_current_user_field_line(line_number){
						jQuery("#delete_selected_digi_gpt_extra_field_" + line_number).remove();
						jQuery("#gpt_field_" + line_number).remove();
					}
				</script>';
		}
		else
		{
			$groupementField .= $options['digi_groupement_extra_field'];
		}

		echo $groupementField;
	}
	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_workunit_extra_field()
	{
		$groupementField = '';
		global $userWorkAccidentMandatoryFields;
		$options = get_option('digirisk_tree_options');
		if(current_user_can('digi_edit_option'))
		{
			$user_extra_fields = unserialize($options['digi_workunit_extra_field']);
			if(is_array($user_extra_fields) && (count($user_extra_fields) > 0)){
				$groupementField .= __('Liste des champs suppl&eacute;mentaires pour les unit&eacute;s de travail', 'evarisk') . '
				<div class="clear user_extra_fields" >' . implode(', ', $user_extra_fields) . '</div>';
			}
			$groupementField .= '
				<div id="digi_ut_extra_field_container" >
					<div id="digi_ut_extra_field" class="digi_ut_extra_field" >'
						. EvaDisplayInput::afficherInput('text', '', '', '', null, 'digirisk_tree_options[digi_workunit_extra_field][]', false, false, 61, '', '', '100%', '', 'left', true) . '
					</div>
					<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'add_vs.png" alt="' . __('Ajouter un champs', 'evarisk') . '" id="add_ut_new_field" />
				</div>
				<div id="delete_digi_ut_extra_field_container" class="hide" ><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'delete_vs.png" alt="' . __('Supprimez ce champs', 'evarisk') . '" id="delete_selected_digi_ut_extra_field" class="delete_selected_digi_ut_extra_field" /></div>
				<div id="digi_ut_extra_field_details" >&nbsp;</div>
				<script type="text/javascript" >
					evarisk(document).ready(function(){
						jQuery("#add_ut_new_field").click(function(){
							lineNumber = jQuery("#digi_ut_extra_field_details div.digi_ut_extra_field").length;
							jQuery("#digi_ut_extra_field_details").append(jQuery("#digi_ut_extra_field_container").html());
							jQuery("#digi_ut_extra_field_details #add_ut_new_field").remove();
							jQuery("#digi_ut_extra_field_details #digi_ut_extra_field").attr("id", "ut_field_" + lineNumber);
							jQuery("#digi_ut_extra_field_details").append(jQuery("#delete_digi_ut_extra_field_container").html());
							jQuery("#digi_ut_extra_field_details #delete_selected_digi_ut_extra_field").attr("onclick", "remove_current_ut_field_line(" + lineNumber + ");");
							jQuery("#digi_ut_extra_field_details #delete_selected_digi_ut_extra_field").attr("id", "delete_selected_digi_ut_extra_field_" + lineNumber);
						});
					});

					function remove_current_ut_field_line(line_number){
						jQuery("#delete_selected_digi_ut_extra_field_" + line_number).remove();
						jQuery("#ut_field_" + line_number).remove();
					}
				</script>';
		}
		else
		{
			$groupementField .= $options['digi_workunit_extra_field'];
		}

		echo $groupementField;
	}

	/**
	*	Return the option value from a given option name
	*
	*	@param string $optionName The option name of the option we want to get the value
	*
	*	@return mixed The option value
	*/
	function getOptionValue($optionName, $option = 'digirisk_options'){
		$digirisk_options = get_option($option);

		return $digirisk_options[$optionName];
	}
	/**
	*	Update the database option
	*
	* @param string $nom The sub option name we want to update
	* @param string $value the sub option value we want to put
	*
	*/
	function updateDigiOption($nom, $value)
	{
		$option = get_option('digirisk_options');

		if(is_array($option))
		{
			$option[$nom] = $value;
			update_option('digirisk_options', $option);
		}
		elseif(is_string($option))
		{
			$optionValue = unserialize($optionValue);
			$optionSubValue = $optionValue[$subOptionName];
			update_option('digirisk_options', serialize($optionValue));
		}
	}

	/**
	*	Return the current database version for the plugin
	*
	*	@param string $subOptionName The option we want to get the value for
	*
	*	@return mixed $optionSubValue The value of the option
	*/
	function getDbOption($subOptionName)
	{
		$optionSubValue = -1;

		/*	Get the db option 	*/
		$optionValue = get_option('digirisk_db_option');
		if($optionValue != '')
		{
			if(is_array($optionValue))
			{
				$optionSubValue = $optionValue[$subOptionName];
			}
			elseif(is_string($optionValue))
			{
				$optionValue = unserialize($optionValue);
				$optionSubValue = $optionValue[$subOptionName];
			}
		}
		/*	Keep the old method to get plugin version because of update	*/
		if($optionSubValue == -1)
		{
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			global $wpdb;
			$subOptionName = eva_tools::IsValid_Variable($subOptionName);
			if( $wpdb->get_var("show tables like '" . TABLE_VERSION . "'") == TABLE_VERSION)
			{
				$query = $wpdb->prepare("SELECT version version
					FROM " . TABLE_VERSION . "
					WHERE nom = %s", $subOptionName);
				$resultat = $wpdb->get_row($query);
				$optionSubValue = $resultat->version;
			}
		}

		return $optionSubValue;
	}
	/**
	*	Update the database option
	*
	* @param string $nom The sub option name we want to update
	* @param string $value the sub option value we want to put
	*
	*/
	function updateDbOption($nom, $value)
	{
		$option = get_option('digirisk_db_option');
		if(is_array($option))
		{
			$option[$nom] = $value;
			update_option('digirisk_db_option', $option);
		}
		elseif(is_string($option))
		{
			$optionValue = unserialize($optionValue);
			$optionSubValue = $optionValue[$subOptionName];
			update_option('digirisk_db_option', serialize($optionValue));
		}
	}
	
	function create_files($allow_access, $id, $psw){
		$dir_psswd = ABSPATH . 'digi_access/';
		$urlPasswd = ".htpasswd"; //chemin du fichier password
		$urlAccess = ABSPATH . ".htaccess"; //chemin du fichier access
		$urlAccessOri = ABSPATH . ".htaccess_original"; //chemin du fichier access

		if($allow_access == 'oui'){
			if(is_file($urlAccess) && is_file($urlAccessOri)){
				unlink($urlAccess);
				copy($urlAccessOri, $urlAccess);
				unlink($urlAccessOri);
			}
			if(is_file($dir_psswd . $urlPasswd)){
				unlink($dir_psswd . $urlPasswd);
				unlink($urlAccess);
			}
		}
		elseif(($id != '') && ($psw != '')){
			$original_file_content = '';
			if(is_file($urlAccessOri)){
				unlink($urlAccessOri);
			}
			if(is_file($urlAccess) && ($_REQUEST['last_value_of_user_access'] != $allow_access)){
				copy($urlAccess, $urlAccessOri);
				/*	Read the old file	for adding content to new htaccess file	*/
				$original_file_content = file($urlAccessOri);
			}
			if(!is_dir($dir_psswd)){
				mkdir($dir_psswd, 0755, true);
				exec('chmod -R 755 ' . $dir_psswd);
			}

			
			$new_htaccess_file_content = fopen($urlAccess, "w");
			$new_htaccess_file_content_lines = 'AuthName "' . html_entity_decode(__('Acc&egrave;s prot&eacute;g&eacute;', 'evarisk')) . '"
AuthType Basic
AuthUserFile "'.$dir_psswd . $urlPasswd.'"
Require valid-user';
			/*	Add orignal file content to the new file	*/
			if($original_file_content != ''){
				$new_htaccess_file_content_lines .= "

";
				foreach($original_file_content as $line){
					$new_htaccess_file_content_lines .= $line;
				}
			}
			fwrite($new_htaccess_file_content, $new_htaccess_file_content_lines);
			Fclose($new_htaccess_file_content);

			$htpasswd_file_content = fopen($dir_psswd . $urlPasswd,"w");
			$password_to_write = $psw;
			if(long2ip(ip2long($_SERVER['REMOTE_ADDR'])) != '127.0.0.1'){
				$password_to_write = crypt($psw);
			}
			$htpasswd_file_content_lines = "$id:" . $password_to_write; //identifiants a r�cup�rer par formulaire
			Fwrite($htpasswd_file_content, $htpasswd_file_content_lines);
			Fclose($htpasswd_file_content);
		}
	}

}