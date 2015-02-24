<?php
	class UploadFileXhr
	{
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
			$name = !empty($_GET['name'])?$_GET['name']:null;
			return $name;
		}
		function getSize(){
			return (int)$_SERVER['CONTENT_LENGTH'];
		}
	}

	class UploadFileForm
	{
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

	function handleUpload() {
		if ( current_user_can( 'upload_files' ) ) {
			$maxFileSize = 100 * 1024 * 1024;

			if (isset($_GET['qqfile'])){
				$original_file = $_GET['qqfile'];
				$file = new UploadFileXhr($original_file);
			}
			elseif (isset($_FILES['qqfile'])){
				$original_file = $_FILES['qqfile'];
				$file = new UploadFileForm();
			}
			else{
				return array(success=>false);
			}

			$size = $file->getSize();
			if ($size == 0){
				return array(success=>false, error=>"File is empty.");
			}
			if ($size > $maxFileSize){
				return array(success=>false, error=>"File is too large.");
			}

			$targetPath = !empty($_GET['folder'])?$_GET['folder']:null;
			if(!empty($targetPath)){
				$targetFile =  str_replace('//','/',$targetPath) . digirisk_tools::slugify($original_file);
				$numero = "";
				$extention = "";
				$nomFichier = "";
				$temps = explode('.', digirisk_tools::slugify($original_file));
				foreach($temps as $temp){
					$nomFichier = $nomFichier . $extention;
					$extention = $temp;
				}
				if(file_exists($targetFile)){
					$numero = 1;
					$nomFichierTest = $nomFichier . $numero . '.' . $extention;
					while(file_exists(str_replace('//','/',$targetPath) . $nomFichierTest)){
						$numero = $numero + 1;
						$nomFichierTest = $nomFichier . $numero . '.'  . $extention;
					}
					$targetFile = str_replace('//','/',$targetPath). $nomFichierTest;
				}

				// Uncomment the following line if you want to make the directory if it doesn't exist
				if(!file_exists(str_replace('//','/',$targetPath))){
					mkdir(str_replace('//','/',$targetPath), 0755, true);
					exec('chmod -R 755 ' . EVA_GENERATED_DOC_DIR);
				}
				$file->save($targetFile);
				$fichier = str_replace(str_replace('\\', '/', EVA_HOME_DIR), '', $targetFile);

				return array("success"=>true, "tableElement"=>(!empty($_GET['tableElement'])?$_GET['tableElement']:null), "idElement"=>(!empty($_GET['idElement'])?$_GET['idElement']:null), "fichier"=>$fichier);
			}
			else{
				return array("success"=>false, "error"=>"You are not allowed to upload file here");
			}
		}
		else {
			die();
		}
	}

?>