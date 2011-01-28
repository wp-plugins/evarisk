<?php
	//Postbox definition
	$postBoxTitle = __('Utilisateurs participant &agrave; l\'&eacute;valuation', 'evarisk');
	$postBoxId = 'postBoxIndividusEvalues';
	$postBoxCallbackFunction = 'getEvaluatedPeoplePostBoxBody';
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_UNITES_DE_TRAVAIL, 'rightSide', 'default');
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_GROUPEMENTS, 'rightSide', 'default');
	
	function getEvaluatedPeoplePostBoxBody($arguments)
	{
		require_once(EVA_LIB_PLUGIN_DIR . 'users/evaUser.class.php');

		$afficheListeUtilisateurs = evaUser::boxUtilisateursEvalues($arguments);

		echo 
			'<div id="message' . TABLE_LIAISON_USER_EVALUATION . '" class="updated fade" style="cursor:pointer; display:none;"></div>
			<div class="margin16" >test</div>
			<div id="listeEmployesEvalues" >' . $afficheListeUtilisateurs . '</div>';
	}

?>