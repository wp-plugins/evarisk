<?php
/**
 * This class allows to work on single activity (equivalent to single row in data base) 
 *
 * @author Evarisk
 * @version v5.0
 */
require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/activite/evaBaseActivity.class.php');

class EvaActivity extends EvaBaseActivity
{
	
/*
 * Data base link
 */

	/**
	 * Save or update the activity in data base
	 */
	function save()
	{
		global $wpdb;
		
		{//Variables cleaning
			$id = (int) eva_tools::IsValid_Variable($this->getId());
			$relatedTaskId = (int) eva_tools::IsValid_Variable($this->getRelatedTaskId());
			$name = eva_tools::IsValid_Variable($this->getName());
			$description = eva_tools::IsValid_Variable($this->getDescription());
			$startDate = eva_tools::IsValid_Variable($this->getStartDate());
			$finishDate = eva_tools::IsValid_Variable($this->getFinishDate());
			$place = eva_tools::IsValid_Variable($this->getPlace());
			$cost = (float) eva_tools::IsValid_Variable($this->getCost());
			$progression = (int) eva_tools::IsValid_Variable($this->getProgression());
			$status = eva_tools::IsValid_Variable($this->getStatus());
		}
		
		//Query creation
		if($id == 0)
		{// Insert in data base
			$sql = "INSERT INTO " . TABLE_ACTIVITE . " (`" . self::relatedTaskId . "`, `" . self::name . "`, `" . self::description . "`, `" . self::startDate . "`,	`" . self::finishDate . "`, `" . self::place . "`, `" . self::cost . "`, `" . self::progression . "`, `" . self::status . "`, `" . self::firstInsert . "`)
				VALUES ('" . mysql_real_escape_string($relatedTaskId) . "', 
								'" . mysql_real_escape_string($name) . "', 
								'" . mysql_real_escape_string($description) . "', 
								'" . mysql_real_escape_string($startDate) . "', 
								'" . mysql_real_escape_string($finishDate) . "',
								'" . mysql_real_escape_string($place) . "', 
								'" . mysql_real_escape_string($cost) . "', 
								'" . mysql_real_escape_string($progression) . "', 
								'" . mysql_real_escape_string($status) . "', 
								'" . date("Y-m-d H:i:s") . "')";
		}
		else
		{//Update of the data base
			$sql = "UPDATE " . TABLE_ACTIVITE . " set 
				`" . self::relatedTaskId . "` = '" . mysql_real_escape_string($relatedTaskId) . "', 
				`" . self::name . "` = '" . mysql_real_escape_string($name) . "', 
				`" . self::description . "` = '" . mysql_real_escape_string($description) . "',
				`" . self::startDate . "` = '" . mysql_real_escape_string($startDate) . "',
				`" . self::finishDate . "` = '" . mysql_real_escape_string($finishDate) . "',
				`" . self::place . "` = '" . mysql_real_escape_string($place) . "',
				`" . self::cost . "` = '" . mysql_real_escape_string($cost) . "',
				`" . self::progression . "` = '" . mysql_real_escape_string($progression) . "',
				`" . self::status . "` = '" . mysql_real_escape_string($status) . "' 
			WHERE `" . self::id . "` = " . mysql_real_escape_string($id);
		}

		//Query execution
		/* We use identity (===) because query can return both, 0 and false
		 * if 0 is return, their is no change but no trouble in database
	 	 */
		if($wpdb->query($sql) === false)
		{//Their is some troubles
			$this->setStatus('error');
		}
		else
		{//Their is no trouble
			$id = $wpdb->insert_id;
			if($this->getId() == null)
			{
				$this->setId($id);
			}
		}
	}
	
	/**
	 * Load the activity with identifier key
	 */
	function load()
	{
		global $wpdb;
		$id = (int) eva_tools::IsValid_Variable($this->getId());
		if($id != 0)
		{
			$wpdbActivity = $wpdb->get_row( "SELECT * FROM " . TABLE_ACTIVITE . " WHERE " . self::id . " = " . $id);
			
			if($wpdbActivity != null)
			{
				$this->convertWpdb($wpdbActivity);
			}
		}
	}
	
/*
 * Others methods
 */

	/**
	 * Transfert an activity from one task to another.
	 * @param int $newRelatedTaskId New relative task identifier
	 */
	function transfert($newRelatedTaskId)
	{
		global $wpdb;
		$wpdb->query("UPDATE " . TABLE_ACTIVITE . " SET " . self::relatedTaskId . " = " . $newRelatedTaskId . " WHERE " . self::id . " = " . $this->getId());
	}
}