<?php
	//Postbox definition
	$postBoxTitle = __('Accidents de travail', 'evarisk');
	$postBoxId = 'postBoxAccidents';
	$postBoxCallbackFunction = 'getAccidentsTravailPostBoxBody';
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_UNITES_DE_TRAVAIL, 'rightSide', 'default');
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_GROUPEMENTS, 'rightSide', 'default');
	
	function getAccidentsTravailPostBoxBody($arguments)
	{
		$idElement = $arguments['idElement'];
		$tableElement = $arguments['tableElement'];
		echo __('&Agrave; venir', 'evarisk');
	}
?>