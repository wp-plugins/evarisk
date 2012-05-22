<?php
/**
 * This class allows to work on many activities (equivalent to many rows in data base) 
 *
 * @author Evarisk
 * @version v5.0
 */
require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/activite/evaBaseActivity.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/activite/evaActivity.class.php');

class EvaActivityTable extends evaBaseActivity
{
	/**
	 * @var array Collection of EvaActivity object indexed by identifier.
	 */
	var $activities;
	
/*
 *	Constructeur et accesseurs
 */
	/**
	 * Constructor of the EvaActivityTable class
	 * @param array $activities Collection of EvaActivity object indexed by identifier.
	 */
	function EvaActivityTable($activities = null)
	{
		$this->activities = $activities;
		unset($this->id);
		unset($this->relatedTaskId);
		unset($this->name);
		unset($this->description);
		unset($this->startDate);
		unset($this->finishDate);
		unset($this->progression);
		unset($this->cout);
		unset($this->place);
		unset($this->firstInsert);
		unset($this->idCreateur);
		unset($this->idResponsable);
		unset($this->idSoldeur);
		unset($this->idSoldeurChef);
		unset($this->ProgressionStatus);
		unset($this->dateSolde);
		unset($this->idPhotoAvant);
		unset($this->idPhotoApres);
	}
	
	/**
	 * Returns the collection of activities
	 * @return array The collection of activities indexed by their identifier
	 */
	function getActivities()
	{
		return $this->activities;
	}
	
	/**
	 * Add a activity to the collection
	 * @param EvaActivity Activity to add to the collection
	 */
	function addActivity($activity)
	{
		$this->activities[$activity->getId()] = $activity;
	}
	
	/**
	 * Remove a activity from the collection
	 * @param EvaActivity Activity to remove from the collection
	 * @return EvaActivity The activity removed
	 */
	function removeActivity($activityId)
	{
		$activity = $this->activities[$activityId];
		unset($this->activities[$activityId]);
		return $activity;
	}
	
	/**
	 * Remove all activities from the collection
	 */
	function removeAllActivities()
	{
		unset($this->activities);
	}
	
	/**
	 * Get the activities like the one use to call (id and left and right limit are non use because they are unique)
	 * @param EvaActivity Activity use to compare
	 */
	function getActivitiesLike($activity)
	{
		global $wpdb;
		
		$this->removeAllActivities();
		
		{//Variables cleaning
			$name = digirisk_tools::IsValid_Variable($activity->getName());
			$relatedTaskId = (int) digirisk_tools::IsValid_Variable($activity->getRelatedTaskId());
			$description = digirisk_tools::IsValid_Variable($activity->getDescription());
			$startDate = digirisk_tools::IsValid_Variable($activity->getStartDate());
			$finishDate = digirisk_tools::IsValid_Variable($activity->getFinishDate());
			$place = digirisk_tools::IsValid_Variable($activity->getPlace());
			$cout = (int) digirisk_tools::IsValid_Variable($activity->getCout());
			$progression = (int) digirisk_tools::IsValid_Variable($activity->getProgression());
			$status = digirisk_tools::IsValid_Variable($activity->getStatus());
			$idCreateur = digirisk_tools::IsValid_Variable($activity->getidCreateur());
			$idResponsable = digirisk_tools::IsValid_Variable($activity->getidResponsable());
			$idSoldeur = digirisk_tools::IsValid_Variable($activity->getidSoldeur());
			$idSoldeurChef = digirisk_tools::IsValid_Variable($activity->getidSoldeurChef());
			$idPhotoAvant = digirisk_tools::IsValid_Variable($activity->getidPhotoAvant());
			$idPhotoApres = digirisk_tools::IsValid_Variable($activity->getidPhotoApres());
			$ProgressionStatus = digirisk_tools::IsValid_Variable($activity->ProgressionStatus());
			$dateSolde = digirisk_tools::IsValid_Variable($activity->dateSolde());
		}
		{//Query creation
			$sql = "SELECT * FROM " . TABLE_TACHE . " WHERE 1";
			if($name != '')
			{
				$sql = $sql . " AND " . self::name . " = '" . mysql_real_escape_string($name) . "'";
			}
			if($relatedTaskId != 0)
			{
				$sql = $sql . " AND " . self::relatedTaskId . " = " . mysql_real_escape_string($relatedTaskId);
			}
			if($description != '')
			{
				$sql = $sql . " AND " . self::description . " = '" . mysql_real_escape_string($description) . "'";
			}
			if($startDate != '' && $startDate != '0000-00-00')
			{
				$sql = $sql . " AND " . self::startDate . " = '" . mysql_real_escape_string($startDate) . "'";
			}
			if($finishDate != '' && $finishDate != '0000-00-00')
			{
				$sql = $sql . " AND " . self::finishDate . " = '" . mysql_real_escape_string($finishDate) . "'";
			}
			if($place != '')
			{
				$sql = $sql . " AND " . self::place . " = '" . mysql_real_escape_string($place) . "'";
			}
			if($cout != '')
			{
				$sql = $sql . " AND " . self::cout . " = '" . mysql_real_escape_string($cout) . "'";
			}
			if($progression != 0)
			{
				$sql = $sql . " AND " . self::progression . " = " . mysql_real_escape_string($progression);
			}
			if($status != '')
			{
				$sql = $sql . " AND " . self::status . " = '" . mysql_real_escape_string($status) . "'";
			}
			if($idCreateur != '')
      {
        $sql = $sql . " AND " . self::idCreateur . " = '" . mysql_real_escape_string($idCreateur) . "'";
      }
			if($idSoldeur != '')
      {
        $sql = $sql . " AND " . self::idSoldeur . " = '" . mysql_real_escape_string($idSoldeur) . "'";
      }
			if($idSoldeurChef != '')
      {
        $sql = $sql . " AND " . self::idSoldeurChef . " = '" . mysql_real_escape_string($idSoldeurChef) . "'";
      }
			if($idResponsable != '')
      {
        $sql = $sql . " AND " . self::idResponsable . " = '" . mysql_real_escape_string($idResponsable) . "'";
      }
			if($ProgressionStatus != '')
      {
        $sql = $sql . " AND " . self::ProgressionStatus . " = '" . mysql_real_escape_string($ProgressionStatus) . "'";
      }
			if($dateSolde != '')
      {
        $sql = $sql . " AND " . self::dateSolde . " = '" . mysql_real_escape_string($dateSolde) . "'";
      }			
			if($idPhotoAvant != '')
      {
        $sql = $sql . " AND " . self::idPhotoAvant . " = '" . mysql_real_escape_string($idPhotoAvant) . "'";
      }			
			if($idPhotoApres != '')
      {
        $sql = $sql . " AND " . self::idPhotoApres . " = '" . mysql_real_escape_string($idPhotoApres) . "'";
      }
		}
		
		$wpdbActivities = $wpdb->get_results($sql);
		
		foreach($wpdbActivities as $wpdbActivity)
		{
			$activity = new EvaActivity();
			$activity->convertWpdb($wpdbActivity);
			$this->addActivity($activity);
		}
	}
}