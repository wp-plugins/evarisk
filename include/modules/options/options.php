<?php
	require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayDesign.class.php' );

	/**
	*	Define the option possible value
	*/
	$optionYesNoList = array();
	$optionYesNoList['oui'] = __('Oui', 'evarisk');
	$optionYesNoList['non'] = __('Non', 'evarisk');

	unset($titres,$classes, $idLignes, $lignesDeValeurs);
	$idLignes = null;
	$idTable = 'pluginOptions';
	$titres[] = __("Nom de l'option", 'evarisk');
	$titres[] = __("Valeur", 'evarisk');
	$classes[] = '';
	$classes[] = '';
	$optionList = options::getOptionList();
	
	unset($ligneDeValeurs);
	$i=0;
	foreach($optionList as $option)
	{
		$optionYesNoList['selected'] = $option->valeur;
		$lineScript = 
			'<script type="text/javascript">
				evarisk(document).ready(function(){
					/* Apply the jEditable handlers to the table */
					evarisk(".' . $option->nom . '").editable( "' . EVA_INC_PLUGIN_URL . 'ajax.php", {
						"data" : \'' . json_encode($optionYesNoList) . '\',
						"type" : "select",
						"submit" : "' . __('Sauvegarder', 'evarisk') . '",
						"cancel" : "' . __('Annuler', 'evarisk') . '",
						"submitdata": function ( value, settings ) {
							return {
								"id": evarisk(this).parent("tr").attr("id").replace("option", ""),
								"post" : true,
								"optionName" : evarisk(this).prev("td").html(),
								"table" : "' . TABLE_OPTION . '",
								"act" : "update"
							};
						},
					});
				});
			</script>';
		$idLignes[] = 'option' . $option->id;
		$nomOption = '';
		$nomOption = ucfirst(str_replace('_', ' ', $option->nom));
		$lignesDeValeurs[$i][] = array('value' => $nomOption, 'class' => '');
		$lignesDeValeurs[$i][] = array('value' => $option->valeur . $lineScript, 'class' => $option->nom);
		$i++;
	}

	$script = '<script type="text/javascript">
		evarisk(document).ready(function(){
			evarisk("#' . $idTable . ' tfoot").remove();
		});
	</script>';
	
	echo EvaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script);