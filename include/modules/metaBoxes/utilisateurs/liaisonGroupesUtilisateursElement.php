<?php

	//Postbox definition
	$postBoxId = 'postBoxGroupesUtilisateurs';
	$postBoxCallbackFunction = array('digirisk_groups', 'groupPostBox');
	switch($_POST['tableElement'])
	{
		case TABLE_GROUPEMENT:
			$postBoxTitle = __('Groupes d\'utilisateurs du groupement', 'evarisk');
			add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_GROUPEMENTS, 'rightSide', 'default', array('groupType' => 'employee'));
			$postBoxId = 'postBoxGroupesEvaluateurs';
			$postBoxTitle = __('Groupes d\'&eacute;valuateurs du groupement', 'evarisk');
			add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_GROUPEMENTS, 'rightSide', 'default', array('groupType' => 'evaluator'));
		break;
		case TABLE_UNITE_TRAVAIL:
			$postBoxTitle = __('Groupes d\'utilisateurs de l\'unit&eacute;', 'evarisk');
			add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_UNITES_DE_TRAVAIL, 'rightSide', 'default', array('groupType' => 'employee'));
			$postBoxId = 'postBoxGroupesEvaluateurs';
			$postBoxTitle = __('Groupes d\'&eacute;valuateurs de l\'unit&eacute;', 'evarisk');
			add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_UNITES_DE_TRAVAIL, 'rightSide', 'default', array('groupType' => 'evaluator'));
		break;
		default:
			$postBoxTitle = __('Groupes d\'utilisateurs affect&eacute;s', 'evarisk');
		break;
	}
