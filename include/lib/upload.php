<?php
	/*
	 * Classes et fonctions pour la sauvegarde
	 */
	class UploadFileXhr {
		function save($path){
			$input = fopen("php://input", "r");
			$fp = fopen($path, "w");
			while ($data = fread($input, 1024)){
				fwrite($fp,$data);
			}
			fclose($fp);
			fclose($input);			
		}
		function getName(){
			return $_GET['name'];
		}
		function getSize(){
			$headers = apache_request_headers();
			return (int)$headers['Content-Length'];
		}
	}

	class UploadFileForm {	
	  function save($path){
			move_uploaded_file($_FILES['qqfile']['tmp_name'], $path);
		}
		function getName(){
			return $_FILES['qqfile']['name'];
		}
		function getSize(){
			return $_FILES['qqfile']['size'];
		}
	}

	function handleUpload(){
		$maxFileSize = 100 * 1024 * 1024;
			
		if (isset($_GET['qqfile'])){
			$file = new UploadFileXhr();
		} elseif (isset($_FILES['qqfile'])){
			$file = new UploadFileForm();
		} else {
			return array(success=>false);
		}	

		$size = $file->getSize();
		if ($size == 0){
			return array(success=>false, error=>"File is empty.");
		}				
		if ($size > $maxFileSize){
			return array(success=>false, error=>"File is too large.");
		}
			
		$pathinfo = pathinfo($file->getName());		
		$ext = $pathinfo['extension'];
		
		$tempFile = $_GET['qqfile'];
		$targetPath = $_GET['folder'];
		$targetFile =  str_replace('//','/',$targetPath) . eva_tools::slugify($_GET['qqfile']);
		$numero = "";
		$extention = "";
		$nomFichier = "";
		$temps = explode('.', eva_tools::slugify($_GET['qqfile']));
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
		if(!file_exists(str_replace('//','/',$targetPath)))
			mkdir(str_replace('//','/',$targetPath), 0755, true);
		$file->save($targetFile);
		$fichier = str_replace(str_replace('\\', '/', EVA_HOME_DIR), '', $targetFile);
		
		return array(success=>true, "tableElement"=>$_GET['tableElement'], "idElement"=>$_GET['idElement'], "fichier"=>$fichier);
	}
?>