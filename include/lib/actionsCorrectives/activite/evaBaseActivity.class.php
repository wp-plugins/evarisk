<?php
/**
 * Class to represent an activity directly connect with the data base
 *
 * @author Evarisk
 * @version v5.0
 */
require_once(EVA_CONFIG);

class EvaBaseActivity
{
/*
 * Data base rows names
 */
	const id = 'id';
	const name = 'nom';
	const relatedTaskId = 'id_tache';
	const description = 'description';
	const startDate = 'dateDebut';
	const finishDate = 'dateFin';
	const cout = 'cout';
	const place = 'lieu';
	const progression = 'avancement';
	const status = 'Status';
	const firstInsert = 'firstInsert';
	const idCreateur = 'idCreateur';
	const idResponsable = 'idResponsable';
	const idSoldeur = 'idSoldeur';
	const idSoldeurChef = 'idSoldeurChef';
	const ProgressionStatus = 'ProgressionStatus';
	const dateSolde = 'dateSolde';
	const idPhotoAvant = 'idPhotoAvant';
	const idPhotoApres = 'idPhotoApres';
	const nom_exportable_plan_action = 'nom_exportable_plan_action';
	const description_exportable_plan_action = 'description_exportable_plan_action';

/*
 * Class variable define
 */

	/**
	 * @var int The activity identifier
	 */
	var $id;
	/**
	 * @var int The related task identifier
	 */
	var $relatedTaskId;
	/**
	 * @var string The activity name
	 */
	var $name;
	/**
	 * @var string The activity description
	 */
	var $description;
	/**
	 * @var date The activity start date
	 */
	var $startDate;
	/**
	 * @var date The activity finish date
	 */
	var $finishDate;
	/**
	 * @var string The activity place
	 */
	var $place;
	/**
	 * @var string The activity cout
	 */
	var $cout;
	/**
	 * @var string The activity progression
	 */
	var $progression;
	/**
	 * @var string The activity status
	 */
	var $status;
	/**
	 * @var string The activity insert date
	 */
	var $firstInsert;
	/**
	 * @var string The task creator id
	 */
	var $idCreateur;
	/**
	 * @var string The person in charge of the task
	 */
	var $idResponsable;
	/**
	 * @var string The task maker
	 */
	var $idSoldeur;
	/**
	 * @var string The task maker
	 */
	var $idSoldeurChef;
	/**
	 * @var string The progression status
	 */
	var $ProgressionStatus;
	/**
	 * @var string The date the action was mark as done
	 */
	var $dateSolde;
	/**
	 * @var string picture before action
	 */
	var $idPhotoAvant;
	/**
	 * @var string picture after action
	 */
	var $idPhotoApres;
	var $nom_exportable_plan_action;
	var $description_exportable_plan_action;
	
/*
 *	Constructeur et accesseurs
 */
	/**
	 * Constructor of the activity class
	 * @param int $id The identifier to set
	 * @param int $relatedTaskId The related task identifier to set
	 * @param string $name The name to set
	 * @param string $description The description to set
	 * @param date $startDate The start date to set
	 * @param date $finishDate The finish date to set
	 * @param string $place The place to set
	 * @param float $cout The cout to set
	 * @param int $progression The progression to set
	 * @param string $status The status to set
	 * @param date $firstInsert The insert date to set
	 * @param integer $idCreateur The task creator identifier
	 * @param integer $idResponsable The identifier of the person in charge of the task
	 * @param integer $idSoldeur The task maker
	 * @param integer $idPhotoAvant The task maker
	 * @param integer $idPhotoApres The task maker
	 * @param string $ProgressionStatus The progression status
	 * @param date $dateSolde The date the action was mark as done
	 */
	function EvaBaseactivity($id = null, $relatedTaskId = null, $name = '',	$description = '', $startDate = '', $finishDate = '', $place = '', $cout = '', $progression = 0, $status = 'Valid', $firstInsert = '', $idCreateur ='', $idResponsable ='', $idSoldeur ='', $idSoldeurChef ='',  $idPhotoAvant ='',  $idPhotoApres ='', $ProgressionStatus ='', $nom_exportable_plan_action ='', $description_exportable_plan_action ='', $dateSolde ='')
	{
		$this->id = $id;
		$this->relatedTaskId = $relatedTaskId;
		$this->name = $name;
		$this->description = $description;
		$this->startDate = $startDate;
		$this->finishDate = $finishDate;
		$this->place = $place;
		$this->cout = $cout;
		$this->progression = $progression;
		$this->status = $status;
		$this->idCreateur = $idCreateur;
		$this->idResponsable = $idResponsable;
		$this->idSoldeur = $idSoldeur;
		$this->idSoldeurChef = $idSoldeurChef;
		$this->idPhotoAvant = $idPhotoAvant;
		$this->idPhotoApres = $idPhotoApres;
		$this->ProgressionStatus = $ProgressionStatus;
		$this->dateSolde = $dateSolde;
		$this->nom_exportable_plan_action = $nom_exportable_plan_action;
		$this->description_exportable_plan_action = $description_exportable_plan_action;
	}
	
