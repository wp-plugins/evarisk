<?php

class options
{

	/**
	*	Return complete options list
	*
	*	@return object $optionList The complete list of option for the softare
	*/
	function getOptionList()
	{
		global $wpdb;

		$sql = $wpdb->prepare("SELECT * FROM " . TABLE_OPTION . " WHERE Status = 'Valid' ");
		$optionList = $wpdb->get_results($sql);

		return $optionList;
	}

	/**
	*	Return a specific option from it's name
	*
	*	@param string $optionName The option name of the option we want to get the value
	*	@param string $optionStatus The option status
	*
	*	@return object $optionValue The option value
	*/
	function getOptionValue($optionName, $optionStatus = 'Valid')
	{
		global $wpdb;

		$sql = $wpdb->prepare("SELECT valeur FROM " . TABLE_OPTION . " WHERE Status = '%s' AND nom = '%s' ", $optionStatus, $optionName);
		$option = $wpdb->get_row($sql);

		$optionValue = $option->valeur;

		return $optionValue;
	}

	/**
	*	Return a specific option it's id
	*
	*	@param string $optionId The option identifier we want to get
	*
	*	@return object $option The option informations
	*/
	function getOption($optionId)
	{
		global $wpdb;

		$sql = $wpdb->prepare("SELECT * FROM " . TABLE_OPTION . " WHERE id = '%s' ", $optionId);
		$option = $wpdb->get_row($sql);

		return $option;
	}

	/**
	*	Update an option
	*
	*	@param integer $optionId The option id we want to update
	*	@param string $optionName The option value we want to set
	*
	*	@return object $option The option query result
	*/
	function updateOption($optionId, $optionValue)
	{
		global $wpdb;

		$sql = $wpdb->prepare("UPDATE " . TABLE_OPTION . " SET valeur = '%s' WHERE id = '%s' ", $optionValue, $optionId);
		$option = $wpdb->query($sql);

		return $option;
	}

	/**
	*	Update an option from it's name
	*
	*	@param string $optionName The option name we want to update
	*	@param string $optionValue The option value we want to update
	*
	*	@return object $option The option query result
	*/
	function updateOptionFromName($optionName, $optionValue)
	{
		global $wpdb;

		$sql = $wpdb->prepare("UPDATE " . TABLE_OPTION . " SET valeur = '%s' WHERE nom = '%s' ", $optionValue, $optionName);
		$option = $wpdb->query($sql);
	}

	/**
	*	Create an option
	*
	*	@param string $optionName The option name we want to create
	*	@param string $optionValue The option value we want to create
	*	@param string $optionShownName The option shown name we want to create
	*	@param string $optionDomain The option domain we want to create
	*	@param string $optionType The option type we want to create
	*	@param string $optionStatus The option status we want to create
	*
	*	@return object $option The option query result
	*/
	function createOption($optionName, $optionValue, $optionShownName, $optionDomain, $optionType, $optionStatus)
	{
		global $wpdb;

		$sql = $wpdb->prepare(
			"INSERT INTO " . TABLE_OPTION . " 
				(domaine, nom, nomAffiche, valeur, status, typeOption)
			VALUES 
				('%s', '%s', '%s', '%s', '%s', '%s')", 
		$optionDomain, $optionName, $optionShownName, $optionValue, $optionStatus, $optionType);

		$option = $wpdb->query($sql);

		return $option;
	}


	/**
	*	Create the form to create a new option
	*
	*	@return string $formOutput The complet html output for the form
	*/
	function editOptionForm($optionType = 'ouinon')
	{
		global $optionYesNoList;
		$formOutput = '';

		$typeValeur = EvaDisplayInput::createComboBox('valeur', $optionYesNoList, '');
		if($optionType != 'ouinon')
		{
			$typeValeur = '<input type="text" value="" name="valeur" id="valeur" />';
		}

		/*	The form definition	*/
		$formOutput = 
		'<div id="createOption" >
			<input type="hidden" id="actionOption" name="actionOption" value="update" />
			<input type="hidden" id="idOption" name="idOption" value="" />
			<div class="hide" id="loadingOptionForm" ><center><img class="margin36" src="' . PICTO_LOADING_ROUND . '" alt="loading..." /></center></div>
			<table cellpadding="0" cellspacing="0" summary="option creation form" id="addOptionTable" style="margin:12px 0px 0px;" >
				<tbody>
					<tr>
						<td colspan="2">' . __('&Eacute;dition de ', 'evarisk') . '<span class="bold" id="optionName" ></span></td>
					</tr>
					<tr>
						<td id="optionTypeField" >' . $typeValeur . '</td>
						<td id="saveOption" ><input type="button" class="alignleft button-primary" name="saveCodeButton" id="saveCodeButton" value="' . __('Enregistrer', 'evarisk') . '" /><input type="button" class="alignleft button-primary" name="cancelCodeEditionButton" id="cancelCodeEditionButton" value="' . __('Annuler', 'evarisk') . '" /></td>
					</tr>
				</tbody>
			</table>
		</div>';

		/*	Javascript associated to the form	*/
		$script = 
		'<script type="text/javascript" >
			evarisk(document).ready(function(){
				evarisk("#saveCodeButton").click(function(){
					evarisk("#loadingOptionForm").show();
					evarisk("#addOptionTable").hide();
					evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
					{
						"post":"true",
						"table":"' . TABLE_OPTION . '",
						"act":evarisk("#actionOption").val(),
						"id":evarisk("#idOption").val(),
						"valeur":evarisk("#valeur").val()
					});
				});
				evarisk("#cancelCodeEditionButton").click(function(){
					emptyOptionForm();
				});
			});
		</script>';

		return $formOutput . $script;
	}

}