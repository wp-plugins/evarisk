<?php
/**
 * 
 * @author Soci&eacute;t&eacute; Evarisk
 * @version v5.0
 */
include_once(EVA_CONFIG);

class eva_documentUnique
{

	/**
	*	Get the different risqs for an element and its descendant
	*
	*	@param mixed $tableElement The element type we want to get the risqs for
	*	@param integer $idElement The element identifier we want to get the risqs for
	*
	*	@return array $lignesDeValeurs The different risqs ordered by element
	*/
	function listeRisquePourElement($tableElement, $idElement)
	{
		$lignesDeValeurs = array();
		$temp = Risque::getRisques($tableElement, $idElement, "Valid");
		if($temp != null)
		{
			foreach($temp as $risque)
			{
				$risques['"' . $risque->id . "'"][] = $risque; 
			}
		}
		if($risques != null)
		{
			$i = 0;
			unset($tmpLigneDeValeurs);
			foreach($risques as $risque)
			{				
				$idMethode = $risque[0]->id_methode;
				$score = Risque::getScoreRisque($risque);
				$quotation = Risque::getEquivalenceEtalon($idMethode, $score, $risque[0]->date);
				$niveauSeuil = Risque::getSeuil($quotation);

				switch($tableElement)
				{
					case TABLE_GROUPEMENT:
						$element = EvaGroupement::getGroupement($idElement);
						break;
					case TABLE_UNITE_TRAVAIL:
						$element = eva_UniteDeTravail::getWorkingUnit($idElement);
						break;
				}

				$tmpLigneDeValeurs[$quotation][$i][] = array('value' => $element->nom, 'class' => '');
				$tmpLigneDeValeurs[$quotation][$i][] = array('value' => $quotation, 'class' => 'Seuil_' . $niveauSeuil);
				$tmpLigneDeValeurs[$quotation][$i][] = array('value' => $risque[0]->nomDanger, 'class' => '');
				$tmpLigneDeValeurs[$quotation][$i][] = array('value' => nl2br($risque[0]->descriptionDanger), 'class' => '');
				$tmpLigneDeValeurs[$quotation][$i][] = array('value' => nl2br($risque[0]->commentaire), 'class' => '');
				$i++;
			}

			krsort($tmpLigneDeValeurs);
			foreach($tmpLigneDeValeurs as $quotationLigneDeValeur => $contenuLigneDeValeur)
			{
				foreach($contenuLigneDeValeur as $ligneDeValeur)
				{
					$lignesDeValeurs[] = $ligneDeValeur;
				}
			}
		}	
		return $lignesDeValeurs;
	}

	
	function listRisk($tableElement, $idElement)
	{
		$lignesDeValeurs = array();

		switch($tableElement)
		{
			case TABLE_GROUPEMENT:
				/*	Recuperation des unites du groupement	*/
				$listeUnitesDeTravail = EvaGroupement::getUnitesEtGroupementDescendants($idElement);
				if(is_array($listeUnitesDeTravail))
				{
					foreach($listeUnitesDeTravail as $key => $uniteDefinition)
					{
						/*	Recuperation des risques associes a l'unite	*/
						$lignesDeValeurs = array_merge($lignesDeValeurs, eva_documentUnique::listeRisquePourElement($uniteDefinition['table'], $uniteDefinition['value']->id));
					}
				}
			break;
		}

		/*	Recuperation des risques associes au groupement	*/
		$lignesDeValeurs = array_merge($lignesDeValeurs, eva_documentUnique::listeRisquePourElement($tableElement, $idElement));

		return $lignesDeValeurs;
	}