	/**
	 * Returns the activity identifier
	 * @return int The identifier
	 */
	function getId()
	{
		return $this->id;
	}
	
	/**
	 * Set the activity identifier
	 * @param int $id The identifier to set
	 */
	function setId($id)
	{
		$this->id = $id;
	}
	
	/**
	 * Returns the activity related task identifier
	 * @return int The related task identifier
	 */
	function getRelatedTaskId()
	{
		return $this->relatedTaskId;
	}
	
	/**
	 * Set the activity related task identifier
	 * @param int $id The related task identifier to set
	 */
	function setRelatedTaskId($relatedTaskId)
	{
		$this->relatedTaskId = $relatedTaskId;
	}
	
	/**
	 * Returns the activity name
	 * @return string The name
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 * Set the activity name
	 * @param string $name The name to set
	 */
	function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * Returns the activity second lige
	 * @return string The second lige
	 */
	function getDescription()
	{
		return $this->description;
	}

	/**
	 * Set the activity description
	 * @param string $description The description to set
	 */
	function setDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * Returns the activity start date
	 * @return date The start date
	 */
	function getStartDate()
	{
		return $this->startDate;
	}

	/**
	 * Set the activity start date
	 * @param date $startDate The start date to set
	 */
	function setStartDate($startDate)
	{
		$this->startDate = $startDate;
	}

	/**
	 * Returns the activity finish date
	 * @return date The finish date
	 */
	function getFinishDate()
	{
		return $this->finishDate;
	}

	/**
	 * Set the activity finish date
	 * @param date $finishDate The finish date to set
	 */
	function setFinishDate($finishDate)
	{
		$this->finishDate = $finishDate;
	}

	/**
	 * Returns the activity place
	 * @return string The place
	 */
	function getPlace()
	{
		return $this->place;
	}

	/**
	 * Set the activity place
	 * @param string $place The place to set
	 */
	function setPlace($place)
	{
		$this->place = $place;
	}

	/**
	 * Returns the activity progression
	 * @return int The progression
	 */
	function getProgression()
	{
		return $this->progression;
	}

	/**
	 * Set the activity progression
	 * @param int $progression The progression to set
	 */
	function setProgression($progression)
	{
		$this->progression = $progression;
	}

	/**
	 * Returns the activity cout
	 * @return float The cout
	 */
	function getCout()
	{
		return $this->cout;
	}

	/**
	 * Set the activity cout
	 * @param float $cout The cout to set
	 */
	function setCout($cout)
	{
		$this->cout = $cout;
	}

	/**
	 * Returns the activity status
	 * @return string The status
	 */
	function getStatus()
	{
		return $this->status;
	}

	/**
	 * Set the activity status
	 * @param string $status The status to set
	 */
	function setStatus($status)
	{
		$this->status = $status;
	}

	/**
	 * Returns the activity insert date
	 * @return date The activity insert date
	 */
	function getFirstInsert()
	{
		return $this->firstInsert;
	}

	/**
	 * Returns the activity insert date
	 * @param date $firstInsert The date to set
	 */
	function setFirstInsert($firstInsert)
	{
		$this->firstInsert = $firstInsert;
	}

	/**
	 * Returns the action creator id
	 * @return integer The action creator id
	 */
	function getidCreateur()
	{
		return $this->idCreateur;
	}

	/**
	 * Returns the action creator id
	 * @param integer $idCreateur The action creator id
	 */
	function setidCreateur($idCreateur)
	{
		$this->idCreateur = $idCreateur;
	}
	
	/**
	 * Returns the action maker id
	 * @return integer The action maker id
	 */
	function getidSoldeur()
	{
		return $this->idSoldeur;
	}

	/**
	 * Returns the action maker id
	 * @param integer $idSoldeur The action maker id
	 */
	function setidSoldeur($idSoldeur)
	{
		$this->idSoldeur = $idSoldeur;
	}
	
	/**
	 * Returns the action maker id
	 * @return integer The action maker id
	 */
	function getidSoldeurChef()
	{
		return $this->idSoldeurChef;
	}

	/**
	 * Returns the action maker id
	 * @param integer $idSoldeurChef The action maker id
	 */
	function setidSoldeurChef($idSoldeurChef)
	{
		$this->idSoldeurChef = $idSoldeurChef;
	}

	/**
	 * Returns the identifier of the person in charge of the action
	 * @return integer The identifier of the person in charge of the action
	 */
	function getidResponsable()
	{
		return $this->idResponsable;
	}

	/**
	 * Returns the identifier of the person in charge of the action
	 * @param integer $idResponsable The identifier of the person in charge of the action
	 */
	function setidResponsable($idResponsable)
	{
		$this->idResponsable = $idResponsable;
	}

	/**
	 * Returns the progression status, if the action is done or not
	 * @return string The progression status
	 */
	function getProgressionStatus()
	{
		return $this->ProgressionStatus;
	}

