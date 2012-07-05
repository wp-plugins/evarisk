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
			$id = (int) digirisk_tools::IsValid_Variable($this->getId());
			$relatedTaskId = (int) digirisk_tools::IsValid_Variable($this->getRelatedTaskId());
			$name = digirisk_tools::IsValid_Variable($this->getName());
			$description = digirisk_tools::IsValid_Variable($this->getDescription());
			$startDate = digirisk_tools::IsValid_Variable($this->getStartDate());
			$finishDate = digirisk_tools::IsValid_Variable($this->getFinishDate());
			$place = digirisk_tools::IsValid_Variable($this->getPlace());
			$cout = (float) digirisk_tools::IsValid_Variable($this->getCout());
			$progression = (int) digirisk_tools::IsValid_Variable($this->getProgression());
			$status = digirisk_tools::IsValid_Variable($this->getStatus());
			$idCreateur = digirisk_tools::IsValid_Variable($current_user->ID);
			$idResponsable = digirisk_tools::IsValid_Variable($this->getidResponsable());
			$idSoldeur = digirisk_tools::IsValid_Variable($this->getidSoldeur());
			$idSoldeurChef = digirisk_tools::IsValid_Variable($this->getidSoldeurChef());
			$ProgressionStatus = digirisk_tools::IsValid_Variable($this->getProgressionStatus());
			$dateSolde = digirisk_tools::IsValid_Variable($this->getdateSolde());
			$idPhotoAvant = digirisk_tools::IsValid_Variable($this->getidPhotoAvant());
			$idPhotoApres = digirisk_tools::IsValid_Variable($this->getidPhotoApres());
			$nom_exportable_plan_action = digirisk_tools::IsValid_Variable($this->getnom_exportable_plan_action());
			$description_exportable_plan_action = digirisk_tools::IsValid_Variable($this->getdescription_exportable_plan_action());
		}
		
		//Query creation
		if($id == 0)
		{// Insert in data base
			$sql = "INSERT INTO " . TABLE_ACTIVITE . " (" . self::relatedTaskId . ", " . self::name . ", " . self::description . ", " . self::startDate . ",	" . self::finishDate . ", " . self::place . ", " . self::cout . ", " . self::progression . ", " . self::status . ", " . self::idCreateur . ", " . self::idResponsable . ", " . self::idSoldeur . ",   " . self::idSoldeurChef . ",  " . self::ProgressionStatus . ",  " . self::dateSolde . ", " . self::idPhotoAvant . ", " . self::idPhotoApres . ", " . self::nom_exportable_plan_action . ", " . self::description_exportable_plan_action . ", " . self::firstInsert . ")
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
								'" . mysql_real_escape_string($nom_exportable_plan_action) . "', 
								'" . mysql_real_escape_string($description_exportable_plan_action) . "', 
								'" . current_time('mysql', 0) . "')";
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
				" . self::nom_exportable_plan_action . " = '" . mysql_real_escape_string($nom_exportable_plan_action) . "' ,
				" . self::description_exportable_plan_action . " = '" . mysql_real_escape_string($description_exportable_plan_action) . "' ,
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
		$id = (int) digirisk_tools::IsValid_Variable($this->getId());
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
	*
	* @param int $newRelatedTaskId New relative task identifier
	*
	*/
	function transfert($newRelatedTaskId)
	{
		global $wpdb;
		$wpdb->query("UPDATE " . TABLE_ACTIVITE . " SET " . self::relatedTaskId . " = " . $newRelatedTaskId . " WHERE " . self::id . " = " . $this->getId());
	}

	/**
	*	Save a new activity (sub-task) into correctiv action database
	*
	* @return array $result The different result state and information
	*/
	function saveNewActivity(){
		global $wpdb;
		require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/tache/evaTask.class.php');

		$activite = new EvaActivity();
		$activite->setName($_POST['nom_activite']);
		$activite->setDescription($_POST['description']);
		$activite->setStartDate($_POST['date_debut']);
		$activite->setFinishDate($_POST['date_fin']);
		$activite->setCout($_POST['cout']);
		$activite->setProgression($_POST['avancement']);
		$activite->setProgressionStatus('notStarted');
		$activite->setnom_exportable_plan_action(!empty($_POST['nom_exportable_plan_action'])?$_POST['nom_exportable_plan_action']:'no');
		$activite->setdescription_exportable_plan_action(!empty($_POST['description_exportable_plan_action'])?$_POST['description_exportable_plan_action']:'no');
		if($_POST['avancement'] > '0'){
			$activite->setProgressionStatus('inProgress');
		}
		if($_POST['avancement'] == '100'){
			$activite->setProgressionStatus('Done');
			global $current_user;
			$activite->setidSoldeur($current_user->ID);
		}
		$activite->setidResponsable($_POST['responsable_activite']);

		$tache = new EvaTask($_POST['parentTaskId']);
		$tache->load();
		$activite->setRelatedTaskId($tache->getId());
		$activite->save();

		$tache->getTimeWindow();
		$tache->computeProgression();
		$tache->save();

		/*	Update the task ancestor	*/
		$wpdbTasks = Arborescence::getAncetre(TABLE_TACHE, $tache->convertToWpdb());
		foreach($wpdbTasks as $task){
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

	/**
	*	Function allowing to display the correctiv action form in frontend
	*/
	function task_asker($args){
		$task_asker = '';

		$main_options = get_option('digirisk_options');
		$taks_asker_available = false;
		$task_asker_not_available_explanation = '';

		if($main_options['digi_ac_allow_front_ask'] == 'oui'){
			if($main_options['digi_ac_front_ask_parent_task_id'] > 1){
				$task_to_check = new EvaTask($main_options['digi_ac_front_ask_parent_task_id']);
				$task_to_check->load();
				if($task_to_check->getName() != ''){
					$taks_asker_available = true;
				}
				else{
					$task_asker_not_available_explanation = __('Impossible de trouver la t&acirc;che principale permettant d\'effectuer les demandes. Vous pouvez contacter votre administrateur pour plus d\'informations', 'evarisk');
				}
			}
			else{
				$task_asker_not_available_explanation = __('Impossible de trouver la t&acirc;che principale permettant d\'effectuer les demandes. Vous pouvez contacter votre administrateur pour plus d\'informations', 'evarisk');
			}
		}
		else{
			$task_asker_not_available_explanation = __('La demande d\'action est d&eacute;sactiv&eacute;e pour le moment. Vous pouvez contacter votre administrateur pour plus d\'informations', 'evarisk');
		}

		if($taks_asker_available){
			$ask_argument['tableProvenance'] = 'correctiv_action_ask';
			$ask_argument['provenance'] = 'ask_correctiv_action';
			$ask_argument['output_mode'] = 'return';
			$ask_argument['requested_action'] = 'ask_correctiv_action';
			if(current_user_can('digi_ask_action_front')){
				$task_asker = '
	<div id="message' . $ask_argument['tableProvenance'] . '" class="digirisk_hide" >&nbsp;</div>
	<div class="clear" >' . 
		self::sub_task_creation_form($ask_argument) . '
	</div>
	<div class="digirisk_hide" id="ajax-response" >&nbsp;</div>';
			}
			else{
				$task_asker = __('Pour demander la cr&eacute;ation d\'une nouvelle action corrective, vous devez &ecirc;tre connect&eacute; et l\'administrateur du site doit vous donner les droits', 'evarisk');
			}
		}
		else{
			$task_asker = $task_asker_not_available_explanation;
		}

		echo $task_asker;
	}
	/**
	*
	*/
	function task_asker_add_picture($table_provenance, $token){
		return '<input type="file" name="correctiv_action_picture" id="correctiv_action_picture" />';
		// evaPhoto::getUploadForm($table_provenance, $token, 'jQuery("#ask_correctiv_action_picture_form").html("");jQuery("#ask_correctiv_action_picture img").attr("src", "' . EVA_UPLOADS_PLUGIN_URL . $table_provenance . "/" . $token . '/" + response); jQuery("#ask_correctiv_action_picture").show();');
	}
	
	/**
	*	Create the form used for new activity creation. Return different shape following parameters
	*
	*	@param array $arguments An array with the different parameters for form outputting (As element table, identifier, original request, ...)
	*
	*	@return string $sub_task_creation_form An html output of the form
	*/
	function sub_task_creation_form($arguments){
		$idElement = $activite_new = $addPictureButton = '';

		/*	Check if an element has been passed as parameter => If there is an element, load the good activity	*/
		if($arguments['idElement'] != null){
			$idElement = $arguments['idElement'];
			$activite = new EvaActivity($idElement);
			$activite->load();
			$contenuInputTitre = $activite->getName();
			$contenuInputDescription = $activite->getDescription();
			$contenuInputDateDebut = $activite->getStartDate();
			$contenuInputDateFin = $activite->getFinishDate();
			$contenuInputCout = $activite->getCout();
			$contenuInputAvancement = $activite->getProgression();
			$contenuInputResponsable = $activite->getidResponsable();
			$contenuInputRealisateur = $activite->getidSoldeur();
			$ProgressionStatus = $activite->getProgressionStatus();
			$nom_exportable_plan_action = $activite->getnom_exportable_plan_action();
			$description_exportable_plan_action = $activite->getdescription_exportable_plan_action();
			$firstInsert = $activite->getFirstInsert();
			$idCreateur = $activite->getidCreateur();
			$grise = false;
			$idPere = $activite->getRelatedTaskId();
			$saveOrUpdate = 'update';
			if(isset($arguments['requested_action']) && ($arguments['requested_action'] == 'control_asked_action')){
				$saveOrUpdate = 'add_control';
				$contenuInputAvancement = 100;
			}
		}
		else{
			$contenuInputTitre = $contenuInputDescription = $contenuInputRealisateur = $contenuInputResponsable = $firstInsert = '';
			$contenuInputDateDebut = date('Y-m-d');
			$contenuInputDateFin = date('Y-m-d');
			$ProgressionStatus = 'Done';
			$nom_exportable_plan_action = $description_exportable_plan_action = '';
			$idCreateur = 0;
			$contenuInputAvancement = 0;
			$idPere = $arguments['idPere'];
			$grise = true;
			$saveOrUpdate = (isset($arguments['requested_action']) && ($arguments['requested_action'] != '') && (!in_array($arguments['requested_action'], array('demandeAction', 'ficheAction')))) ? $arguments['requested_action'] : 'save';
			if(isset($arguments['requested_action']) && ($arguments['requested_action'] == 'demandeAction'))
				$saveOrUpdate = 'addAction';
			elseif(isset($arguments['requested_action']) && ($arguments['requested_action'] == 'ficheAction')){
				$saveOrUpdate = 'add_control';
				$contenuInputAvancement = 100;
			}
		}

		/*	Form initialisation	*/
		$idBouttonEnregistrer = 'save_activite';
		$idBouttonSold = 'actionDone';

		/*	Check if the user in charge of the action is mandatory */
		$idResponsableIsMandatory = digirisk_options::getOptionValue('responsable_Action_Obligatoire');
		/*	Check if an alert must be shown to user when he try to mark an action as done when not at 100 percent		*/
		$alertWhenMarkActionAsDone = digirisk_options::getOptionValue('avertir_Solde_Action_Non_100');

		{/*	Hidden field					*/
			$activite_new .= 
	EvaDisplayInput::afficherInput('hidden', 'post', 'true', '', null, 'post', false, false) . 
	EvaDisplayInput::afficherInput('hidden', 'act', $saveOrUpdate, '', null, 'act', false, false) . 
	EvaDisplayInput::afficherInput('hidden', 'original_act', (!empty($arguments['requested_action'])?$arguments['requested_action']:''), '', null, 'original_act', false, false) . 
	EvaDisplayInput::afficherInput('hidden', 'affichage_activite', $arguments['affichage'], '', null, 'affichage', false, false) . 
	EvaDisplayInput::afficherInput('hidden', 'table', TABLE_ACTIVITE, '', null, 'table', false, false) . 
	EvaDisplayInput::afficherInput('hidden', 'id_activite', $idElement, '', null, 'id', false, false) . 
	EvaDisplayInput::afficherInput('hidden', 'idPere_activite', $idPere, '', null, 'idPere', false, false) . 
	EvaDisplayInput::afficherInput('hidden', 'idsFilAriane_activite', $arguments['idsFilAriane'], '', null, 'idsFilAriane', false, false) . 
	EvaDisplayInput::afficherInput('hidden', 'idProvenance_activite', (!empty($arguments['idProvenance'])?$arguments['idProvenance']:''), '', null, 'idProvenance', false, false) . 
	EvaDisplayInput::afficherInput('hidden', 'tableProvenance_activite', (!empty($arguments['tableProvenance'])?$arguments['tableProvenance']:''), '', null, 'tableProvenance', false, false);
		}
		{/*	Sub-Task name					*/
			$contenuAideTitre = "";
			$labelInput = ucfirst(sprintf(__("nom %s", 'evarisk'), __("de l'action",'evarisk'))) . '&nbsp;<span class="fieldInfo required" >' . __('(obligatoire)', 'evarisk') . '</span> : <div class="alignright" ><input type="checkbox" name="nom_exportable_plan_action" id="nom_exportable_plan_action" value="yes"'.(!empty($nom_exportable_plan_action) && ($nom_exportable_plan_action=='yes')?' checked="checked"':'').' />&nbsp;<label for="nom_exportable_plan_action" >'.__('Exporter dans le plan d\'action', 'evarisk').'</label></div>';
			$nomChamps = "nom_activite";
			$idTitre = "nom_activite";
			$activite_new .= EvaDisplayInput::afficherInput('text', $idTitre, $contenuInputTitre, $contenuAideTitre, $labelInput, $nomChamps, $grise, true, 255, 'titleInput', '', '99%');
		}
		{/*	Sub-Task creation informations		*/		
			if(($firstInsert != '') || ($idCreateur > 0)){
				if(($firstInsert != '') && ($idCreateur > 0)){
					$subtask_creator_infos = evaUser::getUserInformation($idCreateur);
					$activite_new .= sprintf(__('Ajout&eacute;e le %s par %s', 'evarisk'), mysql2date('d M Y', $firstInsert, true), $subtask_creator_infos[$idCreateur]['user_lastname'] . ' ' . $subtask_creator_infos[$idCreateur]['user_firstname']);
				}
				elseif($firstInsert != ''){
					$activite_new .= sprintf(__('Ajout&eacute;e le %s', 'evarisk'), mysql2date('d M Y', $firstInsert, true));
				}
				elseif($idCreateur > 0){
					$subtask_creator_infos = evaUser::getUserInformation($idCreateur);
					$activite_new .= sprintf(__('Ajout&eacute;e par %s', 'evarisk'), $subtask_creator_infos[$idCreateur]['user_lastname'] . ' ' . $subtask_creator_infos[$idCreateur]['user_firstname']);
				}
				$activite_new .= '<br /><br class="clear" />';
			}
		}
		if(!empty($arguments['provenance']) && ($arguments['provenance'] != 'ask_correctiv_action')){
			/*	Sub-Task start date		*/
			$contenuAideTitre = "";
			$id = "date_debut_activite";
			$label = '<label for="' . $id . '" >' . ucfirst(sprintf(__("Date de d&eacute;but %s", 'evarisk'), __("de l'action",'evarisk'))) . '</label> : <span class="fieldInfo pointer" id="putTodayActionStart" >' . __('Aujourd\'hui', 'evarisk') . '</span>';
			$labelInput = '';
			$nomChamps = "date_debut";
			$activite_new .= $label . EvaDisplayInput::afficherInput('text', $id, $contenuInputDateDebut, $contenuAideTitre, $labelInput, $nomChamps, $grise, true, 255, '', 'date', '99%') . '';

			/*	Sub-Task end date			*/
			$contenuAideTitre = "";
			$id = "date_fin_activite";
			$label = '<label for="' . $id . '" >' . ucfirst(sprintf(__("Date de fin %s", 'evarisk'), __("de l'action",'evarisk'))) . '</label> : <span class="fieldInfo pointer" id="putTodayActionEnd" >' . __('Aujourd\'hui', 'evarisk') . '</span>';
			$labelInput = '';
			$nomChamps = "date_fin";
			$activite_new .= $label . EvaDisplayInput::afficherInput('text', $id, $contenuInputDateFin, $contenuAideTitre, $labelInput, $nomChamps, $grise, true, 255, '', 'date', '99%') . '';

			/*	Sub-Task cost					*/
			$contenuAideDescription = "";
			$labelInput = __("Co&ucirc;t", 'evarisk') . ' : ';
			$id = "cout_activite";
			$nomChamps = "cout";
			$activite_new .= EvaDisplayInput::afficherInput('text', $id, $contenuInputCout, $contenuAideDescription, $labelInput, $nomChamps, $grise, true, 255, '', '', '99%');
			
			/*	Sub-Task progression	*/
			$id = "avancement_activite";
			$nomChamps = "avancement";
			$activite_new .= __("Avancement", 'evarisk') . ' : 
<input type="text" name="' . $nomChamps . '" id="' . $id . '" style="width:5%;" value="' . $contenuInputAvancement . '" />' . __('%', 'evarisk') . '<div id="sliderAvancement" >&nbsp;</div>
<script type="text/javascript" >
	digirisk(document).ready(function(){
		jQuery("#' . $id . '").prop("readonly", "readonly");
		jQuery("#sliderAvancement").slider({
			value:' . $contenuInputAvancement . ',
			min: 0,
			max: 100,
			step: 1,
			slide:function(event, ui){
				jQuery("#' . $id . '").val(ui.value);
			}
		});
		jQuery("#' . $id . '").val(jQuery( "#sliderAvancement" ).slider("value"));
		jQuery("#' . $id . '").attr("style", jQuery( "#' . $id . '" ).attr("style") + "border:0px solid #000000;");
	});
</script>';

			/*	Sub-Task Responsible	*/
			$contenuAideDescription = "";		
			$labelInput = __("Responsable", 'evarisk');
			if(digirisk_options::getOptionValue('responsable_Action_Obligatoire') == 'oui'){
				$labelInput .= '&nbsp;<span class="fieldInfo required" >' . __('(obligatoire)', 'evarisk') . '</span>';
			}
			$labelInput .= ' : <span class="fieldInfo" >' . sprintf(__('(vous pouvez d&eacute;finir si ce champs est obligatoire ou non dans le menu %s du plugin)', 'evarisk'), '<a href="' . get_bloginfo('siteurl') . '/wp-admin/options-general.php?page=' . DIGI_URL_SLUG_MAIN_OPTION . '#digirisk_options_correctivaction" target="optionPage" >' . __('Options', 'evarisk') . '</a>') . '</span>'; 
			$id = "responsable_activite";
			$nomChamps = "responsable_activite";

			$activite_new .= '<br/><label for="search_user_responsable_' . $arguments['tableElement'] . '" >' . $labelInput . '</label>' . EvaDisplayInput::afficherInput('hidden', $id, $contenuInputResponsable, '', null, $nomChamps, false, false) . '<div id="responsible_name" >';
			$search_input_state = '';
			$change_input_state = 'hide';
			if($contenuInputResponsable > 0){
				$search_input_state = 'hide';
				$change_input_state = '';
				$responsible = evaUser::getUserInformation($contenuInputResponsable);
				$activite_new .= ELEMENT_IDENTIFIER_U . $contenuInputResponsable . '&nbsp;-&nbsp;' . $responsible[$contenuInputResponsable]['user_lastname'] . ' ' . $responsible[$contenuInputResponsable]['user_firstname'];
			}
			else
				$activite_new .= '&nbsp;';

			$activite_new .= '</div>&nbsp;<span id="change_responsible_' . $arguments['tableElement'] . 'responsible" class="' . $change_input_state . ' change_ac_responsible" >' . __('Changer', 'evarisk') . '</span>&nbsp;&nbsp;<span id="delete_responsible_' . $arguments['tableElement'] . 'responsible" class="' . $change_input_state . ' delete_ac_responsible" >' . __('Enlever le responsable', 'evarisk') . '</span><input class="searchUserToAffect ac_responsable ' . $search_input_state . '" type="text" name="responsable_name_' . $arguments['tableElement'] . '" id="search_user_responsable_' . $arguments['tableElement'] . '" placeholder="' . __('Rechercher dans la liste des utilisateurs', 'evarisk') . '" /><div id="completeUserList' . $arguments['tableElement'] . 'responsible' . $arguments['requested_action'] . '" class="completeUserList completeUserListActionResponsible hide clear" >' . evaUser::afficheListeUtilisateurTable_SimpleSelection($arguments['tableElement'] . 'responsible', $arguments['idElement']) . '</div>
	<script type="text/javascript" >
		digirisk(document).ready(function(){
			jQuery("#search_user_responsable_' . $arguments['tableElement'] . '").click(function(){
				jQuery(".completeUserListActionResponsible").show();
			});
			jQuery("#search_user_responsable_' . $arguments['tableElement'] . '").autocomplete({
				source: "' . EVA_INC_PLUGIN_URL . 'liveSearch/searchUsers.php?table_element=' . $tableElement . '&id_element=' . $arguments['idElement'] . '",
				select: function( event, ui ){
					jQuery("#responsable_activite").val(ui.item.value);
					jQuery("#responsible_name").html(ui.item.label);

					jQuery(".completeUserListActionResponsible").hide();
					jQuery(".searchUserToAffect").hide();
					jQuery("#change_responsible_' . $arguments['tableElement'] . 'responsible").show();
					jQuery("#delete_responsible_' . $arguments['tableElement'] . 'responsible").show();

					jQuery(this).val("");
					jQuery(this).blur();
				}
			});

			jQuery("#change_responsible_' . $arguments['tableElement'] . 'responsible").click(function(){
				jQuery("#search_user_responsable_' . $arguments['tableElement'] . '").show();
				jQuery("#completeUserList' . $arguments['tableElement'] . 'responsible").show();
				jQuery(this).hide();
			});
			jQuery("#delete_responsible_' . $arguments['tableElement'] . 'responsible").click(function(){
				jQuery("#responsable_activite").val("");
				jQuery("#responsible_name").html("&nbsp;");
				jQuery(this).hide();
				jQuery("#change_responsible_' . $arguments['tableElement'] . 'responsible").hide();
				jQuery("#search_user_responsable_' . $arguments['tableElement'] . '").show();
			jQuery("#completeUserList' . $arguments['tableElement'] . 'responsible").hide();
			});
		});
	</script><br class="clear" /><br/><br/><br/>';
		}
		{/*	Sub-Task Description	*/
			$contenuAideDescription = "";
			$labelInput = __("Description", 'evarisk') . ' : <div class="alignright" ><input type="checkbox" name="description_exportable_plan_action" id="description_exportable_plan_action" value="yes"'.(!empty($description_exportable_plan_action) && ($description_exportable_plan_action=='yes')?' checked="checked"':'').' />&nbsp;<label for="description_exportable_plan_action" >'.__('Exporter dans le plan d\'action', 'evarisk').'</label></div>';
			$id = "description_activite";
			$nomChamps = "description";
			$rows = 5;
			$activite_new .= EvaDisplayInput::afficherInput('textarea', $id, $contenuInputDescription, $contenuAideDescription, $labelInput, $nomChamps, $grise, true, $rows, '', '', '99%');
		}

		if(!empty($arguments['provenance']) && ($arguments['provenance'] != 'ask_correctiv_action')){/*	Add possibility for user to add a picture and to affect to an existing element for the asked tasks	*/
			/*	Add a picture	*/
			$token = rand();
			$activite_new .= '<div class="digirisk_hide" id="loading_round_pic" ><div class="main_loading_pic_container" ><img src="' . admin_url('images/loading.gif') . '" alt="loading..." /></div></div><input type="hidden" name="token_for_element" id="token_for_element" value="' . $token . '" /><label for="correctiv_action_before_pic" class="clear" >' . __('Photo', 'evarisk') . '</label>&nbsp;:&nbsp;<div id="ask_correctiv_action_picture_form" >' . self::task_asker_add_picture($arguments['tableProvenance'], $token) . '</div><div id="ask_correctiv_action_picture" class="digirisk_hide" ><img src="' . admin_url('images/loading.gif') . '" alt="correctiv_action_ask_picture" /><div class="pointer" id="dac_pic_change" >' . __('Changer l\'image', 'evarisk') . '</div><script type="text/javascript" >digirisk(document).ready(function(){jQuery("#dac_pic_change").click(function(){jQuery("#ask_correctiv_action_picture_form").html(jQuery("#loading_round_pic").html());jQuery("#ask_correctiv_action_picture img").attr("src", "' . admin_url('images/loading.gif') . '");jQuery("#ask_correctiv_action_picture").hide();jQuery("#ask_correctiv_action_picture_form").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",{"post":"true", "table":"' . TABLE_ACTIVITE . '", "act":"reload_correctiv_asker_picture_form", "tableProvenance":"' . $arguments['tableProvenance'] . '", "token":"' . $token . '", "delete_old":"yes"});});});</script></div>';

			/*	Affect to existing element	*/
			$activite_new .= __('Affecter cette t&acirc;che &agrave; un &eacute;l&eacute;ment existant', 'evarisk') . '&nbsp;:&nbsp;<br/>' . arborescence_special::search_form();
		}

		if(isset($arguments['requested_action']) && (($arguments['requested_action'] == 'ficheAction') || ($arguments['requested_action'] == 'control_asked_action'))){/*	Risk new level cotation	*/
			{/*	Task efficiency	*/
				$activite_new .= sprintf(__('Efficacit&eacute; de la t&acirc;che %s', 'evarisk'), '<input type="text" name="correctiv_action_efficiency_control" id="correctiv_action_efficiency_control' . $idElement . '" value="0" class="correctiv_action_efficiency_control" readonly="readonly" />%') . '<div id="correctiv_action_efficiency_control_slider' . $idElement . '" class="correctiv_action_efficiency_control_slider" >&nbsp;</div>';
			}
			{/*	Load the different var for the risk associated method	*/
				$activite_new .= '
				<fieldset id="divVariablesFormRisque-simpleFAC-fieldset" >
					<legend class="bold" >' . __('Nouvelle &Eacute;valuation', 'evarisk') . ' :</legend>
					<script type="text/javascript">
						digirisk(document).ready(function(){
							setTimeout(function(){
								digirisk("#divVariablesFormRisque-simpleFAC").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",{
									"post":"true", 
									"table":"' . TABLE_METHODE . '", 
									"act":"reloadVariables",
									"idRisque":"' . $arguments['idProvenance'] . '"
								});
							}, 700);
							digirisk("#sliderAvancement").hide();
						})
					</script>
					<div id="divVariablesFormRisque-simpleFAC" ></div>
				</fieldset>';
			}
			{/*	Current risk description	*/
				$contenuInput = '';
				if($arguments['idProvenance'] != ''){
					$risque = Risque::getRisque($arguments['idProvenance']);
				}
				else{
					$risque = null;
				}
				if($risque[0] != null){// Si l'on édite un risque, on remplit l'aire de texte avec sa description
					$contenuInput = $risque[0]->commentaire;
				}
				$labelInput = ucfirst(strtolower(sprintf(__("commentaire %s", 'evarisk'), __('sur le risque', 'evarisk'))));
				$labelInput[1] = ($labelInput[0] == "&") ? ucfirst($labelInput[1]) : $labelInput[1];
				$activite_new .= '<br/><div id="divDescription" class="clear risk_description_container" >' . EvaDisplayInput::afficherInput('textarea', 'descriptionFormRisque', $contenuInput, '', $labelInput . ' : ', 'description_risque', false, DESCRIPTION_RISQUE_OBLIGATOIRE, 3, '', '', '95%', '') . '</div>';
			}
		}

		{/*	Put in progress button	*/
			$idBouttonSetInProgress = 'setActivityInProgress';
			$scriptEnregistrementInProgress = '<script type="text/javascript">
				digirisk("#' . $idBouttonSetInProgress . '").click(function(){
					digirisk("#inProgressButtonContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",{
						"post": "true", 
						"table": "' . TABLE_ACTIVITE . '",
						"act": "setActivityInProgress",
						"id": digirisk("#id_activite").val()
					});
				});
			</script>';
		}

		$sub_task_creation_form = 
'<form method="post" id="informationGeneralesActivite" action="' . EVA_INC_PLUGIN_URL . 'ajax.php" >' . 
$activite_new . EvaDisplayInput::fermerForm('informationGeneralesActivite');

		if((!empty($arguments['output_mode']) && $arguments['output_mode'] == 'return') && !empty($arguments['provenance']) && ($arguments['provenance'] != 'ask_correctiv_action')){/*	Add picture button			*/
			$sub_task_creation_form .= '<div id="photosActionsCorrectives" >&nbsp;</div>';
			$addPictureButton = 
				'<div id="add_picture_alert" class="hide" title="' . __('Modification de la cotation d\'un risque depuis une action corrective', 'evarisk') . '" >&nbsp;</div><input type="button" name="add_control_picture" id="add_control_picture" class="button-primary alignleft" value="' . __('Enregistrer puis ajouter des photos', 'evarisk') . '" />';
		}
		{/*	Add buttons to output		*/
			$inProgressButton = '';
			if(($saveOrUpdate == 'update') && ($ProgressionStatus != '') && ($ProgressionStatus != 'inProgress') && ($contenuInputAvancement != '100')){
				$inProgressButton = '<span id="inProgressButtonContainer" class="alignleft" >' . EvaDisplayInput::afficherInput('button', $idBouttonSetInProgress, __('Passer en cours', 'evarisk'), null, '', $idBouttonSetInProgress, false, true, '', 'button-secondary', '', '', $scriptEnregistrementInProgress, 'left') . '</span>';
			}
			if(($saveOrUpdate == 'add_control') || ($saveOrUpdate == 'ask_correctiv_action') || ($saveOrUpdate == 'addAction') || ($saveOrUpdate == 'save') || ($ProgressionStatus == '') || ($ProgressionStatus == 'inProgress') || ($ProgressionStatus == 'notStarted') || (digirisk_options::getOptionValue('possibilite_Modifier_Action_Soldee') == 'oui')){
				$sub_task_creation_form .= 
					'<div class="alignright" id="ActionSaveButton" >' . $inProgressButton;

				if(($saveOrUpdate == 'update') && (($ProgressionStatus == '') || ($ProgressionStatus == 'notStarted') || ($ProgressionStatus == 'inProgress'))){
					$sub_task_creation_form .= 
						EvaDisplayInput::afficherInput('button', $idBouttonSold, __('Solder l\'action', 'evarisk'), null, '', $idBouttonSold, false, true, '', 'button-secondary', '', '', '', 'left');
				}
				elseif($saveOrUpdate == 'update'){
					$sub_task_creation_form .= 
						'<div style="float:left;" id="ActionSaveButton" >
							<br/>
							<div class="alignright button-primary" >' . 
								__('Cette action est sold&eacute;e', 'evarisk') . 
							'</div>
						</div>';
				}

				$sub_task_creation_form .= 
					$addPictureButton . 
					'<div id="save_button_container"  >' . EvaDisplayInput::afficherInput('button', $idBouttonEnregistrer, __('Enregistrer', 'evarisk'), null, '', $idBouttonEnregistrer, false, true, '', 'button-primary', '', '', '', 'left') . 
					'</div><div id="save_in_progress" class="alignright digirisk_hide" ><img src="' . admin_url('images/loading.gif') . '" alt="loading in progress" /></div></div>';
			}
			else{
				$sub_task_creation_form .= 
					'<div class="alignright button-primary" id="ActionSaveButton" >' . 
						__('Cette action est sold&eacute;e, vous ne pouvez pas la modifier', 'evarisk') . 
					'</div>';
			}
		}

		$sub_task_creation_form .= '
<script type="text/javascript" >
	digirisk(document).ready(function(){
		jQuery("#ActionSaveButton").children("br").remove();
		jQuery("#ActionSaveButton div").children("br").remove();

		jQuery("#putTodayActionStart").click(function(){
			jQuery("#date_debut_activite").val("' . date('Y-m-d') . '");
		});
		jQuery("#putTodayActionEnd").click(function(){
			jQuery("#date_fin_activite").val("' . date('Y-m-d') . '");
		});

		jQuery("#' . $idBouttonEnregistrer . '").click(function(){
			jQuery("#informationGeneralesActivite").submit();
		});

		jQuery("#add_picture_alert").dialog({
			autoOpen:false,
			height:400,
			width:800,
			modal:true
		});
		jQuery("#add_control_picture").click(function(){';
		if(isset($arguments['requested_action']) && ($arguments['requested_action'] != 'demandeAction')){
			$sub_task_creation_form .= '
			var variables = new Array;
			jQuery("#divVariablesFormRisque-simpleFAC input").each(function(){
				variables.push({var: jQuery(this).attr("name"), val: jQuery(this).val()});
			});
			jQuery("#add_picture_alert").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",{
				"post":"true", 
				"table":"' . TABLE_RISQUE . '", 
				"act":"load_quote_validation",
				"idProvenance": "' . $_REQUEST['idProvenance'] . '",
				"tableProvenance": "' . $_REQUEST['tableProvenance'] . '",
				"vars": variables,
				"new_description" : jQuery("#descriptionFormRisque").val()
			});
			jQuery("#add_picture_alert").dialog("open");';
		}
		else{
			$sub_task_creation_form .= '
			jQuery("#act").val("add_control_picture");
			jQuery("#informationGeneralesActivite").submit();';
		}
		$sub_task_creation_form .= '
		});

		jQuery("#' . $idBouttonSold . '").click(function(){
			if(((jQuery("#avancement_activite").val() == "100") && (jQuery("#avancement_activite").val() == "100")) || ("' . $alertWhenMarkActionAsDone . '" == "non") || (("' . $alertWhenMarkActionAsDone . '" == "oui") && confirm((digi_html_accent_for_js("' . __("Vous &eacute;tes sur le point de solder une action dont l\'avancement est de #avancement#%.#retour#Etes vous sur de vouloir continuer ?", 'evarisk') . '").replace("#avancement#", jQuery("#avancement_activite").val())).replace("#retour#", "\r\n")))){
				jQuery("#act").val("actionDone");
				jQuery("#actionDone").html(\'<img src="' . PICTO_LOADING_ROUND . '" alt="loading" />\');
				jQuery("#informationGeneralesActivite").submit();
			}
		});

		jQuery("#informationGeneralesActivite").ajaxForm({
			target: "#ajax-response",
			beforeSubmit: validate_activity_form,
			resetForm: true
		});

		jQuery(".correctiv_action_efficiency_control_slider").slider({
			value:0,
			min: 0,
			max: 100,
			step: 1,
			slide: function(event, ui){
				jQuery("#" + jQuery(this).attr("id").replace("correctiv_action_efficiency_control_slider", "correctiv_action_efficiency_control")).val( ui.value );
			}
		});
	});

	function validate_activity_form(formData, jqForm, options){
		if(digirisk("#' . $idTitre . '").is(".form-input-tip")){
			digirisk("#' . $idTitre . '").val("");
			digirisk("#' . $idTitre . '").removeClass("form-input-tip");
		}

		for(var i=0; i < formData.length; i++){
			if((formData[i].name == "' . $idTitre . '") && !formData[i].value){
				alert(digi_html_accent_for_js("' . __("Vous n\'avez pas donn&eacute; de nom a l'action", 'evarisk') . '"));
				return false;
			}
			else if((formData[i].name == "responsable_activite") && (!formData[i].value || (formData[i].value <= 0)) && ("' . $idResponsableIsMandatory . '" == "oui")){
				alert(digi_html_accent_for_js("' . __("Vous devez choisir une personne en charge de l\'action", 'evarisk') . '"));
				return false;
			}
		}

		if(jQuery("#ask_correctiv_action_picture_form").length > 0){
			jQuery("#ask_correctiv_action_picture_form").html("");
		}
		
		jQuery("#save_button_container").hide();
		jQuery("#save_in_progress").show();

		return true;
	}

</script>';

		if(!empty($arguments['output_mode']) && $arguments['output_mode'] == 'return'){
			return $sub_task_creation_form;
		}

		echo $sub_task_creation_form;
	}

}