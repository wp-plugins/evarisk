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
		global $current_user;
		
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
			$idCreateur = eva_tools::IsValid_Variable($current_user->ID);
			$idResponsable = eva_tools::IsValid_Variable($this->getidResponsable());
			$idSoldeur = eva_tools::IsValid_Variable($this->getidSoldeur());
			$idSoldeurChef = eva_tools::IsValid_Variable($this->getidSoldeurChef());
			$ProgressionStatus = eva_tools::IsValid_Variable($this->getProgressionStatus());
			$dateSolde = eva_tools::IsValid_Variable($this->getdateSolde());
			$hasPriority = eva_tools::IsValid_Variable($this->gethasPriority());
		}
		
		//Query creation
		if($id == 0)
		{// Insert in data base
			$sql = "INSERT INTO " . TABLE_TACHE . " (" . self::name . ", " . self::leftLimit . ", " . self::rightLimit . ", " . self::description . ", " . self::startDate . ",	" . self::finishDate . ", " . self::place . ", " . self::progression . ", " . self::cost . ", " . self::idFrom . ", " . self::tableFrom . ", " . self::status . ", " . self::idCreateur . ", " . self::idResponsable . ", " . self::idSoldeur . ",  " . self::idSoldeurChef . ",  " . self::ProgressionStatus . ", " . self::dateSolde . ", " . self::hasPriority . ", " . self::firstInsert . ")
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
								'" . mysql_real_escape_string($idCreateur) . "', 
								'" . mysql_real_escape_string($idResponsable) . "', 
								'" . mysql_real_escape_string($idSoldeur) . "',
								'" . mysql_real_escape_string($idSoldeurChef) . "',
								'" . mysql_real_escape_string($ProgressionStatus) . "',
								'" . mysql_real_escape_string($dateSolde) . "',
								'" . mysql_real_escape_string($hasPriority) . "',
								NOW())";
		}
		else
		{//Update of the data base
			$sql = "UPDATE " . TABLE_TACHE . " set 
				" . self::name . " = '" . mysql_real_escape_string($name) . "', 
				" . self::leftLimit . " = '" . mysql_real_escape_string($leftLimit) . "', 
				" . self::rightLimit . " = '" . mysql_real_escape_string($rightLimit) . "', 
				" . self::description . " = '" . mysql_real_escape_string($description) . "',
				" . self::startDate . " = '" . mysql_real_escape_string($startDate) . "',
				" . self::finishDate . " = '" . mysql_real_escape_string($finishDate) . "',
				" . self::place . " = '" . mysql_real_escape_string($place) . "',
				" . self::progression . " = '" . mysql_real_escape_string($progression) . "',
				" . self::cost . " = '" . mysql_real_escape_string($cost) . "',
				" . self::idFrom . " = '" . mysql_real_escape_string($idFrom) . "',
				" . self::tableFrom . " = '" . mysql_real_escape_string($tableFrom) . "',
				" . self::status . " = '" . mysql_real_escape_string($status) . "',
				" . self::idResponsable . " = '" . mysql_real_escape_string($idResponsable) . "',
				" . self::idSoldeur . " = '" . mysql_real_escape_string($idSoldeur) . "' ,
				" . self::idSoldeurChef . " = '" . mysql_real_escape_string($idSoldeurChef) . "' ,
				" . self::ProgressionStatus . " = '" . mysql_real_escape_string($ProgressionStatus) . "' ,
				" . self::dateSolde . " = '" . mysql_real_escape_string($dateSolde) . "' ,
				" . self::hasPriority . " = '" . mysql_real_escape_string($hasPriority) . "' 
			WHERE " . self::id . " = " . mysql_real_escape_string($id);
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

	function markAllSubElementAsDone()
	{
		global $current_user;

		$tacheDescendantes = $this->getDescendants();
		foreach($tacheDescendantes as $elements)
		{
			if(is_array($elements))
			{
				foreach($elements as $tache)
				{
					if(is_a($tache, 'evaTask'))
					{
						/*	For security reason we unset the subtask element	*/
						unset($subTask);

						$subTask = new EvaTask($tache->id);

						/*	Set tasks	*/
						$subTask->load();
						$subTask->setProgressionStatus('Done');
						$subTask->setidSoldeurChef($current_user->ID);
						$subTask->setdateSolde(date('Y-m-d H:i:s'));
						$subTask->save();

						/*	Set actions	*/
						$actions = $subTask->getWPDBActivitiesDependOn();
						if( is_array($actions) && (count($actions) > 0) )
						{
							foreach($actions as $action)
							{
								unset($subAction);
								$subAction = new EvaActivity($action->id);
								$subAction->load();
								$subAction->setProgressionStatus('Done');
								$subAction->setidSoldeurChef($current_user->ID);
								$subAction->setdateSolde(date('Y-m-d H:i:s'));
								$subAction->save();
								unset($subAction);
							}
						}

						/*	For security reason we unset the subtask element	*/
						unset($subTask);
					}
				}
			}
		}

		unset($actions);$actions = array();
		$actions = $this->getWPDBActivitiesDependOn();
		if( is_array($actions) && (count($actions) > 0) )
		{
			foreach($actions as $action)
			{
				unset($subAction);
				$subAction = new EvaActivity($action->id);
				$subAction->load();
				$subAction->setProgressionStatus('Done');
				$subAction->setidSoldeurChef($current_user->ID);
				$subAction->setdateSolde(date('Y-m-d H:i:s'));
				$subAction->save();
				unset($subAction);
			}
		}
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
	 * Compute and set the Task start and finish dates
	 */
	function getTimeWindow()
	{
		$TasksAndSubTasks = $this->getDescendants();
		$TasksAndSubTasks->addTask($this);
		$TasksAndSubTasks = $TasksAndSubTasks->getTasks();
		if($TasksAndSubTasks != null AND count($TasksAndSubTasks) > 0)
		{
			$startDate = $this->getStartDate();
			$finishDate = $this->getFinishDate();
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
			$this->setStartDate($startDate);
			$this->setFinishDate($finishDate);
		}
	}

	/**
	* Compute and set the Task progression regarding the sub tasks
	*/
	function computeProgression()
	{
		global $current_user;

		$TasksAndSubTasks = $this->getDescendants();
		$TasksAndSubTasks->addTask($this);
		$TasksAndSubTasks = $TasksAndSubTasks->getTasks();

		$taskDuration = $taskCompleteDuration = 0;
		$totalProgression = $totalSubTask = 0;
		$progressionStatusToSet = $this->getProgressionStatus();
		if(($TasksAndSubTasks != null) && (count($TasksAndSubTasks) > 0))
		{
			foreach($TasksAndSubTasks as $task)
			{
				$activities = $task->getActivitiesDependOn();
				$activities = $activities->getActivities();
				if($activities != null AND count($activities) > 0)
				{
					foreach($activities as $activity)
					{
						{	/*	Old version lookin at the task duration to calculate the progression	*/
							// $activityStartDate = $activity->getStartDate();
							// $startDate = explode('-', $activityStartDate);
							// $activityFinishDate = $activity->getFinishDate();
							// $finishDate = explode('-', $activityFinishDate);
							// $activiteDuration = mktime(0,0,0,$finishDate[1],$finishDate[2],$finishDate[0]) - mktime(0,0,0,$startDate[1], $startDate[2],$startDate[0]);
							// $taskDuration += $activiteDuration;
							// $taskCompleteDuration += $activiteDuration * $activity->getProgression() / 100;
						}

						{
							if(($progressionStatusToSet != 'inProgress') && ($activity->getProgressionStatus() == 'inProgress'))
							{
								$progressionStatusToSet = 'inProgress';
							}
							$totalProgression += $activity->getProgression();
							$totalSubTask++;
						}
					}
				}
				else
				{
					$this->setProgression(0);
				}
			}
		}
		// if($taskDuration > 0)
		if($totalProgression > 0)
		{
			// $progressionToSet = round($taskCompleteDuration / $taskDuration * 100);
			$progressionToSet = round($totalProgression / $totalSubTask);
			$this->setProgression($progressionToSet);
			if(($progressionToSet > 0))
			{
				$this->setProgressionStatus('inProgress');
			}
			if($progressionToSet >= 100)
			{
				$this->setProgressionStatus('Done');
				$this->setidSoldeur($current_user->ID);
				$this->setdateSolde(date('Y-m-d H:i:s'));
			}
		}
		elseif($progressionStatusToSet == 'inProgress')
		{
			$this->setProgressionStatus('inProgress');
		}
	}

	/**
	*	Make the link between a task and an element (risk/work unit/...)
	*
	*	@param mixed $table The element type we want to link
	* @param integer $id The element identifier we want to link
	* @param mixed $listeTaches A string composed by tasks id to link separeted by a delimiter
	*	@param mixed $momentLiaison Defines if the link is made before or after, used for the risk evaluation to know risk level before and after an action
	*/
	function liaisonTacheElement($table, $id, $listeTaches, $momentLiaison = 'before')
	{
		global $wpdb;
		$actionsList = "  ";

		$actions = explode('_ac_', $listeTaches);
		foreach($actions as $actionIndex => $actionID)
		{
			if($actionID > 0)
			{
				$actionsList .= "('', 'valid', '" . $momentLiaison . "', NOW(), '" . $actionID . "', '" . $id . "', '" . $table . "'), ";
			}
		}

		$actionsList = trim(substr($actionsList, 0, -2));
		if($actionsList != '')
		{
			$query = $wpdb->prepare
			(
				"REPLACE INTO " . TABLE_LIAISON_TACHE_ELEMENT . " 
					(id, status, wasLinked, date, id_tache, id_element, table_element) 
				VALUES 
					 " . $actionsList . ";"
			);
			$wpdb->query($query);
		}
	}

	/**
	*	Create a new task from a set of value send by a form
	*
	*	@return integer The new task identifier
	*/
	function saveNewTask()
	{
		$tache = new EvaTask();

		$tache->setName($_POST['nom_activite']);
		$tache->setDescription($_POST['description']);
		$tache->setCost($_POST['cout']);
		$tache->setIdFrom($_POST['idProvenance']);
		$tache->setTableFrom($_POST['tableProvenance']);
		$tache->setidResponsable($_POST['responsable_activite']);
		$tache->setProgressionStatus('notStarted');
		if($_POST['avancement'] > '0')
		{
			$tache->setProgressionStatus('inProgress');
		}
		if(isset($_POST['hasPriority']))
		{
			$tache->sethasPriority($_POST['hasPriority']);
		}
		else
		{
			$tache->sethasPriority('no');
		}
		if($_POST['avancement'] == '100')
		{
			$tache->setProgressionStatus('Done');
			global $current_user;
			$tache->setidSoldeur($current_user->ID);
		}
		$racine = new EvaTask(1);
		$racine->load();
		$tache->setLeftLimit($racine->getRightLimit());
		$tache->setRightLimit($racine->getRightLimit() + 1);
		$racine->setRightLimit($racine->getRightLimit() + 2);
		$racine->save();
		$tache->setStartDate($_POST['date_debut']);
		$tache->setFinishDate($_POST['date_fin']);
		$tache->save();

		return $tache->getId();
	}

	/**
	*	Return the content of box displayed on the dashboard to get information on the different correctiv actions
	*
	*	@param mixed $dashbordParam The type of summary we want to output
	*
	*	@param mixed $acDashboardBox The html content of the box
	*/
	function getTaskForDashBoard($dashbordParam)
	{
		require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayDesign.class.php');
		unset($lignesDeValeurs);
		global $wpdb;
		$moreScript = $acDashboardBox = '';
		$outputDatas = true;

		switch($dashbordParam)
		{
			case 'passed':
			{
				$query = $wpdb->prepare(
					"SELECT * 
					FROM " . TABLE_TACHE . " 
					WHERE Status = 'Valid' 
						AND dateFin <= CURDATE() 
						AND dateFin != '0000-00-00'
						AND ProgressionStatus IN ('inProgress', 'notStarted')
					ORDER BY dateFin DESC"
				);
				$taskList = $wpdb->get_results($query);

				$idTable = 'taskListPassedButNotMarkAsDone';
				$titres = array( __('Nom T&acirc;che', 'evarisk'), __('D&eacute;but', 'evarisk'), __('Fin', 'evarisk'), __('Progression (%)', 'evarisk'), __('Fiche', 'evarisk') );
				if(is_array($taskList) && (count($taskList) > 0))
				{
					foreach($taskList as $task)
					{
						unset($valeurs);
						$valeurs[] = array('value'=>$task->nom);
						$valeurs[] = array('value'=>$task->dateDebut);
						$valeurs[] = array('value'=>$task->dateFin);
						$valeurs[] = array('value'=>$task->avancement);
						$valeurs[] = array('value'=>'<a href="' . get_bloginfo('siteurl') . '/wp-admin/admin.php?page=digirisk_correctiv_actions&amp;elt=edit-node' . $task->id . '" target="seePassedFac" >' . __('Voir l\'action', 'evarisk') . '</a>');
						$lignesDeValeurs[] = $valeurs;
						$idLignes[] = 'taskListPassedButNotMarkedAsDone' . $task->id;
					}
				}
				else
				{
					unset($valeurs);
					$valeurs[] = array('value'=>__('Aucun r&eacute;sultat trouv&eacute;', 'evarisk'));
					$valeurs[] = array('value'=>'');
					$valeurs[] = array('value'=>'');
					$valeurs[] = array('value'=>'');
					$valeurs[] = array('value'=>'');
					$lignesDeValeurs[] = $valeurs;
					$idLignes[] = 'taskListPassedButNotMarkAsDoneEmpty';
					$acDashboardBox .= '<script type="text/javascript" >evarisk("#evaDashboard_ac_passed").remove();</script>';
					$outputDatas = false;
				}

				$classes = array('cbColumnLarge','','','cbNbUserGroup');
				$tableOptions = '';
			}
			break;
			case 'taskToMarkAsDone':
			{
				$query = $wpdb->prepare(
					"SELECT TASK.*
					FROM " . TABLE_TACHE . " AS TASK
					WHERE TASK.Status = 'Valid' 
						AND TASK.dateFin >= CURDATE() 
						AND TASK.dateFin != '0000-00-00'
						AND TASK.ProgressionStatus = 'inProgress'
						AND TASK.id != '1'
					ORDER BY TASK.dateFin DESC"
				);
				$taskList = $wpdb->get_results($query);
				foreach($taskList as $taskIndex => $task)
				{
					$query = $wpdb->prepare(
						"SELECT ACTION.id, ACTION.id_tache, ACTION.ProgressionStatus
						FROM " . TABLE_ACTIVITE . " AS ACTION
						WHERE ACTION.id_tache = '%d'
						",
						$task->id
					);
					$actionList = $wpdb->get_results($query);
					foreach($actionList as $action)
					{
						if($action->ProgressionStatus == 'inProgress')
						{
							unset($taskList[$taskIndex]);
						}
					}
				}

				$idTable = 'taskListToMarkAsDone';
				$titres = array( __('Nom T&acirc;che', 'evarisk'), __('D&eacute;but', 'evarisk'), __('Fin', 'evarisk'), __('Progression (%)', 'evarisk'), __('Fiche', 'evarisk') );
				if(is_array($taskList) && (count($taskList) > 0))
				{
					foreach($taskList as $task)
					{
						unset($valeurs);
						$valeurs[] = array('value'=>$task->nom);
						$valeurs[] = array('value'=>$task->dateDebut);
						$valeurs[] = array('value'=>$task->dateFin);
						$valeurs[] = array('value'=>$task->avancement);
						$valeurs[] = array('value'=>'<a href="' . get_bloginfo('siteurl') . '/wp-admin/admin.php?page=digirisk_correctiv_actions&amp;elt=edit-node' . $task->id . '" target="seePassedFac" >' . __('Voir l\'action', 'evarisk') . '</a>');
						$lignesDeValeurs[] = $valeurs;
					$idLignes[] = 'taskListToMarkAsDone' . $task->id;
					}
				}
				else
				{
					unset($valeurs);
					$valeurs[] = array('value'=>__('Aucun r&eacute;sultat trouv&eacute;', 'evarisk'));
					$valeurs[] = array('value'=>'');
					$valeurs[] = array('value'=>'');
					$valeurs[] = array('value'=>'');
					$valeurs[] = array('value'=>'');
					$lignesDeValeurs[] = $valeurs;
					$idLignes[] = 'taskListToMarkAsDoneEmpty';
					$acDashboardBox .= '<script type="text/javascript" >evarisk("#evaDashboard_task_toDone").remove();</script>';
					$outputDatas = false;
				}

				$classes = array('cbColumnLarge','','','cbNbUserGroup');
				$tableOptions = 
				'';
			}
			break;
			case 'toEvaluate':
			{
				$idTable = 'riskListToReEavaluate';
				$query = $wpdb->prepare(
					"SELECT RISK.*, DANGER.nom AS nomDanger
					FROM " . TABLE_RISQUE . " AS RISK
						INNER JOIN " . TABLE_AVOIR_VALEUR . " AS RISKEVAL ON ((RISKEVAL.id_risque = RISK.id) AND (RISKEVAL.Status = 'Valid'))
						INNER JOIN " . TABLE_LIAISON_TACHE_ELEMENT . " AS LTE ON (LTE.id_element = RISKEVAL.id_evaluation)

						INNER JOIN " . TABLE_DANGER . " AS DANGER ON (DANGER.id = RISK.id_danger)
					WHERE LTE.table_element = '" . TABLE_AVOIR_VALEUR . "'
						AND LTE.wasLinked = 'after'
						AND LTE.status = 'valid'
					GROUP BY RISK.id "
				);
				$riskList = $wpdb->get_results($query);

				$titres = array( __('Nom danger', 'evarisk'), __('Commentaire sur le risque', 'evarisk'), __('&Eacute;l&eacute;ment', 'evarisk'), __('Fiche', 'evarisk') );
				if(is_array($riskList) && (count($riskList) > 0))
				{
					foreach($riskList as $risk)
					{
						unset($valeurs);
						$valeurs[] = array('value'=>$risk->nomDanger);
						$valeurs[] = array('value'=>$risk->commentaire);

						$risqueAffecteA = '';
						$idGroupement = 0;
						switch($risk->nomTableElement)
						{
							case TABLE_GROUPEMENT:
								$idGroupement = $risk->id_element;
								$infos = EvaGroupement::getGroupement($idGroupement);
								$nomElementCourant = $infos->nom;
								$elementToSee = 'node-mainTable-' . $risk->id_element . '-name';
							break;
							case TABLE_UNITE_TRAVAIL:
								$query = $wpdb->prepare("SELECT nom, id_groupement FROM " . TABLE_UNITE_TRAVAIL . " WHERE id = '" . $risk->id_element . "' ");
								$infos = $wpdb->get_row($query);
								$idGroupement = $infos->id_groupement;
								$nomElementCourant = $infos->nom;
								$elementToSee = 'leaf-' . $risk->id_element . '-name';
							break;
							default:
								$risqueAffecteA .= __('Il vous faut ajouter le cas ' . $risk->nomTableElement . ' dans le fichier ' . __FILE__ . ' &agrave; la ligne ' . __LINE__ . ' pour avoir les bonnes informations', 'evarisk');
								$nomElementCourant = $elementToSee = '';
							break;
						}
						if($idGroupement > 0)
						{
							$groupementPere = EvaGroupement::getGroupement($idGroupement);
							$ancetres = Arborescence::getAncetre(TABLE_GROUPEMENT, $groupementPere);
							foreach($ancetres as $ancetre)
							{
								if($ancetre->nom != "Groupement Racine")
								{
									$risqueAffecteA .= $ancetre->nom . ' &raquo; ';
								}
							}
						}
						$completeName = $risqueAffecteA;
						if($groupementPere->nom != $nomElementCourant)
						{
							$completeName .= $groupementPere->nom . ' &raquo; ';
						}
						$valeurs[] = array('value'=>$completeName . $nomElementCourant);
						$valeurs[] = array('value'=>'<a href="' . get_bloginfo('siteurl') . '/wp-admin/admin.php?page=digirisk_risk_evaluation&amp;elt=' . $elementToSee . '&amp;risk=risque-' . $risk->id . '-edit" target="seeRiskToEvaluate" >' . __('Voir le risque', 'evarisk') . '</a>');
						$lignesDeValeurs[] = $valeurs;
						$idLignes[] = 'taskListToReEvaluateRisk' . $risk->id;
					}
				}
				else
				{
					unset($valeurs);
					$valeurs[] = array('value'=>__('Aucun r&eacute;sultat trouv&eacute;', 'evarisk'));
					$valeurs[] = array('value'=>'');
					$valeurs[] = array('value'=>'');
					$valeurs[] = array('value'=>'');
					$lignesDeValeurs[] = $valeurs;
					$idLignes[] = 'taskListToReEvaluateRiskEmpty';
					$acDashboardBox .= '<script type="text/javascript" >evarisk("#evaDashboard_ac_done").remove();</script>';
					$outputDatas = false;
				}

				$classes = array('cbColumnLarge','','','cbNbUserGroup');
				$tableOptions = '';
			}
			break;
			default:
				$taskList = sprintf(__('Vous devez d&eacute;clarer %s dans le fichier %s &agrave; la ligne %d si vous voulez cette information', 'evarisk'), $dashbordParam, __FILE__, __LINE__);
				$outputDatas = false;
			break;
		}

		if($outputDatas)
		{
			$script = 
			'<script type="text/javascript">
				evarisk(document).ready(function() {
					evarisk("#' . $idTable . '").dataTable({
						"bInfo": false,
						"oLanguage": {
							"sSearch": "<span class=\'ui-icon searchDataTableIcon\' >&nbsp;</span>",
							"sEmptyTable": "' . __('Aucune action trouv&eacute;e', 'evarisk') . '",
							"sLengthMenu": "' . __('Afficher _MENU_ actions', 'evarisk') . '",
							"sInfoEmpty": "' . __('Aucune action', 'evarisk') . '",
							"sZeroRecords": "' . __('Aucune action trouv&eacute;e', 'evarisk') . '",
							"oPaginate": {
								"sFirst": "' . __('Premi&eacute;re', 'evarisk') . '",
								"sLast": "' . __('Derni&egrave;re', 'evarisk') . '",
								"sNext": "' . __('Suivante', 'evarisk') . '",
								"sPrevious": "' . __('Pr&eacute;c&eacute;dente', 'evarisk') . '"
							}
						}
						' . $tableOptions . '
					});
					evarisk("#' . $idTable . '").children("tfoot").remove();
					evarisk("#' . $idTable . '_wrapper").removeClass("dataTables_wrapper");
				});
			</script>';
			$acDashboardBox .= evaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script);
		}

		return $acDashboardBox;
	}

	/**
	* Get the priority taks added to a risk
	*
	*	@param mixed $tableElement The element type we want to get the priority task for
	* @param integer $idElement The element identifier we want to get the priority task for
	*
	*	@return object A wordpress database object with the task identifier
	*/
	function getPriorityTask($tableElement, $idElement)
	{
		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT id
			FROM " . TABLE_TACHE . "
			WHERE tableProvenance = '%s'
				AND idProvenance = '%d' 
				AND hasPriority = 'yes'
				AND Status = 'Valid' 
			LIMIT 1",
		$tableElement, $idElement);

		return $wpdb->get_row($query);
	}


	/**
	*
	*/
	function correctivAction($tableElement, $idElement)
	{
		$completeTreeUnderElement = arborescence::completeTree($tableElement, $idElement);
		echo '<pre>';print_r($completeTreeUnderElement);echo '</pre>';
		foreach($completeTreeUnderElement as $element)
		{
		
		}
	}

}