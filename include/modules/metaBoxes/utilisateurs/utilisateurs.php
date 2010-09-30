<?php
	//Postbox definition
	$postBoxTitle = __('Groupes d\'utilisateurs', 'evarisk');
	$postBoxId = 'postBoxIndividus';
	$postBoxCallbackFunction = 'getPeoplePostBoxBody';
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_UNITES_DE_TRAVAIL, 'rightSide', 'default');
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_GROUPEMENTS, 'rightSide', 'default');
	
	function getPeoplePostBoxBody($arguments)
	{
		require_once(EVA_LIB_PLUGIN_DIR . 'users/evaUserGroup.class.php');

		$boxGroupesUtilisateursEvaluation = evaUserGroup::boxGroupesUtilisateursEvaluation($arguments['tableElement'], $arguments['idElement']);

		echo 
			'<div id="message' . TABLE_LIAISON_USER_GROUPS . '" class="updated fade" style="cursor:pointer; display:none;"></div>
			<div id="listeGroupesUtilisateurs" >' . $boxGroupesUtilisateursEvaluation . '</div>';
	}
?>