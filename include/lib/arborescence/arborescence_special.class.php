<?php

class arborescence_special
{

	function arborescenceRisque($tableElement, $idElement)
	{
		$completeTree = Arborescence::completeTree($tableElement, $idElement);
		if( is_array($completeTree) )
		{
			foreach($completeTree as $key => $content)
			{
				if( isset($content['nom']) )
				{
					$risks = arborescence_special::getRiskForElement($tableElement, $idElement);
					$completeTree[$key]['risks'] = $risks;
				}

				if(isset($content['content']) && is_array($content['content']))
				{
					foreach($content['content'] as $index => $subContent)
					{
						if( isset($subContent['table']) && isset($subContent['id']) )
						{
							$completeTree[$key]['content'][$index] = arborescence_special::arborescenceRisque($subContent['table'], $subContent['id']);
						}
						else
						{
							foreach($subContent as $subContentIndex => $subContentContent)
							{
								$risks = arborescence_special::getRiskForElement($subContentContent['table'], $subContentContent['id']);
								$completeTree[$key]['content'][$index][$subContentIndex]['risks'] = $risks;
							}
						}
					}
				}
			}
		}

		return $completeTree;
	}

	function lectureArborescenceRisque($arborescenceALire, $selectedTable, $selectedTableElement, $espacement = '')
	{
		$outputContent = '';

		if( is_array($arborescenceALire) )
		{			
			foreach($arborescenceALire as $key => $content)
			{
				if( isset($content['nom']) )
				{
					$selected = '';
					if(($selectedTable == $content['table']) && ($selectedTableElement == $content['id']))
					{
						$selected = ' checked="checked" ';
					}

					$elementPrefix = '';
					switch($content['table'])
					{
						case TABLE_GROUPEMENT:
							$elementPrefix = 'GP' . $content['id'] . ' - ';
							break;
						case TABLE_UNITE_TRAVAIL:
							$elementPrefix = 'UT' . $content['id'] . ' - ';
							break;
					}
					$outputContent .= arborescence_special::getTreeLine($elementPrefix . $content['nom'], $content['table'] . '-_-' . $content['id'], $content['table'], $espacement, $selected);

					/*	Risk list for the current element	*/
					if( is_array($content['risks']) )
					{
						$riskEspacement = $espacement . '&nbsp;&nbsp;&nbsp;&nbsp;';
						foreach($content['risks'] as $riskId => $riskDefinition)
						{
							$selected = '';
							if(($selectedTable == TABLE_RISQUE) && ($selectedTableElement == $riskId))
							{
								$selected = ' checked="checked" ';
							}
							$outputContent .= arborescence_special::getTreeLine($elementPrefix . $riskDefinition->nomDanger, TABLE_RISQUE . '-_-' . $riskId, TABLE_RISQUE, $riskEspacement, $selected);
						}
					}
				}
				else
				{
					$sum = 0;
					foreach($content as $contentKey => $contentInformations)
					{
						$selected = '';
						if(($selectedTable == $contentInformations['table']) && ($selectedTableElement == $contentInformations['id']))
						{
							$selected = ' checked="checked" ';
						}
						$elementPrefix = '';
						switch($contentInformations['table'])
						{
							case TABLE_GROUPEMENT:
								$elementPrefix = 'GP' . $contentInformations['id'] . ' - ';
								break;
							case TABLE_UNITE_TRAVAIL:
								$elementPrefix = 'UT' . $contentInformations['id'] . ' - ';
								break;
						}
						$outputContent .= arborescence_special::getTreeLine($elementPrefix . $contentInformations['nom'], $contentInformations['table'] . '-_-' . $contentInformations['id'], $contentInformations['table'], $espacement, $selected);

						/*	Risk list for the current element	*/
						if( is_array($contentInformations['risks']) )
						{
							$riskEspacement = $espacement . '&nbsp;&nbsp;&nbsp;&nbsp;';
							foreach($contentInformations['risks'] as $riskId => $riskDefinition)
							{
								$selected = '';
								if(($selectedTable == TABLE_RISQUE) && ($selectedTableElement == $riskId))
								{
									$selected = ' checked="checked" ';
								}
								$elementPrefix = 'R' . $riskId . ' - ';
								$outputContent .= arborescence_special::getTreeLine($elementPrefix . $riskDefinition->nomDanger, TABLE_RISQUE . '-_-' . $riskId, TABLE_RISQUE, $riskEspacement, $selected);
							}
						}

						if(isset($contentInformations['content']) && is_array($contentInformations['content']))
						{
							$subespacement = $espacement . '&nbsp;&nbsp;'; 
							$outputContent .= arborescence_special::lectureArborescenceRisque($contentInformations['content'], $selectedTable, $selectedTableElement, $subespacement);
						}
					}
				}

				if(isset($content['content']) && is_array($content['content']))
				{
					$subespacement = $espacement . '&nbsp;&nbsp;'; 
					$outputContent .= arborescence_special::lectureArborescenceRisque($content['content'], $selectedTable, $selectedTableElement, $subespacement);
				}
			}
		}

		return $outputContent;
	}

