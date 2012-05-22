<?php
// Uploadify v1.6.2
// Copyright (C) 2009 by Ronnie Garcia
// Co-developed by Travis Nickels

require_once(ABSPATH . 'wp-load.php');
require_once(ABSPATH . 'wp-admin/includes/admin.php');
require_once('../../../evarisk.php');
require_once(EVA_CONFIG);
require_once(EVA_LIB_PLUGIN_DIR . 'photo/evaPhoto.class.php');

if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_GET['folder'] . '/';
	$targetFile =  str_replace('//','/',$targetPath) . digirisk_tools::slugify($_FILES['Filedata']['name']);
	$numero = "";
	$extention = "";
	$nomFichier = "";
	$temps = explode('.', digirisk_tools::slugify($_FILES['Filedata']['name']));
	foreach($temps as $temp)
	{
		$nomFichier = $nomFichier . $extention;
		$extention = $temp;
	}
	if(file_exists($targetFile))
	{
		$numero = 1;
		$nomFichierTest = $nomFichier . $numero . '.' . $extention;
		while(file_exists(str_replace('//','/',$targetPath) . $nomFichierTest))
		{
			$numero = $numero + 1;
			$nomFichierTest = $nomFichier . $numero . '.'  . $extention;
		}
		$targetFile = str_replace('//','/',$targetPath). $nomFichierTest;
	}
	
	// Uncomment the following line if you want to make the directory if it doesn't exist
	// mkdir(str_replace('//','/',$targetPath), 0755, true);
	
	if(move_uploaded_file($tempFile,$targetFile)){
		EvaPhoto::saveNewPhoto("toBeUpdated", 0, $nomFichier . $numero . '.' . $extention, $targetFile);
	}
}
