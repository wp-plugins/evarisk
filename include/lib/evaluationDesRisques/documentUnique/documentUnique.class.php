<?php
/**
* Plugin "document unique" manager
* 
*	Define the different method to manage the "document unique" into the plugin
* @author Evarisk <dev@evarisk.com>
* @version 5.0
* @package Digirisk
* @subpackage librairies
*/


/**
* Define the different method to manage the "document unique" into the plugin
* @package Digirisk
* @subpackage librairies
*/
class eva_documentUnique
{
	/**
	*	
	*
	*	@see listeRisquePourElement
	*	@param string $tableElement The type of the element we want to get the risk list for
	*	@param integer $idElement The id of the element we want to get the risk list for
	*	@param string $outputInterfaceType The id of the element we want to get the risk list for
	*
	*	@return array $lignesDeValeurs A complete array with the entire list of risk stored by element
	*/
	function listRisk($tableElement, $idElement, $outputInterfaceType = '')
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
						$lignesDeValeurs = array_merge($lignesDeValeurs, eva_documentUnique::listeRisquePourElement($uniteDefinition['table'], $uniteDefinition['value']->id, $outputInterfaceType));
					}
				}
			break;
		}

		/*	Recuperation des risques associes au groupement	*/
		$lignesDeValeurs = array_merge($lignesDeValeurs, eva_documentUnique::listeRisquePourElement($tableElement, $idElement, $outputInterfaceType));

		return $lignesDeValeurs;
	}
	/**
	*	Get the different risqs for an element and its descendant
	*
	*	@param mixed $tableElement The element type we want to get the risqs for
	*	@param integer $idElement The element identifier we want to get the risqs for
	*	@param string $idElement optionnal The type of the interface output. Introduce to manage the mass updater interface with a minimum of changes
	*
	*	@return array $lignesDeValeurs The different risqs ordered by element
	*/
	function listeRisquePourElement($tableElement, $idElement, $outputInterfaceType = '')
	{
		$lignesDeValeurs = array();

		/*	Get the risk list for the given element	*/
		$temp = Risque::getRisques($tableElement, $idElement, "Valid");
		/*	If there are risks we store in a more simple array for future reading	*/
		if($temp != null)
		{
			foreach($temp as $risque)
			{
				$risques['"' . $risque->id . "'"][] = $risque; 
			}
		}

		/*	If there are risks we read them	*/
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
				$elementPrefix = '';

				switch($tableElement)
				{/*	Define the prefix for the current element looking on the type	*/
					case TABLE_GROUPEMENT:
						$element = EvaGroupement::getGroupement($idElement);
						$elementPrefix = ELEMENT_IDENTIFIER_GP . $idElement . ' - ';
						break;
					case TABLE_UNITE_TRAVAIL:
						$element = eva_UniteDeTravail::getWorkingUnit($idElement);
						$elementPrefix = ELEMENT_IDENTIFIER_UT . $idElement . ' - ';
						break;
				}

				/*	Build the output array we the result	*/
				$tmpLigneDeValeurs[$quotation][$i][] = array('value' => $elementPrefix . $element->nom, 'class' => '');
				$tmpLigneDeValeurs[$quotation][$i][] = array('value' => ELEMENT_IDENTIFIER_R . $risque[0]->id, 'class' => '');
				$tmpLigneDeValeurs[$quotation][$i][] = array('value' => $quotation, 'class' => 'Seuil_' . $niveauSeuil);
				$tmpLigneDeValeurs[$quotation][$i][] = array('value' => $risque[0]->nomDanger, 'class' => '');
				if($outputInterfaceType == '')
				{/*	If we want a "simple" output	*/
					$tmpLigneDeValeurs[$quotation][$i][] = array('value' => $risque[0]->commentaire, 'class' => '');
				}
				else
				{/*	If the output must have specific content	*/
					{/*	Prioritary action	*/
						$contenuInput = '';
						$preconisationActionID = 0;
						if($risque[0] != null)
						{/*		If there si a risk so we add the correctiv action	*/
							$tache = EvaTask::getPriorityTask(TABLE_RISQUE, $risque[0]->id);
							$tache = new EvaTask($tache->id);
							if($tache->id > 0)
							{
								$tache->load();
								$contenuInput = $tache->description;
								$preconisationActionID = $tache->id;
							}
						}
					}

					if($outputInterfaceType == 'massUpdater')
					{/*	In case we are on the mass updater interface	*/
						/*	Add the risq comment input	*/
						$tmpLigneDeValeurs[$quotation][$i][] = array('value' => '<textarea class="risqComment" id="risqComment_' . $risque[0]->id . '" name="risqComment_' . $risque[0]->id . '" >' . $risque[0]->commentaire . '</textarea>', 'class' => '');

						/*	Add the prioritary action input	*/
						if($contenuInput != '')
						{
							$tmpLigneDeValeurs[$quotation][$i][] = array('value' => '<textarea class="risqPrioritaryCA" id="risqPrioritaryCA_' . $preconisationActionID . '" name="risqPrioritaryCA_' . $risque[0]->id . '" >' . $contenuInput . '</textarea>', 'class' => '');
						}
						else
						{
							$tmpLigneDeValeurs[$quotation][$i][] = array('value' => __('Aucune action pr&eacute;vue', 'evarisk'), 'class' => '');
						}

						/*	Add the checkbox to define if this entry must be updated or not	*/
						$tmpLigneDeValeurs[$quotation][$i][] = array('value' => '<input type="checkbox" id="checkboxRisqMassUpdater_' . $risque[0]->id . '" name="checkboxRisqMassUpdater_' . $risque[0]->id . '" value="" class="checkboxRisqMassUpdater" /><input type="hidden" id="prioritaryActionMassUpdater_' . $risque[0]->id . '" value="' . $preconisationActionID . '" />', 'class' => '');
					}
					elseif($outputInterfaceType == 'exportActionPlan')
					{/*	In case we are creating a new DUER	*/
						$tmpLigneDeValeurs[$quotation][$i][] = array('value' => '', 'class' => '');
						$tmpLigneDeValeurs[$quotation][$i][] = array('value' => $contenuInput, 'class' => '');
					}
				}
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
			$lignesDeValeurs = eva_documentUnique::listRisk($tableElement, $idElement, '');

			if($outPut == 'html')
			{
				/*	Si on veut le bilan par ligne	*/
				if($typeBilan == 'ligne')
				{
					{//Cr&eacute;ation de la table	
						{//Script de d&eacute;finition de la dataTable pour la somme des risques par ligne
							$idTable = 'tableBilanRisqueUnitaire' . $tableElement . $idElement . $typeBilan;
							$titres[] = __("&Eacute;l&eacute;ment", 'evarisk');
							$titres[] = __("Id. risque", 'evarisk');
							$titres[] = __("Quotation", 'evarisk');
							$titres[] = ucfirst(strtolower(sprintf(__("nom %s", 'evarisk'), __("du danger", 'evarisk'))));
							$titres[] = ucfirst(strtolower(sprintf(__("commentaire %s", 'evarisk'), __("sur le risque", 'evarisk'))));
							$classes[] = 'columnQuotation';
							$classes[] = 'columnRId';
							$classes[] = 'columnQuotation';
							$classes[] = 'columnNomDanger';
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
									{ "bSortable": true},
									{ "bSortable": false, "sType": "numeric"},
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
					$bilanParUnite = eva_documentUnique::exportData($tableElement, $idElement, 'riskByElement');

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
							' . eva_documentUnique::readExportedDatas($bilanParUnite, 'riskByElement', '', 'html') . '
						</tbody>
					</table>';

					return $recapitulatifRisque;
				}
			}
			elseif($outPut == 'massUpdater')
			{
				$lignesDeValeurs = eva_documentUnique::listRisk($tableElement, $idElement, $outPut);
				if($typeBilan == 'ligne')
				{
					$idTable = 'tableBilanEvaluation' . $tableElement . $idElement . $outPut . $typeBilan;
					$titres[] = __("&Eacute;l&eacute;ment", 'evarisk');
					$titres[] = __("Id. risque", 'evarisk');
					$titres[] = __("Quotation", 'evarisk');
					$titres[] = ucfirst(strtolower(__("danger", 'evarisk')));
					$titres[] = ucfirst(strtolower(sprintf(__("commentaire %s", 'evarisk'), __("sur le risque", 'evarisk'))));
					$titres[] = ucfirst(strtolower(sprintf(__("action prioritaire %s", 'evarisk'), __("pour le risque", 'evarisk'))));
					$titres[] = '';
					$classes[] = 'columnNomElementMassUpdater';
					$classes[] = 'columnRIdMassUpdater';
					$classes[] = 'columnQuotationMassUpdater';
					$classes[] = 'columnNomDangerMassUpdater';
					$classes[] = 'columnCommentaireRisqueMassUpdater';
					$classes[] = 'columnActionPrioritaireRisqueMassUpdater';
					$classes[] = 'columnCBRisqueMassUpdater';

					$scriptVoirRisque = '
<script type="text/javascript">
	evarisk(document).ready(function(){
		evarisk("#' . $idTable . '").dataTable(
		{
			"bPaginate": false, 
			"bLengthChange": false,
			"bAutoWidth": false,
			"bFilter": false,
			"bInfo": false,
			"aaSorting": [[2,"desc"]]
		});
		evarisk("#' . $idTable . ' tfoot").remove();
	});
</script>';

					$recapitulatifRisque = EvaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $scriptVoirRisque);

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
	*	Get a list of risqs and order the different element in an array
	*
	*	@param array $bilanALire An array with all risqs to read. This array is ordered
	*
	*	@return array $listeRisque An ordered array with all the risqs by line. Ordered by risq level
	*/
	function readBilanUnitaire($bilanALire, $outputType = '')
	{
		$listeRisque = $listeRisque[SEUIL_BAS_FAIBLE] = $listeRisque[SEUIL_BAS_APLANIFIER] = $listeRisque[SEUIL_BAS_ATRAITER] = $listeRisque[SEUIL_BAS_INACCEPTABLE] = array();

		$listeTousRisques = $bilanALire;

		if( is_array($listeTousRisques) )
		{
			$indexQuotation = 2;
			foreach($listeTousRisques as $key => $informationsRisque)
			{
				if($informationsRisque[$indexQuotation]['value'] >= SEUIL_BAS_INACCEPTABLE)
				{
					$listeRisque[SEUIL_BAS_INACCEPTABLE][$informationsRisque[$indexQuotation]['value']][$key]['nomElement'] = $informationsRisque[0]['value'];
					$listeRisque[SEUIL_BAS_INACCEPTABLE][$informationsRisque[$indexQuotation]['value']][$key]['identifiantRisque'] = $informationsRisque[1]['value'];
					$listeRisque[SEUIL_BAS_INACCEPTABLE][$informationsRisque[$indexQuotation]['value']][$key]['quotationRisque'] = $informationsRisque[2]['value'];
					$listeRisque[SEUIL_BAS_INACCEPTABLE][$informationsRisque[$indexQuotation]['value']][$key]['nomDanger'] = $informationsRisque[3]['value'];
					if($outputType == 'plan_d_action')
					{
						// $listeRisque[SEUIL_BAS_INACCEPTABLE][$informationsRisque[$indexQuotation]['value']][$key]['preventionExistante'] = $informationsRisque[3]['value'];
						$listeRisque[SEUIL_BAS_INACCEPTABLE][$informationsRisque[$indexQuotation]['value']][$key]['actionPrevention'] = $informationsRisque[4]['value'];
					}
					else
					{
						$listeRisque[SEUIL_BAS_INACCEPTABLE][$informationsRisque[$indexQuotation]['value']][$key]['commentaireRisque'] = $informationsRisque[4]['value'];
					}
				}
				elseif(($informationsRisque[$indexQuotation]['value'] >= SEUIL_BAS_ATRAITER) && ($informationsRisque[$indexQuotation]['value'] <= SEUIL_HAUT_ATRAITER))
				{
					$listeRisque[SEUIL_BAS_ATRAITER][$informationsRisque[$indexQuotation]['value']][$key]['nomElement'] = $informationsRisque[0]['value'];
					$listeRisque[SEUIL_BAS_ATRAITER][$informationsRisque[$indexQuotation]['value']][$key]['identifiantRisque'] = $informationsRisque[1]['value'];
					$listeRisque[SEUIL_BAS_ATRAITER][$informationsRisque[$indexQuotation]['value']][$key]['quotationRisque'] = $informationsRisque[2]['value'];
					$listeRisque[SEUIL_BAS_ATRAITER][$informationsRisque[$indexQuotation]['value']][$key]['nomDanger'] = $informationsRisque[3]['value'];
					if($outputType == 'plan_d_action')
					{
						// $listeRisque[SEUIL_BAS_ATRAITER][$informationsRisque[$indexQuotation]['value']][$key]['preventionExistante'] = $informationsRisque[3]['value'];
						$listeRisque[SEUIL_BAS_ATRAITER][$informationsRisque[$indexQuotation]['value']][$key]['actionPrevention'] = $informationsRisque[4]['value'];
					}
					else
					{
						$listeRisque[SEUIL_BAS_ATRAITER][$informationsRisque[$indexQuotation]['value']][$key]['commentaireRisque'] = $informationsRisque[4]['value'];
					}
				}
				elseif(($informationsRisque[$indexQuotation]['value'] >= SEUIL_BAS_APLANIFIER) && ($informationsRisque[$indexQuotation]['value'] <= SEUIL_HAUT_APLANIFIER))
				{
					$listeRisque[SEUIL_BAS_APLANIFIER][$informationsRisque[$indexQuotation]['value']][$key]['nomElement'] = $informationsRisque[0]['value'];
					$listeRisque[SEUIL_BAS_APLANIFIER][$informationsRisque[$indexQuotation]['value']][$key]['identifiantRisque'] = $informationsRisque[1]['value'];
					$listeRisque[SEUIL_BAS_APLANIFIER][$informationsRisque[$indexQuotation]['value']][$key]['quotationRisque'] = $informationsRisque[2]['value'];
					$listeRisque[SEUIL_BAS_APLANIFIER][$informationsRisque[$indexQuotation]['value']][$key]['nomDanger'] = $informationsRisque[3]['value'];
					if($outputType == 'plan_d_action')
					{
						// $listeRisque[SEUIL_BAS_APLANIFIER][$informationsRisque[$indexQuotation]['value']][$key]['preventionExistante'] = $informationsRisque[3]['value'];
						$listeRisque[SEUIL_BAS_APLANIFIER][$informationsRisque[$indexQuotation]['value']][$key]['actionPrevention'] = $informationsRisque[4]['value'];
					}
					else
					{
						$listeRisque[SEUIL_BAS_APLANIFIER][$informationsRisque[$indexQuotation]['value']][$key]['commentaireRisque'] = $informationsRisque[4]['value'];
					}
				}
				elseif(($informationsRisque[$indexQuotation]['value'] <= SEUIL_HAUT_FAIBLE))
				{
					$listeRisque[SEUIL_BAS_FAIBLE][$informationsRisque[$indexQuotation]['value']][$key]['nomElement'] = $informationsRisque[0]['value'];
					$listeRisque[SEUIL_BAS_FAIBLE][$informationsRisque[$indexQuotation]['value']][$key]['identifiantRisque'] = $informationsRisque[1]['value'];
					$listeRisque[SEUIL_BAS_FAIBLE][$informationsRisque[$indexQuotation]['value']][$key]['quotationRisque'] = $informationsRisque[2]['value'];
					$listeRisque[SEUIL_BAS_FAIBLE][$informationsRisque[$indexQuotation]['value']][$key]['nomDanger'] = $informationsRisque[3]['value'];
					if($outputType == 'plan_d_action')
					{
						// $listeRisque[SEUIL_BAS_FAIBLE][$informationsRisque[$indexQuotation]['value']][$key]['preventionExistante'] = $informationsRisque[3]['value'];
						$listeRisque[SEUIL_BAS_FAIBLE][$informationsRisque[$indexQuotation]['value']][$key]['actionPrevention'] = $informationsRisque[4]['value'];
					}
					else
					{
						$listeRisque[SEUIL_BAS_FAIBLE][$informationsRisque[$indexQuotation]['value']][$key]['commentaireRisque'] = $informationsRisque[4]['value'];
					}
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
			$formulaireDocumentUniqueParams['#NOMDOCUMENT#'] = date('Ymd') . '_documentUnique_' . ELEMENT_IDENTIFIER_GP . $idElement . '_' . eva_tools::slugify_noaccent(str_replace(' ', '_', $groupementInformations->nom));
			$formulaireDocumentUniqueParams['#METHODOLOGIE#'] = (isset($lastDocumentUnique->methodologieDUER) && ($lastDocumentUnique->methodologieDUER != '')) ? $lastDocumentUnique->methodologieDUER : ($methodologieParDefaut);

			$gpmt = EvaGroupement::getGroupement($idElement);
			$groupementAdressComponent = new EvaBaseAddress($gpmt->id_adresse);
			$groupementAdressComponent->load();
			if(($groupementAdressComponent->getFirstLine() != '') && ($groupementAdressComponent->getSecondLine() != '') && ($groupementAdressComponent->getPostalCode() != '') && ($groupementAdressComponent->getCity() != ''))
			{
				$groupementAdress = $groupementAdressComponent->getFirstLine() . " " . $groupementAdressComponent->getSecondLine() . " " . $groupementAdressComponent->getPostalCode() . " " . $groupementAdressComponent->getCity() ;
			}
			else
			{
				$groupementAdress = __('La localisation est indisponible', 'evarisk');
			}
			$formulaireDocumentUniqueParams['#LOCALISATION#'] = (isset($lastDocumentUnique->planDUER) && ($lastDocumentUnique->planDUER != '') && ($lastDocumentUnique->planDUER != 'undefined')) ? $lastDocumentUnique->planDUER : ($groupementAdress);

			$sourcesParDefaut = __("Le document de l'INRS ED 840 pour la sensibilisation aux risques, les pages 3 et 4 de ce document contenant : 
La d&eacute;finition d'un risque et d'un danger, un sch&eacute;ma d'explication
Les 5 crit&egrave;res d'&eacute;valuation qui constituerons la cotation du risque", 'evarisk');
			$formulaireDocumentUniqueParams['#SOURCES#'] = (isset($lastDocumentUnique->sourcesDUER) && ($lastDocumentUnique->sourcesDUER != '') && ($lastDocumentUnique->sourcesDUER != 'undefined')) ? $lastDocumentUnique->sourcesDUER : ($sourcesParDefaut);

			$pourcentageParticipant = 0;
			if(count(evaUser::getBindUsers($idElement, $tableElement)) > 0)
			{
				$pourcentageParticipant = ((count(evaUser::getBindUsers($idElement, $tableElement . '_evaluation')) * 100) / count(evaUser::getBindUsers($idElement, $tableElement)));
			}
			if($pourcentageParticipant >= '75')
			{
				$alerte = __("Le pr&eacute;sent document a &eacute;t&eacute; r&eacute;alis&eacute; pour permettre au chef d'entreprise d'avoir une vision des risques hi&eacute;rarchis&eacute;s dans son &eacute;tablissement. Lors de l'&eacute;valuation, " . $pourcentageParticipant . "% des salari&eacute;s de l'entreprise ont particip&eacute; &agrave; la d&eacute;marche d'&eacute;valuation des risques. Nous consid&eacute;rons que le quota des 75% des salari&eacute;s impliqu&eacute;s dans la d&eacute;marche a donc &eacute;t&eacute; atteint. Ce ratio est significatif de la participation du personnel, gage de r&eacute;ussite de la d&eacute;marche.");
			}
			else
			{
				$alerte = __("La tranche des 75% des salari&eacute;s &eacute;valu&eacute;s n'a pas &eacute;t&eacute; atteinte, puisque seul " . $pourcentageParticipant . "% de ces derniers ont &eacute;t&eacute;s impliqu&eacute;s, et la participation du personnel n'est donc pas suffisamment significative.");
			}
			$formulaireDocumentUniqueParams['#REMARQUEIMPORTANT#'] = ($alerte);

			$lastDocumentUnique->id_model = (isset($lastDocumentUnique->id_model) && ($lastDocumentUnique->id_model != '')) ? $lastDocumentUnique->id_model : 0;

			$output = 
			EvaDisplayDesign::feedTemplate(EvaDisplayDesign::getFormulaireGenerationDUER(), $formulaireDocumentUniqueParams) . '
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
				"id_model":evarisk("#modelToUse' . $tableElement . '").val(),
				"sources":evarisk("#sources").val(),
				"localisation":evarisk("#localisation").val(),
				"alerte":evarisk("#remarque_important").val()
			});
			evarisk("#divDocumentUnique").html(\'<img src="' . PICTO_LOADING . '" />\');
		});';

					if(($lastDocumentUnique->id_model != '') && ($lastDocumentUnique->id_model != '0') && ($lastDocumentUnique->id_model != eva_gestionDoc::getDefaultDocument('document_unique')))
					{
						$output .= '
		setTimeout(function(){
			evarisk("#modelDefaut").click();
		},200);';
					}

					$output .= '
		evarisk("#ui-datepicker-div").hide();
		evarisk("#modelDefaut").click(function(){
			setTimeout(function(){
				if(!evarisk("#modelDefaut").is(":checked"))
				{
					evarisk("#documentUniqueResultContainer").html(\'<img src="' . EVA_IMG_DIVERS_PLUGIN_URL . 'loading.gif" alt="loading" />\');
					evarisk("#documentUniqueResultContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_DUER . '", "act":"loadNewModelForm", "tableElement":"' . $tableElement . '", "idElement":"' . $idElement . '"});
					evarisk("#modelListForGeneration").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_GED_DOCUMENTS . '", "act":"loadDocument", "tableElement":"' . $tableElement . '", "idElement":"' . $idElement . '", "category":"document_unique", "selection":"' . $lastDocumentUnique->id_model . '"});
					evarisk("#modelListForGeneration").show();
				}
				else
				{
					evarisk("#documentUniqueResultContainer").html("");
					evarisk("#modelListForGeneration").html("");
					evarisk("#modelListForGeneration").hide();
				}
			},600);
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

		$tableElement = eva_tools::IsValid_Variable($tableElement);
		$idElement = eva_tools::IsValid_Variable($idElement);

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

		{	/*	Enregistrement d'un document unique	*/
			$query = 
				"INSERT INTO " . TABLE_DUER . " 
					(id, element, elementId, id_model, referenceDUER, dateGenerationDUER, nomDUER, dateDebutAudit, dateFinAudit, nomSociete, telephoneFixe, telephonePortable, telephoneFax, emetteurDUER, destinataireDUER, revisionDUER, planDUER, groupesUtilisateurs, groupesUtilisateursAffectes, risquesUnitaires, risquesParUnite, methodologieDUER, sourcesDUER, alerteDUER, conclusionDUER, plan_d_action) 
				VALUES	
					('', '" . mysql_escape_string($tableElement) . "', '" . mysql_escape_string($idElement) . "', '" . mysql_escape_string($informationDocumentUnique['id_model']) . "', '" . mysql_escape_string($referenceDUER) . "', '" . mysql_escape_string($informationDocumentUnique['dateCreation']) . "', '" . mysql_escape_string($informationDocumentUnique['nomDuDocument']) . "', '" . mysql_escape_string($informationDocumentUnique['dateDebutAudit']) . "', '" . mysql_escape_string($informationDocumentUnique['dateFinAudit']) . "', '" . mysql_escape_string($informationDocumentUnique['nomEntreprise']) . "', '" . mysql_escape_string($informationDocumentUnique['telephoneFixe']) . "', '" . mysql_escape_string($informationDocumentUnique['telephonePortable']) . "', '" . mysql_escape_string($informationDocumentUnique['numeroFax']) . "', '" . mysql_escape_string($informationDocumentUnique['emetteur']) . "', '" . mysql_escape_string($informationDocumentUnique['destinataire']) . "', '" . mysql_escape_string($revisionDocumentUnique) . "', '" . mysql_real_escape_string($informationDocumentUnique['localisation']) . "', '" . mysql_escape_string(serialize(digirisk_groups::getElement('', "'valid'", 'employee'))) . "', '" . mysql_escape_string(serialize(eva_documentUnique::exportData($tableElement, $idElement, 'affectedUserGroup'))) . "', '" . mysql_escape_string(serialize(eva_documentUnique::bilanRisque($tableElement, $idElement, 'ligne', 'print'))) . "', '" . mysql_escape_string(serialize(eva_documentUnique::exportData($tableElement, $idElement, 'riskByElement'))) . "', '" . $wpdb->escape($informationDocumentUnique['methodologie']) . "', '" . mysql_escape_string($informationDocumentUnique['sources']) . "', '" . mysql_escape_string($informationDocumentUnique['alerte']) . "', '', '" . mysql_escape_string(serialize(eva_documentUnique::listRisk($tableElement, $idElement, 'exportActionPlan'))) . "')";
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
				AND status = 'valid'
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
			<td>&nbsp;&nbsp;&nbsp;- (' . ELEMENT_IDENTIFIER_DU . $index . ')&nbsp;&nbsp;' . $DUER['name'] . '_' . $DUER['revision'] . '</td>
			<!-- <td><a href="' . EVA_INC_PLUGIN_URL . 'modules/evaluationDesRisques/documentUnique.php?idElement=' . $idElement . '&table=' . $tableElement . '&id=' . $index . '" target="evaDUERHtml" >Html</a></td> -->';

						/*	Check if an odt file exist to be downloaded	*/
						$odtFile = 'documentUnique/' . $tableElement . '/' . $idElement . '/' . $DUER['fileName'] . '.odt';
						if( is_file(EVA_RESULTATS_PLUGIN_DIR . $odtFile) )
						{
							$outputListeDocumentUnique .= '
			<td class="duerODTFileLink" ><a href="' . EVA_RESULTATS_PLUGIN_URL . $odtFile . '" target="evaDUEROdt" >Odt</a></td>';
						}
						else
						{/*	If an error occured during the first generation, and that no file has been generate we propose a new generationof the file 	*/
							$outputListeDocumentUnique .= '
			<td class="duerODTFileLink" id="reGenerateDUER' . $index . 'Container" ><input class="DUERReGenerationButton" type="button" name="reGenerateDUER' . $index . '" id="reGenerateDUER' . $index . '" value="' . __('Re-g&eacute;n&eacute;rer le fichier odt', 'evarisk') . '" /></td>';
						}

						$outputListeDocumentUnique .= '
			<td class="duerDeleteContainer" ><img id="duerToDelete' . $index . '" src="' . EVA_IMG_ICONES_PLUGIN_URL . 'delete_vs.png" alt="' . __('Supprimer ce document', 'evarisk') . '" title="' . __('Supprimer ce document', 'evarisk') . '" class="alignright deleteDUER" /></td>';

						$outputListeDocumentUnique .= '
		</tr>';
					}
				}
				$outputListeDocumentUnique .= '
	</tbody>
</table>
<script type="text/javascript" >
	(function(){
		/*	If an error occured during the first generation, and that no file has been generate we propose a new generationof the file 	*/
		jQuery(".DUERReGenerationButton").click(function(){
			jQuery("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
				"post":"true", 
				"table":"' . TABLE_DUER . '",
				"act":"reGenerateDUER",
				"tableElement":"' . $tableElement . '",
				"idElement":"' . $idElement . '",
				"idDocument":jQuery(this).attr("id").replace("reGenerateDUER", "")
			});
		});

		/*	In case that the user click on the duer deletion button	*/
		jQuery(".deleteDUER").click(function(){
			jQuery("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
				"post":"true", 
				"table":"' . TABLE_DUER . '",
				"act":"deleteDUER",
				"tableElement":"' . $tableElement . '",
				"idElement":"' . $idElement . '",
				"idDocument":jQuery(this).attr("id").replace("duerToDelete", "")
			});
		});
	})(evarisk)
</script>';
			}
		}
		else
		{
			$outputListeDocumentUnique = __('Aucun document unique n\'a &eacute;t&eacute; g&eacute;n&eacute;r&eacute; pour le moment', 'evarisk');
		}

		return $outputListeDocumentUnique;
	}

	/**
	* Generate an html output for the box to generate the "documet unique" and the work unit sheet
	*
	*	@param mixed $tableElement The element type we are working on
	*	@param integer $idElement The element identifier we are working on
	*
	*	@return mixed $output An html code with the generated output
	*/
	function getBoxBilan($tableElement, $idElement)
	{
		$output = '
<div class="clear" id="summaryDocumentGeneratorSlector" >
	<div class="alignleft selected" id="generateDUER" >' . __('Document unique', 'evarisk') . '</div>
	<div class="alignleft" id="generateFGP" >' . __('Fiches de groupement', 'evarisk') . '</div>
	<div class="alignleft" id="generateFP" >' . __('Fiches de poste', 'evarisk') . '</div>
</div>
<div class="clear" id="bilanBoxContainer" >' . eva_documentUnique::formulaireGenerationDocumentUnique($tableElement, $idElement) . '</div>
<script type="text/javascript" >
		evarisk("#generateDUER").click(function(){
			evarisk("#summaryDocumentGeneratorSlector div").each(function(){
				evarisk(this).removeClass("selected");
			});
			evarisk(this).addClass("selected");
			evarisk("#bilanBoxContainer").html(evarisk("#loadingImg").html());
			evarisk("#bilanBoxContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
				"post":"true", 
				"table":"' . TABLE_DUER . '",
				"act":"documentUniqueGenerationForm",
				"tableElement":"' . $tableElement . '",
				"idElement":"' . $idElement . '"
			});
		});
		evarisk("#generateFGP").click(function(){
			evarisk("#summaryDocumentGeneratorSlector div").each(function(){
				evarisk(this).removeClass("selected");
			});
			evarisk(this).addClass("selected");
			evarisk("#bilanBoxContainer").html(evarisk("#loadingImg").html());
			evarisk("#bilanBoxContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
				"post":"true", 
				"table":"' . TABLE_DUER . '",
				"act":"groupementSheetGeneration",
				"tableElement":"' . $tableElement . '",
				"idElement":"' . $idElement . '"
			});
		});
		evarisk("#generateFP").click(function(){
			evarisk("#summaryDocumentGeneratorSlector div").each(function(){
				evarisk(this).removeClass("selected");
			});
			evarisk(this).addClass("selected");
			evarisk("#bilanBoxContainer").html(evarisk("#loadingImg").html());
			evarisk("#bilanBoxContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
				"post":"true", 
				"table":"' . TABLE_DUER . '",
				"act":"workSheetUnitCollectionGenerationForm",
				"tableElement":"' . $tableElement . '",
				"idElement":"' . $idElement . '"
			});
		});
