<?php

	//Postbox definition
	$postBoxTitle = __('Suivi des modifications', 'evarisk');
	$postBoxId = 'postBoxModificationFollowUp';
	$postBoxCallbackFunction = 'getActivityModificationFollowUpPostBoxBody';
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_TACHE, 'rightSide', 'default');
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_ACTIVITE, 'rightSide', 'default');
	require_once( EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/suivi_activite.class.php');

	function getActivityModificationFollowUpPostBoxBody($arguments)
	{
		$idElement = $arguments['idElement'];
		$tableElement = $arguments['tableElement'];

		echo '<div id="messageInfo' . $tableElement . $idElement . '" style="display:none;" ></div>
			<div id="load' . $tableElement . $idElement . '" >' . suivi_activite::formulaireAjoutSuivi($tableElement, $idElement) . '</div>';
	}

?>