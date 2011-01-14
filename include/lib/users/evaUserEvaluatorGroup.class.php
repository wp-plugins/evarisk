<?php
/**
*	The different utilities to manage users' group in evarisk
*
*	@package 		Evarisk
*	@subpackage Users' group
* @author			Evarisk team <contact@evarisk.com>
*/

class evaUserEvaluatorGroup
{

	/**
	*	Get the different groups
	*
	*	@param integer $groupId The identifier for the group we want to have information about (if we are on a group page and not on the main list)
	*
	*	@return array $resultList The list of existing groups in database
	*/
	function getUserEvaluatorGroup($groupId = null)
	{
		global $wpdb;
		$resultList = array();

		/*	Build the query regarding to the different parameters	*/
		$whatToSelect = ", COUNT( DISTINCT(GRPDETAILS.user_id) ) AS TOTALUSERNUMBER";
		if($groupId != null)
		{
			$whatToSelect .= ", GROUP_CONCAT( 'user', GRPDETAILS.user_id , ',' SEPARATOR '') AS ELEMENT";
		}
		$query =
			"SELECT GRP.* " . $whatToSelect . "
			FROM " . TABLE_EVA_EVALUATOR_GROUP . " AS GRP
				LEFT JOIN " . TABLE_EVA_EVALUATOR_GROUP_DETAILS . " AS GRPDETAILS ON ((GRPDETAILS.evaluator_group_id = GRP.evaluator_group_id) AND (GRPDETAILS.Status = 'valid'))
			WHERE GRP.evaluator_group_status != 'deleted' ";
		if($groupId != null)
		{
			$query = $wpdb->prepare($query . " AND GRP.evaluator_group_id = '%s' ", $groupId);
		}
		$query .= "GROUP BY GRP.evaluator_group_id";

		/*	Execute the query	*/
		$resultList = $wpdb->get_results($query);

		return $resultList;
	}


	/**
	* Output a row in the main grid
	*/
	function RowOutput()
	{
		$rowToOutput = $this->getUserEvaluatorGroup();

		$i=0;
		foreach ($rowToOutput as $rowInformations )
		{
?>
	<tr id="ut-<?php echo $rowInformations->evaluator_group_id . '"'; if(($i%2) == 0) {echo ' class="alternate"';} ?> valign="top">
		<th class="check-column" scope="row">
			<!--<input type="checkbox" value="<?php echo $rowInformations->evaluator_group_id; ?>" name="attribute[]"/>-->
		</th>
		<td><strong><a onclick="javascript:evarisk('#act').val('mod');evarisk('#id').val('<?php echo $rowInformations->evaluator_group_id; ?>');evarisk('#evaUserEvaluatorGroupManagementForm').submit();" style="cursor:pointer;" ><?php echo stripcslashes($rowInformations->evaluator_group_name); ?></a></strong></td>
		<td><strong><?php echo $rowInformations->evaluator_group_description ?></strong></td>
		<td><strong><?php echo $rowInformations->TOTALUSERNUMBER ?></strong></td>
	</tr>
<?php
			$i++;
		}

		if($i <= 0)
		{
?>
	<tr id="ut-<?php echo $rowInformations->evaluator_group_id . '"'; if(($i%2) == 0) {echo ' class="alternate"';} ?> valign="top" >
		<th colspan="4" class="check-column" style="text-align:center;" scope="row"><?php echo __('Aucun r&eacute;sultat','evarisk'); ?></th>
	</tr>
<?php
		}

		return null;
	}

	/**
	*	Prepare the different field before use them in the query
	*
	*	@param array $prm An array containing the fields to prepare
	*	@param mixed $operation The type of query we are preparing the vars for
	*
	*	@return mixed $preparedFields The fields ready to be injected in the query
	*/
	function prepareQuery($prm, $operation = 'creation')
	{
		$preparedFields = array();

		foreach($prm as $field => $value)
		{
			if($field != 'id')
			{
				if($operation == 'creation')
				{
					$preparedFields['fields'][] = $field;
					$preparedFields['values'][] = "'" . mysql_real_escape_string($value) . "'";
				}
				elseif($operation == 'update')
				{
					$preparedFields['values'][] = $field . " = '" . mysql_real_escape_string($value) . "'";
				}
			}
		}

		return $preparedFields;
	}