</script>';

		return $output;
	}

	/**
	*	Export a tree from an element with all it's children and datas for each element
	*
	*	@param string $tableElement The type of the element we want to get the datas for
	*	@param integer $idElement The identifier of the element we want to get tha datas for
	*	@param string $dataTypeToExport The type of the data we want to get. Allows to configur the output content
	*
	*	@return array $completeExport The complete tree with the datas stored by element
	*/
	function exportData($tableElement, $idElement, $dataTypeToExport)
	{
		$completeExport = Arborescence::completeTree($tableElement, $idElement);
		if( is_array($completeExport) )
		{
			foreach($completeExport as $key => $content)
			{
				if( isset($content['nom']) )
				{
					/*	Check the data we want to have	*/
					switch($dataTypeToExport)
					{
						case 'plan_d_action':
							// $datas = digirisk_groups::getBindGroupsWithInformations($idElement, $tableElement . '_employee');
						break;
						case 'affectedUserGroup':
							$datas = digirisk_groups::getBindGroupsWithInformations($idElement, $tableElement . '_employee');
						break;
						case 'riskByElement':
							$risqForElement = Risque::getSommeRisque($tableElement, $idElement);
							$datas = $risqForElement['value'];
						break;
						default:
							$datas = 'nodata';
						break;
					}
					/*	Add	the data to the output result	*/
					$completeExport[$key]['dataContent'] = $datas;
				}

				if(isset($content['content']) && is_array($content['content']))
				{
					foreach($content['content'] as $index => $subContent)
					{
						if( isset($subContent['table']) && isset($subContent['id']) )
						{
							$completeExport[$key]['content'][$index] = eva_documentUnique::exportData($subContent['table'], $subContent['id'], $dataTypeToExport);
						}
						else
						{
							foreach($subContent as $subContentIndex => $subContentContent)
							{
								/*	Check the data we want to have	*/
								switch($dataTypeToExport)
								{
									case 'plan_d_action':
										// $datas = digirisk_groups::getBindGroupsWithInformations($idElement, $tableElement . '_employee');
									break;
									case 'affectedUserGroup':
										$datas = digirisk_groups::getBindGroupsWithInformations($subContentContent['id'], $subContentContent['table'] . '_employee');
									break;
									case 'riskByElement':
										$risqForElement = Risque::getSommeRisque($subContentContent['table'], $subContentContent['id']);
										$datas = $risqForElement['value'];
									break;
									default:
										$datas = 'nodata';
									break;
								}
								/*	Add	the data to the output result	*/
								$completeExport[$key]['content'][$index][$subContentIndex]['dataContent'] = $datas;
							}
						}
					}
				}
			}
		}

		return $completeExport;
	}
	/**
	*	Build output for a complete tree with associated values
	*
	*	@param array $export A complete true with the different content to read for outputing
	*	@param string $exportDataType The type of output to define the output content name
	*	@param mixed $spacer Allows to reproduce the tree by putting a specific number of spaces before each element
	*	@param mixed $outputType Specify wich output type is asked (html, print)
	*	@param integer $i The main tree index
	*
	*	@return mixed $outputContent Depending on the "outputType" parameter, output an array or an html code
	*/
	function readExportedDatas($export, $exportDataType, $spacer = '', $outputType = 'html', $i = 0)
	{
		/*	Check for the output type to prepare the type of the returned vontent	*/
		if($outputType == 'html')
		{
			$outputContent = '';
		}
		elseif($outputType == 'print')
		{
			$outputContent = array();
		}

		if( is_array($export) )
		{
			foreach($export as $key => $content)
			{
				if( isset($content['nom']) )
				{
					switch($content['table'])
					{/*	Check the element type to add a prefix	*/
						case TABLE_GROUPEMENT:
							$elementPrefix = 'GP' . $content['id'] . ' - ';
							break;
						case TABLE_UNITE_TRAVAIL:
							$elementPrefix = 'UT' . $content['id'] . ' - ';
							break;
						default:
							$elementPrefix = '';
						break;
					}
					switch($exportDataType)
					{/*	Check for the needed output type	*/
						case 'affectedUserGroup':
							$elementGroup = $spacer . '  ';
							if( is_array($content['dataContent']) )
							{
								foreach($content['dataContent'] as $groupeId => $groupDefinition)
								{
									$elementGroup .= $groupDefinition['name'] . ', ';
								}
							}
							$dataContent = substr($elementGroup, 0, -2);
						break;
						case 'riskByElement':
							$dataContent = $content['dataContent'];
						break;
					}

					if($outputType == 'html')
					{/*	If we have to output into html	*/
						$outputContent .= 
							'<tr>
								<td>' . $spacer . $elementPrefix . $content['nom'] . '</td>
								<td>' . $dataContent . '</td>
							</tr>';
					}
					elseif($outputType == 'print')
					{/*	If we have to print the content	*/
						$outputContent[$i]['nomElement'] = str_replace('&nbsp;', "-", $spacer) . $elementPrefix . $content['nom'];
						switch($exportDataType)
						{/*	Check for the needed output type	*/
							case 'affectedUserGroup':
								$outputContent[$i]['listeGroupes'] = substr(str_replace('&nbsp;', "-", $elementGroup), 0, -2);
							break;
							case 'riskByElement':
								$outputContent[$i]['quotationTotale'] = $content['dataContent'];
							break;
						}
					}
				}
				else
				{
					$sum = 0;
					foreach($content as $contentKey => $contentInformations)
					{
						switch($contentInformations['table'])
						{/*	Check the element type to add a prefix	*/
							case TABLE_GROUPEMENT:
								$elementPrefix = 'GP' . $contentInformations['id'] . ' - ';
								break;
							case TABLE_UNITE_TRAVAIL:
								$elementPrefix = 'UT' . $contentInformations['id'] . ' - ';
								break;
							default:
								$elementPrefix = '';
							break;
						}

						switch($exportDataType)
						{/*	Check for the needed output type	*/
							case 'affectedUserGroup':
								$elementGroup = $spacer . '  ';
								if( is_array($contentInformations['dataContent']) )
								{
									foreach($contentInformations['dataContent'] as $groupeId => $groupDefinition)
									{
										$elementGroup .= $groupDefinition['name'] . ', ';
									}
								}
								$dataContent = substr($elementGroup, 0, -2);
							break;
							case 'riskByElement':
								$dataContent = $contentInformations['dataContent'];
							break;
						}

						if($outputType == 'html')
						{/*	If we have to output into html	*/
							$outputContent .= 
								'<tr>
									<td>' . $spacer . $elementPrefix . $contentInformations['nom'] . '</td>
									<td>' . $dataContent . '</td>
								</tr>';
						}
						elseif($outputType == 'print')
						{/*	If we have to print the content	*/

							$outputContent[$i]['nomElement'] = str_replace('&nbsp;', "-", $spacer) . $elementPrefix . $contentInformations['nom'];
							switch($exportDataType)
							{/*	Check for the needed output type	*/
								case 'affectedUserGroup':
									$outputContent[$i]['listeGroupes'] = substr(str_replace('&nbsp;', "-", $elementGroup), 0, -2);
								break;
								case 'riskByElement':
									$outputContent[$i]['quotationTotale'] = $contentInformations['dataContent'];
								break;
							}
						}
						$i++;

						if(isset($contentInformations['content']) && is_array($contentInformations['content']))
						{
							$subSpacer = $spacer . '&nbsp;&nbsp;'; 
							if($outputType == 'html')
							{
								$outputContent .= eva_documentUnique::readExportedDatas($contentInformations['content'], $exportDataType, $subSpacer, $outputType);
							}
							else
							{
								$sousLignes = eva_documentUnique::readExportedDatas($contentInformations['content'], $exportDataType, $subSpacer, $outputType, $i);
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
					$subSpacer = $spacer . '&nbsp;&nbsp;'; 
					if($outputType == 'html')
					{
						$outputContent .= eva_documentUnique::readExportedDatas($content['content'], $exportDataType, $subSpacer, $outputType);
					}
					else
					{
						$sousLignes = eva_documentUnique::readExportedDatas($content['content'], $exportDataType, $subSpacer, $outputType, $i);
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

}