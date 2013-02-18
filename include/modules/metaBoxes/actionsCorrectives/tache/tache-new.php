<?php
/*
 * @version v5.0
 */

//Postbox definition
$postBoxTitle = __('Informations G&eacute;n&eacute;rales', 'evarisk');
$postBoxId = 'postBoxGeneralInformation';
$postBoxCallbackFunction = 'getTaskGeneralInformationPostBoxBody';
add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_TACHE, 'rightSide', 'default');
require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/tache/evaTask.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'arborescence.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayInput.class.php' );

function getTaskGeneralInformationPostBoxBody($arguments){
	$postId = '';
	if($arguments['idElement'] != null){
		$postId = $arguments['idElement'];
    $tache = new EvaTask($postId);
		$tache->load();

		$contenuInputTitre = html_entity_decode($tache->getName(), ENT_NOQUOTES, 'UTF-8');
		$contenuInputDescription = $tache->getDescription();
		$idProvenance = $tache->getIdFrom();
		$tableProvenance = $tache->getTableFrom();
		$contenuInputResponsable = $tache->getidResponsable();
		$contenuInputRealisateur = $tache->getidSoldeur();
		$ProgressionStatus = $tache->getProgressionStatus();
		$nom_exportable_plan_action = $tache->getnom_exportable_plan_action();
		$description_exportable_plan_action = $tache->getdescription_exportable_plan_action();
		$startDate = $tache->getStartDate();
		$endDate = $tache->getFinishDate();
		$firstInsert = $tache->getFirstInsert();
		$creatorID = $tache->getidCreateur();
		$efficacite = $tache->getEfficacite();
		$readable_external = $tache->get_external_readable();
		$grise = false;
		$tacheMere = Arborescence::getPere(TABLE_TACHE, $tache->convertToWpdb());
		$idPere = (!empty($tacheMere)?$tacheMere->id:0);
    $saveOrUpdate = 'update';
	}
	else{
		$contenuInputTitre = '';
		$contenuInputDescription = '';
		$contenuInputResponsable = '';
		$contenuInputRealisateur = '';
		$nom_exportable_plan_action = '';
		$description_exportable_plan_action = '';
		$ProgressionStatus = '';
		$startDate = '';
		$endDate = '';
		$firstInsert = '';
		$idProvenance = 0;
		$creatorID = 0;
		$tableProvenance = '';
		$efficacite = '';
		$idPere = $arguments['idPere'];
		$grise = true;
		$readable_external = 'no';
    $saveOrUpdate = 'save';
	}

	/*	Recupere la tache parent pour v�rifier si on peut cocher les cases d'export dans le document unique	*/
	$parent_task = new EvaTask($idPere);
	$parent_task->load();

  $idForm = 'informationGeneralesTache';
	$tache_new = '<form method="post" id="' . $idForm . '" name="' . $idForm . '" action="' . EVA_INC_PLUGIN_URL . 'ajax.php" >';
	{//Champs cach�s
		$tache_new = $tache_new . EvaDisplayInput::afficherInput('hidden', 'actTache', $saveOrUpdate, '', null, 'act', false, false);
		$tache_new = $tache_new . EvaDisplayInput::afficherInput('hidden', 'affichageTache', $arguments['affichage'], '', null, 'affichage', false, false);
		$tache_new = $tache_new . EvaDisplayInput::afficherInput('hidden', 'tableTache', TABLE_TACHE, '', null, 'table', false, false);
		$tache_new = $tache_new . EvaDisplayInput::afficherInput('hidden', 'idTache', $postId, '', null, 'id', false, false);
		$tache_new = $tache_new . EvaDisplayInput::afficherInput('hidden', 'idPereTache', $idPere, '', null, 'idPere', false, false);
		$tache_new = $tache_new . EvaDisplayInput::afficherInput('hidden', 'idsFilArianeTache', $arguments['idsFilAriane'], '', null, 'idsFilAriane', false, false);
		$tache_new = $tache_new . EvaDisplayInput::afficherInput('hidden', 'idProvenanceTache', $idProvenance, '', null, 'idProvenance', false, false);
		$tache_new = $tache_new . EvaDisplayInput::afficherInput('hidden', 'tableProvenanceTache', $tableProvenance, '', null, 'tableProvenance', false, false);
	}
	{//Nom de la t�che
		$contenuAideTitre = "";
		$labelInput = ucfirst(sprintf(__("nom %s", 'evarisk'), __("de la t&acirc;che",'evarisk'))) . ' : ';
		$exportable_option = '';
		if ( !empty ( $parent_task ) ) {
			if ( ( $parent_task->name != __('Tache Racine', 'evarisk') ) && ( $parent_task->nom_exportable_plan_action == 'no' ) ) {
				$labelInput .= '<input type="hidden" name="nom_exportable_plan_action" value="no" />';
				$exportable_option = ' disabled="disabled" title="' . __('L\'export ne peut &ecirc;tre activ&eacute; si la t&acirc;che parente n\'est pas exportable', 'evarisk') . '"';
				$nom_exportable_plan_action = 'no';
			}
		}
		if ( ( $ProgressionStatus == 'Done' ) && (digirisk_options::getOptionValue('possibilite_Modifier_Tache_Soldee') == 'non') ) {
			$exportable_option = ' disabled="disabled" title="' . __('L\'export ne peut &ecirc;tre activ&eacute; car cette t&acirc;che est sold&eacute;e', 'evarisk') . '"';
		}
		$labelInput .= '<div class="alignright" ><input type="checkbox" name="nom_exportable_plan_action" id="nom_exportable_plan_action"' . $exportable_option . ' value="yes"'.(empty($nom_exportable_plan_action) || ($nom_exportable_plan_action=='yes')?' checked="checked"':'').' />&nbsp;<label for="nom_exportable_plan_action" >'.__('Exporter dans le plan d\'action', 'evarisk').'</label></div>';
		$nomChamps = "nom_tache";
		$idTitre = "nom_tache";
		$tache_new .= EvaDisplayInput::afficherInput('text', $idTitre, $contenuInputTitre, $contenuAideTitre, $labelInput, $nomChamps, $grise, true, 255, 'titleInput') . '
<script type="text/javascript" >
	evarisk(document).ready(function(){
		jQuery("#nom_exportable_plan_action").click(function(){
			if( !jQuery(this).is(":checked") ) {
				jQuery("#description_exportable_plan_action").prop("checked",false);
				jQuery("#description_exportable_plan_action").prop("disabled",true);
			}
			else{
				jQuery("#description_exportable_plan_action").prop("disabled",false);
			}
		});
	});
</script>';
	}
	{//Dates
		if(($firstInsert != '') || ($creatorID > 0)){
			$tache_new .='<br/>';
			if(($firstInsert != '') && ($creatorID > 0)){
				$task_creator_infos = evaUser::getUserInformation($creatorID);
				$tache_new .= sprintf(__('Ajout&eacute;e le %s par %s', 'evarisk'), mysql2date('d M Y', $firstInsert, true), $task_creator_infos[$creatorID]['user_lastname'] . ' ' . $task_creator_infos[$creatorID]['user_firstname']);
			}
			elseif($firstInsert != ''){
				$tache_new .= sprintf(__('Ajout&eacute;e le %s', 'evarisk'), mysql2date('d M Y', $firstInsert, true));
			}
			elseif($creatorID > 0){
				$task_creator_infos = evaUser::getUserInformation($creatorID);
				$tache_new .= sprintf(__('Ajout&eacute;e par %s', 'evarisk'), $task_creator_infos[$creatorID]['user_lastname'] . ' ' . $task_creator_infos[$creatorID]['user_firstname']);
			}
			$tache_new .='<br/>';
		}
		if(($startDate != '') && ($endDate != '') && ($startDate != '0000-00-00') && ($endDate != '0000-00-00'))
		{
			$tache_new .='<br/>' . __('D&eacute;but', 'evarisk') . '&nbsp;' . mysql2date('d M Y', $startDate, true) . '&nbsp;-&nbsp;' . __('Fin', 'evarisk') . '&nbsp;' . mysql2date('d M Y', $endDate, true) . '&nbsp;<span style="font-size:9px;" >(' . __('Ces dates sont calcul&eacute;es en fonction de sous-t&acirc;ches', 'evarisk') . ')</span><br/>';
		}
	}
	{//Responsable
		$contenuAideDescription = "";
		$labelInput = __("Responsable", 'evarisk') . ' : ';
		$id = "responsable_tache";
		$nomChamps = "responsable_tache";

		$tache_new .= '<br/><label for="search_user_responsable_' . $arguments['tableElement'] . '" >' . $labelInput . '</label>' . EvaDisplayInput::afficherInput('hidden', $id, $contenuInputResponsable, '', null, $nomChamps, false, false) . '<div id="responsible_name" >';
		$search_input_state = '';
		$change_input_state = 'hide';
		if($contenuInputResponsable > 0){
			$search_input_state = 'hide';
			$change_input_state = '';
			$responsible = evaUser::getUserInformation($contenuInputResponsable);
			$tache_new .= ELEMENT_IDENTIFIER_U . $contenuInputResponsable . '&nbsp;-&nbsp;' . $responsible[$contenuInputResponsable]['user_lastname'] . ' ' . $responsible[$contenuInputResponsable]['user_firstname'];
		}
		else{
			$tache_new .= '&nbsp;';
		}
		$tache_new .= '</div>&nbsp;<span id="change_responsible_' . $arguments['tableElement'] . 'responsible" class="' . $change_input_state . ' change_ac_responsible" >' . __('Changer', 'evarisk') . '&nbsp;/&nbsp;</span><span id="delete_responsible_' . $arguments['tableElement'] . 'responsible" class="' . $change_input_state . ' delete_ac_responsible" >' . __('Enlever le responsable', 'evarisk') . '</span><input class="searchUserToAffect ac_responsable ' . $search_input_state . '" type="text" name="responsable_name_' . $arguments['tableElement'] . '" id="search_user_responsable_' . $arguments['tableElement'] . '" placeholder="' . __('Rechercher dans la liste des utilisateurs', 'evarisk') . '" /><div id="completeUserList' . $arguments['tableElement'] . 'responsible" class="completeUserList completeUserListActionResponsible hide clear" >' . evaUser::afficheListeUtilisateurTable_SimpleSelection($arguments['tableElement'] . 'responsible', $arguments['idElement']) . '</div>
<script type="text/javascript" >
	digirisk(document).ready(function(){
		jQuery("#search_user_responsable_' . $arguments['tableElement'] . '").click(function(){
			jQuery(".completeUserListActionResponsible").show();
		});
		/*	Autocomplete search	*/
		jQuery("#search_user_responsable_' . $arguments['tableElement'] . '").autocomplete({
			source: "' . EVA_INC_PLUGIN_URL . 'liveSearch/searchUsers.php?table_element=' . $arguments['tableElement'] . '&id_element=' . $arguments['idElement'] . '",
			select: function( event, ui ){
				jQuery("#responsable_tache").val(ui.item.value);
				jQuery("#responsible_name").html(ui.item.label);

				jQuery(".completeUserListActionResponsible").hide();
				jQuery(".searchUserToAffect").hide();
				jQuery("#change_responsible_' . $arguments['tableElement'] . 'responsible").show();
				jQuery("#delete_responsible_' . $arguments['tableElement'] . 'responsible").show();

				setTimeout(function(){
					jQuery("#search_user_responsable_' . $arguments['tableElement'] . '").val("");
					jQuery("#search_user_responsable_' . $arguments['tableElement'] . '").blur();
				}, 2);
			}
		});

		jQuery("#change_responsible_' . $arguments['tableElement'] . 'responsible").click(function(){
			jQuery("#search_user_responsable_' . $arguments['tableElement'] . '").show();
			jQuery("#completeUserList' . $arguments['tableElement'] . 'responsible").show();
			jQuery(this).hide();
		});
		jQuery("#delete_responsible_' . $arguments['tableElement'] . 'responsible").click(function(){
			jQuery("#responsable_tache").val("");
			jQuery("#responsible_name").html("&nbsp;");
			jQuery(this).hide();
			jQuery("#change_responsible_' . $arguments['tableElement'] . 'responsible").hide();
			jQuery("#search_user_responsable_' . $arguments['tableElement'] . '").show();
			jQuery("#completeUserList' . $arguments['tableElement'] . 'responsible").hide();
		});
	});
</script><br class="clear" /><br/><br/><br/>';
	}
	{//Efficacite
		$contenuAideDescription = "";
		$labelInput = __("Efficacit&eacute;", 'evarisk') . ' : ';
		$id = "efficacite_tache";
		$nomChamps = "efficacite";
		$tache_new .= sprintf(__('Efficacit&eacute; de la t&acirc;che %s', 'evarisk'), '<input type="text" name="correctiv_action_efficiency_control" id="correctiv_action_efficiency_control" value="' . $efficacite . '" class="correctiv_action_efficiency_control" readonly="readonly" />%') . '<div id="correctiv_action_efficiency_control_slider" class="correctiv_action_efficiency_control_slider" >&nbsp;</div>
		<script type="text/javascript" >
			digirisk(document).ready(function(){
				jQuery(".correctiv_action_efficiency_control_slider").slider({
					value:"' . $efficacite . '",
					min: 0,
					max: 100,
					step: 1,
					slide: function(event, ui){
						jQuery("#" + jQuery(this).attr("id").replace("correctiv_action_efficiency_control_slider", "correctiv_action_efficiency_control")).val( ui.value );
					}
				});
			});
		</script>';
	}
	{//Description
		$contenuAideDescription = "";
		$labelInput = __("Description", 'evarisk') . ' : ';
		$exportable_option = '';
		if ( !empty ( $parent_task ) ) {
			if ( ( ( $parent_task->name != __('Tache Racine', 'evarisk') ) && ( $parent_task->nom_exportable_plan_action == 'no' ) ) || ( $nom_exportable_plan_action == 'no' ) ) {
				$labelInput .= '<input type="hidden" name="description_exportable_plan_action" value="no" />';
				$exportable_option = ' disabled="disabled" title="' . __('L\'export ne peut &ecirc;tre activ&eacute; si la t&acirc;che parente n\'est pas exportable', 'evarisk') . '"';
				$description_exportable_plan_action = 'no';
			}
		}
		if ( ( $ProgressionStatus == 'Done' ) && (digirisk_options::getOptionValue('possibilite_Modifier_Tache_Soldee') == 'non') ) {
			$exportable_option = ' disabled="disabled" title="' . __('L\'export ne peut &ecirc;tre activ&eacute; car cette t&acirc;che est sold&eacute;e', 'evarisk') . '"';
		}
		$labelInput .= '<div class="alignright" ><input type="checkbox" name="description_exportable_plan_action" id="description_exportable_plan_action"' . $exportable_option . ' value="yes"'.(empty($description_exportable_plan_action) || ($description_exportable_plan_action=='yes')?' checked="checked"':'').' />&nbsp;<label for="description_exportable_plan_action" >'.__('Exporter dans le plan d\'action', 'evarisk').'</label></div>';
		$id = "descriptionTache";
		$nomChamps = "description";
		$rows = 5;
		$tache_new .= '<br class="clear" />' . EvaDisplayInput::afficherInput('textarea', $id, $contenuInputDescription, $contenuAideDescription, $labelInput, $nomChamps, $grise, DESCRIPTION_TACHE_OBLIGATOIRE, $rows);
	}
	{//Bouton Enregistrer
		$idBouttonEnregistrer = 'saveTache';

		/*	Check if the user in charge of the action and the maker are mandatory */
		$idResponsableIsMandatory = digirisk_options::getOptionValue('responsable_Tache_Obligatoire');

		$scriptEnregistrementSave = '<script type="text/javascript">
			digirisk(document).ready(function() {
				digirisk("#' . $idBouttonEnregistrer . '").click(function() {
					if(digirisk("#' . $idTitre . '").is(".form-input-tip")){
						document.getElementById("' . $idTitre . '").value="";
						digirisk("#' . $idTitre . '").removeClass("form-input-tip");
					}

					idResponsable = digirisk("#responsable_tache").val();
					idResponsableIsMandatory = "false";
					idResponsableIsMandatory = "' . $idResponsableIsMandatory . '";

					valeurActuelle = digirisk("#' . $idTitre . '").val();
          if(jQuery.trim(valeurActuelle) == ""){
            alert(digi_html_accent_for_js("' . __("Vous n\'avez pas donne de nom a la t&acirc;che", 'evarisk') . '"));
          }
					else if(((idResponsable <= "0") ||(idResponsable == "")) && (idResponsableIsMandatory == "oui")){
						alert(digi_html_accent_for_js("' . __("Vous devez choisir une personne en charge de la t&acirc;che", 'evarisk') . '"));
					}
          else{
						var nom_exportable = "no";
						if(digirisk("#nom_exportable_plan_action").is(":checked")){	var nom_exportable = "yes"; }
						var description_exportable = "no";
						if(digirisk("#description_exportable_plan_action").is(":checked")){	var description_exportable = "yes"; }
            digirisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true",
              "table": "' . TABLE_TACHE . '",
              "act": digirisk("#actTache").val(),
              "id": digirisk("#idTache").val(),
              "nom_tache": digirisk("#' . $idTitre . '").val(),
              "idPere": digirisk("#idPereTache").val(),
              "responsable_tache": digirisk("#responsable_tache").val(),
              "description": digirisk("#descriptionTache").val(),
              "efficacite": digirisk("#correctiv_action_efficiency_control").val(),
              "affichage": digirisk("#affichageTache").val(),
              "idsFilAriane": digirisk("#idsFilArianeTache").val(),
              "idProvenance": digirisk("#idProvenanceTache").val(),
              "tableProvenance": digirisk("#tableProvenanceTache").val(),
							"nom_exportable_plan_action": nom_exportable,
							"description_exportable_plan_action": description_exportable
            });
          }
				});
			});
			</script>';
	}
	{//Bouton Solder
		$idBoutton = 'taskDone';

		$scriptEnregistrementDone = '<script type="text/javascript">
			digirisk(document).ready(function(){
				digirisk("#updateTaskStatus").dialog({
					autoOpen: false,
					width: 800,
					height: 400,
					modal: true,
					buttons:{
						"' . __('Solder', 'evarisk') . '": function(){
							if((digirisk("#markSubAsDone").is(":checked") && confirm(digi_html_accent_for_js("' . __('En soldant cette t&acirc;che, vous solderez tous les &eacute;l&eacute;ments \'en-dessous\'. Etes vous sur?', 'evarisk') . '"))) || (!digirisk("#markSubAsDone").is(":checked"))){
								if(digirisk("#markSubAsDone").is(":checked")){
									var markAllSubElementAsDone = true;
								}
								else{
									var markAllSubElementAsDone = false;
								}
								digirisk("#taskDone").html(\'<img src="' . PICTO_LOADING_ROUND . '" alt="loading" />\');
								digirisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
									"post": "true",
									"table": "' . TABLE_TACHE . '",
									"act": "taskDone",
									"id": digirisk("#idTache").val(),
									"nom_tache": digirisk("#' . $idTitre . '").val(),
									"idPere": digirisk("#idPereTache").val(),
									"responsable_tache": digirisk("#responsable_tache").val(),
									"description": digirisk("#descriptionTache").val(),
									"affichage": digirisk("#affichageTache").val(),
									"idsFilAriane": digirisk("#idsFilArianeTache").val(),
									"idProvenance": digirisk("#idProvenanceTache").val(),
									"tableProvenance": digirisk("#tableProvenanceTache").val(),
									"avancement": digirisk("#avancement").val(),
									"date_fin": digirisk("#date_fin").val(),
									"date_debut": digirisk("#date_debut").val(),
									"markAllSubElementAsDone": markAllSubElementAsDone,
									"nom_exportable_plan_action": digirisk("#nom_exportable_plan_action").val(),
									"description_exportable_plan_action": digirisk("#description_exportable_plan_action").val()
								})
								digirisk(this).dialog("close");
							}
						},
						"' . __('Annuler', 'evarisk') . '":	function(){
							digirisk(this).dialog("close");
						}
					},
					close:function(){
						digirisk(this).html("");
					}
				});
				digirisk("#' . $idBoutton . '").click(function(){
					if(digirisk("#' . $idTitre . '").is(".form-input-tip")){
						document.getElementById("' . $idTitre . '").value="";
						digirisk("#' . $idTitre . '").removeClass("form-input-tip");
					}

					idResponsable = digirisk("#responsable_tache").val();
					idResponsableIsMandatory = "false";
					idResponsableIsMandatory = "' . $idResponsableIsMandatory . '";

					valeurActuelle = digirisk("#' . $idTitre . '").val();
          if(jQuery.trim(valeurActuelle) == ""){
            alert(digi_html_accent_for_js("' . __("Vous n\'avez pas donne de nom a la t&acirc;che", 'evarisk') . '"));
          }
					else if(((idResponsable <= "0") ||(idResponsable == "")) && (idResponsableIsMandatory == "oui")){
						alert(digi_html_accent_for_js("' . __("Vous devez choisir une personne en charge de la t&acirc;che", 'evarisk') . '"));
					}
          else{
						digirisk("#updateTaskStatus").dialog("open");
						digirisk("#updateTaskStatus").html(digirisk("#loadingImg").html());
						digirisk("#updateTaskStatus").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
							"post": "true",
							"table": "' . TABLE_TACHE . '",
							"act": "closeTask",
							"id": digirisk("#idTache").val()
						});
          }
				});
			});
			</script>';
	}
	{//Boutons
		if(($saveOrUpdate == 'save') || ($ProgressionStatus == '') || ($ProgressionStatus == 'inProgress') || ($ProgressionStatus == 'notStarted') || (digirisk_options::getOptionValue('possibilite_Modifier_Tache_Soldee')== 'oui'))
		{
			$tache_new .=
				'<div class="alignright" id="TaskSaveButton" >';

			if(($saveOrUpdate != 'save') && (($ProgressionStatus == '') || ($ProgressionStatus == 'inProgress') || ($ProgressionStatus == 'notStarted')))
			{
			$tache_new .= '
					<div id="updateTaskStatus" class="hide" title="' . __('Mise &agrave; jour du statut de l\'action corrective', 'evarisk') . '" >&nbsp;</div>' .
					EvaDisplayInput::afficherInput('button', $idBoutton, __('Solder la tache', 'evarisk'), null, '', $idBoutton, false, true, '', 'button-primary', '', '', $scriptEnregistrementDone, 'left');
			}
			elseif($saveOrUpdate == 'update')
			{
			$tache_new .=
					'<div style="float:left;" id="TaskSaveButton" >
						<br/>
						<div class="alignright button-primary" >' .
							__('Cette t&acirc;che est sold&eacute;e', 'evarisk') .
						'</div>
					</div>';
			}

			$tache_new .=
					EvaDisplayInput::afficherInput('button', $idBouttonEnregistrer, __('Enregistrer', 'evarisk'), null, '', 'saveTache', false, true, '', 'button-primary', '', '', $scriptEnregistrementSave, 'left') .
				'</div>
				<script type="text/javascript" >digirisk("#TaskSaveButton").children("br").remove();</script>';
		}
		else
		{
			$tache_new .=
				'<div class="alignright button-primary" id="TaskSaveButton" >' .
					__('Cette t&acirc;che est sold&eacute;e, vous ne pouvez pas la modifier', 'evarisk') .
				'</div>';
		}

		if(digirisk_options::getOptionValue('export_tasks') == 'oui'){
			$tache_new .=
				'<br/>
				<div>
					<div class="alignright button-primary" id="taskExportButton" >
						' . __('Exporter', 'evarisk') . '
					</div>
					<div id="taskExportResult" >&nbsp;</div>
				</div>
				<script type="text/javascript" >
					digirisk(document).ready(function(){
						digirisk("#taskExportButton").click(function(){
							digirisk("#taskExportResult").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",{
								"post": "true",
								"table": "' . TABLE_TACHE . '",
								"act": "exportTask",
								"id": digirisk("#idTache").val()
							});
						});
					});
				</script>';
		}
	}

	$tache_new .= EvaDisplayInput::fermerForm($idForm);

	echo $tache_new;
}

?>