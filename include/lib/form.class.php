<?php
/**
* Form management
* 
* Define the different method to create a form dynamically from a database table field list
* @author Evarisk <dev@evarisk.com>
* @version 5.1.2.9
* @package Digirisk
* @subpackage librairies
*/

/**
* Define the different method to create a form dynamically from a database table field list
* @package Digirisk
* @subpackage librairies
*/
class digirisk_form
{
	/**
	*	Create The complete form by defining the form open and close and call the different function that allows to create the different type of input
	*
	*	@param string $name The name of the form
	*	@param array $input_list The list build by the database class' function that get the type of a table
	*	@param string $method The default method for the form Default is set to post
	*	@param string $action The default action for the form Default is set to empty
	*
	*	@return mixed $the_form The complete html output of the form
	*/
	function form($name, $input_list, $method = 'post', $action = '')
	{
		$the_form_content_hidden = $the_form_content = '';
		foreach($input_list as $input_key => $input_def)
		{
			$the_input = self::check_input_type($input_def);
			$input_name = $input_def['name'];
			$input_value = $input_def['value'];
			$input_type = $input_def['type'];

			if($input_type != 'hidden')
			{
				$label = 'for="' . $input_name . '"';
				if(($input_type == 'radio') || ($input_type == 'checkbox'))
				{
					$label = '';
				}
				$the_form_content .= '
<div>
	<label ' . $label . ' >' . __($input_name, 'evarisk') . '</label>&nbsp;:&nbsp;
	' . $the_input . '
</div>';
			}
			else
			{
				$the_form_content_hidden .= '
	' . $the_input;
			}
		}

		$the_form = '
<form name="' . $name . '" id="' . $name . '" method="' . $method . '" action="' . $action . '" >' . $the_form_content_hidden . $the_form_content . '
</form>';

		return $the_form;
	}

	/**
	*	Check the input type
	*
	*	@param array $input_def The input definition
	*
	*	@return string $the_input
	*/
	function check_input_type($input_def, $input_domain = '')
	{
		$input_option = '';
		if($input_def['option'])
		{
			$input_option = $input_def['option'];
		}
		$valueToPut = '';
		if($input_def['valueToPut'])
		{
			$valueToPut = $input_def['valueToPut'];
		}
		$input_id = $input_def['name'];
		if($input_def['id'])
		{
			$input_id = $input_def['id'];
		}
		$input_name = $input_def['name'];
		if($input_domain != '')
		{
			$input_name = $input_domain . '[' . $input_def['name'] . ']';
		}
		$input_value = $input_def['value'];
		$input_type = $input_def['type'];
		$the_input = '';

		if($input_type == 'text')
		{
			$the_input .= self::form_input($input_name, $input_id, $input_value, 'text', $input_option);
		}
		elseif($input_type == 'textarea')
		{
			$the_input .= self::form_input_textarea($input_name, $input_id, $input_value, $input_option);
		}
		elseif($input_type == 'hidden')
		{
			$the_input .= self::form_input($input_name, $input_id, $input_value, 'hidden', $input_option);
		}
		elseif($input_type == 'select')
		{
			$the_input .= self::form_input_select($input_name, $input_id, $input_def['possible_value'], $input_value, $input_option, $valueToPut);
		}
		elseif($input_type == 'radio')
		{
			$the_input .= self::form_input_check($input_name, $input_id, $input_def['possible_value'], $input_value, 'radio', $input_option);
		}
		elseif($input_type == 'checkbox')
		{
			$the_input .= self::form_input_check($input_name, $input_id, $input_def['possible_value'], $input_value, 'checkbox', $input_option);
		}
		elseif($input_type == 'file')
		{
			$the_input .= self::form_input($input_name, $input_id, $input_value, 'file', $input_option);
		}
		elseif($input_type == 'gallery')
		{
			$the_input .= self::form_input($input_name, $input_id, $input_value, 'text', 'readonly = "readonly"') . 'Gallery field to check';
		}

		return $the_input;
	}

	/**
	*	Create an input type text or hidden or password
	*
	*	@param string $name The name of the field given by the database
	*	@param mixed $value The default value for the field Default is empty
	*	@param string $type The input type Could be: text or hidden or passowrd
	*	@param string $option Allows to define options for the input Could be readonly or disabled or style
	*
	*	@return mixed The output code to add to the form
	*/
	function form_input($name, $id, $value = '', $type = 'text', $option = '')
	{
		$allowedType = array('text', 'hidden', 'password', 'file');
		if(in_array($type, $allowedType))
		{
			return '<input type="' . $type . '" name="' . $name . '" id="' . $id . '" value="' . $value . '" ' . $option . ' />' ;
		}
		else
		{
			return __('Input type not allowed here in ' . __FILE__ . 'at line ' . __LINE__, 'evarisk');
		}
	}

