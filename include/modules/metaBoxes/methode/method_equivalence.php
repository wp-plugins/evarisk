<?php

	//Postbox definition
	$postBoxTitle = __('&Eacute;quivalence des variables de la m&eacute;thode avec l\'&eacute;talon', 'evarisk');
	$postBoxId = 'postBoxMethodeEvaluationEquivalenceVariable';
	add_meta_box($postBoxId, $postBoxTitle, array('MethodeEvaluation', 'evaluation_method_variable_equivalence'), PAGE_HOOK_EVARISK_EVALUATION_METHODE, 'rightSide', 'default');