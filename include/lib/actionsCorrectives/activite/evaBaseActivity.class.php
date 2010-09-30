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
	const cost = 'cout';
	const place = 'lieu';
	const progression = 'avancement';
	const status = 'Status';
	const firstInsert = 'firstInsert';

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
	 * @var string The activity cost
	 */
	var $cost;
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
	 * @param float $cost The cost to set
	 * @param int $progression The progression to set
	 * @param string $status The status to set
	 * @param date $firstInsert The insert date to set
	 */
	function EvaBaseactivity($id = null, $relatedTaskId = null, $name = '',	$description = '', $startDate = '', $finishDate = '', $place = '', $cost = '', $progression = 0, $status = 'Valid', $firstInsert = '')
	{
		$this->id = $id;
		$this->relatedTaskId = $relatedTaskId;
		$this->name = $name;
		$this->description = $description;
		$this->startDate = $startDate;
		$this->finishDate = $finishDate;
		$this->place = $place;
		$this->cost = $cost;
		$this->progression = $progression;
		$this->status = $status;
		$this->status = $status;
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
	 * Returns the activity cost
	 * @return float The cost
	 */
	function getCost()
	{
		return $this->cost;
	}

	/**
	 * Set the activity cost
	 * @param float $cost The cost to set
	 */
	function setCost($cost)
	{
		$this->cost = $cost;
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
		$this->setProgression($wpdbActivity->avancement);
		$this->setStatus($wpdbActivity->Status);
		$this->setFirstInsert($wpdbActivity->firstInsert);
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
					self::progression => $this->getProgression(),
					self::status => $this->getStatus(),
					self::firstInsert => $this->getFirstInsert()
				)
			);
	}
}