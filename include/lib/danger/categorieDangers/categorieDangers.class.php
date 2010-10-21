<?php
/**
 * 
 * @author Evarisk
 * @version v5.0
 */
include_once(EVA_CONFIG );
require_once(EVA_LIB_PLUGIN_DIR . 'arborescence.class.php' );
class categorieDangers {
	
	/**
	 * @var int The danger categorie identifier
	 */
	var $id;
	/**
	 * @var string The danger categorie name
	 */
	var $name;
	
/*
 *	Constructeur et accesseurs
 */
	
	/**
	 * Constructor of the danger categorie class
	 * @param int $id The identifier to setI
	 * @param string $name The name to set
	 */
	function categorieDanger($id = NULL, $name = '') {
		$this->id = $id;
		$this->name = $name;
	}
	
	/**
	 * Returns the danger categorie identifier
	 * @return int The identifier
	 */
	function getId()
	{
		return $this->id;
	}
	/**
	 * Set the danger categorie identifier
	 * @param int $id The identifier to set
	 */
	function setId($id)
	{
		$this->id = $id;
	}
	/**
	 * Returns the danger categorie name
	 * @return string The name
	 */
	function getName()
	{
		return $this->name;
	}
	/**
	 * Set the danger categorie name
	 * @param string $name The name to set
	 */
	function setName($name)
	{
		$this->name = $name;
	}
	
/*
 * Autres methodes
 */
	static function getCategorieDanger($id)
	{
		global $wpdb;
		$id = (int) $id;
		$resultat = $wpdb->get_row( "SELECT * FROM " . TABLE_CATEGORIE_DANGER . " WHERE Status = 'Valid' AND id = " . $id);
		return $resultat;
	}
	
	static function getCategorieDangerByName($nom)
	{
		global $wpdb;
		$resultat = $wpdb->get_row( "SELECT * FROM " . TABLE_CATEGORIE_DANGER . " WHERE Status = 'Valid' AND nom='" . $nom . "'");
		return $resultat;
	}
	

	static function getCategoriesDanger($where = "1", $order = "id ASC") {
		global $wpdb;
		$resultat = $wpdb->get_results( "SELECT * FROM " . TABLE_CATEGORIE_DANGER . " WHERE Status = 'Valid' AND " . $where . " ORDER BY " . $order);
		return $resultat;
	}
	
	function getCategoriesName($saufCategorie = '')
	{	
		$categories = categorieDangers::getCategoriesDanger();
		foreach($categories as $categorie)
		{
			if($categorie->nom != $saufCategorie)
			{
				$tab_categories[]=$categorie->nom;
			}
		}
		return $tab_categories;
	}
	
	static function getDangersDeLaCategorie($idCategorie, $where = "1", $order="nom ASC")
	{
		global $wpdb;
		$resultat = $wpdb->get_results( "SELECT * FROM " . TABLE_DANGER . " WHERE  Status = 'Valid' AND id_categorie =" . $idCategorie . " AND " . $where . " ORDER BY ". $order);
		return $resultat;
	}

	static function saveNewCategorie($nom)
	{
		global $wpdb;
		
		$lim = Arborescence::getMaxLimiteDroite(TABLE_CATEGORIE_DANGER);
		$sql = "INSERT INTO " . TABLE_CATEGORIE_DANGER . " (`nom`, `Status`, `limiteGauche`, `limiteDroite`) VALUES ('" . $nom . "', 'Valid', '" . ($lim) . "', '" . ($lim+1) . "')";
		$wpdb->query($sql);
		$sql = "UPDATE " . TABLE_CATEGORIE_DANGER . " SET `limiteDroite`= '" . ($lim + 2)  . "' WHERE`nom` = ('Categorie Racine')";
		$wpdb->query($sql);
	}
	
	static function updateCategorie($id_categorie, $nom, $description, $idCategorieMere)
	{
		global $wpdb;
		
		$sql = "UPDATE `" . TABLE_CATEGORIE_DANGER . "` SET `nom`='" . $nom . "', description='" . $description . "' WHERE `id`='" . $id_categorie . "'";
		$wpdb->query($sql);
		
		$categorieFille =  categorieDangers::getCategorieDanger($id_categorie);
		$categorieDestination =  categorieDangers::getCategorieDanger($idCategorieMere);
		$catMere = Arborescence::getPere(TABLE_CATEGORIE_DANGER, $categorieFille);
		if($categorieDestination->nom != $catMere->nom)
		{
			$racine =  categorieDangers::getCategorieDangerByName("Categorie Racine");
			Arborescence::deplacerElements(TABLE_CATEGORIE_DANGER, $racine, $categorieFille, $categorieDestination);
		}
	}
	
	/**
	  * Set the status of the group wich is the identifier to Delete 
	 */
	static function deleteCategorie($id)
	{
		global $wpdb;
		
		$sql = "UPDATE " . TABLE_CATEGORIE_DANGER . " set `Status`='Deleted' WHERE `id`=" . $id;
		if($wpdb->query($sql))
		{
			echo 
				'<script type="text/javascript">
					$(document).ready(function(){
						$("#message").addClass("updated");
						$("#message").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La cat&eacute;gorie a bien &eacute;t&eacute; supprim&eacute;e', 'evarisk') . '</strong></p>') . '");
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
						$("#message").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La cat&eacute;gorie n\'a pas pu &ecirc;tre supprim&eacute;e', 'evarisk') . '</strong></p>') . '");
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