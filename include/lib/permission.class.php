<?php
/**
* Plugin permissions management
* 
* Define method to manage permission for the software
* @author Evarisk <dev@evarisk.com>
* @version 5.1.3.1
* @package Digirisk
* @subpackage librairies
*/

/**
* Define method to manage permission for the software
* @package Digirisk
* @subpackage librairies
*/
class digirisk_permission
{
	
	/**
	*	Define the database table to ue un the entire script
	*/
	const dbTable = DIGI_DBT_PERMISSION_ROLE;

	/**
	*	Initialise permission for the administrator when installing the plugin
	*/
	function digirisk_init_permission()
	{
		/*	Récupération du rôle administrateur	*/
		$role = get_role('administrator');

		/*	Récupération des "anciens" droits	*/
		$droits = digirisk_permission::getDroitEvarisk();
		foreach($droits as $droit => $appellation)
		{/*	Lecture des "anciens" droits pour les retirer à l'administrateur	*/
			if(($role != null) && $role->has_cap($droit))
			{
				$role->remove_cap($droit);
			}
		}

		/*	Récupération des "nouveaux" droits	*/
		$droits = digirisk_permission::digirisk_get_permission();
		foreach($droits as $droit)
		{/*	Lecture des "nouveaux" droits pour affectation à l'administrateur	*/
			if(($role != null) && !$role->has_cap($droit->permission))
			{
				$role->add_cap($droit->permission);
			}
		}

		/*	Vidage de l'objet rôle	*/
		unset($role);
	}

