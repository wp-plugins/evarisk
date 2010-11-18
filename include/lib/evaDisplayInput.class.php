<?php
/***
 * This class contains the methods allowing to dipslay the main inputs.
 * @author Evarisk
 * @version v5.0
 */
require_once(EVA_CONFIG );
require_once(EVA_LIB_PLUGIN_DIR . 'arborescence.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/groupement/eva_groupement.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/uniteDeTravail/uniteDeTravail.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'danger/categorieDangers/categorieDangers.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'danger/danger/evaDanger.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'veilleReglementaire/evaQuestion.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'veilleReglementaire/evaGroupeQuestion.class.php');
class EvaDisplayInput {

	/**
	  * Open a form.
	  * @param string $method Method use for the form (POST/GET).
	  * @param int $id Id attribut for the form.
	  * @param string $name Name attribute for the form.
	  * @return string Form openning.
	  */
	static function ouvrirForm($method, $id, $name)
	{
		
		return '<form method="' . $method . '" id="' . $id . '" name="' . $name . '" enctype="multipart/form-data">';
	}
	
	/**
	  * Close a form.
	  * @param int $id Id attribut of the form.
	  * @return string Form closure.
	  */
	static function fermerForm($id)
	{
		$script = '
			<script type="text/javascript">
				$(document).ready(function() {
					$("#' . $id . ' :input").filter(".input_required").prev("br").prev("label").addClass("for_required_input");
				});
			</script>';
		return $script . '</form>';
	}
	
