<?php

class options
{

	function optionMainPage()
	{
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
				case 'fichedeposte':
					$domaineOption = __('Fiches de poste', 'evarisk');
				break;
				case 'recommandation':
					$domaineOption = __('Pr&eacute;conisations', 'evarisk');
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
						"sUrl": "' . EVA_INC_PLUGIN_URL . 'js/dataTable/jquery.dataTables.common_translation.txt"
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
		
		echo EvaDisplayDesign::afficherDebutPage(__('Options du logiciel', 'evarisk'), EVA_OPTIONS_ICON, __('options du logiciel', 'evarisk'), __('options du logiciel', 'evarisk'), TABLE_OPTION, false, '', false) . '<div id="messageOption" class="hide updated fade below-h2"></div><div id="ajax-response" ></div><div class="hide" id="loadingImg" ><center><img class="margin36" src="' . PICTO_LOADING_ROUND . '" alt="loading..." /></center></div><div id="light" class="white_content_option" ><div class="closeLightBoxContainer" ><span class="alignright closeLightBoxIcon ui-icon" >&nbsp;</span><span class="alignright" >' . _('Fermer', 'evarisk') . '</span></div><div class="clear" id="optionEdition" ></div></div><div id="fade" class="black_overlay_option" ></div>' . EvaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script);
	}

	/**
	*	Return complete options list
	*
	*	@return object $optionList The complete list of option for the softare
	*/
	function getOptionList()
	{
		global $wpdb;

		$sql = $wpdb->prepare("SELECT * FROM " . TABLE_OPTION . " WHERE Status = 'Valid' ");
		$optionList = $wpdb->get_results($sql);

		return $optionList;
	}

	/**
	*	Return a specific option from it's name
	*
	*	@param string $optionName The option name of the option we want to get the value
	*	@param string $optionStatus The option status
	*
	*	@return object $optionValue The option value
	*/
	function getOptionValue($optionName, $optionStatus = 'Valid')
	{
		global $wpdb;

		$sql = $wpdb->prepare("SELECT valeur FROM " . TABLE_OPTION . " WHERE Status = '%s' AND nom = '%s' ", $optionStatus, $optionName);
		$option = $wpdb->get_row($sql);

		$optionValue = $option->valeur;

		return $optionValue;
	}

	/**
	*	Return a specific option it's id
	*
	*	@param string $optionId The option identifier we want to get
	*
	*	@return object $option The option informations
	*/
	function getOption($optionId)
	{
		global $wpdb;

		$sql = $wpdb->prepare("SELECT * FROM " . TABLE_OPTION . " WHERE id = '%s' ", $optionId);
		$option = $wpdb->get_row($sql);

		return $option;
	}

	/**
	*	Update an option
	*
	*	@param integer $optionId The option id we want to update
	*	@param string $optionName The option value we want to set
	*
	*	@return object $option The option query result
	*/
	function updateOption($optionId, $optionValue)
	{
		global $wpdb;

		$sql = $wpdb->prepare("UPDATE " . TABLE_OPTION . " SET valeur = '%s' WHERE id = '%s' ", $optionValue, $optionId);
		$option = $wpdb->query($sql);

		return $option;
	}

	/**
	*	Update an option from it's name
	*
	*	@param string $optionName The option name we want to update
	*	@param string $optionValue The option value we want to update
	*
	*	@return object $option The option query result
	*/
	function updateOptionFromName($optionName, $optionValue)
	{
		global $wpdb;

		$sql = $wpdb->prepare("UPDATE " . TABLE_OPTION . " SET valeur = '%s' WHERE nom = '%s' ", $optionValue, $optionName);
		$option = $wpdb->query($sql);
	}

	/**
	*	Create an option
	*
	*	@param string $optionName The option name we want to create
	*	@param string $optionValue The option value we want to create
	*	@param string $optionShownName The option shown name we want to create
	*	@param string $optionDomain The option domain we want to create
	*	@param string $optionType The option type we want to create
	*	@param string $optionStatus The option status we want to create
	*
	*	@return object $option The option query result
	*/
	function createOption($optionName, $optionValue, $optionShownName, $optionDomain, $optionType, $optionStatus)
	{
		global $wpdb;

		$sql = $wpdb->prepare(
			"INSERT INTO " . TABLE_OPTION . " 
				(domaine, nom, nomAffiche, valeur, status, typeOption)
			VALUES 
				('%s', '%s', '%s', '%s', '%s', '%s')", 
		$optionDomain, $optionName, $optionShownName, $optionValue, $optionStatus, $optionType);

		$option = $wpdb->query($sql);

		return $option;
	}


	/**
	*	Create the form to create a new option
	*
	*	@return string $formOutput The complet html output for the form
	*/
	function editOptionForm($optionType = 'ouinon')
	{
		global $optionYesNoList;
		$formOutput = '';

		$typeValeur = EvaDisplayInput::createComboBox('valeur', $optionYesNoList, '');
		if($optionType != 'ouinon')
		{
			$typeValeur = '<input type="text" value="" name="valeur" id="valeur" />';
		}

		/*	The form definition	*/
		$formOutput = 
		'<div id="createOption" >
			<input type="hidden" id="actionOption" name="actionOption" value="update" />
			<input type="hidden" id="idOption" name="idOption" value="" />
			<div class="hide" id="loadingOptionForm" ><center><img class="margin36" src="' . PICTO_LOADING_ROUND . '" alt="loading..." /></center></div>
			<table cellpadding="0" cellspacing="0" summary="option creation form" id="addOptionTable" style="margin:12px 0px 0px;" >
				<tbody>
					<tr>
						<td colspan="2">' . __('&Eacute;dition de ', 'evarisk') . '<span class="bold" id="optionName" ></span></td>
					</tr>
					<tr>
						<td id="optionTypeField" >' . $typeValeur . '</td>
						<td id="saveOption" ><input type="button" class="alignleft button-primary" name="saveCodeButton" id="saveCodeButton" value="' . __('Enregistrer', 'evarisk') . '" /><input type="button" class="alignleft button-primary" name="cancelCodeEditionButton" id="cancelCodeEditionButton" value="' . __('Annuler', 'evarisk') . '" /></td>
					</tr>
				</tbody>
			</table>
		</div>';

		/*	Javascript associated to the form	*/
		$script = 
		'<script type="text/javascript" >
			evarisk(document).ready(function(){
				evarisk("#saveCodeButton").click(function(){
					evarisk("#loadingOptionForm").show();
					evarisk("#addOptionTable").hide();
					evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
					{
						"post":"true",
						"table":"' . TABLE_OPTION . '",
						"act":evarisk("#actionOption").val(),
						"id":evarisk("#idOption").val(),
						"valeur":evarisk("#valeur").val()
					});
				});
				evarisk("#cancelCodeEditionButton").click(function(){
					emptyOptionForm();
				});
			});
		</script>';

		return $formOutput . $script;
	}

}