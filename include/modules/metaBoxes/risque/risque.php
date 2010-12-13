<?php
	require_once(EVA_CONFIG);
	require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayDesign.class.php' );
	require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayInput.class.php' );
	require_once(EVA_LIB_PLUGIN_DIR . 'danger/categorieDangers/categorieDangers.class.php' );
	require_once(EVA_LIB_PLUGIN_DIR . 'danger/danger/evaDanger.class.php' );
	require_once(EVA_LIB_PLUGIN_DIR . 'methode/methodeEvaluation.class.php' );
	require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/documentUnique/documentUnique.class.php'); 
	require_once(EVA_LIB_PLUGIN_DIR . 'risque/Risque.class.php');
	require_once(EVA_LIB_PLUGIN_DIR . 'photo/evaPhoto.class.php');


	if(current_user_can('Evarisk_:_voir_les_risques'))
	{
		$postBoxTitle = __('Risques', 'evarisk');
		$postBoxId = 'postBoxRisques';
		$postBoxCallbackFunction = 'getRisquesPostBoxBody';
		add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_UNITES_DE_TRAVAIL, 'rightSide', 'default');
		add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_GROUPEMENTS, 'rightSide', 'default');
	}

	function getRisquesPostBoxBody($element)
	{
		$tableElement = $element['tableElement'];
		$idElement = $element['idElement'];
		if($idElement != null)
		{
			$scriptRisque = '<script type="text/javascript">
					$(document).ready(function(){
						$("#ongletVoirLesRisques").click(function(){
							$("#divFormRisque").hide();
							$("#divDemandeAction' . TABLE_RISQUE . '").hide();
							$("#divSuiviAction' . TABLE_RISQUE . '").hide();
							$("#divFicheAction' . TABLE_RISQUE . '").hide();
							$("#ongletAjouterRisque").removeClass("selected_tab");
							$("#ongletDemandeActionCorrective' . TABLE_RISQUE . '").removeClass("selected_tab");
							$("#ongletSuiviActionCorrective' . TABLE_RISQUE . '").removeClass("selected_tab");
							$("#ongletFicheActionCorrective' . TABLE_RISQUE . '").removeClass("selected_tab");
							$("#divVoirRisques").show();
							$("#ongletVoirLesRisques").addClass("selected_tab");
							$("#divVoirRisques").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_RISQUE . '", "act":"reloadVoirRisque", "tableElement":"' . $tableElement . '","idElement":' . $idElement . '});
							$("#ongletDemandeActionCorrective' . TABLE_RISQUE . '").css("display","none");
							$("#ongletSuiviActionCorrective' . TABLE_RISQUE . '").css("display","none");
							$("#ongletFicheActionCorrective' . TABLE_RISQUE . '").css("display","none");
						});
						$("#ongletAjouterRisque").click(function(){
							showRiskForm();
							$("#divVariablesFormRisque").html(\'<img src="' . PICTO_LOADING . '" />\');
							$("#divVariablesFormRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_METHODE . '", "act":"reloadVariables", "idMethode":$("#methodeFormRisque").val(), "idRisque": "0"});
							$("#historisationContainer").hide();
						});
					});
					function showRiskForm(){
						$("#divVoirRisques").hide();
						$("#divDemandeAction' . TABLE_RISQUE . '").hide();
						$("#divSuiviAction' . TABLE_RISQUE . '").hide();
						$("#divFicheAction' . TABLE_RISQUE . '").hide();
						$("#ongletVoirLesRisques").removeClass("selected_tab");
						$("#ongletDemandeActionCorrective' . TABLE_RISQUE . '").removeClass("selected_tab");
						$("#ongletSuiviActionCorrective' . TABLE_RISQUE . '").removeClass("selected_tab");
						$("#ongletFicheActionCorrective' . TABLE_RISQUE . '").removeClass("selected_tab");
						$("#divFormRisque").show();
						$("#ongletAjouterRisque").addClass("selected_tab");
						$("#ongletDemandeActionCorrective' . TABLE_RISQUE . '").css("display","none");
						$("#ongletSuiviActionCorrective' . TABLE_RISQUE . '").css("display","none");
						$("#ongletFicheActionCorrective' . TABLE_RISQUE . '").css("display","none");
					}
					$("#ongletSuiviFicheActionCorrective' . TABLE_RISQUE . '").click(function(){
						$("#divFormRisque").hide();
						$("#divVoirRisques").hide();
						$("#divDemandeAction' . TABLE_RISQUE . '").hide();
						$("#divSuiviAction' . TABLE_RISQUE . '").show();
						$("#divFicheAction' . TABLE_RISQUE . '").hide();
						$("#ongletAjouterRisque").removeClass("selected_tab");
						$("#ongletVoirLesRisques").removeClass("selected_tab");
						$("#ongletDemandeActionCorrective' . TABLE_RISQUE . '").removeClass("selected_tab");
						$("#ongletSuiviActionCorrective' . TABLE_RISQUE . '").removeClass("selected_tab");
						$("#ongletFicheActionCorrective' . TABLE_RISQUE . '").removeClass("selected_tab");
						$("#ongletSuiviFicheActionCorrective' . TABLE_RISQUE . '").addClass("selected_tab");
						$("#ongletDemandeActionCorrective' . TABLE_RISQUE . '").css("display","none");
						$("#ongletSuiviActionCorrective' . TABLE_RISQUE . '").css("display","none");
						$("#ongletFicheActionCorrective' . TABLE_RISQUE . '").css("display","none");
						$("#ongletSuiviFicheActionCorrective' . TABLE_RISQUE . '").css("display","inline");

						$("#divSuiviAction' . TABLE_RISQUE . '").html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
						$("#divSuiviAction' . TABLE_RISQUE . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "tableElement":"' . $tableElement . '", "idElement":"' . $idElement . '", "nom":"suiviFicheAction"});
					});
				</script>';

			$liEditionRisque = $divEditionRisque = '';
			if(current_user_can(eva_tools::slugify('Evarisk_:_editer_les_risques')))
			{
				$liEditionRisque = '
					<li id="ongletAjouterRisque" class="tabs" style="display: inline"><label tabindex="3">' . ucfirst(strtolower(sprintf(__('Ajouter %s', 'evarisk'), __('un risque', 'evarisk')))) . '</label></li>';
				$divEditionRisque = '<div id="divFormRisque" class="eva_tabs_panel" style="display:none">' . getFormulaireCreationRisque($tableElement, $idElement) . '</div>';
			}

			$taskList = evaActivity::activityList($tableElement, $idElement);
			$liSuiviActionCorrective = '';
			if(count($taskList) > 0)
			{
				$liSuiviActionCorrective = '<li id="ongletSuiviFicheActionCorrective' . TABLE_RISQUE . '" class="tabs" style="display: inline"><label tabindex="3">' . ucfirst(strtolower(__('Suivi des actions correctives', 'evarisk'))) . '</label></li>';
			}

			$corpsPostBoxRisque = $scriptRisque . '
				<div id="message' . TABLE_RISQUE . '" class="updated fade" style="cursor:pointer; display:none;"></div>
				<ul class="eva_tabs eva_tabs_button">
					<li id="ongletVoirLesRisques" class="tabs selected_tab" style="display:inline; margin-left:0.4em;"><label tabindex="3">' . ucfirst(strtolower(sprintf(__('voir %s', 'evarisk'), __('les risques', 'evarisk')))) . '</label></li>' . $liEditionRisque . '
					' . $liSuiviActionCorrective . '
					<li id="ongletDemandeActionCorrective' . TABLE_RISQUE . '" class="tabs" style="display:none;"><label tabindex="3">' . ucfirst(strtolower(__('Demande d\'action corrective', 'evarisk'))) . '</label></li>
					<li id="ongletSuiviActionCorrective' . TABLE_RISQUE . '" class="tabs" style="display:none;"><label tabindex="3">' . ucfirst(strtolower(__('Suivi des actions correctives', 'evarisk'))) . '</label></li>
					<li id="ongletFicheActionCorrective' . TABLE_RISQUE . '" class="tabs" style="display:none;"><label tabindex="3">' . ucfirst(strtolower(__('Fiche d\'action corrective', 'evarisk'))) . '</label></li>
				</ul>
				<div id="divVoirRisques" class="eva_tabs_panel">' . getVoirRisque ($tableElement, $idElement) . '</div>' . $divEditionRisque . '
				<div id="divDemandeAction' . TABLE_RISQUE . '" class="eva_tabs_panel" style="display:none"></div>
				<div id="divSuiviAction' . TABLE_RISQUE . '" class="eva_tabs_panel" style="display:none"></div>
				<div id="divAction' . TABLE_RISQUE . '" class="eva_tabs_panel" style="display:none"></div>
				<div id="divFicheAction' . TABLE_RISQUE . '" class="eva_tabs_panel" style="display:none"></div>';
		}
		else
		{
			switch($tableElement)
			{
				case TABLE_GROUPEMENT:
					$element = __('le groupement', 'evarisk');
					break;
				case TABLE_UNITE_TRAVAIL:
					$element = __('l\'unit&eacute; de travail', 'evarisk');
					break;
				default :
					$element = __('l\'&eacute;l&eacute;ment', 'evarisk');
			}
			$corpsPostBoxRisque = sprintf(__("Veuillez d'abord enregistrer %s.", 'evarisk'), $element);
		}
		echo $corpsPostBoxRisque;
	}

	/*
	* Cr�ation de l'affichage global
	*/
	function getVoirRisque($tableElement, $idElement)
	{
		$temp = Risque::getRisques($tableElement, $idElement, "Valid");
		if($temp != null)
		{
			foreach($temp as $risque)
			{
				$risques['"' . $risque->id . "'"][] = $risque; 
			}
		}
		{//Cr�ation de la table
			unset($titres,$classes, $idLignes, $lignesDeValeurs);
			$idLignes = null;
			$idTable = 'tableRisque' . $tableElement . $idElement;
			$titres[] = __("Quotation", 'evarisk');
			$titres[] = ucfirst(strtolower(sprintf(__("nom %s", 'evarisk'), __("du danger", 'evarisk'))));
			$titres[] = ucfirst(strtolower(sprintf(__("commentaire %s", 'evarisk'), __("sur le risque", 'evarisk'))));
			$titres[] = __("Actions", 'evarisk');
			$classes[] = 'columnQuotation';
			$classes[] = 'columnNomDanger';
			$classes[] = 'columnCommentaireRisque';
			$classes[] = 'columnAction';
			
			$scriptRisque = '';
			if(isset($risques) && ($risques != null))
			{
				foreach($risques as $risque)
				{
					$idligne = 'risque-' . $risque[0]->id;
					$scriptRisque .= '<script type="text/javascript">
						$(document).ready(function() {
							$("#' . $idligne . '-edit").click(function(){
								$("#divFormRisque").html(\'<img src="' . PICTO_LOADING . '" />\');
								showRiskForm();
								$("#divFormRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true",	"table":"' . TABLE_RISQUE . '", "act":"load", "idRisque": "' . $risque[0]->id . '", "idElement":"' . $idElement . '", "tableElement":"' . $tableElement . '"});
							});';
							
					if(options::getOptionValue('action_correctives_avancees') == 'oui')
					{
						$scriptRisque .= 
							'$("#' . $idligne . '-demandeAction").click(function(){
								$("#divFormRisque").hide();
								$("#divVoirRisques").hide();
								$("#ongletAjouterRisque").removeClass("selected_tab");
								$("#ongletVoirLesRisques").removeClass("selected_tab");
								$("#divDemandeAction' . TABLE_RISQUE . '").show();
								$("#ongletDemandeActionCorrective' . TABLE_RISQUE . '").addClass("selected_tab");
								$("#ongletDemandeActionCorrective' . TABLE_RISQUE . '").css("display","inline");
							
								$("#divDemandeAction' . TABLE_RISQUE . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "nom":"demandeAction",	"tableProvenance":"' . TABLE_RISQUE . '", "idProvenance": "' . $risque[0]->id . '"});
								$("#divDemandeAction' . TABLE_RISQUE . '").html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
							});
							
							$("#' . $idligne . '-suiviAction").click(function(){
								$("#divFormRisque").hide();
								$("#divVoirRisques").hide();
								$("#ongletAjouterRisque").removeClass("selected_tab");
								$("#ongletVoirLesRisques").removeClass("selected_tab");
								$("#divSuiviAction' . TABLE_RISQUE . '").show();
								$("#ongletSuiviActionCorrective' . TABLE_RISQUE . '").addClass("selected_tab");
								$("#ongletSuiviActionCorrective' . TABLE_RISQUE . '").css("display","inline");
							
								$("#divSuiviAction' . TABLE_RISQUE . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "nom":"suiviAction",	"tableProvenance":"' . TABLE_RISQUE . '", "idProvenance": "' . $risque[0]->id . '"});
								$("#divSuiviAction' . TABLE_RISQUE . '").html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
							});';
					}
					else
					{
						$scriptRisque .= 
							'$("#' . $idligne . '-FAC").click(function(){
								$("#divFormRisque").hide();
								$("#divVoirRisques").hide();
								$("#divDemandeAction' . TABLE_RISQUE . '").hide();
								$("#divSuiviAction' . TABLE_RISQUE . '").hide();
								$("#divFicheAction' . TABLE_RISQUE . '").show();
								$("#ongletAjouterRisque").removeClass("selected_tab");
								$("#ongletVoirLesRisques").removeClass("selected_tab");
								$("#ongletDemandeActionCorrective' . TABLE_RISQUE . '").removeClass("selected_tab");
								$("#ongletSuiviActionCorrective' . TABLE_RISQUE . '").removeClass("selected_tab");
								$("#ongletFicheActionCorrective' . TABLE_RISQUE . '").addClass("selected_tab");
								$("#ongletDemandeActionCorrective' . TABLE_RISQUE . '").css("display","none");
								$("#ongletSuiviActionCorrective' . TABLE_RISQUE . '").css("display","none");
								$("#ongletFicheActionCorrective' . TABLE_RISQUE . '").css("display","inline");

								$("#divFicheAction' . TABLE_RISQUE . '").html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
								$("#divFicheAction' . TABLE_RISQUE . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "tableElement":"' . $tableElement . '", "idElement":"' . $idElement . '", "nom":"ficheAction",	"tableProvenance":"' . TABLE_RISQUE . '", "idProvenance": "' . $risque[0]->id . '"});
							});';
					}

					$scriptRisque .= 
							'$("#' . $idligne . '-delete").click(function(){
								if(confirm("' . __('Etes vous sur de vouloir supprimer cet enregistrement?', 'evarisk') . '\r\n' . $risque[0]->nomDanger . '\r\n\t' . nl2br($risque[0]->commentaire) . '")){
									$("#divAction' . TABLE_RISQUE . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
										{
											"post":"true",
											"table":"' . TABLE_RISQUE . '",
											"act":"delete",
											"idRisque": "' . $risque[0]->id . '",
											"idElement":"' . $idElement . '",
											"tableElement":"' . $tableElement . '"
										}
									);
								}
							});
						});
					</script>';
					$idLignes[] = $idligne;
					
					$idMethode = $risque[0]->id_methode;
					$score = Risque::getScoreRisque($risque);
					$quotation = Risque::getEquivalenceEtalon($idMethode, $score, $risque[0]->date);
					$niveauSeuil = Risque::getSeuil($quotation);
					
					unset($ligneDeValeurs);
					$ligneDeValeurs[] = array('value' => $quotation, 'class' => 'Seuil_' . $niveauSeuil);
					$ligneDeValeurs[] = array('value' => $risque[0]->nomDanger, 'class' => '');
					$ligneDeValeurs[] = array('value' => nl2br($risque[0]->commentaire), 'class' => '');
					$correctiveActions = '<img style="width:' . TAILLE_PICTOS . ';" id="' . $idligne . '-FAC" src="' . PICTO_LTL_ADD_ACTION . '" alt="' . __('Fiche d\'action corrective', 'evarisk') . '" title="' . __('Fiche d\'action corrective', 'evarisk') . '"/>';
					if(options::getOptionValue('action_correctives_avancees') == 'oui')
					{
						$correctiveActions = '<img style="width:' . TAILLE_PICTOS . ';" id="' . $idligne . '-demandeAction" src="' . PICTO_LTL_ADD_ACTION . '" alt="' . _c('Demande AC|AC pour action corrective', 'evarisk') . '" title="' . __('Demande d\'action corrective', 'evarisk') . '"/><img style="width:' . TAILLE_PICTOS . ';" id="' . $idligne . '-suiviAction" src="' . PICTO_LTL_SUIVI_ACTION . '" alt="' . _c('Suivi AC|AC pour action corrective', 'evarisk') . '" title="' . __('Suivi des actions correctives', 'evarisk') . '"/>';
					}
					$ligneDeValeurs[] = array('value' => $correctiveActions . '<img style="width:' . TAILLE_PICTOS . ';" id="' . $idligne . '-edit" src="' . PICTO_EDIT . '" alt="' . __('Editer', 'evarisk') . '" title="' . __('Editer', 'evarisk') . '"/><img style="width:' . TAILLE_PICTOS . ';" id="' . $idligne . '-delete" src="' . PICTO_DELETE . '" alt="' . __('Supprimer', 'evarisk') . '" title="' . __('Supprimer', 'evarisk') . '"/>', 'class' => '');
					$lignesDeValeurs[] = $ligneDeValeurs;
				}
			}
			$lignesDeValeurs = (isset($lignesDeValeurs))?$lignesDeValeurs:null;
			switch($tableElement)
			{
				case TABLE_GROUPEMENT :
					$scoreRisque = EvaGroupement::getScoreRisque($idElement);
					break;
				case TABLE_UNITE_TRAVAIL :
					$scoreRisque = UniteDeTravail::getScoreRisque($idElement);
					break;
			}

			$scoreRisqueUniteTravail = 0;
			$riskAndSubRisks = documentUnique::listRisk($tableElement, $idElement);
			foreach($riskAndSubRisks as $risk)
			{
				$scoreRisqueUniteTravail += $risk[1]['value'];
			}
			$nombreRisqueUniteTravail = count($riskAndSubRisks);
			$scriptRisque = $scriptRisque . '
				<script type="text/javascript">
					$(document).ready(function() {
						$("#riskSum' . $tableElement . $idElement .'").html("' . $scoreRisqueUniteTravail . '");
						$("#riskNb' . $tableElement . $idElement .'").html("' . $nombreRisqueUniteTravail . '");
						$("#LeftRiskSum' . $tableElement . $idElement .'").html("' . $scoreRisqueUniteTravail . '");
						$("#LeftRiskNb' . $tableElement . $idElement .'").html("' . $nombreRisqueUniteTravail . '");
					});
				</script>';

			{//Script de d�finition de la dataTable
				$scriptVoirRisque = $scriptRisque . '
					<script type="text/javascript">
						$(document).ready(function() {
							$("#' . $idTable . '").dataTable({
								"sPaginationType": "full_numbers", 
								"bAutoWidth": false,
								"bInfo": false,								
								"aoColumns": [
									{ "bSortable": true, "sType": "numeric"},
									{ "bSortable": true},
									{ "bSortable": false},
									{ "bSortable": false }],
								"aaSorting": [[0,"desc"]]
							});
							$("#' . $idTable . ' tfoot").remove();
						});
					</script>';
			}
			
			$voirRisque = EvaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $scriptVoirRisque);
			
			return $voirRisque;
		}	
	}

	/*
	* Cr�ation du formulaire d'ajout/�dition
	*/
	function getFormulaireCreationRisque($tableElement, $idElement, $idRisque = '')
	{
		if($idRisque != '')
		{
			$risque = Risque::getRisque($idRisque);
		}
		else
		{
			$risque = null;
		}
		$formRisque = EvaDisplayInput::ouvrirForm('POST', 'formRisque', 'formRisque');
		
		{// Champs cach�s
			$formRisque = $formRisque . EvaDisplayInput::afficherInput('hidden', 'idRisque', $idRisque, '', null, 'idRisque', false, false);
		}
		
		{//Choix de la cat�gorie de dangers
			$nomRacine = 'Categorie Racine';
			$categorieRacine = categorieDangers::getCategorieDangerByName($nomRacine);
			$categoriesDangers = Arborescence::getDescendants(TABLE_CATEGORIE_DANGER, $categorieRacine);
			$radioGroup = '';
			if($risque[0] != null)
			{// Si l'on �dite un risque, on s�lectionne la bonne cat�gorie de dangers
				$selectionCategorie = $risque[0]->idCategorie;
			}
			else
			{// Sinon on s�lectionne la racine
				$selectionCategorie = $categorieRacine->id;
			}
			if(AFFICHAGE_PICTO_CATEGORIE)
			{
				$radioGroup = '';
				foreach($categoriesDangers as $categorieDangers)
				{
					if($selectionCategorie == $categorieRacine->id)
					{
						$selectionCategorie = $categorieDangers->id;
					}

					$categorieDangerMainPhoto = evaPhoto::getMainPhoto(TABLE_CATEGORIE_DANGER, $categorieDangers->id);
					if($categorieDangerMainPhoto == 'error')
					{
						$categorieDangerMainPhoto = DEFAULT_DANGER_CATEGORIE_PICTO;
						
						/* Lorsqu'une cat�gorie de danger n'a pas de picto alors on stoppait le script
						
						
						$selectionCategorie = $categorieRacine->id;
						$radioGroup = '';
						break; */
					}
					else
					{
						$categorieDangerMainPhoto = EVA_HOME_URL . $categorieDangerMainPhoto;
					}

					$radioGroup .= '<div class="radioPictoCategorie"><input id="cat' . $categorieDangers->id . '" type="radio" name="categoriesDangers" value="' . $categorieDangers->id . '" /><label for="cat' . $categorieDangers->id  . '" ><img src="' . $categorieDangerMainPhoto . '" alt="' . $categorieDangers->nom . '" title="' . $categorieDangers->nom . '" id="imgCat' . $categorieDangers->id . '" /></label></div>';
				}
				if($radioGroup != '')
				{
					$script = '
						<script type="text/javascript">
							$(document).ready(function(){
								$("#cat' . $selectionCategorie . '").click();
								var oldCatId = ($("[name=\'categoriesDangers\']:first").attr("id")).replace("cat","");
								$("[name=\'categoriesDangers\']").click(function(){
									var newCatId = ($(this).attr("id")).replace("cat","");
									if(oldCatId != newCatId)
									{
										$("#categorieDangerFormRisque").val(newCatId);
										$("#divDangerFormRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_CATEGORIE_DANGER . '", "act":"reloadComboDangers", "idElement":$("#categorieDangerFormRisque").val()});
										oldCatId = newCatId;
									}
								});
							});
							$("#divCategorieDangerFormRisque").hide();
						</script>';
					$radioGroup = $script . $radioGroup . '<br class="clear"/>';
				}
			}
			$script = '
				<script type="text/javascript">
					$(document).ready(function(){
						$("#categorieDangerFormRisque").change(function(){
								$("#divDangerFormRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_CATEGORIE_DANGER . '", "act":"reloadComboDangers", "idElement":$("#categorieDangerFormRisque").val()});
						});
					})
				</script>';
			$formRisque = $script . $formRisque . '<div id="divCategorieDangerFormRisque">' . EvaDisplayInput::afficherComboBoxArborescente($racine = $categorieRacine, $table = TABLE_CATEGORIE_DANGER, $idSelect = 'categorieDangerFormRisque', $labelSelect = __('Cat&eacute;gorie de dangers', 'evarisk') . ' : ', $nameSelect = 'categorieDangers', $valeurDefaut = ucfirst(strtolower(sprintf(__("choisissez %s", 'evarisk'), __("une cat&eacute;gorie de dangers", 'evarisk')))), $selectionCategorie) . '</div>';
		}
		
		$formRisque = $script . $radioGroup . $formRisque . '<div id="needDangerCategory">';

		{//Choix du danger
			$dangers = categorieDangers::getDangersDeLaCategorie($selectionCategorie, 'Status="Valid"');
			if(isset($dangers[0]) && ($dangers[0]->id != null))
			{
				$formRisque = $formRisque . '
					<script type="text/javascript">
						$(document).ready(function(){
							$("#needDangerCategory").show();
						})
					</script>';
			}
			else
			{
				$formRisque = $formRisque . '
					<script type="text/javascript">
						$(document).ready(function(){
							$("#needDangerCategory").hide();
						})
					</script>';
			}
			if($risque[0] != null)
			{// Si l'on �dite un risque, on s�lectionne le bon danger
				$selection = $risque[0]->idDanger;
				$selection = evaDanger::getDanger($selection);
			}
			else
			{// Sinon on s�lectionne le premier danger de la cat�gorie
				$selection = (isset($dangers[0]) && ($dangers[0]->id))?$dangers[0]->id:null;
			}
			if($selection != null)
			{
				$nombreDeDangers = count($dangers);
				$afficheSelecteurDanger = '';
				if($nombreDeDangers <= 1)
				{
					$afficheSelecteurDanger = ' display:none; ';
				}
				$formRisque .= '<div  style="' . $afficheSelecteurDanger . '" id="divDangerFormRisque" >' . EvaDisplayInput::afficherComboBox($dangers, 'dangerFormRisque', __('Dangers de la cat&eacute;gorie', 'evarisk') . ' : ', 'danger', '', $selection) . '</div><br />';
			}
		}

		{//Choix de la m�thode
			$methodes = MethodeEvaluation::getMethods('Status="Valid"');
			$script = '
				<script type="text/javascript">
					$(document).ready(function(){
						$("#methodeFormRisque").change(function(){
							$("#divVariablesFormRisque").html(\'<img src="' . PICTO_LOADING . '" />\');
							$("#divVariablesFormRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_METHODE . '", "act":"reloadVariables", "idMethode":$("#methodeFormRisque").val(), "idRisque": "' . $idRisque . '"});
						});
					})
				</script>';
			if($risque[0] != null)
			{// Si l'on �dite un risque, on s�lectionne la bonne m�thode
				$idSelection = $risque[0]->id_methode;
			}
			else
			{// Sinon on s�lectionne la premi�re m�thode
				$idSelection = $methodes[0]->id;
			}
			$selection = MethodeEvaluation::getMethod($idSelection);
			$nombreMethode = count($methodes);
			$afficheSelecteurMethode = '';
			if($nombreMethode <= 1)
			{
				$afficheSelecteurMethode = ' display:none; ';
			}
			$formRisque .= '<div id="choixMethodeEvaluation" style="' . $afficheSelecteurMethode . '" >' . $script . EvaDisplayInput::afficherComboBox($methodes, $idSelect = 'methodeFormRisque', $labelSelect = __('M&eacute;thode d\'&eacute;valuation', 'evarisk') . ' : ', $nameSelect = 'methode', $valeurDefaut = '', $selection) . '</div>';
		}

		{//Evaluation des variables
			$formRisque = $formRisque . '<script type="text/javascript">
					$(document).ready(function(){
						$("#divVariablesFormRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_METHODE . '", "act":"reloadVariables", "idMethode":$("#methodeFormRisque").val(), "idRisque": "' . $idRisque . '"});
					})
				</script>';
			$formRisque = $formRisque . '<div id="divVariablesFormRisque">';
			$formRisque = $formRisque . '</div><!-- /divVariablesFormRisque -->';
		}
		
		{//Description
			$contenuInput = '';
			if($risque[0] != null)
			{// Si l'on �dite un risque, on remplit l'aire de texte avec sa description
				$contenuInput = $risque[0]->commentaire;
			}
			$labelInput = ucfirst(strtolower(sprintf(__("commentaire %s", 'evarisk'), __('sur le risque', 'evarisk'))));
			$labelInput[1] = ($labelInput[0] == "&")?ucfirst($labelInput[1]):$labelInput[1];
			$formRisque = $formRisque . '<br />' . '<div id="divDescription" class="clear" >' . EvaDisplayInput::afficherInput($type='textarea', $id='descriptionFormRisque', $contenuInput, $contenuAide='', $labelInput . ' : ', $nomChamps='description', $grise=false, DESCRIPTION_RISQUE_OBLIGATOIRE, $taille = 3, $classe='', $limitation='', $width='100%', $script='') . '</div>';
		}

		{//Historisation du risque
			if($idRisque != '')
			{
				$formRisque .= '<div class="alignright" id="historisationContainer" ><input type="checkbox" value="non" name="historisation" id="historisation" /><label for="historisation" >' . __('Ne pas afficher dans les historiques de modifications','evarisk') . '</label></div>';
			}
		}

		{//Bouton enregistrer
			$allVariables = MethodeEvaluation::getAllVariables();
			$idBouttonEnregistrer = 'enregistrerFormRisque';
			$scriptEnregistrement = '<script type="text/javascript">
				$(document).ready(function() {	
					$(\'#' . $idBouttonEnregistrer . '\').click(function(){
						var variables = new Array();';
			foreach($allVariables as $variable)
			{
				$scriptEnregistrement .= '
						variables["' . $variable->id . '"] = $("#var' . $variable->id . 'FormRisque").val();';
			}
			$scriptEnregistrement .= '
						var historisation = true;
						if($("#historisation").is(":checked")){
							historisation = false;
						}
						var correctivActions = "";';
			if($idRisque != '')
			{
				$scriptEnregistrement .= '
						$("#correctivActionTab input:checkbox").each(function(){
							if( $(this).is(":checked") )
							{
								acValue = $(this).val();
								correctivActions += acValue + "_ac_";
							}
						});
				';
			}
			$scriptEnregistrement .= ';
						$("#divFormRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
							"post":"true", 
							"table":"' . TABLE_RISQUE . '", 
							"act":"save", 
							"tableElement":"' . $tableElement . '", 
							"idElement":"' . $idElement . '", 
							"idDanger":$("#dangerFormRisque").val(), 
							"idMethode":$("#methodeFormRisque").val(), 
							"histo":historisation, 
							"actionsCorrectives":correctivActions, 
							"variables":variables, 
							"description":$("#descriptionFormRisque").val(), 
							"idRisque":$("#idRisque").val()
						});
						$("#divFormRisque").html(\'<img src="' . PICTO_LOADING . '" />\');
						$("#divVoirRisques").html(\'<img src="' . PICTO_LOADING . '" />\');
						setTimeout(function(){$("#ongletVoirLesRisques").click();},1000);
						return false;
					});
				});
				</script>';
			$formRisque .= EvaDisplayInput::afficherInput('button', $idBouttonEnregistrer, 'Enregistrer', null, '', 'save', false, false, '', 'button-primary alignright', '', '', $scriptEnregistrement);
		}
		
		$formRisque = $formRisque . '</div> <!--/needDangerCategory-->';
		$formRisque = $formRisque . EvaDisplayInput::fermerForm('formRisque');
		
		return $formRisque;
	}

?>