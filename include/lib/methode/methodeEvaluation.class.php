<?php
/**
 * 
 * @author Evarisk
 * @version v5.0
 */
include_once(EVA_CONFIG);
require_once(EVA_LIB_PLUGIN_DIR . 'methode/eva_variable.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'methode/eva_operateur.class.php');
class MethodeEvaluation {
	
	/**
	 * @var Integer The method identifier
	 */
	var $id;
	/**
	 * @var String The method name
	 */
	var $name;
	
/*
 *	Constructeur et accesseurs
 */
	
	/**
	 * Constructor of the method class
	 * @param $id Integer The identifier to setI
	 * @param $name String The name to set
	 */
	function MethodeEvaluation($id = NULL, $name = '') {
		$this->id = $id;
		$this->name = $name;
	}
	
	/**
	 * Return the method identifier
	 * @return Integer The identifier
	 */
	function getId()
	{
		return $this->id;
	}
	/**
	 * Set the method identifier
	 * @param $id Integer The identifier to set
	 */
	function setId($id)
	{
		$this->id = $id;
	}
	/**
	 * Return the method name
	 * @return String The name
	 */
	function getName()
	{
		return $this->name;
	}
	/**
	 * Set the method name
	 * @param $name String The name to set
	 */
	function setName($name)
	{
		$this->name = $name;
	}
	
/*
 * Autres Methodes
 */
	static function getMethod($id)
	{
		global $wpdb;
		$id = (int) $id;
		$t = TABLE_METHODE;
		return $wpdb->get_row( "SELECT * FROM {$t} WHERE id = " . $id);
	}

	static function getMethods($where = "1", $order = "nom ASC") 
	{
		global $wpdb;
		$t = TABLE_METHODE;
		$resultat = $wpdb->get_results( "SELECT * FROM {$t} WHERE " . $where . " ORDER BY " . $order);
		return $resultat;
	}
	
	static function getAllVariables($where = "1", $order = "nom ASC")
	{
		global $wpdb;
		$resultat = eva_Variable::getVariables($where, $order);
		return $resultat;
	}
	
	static function getVariablesMethode($id_methode, $date=null)
	{
		global $wpdb;
		
		if($date==null)
		{
			$date=date('Y-m-d H:i:s');
		}
		$id_methode = (int) $id_methode;
		$tav = TABLE_AVOIR_VARIABLE;
		$tv =  TABLE_VARIABLE ;
		return $wpdb->get_results( "SELECT * 
			FROM " . $tv . ", " . $tav . " t1
			WHERE t1.id_methode=" . $id_methode . " 
			AND t1.date < '" . $date . "'
			AND NOT EXISTS
			(
				SELECT * 
				FROM " . $tav . " t2
				WHERE t2.id_methode=" . $id_methode . " 
				AND t2.date < '" . $date . "'
				AND t1.date < t2.date
			)
			AND id_variable=id 
			ORDER BY ordre ASC");
	}
	
	static function getDistinctVariablesMethode($id_methode, $date=null)
	{
		global $wpdb;
		
		if($date==null)
		{
			$date=date('Y-m-d H:i:s');
		}
		$id_methode = (int) $id_methode;
		$tav = TABLE_AVOIR_VARIABLE;
		$tv =  TABLE_VARIABLE ;
		return $wpdb->get_results( "
			SELECT DISTINCT(nom), id, min, max, annotation
			FROM " . $tv . ", " . $tav . " t1
			WHERE t1.id_methode=" . $id_methode . " 
			AND t1.date < '" . $date . "'
			AND NOT EXISTS
			(
				SELECT * 
				FROM " . $tav . " t2
				WHERE t2.id_methode=" . $id_methode . " 
				AND t2.date < '" . $date . "'
				AND t1.date < t2.date
			)
			AND id_variable=id 
			ORDER BY ordre ASC");
	}
	
	static function getOperateursMethode($id_methode, $date=null)
	{
		global $wpdb;
		
		if($date==null)
		{
			$date=date('Y-m-d H:i:s');
		}
		$id_methode = (int) $id_methode;
		$t = TABLE_AVOIR_OPERATEUR;
		return $wpdb->get_results( "SELECT * 
				FROM " . $t . " t1
				WHERE t1.id_methode=" . $id_methode . " 
				AND t1.date < '" . $date . "'
				AND NOT EXISTS
				(
					SELECT * 
					FROM " . $t . " t2
					WHERE t2.id_methode=" . $id_methode . " 
					AND t2.date < '" . $date . "'
					AND t1.date < t2.date
				)
				ORDER BY ordre ASC");
	}
	
	static function getFormule($id, $date=null)
	{
		global $wpdb;
		
		if($date==null)
		{
			$date=date('Y-m-d H:i:s');
		}
		$id = (int) $id;
		$formule = '';
		$t = TABLE_AVOIR_VARIABLE;
		//on récupère les ids des variables
		$id_variables = $wpdb->get_results("
			SELECT * 
			FROM " . $t . " t1
			WHERE t1.id_methode=" . $id . " 
			AND t1.date < '" . $date . "'
			AND NOT EXISTS
			(
				SELECT * 
				FROM " . $t . " t2
				WHERE t2.id_methode=" . $id . " 
				AND t2.date < '" . $date . "'
				AND t1.date < t2.date
			)
			ORDER BY ordre ASC");
		$ordre=0;
		$table_var = TABLE_VARIABLE;
		$table_avoir_op = TABLE_AVOIR_OPERATEUR;
		//pour chaque id
		foreach($id_variables as $id_variable)
		{
			//on recupere la variable...
			$variable = $wpdb->get_row("SELECT * FROM " . $table_var . " WHERE id=" . $id_variable->id_variable);
			//et l'opérateur
			$operateur = $wpdb->get_row("
				SELECT * 
				FROM " . $table_avoir_op . " t1
				WHERE t1.id_methode=" . $id . " 
				AND t1.date < '" . $date . "'
				AND t1.ordre=" . $ordre . "
				AND NOT EXISTS
				(
					SELECT * 
					FROM " . $table_avoir_op . " t2
					WHERE t2.id_methode=" . $id . " 
					AND t2.date < '" . $date . "'
					AND t2.ordre=" . $ordre . "
					AND t1.date < t2.date
				)
				ORDER BY ordre ASC");
			//et on complète la formule
			$operateur = (!isset($operateur) OR $operateur == null)?'':$operateur->operateur;
			$formule = $formule . ' ' . $operateur . ' ' . $variable->nom;
			$ordre = $ordre + 1;
		}
		return $formule;
	}
	
	static function getEtalon()
	{
		global $wpdb;
		$table = TABLE_ETALON;
		$resultat = $wpdb->get_row( "SELECT * FROM " . $table);
		return $resultat;
	}
	
	static function getEquivalentEtalon($idMethode, $valeurEtalon, $date=null)
	{
		global $wpdb;
		
		if($date==null)
		{
			$date=date('Y-m-d H:i:s');
		}
		$table = TABLE_EQUIVALENCE_ETALON;
		$resultat = $wpdb->get_row("
			SELECT * 
			FROM " . $table . " t1
			WHERE t1.id_methode=" . $idMethode . " 
			AND t1.id_valeur_etalon=" . $valeurEtalon . " 
			AND t1.date < '" . $date . "'
			AND NOT EXISTS
			(
				SELECT * 
				FROM " . $table . " t2
				WHERE t2.id_methode=" . $idMethode . " 
				AND t1.id_valeur_etalon=" . $valeurEtalon . " 
				AND t2.date < '" . $date . "'
				AND t1.date < t2.date
			)");
		return $resultat;
	}
	
}