	/**
	*	Allows to get the permission list into database
	*
	*	@return object $permissionsList A wordpress database object with the existing permission list
	*/
	function digirisk_get_permission()
	{
		global $wpdb;

		$query = $wpdb->prepare(
		"SELECT * FROM 
		" . DIGI_DBT_PERMISSION . "
		WHERE status = 'valid' ");

		$permissionsList = $wpdb->get_results($query);

		return $permissionsList;
	}

	/**
	*	Call the different element in order to edit rights per user
	*
	*	@return string The html output of the permission list for a specific user
	*/
	function user_permission_management()
	{
		global $digi_wp_role;

		/*	Récupération des informations concernant l'utilisateur en cours d'édition	*/
		$user = new WP_User($_REQUEST['user_id']);

		ob_start();
		self::permission_management($user);
		$digiPermissionForm = ob_get_contents();
		ob_end_clean();

		echo '<h3>' . __('Droits d\'acc&egrave;s de l\'utilisateur pour le logiciel Digirisk', 'digirisk') . '</h3>' . $digiPermissionForm;
	}

	/**
	*	Output the html table with the permission list stored by module and sub-module
	*/
	function permission_management($elementToManage)
	{
		global $digi_wp_role;
		if(!is_object($digi_wp_role))
		{
			/*	Instanciation de l'objet role de worpdress	*/
			$digi_wp_role = new WP_Roles();
		}
		$permissionList = array();
		$permissionCap = array();

		/*	Récupération des permissions créées pour rangement par module	*/
		$existingPermission = self::digirisk_get_permission();
		foreach($existingPermission as $permission)
		{
			$permissionList[$permission->permission_module][$permission->permission_sub_module][] = $permission->permission;
			$permissionCap[$permission->permission]['type'] = $permission->permission_type;
			$permissionCap[$permission->permission]['subtype'] = $permission->permission_sub_type;
		}

?>
<table summary="User rights for digirisk" cellpadding="0" cellspacing="0" class="form-table" >
<?php
		if(($_REQUEST['user_id'] != '') && ($_REQUEST['user_id'] > 0))
		{
?>
	<tr>
		<th><?php _e('L&eacute;gende des couleurs sur les permissions', 'evarisk'); ?></th>
		<td>
			<span class="permissionGrantedFromParent" ><?php _e('Permission obtenue par le r&ocirc;le de l\'utilisateur', 'evarisk'); ?></span><br/>
			<span class="permissionGranted" ><?php _e('Permission ajout&eacute;e en plus de celle du r&ocirc;le de l\'utilisateur', 'evarisk'); ?></span>
		</td>
	</tr>
<?php
		}
?>
	<tr>
		<th><?php _e('Raccourci d\'attribution', 'evarisk'); ?></th>
		<td>
			<span class="checkall_right" id="add_checkall" ><?php _e('Tous les droits', 'evarisk'); ?></span>&nbsp;/&nbsp;<span class="uncheckall_right" id="remove_uncheckall" ><?php _e('Aucun droit', 'evarisk'); ?></span><br/>
			<span class="checkall_link" id="add_menu" ><?php _e('Tous les menus', 'evarisk'); ?></span>&nbsp;/&nbsp;<span class="uncheckall_link" id="remove_menu" ><?php _e('Aucun menu', 'evarisk'); ?></span><br/>
			<span class="checkall_link" id="add_read" ><?php _e('Tous les droits en lecture', 'evarisk'); ?></span>&nbsp;/&nbsp;<span  class="uncheckall_link" id="remove_read" ><?php _e('Aucun droit en lecture', 'evarisk'); ?></span><br/>
			<span class="checkall_link" id="add_write" ><?php _e('Tous les droits en &eacute;criture', 'evarisk'); ?></span>&nbsp;/&nbsp;<span  class="uncheckall_link" id="remove_write" ><?php _e('Aucun droit en &eacute;criture', 'evarisk'); ?></span><br/>
			<span class="checkall_link" id="add_delete" ><?php _e('Tous les droits en suppression', 'evarisk'); ?></span>&nbsp;/&nbsp;<span  class="uncheckall_link" id="remove_delete" ><?php _e('Aucun droit en suppression', 'evarisk'); ?></span><br/>
		</td>
	</tr>
	<tr>
		<th>&nbsp;</th>
	</tr>
<?php
		foreach($permissionList as $module => $subModule)
		{
?>
	<tr>
		<th>
			<?php _e('permission_' . $module, 'evarisk'); ?>
			<div class="digi_permission_check_all" ><span id="check_selector_<?php echo $module; ?>" class="checkall" ><?php _e('Tout cocher', 'evarisk'); ?></span>&nbsp;/&nbsp;<span id="uncheck_selector_<?php echo $module; ?>" class="uncheckall" ><?php _e('Tout d&eacute;cocher', 'evarisk'); ?></span></div>
		</th>
		<td>
<?php
			foreach($subModule as $subModuleName => $moduleContent)
			{
?>
			<div class="sub_module <?php echo ($subModuleName != '') ? 'permission_module_' . $subModuleName : ''; ?>" >
				<div class="sub_module_name" >
<?php
				if($subModuleName)
				{
					_e('permission_' . $module . '_' . $subModuleName, 'evarisk');
				}
				else
				{
					_e('permission_' . $module, 'evarisk');
				}
?>
				</div>
				<div class="sub_module_content" >
					<div class="digi_permission_check_all" ><span id="check_selector_<?php echo $module . '_' . $subModuleName; ?>" class="checkall" ><?php _e('Tout cocher', 'evarisk'); ?></span>&nbsp;/&nbsp;<span id="uncheck_selector_<?php echo $module . '_' . $subModuleName; ?>" class="uncheckall" ><?php _e('Tout d&eacute;cocher', 'evarisk'); ?></span></div>
<?php
				/*	Liste des permissions pour le module et le sous-module	*/
				foreach($moduleContent as $permission)
				{
					$checked = $permissionNameClass = '';
					$roleToCopy = isset($_REQUEST['roleToCopy']) ? eva_tools::IsValid_Variable($_REQUEST['roleToCopy']) : '';
					$action = isset($_REQUEST['save']) ? eva_tools::IsValid_Variable($_REQUEST['save']) : '';
					if(($roleToCopy != '') && ($action == 'ok'))
					{
						$roleDetails = $digi_wp_role->get_role($roleToCopy);
						if($roleDetails->has_cap($permission))
						{
							$checked = 'checked="checked"';
						}
					}
					elseif(($elementToManage != null) && $elementToManage->has_cap($permission))
					{
						$checked = 'checked="checked"';
						$permissionNameClass = 'permissionGranted';
						if ( count($elementToManage->caps) > count($elementToManage->roles) && apply_filters('additional_capabilities_display', true, $elementToManage) )
						{
							$roleDetails = $digi_wp_role->get_role(implode('', $elementToManage->roles));
							if ( $roleDetails->has_cap($permission) ) 
							{
								$permissionNameClass = 'permissionGrantedFromParent';
							}
						}
					}
					echo '<input type="checkbox" class="' . $module . ' ' . $subModuleName . ' ' . $module . '_' . $subModuleName . ' ' . $permissionCap[$permission]['type'] . ' ' . $permissionCap[$permission]['subtype'] . ' ' . $permissionCap[$permission]['type'] . '_' . $permissionCap[$permission]['subtype'] . '" name="digi_permission[' . $permission . ']" id="digi_permission_' . $permission . '" value="yes" ' . $checked . ' />&nbsp;<label for="digi_permission_' . $permission . '" class="' . $permissionNameClass . '" >' . __($permission, 'evarisk') . '</label><br/>';
				}
?>
				</div>
			</div>
<?php
			}
?>
		</td>
	</tr>
<?php
		}
?>
</table>
<script type="text/javascript" >
	evarisk(document).ready(function(){
		/**
		*	Define action when clicking on checkall/uncheckall for a module or a sub module
		*/
		evarisk('.checkall').click(function(){
			var module = evarisk(this).attr("id").replace("check_selector_", "");
			evarisk("." + module).each(function(){
				evarisk(this).attr("checked", true);
			});
		});
		evarisk('.uncheckall').click(function(){
			var module = evarisk(this).attr("id").replace("uncheck_selector_", "");
			evarisk("." + module).each(function(){
				evarisk(this).attr("checked", false);
			});
		});

		/**
		*	Define action chen clicking on checkall/uncheckall into the link
		*/
		evarisk('.checkall_link').click(function(){
			var module = evarisk(this).attr("id").replace("add_", "");
			evarisk("." + module).each(function(){
				evarisk(this).attr("checked", true);
			});
		});
		evarisk('.uncheckall_link').click(function(){
			var module = evarisk(this).attr("id").replace("remove_", "");
			evarisk("." + module).each(function(){
				evarisk(this).attr("checked", false);
			});
		});

		/**
		*	Define action chen clicking on checkall/uncheckall into the link
		*/
		evarisk('.checkall_right').click(function(){
			var module = evarisk(this).attr("id").replace("add_", "");
			evarisk("." + module).each(function(){
				evarisk(this).click();
			});
		});
		evarisk('.uncheckall_right').click(function(){
			var module = evarisk(this).attr("id").replace("remove_", "");
			evarisk("." + module).each(function(){
				evarisk(this).click();
			});
		});
	});
</script>
<?php
	}

	/**
	*	Creation of the element management page
	*/
	function elementMainPage()
	{
		global $digi_wp_role;
		global $digi_role;

		$output = $message = '';
		$action = isset($_REQUEST['action']) ? eva_tools::IsValid_Variable($_REQUEST['action']) : '';
		$save = isset($_REQUEST['save']) ? eva_tools::IsValid_Variable($_REQUEST['save']) : '';
		$formAction = isset($_REQUEST[self::dbTable . '_action']) ? eva_tools::IsValid_Variable($_REQUEST[self::dbTable . '_action']) : '';
		$role = isset($_REQUEST['role']) ? eva_tools::IsValid_Variable($_REQUEST['role']) : '';
		$editionInProgress = false;

		/*	Instanciation de l'objet role de worpdress	*/
		$digi_wp_role = new WP_Roles();

		/*	Récupération des roles créés dans digirisk	*/
		$digi_role = array();
		$digiRoles = self::digirisk_get_role();
		foreach($digiRoles as $digiRole)
		{
			$digi_role[$digiRole->role_internal_name] = $digiRole;
		}

		$actionResult = self::elementAction();
		if(($actionResult == 'done') || ($actionResult == 'nothingToUpdate'))
		{
			$message = '<img src="' . EVA_MESSAGE_SUCCESS . '" alt="' . $actionResult . '" class="messageIcone" />' . __('Le r&ocirc;le a &eacute;t&eacute; correctement enregistr&eacute;', 'evarisk');
			if($formAction == 'delete')
			{
			$message = '<img src="' . EVA_MESSAGE_SUCCESS . '" alt="' . $actionResult . '" class="messageIcone" />' . __('Le r&ocirc;le a &eacute;t&eacute; correctement supprim&eacute;', 'evarisk');
			}
		}
		elseif(($actionResult == 'error'))
		{
			$message = '<img src="' . EVA_MESSAGE_ERROR . '" alt="' . $actionResult . '" class="messageIcone" />' . __('Une erreur est survenue lors de l\'enregistrement du r&ocirc;le', 'evarisk');
		}
		elseif(($actionResult == 'rightAdded'))
		{
			$message = '<img src="' . EVA_MESSAGE_SUCCESS . '" alt="' . $actionResult . '" class="messageIcone" />' . __('Les droits du r&ocirc;le ont bien &eacute;t&eacute; mis &agrave; jour', 'evarisk');
		}
		elseif($save == 'ok')
		{
			$message = '<img src="' . EVA_MESSAGE_SUCCESS . '" alt="' . $actionResult . '" class="messageIcone" />' . __('Le r&ocirc;le a &eacute;t&eacute; correctement ajout&eacute;', 'evarisk');
		}

		if((($action == 'edit') && ($role != '')) || ($action == 'add'))
		{
			/*	Get informations about the current element being edited	*/
			$currentEditedElement = self::getElement($role);

			/*	Check if the wanted element realy exist	*/
			if((count($currentEditedElement) > 0) && ($action != 'add'))
			{
				$editionPageTitle = sprintf(__('&Eacute;dition du r&ocirc;le: %s', 'evarisk'), '<span class="digiriskUserGroupEditionName" >' . translate_user_role($currentEditedElement['name']) . '</span>');
				$editionInProgress = true;
				/*	On vérifie que l'utilisateur a bien les droits sur la page courante, sinon on lui affiche un message en le remettant sur la page principale	*/
				if(!current_user_can('digi_edit_user_role'))
				{
					$editionInProgress = false;
					$message = addslashes('<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Vous n\'&ecirc;tes pas autoris&eacute; &agrave; utiliser cette fonctionnalit&eacute;', 'evarisk') . '</strong>');
					$output .= 
'<script type="text/javascript">
	evarisk(document).ready(function(){
		actionMessageShow("#message", "' . $message . '");
		setTimeout(\'actionMessageHide("#message")\',7500);
	});
</script>';
				}
			}
			elseif($action == 'add')
			{
				$id = '';
				$currentEditedElement = '';
				$editionPageTitle = __('Ajouter un r&ocirc;le pour digirisk', 'evarisk');
				$editionInProgress = true;
				/*	On vérifie que l'utilisateur a bien les droits sur la page courante, sinon on lui affiche un message en le remettant sur la page principale	*/
				if(!current_user_can('digi_add_user_role'))
				{
					$editionInProgress = false;
					$message = addslashes('<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Vous n\'&ecirc;tes pas autoris&eacute; &agrave; utiliser cette fonctionnalit&eacute;', 'evarisk') . '</strong>');
					$output .= 
'<script type="text/javascript">
	evarisk(document).ready(function(){
		actionMessageShow("#message", "' . $message . '");
		setTimeout(\'actionMessageHide("#message")\',7500);
	});
</script>';
				}
			}
		}

		if(!$editionInProgress)
		{	/*	In case that we are on the listing page	*/
			/*	Output the list of employees groups	*/
			$output .= EvaDisplayDesign::afficherDebutPage(__('Gestion des droits des utilisateurs par r&ocirc;le', 'evarisk'), DIGI_USER_RIGHT_ICON_S, __('Gestion des droits des utilisateurs par r&ocirc;le', 'evarisk'), __('Gestion des droits des utilisateurs par r&ocirc;le', 'evarisk'), self::dbTable, false, $message, false);
			if(current_user_can('digi_add_user_role'))
			{
				$output .= '<h2 class="clear" ><a href="' . admin_url('users.php?page=' . DIGI_URL_SLUG_USER_RIGHT . '&amp;action=add') . '" class="button add-new-h2" >' . __('Ajouter un r&ocirc;le', 'evarisk') . '</a></h2>';
			}
			$elementList = self::getElement();
			$output .= self::elementList($elementList);

			/*	Ajoute le formulaire de suppression d'un element	*/
			$output .= '<form method="post" id="' . self::dbTable . '_delete_form" action="" ><input type="hidden" name="' . self::dbTable . '_action" id="' . self::dbTable . '_action" value="delete" /><input type="hidden" name="' . self::dbTable . '[id]" id="' . self::dbTable . '_delete_form_id" value="" /></form>';
		}
		else
		{	/*	In case that we are on the edition/addition page	*/
			/*	Start the page content	*/
			$output .= EvaDisplayDesign::afficherDebutPage($editionPageTitle, DIGI_USER_GROUP_ICON_S, __('Groupes d\'utilisateurs', 'evarisk'), __('Groupes d\'utilisateurs', 'evarisk'), self::dbTable, false, $message, false);

			/*	Add the form to edit the element	*/
			$output .= self::elementEdition($currentEditedElement, $role);
		}

		/*	Close the page content	*/
		$output .= EvaDisplayDesign::afficherFinPage();

		if(($actionResult != '') || ($save == 'ok'))
		{
			$output .= '
<script type="text/javascript" >
	evarisk("#message").addClass("updated");
</script>';
		}

		echo $output;
	}

	/**
	*	Regroup the different action to manage the element
	*/
	function elementAction()
	{
		global $wpdb;
		global $current_user;
		global $digi_role;
		global $digi_wp_role;

		/*	Initialize the different vars usefull for the action	*/
		$pageMessage = $actionResult = '';
		$action = isset($_REQUEST[self::dbTable . '_action']) ? eva_tools::IsValid_Variable($_REQUEST[self::dbTable . '_action']) : '';
		$role = isset($_REQUEST[self::dbTable]['id']) ? eva_tools::IsValid_Variable($_REQUEST[self::dbTable]['id']) : '';

		if(($role == '') || array_key_exists($role, $digi_role))
		{
			$roleForId = self::digirisk_get_role($role, 'role_internal_name');
			$roleId = $roleForId[0]->id;
			/*	Basic action 	*/
			if(($action != '') && (($action == 'edit') || ($action == 'editandcontinue')))
			{/*	Edit action	*/
				$_REQUEST[self::dbTable]['last_update_date'] = date('Y-m-d H:i:s');
				$actionResult = eva_database::update($_REQUEST[self::dbTable], $roleId, self::dbTable);
			}
			elseif(($action != '') && (($action == 'delete')))
			{/*	Delete action	*/
				$_REQUEST[self::dbTable]['deletion_date'] = date('Y-m-d H:i:s');
				$_REQUEST[self::dbTable]['deletion_user_id'] = $current_user->ID;
				$_REQUEST[self::dbTable]['status'] = 'deleted';
				$actionResult = eva_database::update($_REQUEST[self::dbTable], $roleId, self::dbTable);
				$digi_wp_role->remove_role($role);
			}
			elseif(($action != '') && (($action == 'save') || ($action == 'saveandcontinue') || ($action == 'add')))
			{/*	Add action	*/
				$_REQUEST[self::dbTable]['role_internal_name'] = 'digirisk_' . str_replace('-', '_', sanitize_title($_REQUEST[self::dbTable]['role_name']));
				$_REQUEST[self::dbTable]['creation_date'] = date('Y-m-d H:i:s');
				$_REQUEST[self::dbTable]['creation_user_id'] = $current_user->ID;
				$actionResult = eva_database::save($_REQUEST[self::dbTable], self::dbTable);

				$role = $_REQUEST[self::dbTable]['role_internal_name'];
				$roleName = $_REQUEST[self::dbTable]['role_name'];
				$digi_wp_role->add_role($role, $roleName);

				$moreParamsForRoleCreation = '';
				$roleToCopy = isset($_REQUEST['roleToCopy']) ? eva_tools::IsValid_Variable($_REQUEST['roleToCopy']) : '';
				if($roleToCopy != '')
				{
					$moreParamsForRoleCreation = '&roleToCopy=' . $roleToCopy;
				}

				wp_redirect(admin_url('users.php?page=' . DIGI_URL_SLUG_USER_RIGHT . "&action=edit&role=" . $role . "&save=ok" . $moreParamsForRoleCreation));
			}
		}
		elseif(!array_key_exists($role, $digi_role))
		{
			$actionResult = 'rightAdded';
		}

		/*	Permission affectation to the selected role	*/
		if(($role != '') && ($action != 'delete') && ($action != 'add'))
		{
			$roleInEdition = $digi_wp_role->get_role($role);
			$existingPermission = self::digirisk_get_permission();
			foreach($existingPermission as $permission)
			{
				if(!$roleInEdition->has_cap($permission->permission) && is_array($_POST['digi_permission']) && array_key_exists($permission->permission, $_POST['digi_permission']))
				{
					$roleInEdition->add_cap($permission->permission);
				}
				elseif(($roleInEdition->has_cap($permission->permission) && is_array($_POST['digi_permission']) && !array_key_exists($permission->permission, $_POST['digi_permission'])) || (!is_array($_POST['digi_permission'])))
				{
					$roleInEdition->remove_cap($permission->permission);
				}
			}
		}

		return $actionResult;
	}
	/**
	*	Create a html table output for element list presentation
	*
	*	@param object $elementList A wordpress object containing the entire element list with the different informations to ouput
	*
	*	@return string $elementOutputTable The html output completely build with the element's list to output
	*/
	function elementList($elementList)
	{
		global $digi_role;

		/*	Define the different table column and column class	*/
		unset($titres,$classes, $idLignes, $lignesDeValeurs);
		$idLignes = null;
		$idTable = 'digirisk_user_groups_';
		$titres[] = __("Nom du r&ocirc;le", 'evarisk');
		$titres[] = __("Droits digirisk", 'evarisk');
		$titres[] = __("Actions", 'evarisk');
		$classes[] = 'digirisk_user_role_column_name';
		$classes[] = 'digirisk_user_role_column_caps_details';
		$classes[] = 'digirisk_user_role_column_action';

		/*	Récupére les droits liés au logiciel digirisk	*/
		$digiriskPermission = self::digirisk_get_permission();
		foreach($digiriskPermission as $permission)
		{
			$digiRight[$permission->permission_module][] = $permission->permission;
		}

		unset($ligneDeValeurs);
		$i=0;
		if(count($elementList) > 0)
		{
			foreach($elementList as $elementKey => $element)
			{
				/*	Define each line id for the table	*/
				$idLignes[] = 'digirisk_users_roles_' . $elementKey;

				/*	Define each column value for each line	*/
				$roleName = translate_user_role($element['name']);
				if(array_key_exists($elementKey, $digi_role))
				{
					$roleName = __($digi_role[$elementKey]->role_name, 'evarisk');
				}
				$lignesDeValeurs[$i][] = array('value' => $roleName, 'class' => 'digirisk_user_groups_cell_name');
				$roleCapabilities = '  ';
				foreach($digiRight as $rightCategory => $rightCategoryContent)
				{
					$rolePermission = ' ';
					foreach($rightCategoryContent as $capabilityName)
					{
						if(array_key_exists($capabilityName, $element['capabilities']))
						{
							$rolePermission .= __($capabilityName, 'evarisk') . ', ';
						}
					}
					$rolePermission = trim(substr($rolePermission, 0, -2));
					if($rolePermission != '')
					{
						$roleCapabilities .= '<span class="digi_permission_category_name" >' . __('permission_' . $rightCategory, 'evarisk') . '&nbsp;:&nbsp;</span>' . $rolePermission . '<br/>';
					}
				}
				if(!current_user_can('digi_view_detail_user_role'))
				{
					$roleCapabilities = __('Vous n\'avez pas les autorisations pour voir le d&eacute;tail de ce r&ocirc;le', 'evarisk');
				}
				elseif(trim($roleCapabilities) == '')
				{
					$roleCapabilities = __('Aucun droit du logiciel digirisk n\'est affect&eacute; &agrave; ce r&ocirc;le', 'evarisk');
				}
				$lignesDeValeurs[$i][] = array('value' => $roleCapabilities, 'class' => 'digirisk_user_role_cell_caps_details');
				$userRoleAction = '';
				if(current_user_can('digi_delete_user_role') && array_key_exists($elementKey, $digi_role))
				{
					$userRoleAction .= '<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'delete_vs.png" alt="' . __('Supprimer ce r&ocirc;le', 'evarisk') . '" title="' . __('Supprimer ce r&ocirc;le', 'evarisk') . '" class="alignright deleteRole" />';
				}
				if(current_user_can('digi_edit_user_role'))
				{
					$userRoleAction .= '<a href="' . admin_url('users.php?page=' . DIGI_URL_SLUG_USER_RIGHT . "&amp;action=edit&amp;role=" . $elementKey) . '" ><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'edit_vs.png" alt="' . __('&Eacute;diter ce r&ocirc;le', 'evarisk') . '" title="' . __('&Eacute;diter ce r&ocirc;le', 'evarisk') . '" class="alignright editRole" /></a>';
				}
				$lignesDeValeurs[$i][] = array('value' => $userRoleAction, 'class' => 'digirisk_user_role_cell_action');
				$i++;
			}
		}
		else
		{
			/*	Define the line id when no result is found	*/
			$idLignes[] = 'no_users_groups';

			/*	Define the line content when no result is found	*/
			$lignesDeValeurs[$i][] = array('value' => __('Aucun r&ocirc;le n\'a &eacute;t&eacute; trouv&eacute;', 'evarisk'), 'class' => '');
			$lignesDeValeurs[$i][] = array('value' => '', 'class' => '');
			$lignesDeValeurs[$i][] = array('value' => '', 'class' => '');
		}

		/*	Transform the html table into a "datatable" (jqueyr plugin) table	*/
		/*	For option adding see jqueyr datatable documentation	*/
		$script = '
<script type="text/javascript">
	evarisk(document).ready(function(){
		evarisk("#' . $idTable . ' tfoot").remove();
		evarisk("#' . $idTable . '").dataTable({
			"bInfo": false,
			"bLengthChange": false,
			"oLanguage":{
				"sUrl": "' . EVA_INC_PLUGIN_URL . 'js/dataTable/jquery.dataTables.common_translation.txt"
			}
		});
		evarisk(".deleteRole").click(function(){
			if(confirm(convertAccentToJS("' . __('&Ecirc;tes vous s&ucirc;r de vouloir supprimer ce r&ocirc;le?', 'evarisk') . '"))){
				var clickedId = evarisk(this).parent("td").parent("tr").attr("id").replace("digirisk_users_roles_", "");
				evarisk("#' . self::dbTable . '_delete_form_id").val(clickedId);
				evarisk("#' . self::dbTable . '_delete_form").submit();
			}
		});
	});
</script>';

		/*	Call the table display function	*/
		$elementOutputTable = EvaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script);

		return $elementOutputTable;
	}
	/**
	*	Get informations about an element into database
	*
	*	@param integer $id optionnal The identifier of the element we want to get
	*	@param string $status optionnal Allows to define if we want to get the entire list of element or just element that have a specific status
	*	@param string $type optionnal The type of the element we want to get
	*
	*	@return object|array A wordpress object with the element informations on case that the request works fine. In the other case return an empty array
	*/
	function getElement($selectedRole = '')
	{
		global $digi_wp_role;
		$roles = '';

		/*	Récupére la liste des rôles existant	*/
		$roles = $digi_wp_role->roles;

		/*	Si on a sélectionné un role en particulier alors on returne uniquement ce role	*/
		if($selectedRole != '')
		{
			$roles = $roles[$selectedRole];
		}

		return $roles;
	}
	/**
	*	Return the different button to save the item currently being added or edited
	*
	*	@return string $currentPageButton The html output code with the different button to add to the interface
	*/
	function getPageFormButton()
	{
		global $digi_role;

		$action = isset($_REQUEST['action']) ? eva_tools::IsValid_Variable($_REQUEST['action']) : 'add';
		$role = isset($_REQUEST['role']) ? eva_tools::IsValid_Variable($_REQUEST['role']) : '';
		$currentPageButton = '';

		if(($action == 'add') && current_user_can('digi_add_user_role'))
		{
			$currentPageButton .= '<input type="submit" class="button-primary" id="add" name="add" value="' . __('Ajouter', 'evarisk') . '" />';
		}
		elseif(current_user_can('digi_edit_user_group'))
		{
			$currentPageButton .= '<input type="submit" class="button-primary" id="save" name="save" value="' . __('Enregistrer', 'evarisk') . '" />';
			//<input type="button" class="button-primary" id="saveandcontinue" name="saveandcontinue" value="' . __('Enregistrer et continuer l\'&eacute;dition', 'evarisk') . '" />';
		}
		if(($action != 'add') && current_user_can('digi_delete_user_role') && array_key_exists($role, $digi_role))
		{
			$currentPageButton .= '<input type="button" class="button-primary" id="delete" name="delete" value="' . __('Supprimer', 'evarisk') . '" />';
		}

		$currentPageButton .= '<h2 class="alignright cancelButton" ><a href="' . admin_url('users.php?page=' . DIGI_URL_SLUG_USER_RIGHT) . '" class="button add-new-h2" >' . __('Retour', 'evarisk') . '</a></h2>';

		return $currentPageButton;
	}
	/**
	*	Define the form to output for the element
	*
	*	@param array $elementInformations The different informations about the element to edit, stored into an array
	*
	*	@return string $elementEditionOutput An html output with the complete edition form for the current element
	*/
	function elementEdition($elementInformations = '', $currentElementId)
	{
		global $digi_wp_role;
		global $digi_role;

		$elementEditionOutput = '';
		$dbFieldToHide = array('creation_user_id', 'deletion_user_id', 'deletion_date', 'creation_date', 'last_update_date', 'role_internal_name', 'status');
		$action = isset($_REQUEST['action']) ? eva_tools::IsValid_Variable($_REQUEST['action']) : 'add';

		$the_form_content_hidden = $the_form_general_content = '';
		if($action == 'add')
		{
			$dbFieldList = eva_database::fields_to_input(self::dbTable);
			foreach($dbFieldList as $input_key => $input_def)
			{
				if(!in_array($input_def['name'], $dbFieldToHide))
				{
					if(($currentElementId == '') || array_key_exists($currentElementId, $digi_role))
					{
						$requestFormValue = isset($_REQUEST[self::dbTable][$input_def['name']]) ? eva_tools::IsValid_Variable($_REQUEST[self::dbTable][$input_def['name']]) : '';
						$currentFieldValue = $input_def['value'];
						if(is_array($elementInformations))
						{
							$currentFieldValue = $elementInformations['name'];
						}
						elseif(($action != '') && ($requestFormValue != ''))
						{
							$currentFieldValue = $requestFormValue;
						}

						if(array_key_exists($currentElementId, $digi_role))
						{
							if($input_def['name'] == 'id')
							{
								$currentFieldValue = $currentElementId;
							}
							elseif($input_def['name'] == 'role_name')
							{
								
							}
						}

						$input_def['value'] = $currentFieldValue;
						$the_input = digirisk_form::check_input_type($input_def, self::dbTable);

						if($input_def['type'] != 'hidden')
						{
							$label = 'for="' . $input_def['name'] . '"';
							if(($input_def['type'] == 'radio') || ($input_def['type'] == 'checkbox'))
							{
								$label = '';
							}
							$input = '
					<div class="clear" >
						<div class="digirisk_form_label digirisk_attr_' . $input_def['name'] . '_label alignleft" >
							<label ' . $label . ' >' . __($input_def['name'], 'evarisk') . '</label>
						</div>
						<div class="digirisk_form_input digirisk_attr_' . $input_def['name'] . '_input alignleft" >
							' . $the_input . '
						</div>
					</div>';
							$the_form_general_content .= $input;
						}
						else
						{
							$the_form_content_hidden .= '
				' . $the_input;
						}
					}
				}
			}
			$the_form_general_content .= '
					<div class="clear" >
						<div class="digirisk_form_label digirisk_attr_role_to_copy_from_label alignleft" >
							<label for="role_to_copy_from" >
								' . __('Cr&eacute;er le r&ocirc;le &agrave; partir d\'un r&ocirc;le existant', 'evarisk') . '
								<div class="digi_permission_explanation" >' . __('Si vous choisissez un r&ocirc;le &agrave; copier, les droits de ce r&ocirc;le seront automatiquement coch&eacute; dans le prochain &eacute;cran', 'evarisk') . '</div>
							</label>
						</div>
						<div class="digirisk_form_input digirisk_attr_role_to_copy_from_input alignleft" >
							<select name="roleToCopy" id="role_to_copy_from" >
								<option value="norole" >' . __('Cr&eacute;er un r&ocirc;le vierge', 'evarisk') . '</option>';
			foreach($digi_wp_role->roles as $roleKey => $roleContent)
			{
				$the_form_general_content .= '
								<option value="' . $roleKey . '" >' . translate_user_role($roleContent['name']) . '</option>';
			}
				$the_form_general_content .= '
							</select>
						</div>
					</div>';
		}
		else
		{
			$the_form_content_hidden .= '<input type="hidden" name="wp_eva__permission_role[id]" id="wp_eva__permission_role_id" value="' . $currentElementId . '" />';
		}

