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
					$outputContent .= arborescence_special::getTreeLine($elementPrefix . $content['nom'], $content['table'] . '_-_' . $content['id'], $content['table'], $espacement, $selected);

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
							$outputContent .= arborescence_special::getTreeLine($elementPrefix . $riskDefinition->nomDanger, TABLE_RISQUE . '_-_' . $riskId, TABLE_RISQUE, $riskEspacement, $selected);
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
						$outputContent .= arborescence_special::getTreeLine($elementPrefix . $contentInformations['nom'], $contentInformations['table'] . '_-_' . $contentInformations['id'], $contentInformations['table'], $espacement, $selected);

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
								$outputContent .= arborescence_special::getTreeLine($elementPrefix . $riskDefinition->nomDanger, TABLE_RISQUE . '_-_' . $riskId, TABLE_RISQUE, $riskEspacement, $selected);
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