<?php

	//Postbox definition
	$postBoxTitle = __('Services, Unit&eacute;s de travail et risques', 'evarisk');
	$postBoxId = 'postBoxHierarchie';
	$postBoxCallbackFunction = 'getHierarchiePostBoxBody';
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_TACHE, 'rightSide', 'default');
	require_once( EVA_LIB_PLUGIN_DIR . 'arborescence/arborescence_special.class.php');
	
	function getHierarchiePostBoxBody($arguments){
		$idElement = $arguments['idElement'];
		$tableElement = $arguments['tableElement'];
		$current_content = '&nbsp;';

		switch($tableElement){
			case TABLE_TACHE:{
				$currentTask = new EvaTask($idElement);
				$currentTask->load();
				$currentTaskAffectedTableId = $currentTask->getIdFrom();
				$currentTaskAffectedTable = $currentTask->getTableFrom();
				$ProgressionStatus = $currentTask->getProgressionStatus();

				$arborescenceRisque = arborescence_special::arborescenceRisque(TABLE_GROUPEMENT, 1);

				$idBouttonEnregistrer = 'enregistrerProvenanceTache';
				$scriptEnregistrement = '';

				if( ($ProgressionStatus == 'inProgress') || ($ProgressionStatus == 'notStarted') || (digirisk_options::getOptionValue('possibilite_Modifier_Tache_Soldee')== 'oui') ){
					$save_button = 
						'<div id="saveLinkTaskElement" >' . EvaDisplayInput::afficherInput('submit', $idBouttonEnregistrer, __('Enregistrer', 'evarisk'), null, '', 'saveTache', false, true, '', 'button-secondary alignright', '', '', $scriptEnregistrement) . '</div><div class="digirisk_hide alignright" id="savingLinkTaskElement" >&nbsp;</div>';
				}
				else{
					$save_button = 
						'<div class="alignright button-secondary" id="TaskSaveButton" >' . __('Cette t&acirc;che est sold&eacute;e, vous ne pouvez pas la modifier', 'evarisk') . '</div>';
				}
				$current_content = arborescence_special::display_mini_hierarchy_for_element_affectation($currentTaskAffectedTable, $currentTaskAffectedTableId);
			}break;
		}

		$output = '
<div id="messageh' . $tableElement . '" class="updated fade digirisk_hide pointer" >&nbsp;</div>
<div title="' . __('Arborescence compl&egrave;te', 'evarisk') . '" id="complete_tree_popup_container" >&nbsp;</div>
<form action="' . EVA_INC_PLUGIN_URL . 'ajax.php" mathod="post" id="hierarchy_affectation_form" >
	<input type="hidden" name="post" value="true" />
	<input type="hidden" name="table" value="' . TABLE_TACHE . '" />
	<input type="hidden" name="act" value="updateProvenance" />
	<input type="hidden" name="id" value="' . $idElement . '" />
	<div class="clear" ><span class="hierarchy_element_selector_container" ><input class="hierarchy_element_selector" type="radio" id="r0" name="selectAffectation[]" value="0" /></span><label id="l0" for="r0" >' . __('Pas d\'affectation', 'evarisk') . '</label></div>
	<div class="auto-search-hierarchic-box clear" >
		<input type="hidden" name="current_element" id="current_element" value="' . $currentTaskAffectedTable . '-_-' . $currentTaskAffectedTableId . '" />
		<input type="hidden" name="receiver_element" id="receiver_element" value="" />
		<div class="clear auto-search-container" >
			<span class="hierarchy_element_selector_container ui-icon" >&nbsp;</span>
			<span class="auto-search-ui-icon ui-icon" >&nbsp;</span>
			<input class="auto-search-input" type="text" id="search_element" value="' . __('Rechercher dans la liste des &eacute;l&eacute;ments', 'evarisk') . '" />
			<img id="complete_tree_popup" title="' . __('Voir l\'arborescence compl&egrave;te', 'evarisk') . '" alt="' . __('Voir l\'arborescence compl&egrave;te', 'evarisk') . '" src="' . DIGI_OPEN_POPUP . '">
		</div>
	</div>
	<div class="clear" id="current_hierarchy_display" >' . $current_content . '</div>
	<div class="clear" >' . $save_button . '</div>
