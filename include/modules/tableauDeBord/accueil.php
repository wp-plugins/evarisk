<?php
	require_once(EVA_LIB_PLUGIN_DIR . 'tableauDeBord/tableauDeBord.class.php');
?>
<div class="wrap">
	<div id="evaDashboardIntro" >
		<h2><b>EVARISK </b>: Progiciel gratuit d'aide &agrave; l'&eacute;valuation des risques professionnels</h2>
		<p><b>Evarisk est specialis&eacute;e dans l'&eacute;tude m&eacute;thodologique de la pr&eacute;vention des risques.</b></p>
		<p>Il d&eacute;coule de cette activit&eacute; la r&eacute;daction du document unique et les d&eacute;marches de pr&eacute;vention n&eacute;cessaires &agrave; la r&eacute;duction des risques.</p>
		<p><b>Evarisk</b> vous propose le premier logiciel libre d'aide &agrave; la r&eacute;daction du document unique. Gr&acirc;ce &agrave; ce logiciel gratuit en Open Source, vous pourrez g&eacute;rer la totalit&eacute; des acteurs, des unit&eacute;s, et des dangers que vous devez recenser dans votre document unique.</p>
	</div>
	<div class="metabox-holder clear">
		<script type="text/javascript">
			$(document).ready( function() {
				$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				$('.postbox h3, .postbox .handlediv').each(function(){$(this).unbind("click");});
				postboxes.add_postbox_toggles("evaDashboard");
				postboxes.init("evaDashboard");
				postboxes.save_state("evaDashboard");
				postboxes.save_order("evaDashboard");
			});
		</script>
<?php

	tableauDeBord::genereTableauDeBord();

?>		
		<div style="float:left;width:49%;" id="evaDashboard_LeftSide" >
			<?php
				/*	Output the different Meta Box define for the dashboard page	*/
				do_meta_boxes("evaDashboard_Left", "advanced", null);
			?>
		</div>
		<div style="float:right;width:49%;" id="evaDashboard_RightSide" >
			<?php
				/*	Output the different Meta Box define for the dashboard page	*/
				do_meta_boxes("evaDashboard_Right", "advanced", null);		
				//To preserve postBoxes order
				wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false);
				//To preserve postBoxes closure state
				wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false);
			?>
		</div>
	</div>
</div>