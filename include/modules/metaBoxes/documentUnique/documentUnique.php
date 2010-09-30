<?php
	//Postbox definition
	$postBoxTitle = __('Bilan', 'evarisk');
	$postBoxId = 'postBoxDocumentUnique';
	$postBoxCallbackFunction = 'getDocumentUniquePostBoxBody';
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_UNITES_DE_TRAVAIL, 'rightSide', 'default');
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_GROUPEMENTS, 'rightSide', 'default');

	require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/documentUnique/documentUnique.class.php' );

	function getDocumentUniquePostBoxBody($arguments)
	{
		if(((int)$arguments['idElement']) == 0)
		{
			$script = '<script type="text/javascript">
					$(document).ready(function() {
						$("#postBoxDocumentUnique").hide();
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
				$(document).ready(function(){
					$("#ongletRisquesLignes").click(function(){
						$("#divRisquesLignes").show();
						$("#divRisquesUnites").hide();
						$("#divDocumentUnique").hide();

						$("#ongletRisquesLignes").addClass("selected_tab");
						$("#ongletRisquesUnites").removeClass("selected_tab");
						$("#ongletDocumentUnique").removeClass("selected_tab");
						
						$("#divRisquesLignes").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_RISQUE . '", "act":"voirRisqueLigne", "tableElement":"' . $tableElement . '","idElement":' . $idElement . '});
					});

					$("#ongletRisquesUnites").click(function(){
						$("#divRisquesLignes").hide();
						$("#divRisquesUnites").show();
						$("#divDocumentUnique").hide();

						$("#ongletRisquesLignes").removeClass("selected_tab");
						$("#ongletRisquesUnites").addClass("selected_tab");
						$("#ongletDocumentUnique").removeClass("selected_tab");
						
						$("#divRisquesUnites").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_RISQUE . '", "act":"voirRisqueUnite", "tableElement":"' . $tableElement . '","idElement":' . $idElement . '});
					});

					$("#ongletDocumentUnique").click(function(){
						$("#divRisquesLignes").hide();
						$("#divRisquesUnites").hide();
						$("#divDocumentUnique").show();

						$("#ongletRisquesLignes").removeClass("selected_tab");
						$("#ongletRisquesUnites").removeClass("selected_tab");
						$("#ongletDocumentUnique").addClass("selected_tab");
						
						$("#divDocumentUnique").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_RISQUE . '", "act":"voirDocumentUnique", "tableElement":"' . $tableElement . '","idElement":' . $idElement . '});
					});
				});
			</script>';

			$corpsPostBoxRisque = $scriptRisque . '
				<div id="message' . TABLE_DUER . '" class="updated fade" style="cursor:pointer; display:none;"></div>
				<ul class="eva_tabs">
					<li id="ongletRisquesLignes" class="tabs selected_tab" style="display:inline; margin-left:0.4em;"><label tabindex="3">' . ucfirst(strtolower( __('Risques unitaires', 'evarisk'))) . '</label></li>
					<li id="ongletRisquesUnites" class="tabs" style="display:inline; margin-left:0.4em;"><label tabindex="3">' . ucfirst(strtolower( __('Risques par unit&eacute;', 'evarisk'))) . '</label></li>
					<li id="ongletDocumentUnique" class="tabs" style="display:inline; margin-left:0.4em;"><label tabindex="3">' . ucfirst(strtolower( __('Document Unique', 'evarisk'))) . '</label></li>
				</ul>
				<div id="divRisquesLignes" class="eva_tabs_panel">' . documentUnique::bilanRisque($tableElement, $idElement, 'ligne') . '</div>
				<div id="divRisquesUnites" class="eva_tabs_panel" style="display:none"></div>
				<div id="divDocumentUnique" class="eva_tabs_panel" style="display:none"></div>';

			echo $corpsPostBoxRisque;
		}
	}
