<?php
	//Postbox definition
	$postBoxTitle = __('Bilan', 'evarisk');
	$postBoxId = 'postBoxDocumentUnique';
	$postBoxCallbackFunction = 'getDocumentUniquePostBoxBody';
	// add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_UNITES_DE_TRAVAIL, 'rightSide', 'default');
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
				evarisk(document).ready(function(){
					evarisk("#ongletRisquesLignes").click(function(){
						evarisk("#divRisquesLignes").show();
						evarisk("#divRisquesUnites").hide();
						evarisk("#divDocumentUnique").hide();
						evarisk("#divHistoriqueDocumentUnique").hide();

						evarisk("#ongletRisquesLignes").addClass("selected_tab");
						evarisk("#ongletRisquesUnites").removeClass("selected_tab");
						evarisk("#ongletDocumentUnique").removeClass("selected_tab");
						evarisk("#ongletHistoriqueDocumentUnique").removeClass("selected_tab");

						evarisk("#divRisquesLignes").html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
						evarisk("#divRisquesLignes").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_RISQUE . '", "act":"voirRisqueLigne", "tableElement":"' . $tableElement . '","idElement":' . $idElement . '});
					});

					evarisk("#ongletRisquesUnites").click(function(){
						evarisk("#divRisquesLignes").hide();
						evarisk("#divRisquesUnites").show();
						evarisk("#divDocumentUnique").hide();
						evarisk("#divHistoriqueDocumentUnique").hide();

						evarisk("#ongletRisquesLignes").removeClass("selected_tab");
						evarisk("#ongletRisquesUnites").addClass("selected_tab");
						evarisk("#ongletDocumentUnique").removeClass("selected_tab");
						evarisk("#ongletHistoriqueDocumentUnique").removeClass("selected_tab");
						
						evarisk("#divRisquesUnites").html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
						evarisk("#divRisquesUnites").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_RISQUE . '", "act":"voirRisqueUnite", "tableElement":"' . $tableElement . '","idElement":' . $idElement . '});
					});

					evarisk("#ongletDocumentUnique").click(function(){
						evarisk("#divRisquesLignes").hide();
						evarisk("#divRisquesUnites").hide();
						evarisk("#divDocumentUnique").show();
						evarisk("#divHistoriqueDocumentUnique").hide();

						evarisk("#ongletRisquesLignes").removeClass("selected_tab");
						evarisk("#ongletRisquesUnites").removeClass("selected_tab");
						evarisk("#ongletDocumentUnique").addClass("selected_tab");
						evarisk("#ongletHistoriqueDocumentUnique").removeClass("selected_tab");
						
						evarisk("#divDocumentUnique").html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
						evarisk("#divDocumentUnique").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_DUER . '", "act":"voirDocumentUnique", "tableElement":"' . $tableElement . '","idElement":' . $idElement . '});
					});

					evarisk("#ongletHistoriqueDocumentUnique").click(function(){
						evarisk("#divRisquesLignes").hide();
						evarisk("#divRisquesUnites").hide();
						evarisk("#divDocumentUnique").hide();
						evarisk("#divHistoriqueDocumentUnique").show();

						evarisk("#ongletRisquesLignes").removeClass("selected_tab");
						evarisk("#ongletRisquesUnites").removeClass("selected_tab");
						evarisk("#ongletDocumentUnique").removeClass("selected_tab");
						evarisk("#ongletHistoriqueDocumentUnique").addClass("selected_tab");
						
						evarisk("#divHistoriqueDocumentUnique").html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
						evarisk("#divHistoriqueDocumentUnique").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_DUER . '", "act":"voirHistoriqueDocumentUnique", "tableElement":"' . $tableElement . '","idElement":' . $idElement . '});
					});
				});
			</script>';

			$corpsPostBoxRisque = $scriptRisque . '
				<div id="message' . TABLE_DUER . '" class="updated fade" style="cursor:pointer; display:none;"></div>
				<ul class="eva_tabs">
					<li id="ongletRisquesLignes" class="tabs selected_tab" style="display:inline; margin-left:0.4em;"><label tabindex="3">' . ucfirst(strtolower( __('Risques unitaires', 'evarisk'))) . '</label></li>
					<li id="ongletRisquesUnites" class="tabs" style="display:inline; margin-left:0.4em;"><label tabindex="3">' . ucfirst(strtolower( __('Risques par unit&eacute;', 'evarisk'))) . '</label></li>
					<li id="ongletDocumentUnique" class="tabs" style="display:inline; margin-left:0.4em;"><label tabindex="3">' . ucfirst(strtolower( __('Document Unique', 'evarisk'))) . '</label></li>
					<li id="ongletHistoriqueDocumentUnique" class="tabs" style="display:inline; margin-left:0.4em;"><label tabindex="4">' . ucfirst(strtolower( __('Historique des documents unique', 'evarisk'))) . '</label></li>
				</ul>
				<div id="divRisquesLignes" class="eva_tabs_panel">' . documentUnique::bilanRisque($tableElement, $idElement, 'ligne') . '</div>
				<div id="divRisquesUnites" class="eva_tabs_panel" style="display:none"></div>
				<div id="divDocumentUnique" class="eva_tabs_panel" style="display:none"></div>
				<div id="divHistoriqueDocumentUnique" class="eva_tabs_panel" style="display:none"></div>';

			echo $corpsPostBoxRisque;
		}
	}
