<?php
	require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayDesign.class.php' );

	unset($titres,$classes, $idLignes, $lignesDeValeurs);
	$idLignes = null;
	$idTable = 'pluginOptions';
	$titres[] = __("Domaine de l'option", 'evarisk');
	$titres[] = __("Nom de l'option", 'evarisk');
	$titres[] = __("Valeur", 'evarisk');
	$titres[] = __("Actions", 'evarisk');
	$classes[] = '';
	$classes[] = '';
	$classes[] = '';
	$classes[] = 'optionsActionColumn';
	$optionList = options::getOptionList();
	
	unset($ligneDeValeurs);
	$i=0;
	foreach($optionList as $option)
	{
		$idLignes[] = 'option' . $option->id;
		$domaineOption = $option->domaine;
		switch($domaineOption)
		{
			case 'risk':
				$domaineOption = __('Risques', 'evarisk');
			break;
			case 'task':
				$domaineOption = __('Actions correctives', 'evarisk');
			break;
			case 'user':
				$domaineOption = __('Utilisateurs', 'evarisk');
			break;
		}
		$lignesDeValeurs[$i][] = array('value' => $domaineOption, 'class' => '');
		$lignesDeValeurs[$i][] = array('value' => ucfirst($option->nomAffiche), 'class' => '');
		$lignesDeValeurs[$i][] = array('value' => '<span class="pointer optionValueContainer" id="optionValueContainer' . $option->id . '" >' . $option->valeur . '</span>', 'class' => $option->nom);
		$lignesDeValeurs[$i][] = array('value' => '<span id="editOption-' . $option->id . '" class="editDataTableRow ui-icon" >&nbsp;</span>', 'class' => $option->nom);
		$i++;
	}

	$script = '<script type="text/javascript">
		evarisk(document).ready(function(){
			evarisk("#' . $idTable . ' tfoot").remove();
			oTable = evarisk("#' . $idTable . '").dataTable({
				"fnDrawCallback": function ( oSettings ) {
					if ( oSettings.aiDisplay.length == 0 )
					{
						return;
					}
					
					var nTrs = evarisk("#' . $idTable . ' tbody tr");
					var iColspan = nTrs[0].getElementsByTagName("td").length;
					var sLastGroup = "";
					var ntrsLength = nTrs.length;
					for(i=0; i < ntrsLength; i++)
					{
						var iDisplayIndex = oSettings._iDisplayStart + i;
						var sGroup = oSettings.aoData[ oSettings.aiDisplay[iDisplayIndex] ]._aData[0];
						if ( sGroup != sLastGroup )
						{
							var nGroup = document.createElement( "tr" );
							var nCell = document.createElement( "td" );
							nCell.colSpan = iColspan;
							nCell.className = "group";
							nCell.innerHTML = sGroup;
							nGroup.appendChild( nCell );
							nTrs[i].parentNode.insertBefore( nGroup, nTrs[i] );
							sLastGroup = sGroup;
						}
					}
				},
				"aoColumns": [ 
					{ "bVisible":    false },
					null,
					null,
					null
				],
				"bPaginate": false,
				"bInfo": false,
				"bLengthChange": false,
				"oLanguage": {
					"sSearch": "<span class=\'ui-icon searchDataTableIcon\' >&nbsp;</span>"
				}
			});
			evarisk(".optionValueContainer").click(function(){
				evarisk("#messageOption").hide();
				evarisk("#light").show();
				evarisk("#fade").show();
				evarisk("#optionEdition").html(evarisk("#loadingImg").html());
				evarisk("#optionEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
				{
					"post":"true",
					"table":"' . TABLE_OPTION . '",
					"act":"editOption",
					"id":evarisk(this).attr("id").replace("optionValueContainer", "editOption-")
				});
			});
			evarisk(".editDataTableRow").click(function(){
				evarisk("#messageOption").hide();
				evarisk("#light").show();
				evarisk("#fade").show();
				evarisk("#optionEdition").html(evarisk("#loadingImg").html());
				evarisk("#optionEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
				{
					"post":"true",
					"table":"' . TABLE_OPTION . '",
					"act":"editOption",
					"id":evarisk(this).attr("id")
				});
			});
		});
	</script>';
	
	echo '<div id="messageOption" class="hide updated fade below-h2"></div><div id="ajax-response" ></div><div class="hide" id="loadingImg" ><center><img class="margin36" src="' . PICTO_LOADING_ROUND . '" alt="loading..." /></center></div><div id="light" class="white_content_option" ><div class="closeLightBoxContainer" ><span class="alignright closeLightBoxIcon ui-icon" >&nbsp;</span><span class="alignright" >' . _('Fermer', 'evarisk') . '</span></div><div class="clear" id="optionEdition" ></div></div><div id="fade" class="black_overlay_option" ></div>' . EvaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script);