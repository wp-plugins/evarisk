<?php
	//Postbox definition
	$postBoxTitle = __('Groupes d\'&eacute;valuateur', 'evarisk');
	$postBoxId = 'postBoxEvaluateurs';
	$postBoxCallbackFunction = 'getEvaluateursPostBoxBody';
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_UNITES_DE_TRAVAIL, 'rightSide', 'default');
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_GROUPEMENTS, 'rightSide', 'default');
	
	function getEvaluateursPostBoxBody($arguments)
	{
		require_once(EVA_LIB_PLUGIN_DIR . 'users/evaUserEvaluatorGroup.class.php');

		$boxGroupesUtilisateursEvaluation = evaUserEvaluatorGroup::boxGroupesUtilisateursEvaluation($arguments['tableElement'], $arguments['idElement']);

		echo 
			'<div id="message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '" class="updated fade" style="cursor:pointer; display:none;"></div>
			<div id="listeGroupesEvaluateurs" >' . $boxGroupesUtilisateursEvaluation . '</div>';
	}


?>