	/**
	*	Create an textarea
	*
	*	@param string $name The name of the field given by the database
	*	@param mixed $value The default value for the field Default is empty
	*	@param string $option Allows to define options for the input Could be maxlength or style
	*
	*	@return mixed The output code to add to the form
	*/
	function form_input_textarea($name, $id, $value = '', $option = '')
	{
		return '<textarea name="' . $name.' " id="' . $id . '" ' . $option . ' rows="4" cols="10" >' . $value . '</textarea>';
	}

	/**
	*	Create a combo box input regarding to the type of content given in parameters could be an array or a wordpress database object
	*
	*	@param string $name The name of the field given by the database
	*	@param mixed $content The list of element to put inot the combo box Could be an array or a wordpress database object with id and nom as field
	*	@param mixed $value The selected value for the field Default is empty
	*	@param string $option Allows to define options for the input Could be onchange
	*
	*	@return mixed $output The output code to add to the form
	*/
	function form_input_select($name, $id, $content, $value = '', $option = '', $optionValue = '')
	{
		global $comboxOptionToHide;

		$output = '';
		if(is_array($content) && (count($content) > 0))
		{
			$output = '<select id="' . $id . '" name="' . $name . '" ' . $option . ' >';

			foreach($content as $index => $datas)
			{
				if(is_object($datas) && (!is_array($comboxOptionToHide) || !in_array($datas->id, $comboxOptionToHide)))
				{
					$selected = ($value == $datas->id) ? ' selected="selected" ' : '';
					$dataText = __($datas->name ,'evarisk');
					if(isset($datas->code))
					{
						$dataText = __($datas->code ,'evarisk');
					}
					$output .= '<option value="' . $datas->id . '" ' . $selected . ' >' . $dataText . '</option>';
				}
				elseif(!is_array($comboxOptionToHide) || !in_array($datas, $comboxOptionToHide))
				{
					$valueToPut = $datas;
					$selected = ($value == $datas) ? ' selected="selected" ' : '';
					if($optionValue == 'index')
					{
						$valueToPut = $index;
						$selected = ($value == $index) ? ' selected="selected" ' : '';
					}
					$output .= '<option value="' . $valueToPut . '" ' . $selected . ' >' . __($datas ,'evarisk') . '</option>';
				}
			}

			$output .= '</select>';
		}

		return $output;
	}

	/**
	*	Create a checkbox input
	*
	*	@param string $name The name of the field given by the database
	*	@param string $id The identifier of the field
	*	@param string $type The input type Could be checkbox or radio
	*	@param mixed $content The list of element to put inot the combo box Could be an array or a wordpress database object with id and nom as field
	*	@param mixed $value The selected value for the field Default is empty
	*	@param string $option Allows to define options for the input Could be onchange
	*
	*	@return mixed $output The output code to add to the form
	*/
	function form_input_check($name, $id, $content, $value = '', $type = 'checkbox', $option = '')
	{
		$allowedType = array('checkbox', 'radio');
		if(in_array($type, $allowedType))
		{
			if(is_array($content) && (count($content) > 0))
			{
				foreach($content as $index => $datas)
				{
					if(is_object($datas))
					{
						$id = $name . '_' . $datas->nom;
						$checked = ($value == $datas->id) ? ' checked="checked" ' : '';
					}
					else
					{
						$id = $name . '_' . $datas;
						$checked = ($value == $datas) ? ' checked="checked" ' : '';
						$output .= '<input type="' . $type . '" name="' . $name . '" id="' . $id . '" value="' . $datas . '" ' . $checked . ' ' . $option . ' />' ;
					}
				}
			}
			else
			{
				$checked = (($value != '') && ($value == $content)) ? ' checked="checked" ' : '';
				$output .= '<input type="' . $type . '" name="' . $name . '" id="' . $id . '" value="' . $content . '" ' . $checked . ' ' . $option . ' />' ;
			}

			return $output;
		}
		else
		{
			return __('Input type not allowed here in ' . __FILE__ . 'at line ' . __LINE__, 'evarisk');
		}
	}

}