	/**
	*	Create an user group
	*
	*	@param array $prm An array containing the different information needed for the group
	*
	*	@return array $status An array containing the result of the method
	*/
	function createUserEvaluatorGroup($prm)
	{
		global $wpdb;
		global $current_user;
		$status = array();

		$preparedFields = $this->prepareQuery($prm['evaUserEvaluatorGroup'],'creation');

		if( !isset($prm['evaUserEvaluatorGroup']['evaluator_group_name']) || (trim($prm['evaUserEvaluatorGroup']['evaluator_group_name']) == '') )
		{
			$status['result'] = 'error';
			$status['errors']['mandatory_field'] = __('Le champs Nom est obligatoire');
		}
		else
		{
			$preparedFields['fields'][] = 'evaluator_group_creation_date';
			$preparedFields['values'][] = 'NOW()';
			$preparedFields['fields'][] = 'evaluator_group_creation_user_id';
			$preparedFields['values'][] = $current_user->ID;

			$query =
				$wpdb->prepare("INSERT INTO " . TABLE_EVA_EVALUATOR_GROUP . "
					(" . implode(', ', $preparedFields['fields']) . ")
				VALUES
					(" . implode(', ', $preparedFields['values']) . ")");
			if($wpdb->query($query))
			{
				$status['result'] = 'ok';
				$status['id'] = $wpdb->insert_id;

				$this->addUserToGroup($status['id'], $_POST['groupUserList']);
			}
			else
			{
				$status['result'] = 'error';
				$status['errors']['query_error'] = __('Une erreur est survenue lors de l\'enregistrement ', 'evarisk');
			}
		}

		return $status;
	}

	/**
	*	Update an user group
	*
	*	@param array $prm An array containing the different information needed for the group
	*
	*	@return array $status An array containing the result of the method
	*/
	function updateUsergroup($prm)
	{
		global $wpdb;
		global $current_user;
		$status = array();

		$preparedFields = $this->prepareQuery($prm['evaUserEvaluatorGroup'],'update');

		if( !isset($prm['evaUserEvaluatorGroup']['evaluator_group_name']) || (trim($prm['evaUserEvaluatorGroup']['evaluator_group_name']) == '') )
		{
			$status['result'] = 'error';
			$status['errors']['mandatory_field'] = __('Le champs Nom est obligatoire');
		}
		else
		{
			$preparedFields['values'][] = "evaluator_group_deletion_date = NOW()";
			$preparedFields['values'][] = "evaluator_group_deletion_user_id = '" . $current_user->ID . "' ";

			$query =
				$wpdb->prepare(
				"UPDATE " . TABLE_EVA_EVALUATOR_GROUP . "
				SET " . implode(', ', $preparedFields['values']) . "
				WHERE evaluator_group_id = '%s' ",
				$prm['evaUserEvaluatorGroup']['id']);
			$wpdb->query($query);

			$status['result'] = 'ok';
			$status['id'] = $prm['evaUserEvaluatorGroup']['id'];

			$this->addUserToGroup($status['id'], $_POST['groupUserList']);
		}

		return $status;
	}

	/**
	*	Add some user to a group
	*
	*	@param integer $groupId The group identifier we want to add the user to
	*	@param array $newUserList An array with the user list we want to add to the group
	*
	*	@return array $result An array containing the result of the query. With the errors' message in case of errors append
	*/
	function addUserToGroup($groupId, $newUserList)
	{
		global $wpdb;
		global $current_user;
		$list = $listToDelete = "  ";
		$result = true;

		/*	Prepare the user list the user selected	*/
		if(substr($newUserList, -1) == ','){$newUserList = substr($newUserList, 0, -1);}
		$userToAdd = explode(',', $newUserList);
		$TmpUserToAdd = array_flip($userToAdd);

		/*	Get the user list already in the group	*/
		$oldUserList = $this->getUserInGroup($groupId);

		foreach($oldUserList as $key => $user)
		{
			$user->user_id = 'user' . $user->user_id;

			/*	If an user that was in the group is not anymore in the list user	*/
			if( !in_array($user->user_id, $userToAdd) && ($user->user_id != '') && ($user->user_id != '0') )
			{
				$userGoodId = str_replace('user', '', $user->user_id);
				$listToDelete .= $wpdb->prepare("('" . $user->id . "', '" . $groupId . "', '" . $userGoodId . "', 'deleted', '" . $user->dateEntree . "', '" . $user->affectationUserId . "', NOW(), '" . $current_user->ID . "'), ");
			}
			elseif( in_array($user->user_id, $userToAdd) && ($user->user_id != '') )
			{
				unset( $TmpUserToAdd[$user->user_id] );
			}
		}
		$userToAdd = array_flip($TmpUserToAdd);
		$listToDelete = substr($listToDelete, 0, -2);

		foreach($userToAdd as $key => $userId)
		{
			if($userId != '')
			{
				$userGoodId = str_replace('user', '', $userId);
				$list .= $wpdb->prepare("('" . $groupId . "', '" . $userGoodId . "', 'Valid', NOW(), '" . $current_user->ID . "'), ");
			}
		}
		$list = substr($list, 0, -2);

		/*	Add the choosen users to the group	*/
		if( trim($listToDelete) != "" )
		{
			$query =
				"REPLACE INTO " . TABLE_EVA_EVALUATOR_GROUP_DETAILS . "
					(id,evaluator_group_id, user_id, Status, dateEntree, affectationUserId, dateSortie, desaffectationUserId)
				VALUES
					" . $listToDelete;
			if(!$wpdb->query($query))
			{
				$result = false;
			}
		}

		/*	Add the choosen users to the group	*/
		if( trim($list) != "" )
		{
			$query =
				"INSERT INTO " . TABLE_EVA_EVALUATOR_GROUP_DETAILS . "
					(evaluator_group_id, user_id, Status, dateEntree, affectationUserId)
				VALUES
					" . $list;
			if(!$wpdb->query($query))
			{
				$result = false;
			}
		}

		return $result;
	}

	/**
	*	Get the list of users from a group
	*
	*	@param integer $groupId The group identifier we want to get the users' list
	*
	*	@return array $userList The different information about users that are in the group
	*/
	function getUserInGroup($groupId)
	{
		global $wpdb;
		$userList = array();

		$query = $wpdb->prepare(
		"SELECT GRPDETAILS.user_id, GRPDETAILS.id, GRPDETAILS.dateEntree, GRPDETAILS.affectationUserId
			FROM " . TABLE_EVA_EVALUATOR_GROUP . " AS GRP
				LEFT JOIN " . TABLE_EVA_EVALUATOR_GROUP_DETAILS . " AS GRPDETAILS ON ((GRPDETAILS.evaluator_group_id = GRP.evaluator_group_id) AND (GRPDETAILS.Status = 'valid'))
			WHERE GRP.evaluator_group_status != 'deleted'
				AND GRP.evaluator_group_id = '%s'
				AND GRPDETAILS.user_id != 'NULL' ",
		$groupId);

		/*	Execute the query	*/
		$userList = $wpdb->get_results($query);

		return $userList;
	}

	/**
	*	Save the new bind between an evaluator group and an element
	*
	*	@param integer $groupId The identifier of the group we want to bind
	*	@param integer $elementId The identifier of the element (in its table) we want to bind
	*	@param string $elementTable The table of the element we want to bind
	*
	*	@return array $status An array containing the result of the method
	*/
	function saveBind($groupId, $elementId, $elementTable)
	{
		global $wpdb;
		global $current_user;
		$status = array();

		$query =
			$wpdb->prepare("INSERT INTO " . TABLE_EVA_EVALUATOR_GROUP_BIND . "
				(id, id_group, table_element, id_element, dateAffectation, affectationUserId)
			VALUES
				('', %d, '%s', %d, NOW(), '%d')", $groupId, $elementTable, $elementId, $current_user->ID);
		if($wpdb->query($query))
		{
			$status['result'] = 'ok';
		}
		else
		{
			$status['result'] = 'error';
			$status['errors']['query_error'] = __('Une erreur est survenue lors de l\'enregistrement', 'evarisk');
		}

		return $status;
	}

	/**
	*	Delete a bind between a evaluator group and an element
	*
	*	@param integer $id The main identifier of a bind
	*	@param integer $groupId The identifier of the evaluator group we want to unbind
	*	@param integer $elementId The identifier of the element (in its table) we want to unbind
	*	@param mixed $elementTable the table of the element we want to unbind
	*
	*	@return array $status An array containing the result of the method
	*/
	function deleteBind($id, $groupId, $elementId, $elementTable)
	{
		global $wpdb;
		global $current_user;
		$status = array();

		$query =
			$wpdb->prepare("UPDATE " . TABLE_EVA_EVALUATOR_GROUP_BIND . "
				SET Status = 'Deleted', dateDesaffectation = NOW(), desaffectationUserId = '%d'
				WHERE id = '%d'
					AND id_group = '%d'
					AND table_element = '%s'
					AND id_element = '%d'", $current_user->ID, $id, $groupId, $elementTable, $elementId);
		if($wpdb->query($query))
		{
			$status['result'] = 'ok';
		}
		else
		{
			$status['result'] = 'error';
			$status['errors']['query_error'] = __('Une erreur est survenue lors de l\'enregistrement', 'evarisk');
		}

		return $status;
	}

	/**
	*	Get the identifier of the groups bind with an element
	*
	*	@param integer $elementId The identifier of the element (in its table) we want to bind
	*	@param string $elementTable The table of the element we want to bind
	*
	*	@return array An array containing the groups identifiers
	*/
	function getBindGroups($elementId, $elementTable)
	{
		global $wpdb;

		$elementId = mysql_real_escape_string($elementId);
		$elementTable = mysql_real_escape_string($elementTable);

		$queryCleanGroupBind = $wpdb->prepare(
		"SELECT id_group, id
		FROM " . TABLE_EVA_EVALUATOR_GROUP_BIND . "
		WHERE table_element = '%s'
			AND id_element = %d
			AND Status='Valid'", $elementTable, $elementId);

		return $wpdb->get_results($queryCleanGroupBind);
	}

	/**
	*	Get the user number affected to a work unit or a group
	*
	*	@param integer $elementId The identifier of the element (in its table) we want to bind
	*	@param string $elementTable The type of the element we want to bind
	*
	*	@return intger The number of employees of the work unit or the group
	*/
	function getUserNumberInWorkUnit($elementId, $elementTable)
	{
		$totalUserInWorkUnit = 0;

		$groupesLies = evaUserEvaluatorGroup::getBindGroups($elementId, $elementTable);
		if($groupesLies != null and count($groupesLies) > 0)
		{
			foreach($groupesLies as $groupeLie)
			{
				$userInGroup = evaUserEvaluatorGroup::getUserInGroup($groupeLie->id_group);
		// echo '<pre>';print_r($userInGroup);echo '</pre>';
				foreach($userInGroup as $users)
				{
					if($users->user_id > 0)
					{
						$userInUnit[$users->user_id] = 1;
					}
				}
			}
		}

		$totalUserInWorkUnit = count($userInUnit);

		return $totalUserInWorkUnit;
	}

	/**
	*	Get the existing evaluator group and compare with the evaluator group list already affected to one given element
	*
	*	@param mixed $tableElement The type of the element we want to get the evaluator list for
	*	@param integer $idElement The element identifier we want to get the evaluator for
	*
	*	@return mixed A dropdown menu with the different evaluator group we can affect to the selected element
	*/
	function afficheListeGroupeUtilisateurNonAffecte($tableElement, $idElement)
	{
		$listeGroupes = array();
		$listeGroupes = evaUserEvaluatorGroup::getUserEvaluatorGroup();
		$tmpListeGroupes = array();
		foreach($listeGroupes as $key => $groupeInformation)
		{
			$tmpListeGroupes[$groupeInformation->evaluator_group_id] = $groupeInformation;
		}
		$listeGroupes = $tmpListeGroupes;
		$listeGroupes[0] = '';

		$listeGroupesAffectes = evaUserEvaluatorGroup::getBindGroups($idElement, $tableElement);
		foreach($listeGroupesAffectes as $index => $affectedGroup)
		{
			if( isset($listeGroupes[$affectedGroup->id_group]) )
			{
				unset($listeGroupes[$affectedGroup->id_group]);
			}
		}

		$tabValue[0] = '0';
		$tabDisplay[0] = __('Cliquez ici pour ajouter', 'evarisk');
		$i=1;
		foreach($listeGroupes as $key => $groupeInformation)
		{
			$tabValue[$i] = $groupeInformation->evaluator_group_id;
			$tabDisplay[$i] = $groupeInformation->evaluator_group_name;
			$i++;
		}

		return EvaDisplayInput::afficherComboBox($listeGroupes, 'groupesEvaluateursNonAssocies', __('Groupes d\'&eacute;valuateurs', 'evarisk') . '&nbsp;:', 'groupesEvaluateursNonAssocies', '', '', $tabValue, $tabDisplay);
	}

	/**
	*	Get the list of evaluator group and show a list with the different possible action and a save button
	*	
	*	@param mixed $tableElement The element type we want to get the evaluator group list for
	*	@param integer $idElement The element identifier we want to get the evaluator group list for
	*	@param boolean $noScript A boolean to specify if the dataTable script must be launch
	*
	*	@return mixed $groupesUtilisateursMetaBox The html output of evaluator group
	*/
	function afficheListeGroupe($tableElement, $idElement, $noScript = false)
	{
		require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayDesign.class.php');
		require_once(EVA_LIB_PLUGIN_DIR . 'users/evaUserEvaluatorGroup.class.php');

		$groupesUtilisateursMetaBox = '';
		$groupesUtilisateursMetaBox = $groupesUtilisateursMetaBox . '<div id="message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '" class="updated fade" style="cursor:pointer; display:none;"></div>';
		{//Création dataTable
			$idTable = 'groupesIndividus' . $tableElement . $idElement;
			$titres = array(__('Affect&eacute;', 'evarisk'), ucfirst(strtolower(sprintf(__('Nom %s', 'evarisk'), __('du groupe d\'utilisateurs', 'evarisk')))), ucfirst(strtolower(sprintf(__('Description %s', 'evarisk'), __('du groupe d\'utilisateurs', 'evarisk')))), __('Nombre d\'utilisateurs', 'evarisk'));
			unset($lignesDeValeurs);
			//on récupère les groupes.
			$groupesUtilisateurs = evaUserEvaluatorGroup::getUserEvaluatorGroup();
			$groupesLies = evaUserEvaluatorGroup::getBindGroups($idElement, $tableElement);
			if($groupesLies != null and count($groupesLies) > 0)
			{
				foreach($groupesLies as $groupeLie)
				{
					$groupesLiesIds[$groupeLie->id_group] = $groupeLie;
				}
			}
			foreach($groupesUtilisateurs as $groupeUtilisateurs)
			{
				unset($valeurs);
				$idLigne = $tableElement . $idElement . 'groupeUtilisateurs' . $groupeUtilisateurs->evaluator_group_id;
				$idCbLigne = 'cb_' . $idLigne;
				$valeurs[] = array('value'=>evaDisplayInput::afficherInput('checkbox', $idCbLigne, null, null, null, 'cb_' . $idLigne));
				$valeurs[] = array('value'=>$groupeUtilisateurs->evaluator_group_name);
				$valeurs[] = array('value'=>$groupeUtilisateurs->evaluator_group_description);
				$valeurs[] = array('value'=>$groupeUtilisateurs->TOTALUSERNUMBER);
				$lignesDeValeurs[] = $valeurs;
				if(isset($groupesLiesIds[$groupeUtilisateurs->evaluator_group_id]))
				{
					$script = $script . '<script type="text/javascript">
						evarisk(document).ready(function() {
							evarisk(\'#' . $idCbLigne . '\').attr("checked", "checked");
						});
					</script>';
				}
				$idLignes[] = $idLigne;
			}
			$classes = array('cbColumnLarge','','','cbNbUserEvaluatorGroup');
			if(!$noScript)
			{
			$script = $script . '<script type="text/javascript">
				evarisk(document).ready(function() {
					evarisk(\'#' . $idTable . '\').dataTable({
						"bLengthChange": false,
						"bAutoWidth": false,
						"bFilter": false,
						"bInfo": false,
						"aoColumns":
						[
							{ "bSortable": false },
							{ "bSortable": true },
							{ "bSortable": false },
							{ "bSortable": true }
						],
						"aaSorting": [[1,\'asc\']]});
				});
			</script>';
			}
			$groupesUtilisateursDataTable = evaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script);

			$groupesUtilisateursMetaBox = $groupesUtilisateursMetaBox . $groupesUtilisateursDataTable;
		}
		{//Bouton Enregistrer
			$idBoutonEnregistrer = 'save_group';
			$scriptEnregistrement = '<script type="text/javascript">
				evarisk(document).ready(function() {
					evarisk(\'#' . $idBoutonEnregistrer . '\').click(function() {
						var groupeUtilisateurs = new Array();
						evarisk(\'#' . $idTable . ' input\').each(function(){
							if(evarisk(this).attr("checked"))
							{
								groupeUtilisateurs.push(evarisk(this).attr("id").replace(new RegExp(/[A-Za-z_\-]+[\d]+[A-Za-z_\-]+/), ""));
							}
						});
						evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true",
							"table": "' . TABLE_EVA_EVALUATOR_GROUP_BIND . '",
							"act": "save",
							"idsGroupes": groupeUtilisateurs,
							"tableElement": "' . $tableElement . '",
							"idElement": "' . $idElement . '"
						});
					});
				});
				</script>';
			$boutonEnregistrer = EvaDisplayInput::afficherInput('button', $idBoutonEnregistrer, __('Enregistrer', 'evarisk'), null, '', 'save', false, true, '', 'button-primary alignright', '', '', $scriptEnregistrement);
			$groupesUtilisateursMetaBox = $groupesUtilisateursMetaBox . $boutonEnregistrer;
		}

		return $groupesUtilisateursMetaBox;
	}

	/**
	*	Prepare the evaluator group box content
	*	
	*	@param mixed $tableElement The element type we want to get the box content for
	*	@param integer $idElement The element identifier we want to get the box content for
	*	
	*	@return mixed The html output for the evaluator group box
	*/
	function boxGroupesUtilisateursEvaluation($tableElement, $idElement)
	{
		/*	Recuperation des groupes non affectes	*/
		$listeGroupesNonAffectes = evaUserEvaluatorGroup::afficheListeGroupeUtilisateurNonAffecte($tableElement, $idElement);

		$scriptEnregistrement =
		'<script type="text/javascript">
			evarisk(document).ready(function() {
				evarisk(\'#groupesEvaluateursNonAssocies\').change(function() {
					evarisk("#chargementBox' . TABLE_EVA_EVALUATOR_GROUP_BIND . '").html(\'<img src="' . PICTO_LOADING_ROUND . '" alt="loading" />\');
					groupesEvaluateurAAssocier = evarisk(\'#groupesEvaluateursNonAssocies\').val();

					evarisk("#listeGroupesEvaluateurs").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
						"post": "true",
						"table": "' . TABLE_EVA_EVALUATOR_GROUP_BIND . '",
						"act": "save",
						"idGroupe": groupesEvaluateurAAssocier,
						"tableElement": "' . $tableElement . '",
						"idElement": "' . $idElement . '"
					});
				});
			});

			function deleteGroupeEvaluateurBind(idGroupe, idBind)
			{
				evarisk("#chargementBox' . TABLE_EVA_EVALUATOR_GROUP_BIND . '").html(\'<img src="' . PICTO_LOADING_ROUND . '" alt="loading" />\');
				evarisk("#listeGroupesEvaluateurs").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
					"post": "true",
					"table": "' . TABLE_EVA_EVALUATOR_GROUP_BIND . '",
					"act": "delete",
					"idGroupe": idGroupe,
					"idBind": idBind,
					"tableElement": "' . $tableElement . '",
					"idElement": "' . $idElement . '"
				});
			}
		</script>';

		/*	Recuperation des groupes deja affectes	*/
		$idTable = 'groupesEvaluateurs' . $tableElement . $idElement;
		$titres = array(ucfirst(strtolower(sprintf(__('Nom %s', 'evarisk'), __('du groupe d\'&eacute;valuateurs', 'evarisk')))), ucfirst(strtolower(sprintf(__('Description %s', 'evarisk'), __('du groupe d\'&eacute;valuateurs', 'evarisk')))), __('Nombre d\'&eacute;valuateur', 'evarisk'), __('Action', 'evarisk'));
		$listeGroupesAffectes = evaUserEvaluatorGroup::getBindGroups($idElement, $tableElement);
		foreach($listeGroupesAffectes as $groupeAffecte)
		{
			$groupInformation = array();
			$groupInformation = evaUserEvaluatorGroup::getUserEvaluatorGroup($groupeAffecte->id_group);

			unset($valeurs);
			$idLigne = $tableElement . $idElement . 'groupeEvaluateur' . $groupeAffecte->id_group;
			$idCbLigne = 'cb_' . $idLigne;
			$valeurs[] = array('value'=>$groupInformation[0]->evaluator_group_name);
			$valeurs[] = array('value'=>$groupInformation[0]->evaluator_group_description);
			$valeurs[] = array('value'=>$groupInformation[0]->TOTALUSERNUMBER);
			$valeurs[] = array('value'=>'<img style="width:' . TAILLE_PICTOS . ';" src="' . PICTO_DELETE . '" id="' . $idCbLigne . '" onclick="javascript:deleteGroupeEvaluateurBind(\'' . $groupeAffecte->id_group . '\', \'' . $groupeAffecte->id . '\');" />');

			$lignesDeValeurs[] = $valeurs;
			$idLignes[] = $idLigne;
		}
		$classes = array('','','cbNbUserEvaluatorGroup','cbColumnLarge');
		$script = $scriptEnregistrement . '<script type="text/javascript">
			evarisk(document).ready(function() {
				evarisk(\'#' . $idTable . '\').dataTable({
					"bLengthChange": false,
					"bAutoWidth": false,
					"bFilter": false,
					"bInfo": false,
					"aoColumns":
					[
						{ "bSortable": false },
						{ "bSortable": true },
						{ "bSortable": false },
						{ "bSortable": true }
					],
					"aaSorting": [[1,\'asc\']]});
			});
		</script>';
		$groupesUtilisateursDataTable = evaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script);

		return '<div id="chargementBox' . TABLE_EVA_EVALUATOR_GROUP_BIND . '" ></div><div style="float:right;margin:10px;" >' . $listeGroupesNonAffectes . '</div>' . $groupesUtilisateursDataTable;
	}

	/**
	*
	*/
	function afficheListeGroupeDU($tableElement, $idElement)
	{
		require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayDesign.class.php');
		require_once(EVA_LIB_PLUGIN_DIR . 'users/evaUserEvaluatorGroup.class.php');

		$groupesUtilisateursMetaBox = '';
		$groupesUtilisateursMetaBox = $groupesUtilisateursMetaBox . '<div id="message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '" class="updated fade" style="cursor:pointer; display:none;"></div>';
		{//Création dataTable
			$idTable = 'groupesIndividus' . $tableElement . $idElement;
			$titres = array(ucfirst(strtolower(sprintf(__('Nom %s', 'evarisk'), __('du groupe d\'utilisateurs', 'evarisk')))), ucfirst(strtolower(sprintf(__('Description %s', 'evarisk'), __('du groupe d\'utilisateurs', 'evarisk')))), __('Nombre d\'utilisateurs', 'evarisk'));
			unset($lignesDeValeurs);
			//on récupère les groupes.
			$groupesUtilisateurs = evaUserEvaluatorGroup::getUserEvaluatorGroup();
			$groupesLies = evaUserEvaluatorGroup::getBindGroups($idElement, $tableElement);
			if($groupesLies != null and count($groupesLies) > 0)
			{
				foreach($groupesLies as $groupeLie)
				{
					$groupesLiesIds[$groupeLie->id_group] = $groupeLie;
				}
			}
			foreach($groupesUtilisateurs as $groupeUtilisateurs)
			{
				unset($valeurs);
				$idLigne = $tableElement . $idElement . 'groupeUtilisateurs' . $groupeUtilisateurs->evaluator_group_id;
				$idCbLigne = 'cb_' . $idLigne;
				$valeurs[] = array('value'=>$groupeUtilisateurs->evaluator_group_name);
				$valeurs[] = array('value'=>$groupeUtilisateurs->evaluator_group_description);
				$valeurs[] = array('value'=>$groupeUtilisateurs->TOTALUSERNUMBER);
				if(isset($groupesLiesIds[$groupeUtilisateurs->evaluator_group_id]))
				{
					$lignesDeValeurs[] = $valeurs;
				}
				$idLignes[] = $idLigne;
			}

			$groupesUtilisateursDataTable = evaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, '');

			$groupesUtilisateursMetaBox = $groupesUtilisateursMetaBox . $groupesUtilisateursDataTable;
		}

		return $groupesUtilisateursMetaBox;
	}
}