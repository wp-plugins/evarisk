<?php
/*
 * @version v5.0
 */
 
	require_once($_GET['abspath'] . 'wp-load.php');
	require_once($_GET['abspath'] . 'wp-admin/includes/admin.php');
	require_once($_GET['evarisk']);
	require_once(EVA_CONFIG);
	require_once(EVA_LIB_PLUGIN_DIR . 'upload.php');
	require_once(EVA_LIB_PLUGIN_DIR . 'photo/evaPhoto.class.php');
	
	$result = handleUpload();
	$tableElement = eva_tools::IsValid_Variable($result['tableElement']);
	$idElement = eva_tools::IsValid_Variable($result['idElement']);
	$fichier = eva_tools::IsValid_Variable($result['fichier']);

	/*	Ajoute la photo à la table de toutes les photos	(GED Photo) */
	$uploadStatus = evaPhoto::saveNewPicture($tableElement, $idElement, $fichier);
	if($uploadStatus != 'error')
	{
		/*	Défini la photo comme étant par défault	*/
		$uploadStatusMainPhoto = evaPhoto::setMainPhoto($tableElement, $idElement, $uploadStatus);
		if($uploadStatusMainPhoto == 'error')
		{
			$result[success] = $uploadStatusMainPhoto;
		}
	}

	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
