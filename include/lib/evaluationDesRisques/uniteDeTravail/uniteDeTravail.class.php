<?php
/**
 * 
 * @author Evarisk
 * @version v5.0
 */
require_once(EVA_CONFIG);
require_once(EVA_LIB_PLUGIN_DIR . 'risque/Risque.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'adresse/evaAddress.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'users/evaUserGroup.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'users/evaUser.class.php');

class UniteDeTravail {
	
	/**
	 * @var int The working unit identifier
	 */
	var $id;
	/**
	 * @var string The working unit name
	 */
	var $name;
	/**
	 * @var string The working unit picture path
	 */
	var $picture;
	
/*
 *	Constructeur et accesseurs
 */
	
	/**
	 * Constructor of the working unit class
	 * @param int $id The identifier to set
	 * @param string $name The name to set
	 * @param string $picture The picture path to set
	 */
	function UniteDeTravail($id = NULL, $name = '', $picture = EVA_DEFAULT_UT_IMAGE) {
		$this->id = $id;
		$this->name = $name;
		$this->picture = $picture;
	}
	
	/**
	 * Returns the working unit identifier
	 * @return int The identifier
	 */
	function getId()
	{
		return $this->id;
	}
	/**
	 * Set the working unit identifier
	 * @param int $id The identifier to set
	 */
	function setId($id)
	{
		$this->id = $id;
	}
	/**
	 * Returns the working unit name
	 * @return string The name
	 */
	function getName()
	{
		return $this->name;
	}
	/**
	 * Set the working unit name
	 * @param string $name The name to set
	 */
	function setName($name)
	{
		$this->name = $name;
	}
	/**
	 * Returns the working unit picture path
	 * @return string The picture path
	 */
	function getPicture()
	{
		return $this->picture;
	}
	/**
	 * Set the working unit picture path
	 * @param string $picture The picture path to set
	 */
	function setpicture($picture)
	{
		$this->picture = $picture;
	}
	
/*
 * Autres Methodes
 */
	
	/**
	 * Returns the working unit witch is the identifier
	 * @param int $id Working unit identifier search
	 * @return The working unit  witch is the identifier
	 */
	static function getWorkingUnit($id)
	{
		global $wpdb;
		$id = (int) $id;
		return $wpdb->get_row( "SELECT * FROM " . TABLE_UNITE_TRAVAIL . " WHERE id = " . $id);
	}
	
	/**
	 * Returns the working unit witch is the name
	 * @param string $nom Working unit name search
	 * @return The working unit 
	 */
	static function getWorkingUnitByName($nom)
	{
		global $wpdb;
		$resultat = $wpdb->get_row( "SELECT * FROM " . TABLE_UNITE_TRAVAIL . " WHERE nom='" . $nom . "'");
		return $resultat;
	}
	
	/**
	 * Returns all working units maching with the where condition and order by the order condition
	 * @param string $where SQL where condition
	 * @param string $order SQL order condition
	 * @return The working units  maching with the where condition and order by the order condition
	 */
	static function getWorkingUnits($where = "1", $order = "id ASC") {
		global $wpdb;
		$resultat = $wpdb->get_results( "SELECT * FROM " . TABLE_UNITE_TRAVAIL . " WHERE " . $where . " ORDER BY " . $order);
		return $resultat;
	}
	
	/**
	 * Returns all working units name whitout the specifie
	 * @param string $saufUnite Working unit name not consider
	 * @return All the  working unit name whitout the specifie
	 */
	static function getWorkingUnitsName($saufUnite = '')
	{	
		$unites = UniteDeTravail::getWorkingUnits();
		foreach($unites as $unite)
		{
			if($unite->nom != $saufUnite)
			{
				$tab_unites[]=$unite->nom;
			}
		}
		if(isset($tab_unites))
			return $tab_unites;
		else return null;
	}
	
	/**
	  * @todo
	  */
	static function getWorkingUnitInfos($idWorkingUnit)
	{
		unset($infos, $info);
		$uniteDeTravail = UniteDeTravail::getWorkingUnit($idWorkingUnit);
		
		$info['nom'] = __('Niveau de risque', 'evarisk');
		$scoreRisqueUniteTravail = UniteDeTravail::getScoreRisque($idWorkingUnit);
		$info['valeur'] = UniteDeTravail::getNiveauRisque($scoreRisqueUniteTravail);
		$info['classeValeur'] = 'risque' . Risque::getSeuil($scoreRisqueUniteTravail) . 'Text risqueText' . TABLE_UNITE_TRAVAIL . $idWorkingUnit;
		$infos[] = $info;
		$info['nom'] = __('Employ&eacute;s', 'evarisk');
		$info['valeur'] = evaUserGroup::getUserNumberInWorkUnit($idWorkingUnit, TABLE_UNITE_TRAVAIL);
		$info['classeValeur'] = '';
		$infos[] = $info;
		$info['nom'] = __('Employ&eacute;s &eacute;valu&eacute;s', 'evarisk');
		$info['valeur'] = count(evaUser::getBindUsers($idWorkingUnit, TABLE_UNITE_TRAVAIL));
		$info['classeValeur'] = '';
		$infos[] = $info;
		return $infos;
	}
	
	function getScoreRisque($id)
	{
		$temp = Risque::getRisques(TABLE_UNITE_TRAVAIL, $id, "Valid");
		if($temp != null)
		{
			foreach($temp as $risque)
			{
				$risques['"' . $risque->id . "'"][] = $risque; 
			}
		}
		$scoreTotal = 0;
		$diviseur = 0;
		$scoreToReturn = 0;
		if(isset($risques) && ($risques != null))
		{
			foreach($risques as $risque)
			{
				$idMethode = $risque[0]->id_methode;
				$score = Risque::getScoreRisque($risque);
				$quotation = Risque::getEquivalenceEtalon($idMethode, $score, $risque[0]->date);
				$niveauSeuil = Risque::getSeuil($quotation);
				$scoreTotal += $quotation * ($niveauSeuil);
				$diviseur += ($niveauSeuil);
			}
			if($diviseur > 0)
			{
				$scoreToReturn = round($scoreTotal/$diviseur, 0);
			}
		}

		return $scoreToReturn;
	}
	
	static function getNiveauRisque($quotation)
	{
		return Risque::getNiveauRisque(Risque::getSeuil($quotation));
	}
	
	static function getNombreRisques($id)
	{
		return Risque::getNombreRisques(TABLE_UNITE_TRAVAIL, $id, "Valid");
	}
	
	//@todo getResponsables
	function getResponsables($id)
	{
		return null;
	}

	/**
	  * Returns the marker informations of a working unit for the google maps
	  * @param string $id Working unit identifier
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
			$workingUnit = $wpdb->get_row( "SELECT * FROM " . TABLE_UNITE_TRAVAIL . " WHERE id =" . $id);
			$address = new EvaAddress($workingUnit->id_adresse);
			$address->load();
			$geoLoc = $address->getGeoLoc();
			$scoreRisque = UniteDeTravail::getScoreRisque($workingUnit->id);
			$geoLoc['info'] = '<img class="alignleft" style="margin-right:0.5em;" src="' . EVA_WORKING_UNIT_ICON . '" alt="Unit&eacute; de travail : "/><strong>' . $workingUnit->nom . '</strong><br /><em>' . __('Risque', 'evarisk') . ' : <span class="valeurInfoElement risque' . Risque::getSeuil($scoreRisque) . 'Text">' . UniteDeTravail::getNiveauRisque($scoreRisque) . '</span></em>';
			$geoLoc['type'] = "unit&eacute; de travail"; 
			$geoLoc['image'] = GOOGLEMAPS_UNITE;
		}
		return $geoLoc;
	}
	
/*
  * Persistance
  */
  
	/**
	 * Save a new working unit.
	 * @param string $nom Working unit name.
	 * @param string $idGroupementPere Father group id.
	 */
  static function saveNewWorkingUnit($nom, $idGroupementPere)
	{
		global $wpdb;
		
		$sql = "INSERT INTO " . TABLE_UNITE_TRAVAIL . " (`nom`, `id_groupement`, `Status`) VALUES ('" . $nom . "', '" . $idGroupementPere . "', 'Valid')";
		return $wpdb->query($sql);
	}
	
	/**
	 * Update the working unit which is the identifier.
	 * @param int $id_unite Working unit identifier (not update).
	 * @param string $nom Working unit name .
	 * @param string $description Working unit description.
	 * @param string $telephone Working unit telephone.
	 * @param string $effectif Working unit effective .
	 * @param string $idAdresse Identifier of the address working unit name in the Adress Table.
	 * @param string $idGroupementPere  father group id.
	 */
	static function updateWorkingUnit($id_unite, $nom, $description, $telephone, $effectif, $idAdresse, $idGroupementPere)
	{
		global $wpdb;
		
		$sql = "UPDATE `" . TABLE_UNITE_TRAVAIL . "` SET `nom`='" . $nom . "', `description`='" . $description . "', `telephoneUnite`='" . $telephone . "', `effectif`='" . $effectif . "', `id_adresse`='" . $idAdresse . "', `id_groupement`='" . $idGroupementPere . "' WHERE `id`='" . $id_unite . "'";
		return $wpdb->query($sql);
	}
	
	/**
	  * Transfer an working unit from a group to an other
	  * @param int $idUnite Working unit to transfer identifier
	  * @param int $idGroupementPere Group which receive the transfer identifier
	  */
	static function transfertUnit($idUnite, $idGroupementPere)
	{
		global $wpdb;
		
		$sql = "UPDATE " . TABLE_UNITE_TRAVAIL . " set `id_groupement`='" . $idGroupementPere . "' WHERE `id`=" . $idUnite;
		$wpdb->query($sql);
	}
	
	/**
	  * Set the status of the  working unit wich is the identifier to Delete 
	 * @param int $id Working unit identifier
	  */
	static function deleteWorkingUnit($id)
	{
		global $wpdb;
		
		$sql = "UPDATE " . TABLE_UNITE_TRAVAIL . " set `Status`='Deleted' WHERE `id`=" . $id;
		if($wpdb->query($sql))
		{
			echo 
				'<script type="text/javascript">
					evarisk(document).ready(function(){
						evarisk("#message").addClass("updated");
						evarisk("#message").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('L\'unit&eacute; a bien &eacute;t&eacute; supprim&eacute;e', 'evarisk') . '</strong></p>') . '");
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
						evarisk("#message").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('L\'unit&eacute; n\'a pas pu &ecirc;tre supprim&eacute;e', 'evarisk') . '</strong></p>') . '");
						evarisk("#message").show();
						setTimeout(function(){
							evarisk("#message").removeClass("updated");
							evarisk("#message").hide();
						},7500);
					});
				</script>';
		}
	}
}