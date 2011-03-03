<?php
	//Postbox definition
	$postBoxTitle = __('Galerie Photos', 'evarisk');
	$postBoxId = 'postBoxGaleriePhotos';
	$postBoxCallbackFunction = 'getGaleriePhotosPostBoxBody';
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_UNITES_DE_TRAVAIL, 'rightSide', 'default');
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_GROUPEMENTS_GESTION, 'rightSide', 'default');
  add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_ACTIVITE, 'rightSide', 'default');
  add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_CATEGORIES_DANGERS, 'rightSide', 'default');

	require_once(EVA_LIB_PLUGIN_DIR . 'photo/evaPhoto.class.php');
	
	function getGaleriePhotosPostBoxBody($arguments)
	{
		$idElement = $arguments['idElement'];
		$tableElement = $arguments['tableElement'];

		$output = evaPhoto::galleryContent($tableElement, $idElement);

		/*	Output the gallery only if the are several picture to show OR that there is only one picture and that no default picture is selected	*/
		// $output .= evaPhoto::outputGallery($tableElement, $idElement);

		echo $output;
	}
