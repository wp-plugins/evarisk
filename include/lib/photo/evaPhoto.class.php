<?php
/**
 * 
 * 
 * @author Evarisk
 * @version v5.0
 */
include_once(EVA_CONFIG );
require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php');

class EvaPhoto {
	
	/**
	 * Returns all photos of the element
	 * @param string $tableElement Table name of the element
	 * @param int $idElement Id of the element in the table
	 * @param string $where SQL where condition
	 * @param string $order SQL order condition
	 * @return The photos of the element maching with the where condition and order by the order condition
	 */
	function getPhotos($tableElement, $idElement, $where = "1", $order = "PICTURE.id ASC")
	{
		global $wpdb;
		
		$tableElement = eva_tools::IsValid_Variable($tableElement);
		$idElement = eva_tools::IsValid_Variable($idElement);
		$where = eva_tools::IsValid_Variable($where);
		$order = eva_tools::IsValid_Variable($order);

		$query = $wpdb->prepare(
			"SELECT PICTURE.*, PICTURE_LINK.isMainPicture
			FROM " . TABLE_PHOTO . " AS PICTURE
				INNER JOIN " . TABLE_PHOTO_LIAISON . " AS PICTURE_LINK ON (PICTURE_LINK.idPhoto = PICTURE.id)
			WHERE PICTURE_LINK.tableElement='" . mysql_real_escape_string($tableElement) . "' 
				AND PICTURE_LINK.idElement='" . mysql_real_escape_string($idElement) . "'
				AND PICTURE_LINK.status = 'valid' 
				AND " . $where . "
			ORDER BY " . $order);
		$photos = $wpdb->get_results($query);

		return $photos;
	}

/*
  * Persistance
  */
	
	/**
	 * Save a new picture.
	 * @param string $tableElement Table name of the element
	 * @param int $idElement Id of the element in the table
	 * @param mixed $photo The path to the picture
	 * @return mixed $status The picture identifier if the photo is well insert and "error" else. 
	 */
	function saveNewPicture($tableElement, $idElement, $photo)
	{
		global $wpdb;
		$status = 'error';
		
		$tableElement = eva_tools::IsValid_Variable($tableElement);
		$idElement = eva_tools::IsValid_Variable($idElement);
		$photo = eva_tools::IsValid_Variable(eva_tools::slugify($photo));

		$query = 
			$wpdb->prepare(
				"INSERT INTO " . TABLE_PHOTO . " 
					(id, photo) 
				VALUES 
					('', '%s')"
				, $photo);
		if($wpdb->query($query))
		{
			$status = evaPhoto::associatePicture($tableElement, $idElement, $wpdb->insert_id);
		}
		return $status;
	}

	/**
	 * Associate a picture to an element.
	 * @param string $tableElement Table name of the element
	 * @param int $idElement Id of the element in the table
	 * @param int $pictureId The picture identifier we want to associate
	 * @return mixed $status The picture identifier if the photo is well insert and "error" else. 
	 */
	function associatePicture($tableElement, $idElement, $pictureId)
	{
		global $wpdb;
		$status = 'error';
		
		$tableElement = eva_tools::IsValid_Variable($tableElement);
		$idElement = eva_tools::IsValid_Variable($idElement);

		$query = 
			$wpdb->prepare(
				"INSERT INTO " . TABLE_PHOTO_LIAISON . " 
					(id, status, isMainPicture, idPhoto, idElement, tableElement) 
				VALUES 
					('', 'valid', 'no', '%d', '%d', '%s')"
				, $pictureId, $idElement, $tableElement);
		if($wpdb->query($query))
		{
			$status = $pictureId;
		}

		return $status;
	}

	/**
	*	Delete a picture. Put the picture status to "deleted"
	*
	*	@param integer $idPhoto The picture identifier to delete
	*
	*	@return mixed $status The result status ("ok" if all is ok, "error" is there is an error)
	*/
	function unAssociatePicture($tableElement, $idElement, $idPhoto)
	{
		global $wpdb;
		$status = 'error';

		$query = 
			$wpdb->prepare(
				"UPDATE " . TABLE_PHOTO_LIAISON . "
				SET status = 'deleted' 
				WHERE tableElement = '%s'
					AND idElement = '%d'
					AND idPhoto = '%d' "
				, $tableElement, $idElement, $idPhoto);
		if($wpdb->query($query))
		{
			$status = 'ok';
		}

		return $status;
	}

	/**
	*	Return a picture gallery for a given element, with the different action for each picture
	*
	*	@param mixed $tableElement The element type we want to get the gallery for
	*	@param integer $idElement The element identifier we want to get the gallery for
	*
	*	@return mixed $gallery The html code width the different element for the picture gallery
	*/
	function getGallery($tableElement, $idElement)
	{
		//<input style="clear:both;float:right;" type="button" value="' . __('Recharger la gallerie', 'evarisk') . '" id="testreloadme" onclick="javascript:reloadcontainer();" />
		$gallery = '
			<div id="galeriePhoto' . $tableElement . $idElement .'">
				<div class="galeryPhoto alignleft">
					<div id="thumbs' . $tableElement . $idElement .'" class="navigation" >
						<ul class="thumbs noscript">';

		$photos = evaPhoto::getPhotos($tableElement, $idElement);
		foreach($photos as $photo)
		{
			$gallery .= '
							<li >
								<a class="thumb" target="picture' . $tableElement . $idElement .'" name="leaf" href="' . EVA_HOME_URL . $photo->photo . '" title="' . $photo->description . '">
									<img src="' . EVA_HOME_URL . $photo->photo . '" alt="' . $photo->description . '" />
								</a>							
								<div class="caption">';

			switch($tableElement)
			{
				case TABLE_ACTIVITE:
					$activite = new EvaActivity($idElement);
					$activite->load();
					if($activite->getidPhotoAvant() == $photo->id)
					{
					$gallery .= '<div class="beforePictureSelection" onclick="javascript:unsetAsBeforePicture(\'' . $tableElement . '\',\'' . $idElement . '\',\'' . $photo->id . '\');" >
										-&nbsp;' . __('N\'est plus la photo avant l\'action', 'evarisk') . '
									</div>';
					}
					else
					{
					$gallery .= '<div class="beforePictureDeselection" onclick="javascript:setAsBeforePicture(\'' . $tableElement . '\',\'' . $idElement . '\',\'' . $photo->id . '\');" >
										-&nbsp;' . __('D&eacute;finir comme photo avant l\'action', 'evarisk') . '
									</div>';
					}
					if($activite->getidPhotoApres() == $photo->id)
					{
					$gallery .= '<div class="afterPictureSelection" onclick="javascript:unsetAsAfterPicture(\'' . $tableElement . '\',\'' . $idElement . '\',\'' . $photo->id . '\');" >
										-&nbsp;' . __('N\'est plus la photo apr&egrave;s l\'action', 'evarisk') . '
									</div>';
					}
					else
					{
					$gallery .= '<div class="afterPictureDeselection" onclick="javascript:setAsAfterPicture(\'' . $tableElement . '\',\'' . $idElement . '\',\'' . $photo->id . '\');" >
										-&nbsp;' . __('D&eacute;finir comme photo apr&egrave;s l\'action', 'evarisk') . '
									</div>';
					}
					// $moreOutputOptions = 'evarisk(".slideshow").remove();';
				break;
				default:
					$moreOutputOptions = '';
				break;
			}

			if($photo->isMainPicture == 'yes')
			{
			$gallery .= '<div class="pictureDefaultSelection" onclick="javascript:DeleteDefaultPicture(\'' . $tableElement . '\',\'' . $idElement . '\',\'' . $photo->id . '\');" >
										-&nbsp;' . __('N\'est plus la photo par d&eacute;faut', 'evarisk') . '
									</div>';
			}
			else
			{
			$gallery .= '<div class="pictureDefaultSelection" onclick="javascript:defaultPicture(\'' . $tableElement . '\',\'' . $idElement . '\',\'' . $photo->id . '\');" >
										-&nbsp;' . __('D&eacute;finir comme photo principale', 'evarisk') . '
									</div>';
			}
			$gallery .= '<div class="pictureDeletion" onclick="javascript:pictureDelete(' . $photo->id . ');" >
										-&nbsp;' . __('Supprimer', 'evarisk') . '
									</div>
								</div>
							</li>';
		}

		$gallery .= '
						</ul>
					</div>
					<!-- Start Advanced Gallery Html Containers -->
					<div id="gallery' . $tableElement . $idElement .'" class="content">
						<div id="controls" class="controls"></div>
						<div id="caption' . $tableElement . $idElement .'" class="caption-container"></div>
						<div class="slideshow-container">
							<div id="loading' . $tableElement . $idElement .'" class="loader"></div>
							<div id="slideshow' . $tableElement . $idElement .'" class="slideshow"></div>
						</div>
					</div>
				</div>
			</div>';

		{	/*	Create the gallery with gallerific jquery plugin AND define the function for picture deletion	*/
			$gallery .= 
			'<script type="text/javascript">
				function defaultPicture(tableElement, idElement, idPhoto)
				{
					evarisk("#defaultPicture' . $tableElement . '_' . $idElement . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
						"post": "true",  
						"table": "' . $tableElement . '",
						"idElement": "' . $idElement . '",
						"idPhoto": idPhoto,
						"act": "defaultPictureSelection"
					});
				}

				function setAsBeforePicture(tableElement, idElement, idPhoto)
				{
					evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
						"post": "true",  
						"table": "' . TABLE_ACTIVITE . '",
						"idElement": "' . $idElement . '",
						"idPhoto": idPhoto,
						"act": "setAsBeforePicture"
					});
				}
				function unsetAsBeforePicture(tableElement, idElement, idPhoto)
				{
					evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
						"post": "true",  
						"table": "' . TABLE_ACTIVITE . '",
						"idElement": "' . $idElement . '",
						"idPhoto": idPhoto,
						"act": "unsetAsBeforePicture"
					});
				}
				function setAsAfterPicture(tableElement, idElement, idPhoto)
				{
					evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
						"post": "true",  
						"table": "' . TABLE_ACTIVITE . '",
						"idElement": "' . $idElement . '",
						"idPhoto": idPhoto,
						"act": "setAsAfterPicture"
					});
				}
				function unsetAsAfterPicture(tableElement, idElement, idPhoto)
				{
					evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
						"post": "true",  
						"table": "' . TABLE_ACTIVITE . '",
						"idElement": "' . $idElement . '",
						"idPhoto": idPhoto,
						"act": "unsetAsAfterPicture"
					});
				}

				function DeleteDefaultPicture(tableElement, idElement, idPhoto)
				{
					evarisk("#defaultPicture' . $tableElement . '_' . $idElement . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
						"post": "true",  
						"table": "' . $tableElement . '",
						"idElement": "' . $idElement . '",
						"idPhoto": idPhoto,
						"act": "DeleteDefaultPictureSelection"
					});
				}

				function pictureDelete(idPicture)
				{
					if(confirm("' . __('Etes vous sur de vouloir supprimer cette photo?', 'evarisk') . '")){
						evarisk("#caption' . $tableElement . $idElement .'").html(\'<img src="' . PICTO_LOADING_ROUND . '" alt="loading" />\');
						setTimeout(function(){
							evarisk("#pictureGallery' . $tableElement . '_' . $idElement . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
								"post": "true",  
								"table": "' . $tableElement . '",
								"idElement": "' . $idElement . '",
								"idPicture": idPicture,
								"act": "deletePicture"
							});
						}, \'1500\');
					}
				}

				evarisk(document).ready(function() {
					// We only want these styles applied when javascript is enabled
					evarisk(\'div.navigation\').css({\'width\' : \'100%\', \'float\' : \'left\'});
					evarisk(\'div.content\').css(\'display\', \'block\');

					// Initially set opacity on thumbs and add
					// additional styling for hover effect on thumbs
					var onMouseOutOpacity = 0.67;
					evarisk(\'#thumbs' . $tableElement . $idElement .' ul.thumbs li\').opacityrollover({
						mouseOutOpacity:   onMouseOutOpacity,
						mouseOverOpacity:  1.0,
						fadeSpeed:         \'fast\',
						exemptionSelector: \'.selected\'
					});
					
					// Initialize Advanced Galleriffic Gallery
					var gallery = evarisk(\'#thumbs' . $tableElement . $idElement .'\').galleriffic({
						delay:                     2500,
						numThumbs:                 5,
						preloadAhead:              10,
						enableTopPager:            false,
						enableBottomPager:         true,
						maxPagesToShow:            8,
						imageContainerSel:         \'#slideshow' . $tableElement . $idElement .'\',
						controlsContainerSel:      \'#controls' . $tableElement . $idElement .'\',
						captionContainerSel:       \'#caption' . $tableElement . $idElement .'\',
						loadingContainerSel:       \'#loading' . $tableElement . $idElement .'\',
						renderSSControls:          true,
						renderNavControls:         true,
						enableKeyboardNavigation:  false,
						playLinkText:              \'Play Slideshow\',
						pauseLinkText:             \'Pause Slideshow\',
						prevLinkText:              \'&lsaquo; Previous Photo\',
						nextLinkText:              \'Next Photo &rsaquo;\',
						nextPageLinkText:          \'Next &rsaquo;\',
						prevPageLinkText:          \'&lsaquo; Prev\',
						enableHistory:             false,
						autoStart:                 false,
						syncTransitions:           true,
						defaultTransitionDuration: 0,
						onSlideChange:             function(prevIndex, nextIndex) {
							// \'this\' refers to the gallery, which is an extension of evarisk(\'#thumbs\')
							this.find(\'ul.thumbs\').children()
								.eq(prevIndex).fadeTo(\'fast\', onMouseOutOpacity).end()
								.eq(nextIndex).fadeTo(\'fast\', 1.0);
						},
						onPageTransitionOut:       function(callback) {
							this.fadeTo(\'fast\', 0.0, callback);
						},
						onPageTransitionIn:        function() {
							this.fadeTo(\'fast\', 1.0);
						}
					});
					setTimeout(function(){
						' . $moreOutputOptions . '
						if(typeof(removeSlideShowViewer) != "undefined")
						{
							evarisk(".slideshow").remove();
						}
					},500);
				});
			</script>';
		}

		return $gallery;
	}

	/**
	*	Return the form to upload picture with the jquery plugin uploadify
	*
	*	@param mixed $tableElement The element type we want to get the form for
	*	@param integer $idElement The element identifier we want to get the form for
	*
	*	@return mixed $uploadForm The html code for the upload form
	*/
	function getUploadForm($tableElement, $idElement)
	{
		$repertoireDestination = str_replace('\\', '/', EVA_UPLOADS_PLUGIN_DIR . $tableElement . '/' . $idElement . '/');
		$idUpload = 'mainPhoto' . $tableElement;
		$allowedExtensions = "['jpeg','jpg','png','gif']";
		$multiple = true;
		$actionUpload = str_replace('\\', '/', EVA_LIB_PLUGIN_URL . "photo/uploadPhoto.php");
		$photoDefaut = '';

		/*	Get the main picture for current element */
		$defaultPicture = evaPhoto::getMainPhoto($tableElement, $idElement);
		switch($tableElement)
		{
			case TABLE_CATEGORIE_DANGER:
				$photoDefaut = ($defaultPicture != 'error') ? (EVA_HOME_URL . $defaultPicture) : DEFAULT_DANGER_CATEGORIE_PICTO;
			break;
			case TABLE_GROUPEMENT:
				$photoDefaut = ($defaultPicture != 'error') ? (EVA_HOME_URL . $defaultPicture) : DEFAULT_GROUP_PICTO;
			break;
			case TABLE_UNITE_TRAVAIL:
				$photoDefaut = ($defaultPicture != 'error') ? (EVA_HOME_URL . $defaultPicture) : DEFAULT_WORKING_UNIT_PICTO;
			break;
			default:
				$photoDefaut = ($defaultPicture != 'error') ? (EVA_HOME_URL . $defaultPicture) : '';
			break;
		}

		$uploadForm = evaPhoto::getFormulaireUploadPhoto($tableElement, $idElement, $repertoireDestination, $idUpload, $allowedExtensions, $multiple, $actionUpload, $photoDefaut);

		return $uploadForm;
	}

	/**
	*	Get the main photo for a given element
	*
	*	@param mixed $tableElement The element type we want to get the main picture for
	*	@param integer $idElement The element identifier we want to get the main picture for
	*
	*	@return mixed $status The result status (The picture if all is ok, "error" is there is an error)
	*/
	function getMainPhoto($tableElement, $idElement)
	{
		global $wpdb;
		$status = 'error';

		$query = 
			$wpdb->prepare(
				"SELECT PICTURE.photo 
				FROM " . TABLE_PHOTO . " AS PICTURE
					INNER JOIN " . TABLE_PHOTO_LIAISON . " AS PICTURE_LINK ON (PICTURE_LINK.idPhoto = PICTURE.id)
				WHERE PICTURE_LINK.tableElement = '%s'
						AND PICTURE_LINK.idElement = '%d' 
						AND PICTURE_LINK.isMainPicture = 'yes'
						AND PICTURE_LINK.status = 'valid' "
				, $tableElement, $idElement);
		if($mainPhotoInformation = $wpdb->get_row($query))
		{
			$status = $mainPhotoInformation->photo;
		}
		if(($status != 'error') && !is_file(EVA_HOME_DIR . $status))
		{
			$status = 'error';
		}

		return $status;
	}

	/**
	*
	*/
	function isMainPicture($tableElement, $idElement, $idPicture)
	{
		global $wpdb;
		$status = 'error';

		$query = 
			$wpdb->prepare(
				"SELECT PICTURE_LINK.isMainPicture 
				FROM " . TABLE_PHOTO . " AS PICTURE
					INNER JOIN " . TABLE_PHOTO_LIAISON . " AS PICTURE_LINK ON (PICTURE_LINK.idPhoto = PICTURE.id)
				WHERE PICTURE_LINK.tableElement = '%s'
						AND PICTURE_LINK.idElement = '%d' 
						AND PICTURE.id = '%d' 
						AND PICTURE_LINK.status = 'valid' "
				, $tableElement, $idElement, $idPicture);
		if($mainPhotoInformation = $wpdb->get_row($query))
		{
			$status = $mainPhotoInformation->isMainPicture;
		}

		return $status;
	}

	/**
	*	Set the main photo for a given element
	*
	*	@param mixed $tableElement The element type we want to set the main picture for
	*	@param integer $idElement The element identifier we want to set the main picture for
	*	@param integer $idPhoto The picture identifier we want to set as the main picture for the given element
	*
	*	@return mixed $status The result status ("ok" if all is ok, "error" is there is an error)
	*/
	function setMainPhoto($tableElement, $idElement, $idPhoto, $isMainPicture = 'yes')
	{
		global $wpdb;
		$status = 'error';

		if($isMainPicture == 'yes')
		{
		/*	Delete the old main photo	*/
			$query = 
				$wpdb->prepare(
					"UPDATE " . TABLE_PHOTO_LIAISON . " 
						SET isMainPicture = 'no' 
						WHERE tableElement = '%s'
							AND idElement = '%d' "
					, $tableElement, $idElement);
			$wpdb->query($query);
		}

		/*	Set the main photo	*/
		$query = 
			$wpdb->prepare(
				"UPDATE " . TABLE_PHOTO_LIAISON . " 
					SET isMainPicture = '%s' 
					WHERE tableElement = '%s'
						AND idElement = '%d'
						AND idPhoto = '%d' "
				, $isMainPicture, $tableElement, $idElement, $idPhoto);
		if($wpdb->query($query))
		{
			$status = 'ok';
		}

		return $status;
	}

	/**
	 * Return a upload form with a thumbnail if multiple is false
	 *
	 * @param string $tableElement Table of the element which is the photo relative to.
	 * @param int $idElement Identifier in the table of the element which is the photo relative to.
	 * @param string $repertoireDestination Repository of the uploaded file.
	 * @param string $idUpload HTML div identifier.
	 * @param string $allowedExtensions Allowed extensions for the upload (ex:"['jpeg','png']"). All extensions is written "[]".
	 * @param bool $multiple Can the user upload multiple files in one time ?
	 * @param string $actionUpload The url of the file call when the user press on upload button.
	 * @param string $photoDefaut The default photo to display.
	 *
	 * @return string The upload form with eventually a thumbnail.
	 */
	function getFormulaireUploadPhoto($tableElement, $idElement, $repertoireDestination, $idUpload, $allowedExtensions, $multiple, $actionUpload, $photoDefaut, $texteBoutton = '', $onCompleteAction = '')
	{
		require_once(EVA_LIB_PLUGIN_DIR . 'upload.php' );

		$texteBoutton = ($texteBoutton == '') ? __("T&eacute;l&eacute;charger un fichier", "evarisk") : $texteBoutton;
		$onCompleteAction = ($onCompleteAction == '') ? 'reloadcontainer();' : $onCompleteAction;
		$actionUpload = ($actionUpload == '') ? EVA_LIB_PLUGIN_URL . 'photo/uploadPhoto.php' : $actionUpload;
		$photoDefaut = ($photoDefaut == '') ? '' : $photoDefaut;
		// $photoDefaut = ($photoDefaut == '') ? EVA_HOME_URL . 'medias/images/Icones/Divers/blankThumbnail.png' : $photoDefaut;
		$repertoireDestination = ($repertoireDestination == '') ? str_replace('\\', '/', EVA_UPLOADS_PLUGIN_DIR . $tableElement . '/' . $idElement . '/') : $repertoireDestination;
		$multiple = $multiple ? 'true' : 'false';

		$formulaireUpload = 
			'<script type="text/javascript">        
				evarisk(document).ready(function(){
					var uploader' . $idUpload . ' = new qq.FileUploader({
						element: document.getElementById("' . $idUpload . '"),
						action: "' . $actionUpload . '",
						allowedExtensions: ' . $allowedExtensions . ',
						multiple: ' . $multiple . ',
						params: {
							"tableElement": "' . $tableElement . '",
							"idElement": "' . $idElement . '",
							"folder": "' . $repertoireDestination . '",
							"abspath": "' . str_replace("\\", "/", ABSPATH) . '",
							"evarisk": "' . str_replace("\\", "/", EVA_HOME_DIR . "evarisk.php") . '"
						},
						onComplete: function(file, response){
							//evarisk("#thumb' . $idUpload . '").attr("src", "' . EVA_UPLOADS_PLUGIN_URL . $tableElement . "/" . $idElement  . '/" + response);
							' . $onCompleteAction . '
						}
					});

					evarisk("#' . $idUpload . ' .qq-upload-button").html("' . $texteBoutton . '");
					
					
					evarisk(".qq-upload-button").each(function(){
						// evarisk(this).html("' . $texteBoutton . '");
						uploader' . $idUpload . '._button = new qq.UploadButton({
							element: uploader' . $idUpload . '._getElement("button"),
							multiple: ' . $multiple . ',
							onChange: function(input){
								uploader' . $idUpload . '._onInputChange(input);
							}
						});
					});
					evarisk(".qq-upload-drop-area").each(function(){
						evarisk(this).html("<span>' . __("D&eacute;poser les fichiers ici pour les t&eacute;l&eacute;charger", "evarisk") . '</span>");
					});
					evarisk("#thumb' . $idUpload . '").parent().show();
				});
			</script>';
			if(($photoDefaut!='') && (is_file(str_replace(EVA_UPLOADS_PLUGIN_URL, EVA_UPLOADS_PLUGIN_DIR, $photoDefaut))))
			{
			$formulaireUpload .= 
			'<div class="thumbnailUpload alignright" id="defaultPicture' . $tableElement . '_' . $idElement . '" >
				<a href="' . $photoDefaut . '" target="mainPicture" ><img id="thumb' . $idUpload . '" src="' . $photoDefaut . '" class="" /></a>
			</div>';
			}
			$formulaireUpload .= 
			'<div id="' . $idUpload . '" class="divUpload">		
				<noscript>			
					<p>Please enable JavaScript to use file uploader.</p>
					<!-- or put a simple form for upload here -->
				</noscript>         
			</div>';

		return $formulaireUpload;
	}

	/**
	*	The different action called when we want to delete a picture (Set the status field to deleted). if the picture we want to delete is the main picture we update the different visible element containing this picture
	*
	*	@param mixed $tableElement The element type we want to delete the picture
	*	@param integer $idElement The element identifier we want to delete the picture
	*	@param integer $idPicture The picture identifier we want to delete
	*
	*	@return mixed The html code to update the different element and to output the succes message
	*/
	function deletePictureAction($tableElement, $idElement, $idPicture)
	{
		$tableElement = $_POST['table'];
		$idElement = $_POST['idElement'];

		/*	Check if the selected picture we want to delete is the main picture or not in order to update the main picture thumb	*/
		$mainPictureUpdate = '';
		$isTheMainPicture = evaPhoto::isMainPicture($tableElement, $idElement, $idPicture);
		if($isTheMainPicture == 'yes')
		{
			$definedDefaultPicture = '';
			switch($tableElement)
			{
				case TABLE_CATEGORIE_DANGER:
					$definedDefaultPicture = DEFAULT_DANGER_CATEGORIE_PICTO;
				break;
				case TABLE_GROUPEMENT:
					$definedDefaultPicture = DEFAULT_GROUP_PICTO;
				break;
				case TABLE_UNITE_TRAVAIL:
					$definedDefaultPicture = DEFAULT_WORKING_UNIT_PICTO;
				break;
			}

			$mainPictureUpdate = 
				'evarisk("#defaultPicture' . $tableElement . '_' . $idElement . '").html("<img src=\'' . $definedDefaultPicture . '\' alt=\'main picture\' />");
				evarisk("#photo' . $tableElement . $idElement . '").attr("src", "' . $definedDefaultPicture . '");';
		}

		/*	Desactivation de la photo selectionnee	*/
		$updateAssociationResult = evaPhoto::unAssociatePicture($tableElement, $idElement, $idPicture);

		$messageInfo = '<script type="text/javascript">
			evarisk(document).ready(function(){
				evarisk("#message' . $tableElement . '_' . $idElement . '").addClass("updated");';
		if($updateAssociationResult != 'error')
		{
			$messageInfo .= '
					evarisk("#message' . $tableElement . '_' . $idElement . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('L\'image a &eacute;t&eacute; supprim&eacute;e.', 'evarisk') . '</strong></p>') . '");';
		}
		else
		{
			$messageInfo .= '
					evarisk("#message' . $tableElement . '_' . $idElement . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('L\'image n\'a pas pu &ecirc;tre supprim&eacute;e.', 'evarisk') . '</strong></p>') . '");';
		}
		$messageInfo .= '
					evarisk("#message' . $tableElement . '_' . $idElement . '").show();
					setTimeout(function(){
						evarisk("#message' . $tableElement . '_' . $idElement . '").removeClass("updated");
						evarisk("#message' . $tableElement . '_' . $idElement . '").hide();
					},7500);
				});
			' . $mainPictureUpdate . '
			</script>';

		/*	Sortie du formulaire 	*/
		return $messageInfo . evaPhoto::outputGallery($tableElement, $idElement);
	}

	/**
	*	The different action called when we want to set/unset a picture as the main picture for an element
	*
	*	@param mixed $tableElement The element type we want to set/unset the main photo for
	*	@param integer $idElement The element identifier we want to set/unset the main photo for
	*	@param integer $idPicture The picture identifier we want to set/unset as the main picture
	*	@param mixed $isMainPicture Define if we want to set or unset as the main picture
	*
	*	@return mixed The html code to update the different element and to output the succes message
	*/
	function setMainPhotoAction($tableElement, $idElement, $idPicture, $isMainPicture = 'yes')
	{
		$updateMainPhotoResult = evaPhoto::setMainPhoto($tableElement, $idElement, $idPicture, $isMainPicture);

		$messageInfo = '<script type="text/javascript">
			evarisk(document).ready(function(){
				evarisk("#message' . $tableElement . '_' . $idElement . '").addClass("updated");';

		if($isMainPicture == 'yes')
		{
			if($updateMainPhotoResult == 'ok')
			{
				$messageInfo .= '
						evarisk("#message' . $tableElement . '_' . $idElement . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('L\'image a &eacute;t&eacute; correctement d&eacute;finie comme photo principale.', 'evarisk') . '</strong></p>') . '");';
			}
			else
			{
				$messageInfo .= '
						evarisk("#message' . $tableElement . '_' . $idElement . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('L\'image n\'a pas pu &ecirc;tre d&eacute;finie comme photo principale.', 'evarisk') . '</strong></p>') . '");';
			}
		}
		elseif($isMainPicture == 'no')
		{
			if($updateMainPhotoResult == 'ok')
			{
				$messageInfo .= '
						evarisk("#message' . $tableElement . '_' . $idElement . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('L\'image n\'est plus la photo principale.', 'evarisk') . '</strong></p>') . '");';
			}
			else
			{
				$messageInfo .= '
						evarisk("#message' . $tableElement . '_' . $idElement . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('L\'image n\'a pas pu &ecirc;tre d&eacute;finie comme n\&eacute;tant plus la photo principale.', 'evarisk') . '</strong></p>') . '");';
			}
		}

		$definedDefaultPicture = '';
		switch($tableElement)
		{
			case TABLE_CATEGORIE_DANGER:
				$definedDefaultPicture = DEFAULT_DANGER_CATEGORIE_PICTO;
			break;
			case TABLE_GROUPEMENT:
				$definedDefaultPicture = DEFAULT_GROUP_PICTO;
			break;
			case TABLE_UNITE_TRAVAIL:
				$definedDefaultPicture = DEFAULT_WORKING_UNIT_PICTO;
			break;
		}
		$defaultPicture = evaPhoto::getMainPhoto($tableElement, $idElement);
		$resultDefaultPicture = ($defaultPicture != 'error') ? (EVA_HOME_URL . $defaultPicture) : $definedDefaultPicture;

		$messageInfo .= '
					evarisk("#message' . $tableElement . '_' . $idElement . '").show();
					setTimeout(function(){
						evarisk("#message' . $tableElement . '_' . $idElement . '").removeClass("updated");
						evarisk("#message' . $tableElement . '_' . $idElement . '").hide();
					},5000);
					reloadcontainer();
				});
				evarisk("#photo' . $tableElement . $idElement . '").attr("src", "' . $resultDefaultPicture . '");
			</script>';

		return $messageInfo . '<img src="' . $resultDefaultPicture . '" alt="" />';
	}

	/**
	*	Output the picture gallery. Check if there are several picture to show or if there is only one picture and no main picture defined
	*
	*	@param mixed $tableElement The element type we want to show the gallery for
	*	@param integer $idElement The element identifier we want to show the gallery for
	*
	*	@return mixed $galleryOutput The html code with the gallery if there are picture to show or a button to show the gallery when only one picture is present for the element
	*/
	function outputGallery($tableElement, $idElement){
		$galleryOutput = '';

		$listePhotoElement = evaPhoto::getPhotos($tableElement, $idElement);
		$elementMainPhoto = evaPhoto::getMainPhoto($tableElement, $idElement);

		if((count($listePhotoElement) > 1) || (($elementMainPhoto == 'error') && (count($listePhotoElement) > 0)))
		{
			$galleryOutput = evaPhoto::getGallery($tableElement, $idElement);
		}
		elseif(count($listePhotoElement) >= 1)
		{
			$galleryOutput = '<input type="button" value="' . __('Voir la galerie', 'evarisk') . '" onclick="javascript:showGallery();" />';
		}

		return $galleryOutput;
	}

	/**
	*	Output the different element for a galery
	*
	*	@param mixed $tableElement The element type we want to show the gallery for
	*	@param integer $idElement The element identifier we want to show the gallery for
	*
	*	@return mixed The code wich will be shown in source code
	*/
	function galleryContent($tableElement, $idElement)
	{
		return 
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
<div id="pictureGallery' . $tableElement . '_' . $idElement . '" >' . evaPhoto::outputGallery($tableElement, $idElement) . '</div>';
	}

}