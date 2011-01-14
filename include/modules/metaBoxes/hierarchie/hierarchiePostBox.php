<?php

	//Postbox definition
	$postBoxTitle = __('Services, Unit&eacute;s de travail et risques', 'evarisk');
	$postBoxId = 'postBoxHierarchie';
	$postBoxCallbackFunction = 'getHierarchiePostBoxBody';
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_TACHE, 'rightSide', 'default');
	require_once( EVA_LIB_PLUGIN_DIR . 'arborescence/arborescence_special.class.php');
	
	function getHierarchiePostBoxBody($arguments)
	{
		$idElement = $arguments['idElement'];
		$tableElement = $arguments['tableElement'];
		$saveButtonOuput = 'yes';

		$output = 
			'<div id="messageh' . $tableElement . '" class="updated fade" style="cursor:pointer; display:none;">test</div>
			<table id="arborescence' . $tableElement . '-' . $idElement . '" summary="arborescence societe" cellpadding="0" cellspacing="0" class="widefat post fixed">
				';

		switch($tableElement)
		{
			case TABLE_TACHE:
				$currentTask = new EvaTask($idElement);
				$currentTask->load();
				$currentTaskAffectedTableId = $currentTask->getIdFrom();
				$currentTaskAffectedTable = $currentTask->getTableFrom();
				$ProgressionStatus = $currentTask->getProgressionStatus();

				$arborescenceRisque = arborescence_special::arborescenceRisque(TABLE_GROUPEMENT, 1);

				$idBouttonEnregistrer = 'enregistrerProvenanceTache';
				$scriptEnregistrement = 
					'<script type="text/javascript">
						evarisk(document).ready(function() {				
							evarisk("#' . $idBouttonEnregistrer . '").click(function() {
								evarisk("#savingLinkTaskElement").html(\'<img src="' . PICTO_LOADING_ROUND . '" alt="loading" />\');
								evarisk("#saveLinkTaskElement").hide();
								evarisk("#savingLinkTaskElement").show();

								evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
									"post": "true", 
									"table": "' . TABLE_TACHE . '",
									"act": "updateProvenance",
									"id": "' . $idElement . '",
									"provenance": evarisk("#arborescence' . $tableElement . '-' . $idElement . ' input:radio:checked").val()
								});
							});
						});
					</script>';

				$output .= '
					<tr>
						<td style="width:2%;" ><input type="radio" id="r0" name="selectAffectaion" value="0" /></td>
						<td><label id="l0" for="r0" >' . __('Pas d\'affectation', 'evarisk') . '</label></td>
					</tr>' . 
					arborescence_special::lectureArborescenceRisque($arborescenceRisque, $currentTaskAffectedTable, $currentTaskAffectedTableId);

				$saveButtonOuput = 'no';
				if( ($ProgressionStatus == 'inProgress') || (options::getOptionValue('possibilite_Modifier_Tache_Soldee')== 'oui') )
				{
					$saveButtonOuput = 'yes';
				}
			break;
		}

		if($saveButtonOuput == 'yes')
		{
		$output .= 
				'	
					<tr>
						<td colspan="2" style="text-align:right;" ><div id="saveLinkTaskElement" >' . EvaDisplayInput::afficherInput('button', $idBouttonEnregistrer, __('Enregistrer', 'evarisk'), null, '', 'saveTache', false, true, '', 'button-primary alignright', '', '', $scriptEnregistrement) . '</div><div style="display:none;" id="savingLinkTaskElement" ></div></td>
					</tr>';
		}		
		else
		{
		$output .= 
				'	
					<tr>
						<td colspan="2" style="text-align:right;" ><div class="alignright button-primary" id="TaskSaveButton" >' . __('Cette t&acirc;che est sold&eacute;e, vous ne pouvez pas la modifier', 'evarisk') . '</div></td>
					</tr>';
		}
		$output .= 
			'
			</table>
			<script type="text/javascript" >
				evarisk(document).ready(
					function(){
						evarisk("#r' . TABLE_GROUPEMENT . '_-_1").hide();
						evarisk("#l' . TABLE_GROUPEMENT . '_-_1").hide();
					}
				);
			</script>';

		echo $output;
	}

?>