	/**
	  * Create the script needed by the input.
	  * @param int $id Id attribut of the input.
	  * @param string $contenuInput Initial filling of the input.
	  * @param string $contenuAide Filling of the input if it is empty.
	  * @param string $labelInput Label text.
	  * @param bool $grise Is the input initially fill by non persistante data.
	  * @param string $class Class to add to the input.
	  * @param string $limitation If this variable is equal to "Number", only number can fill the input. <br />If this variable is equal to "Float", only number and one "." can fill the input. <br />If this variable is equal to "Date", the input become a date picker.
	  * @return string the script needed by the input.
	  */
	static function getScriptInput($id, $contenuInput, $contenuAide, $labelInput, $grise, $class='', $limitation='')
	{
		if($grise)
		{
			$class = $class . ' form-input-tip';
		}
		$script = '<script type="text/javascript">
			$(document).ready(function() {
				document.getElementById("' . $id . '").className = "' . $class . '"; ';
		if(ucfirst(strtolower($limitation)) != 'Date')
		{
			$script = $script . '$(\'#' . $id . '\').focus(function() {
						if($(\'#' . $id . '\').is(".form-input-tip"))
						{
							$(\'#' . $id . '\').val("");
							$(\'#' . $id . '\').removeClass(\'form-input-tip\');
						}
					});
					
					$(\'#' . $id . '\').blur(function() {
						if($(\'#' . $id . '\').val() == "")
						{
							$(\'#' . $id . '\').addClass(\'form-input-tip\');
							$(\'#' . $id . '\').val("' . $contenuAide . '");
						}
					});
					
					if($(\'#' . $id . '\').val() == "")
					{
						$(\'#' . $id . '\').addClass(\'form-input-tip\');
						$(\'#' . $id . '\').val("' . $contenuAide . '");
					}';
		}
		switch(ucfirst(strtolower($limitation)))
		{
			case 'Number' :
				$script = $script . '$(\'#' . $id . '\').keypress(function(event) {
					if (event.which && (event.which < 48 || event.which >57) && event.which != 8) {
						event.preventDefault();
					}
				});';
				break;
			case 'Float' :
				$script = $script . '$(\'#' . $id . '\').keypress(function(event) {
						if (event.which && (event.which < 48 || event.which >57) && event.which != 8 && event.which != 46) {
							event.preventDefault();
						}
						if (event.which == 46)
						{
							var reg = /\./;
							if(reg.exec($(\'#' . $id . '\').val()) != null)
							{
								event.preventDefault();
							}
						}
					});';
				break;
			case 'Date' :
				$locale = preg_replace('/([^_]+).+/', '$1', get_locale());
				$locale = ($locale == 'en') ? '' : $locale;
				$script = $script . '$(\'#' . $id . '\').datepicker($.datepicker.regional["' . $locale . '"]);';
				$script = $script . '$(\'#' . $id . '\').datepicker("option", "dateFormat", "yy-mm-dd");';
				$script = $script . '$(\'#' . $id . '\').datepicker("option", "changeMonth", true);';
				$script = $script . '$(\'#' . $id . '\').datepicker("option", "changeYear", true);';
				$script = $script . '$(\'#' . $id . '\').datepicker("option", "navigationAsDateFormat", true);';
				break;
		}
		$script = $script . '
          $("#' . $id . '").val("' . $contenuInput . '"); 
				});
			</script>';
		return $script;
	}

	
	
	/**
	  * Create the script needed by the input.
	  * @see getScriptInput.
	  * @param string $type Type of the input (text, button, textarea, hidden).
	  * @param int $id Id attribut of the input.
	  * @param string $contenuInput Initial filling of the input.
	  * @param string $contenuAide Filling of the input if it is empty.
	  * @param string $labelInput Label text.
	  * @param string $nomChamps Name attribut of the input.
	  * @param bool $grise Is the input initially fill by non persistante data.
	  * @param int $tailleMaximum length of the input/Number of rows for a textarea.
	  * @param string $class Class to add to the input.
	  * @param string $limitation If this variable is equal to "Number", only number can fill the input. <br />If this variable is equal to "Float", only number and one "." can fill the input. <br />If this variable is equal to "Date", the input become a date picker.
	  * @param string $width Width attribut of the input.
	  * @param string $script Additional script for the input.
	  * @param string $float Where must float the div : right or left.
	  * @return string The input.
	  */
	static function afficherInput($type, $id, $contenuInput, $contenuAide, $labelInput, $nomChamps, $grise=false, $obligatoire=false, $taille = 255, $classe='', $limitation='', $width='100%', $script='', $float='', $withoutClear=false)
	{
		if($obligatoire)
		{
			$classe = 'input_required ' . $classe;
		}
		$input = EvaDisplayInput::getScriptInput($id, $contenuInput, $contenuAide, $labelInput, $grise, $classe, $limitation) . '<div style="float:' . $float . '">' . $script;
		if($labelInput != null)
		{
			$input = $input . '<label for="' . $id . '">' . $labelInput . '</label>';
		}
		if($type == 'button' AND in_array('button-primary', explode(' ', $classe)))
		{
			$input = $input . '<br />';
		}
		switch($type)
		{
			case 'textarea':
				$input = $input . '
						<textarea style="clear: both; width: ' . $width . '" id="' . $id . '" rows=\'' . $taille . '\'';
						$input = $input . ' name="' . $nomChamps . '" cols=\'' . $taille . '\' tabindex="1">' . $contenuInput . '</textarea>';
				break;
			default:
				$input = $input . '
						<input style="clear: both; width: ' . $width . '" maxlength="' . $taille . '" type="' . $type . '" autocomplete="off" id="' . $id . '" value="' . $contenuInput . '" tabindex="1" name="' . $nomChamps . '"/>';
				break;
		}
		$input = $input . '</div>';
		if($type != 'hidden')
		{
			if(!$withoutClear)
			{
				$input = $input . '<br  class="clear" />';
			}
		}
		return $input;
	}
	
	/**
	  * Create an comboBox with indentation to simulate an tree
	  * @see creerComboBoxArborescente.
	  * @param string $elements All elements of the select.
	  * @param int $idSelect Id attribut of the select.
	  * @param string $labelSelect Label text.
	  * @param string $nameSelect Name attribut of the select..
	  * @param string $valeurDefaut Displayed text for the first element if not in $elements.
	  * @param int $selection Id of selected element.
	  * @param array $tabValue List of the value attribute (ordered) for the select entries (if there is any, it is the "id" field in the table of the element)
	  * @param array $tabDisplay List of the value to display (ordered) for the select entries (if there is any, it is the "nom" field in the table of the element).
	  * @return string The comboBox.
	  */
	static function afficherComboBox($elements, $idSelect, $labelSelect, $nameSelect, $valeurDefaut = "", $selection = "", $tabValue = null, $tabDisplay = null)
	{
		$comboBox = '<label id="lbl_' . $idSelect . '" for="' . $idSelect . '">' . $labelSelect . '</label>
						<select class="inputCategorieMere" id="' . $idSelect . '" name="' . $nameSelect . '" autocomplete="off">';
		if($valeurDefaut != "")
			$comboBox = $comboBox . '<option value="">' . $valeurDefaut . '</option>';
		for($i=0; $i<count($elements); $i++)
		{
			$element = $elements[$i];
			if($tabValue != null)
			{
				$comboBox = $comboBox . '<option value="' . $tabValue[$i] . '"';
			}
			else
			{
				$comboBox = $comboBox . '<option value="' . $element->id . '"';
			}
			if((isset($selection)) AND $selection != "")
			{
				if($element == $selection)
				{
					$comboBox = $comboBox . ' selected="selected"';
				}
			}
			if($tabDisplay != null)
			{
				$comboBox = $comboBox . '> ' . $tabDisplay[$i] . ' </option>';
			}
			else
			{
				$comboBox = $comboBox . '> ' . $element->nom . ' </option>';
			}
		}
		$comboBox = $comboBox . '</select>';
		return $comboBox;
	}
	
	/**
	  * Create an comboBox with indentation to simulate an tree
	  * @see creerComboBoxArborescente.
	  * @param string $racine Root object of the tree.
	  * @param string $table Table name of the tree.
	  * @param int $idSelect Id attribut of the select.
	  * @param string $labelSelect Label text.
	  * @param string $nameSelect Name attribut of the select..
	  * @param string $valeurDefaut Displayed text for the root.
	  * @param int $selection Id of selected element.
	  * @return string The comboBox.
	  */
	static function afficherComboBoxArborescente($racine, $table, $idSelect, $labelSelect, $nameSelect, $valeurDefaut = "", $selection = -1)
	{
		$elements = Arborescence::getFils($table, $racine);
		$trouveElement = count($elements);	
		$maListe = '';
		if($trouveElement)
		{
			$maListe = EvaDisplayInput::creerComboBoxArborescente($elements, $table, 1, $selection);
		}
		return '<label id="lbl_' . $idSelect . '" for="' . $idSelect . '">' . $labelSelect . '</label>
						<select class="inputSelectArborescent" id="' . $idSelect . '" name="' . $nameSelect . '" autocomplete="off">
							<option value="' . $racine->id . '">' . $valeurDefaut . '</option>
							' . $maListe . '</select>';							
	}
	
	/**
	  * Fill the comboBox make by afficherComboBoxArborescente.
	  * @see afficherComboBoxArborescente.
	  * @param string $elements List of son of an element.
	  * @param string $table Table name of the tree.
	  * @param int $niveau Level in classical tree display.
	  * @param int $selection Id of selected element.
	  * @return string The comboBox.
	  */
	static function creerComboBoxArborescente($elements, $table, $niveau, $selection)
	{
		$maListe = '';
		foreach ($elements as $element )
		{
			$space = '';
			for($i=0; $i<(3*$niveau); $i++)
			{
				$space = $space . '&nbsp;';
			}
			$selected = '';
			if($element->id == $selection)
			{
				$selected = ' selected="selected"';
			}
			$supplementText = '';
			if($table == TABLE_GROUPE_QUESTION)
			{
				$supplementText = $element->code . '. ';
			}
			
			$maListe = $maListe . '<option value="' . $element->id . '" ' . $selected . ' > ' . $space . $supplementText . $element->nom . '&nbsp;' . ' </option>';
			$elements_fils = Arborescence::getFils($table, $element);
			$trouveElement = count($elements_fils);			
			if($trouveElement)
			{
				$maListe = $maListe . EvaDisplayInput::creerComboBoxArborescente($elements_fils, $table, $niveau+1, $selection);
			}
		}
		return $maListe;
	}
}