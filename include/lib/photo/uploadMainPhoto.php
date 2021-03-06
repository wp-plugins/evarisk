<?php
/*
* @version v5.0
*/

	define('DOING_AJAX', true);
	define('WP_ADMIN', true);
	require_once('../../../../../../wp-load.php');
	require_once(ABSPATH . 'wp-admin/includes/admin.php');
	require_once('../../../evarisk.php');
	require_once(EVA_INC_PLUGIN_DIR . 'includes.php' );

	require_once(EVA_LIB_PLUGIN_DIR . 'upload.php');
	require_once(EVA_LIB_PLUGIN_DIR . 'photo/evaPhoto.class.php');
	
	$result = handleUpload();
	$tableElement = !empty($result['tableElement'])?digirisk_tools::IsValid_Variable($result['tableElement']):null;
	$idElement = !empty($result['idElement'])?digirisk_tools::IsValid_Variable($result['idElement']):null;
	$fichier = !empty($result['fichier'])?digirisk_tools::IsValid_Variable($result['fichier']):null;

	if(!empty($tableElement) && !empty($idElement) && !empty($fichier)){
		/*	Ajoute la photo � la table de toutes les photos	(GED Photo) */
		$uploadStatus = evaPhoto::saveNewPicture($tableElement, $idElement, $fichier);
		if($uploadStatus != 'error')
		{
			/*	D�fini la photo comme �tant par d�fault	*/
			$uploadStatusMainPhoto = evaPhoto::setMainPhoto($tableElement, $idElement, $uploadStatus);
			if($uploadStatusMainPhoto == 'error')
			{
				$result[success] = $uploadStatusMainPhoto;
			}
		}
	}
	else{
		$result["success"] = false;
	}

	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
