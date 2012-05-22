<?php

	//Postbox definition
	$postBoxTitle = __('Informations g&eacute;n&eacute;rales', 'evarisk');
	$postBoxId = 'postBoxRecommandationCategoryEdition';
	add_meta_box($postBoxId, $postBoxTitle, array('evaRecommandationCategory', 'recommandation_category_form'), PAGE_HOOK_EVARISK_CATEGORIE_PRECONISATION, 'rightSide', 'default');