	/**
	 * Returns The progression status of the action
	 * @param string $ProgressionStatus The progression status of the action
	 */
	function setProgressionStatus($ProgressionStatus)
	{
		$this->ProgressionStatus = $ProgressionStatus;
	}

	/**
	 * Returns The date that the action was mark as done
	 * @return date the date
	 */
	function getdateSolde()
	{
		return $this->dateSolde;
	}

	/**
	 * Returns The date that the action was mark as done
	 * @param string $dateSolde The date
	 */
	function setdateSolde($dateSolde)
	{
		$this->dateSolde = $dateSolde;
	}
	
	/**
	 * Returns The date that the action was mark as done
	 * @return date the date
	 */
	function getidPhotoAvant()
	{
		return $this->idPhotoAvant;
	}

	/**
	 * Returns The date that the action was mark as done
	 * @param string $dateSolde The date
	 */
	function setidPhotoAvant($idPhotoAvant)
	{
		$this->idPhotoAvant = $idPhotoAvant;
	}
	
	/**
	 * Returns The date that the action was mark as done
	 * @return date the date
	 */
	function getidPhotoApres()
	{
		return $this->idPhotoApres;
	}

	/**
	 * Returns The date that the action was mark as done
	 * @param string $dateSolde The date
	 */
	function setidPhotoApres($idPhotoApres)
	{
		$this->idPhotoApres = $idPhotoApres;
	}
	
	/**
	 * Returns The date that the action was mark as done
	 * @return date the date
	 */
	function getnom_exportable_plan_action()
	{
		return $this->nom_exportable_plan_action;
	}

	/**
	 * Returns The date that the action was mark as done
	 * @param string $dateSolde The date
	 */
	function setnom_exportable_plan_action($nom_exportable_plan_action)
	{
		$this->nom_exportable_plan_action = $nom_exportable_plan_action;
	}
	/**
	 * Returns The date that the action was mark as done
	 * @return date the date
	 */
	function getdescription_exportable_plan_action()
	{
		return $this->description_exportable_plan_action;
	}

	/**
	 * Returns The date that the action was mark as done
	 * @param string $dateSolde The date
	 */
	function setdescription_exportable_plan_action($description_exportable_plan_action)
	{
		$this->description_exportable_plan_action = $description_exportable_plan_action;
	}
	
/*
 * Others methods
 */

	/**
	 * Convert a wpdb object to an EvaBaseActivity object
	 * @param object $wpdbActivity The object to convert
	 */
	function convertWpdb($wpdbActivity)
	{
		$this->setId($wpdbActivity->id);
		$this->setRelatedTaskId($wpdbActivity->id_tache);
		$this->setName($wpdbActivity->nom);
		$this->setDescription($wpdbActivity->description);
		$this->setStartDate($wpdbActivity->dateDebut);
		$this->setFinishDate($wpdbActivity->dateFin);
		$this->setPlace($wpdbActivity->lieu);
		$this->setCout($wpdbActivity->cout);
		$this->setProgression($wpdbActivity->avancement);
		$this->setStatus($wpdbActivity->Status);
		$this->setFirstInsert($wpdbActivity->firstInsert);
		$this->setidCreateur($wpdbActivity->idCreateur);
		$this->setidSoldeur($wpdbActivity->idSoldeur);
		$this->setidResponsable($wpdbActivity->idResponsable);
		$this->setProgressionStatus($wpdbActivity->ProgressionStatus);
		$this->setdateSolde($wpdbActivity->dateSolde);
		$this->setidPhotoAvant($wpdbActivity->idPhotoAvant);
		$this->setidPhotoApres($wpdbActivity->idPhotoApres);
		$this->setnom_exportable_plan_action($wpdbActivity->nom_exportable_plan_action);
		$this->setdescription_exportable_plan_action($wpdbActivity->description_exportable_plan_action);
	}

	/**
	 * Convert an EvaBaseActivity object to a wpdb object
	 * @return object The converted object
	 */
	function convertToWpdb()
	{
		return 
			(object)(
				array(
					self::id => $this->getId(),
					self::relatedTaskId => $this->getRelatedTaskId(),
					self::name => $this->getName(),
					self::description => $this->getDescription(),
					self::startDate => $this->getStartDate(),
					self::finishDate => $this->getFinishDate(),
					self::place => $this->getPlace(),
					self::cout => $this->getcout(),
					self::progression => $this->getProgression(),
					self::status => $this->getStatus(),
					self::idCreateur => $this->getidCreateur(),
					self::idSoldeur => $this->getidSoldeur(),
					self::idPhotoAvant => $this->getidPhotoAvant(),
					self::idPhotoApres => $this->getidPhotoApres(),
					self::idResponsable => $this->getidResponsable(),
					self::firstInsert => $this->getFirstInsert(),
					self::ProgressionStatus => $this->ProgressionStatus(),
					self::nom_exportable_plan_action => $this->getnom_exportable_plan_action(),
					self::description_exportable_plan_action => $this->getdescription_exportable_plan_action(),
					self::dateSolde => $this->dateSolde()
				)
			);
	}
}