	/**
	*	Output the risqs summary for an element
	*
	*	@param mixed $tableElement The element type we want to show the summary for
	*	@param integer $idElement The element identifier we want to show the summary for
	*	@param mixed $typeBilan Define if the output must be the summary by risq or by work unit
	*	@param mixed $outPut Define the ouptu type we want to get
	*
	*	@return mixed An html result with the different risqs or a link to print the work unit sheet
	*/
	function bilanRisque($tableElement, $idElement, $typeBilan = 'ligne', $outPut = 'html')
	{
		unset($titres, $classes, $idLignes, $lignesDeValeurs);

		if($tableElement == TABLE_GROUPEMENT)
		{
			$lignesDeValeurs = eva_documentUnique::listRisk($tableElement, $idElement);

			if($outPut == 'html')
			{
				/*	Si on veut le bilan par ligne	*/
				if($typeBilan == 'ligne')
				{
					{//Cr&eacute;ation de la table	
						{//Script de d&eacute;finition de la dataTable pour la somme des risques par ligne
							$idTable = 'tableBilanRisqueUnitaire' . $tableElement . $idElement . $typeBilan;
							$titres[] = __("&Eacute;l&eacute;ment", 'evarisk');
							$titres[] = __("Quotation", 'evarisk');
							$titres[] = ucfirst(strtolower(sprintf(__("nom %s", 'evarisk'), __("du danger", 'evarisk'))));
							$titres[] = ucfirst(strtolower(sprintf(__("description %s", 'evarisk'), __("du danger", 'evarisk'))));
							$titres[] = ucfirst(strtolower(sprintf(__("commentaire %s", 'evarisk'), __("sur le risque", 'evarisk'))));
							$classes[] = 'columnQuotation';
							$classes[] = 'columnQuotation';
							$classes[] = 'columnNomDanger';
							$classes[] = 'columnDescriptionDanger';
							$classes[] = 'columnCommentaireRisque';

							$scriptVoirRisque = $scriptRisque . '
							<script type="text/javascript">
							evarisk(document).ready(function() {
								evarisk("#' . $idTable . '").dataTable(
								{
								"bPaginate": false, 
								"bLengthChange": false,
								"bAutoWidth": false,
								"bFilter": false,
								"bInfo": false,
								"aoColumns": 
								[
									{ "bSortable": true},
									{ "bSortable": false, "sType": "numeric"},
									{ "bSortable": false},
									{ "bSortable": false},
									{ "bSortable": false}
								],
									"aaSorting": [[1,"desc"]]});
								evarisk("#' . $idTable . ' tfoot").remove();
							});
							</script>';

							$recapitulatifRisque = EvaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $scriptVoirRisque);
						}

						return $recapitulatifRisque;
					}
				}

				/*	Si on veut le bilan par unit&eacute; de travail	*/
				elseif($typeBilan == 'unite')
				{
					$bilanParUnite = eva_documentUnique::bilanParUnite($tableElement, $idElement);

					$recapitulatifRisque = 
					'<table summary="risqsSummary' . $tableElement . '-' . $idElement . '" cellpadding="0" cellspacing="0" class="widefat post fixed">
						<thead>
							<tr>
								<th>' . __('&Eacute;l&eacute;ment', 'evarisk') . '</th>
								<th>' . __('Somme des quotation', 'evarisk') . '</th>
							</tr>
						</thead>
						
						<tfoot>
						</tfoot>
						
						<tbody>
							' . eva_documentUnique::readBilanParUnite($bilanParUnite, '', 'html') . '
						</tbody>
					</table>';

					return $recapitulatifRisque;
				}
			}
			else
			{
				return $lignesDeValeurs;
			}
		}
	}


	/**
	*	Allows to get the different tree element with the different user groups affected
	*
	*	@param mixed $tableElement The element type we want to get the different risqs for
	*	@param integer $idElement The element identifier we want to get the different risqs for
	*
	*	@return array $completeTree An array with the complete element tree and their affected user groups
	*/
	function listeGroupeUtilisateurAffectes($tableElement, $idElement)
	{
		$completeTree = Arborescence::completeTree($tableElement, $idElement);
		if( is_array($completeTree) )
		{
			foreach($completeTree as $key => $content)
			{
				if( isset($content['nom']) )
				{
					$groups = evaUserGroup::getBindGroupsWithInformations($idElement, $tableElement);
					$completeTree[$key]['groups'] = $groups;
				}
				$i++;

				if(isset($content['content']) && is_array($content['content']))
				{
					foreach($content['content'] as $index => $subContent)
					{
						$subespacement = $espacement . '&nbsp;&nbsp;';
						if( isset($subContent['table']) && isset($subContent['id']) )
						{
							$completeTree[$key]['content'][$index] = eva_documentUnique::listeGroupeUtilisateurAffectes($subContent['table'], $subContent['id'], $subespacement, $i);
						}
						else
						{
							foreach($subContent as $subContentIndex => $subContentContent)
							{
								$groups = evaUserGroup::getBindGroupsWithInformations($subContentContent['id'], $subContentContent['table']);
								$completeTree[$key]['content'][$index][$subContentIndex]['groups'] = $groups;
							}
						}
					}
				}
			}
		}

		return $completeTree;
	}


	/**
	*	Prepare the output for element tree with their user groups
	*
	*	@param array $groupeALire The complete tree to read and to prepare to output
	*	@param mixed $espacement Allows to reproduce the tree by putting a specific number of spaces before each element
	*	@param mixed $typeSortie Specify wich output type is asked (html, print)
	*	@param integer $i The main tree index
	*
	*	@return mixed $outputContent Depending on the "typeSortie" parameter, output an array or an html code
	*/
	function readlisteGroupeUtilisateurAffectes($groupesALire, $espacement = '', $typeSortie = 'html', $i = 0)
	{
		if($typeSortie == 'html')
		{
			$outputContent = '';
		}
		elseif($typeSortie == 'print')
		{
			$outputContent = array();
		}

		if( is_array($groupesALire) )
		{			
			foreach($groupesALire as $key => $content)
			{
				if( isset($content['nom']) )
				{
					$elementGroup = $espacement . '  ';
					if( is_array($content['groups']) )
					{
						foreach($content['groups'] as $groupeId => $groupDefinition)
						{
							$elementGroup .= $groupDefinition['name'] . ', ';
						}
					}

					if($typeSortie == 'html')
					{
						$outputContent .= 
							'<tr>
								<td>' . $espacement . $content['nom'] . '</td>
								<td>' . substr($elementGroup, 0, -2) . '</td>
							</tr>';
					}
					elseif($typeSortie == 'print')
					{
						$outputContent[$i]['nomElement'] = str_replace('&nbsp;', " ", $espacement) . $content['nom'];
						$outputContent[$i]['listeGroupes'] = substr(str_replace('&nbsp;', " ", $elementGroup), 0, -2);
					}
				}
				else
				{
					$sum = 0;
					foreach($content as $contentKey => $contentInformations)
					{
						$elementGroup = $espacement . '  ';
						if( is_array($contentInformations['groups']) )
						{
							foreach($contentInformations['groups'] as $groupeId => $groupDefinition)
							{
								$elementGroup .= $groupDefinition['name'] . ', ';
							}
						}

						if($typeSortie == 'html')
						{
							$outputContent .= 
								'<tr>
									<td>' . $espacement . $contentInformations['nom'] . '</td>
									<td>' . substr($elementGroup, 0, -2) . '</td>
								</tr>';
						}
						elseif($typeSortie == 'print')
						{
							$outputContent[$i]['nomElement'] = str_replace('&nbsp;', " ", $espacement) . $contentInformations['nom'];
							$outputContent[$i]['listeGroupes'] = substr(str_replace('&nbsp;', " ", $elementGroup), 0, -2);
						}
						$i++;

						if(isset($contentInformations['content']) && is_array($contentInformations['content']))
						{
							$subespacement = $espacement . '&nbsp;&nbsp;'; 
							if($typeSortie == 'html')
							{
								$outputContent .= eva_documentUnique::readlisteGroupeUtilisateurAffectes($contentInformations['content'], $subespacement, $typeSortie);
							}
							else
							{
								$sousLignes = eva_documentUnique::readlisteGroupeUtilisateurAffectes($contentInformations['content'], $subespacement, $typeSortie, $i);
								foreach($sousLignes as $index => $sousLigneContent)
								{
									$outputContent[] = $sousLigneContent;
									$i++;
								}
							}
						}
					}
				}
				$i++;

				if(isset($content['content']) && is_array($content['content']))
				{
					$subespacement = $espacement . '&nbsp;&nbsp;'; 
					if($typeSortie == 'html')
					{
						$outputContent .= eva_documentUnique::readlisteGroupeUtilisateurAffectes($content['content'], $subespacement, $typeSortie);
					}
					else
					{
						$sousLignes = eva_documentUnique::readlisteGroupeUtilisateurAffectes($content['content'], $subespacement, $typeSortie, $i);
						foreach($sousLignes as $index => $sousLigneContent)
						{
							$outputContent[] = $sousLigneContent;
							$i++;
						}
					}
				}
			}
		}

		return $outputContent;
	}


	/**
	*	Prepare the output of all the existing user groups
	*
	*	@param array $groupeALire The complete group list we have to treat
	*	@param mixed $typeSortie Specify wich output type is asked (html, print)
	*
	*	@return mixed $outputContent Depending on the "typeSortie" parameter, output an array or an html code
	*/
	function readListeGroupesUtilisateurs($groupesALire, $typeSortie = 'html')
	{
		if($typeSortie == 'html')
		{
			$outputContent = '';
		}
		elseif($typeSortie == 'print')
		{
			$outputContent = array();
		}

		if( is_array($groupesALire) )
		{
			foreach($groupesALire as $index => $groupDefinition)
			{
				if($groupDefinition->user_group_name == '')$groupDefinition->user_group_name = '&nbsp;';
				if($groupDefinition->user_group_description == '')$groupDefinition->user_group_description = '&nbsp;';
				if($groupDefinition->TOTALUSERNUMBER == '')$groupDefinition->TOTALUSERNUMBER = '0';
				if($typeSortie == 'html')
				{
					$outputContent .= 
						'<tr>
							<td>' . $groupDefinition->user_group_name . '</td>
							<td>' . $groupDefinition->user_group_description . '</td>
							<td>' . $groupDefinition->TOTALUSERNUMBER . '</td>
						</tr>';
				}
				elseif($typeSortie == 'print')
				{
					$outputContent[$index]['userGroupName'] = $groupDefinition->user_group_name;
					$outputContent[$index]['userGroupDescription'] = $groupDefinition->user_group_description;
					$outputContent[$index]['userGroupTotalUserNumber'] = $groupDefinition->TOTALUSERNUMBER;
				}
			}
		}

		return $outputContent;
	}


	/**
	*	Get all the risqs for an element and all it's descendant
	*
	*	@param mixed $tableElement The element type we want to get the different risqs for
	*	@param integer $idElement The element identifier we want to get the different risqs for
	*
	*	@return array $bilanRisqueParUniteArborescent An array with all the risq for the element we are on (with all the descendant risqs)
	*/
	function bilanParUnite($tableElement, $idElement, $espacement = '', $i = 0)
	{
		$completeTree = Arborescence::completeTree($tableElement, $idElement);
		if( is_array($completeTree) )
		{
			foreach($completeTree as $key => $content)
			{
				if( isset($content['nom']) )
				{
					/*	Get the risq for the current element	*/
					$risqForElement = Risque::getSommeRisque($content['table'], $content['id']);
					$completeTree[$key]['quotation'] = $risqForElement['value'];
				}
				$i++;

				if(isset($content['content']) && is_array($content['content']))
				{
					foreach($content['content'] as $index => $subContent)
					{
						$subespacement = $espacement . '&nbsp;&nbsp;';
						if( isset($subContent['table']) && isset($subContent['id']) )
						{
							$completeTree[$key]['content'][$index] = eva_documentUnique::bilanParUnite($subContent['table'], $subContent['id'], $subespacement, $i);
						}
						else
						{
							foreach($subContent as $subContentIndex => $subContentContent)
							{
								/*	Get the risq for the current element	*/
								$risqForElement = Risque::getSommeRisque($subContentContent['table'], $subContentContent['id']);
								$completeTree[$key]['content'][$index][$subContentIndex]['quotation'] = $risqForElement['value'];
							}
						}
					}
				}
			}
		}

		return $completeTree;
	}


	/**
	*	Get a list of risqs and prepare the output for a given output type for a work unit
	*
	*	@param array $bilanALire An array with all risqs to read. This array is ordered
	*	@param mixed $espacement The spacement element to recreate the tree
	*	@param array $typeSortie The output type we want to get
	*	@param array $i A counter to order the tree
	*
	*	@return mixed $outputContent Depending on the asked output type an html output or a prepared file
	*/
	function readBilanParUnite($bilanALire, $espacement = '', $typeSortie = 'html', $i = 0)
	{
		if($typeSortie == 'html')
		{
			$outputContent = '';
		}
		elseif($typeSortie == 'print')
		{
			$outputContent = array();
		}

		if( is_array($bilanALire) )
		{
			foreach($bilanALire as $key => $content)
			{
				if( isset($content['nom']) )
				{
					if($typeSortie == 'html')
					{
						$outputContent .= 
							'<tr>
								<td>' . $espacement . $content['nom'] . '</td>
								<td>' . $content['quotation'] . '</td>
							</tr>';
					}
					elseif($typeSortie == 'print')
					{
						$outputContent[$i]['nomElement'] = str_replace('&nbsp;', " ", $espacement) . $content['nom'];
						$outputContent[$i]['quotationTotale'] = $content['quotation'];
					}
				}
				else
				{
					$sum = 0;
					foreach($content as $contentKey => $contentInformations)
					{
						if($typeSortie == 'html')
						{
							$outputContent .= 
								'<tr>
									<td>' . $espacement . $contentInformations['nom'] . '</td>
									<td>' . $contentInformations['quotation'] . '</td>
								</tr>';
						}
						elseif($typeSortie == 'print')
						{
							$outputContent[$i]['nomElement'] = str_replace('&nbsp;', " ", $espacement) . $contentInformations['nom'];
							$outputContent[$i]['quotationTotale'] = $contentInformations['quotation'];
						}
						$i++;
						if(isset($contentInformations['content']) && is_array($contentInformations['content']))
						{
							$subespacement = $espacement . '&nbsp;&nbsp;'; 
							if($typeSortie == 'html')
							{
								$outputContent .= eva_documentUnique::readBilanParUnite($contentInformations['content'], $subespacement, $typeSortie);
							}
							else
							{
								$sousLignes = eva_documentUnique::readBilanParUnite($contentInformations['content'], $subespacement, $typeSortie, $i);
								foreach($sousLignes as $index => $sousLigneContent)
								{
									$outputContent[] = $sousLigneContent;
									$i++;
								}
							}
						}
					}
				}
				$i++;

				if(isset($content['content']) && is_array($content['content']))
				{
					$subespacement = $espacement . '&nbsp;&nbsp;'; 
					if($typeSortie == 'html')
					{
						$outputContent .= eva_documentUnique::readBilanParUnite($content['content'], $subespacement, $typeSortie);
					}
					else
					{
						$sousLignes = eva_documentUnique::readBilanParUnite($content['content'], $subespacement, $typeSortie, $i);
						foreach($sousLignes as $index => $sousLigneContent)
						{
							$outputContent[] = $sousLigneContent;
							$i++;
						}
					}
				}
			}
		}

		return $outputContent;
	}


	/**
	*	Get a list of risqs and order the different element in an array
	*
	*	@param array $bilanALire An array with all risqs to read. This array is ordered
	*
	*	@return array $listeRisque An ordered array with all the risqs by line. Ordered by risq level
	*/
	function readBilanUnitaire($bilanALire)
	{
		$listeRisque = $listeRisque[SEUIL_BAS_FAIBLE] = $listeRisque[SEUIL_BAS_APLANIFIER] = $listeRisque[SEUIL_BAS_ATRAITER] = $listeRisque[SEUIL_BAS_INACCEPTABLE] = array();

		$listeTousRisques = $bilanALire;

		if( is_array($listeTousRisques) )
		{
			foreach($listeTousRisques as $key => $informationsRisque)
			{
				if($informationsRisque[1]['value'] >= SEUIL_BAS_INACCEPTABLE)
				{
					$listeRisque[SEUIL_BAS_INACCEPTABLE][$informationsRisque[1]['value']][$key]['nomElement'] = $informationsRisque[0]['value'];
					$listeRisque[SEUIL_BAS_INACCEPTABLE][$informationsRisque[1]['value']][$key]['quotationRisque'] = $informationsRisque[1]['value'];
					$listeRisque[SEUIL_BAS_INACCEPTABLE][$informationsRisque[1]['value']][$key]['nomDanger'] = $informationsRisque[2]['value'];
					$listeRisque[SEUIL_BAS_INACCEPTABLE][$informationsRisque[1]['value']][$key]['commentaireRisque'] = $informationsRisque[4]['value'];
				}
				elseif(($informationsRisque[1]['value'] >= SEUIL_BAS_ATRAITER) && ($informationsRisque[1]['value'] <= SEUIL_HAUT_ATRAITER))
				{
					$listeRisque[SEUIL_BAS_ATRAITER][$informationsRisque[1]['value']][$key]['nomElement'] = $informationsRisque[0]['value'];
					$listeRisque[SEUIL_BAS_ATRAITER][$informationsRisque[1]['value']][$key]['quotationRisque'] = $informationsRisque[1]['value'];
					$listeRisque[SEUIL_BAS_ATRAITER][$informationsRisque[1]['value']][$key]['nomDanger'] = $informationsRisque[2]['value'];
					$listeRisque[SEUIL_BAS_ATRAITER][$informationsRisque[1]['value']][$key]['commentaireRisque'] = $informationsRisque[4]['value'];
				}
				elseif(($informationsRisque[1]['value'] >= SEUIL_BAS_APLANIFIER) && ($informationsRisque[1]['value'] <= SEUIL_HAUT_APLANIFIER))
				{
					$listeRisque[SEUIL_BAS_APLANIFIER][$informationsRisque[1]['value']][$key]['nomElement'] = $informationsRisque[0]['value'];
					$listeRisque[SEUIL_BAS_APLANIFIER][$informationsRisque[1]['value']][$key]['quotationRisque'] = $informationsRisque[1]['value'];
					$listeRisque[SEUIL_BAS_APLANIFIER][$informationsRisque[1]['value']][$key]['nomDanger'] = $informationsRisque[2]['value'];
					$listeRisque[SEUIL_BAS_APLANIFIER][$informationsRisque[1]['value']][$key]['commentaireRisque'] = $informationsRisque[4]['value'];
				}
				elseif(($informationsRisque[1]['value'] <= SEUIL_HAUT_FAIBLE))
				{
					$listeRisque[SEUIL_BAS_FAIBLE][$informationsRisque[1]['value']][$key]['nomElement'] = $informationsRisque[0]['value'];
					$listeRisque[SEUIL_BAS_FAIBLE][$informationsRisque[1]['value']][$key]['quotationRisque'] = $informationsRisque[1]['value'];
					$listeRisque[SEUIL_BAS_FAIBLE][$informationsRisque[1]['value']][$key]['nomDanger'] = $informationsRisque[2]['value'];
					$listeRisque[SEUIL_BAS_FAIBLE][$informationsRisque[1]['value']][$key]['commentaireRisque'] = $informationsRisque[4]['value'];
				}
			}
			if(is_array($listeRisque[SEUIL_BAS_FAIBLE]))krsort($listeRisque[SEUIL_BAS_FAIBLE]);
			if(is_array($listeRisque[SEUIL_BAS_APLANIFIER]))krsort($listeRisque[SEUIL_BAS_APLANIFIER]);
			if(is_array($listeRisque[SEUIL_BAS_ATRAITER]))krsort($listeRisque[SEUIL_BAS_ATRAITER]);
			if(is_array($listeRisque[SEUIL_BAS_INACCEPTABLE]))krsort($listeRisque[SEUIL_BAS_INACCEPTABLE]);
		}

		return $listeRisque;
	}


	/**
	*	Output a form with the different field needed to save and generate a new document. If the element type is a work unit, we propose to print the work unit sheet
	*
	*	@param mixed $tableElement The element type we want to generate a document for
	*	@param integer $idElement The element identifier we want to generate a document for
	*
	*	@return mixed The form or a link to the work unit sheet to print
	*/
	function formulaireGenerationDocumentUnique($tableElement, $idElement)
	{
		require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/documentUnique/templateDocumentUnique.tpl.php');

		unset($formulaireDocumentUniqueParams);
		$formulaireDocumentUniqueParams = array();
		$lastDocumentUnique = eva_documentUnique::getDernierDocumentUnique($tableElement, $idElement);
		$formulaireDocumentUniqueParams['#DATEFORM1#'] = date('Y-m-d');

		if($tableElement == TABLE_GROUPEMENT)
		{
			$formulaireDocumentUniqueParams['#DATEDEBUT1#'] = date('Y-m-d');
			$formulaireDocumentUniqueParams['#DATEFIN1#'] = date('Y-m-d');
			$groupementInformations = Evagroupement::getGroupement($idElement);
			$formulaireDocumentUniqueParams['#NOMENTREPRISE#'] = $groupementInformations->nom;
			if(($groupementInformations->nom != $lastDocumentUnique->nomSociete) && ($lastDocumentUnique->nomSociete != ''))
			{
				$formulaireDocumentUniqueParams['#NOMENTREPRISE#'] = (isset($lastDocumentUnique->nomSociete) && ($lastDocumentUnique->nomSociete != '')) ? $lastDocumentUnique->nomSociete : '';
			}
			$formulaireDocumentUniqueParams['#TELFIXE#'] = (isset($lastDocumentUnique->telephoneFixe) && ($lastDocumentUnique->telephoneFixe != '')) ? $lastDocumentUnique->telephoneFixe : '';
			$formulaireDocumentUniqueParams['#TELPORTABLE#'] = (isset($lastDocumentUnique->telephonePortable) && ($lastDocumentUnique->telephonePortable != '')) ? $lastDocumentUnique->telephonePortable : '';
			$formulaireDocumentUniqueParams['#TELFAX#'] = (isset($lastDocumentUnique->telephoneFax) && ($lastDocumentUnique->telephoneFax != '')) ? $lastDocumentUnique->telephoneFax : '';
			$formulaireDocumentUniqueParams['#EMETTEUR#'] = (isset($lastDocumentUnique->emetteurDUER) && ($lastDocumentUnique->emetteurDUER != '')) ? $lastDocumentUnique->emetteurDUER : '';
			$formulaireDocumentUniqueParams['#DESTINATAIRE#'] = (isset($lastDocumentUnique->destinataireDUER) && ($lastDocumentUnique->destinataireDUER != '')) ? $lastDocumentUnique->destinataireDUER : '';
			$formulaireDocumentUniqueParams['#NOMDOCUMENT#'] = date('Ymd') . '_documentUnique_GP' . $idElement . '_' . eva_tools::slugify_noaccent(str_replace(' ', '_', $groupementInformations->nom));
			$formulaireDocumentUniqueParams['#METHODOLOGIE#'] = (isset($lastDocumentUnique->methodologieDUER) && ($lastDocumentUnique->methodologieDUER != '')) ? $lastDocumentUnique->methodologieDUER : ($methodologieParDefaut);
			$formulaireDocumentUniqueParams['#SOURCES#'] = (isset($lastDocumentUnique->sourcesDUER) && ($lastDocumentUnique->sourcesDUER != '')) ? $lastDocumentUnique->sourcesDUER : ($sourcesParDefaut);
			$lastDocumentUnique->id_model = (isset($lastDocumentUnique->id_model) && ($lastDocumentUnique->id_model != '')) ? $lastDocumentUnique->id_model : 0;

			$output = 
			EvaDisplayDesign::feedTemplate(EvaDisplayDesign::getFormulaireGenerationDUER(), $formulaireDocumentUniqueParams) . 
			'<input type="hidden" name="oldIdModel" id="oldIdModel" value="' . $lastDocumentUnique->id_model . '" />
			<script type="text/javascript" >
				evarisk(document).ready(function(){
					evarisk("#dateCreation").datepicker();
					evarisk("#dateCreation").datepicker("option", {dateFormat: "yy-mm-dd"});

					evarisk("#dateDebutAudit").datepicker();
					evarisk("#dateDebutAudit").datepicker("option", {dateFormat: "yy-mm-dd"});

					evarisk("#dateFinAudit").datepicker();
					evarisk("#dateFinAudit").datepicker("option", {dateFormat: "yy-mm-dd"});

					evarisk("#genererDUER").click(function(){
						evarisk("#divDocumentUnique").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
							"post":"true", 
							"table":"' . TABLE_DUER . '", 
							"act":"saveDocumentUnique", 
							"tableElement":"' . $tableElement . '",
							"idElement":"' . $idElement . '", 
							"dateCreation":evarisk("#dateCreation").val(), 
							"dateDebutAudit":evarisk("#dateDebutAudit").val(), 
							"dateFinAudit":evarisk("#dateFinAudit").val(), 
							"nomEntreprise":evarisk("#nomEntreprise").val(),
							"telephoneFixe":evarisk("#telephoneFixe").val(),
							"telephonePortable":evarisk("#telephonePortable").val(),
							"numeroFax":evarisk("#numeroFax").val(),
							"emetteur":evarisk("#emetteur").val(),
							"destinataire":evarisk("#destinataire").val(),
							"nomDuDocument":evarisk("#nomDuDocument").val(),
							"methodologie":evarisk("#methodologie").val(),
							"id_model":evarisk("#DUERModelToUse").val(),
							"sources":evarisk("#sources").val()
						});
						evarisk("#divDocumentUnique").html(\'<img src="' . PICTO_LOADING . '" />\');
						evarisk("#divDocumentUnique").html(\'<img src="' . PICTO_LOADING . '" />\');
					});';

					if($lastDocumentUnique->id_model > 1)
					{
						$output .= '
						setTimeout(function(){
							evarisk("#modelDefaut").click();
						},100);';
					}

					$output .= 'evarisk("#ui-datepicker-div").hide();
					evarisk("#modelDefaut").click(function(){
						clearTimeout();
						setTimeout(function(){
							if(!evarisk("#modelDefaut").is(":checked"))
							{
								evarisk("#documentUniqueResultContainer").html(\'<img src="' . EVA_IMG_DIVERS_PLUGIN_URL . 'loading.gif" alt="loading" />\');
								evarisk("#documentUniqueResultContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_DUER . '", "act":"loadNewModelForm", "tableElement":"' . $tableElement . '", "idElement":' . $idElement . '});
								evarisk("#modelListForDUERGeneration").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_GED_DOCUMENTS . '", "act":"loadDocument", "tableElement":"' . $tableElement . '", "idElement":' . $idElement . ', "category":"document_unique", "selection":evarisk("#oldIdModel").val()});
								evarisk("#modelListForDUERGeneration").show();
							}
							else
							{
								evarisk("#documentUniqueResultContainer").html("");
								evarisk("#modelListForDUERGeneration").html("");
								evarisk("#modelListForDUERGeneration").hide();
							}
							},500
						);
					});
				});
			</script>';
		}
		elseif($tableElement == TABLE_UNITE_TRAVAIL)
		{
			$workUnitinformations = eva_UniteDeTravail::getWorkingUnit($idElement);
			$formulaireDocumentUniqueParams['#NOMDOCUMENT#'] = date('Ymd') . '_ficheDePoste_' . eva_tools::slugify_noaccent(str_replace(' ', '_', $workUnitinformations->nom));
			
			$output = EvaDisplayDesign::feedTemplate(EvaDisplayDesign::getFormulaireGenerationFicheDePoste(), $formulaireDocumentUniqueParams) . '
<script type="text/javascript" >
	evarisk(document).ready(function(){
		evarisk("#genererFP").click(function(){
			evarisk("#divImpressionFicheDePoste").html(evarisk("#loadingImg").html());
			evarisk("#divImpressionFicheDePoste").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",
			{
				"post":"true",
				"table":"' . TABLE_DUER . '",
				"act":"saveFichePoste",
				"tableElement":"' . $tableElement . '",
				"idElement":"' . $idElement . '",
				"dateCreation":evarisk("#dateCreationFicheDePoste").val(),
				"nomDuDocument":evarisk("#nomFicheDePoste").val()
			});
		});
	});
</script>';
		}

		return $output;
	}


	/**
	*	Save a "document unique" in database
	*
	*	@param mixed $tableElement The element type we want to save a new document for
	*	@param integer $idElement The element identifier we want to save a new document for
	*	@param array $informationDocumentUnique An array with all information to create the new document. Those informations come from the form
	*
	*	@return array $status An array with the response status, if it's ok or not
	*/
	function saveNewDocumentUnique ($tableElement, $idElement, $informationDocumentUnique)
	{
		global $wpdb;
		$status = array();

		require_once(EVA_LIB_PLUGIN_DIR . 'users/evaUserGroup.class.php');

		$tableElement = eva_tools::IsValid_Variable($tableElement);
		$idElement = eva_tools::IsValid_Variable($idElement);

		{	/*	Calcul du pourcentage de participant &agrave; l'&eacute;valuation */
			$pourcentageParticipant = 0;
			if(evaUserGroup::getUserNumberInWorkUnit($idElement, $tableElement) > 0)
			{
				$pourcentageParticipant = (count(evaUser::getBindUsers($idElement, $tableElement)) * 100) / evaUserGroup::getUserNumberInWorkUnit($idElement, $tableElement);
			}
			if($pourcentageParticipant >= 75)
			{
				$alerte = __("Le pr&eacute;sent document a &eacute;t&eacute; r&eacute;alis&eacute; pour permettre au chef d'entreprise d'avoir une vision des risques hi&eacute;rarchis&eacute;s dans son &eacute;tablissement. Lors de l'&eacute;valuation, " . $pourcentageParticipant . "% des salari&eacute;s de l'entreprise ont particip&eacute; &agrave; la d&eacute;marche d'&eacute;valuation des risques. Nous consid&eacute;rons que le quota des 75% des salari&eacute;s impliqu&eacute;s dans la d&eacute;marche a donc &eacute;t&eacute; atteint. Ce ratio est significatif de la participation du personnel, gage de r&eacute;ussite de la d&eacute;marche.");
			}
			else
			{
				$alerte = __("La tranche des 75% des salari&eacute;s &eacute;valu&eacute;s n'a pas &eacute;t&eacute; atteinte, puisque seul " . $pourcentageParticipant . "% de ces derniers ont &eacute;t&eacute;s impliqu&eacute;s, et la participation du personnel n'est donc pas suffisament significative.");
			}
		}

		{	/*	R&eacute;vision du document unique, en fonction de l'element et de la date de g&eacute;n&eacute;ration	*/
			$revision = '';
			$query = 
				"SELECT max(revisionDUER) AS lastRevision
				FROM " . TABLE_DUER . " 
				WHERE element = '" . mysql_escape_string($tableElement) . "' 
					AND elementId = '" . mysql_escape_string($idElement) . "' ";
					// AND dateGenerationDUER = '" . mysql_escape_string($informationDocumentUnique['dateCreation']) . "' ";
			$revision = $wpdb->get_row($query);
			$revisionDocumentUnique = $revision->lastRevision + 1;
		}

		{	/*	G&eacute;n&eacute;ration de la r&eacute;f&eacute;rence du document unique	*/
			switch($tableElement)
			{
				case TABLE_GROUPEMENT:
					$element = 'gpt';
				break;
				case TABLE_UNITE_TRAVAIL:
					$element = 'ut';
				break;
				default:
					$element = $tableElement;
				break;
			}
			$referenceDUER = substr($informationDocumentUnique['emetteur'], 0, 1) . str_replace('-', '', $informationDocumentUnique['dateCreation']) . '-' . $element . $idElement . '-V' . $revisionDocumentUnique;
		}

		{	/*	G&eacute;n&eacute;ration du nom du document unique si aucun nom n'a &eacute;t&eacute; envoy&eacute;	*/
			if($informationDocumentUnique['nomDuDocument'] == '')
			{
				$dateElement = explode(' ', $informationDocumentUnique['dateCreation']);

				$documentName = str_replace('-', '', $dateElement[0]) . '_documentUnique_' . eva_tools::slugify_noaccent(str_replace(' ', '_', $informationDocumentUnique['nomEntreprise']));

				$informationDocumentUnique['nomDuDocument'] = $documentName;
			}
		}

		{	/*	Génération de l'adresse du groupement	*/
			$gpmt = EvaGroupement::getGroupement($idElement);
			$groupementAdressComponent = new EvaBaseAddress($gpmt->id_adresse);
			$groupementAdressComponent->load();
			$groupementAdress = $groupementAdressComponent->getFirstLine() . " " . $groupementAdressComponent->getSecondLine() . " " . $groupementAdressComponent->getPostalCode() . " " . $groupementAdressComponent->getCity() ;
		}

		{	/*	Enregistrement d'un document unique	*/
			$query = 
				"INSERT INTO " . TABLE_DUER . " 
					(id, element, elementId, id_model, referenceDUER, dateGenerationDUER, nomDUER, dateDebutAudit, dateFinAudit, nomSociete, telephoneFixe, telephonePortable, telephoneFax, emetteurDUER, destinataireDUER, revisionDUER, planDUER, groupesUtilisateurs, groupesUtilisateursAffectes, risquesUnitaires, risquesParUnite, methodologieDUER, sourcesDUER, alerteDUER, conclusionDUER) 
				VALUES	
					('', '" . mysql_escape_string($tableElement) . "', '" . mysql_escape_string($idElement) . "', '" . mysql_escape_string($informationDocumentUnique['id_model']) . "', '" . mysql_escape_string($referenceDUER) . "', '" . mysql_escape_string($informationDocumentUnique['dateCreation']) . "', '" . mysql_escape_string($informationDocumentUnique['nomDuDocument']) . "', '" . mysql_escape_string($informationDocumentUnique['dateDebutAudit']) . "', '" . mysql_escape_string($informationDocumentUnique['dateFinAudit']) . "', '" . mysql_escape_string($informationDocumentUnique['nomEntreprise']) . "', '" . mysql_escape_string($informationDocumentUnique['telephoneFixe']) . "', '" . mysql_escape_string($informationDocumentUnique['telephonePortable']) . "', '" . mysql_escape_string($informationDocumentUnique['numeroFax']) . "', '" . mysql_escape_string($informationDocumentUnique['emetteur']) . "', '" . mysql_escape_string($informationDocumentUnique['destinataire']) . "', '" . mysql_escape_string($revisionDocumentUnique) . "', '" . mysql_real_escape_string($groupementAdress) . "', '" . mysql_escape_string(serialize(evaUserGroup::getUserGroup())) . "', '" . mysql_escape_string(serialize(eva_documentUnique::listeGroupeUtilisateurAffectes($tableElement, $idElement))) . "', '" . mysql_escape_string(serialize(eva_documentUnique::bilanRisque($tableElement, $idElement, 'ligne', 'print'))) . "', '" . mysql_escape_string(serialize(eva_documentUnique::bilanParUnite($tableElement, $idElement))) . "', '" . ($informationDocumentUnique['methodologie']) . "', '" . ($informationDocumentUnique['sources']) . "', '" . mysql_escape_string($alerte) . "', '')";
			if($wpdb->query($query) === false)
			{
				$status['result'] = 'error'; 
				$status['errors']['query_error'] = __('Une erreur est survenue lors de l\'enregistrement', 'evarisk');
			}
			else
			{
				$status['result'] = 'ok';
				/*	Save the odt file	*/
				eva_gestionDoc::generateSummaryDocument($tableElement, $idElement, 'odt');
				// eva_documentUnique::generateSummaryDocument($tableElement, $idElement, 'odt');
			}
		}

		return $status;
	}


	/**
	*	Get the last "document unique" for a given element
	*
	*	@param mixed $tableElement The element type we want to get the last document for
	*	@param integer $idElement The element identifier we want to get the lat document for
	*
	*	@return mixed $lastDocumentUnique An object with all information about the last document
	*/
	function getDernierDocumentUnique($tableElement, $idElement, $id = '')
	{
		global $wpdb;
		$lastDocumentUnique = array();

		$query = 
			"SELECT * 
			FROM " . TABLE_DUER . "
			WHERE element = '" . mysql_escape_string($tableElement) . "'
				AND elementId = '" . mysql_escape_string($idElement) . "'";
		if($id != '')
		{
		$query .= "AND id = '" . mysql_escape_string($id) . "'";
		}
		$query .= "ORDER BY id DESC
			LIMIT 1";
		$lastDocumentUnique = $wpdb->get_row($query);

		return $lastDocumentUnique;
	}


	/**
	* Get the list of document that have been generated for a given element
	*
	*	@param mixed $tableElement The element type we want to get the list for
	*	@param integer $idElement The element identifier we want to get the list for
	*
	*	@return mixed $outputListeDocumentUnique An html code with the list or a message if no document were generated
	*/
	function getDUERList($tableElement, $idElement)
	{
		global $wpdb;
		$isteDocumentUnique = array();
		$outputListeDocumentUnique = '';

		$query = 
			"SELECT *, DATE_FORMAT(dateGenerationDUER, '%Y/%m/%d') AS DateGeneration
			FROM " . TABLE_DUER . "
			WHERE element = '" . mysql_escape_string($tableElement) . "'
				AND elementId = '" . mysql_escape_string($idElement) . "'
			ORDER BY dateGenerationDUER DESC, revisionDUER DESC";
		$listeDocumentUnique = $wpdb->get_results($query);

		if( count($listeDocumentUnique) > 0 )
		{
			$listeParDate = array();
			foreach($listeDocumentUnique as $index => $documentUnique)
			{
				if($documentUnique->nomDUER == '')
				{
					$dateElement = explode(' ', $documentUnique->dateGenerationDUER);

					$documentName = str_replace('-', '', $dateElement[0]) . '_documentUnique_' . eva_tools::slugify_noaccent(str_replace(' ', '_', $documentUnique->nomSociete)) . '_V' . $documentUnique->revisionDUER;

					$documentUnique->nomDUER = $documentName;
				}
				$listeParDate[$documentUnique->DateGeneration][$documentUnique->id]['name'] = $documentUnique->nomDUER;
				$listeParDate[$documentUnique->DateGeneration][$documentUnique->id]['fileName'] = $documentUnique->nomDUER . '_V' . $documentUnique->revisionDUER;
				$listeParDate[$documentUnique->DateGeneration][$documentUnique->id]['revision'] = 'V' . $documentUnique->revisionDUER;
			}

			if( count($listeParDate) > 0 )
			{
				$outputListeDocumentUnique .= 
					'<table summary="" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;" >
						<thead></thead>
						<tfoot></tfoot>
						<tbody>';
				foreach($listeParDate as $date => $listeDUDate)
				{
					$outputListeDocumentUnique .= '
							<tr>
								<td colspan="3" style="text-decoration:underline;font-weight:bold;" >Le ' . $date . '</td>
							</tr>';
					foreach($listeDUDate as $index => $DUER)
					{
						$outputListeDocumentUnique .= '
							<tr>
								<td>&nbsp;&nbsp;&nbsp;- ' . $DUER['name'] . '_' . $DUER['revision'] . '</td>
								<!-- <td><a href="' . EVA_INC_PLUGIN_URL . 'modules/evaluationDesRisques/documentUnique.php?idElement=' . $idElement . '&table=' . $tableElement . '&id=' . $index . '" target="evaDUERHtml" >Html</a></td> -->';

						/*	Check if an odt file exist to be downloaded	*/
						$odtFile = 'documentUnique/' . $tableElement . '/' . $idElement . '/' . $DUER['fileName'] . '.odt';
						if( is_file(EVA_RESULTATS_PLUGIN_DIR . $odtFile) )
						{
						$outputListeDocumentUnique .= '
							<td><a href="' . EVA_RESULTATS_PLUGIN_URL . $odtFile . '" target="evaDUEROdt" >Odt</a></td>';
						}

						$outputListeDocumentUnique .= '
							</tr>';
					}
				}
				$outputListeDocumentUnique .= '
							<tr>
								<td style="padding:18px;" ><a href="' . LINK_TO_DOWNLOAD_OPEN_OFFICE . '" target="OOffice" >' . __('T&eacute;l&eacute;charger Open Office', 'evarisk') . '</a></td>
							</tr>
						</tbody>
					</table>';
			}
		}
		else
		{
			$outputListeDocumentUnique = __('Aucun document unique n\'a &eacute;t&eacute; g&eacute;n&eacute;r&eacute; pour le moment', 'evarisk');
		}

		return $outputListeDocumentUnique;
	}

}