		/*	Récupération des droits affectés au role en cours d'édition	*/
		$digiPermissionForm = '';
		if($currentElementId != '')
		{
			$roleInEdition = $digi_wp_role->get_role($currentElementId);
			/*	Récupération du code permettant d'afficher la liste des droits disponible pour le logiciel digirisk	*/
			ob_start();
			self::permission_management($roleInEdition);
			$digiPermissionForm = ob_get_contents();
			ob_end_clean();
			$digiPermissionForm = '
	<fieldset class="clear digiriskUserRoleCapabilitiesDetails" >
		<legend>' . __('Permissions du r&ocirc;le', 'evarisk') . '</legend>
		' . $digiPermissionForm . '
	</fieldset>';
		}

		$elementEditionOutput = '
<form action="" method="post" id="' . self::dbTable . '_form" >
	<div id="pageHeaderButtonContainer" class="pageHeaderButton" >' . self::getPageFormButton() . '</div>
	<input type="hidden" name="' . self::dbTable . '_action" id="' . self::dbTable . '_action" value="' . $action . '" /> 
	' . $the_form_content_hidden . '
	' . $the_form_general_content . '
	' . $digiPermissionForm . '
</form>
<script type="text/javascript" >
	evarisk(document).ready(function(){
		evarisk("#delete").click(function(){
			if(confirm(convertAccentToJS("' . __('&Ecirc;tes vous s&ucirc;r de vouloir supprimer ce r&ocirc;le ?', 'evarisk') . '"))){
				evarisk("#' . self::dbTable . '_action").val("delete");
				evarisk("#' . self::dbTable . '_form").submit();
			}
		});
	});
</script>';

