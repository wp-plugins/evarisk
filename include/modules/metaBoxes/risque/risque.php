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


	$postBoxTitle = __('Risques', 'evarisk');
	/*	If the postBoxId change don't forget to replace each iteration in this script	*/
	$postBoxId = 'postBoxRisques';
	$postBoxCallbackFunction = 'getRisquesPostBoxBody';
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_UNITES_DE_TRAVAIL, 'rightSide', 'default');
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_GROUPEMENTS, 'rightSide', 'default');


	function getRisquesPostBoxBody($element){
		$tableElement = $element['tableElement'];
		$idElement = $element['idElement'];
		if($idElement != null){
			$scriptRisque = '<script type="text/javascript">
					var TABLE_RISQUE = "' . TABLE_RISQUE . '";

					digirisk(document).ready(function(){
						//	Show the risk list for the actual element
						digirisk("#ongletVoirLesRisques").click(function(){
							digirisk("#divVoirRisques").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",{
								"post":"true",
								"table":"' . TABLE_RISQUE . '",
								"act":"reloadVoirRisque",
								"tableElement":"' . $tableElement . '",
								"idElement":' . $idElement . '
							});
							tabChange("#divVoirRisques", "#ongletVoirLesRisques");
							hideExtraTab();
						});

						//	Show the existing corrective action on the actual element
						digirisk("#ongletSuiviFicheActionCorrective' . TABLE_RISQUE . '").click(function(){
							digirisk("#divSuiviAction' . TABLE_RISQUE . '").html(digirisk("#loadingImg").html());
							digirisk("#divSuiviAction' . TABLE_RISQUE . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",{
								"post":"true",
								"tableElement":"' . $tableElement . '",
								"idElement":"' . $idElement . '",
								"nom":"suiviFicheAction"
							});
							tabChange("#divSuiviAction' . TABLE_RISQUE . '", "#ongletSuiviFicheActionCorrective' . TABLE_RISQUE . '");
							hideExtraTab();
							digirisk("#ongletSuiviFicheActionCorrective' . TABLE_RISQUE . '").css("display","inline");
						});

						//	Show the existing corrective action on the actual element
						digirisk("#ongletHistoRisque' . TABLE_RISQUE . '").click(function(){
							digirisk("#divHistoRisk' . TABLE_RISQUE . '").html(digirisk("#loadingImg").html());
							digirisk("#divHistoRisk' . TABLE_RISQUE . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",{
								"post":"true",
								"tableElement":"' . $tableElement . '",
								"idElement":"' . $idElement . '",
								"nom":"histo-risk"
							});
							tabChange("#divHistoRisk' . TABLE_RISQUE . '", "#ongletHistoRisque' . TABLE_RISQUE . '");
							hideExtraTab();
							digirisk("#ongletHistoRisque' . TABLE_RISQUE . '").css("display","inline");
						});

						//	Output the form to add a new risk
						digirisk("#ongletAjouterRisque, #addRisqNormalMode").click(function(){
							digirisk("#risqManagementselector div").each(function(){
								digirisk(this).show();
								digirisk(this).removeClass("selected");
							});
							digirisk("#addRisqNormalMode").addClass("selected");
							digirisk("#formRisque").html(digirisk("#loadingImg").html());
							digirisk("#formRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",{
								"post":"true",
								"table":"' . TABLE_RISQUE . '",
								"act":"reloadRiskForm",
								"tableElement":"' . $tableElement . '",
								"idElement":"' . $idElement . '",
								"idRisque": ""
							});
							tabChange("#formRisque", "#ongletAjouterRisque");
							hideExtraTab();
							jQuery("#ongletAjouterRisque").show();
							digirisk("#divDangerContainer :radio").each(function(){
								digirisk(this).prop("checked", "");
							});
							digirisk("#divDangerContainer").css("display", "block");
							digirisk("#divDangerContainerSwitch").css("display", "none");
							digirisk("#historisationContainer").hide();
							digirisk("#associatedPictureContainer").hide();
							digirisk("#divFormRisque").show();
						});

						digirisk("#ongletAjouterRisquePhoto, #addRisqAdvancedMode").click(function(){
							digirisk("#risqManagementselector div").each(function(){
								digirisk(this).show();
								digirisk(this).removeClass("selected");
							});
							digirisk("#addRisqAdvancedMode").addClass("selected");
							digirisk("#formRisque").html(digirisk("#loadingImg").html());
							digirisk("#formRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",{
								"post":"true",
								"table":"' . TABLE_RISQUE . '",
								"act":"loadAdvancedRiskForm",
								"tableElement":"' . $tableElement . '",
								"idElement":"' . $idElement . '",
								"idRisque": ""
							});
							tabChange("#formRisque", "#ongletAjouterRisque");
							hideExtraTab();
							jQuery("#ongletAjouterRisque").show();
							digirisk("#divFormRisque").show();
						});

						digirisk("#risqMassUpdater").dialog({
							autoOpen:false,
							height:600,
							width:800,
							modal:true
						});
						digirisk("#ongletMassUpdate' . TABLE_RISQUE . '").click(function(){
							digirisk("#risqMassUpdater").html(digirisk("#loadingImg").html());
							digirisk("#risqMassUpdater").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",{
								"post":"true",
								"table":"' . TABLE_RISQUE . '",
								"act":"loadRisqMassUpdater",
								"tableElement":"' . $tableElement . '",
								"idElement":"' . $idElement . '"
							});
							digirisk("#risqMassUpdater").dialog("open");
						});
					});
				</script>';


			$liAjoutRisque = '
					<li id="ongletAjouterRisque" class="tabs" style="display:inline"><label tabindex="2">' . ucfirst(strtolower(sprintf(__('Ajouter %s', 'evarisk'), __('un risque', 'evarisk')))) . '</label></li>';
			$liEditionRisque = '
					<li id="ongletEditerRisque" class="tabs" style="display:none"><label tabindex="2">' . ucfirst((sprintf(__('&Eacute;diter %s', 'evarisk'), __('un risque', 'evarisk')))) . '</label></li>';
			$liControlAskAction = '
					<li id="ongletControlerActionDemandee" class="tabs" style="display:none"><label tabindex="2">' . ucfirst((sprintf(__('Contr&ocirc;le %s', 'evarisk'), __('d\'une action demand&eacute;e', 'evarisk')))) . '</label></li>';
			$divEditionRisque = '
