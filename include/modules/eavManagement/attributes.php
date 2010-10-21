<?php

	require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php' );
	include_once(EVA_INC_PLUGIN_DIR . 'config/config.php' );

	$attributeAction = isset($_POST['act']) ? eva_tools::IsValid_Variable($_POST['act']) : '';
	$attributeId = isset($_POST['id']) ? eva_tools::IsValid_Variable($_POST['id']) : '';

	$disableInputTypeDropDown = false;
	$createOK = $updateOK = $deleteOK = false;
	$actionButtonLabel = __('Ajouter un attribut');

	/* Define the different column to output in grid	*/
	$user_attributes_columns = array();
	$user_attributes_columns['cb'] = '';//'<input type="checkbox" />';
	$user_attributes_columns['frontend_label'] = 'Nom attribut';
	$user_attributes_columns['attribute_input'] = 'Sortie attribut';
	register_column_headers('users_attributes',$user_attributes_columns);

	/*	Get the different _REQUEST values, to fill the field and to catch the form content that the user send	*/
	$fieldForAttributes = array();
	foreach ( array('attribute_frontend_label' => 'frontend_label', 'attribute_frontend_input' => 'frontend_input', 'id' => 'id') as $post_field => $var ) {
		$var = "attribute_$var";
		if ( ! isset($$var) )
		{
			$$var = isset($_POST[$post_field]) ? stripslashes($_POST[$post_field]) : '';
		}

		if(isset($attributeAction) && ($attributeAction != '') && ($attributeAction != 'add') && ($attributeAction != 'mod'))
		{
			if(($post_field == 'attribute_frontend_label') && (trim($attribute_frontend_label) == ''))
			{
				$eav_errors = new WP_Error();
				$eav_errors->add('mandatory_field',__('Le champs Label est obligatoire'));
			}

			$fieldForAttributes['attribute'][str_replace('attribute_','',$var)] = $$var;
		}
	}

	/*	Actions	*/
	switch ($attributeAction)
	{
		case 'add':
			$attributeAction = 'addattribute';
			break;
		case 'del':
			$fieldForAttributes['attribute']['id'] = $attributeId;

			unset($fieldForAttributes['attribute']['frontend_input']);
			$fieldForAttributes['assignation']['entity_type_id'] = 1;
			$fieldForAttributes['assignation']['attributeSetId'] = '1';
			$fieldForAttributes['assignation']['attributeGroupId'] = '1';
			$fieldForAttributes['attribute']['attribute_status'] = 'deleted';
			$deletion_result = $eav_attribute->updateAttribute($fieldForAttributes);
			if($deletion_result['result'] == 'error')
			{
				$eav_errors = new WP_Error();
				foreach($deletion_result['errors'] as $errorType => $errorMessage)
				{
					$eav_errors->add($errorType,__($errorMessage));
				}
			}
			else
			{
				$attributeAction = '';
				$deleteOK = true;
			}
			break;
		case 'addattribute':
			if(!isset($eav_errors) && is_array($fieldForAttributes))
			{
				$fieldForAttributes['attribute']['attribute_code'] = sanitize_title($fieldForAttributes['attribute']['frontend_label']);
				$fieldForAttributes['attribute']['backend_type'] = $attributeInputStorageConnection[$fieldForAttributes['attribute']['frontend_input']];
				$fieldForAttributes['attribute']['entity_type_id'] = 1;
				$fieldForAttributes['assignation']['entity_type_id'] = 1;
				$fieldForAttributes['assignation']['attributeSetId'] = '1';
				$fieldForAttributes['assignation']['attributeGroupId'] = '1';
				$creation_result = $eav_attribute->createAttribute($fieldForAttributes);

				if($creation_result['result'] == 'error')
				{
					$eav_errors = new WP_Error();
					foreach($creation_result['errors'] as $errorType => $errorMessage)
					{
						$eav_errors->add($errorType,__($errorMessage));
					}
				}
				else
				{
					$id = $creation_result['id'];
					$attributeAction = '';
					$createOK = true;
					if($fieldForAttributes['attribute']['frontend_input'] == 'select')
					{
						$attributeAction = 'addoption';
						$attributeId = $id;
					}
				}
			}
			break;
		case 'modattribute':
			if(!isset($eav_errors) && is_array($fieldForAttributes))
			{
				$disableInputTypeDropDown = true;

				unset($fieldForAttributes['attribute']['frontend_input']);
				$fieldForAttributes['assignation']['entity_type_id'] = 1;
				$fieldForAttributes['assignation']['attributeSetId'] = '1';
				$fieldForAttributes['assignation']['attributeGroupId'] = '1';
				$updateResult = $eav_attribute->updateAttribute($fieldForAttributes);
				if($updateResult['result'] == 'error')
				{
					$eav_errors = new WP_Error();
					foreach($updateResult['errors'] as $errorType => $errorMessage)
					{
						$eav_errors->add($errorType,__($errorMessage));
					}
				}
				else
				{
					$id = $updateResult['id'];
					$attributeAction = '';
					$updateOK = true;
					// $attributeAction = 'addoption';
					// $attributeId = $id;
				}
			}
			break;
		case 'mod':
			$query = 
				$wpdb->prepare(
				"SELECT frontend_label, frontend_input
				FROM " . TABLE_ATTRIBUTE . "
				WHERE attribute_id = %s", 
				$attributeId);
			$result = $wpdb->get_row($query);
			$attribute_frontend_label = $result->frontend_label;
			$attribute_frontend_input = $result->frontend_input;
			$attributeAction = 'modattribute';
			$disableInputTypeDropDown = true;
			$actionButtonLabel = __('Mettre &agrave; jour');
			break;
	}
?>
<script type="text/javascript" > var EVA_IMG_DIVERS_PLUGIN_URL = '<?php echo EVA_IMG_DIVERS_PLUGIN_URL; ?>'</script>
<form method="post" id="attributeManagementForm" name="attributeManagementForm" action="" >
	<div class="wrap">
		<input type="hidden" value="<?php echo $attributeAction; ?>" name="act" id="act" />
		<input type="hidden" value="<?php echo $attributeId; ?>" name="id" id="id" />
<?php
	switch ($attributeAction)
	{
		case 'mod':
		case 'modattribute':
		case 'add':
		case 'addattribute':
			require_once(EVA_MODULES_PLUGIN_DIR . 'eavManagement/attributes-manage.php');
			break;

		case 'addoption':
			echo '<script type="text/javascript" >$("#act").val("mod");$("#attributeManagementForm").submit();</script>';
			break;

		default:
			require_once(EVA_MODULES_PLUGIN_DIR . 'eavManagement/attributes-list.php');
			break;
	}
?>
	</div>
</form>