<?php
	require_once(EVA_CONFIG );
	require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayDesign.class.php' );
	require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayInput.class.php' );
	require_once(EVA_LIB_PLUGIN_DIR . 'danger/categorieDangers/categorieDangers.class.php' );
	require_once(EVA_LIB_PLUGIN_DIR . 'danger/danger/evaDanger.class.php' );
	require_once(EVA_LIB_PLUGIN_DIR . 'methode/methodeEvaluation.class.php' );
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
							$("#ongletAjouterRisque").removeClass("selected_tab");
							$("#ongletDemandeActionCorrective' . TABLE_RISQUE . '").removeClass("selected_tab");
							$("#ongletSuiviActionCorrective' . TABLE_RISQUE . '").removeClass("selected_tab");
							$("#divVoirRisques").show();
							$("#ongletVoirLesRisques").addClass("selected_tab");
							$("#divVoirRisques").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_RISQUE . '", "act":"reloadVoirRisque", "tableElement":"' . $tableElement . '","idElement":' . $idElement . '});
							$("#ongletDemandeActionCorrective' . TABLE_RISQUE . '").css("display","none");
							$("#ongletSuiviActionCorrective' . TABLE_RISQUE . '").css("display","none");
						});
						$("#ongletAjouterRisque").click(function(){
							$("#divVoirRisques").hide();
							$("#divDemandeAction' . TABLE_RISQUE . '").hide();
							$("#divSuiviAction' . TABLE_RISQUE . '").hide();
							$("#ongletVoirLesRisques").removeClass("selected_tab");
							$("#ongletDemandeActionCorrective' . TABLE_RISQUE . '").removeClass("selected_tab");
							$("#ongletSuiviActionCorrective' . TABLE_RISQUE . '").removeClass("selected_tab");
							$("#divFormRisque").show();
							$("#ongletAjouterRisque").addClass("selected_tab");
							$("#ongletDemandeActionCorrective' . TABLE_RISQUE . '").css("display","none");
							$("#ongletSuiviActionCorrective' . TABLE_RISQUE . '").css("display","none");
						});
					});
				</script>';
			$liEditionRisque = $divEditionRisque = '';
			if(current_user_can(eva_tools::slugify('Evarisk_:_editer_les_risques')))
			{
				$liEditionRisque = '
					<li id="ongletAjouterRisque" class="tabs" style="display: inline"><label tabindex="3">' . ucfirst(strtolower(sprintf(__('Ajouter %s', 'evarisk'), __('un risque', 'evarisk')))) . '</label></li>';
				$divEditionRisque = '<div id="divFormRisque" class="eva_tabs_panel" style="display:none">' . getFormulaireCreationRisque($tableElement, $idElement) . '</div>';
			}
			$corpsPostBoxRisque = $scriptRisque . '
				<div id="message' . TABLE_RISQUE . '" class="updated fade" style="cursor:pointer; display:none;"></div>
				<ul class="eva_tabs eva_tabs_button">
					<li id="ongletVoirLesRisques" class="tabs selected_tab" style="display:inline; margin-left:0.4em;"><label tabindex="3">' . ucfirst(strtolower(sprintf(__('voir %s', 'evarisk'), __('les risques', 'evarisk')))) . '</label></li>' . $liEditionRisque . '
					<li id="ongletDemandeActionCorrective' . TABLE_RISQUE . '" class="tabs" style="display:none;"><label tabindex="3">' . ucfirst(strtolower(__('Demande d\'action corrective', 'evarisk'))) . '</label></li>
					<li id="ongletSuiviActionCorrective' . TABLE_RISQUE . '" class="tabs" style="display:none;"><label tabindex="3">' . ucfirst(strtolower(__('Suivi des actions correctives', 'evarisk'))) . '</label></li>
				</ul>
				<div id="divVoirRisques" class="eva_tabs_panel">' . getVoirRisque ($tableElement, $idElement) . '</div>' . $divEditionRisque . '
				<div id="divDemandeAction' . TABLE_RISQUE . '" class="eva_tabs_panel" style="display:none"></div>
				<div id="divSuiviAction' . TABLE_RISQUE . '" class="eva_tabs_panel" style="display:none"></div>
				<div id="divAction' . TABLE_RISQUE . '" class="eva_tabs_panel" style="display:none"></div>';
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
	function getVoirRisque ($tableElement, $idElement)
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
			// $titres[] = ucfirst(strtolower(sprintf(__("description %s", 'evarisk'), __("du danger", 'evarisk'))));
			$titres[] = ucfirst(strtolower(sprintf(__("commentaire %s", 'evarisk'), __("sur le risque", 'evarisk'))));
			$titres[] = __("&Eacute;tat", 'evarisk');
			$titres[] = __("Actions", 'evarisk');
			$classes[] = 'columnQuotation';
			$classes[] = 'columnNomDanger';
			// $classes[] = 'columnDescriptionDanger';
			$classes[] = 'columnCommentaireRisque';
			$classes[] = 'columnEtat';
			$classes[] = 'columnAction';
			
			$scriptRisque = '';
			if(isset($risques) && ($risques != null))
			{
				foreach($risques as $risque)
				{
					$idligne = 'risque-' . $risque[0]->id;
					$scriptRisque = $scriptRisque . '<script type="text/javascript">
						$(document).ready(function() {
							$("#' . $idligne . '-edit").click(function(){
								$("#divFormRisque").html(\'<img src="' . PICTO_LOADING . '" />\');
								$("#ongletAjouterRisque").click();
								$("#divFormRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true",	"table":"' . TABLE_RISQUE . '", "act":"load", "idRisque": "' . $risque[0]->id . '", "idElement":"' . $idElement . '", "tableElement":"' . $tableElement . '"});
							});
							
							$("#' . $idligne . '-demandeAction").click(function(){
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
							});

							$("#' . $idligne . '-delete").click(function(){
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
					// $ligneDeValeurs[] = array('value' => nl2br($risque[0]->descriptionDanger), 'class' => '');
					$ligneDeValeurs[] = array('value' => nl2br($risque[0]->commentaire), 'class' => '');
					$ligneDeValeurs[] = array('value' => '', 'class' => '');
					// $ligneDeValeurs[] = array('value' => '<img id="' . $idligne . '-demandeAction" src="' . PICTO_LTL_ADD_ACTION . '" alt="' . _c('Demande AC|AC pour action corrective', 'evarisk') . '" title="' . __('Demande d\'action corrective', 'evarisk') . '"/><img id="' . $idligne . '-suiviAction" src="' . PICTO_LTL_SUIVI_ACTION . '" alt="' . _c('Suivi AC|AC pour action corrective', 'evarisk') . '" title="' . __('Suivi des actions correctives', 'evarisk') . '"/><img id="' . $idligne . '-edit" src="' . PICTO_EDIT . '" alt="' . __('Editer', 'evarisk') . '" title="' . __('Editer', 'evarisk') . '"/><img id="' . $idligne . '-delete" src="' . PICTO_DELETE . '" alt="' . __('Supprimer', 'evarisk') . '" title="' . __('Supprimer', 'evarisk') . '"/>', 'class' => '');
					$ligneDeValeurs[] = array('value' => '<img id="' . $idligne . '-edit" src="' . PICTO_EDIT . '" alt="' . __('Editer', 'evarisk') . '" title="' . __('Editer', 'evarisk') . '"/><img id="' . $idligne . '-delete" src="' . PICTO_DELETE . '" alt="' . __('Supprimer', 'evarisk') . '" title="' . __('Supprimer', 'evarisk') . '"/>', 'class' => '');
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
			$seuilRisque = Risque::getSeuil($scoreRisque);
			$niveauRisque = Risque::getNiveauRisque($seuilRisque);
			$scriptRisque = $scriptRisque . '
				<script type="text/javascript">
					$(document).ready(function() {
						$(".risqueText' . $tableElement . $idElement .'").each(function(){
							$(this).html("' . $niveauRisque . '");
							for(var i = 0; i <= 100; i++)
								$(this).removeClass("risque" + i + "Text");
							$(this).addClass("risque' . $seuilRisque . 'Text");
						});
						$(".risqueInfo' . $tableElement . $idElement .'").each(function(){
							$(this).html("' . $niveauRisque . '");
							for(var i = 0; i <= 100; i++)
								$(this).removeClass("risque" + i + "Info");
							$(this).addClass("risque' . $seuilRisque . 'Info");
						});
					});
				</script>';

			{//Script de définition de la dataTable
				$scriptVoirRisque = $scriptRisque . '
					<script type="text/javascript">
						$(document).ready(function() {
							$("#' . $idTable . '").dataTable({
								"sPaginationType": "full_numbers", 
								"bAutoWidth": false, 
								"aoColumns": [
									{ "bSortable": true, "sType": "numeric"},
									{ "bSortable": true},
									{ "bSortable": false},
									{ "bSortable": false},
									{ "bSortable": false }],
								"aaSorting": [[0,"desc"]]
							});
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
	function getFormulaireCreationRisque ($tableElement, $idElement, $idRisque = '')
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
		
		{// Champs cachés
			$formRisque = $formRisque . EvaDisplayInput::afficherInput('hidden', 'idRisque', $idRisque, '', null, 'idRisque', false, false);
		}
		
		{//Choix de la catégorie de dangers
			$nomRacine = 'Categorie Racine';
			$categorieRacine = categorieDangers::getCategorieDangerByName($nomRacine);
			$categoriesDangers = Arborescence::getDescendants(TABLE_CATEGORIE_DANGER, $categorieRacine);
			$radioGroup = '';
			if($risque[0] != null)
			{// Si l'on édite un risque, on sélectionne la bonne catégorie de dangers
				$selectionCategorie = $risque[0]->idCategorie;
			}
			else
			{// Sinon on sélectionne la racine
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
						
						/* Lorsqu'une catégorie de danger n'a pas de picto alors on affichait stoppait le script
						
						
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
		switch(OPTIONS_AVANCEEES_EVALUATION_RISQUE)
		{
			case 'collapsed':
			{// + Avancé
				$script = '
					<script type="text/javascript">
						$(document).ready(function(){
							$("#boutonAvanceRisque").toggle(
								function()
								{
									$("#avanceRisque").show();
									$(this).children("span:first").html("-");
								},
								function()
								{
									$("#avanceRisque").hide();
									$(this).children("span:first").html("+");
								}
							);
						});
					</script>';
				$formRisque = $formRisque . $script . '<div id="boutonAvanceRisque" class="alignright"><span>+</span> ' . __('Avanc&eacute;', 'evarisk') . '</div><br class="clear"/>';
				$displayDivAvanceRisque = 'none';
				break;
			}
			case 'expanded':
			{
				$displayDivAvanceRisque = 'block';
				break;
			}
			case 'invisible':
			{
				$displayDivAvanceRisque = 'none';
				break;
			}
		}
		$formRisque = $formRisque . '<div style="display:' . $displayDivAvanceRisque . ';" id="avanceRisque">';
		
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
			{// Si l'on édite un risque, on sélectionne le bon danger
				$selection = $risque[0]->idDanger;
				$selection = evaDanger::getDanger($selection);
			}
			else
			{// Sinon on sélectionne le premier danger de la catégorie
				$selection = (isset($dangers[0]) && ($dangers[0]->id))?$dangers[0]->id:null;
			}
			if($selection != null)
				$formRisque = $formRisque . '<div id="divDangerFormRisque">' . EvaDisplayInput::afficherComboBox($dangers, $idSelect = 'dangerFormRisque', $labelSelect = __('Dangers', 'evarisk') . ' : ', $nameSelect = 'danger', $valeurDefaut = '', $selection) . '</div><br />';
		}

		{//Choix de la méthode
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
			{// Si l'on édite un risque, on sélectionne la bonne méthode
				$idSelection = $risque[0]->id_methode;
			}
			else
			{// Sinon on sélectionne la première méthode
				$idSelection = $methodes[0]->id;
			}
			$selection = MethodeEvaluation::getMethod($idSelection);
			$formRisque = $script . $formRisque . EvaDisplayInput::afficherComboBox($methodes, $idSelect = 'methodeFormRisque', $labelSelect = __('M&eacute;thode d\'&eacute;valuation', 'evarisk') . ' : ', $nameSelect = 'methode', $valeurDefaut = '', $selection) . '<br />';
		}
		$formRisque = $formRisque . '</div> <!--/avanceRisque-->';
		
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
			if($risque[0] != null)
			{// Si l'on édite un risque, on remplit l'aire de texte avec sa description
				$contenuInput = $risque[0]->commentaire;
			}
			else
			{// Sinon on ne met rien dans l'aire de texte
				$contenuInput = '';
			}
			$labelInput = ucfirst(strtolower(sprintf(__("commentaire %s", 'evarisk'), __('sur le risque', 'evarisk'))));
			$labelInput[1] = ($labelInput[0] == "&")?ucfirst($labelInput[1]):$labelInput[1];
			$formRisque = $formRisque . '<br />' . '<div id="divDescription" class="clear" >' . EvaDisplayInput::afficherInput($type='textarea', $id='descriptionFormRisque', $contenuInput, $contenuAide='', $labelInput . ' : ', $nomChamps='description', $grise=false, DESCRIPTION_RISQUE_OBLIGATOIRE, $taille = 3, $classe='', $limitation='', $width='100%', $script='') . '</div>';
		}
		
		{//Bouton enregistrer
			$allVariables = MethodeEvaluation::getAllVariables();
			$idBouttonEnregistrer = 'enregistrerFormRisque';
			$scriptEnregistrement = '<script type="text/javascript">
				$(document).ready(function() {	
					$(\'#' . $idBouttonEnregistrer . '\').click(function() {
						var variables = new Array();';
			foreach($allVariables as $variable)
			{
				$scriptEnregistrement = $scriptEnregistrement . '
						variables["' . $variable->id . '"] = $("#var' . $variable->id . 'FormRisque").val();';
			}
			$scriptEnregistrement = $scriptEnregistrement . '
						$("#divFormRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
							"post":"true", 
							"table":"' . TABLE_RISQUE . '", 
							"act":"save", 
							"tableElement":"' . $tableElement . '",
							"idElement":"' . $idElement . '", 
							"idDanger":$("#dangerFormRisque").val(), 
							"idMethode":$("#methodeFormRisque").val(), 
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
			$formRisque = $formRisque . EvaDisplayInput::afficherInput('button', $idBouttonEnregistrer, 'Enregistrer', null, '', 'save', false, false, '', 'button-primary alignright', '', '', $scriptEnregistrement);
		}
		
		$formRisque = $formRisque . '</div> <!--/needDangerCategory-->';
		$formRisque = $formRisque . EvaDisplayInput::fermerForm('formRisque');
		
		return $formRisque;
	}
?>