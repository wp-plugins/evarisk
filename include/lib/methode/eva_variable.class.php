<?php
/**
 * 
 * @author Evarisk
 * @version v5.0
 */

class eva_Variable {
	
	/**
	 * @var Integer The variable identifier
	 */
	var $id;
	/**
	 * @var String The variable name
	 */
	var $name;
	/**
	 * @var Integer The variable minimum
	 */
	var $min;
	/**
	 * @var Integer The variable maximum
	 */
	var $max;
	/**
	 * @var String The variable annotation
	 */
	var $annotation;
	
/*
 *	Constructeur et accesseurs
 */
	
	/**
	 * Constructor of the variable class
	 * @param $id Integer The identifier to setI
	 * @param $name String The name to set
	 * @param $min Integer The minimum to set
	 * @param $max Integer The maximum to set
	 * @param $annotation String The annotation to set
	 */
	function EvaVariable($id = NULL, $name = '', $min = '', $max = '', $annotation = '') {
		$this->id = $id;
		$this->name = $name;
		$this->min = $min;
		$this->max = $max;
		$this->annotation = $annotation;
	}
	
	/**
	 * Return the variable identifier
	 * @return Integer The identifier
	 */
	function getId()
	{
		return $this->id;
	}
	/**
	 * Set the variable identifier
	 * @param $id Integer The identifier to set
	 */
	function setId($id)
	{
		$this->id = $id;
	}
	/**
	 * Return the variable name
	 * @return String The name
	 */
	function getName()
	{
		return $this->name;
	}
	/**
	 * Set the variable name
	 * @param $name String The name to set
	 */
	function setName($name)
	{
		$this->name = $name;
	}
	/**
	 * Return the variable minimum
	 * @return $min Integer The minimum
	 */
	function getMin($min)
	{
		return $this->min;
	}
	/**
	 * Set the variable minimum
	 * @param $min Integer The minimum to set
	 */
	function setMin($min)
	{
		$this->min = $min;
	}
	/**
	 * Return the variable maximum
	 * @return $max String The maximum
	 */
	function getMax($max)
	{
		return $this->max;
	}
	/**
	 * Set the variable maximum
	 * @param $max String The maximum to set
	 */
	function setMax($max)
	{
		$this->max = $max;
	}
	/**
	 * Return the variable annotation
	 * @return $annotation String The annotation
	 */
	function getAnnotation($annotation)
	{
		return $this->annotation;
	}
	/**
	 * Set the variable annotation
	 * @param $annotation String The annotation to set
	 */
	function setAnnotation($annotation)
	{
		$this->annotation = $annotation;
	}
	
/*
 * Autres variables
 */
	static function getVariable($id)
	{
		global $wpdb;
		$id = (int) $id;
		$t = TABLE_VARIABLE;
		return $wpdb->get_row( "SELECT * FROM {$t} WHERE id = " . $id);
	}

	static function getVariables($where = "1", $order = "id ASC")
	{
		global $wpdb;
		$t = TABLE_VARIABLE;
		$resultat = $wpdb->get_results( "SELECT * FROM {$t} WHERE " . $where . " ORDER BY " . $order);
		return $resultat;
	}

	static function getValeurAlternative($idVariable, $valeur, $date = '')
	{
		if($date == '')
		{
			$date = date('Y-m-d H:i:s');
		}
		
		global $wpdb;
		$t = TABLE_VALEUR_ALTERNATIVE;
		$sql = "
			SELECT * 
			FROM " . TABLE_VALEUR_ALTERNATIVE . " tva1
			WHERE tva1.id_variable = " . $idVariable . "
			AND tva1.valeur = " . $valeur . "
			AND tva1.date < '" . $date . "'
			AND NOT EXISTS
			(
				SELECT * 
				FROM " . TABLE_VALEUR_ALTERNATIVE . " tva2
				WHERE tva2.id_variable = " . $idVariable . "
				AND tva2.valeur = " . $valeur . "
				AND tva2.date < '" . $date . "'
				AND tva2.date > tva1.date
			)
			";
		$resultat = $wpdb->get_row($sql);
		if($resultat != null)
		{
			$valeurAlternative = $resultat->valeurAlternative;
		}
		else
		{
			$valeurAlternative = $valeur;
		}
		return $valeurAlternative;
	}

	function create_basic_variable(){
		global $evaluation_main_vars, $wpdb;

		foreach($evaluation_main_vars as $var_index => $var_definition){
			$var_content = array();
			foreach($var_definition as $field_name => $field_value){
				$var_content[$field_name] = $field_value;
			}
			$wpdb->insert(TABLE_VARIABLE, $var_content);
		}
	}

}