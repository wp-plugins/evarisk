<?php

	//Postbox definition
	$postBoxTitle = __('Accidents de travail', 'evarisk');
	$postBoxId = 'postBoxAccidents';
	$postBoxCallbackFunction = array('digirisk_accident', 'get_post_box');

	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_GROUPEMENTS, 'rightSide', 'default');
