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
			$grise = false;
			$idPere = $activite->getRelatedTaskId();
			$saveOrUpdate = 'update';
			if(isset($arguments['requested_action']) && ($arguments['requested_action'] == 'control_asked_action')){
				$saveOrUpdate = 'add_control';
				$contenuInputAvancement = 100;
			}
		}
		else{
			$contenuInputTitre = $contenuInputDescription = $contenuInputRealisateur = $contenuInputResponsable = '';
			$contenuInputDateDebut = date('Y-m-d');
			$contenuInputDateFin = date('Y-m-d');
			$ProgressionStatus = 'Done';
			$contenuInputAvancement = 0;
			$idPere = $arguments['idPere'];
			$grise = true;
			$saveOrUpdate = 'save';
			if(isset($arguments['requested_action']) && ($arguments['requested_action'] == 'demandeAction')){
				$saveOrUpdate = 'addAction';
			}
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
	EvaDisplayInput::afficherInput('hidden', 'original_act', $arguments['requested_action'], '', null, 'original_act', false, false) . 
	EvaDisplayInput::afficherInput('hidden', 'affichage_activite', $arguments['affichage'], '', null, 'affichage', false, false) . 
	EvaDisplayInput::afficherInput('hidden', 'table', TABLE_ACTIVITE, '', null, 'table', false, false) . 
	EvaDisplayInput::afficherInput('hidden', 'id_activite', $idElement, '', null, 'id', false, false) . 
	EvaDisplayInput::afficherInput('hidden', 'idPere_activite', $idPere, '', null, 'idPere', false, false) . 
	EvaDisplayInput::afficherInput('hidden', 'idsFilAriane_activite', $arguments['idsFilAriane'], '', null, 'idsFilAriane', false, false) . 
	EvaDisplayInput::afficherInput('hidden', 'idProvenance_activite', $arguments['idProvenance'], '', null, 'idProvenance', false, false) . 
	EvaDisplayInput::afficherInput('hidden', 'tableProvenance_activite', $arguments['tableProvenance'], '', null, 'tableProvenance', false, false);
		}
		{/*	Sub-Task name					*/
			$contenuAideTitre = "";
			$labelInput = ucfirst(sprintf(__("nom %s", 'evarisk'), __("de l'action",'evarisk'))) . '&nbsp;<span class="fieldInfo required" >' . __('(obligatoire)', 'evarisk') . '</span> :';
			$nomChamps = "nom_activite";
			$idTitre = "nom_activite";
			$activite_new .= EvaDisplayInput::afficherInput('text', $idTitre, $contenuInputTitre, $contenuAideTitre, $labelInput, $nomChamps, $grise, true, 255, 'titleInput', '', '99%');
		}
		{/*	Sub-Task start date		*/
			$contenuAideTitre = "";
			$id = "date_debut_activite";
			$label = '<label for="' . $id . '" >' . ucfirst(sprintf(__("Date de d&eacute;but %s", 'evarisk'), __("de l'action",'evarisk'))) . '</label> : <span class="fieldInfo pointer" id="putTodayActionStart" >' . __('Aujourd\'hui', 'evarisk') . '</span>';
			$labelInput = '';
			$nomChamps = "date_debut";
			$activite_new .= $label . EvaDisplayInput::afficherInput('text', $id, $contenuInputDateDebut, $contenuAideTitre, $labelInput, $nomChamps, $grise, true, 255, '', 'date', '99%') . '';
		}
		{/*	Sub-Task end date			*/
			$contenuAideTitre = "";
			$id = "date_fin_activite";
			$label = '<label for="' . $id . '" >' . ucfirst(sprintf(__("Date de fin %s", 'evarisk'), __("de l'action",'evarisk'))) . '</label> : <span class="fieldInfo pointer" id="putTodayActionEnd" >' . __('Aujourd\'hui', 'evarisk') . '</span>';
			$labelInput = '';
			$nomChamps = "date_fin";
			$activite_new .= $label . EvaDisplayInput::afficherInput('text', $id, $contenuInputDateFin, $contenuAideTitre, $labelInput, $nomChamps, $grise, true, 255, '', 'date', '99%') . '';
		}
		{/*	Sub-Task cost					*/
			$contenuAideDescription = "";
			$labelInput = __("Co&ucirc;t", 'evarisk') . ' : ';
			$id = "cout_activite";
			$nomChamps = "cout";
			$activite_new .= EvaDisplayInput::afficherInput('text', $id, $contenuInputCout, $contenuAideDescription, $labelInput, $nomChamps, $grise, true, 255, '', '', '99%');
		}
		{/*	Sub-Task progression	*/
			$id = "avancement_activite";
			$nomChamps = "avancement";
			$activite_new .= __("Avancement", 'evarisk') . ' : 
<input type="text" name="' . $nomChamps . '" id="' . $id . '" style="width:5%;" value="' . $contenuInputAvancement . '" />' . __('%', 'evarisk') . '<div id="sliderAvancement" >&nbsp;</div>
<script type="text/javascript" >
	evarisk(document).ready(function(){
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
		}
		{/*	Sub-Task Responsible	*/
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
			else{
				$activite_new .= '&nbsp;';
			}
			$activite_new .= '</div>&nbsp;<span id="change_responsible_' . $arguments['tableElement'] . 'responsible" class="' . $change_input_state . ' change_ac_responsible" >' . __('Changer', 'evarisk') . '</span>&nbsp;&nbsp;<span id="delete_responsible_' . $arguments['tableElement'] . 'responsible" class="' . $change_input_state . ' delete_ac_responsible" >' . __('Enlever le responsable', 'evarisk') . '</span><input class="searchUserToAffect ac_responsable ' . $search_input_state . '" type="text" name="responsable_name_' . $arguments['tableElement'] . '" id="search_user_responsable_' . $arguments['tableElement'] . '" value="' . __('Rechercher dans la liste des utilisateurs', 'evarisk') . '" /><div id="completeUserList' . $arguments['tableElement'] . 'responsible' . $arguments['requested_action'] . '" class="completeUserList completeUserListActionResponsible hide clear" >' . evaUser::afficheListeUtilisateurTable_SimpleSelection($arguments['tableElement'] . 'responsible', $arguments['idElement']) . '</div>
	<script type="text/javascript" >
		evarisk(document).ready(function(){
			jQuery("#search_user_responsable_' . $arguments['tableElement'] . '").click(function(){
				jQuery(this).val("");
				jQuery(".completeUserListActionResponsible").show();
			});
			jQuery("#search_user_responsable_' . $arguments['tableElement'] . '").blur(function(){
				jQuery(this).val(convertAccentToJS("' . __('Rechercher dans la liste des utilisateurs', 'evarisk') . '"));
			});
			jQuery("#search_user_responsable_' . $arguments['tableElement'] . '").autocomplete("' . EVA_INC_PLUGIN_URL . 'liveSearch/searchUsers.php?table_element=' . $tableElement . '&id_element=' . $arguments['idElement'] . '");
			jQuery("#search_user_responsable_' . $arguments['tableElement'] . '").result(function(event, data, formatted){
				jQuery("#responsable_activite").val(data[1]);
				jQuery("#responsible_name").html(data[0]);
				jQuery("#search_user_responsable_' . $arguments['tableElement'] . '").val(convertAccentToJS("' . __('Rechercher dans la liste des utilisateurs', 'evarisk') . '"));
				jQuery(".completeUserListActionResponsible").hide();
				jQuery(".searchUserToAffect").hide();
				jQuery("#change_responsible_' . $arguments['tableElement'] . 'responsible").show();
				jQuery("#delete_responsible_' . $arguments['tableElement'] . 'responsible").show();
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
			$labelInput = __("Description", 'evarisk') . ' : ';
			$id = "description_activite";
			$nomChamps = "description";
			$rows = 5;
			$activite_new .= EvaDisplayInput::afficherInput('textarea', $id, $contenuInputDescription, $contenuAideDescription, $labelInput, $nomChamps, $grise, true, $rows, '', '', '99%');
		}

		if(isset($arguments['requested_action']) && (($arguments['requested_action'] == 'ficheAction') || ($arguments['requested_action'] == 'control_asked_action'))){/*	Risk new level cotation	*/
			{/*	Task efficiency	*/
				$activite_new .= sprintf(__('Efficacit&eacute; de la t&acirc;che %s', 'evarisk'), '<input type="text" name="correctiv_action_efficiency_control" id="correctiv_action_efficiency_control' . $tache->id . '" value="0" class="correctiv_action_efficiency_control" readonly="readonly" />%') . '<div id="correctiv_action_efficiency_control_slider' . $tache->id . '" class="correctiv_action_efficiency_control_slider" >&nbsp;</div>';
			}
			{/*	Load the different var for the risk associated method	*/
				$activite_new .= '
				<fieldset id="divVariablesFormRisque-simpleFAC-fieldset" >
					<legend class="bold" >' . __('Nouvelle &Eacute;valuation', 'evarisk') . ' :</legend>
					<script type="text/javascript">
						evarisk(document).ready(function(){
							setTimeout(function(){
								evarisk("#divVariablesFormRisque-simpleFAC").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",{
									"post":"true", 
									"table":"' . TABLE_METHODE . '", 
									"act":"reloadVariables",
									"idRisque":"' . $arguments['idProvenance'] . '"
								});
							}, 700);
							evarisk("#sliderAvancement").hide();
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
				evarisk("#' . $idBouttonSetInProgress . '").click(function(){
					evarisk("#inProgressButtonContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",{
						"post": "true", 
						"table": "' . TABLE_ACTIVITE . '",
						"act": "setActivityInProgress",
						"id": evarisk("#id_activite").val()
					});
				});
			</script>';
		}

		$sub_task_creation_form = 
'<form method="post" id="informationGeneralesActivite" name="informationGeneralesActivite" action="' . EVA_INC_PLUGIN_URL . 'ajax.php" >' . 
$activite_new . EvaDisplayInput::fermerForm('informationGeneralesActivite');

		if($arguments['output_mode'] == 'return'){/*	Add picture button			*/
			$sub_task_creation_form .= '<div id="photosActionsCorrectives" >&nbsp;</div>';
			$addPictureButton = 
				'<div id="add_picture_alert" class="hide" title="' . __('Modification de la cotation d\'un risque depuis une action corrective', 'evarisk') . '" >&nbsp;</div><input type="button" name="add_control_picture" id="add_control_picture" class="button-primary alignleft" value="' . __('Enregistrer puis ajouter des photos', 'evarisk') . '" />';
		}
		{/*	Add buttons to output		*/
			$inProgressButton = '';
			if(($saveOrUpdate == 'update') && ($ProgressionStatus != '') && ($ProgressionStatus != 'inProgress') && ($contenuInputAvancement != '100')){
				$inProgressButton = '<span id="inProgressButtonContainer" class="alignleft" >' . EvaDisplayInput::afficherInput('button', $idBouttonSetInProgress, __('Passer en cours', 'evarisk'), null, '', $idBouttonSetInProgress, false, true, '', 'button-secondary', '', '', $scriptEnregistrementInProgress, 'left') . '</span>';
			}
			if(($saveOrUpdate == 'add_control') || ($saveOrUpdate == 'addAction') || ($saveOrUpdate == 'save') || ($ProgressionStatus == '') || ($ProgressionStatus == 'inProgress') || ($ProgressionStatus == 'notStarted') || (digirisk_options::getOptionValue('possibilite_Modifier_Action_Soldee') == 'oui')){
				$sub_task_creation_form .= 
					'<div class="alignright" id="ActionSaveButton" >' . $inProgressButton;

				if(($saveOrUpdate == 'update') && (($ProgressionStatus == '') || ($ProgressionStatus == 'notStarted') || ($ProgressionStatus == 'inProgress'))){
					$sub_task_creation_form .= 
						EvaDisplayInput::afficherInput('button', $idBouttonSold, __('Solder l\'action', 'evarisk'), null, '', $idBouttonSold, false, true, '', 'button-secondary', '', '', $scriptEnregistrementDone, 'left');
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
					EvaDisplayInput::afficherInput('button', $idBouttonEnregistrer, __('Enregistrer', 'evarisk'), null, '', $idBouttonEnregistrer, false, true, '', 'button-primary', '', '', '', 'left') . 
					'</div>';
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
	evarisk(document).ready(function(){
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
			if(((jQuery("#avancement_activite").val() == "100") && (jQuery("#avancement_activite").val() == "100")) || ("' . $alertWhenMarkActionAsDone . '" == "non") || (("' . $alertWhenMarkActionAsDone . '" == "oui") && confirm((convertAccentToJS("' . __("Vous &eacute;tes sur le point de solder une action dont l\'avancement est de #avancement#%.#retour#Etes vous sur de vouloir continuer ?", 'evarisk') . '").replace("#avancement#", jQuery("#avancement_activite").val())).replace("#retour#", "\r\n")))){
				jQuery("#act").val("actionDone");
				jQuery("#actionDone").html(\'<img src="' . PICTO_LOADING_ROUND . '" alt="loading" />\');
				jQuery("#informationGeneralesActivite").submit();
			}
		});

		jQuery("#informationGeneralesActivite").ajaxForm({
			target: "#ajax-response",
			beforeSubmit: validate_activity_form
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
		if(evarisk("#' . $idTitre . '").is(".form-input-tip")){
			evarisk("#' . $idTitre . '").val("");
			evarisk("#' . $idTitre . '").removeClass("form-input-tip");
		}

		for(var i=0; i < formData.length; i++){
			if((formData[i].name == "' . $idTitre . '") && !formData[i].value){
				alert(convertAccentToJS("' . __("Vous n\'avez pas donn&eacute; de nom a l'action", 'evarisk') . '"));
				return false;
			}
			else if((formData[i].name == "responsable_activite") && (!formData[i].value || (formData[i].value <= 0)) && ("' . $idResponsableIsMandatory . '" == "oui")){
				alert(convertAccentToJS("' . __("Vous devez choisir une personne en charge de l\'action", 'evarisk') . '"));
				return false;
			}
		}

		return true;
	}

</script>';

		if($arguments['output_mode'] == 'return'){
			return $sub_task_creation_form;
		}

		echo $sub_task_creation_form;
	}

}