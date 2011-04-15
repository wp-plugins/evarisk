<?php
	//Postbox definition
	$postBoxTitle = __('Fiche de poste', 'evarisk');
	$postBoxId = 'postBoxFicheDePoste';
	$postBoxCallbackFunction = 'getWorkUnitSheetPostBoxBody';
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_UNITES_DE_TRAVAIL, 'rightSide', 'default');

	require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/ficheDePoste/ficheDePoste.class.php' );

	function getWorkUnitSheetPostBoxBody($arguments)
	{
		if(((int)$arguments['idElement']) == 0)
		{
			$script = '<script type="text/javascript">
					evarisk(document).ready(function() {
						evarisk("#' . $postBoxId . '").hide();
					});
				</script>';
			echo $script;
		}
		else
		{
			$idElement = $arguments['idElement'];
			$tableElement = $arguments['tableElement'];

			$corpsPostBoxRisque = '
<div id="message' . TABLE_FP . '" class="updated fade" style="cursor:pointer; display:none;"></div>
<ul class="eva_tabs">
	<li id="ongletImpressionFicheDePoste" class="tabs selected_tab" style="display:inline; margin-left:0.4em;"><label tabindex="1">' . ucfirst(strtolower( __('Fiches de poste', 'evarisk'))) . '</label></li>
	<li id="ongletHistoriqueFicheDePoste" class="tabs" style="display:inline; margin-left:0.4em;"><label tabindex="2">' . ucfirst(strtolower( __('Historique des fiches de poste', 'evarisk'))) . '</label></li>
</ul>
<div id="divImpressionFicheDePoste" class="eva_tabs_panel">' . eva_WorkUnitSheet::getWorkUnitSheetGenerationForm($tableElement, $idElement) . '</div>
<div id="divHistoriqueFicheDePoste" class="eva_tabs_panel" style="display:none"></div>
<script type="text/javascript" >
	evarisk(document).ready(function(){
		evarisk("#ongletImpressionFicheDePoste").click(function(){
			commonTabChange("postBoxFicheDePoste", "#divImpressionFicheDePoste", "#ongletHistoriqueFicheDePoste");
			evarisk("#divImpressionFicheDePoste").html(evarisk("#loadingImg").html());
			evarisk("#divImpressionFicheDePoste").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
			{
				"post":"true", 
				"table":"' . TABLE_FP . '", 
				"act":"generateWorkUnitSheet", 
				"tableElement":"' . $tableElement . '",
				"idElement":' . $idElement . '
			});
		});
		evarisk("#ongletHistoriqueFicheDePoste").click(function(){
			commonTabChange("postBoxFicheDePoste", "#divHistoriqueFicheDePoste", "#ongletImpressionFicheDePoste");
			evarisk("#divHistoriqueFicheDePoste").html(evarisk("#loadingImg").html());
			evarisk("#divHistoriqueFicheDePoste").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
			{
				"post":"true", 
				"table":"' . TABLE_FP . '", 
				"act":"workUnitSheetHisto", 
				"tableElement":"' . $tableElement . '",
				"idElement":' . $idElement . '
			});
		});
	});
</script>';

			echo $corpsPostBoxRisque;
		}
	}
