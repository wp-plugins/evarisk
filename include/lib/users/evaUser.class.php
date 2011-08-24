<?php
/**
*	The different utilities to manage users in evarisk
*
*	@package 		Digirisk
*	@subpackage users
* @author			Evarisk <dev@evarisk.com>
*/

class evaUser
{
	/**
	*	Set the current attribute group regarding the 
	*/
	protected $_currentAttributeGroup = '';
	/**
	*	Set the current attribute group regarding the 
	*/
	protected $_userToUpdate = '';



	/**
	*	Get the wordpress' user list
	*
	*	@return array $userlist An object containing the different subscriber
	*/
	function getUserList()
	{
		global $wpdb;

		$query = 
			"SELECT USERS.ID
			FROM wp_users AS USERS";
		$userList = $wpdb->get_results($query);

		return $userList;
	}
	/**
	*	Get the wordpress' user list
	*
	*	@return array $userlist An object containing the different subscriber
	*/
	function getCompleteUserList()
	{
		$listeComplete = array();

		$listeUtilisateurs = evaUser::getUserList();
		foreach($listeUtilisateurs as $utilisateurs)
		{
			if($utilisateurs->ID != 1)
			{
				$user_info = get_userdata($utilisateurs->ID);

				unset($valeurs);
				$valeurs['user_id'] = $user_info->ID;
				$valeurs['user_registered'] = $user_info->user_registered;
				if( (isset($user_info->user_lastname) && ($user_info->user_lastname != '')) )
				{
					$valeurs['user_lastname'] = $user_info->user_lastname;
				}
				else
				{
					$valeurs['user_lastname'] = '';
				}
				if( (isset($user_info->user_firstname) && ($user_info->user_firstname != '')) )
				{
					$valeurs['user_firstname'] = $user_info->user_firstname;
				}
				else
				{
					$valeurs['user_firstname'] = $user_info->user_nicename;
				}

				$listeComplete[$user_info->ID] = $valeurs;
			}
		}

		return $listeComplete;
	}
	/**
	*	Get the wordpress' user list
	*
	*	@return array $userlist An object containing the different subscriber
	*/
	function getUserInformation($userId)
	{
		$listeComplete = array();

		$user_info = get_userdata($userId);

		unset($valeurs);
		$valeurs['user_id'] = $user_info->ID;
		if( (isset($user_info->user_lastname) && ($user_info->user_lastname != '')) )
		{
			$valeurs['user_lastname'] = $user_info->user_lastname;
		}
		else
		{
			$valeurs['user_lastname'] = '';
		}
		if( (isset($user_info->user_firstname) && ($user_info->user_firstname != '')) )
		{
			$valeurs['user_firstname'] = $user_info->user_firstname;
		}
		else
		{
			$valeurs['user_firstname'] = $user_info->user_nicename;
		}

		$listeComplete[$user_info->ID] = $valeurs;

		return $listeComplete;
	}


	/**
	*	Get the identifier of the groups bind with an element
	*
	*	@param integer $elementId The identifier of the element (in its table) we want to bind
	*	@param string $elementTable The table of the element we want to bind
	*
	*	@return array An array containing the groups identifiers
	*/
	function getBindUsers($elementId, $elementTable)
	{
		global $wpdb;
		
		$elementId = mysql_real_escape_string($elementId);
		$elementTable = mysql_real_escape_string($elementTable);
		
		$queryCleanGroupBind = $wpdb->prepare("SELECT id_user FROM " . TABLE_LIAISON_USER_ELEMENT . " WHERE table_element = '%s' AND id_element = %d and status='valid'", $elementTable, $elementId);
		
		return $wpdb->get_results($queryCleanGroupBind);
	}

	/**
	*	Return different informations about users
	*/
	function dashBoardStats()
	{
		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT 
				(
					SELECT count(USERS.ID)
					FROM " . $wpdb->prefix . "users AS USERS
					WHERE USERS.ID != 1
				) AS TOTAL_USER, 
				(
					SELECT COUNT( DISTINCT( USER_LINK_EVALUATION.id_user ) )
					FROM " . TABLE_LIAISON_USER_ELEMENT . " AS USER_LINK_EVALUATION
					WHERE USER_LINK_EVALUATION.table_element = '" . TABLE_UNITE_TRAVAIL . "_evaluation'
						AND status = 'valid'
					GROUP BY USER_LINK_EVALUATION.table_element
				) AS EVALUATED_USER
			LIMIT 1"
		);

