<?php
/**
*	The different utilities to manage users in evarisk
*
*	@package 		Digirisk
*	@subpackage users
* @author			Evarisk <dev@evarisk.com>
*/

class evaUser
{
	/**
	*	Set the current attribute group regarding the 
	*/
	protected $_currentAttributeGroup = '';
	/**
	*	Set the current attribute group regarding the 
	*/
	protected $_userToUpdate = '';



	/**
	*	Get the wordpress' user list
	*
	*	@return array $userlist An object containing the different subscriber
	*/
	function getUserList()
	{
		global $wpdb;

		$query = 
			"SELECT USERS.ID
			FROM " . $wpdb->users . " AS USERS";
		$userList = $wpdb->get_results($query);

		return $userList;
	}
	/**
	*	Get the wordpress' user list
	*
	*	@return array $userlist An object containing the different subscriber
	*/
	function getCompleteUserList()
	{
		$listeComplete = array();

		$listeUtilisateurs = evaUser::getUserList();
		foreach($listeUtilisateurs as $utilisateurs){
			if($utilisateurs->ID != 1){
				$user_info = get_userdata($utilisateurs->ID);

				unset($valeurs);
				$valeurs['user_id'] = $user_info->ID;
				$valeurs['user_registered'] = $user_info->user_registered;
				if( (isset($user_info->user_lastname) && ($user_info->user_lastname != '')) ){
					$valeurs['user_lastname'] = $user_info->user_lastname;
				}
				else{
					$valeurs['user_lastname'] = '';
				}
				if( (isset($user_info->user_firstname) && ($user_info->user_firstname != '')) ){
					$valeurs['user_firstname'] = $user_info->user_firstname;
				}
				else{
					$valeurs['user_firstname'] = $user_info->user_nicename;
				}

				$listeComplete[$user_info->ID] = $valeurs;
			}
		}

		return $listeComplete;
	}
	/**
	*	Get the wordpress' user list
	*
	*	@return array $userlist An object containing the different subscriber
	*/
	function getUserInformation($userId)
	{
		$listeComplete = array();

		$user_info = get_userdata($userId);

		unset($valeurs);
		$valeurs['user_id'] = $user_info->ID;
		if( (isset($user_info->user_lastname) && ($user_info->user_lastname != '')) )
		{
			$valeurs['user_lastname'] = $user_info->user_lastname;
		}
		else
		{
			$valeurs['user_lastname'] = '';
		}
		if( (isset($user_info->user_firstname) && ($user_info->user_firstname != '')) )
		{
			$valeurs['user_firstname'] = $user_info->user_firstname;
		}
		else
		{
			$valeurs['user_firstname'] = $user_info->user_nicename;
		}

		$listeComplete[$user_info->ID] = $valeurs;

		return $listeComplete;
	}

