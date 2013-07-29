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
class eva_documentUnique {

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
	function listRisk($tableElement, $idElement, $outputInterfaceType = '', $recursiv_mode = true) {
		$lignesDeValeurs = array();

		if($recursiv_mode == 'true'){
			switch($tableElement){
				case TABLE_GROUPEMENT:{
					/*	Recuperation des unites du groupement	*/
					$listeUnitesDeTravail = EvaGroupement::getUnitesEtGroupementDescendants($idElement);
					if(is_array($listeUnitesDeTravail)){
						foreach($listeUnitesDeTravail as $key => $uniteDefinition){
							/*	Recuperation des risques associes a l'unite	*/
							$lignesDeValeurs = array_merge($lignesDeValeurs, eva_documentUnique::listeRisquePourElement($uniteDefinition['table'], $uniteDefinition['value']->id, $outputInterfaceType));
						}
					}
				}
				break;
			}
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
	function listeRisquePourElement($tableElement, $idElement, $outputInterfaceType = '') {
		global $wpdb;
		$lignesDeValeurs = array();

		/*	Get the risk list for the given element	*/
		$temp = Risque::getRisques($tableElement, $idElement, "Valid");
		/*	If there are risks we store in a more simple array for future reading	*/
		if($temp != null){
			foreach($temp as $risque){
				$risques['"' . $risque->id . "'"][] = $risque;
			}
		}

		/*	If there are risks we read them	*/
		if(!empty($risques)){
			$i = 0;
			unset($tmpLigneDeValeurs);
			foreach ($risques as $risque) {
				$idMethode = $risque[0]->id_methode;
				$score = Risque::getScoreRisque($risque);
				$quotation = Risque::getEquivalenceEtalon($idMethode, $score, $risque[0]->date);
				$niveauSeuil = Risque::getSeuil($quotation);
				$elementPrefix = '';

				switch ($tableElement) {/*	Define the prefix for the current element looking on the type	*/
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
				$tmpLigneDeValeurs[$quotation][$i][] = array('value' => ELEMENT_IDENTIFIER_R . $risque[0]->id . ' - ' . ELEMENT_IDENTIFIER_E . $risque[0]->id_evaluation, 'class' => '');
				$tmpLigneDeValeurs[$quotation][$i][] = array('value' => $quotation, 'class' => 'Seuil_' . $niveauSeuil);
				$tmpLigneDeValeurs[$quotation][$i][] = array('value' => $risque[0]->nomDanger, 'class' => '');

				if ( empty($outputInterfaceType) || ($outputInterfaceType == 'export_risk_summary') ) {/*	If we want a "simple" output	*/
					$query = $wpdb->prepare("SELECT GROUP_CONCAT(id_evaluation) as risk_eval_list FROM " . TABLE_AVOIR_VALEUR . " WHERE id_risque = %d GROUP BY id_risque", $risque[0]->id);
					$risk_eval_list = $wpdb->get_var($query);
					$query = $wpdb->prepare(
						"SELECT *
						FROM " . TABLE_ACTIVITE_SUIVI . "
						WHERE id_element IN (" . $risk_eval_list . ")
							AND table_element = '%s'
							AND status = 'valid'
						ORDER BY date DESC",
						TABLE_AVOIR_VALEUR
					);
					$risk_comment_list = $wpdb->get_results($query);
					$risk_comment_export = '';
					if ( !empty($risk_comment_list) ) {
						foreach ( $risk_comment_list as $risk_comment ) {
							$risk_comment_export .= str_replace('<br />', "
", mysql2date('d-m-Y H:i', $risk_comment->date_ajout, true ) . ' - ' . $risk_comment->commentaire) . "

";
						}
					}
					$tmpLigneDeValeurs[$quotation][$i][] = array('value' => $risk_comment_export, 'class' => '');
				}

				if ( !empty($outputInterfaceType) ) {/*	If the output must have specific content	*/
					/*	Prioritary action	*/
					$contenuInput = '';
					$preconisationActionID = 0;
					if (!empty($risque[0])) {/*		If there si a risk so we add the correctiv action	*/
						$tache = EvaTask::getPriorityTask(TABLE_RISQUE, $risque[0]->id);
						if (!empty($tache) && (count($tache)==1)) {
							$tache = new EvaTask($tache[0]->id);
							if($tache->id > 0){
								$tache->load();
								$contenuInput = $tache->description;
								$preconisationActionID = $tache->id;
							}
						}
					}

					if ($outputInterfaceType == 'massUpdater') {/*	In case we are on the mass updater interface	*/
						/*	Add the risq comment input	*/
						$follow_up_list = suivi_activite::getSuiviActivite(TABLE_AVOIR_VALEUR, $risque[0]->id_evaluation);
						$follow_up_content = '';
						if ( !empty($follow_up_list) ) {
							foreach ( $follow_up_list as $follow_up ) {
								$follow_up_content .= suivi_activite::formulaireAjoutSuivi(TABLE_AVOIR_VALEUR, $risque[0]->id_evaluation, false, $follow_up->id, "risqComment[" . $risque[0]->id . "][" . $follow_up->id . "]", 'inline');
							}
						}
						else {
							$follow_up_content .= suivi_activite::formulaireAjoutSuivi(TABLE_AVOIR_VALEUR, $risque[0]->id_evaluation, false, 0, "risqComment[" . $risque[0]->id . "][0]", 'inline');
						}
						$tmpLigneDeValeurs[$quotation][$i][] = array('value' => $follow_up_content, 'class' => '');

						/*	Add the prioritary action input	*/
						if($contenuInput != '')
							$tmpLigneDeValeurs[$quotation][$i][] = array('value' => '<textarea class="risqPrioritaryCA" id="risqPrioritaryCA_' . $preconisationActionID . '" name="risqPrioritaryCA[' . $risque[0]->id . '][' . $preconisationActionID . ']" >' . $contenuInput . '</textarea>', 'class' => '');
						else
							$tmpLigneDeValeurs[$quotation][$i][] = array('value' => __('Aucune action pr&eacute;vue', 'evarisk'), 'class' => '');

						/*	Add the checkbox to define if this entry must be updated or not	*/
						$tmpLigneDeValeurs[$quotation][$i][] = array('value' => '<input type="checkbox" id="checkboxRisqMassUpdater_' . $risque[0]->id . '" name="checkboxRisqMassUpdater[]" value="' . $risque[0]->id . '" class="checkboxRisqMassUpdater" /><input type="hidden" id="prioritaryActionMassUpdater_' . $risque[0]->id . '" value="' . $preconisationActionID . '" />', 'class' => '');
					}
					else if ( ($outputInterfaceType == 'exportActionPlan') || ($outputInterfaceType == 'export_risk_summary') ) {/*	In case we are creating a new DUER	*/
						$query = $wpdb->prepare("SELECT TASK.id FROM ".TABLE_TACHE." AS TASK WHERE TASK.tableProvenance=%s AND idProvenance=%d", TABLE_RISQUE, $risque[0]->id);
						$associated_task_list = $wpdb->get_results($query);
						$associated_task_to_export = '';
						foreach ( $associated_task_list as $task ) {
							$racine = Arborescence::getRacine(TABLE_TACHE, " id='" . $task->id . "' ");

							$task_export_content='';
							if ( $racine->nom_exportable_plan_action == 'yes' ) {
								$task_export_content .= $racine->nom;

								if ( $racine->description_exportable_plan_action == 'yes' ) {
									$task_export_content .= ' : ' . $racine->description.' ';
								}

								$follow_up_list = suivi_activite::getSuiviActivite(TABLE_TACHE, $task->id);
								if ( !empty($follow_up_list) ) {
									$follow_up_content = '';
									foreach ( $follow_up_list as $follow_up ) {
										if ($follow_up->export == 'yes') {
										$follow_up_content .= str_replace('<br />', "
", mysql2date('d-m-Y H:i', $follow_up->date_ajout, true ) . ' - ' . $follow_up->commentaire) . "

";
										}
									}
									if ( !empty($follow_up_content) ) {
										$task_export_content .= "
- " . __('Commentaires', 'evarisk') . " -
" . $follow_up_content;
									}
								}
							}

							if ( !empty( $task_export_content ) ) {
								$associated_task_to_export .= actionsCorrectives::check_progression_status_for_output($racine->ProgressionStatus) . ' - '. (!empty($racine->avancement) ? $racine->avancement : 0) . '% - ' . ELEMENT_IDENTIFIER_T.$racine->id.' - '.$task_export_content.'
';
							}

							$elements = Arborescence::getFils(TABLE_TACHE, $racine, "nom ASC");
							$subcontent = self::output_correctiv_action_tree($elements, $racine, TABLE_TACHE);
							$associated_task_to_export .= (!empty($subcontent)?$subcontent:'');
						}
						$tmpLigneDeValeurs[$quotation][$i][] = array('value' => $associated_task_to_export, 'class' => '');
						$tmpLigneDeValeurs[$quotation][$i][] = array('value' => $contenuInput, 'class' => '');
					}
				}

				if ( empty($outputInterfaceType) || ($outputInterfaceType == 'export_risk_summary') ) {
					$pictureAssociated = evaPhoto::getPhotos(TABLE_RISQUE, $risque[0]->id);
					if ( !empty($pictureAssociated) ) {
						$tmpLigneDeValeurs[$quotation][$i][] = array('value' => $pictureAssociated[0]->photo, 'class' => '');
					}
					else {
						$tmpLigneDeValeurs[$quotation][$i][] = array('value' => 'noPicture', 'class' => '');
					}

					$methode_details = '';
					$methode_info = MethodeEvaluation::getMethod($risque[0]->id_methode);
					$listeVariables = MethodeEvaluation::getVariablesMethode($risque[0]->id_methode, $risque[0]->date);
					unset($listeIdVariables);
					$listeIdVariables = array();
					foreach ($listeVariables as $ordre => $variable) {
						$listeIdVariables['"' . $variable->id . '"'][]=$ordre;
					}
					unset($listeValeurs);
					foreach ($risque as $ligneRisque) {
						$date_to_take = $risque[0]->date;
						if (!empty($listeIdVariables) && !empty($listeIdVariables['"' . $ligneRisque->id_variable . '"']) && is_array($listeIdVariables['"' . $ligneRisque->id_variable . '"'])) {
							foreach ($listeIdVariables['"' . $ligneRisque->id_variable . '"'] as $ordre) {
								$var_infos = eva_Variable::getVariable($ligneRisque->id_variable);
								$listeValeurs[$ordre] = '<br />' . ELEMENT_IDENTIFIER_V . $ligneRisque->id_variable . ' - ' . $var_infos->nom . ' : ' . Eva_variable::getValeurAlternative($ligneRisque->id_variable, $ligneRisque->valeur, $date_to_take);
							}
						}
					}
					if (!empty($listeValeurs) && is_array($listeValeurs)) {
						ksort($listeValeurs);
						foreach ($listeValeurs as $val) {
							$methode_details .= $val;
						}
					}

					$tmpLigneDeValeurs[$quotation][$i][] = array('value' => ELEMENT_IDENTIFIER_ME . $risque[0]->id_methode . ' - ' . $methode_info->nom . $methode_details, 'class' => '');
				}

				$i++;
			}

			krsort( $tmpLigneDeValeurs );
			foreach ( $tmpLigneDeValeurs as $quotationLigneDeValeur => $contenuLigneDeValeur ) {
				foreach($contenuLigneDeValeur as $ligneDeValeur){
					$lignesDeValeurs[] = $ligneDeValeur;
				}
			}
		}

		return $lignesDeValeurs;
	}

	/**
	 *
	 */
	function output_correctiv_action_tree($elementsFils, $elementPere, $table, $output_type = '', $separator = '') {
		global $wpdb;
		$monCorpsTable = $monCorpsSubElements = $output = '';

		if ($output_type == 'unaffected_task') {
			$monCorpsTable = $monCorpsSubElements = $output = array();
		}

		$tacheMere = new EvaTask($elementPere->id);
		$tacheMere->load();
		$subElements = $tacheMere->getWPDBActivitiesDependOn();

		foreach($subElements as $subElement){
			$content='';

			if ( $subElement->nom_exportable_plan_action == 'yes' ) {
				$content.=$subElement->nom;

				if ( $subElement->description_exportable_plan_action == 'yes' ) {
					$content .= ' : ' . $subElement->description.' ';
				}

				$follow_up_list = suivi_activite::getSuiviActivite(TABLE_ACTIVITE, $subElement->id);
				if ( !empty($follow_up_list) ) {
					$follow_up_content = '';
					foreach ( $follow_up_list as $follow_up ) {
						if ($follow_up->export == 'yes') {
							$follow_up_content .= str_replace('<br />', "
", mysql2date('d-m-Y H:i', $follow_up->date_ajout, true ) . ' - ' . $follow_up->commentaire) . "

";
						}
					}
					if ( !empty($follow_up_content) ) {
						$content .= "
- " . __('Commentaires', 'evarisk') . " -
" . $follow_up_content;
					}
				}

			}

			if ( !empty( $content ) ) {
				if ( empty( $output_type ) ) {
					$monCorpsSubElements .= actionsCorrectives::check_progression_status_for_output($subElement->ProgressionStatus) . ' ('. (!empty($subElement->avancement) ? $subElement->avancement : 0) . '%) ' . DIGI_TASK_SEP.' '.DIGI_SUBTASK_SEP.' '.ELEMENT_IDENTIFIER_ST.$subElement->id.' - '.$content.'
';
				}
				elseif ( $output_type == 'unaffected_task' ) {
					$monCorpsSubElements[ELEMENT_IDENTIFIER_ST.$subElement->id]['idAction'] = $separator . ' '.DIGI_SUBTASK_SEP.' '.ELEMENT_IDENTIFIER_ST.$subElement->id;
					$monCorpsSubElements[ELEMENT_IDENTIFIER_ST.$subElement->id]['etatAction'] = actionsCorrectives::check_progression_status_for_output($subElement->ProgressionStatus) . ' ('. (!empty($subElement->avancement) ? $subElement->avancement : 0) . '%)';
					$monCorpsSubElements[ELEMENT_IDENTIFIER_ST.$subElement->id]['nomAction'] = $subElement->nom;
					$monCorpsSubElements[ELEMENT_IDENTIFIER_ST.$subElement->id]['descriptionAction'] = $subElement->description;
					$follow_up_list = suivi_activite::getSuiviActivite(TABLE_ACTIVITE, $subElement->id);
					if ( !empty($follow_up_list) ) {
						$follow_up_content = '';
						foreach ( $follow_up_list as $follow_up ) {
							if ($follow_up->export == 'yes') {
								$follow_up_content .= str_replace('<br />', "
", mysql2date('d-m-Y H:i', $follow_up->date_ajout, true ) . ' - ' . $follow_up->commentaire) . "

";
							}
						}
						if ( !empty($follow_up_content) ) {
							$monCorpsSubElements[ELEMENT_IDENTIFIER_ST.$subElement->id]['descriptionAction'] .= "
- " . __('Commentaires', 'evarisk') . " -
" . $follow_up_content;
						}
					}
					$monCorpsSubElements[ELEMENT_IDENTIFIER_ST.$subElement->id]['ajoutAction'] = mysql2date('d F Y', $subElement->firstInsert, true);
					$responsable_infos = evaUser::getUserInformation($subElement->idResponsable);
					$monCorpsSubElements[ELEMENT_IDENTIFIER_ST.$subElement->id]['responsableAction'] = (($subElement->idResponsable>0) ? ELEMENT_IDENTIFIER_U.$subElement->idResponsable.' - '.$responsable_infos['user_lastname'].' '.$responsable_infos['user_firstname'] : __('Pas de responsable d&eacute;fini', 'evarisk'));
					$monCorpsSubElements[ELEMENT_IDENTIFIER_ST.$subElement->id]['affectationAction'] = __('Aucune affectation pour cette t&acirc;che', 'evarisk');
				}
			}
		}

		if ( !empty( $elementsFils ) ) {
			foreach($elementsFils as $element){
				$elements_fils = '';
				$elements_fils = Arborescence::getFils($table, $element, "nom ASC");
				$elements_pere = Arborescence::getPere($table, $element, " Status = 'Deleted' ");

				if ( empty( $elements_pere ) && ( $element->tableProvenance != TABLE_RISQUE ) ) {
					$tache = new EvaTask($element->id);
					$tache->load();
					$subElements = $tache->getWPDBActivitiesDependOn();
					$trouveElement = count($elements_fils) + count($subElements);
					$content='';

					$exportable_element = false;
					if ( $element->nom_exportable_plan_action == 'yes' ) {
						$exportable_element = true;
						$content.=$element->nom;

						if ( $element->description_exportable_plan_action == 'yes' ) {
							$content .= ' : ' . $element->description.' ';
						}

						$follow_up_list = suivi_activite::getSuiviActivite(TABLE_TACHE, $element->id);
						if ( !empty($follow_up_list) ) {
							$follow_up_content = '';
							foreach ( $follow_up_list as $follow_up ) {
								if ($follow_up->export == 'yes') {
									$follow_up_content .= str_replace('<br />', "
", mysql2date('d-m-Y H:i', $follow_up->date_ajout, true ) . ' - ' . $follow_up->commentaire) . "

";
								}
							}
							if ( !empty($follow_up_content) ) {
								$content .= "
- " . __('Commentaires', 'evarisk') . " -
" . $follow_up_content;
							}
						}
					}

					if ( $exportable_element ) {
						if ( empty( $output_type ) ) {
							$monCorpsTable .= DIGI_TASK_SEP.' '.ELEMENT_IDENTIFIER_T.$element->id.' - '.$content.'
';
						}
						elseif ($output_type == 'unaffected_task') {
							$monCorpsTable[ELEMENT_IDENTIFIER_T.$element->id]['idAction'] = $separator . DIGI_TASK_SEP.' '.ELEMENT_IDENTIFIER_T.$element->id;
							$monCorpsTable[ELEMENT_IDENTIFIER_T.$element->id]['nomAction'] = $element->nom;
							$monCorpsTable[ELEMENT_IDENTIFIER_T.$element->id]['descriptionAction'] = $element->description;

							$follow_up_list = suivi_activite::getSuiviActivite(TABLE_TACHE, $element->id);
							if ( !empty($follow_up_list) ) {
								$follow_up_content = '';
								foreach ( $follow_up_list as $follow_up ) {
									if ($follow_up->export == 'yes') {
										$follow_up_content .= str_replace('<br />', "
", mysql2date('d-m-Y H:i', $follow_up->date_ajout, true ) . ' - ' . $follow_up->commentaire) . "

";
									}
								}
								if ( !empty($follow_up_content) ) {
									$monCorpsTable[ELEMENT_IDENTIFIER_T.$element->id]['descriptionAction'] .= "
- " . __('Commentaires', 'evarisk') . " -
" . $follow_up_content;
								}
							}

							$monCorpsTable[ELEMENT_IDENTIFIER_T.$element->id]['ajoutAction'] = mysql2date('d F Y', $element->firstInsert, true);
							$responsable_infos = evaUser::getUserInformation($element->idResponsable);
							$monCorpsTable[ELEMENT_IDENTIFIER_T.$element->id]['responsableAction'] = (($element->idResponsable>0) ? ELEMENT_IDENTIFIER_U.$element->idResponsable.' - '.$responsable_infos['user_lastname'].' '.$responsable_infos['user_firstname'] : __('Pas de responsable d&eacute;fini', 'evarisk'));
							$affectation = $wpdb->prepare("SELECT nom FROM ".$element->tableProvenance." WHERE id=%d", $element->idProvenance);
							switch ( $element->tableProvenance ) {
								case TABLE_GROUPEMENT:
									$element_identifier = ELEMENT_IDENTIFIER_GP;
								break;
								case TABLE_UNITE_TRAVAIL:
									$element_identifier = ELEMENT_IDENTIFIER_UT;
								break;
							}
							$monCorpsTable[ELEMENT_IDENTIFIER_T.$element->id]['affectationAction'] = (($element->idProvenance>0) ? $element_identifier.$element->idProvenance.' - '.$wpdb->get_var($affectation) : __('Aucune affectation pour cette t&acirc;che', 'evarisk') );
						}

						if ( $trouveElement ) {
							$subcontent = self::output_correctiv_action_tree($elements_fils, $element, $table, $output_type, $separator . DIGI_TASK_SEP);

							if ( empty( $output_type ) )
								$monCorpsTable .= (!empty($subcontent)?DIGI_TASK_SEP.' '.$subcontent:'');
							elseif ($output_type == 'unaffected_task') {
								$monCorpsTable = array_merge((array)$monCorpsTable, (array)$subcontent);
							}
						}
					}
				}
			}
		}

		if ( empty( $output_type ) ) {
			$output = $monCorpsTable . $monCorpsSubElements;
		}
		elseif ($output_type == 'unaffected_task') {
			$output = array_merge((array)$monCorpsTable, (array)$monCorpsSubElements);
		}

		return $output;
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
	function bilanRisque($tableElement, $idElement, $typeBilan = 'ligne', $outPut = 'html') {
		unset($titres, $classes, $idLignes, $lignesDeValeurs);

		if($tableElement == TABLE_GROUPEMENT){
			$lignesDeValeurs = eva_documentUnique::listRisk($tableElement, $idElement, '');

			if($outPut == 'html'){
				/**	Build output with datatable jquery plugin if requested result are by line	*/
				if ($typeBilan == 'ligne') {
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
					digirisk(document).ready(function() {
						digirisk("#' . $idTable . '").dataTable( {
							"bPaginate": false,
							"bLengthChange": false,
							"bAutoWidth": false,
							"bFilter": false,
							"bInfo": false,
							"aoColumns": [
								{ "bSortable": true},
								{ "bSortable": true},
								{ "bSortable": true, "sType": "numeric"},
								{ "bSortable": false},
								{ "bSortable": false},
							],
							"aaSorting": [[2,"desc"]]
						});
						digirisk("#' . $idTable . ' tfoot").remove();
					});
					</script>';

					$real_line_to_show = array();
					foreach ( $lignesDeValeurs as $line_key => $line_content ) {
						$i = 0;
						foreach ( $line_content as $line_content_details ) {
							if ( $i <= 4) {
								$real_line_to_show[$line_key][] = $line_content_details;
							}
							$i++;
						}
					}

					$recapitulatifRisque = EvaDisplayDesign::getTable($idTable, $titres, $real_line_to_show, $classes, $idLignes, $scriptVoirRisque);

					return $recapitulatifRisque;
				}
				/*	Si on veut le bilan par unit&eacute; de travail	*/
				elseif($typeBilan == 'unite'){
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
			else if ($outPut == 'massUpdater') {
				$lignesDeValeurs = eva_documentUnique::listRisk($tableElement, $idElement, $outPut);
				if($typeBilan == 'ligne') {
					$idLignes = array();
					foreach($lignesDeValeurs as $ligne_key => $ligne){
						$idLignes[$ligne_key] = $ligne[1]['value'];
					}

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
	digirisk(document).ready(function(){
		digirisk("#' . $idTable . '").dataTable(
		{
			"bPaginate": false,
			"bLengthChange": false,
			"bAutoWidth": false,
			"bFilter": false,
			"bInfo": false,
			"aaSorting": [[2,"desc"]]
		});
		digirisk("#' . $idTable . ' tfoot").remove();
	});
</script>';

					$recapitulatifRisque = EvaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $scriptVoirRisque);

					return $recapitulatifRisque;
				}
			}
			else
				return $lignesDeValeurs;
		}
	}

	/**
	*	Get a list of risqs and order the different element in an array
	*
	*	@param array $bilanALire An array with all risqs to read. This array is ordered
	*
	*	@return array $listeRisque An ordered array with all the risqs by line. Ordered by risq level
	*/
	function readBilanUnitaire( $bilanALire, $outputType = '' ) {
		$listeRisque = $listeRisque[SEUIL_BAS_FAIBLE] = $listeRisque[SEUIL_BAS_APLANIFIER] = $listeRisque[SEUIL_BAS_ATRAITER] = $listeRisque[SEUIL_BAS_INACCEPTABLE] = array();

		if( is_array($bilanALire) ){
			$indexQuotation = 2;
			foreach($bilanALire as $key => $informationsRisque){
				if($informationsRisque[$indexQuotation]['value'] >= SEUIL_BAS_INACCEPTABLE){
					$listeRisque[SEUIL_BAS_INACCEPTABLE][$informationsRisque[$indexQuotation]['value']][$key]['nomElement'] = $informationsRisque[0]['value'];
					$listeRisque[SEUIL_BAS_INACCEPTABLE][$informationsRisque[$indexQuotation]['value']][$key]['identifiantRisque'] = $informationsRisque[1]['value'];
					$listeRisque[SEUIL_BAS_INACCEPTABLE][$informationsRisque[$indexQuotation]['value']][$key]['quotationRisque'] = $informationsRisque[2]['value'];
					$listeRisque[SEUIL_BAS_INACCEPTABLE][$informationsRisque[$indexQuotation]['value']][$key]['nomDanger'] = $informationsRisque[3]['value'];
					if($outputType == 'plan_d_action')
						$listeRisque[SEUIL_BAS_INACCEPTABLE][$informationsRisque[$indexQuotation]['value']][$key]['actionPrevention'] = $informationsRisque[4]['value'];
					else if ( $outputType == 'risk_summary' ) {
						$listeRisque[SEUIL_BAS_INACCEPTABLE][$informationsRisque[$indexQuotation]['value']][$key]['commentaireRisque'] = $informationsRisque[4]['value'];
						$listeRisque[SEUIL_BAS_INACCEPTABLE][$informationsRisque[$indexQuotation]['value']][$key]['actionPrevention'] = $informationsRisque[5]['value'];
						$listeRisque[SEUIL_BAS_INACCEPTABLE][$informationsRisque[$indexQuotation]['value']][$key]['photoAssociee'] = $informationsRisque[7]['value'];
						$listeRisque[SEUIL_BAS_INACCEPTABLE][$informationsRisque[$indexQuotation]['value']][$key]['methodeElement'] = $informationsRisque[8]['value'];
					}
					else
						$listeRisque[SEUIL_BAS_INACCEPTABLE][$informationsRisque[$indexQuotation]['value']][$key]['commentaireRisque'] = $informationsRisque[4]['value'];
				}
				elseif(($informationsRisque[$indexQuotation]['value'] >= SEUIL_BAS_ATRAITER) && ($informationsRisque[$indexQuotation]['value'] <= SEUIL_HAUT_ATRAITER)){
					$listeRisque[SEUIL_BAS_ATRAITER][$informationsRisque[$indexQuotation]['value']][$key]['nomElement'] = $informationsRisque[0]['value'];
					$listeRisque[SEUIL_BAS_ATRAITER][$informationsRisque[$indexQuotation]['value']][$key]['identifiantRisque'] = $informationsRisque[1]['value'];
					$listeRisque[SEUIL_BAS_ATRAITER][$informationsRisque[$indexQuotation]['value']][$key]['quotationRisque'] = $informationsRisque[2]['value'];
					$listeRisque[SEUIL_BAS_ATRAITER][$informationsRisque[$indexQuotation]['value']][$key]['nomDanger'] = $informationsRisque[3]['value'];
					if($outputType == 'plan_d_action')
						$listeRisque[SEUIL_BAS_ATRAITER][$informationsRisque[$indexQuotation]['value']][$key]['actionPrevention'] = $informationsRisque[4]['value'];
					else if ( $outputType == 'risk_summary' ) {
						$listeRisque[SEUIL_BAS_ATRAITER][$informationsRisque[$indexQuotation]['value']][$key]['commentaireRisque'] = $informationsRisque[4]['value'];
						$listeRisque[SEUIL_BAS_ATRAITER][$informationsRisque[$indexQuotation]['value']][$key]['actionPrevention'] = $informationsRisque[5]['value'];
						$listeRisque[SEUIL_BAS_ATRAITER][$informationsRisque[$indexQuotation]['value']][$key]['photoAssociee'] = $informationsRisque[7]['value'];
						$listeRisque[SEUIL_BAS_ATRAITER][$informationsRisque[$indexQuotation]['value']][$key]['methodeElement'] = $informationsRisque[8]['value'];
					}
					else
						$listeRisque[SEUIL_BAS_ATRAITER][$informationsRisque[$indexQuotation]['value']][$key]['commentaireRisque'] = $informationsRisque[4]['value'];
				}
				elseif(($informationsRisque[$indexQuotation]['value'] >= SEUIL_BAS_APLANIFIER) && ($informationsRisque[$indexQuotation]['value'] <= SEUIL_HAUT_APLANIFIER)){
					$listeRisque[SEUIL_BAS_APLANIFIER][$informationsRisque[$indexQuotation]['value']][$key]['nomElement'] = $informationsRisque[0]['value'];
					$listeRisque[SEUIL_BAS_APLANIFIER][$informationsRisque[$indexQuotation]['value']][$key]['identifiantRisque'] = $informationsRisque[1]['value'];
					$listeRisque[SEUIL_BAS_APLANIFIER][$informationsRisque[$indexQuotation]['value']][$key]['quotationRisque'] = $informationsRisque[2]['value'];
					$listeRisque[SEUIL_BAS_APLANIFIER][$informationsRisque[$indexQuotation]['value']][$key]['nomDanger'] = $informationsRisque[3]['value'];
					if($outputType == 'plan_d_action')
						$listeRisque[SEUIL_BAS_APLANIFIER][$informationsRisque[$indexQuotation]['value']][$key]['actionPrevention'] = $informationsRisque[4]['value'];
					else if ( $outputType == 'risk_summary' ) {
						$listeRisque[SEUIL_BAS_APLANIFIER][$informationsRisque[$indexQuotation]['value']][$key]['commentaireRisque'] = $informationsRisque[4]['value'];
						$listeRisque[SEUIL_BAS_APLANIFIER][$informationsRisque[$indexQuotation]['value']][$key]['actionPrevention'] = $informationsRisque[5]['value'];
						$listeRisque[SEUIL_BAS_APLANIFIER][$informationsRisque[$indexQuotation]['value']][$key]['photoAssociee'] = $informationsRisque[5]['value'];
						$listeRisque[SEUIL_BAS_APLANIFIER][$informationsRisque[$indexQuotation]['value']][$key]['methodeElement'] = $informationsRisque[8]['value'];
					}
					else
						$listeRisque[SEUIL_BAS_APLANIFIER][$informationsRisque[$indexQuotation]['value']][$key]['commentaireRisque'] = $informationsRisque[4]['value'];
				}
				elseif(($informationsRisque[$indexQuotation]['value'] <= SEUIL_HAUT_FAIBLE)){
					$listeRisque[SEUIL_BAS_FAIBLE][$informationsRisque[$indexQuotation]['value']][$key]['nomElement'] = $informationsRisque[0]['value'];
					$listeRisque[SEUIL_BAS_FAIBLE][$informationsRisque[$indexQuotation]['value']][$key]['identifiantRisque'] = $informationsRisque[1]['value'];
					$listeRisque[SEUIL_BAS_FAIBLE][$informationsRisque[$indexQuotation]['value']][$key]['quotationRisque'] = $informationsRisque[2]['value'];
					$listeRisque[SEUIL_BAS_FAIBLE][$informationsRisque[$indexQuotation]['value']][$key]['nomDanger'] = $informationsRisque[3]['value'];
					if($outputType == 'plan_d_action')
						$listeRisque[SEUIL_BAS_FAIBLE][$informationsRisque[$indexQuotation]['value']][$key]['actionPrevention'] = $informationsRisque[4]['value'];
					else if ( $outputType == 'risk_summary' ) {
						$listeRisque[SEUIL_BAS_FAIBLE][$informationsRisque[$indexQuotation]['value']][$key]['commentaireRisque'] = $informationsRisque[4]['value'];
						$listeRisque[SEUIL_BAS_FAIBLE][$informationsRisque[$indexQuotation]['value']][$key]['actionPrevention'] = $informationsRisque[5]['value'];
						$listeRisque[SEUIL_BAS_FAIBLE][$informationsRisque[$indexQuotation]['value']][$key]['photoAssociee'] = $informationsRisque[7]['value'];
						$listeRisque[SEUIL_BAS_FAIBLE][$informationsRisque[$indexQuotation]['value']][$key]['methodeElement'] = $informationsRisque[8]['value'];
					}
					else
						$listeRisque[SEUIL_BAS_FAIBLE][$informationsRisque[$indexQuotation]['value']][$key]['commentaireRisque'] = $informationsRisque[4]['value'];
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
	function formulaireGenerationDocumentUnique($tableElement, $idElement){
		require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/documentUnique/templateDocumentUnique.tpl.php');

		unset($formulaireDocumentUniqueParams);
		$formulaireDocumentUniqueParams = array();
		$lastDocumentUnique = eva_documentUnique::getDernierDocumentUnique($tableElement, $idElement);
		$formulaireDocumentUniqueParams['#DATEFORM1#'] = date('Y-m-d');

		if($tableElement == TABLE_GROUPEMENT){
			$formulaireDocumentUniqueParams['#DATEDEBUT1#'] = date('Y-m-d');
			$formulaireDocumentUniqueParams['#DATEFIN1#'] = date('Y-m-d');
			$groupementInformations = Evagroupement::getGroupement($idElement);
			$formulaireDocumentUniqueParams['#NOMENTREPRISE#'] = $groupementInformations->nom;
			if(!empty($groupementInformations->nom) && !empty($lastDocumentUnique->nomSociete) && ($groupementInformations->nom != $lastDocumentUnique->nomSociete))
				$formulaireDocumentUniqueParams['#NOMENTREPRISE#'] = (isset($lastDocumentUnique->nomSociete) && ($lastDocumentUnique->nomSociete != '')) ? $lastDocumentUnique->nomSociete : '';
			$formulaireDocumentUniqueParams['#TELFIXE#'] = (isset($lastDocumentUnique->telephoneFixe) && ($lastDocumentUnique->telephoneFixe != '')) ? $lastDocumentUnique->telephoneFixe : '';
			$formulaireDocumentUniqueParams['#TELPORTABLE#'] = (isset($lastDocumentUnique->telephonePortable) && ($lastDocumentUnique->telephonePortable != '')) ? $lastDocumentUnique->telephonePortable : '';
			$formulaireDocumentUniqueParams['#TELFAX#'] = (isset($lastDocumentUnique->telephoneFax) && ($lastDocumentUnique->telephoneFax != '')) ? $lastDocumentUnique->telephoneFax : '';
			$formulaireDocumentUniqueParams['#EMETTEUR#'] = (isset($lastDocumentUnique->emetteurDUER) && ($lastDocumentUnique->emetteurDUER != '')) ? $lastDocumentUnique->emetteurDUER : '';
			$formulaireDocumentUniqueParams['#DESTINATAIRE#'] = (isset($lastDocumentUnique->destinataireDUER) && ($lastDocumentUnique->destinataireDUER != '')) ? $lastDocumentUnique->destinataireDUER : '';
			$formulaireDocumentUniqueParams['#NOMDOCUMENT#'] = date('Ymd') . '_documentUnique_' . ELEMENT_IDENTIFIER_GP . $idElement . '_' . digirisk_tools::slugify_noaccent(str_replace(' ', '_', $groupementInformations->nom));
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
			$formulaireDocumentUniqueParams['#REMARQUEIMPORTANT#'] = (isset($lastDocumentUnique->alerteDUER) && ($lastDocumentUnique->alerteDUER != '') && ($lastDocumentUnique->alerteDUER != 'undefined')) ? $lastDocumentUnique->alerteDUER : $alerte;

			if ( empty($lastDocumentUnique->id_model) && !empty($lastDocumentUnique) ) {
				$lastDocumentUnique->id_model = 0;
			}

			$output =
			EvaDisplayDesign::feedTemplate(EvaDisplayDesign::getFormulaireGenerationDUER(), $formulaireDocumentUniqueParams) . '
<script type="text/javascript" >
	digirisk(document).ready(function(){
		digirisk("#dateCreation").datepicker();
		digirisk("#dateCreation").datepicker("option", {dateFormat: "yy-mm-dd"});

		digirisk("#dateDebutAudit").datepicker();
		digirisk("#dateDebutAudit").datepicker("option", {dateFormat: "yy-mm-dd"});

		digirisk("#dateFinAudit").datepicker();
		digirisk("#dateFinAudit").datepicker("option", {dateFormat: "yy-mm-dd"});

		digirisk("#genererDUER").click(function(){
			digirisk("#divDocumentUnique").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
				"post":"true",
				"table":"' . TABLE_DUER . '",
				"act":"saveDocumentUnique",
				"tableElement":"' . $tableElement . '",
				"idElement":"' . $idElement . '",
				"dateCreation":digirisk("#dateCreation").val(),
				"dateDebutAudit":digirisk("#dateDebutAudit").val(),
				"dateFinAudit":digirisk("#dateFinAudit").val(),
				"nomEntreprise":digirisk("#nomEntreprise").val(),
				"telephoneFixe":digirisk("#telephoneFixe").val(),
				"telephonePortable":digirisk("#telephonePortable").val(),
				"numeroFax":digirisk("#numeroFax").val(),
				"emetteur":digirisk("#emetteur").val(),
				"destinataire":digirisk("#destinataire").val(),
				"nomDuDocument":digirisk("#nomDuDocument").val(),
				"methodologie":digirisk("#methodologie").val(),
				"id_model":digirisk("#modelToUse' . $tableElement . '").val(),
				"sources":digirisk("#sources").val(),
				"localisation":digirisk("#localisation").val(),
				"alerte":digirisk("#remarque_important").val()
			});
			digirisk("#divDocumentUnique").html(\'<img src="' . PICTO_LOADING . '" />\');
		});';

					if ( !empty($lastDocumentUnique->id_model) && ($lastDocumentUnique->id_model != eva_gestionDoc::getDefaultDocument('document_unique'))) {
						$output .= '
		setTimeout(function(){
			digirisk("#modelDefaut").click();
		},200);';
					}

					$output .= '
		digirisk("#ui-datepicker-div").hide();
		digirisk("#modelDefaut").click(function(){
			setTimeout(function(){
				if(!digirisk("#modelDefaut").is(":checked")){
					digirisk("#documentUniqueResultContainer").html(\'<img src="' . EVA_IMG_DIVERS_PLUGIN_URL . 'loading.gif" alt="loading" />\');
					digirisk("#documentUniqueResultContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_DUER . '", "act":"loadNewModelForm", "tableElement":"' . $tableElement . '", "idElement":"' . $idElement . '"});
					digirisk("#modelListForGeneration").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_GED_DOCUMENTS . '", "act":"load_model_combobox", "tableElement":"' . $tableElement . '", "idElement":"' . $idElement . '", "category":"document_unique", "selection":"' . (!empty($lastDocumentUnique->id_model) ? $lastDocumentUnique->id_model : '') . '"});
					digirisk("#modelListForGeneration").show();
				}
				else{
					digirisk("#documentUniqueResultContainer").html("");
					digirisk("#modelListForGeneration").html("");
					digirisk("#modelListForGeneration").hide();
				}
			},600);
		});
	});
</script>';
		}
		elseif($tableElement == TABLE_UNITE_TRAVAIL)
		{
			$workUnitinformations = eva_UniteDeTravail::getWorkingUnit($idElement);
			$formulaireDocumentUniqueParams['#NOMDOCUMENT#'] = date('Ymd') . '_ficheDePoste_' . digirisk_tools::slugify_noaccent(str_replace(' ', '_', $workUnitinformations->nom));

			$output = EvaDisplayDesign::feedTemplate(EvaDisplayDesign::getFormulaireGenerationFicheDePoste(), $formulaireDocumentUniqueParams) . '
<script type="text/javascript" >
	digirisk(document).ready(function(){
		digirisk("#genererFP").click(function(){
			digirisk("#divImpressionFicheDePoste").html(digirisk("#loadingImg").html());
			digirisk("#divImpressionFicheDePoste").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",
			{
				"post":"true",
				"table":"' . TABLE_DUER . '",
				"act":"saveFichePoste",
				"tableElement":"' . $tableElement . '",
				"idElement":"' . $idElement . '",
				"dateCreation":digirisk("#dateCreationFicheDePoste").val(),
				"nomDuDocument":digirisk("#nomFicheDePoste").val()
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

		$tableElement = digirisk_tools::IsValid_Variable($tableElement);
		$idElement = digirisk_tools::IsValid_Variable($idElement);

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

				$documentName = str_replace('-', '', $dateElement[0]) . '_documentUnique_' . digirisk_tools::slugify_noaccent(str_replace(' ', '_', $informationDocumentUnique['nomEntreprise']));

				$informationDocumentUnique['nomDuDocument'] = $documentName;
			}
		}

		$plan_d_action = array();
		$plan_d_action['affected'] = eva_documentUnique::listRisk($tableElement, $idElement, 'exportActionPlan');
		$plan_d_action['unaffected'] = actionsCorrectives::get_correctiv_action_for_duer();

		{	/*	Enregistrement d'un document unique	*/
			$query =
				"INSERT INTO " . TABLE_DUER . "
					(id, element, elementId, id_model, referenceDUER, dateGenerationDUER, nomDUER, dateDebutAudit, dateFinAudit, nomSociete, telephoneFixe, telephonePortable, telephoneFax, emetteurDUER, destinataireDUER, revisionDUER, planDUER, groupesUtilisateurs, groupesUtilisateursAffectes, risquesUnitaires, risquesParUnite, methodologieDUER, sourcesDUER, alerteDUER, conclusionDUER, plan_d_action)
				VALUES
					('', '" . mysql_escape_string($tableElement) . "', '" . mysql_escape_string($idElement) . "', '" . mysql_escape_string($informationDocumentUnique['id_model']) . "', '" . mysql_escape_string($referenceDUER) . "', '" . mysql_escape_string($informationDocumentUnique['dateCreation']) . "', '" . mysql_escape_string($informationDocumentUnique['nomDuDocument']) . "', '" . mysql_escape_string($informationDocumentUnique['dateDebutAudit']) . "', '" . mysql_escape_string($informationDocumentUnique['dateFinAudit']) . "', '" . mysql_escape_string($informationDocumentUnique['nomEntreprise']) . "', '" . mysql_escape_string($informationDocumentUnique['telephoneFixe']) . "', '" . mysql_escape_string($informationDocumentUnique['telephonePortable']) . "', '" . mysql_escape_string($informationDocumentUnique['numeroFax']) . "', '" . mysql_escape_string($informationDocumentUnique['emetteur']) . "', '" . mysql_escape_string($informationDocumentUnique['destinataire']) . "', '" . mysql_escape_string($revisionDocumentUnique) . "', '" . mysql_real_escape_string($informationDocumentUnique['localisation']) . "', '" . mysql_escape_string(serialize(digirisk_groups::getElement('', "'valid'", 'employee'))) . "', '" . mysql_escape_string(serialize(eva_documentUnique::exportData($tableElement, $idElement, 'affectedUserGroup'))) . "', '" . mysql_escape_string(serialize(eva_documentUnique::bilanRisque($tableElement, $idElement, 'ligne', 'print'))) . "', '" . mysql_escape_string(serialize(eva_documentUnique::exportData($tableElement, $idElement, 'riskByElement'))) . "', '" . $wpdb->escape($informationDocumentUnique['methodologie']) . "', '" . mysql_escape_string($informationDocumentUnique['sources']) . "', '" . mysql_escape_string($informationDocumentUnique['alerte']) . "', '', '" . mysql_escape_string(serialize($plan_d_action)) . "')";
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

					$documentName = str_replace('-', '', $dateElement[0]) . '_documentUnique_' . digirisk_tools::slugify_noaccent(str_replace(' ', '_', $documentUnique->nomSociete)) . '_V' . $documentUnique->revisionDUER;

					$documentUnique->nomDUER = $documentName;
				}
				$listeParDate[$documentUnique->DateGeneration][$documentUnique->id]['name'] = $documentUnique->nomDUER;
				$listeParDate[$documentUnique->DateGeneration][$documentUnique->id]['fileName'] = $documentUnique->nomDUER . '_V' . $documentUnique->revisionDUER;
				$listeParDate[$documentUnique->DateGeneration][$documentUnique->id]['revision'] = 'V' . $documentUnique->revisionDUER;
			}

			if( count($listeParDate) > 0 )
			{
				$outputListeDocumentUnique .=
'<table summary="" cellpadding="0" cellspacing="0" class="associated_document_list" >
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
			if(confirm(digi_html_accent_for_js("' . __('&Ecirc;tes vous s&ucirc;r de vouloir supprimer ce document unique?', 'evarisk') . '"))){
				jQuery("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
					"post":"true",
					"table":"' . TABLE_DUER . '",
					"act":"deleteDUER",
					"tableElement":"' . $tableElement . '",
					"idElement":"' . $idElement . '",
					"idDocument":jQuery(this).attr("id").replace("duerToDelete", "")
				});
			}
		});
	})(digirisk)
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
	function getBoxBilan($tableElement, $idElement) {

// 		<div class="alignleft" id="generateCSV" >' . __('Exporter un r&eacute;sum&eacute; en csv', 'evarisk') . '</div>

		$output = '
<div class="clear" id="summaryDocumentGeneratorSlector" >
	<div class="alignleft selected" id="generateDUER" >' . __('Document unique', 'evarisk') . '</div>
	<div class="alignleft" id="generateFGP" >' . __('Fiches de groupement', 'evarisk') . '</div>
	<div class="alignleft" id="generateFP" >' . __('Fiches de poste', 'evarisk') . '</div>
	<div class="alignleft" id="generateRS" >' . __('Synth&egrave;se des risques', 'evarisk') . '</div>
	<div class="alignleft" id="generateFEP" >' . __('Fiches de p&eacute;nibilit&eacute;', 'evarisk') . '</div>
</div>
<div class="clear" id="bilanBoxContainer" >' . eva_documentUnique::formulaireGenerationDocumentUnique($tableElement, $idElement) . '</div>
<script type="text/javascript" >
		digirisk("#generateDUER").click(function(){
			digirisk("#summaryDocumentGeneratorSlector div").each(function(){
				digirisk(this).removeClass("selected");
			});
			digirisk(this).addClass("selected");
			digirisk("#bilanBoxContainer").html(digirisk("#loadingImg").html());
			digirisk("#bilanBoxContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
				"post":"true",
				"table":"' . TABLE_DUER . '",
				"act":"documentUniqueGenerationForm",
				"tableElement":"' . $tableElement . '",
				"idElement":"' . $idElement . '"
			});
		});
		digirisk("#generateFGP").click(function(){
			digirisk("#summaryDocumentGeneratorSlector div").each(function(){
				digirisk(this).removeClass("selected");
			});
			digirisk(this).addClass("selected");
			digirisk("#bilanBoxContainer").html(digirisk("#loadingImg").html());
			digirisk("#bilanBoxContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
				"post":"true",
				"table":"' . TABLE_DUER . '",
				"act":"groupementSheetGeneration",
				"tableElement":"' . $tableElement . '",
				"idElement":"' . $idElement . '"
			});
		});
		digirisk("#generateFP").click(function(){
			digirisk("#summaryDocumentGeneratorSlector div").each(function(){
				digirisk(this).removeClass("selected");
			});
			digirisk(this).addClass("selected");
			digirisk("#bilanBoxContainer").html(digirisk("#loadingImg").html());
			digirisk("#bilanBoxContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
				"post":"true",
				"table":"' . TABLE_DUER . '",
				"act":"workSheetUnitCollectionGenerationForm",
				"tableElement":"' . $tableElement . '",
				"idElement":"' . $idElement . '"
			});
		});
		digirisk("#generateRS").click(function(){
			digirisk("#summaryDocumentGeneratorSlector div").each(function(){
				digirisk(this).removeClass("selected");
			});
			digirisk(this).addClass("selected");
			digirisk("#bilanBoxContainer").html(digirisk("#loadingImg").html());
			digirisk("#bilanBoxContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
				"post":"true",
				"table":"' . TABLE_DUER . '",
				"act":"riskListingGeneration",
				"tableElement":"' . $tableElement . '",
				"idElement":"' . $idElement . '"
			});
		});
		digirisk("#generateFEP").click(function(){
			digirisk("#summaryDocumentGeneratorSlector div").each(function(){
				digirisk(this).removeClass("selected");
			});
			digirisk(this).addClass("selected");
			digirisk("#bilanBoxContainer").html(digirisk("#loadingImg").html());
			digirisk("#bilanBoxContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
				"post":"true",
				"table":"' . TABLE_DUER . '",
				"act":"ficheDePenibiliteGeneration",
				"tableElement":"' . $tableElement . '",
				"idElement":"' . $idElement . '"
			});
		});
		digirisk("#generateCSV").click(function(){
			digirisk("#summaryDocumentGeneratorSlector div").each(function(){
				digirisk(this).removeClass("selected");
			});
			digirisk(this).addClass("selected");
			digirisk("#bilanBoxContainer").html(digirisk("#loadingImg").html());
			digirisk("#bilanBoxContainer").load("' . admin_url('admin-ajax.php') . '", {
				"action": "digi_ajax_load_field_for_export",
				"export_type": "tree_element",
				"tableElement":"' . $tableElement . '",
				"idElement":"' . $idElement . '",
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