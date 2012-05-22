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
					digirisk(document).ready(function() {
						digirisk("#' . $postBoxId . '").hide();
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
<ul class="eva_tabs">';
	if(current_user_can('digi_edit_unite') || current_user_can('digi_edit_unite_' . $arguments['idElement']))
	{
		$userNotAllowed = '';
		$corpsPostBoxRisque .= '
	<li id="ongletImpressionFicheDePoste" class="tabs selected_tab" style="display:inline; margin-left:0.4em;"><label tabindex="1">' . ucfirst(strtolower( __('Fiches de poste', 'evarisk'))) . '</label></li>';
	}
	else
	{
		$userNotAllowed = 'digirisk("#ongletHistoriqueFicheDePoste").click();';
	}
	$corpsPostBoxRisque .= '
	<li id="ongletHistoriqueFicheDePoste" class="tabs" style="display:inline; margin-left:0.4em;"><label tabindex="2">' . ucfirst(strtolower( __('Historique des fiches de poste', 'evarisk'))) . '</label></li>
</ul>
<div id="divImpressionFicheDePoste" class="eva_tabs_panel">' . eva_WorkUnitSheet::getWorkUnitSheetGenerationForm($tableElement, $idElement) . '</div>
<div id="divHistoriqueFicheDePoste" class="eva_tabs_panel" style="display:none"></div>
<script type="text/javascript" >
	digirisk(document).ready(function(){
		digirisk("#ongletImpressionFicheDePoste").click(function(){
			commonTabChange("postBoxFicheDePoste", "#divImpressionFicheDePoste", "#ongletHistoriqueFicheDePoste");
			digirisk("#divImpressionFicheDePoste").html(digirisk("#loadingImg").html());
			digirisk("#divImpressionFicheDePoste").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
			{
				"post":"true", 
				"table":"' . TABLE_FP . '", 
				"act":"generateWorkUnitSheet", 
				"tableElement":"' . $tableElement . '",
				"idElement":' . $idElement . '
			});
		});
		digirisk("#ongletHistoriqueFicheDePoste").click(function(){
			commonTabChange("postBoxFicheDePoste", "#divHistoriqueFicheDePoste", "#ongletImpressionFicheDePoste");
			digirisk("#divHistoriqueFicheDePoste").html(digirisk("#loadingImg").html());
			digirisk("#divHistoriqueFicheDePoste").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
			{
				"post":"true", 
				"table":"' . TABLE_FP . '", 
				"act":"workUnitSheetHisto", 
				"tableElement":"' . $tableElement . '",
				"idElement":' . $idElement . '
			});
		});
		' . $userNotAllowed . '
	});
</script>';

			echo $corpsPostBoxRisque;
		}
	}
