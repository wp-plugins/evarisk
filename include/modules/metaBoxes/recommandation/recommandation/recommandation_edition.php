<?php

	//Postbox definition
	$postBoxTitle = __('Informations g&eacute;n&eacute;rales', 'evarisk');
	$postBoxId = 'postBoxRecommandationEdition';
	add_meta_box($postBoxId, $postBoxTitle, array('evaRecommandation', 'recommandation_form'), PAGE_HOOK_EVARISK_PRECONISATION, 'rightSide', 'default');