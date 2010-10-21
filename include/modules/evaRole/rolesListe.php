<h2 id="add-new-role" ><?php _e('R&ocirc;le') ?><input type="submit" class="button add-new-h2" onclick="javascript:$('#act').val('add');$('#evaRoleForm').submit();" value="<?php _e('Ajouter') ?>" name="AjouterRole"/></h2>

<?php
	if($createOK)
	{
		$actionMessage = __('Le r&ocirc;le &agrave; &eacute;t&eacute; cr&eacute; avec succ&eacute;s','evarisk');
	}
	if($updateOK)
	{
		$actionMessage = __('Le r&ocirc;le &agrave; &eacute;t&eacute; modifi&eacute; avec succ&eacute;s','evarisk');
	}
	if($deleteOK)
	{
		$actionMessage = __('Le r&ocirc;le &agrave; &eacute;t&eacute; supprim&eacute; avec succ&eacute;s','evarisk');
	}

	if($createOK || $updateOK || $deleteOK)
	{
?>
	<div id="evaMessage" class="updated fade below-h2" >
		<strong><img src="<?php echo  EVA_IMG_ICONES_PLUGIN_URL ?>success_vs.png" alt="response" style="vertical-align:middle;" /><?php echo $actionMessage ?></strong>
	</div>
	<script type="text/javascript" >setTimeout(function(){$('#evaMessage').remove()},5000);</script>
<?php
	}
?>
<table class="widefat fixed" cellspacing="0">
<thead>
<tr class="thead">
<?php print_column_headers('evaRole') ?>
</tr>
</thead>
<tfoot>
<tr class="thead">
<?php print_column_headers('evaRole', false) ?>
</tr>
</tfoot>

<tbody id="users" class="list:user user-list">
<?php
	$evaRole->RowOutput();
?>
</tbody>
</table>