	function getRiskForElement($tableElement, $idElement)
	{
		$tmprisks = array();

		$risks = Risque::getRisques($tableElement, $idElement, 'Valid');
		foreach($risks as $risk)
		{
			$tmprisks[$risk->id] = $risk;
		}

		return $tmprisks;
	}

	function getTreeLine($lineContent, $elementId, $tableElement, $espacement, $selected)
	{
		switch($tableElement)
		{
			case TABLE_RISQUE:
				$picto = PICTO_LTL_EVAL_RISK;
			break;
			case TABLE_UNITE_TRAVAIL:
				$picto = ULTRASMALL_WORKING_UNIT_PICTO;
			break;
			default:
				$picto = ULTRASMALL_GROUP_PICTO;
			break;
		}
		$hierarchieLine = 									
			'<tr>
				<td style="width:2%;" ><input ' . $selected . ' type="radio" id="r' . $elementId . '" name="selectAffectaion" value="' . $elementId . '" /></td>
				<td><label id="l' . $elementId . '" for="r' . $elementId . '" >' . $espacement . '<img style="height:15px;" alt="' . $tableElement . '" src="' . $picto . '" />' . $lineContent . '</label></td>
			</tr>';

		return $hierarchieLine;
	}

	function display_mini_hierarchy_for_element_affectation($table_element, $id_element){
		if(($table_element != '') && ($id_element > 0)){
		global $wpdb;
		switch($table_element){
			case TABLE_RISQUE:
				/*	Get the associated element	*/
				$query = $wpdb->prepare(
"SELECT R.nomTableElement, R.id_element, D.nom
FROM " . TABLE_RISQUE . " AS R
INNER JOIN " . TABLE_DANGER . " AS D ON ((D.id = R.id_danger) AND (D.Status = 'Valid'))
WHERE R.id = %d", $id_element);
				$risk_element = $wpdb->get_row($query);
				/*	Get associated element informations */
				$space_number = 1;
				switch($risk_element->nomTableElement){
					case TABLE_UNITE_TRAVAIL:{
						$query = $wpdb->prepare("SELECT nom, id_groupement FROM " . $risk_element->nomTableElement . " WHERE id = %d", $risk_element->id_element);
						$direct_parent_element = $wpdb->get_row($query);
						$direct_parent = '<div class="clear" ><span class="hierarchy_element_selector_container" ><input name="selectAffectation[]" type="radio" value="' . $risk_element->nomTableElement . '-_-' . $work_unit_direct_parent->id . '" class="hierarchy_element_selector" id="' . $risk_element->nomTableElement . '-_-' . $work_unit_direct_parent->id . '" /></span>&nbsp;&nbsp;&nbsp;<label for="' . $risk_element->nomTableElement . '-_-' . $work_unit_direct_parent->id . '" ><img style="height:15px;" alt="' . TABLE_UNITE_TRAVAIL . '_' . ELEMENT_IDENTIFIER_UT . $direct_parent_element->id . '" src="' . ULTRASMALL_WORKING_UNIT_PICTO . '" />' . ELEMENT_IDENTIFIER_UT . $risk_element->id_element . '&nbsp-&nbsp;' . $direct_parent_element->nom . '</label></div>';
						$space_number++;
						/*	Get information about work unit direct parent	*/
						$query = $wpdb->prepare("SELECT id, nom FROM " . TABLE_GROUPEMENT . " WHERE id = %d", $direct_parent_element->id_groupement);
						$work_unit_direct_parent = $wpdb->get_row($query);
						$direct_parent = '<div class="clear" ><span class="hierarchy_element_selector_container" ><input name="selectAffectation[]" type="radio" value="' . TABLE_GROUPEMENT . '-_-' . $work_unit_direct_parent->id . '" id="' . TABLE_GROUPEMENT . '-_-' . $work_unit_direct_parent->id . '" class="hierarchy_element_selector" /></span><label for="' . TABLE_GROUPEMENT . '-_-' . $work_unit_direct_parent->id . '" ><img style="height:15px;" alt="' . TABLE_GROUPEMENT . '_' . ELEMENT_IDENTIFIER_GP . $work_unit_direct_parent->id . '" src="' . ULTRASMALL_GROUP_PICTO . '" />' . ELEMENT_IDENTIFIER_GP . $work_unit_direct_parent->id . '&nbsp-&nbsp;' . $work_unit_direct_parent->nom . '</label></div>' . $direct_parent;
						$space_number++;
						$gpt_id = $work_unit_direct_parent->id;
					}break;
					case TABLE_GROUPEMENT:{
						$query = $wpdb->prepare("SELECT nom FROM " . $risk_element->nomTableElement . " WHERE id = %d", $risk_element->id_element);
						$direct_parent = '<div class="clear" ><span class="hierarchy_element_selector_container" ><input name="selectAffectation[]" type="radio" value="' . $risk_element->nomTableElement . '-_-' . $risk_element->id_element . '" id="' . $risk_element->nomTableElement . '-_-' . $risk_element->id_element . '" class="hierarchy_element_selector" /></span><label for="' . $risk_element->nomTableElement . '-_-' . $risk_element->id_element . '" ><img style="height:15px;" alt="' . TABLE_GROUPEMENT . '_' . ELEMENT_IDENTIFIER_GP . $risk_element->id_element . '" src="' . ULTRASMALL_GROUP_PICTO . '" />' . ELEMENT_IDENTIFIER_GP . $risk_element->id_element . '&nbsp-&nbsp;' . $wpdb->get_var($query) . '</label></div>';
						$space_number++;
						$gpt_id = $risk_element->id_element ;
					}break;
				}
				$picto = PICTO_LTL_EVAL_RISK;
				$element_identifier = ELEMENT_IDENTIFIER_R;
				$element_name = $risk_element->nom;
			break;
			case TABLE_UNITE_TRAVAIL:
				$picto = ULTRASMALL_WORKING_UNIT_PICTO;
				$element_identifier = ELEMENT_IDENTIFIER_UT;
				$query = $wpdb->prepare("SELECT nom, id_groupement FROM " . TABLE_UNITE_TRAVAIL . " WHERE id = %d", $id_element);
				$element = $wpdb->get_row($query);

				/*	Get information about work unit direct parent	*/
				$query = $wpdb->prepare("SELECT id, nom FROM " . TABLE_GROUPEMENT . " WHERE id = %d", $element->id_groupement);
				$work_unit_direct_parent = $wpdb->get_row($query);
				$direct_parent = '<div class="clear" ><span class="hierarchy_element_selector_container" ><input name="selectAffectation[]" type="radio" value="' . TABLE_GROUPEMENT . '-_-' . $work_unit_direct_parent->id . '" id="' . TABLE_GROUPEMENT . '-_-' . $work_unit_direct_parent->id . '" class="hierarchy_element_selector" /></span><label for="' . TABLE_GROUPEMENT . '-_-' . $work_unit_direct_parent->id . '" ><img style="height:15px;" alt="' . TABLE_GROUPEMENT . '_' . ELEMENT_IDENTIFIER_GP . $work_unit_direct_parent->id . '" src="' . ULTRASMALL_GROUP_PICTO . '" />' . ELEMENT_IDENTIFIER_GP . $work_unit_direct_parent->id . '&nbsp-&nbsp;' . $work_unit_direct_parent->nom . '</label></div>' . $direct_parent;
				$space_number++;
				$gpt_id = $element->id_groupement;

				$element_name = $element->nom;
			break;
			case TABLE_GROUPEMENT:
				$picto = ULTRASMALL_GROUP_PICTO;
				$element_identifier = ELEMENT_IDENTIFIER_GP;
				$query = $wpdb->prepare("SELECT nom FROM " . TABLE_GROUPEMENT . " WHERE id = %d", $id_element);
				$element_name = $wpdb->get_var($query);
				$gpt_id = $id_element;
			break;
		}

		/*	Get complete tree	*/
		$query = $wpdb->prepare("SELECT * FROM " . TABLE_GROUPEMENT . " WHERE Status = 'Valid' AND id = %d", $gpt_id);
		$element_to_get_parent_tree = $wpdb->get_row($query);
		$ancetres = Arborescence::getAncetre(TABLE_GROUPEMENT, $element_to_get_parent_tree);
		$miniFilAriane = '         ';
		foreach($ancetres as $ancetre){
			if($ancetre->nom != "Groupement Racine"){
				$miniFilAriane .= '<img style="height:15px;" class="middleAlign" alt="' . TABLE_GROUPEMENT . '_' . ELEMENT_IDENTIFIER_GP . $ancetre->id . '" src="' . ULTRASMALL_GROUP_PICTO . '" />' . ELEMENT_IDENTIFIER_GP . $ancetre->id . '&nbsp;-&nbsp;' . $ancetre->nom . ' &raquo; ';
			}
		}

		return substr($miniFilAriane,0, -9) . $direct_parent . '<div class="clear hierarchy_selected_element" ><span class="hierarchy_element_selector_container" ><input name="selectAffectation[]" type="radio" value="' . $table_element . '-_-' . $id_element . '" id="' . $table_element . '-_-' . $id_element . '" checked="checked" class="hierarchy_element_selector" /></span>' . str_repeat('&nbsp;&nbsp;&nbsp;', $space_number) . '<label for="' . $table_element . '-_-' . $id_element . '"><img style="height:15px;" alt="' . $table_element . '_' . $element_identifier . $id_element . '" src="' . $picto . '" />' . $element_identifier . $id_element . '&nbsp-&nbsp;' . $element_name . '</label></div>';
		}
		else{
			return __('Cette t&acirc;che n\'est associ&eacute;e &agrave; aucun &eacute;l&eacute;ment de l\'arborescence pour le moment', 'evarisk');
		}
	}

	function arborescenceActionCorrectives($tableElement, $idElement)
	{
		$completeTree = Arborescence::completeTree($tableElement, $idElement);
		if( is_array($completeTree) )
		{
			foreach($completeTree as $key => $content)
			{
				if( isset($content['nom']) )
				{
					$correctivA = arborescence_special::getACForElement($tableElement, $idElement);
					$completeTree[$key]['correctivA'] = $correctivA;
				}

				if(isset($content['content']) && is_array($content['content']))
				{
					foreach($content['content'] as $index => $subContent)
					{
						if( isset($subContent['table']) && isset($subContent['id']) )
						{
							$completeTree[$key]['content'][$index] = arborescence_special::arborescenceRisque($subContent['table'], $subContent['id']);
						}
						else
						{
							foreach($subContent as $subContentIndex => $subContentContent)
							{
								$correctivA = arborescence_special::getACForElement($subContentContent['table'], $subContentContent['id']);
								$completeTree[$key]['content'][$index][$subContentIndex]['correctivA'] = $correctivA;
							}
						}
					}
				}
			}
		}

		return $completeTree;
	}
	
	function lectureArborescenceAC($arborescenceALire, $selectedTable, $selectedTableElement, $espacement = '')
	{
		$outputContent = '';

		if( is_array($arborescenceALire) )
		{			
			foreach($arborescenceALire as $key => $content)
			{
				if( isset($content['nom']) )
				{
					/*	Risk list for the current element	*/
					if( is_array($content['correctivA']) )
					{
						$riskEspacement = $espacement . '&nbsp;&nbsp;&nbsp;&nbsp;';
						foreach($content['correctivA'] as $riskId => $riskDefinition)
						{
							$outputContent .= arborescence_special::taskContent($riskId);
						}
					}
				}
				else
				{
					$sum = 0;
					foreach($content as $contentKey => $contentInformations)
					{
						/*	Risk list for the current element	*/
						if( is_array($contentInformations['correctivA']) )
						{
							$riskEspacement = $espacement . '&nbsp;&nbsp;&nbsp;&nbsp;';
							foreach($contentInformations['correctivA'] as $riskId => $riskDefinition)
							{
							$outputContent .= arborescence_special::taskContent($riskId);
							}
						}

						if(isset($contentInformations['content']) && is_array($contentInformations['content']))
						{
							$subespacement = $espacement . '&nbsp;&nbsp;'; 
							$outputContent .= arborescence_special::lectureArborescenceAC($contentInformations['content'], $selectedTable, $selectedTableElement, $subespacement);
						}
					}
				}

				if(isset($content['content']) && is_array($content['content']))
				{
					$subespacement = $espacement . '&nbsp;&nbsp;'; 
					$outputContent .= arborescence_special::lectureArborescenceAC($content['content'], $selectedTable, $selectedTableElement, $subespacement);
				}
			}
		}

		return $outputContent;
	}

	function taskContent($id)
	{
		$tache = new EvaTask($id);
		$tache->load();
		$TasksAndSubTasks = $tache->getDescendants();
		$TasksAndSubTasks->addTask($tache);
		$TasksAndSubTasks = $TasksAndSubTasks->getTasks();
		if($TasksAndSubTasks != null AND count($TasksAndSubTasks) > 0)
		{
			foreach($TasksAndSubTasks as $task)
			{
				if($task->id != $tache->id)
				{
					$existingPreconisation .= '* ' . $task->name;
					if($task->description != '')
					{
						$existingPreconisation .= '(' . $task->description . ')';
					}
					$existingPreconisation .= " 
";
				}
				$activities = $task->getActivitiesDependOn();
				$activities = $activities->getActivities();
				if(($activities != null) AND (count($activities) > 0))
				{
					foreach($activities as $activity)
					{
						$existingPreconisation .= '* ' . $activity->name;
						if($activity->description != '')
						{
							$existingPreconisation .= '(' . $activity->description . ')';
						}
						$existingPreconisation .= " 
";
					}
				}
			}
		}
		return $existingPreconisation;
	}

	function getACForElement($tableElement, $idElement)
	{
		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT id_tache 
			FROM " . TABLE_LIAISON_TACHE_ELEMENT . "
			WHERE id_element = %d
				AND table_element = %s",
		$idElement, $tableElement);

		return $wpdb->get_results($query);
	}

}