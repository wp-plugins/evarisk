<?php
/**
 * 
 * @author Evarisk
 * @version v5.0
 */
include_once(EVA_CONFIG );
include_once(EVA_LIB_PLUGIN_DIR . 'adresse/evaAddress.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'users/evaUserGroup.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'users/evaUser.class.php');

class EvaGroupement {
	
	/**
	 * @var int The groupement  identifier
	 */
	var $id;
	/**
	 * @var string The groupement  name
	 */
	var $name;
	
/*
 *	Constructeur et accesseurs
 */
	
	/**
	 * Constructor of the groupement  class
	 * @param int $id The identifier to setI
	 * @param string $name The name to set
	 */
	function EvaGroupement($id = NULL, $name = '') {
		$this->id = $id;
		$this->name = $name;
	}
	
	/**
	 * Returns the groupement  identifier
	 * @return int The identifier
	 */
	function getId()
	{
		return $this->id;
	}
	/**
	 * Set the groupement  identifier
	 * @param int $id The identifier to set
	 */
	function setId($id)
	{
		$this->id = $id;
	}
	/**
	 * Returns the groupement  name
	 * @return string The name
	 */
	function getName()
	{
		return $this->name;
	}
	/**
	 * Set the groupement  name
	 * @param string $name The name to set
	 */
	function setName($name)
	{
		$this->name = $name;
	}
	
/*
 * Autres methodes
 */
	
	/**
	 * Returns the group witch is the identifier
	 * @param int $id Group identifier search
	 * @return The group  witch is the identifier
	 */
	static function getGroupement($id)
	{
		global $wpdb;
		$id = (int) $id;
		$resultat = $wpdb->get_row( "SELECT * FROM " . TABLE_GROUPEMENT . " WHERE id = " . $id);
		return $resultat;
	}
	
	/**
	 * Returns the group witch is the name
	 * @param string $nom Group name search
	 * @return The group  witch is the name
	 */
	static function getGroupementByName($nom)
	{
		global $wpdb;
		$resultat = $wpdb->get_row( "SELECT * FROM " . TABLE_GROUPEMENT . " WHERE nom='" . $nom . "'");
		return $resultat;
	}
	
	/**
	 * Returns all group maching with the where condition and order by the order condition
	 * @param string $where SQL where condition
	 * @param string $order SQL order condition
	 * @return The groups maching with the where condition and order by the order condition
	 */
	static function getGroupements($where = "1", $order = "id ASC") {
		global $wpdb;
		$resultat = $wpdb->get_results( "SELECT * FROM " . TABLE_GROUPEMENT . " WHERE nom<>'Groupement Racine' AND " . $where . " ORDER BY " . $order);
		return $resultat;
	}
	
	/**
	 * Returns all group name whitout the specifie
	 * @param string $saufGroupement group name not consider
	 * @return All the  groups name whitout the specifie
	 */
	function getGroupementsName($saufGroupement = '')
	{	
		$groupements = EvaGroupement::getGroupements();
		foreach($groupements as $groupement)
		{
			if($groupement->nom != $saufGroupement)
			{
				$tab_groupements[]=$groupement->nom;
			}
		}
		if(isset($tab_groupements))
			return $tab_groupements;
		else return null;
	}
	
	/**
	  * Returns all working unit belonging to the group witch is identifier
	  * @param string $where The SQL where condition
	  * @param string $order The SQL order condition
	  * @return the working units  belonging to the group witch is identifier
	  */
	static function getUnitesDuGroupement($idGroupement, $where = "1", $order="nom ASC")
	{
		global $wpdb;
		$resultat = $wpdb->get_results( "SELECT * FROM " . TABLE_UNITE_TRAVAIL . " WHERE id_Groupement =" . $idGroupement . " AND " . $where . " AND Status = 'Valid' ORDER BY ". $order);
		return $resultat;
	}
	
	/**
	  * Returns all working unit belonging to the group witch is identifier or belonging to his descendants
	  * @param int $idGroupement The group identifier
	  * @param string $where The SQL where condition
	  * @param string $order The SQL order condition
	  * @return the working units  belonging to the group witch is identifier
	  */
	static function getUnitesDescendantesDuGroupement($idGroupement, $where = "1", $order="nom ASC")
	{
		global $wpdb;
		$groupement = EvaGroupement::getGroupement($idGroupement);
		$sousEntitesGroupement = Arborescence::getDescendants(TABLE_GROUPEMENT, $groupement);
		unset($tabId);
		$tabId[] = $idGroupement;
		foreach($sousEntitesGroupement as $sousEntiteGroupement)
		{
			$tabId[] = $sousEntiteGroupement->id;
		}
		$resultat = $wpdb->get_results( "SELECT * FROM " . TABLE_UNITE_TRAVAIL . " WHERE id_Groupement in (" . implode(', ', $tabId) . ") AND " . $where . " ORDER BY ". $order);
		return $resultat;
	}
	
