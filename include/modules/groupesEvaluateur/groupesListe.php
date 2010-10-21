<h2 id="add-new-user-group" ><?php _e('Groupes d\'&eacute;valuateurs') ?><input type="submit" class="button add-new-h2" onclick="javascript:$('#act').val('add');$('#evaUserEvaluatorGroupManagementForm').submit();" value="<?php _e('Ajouter') ?>" name="AjouterNouvelleMethode"/></h2>

<?php
	if($createOK)
	{
		$actionMessage = __('Le groupe &agrave; &eacute;t&eacute; cr&eacute; avec succ&eacute;s','evarisk');
	}
	if($updateOK)
	{
		$actionMessage = __('Le groupe &agrave; &eacute;t&eacute; modifi&eacute; avec succ&eacute;s','evarisk');
	}
	if($deleteOK)
	{
		$actionMessage = __('Le groupe &agrave; &eacute;t&eacute; supprim&eacute; avec succ&eacute;s','evarisk');
	}

	if($createOK || $updateOK || $deleteOK)
	{
?>
	<div id="evaGroupMessage" class="updated fade below-h2" >
		<strong><img src="<?php echo  EVA_IMG_ICONES_PLUGIN_URL ?>veille-reponse.gif" alt="response" style="vertical-align:middle;" /><?php echo $actionMessage ?></strong>
	</div>
	<script type="text/javascript" >setTimeout(function(){$('#evaGroupMessage').remove()},5000);</script>
<?php
	}
?>
<table class="widefat fixed" cellspacing="0">
<thead>
<tr class="thead">
<?php print_column_headers('evaUserEvaluatorGroup') ?>
</tr>
</thead>
<tfoot>
<tr class="thead">
<?php print_column_headers('evaUserEvaluatorGroup', false) ?>
</tr>
</tfoot>

<tbody id="users" class="list:user user-list">
<?php
	$evaUserEvaluatorGroup->RowOutput();
?>
</tbody>
</table>