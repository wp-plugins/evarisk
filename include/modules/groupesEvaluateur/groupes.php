<?php
	require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php' );
	include_once(EVA_INC_PLUGIN_DIR . 'config/config.php' );

	include_once(EVA_LIB_PLUGIN_DIR . 'users/evaUserEvaluatorGroup.class.php' );
	$evaUserEvaluatorGroup = new evaUserEvaluatorGroup();

	include_once(EVA_LIB_PLUGIN_DIR . 'users/evaUser.class.php');
	$evaUser = new evaUser('user_profile');
	$listExistingUser = $evaUser->getUserList();

	include_once(EVA_LIB_PLUGIN_DIR . 'evaRole/evaRole.class.php' );
	$evaRole = new evaRole();

	$evaUserEvaluatorGroupAction = isset($_REQUEST['act']) ? eva_tools::IsValid_Variable($_REQUEST['act']) : '';
	$evaUserEvaluatorGroupId = isset($_REQUEST['id']) ? eva_tools::IsValid_Variable($_REQUEST['id']) : '';
	$groupUserList = isset($_REQUEST['groupUserList']) ? eva_tools::IsValid_Variable($_REQUEST['groupUserList']) : '';

	$createOK = $updateOK = $deleteOK = false;
	$actionButtonLabel = __('Ajouter un groupe');
	$managementTitle = __('Ajouter un groupe', 'evarisk');
	$userAlreadyAffected = array();

	/* Define the different column to output in grid	*/
	$evaUserEvaluatorGroups_columns = array();
	$evaUserEvaluatorGroups_columns['cb'] = '';//'<input type="checkbox" />';
	$evaUserEvaluatorGroups_columns['evaluator_group_name'] = 'Nom du groupe';
	$evaUserEvaluatorGroups_columns['evaluator_group_description'] = 'Description du groupe';
	$evaUserEvaluatorGroups_columns['user_group_content_number'] = 'Nombre d\'utilisateur';
	register_column_headers('evaUserEvaluatorGroup',$evaUserEvaluatorGroups_columns);

	$formFieldList = array('evaluator_group_name' => 'evaluator_group_name', 'evaluator_group_description' => 'evaluator_group_description', 'id' => 'id');

	/*	Get the different _REQUEST values, to fill the field and to catch the form content that the user send	*/
	$fieldForAttributes = array();
	foreach ($formFieldList as $post_field => $var ) {
		$var = "$var";
		if( !isset($$var) )
		{
			$$var = isset($_POST[$post_field]) ? stripslashes($_POST[$post_field]) : '';
		}

		if(isset($evaUserEvaluatorGroupAction) && ($evaUserEvaluatorGroupAction != '') && ($evaUserEvaluatorGroupAction != 'add') && ($evaUserEvaluatorGroupAction != 'mod'))
		{
			if(($post_field == 'evaluator_group_name') && (trim($evaluator_group_name) == ''))
			{
				$eav_errors = new WP_Error();
				$eav_errors->add('mandatory_field',__('Le champs Nom est obligatoire'));
			}

			if(isset($_POST['role']) && (is_array($_POST['role'])))
			{
				$fieldForAttributes['evaUserEvaluatorGroupRole']['groupRole'] = $_POST['role'];
			}
			else
			{
				$fieldForAttributes['evaUserEvaluatorGroupRole']['groupRole'] = '';
			}

			$fieldForAttributes['evaUserEvaluatorGroup'][$var] = $$var;
		}
	}

	/*	Actions	*/
	switch ($evaUserEvaluatorGroupAction)
	{
		case 'add':
			$evaUserEvaluatorGroupAction = 'addevaUserEvaluatorGroup';
			break;
		case 'del':
			$evaUserEvaluatorGroupAction = '';
			$fieldForAttributes['evaUserEvaluatorGroup']['evaluator_group_id'] = $evaUserEvaluatorGroupId;
			$fieldForAttributes['evaUserEvaluatorGroup']['evaluator_group_status'] = 'deleted';
			$deletion_result = $evaUserEvaluatorGroup->updateUsergroup($fieldForAttributes);
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
				$evaUserEvaluatorGroupAction = '';
				$deleteOK = true;
			}
			break;
		case 'addevaUserEvaluatorGroup':
			if(!isset($eav_errors) && is_array($fieldForAttributes))
			{
				$fieldForAttributes['evaUserEvaluatorGroup']['evaluator_group_status'] = 'valid';
				$creation_result = $evaUserEvaluatorGroup->createUserEvaluatorGroup($fieldForAttributes);

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

					$evaUserEvaluatorGroupAction = '';
					$createOK = true;
				}
			}
			break;
		case 'modevaUserEvaluatorGroup':
			$managementTitle = __('Modifier un groupe', 'evarisk');
			if(!isset($eav_errors) && is_array($fieldForAttributes))
			{
				$updateResult = $evaUserEvaluatorGroup->updateUsergroup($fieldForAttributes);
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

					$evaUserEvaluatorGroupAction = '';
					$updateOK = true;
				}
			}
			$actionButtonLabel = __('Mettre &agrave; jour');
			break;
		case 'mod':
			$managementTitle = __('Modifier un groupe', 'evarisk');
			$evaUserEvaluatorGroupInfos = $evaUserEvaluatorGroup->getUserEvaluatorGroup($evaUserEvaluatorGroupId);
			$evaUserEvaluatorGroupAction = 'modevaUserEvaluatorGroup';

			$evaluator_group_name = $evaUserEvaluatorGroupInfos[0]->evaluator_group_name;
			$evaluator_group_description = $evaUserEvaluatorGroupInfos[0]->evaluator_group_description;

			$groupUserList = $evaUserEvaluatorGroupInfos[0]->ELEMENT;
			$userAlreadyAffected = explode(',', $groupUserList);

			$actionButtonLabel = __('Mettre &agrave; jour', 'evarisk');
			break;
	}
?>
<form method="post" id="evaUserEvaluatorGroupManagementForm" name="evaUserEvaluatorGroupManagementForm" action="" >
	<div class="wrap">
		<input type="hidden" value="<?php echo $evaUserEvaluatorGroupAction; ?>" name="act" id="act" />
		<input type="hidden" value="<?php echo $evaUserEvaluatorGroupId; ?>" name="id" id="id" />
<?php
	switch ($evaUserEvaluatorGroupAction)
	{
		case 'mod':
		case 'modevaUserEvaluatorGroup':
		case 'add':
		case 'addevaUserEvaluatorGroup':
			include_once(EVA_MODULES_PLUGIN_DIR . 'groupesEvaluateur/groupesGestion.php');
			break;

		default:
			include_once(EVA_MODULES_PLUGIN_DIR . 'groupesEvaluateur/groupesListe.php');
			break;
	}
?>
	</div>
</form>