<div id="divFormRisque" class="eva_tabs_panel hide" >';
			if((digirisk_options::getOptionValue('risques_avances') == 'oui') && ($idRisque == '')){
				$divEditionRisque .= '
<div class="clear" id="risqManagementselector" >
	<div class="alignleft selected" id="addRisqNormalMode" >' . ucfirst(strtolower(__('Mode simple', 'evarisk'))) . '</div>
	<div class="alignleft" id="addRisqAdvancedMode" >' . ucfirst(strtolower(__('Mode avanc&eacute; (par photo)', 'evarisk'))) . '</div>
</div>';
			}
			$divEditionRisque .=
'<div class="clear" >&nbsp;</div>
<div class="clear" id="formRisque" >&nbsp;</div>
</div>';

			switch($tableElement)
			{
				case TABLE_GROUPEMENT:
					if(!current_user_can('digi_edit_groupement') && !current_user_can('digi_edit_groupement_' . $idElement))
					{
						$liAjoutRisque = $liEditionRisque = $divEditionRisque = '';
					}
				break;
				case TABLE_UNITE_TRAVAIL:
					if(!current_user_can('digi_edit_unite') && !current_user_can('digi_edit_unite_' . $idElement))
					{
						$liAjoutRisque = $liEditionRisque = $divEditionRisque = '';
					}
				break;
			}

			$taskList = actionsCorrectives::get_activity_associated_to_risk($tableElement, $idElement);
			$liSuiviActionCorrective = '';
			if((count($taskList) > 0) && current_user_can('digi_follow_action')){
				$liSuiviActionCorrective = '<li id="ongletSuiviFicheActionCorrective' . TABLE_RISQUE . '" class="tabs" style="display:inline" ><label tabindex="4">' . ucfirst(strtolower(__('Suivi des actions correctives', 'evarisk'))) . '</label></li>';
			}

			$temp = Risque::getRisques($tableElement, $idElement, "Valid");
			$liHistoRisque = '';
			if((count($temp) > 0) && current_user_can('digi_view_risk_histo')){
				$liHistoRisque = '<li id="ongletHistoRisque' . TABLE_RISQUE . '" class="tabs" style="display:inline" ><label tabindex="5">' . ucfirst(strtolower(__('Historique des risques', 'evarisk'))) . '</label></li>';
			}

			$corpsPostBoxRisque = $scriptRisque . '
				<div class="hide" id="risqMassUpdater" title="' . __('V&eacute;rification en masse de l\'&eacute;valuation', 'evarisk') . '" >&nbsp;</div>
				<div id="message' . TABLE_RISQUE . '" class="updated fade hide" ></div>
				<ul class="eva_tabs" style="margin-bottom:2px;" >
					<li id="ongletVoirLesRisques" class="tabs selected_tab" style="display:inline; margin-left:0.4em;"><label tabindex="1">' . ucfirst(strtolower(sprintf(__('voir %s', 'evarisk'), __('les risques', 'evarisk')))) . '</label></li>' . $liAjoutRisque . $liEditionRisque . '
					' . $liSuiviActionCorrective . $liControlAskAction . $liHistoRisque . '
					<li id="ongletDemandeActionCorrective' . TABLE_RISQUE . '" class="tabs" style="display:none;"><label tabindex="6">' . ucfirst(strtolower(__('Demande d\'action corrective', 'evarisk'))) . '</label></li>
					<li id="ongletSuiviActionCorrective' . TABLE_RISQUE . '" class="tabs" style="display:none;"><label tabindex="7">' . ucfirst(strtolower(__('Suivi des actions correctives', 'evarisk'))) . '</label></li>
					<li id="ongletFicheActionCorrective' . TABLE_RISQUE . '" class="tabs" style="display:none;"><label tabindex="8">' . ucfirst(strtolower(__('Contr&ocirc;le des actions corrective', 'evarisk'))) . '</label></li>
					<li id="ongletHistoRisk' . TABLE_RISQUE . '" class="tabs" style="display:none;"><label tabindex="9">' . ucfirst(strtolower(__('Historique du risque', 'evarisk'))) . '</label></li>';
			if($tableElement == TABLE_GROUPEMENT){
				$corpsPostBoxRisque .=
					'<li id="ongletMassUpdate' . TABLE_RISQUE . '" class="tabs" ><label tabindex="8">' . ucfirst(strtolower(__('Vue d\'ensemble', 'evarisk'))) . '</label></li>';
			}
			$corpsPostBoxRisque .=
				'</ul>
				<div id="divVoirRisques" class="eva_tabs_panel" >' . getVoirRisque($tableElement, $idElement) . '</div>' . $divEditionRisque . '
				<div id="divDemandeAction' . TABLE_RISQUE . '" class="eva_tabs_panel" style="display:none"></div>
				<div id="divSuiviAction' . TABLE_RISQUE . '" class="eva_tabs_panel" style="display:none"></div>
				<div id="divAction' . TABLE_RISQUE . '" class="eva_tabs_panel" style="display:none"></div>
				<div id="divFicheAction' . TABLE_RISQUE . '" class="eva_tabs_panel" style="display:none"></div>
				<div id="divHistoRisk' . TABLE_RISQUE . '" class="eva_tabs_panel" style="display:none"></div>';
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
	* Cr?ation de l'affichage global
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
		{//Cr?ation de la table
			unset($titres,$classes, $idLignes, $lignesDeValeurs);
			$idLignes = null;
			$idTable = 'tableRisque' . $tableElement . $idElement;
			$titres[] = __("Id.", 'evarisk');
			$titres[] = __("Quotation", 'evarisk');
			$titres[] = ucfirst(strtolower(sprintf(__("nom %s", 'evarisk'), __("du danger", 'evarisk'))));
			$titres[] = ucfirst(strtolower(sprintf(__("commentaire %s", 'evarisk'), __("sur le risque", 'evarisk'))));
			$titres[] = __("Actions", 'evarisk');
			$classes[] = 'columnRId';
			$classes[] = 'columnQuotation';
			$classes[] = 'columnNomDanger';
			$classes[] = 'columnCommentaireRisque';
			$classes[] = 'columnAction';

			$scriptRisque = '';
			if(isset($risques) && ($risques != null)){
				foreach($risques as $risque){
					$idligne = 'risque-' . $risque[0]->id;
					$scriptRisque .= '
<script type="text/javascript">
	digirisk(document).ready(function(){';

					if(digirisk_options::getOptionValue('action_correctives_avancees') == 'oui'){
						$scriptRisque .= '
	digirisk("#' . $idligne . '-demandeAction").click(function(){
		tabChange("#divDemandeAction' . TABLE_RISQUE . '", "#ongletDemandeActionCorrective' . TABLE_RISQUE . '");
		hideExtraTab();
		digirisk("#ongletDemandeActionCorrective' . TABLE_RISQUE . '").css("display","inline");
		digirisk("#divDemandeAction' . TABLE_RISQUE . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "nom":"demandeAction","tableElement":"' . $tableElement . '","idElement":"' . $idElement . '", "tableProvenance":"' . TABLE_RISQUE . '", "idProvenance": "' . $risque[0]->id . '"});
		digirisk("#divDemandeAction' . TABLE_RISQUE . '").html(digirisk("#loadingImg").html());
	});';
					}

					$scriptRisque .= '
		digirisk("#' . $idligne . '-suiviAction").click(function(){
			tabChange("#divSuiviAction' . TABLE_RISQUE . '", "#ongletSuiviActionCorrective' . TABLE_RISQUE . '");
			hideExtraTab();
			digirisk("#ongletSuiviActionCorrective' . TABLE_RISQUE . ' label").html("' . sprintf(__('Actions correctives pour %s', 'evarisk'), ELEMENT_IDENTIFIER_R . $risque[0]->id . '&nbsp;-&nbsp;' . $risque[0]->nomDanger) . '");
			digirisk("#ongletSuiviActionCorrective' . TABLE_RISQUE . '").css("display","inline");
			digirisk("#divSuiviAction' . TABLE_RISQUE . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "nom":"suiviAction",	"tableProvenance":"' . TABLE_RISQUE . '", "idProvenance": "' . $risque[0]->id . '"});
			digirisk("#divSuiviAction' . TABLE_RISQUE . '").html(digirisk("#loadingImg").html());
		});
	});
</script>';
					$idLignes[] = $idligne;

					$idMethode = $risque[0]->id_methode;
					$score = Risque::getScoreRisque($risque);
					$quotation = Risque::getEquivalenceEtalon($idMethode, $score, $risque[0]->date);
					$niveauSeuil = Risque::getSeuil($quotation);

					unset($ligneDeValeurs);
					$ligneDeValeurs[] = array('value' => ELEMENT_IDENTIFIER_R . $risque[0]->id, 'class' => '');
					$ligneDeValeurs[] = array('value' => $quotation, 'class' => 'Seuil_' . $niveauSeuil);
					$ligneDeValeurs[] = array('value' => $risque[0]->nomDanger, 'class' => '');
					$ligneDeValeurs[] = array('value' => nl2br($risque[0]->commentaire), 'class' => '');
					$more_action = '';
					if(digirisk_options::getOptionValue('action_correctives_avancees') == 'oui'){
						if(current_user_can('digi_add_task')){
							$more_action .= '<img style="width:' . TAILLE_PICTOS . ';" id="' . $idligne . '-demandeAction" src="' . PICTO_LTL_ASK_ACTION . '" alt="' . _c('Demande AC|AC pour action corrective', 'evarisk') . '" title="' . __('Demande d\'action corrective', 'evarisk') . '"/>';
						}
						if(current_user_can('digi_follow_action')){
							$more_action .= '<img style="width:' . TAILLE_PICTOS . ';" id="' . $idligne . '-suiviAction" src="' . PICTO_LTL_SUIVI_ACTION . '" alt="' . _c('Suivi AC|AC pour action corrective', 'evarisk') . '" title="' . __('Suivi des actions correctives', 'evarisk') . '"/>';
						}
					}
					if(current_user_can('digi_control_task')){
						$more_action .= '<img style="width:' . TAILLE_PICTOS . ';" id="' . $idligne . '-FAC" src="' . PICTO_LTL_ADD_ACTION . '" alt="' . __('Action de contr&ocirc;le', 'evarisk') . '" title="' . __('Action de contr&ocirc;le', 'evarisk') . '" class="simple-FAC" />';
					}

					if(current_user_can('digi_view_risk_histo')){
						$more_action .= '<img style="width:' . TAILLE_PICTOS . ';" id="' . $idligne . '-histo_risk" src="' . DIGI_PICTO_HISTO_RISK . '" alt="' . _c('&Eacute;volution du risque', 'evarisk') . '" title="' . __('&Eacute;volution du risque', 'evarisk') . '" class="risk-histo" />';
					}

					switch($tableElement){
						case TABLE_GROUPEMENT:
							if(current_user_can('digi_edit_groupement') || current_user_can('digi_edit_groupement_' . $idElement)){
								$ligneDeValeurs[] = array('value' => $more_action . '<img style="width:' . TAILLE_PICTOS . ';" id="' . $idligne . '-edit" src="' . PICTO_EDIT . '" alt="' . __('Editer', 'evarisk') . '" title="' . __('Editer', 'evarisk') . '" class="edit-risk" /><img style="width:' . TAILLE_PICTOS . ';" id="' . $idligne . '-delete" src="' . PICTO_DELETE . '" alt="' . __('Supprimer', 'evarisk') . '" title="' . __('Supprimer', 'evarisk') . '" class="delete-risk" />', 'class' => 'risk_line_action');
							}
							else{
								$ligneDeValeurs[] = array('value' => '', 'class' => '');
							}
						break;
						case TABLE_UNITE_TRAVAIL:
							if(current_user_can('digi_edit_unite') || current_user_can('digi_edit_unite_' . $idElement)){
								$ligneDeValeurs[] = array('value' => $more_action . '<img style="width:' . TAILLE_PICTOS . ';" id="' . $idligne . '-edit" src="' . PICTO_EDIT . '" alt="' . __('Editer', 'evarisk') . '" title="' . __('Editer', 'evarisk') . '" class="edit-risk" /><img style="width:' . TAILLE_PICTOS . ';" id="' . $idligne . '-delete" src="' . PICTO_DELETE . '" alt="' . __('Supprimer', 'evarisk') . '" title="' . __('Supprimer', 'evarisk') . '" class="delete-risk" />', 'class' => 'risk_line_action');
							}
							else{
								$ligneDeValeurs[] = array('value' => '', 'class' => '');
							}
						break;
					}

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
					$scoreRisque = eva_UniteDeTravail::getScoreRisque($idElement);
					break;
			}

			$scoreRisqueUniteTravail = 0;
			$riskAndSubRisks = eva_documentUnique::listRisk($tableElement, $idElement);
			foreach($riskAndSubRisks as $risk){
				$scoreRisqueUniteTravail += $risk[2]['value'];
			}
			$nombreRisqueUniteTravail = count($riskAndSubRisks);

			{//Script de d?finition de la dataTable
				$scriptVoirRisque = $scriptRisque . '
<script type="text/javascript">
	digirisk(document).ready(function() {
		jQuery(".edit-risk").click(function(){
			jQuery("#formRisque").html(jQuery("#loadingImg").html());
			jQuery("#ongletEditerRisque").show();
			tabChange("#formRisque", "#ongletEditerRisque");
			jQuery("#formRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",{
				"post":"true",
				"table":"' . TABLE_RISQUE . '",
				"act":"load",
				"idRisque": jQuery(this).attr("id").replace("risque-", "").replace("-edit", ""),
				"idElement":"' . $idElement . '",
				"tableElement":"' . $tableElement . '"
			});
			jQuery("#divFormRisque").show();
			jQuery("#risqManagementselector div").each(function(){
				jQuery(this).hide();
			});
		});
		digirisk(".delete-risk").click(function(){
			var nameDanger = digirisk(this).closest("tr").children("td").eq(1).html();
			var commentaireRisque = digirisk(this).closest("tr").children("td").eq(2).html().replace("<br>", "\r\n");
			if(confirm("' . __('Etes vous sur de vouloir supprimer cet enregistrement?', 'evarisk') . '\r\n" + nameDanger + "\r\n\t" + commentaireRisque)){
				digirisk("#divAction' . TABLE_RISQUE . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",
				{
					"post":"true",
					"table":"' . TABLE_RISQUE . '",
					"act":"delete",
					"idRisque": digirisk(this).attr("id").replace("risque-", "").replace("-delete", ""),
					"idElement":"' . $idElement . '",
					"tableElement":"' . $tableElement . '"
				});
			}
		});
		digirisk(".simple-FAC").click(function(){
			tabChange("#divFicheAction' . TABLE_RISQUE . '", "#ongletFicheActionCorrective' . TABLE_RISQUE . '");
			hideExtraTab();
			digirisk("#ongletFicheActionCorrective' . TABLE_RISQUE . '").css("display","inline");
			digirisk("#divFicheAction' . TABLE_RISQUE . '").html(digirisk("#loadingImg").html());
			digirisk("#divFicheAction' . TABLE_RISQUE . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",
			{
				"post":"true",
				"tableElement":"' . $tableElement . '",
				"idElement":"' . $idElement . '",
				"nom":"ficheAction",
				"tableProvenance":"' . TABLE_RISQUE . '",
				"idProvenance": digirisk(this).attr("id").replace("risque-", "").replace("-FAC", "")
			});
		});

		digirisk(".risk-histo").click(function(){
			tabChange("#divHistoRisk' . TABLE_RISQUE . '", "#ongletHistoRisk' . TABLE_RISQUE . '");
			hideExtraTab();
			digirisk("#ongletHistoRisk' . TABLE_RISQUE . '").css("display","inline");
			digirisk("#divHistoRisk' . TABLE_RISQUE . '").html(digirisk("#loadingImg").html());
			digirisk("#divHistoRisk' . TABLE_RISQUE . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",{
				"post":"true",
				"tableElement":"' . $tableElement . '",
				"idElement":"' . $idElement . '",
				"nom":"histo-risk",
				"tableProvenance":"' . TABLE_RISQUE . '",
				"idProvenance": digirisk(this).attr("id").replace("risque-", "").replace("-histo_risk", ""),
				"output_mistake":""
			});
		});

		//	Update The risk number and score in the different part of the screen
		digirisk("#riskSum' . $tableElement . $idElement .'").html("' . $scoreRisqueUniteTravail . '");
		digirisk("#riskNb' . $tableElement . $idElement .'").html("' . $nombreRisqueUniteTravail . '");
		digirisk("#LeftRiskSum' . $tableElement . $idElement .'").html("' . $scoreRisqueUniteTravail . '");
		digirisk("#LeftRiskNb' . $tableElement . $idElement .'").html("' . $nombreRisqueUniteTravail . '");

		digirisk("#' . $idTable . '").dataTable({
			"sPaginationType": "full_numbers",
			"bAutoWidth": false,
			"bInfo": false,
			"aoColumns": [
				{ "bSortable": false},
				{ "bSortable": true, "sType": "numeric"},
				{ "bSortable": true},
				{ "bSortable": false},
				{ "bSortable": false }],
			"aaSorting": [[0,"desc"]],
			"oLanguage": {
				"sSearch": "<span class=\'ui-icon searchDataTableIcon\' >&nbsp;</span>",
				"sEmptyTable": "' . __('Aucun risque trouv&eacute;', 'evarisk') . '",
				"sLengthMenu": "' . __('Afficher _MENU_ risques', 'evarisk') . '",
				"sInfoEmpty": "' . __('Aucun risque', 'evarisk') . '",
				"sZeroRecords": "' . __('Aucun risque trouv&eacute;', 'evarisk') . '",
				"oPaginate": {
					"sFirst": "' . __('Premi&eacute;re', 'evarisk') . '",
					"sLast": "' . __('Derni&egrave;re', 'evarisk') . '",
					"sNext": "' . __('Suivante', 'evarisk') . '",
					"sPrevious": "' . __('Pr&eacute;c&eacute;dente', 'evarisk') . '"
				}
			}
		});
		digirisk("#' . $idTable . ' tfoot").remove();
		digirisk("#' . $idTable . '_wrapper").removeClass("dataTables_wrapper");
	});
</script>';
			}

			$voirRisque = EvaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $scriptVoirRisque);

			return $voirRisque;
		}
	}

	/*
	* Cr?ation du formulaire d'ajout/?dition
	*/
	function getFormulaireCreationRisque($tableElement, $idElement, $idRisque = '', $formId = '', $methode =''){
		global $wpdb;

		$divDangerContainerStyle = $script = '';
		$divDangerContainerSwitchStyle = ' style="display:none;" ';
		if($idRisque != ''){
			$risque = Risque::getRisque($idRisque);
			$divDangerContainerStyle = ' style="display:none;" ';
			$divDangerContainerSwitchStyle = '';
		}
		else{
			$risque = null;
		}

		$sub_action = (!empty($_REQUEST['sub_action'])?digirisk_tools::IsValid_Variable($_REQUEST['sub_action']):'');
		$task_to_associate = (!empty($_REQUEST['task_to_associate'])?digirisk_tools::IsValid_Variable($_REQUEST['task_to_associate']):'');

		{//Choix de la cat?gorie de dangers
			$categorieDanger = categorieDangers::getCategorieDangerForRiskEvaluation($risque, $formId);
			$script .= $categorieDanger['script'];
			$selectionCategorie = $categorieDanger['selectionCategorie'];
		}
		{//Choix du danger
			$ListDanger = evaDanger::getDangerForRiskEvaluation($selectionCategorie, $risque, $formId);
			$script .= $ListDanger['script'];
		}

		$formRisque =
EvaDisplayInput::ouvrirForm('POST', $formId . 'formRisque-', $formId . 'formRisque-') .
EvaDisplayInput::afficherInput('hidden', $formId . 'idRisque', $idRisque, '', null, 'idRisque', false, false);
		if(($sub_action != 'control_asked_action') || ($task_to_associate <= 0)){
			$formRisque .= '
	<div>
		<div id="' . $formId . 'divDangerContainerSwitch" ' . $divDangerContainerSwitchStyle . ' class="pointer" >
			<img id="' . $formId . 'divDangerContainerSwitchPic" src="' . PICTO_EXPAND . '" alt="' . __('collapsor', 'evarisk') . '" style="vertical-align:middle;" />
			<span style="vertical-align:middle;" >' . __('Voir les dangers', 'evarisk') . '</span>
		</div>
		<div id="' . $formId . 'divDangerContainer" ' . $divDangerContainerStyle . ' >' . $categorieDanger['list'] . $ListDanger['list'] . '</div>
	</div>';
		}
		else{
			$formRisque .= EvaDisplayInput::afficherInput('hidden', $formId . 'dangerFormRisque', $risque[0]->idDanger, '', '', 'danger');

			$task = new EvaTask();
			$task->setId($task_to_associate);
			$task->load();

			$formRisque .= '
				<fieldset class="asked_action_control" >
					<legend>' . sprintf(__('Vous &ecirc;tes sur le point de r&eacute;aliser le contr&ocirc;le de l\'action %s', 'evarisk'), '<span class="bold" >' . ELEMENT_IDENTIFIER_T . $task_to_associate . '&nbsp;-&nbsp;' . $task->name . '</span>') . '</legend>
					<div class="asked_action_control_details" >&nbsp;</div>
					<div class="asked_action_control_efficiency" >' . sprintf(__('Efficacit&eacute; de la t&acirc;che %s', 'evarisk'), '<input type="text" name="correctiv_action_efficiency_control" id="correctiv_action_efficiency_control' . $task_to_associate . '" value="0" class="correctiv_action_efficiency_control" readonly="readonly" />%') . '<div id="correctiv_action_efficiency_control_slider' . $task_to_associate . '" class="correctiv_action_efficiency_control_slider" >&nbsp;</div></div>
				</fieldset>
				<script type="text/javascript" >
					digirisk(document).ready(function(){
						jQuery(".asked_action_control_details").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
							"post": "true",
							"table": "' . TABLE_TACHE . '",
							"id": "' . TABLE_TACHE . '_t_elt_' . $task_to_associate . '",
							"act": "load_details_simple"
						});
						jQuery(".correctiv_action_efficiency_control_slider").slider({
							value:0,
							min: 0,
							max: 100,
							step: 1,
							slide: function (event, ui) {
								jQuery("#" + jQuery(this).attr("id").replace("correctiv_action_efficiency_control_slider", "correctiv_action_efficiency_control")).val( ui.value );
							}
						});
					});
				</script>';
		}

		{/*	Get method list	*/
			$methodes = MethodeEvaluation::getMethods('Status="Valid"');
			if($risque[0] != null)// Si l'on �dite un risque, on s�lectionne la bonne m�thode
				$idSelection = $risque[0]->id_methode;
			else {// Sinon on s�lectionne la premi�re m�thode
				$idSelection = $methodes[0]->id;
			}
			$output_method = array();

			$methode_output_value = $methode_output = array();
			foreach($methodes as $methode){
				$method_vars = MethodeEvaluation::getVariablesMethode($methode->id);
				if(!empty($method_vars)){
					$methode_output_value[] = $methode->id;
					$methode_output[] = ELEMENT_IDENTIFIER_ME . $methode->id . ' - ' .  $methode->nom;
					$output_method[]=$methode;
					if ( ($methode->default_methode == 'yes') && empty($risque[0]) ) {
						$idSelection = $methode->id;
					}
				}
			}
		}

		if(($sub_action != 'control_asked_action') || ($task_to_associate <= 0)){//Choix de la m�thode
			$script .= '
			digirisk("#' . $formId . 'methodeFormRisque").change(function(){
				digirisk("#' . $formId . 'divVariablesFormRisque").html(digirisk("#loadingImg").html());
				digirisk("#' . $formId . 'divVariablesFormRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_METHODE . '", "act":"reloadVariables", "idMethode":digirisk("#' . $formId . 'methodeFormRisque").val(), "idRisque": "' . $idRisque . '"});
			});';
			$selection = MethodeEvaluation::getMethod($idSelection);
			$nombreMethode = count($output_method);
			$afficheSelecteurMethode = '';
			if($nombreMethode <= 1){
				$afficheSelecteurMethode = ' display:none; ';
			}
			$formRisque .= '<div id="choixMethodeEvaluation" style="' . $afficheSelecteurMethode . '" >' . EvaDisplayInput::afficherComboBox($output_method, $formId . 'methodeFormRisque', __('M&eacute;thode d\'&eacute;valuation', 'evarisk') . ' : ', 'methode', '', $selection, $methode_output_value, $methode_output) . '</div>';
		}
		else{
			$formRisque .= EvaDisplayInput::afficherInput('hidden', $formId . 'methodeFormRisque', $idSelection, '', '', 'methode');
		}

		{//Evaluation des variables
			$formRisque .=
'<script type="text/javascript">
	digirisk(document).ready(function(){
		digirisk("#' . $formId . 'divVariablesFormRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_METHODE . '", "tableElement":"' . $tableElement . '",  "idElement":"' . $idElement . '", "act":"reloadVariables", "idMethode":digirisk("#' . $formId . 'methodeFormRisque").val(), "idRisque": "' . $idRisque . '", "formId": "' . $formId . '"});
	})
