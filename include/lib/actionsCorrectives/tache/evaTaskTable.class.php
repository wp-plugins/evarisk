<?php
/**
 * This class allows to work on many tasks (equivalent to many rows in data base) 
 *
 * @author Evarisk
 * @version v5.0
 */
require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/tache/evaBaseTask.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/tache/evaTask.class.php');

class EvaTaskTable extends EvaBaseTask
{
	/**
	 * @var array Collection of evaTask object indexed by identifier.
	 */
	var $tasks;
  
/*
 *	Constructeur et accesseurs
 */
	/**
	 * Constructor of the EvaTaskTable class
	 * @param array $tasks Collection of evaTask object indexed by identifier.
	 */
	function EvaTaskTable($tasks = array())
	{
		$this->tasks = $tasks;
		unset($this->id);
		unset($this->leftLimit);
		unset($this->rightLimit);
		unset($this->name);
		unset($this->description);
		unset($this->startDate);
		unset($this->finishDate);
		unset($this->progression);
		unset($this->cost);
		unset($this->place);
		unset($this->idFrom);
		unset($this->tableFrom);
		unset($this->firstInsert);
		unset($this->idCreateur);
		unset($this->idResponsable);
		unset($this->idSoldeur);
		unset($this->idSoldeurChef);
		unset($this->ProgressionStatus);
		unset($this->dateSolde);
	}
  
	/**
	 * Returns the collection of tasks
	 * @return array The collection of tasks indexed by their identifier
	 */
	function getTasks()
	{
		return $this->tasks;
	}
  
	/**
	 * Add a task to the collection
	 * @param EvaTask Task to add to the collection
	 */
	function addTask($task)
	{
		$this->tasks[$task->getId()] = $task;
	}
  
	/**
	 * Remove a task from the collection
	 * @param EvaTask Task to remove from the collection
	 * @return EvaTask The task removed
	 */
	function removeTask($taskId)
	{
		$task = $this->tasks[$taskId];
		unset($this->tasks[$taskId]);
		return $task;
	}
  
	/**
	 * Remove all tasks from the collection
	 */
	function removeAllTasks()
	{
		unset($this->tasks);
		$this->tasks = array();
	}

/*
 * Others methods
 */
  /**
	 * Get the tasks like the one in parameter (id and left and right limit are non use because they are unique)
	 * @param EvaTask Task use to compare
	 */
	function getTasksLike($task)
	{
		global $wpdb;
    
		$this->removeAllTasks();
    
		{//Variables cleaning
			$name = eva_tools::IsValid_Variable($task->getName());
			$description = eva_tools::IsValid_Variable($task->getDescription());
			$startDate = eva_tools::IsValid_Variable($task->getStartDate());
			$finishDate = eva_tools::IsValid_Variable($task->getFinishDate());
			$place = eva_tools::IsValid_Variable($task->getPlace());
			$progression = (int) eva_tools::IsValid_Variable($task->getProgression());
			$idFrom = (int) eva_tools::IsValid_Variable($task->getIdFrom());
			$tableFrom = eva_tools::IsValid_Variable($task->getTableFrom());
			$status = eva_tools::IsValid_Variable($task->getStatus());
			$idCreateur = eva_tools::IsValid_Variable($task->getidCreateur());
			$idResponsable = eva_tools::IsValid_Variable($task->getidResponsable());
			$idSoldeur = eva_tools::IsValid_Variable($task->getidSoldeur());
			$idSoldeurChef = eva_tools::IsValid_Variable($task->getidSoldeurChef());
			$ProgressionStatus = eva_tools::IsValid_Variable($task->getProgressionStatus());
			$dateSolde = eva_tools::IsValid_Variable($task->getdateSolde());
		}
    {//Query creation
      $sql = "SELECT * FROM " . TABLE_TACHE . " WHERE 1";
      if($name != '')
      {
        $sql = $sql . " AND " . self::name . " = '" . mysql_real_escape_string($name) . "'";
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
      if($progression != 0)
      {
        $sql = $sql . " AND " . self::progression . " = " . mysql_real_escape_string($progression);
      }
      if($idFrom != 0)
      {
        $sql = $sql . " AND " . self::idFrom . " = " . mysql_real_escape_string($idFrom);
      }
      if($tableFrom != '')
      {
        $sql = $sql . " AND " . self::tableFrom . " = '" . mysql_real_escape_string($tableFrom) . "'";
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
        $sql = $sql . " AND " . self::ProgressionStatus . " IN (" . ($ProgressionStatus) . ") ";
      }
			if($dateSolde != '')
      {
        $sql = $sql . " AND " . self::dateSolde . " = '" . mysql_real_escape_string($dateSolde) . "'";
      }
    }
    
    $wpdbTasks = $wpdb->get_results($sql);
    
    foreach($wpdbTasks as $wpdbTask)
    {
      $task = new EvaTask();
      $task->convertWpdb($wpdbTask);
      $this->addTask($task);
    }
	}
}