	/**
	*	Add the different mandatory fields for the user in case of accident
	*/
	function user_additionnal_field_save($user_id){
		global $userWorkAccidentMandatoryFields;
		$user_is_valid_for_accident = 'yes';
		foreach($userWorkAccidentMandatoryFields as $field_identifier){
			if(isset($_REQUEST['digirisk_user_information'][$field_identifier]) && (trim($_REQUEST['digirisk_user_information'][$field_identifier]) == '')){
				$user_is_valid_for_accident = 'no';
			}
		}
		$_REQUEST['digirisk_user_information']['user_is_valid_for_accident'] = $user_is_valid_for_accident;
		update_usermeta($user_id, 'digirisk_information', $_REQUEST['digirisk_user_information']);
	}
	/**
	*	Add the different mandatory fields for the user in case of accident
	*/
	function user_additionnal_field($user, $output_type = 'normal'){
		global $optionUserGender, $optionUserNationality, $userWorkAccidentMandatoryFields;
		$user_additionnal_field = $user_additionnal_field_alert = '';
		$required_class = array();

		/*	Get the current user meta	*/
		$user_meta = get_user_meta($user->ID, 'digirisk_information', false);
		if(is_array($user_meta[0]) && (count($user_meta[0]) > 0)){
			$required_filed_empty = 0;
			foreach($user_meta[0] as $field_identifier => $field_content){
				$required_class[$field_identifier] = '';
				if((trim($field_content) == '') && (in_array($field_identifier, $userWorkAccidentMandatoryFields))){
					if($field_identifier == 'user_imatriculation_key'){
						$field_identifier = 'user_imatriculation';
					}
					elseif($field_identifier == 'user_adress_2'){
						$field_identifier = 'user_adress';
					}
					$required_class[$field_identifier] = ' class="required" ';
					$required_filed_empty++;
				}
			}
			if($required_filed_empty > 0){
				$user_additionnal_field_alert = '<span class="required" >' . __('Les champs marqu&eacutes en rouge sont obligatoire pour que l\'utilisateur soit &eacute;ligible &agrave; la d&eacute;claration d\'accident du travail', 'evarisk') . '</span>';
			}
		}
		else{
			$user_additionnal_field_alert = '<span class="required" >' . __('L\'ensemble des champs ci-dessous sont obligatoire pour que l\'utilisateur soit &eacute;ligible &agrave; la d&eacute;claration d\'accident du travail', 'evarisk') . '</span>';
			$required_class['user_imatriculation'] = $required_class['user_birthday'] = $required_class['user_gender'] = $required_class['user_nationnality'] = $required_class['user_adress'] =  $required_class['user_hiring_date'] = $required_class['user_profession'] = $required_class['user_professional_qualification'] = ' class="required" ';
		}
		if($output_type == 'normal'){
			$user_additionnal_field .= '
<h3 id="digi_user_informations" >' . __('Informations compl&eacute;mentaires pour le logiciel Digirisk', 'digirisk') . '</h3>
' . $user_additionnal_field_alert;
			if($user_meta[0]['user_is_valid_for_accident'] == 'yes'){
				$user_additionnal_field .= '<span class="user_is_valid_for_accident" >' . __('L\'utilisateur est &eacute;ligible &agrave; la d&eacute;claration d\'un accident du travail', 'evarisk') . '</span>';
			}
			elseif($user_meta[0]['user_is_valid_for_accident'] == 'no'){
				$user_additionnal_field .= '<span class="user_is_not_valid_for_accident" >' . __('L\'utilisateur n\'est pas &eacute;ligible &agrave; la d&eacute;claration d\'un accident du travail', 'evarisk') . '</span>';
			}
		}
$user_additionnal_field .= '
<table class="' . (($output_type == 'normal') ? 'form-table' : '') . '" >
	<tr>
		<th>
			<label for="user_imatriculation" ' . $required_class['user_imatriculation'] . ' >' . __('N&ordm; d\'immatriculation', 'evarisk') . '</label>
		</th>
		<td>
			' .	EvaDisplayInput::afficherInput('text', 'user_imatriculation', $user_meta[0]['user_imatriculation'], '', null, 'digirisk_user_information[user_imatriculation]', false, false, 13, 'regular-text', '', '', '', 'digi_user_immatriculation', true) . '
			' .	EvaDisplayInput::afficherInput('text', 'user_imatriculation_key', $user_meta[0]['user_imatriculation_key'], '', null, 'digirisk_user_information[user_imatriculation_key]', false, false, 2, '', '', '10%', '', '', true) . '
		</td>
	</tr>
	<tr>
		<th>
			<label for="user_birthday" ' . $required_class['user_birthday'] . ' >' . __('Date de naissance', 'evarisk') . '</label>
		</th>
		<td>
			' .	EvaDisplayInput::afficherInput('text', 'user_birthday', $user_meta[0]['user_birthday'], '', null, 'digirisk_user_information[user_birthday]', false, false, 10, 'regular-text', 'date', '') . '
		</td>
	</tr>
	<tr>
		<th>
			<label for="user_gender" ' . $required_class['user_gender'] . ' >' . __('Sexe', 'evarisk') . '</label>
		</th>
		<td>
			' .	EvaDisplayInput::createComboBox('user_gender', 'digirisk_user_information[user_gender]', $optionUserGender, $user_meta[0]['user_gender'], 'user_combo') . '
		</td>
	</tr>
	<tr>
		<th>
			<label for="user_nationnality" ' . $required_class['user_nationnality'] . ' >' . __('Nationalit&eacute;', 'evarisk') . '</label>
		</th>
		<td>
			' .	EvaDisplayInput::createComboBox('user_nationnality', 'digirisk_user_information[user_nationnality]', $optionUserNationality, $user_meta[0]['user_nationnality'], 'user_combo') . '
		</td>
	</tr>';
	if($output_type == 'normal'){
		$user_additionnal_field .= 
	'<tr>
		<th>
			&nbsp;
		</th>
		<td>
			&nbsp;
		</td>
	</tr>';
	}
		$user_additionnal_field .= '
	<tr>
		<th>
			<label for="user_adress" ' . $required_class['user_adress'] . ' >' . __('Adresse ligne 1', 'evarisk') . '</label><br/>
			<label for="user_adress_2" ' . $required_class['user_adress_2'] . ' >' . __('Adresse ligne 2', 'evarisk') . '</label>
		</th>
		<td>
			' .	EvaDisplayInput::afficherInput('text', 'user_adress', $user_meta[0]['user_adress'], '', null, 'digirisk_user_information[user_adress]', false, false, 255, 'regular-text', '', '', '', '', true) . '
			' .	EvaDisplayInput::afficherInput('text', 'user_adress_2', $user_meta[0]['user_adress_2'], '', null, 'digirisk_user_information[user_adress_2]', false, false, 255, 'regular-text', '', '', '', '', true) . '
		</td>
	</tr>
	<tr>
		<th>
			<label for="user_adress_postal_code" ' . $required_class['user_adress'] . ' >' . __('Code postal', 'evarisk') . '</label>
		</th>
		<td>
			' .	EvaDisplayInput::afficherInput('text', 'user_adress_postal_code', $user_meta[0]['user_adress_postal_code'], '', null, 'digirisk_user_information[user_adress_postal_code]', false, false, 255, 'regular-text', '', '', '', '', true) . '
		</td>
	</tr>
	<tr>
		<th>
			<label for="user_adress_city" ' . $required_class['user_adress'] . ' >' . __('Ville', 'evarisk') . '</label>
		</th>
		<td>
			' .	EvaDisplayInput::afficherInput('text', 'user_adress_city', $user_meta[0]['user_adress_city'], '', null, 'digirisk_user_information[user_adress_city]', false, false, 255, 'regular-text', '', '', '', '', true) . '
		</td>
	</tr>
	<tr>
		<th>
			&nbsp;
		</th>
		<td>
			&nbsp;
		</td>
	</tr>
	<tr>
		<th>
			<label for="user_hiring_date" ' . $required_class['user_hiring_date'] . ' >' . __('Date d\'embauche', 'evarisk') . '</label>
		</th>
		<td>
			' .	EvaDisplayInput::afficherInput('text', 'user_hiring_date', $user_meta[0]['user_hiring_date'], '', null, 'digirisk_user_information[user_hiring_date]', false, false, 10, 'regular-text', 'date', '') . '
		</td>
	</tr>
	<tr>
		<th>
			<label for="user_profession" ' . $required_class['user_profession'] . ' >' . __('Profession', 'evarisk') . '</label>
		</th>
		<td>
			' .	EvaDisplayInput::afficherInput('text', 'user_profession', $user_meta[0]['user_profession'], '', null, 'digirisk_user_information[user_profession]', false, false, 255, 'regular-text', '', '', '', '', true) . '
		</td>
	</tr>
	<tr>
		<th>
			<label for="user_professional_qualification" ' . $required_class['user_professional_qualification'] . ' >' . __('Qualification professionnelle', 'evarisk') . '</label>
		</th>
		<td>
			' .	EvaDisplayInput::afficherInput('text', 'user_professional_qualification', $user_meta[0]['user_professional_qualification'], '', null, 'digirisk_user_information[user_professional_qualification]', false, false, 255, 'regular-text', '', '', '', '', true) . '
		</td>
	</tr>
	<tr>
		<th>
			&nbsp;
		</th>
		<td>
			&nbsp;
		</td>
	</tr>
	<tr>
		<th>
			<label for="user_insurance_ste" ' . $required_class['user_insurance_ste'] . ' >' . __('Soci&eacute;t&eacute; d\'assurance', 'evarisk') . '</label>
		</th>
		<td>
			' .	EvaDisplayInput::afficherInput('text', 'user_insurance_ste', $user_meta[0]['user_insurance_ste'], '', null, 'digirisk_user_information[user_insurance_ste]', false, false, 10, 'regular-text', '', '') . '
		</td>
	</tr>';

	$options = get_option('digirisk_options');
	$user_extra_fields = unserialize($options['digi_users_digirisk_extra_field']);
	if(is_array($user_extra_fields) && (count($user_extra_fields) > 0)){
		foreach($user_extra_fields as $field){
			$user_additionnal_field .= 
	'<tr>
		<th>
			<label for="user_' . $field . '" ' . $required_class[$field] . ' >' . __($field, 'evarisk') . '</label>
		</th>
		<td>
			' .	EvaDisplayInput::afficherInput('text', 'user_' . $field, $user_meta[0][$field], '', null, 'digirisk_user_information[' . $field . ']', false, false, 10, 'regular-text', '', '') . '
		</td>
	</tr>';
		}
	}
	
	$user_additionnal_field .= 
