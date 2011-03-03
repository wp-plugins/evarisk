<?php
/*
 * @version v5.0
 */
 
	require_once($_GET['abspath'] . 'wp-load.php');
	require_once($_GET['abspath'] . 'wp-admin/includes/admin.php');
	require_once($_GET['evarisk']);
	require_once(EVA_CONFIG);
	require_once(EVA_LIB_PLUGIN_DIR . 'upload.php');
	require_once(EVA_LIB_PLUGIN_DIR . 'gestionDocumentaire/gestionDoc.class.php');

	$result = handleUpload();
	$table = eva_tools::IsValid_Variable($_GET['table']);
	$tableElement = eva_tools::IsValid_Variable($result['tableElement']);
	$idElement = eva_tools::IsValid_Variable($result['idElement']);
	$fichier = eva_tools::IsValid_Variable($result['fichier']);
	
	$uploadStatus = eva_gestionDoc::saveNewDoc($table, $tableElement, $idElement, $fichier);
	if($uploadStatus == 'error')
	{
		$result[success] = false;
	}

	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
