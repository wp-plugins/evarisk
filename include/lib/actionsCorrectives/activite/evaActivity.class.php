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
		global $current_user;
		
		{//Variables cleaning
			$id = (int) eva_tools::IsValid_Variable($this->getId());
			$relatedTaskId = (int) eva_tools::IsValid_Variable($this->getRelatedTaskId());
			$name = eva_tools::IsValid_Variable($this->getName());
			$description = eva_tools::IsValid_Variable($this->getDescription());
			$startDate = eva_tools::IsValid_Variable($this->getStartDate());
			$finishDate = eva_tools::IsValid_Variable($this->getFinishDate());
			$place = eva_tools::IsValid_Variable($this->getPlace());
			$cout = (float) eva_tools::IsValid_Variable($this->getCout());
			$progression = (int) eva_tools::IsValid_Variable($this->getProgression());
			$status = eva_tools::IsValid_Variable($this->getStatus());
			$idCreateur = eva_tools::IsValid_Variable($current_user->ID);
			$idResponsable = eva_tools::IsValid_Variable($this->getidResponsable());
			$idSoldeur = eva_tools::IsValid_Variable($this->getidSoldeur());
			$idSoldeurChef = eva_tools::IsValid_Variable($this->getidSoldeurChef());
			$ProgressionStatus = eva_tools::IsValid_Variable($this->getProgressionStatus());
			$dateSolde = eva_tools::IsValid_Variable($this->getdateSolde());
			$idPhotoAvant = eva_tools::IsValid_Variable($this->getidPhotoAvant());
			$idPhotoApres = eva_tools::IsValid_Variable($this->getidPhotoApres());
		}
		
		//Query creation
		if($id == 0)
		{// Insert in data base
			$sql = "INSERT INTO " . TABLE_ACTIVITE . " (" . self::relatedTaskId . ", " . self::name . ", " . self::description . ", " . self::startDate . ",	" . self::finishDate . ", " . self::place . ", " . self::cout . ", " . self::progression . ", " . self::status . ", " . self::idCreateur . ", " . self::idResponsable . ", " . self::idSoldeur . ",   " . self::idSoldeurChef . ",  " . self::ProgressionStatus . ",  " . self::dateSolde . ", " . self::idPhotoAvant . ", " . self::idPhotoApres . ", " . self::firstInsert . ")
				VALUES ('" . mysql_real_escape_string($relatedTaskId) . "', 
								'" . mysql_real_escape_string($name) . "', 
								'" . mysql_real_escape_string($description) . "', 
								'" . mysql_real_escape_string($startDate) . "', 
								'" . mysql_real_escape_string($finishDate) . "',
								'" . mysql_real_escape_string($place) . "', 
								'" . mysql_real_escape_string($cout) . "', 
								'" . mysql_real_escape_string($progression) . "', 
								'" . mysql_real_escape_string($status) . "', 
								'" . mysql_real_escape_string($idCreateur) . "', 
								'" . mysql_real_escape_string($idResponsable) . "', 
								'" . mysql_real_escape_string($idSoldeur) . "', 
								'" . mysql_real_escape_string($idSoldeurChef) . "', 
								'" . mysql_real_escape_string($ProgressionStatus) . "', 
								'" . mysql_real_escape_string($dateSolde) . "', 
								'" . mysql_real_escape_string($idPhotoAvant) . "', 
								'" . mysql_real_escape_string($idPhotoApres) . "', 
								NOW())";
		}
		else
		{//Update of the data base
			$sql = "UPDATE " . TABLE_ACTIVITE . " set 
				" . self::relatedTaskId . " = '" . mysql_real_escape_string($relatedTaskId) . "', 
				" . self::name . " = '" . mysql_real_escape_string($name) . "', 
				" . self::description . " = '" . mysql_real_escape_string($description) . "',
				" . self::startDate . " = '" . mysql_real_escape_string($startDate) . "',
				" . self::finishDate . " = '" . mysql_real_escape_string($finishDate) . "',
				" . self::place . " = '" . mysql_real_escape_string($place) . "',
				" . self::cout . " = '" . mysql_real_escape_string($cout) . "',
				" . self::progression . " = '" . mysql_real_escape_string($progression) . "',
				" . self::status . " = '" . mysql_real_escape_string($status) . "' ,
				" . self::idResponsable . " = '" . mysql_real_escape_string($idResponsable) . "' ,
				" . self::idSoldeur . " = '" . mysql_real_escape_string($idSoldeur) . "' ,
				" . self::idSoldeurChef . " = '" . mysql_real_escape_string($idSoldeurChef) . "' ,
				" . self::ProgressionStatus . " = '" . mysql_real_escape_string($ProgressionStatus) . "' ,
				" . self::idPhotoAvant . " = '" . mysql_real_escape_string($idPhotoAvant) . "' ,
				" . self::idPhotoApres . " = '" . mysql_real_escape_string($idPhotoApres) . "' ,
				" . self::dateSolde . " = '" . mysql_real_escape_string($dateSolde) . "' 
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

	function saveNewActivity()
	{
		global $wpdb;
		require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/tache/evaTask.class.php');

		$tache = new EvaTask();
		$tache->setName($_POST['nom_activite']);
		$tache->setDescription($_POST['description']);
		$tache->setCost($_POST['cout']);
		$tache->setIdFrom($_POST['idProvenance']);
		$tache->setTableFrom($_POST['tableProvenance']);
		$tache->setidResponsable($_POST['responsable_activite']);
		$tache->setProgressionStatus('inProgress');
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
		$activite = new EvaActivity();
		$activite->setName($_POST['nom_activite']);
		$activite->setDescription($_POST['description']);
		$activite->setStartDate($_POST['date_debut']);
		$activite->setFinishDate($_POST['date_fin']);
		$activite->setCout($_POST['cout']);
		$activite->setProgression($_POST['avancement']);
		$activite->setProgressionStatus('inProgress');
		if($_POST['avancement'] == '100')
		{
			$activite->setProgressionStatus('Done');
			global $current_user;
			$activite->setidSoldeur($current_user->ID);
		}
		$activite->setidResponsable($_POST['responsable_activite']);
		$tache->setStartDate($_POST['date_debut']);
		$tache->setFinishDate($_POST['date_fin']);
		$tache->getTimeWindow();
		$tache->computeProgression();
		$tache->save();
		$activite->setRelatedTaskId($tache->getId());
		$activite->save();

		/*	Make the link between a corrective action and a risk evaluation	*/
		$query = 
			$wpdb->prepare(
				"SELECT id_evaluation 
				FROM " . TABLE_AVOIR_VALEUR . " 
				WHERE id_risque = '%d' 
					AND Status = 'Valid' 
				ORDER BY id DESC 
				LIMIT 1", 
				$_POST['idProvenance']
			);
		$evaluation = $wpdb->get_row($query);
		evaTask::liaisonTacheElement(TABLE_AVOIR_VALEUR, $evaluation->id_evaluation, $tache->getId(), 'before');

		/*	Update the task ancestor	*/
		$wpdbTasks = Arborescence::getAncetre(TABLE_TACHE, $tache->convertToWpdb());
		foreach($wpdbTasks as $task)
		{
			unset($ancestorTask);
			$ancestorTask = new EvaTask($task->id);
			$ancestorTask->load();
			$ancestorTask->computeProgression();
			$ancestorTask->save();
			unset($ancestorTask);
		}

		$result['task_status'] = $tache->getStatus();
		$result['action_status'] = $activite->getStatus();
		$result['task_id'] = $tache->getId();
		$result['action_id'] = $activite->getId();

		return $result;
	}

	function activityList($tableElement, $idElement)
	{
		$tachesActionsCorrectives = array();
		$riskList = Risque::getRisques($tableElement, $idElement, "Valid");
		if($riskList != null)
		{
			foreach($riskList as $risque)
			{
				$risques[$risque->id][] = $risque; 
			}
		}
		if(count($risques) > 0)
		{
			foreach($risques as $idRisque => $infosRisque)
			{
				$actionsCorrectives = '';
				$taches = new EvaTaskTable();
				$tacheLike = new EvaTask();
				$tacheLike->setIdFrom($idRisque);
				$tacheLike->setTableFrom(TABLE_RISQUE);
				$taches->getTasksLike($tacheLike);
				$tachesActionsCorrectives = $taches->getTasks();
			}
		}

		return $tachesActionsCorrectives;
	}

}