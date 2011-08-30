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
	static function getCategorieDanger($id, $status = " Status = 'Valid' ")
	{
		global $wpdb;
		$id = (int) $id;
		$resultat = $wpdb->get_row( "SELECT * FROM " . TABLE_CATEGORIE_DANGER . " WHERE 1 AND id = " . $id);
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
			$racine = categorieDangers::getCategorieDangerByName("Categorie Racine");
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
					evarisk(document).ready(function(){
						evarisk("#message").addClass("updated");
						evarisk("#message").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La cat&eacute;gorie a bien &eacute;t&eacute; supprim&eacute;e', 'evarisk') . '</strong></p>') . '");
						evarisk("#message").show();
						setTimeout(function(){
							evarisk("#message").removeClass("updated");
							evarisk("#message").hide();
						},7500);
					});
				</script>';
		}
		else
		{
			echo 
				'<script type="text/javascript">
					evarisk(document).ready(function(){
						evarisk("#message").addClass("updated");
						evarisk("#message").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La cat&eacute;gorie n\'a pas pu &ecirc;tre supprim&eacute;e', 'evarisk') . '</strong></p>') . '");
						evarisk("#message").show();
						setTimeout(function(){
							evarisk("#message").removeClass("updated");
							evarisk("#message").hide();
						},7500);
					});
				</script>';
		}
	}

	function getCategorieDangerForRiskEvaluation($risque, $formId = '')
	{
		$categoryResult = array();
		$categoryResult['list'] = '';
		$categoryResult['script'] = '';
		$categoryResult['selectionCategorie'] = '';

		$nomRacine = 'Categorie Racine';
		$categorieRacine = categorieDangers::getCategorieDangerByName($nomRacine);
		$categoriesDangers = Arborescence::getDescendants(TABLE_CATEGORIE_DANGER, $categorieRacine);

		if($risque[0] != null)
		{// Si l'on édite un risque, on sélectionne la bonne catégorie de dangers
			$selectionCategorie = $risque[0]->idCategorie;
		}
		else
		{// Sinon on sélectionne la racine
			$selectionCategorie = $categorieRacine->id;
		}

		if(AFFICHAGE_PICTO_CATEGORIE)
		{
			foreach($categoriesDangers as $categorieDangers)
			{
				if($selectionCategorie == $categorieRacine->id)
				{
					$selectionCategorie = $categorieDangers->id;
				}
				$categoryResult['selectionCategorie'] = $selectionCategorie;
				$categorieDangerMainPhoto = evaPhoto::getMainPhoto(TABLE_CATEGORIE_DANGER, $categorieDangers->id);
				$categorieDangerMainPhoto = evaPhoto::checkIfPictureIsFile($categorieDangerMainPhoto, TABLE_CATEGORIE_DANGER);
				$categoryResult['list'] .= '<div class="radioPictoCategorie" ><input id="' . $formId . 'cat' . $categorieDangers->id . '" type="radio" name="' . $formId . 'categoriesDangers"  class="categoriesDangers" value="' . $categorieDangers->id . '" /><label for="' . $formId . 'cat' . $categorieDangers->id  . '" ><img src="' . $categorieDangerMainPhoto . '" alt="' . $categorieDangers->nom . '" title="' . $categorieDangers->nom . '" id="' . $formId . 'imgCat' . $categorieDangers->id . '" /></label></div>';
			}
			if($categoryResult['list'] != '')
			{
				$formIdSelector = ($formId != '') ? '#' . $formId . ' ' : '';
				$categoryResult['script'] .= '
		evarisk("#' . $formId . 'divCategorieDangerFormRisque").hide();
		evarisk("#' . $formId . 'cat' . $selectionCategorie . '").click();
		var ' . $formId . 'oldCatId = "' . $selectionCategorie . '";
		evarisk("' . $formIdSelector . '.categoriesDangers").click(function(){
			var ' . $formId . 'newCatId = (evarisk(this).attr("id")).replace("' . $formId . 'cat","");
			if(' . $formId . 'oldCatId != ' . $formId . 'newCatId)
			{
				evarisk("#' . $formId . 'categorieDangerFormRisque").val(' . $formId . 'newCatId);
				evarisk("#' . $formId . 'divDangerFormRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
				{
					"post":"true", 
					"table":"' . TABLE_CATEGORIE_DANGER . '", 
					"act":"reloadComboDangers", 
					"idElement":evarisk("#' . $formId . 'categorieDangerFormRisque").val(),
					"formId":"' . $formId . '"
				});
				' . $formId . 'oldCatId = ' . $formId . 'newCatId;
			}
		});';
			}
		}
		$categoryResult['list'] .= '
		<div id="' . $formId . 'divCategorieDangerFormRisque" >' . EvaDisplayInput::afficherComboBoxArborescente($categorieRacine, TABLE_CATEGORIE_DANGER, $formId . 'categorieDangerFormRisque', __('Cat&eacute;gorie de dangers', 'evarisk') . ' : ', 'categorieDangers', ucfirst(strtolower(sprintf(__("choisissez %s", 'evarisk'), __("une cat&eacute;gorie de dangers", 'evarisk')))), $selectionCategorie) . '</div>';
		$categoryResult['script'] .= '
	evarisk("#' . $formId . 'categorieDangerFormRisque").change(function(){
		evarisk("#' . $formId . 'divDangerFormRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
		{
			"post":"true", 
			"table":"' . TABLE_CATEGORIE_DANGER . '", 
			"act":"reloadComboDangers", 
			"idElement":evarisk("#' . $formId . 'categorieDangerFormRisque").val(),
			"formId":"' . $formId . '"
		});
	});';

		return $categoryResult;
	}

	/**
	* Returns all working unit belonging to the group witch is identifier or belonging to his descendants
	* @param int $elementId The group identifier
	* @param string $where The SQL where condition
	* @param string $order The SQL order condition
	* @return the working units  belonging to the group witch is identifier
	*/
	function getChildren($elementId, $where = "1", $order="nom ASC")
	{
		global $wpdb;
		$element = categorieDangers::getCategorieDanger($elementId, '');
		$subElements = Arborescence::getDescendants(TABLE_CATEGORIE_DANGER, $element);
		unset($tabId);
		$tabId[] = $elementId;
		foreach($subElements as $subElement)
		{
			$tabId[] = $subElement->id;
		}
		$resultat = $wpdb->get_results( "SELECT * FROM " . TABLE_DANGER . " WHERE id_categorie in (" . implode(', ', $tabId) . ") AND " . $where . " ORDER BY ". $order);
		return $resultat;
	}
}