'</table>';

		echo $user_additionnal_field;
	}

	/**
	*	Get the identifier of the groups bind with an element
	*
	*	@param integer $elementId The identifier of the element (in its table) we want to bind
	*	@param string $elementTable The table of the element we want to bind
	*
	*	@return array An array containing the groups identifiers
	*/
	function getBindUsers($elementId, $elementTable)
	{
		global $wpdb;
		
		$elementId = mysql_real_escape_string($elementId);
		$elementTable = mysql_real_escape_string($elementTable);
		
		$queryCleanGroupBind = $wpdb->prepare("SELECT id_user FROM " . TABLE_LIAISON_USER_ELEMENT . " WHERE table_element = '%s' AND id_element = %d and status='valid'", $elementTable, $elementId);
		
		return $wpdb->get_results($queryCleanGroupBind);
	}

	/**
	*	Output a table with the different users binded to an element
	*
	*	@param mixed $tableElement The element type we want to get the user list for
	*	@param integer $idElement The element identifier we want to get the user list for
	*
	*	@return mixed $utilisateursMetaBox The entire html code to output
	*/
	function afficheListeUtilisateurTable_SimpleSelection($tableElement, $idElement){
		$utilisateursMetaBox = '';
		$idBoutonEnregistrer = 'save_group' . $tableElement;

		$idTable = 'listeIndividus' . $tableElement . $idElement;
		$titres = array( '', ucfirst(strtolower(__('Id.', 'evarisk'))), ucfirst(strtolower(__('Nom', 'evarisk'))), ucfirst(strtolower(__('Pr&eacute;nom', 'evarisk'))), ucfirst(strtolower(__('Inscription', 'evarisk'))));
		if(is_int(strpos($tableElement, DIGI_DBT_ACCIDENT))){
			$titres[] = ucfirst(strtolower(__('Valide accident', 'evarisk')));
		}
		unset($lignesDeValeurs);
		$default_sort_column = 4;

		//on récupère les utilisateurs déjà affectés à l'élément en cours.
		$listeUtilisateursLies = array();
		$utilisateursLies = evaUserLinkElement::getAffectedUser($tableElement, $idElement);
		if(is_array($utilisateursLies ) && (count($utilisateursLies) > 0)){
			foreach($utilisateursLies as $utilisateur){
				$listeUtilisateursLies[$utilisateur->id_user] = $utilisateur;
			}
		}
		$listeUtilisateurs = evaUser::getCompleteUserList();
		if(is_array($listeUtilisateurs) && (count($listeUtilisateurs) > 0)){
			foreach($listeUtilisateurs as $utilisateur){
				unset($valeurs);
				$idLigne = $tableElement . $idElement . 'listeUtilisateurs' . $utilisateur['user_id'];
				$idCbLigne = 'cb_' . $idLigne;
				$moreLineClass = 'userIsNotLinked';
				if(isset($listeUtilisateursLies[$utilisateur['user_id']])){
					$moreLineClass = 'userIsLinked';
				}
				$valeurs[] = array('value'=>'<span id="actionButton' . $tableElement . 'UserLink' . $utilisateur['user_id'] . '" class="buttonActionUserLinkList ' . $moreLineClass . '  ui-icon pointer" >&nbsp;</span>');
				$valeurs[] = array('value'=>ELEMENT_IDENTIFIER_U . $utilisateur['user_id']);
				$valeurs[] = array('value'=>$utilisateur['user_lastname']);
				$valeurs[] = array('value'=>$utilisateur['user_firstname']);
				$valeurs[] = array('value'=>mysql2date('d M Y', $utilisateur['user_registered'], true));
				
				if(is_int(strpos($tableElement, DIGI_DBT_ACCIDENT))){
					$user_meta = get_user_meta($utilisateur['user_id'], 'digirisk_information', false);
					$user_is_valid_for_accident = __('Non', 'evarisk');
					if(current_user_can('edit_users')){
						$user_is_valid_for_accident .= '
						<a target="digi_user_edit" href="' . admin_url('user-edit.php?user_id=' . $utilisateur['user_id']) . '#digi_user_informations" ><img src="' . str_replace('.png', '_vs.png', PICTO_EDIT) . '" title="' . __('&Eacute;diter l\'utilisateur', 'evarisk') . '" alt="edit user" /></a>';
					}
					$user_is_valid_for_accident_class = 'user_is_not_valid_for_accident';
					if(isset($user_meta[0]) && (isset($user_meta[0]['user_is_valid_for_accident']) && ($user_meta[0]['user_is_valid_for_accident'] == 'yes'))){
						$user_is_valid_for_accident = __('Oui', 'evarisk');
						$user_is_valid_for_accident_class = 'user_is_valid_for_accident';
					}
					$valeurs[] = array('value'=>$user_is_valid_for_accident, 'class'=>$user_is_valid_for_accident_class);
					$default_sort_column = 5;
				}
				$lignesDeValeurs[] = $valeurs;
				$idLignes[] = $idLigne;
			}
		}
		else{
			$valeurs[] = array('value'=>'');
			$valeurs[] = array('value'=>'');
			$valeurs[] = array('value'=>__('Aucun r&eacute;sultat trouv&eacute;', 'evarisk'));
			$valeurs[] = array('value'=>'');
			$valeurs[] = array('value'=>'');
			if(is_int(strpos($tableElement, DIGI_DBT_ACCIDENT))){
				$valeurs[] = array('value'=>'');
				$default_sort_column = 5;
			}
			$lignesDeValeurs[] = $valeurs;
			$idLignes[] = $tableElement . $idElement . 'listeUtilisateursVide';
		}

		$classes = array('addUserButtonDTable','userIdentifierColumn','','','','valid_accident_col');
		switch($tableElement){
			case DIGI_DBT_ACCIDENT . 'witness':
			{
				$user_infos_container = 'accident_witness_" + currentId + "';
				$user_infos_act = 'loadWitnessInfo';
				$user_act_add_container = 'jQuery("#search_accident_witness_details").append("<div id=\'accident_witness_" + currentId  + "\' ></div>");';
				$user_more_action = '
				jQuery("#' . $user_infos_container . '").load(EVA_AJAX_FILE_URL,{
					"post":"true",
					"act":"' . $user_infos_act . '",
					"id_user":currentId
				});';
			}
			break;
			case DIGI_DBT_ACCIDENT . 'third_party':
			{
				$user_infos_container = 'accident_third_party_" + currentId + "';
				$user_infos_act = 'loadThirdPartyInfo';
				$user_act_add_container = 'jQuery("#search_accident_third_party_details").append("<div id=\'accident_third_party_" + currentId  + "\' ></div>");';
				$user_more_action = '
				jQuery("#' . $user_infos_container . '").load(EVA_AJAX_FILE_URL,{
					"post":"true",
					"act":"' . $user_infos_act . '",
					"id_user":currentId
				});';
			}
			break;
			case DIGI_DBT_ACCIDENT:
			{
				$user_infos_container = 'work_accident_user_details';
				$user_act_add_container = 'jQuery("#' . $user_infos_container . '").html(jQuery("#loadingImg").html());';
				$user_infos_act = 'loadUserInfo';
				$user_more_action = '
				jQuery("#' . $user_infos_container . '").load(EVA_AJAX_FILE_URL,{
					"post":"true",
					"act":"' . $user_infos_act . '",
					"id_user":currentId
				});
				jQuery("#victim_selector").hide();
				jQuery("#victim_changer").show();';
			}
			break;
			case TABLE_TACHE . 'responsible':
			{
				$user_infos_container = '';
				$user_act_add_container = '';
				$user_infos_act = '';
				$user_more_action = '
				var lastname = evarisk(this).children("td:nth-child(3)").html();
				var firstname = evarisk(this).children("td:nth-child(4)").html();
				jQuery("#responsable_tache").val(currentId);
				jQuery(".completeUserListActionResponsible").hide();
				jQuery(".searchUserToAffect").hide();
				jQuery("#responsible_name").html("' . ELEMENT_IDENTIFIER_U . '" + currentId + " - " + lastname + " " + firstname);
				jQuery("#change_responsible_' . $tableElement . '").show();
				jQuery("#delete_responsible_' . $tableElement . '").show();';
			}
			break;
			case TABLE_UNITE_TRAVAIL . 'responsible':
			case TABLE_GROUPEMENT . 'responsible':
			case TABLE_RISQUE . 'responsible':
			case TABLE_ACTIVITE . 'responsible':
			{
				$user_infos_container = '';
				$user_act_add_container = '';
				$user_infos_act = '';
				$user_more_action = '
				var lastname = evarisk(this).children("td:nth-child(3)").html();
				var firstname = evarisk(this).children("td:nth-child(4)").html();
				jQuery("#responsable_activite").val(currentId);
				jQuery(".completeUserListActionResponsible").hide();
				jQuery(".searchUserToAffect").hide();
				jQuery("#responsible_name").html("' . ELEMENT_IDENTIFIER_U . '" + currentId + " - " + lastname + " " + firstname);
				jQuery("#change_responsible_' . $tableElement . '").show();
				jQuery("#delete_responsible_' . $tableElement . '").show();';
			}
			break;
			default:
			{
				$user_infos_container = '';
				$user_act_add_container = '';
				$user_infos_act = '';
				$user_more_action = '';
			}
			break;
		}
		$script = 
'<script type="text/javascript">
	evarisk(document).ready(function(){
		jQuery("#' . $idTable . '").dataTable({
			"bAutoWidth": false,
			"bInfo": false,
			"bPaginate": false,
			"bFilter": false,
			"aaSorting": [[' . $default_sort_column . ',"desc"]]
		});
		jQuery("#' . $idTable . '").children("tfoot").remove();
		jQuery("#' . $idTable . '").removeClass("dataTables_wrapper");
		jQuery(".tr_' . $idTable . '.odd, .tr_' . $idTable . '.even").click(function(){
			if(jQuery(this).children("td:first").children("span").hasClass("userIsNotLinked")){
				var currentId = jQuery(this).attr("id").replace("' . $tableElement . $idElement . 'listeUtilisateurs", "");
				' . $user_act_add_container . '' . $user_more_action . '
			}
		});
	});
</script>';

		$utilisateursMetaBox .= evaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script);

		return $utilisateursMetaBox;
	}


	/**
	*	Return different informations about users
	*/
	function dashBoardStats()
	{
		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT 
				(
					SELECT count(USERS.ID)
					FROM " . $wpdb->prefix . "users AS USERS
					WHERE USERS.ID != 1
				) AS TOTAL_USER, 
				(
					SELECT COUNT( DISTINCT( USER_LINK_EVALUATION.id_user ) )
					FROM " . TABLE_LIAISON_USER_ELEMENT . " AS USER_LINK_EVALUATION
					WHERE ((USER_LINK_EVALUATION.table_element = '" . TABLE_UNITE_TRAVAIL . "_evaluation') || (USER_LINK_EVALUATION.table_element = '" . TABLE_GROUPEMENT . "_evaluation'))
						AND status = 'valid'
				) AS EVALUATED_USER
			LIMIT 1"
		);

		return $wpdb->get_row($query);
	}

	/**
	*
	*/
	function importUserPage()
	{
		global $wpdb;
		$separatorExample = '<span class="fieldSeparator" >[fieldSeparator]</span>';

		$importAction = isset($_POST['act']) ? eva_tools::IsValid_Variable($_POST['act']) : '';
		$userRoles = isset($_POST['userRoles']) ? eva_tools::IsValid_Variable($_POST['userRoles']) : '';
		$fieldSeparator = isset($_POST['fieldSeparator']) ? eva_tools::IsValid_Variable($_POST['fieldSeparator']) : '';
		$sendUserMail = isset($_POST['sendUserMail']) ? eva_tools::IsValid_Variable($_POST['sendUserMail']) : '';

		$optionEmailDomain = '';
		$checkEmailDomain = digirisk_options::getOptionValue('emailDomain');
		if(isset($_POST['domaineMail']) && ($checkEmailDomain != $_POST['domaineMail']))
		{
			digirisk_options::updateDigiOption('emailDomain', $_POST['domaineMail']);
			$checkEmailDomain = digirisk_options::getOptionValue('emailDomain');
		}

		if($importAction != '')
		{
			$userToCreate = array();
			$importResult = '';

			/*	Check if there are lines to create without sending a file	*/
			$userLinesToCreate = isset($_POST['userLinesToCreate']) ? (string) eva_tools::IsValid_Variable($_POST['userLinesToCreate']) : '';
			if($userLinesToCreate != '')
			{
				$userToCreate = array_merge($userToCreate, explode("\n", trim($userLinesToCreate)));
			}
			else
			{
				$importResult .= __('Aucun utilisateurs n\'a &eacute;t&eacute; ajout&eacute; depuis le champs texte', 'evarisk') . '<br/>';
			}

			/*	Check if a file has been sending */
			if($_FILES['userFileToCreate']['error'] != UPLOAD_ERR_NO_FILE)
			{
				$file = $_FILES['userFileToCreate'];
				if($file['error'])
				{
					switch ($file['error']){
						case UPLOAD_ERR_INI_SIZE:
							$subFileError .= sprintf(__('Le fichier que vous avez envoy&eacute; est trop lourd: %s taille autoris&eacute;e %s', 'evarisk'), $file['size'], upload_max_filesize);
						break;
						case UPLOAD_ERR_FORM_SIZE:
							$subFileError .= sprintf(__('Le fichier que vous avez envoy&eacute; est trop lourd: %s taille autoris&eacute;e %s', 'evarisk'), $file['size'], upload_max_filesize);
						break;
						case UPLOAD_ERR_PARTIAL:
							$subFileError .= __('Le fichier que vous avez envoy&eacute; n\'a pas &eacute;t&eacute; compl&eacute;tement envoy&eacute;', 'evarisk');
						break;
					}
					$importResult .= '<h4 style="color:#FF0000;">' . __('Une erreur est survenue lors de l\'envoie du fichier', 'evarisk') . '</h4><p>' . $subFileError . '</p>';
				}
				elseif(!is_uploaded_file($file['tmp_name']))
				{
					$importResult .= sprintf(__('Le fichier %s n\'a pas pu &ecirc;tre envoy&eacute;', 'evarisk'), $file['name']);
				}
				else
				{
					$userToCreate = array_merge($userToCreate, file($file['tmp_name']));
				}
			}
			else
			{
				// $importResult .= __('Aucun fichier n\'a &eacute;t&eacute; envoy&eacute;', 'evarisk') . '<br/>';
			}

			if(is_array($userToCreate) && (count($userToCreate) > 0))
			{
				$createdUserNumber = 0;
				$errors = array();

				foreach($userToCreate as $userInfos) 
				{
					$userInfosComponent = array();
					if (trim($userInfos) != '') 
					{
						$userInfosComponent = explode($fieldSeparator, $userInfos);
						$userInfosComponent[0] = trim(strtolower(eva_tools::slugify_noaccent($userInfosComponent[0])));
						$userInfosComponent[1] = trim($userInfosComponent[1]);
						$userInfosComponent[2] = trim($userInfosComponent[2]);
						$userInfosComponent[3] = trim($userInfosComponent[3]);
						$userInfosComponent[4] = trim(strtolower(eva_tools::slugify_noaccent($userInfosComponent[4])));
						$userInfosComponent[5] = trim($userInfosComponent[5]);
						$checkErrors = 0;

						/*	Check if the email adress is valid or already exist	*/
						if(!is_email($userInfosComponent[4]))
						{
							$errors[] = sprintf(__('L\'adresse email <b>' . $userInfosComponent[4] . '</b> de la ligne %s n\'est <b>pas valide</b>', 'evarisk'), $userInfos);
							$checkErrors++;
						}
						$checkIfMailExist = $wpdb->get_row("SELECT user_email FROM " . $wpdb->users . " WHERE user_email = '" . mysql_real_escape_string($userInfosComponent[4]) . "'");
						if($checkIfMailExist)
						{
							$errors[] = sprintf(__('L\'adresse email <b>' . $userInfosComponent[4] . '</b> de la ligne %s est <b>d&eacute;j&agrave; utilis&eacute;</b>', 'evarisk'), $userInfos);
							$checkErrors++;
						}

						/*	Check if the username is valid or already exist	*/
						if(!validate_username($userInfosComponent[0]))
						{
							$errors[] = sprintf(__('L\'identifiant <b>' . $userInfosComponent[0] . '</b> de la ligne %s n\'est <b>pas valide</b>', 'evarisk'), $userInfos);
							$checkErrors++;
						}
						if(username_exists($userInfosComponent[0]))
						{
							$errors[] = sprintf(__('L\'identifiant <b>' . $userInfosComponent[0] . '</b> de la ligne %s est <b>d&eacute;j&agrave; utilis&eacute;</b>', 'evarisk'), $userInfos);
							$checkErrors++;
						}

						/*	There are no errors on the email and username so we can create the user	*/
						if($checkErrors == 0)
						{
							/*	Check if the password is given in the list to create, if not we generate one */
							if($userInfosComponent[3] == '')
							{
								$userInfosComponent[3] = substr(md5(uniqid(microtime())), 0, 7);
							}

							/*	Start creating the user	*/
							$newUserID = 0;
							$newUserID = 
								wp_insert_user(
									array(
											"user_login" => $userInfosComponent[0],
											"first_name" => $userInfosComponent[1],
											"last_name" => $userInfosComponent[2],
											"user_pass" => $userInfosComponent[3],
											"user_email" => $userInfosComponent[4]
										)
								);

							if($newUserID <= 0)
							{
								$errors[] = sprintf(__('L\'utilisateur de la ligne %s n\'a pas pu &ecirc;tre ajout&eacute;', 'evarisk'), $userInfos);
							}
							else
							{
								$user_import['user_imatriculation'] = $userInfosComponent[6];
								$user_import['user_imatriculation_key'] = $userInfosComponent[7];
								$user_import['user_birthday'] = $userInfosComponent[8];
								$user_import['user_gender'] = $userInfosComponent[9];
								$user_import['user_nationnality'] = $userInfosComponent[10];
								$user_import['user_adress'] = $userInfosComponent[11];
								$user_import['user_adress_2'] = $userInfosComponent[12];
								$user_import['user_adress_postal_code'] = $userInfosComponent[13];
								$user_import['user_adress_city'] = $userInfosComponent[14];
								$user_import['user_hiring_date'] = $userInfosComponent[15];
								$user_import['user_profession'] = $userInfosComponent[16];
								$user_import['user_professional_qualification'] = $userInfosComponent[17];
								$user_import['user_insurance_ste'] = $userInfosComponent[18];
								
								global $userWorkAccidentMandatoryFields;
								$user_is_valid_for_accident = 'yes';
								foreach($userWorkAccidentMandatoryFields as $field_identifier){
									if(isset($user_import[$field_identifier]) && (trim($user_import[$field_identifier]) == '')){
										$user_is_valid_for_accident = 'no';
									}
								}
								$user_import['user_is_valid_for_accident'] = $user_is_valid_for_accident;

								update_usermeta($newUserID, 'digirisk_information', $user_import);

								if($sendUserMail != '')
								{
									wp_new_user_notification($newUserID, $userInfosComponent[3]);
								}
								$createdUserNumber++;

								/*	Affect a role to the new user regarding on the import file or lines and if empty the main roe field	*/
								if ($userInfosComponent[5] == '') 
								{
									$userInfosComponent[5] = $userRoles;
								}
								$userRole = new WP_User($newUserID);
								$userRole->set_role($userInfosComponent[5]);
							}
						}
					}
				}

				if($createdUserNumber >= 1)
				{
					$subResult = sprintf(__('%s utilisateur a &eacute;t&eacute; cr&eacute;&eacute;', 'evarisk'), $createdUserNumber);
					if($createdUserNumber > 1)
					{
						$subResult = sprintf(__('%s utilisateurs ont &eacute;t&eacute; cr&eacute;&eacute;s', 'evarisk'), $createdUserNumber);
					}
					
					$importResult .= '<h4 style="color:#00CC00;">' . __('L\'import s\'est termin&eacute; avec succ&eacute;s. Veuillez trouver le r&eacute;sultat ci-dessous', 'evarisk') . '</h4><ul>' . $subResult . '</ul>';


					if($sendUserMail != '')
					{
						$importResult .= '<div style="font-weight:bold;" >' . __('Les nouveaux utilisateurs recevront leurs mot de passe par email', 'evarisk') . '</div>';
					}
				}
				if(is_array($errors) && (count($errors) > 0))
				{
					$subErrors = '';
					foreach($errors as $er)
					{
						$subErrors .= '<li>' . $er . '</li>';
					}
					$importResult .= '<h4 style="color:#FF0000;">' . __('Des erreurs sont survenues. Veuillez trouver la liste ci-dessous', 'evarisk') . '</h4><ul>' . $subErrors . '</ul>';
				}
			}
?>
		<div style="width:80%;margin:18px auto;padding:6px;border:1px dashed;"  ><?php echo $importResult; ?></div>
<?php
		}
?>		
<div id="icon-users" class="icon32"><br /></div>
<h2><?php _e('Import d\'utilisateurs', 'evarisk'); ?></h2>
<br/>
<div id="ajax-response" style="display:none;" >&nbsp;</div>
<script type="text/javascript" >
	function changeSeparator(){
		evarisk('.fieldSeparator').html(evarisk('#fieldSeparator').val());
	}
	evarisk(document).ready(function(){
		changeSeparator();
		evarisk('#fieldSeparator').blur(function(){changeSeparator()});
		evarisk('#userLinesToCreate').blur(function(){
			if(jQuery(this).val() != ''){
				jQuery("#importSubmit_rapid").attr("disabled", false);
			}
			else{
				jQuery("#importSubmit_rapid").attr("disabled", true);
			}
		});
		evarisk('#userLinesToCreate').keypress(function(){
			if(jQuery(this).val() != ''){
				jQuery("#importSubmit_rapid").attr("disabled", false);
			}
			else{
				jQuery("#importSubmit_rapid").attr("disabled", true);
			}
		});

		evarisk('#ajouterUtilisateurListe').click(function(){
			var error = 0;
			evarisk('#mailDomainContainer').css('color', '#000000');
			evarisk('#firstNameContainer').css('color', '#000000');
			evarisk('#lastNameContainer').css('color', '#000000');
			evarisk('#fastAddErrorMessage').hide();

			evarisk('#domaineMail').val(evarisk('#domaineMail').val().replace("@", ""));

			if(evarisk('#domaineMail').val() == ""){
				evarisk('#mailDomainContainer').css('color', '#FF0000');
				error++;
			}
			if(evarisk('#prenomUtilisateur').val() == ""){
				evarisk('#firstNameContainer').css('color', '#FF0000');
				error++;
			}
			if(evarisk('#nomUtilisateur').val() == ""){
				evarisk('#lastNameContainer').css('color', '#FF0000');
				error++;
			}

			if(error > 0){
				evarisk('#fastAddErrorMessage').show();
			}
			else{
				jQuery("#importSubmit_rapid").attr("disabled", false);
				identifiant = evarisk('#prenomUtilisateur').val() + '.' + evarisk('#nomUtilisateur').val();
				prenom = evarisk('#prenomUtilisateur').val();
				nom = evarisk('#nomUtilisateur').val();
				motDePasse = evarisk('#motDePasse').val();
				emailUtilisateur = evarisk('#prenomUtilisateur').val() + '.' + evarisk('#nomUtilisateur').val() + '@' + evarisk('#domaineMail').val();
				roleUtilisateur = evarisk('#userRoles').val();

				user_imatriculation = evarisk('#user_imatriculation').val();
				user_imatriculation_key = evarisk('#user_imatriculation_key').val();
				user_birthday = evarisk('#user_birthday').val();
				user_gender = evarisk('#user_gender').val();
				user_nationnality = evarisk('#user_nationnality').val();
				user_adress_1 = evarisk('#user_adress').val();
				user_adress_2 = evarisk('#user_adress_2').val();
				user_adress_postal_code = evarisk('#user_adress_postal_code').val();
				user_adress_city = evarisk('#user_adress_city').val();
				user_hiring_date = evarisk('#user_hiring_date').val();
				user_profession = evarisk('#user_profession').val();
				user_professional_qualification = evarisk('#user_professional_qualification').val();
				user_insurance_ste = evarisk('#user_insurance_ste').val();

				newline = identifiant + evarisk('#fieldSeparator').val() + prenom + evarisk('#fieldSeparator').val() + nom + evarisk('#fieldSeparator').val() + motDePasse + evarisk('#fieldSeparator').val() + emailUtilisateur + evarisk('#fieldSeparator').val() + roleUtilisateur;

				newline += evarisk('#fieldSeparator').val() + user_imatriculation + evarisk('#fieldSeparator').val() + user_imatriculation_key + evarisk('#fieldSeparator').val() + user_birthday + evarisk('#fieldSeparator').val() + user_gender + evarisk('#fieldSeparator').val() + user_nationnality + evarisk('#fieldSeparator').val() + user_adress_1 + evarisk('#fieldSeparator').val() + user_adress_2 + evarisk('#fieldSeparator').val() + user_adress_postal_code + evarisk('#fieldSeparator').val() + user_adress_city + evarisk('#fieldSeparator').val() + user_hiring_date + evarisk('#fieldSeparator').val() + user_profession + evarisk('#fieldSeparator').val() + user_professional_qualification + evarisk('#fieldSeparator').val() + user_insurance_ste;

				if(evarisk('#userLinesToCreate').val() != ''){
					newline = '\r\n' + newline;
				}
				evarisk('#userLinesToCreate').val(evarisk('#userLinesToCreate').val() + newline);
				evarisk('#prenomUtilisateur').val("");
				evarisk('#nomUtilisateur').val("");

				evarisk('#user_imatriculation').val("");
				evarisk('#user_imatriculation_key').val("");
				evarisk('#user_birthday').val("");
				evarisk('#user_gender').val("");
				evarisk('#user_nationnality').val("");
				evarisk('#user_adress').val("");
				evarisk('#user_adress_2').val("");
				evarisk('#user_adress_postal_code').val("");
				evarisk('#user_adress_city').val("");
				evarisk('#user_hiring_date').val("");
				evarisk('#user_profession').val("");
				evarisk('#user_professional_qualification').val("");
				evarisk('#user_insurance_ste').val("");

<?php echo $optionEmailDomain;	?>
			}
		});

		jQuery("#import_user_form_file_container_switcher").click(function(){
			jQuery("#import_user_form_file_container").toggle();
			jQuery("#user_import_container_switcher_icon").toggleClass("user_import_container_opener");
			jQuery("#user_import_container_switcher_icon").toggleClass("user_import_container_closer");
		});

		jQuery("#complementary_fieds_switcher").click(function(){
			goTo("#digirisk_import_user_easy_form_container");
			jQuery("#complementary_fieds").toggle();
			jQuery("#complementary_fieds_icon").toggleClass("user_import_container_opener");
			jQuery("#complementary_fieds_icon").toggleClass("user_import_container_closer");
		});
	});
</script>
<form enctype="multipart/form-data" method="post" action="" >
	<input type="hidden" name="act" id="act" value="1" />

	<!-- 	Start of fast add part	-->
	<h3 class="clear" ><?php echo __('Ajout rapide d\'utilisateurs', 'evarisk'); ?></h3>
	<table summary="Fast user adding section" cellpadding="0" cellspacing="0" class="digirisk_import_user_easy_form_container" id="digirisk_import_user_easy_form_container" >
		<tr>
			<td class="bold" ><?php _e('Informations obligatoires', 'evarisk'); ?></td>
			<td id="complementary_fieds_switcher" class="pointer" ><span id="complementary_fieds_icon" class="alignleft ui-icon user_import_container_opener" >&nbsp;</span><?php _e('Champs suppl&eacute;mentaires', 'evarisk'); ?></td>
		</tr>
		<tr>
			<td class="digi_mandatory_fields_container" >
				<table summary="Fast user adding section" cellpadding="0" cellspacing="0" class="digirisk_import_user_easy_form" >
					<tr>
						<td class="digi_import_user_main_info_name" id="mailDomainContainer"><?php echo ucfirst(strtolower(__('domaine de l\'adresse email', 'evarisk'))); ?></td>
						<td class="digi_import_user_main_info_input" ><div class="alignleft" ><?php _e('adresse.email', 'evarisk'); ?>@</div><input type="text" value="<?php echo $checkEmailDomain; ?>" id="domaineMail" name="domaineMail" /></td>
					</tr>
					<tr>
						<td class="digi_import_user_main_info_name" ><?php echo ucfirst(strtolower(__('mot de passe par d&eacute;faut', 'evarisk'))); ?></td>
						<td class="digi_import_user_main_info_input"><input type="text" value="" id="motDePasse" name="motDePasse" /><br/>
						<span style="font-size:9px;" ><?php echo __('Laissez vide pour un mot de passe al&eacute;atoire', 'evarisk'); ?></span></td>
					</tr>
					<tr>
						<td class="digi_import_user_main_info_name" >
							<?php echo __('Envoyer le mot de passe aux utilisateurs.', 'evarisk'); ?>
						</td>
						<td class="digi_import_user_main_info_input" >
							<input type="checkbox" name="sendUserMail" id="sendUserMail" /><span style="font-weight:bold;font-size:9px;" ><?php echo __('(Peut ne pas fonctionner sur certains serveurs)', 'evarisk'); ?></span>
						</td>
					</tr>
					<tr>
						<td class="digi_import_user_main_info_name" id="lastNameContainer"><?php echo ucfirst(strtolower(__('nom', 'evarisk'))); ?></td>
						<td class="digi_import_user_main_info_input" ><input type="text" value="" id="nomUtilisateur" name="nomUtilisateur" /></td>
					</tr>
					<tr>
						<td class="digi_import_user_main_info_name" id="firstNameContainer"><?php echo ucfirst(strtolower(__('prenom', 'evarisk'))); ?></td>
						<td class="digi_import_user_main_info_input" ><input type="text" value="" id="prenomUtilisateur" name="prenomUtilisateur" /></td>
					</tr>
					<tr>
						<td class="digi_import_user_main_info_name">
							<?php echo __('R&ocirc;le pour les utilisateurs', 'evarisk'); ?><br/>
							<span style="font-style:italic;font-size:10px;" ><?php echo __('Si aucun r&ocirc;le n\'a &eacute;t&eacute; d&eacute;fini dans le fichier', 'evarisk'); ?></span>
						</td>
						<td class="digi_import_user_main_info_input" >
							<select name="userRoles" id="userRoles" >
								<?php
									if ( !isset($wp_roles) )
									{
										$wp_roles = new WP_Roles();
									}
									foreach ($wp_roles->get_names() as $role => $roleName)
									{
										$selected = '';
										if(($userRoles == '') && ($role == 'subscriber'))
										{
											$selected = 'selected = "selected"';
										}
										elseif(($userRoles != '') && ($role == $userRoles))
										{
											$selected = 'selected = "selected"';
										}
										echo '<option value="' . $role . '" ' . $selected . ' >' . __($roleName) . '</option>';
									}
								?>
							</select>
						</td>
					</tr>
				</table>
			
			</td>
			<td class="digi_complmentary_fields_container" >
				<div id="complementary_fieds" class="hide" ><?php self::user_additionnal_field(null, 'import'); ?></div>
			</td>
		</tr>

		<tr>
			<td >&nbsp;</td>
		</tr>

		<tr>
			<td colspan="4" style="text-align:center;" ><input type="button" class="button-primary" value="<?php echo __('Ajouter &agrave; la liste des utilisateurs &agrave; importer', 'evarisk'); ?>" id="ajouterUtilisateurListe" name="ajouterUtilisateurListe" /><div id="fastAddErrorMessage" style="display:none;color:#FF0000;" ><?php echo __('Merci de remplir les champs marqu&eacute;s en rouge', 'evarisk'); ?></div></td>
		</tr>
		<tr>
			<td colspan="4" style="text-align:center;" ><textarea name="userLinesToCreate" id="userLinesToCreate" cols="70" rows="5"></textarea></td>
		</tr>
	</table>
	<!-- 	Submit form button	-->
<?php
	if(current_user_can('digi_import_user')){
?>
	<div class="user_rapid_import_button" ><input disabled="disabled" type="submit" class="button-primary" name="importSubmit_rapid" id="importSubmit_rapid" value="<?php echo __('Importer les utilisateurs', 'evarisk'); ?>" /></div>
<?php
	}
?>


	<br/>
	<br/>
	<br/>


	<!-- 	Start of file specification part	-->
	<h3 class="pointer" id="import_user_form_file_container_switcher" ><span id="user_import_container_switcher_icon" class="alignleft ui-icon user_import_container_opener" >&nbsp;</span><?php echo __('Ajout d\'utilisateur depuis un fichier', 'evarisk'); ?></h3>
	<div id="import_user_form_file_container" class="hide" >
		<div >
			<div><a href="<?php echo EVA_MODELES_PLUGIN_URL; ?>import_users.ods" ><?php echo __('Vous pouvez t&eacute;l&eacute;charger le fichier pour construire l\'import ici', 'evarisk'); ?></a></div>
			<?php echo __('Chaque ligne devra respecter le format ci-apr&egrave;s&nbsp;:', 'evarisk'); ?>
			<br/><span style="font-style:italic;font-size:10px;" ><?php echo '<span style="color:#CC0000;" >' . __('Les champs identifiants et email sont obligatoires.', 'evarisk') . '</span><br/>' . __('Vous n\'&ecirc;tes pas oblig&eacute; de renseigner tous les champs mais tous les s&eacute;parateur doivent &ecirc;tre pr&eacute;sent.', 'evarisk') . '&nbsp;&nbsp;' . __('Exemple&nbsp;', 'evarisk') . '&nbsp;<span style="font-weight:bold;" >' . __('identifiant', 'evarisk') . $separatorExample . $separatorExample . $separatorExample . $separatorExample . __('email', 'evarisk') . $separatorExample . '</span>' . $separatorExample . $separatorExample . $separatorExample . $separatorExample . $separatorExample . $separatorExample . $separatorExample . $separatorExample . $separatorExample . $separatorExample . $separatorExample . $separatorExample . $separatorExample; ?></span>
			<div style="margin:3px 6px;padding:12px;border:1px solid #333333;width:80%;text-align:center;" ><?php echo '<span style="color:#CC0000;" >' . __('identifiant', 'evarisk') . '</span>' . $separatorExample . __('prenom', 'evarisk') . $separatorExample . __('nom', 'evarisk') . $separatorExample . __('mot de passe', 'evarisk') . $separatorExample . '<span style="color:#CC0000;" >' . __('email', 'evarisk') . '</span>' . $separatorExample . __('role', 'evarisk') . '<span class="italic digi_import_user_additionnal_field" >' . $separatorExample . __('n&ordm; immatriculation', 'evarisk') . $separatorExample . __('cl&eacute; immatriculation', 'evarisk') . $separatorExample . __('date de naissance', 'evarisk') . $separatorExample . __('sexe', 'evarisk') . $separatorExample . __('nationalit&eacute;', 'evarisk') . $separatorExample . __('adresse 1', 'evarisk') . $separatorExample . __('adresse 2', 'evarisk') . $separatorExample . __('code postal', 'evarisk') . $separatorExample . __('ville', 'evarisk') . $separatorExample . __('date embauche', 'evarisk') . $separatorExample . __('profession', 'evarisk') . $separatorExample . __('qualification professionnelle', 'evarisk') . $separatorExample . __('societe d\'assurance', 'evarisk') . '</span>'; ?></div>
		</div>
		<div >
			<table style="margin:0px 36px;" summary="" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<?php echo __('S&eacute;parateur de champs', 'evarisk'); ?>
					</td>
					<td>
						<input type="text" name="fieldSeparator" id="fieldSeparator" value=";" />
					</td>
				</tr>
			</table>
		</div><?php echo __('Vous pouvez envoyer un fichier contenant les utilisateurs &agrave; cr&eacute;er (extension autoris&eacute;e *.odt, *.csv, *.txt)', 'evarisk'); ?>
		<input type="file" id="userFileToCreate" name="userFileToCreate" />
		<!-- 	Submit form button	-->
<?php
	if(current_user_can('digi_import_user')){
?>
		<div class="user_import_button" ><input type="submit" class="button-primary" name="importSubmit" id="importSubmit" value="<?php echo __('Importer les utilisateurs', 'evarisk'); ?>" /></div>
<?php
	}
?>
	</div>

</form>
<?php
	}









	/**
	*	Create an instance for a user. Initialize the entity id related to the user
	*/
	function evaUser($AttributeGroupName)
	{
		global $eav_attribute;

		$eav_attribute->setCurrentEntityTypeId($eav_attribute->getEntityInformation('eva_users'));

		$this->_currentAttributeGroup = $AttributeGroupName;
	}
	/**
	*	Get the query that will be launch to update different attribute
	*
	*	@return mixed $this->_attributesValueQuery The query we will launch to update information
	*/
	function setUserToUpdate($userId)
	{
		$this->_userToUpdate = $userId;
	}
	/**
	*	Get the list of attribute available for a given group
	*
	*	@return mixed $attributeForm The different element to output
	*/
	function evaUserAttributeForm()
	{
		global $eav_attribute;

		$attributeForm = $eav_attribute->attributeFormOutput($this->_currentAttributeGroup, $this->_userToUpdate);

		return $this->evaUserAttributeFormTemplate($attributeForm);
	}
	/**
	*	Output a container with the different element for user
	*
	*	@param mixed $content The different element to output in the template
	*/
	function evaUserAttributeFormTemplate($content)
	{
		if(trim($content) != '')
		{
?>
<h3><?php _e('Informations compl&eacute;mentaires evarisk'); ?></h3>
<div class="form-table" id="evaUserInformation" >
	<?php echo $content; ?>
</div>
<?php
		}
	}
	/**
	*	Update the different information for an user profile. First we check that there is an user update form that were send, then if it's the case we do the job
	*/
	function evaUserUpdateProfile()
	{
		/* Check if we send the user update form, if it is not the case we return to the normal output */
		if(!isset($_POST['user_id'])) return;

		global $eav_attribute;

		if(isset($_REQUEST['userAttributes']) && is_array($_REQUEST['userAttributes']) && (count($_REQUEST['userAttributes']) > 0))
		{
			foreach ($_REQUEST['userAttributes'] as $attributeType => $attributeContent) :
					$eav_attribute->createAttributesValueHeaderQuery($attributeType);
				foreach ($attributeContent as $attributeCode => $attributeValue) :
					$eav_attribute->createAttributesValueQuery($attributeCode, $_POST['user_id'], $attributeValue);
				endforeach;
					$eav_attribute->attributesValueQueryTrimmer();
					$eav_attribute->setAttributesValue();
					$eav_attribute->attributesValueQueryTrimmer(true);
			endforeach;
		}
	}


}