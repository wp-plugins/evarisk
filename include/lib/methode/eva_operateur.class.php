<?php
/**
 * 
 * @author Evarisk
 * @version v5.0
 */
include_once(EVA_CONFIG);
class eva_Operateur {
	
	/**
	 * @var String The operator symbole
	 */
	var $symbole;
	
/*
 *	Constructeur et accesseurs
 */
	
	/**
	 * Constructor of the operator class
	 * @param $symbole String The symbole to set
	 */
	function EvaOperateur($symbole = '') {
		$this->symbole = $symbole;
	}
	
	/**
	 * Return the operator symbole
	 * @return String The symbole
	 */
	function getSymbole()
	{
		return $this->symbole;
	}
	/**
	 * Set the operator symbole
	 * @param $symbole String The symbole to set
	 */
	function setSymbole($symbole)
	{
		$this->symbole = $symbole;
	}
	
/*
 * Autres operatores
 */
	static function getOperator($symbole)
	{
		global $wpdb;
		$symbole = $symbole;
		$t = TABLE_OPERATEUR;
		return $wpdb->get_row( "SELECT * FROM {$t} WHERE symbole = " . $symbole);
	}

	static function getOperators($where = "1") {
		global $wpdb;
		$t = TABLE_OPERATEUR;
		$resultat = $wpdb->get_results( "SELECT * FROM {$t} WHERE " . $where);
		return $resultat;
	}
}