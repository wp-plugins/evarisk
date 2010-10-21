<?php
	require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php' );
	include_once(EVA_INC_PLUGIN_DIR . 'config/config.php' );

	include_once(EVA_LIB_PLUGIN_DIR . 'users/evaUserGroup.class.php' );
	$evaUserGroup = new evaUserGroup();

	include_once(EVA_LIB_PLUGIN_DIR . 'users/evaUser.class.php');
	$evaUser = new evaUser('user_profile');
	$listExistingUser = $evaUser->getUserList();

	include_once(EVA_LIB_PLUGIN_DIR . 'evaRole/evaRole.class.php' );
	$evaRole = new evaRole();

	$evaUserGroupAction = isset($_REQUEST['act']) ? eva_tools::IsValid_Variable($_REQUEST['act']) : '';
	$evaUserGroupId = isset($_REQUEST['id']) ? eva_tools::IsValid_Variable($_REQUEST['id']) : '';
	$groupUserList = isset($_REQUEST['groupUserList']) ? eva_tools::IsValid_Variable($_REQUEST['groupUserList']) : '';

	$createOK = $updateOK = $deleteOK = false;
	$actionButtonLabel = __('Ajouter un groupe');
	$managementTitle = __('Ajouter un groupe', 'evarisk');
	$userAlreadyAffected = array();

	/* Define the different column to output in grid	*/
	$evaUserGroups_columns = array();
	$evaUserGroups_columns['cb'] = '';//'<input type="checkbox" />';
	$evaUserGroups_columns['user_group_name'] = 'Nom du groupe';
	$evaUserGroups_columns['user_group_description'] = 'Description du groupe';
	$evaUserGroups_columns['user_group_content_number'] = 'Nombre d\'utilisateur';
	register_column_headers('evaUserGroup',$evaUserGroups_columns);

	$formFieldList = array('user_group_name' => 'user_group_name', 'user_group_description' => 'user_group_description', 'id' => 'id');

	/*	Get the different _REQUEST values, to fill the field and to catch the form content that the user send	*/
	$fieldForAttributes = array();
	foreach ($formFieldList as $post_field => $var ) {
		$var = "$var";
		if( !isset($$var) )
		{
			$$var = isset($_POST[$post_field]) ? stripslashes($_POST[$post_field]) : '';
		}

		if(isset($evaUserGroupAction) && ($evaUserGroupAction != '') && ($evaUserGroupAction != 'add') && ($evaUserGroupAction != 'mod'))
		{
			if(($post_field == 'user_group_name') && (trim($user_group_name) == ''))
			{
				$eav_errors = new WP_Error();
				$eav_errors->add('mandatory_field',__('Le champs Nom est obligatoire'));
			}

			if(isset($_POST['role']) && (is_array($_POST['role'])))
			{
				$fieldForAttributes['evaUserGroupRole']['groupRole'] = $_POST['role'];
			}
			else
			{
				$fieldForAttributes['evaUserGroupRole']['groupRole'] = '';
			}

			$fieldForAttributes['evaUserGroup'][$var] = $$var;
		}
	}

	/*	Actions	*/
	switch ($evaUserGroupAction)
	{
		case 'add':
			$evaUserGroupAction = 'addevaUserGroup';
			break;
		case 'del':
			$evaUserGroupAction = '';
			$fieldForAttributes['evaUserGroup']['user_group_id'] = $evaUserGroupId;
			$fieldForAttributes['evaUserGroup']['user_group_status'] = 'deleted';
			$deletion_result = $evaUserGroup->updateUsergroup($fieldForAttributes);
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
				$evaUserGroupAction = '';
				$deleteOK = true;
			}
			break;
		case 'addevaUserGroup':
			if(!isset($eav_errors) && is_array($fieldForAttributes))
			{
				$fieldForAttributes['evaUserGroup']['user_group_status'] = 'valid';
				$creation_result = $evaUserGroup->createUserGroup($fieldForAttributes);

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

					if( is_array($_POST['role']) )
					{
						foreach($_POST['role'] as $roleKey => $roleLabel)
						{
							$usersList = explode(',', $_POST['groupUserList']);
							foreach($usersList as $userKey => $userId)
							{
								if($userId != '')
								{
									$userGoodId = str_replace('user', '', $userId);
									$evaRole->manageRole($userGoodId, $roleLabel, 'add');
								}
							}
						}
					}

					$evaUserGroupAction = '';
					$createOK = true;
				}
			}
			break;
		case 'modevaUserGroup':
			$managementTitle = __('Modifier un groupe', 'evarisk');
			if(!isset($eav_errors) && is_array($fieldForAttributes))
			{
				$updateResult = $evaUserGroup->updateUsergroup($fieldForAttributes);
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

					$evaUserGroupAction = '';
					$updateOK = true;
				}
			}
			$actionButtonLabel = __('Mettre &agrave; jour');
			break;
		case 'mod':
			$managementTitle = __('Modifier un groupe', 'evarisk');
			$evaUserGroupInfos = $evaUserGroup->getUserGroup($evaUserGroupId);
			$evaUserGroupAction = 'modevaUserGroup';

			$user_group_name = $evaUserGroupInfos[0]->user_group_name;
			$user_group_description = $evaUserGroupInfos[0]->user_group_description;

			$groupUserList = $evaUserGroupInfos[0]->ELEMENT;
			$userAlreadyAffected = explode(',', $groupUserList);

			$actionButtonLabel = __('Mettre &agrave; jour', 'evarisk');
			break;
	}
?>
<form method="post" id="evaUserGroupManagementForm" name="evaUserGroupManagementForm" action="" >
	<div class="wrap">
		<input type="hidden" value="<?php echo $evaUserGroupAction; ?>" name="act" id="act" />
		<input type="hidden" value="<?php echo $evaUserGroupId; ?>" name="id" id="id" />
<?php
	switch ($evaUserGroupAction)
	{
		case 'mod':
		case 'modevaUserGroup':
		case 'add':
		case 'addevaUserGroup':
			include_once(EVA_MODULES_PLUGIN_DIR . 'groupesUtilisateur/groupesGestion.php');
			break;

		default:
			include_once(EVA_MODULES_PLUGIN_DIR . 'groupesUtilisateur/groupesListe.php');
			break;
	}
?>
	</div>
</form>