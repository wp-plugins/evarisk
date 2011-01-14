<?php
/**
*	The different utilities to manage attribute
*
*	@package 		Evarisk
*	@subpackage eav_attribute
* @author			Evarisk team <contact@evarisk.com>
*/

require_once(EVA_LIB_PLUGIN_DIR . 'eav/eav_entity.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayInput.class.php');

class eav_attribute extends eav_entity
{
	/**
	* The identifier of the entity used in the current instance
	*/
	protected $_currentEntityTypeId = 0;

	/**
	* The query we are doing in order to update the value of attributes for a given user
	*/
	protected $_attributesValueQuery = '  ';

	/**
	*	Set the entity id as a protected var for the current instance
	*
	*	@param integer $entityTypeId The entity we are using for actual instance
	*
	*	@return integer $_currentEntityTypeId Set the entity as a protected var
	*/
	function setCurrentEntityTypeId($entityTypeId)
	{
		return $this->_currentEntityTypeId = $entityTypeId;
	}


	/**
	*	Get the query that will be launch to update different attribute
	*
	*	@return mixed $this->_attributesValueQuery The query we will launch to update information
	*/
	function getAttributesValueQuery()
	{
		return $this->_attributesValueQuery;
	}


	/**
	*	Get the attributes list for a given entity
	*
	*	@return array $attributesList An array containing all attributes related to the entity
	*/
	function getEntityAttributeList()
	{
		global $wpdb;
		$attributesList = array();

		$query = 	
			$wpdb->prepare("SELECT *
			FROM " . TABLE_ATTRIBUTE . "
			WHERE entity_type_id = '%s'
				AND attribute_status != 'deleted'
			ORDER BY attribute_id", $this->_currentEntityTypeId);
		$attributesList = $wpdb->get_results($query);

		return $attributesList;
	}


	/**
	*	Get an attribute identifier form a given attribute code
	*
	* @param string $attributeCode The code of the attribute we want to have the identifier
	*
	*	@return integer $attributeId The id of the requested attribute
	*/
	function getAttributeIdFromCode($attributeCode)
	{
		global $wpdb;
		$attributeId = array();

		$query = 	
			$wpdb->prepare("SELECT attribute_id
			FROM " . TABLE_ATTRIBUTE . "
			WHERE attribute_code = '%s' 
			ORDER BY attribute_id", $attributeCode);
		$attribute = $wpdb->get_col($query);
		$attributeId = $attribute[0];

		return $attributeId;
	}


	/**
	*	Get the list of attribute composing a group
	*
	*	@param mixed $groupName The group name we want to have the attribute list
	*
	*	@return array $attributeListByGroup An object containing all attributes wich are in the requested group
	*/
	function getAttributeListByGroup($groupName, $entityId)
	{
		global $wpdb;
		$attributeListByGroup = array();

		$query = 
			$wpdb->prepare(
			'SELECT ATTRIBUTES.frontend_label, ATTRIBUTES.frontend_input, ATTRIBUTES.attribute_code, ATTRIBUTES.attribute_id, ATTRIBUTES.backend_type,
				ATTDATETIME.value AS VALUE_DATETIME, ATTDECIMAL.value AS VALUE_DECIMAL, ATTINT.value AS VALUE_INT, ATTTEXT.value AS VALUE_TEXT, ATTVARCHAR.value AS VALUE_VARCHAR
			FROM ' . TABLE_ENTITY_ATTRIBUTE_LINK . ' AS EALINK
				INNER JOIN ' . TABLE_ATTRIBUTE_GROUP . ' AS ATTRGROUP ON (ATTRGROUP.attribute_group_id = EALINK.attribute_group_id)
				INNER JOIN ' . TABLE_ATTRIBUTE . ' AS ATTRIBUTES ON (ATTRIBUTES.attribute_id = EALINK.attribute_id)
				INNER JOIN ' . TABLE_ENTITY . ' AS ENTITIES ON (ENTITIES.entity_type_id = EALINK.entity_type_id)
					LEFT JOIN ' . sprintf(TABLE_ATTRIBUTE_VALUE,"users_","datetime") . ' AS ATTDATETIME ON ((ATTDATETIME.attribute_id = ATTRIBUTES.attribute_id) AND (ATTDATETIME.entity_id = "%1$d"))
					LEFT JOIN ' . sprintf(TABLE_ATTRIBUTE_VALUE,"users_","decimal") . ' AS ATTDECIMAL ON ((ATTDECIMAL.attribute_id = ATTRIBUTES.attribute_id) AND (ATTDECIMAL.entity_id = "%1$d"))
					LEFT JOIN ' . sprintf(TABLE_ATTRIBUTE_VALUE,"users_","int") . ' AS ATTINT ON ((ATTINT.attribute_id = ATTRIBUTES.attribute_id) AND (ATTINT.entity_id = "%1$d"))
					LEFT JOIN ' . sprintf(TABLE_ATTRIBUTE_VALUE,"users_","text") . ' AS ATTTEXT ON ((ATTTEXT.attribute_id = ATTRIBUTES.attribute_id) AND (ATTTEXT.entity_id = "%1$d"))
					LEFT JOIN ' . sprintf(TABLE_ATTRIBUTE_VALUE,"users_","varchar") . ' AS ATTVARCHAR ON ((ATTVARCHAR.attribute_id = ATTRIBUTES.attribute_id) AND (ATTVARCHAR.entity_id = "%1$d"))
			WHERE EALINK.entity_type_id = "%2$d"
				AND ATTRGROUP.attribute_group_name = "%3$s"
			GROUP BY ATTRIBUTES.attribute_id
			ORDER BY EALINK.sort_order ASC', 
				$entityId, $this->_currentEntityTypeId, $groupName);
		$attributeListByGroup = $wpdb->get_results($query, 'ARRAY_A');

		return $attributeListByGroup;
	}

	/**
	*
	*/
	function setAttributeOption($attributeId)
	{
		global $wpdb;

		$query = "INSERT INTO " . TABLE_ATTRIBUTE_OPTION . " (option_id, attribute_id) VALUES ('', '" . $attributeId . "')";
		$wpdb->query($query);
	}
	/**
	*
	*/
	function setAttributeOptionValues($attributeId)
	{
		global $wpdb;
		$moreQuery = "  ";

		/*	Get the option id for the current attribute	*/
		$query = "SELECT option_id FROM " . TABLE_ATTRIBUTE_OPTION . " WHERE attribute_id = '" . $attributeId . "' LIMIT 1";
		$optionId = $wpdb->get_col($query);

		if(is_array($_POST['existingDropDownChoice']) && (count($_POST['existingDropDownChoice']) > 0))
		{
			foreach($_POST['existingDropDownChoice'] as $key => $value)
			{
				$moreQuery .= " ('" . $key . "', '" . $optionId[0] . "', '" . $value . "'), ";
			}
		}

		if(is_array($_POST['newDropDownChoice']) && (count($_POST['newDropDownChoice']) > 0))
		{
			foreach($_POST['newDropDownChoice'] as $key => $value)
			{
				if($value != '')
				{
					$moreQuery .= " ('', '" . $optionId[0] . "', '" . $value . "'), ";
				}
			}
		}

		$moreQuery = substr($moreQuery, 0, -2);
		if( trim($moreQuery) != "" )
		{
			$query = $wpdb->prepare(
				"REPLACE INTO " . TABLE_ATTRIBUTE_OPTION_VALUE . " 
					(value_id, option_id, value) VALUES " . $moreQuery);
			$wpdb->query($query);
		}
	}
	/**
	*	Get the different options for an attribute wich is a dropdown or a list of possibilities
	*
	*	@param integer $attributeId The identifier of the attribute we want to have the option list
	*
	*	@return array $optionList An composed array containing the different option formatted like: array('id'=>'theId','nom'=>'theName')
	*/
	function GetAttributeOption($attributeId)
	{
		global $wpdb;
		$optionList = array();

		$query = 
			$wpdb->prepare("SELECT OPTIONVALUE.value_id AS id, OPTIONVALUE.value AS nom
			FROM " . TABLE_ATTRIBUTE_OPTION . " AS AOPTION
				INNER JOIN " . TABLE_ATTRIBUTE_OPTION_VALUE . " AS OPTIONVALUE ON (OPTIONVALUE.option_id = AOPTION.option_id)
			WHERE AOPTION.attribute_id = '%s' ",
			$attributeId);
		$optionList = $wpdb->get_results($query);

		return $optionList;
	}

	/**
	*	Get the selected options for an attribute wich is a dropdown or a list of possibilities
	*
	*	@param integer $attributeId The identifier of the attribute we want to have the option list
	*
	*	@return array $optionList An composed array containing the different option formatted like: array('id'=>'theId','nom'=>'theName')
	*/
	function GetAttributeSelectedOption($attributeId, $selectedValue = '')
	{
		global $wpdb;
		$optionList = array();

		$query = 
			"SELECT OPTIONVALUE.value_id AS id, OPTIONVALUE.value AS nom
			FROM " . TABLE_ATTRIBUTE_OPTION . " AS AOPTION
				INNER JOIN " . TABLE_ATTRIBUTE_OPTION_VALUE . " AS OPTIONVALUE ON (OPTIONVALUE.option_id = AOPTION.option_id)
			WHERE AOPTION.attribute_id = '%d' ";
		if($selectedValue != '')
		{
			$query .= " 
				AND OPTIONVALUE.value_id = '" . $selectedValue . "' ";
		}
		$query = $wpdb->prepare($query, $attributeId);

		$optionList = $wpdb->get_results($query);

		return $optionList;
	}


	/*	Bloc containing all methods that are related to attributes output 		*/
		/**
		* Output a row in the main grid
		*/
		function attributeRowOutput()
		{
			$attributeToOutPut = $this->getEntityAttributeList();

			$i=0;
			foreach ($attributeToOutPut as $attributesInfos ) :
?>
		<tr id="ut-<?php echo $attributesInfos->attribute_id . '"'; if(($i%2) == 0) {echo ' class="alternate"';} ?> valign="top">
			<th class="check-column" scope="row">
			<!--	<input type="checkbox" value="<?php echo $attributesInfos->attribute_id; ?>" name="attribute[]"/> -->
			</th>
			<td><strong><a onclick="javascript:evarisk('#act').val('mod');evarisk('#id').val('<?php echo $attributesInfos->attribute_id; ?>');evarisk('#attributeManagementForm').submit();" ><?php echo stripcslashes($attributesInfos->frontend_label); ?></a></strong></td>
			<td><strong><?php echo $attributesInfos->frontend_input ?></strong></td>
		</tr>
<?php
				$i++;
			endforeach;

			if($i <= 0)
			{
?>
		<tr id="ut-0" valign="top" >
			<th colspan="3" class="check-column" style="text-align:center;" scope="row"><?php echo __('Aucun r&eacute;sultat','evarisk'); ?></th> 
		</tr>
<?php
			}

			return null;
		}

		/**
		*	Get the list of atttribute for a given attribute group and output it as a form
		*
		*	@param mixed $groupName The group Name we want to output
		*
		*	@return mixed $theOutput The complete output template composed by the different form element
		*/
		function attributeFormOutput($groupName, $entityId)
		{
			$theOutput = '';
			$attributeListToOutput = $this->getAttributeListByGroup($groupName, $entityId);

			if(count($attributeListToOutput) > 0)
			{
				foreach ($attributeListToOutput as $attributesInfos ) :
					$elementId = $attributesInfos['attribute_code'];
					$elementName = 'userAttributes[' . $attributesInfos['backend_type'] . '][' . $attributesInfos['attribute_code'] . ']';
					$elementLabel = $attributesInfos['frontend_label'];
					$value = $attributesInfos['VALUE_' . strtoupper($attributesInfos['backend_type'])];

					switch($attributesInfos['frontend_input'])
					{
						case 'select':
							$theOutput .= EvaDisplayInput::afficherComboBox($this->GetAttributeOption($attributesInfos['attribute_id']), $elementId, $elementLabel, $elementName, '', $this->GetAttributeSelectedOption($attributesInfos['attribute_id'],$value));
							break;
						case 'integer':
							$theOutput .= EvaDisplayInput::afficherInput('text', $elementId, $value, '', $elementLabel, $elementName, false, false, '', '', 'Number', '25em', '', '');
							break;
						case 'decimal':
							$theOutput .= EvaDisplayInput::afficherInput('text', $elementId, $value, '', $elementLabel, $elementName, false, false, '', '', 'Float', '25em', '', '');
							break;
						case 'date':
							$theOutput .= EvaDisplayInput::afficherInput('text', $elementId, $value, '', $elementLabel, $elementName, false, false, '', '', 'Date', '25em', '', '');
							break;
						default:
							switch($attributesInfos['frontend_input'])
							{
								case 'textarea':
									$length = 5;
									break;
								default:
									$length = 255;
									break;
							}
							$theOutput .= EvaDisplayInput::afficherInput($attributesInfos['frontend_input'], $elementId, $value, '', $elementLabel, $elementName, false, false, $length, '', '', '25em', '', '');
							break;
					}
				endforeach;

				$theOutput = '<div class="eav-input" >' . $theOutput . '</div>';
			}

			return $theOutput;
		}

		/**
		*	Create a dropdown with the different fied type available for an attribute
		*/
		function attributeInputTypeDropDown($selectedType, $disable = false)
		{
			global $attributeInputType;
			$inputTypeDropDown = '';

			$disableSelect = '';
			if($disable)
			{
				$disableSelect = ' disabled = "disabled" ';
			}
			$inputTypeDropDown .= '<select ' . $disableSelect . ' id="attribute_frontend_input" name="attribute_frontend_input" >';
			foreach($attributeInputType as $type => $typeOutputText)
			{
				$selected = '';
				if($type == $selectedType)
				{
					$selected = ' selected = "selected" ';
				}
				$inputTypeDropDown .= '<option value="' . $type . '" ' . $selected . '>' . $typeOutputText . '</option>';
			}
			$inputTypeDropDown .= '</select>';

			return $inputTypeDropDown;
		}

	/*	Bloc containing all methods that are related to attribute values set	*/
		/**
		* Prepare just the begin of the query. Specify the table name and the different column. It allow to do only one request for all attributes of the same type
		*
		*	@param string $attributeValueType The type of the attribute, needed to update the good database table
		*/
		function createAttributesValueHeaderQuery($attributeValueType)
		{
			global $wpdb;
			$entityTablePrefix = $this->getEntityInformation($this->_currentEntityTypeId, 'entity_type_id', value_table_prefix);

			$this->_attributesValueQuery .= 
					"REPLACE INTO " . sprintf(TABLE_ATTRIBUTE_VALUE,$entityTablePrefix,$attributeValueType) . " 
						(value_id, entity_type_id, attribute_id, entity_id, value) 
					VALUES ";

			return true;
		}
		
		/**
		* Prepare the list of different values we will set in database for a given attribute set type
		*
		*	@param string $attributeCode The attribute code sand in the update form
		*	@param integer $entityId The entity id (user id / product id) 
		*	@param mixed $value The attribute value we have to set in database
		*
		*/
		function createAttributesValueQuery($attributeCode, $entityId, $value)
		{
			global $wpdb;
			$attributeId = $this->getAttributeIdFromCode($attributeCode);
			$entityTablePrefix = $this->getEntityInformation($this->_currentEntityTypeId, 'entity_type_id', value_table_prefix);

			$this->_attributesValueQuery .= 
				$wpdb->prepare(
					" ('', %s, %s, %s, %s), ",  $this->_currentEntityTypeId, $attributeId, $entityId, $value );

			return true;
		}

		/**
		* Trim the attribute update query
		*
		*	@param boolean $empty Allow to completely empty the request or just to dry it
		*/
		function attributesValueQueryTrimmer($empty = false)
		{
			if($empty)
			{
				$this->_attributesValueQuery = '  ';
			}
			else
			{
				$this->_attributesValueQuery = substr($this->_attributesValueQuery,0,-2) . '; ';
			}

			return true;
		}

		/**
		*	Execute the query to set the different value for the attributes.
		*/
		function setAttributesValue()
		{
			global $wpdb;
			$wpdb->query($this->getAttributesValueQuery());

			return true;
		}


	/*	Bloc containing all methods allowing to manage attributes (Creation/Modification)	*/
		/**
		*	Prepare the different field before use them in the query
		*
		*	@param array $prm An array containing the fields to prepare
		*	@param mixed $operation The type of query we are preparing the vars for
		*
		*	@return mixed $preparedFields The fields ready to be injected in the query
		*/
		function prepareAttributeQuery($prm, $operation = 'creation')
		{
			$preparedFields = array();

			foreach($prm as $field => $value)
			{
				if($field != 'id')
				{
					if($operation == 'creation')
					{
						$preparedFields['fields'][] = $field;
						$preparedFields['values'][] = "'" . mysql_real_escape_string($value) . "'";
					}
					elseif($operation == 'update')
					{
						$preparedFields['values'][] = $field . " = '" . mysql_real_escape_string($value) . "'";
					}
				}
			}

			return $preparedFields;
		}
		/**
		*	Create an attribute
		*
		*	@param array $prm An array containing the different information needed for the attribute
		*
		*	@return array $status An array containing the result of the method
		*/
		function createAttribute($prm)
		{
			global $wpdb;
			$status = array();

			$preparedFields = $this->prepareAttributeQuery($prm['attribute'],'creation');

			if( !isset($prm['attribute']['frontend_label']) || (trim($prm['attribute']['frontend_label']) == '') )
			{
				$status['result'] = 'error';
				$status['errors']['mandatory_field'] = __('Le champs label est obligatoire');
			}
			else
			{
				$query = 
					$wpdb->prepare("INSERT INTO " . TABLE_ATTRIBUTE . "
						(" . implode(', ', $preparedFields['fields']) . ")
					VALUES
						(" . implode(', ', $preparedFields['values']) . ")");
				$wpdb->query($query);

				$status['result'] = 'ok';
				$status['id'] = $wpdb->insert_id;

				$this->setAttributeOption($status['id']);
				$this->setAttributeOptionValues($status['id']);

				$this->assignAttributeToGroup($prm['assignation']['entity_type_id'], $prm['assignation']['attributeSetId'], $prm['assignation']['attributeGroupId'], $status['id']);
			}

			return $status;
		}
		/**
		*	Create an attribute
		*
		*	@param array $prm An array containing the different information needed for the attribute
		*
		*	@return array $status An array containing the result of the method
		*/
		function updateAttribute($prm)
		{
			global $wpdb;
			$status = array();

			$preparedFields = $this->prepareAttributeQuery($prm['attribute'],'update');

			if( !isset($prm['attribute']['frontend_label']) || (trim($prm['attribute']['frontend_label']) == '') )
			{
				$status['result'] = 'error';
				$status['errors']['mandatory_field'] = __('Le champs label est obligatoire');
			}
			else
			{
				$query = 
					$wpdb->prepare(
					"UPDATE " . TABLE_ATTRIBUTE . " 
					SET " . implode(', ', $preparedFields['values']) . " 
					WHERE attribute_id = '%s' ",
					$prm['attribute']['id']);
				$wpdb->query($query);

				$status['result'] = 'ok';
				$status['id'] = $prm['attribute']['id'];

				$this->setAttributeOptionValues($status['id']);

				$this->assignAttributeToGroup($prm['assignation']['entity_type_id'], $prm['assignation']['attributeSetId'], $prm['assignation']['attributeGroupId'], $status['id']);
			}

			return $status;
		}
		/**
		*
		*/
		function assignAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, $attributeId)
		{
			global $wpdb;
			$query = 
				$wpdb->prepare(
				"REPLACE INTO " . TABLE_ENTITY_ATTRIBUTE_LINK . "
					(entity_attribute_id, entity_type_id, attribute_set_id, attribute_group_id, attribute_id, sort_order)
				VALUES
					('',%s,%s,%s,%s,(sort_order+1))",
					$entityTypeId, $attributeSetId, $attributeGroupId, $attributeId);
			$wpdb->query($query);
		}
}