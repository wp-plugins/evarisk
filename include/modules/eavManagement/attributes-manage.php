<?php
/**
 * New Attribute Administration Panel.
 *
 * @package Evarisk
 * @subpackage eav_attribute
 */

if ( !current_user_can('Evarisk_:_gerer_attributs') )
{
	wp_die(__('Cheatin&#8217; uh?'));
}

	$evaAction = false;
	if(isset($_REQUEST['act']) && ($_REQUEST['act'] == 'addevaUserGroup') && !$createOK)
	{
		$evaAction = true;
		$actionMessage = __('L\'attribut n\'&agrave; pas pu &ecirc;tre cr&eacute;&eacute;','evarisk');
	}
	if(isset($_REQUEST['act']) && ($_REQUEST['act'] == 'modevaUserGroup') && !$updateOK)
	{
		$evaAction = true;
		$actionMessage = __('L\'attribut n\'&agrave; pas pu &ecirc;tre mofifi&eacute;','evarisk');
	}
	if(isset($_REQUEST['act']) && ($_REQUEST['act'] == 'del') && !$deleteOK)
	{
		$evaAction = true;
		$actionMessage = __('L\'attribut n\'&agrave; pas pu &ecirc;tre supprim&eacute;','evarisk');
	}

	if($evaAction)
	{
?>
	<div id="evaMessage" class="updated fade below-h2" >
		<strong><img src="<?php echo  EVA_IMG_ICONES_PLUGIN_URL ?>error_vs.png" alt="response" style="vertical-align:middle;" /><?php echo $actionMessage ?></strong>
	</div>
	<script type="text/javascript" >setTimeout(function(){evarisk('#evaMessage').remove()},5000);</script>
<?php
	}

?>
<div class="wrap">
<h2 id="add-new-attribute"><?php _e('Ajouter un attribut') ?></h2>
<p><a href="<?php echo $_SERVER['REQUEST_URI']; ?>" ><?php _e('&larr; Retour aux attributs'); ?></a></p>

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

<table class="form-table">
	<tr class="form-field form-required">
		<th scope="row"><label for="attribute_frontend_label"><?php echo __('Label', 'evarisk'); ?> <span class="description"><?php _e('(required)'); ?></span></label></th>
		<td><input name="attribute_frontend_label" type="text" id="attribute_frontend_label" value="<?php echo ($attribute_frontend_label); ?>" /></td>
	</tr>

	<tr class="form-field">
		<th scope="row"><label for="attribute_frontend_input"><?php echo __('Type du champs', 'evarisk'); ?></label></th>
		<td><?php echo $eav_attribute->attributeInputTypeDropDown($attribute_frontend_input, $disableInputTypeDropDown); ?></td>
	</tr>

<?php
if($attribute_frontend_input == 'select') :
	$optionList = $eav_attribute->GetAttributeOption($attributeId);
?>
	<tr class="form-field">
		<th scope="row"><?php echo __('Liste de choix', 'evarisk'); ?></th>
		<td >
			<ul id="listeDeChoix" >
<?php
	if(is_array($optionList) && (count($optionList) > 0))
	{
		foreach($optionList as $key => $optionDescription)
		{
?>
				<li><input type="text" name="existingDropDownChoice[<?php echo $optionDescription->id; ?>]" value="<?php echo $optionDescription->nom; ?>" /></li>
<?php	
		}
	}
	else
	{
?>
				<li><input type="text" name="newDropDownChoice[]" value="" /></li>
<?php		
	}
?>
			</ul>
			<span id="addChoice" ><?php echo __('Ajouter un choix', 'evarisk'); ?></span>
		</td>
	</tr>
<?php
endif;
?>
</table>

<p class="submit">
	<input name="addattribute" type="submit" id="addattribute" class="button-primary" value="<?php echo $actionButtonLabel; ?>" />	
	
	<?php if(($attributeAction != 'add') && ($attributeAction != 'addattribute')) : ?>
	<input onclick="javascript:if(confirm('<?php echo __('&Ecirc;tes vous sur de vouloir supprimer cette entr&eacute;e?','evarisk') ; ?>')){evarisk('#act').val('del');evarisk('#attributeManagementForm').submit();}else{return false;}"  name="delAttribute" type="submit" id="delAttribute" class="button add-new-h2" value="<?php echo __('Supprimer l\'attribut', 'evarisk'); ?>" />
	<?php endif; ?>
</p>

</div>