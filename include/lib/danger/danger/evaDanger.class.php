<?php
/**
 * 
 * @author Evarisk
 * @version v5.0
 */
include_once(EVA_CONFIG );
class EvaDanger {
	
	/**
	 * @var int The danger  identifier
	 */
	var $id;
	/**
	 * @var string The danger  name
	 */
	var $name;
	
/*
 *	Constructeur et accesseurs
 */
	
	/**
	 * Constructor of the danger  class
	 * @param int $id The identifier to setI
	 * @param string $name The name to set
	 */
	function EvaDanger($id = NULL, $name = '') {
		$this->id = $id;
		$this->name = $name;
	}
	
	/**
	 * Returns the danger  identifier
	 * @return int The identifier
	 */
	function getId()
	{
		return $this->id;
	}
	/**
	 * Set the danger  identifier
	 * @param int $id The identifier to set
	 */
	function setId($id)
	{
		$this->id = $id;
	}
	/**
	 * Returns the danger  name
	 * @return string The name
	 */
	function getName()
	{
		return $this->name;
	}
	/**
	 * Set the danger  name
	 * @param string $name The name to set
	 */
	function setName($name)
	{
		$this->name = $name;
	}
	
/*
 * Autres methodes
 */
	static function getDanger($id)
	{
		global $wpdb;
		$id = (int) $id;
		$resultat = $wpdb->get_row( "SELECT * FROM " . TABLE_DANGER . " WHERE Status = 'Valid' AND id = " . $id);
		return $resultat;
	}
	
	static function getDangerByName($nom)
	{
		global $wpdb;
		$resultat = $wpdb->get_row( "SELECT * FROM " . TABLE_DANGER . " WHERE Status = 'Valid' AND nom='" . $nom . "'");
		return $resultat;
	}
	

	static function getDangers($where = "1", $order = "id ASC") {
		global $wpdb;
		$resultat = $wpdb->get_results( "SELECT * FROM " . TABLE_DANGER . " WHERE Status = 'Valid' AND " . $where . " ORDER BY " . $order);
		return $resultat;
	}
	
	static function getDangersName($saufDanger = '')
	{	
		$dangers = EvaDanger::getDangers();
		foreach($dangers as $danger)
		{
			if($danger->nom != $saufDanger)
			{
				$tab_dangers[]=$danger->nom;
			}
		}
		return $tab_dangers;
	}
	
/*
Persistance
*/

	static function saveNewDanger($nom, $idCategorieMere)
	{
		global $wpdb;
		
		$nom = eva_tools::IsValid_Variable($nom);
		$idCategorieMere = eva_tools::IsValid_Variable($idCategorieMere);
		
		$sql = "INSERT INTO " . TABLE_DANGER . " (`nom`, `id_categorie`, `Status`) VALUES ('" . mysql_real_escape_string($nom) . "', '" . mysql_real_escape_string($idCategorieMere) . "', 'Valid')";
		$wpdb->query($sql);
	}
	
	static function updateDanger($id, $nom, $idCategorieMere, $description)
	{
		global $wpdb;
		
		$id = eva_tools::IsValid_Variable($id);
		$nom = eva_tools::IsValid_Variable($nom);
		$idCategorieMere = eva_tools::IsValid_Variable($idCategorieMere);
		$description = eva_tools::IsValid_Variable($description);
		
		$sql = "UPDATE " . TABLE_DANGER . " set `nom`='" . mysql_real_escape_string($nom) . "', `id_categorie`='" . mysql_real_escape_string($idCategorieMere) . "', description='" . mysql_real_escape_string($description) . "' WHERE `id`=" . mysql_real_escape_string($id);
		$wpdb->query($sql);
	}
	
	/**
	  * Transfer an working unit from a group to an other
	  * @param int $idDanger Working unit to transfer identifier
	  * @param int $idCategorieMere Group which receive the transfer identifier
	  */
	static function transfertDanger($idDanger, $idCategorieMere)
	{
		global $wpdb;
		
		$sql = "UPDATE " . TABLE_DANGER . " set `id_categorie`='" . $idCategorieMere . "' WHERE `id`=" . $idDanger;
		$wpdb->query($sql);
	}
	
	static function deleteDanger($id)
	{
		global $wpdb;
		
		$sql = "UPDATE " . TABLE_DANGER . " set `Status`='Deleted' WHERE `id`=" . $id;
		if($wpdb->query($sql))
		{
			echo 
				'<script type="text/javascript">
					$(document).ready(function(){
						$("#message").addClass("updated");
						$("#message").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'veille-reponse.gif" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le danger a bien &eacute;t&eacute; supprim&eacute;', 'evarisk') . '</strong></p>') . '");
						$("#message").show();
						setTimeout(function(){
							$("#message").removeClass("updated");
							$("#message").hide();
						},7500);
					});
				</script>';
		}
		else
		{
			echo 
				'<script type="text/javascript">
					$(document).ready(function(){
						$("#message").addClass("updated");
						$("#message").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'veille-reponse.gif" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le danger n\'a pas pu &ecirc;tre supprim&eacute;', 'evarisk') . '</strong></p>') . '");
						$("#message").show();
						setTimeout(function(){
							$("#message").removeClass("updated");
							$("#message").hide();
						},7500);
					});
				</script>';
		}
	}
}