<?php
/**
* Template manager
* 
* Define the different method to manage the plugin template
* @author Evarisk <dev@evarisk.com>
* @version 5.1.4.5
* @package digirisk
* @subpackage librairies
*/

/**
* Define the different method to manage the plugin template
*
* @package digirisk
* @subpackage librairies
*/
class digirisk_display
{

	/**
	* Returns the header display of a classical HTML page.
	*
	* @see end_page
	*
	* @param string $titrePage Title of the page.
	* @param string $icone Path of the icon.
	* @param string $titreIcone Title attribute of the icon.
	* @param string $altIcon Alt attribute of the icon.
	* @param string $element_type Table where the page is link.
	* @param bool $boutonAjouter Must the page have a button "Add" next to the title ?
	* @param string $messageInfo The information message.
	* @param bool $choixAffichage Must the page offer a choice of display ?
	*
	* @return string HTML code of the header display.
	*/
	function start_page($titrePage, $icone, $titreIcone, $altIcon, $element_type, $boutonAjouter=true, $messageInfo='', $choixAffichage=false, $affichageNotes = true, $page_icon_id = ''){
		$debutPage = '';

		ob_start();
?>
<div class="digirisk_hide" id="loadingImg" ><div class="main_loading_pic_container" ><img src="<?php echo PICTO_LOADING; ?>" alt="loading..." /></div></div>
<div class="digirisk_hide" id="round_loading_img" ><div class="round_loading_img" ><img src="<?php echo PICTO_LOADING_ROUND; ?>" alt="loading..." /></div></div>
<div class="digirisk_hide" id="dataTable_search_icon" ><span class='ui-icon searchDataTableIcon' >&nbsp;</span></div>
<div class="wrap">
	<div class="icon32" <?php echo $page_icon_id; ?> >
<?php
	if($icone != ''){
?>
	<img alt="<?php echo $altIcon; ?>" src="<?php echo $icone; ?>" title="<?php echo $titreIcone; ?>" />
<?php
	}
	else{
?>
		&nbsp;
<?php
	}
?>
</div>
	<h2 >
<?php
		echo $titrePage;
		if($boutonAjouter){
?>
		<a class="button add-new-h2" onclick="javascript:document.getElementById(\'act\').value=\'add\'; document.forms.form.submit();"><?php _e('Ajouter', 'evarisk'); ?></a>
<?php
		}
?>
	</h2>
	<div id="champsCaches" class="clear digirisk_hide" >
		<input type="hidden" id="pagemainPostBoxReference" value="1" />
		<input type="hidden" id="identifiantActuellemainPostBox" value="1" />
	</div>
	<div id="message" class="fade below-h2 evaMessage"><?php echo $messageInfo; ?></div>
	<div class="main_page_options_container" >
<?php
		if($affichageNotes){
			echo evaNotes::noteDialogMaker();
?>
	<script type="text/javascript">
		evarisk(document).ready(function(){
<?php echo evaNotes::noteDialogScriptMaker(); ?>
		});
	</script>
<?php
		}
		if($choixAffichage){
?>
		<div id="digirisk_shape_selector" >
			<span id="rightEnlarging" class="rightEnlarging"></span>
			<div id="enlarging" class="enlarging"></div>
			<span id="equilize" class="enlarging digirisk_hide"></span>
			<span id="leftEnlarging" class="leftEnlarging"></span>
		</div>
<?php
		}
?>
	</div>
<?php

		$debutPage = ob_get_contents();;
		ob_end_clean();

		return $debutPage;
	}

	/**
	* Closes the "div" tag open in the header display  of a classical HTML page.
	*
	* @see start_page
	* @return  the closure.
	*/
	function end_page(){
		$end_page = '';

		ob_start();
?>
	<div class="clear digirisk_hide" id="ajax-response" >&nbsp;</div>
</div>
<script type="text/javascript" >
	evarisk(document).ready(function(){
		main_page_shape_selector();
	});
</script>
<?php
		$end_page = ob_get_contents();;
		ob_end_clean();

		return $end_page;
	}

}