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
	*	Return a specific option
	*
	*	@param string $optionName The option name of the option we want to get the value
	*
	*	@return object $optionValue The option value
	*/
	function getOptionValue($optionName)
	{
		global $wpdb;

		$sql = $wpdb->prepare("SELECT valeur FROM " . TABLE_OPTION . " WHERE Status = 'Valid' AND nom = '%s' ", $optionName);
		$option = $wpdb->get_row($sql);

		$optionValue = $option->valeur;

		return $optionValue;
	}

	/**
	*	Return a specific option
	*
	*	@param string $optionName The option name of the option we want to get the value
	*
	*	@return object $optionValue The option value
	*/
	function updateOption($optionId, $optionValue)
	{
		global $wpdb;

		$sql = $wpdb->prepare("UPDATE " . TABLE_OPTION . " SET valeur = '%s' WHERE id = '%s' ", $optionValue, $optionId);
		$option = $wpdb->query($sql);
	}

}