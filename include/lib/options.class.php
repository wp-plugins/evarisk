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
	function evarisk_add_options() 
	{
		register_setting('digirisk_options', 'digirisk_options', array('digirisk_options', 'digirisk_options_validator'));
		register_setting('digirisk_db_option', 'digirisk_db_option');

		{/* Declare the different options for the correctiv actions	*/
			add_settings_section('digi_options_ac', __('Options pour les actions correctives', 'evarisk'), array('digirisk_options', 'options_output_ac'), 'digirisk_options_settings');
			/*	Add the different field for the correctives actions	*/
			add_settings_field('digi_ac_supervisormandatory_field', __('Responsable des t&acirc;ches obligatoire', 'evarisk'), array('digirisk_options', 'digi_ac_supervisormandatory_field'), 'digirisk_options_settings', 'digi_options_ac');
			add_settings_field('digi_ac_subsupervisormandatory_field', __('Responsable des sous-t&acirc;ches obligatoire', 'evarisk'), array('digirisk_options', 'digi_ac_subsupervisormandatory_field'), 'digirisk_options_settings', 'digi_options_ac');
			add_settings_field('digi_ac_changesold_field', __('Possibilit&eacute; de modifier une t&acirc;che sold&eacute;e', 'evarisk'), array('digirisk_options', 'digi_ac_changesold_field'), 'digirisk_options_settings', 'digi_options_ac');
			add_settings_field('digi_ac_changesubsold_field', __('Possibilit&eacute; de modifier une sous-t&acirc;che sold&eacute;e', 'evarisk'), array('digirisk_options', 'digi_ac_changesubsold_field'), 'digirisk_options_settings', 'digi_options_ac');
			add_settings_field('digi_ac_alertsoldnot100_field', __('Avertir lorsqu\'on tente de solder une t&acirc;che qui n\'a pas atteint les 100%', 'evarisk'), array('digirisk_options', 'digi_ac_alertsoldnot100_field'), 'digirisk_options_settings', 'digi_options_ac');
			add_settings_field('digi_ac_alertundersoldnot100_field', __('Avertir lorsqu\'on tente de solder une t&acirc;che ayant des sous-t&acirc;ches qui n\'ont pas atteint les 100%', 'evarisk'), array('digirisk_options', 'digi_ac_alertundersoldnot100_field'), 'digirisk_options_settings', 'digi_options_ac');
			add_settings_field('digi_ac_displayonlysoldtaskinrisk_field', __('Affecter uniquement les t&acirc;ches sold&eacute;es aux risques', 'evarisk'), array('digirisk_options', 'digi_ac_displayonlysoldtaskinrisk_field'), 'digirisk_options_settings', 'digi_options_ac');
			add_settings_field('digi_ac_advancedCA_field', __('Activer les actions correctives avanc&eacute;es', 'evarisk'), array('digirisk_options', 'digi_ac_advancedCA_field'), 'digirisk_options_settings', 'digi_options_ac');
			add_settings_field('digi_ac_createUtaks4PA_field', __('Cr&eacute;er une sous-t&acirc;che pour les actions prioritaires', 'evarisk'), array('digirisk_options', 'digi_ac_createUtaks4PA_field'), 'digirisk_options_settings', 'digi_options_ac');
			add_settings_field('digi_ac_exportprioritytaskonly_field', __('Exporter uniquement les t&acirc;ches prioritaires', 'evarisk'), array('digirisk_options', 'digi_ac_exportprioritytaskonly_field'), 'digirisk_options_settings', 'digi_options_ac');
			add_settings_field('digi_ac_taskexport_field', __('Afficher le bouton d\'export des actions correctives au format texte', 'evarisk'), array('digirisk_options', 'digi_ac_taskexport_field'), 'digirisk_options_settings', 'digi_options_ac');
		}

		{/*	Declare the different options for the risks	*/
			add_settings_section('digi_risk_options', __('Options pour les risques', 'evarisk'), array('digirisk_options', 'options_output_risk'), 'digirisk_options_settings');
			/*	Add the different field for the correctives actions	*/
			add_settings_field('digi_risk_advancedrisk_field', __('Activer l\'&eacute;valuation des risques avanc&eacute;e', 'evarisk'), array('digirisk_options', 'digi_risk_advancedrisk_field'), 'digirisk_options_settings', 'digi_risk_options');
		}

		{/*	Declare the different options for the work unit sheet	*/
			add_settings_section('digi_fp_options', __('Options pour les fiches de poste', 'evarisk'), array('digirisk_options', 'options_output_fp'), 'digirisk_options_settings');
			/*	Add the different field for the correctives actions	*/
			add_settings_field('digi_fp_picsize_field', __('Taille de la photo dans la fiche de poste (cm)', 'evarisk'), array('digirisk_options', 'digi_fp_picsize_field'), 'digirisk_options_settings', 'digi_fp_options');
		}

		{/*	Declare the different options for the recommandation	*/
			add_settings_section('digi_recommandation_options', __('Options pour les pr&eacute;conisations', 'evarisk'), array('digirisk_options', 'options_output_recommandation'), 'digirisk_options_settings');
			/*	Add the different field for the correctives actions	*/
			add_settings_field('digi_recommandation_efficiency_field', __('Activer l\'efficacit&eacute; des pr&eacute;conisations', 'evarisk'), array('digirisk_options', 'digi_recommandation_efficiency_field'), 'digirisk_options_settings', 'digi_recommandation_options');
		}

		{/*	Declare the different options for the users	*/
			add_settings_section('digi_users_options', __('Options pour les utilisateurs', 'evarisk'), array('digirisk_options', 'options_output_users'), 'digirisk_options_settings');
			/*	Add the different field for the correctives actions	*/
			add_settings_field('digi_recommandation_efficiency_field', __('Domaine par d&eacute;faut pour les e-mail utilisateurs (sans @)', 'evarisk'), array('digirisk_options', 'digi_users_emaildomain_field'), 'digirisk_options_settings', 'digi_users_options');
		}

		{/*	Declare the different options for the products if plugin exists and is active	*/
			if (is_plugin_active('wpshop/wp-shop.php'))
			{
				add_settings_section('digi_product_options', __('Options pour les produits', 'evarisk'), array('digirisk_options', 'options_output_products'), 'digirisk_options_settings');
			/*	Add the different field for the correctives actions	*/
				add_settings_field('digi_product_categories_field', __('Cat&eacute;gorie(s) de produits &agrave; afficher pour affectation aux &eacute;l&eacute;ments', 'evarisk'), array('digirisk_options', 'digi_product_categories_field'), 'digirisk_options_settings', 'digi_product_options');
			}
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
	<form action="options.php" method="post">

	<?php settings_fields('digirisk_options'); ?>
	<?php do_settings_sections('digirisk_options_settings'); ?>

	<br/><br/>
<?php
if(current_user_can('digi_edit_option'))
{
?>
	<input class="button-primary" name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
<?php
}
?>
	</form>
</div>
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
	function digirisk_options_validator($input)
	{
		$newinput['responsable_Tache_Obligatoire'] = trim($input['responsable_Tache_Obligatoire']);
		$newinput['responsable_Action_Obligatoire'] = trim($input['responsable_Action_Obligatoire']);
		$newinput['possibilite_Modifier_Tache_Soldee'] = trim($input['possibilite_Modifier_Tache_Soldee']);
		$newinput['possibilite_Modifier_Action_Soldee'] = trim($input['possibilite_Modifier_Action_Soldee']);
		$newinput['avertir_Solde_Action_Non_100'] = trim($input['avertir_Solde_Action_Non_100']);
		$newinput['avertir_Solde_Tache_Ayant_Action_Non_100'] = trim($input['avertir_Solde_Tache_Ayant_Action_Non_100']);
		$newinput['affecter_uniquement_tache_soldee_a_un_risque'] = trim($input['affecter_uniquement_tache_soldee_a_un_risque']);
		$newinput['action_correctives_avancees'] = trim($input['action_correctives_avancees']);
		$newinput['creation_sous_tache_preconisation'] = trim($input['creation_sous_tache_preconisation']);
		$newinput['export_only_priority_task'] = trim($input['export_only_priority_task']);
		$newinput['export_tasks'] = trim($input['export_tasks']);

		$newinput['risques_avances'] = trim($input['risques_avances']);

		$newinput['taille_photo_poste_fiche_de_poste'] = trim($input['taille_photo_poste_fiche_de_poste']);

		$newinput['recommandation_efficiency_activ'] = trim($input['recommandation_efficiency_activ']);

		$newinput['emailDomain'] = trim(str_replace('@', '', $input['emailDomain']));

		$newinput['product_categories'] = serialize($input['product_categories']);

		return $newinput;
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
	function digi_ac_createUtaks4PA_field()
	{
		global $optionYesNoList;
		$options = get_option('digirisk_options');
		if(current_user_can('digi_edit_option'))
		{
			echo EvaDisplayInput::createComboBox('digi_ac_createUtaks4PA_field', 'digirisk_options[creation_sous_tache_preconisation]', $optionYesNoList, $options['creation_sous_tache_preconisation']);
		}
		else
		{
			echo $options['creation_sous_tache_preconisation'];
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
	function digi_users_emaildomain_field()
	{
		$options = get_option('digirisk_options');
		if(current_user_can('digi_edit_option'))
		{
			echo "<input id='emailDomain' name='digirisk_options[emailDomain]' size='40' type='text' value='{$options['emailDomain']}' />";
		}
		else
		{
			echo $options['emailDomain'];
		}
	}

	/**
	*	Function allowing to set a explication area for the settings section
	*/
	function options_output_products()
	{
		_e('Cochez les cases correspondantes aux cat&eacute;gories de produits que vous souhaitez ajouter dans la partie &eacute;valuation des risques. Une cat&eacute;gorie sera affich&eacute;e dans une boite', 'evarisk');
	}
	/**
	*	Define the output fot the field. Get the option value to put the good value by default
	*/
	function digi_product_categories_field()
	{
		$options = get_option('digirisk_options');
		$productCategories = wpshop_attributes::getElementWithAttributeAndValue(WPSHOP_DBT_CATEGORY, wpshop_entities::getEntityIdFromCode('product_category'), 1, 'code', '', "'valid'");
		$i = 1;
		$tableContent = '';
		$choosenCategories = unserialize($options['product_categories']);
		foreach($productCategories as $productCategoryId => $productCategoryDef)
		{
			$checked = (is_array($choosenCategories) && in_array($productCategoryId, $choosenCategories)) ? ' checked="checked" ' : '';
			if($i == 1)
			{
				$tableContent .= '<tr>';
			}
			$tableContent .= '<td><input ' . $checked . ' id="productCategory' . $productCategoryId . '" name="digirisk_options[product_categories][]" type="checkbox" value="' . $productCategoryId . '" /><label for="productCategory' . $productCategoryId . '" >' . $productCategoryDef['attributes']['product_category_name']['value'] . '</label></td>';
			if($i == 2)
			{
				$tableContent .= '</tr>';
				$i = 0;
			}
			$i++;
		}
		if($i == 2)
		{
			$tableContent .= '</tr>';
		}

		echo '<table summary="product categories listing" cellpadding="0" cellspacing="0" >' . $tableContent . '</table>';
	}

	/**
	*	Return the option value from a given option name
	*
	*	@param string $optionName The option name of the option we want to get the value
	*
	*	@return mixed The option value
	*/
	function getOptionValue($optionName)
	{
		$digirisk_options = get_option('digirisk_options');

		return $digirisk_options[$optionName];
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

}