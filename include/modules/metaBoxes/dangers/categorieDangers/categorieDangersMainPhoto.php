<?php
/*
 * @version v5.0
 */
 	
//Postbox definition
$postBoxTitle = __('Photos', 'evarisk');
$postBoxId = 'categorieDangersMainPhotoPostBoxBody';
$postBoxCallbackFunction = 'getCategorieDangersMainPhotoPostBoxBody';
add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_CATEGORIES_DANGERS, 'rightSide', 'default');
require_once(EVA_LIB_PLUGIN_DIR . 'danger/categorieDangers/categorieDangers.class.php' ); 
require_once(EVA_LIB_PLUGIN_DIR . 'photo/evaPhoto.class.php' );

	function getCategorieDangersMainPhotoPostBoxBody($arguments)
	{
		$idElement = $arguments['idElement'];
		$tableElement = $arguments['tableElement'];

		$output =  
			'<script type="text/javascript">
				function reloadcontainer()
				{
					evarisk("#pictureGallery' . $tableElement . '_' . $idElement .'").html(\'<img src="' . PICTO_LOADING_ROUND . '" alt="loading" />\');
					evarisk("#pictureGallery' . $tableElement . '_' . $idElement . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
						"post": "true",  
						"table": "' . $tableElement . '",
						"idElement": "' . $idElement . '",
						"act": "reloadGallery"
					});
				}
				function showGallery()
				{
					evarisk("#pictureGallery' . $tableElement . '_' . $idElement .'").html(\'<img src="' . PICTO_LOADING_ROUND . '" alt="loading" />\');
					evarisk("#pictureGallery' . $tableElement . '_' . $idElement . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
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
