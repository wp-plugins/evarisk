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
		/*	If the postBoxId change don't forget to replace each iteration in this script	*/
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
					var TABLE_RISQUE = "' . TABLE_RISQUE . '";

					evarisk(document).ready(function(){
						//	Show the risk list for the actual element
						evarisk("#ongletVoirLesRisques").click(function(){
							evarisk("#divVoirRisques").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
							{
								"post":"true", 
								"table":"' . TABLE_RISQUE . '", 
								"act":"reloadVoirRisque", 
								"tableElement":"' . $tableElement . '",
								"idElement":' . $idElement . '
							}
							);
							tabChange("#divVoirRisques", "#ongletVoirLesRisques");
							hideExtraTab();
						});

						//	Show the existing corrective action on the actual element
						evarisk("#ongletSuiviFicheActionCorrective' . TABLE_RISQUE . '").click(function(){
							evarisk("#divSuiviAction' . TABLE_RISQUE . '").html(evarisk("#loadingImg").html());
							evarisk("#divSuiviAction' . TABLE_RISQUE . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
							{
								"post":"true", 
								"tableElement":"' . $tableElement . '", 
								"idElement":"' . $idElement . '", 
								"nom":"suiviFicheAction"
							}
							);
							tabChange("#divSuiviAction' . TABLE_RISQUE . '", "#ongletSuiviFicheActionCorrective' . TABLE_RISQUE . '");
							hideExtraTab();
							evarisk("#ongletSuiviFicheActionCorrective' . TABLE_RISQUE . '").css("display","inline");
						});

						//	Output the form to add a new risk
						evarisk("#ongletAjouterRisque").click(function(){
							evarisk("#formRisque").html(evarisk("#loadingImg").html());
							evarisk("#formRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
								{
									"post":"true", 
									"table":"' . TABLE_RISQUE . '", 
									"act":"reloadRiskForm", 
									"tableElement":"' . $tableElement . '", 
									"idElement":"' . $idElement . '", 
									"idRisque": ""
								}
							);
							tabChange("#divFormRisque", "#ongletAjouterRisque");
							hideExtraTab();
							evarisk("#divDangerContainer :radio").each(function(){
								evarisk(this).attr("checked", "");
							});
							evarisk("#divDangerContainer").css("display", "block");
							evarisk("#divDangerContainerSwitch").css("display", "none");
							evarisk("#historisationContainer").hide();
							evarisk("#associatedPictureContainer").hide();
						});

						evarisk("#ongletAjouterRisquePhoto").click(function(){
							evarisk("#formRisque").html(evarisk("#loadingImg").html());
							evarisk("#formRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
							{
								"post":"true", 
								"table":"' . TABLE_RISQUE . '", 
								"act":"loadAdvancedRiskForm", 
								"tableElement":"' . $tableElement . '", 
								"idElement":"' . $idElement . '", 
								"idRisque": ""
							});
							tabChange("#divFormRisque", "#ongletAjouterRisquePhoto");
							hideExtraTab();
						});
					});
				</script>';

			$liEditionRisque = $divEditionRisque = '';
			if(current_user_can(eva_tools::slugify('Evarisk_:_editer_les_risques')))
			{
				$advancedFormRisque = '';
				if((options::getOptionValue('risques_avances') == 'oui') && ($idRisque == ''))
				{
					$advancedFormRisque = '<li id="ongletAjouterRisquePhoto" class="tabs" style="display: inline"><label tabindex="3">' . ucfirst(strtolower(__('Ajouter des risques depuis des photos', 'evarisk'))) . '</label></li>';
				}
				$liEditionRisque = '
					<li id="ongletAjouterRisque" class="tabs" style="display: inline"><label tabindex="3">' . ucfirst(strtolower(sprintf(__('Ajouter %s', 'evarisk'), __('un risque', 'evarisk')))) . '</label></li>' . $advancedFormRisque;

				$divEditionRisque = '<div id="divFormRisque" class="eva_tabs_panel" style="display:none">' . getFormulaireCreationRisque($tableElement, $idElement) . '</div>';
			}

			$taskList = evaActivity::activityList($tableElement, $idElement);
			$liSuiviActionCorrective = '';
			if(count($taskList) > 0)
			{
				$liSuiviActionCorrective = '<li id="ongletSuiviFicheActionCorrective' . TABLE_RISQUE . '" class="tabs" style="display:inline" ><label tabindex="3">' . ucfirst(strtolower(__('Suivi des actions correctives', 'evarisk'))) . '</label></li>';
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
	* Création de l'affichage global
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
		{//Création de la table
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
					$scriptRisque .= 
					'<script type="text/javascript">
						evarisk(document).ready(function() {
							evarisk("#' . $idligne . '-edit").click(function(){
								evarisk("#divFormRisque").html(evarisk("#loadingImg").html());
								tabChange("#divFormRisque", "#ongletAjouterRisque");
								evarisk("#divFormRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true",	"table":"' . TABLE_RISQUE . '", "act":"load", "idRisque": "' . $risque[0]->id . '", "idElement":"' . $idElement . '", "tableElement":"' . $tableElement . '"});
							});';

					if(options::getOptionValue('action_correctives_avancees') == 'oui')
					{
						$scriptRisque .= 
							'evarisk("#' . $idligne . '-demandeAction").click(function(){
								tabChange("#divDemandeAction' . TABLE_RISQUE . '", "#ongletDemandeActionCorrective' . TABLE_RISQUE . '");
								hideExtraTab();
								evarisk("#ongletDemandeActionCorrective' . TABLE_RISQUE . '").css("display","inline");
								evarisk("#divDemandeAction' . TABLE_RISQUE . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "nom":"demandeAction",	"tableProvenance":"' . TABLE_RISQUE . '", "idProvenance": "' . $risque[0]->id . '"});
								evarisk("#divDemandeAction' . TABLE_RISQUE . '").html(evarisk("#loadingImg").html());
							});
							
							evarisk("#' . $idligne . '-suiviAction").click(function(){
								tabChange("#divSuiviAction' . TABLE_RISQUE . '", "#ongletSuiviActionCorrective' . TABLE_RISQUE . '");
								hideExtraTab();
								evarisk("#ongletSuiviActionCorrective' . TABLE_RISQUE . '").css("display","inline");
								evarisk("#divSuiviAction' . TABLE_RISQUE . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "nom":"suiviAction",	"tableProvenance":"' . TABLE_RISQUE . '", "idProvenance": "' . $risque[0]->id . '"});
								evarisk("#divSuiviAction' . TABLE_RISQUE . '").html(evarisk("#loadingImg").html());
							});';
					}
					else
					{
						$scriptRisque .= 
							'evarisk("#' . $idligne . '-FAC").click(function(){
								tabChange("#divFicheAction' . TABLE_RISQUE . '", "#ongletFicheActionCorrective' . TABLE_RISQUE . '");
								hideExtraTab();
								evarisk("#ongletFicheActionCorrective' . TABLE_RISQUE . '").css("display","inline");
								evarisk("#divFicheAction' . TABLE_RISQUE . '").html(evarisk("#loadingImg").html());
								evarisk("#divFicheAction' . TABLE_RISQUE . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "tableElement":"' . $tableElement . '", "idElement":"' . $idElement . '", "nom":"ficheAction",	"tableProvenance":"' . TABLE_RISQUE . '", "idProvenance": "' . $risque[0]->id . '"});
							});';
					}

					$scriptRisque .= 
							'evarisk("#' . $idligne . '-delete").click(function(){
								if(confirm("' . __('Etes vous sur de vouloir supprimer cet enregistrement?', 'evarisk') . '\r\n' . $risque[0]->nomDanger . '\r\n\t' . nl2br($risque[0]->commentaire) . '")){
									evarisk("#divAction' . TABLE_RISQUE . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
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

			{//Script de définition de la dataTable
				$scriptVoirRisque = $scriptRisque . '
<script type="text/javascript">
	evarisk(document).ready(function() {

		//	Update The risk number and score in the different part of the screen
		evarisk("#riskSum' . $tableElement . $idElement .'").html("' . $scoreRisqueUniteTravail . '");
		evarisk("#riskNb' . $tableElement . $idElement .'").html("' . $nombreRisqueUniteTravail . '");
		evarisk("#LeftRiskSum' . $tableElement . $idElement .'").html("' . $scoreRisqueUniteTravail . '");
		evarisk("#LeftRiskNb' . $tableElement . $idElement .'").html("' . $nombreRisqueUniteTravail . '");

		evarisk("#' . $idTable . '").dataTable({
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
		evarisk("#' . $idTable . ' tfoot").remove();
	});
</script>';
			}
			
			$voirRisque = EvaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $scriptVoirRisque);
			
			return $voirRisque;
		}	
	}

	/*
	* Création du formulaire d'ajout/édition
	*/
	function getFormulaireCreationRisque($tableElement, $idElement, $idRisque = '')
	{
		$divDangerContainerStyle = $script = '';
		$divDangerContainerSwitchStyle = ' style="display:none;" ';
		if($idRisque != '')
		{
			$risque = Risque::getRisque($idRisque);
			$divDangerContainerStyle = ' style="display:none;" ';
			$divDangerContainerSwitchStyle = '';
		}
		else
		{
			$risque = null;
		}

		{//Choix de la catégorie de dangers
			$categorieDanger = categorieDangers::getCategorieDangerForRiskEvaluation($risque);
			$script .= $categorieDanger['script'];
			$selectionCategorie = $categorieDanger['selectionCategorie'];
		}
		{//Choix du danger
			$ListDanger = evaDanger::getDangerForRiskEvaluation($selectionCategorie, $risque);
			$script .= $ListDanger['script'];
		}		

		$formRisque = 
EvaDisplayInput::ouvrirForm('POST', 'formRisque', 'formRisque') .
EvaDisplayInput::afficherInput('hidden', 'idRisque', $idRisque, '', null, 'idRisque', false, false) . 
'<div id="formRisque" >
	' . $advancedFormRisque . '
	<div>
		<div id="divDangerContainerSwitch" ' . $divDangerContainerSwitchStyle . ' class="pointer" >
			<img id="divDangerContainerSwitchPic" src="' . PICTO_EXPAND . '" alt="' . __('collapsor', 'evarisk') . '" style="vertical-align:middle;" />
			<span style="vertical-align:middle;" >' . __('Voir les dangers', 'evarisk') . '</span>
		</div>
		<div id="divDangerContainer" ' . $divDangerContainerStyle . ' >' . $categorieDanger['list'] . $ListDanger['list'] . '</div>
	</div>';

		{//Choix de la méthode
			$methodes = MethodeEvaluation::getMethods('Status="Valid"');
			$script .= '
			evarisk("#methodeFormRisque").change(function(){
				evarisk("#divVariablesFormRisque").html(evarisk("#loadingImg").html());
				evarisk("#divVariablesFormRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_METHODE . '", "act":"reloadVariables", "idMethode":evarisk("#methodeFormRisque").val(), "idRisque": "' . $idRisque . '"});
			});';
			if($risque[0] != null)
			{// Si l'on édite un risque, on sélectionne la bonne méthode
				$idSelection = $risque[0]->id_methode;
			}
			else
			{// Sinon on sélectionne la première méthode
				$idSelection = $methodes[0]->id;
			}
			$selection = MethodeEvaluation::getMethod($idSelection);
			$nombreMethode = count($methodes);
			$afficheSelecteurMethode = '';
			if($nombreMethode <= 1)
			{
				$afficheSelecteurMethode = ' display:none; ';
			}
			$formRisque .= '<div id="choixMethodeEvaluation" style="' . $afficheSelecteurMethode . '" >' . EvaDisplayInput::afficherComboBox($methodes, $idSelect = 'methodeFormRisque', $labelSelect = __('M&eacute;thode d\'&eacute;valuation', 'evarisk') . ' : ', $nameSelect = 'methode', $valeurDefaut = '', $selection) . '</div>';
		}

		{//Evaluation des variables
			$formRisque .= 
'<script type="text/javascript">
	evarisk(document).ready(function(){
		evarisk("#divVariablesFormRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_METHODE . '", "act":"reloadVariables", "idMethode":evarisk("#methodeFormRisque").val(), "idRisque": "' . $idRisque . '"});
	})
</script>
<div id="divVariablesFormRisque"></div><!-- /divVariablesFormRisque -->';
		}
		
		{//Description
			$contenuInput = '';
			if($risque[0] != null)
			{// Si l'on édite un risque, on remplit l'aire de texte avec sa description
				$contenuInput = $risque[0]->commentaire;
			}
			$labelInput = ucfirst(strtolower(sprintf(__("commentaire %s", 'evarisk'), __('sur le risque', 'evarisk'))));
			$labelInput[1] = ($labelInput[0] == "&")?ucfirst($labelInput[1]):$labelInput[1];
			$formRisque .= '<br /><div id="divDescription" class="clear" >' . EvaDisplayInput::afficherInput('textarea', 'descriptionFormRisque', $contenuInput, '', $labelInput . ' : ', 'description', false, DESCRIPTION_RISQUE_OBLIGATOIRE, 3, '', '', '100%', '') . '</div>';
		}

		{//Photo associée au risque
			if($idRisque != '')
			{
				$pictureAssociated = evaPhoto::getPhotos(TABLE_RISQUE, $idRisque);
				if(count($pictureAssociated) > 0)
				{
					$formRisque .= '<div class="alignleft pointer" id="associatedPictureContainer" style="width:90%;" >' . __('Photo associ&eacute;e &agrave; ce risque', 'evarisk') . '<div id="deletePictureAssociation" ><span class="ui-icon deleteLinkBetwwenRiskAndPicture alignleft" title="' . __('Supprimer cette liaison', 'evarisk') . '" >&nbsp;</span>' . __('Supprimer l\'association', 'evarisk') . '</div><img class="alignleft riskPictureThumbs" src="' . EVA_HOME_URL . $pictureAssociated[0]->photo . '" alt="picture to associated to this risk unvailable" /></div>';
					$script .= '
		evarisk("#deletePictureAssociation").click(function(){
			evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
			{
				"post":"true",
				"table":"' . TABLE_RISQUE . '",
				"tableElement":"' . TABLE_RISQUE . '",
				"idElement":"' . $idRisque . '",
				"act":"unAssociatePicture",
				"idPicture":"' . $pictureAssociated[0]->id . '"
			});
		});';
				}
			}
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
			$scriptEnregistrement = 
'<script type="text/javascript">
	evarisk(document).ready(function(){
		//	Change the state of the danger container
		evarisk("#divDangerContainerSwitch").click(function(){
			if(evarisk("#divDangerContainerSwitchPic").attr("src") == "' . PICTO_EXPAND . '"){
				evarisk("#divDangerContainerSwitchPic").attr("src", "' . PICTO_COLLAPSE . '");
			}
			else{
				evarisk("#divDangerContainerSwitchPic").attr("src", "' . PICTO_EXPAND . '");
			}
			evarisk("#divDangerContainer").toggle();
		});
		evarisk("#' . $idBouttonEnregistrer . '").click(function(){
			var variables = new Array();';
			foreach($allVariables as $variable)
			{
				$scriptEnregistrement .= '
			variables["' . $variable->id . '"] = evarisk("#var' . $variable->id . 'FormRisque").val();';
			}
			$scriptEnregistrement .= '
			var historisation = true;
			if(evarisk("#historisation").is(":checked")){
				historisation = false;
			}
			var correctivActions = "";';
			if($idRisque != '')
			{
				$scriptEnregistrement .= '
			evarisk("#correctivActionTab input:checkbox").each(function(){
				if( evarisk(this).is(":checked") )
				{
					acValue = evarisk(this).val();
					correctivActions += acValue + "_ac_";
				}
			});';
			}
			$scriptEnregistrement .= '
			evarisk("#divFormRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
			{
				"post":"true", 
				"table":"' . TABLE_RISQUE . '", 
				"act":"save", 
				"tableElement":"' . $tableElement . '", 
				"idElement":"' . $idElement . '", 
				"idDanger":evarisk("#dangerFormRisque").val(), 
				"idMethode":evarisk("#methodeFormRisque").val(), 
				"histo":historisation, 
				"actionsCorrectives":correctivActions, 
				"variables":variables, 
				"description":evarisk("#descriptionFormRisque").val(), 
				"idRisque":evarisk("#idRisque").val()
			});
			evarisk("#divFormRisque").html(evarisk("#loadingImg").html());
			evarisk("#divVoirRisques").html(evarisk("#loadingImg").html());
			setTimeout(function(){evarisk("#ongletVoirLesRisques").click();},1000);
			return false;
		});
	});
</script>';
			$formRisque .= EvaDisplayInput::afficherInput('button', $idBouttonEnregistrer, 'Enregistrer', null, '', 'save', false, false, '', 'button-primary alignright', '', '', $scriptEnregistrement);
		}
		
		$formRisque .= '
</div>'	. 
EvaDisplayInput::fermerForm('formRisque') . '
<script type="text/javascript">
	evarisk(document).ready(function(){
		' . $script . '
	});
</script>';
		
		return $formRisque;
	}

	/**
	*	Create an advanced form to add a risq to an element
	*	
	*	@param mixed $tableElement The element type we want to add risq to
	*	@param integer $idElement The element identifier we want to add risq to
	*
	*	@return mixed $advancedForm The complete html output for the form
	*/
	function getAvancedFormulaireCreationRisque($tableElement, $idElement)
	{
		$advancedForm = '';
		$script = '';

		/*	Add The form button to add a new picture	*/
		$advancedForm = '<div style="display:table;width:95%;margin:0px 0px 12px 0px;" ><div class="alignleft" >' . Risque::getRisqueNonAssociePhoto($tableElement, $idElement) . '</div><div id="sendNewPictureForm" class="alignright" style="margin:12px 0px;" >' . evaPhoto::getFormulaireUploadPhoto($tableElement, $idElement, str_replace('\\', '/', EVA_UPLOADS_PLUGIN_DIR . $tableElement . '/' . $idElement . '/'), 'pictureToAssociateToRisk', "['jpeg','jpg','png','gif']", true, '', '', __('Envoyer des photos', 'evarisk'), 'evarisk("#ongletAjouterRisquePhoto").click();') . '</div></div>';

		/*	Get the picture list associated to the current element	*/
		$pictureList = evaPhoto::getPhotos($tableElement, $idElement);
		foreach($pictureList as $picture)
		{
			$currentId = 'picture_' . $picture->id . '_';

			if(is_file(EVA_HOME_DIR . $picture->photo))
			{
				/*	Check if there are already risks that are associated to this picture	*/
				$riskListForPicture = '';
				$riskListForPicture = Risque::getRisqueAssociePhoto($picture->id);

				/*	Add the picture to the output	*/
				$advancedForm .= '
<div id="' . $currentId . '" class="clear" style="margin:0px 0px 12px;" >
	<div class="clear" >
		<img id="addRiskByPictureId' . $currentId . '" class="alignleft riskPictureThumbs" src="' . EVA_HOME_URL . $picture->photo . '" alt="picture to associated to a risk ' . $picture->id . '" />
		<div style="width:75%;" id="addRiskByPictureButtonId' . $currentId . '" class="alignleft pointer" >
			<img id="divDangerContainerSwitchPic' . $currentId . '" src="' . PICTO_EXPAND . '" alt="' . __('collapsor', 'evarisk') . '" style="vertical-align:middle;" class="expandablePics addRiskByPictureButton" />
			<span style="vertical-align:middle;" class="addRiskByPictureButton" id="addRiskForPictureText' . $currentId . '" >' . __('Ajouter un risque pour cette photo', 'evarisk') . '</span>
			<div class="riskAssociatedToPictureContainer" id="riskAssociatedToPicture' . $currentId . '" >' . $riskListForPicture . '</div>
		</div>
	</div>
	<div id="' . $currentId . 'content" class="clear" style="padding:12px 0px;" >&nbsp;</div>
</div>';
			}
		}

		{/*	Define different javascript action associated to the advanced form	*/
		$script = '
<script type="text/javascript" >
	var draggedObjectFather;
	evarisk(document).ready(function(){
		evarisk(".riskPictureThumbs").click(function(){
			loadAdvancedRiskForm(evarisk(this).attr("id").replace("addRiskByPictureId",""));
			checkOpenRiskNumber();
		});
		evarisk(".addRiskByPictureButton").click(function(){
			loadAdvancedRiskForm(evarisk(this).parent("div").attr("id").replace("addRiskByPictureButtonId",""));
			checkOpenRiskNumber();
		});
		evarisk("#saveMassRiskWithPicture").click(function(){
			evarisk(".saveRiskFormButton").each(function(){
				evarisk(this).click();
			});
		});
		evarisk(".riskAssociatedToPictureContainer").droppable({
			accept:".riskAssociatedToPicture",
			activeClass: "ui-state-hover",
			hoverClass: "ui-state-active",
			over: function(event, ui){
				if(evarisk(this).html() == ""){
					evarisk(this).html("' . __('D&eacute;poser ici pour affecter ce risque &agrave; cette photo', 'evarisk') . '");
				}
			},
			drop: function(event, ui){
				evarisk(ui.draggable).remove();
				evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
				{
					"post":"true",
					"table":"' . TABLE_RISQUE . '",
					"act":"associateRiskToPicture",
					"tableElement":"' . TABLE_RISQUE . '",
					"idElement":evarisk(ui.draggable).attr("id").replace("loadRiskId", ""),
					"idPicture":evarisk(this).attr("id").replace("riskAssociatedToPicture",""),
					"oldidPicture":draggedObjectFather
				});
			}
		});
		evarisk("#seeRiskToAssociate").click(function(){
			if(evarisk("#seeRiskToAssociatePic").attr("src") == "' . PICTO_EXPAND . '"){
				evarisk("#seeRiskToAssociatePic").attr("src", "' . PICTO_COLLAPSE . '");
			}
			else{
				evarisk("#seeRiskToAssociatePic").attr("src", "' . PICTO_EXPAND . '");
			}
		});
	});
	//Verification du nombre de risque ouvert pour ajout avec une photo, si supérieur à 1 alors on affiche le bouton enregistrer tout
	function checkOpenRiskNumber()
	{
		var openNumber = 0
		evarisk(".expandablePics").each(function(){
			if(evarisk(this).attr("src") == "' . PICTO_COLLAPSE . '"){
				openNumber++;
			}
		});
		if(openNumber > 1){
			evarisk("#saveMassRiskWithPicture").show();
		}
		else{
			evarisk("#saveMassRiskWithPicture").hide();
		}
	}
	//Chargement du formulaire d\'ajout d\'un risque
	function loadAdvancedRiskForm(idToLoad)
	{
		if(evarisk("#divDangerContainerSwitchPic" + idToLoad).attr("src") == "' . PICTO_EXPAND . '"){
			evarisk("#divDangerContainerSwitchPic" + idToLoad).attr("src", "' . PICTO_COLLAPSE . '");
			evarisk("#addRiskForPictureText" + idToLoad).html("' . __('Annuler l\'ajout du risque', 'evarisk') . '");
			evarisk("#" + idToLoad + "content").html(evarisk("#loadingImg").html());
			evarisk("#" + idToLoad + "content").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
			{
				"post":"true",
				"table":"' . TABLE_RISQUE . '",
				"act":"addRiskByPicture",
				"tableElement":"' . $tableElement . '",
				"idElement":"' . $idElement . '",
				"currentId":idToLoad
			});
		}
		else{
			evarisk("#divDangerContainerSwitchPic" + idToLoad).attr("src", "' . PICTO_EXPAND . '");
			evarisk("#addRiskForPictureText" + idToLoad).html("' . __('Ajouter un risque pour cette photo', 'evarisk') . '");
			evarisk("#" + idToLoad + "content").html("");
		}
	}
</script>';
		}

		return $advancedForm . '<div id="massSaveButton" ><input style="display:none;" class="button-primary alignright" type="button" name="saveMassRiskWithPicture" id="saveMassRiskWithPicture" value="' . __('Enregistrer tout', 'evarisk') . '" /></div>' . $script;
	}

?>