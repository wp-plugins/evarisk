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
					$outputContent .= arborescence_special::getTreeLine($content['nom'], $content['table'] . '_-_' . $content['id'], $content['table'], $espacement, $selected);

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
							$outputContent .= $outputContent .= arborescence_special::getTreeLine($riskDefinition->nomDanger, TABLE_RISQUE . '_-_' . $riskId, TABLE_RISQUE, $riskEspacement, $selected);
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
						$outputContent .= arborescence_special::getTreeLine($contentInformations['nom'], $contentInformations['table'] . '_-_' . $contentInformations['id'], $contentInformations['table'], $espacement, $selected);

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
								$outputContent .= arborescence_special::getTreeLine($riskDefinition->nomDanger, TABLE_RISQUE . '_-_' . $riskId, TABLE_RISQUE, $riskEspacement, $selected);
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

}