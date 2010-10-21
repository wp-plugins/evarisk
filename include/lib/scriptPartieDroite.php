<?php
	
	function getScriptPartieDroite($pageHook)
	{
		return '
			<script type="text/javascript">
				$(document).ready( function() {
					$(\'.if-js-closed\').removeClass(\'if-js-closed\').addClass(\'closed\');
					$(\'.postbox h3, .postbox .handlediv\').each(function(){$(this).unbind("click");});
					postboxes.add_postbox_toggles(\'' . $pageHook . '\');
					postboxes.init(\'' . $pageHook . '\');
					postboxes.save_state(\'' . $pageHook . '\');
					postboxes.save_order(\'' . $pageHook . '\');
					
					// if($(\'#screen-meta-main\').attr("id") != "screen-meta-main")
					// {
						// $(\'#screen-meta\').attr("id",\'screen-meta-main\');
						// $(\'#contextual-help-wrap\').attr("id",\'contextual-help-wrap-main\');
						// $(\'#screen-meta-links\').attr("id",\'screen-meta-links-main\');
						// $(\'#contextual-help-link-wrap\').attr("id",\'contextual-help-link-wrap-main\');
						// $("#contextual-help-link").unbind("click");
						// $(\'#contextual-help-link\').attr("id",\'contextual-help-link-main\');
						// $(\'#contextual-help-link-main\').click(function(){
							// $(\'#contextual-help-wrap-main\').toggleClass("hidden");
							// $(\'#screen-options-link-wrap-main\').toggleClass("hidden");
							// if(!($(\'#contextual-help-wrap-main\').is(".hidden")))
							// {
								// $(\'#contextual-help-link-main\').attr("style","background-image: url(http://localhost/wordpress/wp-admin/images/screen-options-right-up.gif?ver=20100531);");
							// }
							// else
							// {
								// $(\'#contextual-help-link-main\').attr("style","");
							// }
						// });
					// }
					// $(\'#show-settings-link\').click(function(){
						// $(\'#screen-options-wrap\').toggleClass("hidden");
						// $(\'#contextual-help-link-wrap\').toggleClass("hidden");
						// if(!($(\'#screen-options-wrap\').is(".hidden")))
						// {
							// $(\'#show-settings-link\').attr("style","background-image: url(http://localhost/wordpress/wp-admin/images/screen-options-right-up.gif?ver=20100531);");
						// }
						// else
						// {
							// $(\'#show-settings-link\').attr("style","");
						// }
					// });
					// $(\'#contextual-help-link\').click(function(){
						// $(\'#contextual-help-wrap\').toggleClass("hidden");
						// $(\'#screen-options-link-wrap\').toggleClass("hidden");
						// if(!($(\'#contextual-help-wrap\').is(".hidden")))
						// {
							// $(\'#contextual-help-link\').attr("style","background-image: url(http://localhost/wordpress/wp-admin/images/screen-options-right-up.gif?ver=20100531);");
						// }
						// else
						// {
							// $(\'#contextual-help-link\').attr("style","");
						// }
					// });
				});
			</script>';
	}
?>