<?php
/**
* Plugin tools management
* 
* Define the different tools available in the plugin
* @author Evarisk <dev@evarisk.com>
* @version 5.1.4.5
* @package Digirisk
* @subpackage librairies
*/

/**
* Define the different tools available in the plugin
* @package Digirisk
* @subpackage librairies
*/
class digirisk_tools
{

	/**
	*	Create the html ouput code for the tools page
	*
	*	@return The html code to output for tools page
	*/
	function main_page(){
		echo digirisk_display::start_page(__('Outils du logiciel Digirisk', 'evarisk'), EVA_OPTIONS_ICON, __('Outils du logiciel', 'evarisk'), __('Outils du logiciel', 'evarisk'), TABLE_OPTION, false, '', false, true);
?>
<div id="digirisk_configurations_container" class="clear" >
	<div id="tools_tabs" >
		<ul>
			<li><a href="<?php echo  EVA_INC_PLUGIN_URL; ?>ajax.php?post=true&amp;nom=tools&amp;action=db_manager" title="digirisk_tools_tab_container" ><?php _e('V&eacute;rification de la base de donn&eacute;es', 'evarisk'); ?></a></li>
		</ul>
		<div id="digirisk_tools_tab_container" >&nbsp;</div>
	</div>
</div>
<script type="text/javascript" >
	evarisk(document).ready(function(){
		jQuery("#digirisk_tools_tab_container").html(jQuery("#round_loading_img").html());
		jQuery("#tools_tabs").tabs({
      select: function(event, ui){
				jQuery("#digirisk_tools_tab_container").html(jQuery("#round_loading_img").html());
				var url = jQuery.data(ui.tab, "load.tabs");
				jQuery("#digirisk_tools_tab_container").load(url);
				jQuery("#tools_tabs ul li").each(function(){
					jQuery(this).removeClass("ui-tabs-selected ui-state-active");
				});
				jQuery("#tools_tabs ul li:eq(" + ui.index + ")").addClass("ui-tabs-selected ui-state-active");
				return false;
      }
		});
	});
</script>
<?php
		echo digirisk_display::end_page();
	}

}
