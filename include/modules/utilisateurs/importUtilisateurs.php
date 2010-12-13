<?php
	$separatorExample = '<span class="fieldSeparator" >[fieldSeparator]</span>';

	$importAction = isset($_POST['act']) ? eva_tools::IsValid_Variable($_POST['act']) : '';
	$userRoles = isset($_POST['userRoles']) ? eva_tools::IsValid_Variable($_POST['userRoles']) : '';
	$fieldSeparator = isset($_POST['fieldSeparator']) ? eva_tools::IsValid_Variable($_POST['fieldSeparator']) : '';
	$sendUserMail = isset($_POST['sendUserMail']) ? eva_tools::IsValid_Variable($_POST['sendUserMail']) : '';

	if($importAction != '')
	{

		$userToCreate = array();
		$importResult = '';

		/*	Check if there are lines to create without sending a file	*/
		$userLinesToCreate = isset($_POST['userLinesToCreate']) ? (string) eva_tools::IsValid_Variable($_POST['userLinesToCreate']) : '';
		if($userLinesToCreate != '')
		{
			$userToCreate = array_merge($userToCreate, explode("\n", trim($userLinesToCreate)));
		}
		else
		{
			$importResult .= __('Aucun utilisateurs n\'a &eacute;t&eacute; ajout&eacute; depuis le champs texte', 'evarisk') . '<br/>';
		}

		/*	Check if a file has been sending */
		if($_FILES['userFileToCreate']['error'] != UPLOAD_ERR_NO_FILE)
		{
			$file = $_FILES['userFileToCreate'];
			if($file['error'])
			{
				switch ($file['error']){
					case UPLOAD_ERR_INI_SIZE:
						$subFileError .= sprintf(__('Le fichier que vous avez envoy&eacute; est trop lourd: %s taille autoris&eacute;e %s', 'evarisk'), $file['size'], upload_max_filesize);
					break;
					case UPLOAD_ERR_FORM_SIZE:
						$subFileError .= sprintf(__('Le fichier que vous avez envoy&eacute; est trop lourd: %s taille autoris&eacute;e %s', 'evarisk'), $file['size'], upload_max_filesize);
					break;
					case UPLOAD_ERR_PARTIAL:
						$subFileError .= __('Le fichier que vous avez envoy&eacute; n\'a pas &eacute;t&eacute; compl&eacute;tement envoy&eacute;', 'evarisk');
					break;
				}
				$importResult .= '<h4 style="color:#FF0000;">' . __('Une erreur est survenue lors de l\'envoie du fichier', 'evarisk') . '</h4><p>' . $subFileError . '</p>';
			}
			elseif(!is_uploaded_file($file['tmp_name']))
			{
				$importResult .= sprintf(__('Le fichier %s n\'a pas pu &ecirc;tre envoy&eacute;', 'evarisk'), $file['name']);
			}
			else
			{
				$userToCreate = array_merge($userToCreate, file($file['tmp_name']));
			}
		}
		else
		{
			// $importResult .= __('Aucun fichier n\'a &eacute;t&eacute; envoy&eacute;', 'evarisk') . '<br/>';
		}

		if(is_array($userToCreate) && (count($userToCreate) > 0))
		{
			$createdUserNumber = 0;
			$errors = array();

			foreach($userToCreate as $userInfos) 
			{
				$userInfosComponent = array();
				if (trim($userInfos) != '') 
				{
					$userInfosComponent = explode($fieldSeparator, $userInfos);
					$userInfosComponent[0] = trim($userInfosComponent[0]);
					$userInfosComponent[1] = trim($userInfosComponent[1]);
					$userInfosComponent[2] = trim($userInfosComponent[2]);
					$userInfosComponent[3] = trim($userInfosComponent[3]);
					$userInfosComponent[4] = trim($userInfosComponent[4]);
					$userInfosComponent[5] = trim($userInfosComponent[5]);
					$checkErrors = 0;

					/*	Check if the email adress is valid or already exist	*/
					if(!is_email($userInfosComponent[4]))
					{
						$errors[] = sprintf(__('L\'adresse email <b>' . $userInfosComponent[4] . '</b> de la ligne %s n\'est <b>pas valide</b>', 'evarisk'), $userInfos);
						$checkErrors++;
					}
					$checkIfMailExist = $wpdb->get_row("SELECT user_email FROM " . $wpdb->users . " WHERE user_email = '" . mysql_real_escape_string($userInfosComponent[4]) . "'");
					if($checkIfMailExist)
					{
						$errors[] = sprintf(__('L\'adresse email <b>' . $userInfosComponent[4] . '</b> de la ligne %s est <b>d&eacute;j&agrave; utilis&eacute;</b>', 'evarisk'), $userInfos);
						$checkErrors++;
					}

					/*	Check if the username is valid or already exist	*/
					if(!validate_username($userInfosComponent[0]))
					{
						$errors[] = sprintf(__('L\'identifiant <b>' . $userInfosComponent[0] . '</b> de la ligne %s n\'est <b>pas valide</b>', 'evarisk'), $userInfos);
						$checkErrors++;
					}
					if(username_exists($userInfosComponent[0]))
					{
						$errors[] = sprintf(__('L\'identifiant <b>' . $userInfosComponent[0] . '</b> de la ligne %s est <b>d&eacute;j&agrave; utilis&eacute;</b>', 'evarisk'), $userInfos);
						$checkErrors++;
					}

					/*	There are no errors on the email and username so we can create the user	*/
					if($checkErrors == 0)
					{
						/*	Check if the password is given in the list to create, if not we generate one */
						if($userInfosComponent[3] == '')
						{
							$userInfosComponent[3] = substr(md5(uniqid(microtime())), 0, 7);
						}

						/*	Start creating the user	*/
						$newUserID = 0;
						$newUserID = 
							wp_insert_user(
								array(
										"user_login" => $userInfosComponent[0],
										"first_name" => $userInfosComponent[1],
										"last_name" => $userInfosComponent[2],
										"user_pass" => $userInfosComponent[3],
										"user_email" => $userInfosComponent[4]
									)
							);

						if($newUserID <= 0)
						{
							$errors[] = sprintf(__('L\'utilisateur de la ligne %s n\'a pas pu &ecirc;tre ajout&eacute;', 'evarisk'), $userInfos);
						}
						else
						{
							if($sendUserMail != '')
							{
								wp_new_user_notification($newUserID, $userInfosComponent[3]);
							}
							$createdUserNumber++;

							/*	Affect a role to the new user regarding on the import file or lines and if empty the main roe field	*/
							if ($userInfosComponent[5] == '') 
							{
								$userInfosComponent[5] = $userRoles;
							}
							$userRole = new WP_User($newUserID);
							$userRole->set_role($userInfosComponent[5]);
						}

					}
				}
			}

			if($createdUserNumber >= 1)
			{
				$subResult = sprintf(__('%s utilisateur a &eacute;t&eacute; cr&eacute;&eacute;', 'evarisk'), $createdUserNumber);
				if($createdUserNumber > 1)
				{
					$subResult = sprintf(__('%s utilisateurs ont &eacute;t&eacute; cr&eacute;&eacute;s', 'evarisk'), $createdUserNumber);
				}
				
				$importResult .= '<h4 style="color:#00CC00;">' . __('L\'import s\'est termin&eacute; avec succ&eacute;s. Veuillez trouver le r&eacute;sultat ci-dessous', 'evarisk') . '</h4><ul>' . $subResult . '</ul>';


				if($sendUserMail != '')
				{
					$importResult .= '<div style="font-weight:bold;" >' . __('Les nouveaux utilisateurs recevront leurs mot de passe par email', 'evarisk') . '</div>';
				}
			}
			if(is_array($errors) && (count($errors) > 0))
			{
				$subErrors = '';
				foreach($errors as $er)
				{
					$subErrors .= '<li>' . $er . '</li>';
				}
				$importResult .= '<h4 style="color:#FF0000;">' . __('Des erreurs sont survenues. Veuillez trouver la liste ci-dessous', 'evarisk') . '</h4><ul>' . $subErrors . '</ul>';
			}
		}
?>
	<div style="width:80%;margin:18px auto;padding:6px;border:1px dashed;"  ><?php echo $importResult; ?></div>
<?php
	}