</script>
<div id="' . $formId . 'divVariablesFormRisque" class="clear" ></div><!-- /' . $formId . 'divVariablesFormRisque -->';
		}

		{//Description
			$contenuInput = '';
			if($risque[0] != null)
			{// Si l'on ?dite un risque, on remplit l'aire de texte avec sa description
				$contenuInput = $risque[0]->commentaire;
			}
			$labelInput = ucfirst(strtolower(sprintf(__("commentaire %s", 'evarisk'), __('sur le risque', 'evarisk'))));
			$labelInput[1] = ($labelInput[0] == "&")?ucfirst($labelInput[1]):$labelInput[1];
			$formRisque .= '<br/><div id="' . $formId . 'divDescription" class="clear risk_description_container" >' . EvaDisplayInput::afficherInput('textarea', $formId . 'descriptionFormRisque', $contenuInput, '', $labelInput . ' : ', 'description_risque', false, DESCRIPTION_RISQUE_OBLIGATOIRE, 3, '', '', '95%', '') . '</div>';
		}

		if(current_user_can('digi_add_task')){//Preconisation (action prioritaire)
			$contenuInput = '';
			$labelInput = ucfirst(strtolower(__("Ajouter une action corrective pour le risque", 'evarisk')));
			$formRisque .= '<br/><div id="divPreconisation" class="clear" >' . EvaDisplayInput::afficherInput('textarea', $formId . 'preconisationRisque', $contenuInput, '', $labelInput . ' : ', $formId . 'preconisationRisque', false, DESCRIPTION_RISQUE_OBLIGATOIRE, 3, '', '', '95%', '') . '</div>';
		}
		if(current_user_can('digi_view_correctiv_action') && ($risque[0] != null) && (($sub_action != 'control_asked_action') || ($task_to_associate <= 0))){
			$formRisque .= '<div id="' . $idElement . 'divPreconisationExistante" class="clear" >&nbsp;</div>';
		}

		if(($sub_action != 'control_asked_action') || ($task_to_associate <= 0)){//Photo associ?e au risque
			if($idRisque != ''){
				$pictureAssociated = evaPhoto::getPhotos(TABLE_RISQUE, $idRisque);
				if(count($pictureAssociated) > 0){
					$formRisque .= '<div class="alignleft pointer" id="' . $idElement . 'associatedPictureContainer" style="width:90%;" >' . __('Photo associ&eacute;e &agrave; ce risque', 'evarisk') . '<div id="' . $idElement . 'deletePictureAssociation" ><span class="ui-icon deleteLinkBetwwenRiskAndPicture alignleft" title="' . __('Supprimer cette liaison', 'evarisk') . '" >&nbsp;</span>' . __('Supprimer l\'association', 'evarisk') . '</div><img class="alignleft riskPictureThumbs" src="' . EVA_GENERATED_DOC_URL . $pictureAssociated[0]->photo . '" alt="picture to associated to this risk unvailable" /></div>';
					$script .= '
		digirisk("#' . $idElement . 'deletePictureAssociation").click(function(){
			digirisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",
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

		if(current_user_can('digi_not_historicize_risk') && (($sub_action != 'control_asked_action') || ($task_to_associate <= 0)) && ($idRisque != '')){//Historisation du risque
			$formRisque .= '<div class="alignright" id="' . $idElement . 'historisationContainer" ><input type="checkbox" value="non" name="' . $idElement . 'historisation" id="' . $idElement . 'historisation" /><label for="historisation" >' . __('Ne pas afficher l\'ancienne cotation dans les historiques de modifications','evarisk') . '</label></div>';
		}

		{//Bouton enregistrer
			$allVariables = MethodeEvaluation::getAllVariables();
			$idBouttonEnregistrer = 'enregistrerFormRisque' . $formId;
			$scriptEnregistrement =
'<script type="text/javascript">
	digirisk(document).ready(function(){
		//	Change the state of the danger container
		digirisk("#' . $formId . 'divDangerContainerSwitch").click(function(){
			if(digirisk("#' . $formId . 'divDangerContainerSwitchPic").attr("src") == "' . PICTO_EXPAND . '"){
				digirisk("#' . $formId . 'divDangerContainerSwitchPic").attr("src", "' . PICTO_COLLAPSE . '");
			}
			else{
				digirisk("#' . $formId . 'divDangerContainerSwitchPic").attr("src", "' . PICTO_EXPAND . '");
			}
			digirisk("#' . $formId . 'divDangerContainer").toggle();
		});
		digirisk("#' . $idBouttonEnregistrer . '").click(function() {
			goTo("#postBoxRisques");
			var variables = new Array();
			jQuery(".digi_method_var_value").each(function() {
				var var_id = jQuery(this).attr("id").replace("' . $formId . '_digi_eval_method_var_", "");
				var add_to_tab = true;
				if ( jQuery(this).hasClass("score_risque_checkbox") ) {
					if (jQuery(this).is(":checked")) {
						var new_var = var_id.split("-x-");
						var_id = new_var[0];
					}
					else {
						add_to_tab = false;
					}
				}
				if (add_to_tab) {
					variables[var_id] = jQuery(this).val();
				}
			});

			var historisation = true;
			if(digirisk("#' . $formId . 'historisation").is(":checked")){
				historisation = false;
			}

			digirisk("#formRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",{
				"post":"true",
				"table":"' . TABLE_RISQUE . '",
				"act":"save",
				"tableElement":"' . $tableElement . '",
				"idElement":"' . $idElement . '",
				"idDanger":digirisk("#' . $formId . 'dangerFormRisque").val(),
				"idMethode":digirisk("#' . $formId . 'methodeFormRisque").val(),
				"histo":historisation,
				"variables":variables,
				"description_risque":digirisk("#' . $formId . 'descriptionFormRisque").val(),
				"preconisationRisque":digirisk("#' . $formId . 'preconisationRisque").val(),
				"print_action_description_in_duer":digirisk("#' . $formId . 'print_action_description_duer").val(),
				"idRisque":digirisk("#' . $formId . 'idRisque").val(),
				"pictureId":"' . $formId . '"';
			if (($sub_action == 'control_asked_action') || ($task_to_associate > 0)) {
				$scriptEnregistrement .= ',
				"actionsCorrectives":"' . $task_to_associate . '",
				"action_efficiency":jQuery("#correctiv_action_efficiency_control' . $task_to_associate . '").val()';
			}
			$scriptEnregistrement .= '
			});
			digirisk("#formRisque").html(digirisk("#loadingImg").html());
			digirisk("#divVoirRisques").html(digirisk("#loadingImg").html());
			setTimeout(function(){digirisk("#ongletVoirLesRisques").click();},1000);
			return false;
		});';
			if($idRisque != ''){
				$scriptEnregistrement .= '
		digirisk("#' . $idElement . 'divPreconisationExistante").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
			"post":"true",
			"table":"' . TABLE_RISQUE . '",
			"tableElement":"' . $tableElement . '",
			"idElement":"' . $idElement . '",
			"act":"loadAssociatedTask",
			"idRisque":digirisk("#' . $formId . 'idRisque").val(),
			"priority":"yes"
		});';
			}
			$scriptEnregistrement .= '
	});
</script>';
			$formRisque .= EvaDisplayInput::afficherInput('button', $idBouttonEnregistrer, __('Enregistrer', 'evarisk'), null, '', 'save', false, false, '', 'button-primary alignright saveRiskFormButton', '', '', $scriptEnregistrement);
		}

		$formRisque .= '
'	.
EvaDisplayInput::fermerForm($formId . 'formRisque-') . '
<script type="text/javascript">
	digirisk(document).ready(function(){
		' . $script . '
		digirisk("#risk_priority_task").treeTable();
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
		$advancedForm = '<div style="display:table;width:95%;margin:0px 0px 12px 0px;" ><div class="alignleft" >' . Risque::getRisqueNonAssociePhoto($tableElement, $idElement) . '</div><div id="sendNewPictureForm" class="alignright" style="margin:12px 0px;" >' . evaPhoto::getFormulaireUploadPhoto($tableElement, $idElement, str_replace('\\', '/', EVA_UPLOADS_PLUGIN_DIR . $tableElement . '/' . $idElement . '/'), 'pictureToAssociateToRisk', "['jpeg','jpg','png','gif']", true, '', '', __('Envoyer des photos', 'evarisk'), 'digirisk("#addRisqAdvancedMode").click();') . '</div></div>';

		/*	Get the picture list associated to the current element	*/
		$pictureList = evaPhoto::getPhotos($tableElement, $idElement);
		foreach($pictureList as $picture)
		{
			$currentId = 'picture_' . $picture->id . '_';

			if(is_file(EVA_GENERATED_DOC_DIR . $picture->photo))
			{
				/*	Check if there are already risks that are associated to this picture	*/
				$riskListForPicture = '';
				$riskListForPicture = Risque::getRisqueAssociePhoto($picture->id);

				/*	Add the picture to the output	*/
				$advancedForm .= '
<div id="' . $currentId . '" class="clear" style="margin:0px 0px 12px;" >
	<div class="clear" >
		<img id="addRiskByPictureId' . $currentId . '" class="alignleft riskPictureThumbs" src="' . EVA_GENERATED_DOC_URL . $picture->photo . '" alt="picture to associated to a risk ' . $picture->id . '" />
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
	digirisk(document).ready(function(){
		digirisk(".riskPictureThumbs").click(function(){
			loadAdvancedRiskForm(digirisk(this).attr("id").replace("addRiskByPictureId",""));
			// checkOpenRiskNumber();
		});
		digirisk(".addRiskByPictureButton").click(function(){
			loadAdvancedRiskForm(digirisk(this).parent("div").attr("id").replace("addRiskByPictureButtonId",""));
			// checkOpenRiskNumber();
		});
		digirisk("#saveMassRiskWithPicture").click(function(){
			digirisk(".saveRiskFormButton").each(function(){
				digirisk(this).click();
			});
		});
		digirisk(".riskAssociatedToPictureContainer").droppable({
			accept:".riskAssociatedToPicture",
			activeClass: "ui-state-hover",
			hoverClass: "ui-state-active",
			over: function(event, ui){
				if(digirisk(this).html() == ""){
					digirisk(this).html("' . __('D&eacute;poser ici pour affecter ce risque &agrave; cette photo', 'evarisk') . '");
				}
			},
			drop: function(event, ui){
				digirisk(ui.draggable).remove();
				digirisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",
				{
					"post":"true",
					"table":"' . TABLE_RISQUE . '",
					"act":"associateRiskToPicture",
					"tableElement":"' . TABLE_RISQUE . '",
					"idElement":digirisk(ui.draggable).attr("id").replace("loadRiskId", ""),
					"idPicture":digirisk(this).attr("id").replace("riskAssociatedToPicture",""),
					"oldidPicture":draggedObjectFather
				});
			}
		});
		digirisk("#seeRiskToAssociate").click(function(){
			if(digirisk("#seeRiskToAssociatePic").attr("src") == "' . PICTO_EXPAND . '"){
				digirisk("#seeRiskToAssociatePic").attr("src", "' . PICTO_COLLAPSE . '");
			}
			else{
				digirisk("#seeRiskToAssociatePic").attr("src", "' . PICTO_EXPAND . '");
			}
		});
	});
	//Verification du nombre de risque ouvert pour ajout avec une photo, si sup?rieur ? 1 alors on affiche le bouton enregistrer tout
	function checkOpenRiskNumber()
	{
		var openNumber = 0
		digirisk(".expandablePics").each(function(){
			if(digirisk(this).attr("src") == "' . PICTO_COLLAPSE . '"){
				openNumber++;
			}
		});
		if(openNumber > 1){
			digirisk("#saveMassRiskWithPicture").show();
		}
		else{
			digirisk("#saveMassRiskWithPicture").hide();
		}
	}
	//Chargement du formulaire d\'ajout d\'un risque
	function loadAdvancedRiskForm(idToLoad)
	{
		if(digirisk("#divDangerContainerSwitchPic" + idToLoad).attr("src") == "' . PICTO_EXPAND . '"){
			digirisk("#divDangerContainerSwitchPic" + idToLoad).attr("src", "' . PICTO_COLLAPSE . '");
			digirisk("#addRiskForPictureText" + idToLoad).html("' . __('Annuler l\'ajout du risque', 'evarisk') . '");
			digirisk("#" + idToLoad + "content").html(digirisk("#loadingImg").html());
			digirisk("#" + idToLoad + "content").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",
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
			digirisk("#divDangerContainerSwitchPic" + idToLoad).attr("src", "' . PICTO_EXPAND . '");
			digirisk("#addRiskForPictureText" + idToLoad).html("' . __('Ajouter un risque pour cette photo', 'evarisk') . '");
			digirisk("#" + idToLoad + "content").html("");
		}
	}
</script>';
		}

		return $advancedForm . '<div id="massSaveButton" ><input style="display:none;" class="button-primary alignright" type="button" name="saveMassRiskWithPicture" id="saveMassRiskWithPicture" value="' . __('Enregistrer tout', 'evarisk') . '" /></div>' . $script;
	}

?>