<?php
	//Postbox definition
	$postBoxTitle = __('Galerie Photos', 'evarisk');
	$postBoxId = 'postBoxGaleriePhotos';
	$postBoxCallbackFunction = 'getGaleriePhotosPostBoxBody';
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_UNITES_DE_TRAVAIL, 'rightSide', 'default');
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_GROUPEMENTS_GESTION, 'rightSide', 'default');
  add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_TACHE, 'rightSide', 'default');
  add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_ACTIVITE, 'rightSide', 'default');
  add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_CATEGORIES_DANGERS, 'rightSide', 'default');

	require_once(EVA_LIB_PLUGIN_DIR . 'photo/evaPhoto.class.php');
	
	function getGaleriePhotosPostBoxBody($arguments)
	{
		$idElement = $arguments['idElement'];
		$tableElement = $arguments['tableElement'];

		switch($tableElement)
		{
			case TABLE_CATEGORIE_DANGER:
				$userCanUploadPicture = current_user_can('digi_edit_danger_category');
			break;
			case TABLE_GROUPEMENT:
				$userCanUploadPicture = current_user_can('digi_edit_groupement');
				if(!$userCanUploadPicture)
				{
					$userCanUploadPicture = current_user_can('digi_edit_groupement_' . $idElement);
				}
			break;
			case TABLE_UNITE_TRAVAIL:
				$userCanUploadPicture = current_user_can('digi_edit_unite');
				if(!$userCanUploadPicture)
				{
					$userCanUploadPicture = current_user_can('digi_edit_unite_' . $idElement);
				}
			break;
			case TABLE_TACHE:
				$userCanUploadPicture = current_user_can('digi_edit_task');
			break;
			case TABLE_ACTIVITE:
				$userCanUploadPicture = current_user_can('digi_edit_action');
			break;
			default:
				$userCanUploadPicture = true;
			break;
		}

		$output = evaPhoto::galleryContent($tableElement, $idElement, $userCanUploadPicture);

		echo $output;
	}
