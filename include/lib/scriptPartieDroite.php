<?php

	function getScriptPartieDroite($pageHook)
	{
		return '
			<script type="text/javascript">
				evarisk(document).ready(function(){
					evarisk(".if-js-closed").removeClass("if-js-closed").addClass("closed");
					evarisk(".postbox h3, .postbox .handlediv").each(function(){evarisk(this).unbind("click");});
					postboxes.add_postbox_toggles("' . $pageHook . '");
					postboxes.init("' . $pageHook . '");
					postboxes.save_state("' . $pageHook . '");
					postboxes.save_order("' . $pageHook . '");
				});
			</script>';
	}

?>