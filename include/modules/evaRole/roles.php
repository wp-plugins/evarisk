<?php
	require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php' );
	include_once(EVA_INC_PLUGIN_DIR . 'config/config.php' );

	include_once(EVA_LIB_PLUGIN_DIR . 'evaRole/evaRole.class.php' );
	$evaRole = new evaRole();

	$evaRoleAction = isset($_REQUEST['act']) ? eva_tools::IsValid_Variable($_REQUEST['act']) : '';
	$evaRoleId = isset($_REQUEST['id']) ? eva_tools::IsValid_Variable($_REQUEST['id']) : '';

	$createOK = $updateOK = $deleteOK = false;
	$actionButtonLabel = __('Ajouter un r&ocirc;le', 'evarisk');
	$managementTitle = __('Ajouter un r&ocirc;le', 'evarisk');
	$roleCapabilities = array();

	$evariskCapability = getDroitEvarisk();

	/* Define the different column to output in grid	*/
	$evaRole_columns = array();
	$evaRole_columns['cb'] = '';//'<input type="checkbox" />';
	$evaRole_columns['eva_role_name'] = 'Nom du groupe';
	$evaRole_columns['eva_role_description'] = 'Description du groupe';
	$evaRole_columns['eva_role_capabilities'] = 'Droits des utilisateurs';
	register_column_headers('evaRole',$evaRole_columns);

	$formFieldList = array('eva_role_name' => 'eva_role_name', 'eva_role_description' => 'eva_role_description', 'id' => 'id');

	/*	Get the different _REQUEST values, to fill the field and to catch the form content that the user send	*/
	$fieldForAttributes = array();
	foreach ($formFieldList as $post_field => $var ) {
		$var = "$var";
		if( !isset($$var) )
		{
			$$var = isset($_POST[$post_field]) ? stripslashes($_POST[$post_field]) : '';
		}

		if(isset($evaRoleAction) && ($evaRoleAction != '') && ($evaRoleAction != 'add') && ($evaRoleAction != 'mod'))
		{
			if(($post_field == 'eva_role_name') && (trim($eva_role_name) == ''))
			{
				$eav_errors = new WP_Error();
				$eav_errors->add('mandatory_field',__('Le champs Nom est obligatoire'));
			}

			if(isset($_REQUEST['eva_role_capabilities']))
			{
				$fieldForAttributes['evaRole']['eva_role_capabilities'] = implode(',', $_REQUEST['eva_role_capabilities']);
			}
			else
			{
				$fieldForAttributes['evaRole']['eva_role_capabilities'] = '';
			}

			$fieldForAttributes['evaRole'][$var] = $$var;
		}
	}

	/*	Actions	*/
	switch ($evaRoleAction)
	{
		case 'add':
			$evaRoleAction = 'addevaRole';
			break;
		case 'del':
			$evaRoleAction = '';
			$fieldForAttributes['evaRole']['eva_role_id'] = $evaRoleId;
			$fieldForAttributes['evaRole']['eva_role_status'] = 'deleted';
			$deletion_result = $evaRole->update($fieldForAttributes);
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
				$evaRole->removeRole($evaRole->getRoleName($evaRoleId));
				$evaRoleAction = '';
				$deleteOK = true;
			}
			break;
		case 'addevaRole':
			if(!isset($eav_errors) && is_array($fieldForAttributes))
			{
				$fieldForAttributes['evaRole']['eva_role_status'] = 'valid';
				$fieldForAttributes['evaRole']['eva_role_label'] = sanitize_title($fieldForAttributes['evaRole']['eva_role_name']);
				$creation_result = $evaRole->create($fieldForAttributes);

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

					$evaRoleAction = '';
					$createOK = true;
				}
			}
			break;
		case 'modevaRole':
			$managementTitle = __('Modifier un r&ocirc;le', 'evarisk');
			if(!isset($eav_errors) && is_array($fieldForAttributes))
			{
				$updateResult = $evaRole->update($fieldForAttributes);
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

					$evaRoleAction = '';
					$updateOK = true;
				}
			}
			$actionButtonLabel = __('Mettre &agrave; jour');
			break;
		case 'mod':
			$managementTitle = __('Modifier un r&ocirc;le', 'evarisk');
			$evaRoleInfos = $evaRole->getList($evaRoleId);
			$evaRoleAction = 'modevaRole';

			$eva_role_name = $evaRoleInfos[0]->eva_role_name;
			$eva_role_description = $evaRoleInfos[0]->eva_role_description;

			$eva_role_capabilities = $evaRoleInfos[0]->eva_role_capabilities;
			$roleCapabilities = explode(',', $eva_role_capabilities);

			$actionButtonLabel = __('Mettre &agrave; jour', 'evarisk');
			break;
	}
?>
<form method="post" id="evaRoleForm" name="evaRoleForm" action="" >
	<div class="wrap">
		<input type="hidden" value="<?php echo $evaRoleAction; ?>" name="act" id="act" />
		<input type="hidden" value="<?php echo $evaRoleId; ?>" name="id" id="id" />
<?php
	switch ($evaRoleAction)
	{
		case 'mod':
		case 'modevaRole':
		case 'add':
		case 'addevaRole':
			include_once(EVA_MODULES_PLUGIN_DIR . 'evaRole/rolesGestion.php');
			break;

		default:
			include_once(EVA_MODULES_PLUGIN_DIR . 'evaRole/rolesListe.php');
			break;
	}
?>
	</div>
</form>