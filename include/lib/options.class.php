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
	*	@param string $optionStatus The option status we want to create
	*
	*	@return object $option The option query result
	*/
	function createOption($optionName, $optionValue, $optionStatus)
	{
		global $wpdb;

		$sql = $wpdb->prepare("INSERT INTO " . TABLE_OPTION . " (nom, valeur, status) VALUES ('%s', '%s', '%s')", $optionName, $optionValue, $optionStatus);
		$option = $wpdb->query($sql);
	}

}