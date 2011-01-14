<?php
/**
 * Group Administration Panel.
 *
 * @package Evarisk
 * @subpackage users
 */

	if ( !current_user_can('Evarisk_:_gerer_groupes_utilisateurs') )
	{
		wp_die(__('Cheatin&#8217; uh?'));
	}

	$evaGroupAction = false;
	if(isset($_REQUEST['act']) && ($_REQUEST['act'] == 'addevaUserGroup') && !$createOK)
	{
		$evaGroupAction = true;
		$actionMessage = __('Le groupe n\'&agrave; pas pu &ecirc;tre cr&eacute;&eacute;','evarisk');
	}
	if(isset($_REQUEST['act']) && ($_REQUEST['act'] == 'modevaUserGroup') && !$updateOK)
	{
		$evaGroupAction = true;
		$actionMessage = __('Le groupe n\'&agrave; pas pu &ecirc;tre mofifi&eacute;','evarisk');
	}
	if(isset($_REQUEST['act']) && ($_REQUEST['act'] == 'del') && !$deleteOK)
	{
		$evaGroupAction = true;
		$actionMessage = __('Le groupe n\'&agrave; pas pu &ecirc;tre supprim&eacute;','evarisk');
	}

	if($evaGroupAction)
	{
?>
	<div id="evaGroupMessage" class="updated fade below-h2" >
		<strong><img src="<?php echo  EVA_IMG_ICONES_PLUGIN_URL ?>error_vs.png" alt="response" style="vertical-align:middle;" /><?php echo $actionMessage ?></strong>
	</div>
	<script type="text/javascript" >setTimeout(function(){evarisk('#evaGroupMessage').remove()},5000);</script>
<?php
	}

?>
<div class="wrap" id="groupeUtilisateur" >
<h2 id="add-new-group"><?php echo $managementTitle; ?></h2>
<p><a href="<?php echo $_SERVER['REQUEST_URI']; ?>" ><?php _e('&larr; Retour aux groupes'); ?></a></p>

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

<fieldset class="mainUserGroupInfosFieldset" >
	<legend class="titleDiv" ><?php echo __('Informations principales','evarisk'); ?></legend>
	<table class="form-table">
		<tr class="form-field form-required">
			<th scope="row"><label for="user_group_name"><?php _e('Nom du groupe'); ?><span class="description"><?php _e('(required)'); ?></span></label></th>
			<td><input name="user_group_name" type="text" id="user_group_name" value="<?php echo $user_group_name; ?>" /></td>
		</tr>

		<tr class="form-field">
			<th scope="row"><label for="user_group_description"><?php _e('Description du groupe'); ?></label></th>
			<td><textarea rows="5" cols="5" id="user_group_description" name="user_group_description" ><?php echo $user_group_description; ?></textarea></td>
		</tr>
	</table>
</fieldset>

