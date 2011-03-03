<?php
	//Postbox definition
	$postBoxTitle = __('Bilan', 'evarisk');
	$postBoxId = 'postBoxDocumentUnique';
	$postBoxCallbackFunction = 'getDocumentUniquePostBoxBody';
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_GROUPEMENTS, 'rightSide', 'default');

	require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/documentUnique/documentUnique.class.php' );

	function getDocumentUniquePostBoxBody($arguments)
	{
		if(((int)$arguments['idElement']) == 0)
		{
			$script = '<script type="text/javascript">
					evarisk(document).ready(function() {
						evarisk("#postBoxDocumentUnique").hide();
					});
				</script>';
			echo $script;
		}
		else
		{
			$idElement = $arguments['idElement'];
			$tableElement = $arguments['tableElement'];

			$scriptRisque = 
			'<script type="text/javascript">
				function loadBilanBoxContent(boxId, action, table){
					evarisk(boxId).html(evarisk("#loadingImg").html());
					evarisk(boxId).load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
					{
						"post":"true",
						"table":table,
						"act":action,
						"tableElement":"' . $tableElement . '",
						"idElement":' . $idElement . '
					});
				}

				evarisk(document).ready(function(){
					evarisk("#ongletRisquesLignes").click(function(){
						commonTabChange("postBoxDocumentUnique", "#divRisquesLignes", "#ongletRisquesLignes");
						loadBilanBoxContent("#divRisquesLignes", "voirRisqueLigne", "' . TABLE_RISQUE . '");
					});

					evarisk("#ongletRisquesUnites").click(function(){
						commonTabChange("postBoxDocumentUnique", "#divRisquesUnites", "#ongletRisquesUnites");
						loadBilanBoxContent("#divRisquesUnites", "voirRisqueUnite", "' . TABLE_RISQUE . '");
					});

					evarisk("#ongletDocumentUnique").click(function(){
						commonTabChange("postBoxDocumentUnique", "#divDocumentUnique", "#ongletDocumentUnique");
						loadBilanBoxContent("#divDocumentUnique", "voirDocumentUnique", "' . TABLE_DUER . '");
					});

					evarisk("#ongletHistoriqueDocumentUnique").click(function(){
						commonTabChange("postBoxDocumentUnique", "#divHistoriqueDocumentUnique", "#ongletHistoriqueDocumentUnique");
						loadBilanBoxContent("#divHistoriqueDocumentUnique", "voirHistoriqueDocumentUnique", "' . TABLE_DUER . '");
					});

					evarisk("#ongletFicheDePoste").click(function(){
						commonTabChange("postBoxDocumentUnique", "#divFicheDePoste", "#ongletFicheDePoste");
						loadBilanBoxContent("#divFicheDePoste", "voirHistoriqueFicheDePosteGroupement", "' . TABLE_FP . '");
					});
				});
			</script>';

			$corpsPostBoxRisque = $scriptRisque . '
				<div id="message' . TABLE_DUER . '" class="updated fade" style="cursor:pointer; display:none;"></div>
				<ul class="eva_tabs" style="margin-bottom:12px;" >
					<li id="ongletDocumentUnique" class="tabs selected_tab" style="display:inline; margin-left:0.4em;"><label tabindex="1">' . ucfirst(strtolower( __('Document Unique', 'evarisk'))) . '</label></li>
					<li id="ongletRisquesLignes" class="tabs" style="display:inline; margin-left:0.4em;"><label tabindex="2">' . ucfirst(strtolower( __('Risques unitaires', 'evarisk'))) . '</label></li>
					<li id="ongletRisquesUnites" class="tabs" style="display:inline; margin-left:0.4em;"><label tabindex="3">' . ucfirst(strtolower( __('Risques par unit&eacute;', 'evarisk'))) . '</label></li>
					<li id="ongletHistoriqueDocumentUnique" class="tabs" style="display:inline; margin-left:0.4em;"><label tabindex="4">' . ucfirst(strtolower( __('Historique des documents unique', 'evarisk'))) . '</label></li>
					<li id="ongletFicheDePoste" class="tabs" style="display:inline; margin-left:0.4em;"><label tabindex="5">' . ucfirst(strtolower( __('Fiches de poste', 'evarisk'))) . '</label></li>
				</ul>
				<div id="divRisquesUnites" class="eva_tabs_panel">' . eva_documentUnique::formulaireGenerationDocumentUnique($tableElement, $idElement) . '<script type="text/javascript" >evarisk(document).ready(function(){evarisk("#ui-datepicker-div").hide();});</script></div>
				<div id="divRisquesLignes" class="eva_tabs_panel" style="display:none"></div>
				<div id="divDocumentUnique" class="eva_tabs_panel" style="display:none"></div>
				<div id="divHistoriqueDocumentUnique" class="eva_tabs_panel" style="display:none"></div>
				<div id="divFicheDePoste" class="eva_tabs_panel" style="display:none"></div>';

			echo $corpsPostBoxRisque;
		}
	}
