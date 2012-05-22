<?php

	//Postbox definition
	$postBoxTitle = __('Informations g&eacute;n&eacute;rales', 'evarisk');
	$postBoxId = 'postBoxMethodeEvaluationEdition';
	add_meta_box($postBoxId, $postBoxTitle, array('MethodeEvaluation', 'evaluation_method_form'), PAGE_HOOK_EVARISK_EVALUATION_METHODE, 'rightSide', 'default');