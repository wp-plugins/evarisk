<?php

	//Postbox definition
	$postBoxId = 'postBoxUtilisateurs';
	$postBoxCallbackFunction = 'getUtilisateursPostBoxBody';
	switch($_POST['tableElement'])
	{
		case TABLE_TACHE:
			$postBoxTitle = __('Utilisateurs affect&eacute;s &agrave; la r&eacute;alisation de la t&acirc;che', 'evarisk');
			add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_TACHE, 'rightSide', 'default');
		break;
		case TABLE_ACTIVITE:
			$postBoxTitle = __('Utilisateurs affect&eacute;s &agrave; la r&eacute;alisation de l\'action', 'evarisk');
			add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_ACTIVITE, 'rightSide', 'default');
		break;
		case TABLE_GROUPEMENT:
			$postBoxTitle = __('Utilisateurs du groupement', 'evarisk');
			add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_GROUPEMENTS, 'rightSide', 'default');
			$postBoxId = 'postBoxUtilisateursEvaluated';
			$postBoxTitle = __('Utilisateurs participant &agrave; l\'&eacute;valuation', 'evarisk');
			add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_GROUPEMENTS, 'rightSide', 'default', array('tableElement' => TABLE_GROUPEMENT . '_evaluation'));
		break;
		case TABLE_UNITE_TRAVAIL:
			$postBoxTitle = __('Utilisateurs de l\'unit&eacute;', 'evarisk');
			add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_UNITES_DE_TRAVAIL, 'rightSide', 'default');
			$postBoxId = 'postBoxUtilisateursEvaluated';
			$postBoxTitle = __('Utilisateurs participant &agrave; l\'&eacute;valuation', 'evarisk');
			add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_UNITES_DE_TRAVAIL, 'rightSide', 'default', array('tableElement' => TABLE_UNITE_TRAVAIL . '_evaluation'));
		break;
		default:
			$postBoxTitle = __('Utilisateurs affect&eacute;s', 'evarisk');
		break;
	}

	require_once(EVA_LIB_PLUGIN_DIR . 'users/evaUserLinkElement.class.php');
	
	function getUtilisateursPostBoxBody($arguments, $moreArgs = '')
	{
		$idElement = $arguments['idElement'];
		$tableElement = $arguments['tableElement'];

		if(is_array($moreArgs) && isset($moreArgs['args']['tableElement']))
		{
			$tableElement = $moreArgs['args']['tableElement'];
		}

		echo 
'
<div style="display:none;" id="messageInfo_' . $tableElement . $idElement . '_affectUser" ></div>
<div id="userList' . $tableElement . '" >' . evaUserLinkElement::afficheListeUtilisateur($tableElement, $idElement) . '</div>';
	}

?>