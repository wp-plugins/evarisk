<?php

	//Postbox definition
	$postBoxTitle = __('Pr&eacute;conisations', 'evarisk');
	$postBoxId = 'postBoxRecommandations';
	add_meta_box($postBoxId, $postBoxTitle, array('evaRecommandation', 'getRecommandationsPostBoxBody'), PAGE_HOOK_EVARISK_GROUPEMENTS, 'rightSide', 'default');
	add_meta_box($postBoxId, $postBoxTitle, array('evaRecommandation', 'getRecommandationsPostBoxBody'), PAGE_HOOK_EVARISK_UNITES_DE_TRAVAIL, 'rightSide', 'default');