<fieldset class="userGroupMembersFieldset" >
	<legend class="titleDiv" ><?php echo __('Utilisateurs du groupe','evarisk'); ?></legend>
	<?php echo __('Pour ajouter un utilisateur &agrave; un groupe vous pouvez le glisser dans la liste des utilisateurs du groupe ou double cliquer sur l\'utilisateur','evarisk'); ?>
	<table class="form-table">
		<tr>
			<td>
				<input type="hidden" id="groupUserList" name="groupUserList" value="<?php echo $groupUserList; ?>" />
				<script type="text/javascript">var PICTO_DELETE = '<?php echo PICTO_DELETE_VSMALL; ?>'</script>
				<div id="usersManagement">
					<h1 class="ui-widget-header" ><?php echo __('Liste des utilisateurs disponible','evarisk'); ?></h1>
					<div id="usersList">
						<div class="ui-widget-content">
							<ul>
								<?php 
									$elementStyle = ' style="display: none;" ';
									if( is_array($userAlreadyAffected) && ((count($userAlreadyAffected) - 1) == count($listExistingUser)))
									{
										$elementStyle = '';
									}
								?>
								<li id="nouser" <?php echo $elementStyle; ?> class="ui-state-disabled" ><?php echo __('Aucun utilisateur non affect&eacute;','evarisk'); ?></li>
								<?php
									foreach($listExistingUser as $key => $value)
									{
										if($value->ID != 1)
										{
											$user_info = get_userdata($value->ID);
											$id = 'user' . $value->ID;

											$elementStyle = '';
											if(in_array($id, $userAlreadyAffected))
											{
												$elementStyle = 'style = "display: none;"';
											}
											if((isset($user_info->last_name)) && ($user_info->last_name != null) && (isset($user_info->first_name)) && ($user_info->first_name != null))
											{
												echo '<li ' . $elementStyle . ' ondblclick="javascript:addUserToGroup(\'' . $id . '\', \'' . $user_info->last_name . " " . $user_info->first_name . '\');" id="' . $id . '" >' . $user_info->last_name . " " . $user_info->first_name . '</li>';
											}
											else
											{
												echo '<li ' . $elementStyle . ' ondblclick="javascript:addUserToGroup(\'' . $id . '\', \'' . $user_info->user_nicename . '\');" id="' . $id . '" >' . $user_info->user_nicename . '</li>';
											}
										}
									}
								?>
							</ul>
						</div>
					</div>
				</div>
			</td>
			<td>
				<div id="groupContent">
					<h1 class="ui-widget-header"><?php echo __('Liste des utilisateurs du groupe','evarisk'); ?></h1>
					<div class="ui-widget-content">
						<ol>
							<?php 
								$elementStyle = '';
								if(is_array($userAlreadyAffected) && (count($userAlreadyAffected) > 0) && (isset($userAlreadyAffected[0]) && ($userAlreadyAffected[0] != '')))
								{
									$elementStyle = 'style="display: none;"';
								}
							?>
							<li <?php echo $elementStyle; ?> class="placeholder" ><?php echo __('Glisser les utilisateurs ici','evarisk'); ?></li>
							<?php
								if(is_array($userAlreadyAffected) && (count($userAlreadyAffected) > 0))
								{
									foreach($userAlreadyAffected as $key => $value)
									{
										if($value != '')
										{
											$user_info = get_userdata(str_replace('user', '', $value));
											$id = $value;
											if((isset($user_info->last_name)) && ($user_info->last_name != null) && (isset($user_info->first_name)) && ($user_info->first_name != null))
											{
												echo '<li id="' . $id . '_added" >' . $user_info->last_name . " " . $user_info->first_name . '<img id="' . $id . '_del" onclick="javascript:deleteUserFromGroup(\'' . $id . '\');" src="' . PICTO_DELETE_VSMALL . '" alt="delete" /></li>';
											}
											else
											{
												echo '<li id="' . $id . '_added" >' . $user_info->user_nicename . '<img id="' . $id . '_del" onclick="javascript:deleteUserFromGroup(\'' . $id . '\');" src="' . PICTO_DELETE_VSMALL . '" alt="delete" /></li>';
											}
										}
									}
								}
							?>
						</ol>
					</div>
				</div>
			</td>
		</tr>
	</table>
</fieldset>

<p class="submit">
	<input name="addgroup" type="submit" id="addgroup" class="button-primary" value="<?php echo $actionButtonLabel; ?>" />
	<?php if(($evaUserGroupAction != 'add') && ($evaUserGroupAction != 'addevaUserGroup')) : ?>
	<input onclick="javascript:if(confirm('<?php echo __('&Ecirc;tes vous sur de vouloir supprimer cette entr&eacute;e?','evarisk') ; ?>')){evarisk('#act').val('del');evarisk('#userGroupManagementForm').submit();}else{return false;}"  name="delGroup" type="submit" id="delGroup" class="button add-new-h2" value="<?php echo __('Supprimer le groupe', 'evarisk'); ?>" />
	<?php endif; ?>
</p>

</div>