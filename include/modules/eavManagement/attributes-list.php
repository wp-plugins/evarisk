<h2 id="add-new-user-attributes"><?php echo __('Attributs Utilisateur', 'evarisk') ?><input type="submit" class="button add-new-h2" onclick="javascript:$('#act').val('add');$('#attributeManagementForm').submit();" value="Ajouter" name="AjouterNouvelAttribut"/></h2>

<?php
	if($createOK)
	{
		$actionMessage = __('L\'attribut &agrave; &eacute;t&eacute; cr&eacute; avec succ&eacute;s','evarisk');
	}
	if($updateOK)
	{
		$actionMessage = __('L\'attribut &agrave; &eacute;t&eacute; modifi&eacute; avec succ&eacute;s','evarisk');
	}
	if($deleteOK)
	{
		$actionMessage = __('L\'attribut &agrave; &eacute;t&eacute; supprim&eacute; avec succ&eacute;s','evarisk');
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
<?php print_column_headers('users_attributes') ?>
</tr>
</thead>
<tfoot>
<tr class="thead">
<?php print_column_headers('users_attributes', false) ?>
</tr>
</tfoot>

<tbody id="attributes" class="list:attributes attributes-list">
<?php
	$eav_attribute->setCurrentEntityTypeId($eav_attribute->getEntityInformation('eva_users'));
	$evariskUserEntityId = $eav_attribute->getEntityInformation('eva_users');

	$eav_attribute->attributeRowOutput($evariskUserEntityId);
?>
</tbody>
</table>