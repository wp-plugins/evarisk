<?php
	//Postbox definition
	$postBoxTitle = __('Galerie Photos', 'evarisk');
	$postBoxId = 'postBoxGaleriePhotos';
	$postBoxCallbackFunction = 'getGaleriePhotosPostBoxBody';
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_UNITES_DE_TRAVAIL, 'rightSide', 'default');
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_GROUPEMENTS, 'rightSide', 'default');

	require_once(EVA_LIB_PLUGIN_DIR . 'photo/evaPhoto.class.php');
	
	function getGaleriePhotosPostBoxBody($arguments)
	{
		$idElement = $arguments['idElement'];
		$tableElement = $arguments['tableElement'];

		$output =  
			'<script type="text/javascript">
				function reloadcontainer()
				{
					$("#pictureGallery' . $tableElement . '_' . $idElement .'").html(\'<img src="' . EVA_IMG_DIVERS_PLUGIN_URL . 'loading.gif" alt="loading" />\');
					$("#pictureGallery' . $tableElement . '_' . $idElement . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
						"post": "true",  
						"table": "' . $tableElement . '",
						"idElement": "' . $idElement . '",
						"act": "reloadGallery"
					});
				}
				function showGallery()
				{
					$("#pictureGallery' . $tableElement . '_' . $idElement .'").html(\'<img src="' . EVA_IMG_DIVERS_PLUGIN_URL . 'loading.gif" alt="loading" />\');
					$("#pictureGallery' . $tableElement . '_' . $idElement . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
						"post": "true",  
						"table": "' . $tableElement . '",
						"idElement": "' . $idElement . '",
						"act": "showGallery"
					});
				}
			</script>
			<div id="message' . $tableElement . '_' . $idElement . '" ></div>
			<div id="pictureUploadForm' . $tableElement . '_' . $idElement . '" >' . evaPhoto::getUploadForm($tableElement, $idElement) . '</div>
			<div id="pictureGallery' . $tableElement . '_' . $idElement . '" >';

		/*	Output the gallery only if the are several picture to show OR that there is only one picture and that no default picture is selected	*/
		$output .= evaPhoto::outputGallery($tableElement, $idElement);
		
		$output .= '</div>';

		echo $output;
	}