	/**
	  * Returns all working unit and groups belonging to the group witch is identifier
	  * @param int $idGroupement The group identifier
	  * @return the working units and groups belonging to the group witch is identifier
	  */
	static function getUnitesEtGroupementDescendants($idGroupement)
	{
		global $wpdb;
		$groupement = EvaGroupement::getGroupement($idGroupement);
		$sousEntitesGroupement = Arborescence::getFils(TABLE_GROUPEMENT, $groupement, 1, "nom ASC");
		unset($resultats);
		$unites = EvaGroupement::getUnitesDescendantesDuGroupement($idGroupement, 1, "nom ASC");
		$indiceGroupement = 0;
		$indiceUnites = 0;
		while(($indiceGroupement < count($sousEntitesGroupement)) and ($indiceUnites < count($unites)))
		{
			if($sousEntitesGroupement[$indiceGroupement]->nom > $unites[$indiceUnites]->nom)
			{
				$resultats[] = array('value' => $unites[$indiceUnites], 'table' => TABLE_UNITE_TRAVAIL);
				$indiceUnites ++;
			}
			else
			{
				$resultats[] = array('value' => $sousEntitesGroupement[$indiceGroupement], 'table' => TABLE_GROUPEMENT);
				$indiceGroupement ++;
			}
		}
		for($i=$indiceGroupement; $i<count($sousEntitesGroupement); $i++)
		{
			$resultats[] = array('value' => $sousEntitesGroupement[$i], 'table' => TABLE_GROUPEMENT);
		}
		for($i=$indiceUnites; $i<count($unites); $i++)
		{
			$resultats[] = array('value' => $unites[$i], 'table' => TABLE_UNITE_TRAVAIL);
		}
		return $resultats;
	}
	
	/**
	  * @todo employé évalué
	  */
	static function getInfosGroupement($idGroupement)
	{		
		unset($infos, $info);
		$groupement = EvaGroupement::getGroupement($idGroupement);
		$sousEntitesGroupement = Arborescence::getFils(TABLE_GROUPEMENT, $groupement);
		$entitesDescendanteGroupement = Arborescence::getDescendants(TABLE_GROUPEMENT, $groupement);
		$unitesGroupement = EvaGroupement::getUnitesDuGroupement($idGroupement);
		$unitesDescendanteGroupement = EvaGroupement::getUnitesDescendantesDuGroupement($idGroupement);
		
		$info['nom'] = __('Niveau de risque', 'evarisk');
		$scoreRisqueGroupement = EvaGroupement::getScoreRisque($idGroupement);
		$info['valeur'] = EvaGroupement::getNiveauRisque($scoreRisqueGroupement);
		$info['classeValeur'] = 'risque' . Risque::getSeuil($scoreRisqueGroupement) . 'Text risqueText' . TABLE_GROUPEMENT . $idGroupement;
		$infos[] = $info;
		$info['nom'] = __('Unit&eacute;s', 'evarisk');
		$info['valeur'] = count($unitesGroupement) . '(' . count($unitesDescendanteGroupement) . ')';
		$info['classeValeur'] = '';
		$infos[] = $info;
		$info['nom'] = __('Sous groupements', 'evarisk');
		$info['valeur'] = count($sousEntitesGroupement) . '(' . count($entitesDescendanteGroupement) . ')';
		$info['classeValeur'] = '';
		$infos[] = $info;
		$info['nom'] = __('Employ&eacute;s', 'evarisk');
		$info['valeur'] = evaUserGroup::getUserNumberInWorkUnit($idGroupement, TABLE_GROUPEMENT);
		$info['classeValeur'] = '';
		$infos[] = $info;
		$info['nom'] = __('Employ&eacute;s &eacute;valu&eacute;s', 'evarisk');
		$info['valeur'] = count(evaUser::getBindUsers($idGroupement, TABLE_GROUPEMENT));
		$info['classeValeur'] = '';
		$infos[] = $info;
		return $infos;
	}
	
	//@todo getScoreRisque
	static function getScoreRisque($id)
	{
		$scoreTotal = 0;
		$unites = EvaGroupement::getUnitesDescendantesDuGroupement($id);
		$scoreTotal = 0;
		$diviseur = 0;
		if($unites != null)
		{
			foreach($unites as $unite)
			{
				$score = UniteDeTravail::getScoreRisque($unite->id);
				$coef = (Risque::getSeuil($score) - 1) * UniteDeTravail::getNombreRisques($unite->id);
				$scoreTotal = $scoreTotal + $score * $coef;
				$diviseur = $diviseur + $coef;
			}
		}
		$temp = Risque::getRisques(TABLE_GROUPEMENT, $id, "Valid");
		if($temp != null)
		{
			foreach($temp as $risque)
			{
				$risques['"' . $risque->id . "'"][] = $risque; 
			}
		}
		if(isset($risques) && ($risques != null))
		{
			foreach($risques as $risque)
			{
				$idMethode = $risque[0]->id_methode;
				$score = Risque::getScoreRisque($risque);
				$quotation = Risque::getEquivalenceEtalon($idMethode, $score, $risque[0]->date);
				$niveauSeuil = Risque::getSeuil($quotation);
				$scoreTotal = $scoreTotal + $quotation * ($niveauSeuil - 1);
				$diviseur = $diviseur + ($niveauSeuil - 1);
			}
		}
		if(isset($diviseur) && ($diviseur != 0))
		{
			return round($scoreTotal / $diviseur, 0);
		}
		else
		{
			return 0;
		}
	}
	
