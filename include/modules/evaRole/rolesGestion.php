<?php
/**
 * Group Administration Panel.
 *
 * @package Evarisk
 * @subpackage users
 */

	if ( !current_user_can('Evarisk_:_gerer_droit_d_acces') )
	{
		wp_die(__('Cheatin&#8217; uh?'));
	}

	$hasEvaRoleAction = false;
	if(isset($_REQUEST['act']) && ($_REQUEST['act'] == 'addevaRole') && !$createOK)
	{
		$hasEvaRoleAction = true;
		$actionMessage = __('Le r&ocirc;le n\'&agrave; pas pu &ecirc;tre cr&eacute;&eacute;','evarisk');
	}
	if(isset($_REQUEST['act']) && ($_REQUEST['act'] == 'modevaRole') && !$updateOK)
	{
		$hasEvaRoleAction = true;
		$actionMessage = __('Le r&ocirc;le n\'&agrave; pas pu &ecirc;tre mofifi&eacute;','evarisk');
	}
	if(isset($_REQUEST['act']) && ($_REQUEST['act'] == 'del') && !$deleteOK)
	{
		$hasEvaRoleAction = true;
		$actionMessage = __('Le r&ocirc;le n\'&agrave; pas pu &ecirc;tre supprim&eacute;','evarisk');
	}

	if($hasEvaRoleAction)
	{
?>
	<div id="evaMessage" class="updated fade below-h2" >
		<strong><img src="<?php echo  EVA_IMG_ICONES_PLUGIN_URL ?>error_vs.png" alt="response" style="vertical-align:middle;" /><?php echo $actionMessage ?></strong>
	</div>
	<script type="text/javascript" >setTimeout(function(){$('#evaMessage').remove()},5000);</script>
<?php
	}

?>
<div class="wrap" id="evaRole" >
<h2 id="add-new-role"><?php echo $managementTitle; ?></h2>
<p><a href="<?php echo $_SERVER['REQUEST_URI']; ?>" ><?php echo __('&larr; Retour aux r&ocirc;les','evarisk'); ?></a></p>

<?php if ( isset( $eav_errors ) && is_wp_error( $eav_errors ) ) : ?>
<div class="error">
	<ul>
	<?php
	foreach( $eav_errors->get_error_messages() as $message )
		echo "<li>$message</li>";
	?>
	</ul>
</div>
<?php endif; ?>

<div id="ajax-response"></div>

<fieldset class="mainRoleInfosFieldset" >
	<legend class="titleDiv" ><?php echo __('Informations principales','evarisk'); ?></legend>
	<table class="form-table">
		<tr class="form-field form-required">
			<th scope="row"><label for="eva_role_name"><?php echo __('Nom du r&ocirc;le', 'evarisk'); ?><span class="description"><?php _e('(required)'); ?></span></label></th>
			<td><input name="eva_role_name" type="text" id="eva_role_name" value="<?php echo $eva_role_name; ?>" /></td>
		</tr>

		<tr class="form-field">
			<th scope="row"><label for="eva_role_description"><?php echo __('Description du r&ocirc;le', 'evarisk'); ?></label></th>
			<td><textarea rows="5" cols="5" id="eva_role_description" name="eva_role_description" ><?php echo $eva_role_description; ?></textarea></td>
		</tr>
	</table>
</fieldset>

<fieldset class="groupCapabilitiesFieldset" >
	<legend class="titleDiv" ><?php echo __('Droits du r&ocirc;le','evarisk'); ?></legend>
	<table class="form-table">
		<tr class="form-field form-required">
			<th scope="row"><?php echo __('Droit d\'acc&egrave;s', 'evarisk'); ?>
				<div ><span id="cocheTout" ><?php echo __('Tout cocher', 'evarisk'); ?></span>&nbsp;/&nbsp;<span id="deCocheTout" ><?php echo __('Tout d&eacute;cocher', 'evarisk'); ?></span></div>
			</th>
			<td id="capabilities" >
			<?php
				foreach(getDroitEvarisk() as $key => $capability)
				{
					$checked = '';
					if(in_array($key, $roleCapabilities))
					{
						$checked = ' checked="checked" ';
					}
			?>
				<div class="capability" >
					<input type="checkbox" <?php echo $checked; ?>name="eva_role_capabilities[]" id="<?php echo $key ?>" value="<?php echo $key ?>" />
					<label for="<?php echo $key ?>" ><?php echo ucfirst(str_replace('_', '&nbsp;', $capability)); ?></label>
				</div>
			<?php
				}
			?>
			</td>
		</tr>
	</table>
</fieldset>

<p class="submit">
	<input name="addgroup" type="submit" id="addgroup" class="button-primary" value="<?php echo $actionButtonLabel; ?>" />
	<?php if(($evaRoleAction != 'add') && ($evaRoleAction != 'addevaRole')) : ?>
	<input onclick="javascript:if(confirm('<?php echo __('&Ecirc;tes vous sur de vouloir supprimer cette entr&eacute;e?','evarisk') ; ?>')){$('#act').val('del');$('#evaRoleForm').submit();}else{return false;}"  name="delGroup" type="submit" id="delGroup" class="button add-new-h2" value="<?php echo __('Supprimer le r&ocirc;le', 'evarisk'); ?>" />
	<?php endif; ?>
</p>

</div>