</form>
<script type="text/javascript" >
	evarisk(document).ready(function(){
		/*	Tree-element Search autocompletion	*/
		jQuery("#search_element").live("click", function(){
			jQuery(this).val("");
		});
		jQuery("#search_element").autocomplete("' . EVA_INC_PLUGIN_URL . 'liveSearch/searchGp_UT.php?table_element=' . $tableElement . '&id_element=' . $idElement . '&element_type=' . TABLE_UNITE_TRAVAIL . '-t-' . TABLE_GROUPEMENT . '-t-' . TABLE_RISQUE . '");
		jQuery("#search_element").result(function(event, data, formatted){
			jQuery(this).val(convertAccentToJS("' . __('Rechercher dans la liste des &eacute;l&eacute;ments', 'evarisk') . '"));
			jQuery("#receiver_element").val(data[1]);
			check_if_value_changed("' . $idBouttonEnregistrer . '");
			jQuery("#current_hierarchy_display").html(jQuery("#loading_round_pic").html());
			jQuery("#current_hierarchy_display").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",{
				"post": "true",
				"nom": "hierarchy",
				"action": "load_partial",
				"selected_element": data[1]
			});
		});

		jQuery(".hierarchy_element_selector").live("click", function(){
			jQuery("#receiver_element").val(jQuery(this).val());
			check_if_value_changed("' . $idBouttonEnregistrer . '");
		});

		/*	Create an ajax form	*/
		jQuery("#hierarchy_affectation_form").ajaxForm({
			target: "#ajax-response",
			beforeSubmit: function(){
				jQuery("#savingLinkTaskElement").html(jQuery("#loading_round_pic center").html());
				jQuery("#saveLinkTaskElement").hide();
				jQuery("#savingLinkTaskElement").show();
			}
		});
		jQuery("#complete_tree_popup_container").dialog({
			autoOpen: false,
			height: 600,
			width: 800,
			modal: true,
			buttons:{
				"' . __('Choisir', 'evarisk') . '": function(){
					jQuery("#complete_hierarchy_table input:radio").each(function(){
						if(jQuery(this).is(":checked")){
							jQuery("#receiver_element").val(jQuery(this).val());
							jQuery("#current_hierarchy_display").html(jQuery("#loading_round_pic").html());
							jQuery("#current_hierarchy_display").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",{
								"post": "true",
								"nom": "hierarchy",
								"action": "load_partial",
								"selected_element": jQuery(this).val()
							});
							check_if_value_changed("' . $idBouttonEnregistrer . '");
						}
					});
					jQuery(this).dialog("close");
				},
				"' . __('Annuler', 'evarisk') . '": function(){
					jQuery(this).dialog("close");
				}
			}
		});
		jQuery("#complete_tree_popup").click(function(){
			var current_selected_element = jQuery("#receiver_element").val();
			if(current_selected_element == ""){
				current_selected_element = jQuery("#current_element").val();
			}
			jQuery("#complete_tree_popup_container").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",{
				"post": "true",
				"nom": "hierarchy",
				"action": "load_complete",
				"selected": current_selected_element
			});
			jQuery("#complete_tree_popup_container").dialog("open");
		});
	});

	function check_if_value_changed(button){
		if(jQuery("#receiver_element").val() != jQuery("#current_element").val()){
			jQuery("#" + button).removeClass("button-secondary");
			jQuery("#" + button).addClass("button-primary");
		}
		else{
			jQuery("#" + button).addClass("button-secondary");
			jQuery("#" + button).removeClass("button-primary");
		}
	}
</script>';

		echo $output;
	}

?>