		return $wpdb->get_row($query);
	}

	function importUserPage()
	{
		global $wpdb;
		$separatorExample = '<span class="fieldSeparator" >[fieldSeparator]</span>';

		$importAction = isset($_POST['act']) ? eva_tools::IsValid_Variable($_POST['act']) : '';
		$userRoles = isset($_POST['userRoles']) ? eva_tools::IsValid_Variable($_POST['userRoles']) : '';
		$fieldSeparator = isset($_POST['fieldSeparator']) ? eva_tools::IsValid_Variable($_POST['fieldSeparator']) : '';
		$sendUserMail = isset($_POST['sendUserMail']) ? eva_tools::IsValid_Variable($_POST['sendUserMail']) : '';

		$optionEmailDomain = '';
		$checkEmailDomain = digirisk_options::getOptionValue('emailDomain');
		if(isset($_POST['domaineMail']) && ($checkEmailDomain != $_POST['domaineMail']))
		{
			digirisk_options::updateDigiOption('emailDomain', $_POST['domaineMail']);
			$checkEmailDomain = digirisk_options::getOptionValue('emailDomain');
		}

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
<div id="ajax-response" style="display:none;" >&nbsp;</div>
<script type="text/javascript" >
	function changeSeparator(){
		evarisk('.fieldSeparator').html(evarisk('#fieldSeparator').val());
	}
	evarisk(document).ready(function(){
		changeSeparator();
		evarisk('#fieldSeparator').blur(function(){changeSeparator()});

		evarisk('#ajouterUtilisateurListe').click(function(){
			var error = 0;
			evarisk('#mailDomainContainer').css('color', '#000000');
			evarisk('#firstNameContainer').css('color', '#000000');
			evarisk('#lastNameContainer').css('color', '#000000');
			evarisk('#fastAddErrorMessage').hide();

			evarisk('#domaineMail').val(evarisk('#domaineMail').val().replace("@", ""));

			if(evarisk('#domaineMail').val() == ""){
				evarisk('#mailDomainContainer').css('color', '#FF0000');
				error++;
			}
			if(evarisk('#prenomUtilisateur').val() == ""){
				evarisk('#firstNameContainer').css('color', '#FF0000');
				error++;
			}
			if(evarisk('#nomUtilisateur').val() == ""){
				evarisk('#lastNameContainer').css('color', '#FF0000');
				error++;
			}

			if(error > 0){
				evarisk('#fastAddErrorMessage').show();
			}
			else{
				identifiant = evarisk('#prenomUtilisateur').val() + '.' + evarisk('#nomUtilisateur').val();
				prenom = evarisk('#prenomUtilisateur').val();
				nom = evarisk('#nomUtilisateur').val();
				motDePasse = evarisk('#motDePasse').val();
				emailUtilisateur = evarisk('#prenomUtilisateur').val() + '.' + evarisk('#nomUtilisateur').val() + '@' + evarisk('#domaineMail').val();
				roleUtilisateur = evarisk('#userRoles').val();

				newline = identifiant + evarisk('#fieldSeparator').val() + prenom + evarisk('#fieldSeparator').val() + nom + evarisk('#fieldSeparator').val() + motDePasse + evarisk('#fieldSeparator').val() + emailUtilisateur + evarisk('#fieldSeparator').val() + roleUtilisateur;

				if(evarisk('#userLinesToCreate').val() != ''){
					newline = '\r\n' + newline;
				}
				evarisk('#userLinesToCreate').val(evarisk('#userLinesToCreate').val() + newline);
				evarisk('#prenomUtilisateur').val("");
				evarisk('#nomUtilisateur').val("");

<?php echo $optionEmailDomain;	?>
			}
		});
	});
</script>
<form enctype="multipart/form-data" method="post" action="" >
	<input type="hidden" name="act" id="act" value="1" />

	<!-- 	Start of file specification part	-->
	<h3><?php echo __('Sp&eacute;cifications pour le fichier', 'evarisk'); ?></h3>
	<div class="alignleft" >
		<?php echo __('Chaque ligne de d&eacute;finition devra respecter le format ci-apr&egrave;s&nbsp;:', 'evarisk'); ?>
		<br/><span style="font-style:italic;font-size:10px;" ><?php echo '<span style="color:#CC0000;" >' . __('Les champs identifiants et email sont obligatoires.', 'evarisk') . '</span><br/>' . __('Vous n\'&ecirc;tes pas oblig&eacute; de renseigner tous les champs mais tous les s&eacute;parateur doivent &ecirc;tre pr&eacute;sent.', 'evarisk') . '<br/>' . __('Exemple&nbsp;', 'evarisk') . '&nbsp;<span style="font-weight:bold;" >' . __('identifiant', 'evarisk') . $separatorExample . $separatorExample . $separatorExample . $separatorExample . __('email', 'evarisk') . $separatorExample . '</span>'; ?></span>
		<div style="margin:3px 6px;padding:12px;border:1px solid #333333;width:80%;text-align:center;" ><?php echo '<span style="color:#CC0000;" >' . __('identifiant', 'evarisk') . '</span>' . $separatorExample . __('prenom', 'evarisk') . $separatorExample . __('nom', 'evarisk') . $separatorExample . __('mot de passe', 'evarisk') . $separatorExample . '<span style="color:#CC0000;" >' . __('email', 'evarisk') . '</span>' . $separatorExample . __('role', 'evarisk'); ?></div>
	</div>
	<div class="floatleft" style="margin: 0px 36px;" >
		<table style="margin:0px 36px;" summary="" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<?php echo __('S&eacute;parateur de champs', 'evarisk'); ?>
				</td>
				<td>
					<input type="text" name="fieldSeparator" id="fieldSeparator" value=";" />
				</td>
			</tr>
			<tr>
				<td>
					<?php echo __('R&ocirc;le pour les utilisateurs', 'evarisk'); ?><br/>
					<span style="font-style:italic;font-size:10px;" ><?php echo __('Si aucun r&ocirc;le n\'a &eacute;t&eacute; d&eacute;fini dans le fichier', 'evarisk'); ?></span>
				</td>
				<td>
					<select name="userRoles" id="userRoles" >
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
				<td>
					<?php echo __('Envoyer le mot de passe aux utilisateurs.', 'evarisk'); ?>
					<br/><span style="font-weight:bold;font-size:9px;" ><?php echo __('(Peut ne pas fonctionner sur certains serveurs)', 'evarisk'); ?></span>
				</td>
				<td>
					<input type="checkbox" name="sendUserMail" id="sendUserMail" />
				</td>
			</tr>
		</table>
	</div>


	<!-- 	Start of fast add part	-->
	<h3 class="clear" ><?php echo __('Ajout rapide d\'utilisateurs', 'evarisk'); ?></h3>
	<table summary="Fast user adding section" cellpadding="0" cellspacing="0" >
		<tr>
			<td id="mailDomainContainer"><?php echo ucfirst(strtolower(__('domaine de l\'adresse email (sans le @)', 'evarisk'))); ?></td>
			<td><input type="text" value="<?php echo $checkEmailDomain; ?>" id="domaineMail" name="domaineMail" /></td>
			<td style="text-align:right;" ><?php echo ucfirst(strtolower(__('mot de passe par d&eacute;faut', 'evarisk'))); ?><br/><span style="font-size:9px;" ><?php echo __('Laissez vide pour un mot de passe al&eacute;atoire', 'evarisk'); ?></span></td>
			<td><input type="text" value="" id="motDePasse" name="motDePasse" /></td>
		</tr>
		<tr>
			<td colspan="2" >&nbsp;</td>
		</tr>
		<tr>
			<td id="firstNameContainer"><?php echo ucfirst(strtolower(__('prenom', 'evarisk'))); ?></td>
			<td id="lastNameContainer"><?php echo ucfirst(strtolower(__('nom', 'evarisk'))); ?></td>
		</tr>
		<tr>
			<td><input type="text" value="" id="prenomUtilisateur" name="prenomUtilisateur" /></td>
			<td><input type="text" value="" id="nomUtilisateur" name="nomUtilisateur" /></td>
			<td><input type="button" class="button-secondary" value="<?php echo __('Ajouter &agrave; la liste des utilisateurs &agrave; importer', 'evarisk'); ?>" id="ajouterUtilisateurListe" name="ajouterUtilisateurListe" /><div id="fastAddErrorMessage" style="display:none;color:#FF0000;" ><?php echo __('Merci de remplir les champs marqu&eacute;s en rouge', 'evarisk'); ?></div></td>
		</tr>
	</table>


	<!-- 	Start of user list to import part	-->
	<h3><?php echo __('Utilisateurs &agrave; importer', 'evarisk'); ?></h3>
	<table summary="User list to import section" cellpadding="0" cellspacing="0" >
		<tr>
			<td colspan="3"><a href="<?php echo EVA_MODELES_PLUGIN_URL; ?>import_users.ods" ><?php echo __('Vous pouvez t&eacute;l&eacute;charger le fichier pour construire l\'import ici', 'evarisk'); ?></a></td>
		</tr>
		<tr>
			<td><?php echo __('Vous pouvez entrer directement le utilisateurs que vous souhaitez cr&eacute;er ici.<br/>Chaques utilisateur sera s&eacute;par&eacute; par un retour &agrave; la ligne', 'evarisk'); ?></td>
			<td rowspan="2" ><?php echo __('-&nbsp;et&nbsp;/&nbsp;ou&nbsp;-', 'evarisk'); ?></td>
			<td><?php echo __('Vous pouvez envoyer un fichier contenant les utilisateurs &agrave; cr&eacute;er (extension autoris&eacute;e *.odt, *.csv, *.txt)', 'evarisk'); ?></td>
		</tr>
		<tr>
			<td><textarea name="userLinesToCreate" id="userLinesToCreate" cols="70" rows="5"></textarea></td>
			<td style="vertical-align:top;" ><input type="file" id="userFileToCreate" name="userFileToCreate" /></td>
		</tr>
	</table>


	<!-- 	Submit form button	-->
	<input type="submit" class="button-primary" name="importSubmit" id="importSubmit" value="<?php echo __('Importer les utilisateurs', 'evarisk'); ?>" />
</form>
<?php
	}









	/**
	*	Create an instance for a user. Initialize the entity id related to the user
	*/
	function evaUser($AttributeGroupName)
	{
		global $eav_attribute;

		$eav_attribute->setCurrentEntityTypeId($eav_attribute->getEntityInformation('eva_users'));

		$this->_currentAttributeGroup = $AttributeGroupName;
	}
	/**
	*	Get the query that will be launch to update different attribute
	*
	*	@return mixed $this->_attributesValueQuery The query we will launch to update information
	*/
	function setUserToUpdate($userId)
	{
		$this->_userToUpdate = $userId;
	}
	/**
	*	Get the list of attribute available for a given group
	*
	*	@return mixed $attributeForm The different element to output
	*/
	function evaUserAttributeForm()
	{
		global $eav_attribute;

		$attributeForm = $eav_attribute->attributeFormOutput($this->_currentAttributeGroup, $this->_userToUpdate);

		return $this->evaUserAttributeFormTemplate($attributeForm);
	}
	/**
	*	Output a container with the different element for user
	*
	*	@param mixed $content The different element to output in the template
	*/
	function evaUserAttributeFormTemplate($content)
	{
		if(trim($content) != '')
		{
?>
<h3><?php _e('Informations compl&eacute;mentaires evarisk'); ?></h3>
<div class="form-table" id="evaUserInformation" >
	<?php echo $content; ?>
</div>
<?php
		}
	}
	/**
	*	Update the different information for an user profile. First we check that there is an user update form that were send, then if it's the case we do the job
	*/
	function evaUserUpdateProfile()
	{
		/* Check if we send the user update form, if it is not the case we return to the normal output */
		if(!isset($_POST['user_id'])) return;

		global $eav_attribute;

		if(isset($_REQUEST['userAttributes']) && is_array($_REQUEST['userAttributes']) && (count($_REQUEST['userAttributes']) > 0))
		{
			foreach ($_REQUEST['userAttributes'] as $attributeType => $attributeContent) :
					$eav_attribute->createAttributesValueHeaderQuery($attributeType);
				foreach ($attributeContent as $attributeCode => $attributeValue) :
					$eav_attribute->createAttributesValueQuery($attributeCode, $_POST['user_id'], $attributeValue);
				endforeach;
					$eav_attribute->attributesValueQueryTrimmer();
					$eav_attribute->setAttributesValue();
					$eav_attribute->attributesValueQueryTrimmer(true);
			endforeach;
		}
	}


}