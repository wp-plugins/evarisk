<?php

function getGallery($tableElement, $idElement)
{
	require_once(EVA_LIB_PLUGIN_DIR . 'photo/evaPhoto.class.php');
	
	$gallery = '
		<div id="galeriePhoto' . $tableElement . $idElement .'">
			<div class="galeryPhoto alignleft">
				<!-- Start Advanced Gallery Html Containers -->
				<div id="gallery' . $tableElement . $idElement .'" class="content">
					<div class="slideshow-container">
						<div id="loading' . $tableElement . $idElement .'" class="loader"></div>
						<div id="slideshow' . $tableElement . $idElement .'" class="slideshow"></div>
					</div>
					<div id="caption' . $tableElement . $idElement .'" class="caption-container"></div>
				</div>
				<div id="thumbs' . $tableElement . $idElement .'" class="navigation">
					<ul class="thumbs noscript">';
				
	$photos = evaPhoto::getPhotos($tableElement, $idElement);
	foreach($photos as $photo)
	{
		$gallery = $gallery . '
					<li>
						<a class="thumb" name="leaf" href="' . EVA_HOME_URL . $photo->photo . '" title="' . $photo->description . '">
							<img src="' . EVA_HOME_URL . $photo->photo . '" alt="' . $photo->description . '" />
						</a>
					</li>';
	}

	$gallery = $gallery . '
				</ul>
			</div>
			<div style="clear: both;"></div>
			<div id="photoUpload' . $tableElement . $idElement .'">' . __('Le bouton de t&eacute;l&eacute;chargement devrait apparaitre', 'evarisk') . '</div>
			<a href="javascript:$(\'#photoUpload' . $tableElement . $idElement .'\').fileUploadStart()">' . __('D&eacute;marrer le t&eacute;l&eacute;chargement', 'evarisk') . '</a> |  <a href="javascript:$(\'#photoUpload' . $tableElement . $idElement .'\').fileUploadClearQueue()">' . __('&Eacute;ffacer la file d\'attente', 'evarisk') . '</a>
		</div>
	<script type="text/javascript">
		
		$(document).ready(function(){
			$("#photoUpload' . $tableElement . $idElement .'").fileUpload({
				"uploader": "' . EVA_INC_PLUGIN_URL . 'js/uploadify/uploader.swf",
				"cancelImg": "' . EVA_IMG_DIVERS_PLUGIN_URL . 'cancel.png",
				"script": "../wp-content/plugins/Evarisk/include/js/uploadify/upload.php",
				"folder": "../wp-content/plugins/Evarisk/medias/uploads/photos",
				"fileDesc": "Image File (*.jpg ; *.jpeg ; *.png ; *.bmp ; *.gif ; *.tif ; *.tiff)",
				"fileExt": "*.jpg;*.jpeg;*.png;*.bmp;*.gif;*.tif;*.tiff",
				"multi": true,
				"buttonText": "' . __('Parcourir', 'evarisk') . '",
				"checkScript": "' . EVA_INC_PLUGIN_URL . 'js/uploadify/check.php",
				"displayData": "percentage",
				"tableElement": "' . $tableElement . '",
				"idElement": "' . $idElement . '",
				"table": "' . TABLE_PHOTO . '",
				"ajaxFile": "' . EVA_INC_PLUGIN_URL . 'ajax.php",
				"simUploadLimit": 5
			});
		});
		jQuery(document).ready(function($) {
			// We only want these styles applied when javascript is enabled
			$(\'div.navigation\').css({\'width\' : \'100%\', \'float\' : \'left\'});
			$(\'div.content\').css(\'display\', \'block\');

			// Initially set opacity on thumbs and add
			// additional styling for hover effect on thumbs
			var onMouseOutOpacity = 0.67;
			$(\'#thumbs ul.thumbs li\').opacityrollover({
				mouseOutOpacity:   onMouseOutOpacity,
				mouseOverOpacity:  1.0,
				fadeSpeed:         \'fast\',
				exemptionSelector: \'.selected\'
			});
			
			// Initialize Advanced Galleriffic Gallery
			var gallery = $(\'#thumbs\').galleriffic({
				delay:                     2500,
				numThumbs:                 3,
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
					// \'this\' refers to the gallery, which is an extension of $(\'#thumbs\')
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
		});
	</script>
	<div/>';
	return $gallery;
}
?>