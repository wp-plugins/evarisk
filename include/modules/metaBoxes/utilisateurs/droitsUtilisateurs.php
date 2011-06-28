<?php

	//Postbox definition
	$postBoxId = 'postBoxDroitssUtilisateurs';
	$postBoxCallbackFunction = array('digirisk_permission', 'userRightPostBox');
	switch($_POST['tableElement'])
	{
		case TABLE_GROUPEMENT:
			$postBoxTitle = __('Droits des utilisateurs du groupement', 'evarisk');
			add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_GROUPEMENTS, 'rightSide', 'default');
		break;
		case TABLE_UNITE_TRAVAIL:
			$postBoxTitle = __('Droits des utilisateurs de l\'unit&eacute;', 'evarisk');
			add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_UNITES_DE_TRAVAIL, 'rightSide', 'default');
		break;
		default:
			$postBoxTitle = __('Droits des utilisateurs', 'evarisk');
		break;
	}
