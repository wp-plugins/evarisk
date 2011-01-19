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
	require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/activite/evaActivity.class.php');

	$result = handleUpload();
	$tableElement = eva_tools::IsValid_Variable($result['tableElement']);
	$idElement = eva_tools::IsValid_Variable($result['idElement']);
	$fichier = eva_tools::IsValid_Variable($result['fichier']);
	
	$uploadStatus = evaPhoto::saveNewPicture($tableElement, $idElement, $fichier);
	if($uploadStatus == 'error')
	{
		$result[success] = false;
	}
	else
	{
		$action = new EvaActivity($idElement);
		$action->load();
		$action->setidPhotoAvant($uploadStatus);
		$action->save();
	}
	
	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);