		return $elementEditionOutput;
	}



	/**
	*	Allows to get the role list added for digirisk
	*
	*	@return object $digiriskRoleList A wordpress database object with the existing role list
	*/
	function digirisk_get_role($id = '', $field = '')
	{
		global $wpdb;

		$moreQuery = "";
		if(($id != '') && ($field != ''))
		{
			$moreQuery .= "
			AND " . $field . " = '" . $id . "' ";
		}

		$query = $wpdb->prepare(
		"SELECT * FROM 
		" . DIGI_DBT_PERMISSION_ROLE . "
		WHERE status = 'valid' " . $moreQuery);

		$digiriskRoleList = $wpdb->get_results($query);

		return $digiriskRoleList;
	}


	/**
	*	Update user right's. Check if there is a user id send by post method, if it is the case so we launch user rights' update process
	*
	*/
	function user_permission_set()
	{
		/*	Vérification qu'il existe bien un utilisateur à mettre à jour avant d'effectuer une action	*/
		if ( ! $_POST['user_id'] ) return;
		/*	Récupération des informations concernant l'utilisateur en cours d'édition	*/
		$user = new WP_User($_POST['user_id']);

		/*	Récupération des permissions envoyées	*/
		$userCapsList = $_POST['digi_permission'];

		/*	Récupération des permissions existantes	*/
		$existingPermission = self::digirisk_get_permission();
		foreach($existingPermission as $permission)
		{
			/*	Vérification de la permission actuelle au cas ou elle serait nulle	*/
			if($permission->permission != '')
			{
				/*	Si l'utilisateur possède une permission mais que celle ci n'est plus cochée => Suppression de la permission	*/
				if( $user->has_cap($permission->permission) && ((!array_key_exists($permission->permission, $userCapsList)) || (isset($userCapsList[$permission->permission]) && ($userCapsList[$permission->permission] != 'yes'))) )
				{
					$user->remove_cap($permission->permission);
				}
				/*	Si l'utilisateur ne possède pas la permission mais que celle ci est cochée  => Ajout de la permission	*/
				elseif( !$user->has_cap($permission->permission) && ($userCapsList[$permission->permission] == 'yes'))
				{
					$user->add_cap($permission->permission);
				}
			}
		}
	}


	/**
	*	Create the permission database table
	*/
	function create_permission_db()
	{
		global $wpdb;
		$query = 
			"CREATE TABLE IF NOT EXISTS " . DIGI_DBT_PERMISSION . " (
				id int(10) unsigned NOT NULL auto_increment,
				status enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
				creation_date datetime ,
				last_update_date datetime ,
				set_by_default enum('yes', 'no') collate utf8_unicode_ci NOT NULL default 'no' ,
				permission_type enum('read', 'write', 'delete') collate utf8_unicode_ci NOT NULL default 'read',
				permission_sub_type varchar(64) collate utf8_unicode_ci NOT NULL,
				permission_module varchar(64) collate utf8_unicode_ci NOT NULL ,
				permission_sub_module varchar(64) collate utf8_unicode_ci NOT NULL ,
				permission varchar(64) collate utf8_unicode_ci NOT NULL ,
				PRIMARY KEY (id),
				KEY status (status),
				KEY permission_type (permission_type),
				UNIQUE permission_unique_key (permission_module, permission)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Define the different permissions available'; ";
		$wpdb->query($query);

		digirisk_permission::insert_permission();
	}

	/**
	*	Insert the different permission into the permission table
	*/
	function insert_permission()
	{
		global $wpdb;

		$query = $wpdb->prepare(
				"INSERT INTO " . DIGI_DBT_PERMISSION . " (id, status, creation_date, set_by_default, permission_type, permission_sub_type, permission_module, permission_sub_module, permission)
					VALUES 
				('', 'valid', NOW(), 'no', 'read', '', 'dashboard', 'menu', 'digi_view_dashboard_menu'),
				('', 'valid', NOW(), 'no', 'read', '', 'recommandation', 'menu', 'digi_view_recommandation_menu'),
				('', 'valid', NOW(), 'no', 'read', '', 'method', 'menu', 'digi_view_method_menu'),
				('', 'valid', NOW(), 'no', 'read', '', 'danger', 'menu', 'digi_view_danger_menu'),
				('', 'valid', NOW(), 'no', 'read', '', 'evaluation', 'menu', 'digi_view_evaluation_menu'),
				('', 'valid', NOW(), 'no', 'read', '', 'correctiv_action', 'menu', 'digi_view_correctiv_action_menu'),
				('', 'valid', NOW(), 'no', 'read', '', 'user', 'menu', 'digi_view_user_groups_menu'),
				('', 'valid', NOW(), 'no', 'read', '', 'user', 'menu', 'digi_view_user_import_menu'),
				('', 'valid', NOW(), 'no', 'read', '', 'user', 'menu', 'digi_user_right_management_menu'),
				('', 'valid', NOW(), 'no', 'read', '', 'option', 'menu', 'digi_view_options_menu'),
				('', 'moderated', NOW(), 'no', 'read', '', 'referential', 'menu', 'digi_view_regulatory_monitoring_menu'),

				('', 'valid', NOW(), 'no', 'read', '', 'user', 'user', 'digi_view_user_group'),
				('', 'valid', NOW(), 'no', 'write', 'add', 'user', 'user', 'digi_add_user_group'),
				('', 'valid', NOW(), 'no', 'write', 'edit', 'user', 'user', 'digi_edit_user_group'),
				('', 'valid', NOW(), 'no', 'read', '', 'user', 'user', 'digi_view_detail_user_group'),
				('', 'valid', NOW(), 'no', 'delete', '', 'user', 'user', 'digi_delete_user_group'),

				('', 'valid', NOW(), 'no', 'read', '', 'user', 'evaluator', 'digi_view_evaluator_group'),
				('', 'valid', NOW(), 'no', 'write', 'add', 'user', 'evaluator', 'digi_add_evaluator_group'),
				('', 'valid', NOW(), 'no', 'write', 'edit', 'user', 'evaluator', 'digi_edit_evaluator_group'),
				('', 'valid', NOW(), 'no', 'read', '', 'user', 'evaluator', 'digi_view_detail_evaluator_group'),
				('', 'valid', NOW(), 'no', 'delete', '', 'user', 'evaluator', 'digi_delete_evaluator_group'),

				('', 'valid', NOW(), 'no', 'write', 'add', 'user', 'role', 'digi_add_user_role'),
				('', 'valid', NOW(), 'no', 'write', 'edit', 'user', 'role', 'digi_edit_user_role'),
				('', 'valid', NOW(), 'no', 'read', 'detail', 'user', 'role', 'digi_view_detail_user_role'),
				('', 'valid', NOW(), 'no', 'delete', '', 'user', 'role', 'digi_delete_user_role'),

				('', 'valid', NOW(), 'no', 'write', 'edit', 'user', 'right', 'digi_manage_user_right'),

				('', 'moderated', NOW(), 'no', 'write', 'add', 'recommandation', 'category', 'digi_add_recommandation_cat'),
				('', 'valid', NOW(), 'no', 'read', 'detail', 'recommandation', 'category', 'digi_view_detail_recommandation_cat'),
				('', 'valid', NOW(), 'no', 'write', 'edit', 'recommandation', 'category', 'digi_edit_recommandation_cat'),
				('', 'valid', NOW(), 'no', 'delete', '', 'recommandation', 'category', 'digi_delete_recommandation_cat'),
				('', 'valid', NOW(), 'no', 'write', 'add', 'recommandation', '', 'digi_add_recommandation'),
				('', 'valid', NOW(), 'no', 'read', 'detail', 'recommandation', '', 'digi_view_detail_recommandation'),
				('', 'valid', NOW(), 'no', 'write', 'edit', 'recommandation', '', 'digi_edit_recommandation'),
				('', 'valid', NOW(), 'no', 'delete', '', 'recommandation', '', 'digi_delete_recommandation'),

				('', 'valid', NOW(), 'no', 'write', 'add', 'method', '', 'digi_add_method'),
				('', 'valid', NOW(), 'no', 'write', 'edit', 'method', '', 'digi_edit_method'),
				('', 'valid', NOW(), 'no', 'read', 'detail', 'method', '', 'digi_view_detail_method'),
				('', 'valid', NOW(), 'no', 'delete', '', 'method', '', 'digi_delete_method'),
				('', 'valid', NOW(), 'no', 'write', 'add', 'method', 'vars', 'digi_add_method_var'),

				('', 'valid', NOW(), 'no', 'write', 'add', 'danger', 'category', 'digi_add_danger_category'),
				('', 'valid', NOW(), 'no', 'write', 'edit', 'danger', 'category', 'digi_edit_danger_category'),
				('', 'valid', NOW(), 'no', 'write', 'move', 'danger', 'category', 'digi_move_danger_category'),
				('', 'valid', NOW(), 'no', 'read', 'detail', 'danger', 'category', 'digi_view_detail_danger_category'),
				('', 'valid', NOW(), 'no', 'delete', '', 'danger', 'category', 'digi_delete_danger_category'),
				('', 'valid', NOW(), 'no', 'write', 'add', 'danger', '', 'digi_add_danger'),
				('', 'valid', NOW(), 'no', 'write', 'edit', 'danger', '', 'digi_edit_danger'),
				('', 'valid', NOW(), 'no', 'write', 'move', 'danger', '', 'digi_move_danger'),
				('', 'valid', NOW(), 'no', 'read', 'detail', 'danger', '', 'digi_view_detail_danger'),
				('', 'valid', NOW(), 'no', 'delete', '', 'danger', '', 'digi_delete_danger'),

				('', 'valid', NOW(), 'no', 'write', 'edit', 'option', '', 'digi_edit_option'),

				('', 'valid', NOW(), 'no', 'write', 'add', 'arborescence', 'groupement', 'digi_add_groupement'),
				('', 'valid', NOW(), 'no', 'write', 'edit', 'arborescence', 'groupement', 'digi_edit_groupement'),
				('', 'valid', NOW(), 'no', 'write', 'move', 'arborescence', 'groupement', 'digi_move_groupement'),
				('', 'valid', NOW(), 'no', 'read', 'detail', 'arborescence', 'groupement', 'digi_view_detail_groupement'),
				('', 'valid', NOW(), 'no', 'delete', '', 'arborescence', 'groupement', 'digi_delete_groupement'),

				('', 'valid', NOW(), 'no', 'write', 'add', 'arborescence', 'unite', 'digi_add_unite'),
				('', 'valid', NOW(), 'no', 'write', 'edit', 'arborescence', 'unite', 'digi_edit_unite'),
				('', 'valid', NOW(), 'no', 'wrtie', 'move', 'arborescence', 'unite', 'digi_move_unite'),
				('', 'valid', NOW(), 'no', 'read', 'detail', 'arborescence', 'unite', 'digi_view_detail_unite'),
				('', 'valid', NOW(), 'no', 'delete', '', 'arborescence', 'unite', 'digi_delete_unite')");

		$wpdb->query($query);	
	}


	/**
	*
	*/
	function userRightPostBox($arguments, $moreArgs = '')
	{
		$idElement = $arguments['idElement'];
		$tableElement = $arguments['tableElement'];
		$utilisateursMetaBox = '<div class="hide" id="message_' . $tableElement . '_' . $idElement . '_userRight" ></div>';
		$idBoutonEnregistrer = 'save_right_' . $tableElement;
		$userRightDetail_edit_old = $userRightDetail_delete_old = $userRightDetail_see_old = $userRightDetail_add_gpt_old = $userRightDetail_add_unit_old = '';

		$idTable = 'listeIndividusPourDroits' . $tableElement . $idElement;
		unset($titres);
		$titres[] = __('Affect&eacute; &agrave; l\'&eacute;l&eacute;ment', 'evarisk');
		$titres[] = ucfirst(strtolower(__('Nom', 'evarisk')));
		$titres[] = ucfirst(strtolower(__('Pr&eacute;nom', 'evarisk')));
		$titres[] = __('Voir', 'evarisk');
		$titres[] = __('&Eacute;diter', 'evarisk');
		$titres[] = __('Supprimer', 'evarisk');
		switch($tableElement)
		{
			case TABLE_GROUPEMENT;
				/*	Add button for groupement or unit adding	*/
				$titres[] = __('Ajouter un groupement', 'evarisk');
				$titres[] = __('Ajouter une unit&eacute;', 'evarisk');
			break;
		}
		unset($lignesDeValeurs);

		//on récupère les utilisateurs déjà affectés à l'élément en cours.
		$listeUtilisateursLies = array();
		$utilisateursLies = evaUserLinkElement::getAffectedUser($tableElement, $idElement);
		if(is_array($utilisateursLies) && (count($utilisateursLies) > 0))
		{
			foreach($utilisateursLies as $utilisateur)
			{
				$listeUtilisateursLies[$utilisateur->id_user] = $utilisateur;
			}
		}

		$listeUtilisateurs = evaUser::getCompleteUserList();
		if(is_array($listeUtilisateurs) && (count($listeUtilisateurs) > 0))
		{
			foreach($listeUtilisateurs as $utilisateur)
			{
				$user = new WP_User($utilisateur['user_id']);

				unset($valeurs);
				$idLigne = $tableElement . $idElement . 'listeUtilisateurs' . $utilisateur['user_id'];
				$idCbLigne = 'cb_' . $idLigne;

				$utilisateurAffecteClass = '';
				$utilisateurAffecte = ucfirst(strtolower(__('non', 'evarisk')));
				if(array_key_exists($utilisateur['user_id'], $listeUtilisateursLies))
				{
					$utilisateurAffecteClass = 'userAffecte';
					$utilisateurAffecte = ucfirst(strtolower(__('oui', 'evarisk')));
				}

				$valeurs[] = array('value'=>$utilisateurAffecte, 'class'=>$utilisateurAffecteClass);
				$valeurs[] = array('value'=>$utilisateur['user_lastname'], 'class'=>$utilisateurAffecteClass);
				$valeurs[] = array('value'=>$utilisateur['user_firstname'], 'class'=>$utilisateurAffecteClass);
				switch($tableElement)
				{
					case TABLE_GROUPEMENT;
						$endPermission = 'groupement';
					break;
					case TABLE_UNITE_TRAVAIL;
						$endPermission = 'unite';
					break;
					default:
						$endPermission = '';
					break;
				}
				$viewCheckBox = '';
				if($user->has_cap('digi_view_detail_' . $endPermission) || $user->has_cap('digi_view_detail_' . $endPermission . '_' . $idElement))
				{
					$viewCheckBox = ' checked="checked" ';
					$userRightDetail_see_old .= 'digi_view_detail_' . $endPermission . '_' . $idElement . '!#!' . $utilisateur['user_id'] . '#!#';
				}
				$valeurs[] = array('class'=>'rightCell ' . $utilisateurAffecteClass, 'value'=>'<input type="checkbox" name="user_see[' . $tableElement . '_' . $idElement . '_' . $utilisateur['user_id'] . ']" value="digi_view_detail_' . $endPermission . '_' . $idElement . '" id="user_see_' . $utilisateur['user_id'] . '" class="see" ' . $viewCheckBox . ' />');
				$editCheckBox = '';
				if($user->has_cap('digi_edit_' . $endPermission) || $user->has_cap('digi_edit_' . $endPermission . '_' . $idElement))
				{
					$editCheckBox = ' checked="checked" ';
					$userRightDetail_edit_old .= 'digi_edit_' . $endPermission . '_' . $idElement . '!#!' . $utilisateur['user_id'] . '#!#';
				}
				$valeurs[] = array('class'=>'rightCell ' . $utilisateurAffecteClass, 'value'=>'<input type="checkbox" name="user_edit[' . $tableElement . '_' . $idElement . '_' . $utilisateur['user_id'] . ']" value="digi_edit_' . $endPermission . '_' . $idElement . '" id="user_edit_' . $utilisateur['user_id'] . '" class="edit" ' . $editCheckBox . ' />');
				$deleteCheckBox = '';
				if($user->has_cap('digi_delete_' . $endPermission) || $user->has_cap('digi_delete_' . $endPermission . '_' . $idElement))
				{
					$deleteCheckBox = ' checked="checked" ';
					$userRightDetail_delete_old .= 'digi_delete_' . $endPermission . '_' . $idElement . '!#!' . $utilisateur['user_id'] . '#!#';
				}
				$valeurs[] = array('class'=>'rightCell ' . $utilisateurAffecteClass, 'value'=>'<input type="checkbox" name="user_delete[' . $tableElement . '_' . $idElement . '_' . $utilisateur['user_id'] . ']" value="digi_delete_' . $endPermission . '_' . $idElement . '" id="user_delete_' . $utilisateur['user_id'] . '" class="delete" ' . $deleteCheckBox . ' />');
				switch($tableElement)
				{
					case TABLE_GROUPEMENT;
						/*	Add button for groupement or unit adding	*/
						$viewCheckBox = '';
						if($user->has_cap('digi_add_groupement_' . $endPermission) || $user->has_cap('digi_add_groupement_' . $endPermission . '_' . $idElement))
						{
							$viewCheckBox = ' checked="checked" ';
							$userRightDetail_add_gpt_old .= 'digi_add_groupement_' . $endPermission . '_' . $idElement . '!#!' . $utilisateur['user_id'] . '#!#';
						}
						$valeurs[] = array('class'=>'rightCell ' . $utilisateurAffecteClass, 'value'=>'<input type="checkbox" name="user_add_gpt[' . $tableElement . '_' . $idElement . '_' . $utilisateur['user_id'] . ']" value="digi_add_groupement_' . $endPermission . '_' . $idElement . '" id="user_add_gpt' . $utilisateur['user_id'] . '" class="add_groupement" ' . $viewCheckBox . ' />');
						$viewCheckBox = '';
						if($user->has_cap('digi_add_unite_' . $endPermission) || $user->has_cap('digi_add_unite_' . $endPermission . '_' . $idElement))
						{
							$viewCheckBox = ' checked="checked" ';
							$userRightDetail_add_unit_old .= 'digi_add_unite_' . $endPermission . '_' . $idElement . '!#!' . $utilisateur['user_id'] . '#!#';
						}
						$valeurs[] = array('class'=>'rightCell ' . $utilisateurAffecteClass, 'value'=>'<input type="checkbox" name="user_add_unit[' . $tableElement . '_' . $idElement . '_' . $utilisateur['user_id'] . ']" value="digi_add_unite_' . $endPermission . '_' . $idElement . '" id="user_add_unit' . $utilisateur['user_id'] . '" class="add_unite" ' . $viewCheckBox . ' />');
					break;
				}

				$lignesDeValeurs[] = $valeurs;
				$idLignes[] = $idLigne;
			}
		}
		else
		{
			$valeurs[] = array('value'=>__('Aucun r&eacute;sultat trouv&eacute;', 'evarisk'));
			$valeurs[] = array('value'=>'');
			$valeurs[] = array('value'=>'');
			$valeurs[] = array('value'=>'');
			$valeurs[] = array('value'=>'');
			$lignesDeValeurs[] = $valeurs;
			$idLignes[] = $tableElement . $idElement . 'listeUtilisateursVide';
		}

		$classes = array('','','','rightColumn','rightColumn','rightColumn');
		$script = '
<script type="text/javascript">
	evarisk(document).ready(function(){
		evarisk("#' . $idTable . '").dataTable({
			"bAutoWidth": false,
			"bInfo": false,
			"aaSorting": [[0, "desc"]],
			"sScrollY": "200px",
			"bPaginate": false,
			"aoColumns": [
				{ "bVisible": false},
				null,
				null,
				null,
				null,';
			switch($tableElement)
			{
				case TABLE_GROUPEMENT;
					$script .= '
				null,
				null,';
				$classes[] = 'rightColumn';
				$classes[] = 'rightColumn';
				break;
			}
				$script .= '
				null
			],
			"oLanguage":{
				"sLengthMenu": "' . sprintf(__('Afficher %s enregistrements', 'evarisk'), '_MENU_') . '",
				"sZeroRecords": "' . __('Aucun r&eacute;sultat', 'evarisk') . '",
				"sSearch": "<span class=\'ui-icon searchDataTableIcon\' >&nbsp;</span>"
			}
		});
	});
</script>';

		$utilisateursMetaBox .= __('L&eacute;gende', 'evarisk') . '&nbsp;:&nbsp;<div class="userAffecte userAffecteExplanation" >' . __('Utilisateurs affect&eacute;s &agrave; l\'&eacute;l&eacute;ment en cours d\'&eacute;dition', 'evarisk') . '</div>';

		$utilisateursMetaBox .= '
		<input type="hidden" name="userRightDetail_see_old" id="userRightDetail_see_old" value="' . $userRightDetail_see_old . '" /><input type="hidden" name="userRightDetail_delete_old" id="userRightDetail_delete_old" value="' . $userRightDetail_delete_old . '" /><input type="hidden" name="userRightDetail_edit_old" id="userRightDetail_edit_old" value="' . $userRightDetail_edit_old . '" /><input type="hidden" name="userRightDetail_add_gpt_old" id="userRightDetail_add_gpt_old" value="' . $userRightDetail_add_gpt_old . '" /><input type="hidden" name="userRightDetail_add_unit_old" id="userRightDetail_add_unit_old" value="' . $userRightDetail_add_unit_old . '" />
		<input type="hidden" name="userRightDetail_see" id="userRightDetail_see" value="" /><input type="hidden" name="userRightDetail_delete" id="userRightDetail_delete" value="" /><input type="hidden" name="userRightDetail_edit" id="userRightDetail_edit" value="" /><input type="hidden" name="userRightDetail_add_gpt" id="userRightDetail_add_gpt" value="" /><input type="hidden" name="userRightDetail_add_unit" id="userRightDetail_add_unit" value="" /><div class="userTableContainer" >' . evaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script) . '</div>';

		$scriptEnregistrement = '
<script type="text/javascript">
	evarisk("#' . $idBoutonEnregistrer . '").click(function(){
		evarisk("#saveButtonLoading_userRight' . $tableElement . '").show();
		evarisk("#saveButtonContainer_userRight' . $tableElement . '").hide();

		evarisk("#userRightDetail_see").val("");
		evarisk(".see").each(function(){
			if(evarisk(this).is(":checked")){
				evarisk("#userRightDetail_see").val( evarisk("#userRightDetail_see").val() + evarisk(this).val() + "!#!" + evarisk(this).attr("id").replace("user_see_", "") + "#!#" );
			}
		});

		evarisk("#userRightDetail_delete").val("");
		evarisk(".delete").each(function(){
			if(evarisk(this).is(":checked")){
				evarisk("#userRightDetail_delete").val( evarisk("#userRightDetail_delete").val() + evarisk(this).val() + "!#!" + evarisk(this).attr("id").replace("user_delete_", "") + "#!#" );
			}
		});

		evarisk("#userRightDetail_edit").val("");
		evarisk(".edit").each(function(){
			if(evarisk(this).is(":checked")){
				evarisk("#userRightDetail_edit").val( evarisk("#userRightDetail_edit").val() + evarisk(this).val() + "!#!" + evarisk(this).attr("id").replace("user_edit_", "") + "#!#" );
			}
		});

		evarisk("#userRightDetail_add_gpt").val("");
		evarisk(".add_groupement").each(function(){
			if(evarisk(this).is(":checked")){
				evarisk("#userRightDetail_add_gpt").val( evarisk("#userRightDetail_add_gpt").val() + evarisk(this).val() + "!#!" + evarisk(this).attr("id").replace("user_add_gpt", "") + "#!#" );
			}
		});

		evarisk("#userRightDetail_add_unit").val("");
		evarisk(".add_unite").each(function(){
			if(evarisk(this).is(":checked")){
				evarisk("#userRightDetail_add_unit").val( evarisk("#userRightDetail_add_unit").val() + evarisk(this).val() + "!#!" + evarisk(this).attr("id").replace("user_add_unit", "") + "#!#" );
			}
		});

		evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
			"table": "' . DIGI_DBT_PERMISSION . '",
			"act": "save",
			"user_see": evarisk("#userRightDetail_see").val(),
			"user_delete": evarisk("#userRightDetail_delete").val(),
			"user_edit": evarisk("#userRightDetail_edit").val(),
			"user_add_gpt": evarisk("#userRightDetail_add_gpt").val(),
			"user_add_unit": evarisk("#userRightDetail_add_unit").val(),

			"user_see_old": evarisk("#userRightDetail_see_old").val(),
			"user_delete_old": evarisk("#userRightDetail_delete_old").val(),
			"user_edit_old": evarisk("#userRightDetail_edit_old").val(),
			"user_add_gpt_old": evarisk("#userRightDetail_add_gpt_old").val(),
			"user_add_unit_old": evarisk("#userRightDetail_add_unit_old").val(),

			"tableElement": "' . $tableElement . '",
			"idElement": "' . $idElement . '"
		});

	});
</script>';

		$utilisateursMetaBox .= '<div class="clear" ><div id="saveButtonLoading_userRight' . $tableElement . '" style="display:none;" class="clear alignright" ><img src="' . PICTO_LOADING_ROUND . '" alt="loading in progress" /></div><div id="saveButtonContainer_userRight' . $tableElement . '" >' . EvaDisplayInput::afficherInput('button', $idBoutonEnregistrer, __('Enregistrer', 'evarisk'), null, '', 'save', false, true, '', 'button-primary alignright', '', '', $scriptEnregistrement) . '</div></div>';

		echo '<div id="userRightContainerBox" >' . $utilisateursMetaBox . '</div>';
	}


	/**
	*	Define the permission that was create at the plugin beginning. From version 5.1.3.1 is used for delete existing right
	*	@deprecated deprecated since version 5.1.3.1
	*
	*	@return array The different right previously added by the plugin (before version 5.1.3.1)
	*/
	function getDroitEvarisk()
	{
		return array(
			'Evarisk_:_utiliser_le_plugin' => __('utiliser le plugin','evarisk'),
			'Evarisk_:_voir_les_groupements' => sprintf(__('voir %s','evarisk'), __('les groupements','evarisk')),
			'Evarisk_:_voir_son_groupement' => sprintf(__('voir %s','evarisk'), __('son groupement','evarisk')),
			'Evarisk_:_voir_les_unites' => sprintf(__('voir %s','evarisk'), __('les unit&eacute;s de travail','evarisk')),
			'Evarisk_:_voir_son_unite' => sprintf(__('voir %s','evarisk'), __('son unit&eacute; de travail','evarisk')),
			'Evarisk_:_voir_les_dangers' => sprintf(__('voir %s','evarisk'), __('les dangers','evarisk')),
			'Evarisk_:_voir_les_methodes' => sprintf(__('voir %s','evarisk'), __('les m&eacute;thodes d\'&eacute;valuation','evarisk')),
			'Evarisk_:_voir_les_risques' =>sprintf(__('voir %s','evarisk'), __('les risques','evarisk')),
			'Evarisk_:_voir_les_veilles' => sprintf(__('voir %s','evarisk'), __('les veilles','evarisk')),
			'Evarisk_:_voir_les_actions' => sprintf(__('voir %s','evarisk'), __('les actions correctives','evarisk')),
			'Evarisk_:_editer_les_groupements' => sprintf(__('&eacute;diter %s','evarisk'), __('les groupements','evarisk')),
			'Evarisk_:_editer_les_unites' => sprintf(__('&eacute;diter %s','evarisk'), __('les unit&eacute;s de travail','evarisk')),
			'Evarisk_:_editer_les_dangers' => sprintf(__('&eacute;diter %s','evarisk'), __('les dangers','evarisk')),
			'Evarisk_:_editer_les_methodes' => sprintf(__('&eacute;diter %s','evarisk'), __('les methodes','evarisk')),
			'Evarisk_:_editer_les_risques' => sprintf(__('&eacute;diter %s','evarisk'), __('les risques','evarisk')),
			'Evarisk_:_editer_les_veilles' => sprintf(__('&eacute;diter %s','evarisk'), __('les veilles','evarisk')),
			'Evarisk_:_creer_referenciel' => sprintf(__('cr&eacute;er %s','evarisk'), __('des r&eacute;f&eacute;renciels','evarisk')),
			'Evarisk_:_gerer_attributs' => sprintf(__('g&eacute;rer %s','evarisk'), __('les attributs','evarisk')),
			'Evarisk_:_gerer_groupes_utilisateurs' => sprintf(__('g&eacute;rer %s','evarisk'), __('les groupes d\'utilisateurs','evarisk')),
			'Evarisk_:_gerer_droit_d_acces' => sprintf(__('g&eacute;rer %s','evarisk'), __('les droits d\'acc&egrave;s','evarisk')),
			'Evarisk_:_gerer_groupes_evaluateurs' => sprintf(__('g&eacute;rer %s','evarisk'), __('les groupes d\'&eacute;valuateurs','evarisk')),
			'Evarisk_:_gerer_utilisateurs' => sprintf(__('g&eacute;rer %s','evarisk'), __('les utilisateurs','evarisk')),
			'Evarisk_:_editer_les_options' => sprintf(__('g&eacute;rer %s','evarisk'), __('les options','evarisk')),
			'Evarisk_:_voir_les_preconisations' => sprintf(__('voir %s','evarisk'), __('les pr&eacute;conisations','evarisk'))
		);
	}

}