?>
<script type="text/javascript" >
	function changeSeparator()
	{
		$('.fieldSeparator').html($('#fieldSeparator').val());
	}
	$(document).ready(function(){
		changeSeparator();
		$('#fieldSeparator').blur(function(){changeSeparator()});
	});
</script>
<form enctype="multipart/form-data" method="post" action="" >
	<input type="hidden" name="act" id="act" value="1" />

	<!-- 	Start of file specification part	-->
	<h3><?php echo __('Sp&eacute;cifications pour le fichier', 'evarisk'); ?></h3>
	<?php echo __('Vous pouvez d&eacute;finir le s&eacute;parateur de champs', 'evarisk'); ?><input type="text" name="fieldSeparator" id="fieldSeparator" value=";" />
	<br/><br/>
	<?php echo __('Chaque ligne de d&eacute;finition devra respecter le format ci-apr&egrave;s&nbsp;:', 'evarisk'); ?>
	<br/><span style="font-style:italic;" ><?php echo __('Les champs identifiants et email sont obligatoires. Vous n\'&ecirc;tes pas oblig&eacute; de renseigner tous les champs mais tous les s&eacute;parateur doivent &ecirc;tre pr&eacute;sent', 'evarisk'); ?></span>
	<div style="margin:21px;padding:12px;border:1px solid #333333;width:40%;text-align:center;" ><?php echo __('identifiant' . $separatorExample . 'prenom' . $separatorExample . 'nom' . $separatorExample . 'mot de passe' . $separatorExample . 'email' . $separatorExample . 'role', 'evarisk'); ?></div>


	<!-- 	Start of user list to import part	-->
	<h3><?php echo __('Utilisateurs &agrave; importer', 'evarisk'); ?></h3>
	<table>
		<tr>
			<td><?php echo __('Vous pouvez entrer directement le utilisateurs que vous souhaitez cr&eacute;er ici.<br/>Chaques utilisateur sera s&eacute;par&eacute; par un retour &agrave; la ligne', 'evarisk'); ?></td>
			<td rowspan="2" ><?php echo __('-&nbsp;et&nbsp;/&nbsp;ou&nbsp;-', 'evarisk'); ?></td>
			<td><?php echo __('Vous pouvez envoyer un fichier contenant les utilisateurs &agrave; cr&eacute;er (extension autoris&eacute;e *.odt, *.csv, *.txt)', 'evarisk'); ?></td>
		</tr>
		<tr>
			<td><textarea name="userLinesToCreate" cols="70" rows="12"></textarea></td>
			<td style="vertical-align:top;" ><input type="file" id="userFileToCreate" name="userFileToCreate" /></td>
		</tr>
		<tr>
			<td colspan="3" style="padding-top:12px;text-align:center;" ><?php echo __('Si vous n\'avez pas sp&eacute;cifi&eacute; de r&ocirc;le dans la liste vous pouvez choisir le r&ocirc;le attribu&eacute; aux utilisateurs', 'evarisk'); ?></td>
		</tr>
		<tr>
			<td colspan="3" style="font-weight:bold;text-align:center;" >
				<?php echo __('R&ocirc;le pour les utilisateurs', 'evarisk'); ?>
				<select name="userRoles" >
					<?php
						if ( !isset($wp_roles) )
						{
							$wp_roles = new WP_Roles();
						}
						foreach ($wp_roles->get_names() as $role => $roleName)
						{
							$selected = '';
							if(($userRoles == '') && ($role == 'subscriber'))
							{
								$selected = 'selected = "selected"';
							}
							elseif(($userRoles != '') && ($role == $userRoles))
							{
								$selected = 'selected = "selected"';
							}
							echo '<option value="' . $role . '" ' . $selected . ' >' . $roleName . '</option>';
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="text-align:center;" >
				<input type="checkbox" name="sendUserMail" id="sendUserMail" /><?php echo __('Envoyer le mot de passe aux utilisateurs.', 'evarisk'); ?><br/><span style="font-weight:bold;" ><?php echo __('(Attention cette option peut ne pas fonctionner sur certains serveurs)', 'evarisk'); ?></span>
			</td>
		</tr>
	</table>


	<!-- 	Submit form button	-->
	<input type="submit" class="button" name="importSubmit" id="importSubmit" value="<?php echo __('Importer les utilisateurs', 'evarisk'); ?>" />
</form>