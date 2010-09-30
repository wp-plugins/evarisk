<?php
/**
 * This class allows to work on single task (equivalent to single row in data base) 
 *
 * @author Evarisk
 * @version v5.0
 */
require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/tache/evaBaseTask.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/tache/evaTaskTable.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/activite/evaActivity.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/activite/evaActivityTable.class.php');

class EvaTask extends EvaBaseTask
{	
/*
 * Data base link
 */
	/**
	 * Save or update the Task in data base
	 */
	function save()
	{
		global $wpdb;
		
		{//Variables cleaning
			$id = (int) eva_tools::IsValid_Variable($this->getId());
			$name = eva_tools::IsValid_Variable($this->getName());
			$leftLimit = (int) eva_tools::IsValid_Variable($this->getLeftLimit());
			$rightLimit = (int) eva_tools::IsValid_Variable($this->getRightLimit());
			$description = eva_tools::IsValid_Variable($this->getDescription());
			$startDate = eva_tools::IsValid_Variable($this->getStartDate());
			$finishDate = eva_tools::IsValid_Variable($this->getFinishDate());
			$place = eva_tools::IsValid_Variable($this->getPlace());
			$progression = (int) eva_tools::IsValid_Variable($this->getProgression());
			$cost = (int) eva_tools::IsValid_Variable($this->getCost());
			$idFrom = (int) eva_tools::IsValid_Variable($this->getIdFrom());
			$tableFrom = eva_tools::IsValid_Variable($this->getTableFrom());
			$status = eva_tools::IsValid_Variable($this->getStatus());
		}
		
		//Query creation
		if($id == 0)
		{// Insert in data base
			$sql = "INSERT INTO " . TABLE_TACHE . " (`" . self::name . "`, `" . self::leftLimit . "`, `" . self::rightLimit . "`, `" . self::description . "`, `" . self::startDate . "`,	`" . self::finishDate . "`, `" . self::place . "`, `" . self::progression . "`, `" . self::cost . "`, `" . self::idFrom . "`, `" . self::tableFrom . "`, `" . self::status . "`, `" . self::firstInsert . "`)
				VALUES ('" . mysql_real_escape_string($name) . "', 
								'" . mysql_real_escape_string($leftLimit) . "', 
								'" . mysql_real_escape_string($rightLimit) . "', 
								'" . mysql_real_escape_string($description) . "', 
								'" . mysql_real_escape_string($startDate) . "', 
								'" . mysql_real_escape_string($finishDate) . "',
								'" . mysql_real_escape_string($place) . "', 
								'" . mysql_real_escape_string($progression) . "', 
								'" . mysql_real_escape_string($cost) . "', 
								'" . mysql_real_escape_string($idFrom) . "', 
								'" . mysql_real_escape_string($tableFrom) . "', 
								'" . mysql_real_escape_string($status) . "', 
								'" . date("Y-m-d H:i:s") . "')";
		}
		else
		{//Update of the data base
			$sql = "UPDATE " . TABLE_TACHE . " set 
				`" . self::name . "` = '" . mysql_real_escape_string($name) . "', 
				`" . self::leftLimit . "` = '" . mysql_real_escape_string($leftLimit) . "', 
				`" . self::rightLimit . "` = '" . mysql_real_escape_string($rightLimit) . "', 
				`" . self::description . "` = '" . mysql_real_escape_string($description) . "',
				`" . self::startDate . "` = '" . mysql_real_escape_string($startDate) . "',
				`" . self::finishDate . "` = '" . mysql_real_escape_string($finishDate) . "',
				`" . self::place . "` = '" . mysql_real_escape_string($place) . "',
				`" . self::progression . "` = '" . mysql_real_escape_string($progression) . "',
				`" . self::cost . "` = '" . mysql_real_escape_string($cost) . "',
				`" . self::idFrom . "` = '" . mysql_real_escape_string($idFrom) . "',
				`" . self::tableFrom . "` = '" . mysql_real_escape_string($tableFrom) . "',
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
	 * Load the Task with identifier key
	 */
	function load()
	{
		global $wpdb;
		
		$id = (int) eva_tools::IsValid_Variable($this->getId());
		if($id != 0)
		{
			$wpdbTask = $wpdb->get_row( "SELECT * FROM " . TABLE_TACHE . " WHERE " . self::id . " = " . $id);
			
			if($wpdbTask != null)
			{
				$this->convertWpdb($wpdbTask);
			}
		}
	}

/*
 * Others methods
 */
	/**
	 * Get the activities depend on the task
	 * @return object The activities in the form of wpdb object
	 */
	function getWPDBActivitiesDependOn()
	{
		global $wpdb;
		
		$query = $wpdb->prepare("
			SELECT *
			FROM " . TABLE_ACTIVITE . "
			WHERE " . EvaActivity::relatedTaskId . " = %d
				AND " . EvaActivity::status . " = 'Valid'", $this->getId()
		);
		return $wpdb->get_results($query);
	}

	/**
	 * Get the activities depend on the task
	 * @return EvaActivity The activities in the form of EvaActivity object
	 */
	function getActivitiesDependOn()
	{
		$wpdbActivities = $this->getWPDBActivitiesDependOn();
		$activities = new EvaActivityTable();
		foreach($wpdbActivities as $wpdbActivity)
		{
			$activity = new EvaActivity();
			$activity->convertWpdb($wpdbActivity);
			$activities->addActivity($activity);
		}
		return $activities;
	}

	/**
	 * Transfert a (sub-)task from one task to another.
	 * @param int $newMotherTaskId New mother task identifier
	 */
	function transfert($newMotherTaskId)
	{
		$rootTask = new EvaTask(1);
		$rootTask->load();
		$wpdbRootTask = $rootTask->convertToWpdb();
		$wpdbDaughterTask = $this->convertToWpdb();
		$motherTask = new EvaTask($newMotherTaskId);
		$motherTask->load();
		$wpdbMotherTask = $motherTask->convertToWpdb();
		Arborescence::deplacerElements(TABLE_TACHE, $wpdbRootTask, $wpdbDaughterTask, $wpdbMotherTask);
	}

	/**
	 * Get the descendants of the task.
	 * @return EvaTaskTable The descendants tasks
	 */
	function getDescendants()
	{
		$wpdbTask = $this->convertToWpdb();
		$wpdbTasks = Arborescence::getDescendants(TABLE_TACHE, $wpdbTask, $where = 1, $order= "limiteGauche ASC");
		$descendants = new EvaTaskTable();
		$descendants->removeAllTasks();
		
		foreach($wpdbTasks as $wpdbTask)
		{
			$task = new EvaTask();
			$task->convertWpdb($wpdbTask);
			$descendants->addTask($task);
		}
		
		return $descendants;
	}

	/**
	 * Get the level of the task in the tree.
	 * @return int The level.
	 */
	function getLevel()
	{
		$wpdbTask = $this->convertToWpdb();
		$ancestors = Arborescence::getAncetre(TABLE_TACHE, $wpdbTask);
		/* The root is not a real task.
		 * It is here to coagulate the table.
		 * So we must reduce the count by one.*/
		return count($ancestors) - 1;
	}

	/**
	 * Comput and set the Task start and finish dates
	 */
	function getTimeWindow()
	{
		$TasksAndSubTasks = $this->getDescendants();
		$TasksAndSubTasks->addTask($this);
		$TasksAndSubTasks = $TasksAndSubTasks->getTasks();
		$startDate = '9999-12-31';
		$finishDate = '1-1-1';
		if($TasksAndSubTasks != null AND count($TasksAndSubTasks) > 0)
		{
			foreach($TasksAndSubTasks as $task)
			{
				$activities = $task->getActivitiesDependOn();
				$activities = $activities->getActivities();
				if($activities != null AND count($activities) > 0)
				{
					foreach($activities as $activity)
					{
						$date = explode('-', $startDate);
						$startYear = $date[0];
						$startMonth = $date[1];
						$startDay = $date[2];
						$date = explode('-', $finishDate);
						$finishYear = $date[0];
						$finishMonth = $date[1];
						$finishDay = $date[2];
						$activityStartDate = $activity->getStartDate();
						$date = explode('-', $activityStartDate);
						$activityStartYear = $date[0];
						$activityStartMonth = $date[1];
						$activityStartDay = $date[2];
						$activityFinishDate = $activity->getFinishDate();
						$date = explode('-', $activityFinishDate);
						$activityFinishYear = $date[0];
						$activityFinishMonth = $date[1];
						$activityFinishMonth = $date[2];
						echo $activityFinishDay;
						if(($activityStartYear < $startYear) 
							OR ($activityStartYear == $startYear AND $activityStartMonth < $startMonth) 
							OR ($activityStartYear == $startYear AND $activityStartMonth == $startMonth AND $activityStartDay < $startDay))
						{
							$startDate = $activity->getStartDate();
						}
						if(($activityFinishYear > $finishYear) 
							OR ($activityFinishYear == $finishYear AND $activityFinishMonth > $finishMonth) 
							OR ($activityFinishYear == $finishYear AND $activityFinishMonth == $finishMonth AND $activityFinishDay > $finishDay))
						{
							$finishDate = $activity->getFinishDate();
						}
					}
				}
			}
		}
		$this->setStartDate($startDate);
		$this->setFinishDate($finishDate);
	}

	/**
	 * Compute and set the Task progression
	 */
	function computeProgression()
	{
		$TasksAndSubTasks = $this->getDescendants();
		$TasksAndSubTasks->addTask($this);
		$TasksAndSubTasks = $TasksAndSubTasks->getTasks();
		$taskDuration = 0;
		$taskCompleteDuration = 0;
		if($TasksAndSubTasks != null AND count($TasksAndSubTasks) > 0)
		{
			foreach($TasksAndSubTasks as $task)
			{
				$activities = $task->getActivitiesDependOn();
				$activities = $activities->getActivities();
				if($activities != null AND count($activities) > 0)
				{
					foreach($activities as $activity)
					{
						$activityStartDate = $activity->getStartDate();
						$startDate = explode('-', $activityStartDate);
						$activityFinishDate = $activity->getFinishDate();
						$finishDate = explode('-', $activityFinishDate);
						$activiteDuration = mktime(0,0,0,$finishDate[1],$finishDate[2],$finishDate[0]) - mktime(0,0,0,$startDate[1], $startDate[2],$startDate[0]);
						$taskDuration = $taskDuration + $activiteDuration;
						$taskCompleteDuration = $taskCompleteDuration + $activiteDuration * $activity->getProgression() / 100;
					}
				}
			}
		}
		$this->setProgression(round($taskCompleteDuration / $taskDuration * 100));
	}
}