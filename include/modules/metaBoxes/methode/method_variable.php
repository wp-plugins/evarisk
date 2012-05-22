<?php

	//Postbox definition
	$postBoxTitle = __('Variables de la m&eacute;thode', 'evarisk');
	$postBoxId = 'postBoxMethodeEvaluationVariable';
	add_meta_box($postBoxId, $postBoxTitle, array('MethodeEvaluation', 'evaluation_method_variable_manager'), PAGE_HOOK_EVARISK_EVALUATION_METHODE, 'rightSide', 'default');