	static function getNiveauRisque($quotation)
	{
		return Risque::getNiveauRisque(Risque::getSeuil($quotation));
	}

	/**
	  * Returns the marker informations of a group for the google maps
	  * @param string $id Group identifier
	  * @return the marker informations for the google maps
	  */
	static function getMarkersGeoLoc($id)
	{
		global $wpdb;
		
		if($id == null)
		{
			$geoLoc = null;
		}
		else
		{
			$group = $wpdb->get_row( "SELECT * FROM " . TABLE_GROUPEMENT . " WHERE id =" . $id);
			$address = new EvaAddress($group->id_adresse);
			$address->load();
			$geoLoc = $address->getGeoLoc();
			$scoreRisque = EvaGroupement::getScoreRisque($group->id);
			$geoLoc['info'] = '<img class="alignleft" style="margin-right:0.5em;" src="' . EVA_GROUPEMENT_ICON . '" alt="Groupement : "/><strong>' . $group->nom . '</strong><br /><em>' . __('Risque', 'evarisk') . ' : <span class="valeurInfoElement risque' . Risque::getSeuil($scoreRisque) . 'Text">' . EvaGroupement::getNiveauRisque($scoreRisque) . '</span></em>';
			$geoLoc['type'] = "groupement"; 
			$geoLoc['image'] = GOOGLEMAPS_GROUPE; 
			return $geoLoc;
		}
	}
/*
  * Persistance
  */
	/**
	 * Save a new group
	 * @param string $nom group unit name
	 */
	static function saveNewGroupement($nom)
	{
		global $wpdb;
		
		$lim = Arborescence::getMaxLimiteDroite(TABLE_GROUPEMENT);
		$sql = "INSERT INTO " . TABLE_GROUPEMENT . " (`nom`, `Status`, `limiteGauche`, `limiteDroite`) VALUES ('" . $nom . "', 'Valid', '" . ($lim) . "', '" . ($lim+1) . "')";
		$wpdb->query($sql);
		$sql = "UPDATE " . TABLE_GROUPEMENT . " SET `limiteDroite`= '" . ($lim + 2)  . "' WHERE`nom` = ('Groupement Racine')";
		$wpdb->query($sql);
	}
	
	/**
	 * Update the group which is the identifier
	 * @param string $id_Groupement group identifier (not update)
	 * @param string $nom group name 
	 * @param string $description group description
	 * @param string $telephone group telephone
	 * @param string $effectif group effective 
	 * @param string $idAdresse Identifier of the address group name in the Adress Table
	 * @param string $idGroupementPere  father group id
	 */
	static function updateGroupement($id_Groupement, $nom, $description, $telephone, $effectif, $idAdresse, $idGroupementPere)
	{
		global $wpdb;
		
		$sql = "UPDATE `" . TABLE_GROUPEMENT . "` SET `nom`='" . $nom . "', `description`='" . $description . "', `telephoneGroupement`='" . $telephone . "', `effectif`='" . $effectif . "', `id_adresse`='" . $idAdresse . "' WHERE `id`='" . $id_Groupement . "'";
		$wpdb->query($sql);
		
		$groupementFils =  EvaGroupement::getGroupement($id_Groupement);
		$groupementDestination =  EvaGroupement::getGroupement($idGroupementPere);
		$groupementPere = Arborescence::getPere(TABLE_GROUPEMENT, $groupementFils);
		if($groupementDestination->id != $groupementPere->id)
		{
			$racine =  EvaGroupement::getGroupementByName("Groupement Racine");
			Arborescence::deplacerElements(TABLE_GROUPEMENT, $racine, $groupementFils, $groupementDestination);
		}
	}
	
	/**
	  * Set the status of the group wich is the identifier to Delete 
	 */
	static function deleteGroupement($id)
	{
		global $wpdb;
		
		$sql = "UPDATE " . TABLE_GROUPEMENT . " set `Status`='Deleted' WHERE `id`=" . $id;
		if($wpdb->query($sql))
		{
			echo 
				'<script type="text/javascript">
					$(document).ready(function(){
						$("#message").addClass("updated");
						$("#message").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le groupement a bien &eacute;t&eacute; supprim&eacute;e', 'evarisk') . '</strong></p>') . '");
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
						$("#message").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le groupement n\'a pas pu &ecirc;tre supprim&eacute;e', 'evarisk') . '</strong></p>') . '");
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