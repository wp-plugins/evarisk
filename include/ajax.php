<?php
/*
 *
 * @author Evarisk
 * @version v5.0
 */
define('DOING_AJAX', true);
define('WP_ADMIN', true);
require_once('../../../../wp-load.php');
require_once(ABSPATH . 'wp-admin/includes/admin.php');
require_once('../evarisk.php');
require_once(EVA_INC_PLUGIN_DIR . 'includes.php' );

require_once(EVA_LIB_PLUGIN_DIR . 'evaGoogleMaps.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'adresse/evaAddress.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'danger/categorieDangers/categorieDangers.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'danger/danger/evaDanger.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'options.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayDesign.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayInput.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'arborescence.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/groupement/eva_groupement.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/uniteDeTravail/uniteDeTravail.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'veilleReglementaire/evaAnswerToQuestion.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'veilleReglementaire/evaGroupeQuestion.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/tache/evaTask.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/tache/evaTaskTable.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/activite/evaActivity.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/suivi_activite.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'methode/methodeEvaluation.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'risque/Risque.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'users/evaUser.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'photo/evaPhoto.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'gestionDocumentaire/gestionDoc.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'evaRecommandation/evaRecommandation.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'database.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'Zip/Zip.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'evaNotes.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/ficheDePoste/ficheDePoste.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/ficheDePoste/ficheDeGroupement.class.php');

@header('Content-Type: text/html; charset=' . get_option('blog_charset'));

/*
 * Param�tres pass�s en POST
 */
if($_REQUEST['post'] == 'true')
{
	/*	Refactoring actions	*/
	if(isset($_REQUEST['act']))
	{
		switch($_REQUEST['act'])
		{
			case 'edit':
			case 'add':
			case 'changementPage':
			{
				$tableId = 'mainTable';
				$output = '
					<script type="text/javascript">
						evarisk(document).ready(function() {
							initialiseClassicalPage();';
				if($_REQUEST['affichage'] == "affichageTable")
				{
					$output .= '
							initialiseEditedElementInGridMode("photo' . $_REQUEST['table'] . $_REQUEST['id'] . '");';
				}
				else
				{
					switch($_REQUEST['table'])
					{
						case TABLE_GROUPEMENT:
						case TABLE_CATEGORIE_DANGER:
						case TABLE_TACHE:
							$output .= '
							evarisk("#node-' . $tableId . '-' . $_REQUEST['id'] . '").addClass("edited");';
						break;
						case TABLE_UNITE_TRAVAIL:
						case TABLE_DANGER:
						case TABLE_ACTIVITE:
							$output .= '
							evarisk("#leaf-' . $_REQUEST['id'] . '").addClass("edited");';
						break;
					}
				}
				$output .= '
						});
					</script>';
				echo $output;
				require_once(EVA_MODULES_PLUGIN_DIR . 'partieDroite.php');
			}
			break;

			/*	Actions on picture	*/
			case 'defaultPictureSelection':
				echo evaPhoto::setMainPhotoAction($_REQUEST['table'], $_REQUEST['idElement'], $_REQUEST['idPhoto']);
			break;
			case 'DeleteDefaultPictureSelection':
				echo evaPhoto::setMainPhotoAction($_REQUEST['table'], $_REQUEST['idElement'], $_REQUEST['idPhoto'], 'no');
			break;
			case 'deletePicture':
				echo evaPhoto::deletePictureAction($_REQUEST['table'], $_REQUEST['idElement'], $_REQUEST['idPicture']);
			break;
			case 'reloadGallery':
				$script = 
				'<script type="text/javascript">
					evarisk(document).ready(function(){
						evarisk(".qq-upload-list").hide();
					});
				</script>';
				echo $script . evaPhoto::outputGallery($_REQUEST['table'], $_REQUEST['idElement']);
			break;
			case 'showGallery':
				echo evaPhoto::getGallery($_REQUEST['table'], $_REQUEST['idElement']);
			break;

			case 'saveDigiNote':
				echo evaNotes::saveDigiNote($_REQUEST['notesContent']);
			break;
			case 'loadDiginote':
				echo evaNotes::noteDialogForm();
			break;

			case 'loadUserInfo':
			{
				echo digirisk_accident::get_victim_accident_informations($_REQUEST['id_user']);
			}			
			break;

			case 'loadWitnessInfo':
			{
				echo digirisk_accident::get_accident_third_party_informations($_REQUEST['id_user'], 'witness');
			}			
			break;

			case 'loadThirdPartyInfo':
			{
				echo digirisk_accident::get_accident_third_party_informations($_REQUEST['id_user'], 'third_party');
			}			
			break;
		}
	}

	if(isset($_REQUEST['table']))
	{
		switch($_REQUEST['table'])
		{
			case TABLE_GROUPEMENT:
				switch($_REQUEST['act'])
				{
					case 'transfert':
					{
						$fils = $_REQUEST['idElementSrc'];
						$pere = $_REQUEST['idElementDest'];
						$idPere = str_replace('node-' . $_REQUEST['location'] . '-','', $pere);
						$idFils = (string)((int) str_replace('node-' . $_REQUEST['location'] . '-','', $fils));
						$groupementPere = EvaGroupement::getGroupement($idPere);
						if($idFils == str_replace('node-' . $_REQUEST['location'] . '-','', $fils)) //Le fils est un groupement
						{
							$groupement = EvaGroupement::getGroupement($idFils);
							$descendants = Arborescence::getDescendants(TABLE_GROUPEMENT, $groupement);
							$sourceIsParentOfDest = false;
							foreach($descendants as $sourceDescendants)
							{
								if($sourceDescendants->id == $idPere)
								{
									$sourceIsParentOfDest = true;
								}
							}
							if(!$sourceIsParentOfDest)
							{
								$pereActu = Arborescence::getPere($_REQUEST['table'], $groupement);
								if($pereActu->id != $idPere)
								{
									$_REQUEST['act'] = 'update';
									$_REQUEST['id'] = $groupement->id;
									$_REQUEST['nom_groupement'] = $groupement->nom;
									$_REQUEST['description'] = $groupement->description;
									$_REQUEST['telephone'] = $groupement->telephoneGroupement;
									$_REQUEST['effectif'] = $groupement->effectif;
									
									$address = new EvaAddress($groupement->id_adresse);
									
									$address->load();
									$contenuInputLigne1 = $address->getFirstLine();
									$contenuInputLigne2 = $address->getSecondLine();
									$contenuInputCodePostal = $address->getPostalCode();
									$contenuInputVille = $address->getCity();
									
									$_REQUEST['adresse_ligne_1'] = $address->getFirstLine();
									$_REQUEST['adresse_ligne_2'] = $address->getSecondLine();
									$_REQUEST['code_REQUESTal'] = $address->getPostalCode();
									$_REQUEST['ville'] = $address->getCity();
									$_REQUEST['longitude'] = $address->getLongitude();
									$_REQUEST['latitude'] = $address->getLatitude();
									$_REQUEST['groupementPere'] = $idPere;
									require_once(EVA_METABOXES_PLUGIN_DIR . 'evaluationDesRisques/groupement/groupementPersistance.php');
								}
							}
							else
							{
								echo 
	'<script type="text/javascript" >
			setTimeout(\'actionMessageShow("#message", "<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="error" />' . __('Vous ne pouvez pas effectuer ce d&eacute;placement', 'evarisk') . '")\', 700);
			setTimeout(\'actionMessageHide("#message")\',7500);
	</script>';
							}
						}
						else //Le fils est une unit�
						{
							$idFils = str_replace('leaf-','', $fils);
							eva_uniteDeTravail::transfertUnit($idFils, $idPere);
						}
					}
					break;
					case 'save':
					case 'update':
					{
						global $wpdb;
						switch($_REQUEST['act'])
						{
							case 'save':
								$action = __('sauvegard&eacute;e', 'evarisk');
								$afterActionList = '
										evarisk("#partieGauche").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
										{
											"post": "true", 
											"table": "' . TABLE_GROUPEMENT . '",
											"act": "edit",
											"id": "' . $_REQUEST['id'] . '",
											"partie": "left",
											"menu": evarisk("#menu").val(),
											"affichage": "affichageListe",
											"expanded": expanded
										});';
								$afterActionTable = '
										evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
											"table": "' . TABLE_GROUPEMENT . '",
											"act": "edit",
											"id": "' . $_REQUEST['id'] . '",
											"partie": "left",
											"menu": evarisk("#menu").val(),
											"affichage": "affichageTable",
											"page": evarisk("#pagemainPostBoxReference").val(),
											"idPere": evarisk("#identifiantActuellemainPostBox").val(),
											"partition": "tout"
										})';
								break;
							case 'update':
								$action = __('mise &agrave; jour', 'evarisk');
								$afterActionList = '';
								$afterActionTable = '';
								break;
						}	
						require_once(EVA_METABOXES_PLUGIN_DIR . 'evaluationDesRisques/groupement/groupementPersistance.php');		
						$messageInfo = '
							<script type="text/javascript">
								evarisk(document).ready(function(){
									actionMessageShow("#message", "' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s a correctement &eacute;t&eacute; %s', 'evarisk') . '</strong></p>', __('du groupement', 'evarisk') . ' "' . stripslashes($_REQUEST['nom_groupement']) . '"', $action)) . '");
									setTimeout(\'actionMessageHide("#message")\',7500);
									
									evarisk("#rightEnlarging").show();
									evarisk("#equilize").click();
									evarisk("#partieEdition").html(evarisk("#loadingImg").html());
									if("' . $_REQUEST['affichage'] . '" == "affichageTable")
									{
										if(evarisk("#filAriane :last-child").is("label"))
											evarisk("#filAriane :last-child").remove();
										evarisk("#filAriane :last-child").after("<label>&nbsp;&raquo;&nbsp;&Eacute;dition&nbsp;de&nbsp;' . $_REQUEST['nom_groupement'] . '</label>");
										evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
											"table": "' . TABLE_GROUPEMENT . '",
											"act": "edit",
											"id": "' . $_REQUEST['id'] . '",
											"partie": "right",
											"menu": evarisk("#menu").val(),
											"affichage": "affichageTable",
											"page": evarisk("#pagemainPostBoxReference").val(),
											"idPere": evarisk("#identifiantActuellemainPostBox").val(),
											"partition": "tout"
										});
										' . $afterActionTable	. '
									}
									else
									{
										var expanded = new Array();
										evarisk(".expanded").each(function(){expanded.push(evarisk(this).attr("id"));});
										evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
											"table": "' . TABLE_GROUPEMENT . '",
											"act": "edit",
											"id": "' . $_REQUEST['id'] . '",
											"partie": "right",
											"menu": evarisk("#menu").val(),
											"affichage": "affichageListe",
											"expanded": expanded
										});
										' . $afterActionList . '
									}
									evarisk("#node-mainTable-' . $_REQUEST['id'] . ' td:first-child").children("span.nomNoeudArbre").html("' . $_REQUEST['nom_groupement'] . '");
								});
							</script>';
						echo $messageInfo;
					}
					break;
					case 'delete':
					{
						require_once(EVA_METABOXES_PLUGIN_DIR . 'evaluationDesRisques/groupement/groupementPersistance.php');		
						echo '
							<script type="text/javascript">
								evarisk(document).ready(function() {
									initialiseClassicalPage();
									evarisk("#partieEdition").html(" ");
								});
							</script>';
						require_once(EVA_MODULES_PLUGIN_DIR . 'partieDroite.php');
					}
					break;
					case 'updateByField':
					{
						$id_Groupement = mysql_real_escape_string(eva_tools::IsValid_Variable($_REQUEST['id']));
						$whatToUpdate = mysql_real_escape_string(eva_tools::IsValid_Variable($_REQUEST['whatToUpdate']));
						$whatToSet = mysql_real_escape_string(eva_tools::IsValid_Variable($_REQUEST['whatToSet']));

						switch($whatToUpdate){
							case 'nom':
							{
								$messageContainerId = 'message';
								$actionAfterSuccess = 'evarisk("#validChangeTitre").hide();
			evarisk("#titreGp' . $id_Groupement . '").removeClass("titleInfoSelected");
			evarisk("#node-mainTable-' . $id_Groupement . '-name span.nomNoeudArbre").html("' . $whatToSet . '");';
							}
							break;
							default:
							{
								$messageContainerId = 'message';
								$actionAfterSuccess = '';
							}
							break;
						}

						$updateGroupement = EvaGroupement::updateGroupementByField($id_Groupement, $whatToUpdate, $whatToSet);
						if($updateGroupement)
						{
							$messageInfo = addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Sauvegarde r&eacute;ussie', 'evarisk') . '</strong></p>');
						}
						else
						{
							$messageInfo = addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La sauvegarde a &eacute;chou&eacute;e', 'evarisk') . '</strong></p>');
							$actionAfterSuccess = '';
						}
	echo 
	'<script type="text/javascript">
		evarisk(document).ready(function(){
			actionMessageShow("#' . $messageContainerId . '", "' . $messageInfo . '");
			setTimeout(\'actionMessageHide("#' . $messageContainerId . '")\',7500);

			' . $actionAfterSuccess . '
		});
	</script>';
					}
					break;
					case 'reactiv_deleted':
					{
						$nom_groupement = (isset($_REQUEST['nom_groupement']) && ($_REQUEST['nom_groupement'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['nom_groupement']) : '';
						$currentGpt = EvaGroupement::getGroupements(" nom = '" . $nom_groupement . "' ");
						if(EvaGroupement::updateGroupementByField($currentGpt[0]->id, 'Status', 'Valid'))
						{
							$messageInfo = addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La restauration a r&eacute;ussie', 'evarisk') . '</strong></p>');
							$moreAction = 'evarisk("#partieEdition").html(" ");evarisk("#partieGauche").html(evarisk("#loadingImg").html());
		var expanded = new Array();
		evarisk(".expanded").each(function(){expanded.push(evarisk(this).attr("id"));});
		evarisk("#partieGauche").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
			"post": "true", 
			"table": "' . TABLE_GROUPEMENT . '",
			"act": "edit",
			"id": "' . $_REQUEST['id'] . '",
			"partie": "left",
			"menu": evarisk("#menu").val(),
			"affichage": "affichageListe",
			"expanded": expanded
		});';
						}
						else
						{
							$messageInfo = addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La restauration a &eacute;chou&eacute;e', 'evarisk') . '</strong></p>');
							$moreAction = '';
						}

						echo 
	'<script type="text/javascript">
		evarisk("#existingElementDialog").dialog("close");

		actionMessageShow("#message", "' . $messageInfo . '");
		setTimeout(\'actionMessageHide("#message")\',7500);

	' . $moreAction . '
	</script>';
					}
					break;

					case 'load_groupement_form':
					{
						include_once(EVA_METABOXES_PLUGIN_DIR . 'evaluationDesRisques/groupement/groupement-new.php');
						$_REQUEST['dont_display_button'] = 'yes';
						$_REQUEST['form_id'] = '_accident';
						getGroupGeneralInformationPostBoxBody($_REQUEST);
					}
					break;
					case 'save_groupement_missing_informations':
					{
						$_REQUEST['act'] = 'update';
						require_once(EVA_METABOXES_PLUGIN_DIR . 'evaluationDesRisques/groupement/groupementPersistance.php');	
						echo '
<script type="text/javascript" >
	evarisk(document).ready(function(){
		jQuery("#divAccidenContainer").load(EVA_AJAX_FILE_URL,{
			"post":"true",
			"table":"' . DIGI_DBT_ACCIDENT . '",
			"act":"previous_step",
			"accident_id": jQuery("#accident_form #tableElement").val(),
			"tableElement": jQuery("#accident_form #tableElement").val(),
			"idElement": jQuery("#accident_form #idElement").val(),
			"step_to_load":jQuery("#accident_form #accident_form_step").val() 
		});
	});
</script>';
					}
					break;
				}
				break;
			case TABLE_UNITE_TRAVAIL:
				switch($_REQUEST['act'])
				{
					case 'save':
					case 'update':
					{
						$previousAct = $_REQUEST['act'];
						$workingUnitResult = false;
						require_once(EVA_METABOXES_PLUGIN_DIR . 'evaluationDesRisques/uniteDeTravail/uniteTravailPersistance.php' );
						if($workingUnitResult)
						{
							switch($previousAct)
							{
								case 'save':
								{
									$action = __('sauvegard&eacute;e', 'evarisk');
									$afterActionList = '
										evarisk("#partieGauche").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
										{
											"post": "true", 
											"table": "' . TABLE_UNITE_TRAVAIL . '",
											"act": "edit",
											"id": "' . $_REQUEST['id'] . '",
											"partie": "left",
											"menu": evarisk("#menu").val(),
											"affichage": "affichageListe",
											"expanded": expanded
										});';
									$afterActionTable = '
											evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
											{
												"post": "true", 
												"table": "' . TABLE_UNITE_TRAVAIL . '",
												"id": "' . $_REQUEST['id'] . '",
												"page": evarisk("#pagemainPostBoxReference").val(),
												"idPere": evarisk("#identifiantActuellemainPostBox").val(),
												"act": "edit",
												"partie": "left",
												"menu": evarisk("#menu").val(),
												"affichage": "affichageTable",
												"partition": "tout"
											});';
								}
								break;
								case 'update':
								{
									$action = __('mise &agrave; jour', 'evarisk');
									$afterActionList = '
		evarisk("#leaf-' . $_REQUEST['id'] . ' span.nomFeuilleArbre").html("' . $_REQUEST['nom_unite_travail'] . '");';
									$afterActionTable = '';
								}
								break;
							}

							$messageInfo = '
<script type="text/javascript">
	evarisk(document).ready(function(){
		actionMessageShow("#message", "' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s a correctement &eacute;t&eacute; %s', 'evarisk') . '</strong></p>', __('de l\'unit&eacute; de travail', 'evarisk') . ' "' . stripslashes($_REQUEST['nom_unite_travail']) . '"', $action)) . '");
		setTimeout(\'actionMessageHide("#message")\',7500);
		
		evarisk("#rightEnlarging").show();
		evarisk("#equilize").click();
		evarisk("#partieEdition").html(evarisk("#loadingImg").html());';
							if($_REQUEST['affichage'] == 'affichageTable')
							{
								$messageInfo .= '
		if(evarisk("#filAriane :last-child").is("label"))
			evarisk("#filAriane :last-child").remove();
		evarisk("#filAriane :last-child").after(\'<label>&nbsp;&raquo;&nbsp;&Eacute;dition&nbsp;de&nbsp;' . $_REQUEST['nom_unite_travail'] . '</label>\');
		if(evarisk("#filAriane :last-child").is("label"))
			evarisk("#filAriane :last-child").remove();
		evarisk("#rightEnlarging").show();
		evarisk("#equilize").click();
		evarisk("#filAriane :last-child").after(\'<label>&nbsp;&raquo;&nbsp;&Eacute;dition&nbsp;de&nbsp;' . $_REQUEST['nom_unite_travail'] . '</label>\');
		evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
		{
			"post": "true", 
			"table": "' . TABLE_UNITE_TRAVAIL . '",
			"id": "' . $_REQUEST['id'] . '",
			"page": evarisk("#pagemainPostBoxReference").val(),
			"idPere": evarisk("#identifiantActuellemainPostBox").val(),
			"act": "edit",
			"partie": "right",
			"menu": evarisk("#menu").val(),
			"affichage": "affichageTable",
			"partition": "tout"
		});
		' . $afterActionTable;
							}
							else
							{
								$messageInfo .= '
		var expanded = new Array();
		evarisk(".expanded").each(function(){expanded.push(evarisk(this).attr("id"));});
		evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
		{
			"post": "true", 
			"table": "' . TABLE_UNITE_TRAVAIL . '",
			"act": "edit",
			"id": "' . $_REQUEST['id'] . '",
			"partie": "right",
			"menu": evarisk("#menu").val(),
			"affichage": "affichageListe",
			"expanded": expanded
		});
		' . $afterActionList;
							}
							$messageInfo .= '
	});
</script>';
						}
						else
						{
							$messageInfo = '
<script type="text/javascript">
	evarisk(document).ready(function(){
		actionMessageShow("#message", "' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Une erreur est survenue lors de l\'enregistrement de la fiche %s', 'evarisk') . '</strong></p>', __('de l\'unit&eacute; de travail', 'evarisk') . ' "' . stripslashes($_REQUEST['nom_unite_travail']) . '"')) . '");
		setTimeout(\'actionMessageHide("#message")\',7500);
	});
</script>';
						}
						echo $messageInfo;
					}
					break;
					case 'delete':
					{
						require_once(EVA_METABOXES_PLUGIN_DIR . 'evaluationDesRisques/uniteDeTravail/uniteTravailPersistance.php' );
						echo '
							<script type="text/javascript">
								evarisk(document).ready(function() {
									initialiseClassicalPage();
									evarisk("#partieEdition").html(" ");
								});
							</script>';
						require_once(EVA_MODULES_PLUGIN_DIR . 'partieDroite.php');
					}
					break;
					case 'updateByField':
					{
						$id_unite = mysql_real_escape_string(eva_tools::IsValid_Variable($_REQUEST['id']));
						$whatToUpdate = mysql_real_escape_string(eva_tools::IsValid_Variable($_REQUEST['whatToUpdate']));
						$whatToSet = mysql_real_escape_string(eva_tools::IsValid_Variable($_REQUEST['whatToSet']));

						switch($whatToUpdate){
							case 'nom':
							{
								$messageContainerId = 'message';
								$actionAfterSuccess = 'evarisk("#validChangeTitre").hide();
			evarisk("#titreWU' . $id_unite . '").removeClass("titleInfoSelected");
			evarisk("#leaf-' . $id_unite . ' span.nomFeuilleArbre").html("' . $whatToSet . '");';
							}
							break;
							default:
							{
								$messageContainerId = 'message';
								$actionAfterSuccess = '';
							}
							break;
						}

						$updateGroupement = eva_UniteDeTravail::updateWorkingUnitByField($id_unite, $whatToUpdate, $whatToSet);
						if($updateGroupement)
						{
							$messageInfo = addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Sauvegarde r&eacute;ussie', 'evarisk') . '</strong></p>');
						}
						else
						{
							$messageInfo = addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La sauvegarde a &eacute;chou&eacute;e', 'evarisk') . '</strong></p>');
							$actionAfterSuccess = '';
						}
	echo 
	'<script type="text/javascript">
		evarisk(document).ready(function(){
			actionMessageShow("#' . $messageContainerId . '", "' . $messageInfo . '");
			setTimeout(\'actionMessageHide("#' . $messageContainerId . '")\',7500);

			' . $actionAfterSuccess . '
		});
	</script>';
					}
					break;
					case 'reactiv_deleted':
					{
						$nom_groupement = (isset($_REQUEST['nom_groupement']) && ($_REQUEST['nom_groupement'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['nom_groupement']) : '';
						$currentGpt = EvaGroupement::getGroupements(" nom = '" . $nom_groupement . "' ");
						if(EvaGroupement::updateGroupementByField($currentGpt[0]->id, 'Status', 'Valid'))
						{
							$messageInfo = addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La restauration a r&eacute;ussie', 'evarisk') . '</strong></p>');
							$moreAction = 'evarisk("#partieEdition").html(" ");
		var expanded = new Array();
		evarisk(".expanded").each(function(){expanded.push(evarisk(this).attr("id"));});
		evarisk("#partieGauche").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
			"post": "true", 
			"table": "' . TABLE_GROUPEMENT . '",
			"act": "edit",
			"id": "' . $_REQUEST['id'] . '",
			"partie": "left",
			"menu": evarisk("#menu").val(),
			"affichage": "affichageListe",
			"expanded": expanded
		});';
						}
						else
						{
							$messageInfo = addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La restauration a &eacute;chou&eacute;e', 'evarisk') . '</strong></p>');
							$moreAction = '';
						}

						echo 
	'<script type="text/javascript">
		evarisk("#existingElementDialog").dialog("close");

		actionMessageShow("#message", "' . $messageInfo . '");
		setTimeout(\'actionMessageHide("#message")\',7500);

	' . $moreAction . '
	</script>';
					}
				}
				break;
			case TABLE_CATEGORIE_DANGER:
				switch($_REQUEST['act'])
				{				
					case 'transfert':
					{
						$fils = $_REQUEST['idElementSrc'];
						$pere = $_REQUEST['idElementDest'];
						$idFils = (string)((int) str_replace('node-' . $_REQUEST['location'] . '-','', $fils));
						$idPere = str_replace('node-' . $_REQUEST['location'] . '-','', $pere);
						if($idFils == str_replace('node-' . $_REQUEST['location'] . '-','', $fils))
						//Le fils est une cat�gorie
						{
							$categorie = categorieDangers::getCategorieDanger($idFils);
							$pereActu = Arborescence::getPere($_REQUEST['table'], $categorie);
							if($pereActu->id != $idPere)
							{
								$_REQUEST['act'] = 'update';
								$_REQUEST['id'] = $idFils;
								$_REQUEST['nom_categorie'] = $categorie->nom;
								$_REQUEST['categorieMere'] = $idPere;
								require_once(EVA_METABOXES_PLUGIN_DIR . 'dangers/categorieDangers/categorieDangersPersistance.php');
							}
						}
						else
						//Le fils est un danger
						{
							$idFils = str_replace('leaf-','', $fils);
							evaDanger::transfertDanger($idFils, $idPere);
						}
					}
					break;
					case 'reloadComboDangers':
					{
						$formId = eva_tools::IsValid_Variable($_REQUEST['formId']);
						$idElement = eva_tools::IsValid_Variable($_REQUEST['idElement']);
						$dangers = categorieDangers::getDangersDeLaCategorie($idElement, 'Status="Valid"');
						$script = '';
						if($dangers[0]->id != null)
						{
							$script .= '
								<script type="text/javascript">
									evarisk(document).ready(function(){
										evarisk("#needDangerCategory").show();';
										if(count($dangers) > 1)
										{
											$script .= 
										'
										evarisk("#' . $formId . 'divDangerFormRisque").show();
										evarisk("#boutonAvanceRisque").children("span:first").html("-");';
										}
										else
										{
											$script .= 
										'
										evarisk("#' . $formId . 'divDangerFormRisque").hide();
										evarisk("#boutonAvanceRisque").children("span:first").html("+");';
										}
							$script .= 
									'})
								</script>';
						}
						else
						{
							$script .= '
<script type="text/javascript">
	evarisk(document).ready(function(){
		evarisk("#needDangerCategory").hide();
	})
</script>';
						}
						echo $script . EvaDisplayInput::afficherComboBox($dangers, $formId . 'dangerFormRisque', __('Dangers de la cat&eacute;gorie', 'evarisk') . ' : ', 'danger', '', $dangers[0]->id);
					}
					break;
					case 'save':
					case 'update':
					{
						switch($_REQUEST['act'])
						{
							case 'save':
								$action = __('sauvegard&eacute;e', 'evarisk');
								break;
							case 'update':
								$action = __('mise &agrave; jour', 'evarisk');
								break;
						}
						require_once(EVA_METABOXES_PLUGIN_DIR . 'dangers/categorieDangers/categorieDangersPersistance.php');	
						$messageInfo = '
							<script type="text/javascript">
								evarisk(document).ready(function(){
									actionMessageShow("#message", "' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s a correctement &eacute;t&eacute; %s', 'evarisk') . '</strong></p>', __('de la cat&eacute;gorie de dangers', 'evarisk') . ' "' . stripslashes($_REQUEST['nom_categorie']) . '"', $action)) . '");
									setTimeout(\'actionMessageHide("#message")\',7500);

									evarisk("#rightEnlarging").show();
									evarisk("#equilize").click();
									evarisk("#partieEdition").html(evarisk("#loadingImg").html());

									if("' . $_REQUEST['affichage'] . '" == "affichageTable")
									{
										if(evarisk("#filAriane :last-child").is("label"))
											evarisk("#filAriane :last-child").remove();
										evarisk("#filAriane :last-child").after(\'<label>&nbsp;&raquo;&nbsp;&Eacute;dition&nbsp;de&nbsp;' . $_REQUEST['nom_categorie'] . '</label>\');
										evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
											"table": "' . TABLE_CATEGORIE_DANGER . '",
											"id": "' . $_REQUEST['id'] . '",
											"page": evarisk("#pagemainPostBoxReference").val(),
											"idPere": evarisk("#identifiantActuellemainPostBox").val(),
											"act": "edit",
											"partie": "right",
											"menu": "",
											"affichage": "affichageTable",
											"partition": "tout"
										});
									}
									else
									{
										var expanded = new Array();
										evarisk(".expanded").each(function(){expanded.push(evarisk(this).attr("id"));});
										evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
											"table": "' . TABLE_CATEGORIE_DANGER . '",
											"act": "edit",
											"id": "' . $_REQUEST['id'] . '",
											"partie": "right",
											"menu": "",
											"affichage": "affichageListe",
											"expanded": expanded
										});
										evarisk("#partieGauche").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
											"table": "' . TABLE_CATEGORIE_DANGER . '",
											"act": "edit",
											"page": evarisk("#pagemainPostBoxReference").val(),
											"idPere": evarisk("#identifiantActuellemainPostBox").val(),
											"partie": "left",
											"menu": "",
											"affichage": "affichageListe",
											"partition": "tout"
										});
									}
								});
							</script>';
						echo $messageInfo;
					}
					break;
					case 'delete':
					{
						require_once(EVA_METABOXES_PLUGIN_DIR . 'dangers/categorieDangers/categorieDangersPersistance.php');
						echo '
							<script type="text/javascript">
								evarisk(document).ready(function() {
									initialiseClassicalPage();
									evarisk("#partieEdition").html(" ");
								});
							</script>';
						require_once(EVA_MODULES_PLUGIN_DIR . 'partieDroite.php');
					}
					break;
				}
				break;
			case TABLE_DANGER:
				switch($_REQUEST['act'])
				{
					case 'save':
					case 'update':
					{
						switch($_REQUEST['act'])
						{
							case 'save':
								$action = __('sauvegard&eacute;e', 'evarisk');
								break;
							case 'update':
								$action = __('mise &agrave; jour', 'evarisk');
								break;
						}
						require_once(EVA_METABOXES_PLUGIN_DIR . 'dangers/danger/dangerPersistance.php' );
						$messageInfo = '
							<script type="text/javascript">
								evarisk(document).ready(function(){
									actionMessageShow("#message", "' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s a correctement &eacute;t&eacute; %s', 'evarisk') . '</strong></p>', __('du danger', 'evarisk') . ' "' . stripslashes($_REQUEST['nom_danger']) . '"', $action)) . '");
									setTimeout(\'actionMessageHide("#message")\',7500);
									
									evarisk("#rightEnlarging").show();
									evarisk("#equilize").click();
									evarisk("#filAriane :last-child").after(\'<label>&nbsp;&raquo;&nbsp;&Eacute;dition&nbsp;de&nbsp;' . $_REQUEST['nom_danger'] . '</label>\');
									
									evarisk("#partieEdition").html(evarisk("#loadingImg").html());
									if("' . $_REQUEST['affichage'] . '" == "affichageTable")
									{
										if(evarisk("#filAriane :last-child").is("label"))
											evarisk("#filAriane :last-child").remove();
										evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
											"table": "' . TABLE_DANGER . '",
											"id": "' . $_REQUEST['id'] . '",
											"page": evarisk("#pagemainPostBoxReference").val(),
											"idPere": evarisk("#identifiantActuellemainPostBox").val(),
											"act": "edit",
											"partie": "right",
											"menu": evarisk("#menu").val(),
											"affichage": "affichageTable",
											"partition": "tout"
										});
									}
									else
									{
										var expanded = new Array();
										evarisk(".expanded").each(function(){expanded.push(evarisk(this).attr("id"));});
										evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
											"table": "' . TABLE_DANGER . '",
											"act": "edit",
											"id": "' . $_REQUEST['id'] . '",
											"partie": "right",
											"menu": evarisk("#menu").val(),
											"affichage": "affichageListe",
											"expanded": expanded
										});
										evarisk("#partieGauche").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
											"table": "' . TABLE_DANGER . '",
											"act": "edit",
											"page": evarisk("#pagemainPostBoxReference").val(),
											"idPere": evarisk("#identifiantActuellemainPostBox").val(),
											"partie": "left",
											"menu": "",
											"affichage": "affichageListe",
											"partition": "tout"
										});
									}
								});
							</script>';
						echo $messageInfo;
					}
					break;
					case 'delete':
					{
						require_once(EVA_METABOXES_PLUGIN_DIR . 'dangers/danger/dangerPersistance.php');
						echo '
							<script type="text/javascript">
								evarisk(document).ready(function() {
									initialiseClassicalPage();
									evarisk("#partieEdition").html(" ");
								});
							</script>';
						require_once(EVA_MODULES_PLUGIN_DIR . 'partieDroite.php');
					}
					break;
				}
				break;
			case TABLE_RISQUE:
				switch($_REQUEST['act'])
				{
					case 'save':
					{
						$pictureId = isset($_REQUEST['pictureId']) ? (eva_tools::IsValid_Variable($_REQUEST['pictureId'])) : '';
						$retourALaLigne = array("\r\n", "\n", "\r");
						$_REQUEST['description'] = str_replace($retourALaLigne, "[retourALaLigne]",$_REQUEST['description']);
						$_REQUEST['description'] = str_replace("�", "'",$_REQUEST['description']);
						$tableElement = $_REQUEST['tableElement'];
						$idElement = $_REQUEST['idElement'];
						require_once(EVA_METABOXES_PLUGIN_DIR . 'risque/risquePersistance.php');

						if($pictureId != '')
						{
							if($idRisque > 0)
							{
								$moreMessage = '
		actionMessageShow("#' . $pictureId . 'content", "' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le risque a bien &eacute;t&eacute; ajout&eacute;', 'evarisk') . '</strong></p>') . '");
		setTimeout(\'evarisk("#' . $pictureId . 'content").html("");evarisk("#' . $pictureId . 'content").removeClass("updated");\',3000);
		goTo("#' . $pictureId . '");';
							}
							else
							{
								$moreMessage = '
		actionMessageShow("#' . $pictureId . 'content", "' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le risque n\'a pas pu &ecirc;tre ajout&eacute;', 'evarisk') . '</strong></p>') . '");
		setTimeout(\'evarisk("#' . $pictureId . 'content").html("");evarisk("#' . $pictureId . 'content").removeClass("updated");\',3000);';
							}

							require_once(EVA_LIB_PLUGIN_DIR . 'photo/evaPhoto.class.php');
							evaPhoto::associatePicture(TABLE_RISQUE, $idRisque, str_replace('picture', '', str_replace('_', '', $pictureId)));
							echo '
<script type="text/javascript" >
	evarisk("#addRiskForPictureText' . $pictureId . '").html("' . __('Ajouter un risque pour cette photo', 'evarisk') . '");
	evarisk("#divDangerContainerSwitchPic' . $pictureId . '").attr("src").replace("collapse", "expand");
	evarisk("#riskAssociatedToPicture' . $pictureId . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
	{
		"post":"true",
		"table":"' . TABLE_RISQUE . '",
		"act":"reloadRiskAssociatedToPicture",
		"idPicture":"' . str_replace('picture', '', str_replace('_', '', $pictureId)) . '"
	});
	' . $moreMessage . '
</script>';
						}
						else
						{
							require_once(EVA_METABOXES_PLUGIN_DIR . 'risque/risque.php');
							echo getFormulaireCreationRisque($tableElement, $idElement);
						}
					}
					break;
					case 'associateRiskToPicture':
					{
						$tableElement = isset($_REQUEST['tableElement']) ? (eva_tools::IsValid_Variable($_REQUEST['tableElement'])) : '';
						$idElement = isset($_REQUEST['idElement']) ? (eva_tools::IsValid_Variable($_REQUEST['idElement'])) : '';
						$oldidPicture = isset($_REQUEST['oldidPicture']) ? (eva_tools::IsValid_Variable($_REQUEST['oldidPicture'])) : '';
						$idPhoto = isset($_REQUEST['idPicture']) ? (eva_tools::IsValid_Variable($_REQUEST['idPicture'])) : '';

						/*	Unassociate the risk to the picture	*/
						if($oldidPicture != '')
						{
							evaPhoto::unAssociatePicture($tableElement, $idElement, str_replace('picture', '', str_replace('_', '', $oldidPicture)));
							echo '
<script type="text/javascript" >
	evarisk("#riskAssociatedToPicture' . $oldidPicture . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
	{
		"post":"true",
		"table":"' . TABLE_RISQUE . '",
		"act":"reloadRiskAssociatedToPicture",
		"idPicture":"' . str_replace('picture', '', str_replace('_', '', $oldidPicture)) . '"
	});
</script>';
						}
						/*	Associate the risk to the picture	*/
						$associateResult = evaPhoto::associatePicture($tableElement, $idElement, str_replace('picture', '', str_replace('_', '', $idPhoto)));
						echo '
<script type="text/javascript" >
	evarisk("#riskAssociatedToPicture' . $idPhoto . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
	{
		"post":"true",
		"table":"' . TABLE_RISQUE . '",
		"act":"reloadRiskAssociatedToPicture",
		"idPicture":"' . str_replace('picture', '', str_replace('_', '', $idPhoto)) . '"
	});
</script>';
					}
					break;
					case 'unAssociatePicture':
					{
						$tableElement = isset($_REQUEST['tableElement']) ? (eva_tools::IsValid_Variable($_REQUEST['tableElement'])) : '';
						$idElement = isset($_REQUEST['idElement']) ? (eva_tools::IsValid_Variable($_REQUEST['idElement'])) : '';
						$idPhoto = isset($_REQUEST['idPicture']) ? (eva_tools::IsValid_Variable($_REQUEST['idPicture'])) : '';

						/*	Unassociate the risk to the picture	*/
						if($idPhoto != '')
						{
							evaPhoto::unAssociatePicture($tableElement, $idElement, $idPhoto);
							echo '
<script type="text/javascript" >
	if(evarisk("#riskAssociatedToPicturepicture_' . $idPhoto . '_")){
		evarisk("#riskAssociatedToPicturepicture_' . $idPhoto . '_").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
		{
			"post":"true",
			"table":"' . TABLE_RISQUE . '",
			"act":"reloadRiskAssociatedToPicture",
			"idPicture":"' . str_replace('picture', '', str_replace('_', '', $idPhoto)) . '"
		});
	}
	if(evarisk("#associatedPictureContainer")){
		evarisk("#associatedPictureContainer").html("");
	}
</script>';
						}
					}
					break;
					case 'reloadRiskAssociatedToPicture':
						$idPicture = isset($_REQUEST['idPicture']) ? (eva_tools::IsValid_Variable($_REQUEST['idPicture'])) : '';
						echo Risque::getRisqueAssociePhoto($idPicture);
					break;
					case 'delete':
						require_once(EVA_METABOXES_PLUGIN_DIR . 'risque/risquePersistance.php');
					break;
					case 'load':
						require_once(EVA_METABOXES_PLUGIN_DIR . 'risque/risque.php');
						echo getFormulaireCreationRisque($_REQUEST['tableElement'], $_REQUEST['idElement'], $_REQUEST['idRisque']);
					break;
					case 'reloadVoirRisque' :
						require_once(EVA_METABOXES_PLUGIN_DIR . 'risque/risque.php');
						$tableElement = $_REQUEST['tableElement'];
						$idElement = $_REQUEST['idElement'];
						echo getVoirRisque($tableElement, $idElement);
					break;
					case 'voirRisqueLigne' :
						require_once(EVA_METABOXES_PLUGIN_DIR . 'documentUnique/documentUnique.php');
						$tableElement = $_REQUEST['tableElement'];
						$idElement = $_REQUEST['idElement'];
						echo eva_documentUnique::bilanRisque($tableElement, $idElement, 'ligne');
					break;
					case 'reloadRiskForm' :
						require_once(EVA_METABOXES_PLUGIN_DIR . 'risque/risque.php');
						echo getFormulaireCreationRisque($_REQUEST['tableElement'], $_REQUEST['idElement'], $_REQUEST['idRisque']);
					break;
					case 'loadAdvancedRiskForm' :
						require_once(EVA_METABOXES_PLUGIN_DIR . 'risque/risque.php');
						echo getAvancedFormulaireCreationRisque($_REQUEST['tableElement'], $_REQUEST['idElement']);
					break;
					case 'voirRisqueUnite' :
						require_once(EVA_METABOXES_PLUGIN_DIR . 'documentUnique/documentUnique.php');
						$tableElement = $_REQUEST['tableElement'];
						$idElement = $_REQUEST['idElement'];
						echo eva_documentUnique::bilanRisque($tableElement, $idElement, 'unite');
					break;
					case 'addRiskByPicture':
					{
						$addRiskByPictureForm = '';
						$tableElement = isset($_REQUEST['tableElement']) ? (eva_tools::IsValid_Variable($_REQUEST['tableElement'])) : '';
						$idElement = isset($_REQUEST['idElement']) ? (eva_tools::IsValid_Variable($_REQUEST['idElement'])) : '';
						$currentId = isset($_REQUEST['currentId']) ? (eva_tools::IsValid_Variable($_REQUEST['currentId'])) : '';

						require_once(EVA_METABOXES_PLUGIN_DIR . 'risque/risque.php');
						echo  getFormulaireCreationRisque($tableElement, $idElement, '', $currentId);
					}
					break;
					case 'addRiskByPicture-old':
					{
						$addRiskByPictureForm = '';
						$tableElement = isset($_REQUEST['tableElement']) ? (eva_tools::IsValid_Variable($_REQUEST['tableElement'])) : '';
						$idElement = isset($_REQUEST['idElement']) ? (eva_tools::IsValid_Variable($_REQUEST['idElement'])) : '';
						$currentId = isset($_REQUEST['currentId']) ? (eva_tools::IsValid_Variable($_REQUEST['currentId'])) : '';

						{//Choix de la cat�gorie de dangers
							$categorieDanger = categorieDangers::getCategorieDangerForRiskEvaluation(NULL, $currentId);
							$script .= $categorieDanger['script'];
							$selectionCategorie = $categorieDanger['selectionCategorie'];
							$addRiskByPictureForm .= $categorieDanger['list'];
						}
						{//Choix du danger
							$ListDanger = evaDanger::getDangerForRiskEvaluation($selectionCategorie, NULL, $currentId);
							$script .= $ListDanger['script'];
							$addRiskByPictureForm .= $ListDanger['list'];
						}

						{//Choix de la m�thode
							$methodes = MethodeEvaluation::getMethods('Status="Valid"');
							$script .= '
							evarisk("#' . $currentId . 'methodeFormRisque").change(function(){
								evarisk("#' . $currentId . 'divVariablesFormRisque").html(evarisk("#loadingImg").html());
								evarisk("#' . $currentId . 'divVariablesFormRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_METHODE . '", "act":"reloadVariables", "idMethode":evarisk("#' . $currentId . 'methodeFormRisque").val(), "idRisque": "' . $idRisque . '"});
							});';
							if($risque[0] != null)
							{// Si l'on �dite un risque, on s�lectionne la bonne m�thode
								$idSelection = $risque[0]->id_methode;
							}
							else
							{// Sinon on s�lectionne la premi�re m�thode
								$idSelection = $methodes[0]->id;
							}
							$selection = MethodeEvaluation::getMethod($idSelection);
							$nombreMethode = count($methodes);
							$afficheSelecteurMethode = '';
							if($nombreMethode <= 1)
							{
								$afficheSelecteurMethode = ' display:none; ';
							}
							$addRiskByPictureForm .= '
				<div id="choixMethodeEvaluation" style="' . $afficheSelecteurMethode . '" >' . EvaDisplayInput::afficherComboBox($methodes, $currentId . 'methodeFormRisque', __('M&eacute;thode d\'&eacute;valuation', 'evarisk') . ' : ', 'methode', '', $selection) . '</div>';
						}

						{//Evaluation des variables
							$script .= '
					evarisk("#' . $currentId . 'divVariablesFormRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_METHODE . '", "act":"reloadVariables", "idMethode":evarisk("#' . $currentId . 'methodeFormRisque").val(), "idRisque": "0", "formId": "' . $currentId . '"});';
							$addRiskByPictureForm .= '
				<div id="' . $currentId . 'divVariablesFormRisque" class="clear" ></div><!-- /' . $currentId . 'divVariablesFormRisque -->';
						}

						{//Description
							$contenuInput = '';
							if($risque[0] != null)
							{// Si l'on �dite un risque, on remplit l'aire de texte avec sa description
								$contenuInput = $risque[0]->commentaire;
							}
							$labelInput = ucfirst(strtolower(sprintf(__("commentaire %s", 'evarisk'), __('sur le risque', 'evarisk'))));
							$labelInput[1] = ($labelInput[0] == "&")?ucfirst($labelInput[1]):$labelInput[1];
							$addRiskByPictureForm .= '
				<div id="' . $currentId . 'divDescription" class="clear" >' . EvaDisplayInput::afficherInput('textarea', '' . $currentId . 'descriptionFormRisque', $contenuInput, '', $labelInput . ' : ', 'description', false, DESCRIPTION_RISQUE_OBLIGATOIRE, 3, '', '', '100%', '') . '</div>';
						}

						{//Bouton enregistrer
							$allVariables = MethodeEvaluation::getAllVariables();
							$idBouttonEnregistrer = 'enregistrerFormRisque' . $currentId;
							$scriptEnregistrement = 
'<script type="text/javascript">
	evarisk(document).ready(function(){
		evarisk("#' . $idBouttonEnregistrer . '").click(function(){
			var variables = new Array();';
			foreach($allVariables as $variable)
			{
				$scriptEnregistrement .= '
			variables["' . $variable->id . '"] = evarisk("#' . $currentId . 'var' . $variable->id . 'FormRisque").val();';
			}
			$scriptEnregistrement .= '
			var historisation = true;
			var correctivActions = "";
			evarisk("#' . $currentId . 'content").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
			{
				"post":"true", 
				"table":"' . TABLE_RISQUE . '", 
				"act":"saveAdvanced", 
				"tableElement":"' . $tableElement . '", 
				"idElement":"' . $idElement . '", 
				"idDanger":evarisk("#' . $currentId . 'dangerFormRisque").val(), 
				"idMethode":evarisk("#' . $currentId . 'methodeFormRisque").val(), 
				"histo":historisation, 
				"actionsCorrectives":correctivActions, 
				"variables":variables, 
				"description":evarisk("#' . $currentId . 'descriptionFormRisque").val(), 
				"idRisque":"", 
				"currentId":"' . $currentId . '"
			});
		});
	});
</script>';
							$addRiskByPictureForm .= EvaDisplayInput::afficherInput('button', $idBouttonEnregistrer, 'Enregistrer', null, '', 'save' . $currentId, false, false, '', 'button-primary alignright saveRiskFormButton', '', '', $scriptEnregistrement);
						}

						echo $addRiskByPictureForm . '
<script type="text/javascript">
	evarisk(document).ready(function(){
		' . $script . '
	});
</script>';
					}
					break;

					case 'loadRisqMassUpdater':
					{
						require_once(EVA_METABOXES_PLUGIN_DIR . 'documentUnique/documentUnique.php');
						$tableElement = $_REQUEST['tableElement'];
						$idElement = $_REQUEST['idElement'];
						$output = '<div id="ajax-response-massUpdater" class="hide" >&nbsp;</div><div id="messageRisqMassUpdater" class="evaMessage hide fade updated" >&nbsp;</div>
<div class="massUpdaterListing" >' . eva_documentUnique::bilanRisque($tableElement, $idElement, 'ligne', 'massUpdater') . '</div>
<div class="clear alignright" ><span id="checkAllBoxMassUpdater" class="massUpdaterChecbkoxAction" >' . __('Tout cocher', 'evarisk') . '</span>&nbsp;/&nbsp;<span id="uncheckAllBoxMassUpdater" class="massUpdaterChecbkoxAction" >' . __('Tout d&eacutecocher', 'evarisk') . '</span>&nbsp;/&nbsp;<span id="reverseSelectionBoxMassUpdater" class="massUpdaterChecbkoxAction" >' . __('Inverser la s&eacute;lection', 'evarisk') . '</span><img src="' . EVA_ARROW_TOP . '" alt="arrow_top" class="checkboxRisqMassUpdaterSelector_bottom" /></div>
<div class="clear alignright risqMassUpdaterChooserExplanation" >' . __('Cochez les cases pour prendre en compte les modifications', 'evarisk') . '</div>
<div class="clear alignright" >';
	switch($tableElement)
	{
		case TABLE_GROUPEMENT:
			if(current_user_can('digi_edit_groupement') || current_user_can('digi_edit_groupement_' . $idElement))
			{
				$output .= 
	'
	<input type="button" class="button-primary" name="saveRisqMassModification" id="saveRisqMassModification" value="' . __('Enregistrer', 'evarisk') . '" />';;
			}
		break;
		case TABLE_UNITE_TRAVAIL:
			if(current_user_can('digi_edit_unite') || current_user_can('digi_edit_unite_' . $idElement))
			{
				$output .= 
	'
	<input type="button" class="button-primary" name="saveRisqMassModification" id="saveRisqMassModification" value="' . __('Enregistrer', 'evarisk') . '" />';;
			}
		break;
	}
	$output .= '
	<input type="button" class="button-secondary" name="cancelRisqMassModification" id="cancelRisqMassModification" value="' . __('Annuler', 'evarisk') . '" /> 
</div>
<script type="text/javascript" >
	evarisk("#risqMassUpdater textarea").keypress(function(){
		if(evarisk(this).hasClass("risqComment")){
			currentLineIdentifier = evarisk(this).attr("id").replace("risqComment_", "");
		}
		else if(evarisk(this).hasClass("risqPrioritaryCA")){
			currentLineIdentifier = evarisk(this).attr("name").replace("risqPrioritaryCA_", "");
		}
		evarisk("#checkboxRisqMassUpdater_" + currentLineIdentifier).prop("checked", "checked");
	});
	evarisk("#risqMassUpdater textarea").mousedown(function(){
		if(evarisk(this).hasClass("risqComment")){
			currentLineIdentifier = evarisk(this).attr("id").replace("risqComment_", "");
		}
		else if(evarisk(this).hasClass("risqPrioritaryCA")){
			currentLineIdentifier = evarisk(this).attr("name").replace("risqPrioritaryCA_", "");
		}
		evarisk("#checkboxRisqMassUpdater_" + currentLineIdentifier).prop("checked", "checked");
	});

	evarisk("#checkAllBoxMassUpdater").click(function(){
		evarisk(".checkboxRisqMassUpdater").each(function(){
			evarisk(this).prop("checked", "checked");
		});
	});
	evarisk("#uncheckAllBoxMassUpdater").click(function(){
		evarisk(".checkboxRisqMassUpdater").each(function(){
			evarisk(this).prop("checked", "");
		});
	});
	evarisk("#reverseSelectionBoxMassUpdater").click(function(){
		evarisk(".checkboxRisqMassUpdater").each(function(){
			if(evarisk(this).is(":checked")){
				evarisk(this).prop("checked", "");
			}
			else{
				evarisk(this).prop("checked", "checked");
			}
		});
	});
	evarisk("#saveRisqMassModification").click(function(){
		risqComment = "";
		risqPrioritaryAction = "";
		evarisk(".checkboxRisqMassUpdater").each(function(){
			if(evarisk(this).is(":checked")){
				var currentLineRisqIdentifier = evarisk(this).attr("id").replace("checkboxRisqMassUpdater_", "");
				var currentLinePrioritaryActionIdentifier = evarisk("#prioritaryActionMassUpdater_" + currentLineRisqIdentifier).val();
				risqComment += currentLineRisqIdentifier  + "XevaValueDelimiteRevaX" + evarisk("#risqComment_" + currentLineRisqIdentifier).val() + "XevaEntryDelimiteRevaX";
				risqPrioritaryAction += currentLinePrioritaryActionIdentifier  + "XevaValueDelimiteRevaX" + evarisk("#risqPrioritaryCA_" + currentLinePrioritaryActionIdentifier).val() + "XevaEntryDelimiteRevaX";
			}
		});

		evarisk("#ajax-response-massUpdater").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
		{
			"post":"true", 
			"table":"' . TABLE_RISQUE . '", 
			"act":"saveRisqMassUpdater",
			"risqComment":risqComment,
			"risqPrioritaryAction":risqPrioritaryAction
		});
	});
	evarisk("#cancelRisqMassModification").click(function(){
		var hasModification = false;
		evarisk(".checkboxRisqMassUpdater").each(function(){
			if(evarisk(this).is(":checked")){
				hasModification = true;
			}
		});
		if(!hasModification || (confirm(convertAccentToJS("' . __('&Ecirc;tes vous sur de vouloir annuler les modifications en cours?', 'evarisk') . '")))){
			evarisk("#risqMassUpdater").dialog("close");
		}
	});
</script>';

echo $output;
					}
					break;
					case 'saveRisqMassUpdater':
					{
						$risqComment = eva_tools::IsValid_Variable($_REQUEST['risqComment']);
						$risqPrioritaryAction = eva_tools::IsValid_Variable($_REQUEST['risqPrioritaryAction']);

						/*	Start risq comment update	*/
						$risqError = false;
						$risqCommentLines = explode("XevaEntryDelimiteRevaX", $risqComment);
						foreach($risqCommentLines as $line)
						{
							if($line != '')
							{
								$query = "";
								$risqCommentLineValue = explode("XevaValueDelimiteRevaX", $line);
								$query = $wpdb->prepare("UPDATE " . TABLE_RISQUE . " SET commentaire = %s WHERE id = %d;
", $risqCommentLineValue[1], $risqCommentLineValue[0]);
								if( !$wpdb->query($query) && $wpdb->query($query) != 0 )
								{
									$risqError = true;
								}
							}
						}
						if($risqError)
						{
							$risqErrorMessage = '<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="response" style="vertical-align:middle;" />' . __('Une ou plusieurs erreurs sont survenues lors de l\'enregistrement des corrections pour les risques.', 'evarisk');
						}
						else
						{
							$risqErrorMessage = '<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />' . __('Tous les risques ont &eacute;t&eacute; mis &agrave; jour', 'evarisk');
						}

						/*	Start risq associated prioritary action update	*/
						$risqACError = false;
						$risqPrioritaryActionLines = explode("XevaEntryDelimiteRevaX", $risqPrioritaryAction);
						foreach($risqPrioritaryActionLines as $line)
						{
							if($line != '')
							{
								$query = "";
								$risqPrioritaryActionLineValue = explode("XevaValueDelimiteRevaX", $line);
								$query = $wpdb->prepare("UPDATE " . TABLE_TACHE . " SET description = %s WHERE id = %d;
", $risqPrioritaryActionLineValue[1], $risqPrioritaryActionLineValue[0]);
								if( !$wpdb->query($query) && $wpdb->query($query) != 0 )
								{
									$risqACError = true;
								}
							}
						}
						if($risqACError)
						{
							$risqACErrorMessage = '<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="response" style="vertical-align:middle;" />' . __('Une ou plusieurs erreurs sont survenues lors de l\'enregistrement des corrections pour les actions prioritaires.', 'evarisk');
						}
						else
						{
							$risqACErrorMessage = '<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />' . __('Toutes les actions prioritaires ont &eacute;t&eacute; mise &agrave; jour', 'evarisk');
						}

						echo '
<script type="text/javascript" >
	evarisk("#uncheckAllBoxMassUpdater").click();
	actionMessageShow("#messageRisqMassUpdater", "' . addslashes($risqErrorMessage) . '<br/>' . addslashes($risqACErrorMessage) . '");
	setTimeout(\'actionMessageHide("#messageRisqMassUpdater")\',7500);
</script>';

					}
					break;
					case 'loadAssociatedTask':
					{
						$id_risque = eva_tools::IsValid_Variable($_REQUEST['idRisque']);
						$idTable = 'loadAssociatedTask' . $id_risque;
						/*	Get the different corrective actions for the actual risk	*/
						$actionsCorrectives = '';
						$taches = new EvaTaskTable();
						$tacheLike = new EvaTask();
						$tacheLike->setIdFrom($id_risque);
						$tacheLike->setTableFrom(TABLE_RISQUE);
						$taches->getTasksLike($tacheLike);
						$tachesActionsCorrectives = $taches->getTasks();
						if(count($tachesActionsCorrectives) > 0)
						{
							$hasActions = true;
							$spacer = '';
							$actionsCorrectives .= '
				<div class="hide" id="riskAssociatedTask' . $id_risque . '" title="' . __('D&eacute;tails d\'une action corrective', 'evarisk') . '" >&nbsp;</div>
				<table id="' . $idTable . '" cellspacing="0" class="widefat post fixed">
					<thead>
						<tr class="white_background" >
							<th >' . sprintf(__('Actions correctives associ&eacute;s au risque %s', 'evarisk'), ELEMENT_IDENTIFIER_R . $id_risque) . '</th>
							<th >' . __('Informations', 'evarisk') . '</th>
							<th class="CorrectivActionFollowStateActionColumn" >&nbsp;</th>
						</tr>
					</thead>
					<tbody>';
							foreach($tachesActionsCorrectives as $taskDefinition)
							{
								$monCorpsTable = '';
								$racine = Arborescence::getRacine(TABLE_TACHE, " id='" . $taskDefinition->id . "' ");

								$actionsCorrectives .= '
						<tr id="node-' . $idTable . '-' . $racine->id . '" class="parent racineArbre">
							<td id="tdRacine' . $idTable . ELEMENT_IDENTIFIER_T . $racine->id . '" class="loadAssociatedTask_elt_name" >' . ELEMENT_IDENTIFIER_T . $racine->id . '&nbsp;-&nbsp;' . $racine->nom . '</td>
							<td id="tdInfoRacine' . $idTable . ELEMENT_IDENTIFIER_T . $racine->id . '">' . $racine->avancement . '%&nbsp;(' . actionsCorrectives::check_progression_status_for_output($racine->ProgressionStatus) . ')&nbsp;-&nbsp;&nbsp;' . __('D&eacute;but', 'evarisk') . '&nbsp;' . mysql2date('d M Y', $racine->dateDebut, true) . '&nbsp;-&nbsp;' . __('Fin', 'evarisk') . '&nbsp;' . mysql2date('d M Y', $racine->dateFin, true) . '</td>
							<td id="tdActionRacine' . $idTable . ELEMENT_IDENTIFIER_T . $racine->id . '" class="CorrectivActionFollowStateActionColumn" ><img src="' . str_replace('.png', '_vs.png', PICTO_VIEW) . '" alt="view_details" id="' . TABLE_TACHE . '_t_elt_' . $racine->id . '" /></td>
						</tr>';

								$elements = Arborescence::getFils(TABLE_TACHE, $racine, "nom ASC");
								$actionsCorrectives .= EvaDisplayDesign::build_tree($elements, $racine, TABLE_TACHE, 'Info', $idTable, true);
							}
							$actionsCorrectives .= '
					</tbody>
				</table>
				<script type="text/javascript">
					evarisk(document).ready(function(){
						/*	Change the simple table in treetable	*/
						evarisk("#' . $idTable . '").treeTable();
						evarisk("#' . $idTable . ' tr.parent").each(function(){
							var childNodes = evarisk("table#' . $idTable . ' tbody tr.child-of-" + evarisk(this).attr("id"));
							if(childNodes.length > 0){
								evarisk(this).addClass("aFils");				
								var premierFils = evarisk("table#' . $idTable . ' tbody tr.child-of-" + evarisk(this).attr("id") + ":first").attr("id");
								if(premierFils != premierFils.replace(/node/g,"")){
									evarisk(this).addClass("aFilsNoeud");
								}
								else{
									evarisk(this).addClass("aFilsFeuille");
								}
							}
							else{
								evarisk(this).removeClass("aFils");
								evarisk(this).addClass("sansFils");
							}
						});

						/*	Add the dialog box in order to see correctiv action details	*/
						evarisk("#riskAssociatedTask' . $id_risque . '").dialog({
							"autoOpen":false,
							"height":400,
							"width":800,
							"modal":true,
							"buttons":{
								"' . __('fermer', 'evarisk') . '": function(){
									evarisk(this).dialog("close");
								}
							},
							"close":function(){
								evarisk("#riskAssociatedTask' . $id_risque . '").html("");
							}
						});

						/*	Add the action when user click on the 	*/
						evarisk(".CorrectivActionFollowStateActionColumn").click(function(){
							evarisk("#riskAssociatedTask' . $id_risque . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
								"table": "' . TABLE_TACHE . '",
								"id": evarisk(this).children("img").attr("id"),
								"act": "loadDetails"
							});

							evarisk("#riskAssociatedTask' . $id_risque . '").dialog("open");
						});
					});
				</script>';
						}
						else
						{
							$actionsCorrectives .= __('Aucune action n\'est associ&eacute;e &agrave; ce risque', 'evarisk');
						}
						echo $actionsCorrectives;
					}
					break;
				}
				break;
			case TABLE_METHODE:
				switch($_REQUEST['act'])
				{
					case 'reloadVariables':
					{
						$idMethode = eva_tools::IsValid_Variable($_REQUEST['idMethode']);
						$idRisque = eva_tools::IsValid_Variable($_REQUEST['idRisque']);
						$formId = (isset($_REQUEST['formId'])) ? eva_tools::IsValid_Variable($_REQUEST['formId']) : '';
						unset($valeurInitialVariables);
						if($idRisque != '')
						{	
							$risque = Risque::getRisque($idRisque);
							foreach($risque as $ligneRisque)
							{
								$valeurInitialVariables[$ligneRisque->id_variable] = $ligneRisque->valeur;
							}
						}
						$variables = MethodeEvaluation::getDistinctVariablesMethode($idMethode);
						$affichage = '';
						foreach($variables as $variable)
						{
							if(isset($valeurInitialVariables))
							{
								$valeurInitialVariable = $valeurInitialVariables[$variable->id];
							}
							else
							{
								$valeurInitialVariable = $variable->min;
							}
							{//Script de la variable
								$affichage .= '<script type="text/javascript">
									evarisk(document).ready(function() {
										evarisk("#' . $formId . 'slider-range-min' . $variable->id . '").slider({
											range: "min",
											value: ' . $valeurInitialVariable . ',
											min:	' . $variable->min . ',
											max:	' . $variable->max . ',
											slide: function(event, ui){
												evarisk("#' . $formId . 'var' . $variable->id . 'FormRisque").val(ui.value);
											}
										});
										evarisk("#' . $formId . 'var' . $variable->id . 'FormRisque").val(evarisk("#' . $formId . 'slider-range-min' . $variable->id . '").slider("value"));
									});
								</script>';
							}
							{//Affichage de la variable
								$affichage .= '
									<label for="' . $formId . 'var' . $variable->id . 'FormRisque">' . $variable->nom . ' :</label>
									<input type="text" class="sliderValue" disabled="disabled" id="' . $formId . 'var' . $variable->id . 'FormRisque" name="' . $formId . 'variables[]" />
									<div id="' . $formId . 'slider-range-min' . $variable->id . '" class="slider_variable"></div>';
							}
						}

						/*	START - Get the explanation picture if exist - START	*/
						$methodExplanationPicture = '';
						$defaultPicture = evaPhoto::getMainPhoto(TABLE_METHODE, $idMethode);
						if(($defaultPicture != '') && (is_file(EVA_GENERATED_DOC_DIR . $defaultPicture)))
						{
							$methodExplanationPicture = '<img src="' . EVA_GENERATED_DOC_URL . $defaultPicture . '" alt="" style="width:100%;" />';
						}
						/*	END - Get the explanation picture if exist - END	*/

						/*	START - Check if there are task to associate */
						$tachesAssociees = array();
						if($idRisque > '0')
						{
							//On r�cup�re les actions relatives � l'�l�ment de provenance.
							$tachesSoldees = new EvaTaskTable();
							$tacheLike = new EvaTask();
							$tacheLike->setIdFrom($idRisque);
							$tacheLike->setTableFrom(TABLE_RISQUE);
							if(digirisk_options::getOptionValue('affecter_uniquement_tache_soldee_a_un_risque') == 'oui')
							{
								$tacheLike->setProgressionStatus("'Done', 'DoneByChief'");
							}
							$tachesSoldees->getTasksLike($tacheLike);
							$tachesAssociees = $tachesSoldees->getTasks();
							
							/*	Check if there are actions that are not done to alert the user when he will change the risk level	*/
							$tachesNonSoldees = new EvaTaskTable();
							$tacheNonSoldeeLike = new EvaTask();
							$tacheNonSoldeeLike->setIdFrom($idRisque);
							$tacheNonSoldeeLike->setTableFrom(TABLE_RISQUE);
							$tacheNonSoldeeLike->setProgressionStatus("'inProgress'");
							$tachesNonSoldees->getTasksLike($tacheNonSoldeeLike);
							$tachesAssocieesNonSoldees = $tachesNonSoldees->getTasks();
						}
						/*	END - Check if there are task to associate */

						$rightContainer = '';
						if((count($tachesAssociees) < 1) && (count($tachesAssocieesNonSoldees) < 1))
						{
							$rightContainer = $methodExplanationPicture;
						}
						else
						{
							$taskToMatch = '';
							if(is_array($tachesAssociees))
							{
								$taskToMatch .= '<table summary="" cellpadding="0" cellspacing="0" >';
								foreach($tachesAssociees as $tache)
								{
									$taskToMatch .= '<tr><td><input type="checkbox" class="acLinkRisksChecbox" name="associatedTask" id="associatedTask' . $tache->id . '" value="' . $tache->id . '" /></td><td style="padding:6px;" ><label for="associatedTask' . $tache->id . '" >' . $tache->name . '</label></td></tr>';
								}
								$taskToMatch .= '</table>';
							}

							if(is_array($tachesAssocieesNonSoldees) && (digirisk_options::getOptionValue('affecter_uniquement_tache_soldee_a_un_risque') == 'oui'))
							{
								$taskToMatch .= '<div style="text-align:justify;color:red;" >' . __('Des actions correctives sont actuellement en cours et ne sont pas sold&eacute;es. Si vous voulez lier la modification de ce risque &agrave; une de ces actions corrective, vous devez d\'abord solder celles-ci.', 'evarisk') . '</div>';
							}

							$rightContainer = 
								'<div id="moreRiskAction" >
									<ul>
										<li><a href="#correctivActionTab">' . __('Actions correctives', 'evarisk') . '</a></li>
										<li><a href="#explanationTab">' . __('Explications', 'evarisk') . '</a></li>
									</ul>
									<div id="correctivActionTab" >' . __('Si la r&eacute;-&eacute;valuation du risque d&eacute;coule d\'une action corrective. Vous pouvez associer ces &eacute;l&eacute;ments.', 'evarisk') . '<hr/>' . $taskToMatch  . '</div>
									<div id="explanationTab" >' . $methodExplanationPicture . '</div>
								</div>
								<script type="text/javascript" >
									evarisk(document).ready(function(){
										evarisk("#moreRiskAction").tabs();
									})
								</script>';
						}

						echo '<div class="alignleft" style="width:30%;" >' . $affichage . '</div><div class="alignright" style="width:70%;" >' . $rightContainer . '</div>';
					}
					break;
					case 'reloadVariables-FAC':
					{
						$idRisque = eva_tools::IsValid_Variable($_REQUEST['idRisque']);
						unset($valeurInitialVariables);
						if($idRisque != '')
						{	
							$risque = Risque::getRisque($idRisque);
							foreach($risque as $ligneRisque)
							{
								$idMethode = $ligneRisque->id_methode;
								$valeurInitialVariables[$ligneRisque->id_variable] = $ligneRisque->valeur;
							}
						}
						$variables = MethodeEvaluation::getDistinctVariablesMethode($idMethode);

						$affichage = '';
						foreach($variables as $variable)
						{
							if(isset($valeurInitialVariables))
							{
								$valeurInitialVariable = $valeurInitialVariables[$variable->id];
							}
							else
							{
								$valeurInitialVariable = $variable->min;
							}
							{//Script de la variable
								$affichage .= '<script type="text/javascript">
									evarisk(document).ready(function(){
										evarisk("#slider-range-min-FAC' . $variable->id . '").slider({
											range: "min",
											value: ' . $valeurInitialVariable . ',
											min:	' . $variable->min . ',
											max:	' . $variable->max . ',
											slide: function(event, ui){
												evarisk("#var' . $variable->id . 'FormRisque-FAC").val(ui.value);
											}
										});
										evarisk("#var' . $variable->id . 'FormRisque-FAC").val(evarisk("#slider-range-min-FAC' . $variable->id . '").slider("value"));
									});
								</script>';
							}
							{//Affichage de la variable
								$affichage .= '
									<span id="plusVar' . $variable->id . 'FormRisque-FAC" class="plusVariable"><label for="var' . $variable->id . 'FormRisque-FAC">' . $variable->nom . ' :</label></span>
									<input type="text" class="sliderValue" disabled="disabled" id="var' . $variable->id . 'FormRisque-FAC" name="variables[]" />
									<div id="slider-range-min-FAC' . $variable->id . '" class="slider_variable"></div>';
							}
						}

						/*	START - Get the explanation picture if exist - START	*/
						$methodExplanationPicture = '';
						$defaultPicture = evaPhoto::getMainPhoto(TABLE_METHODE, $idMethode);
						if(($defaultPicture != '') && (is_file(EVA_GENERATED_DOC_DIR . $defaultPicture)))
						{
							$methodExplanationPicture = '<img src="' . EVA_GENERATED_DOC_URL . $defaultPicture . '" alt="" style="width:100%;" />';
						}
						/*	END - Get the explanation picture if exist - END	*/

						$rightContainer = $methodExplanationPicture;

						echo '<div class="alignleft" style="width:30%;" >' . $affichage . '</div><div class="alignright" style="width:70%;" >' . $rightContainer . '</div>';
					}
					break;
				}
			break;
			case TABLE_GROUPE_QUESTION:
				switch($_REQUEST['act'])
				{
					case 'transfert':
					{
						$fils = $_REQUEST['idElementSrc'];
						$pere = $_REQUEST['idElementDest'];
						$pereOriginel = $_REQUEST['idElementOrigine'];
						$idPere = str_replace('node-' . $_REQUEST['location'] . '-','', $pere);
						$idFils = (string)((int) str_replace('node-' . $_REQUEST['location'] . '-','', $fils));
						$idPereOriginel = str_replace('node-' . $_REQUEST['location'] . '-','', $pereOriginel);
						if($idFils == str_replace('node-' . $_REQUEST['location'] . '-','', $fils))
						//Le fils est un groupe de questions
						{
							$idFils = str_replace('node-' . $_REQUEST['location'] . '-','', $fils);
							$groupeQuestion = evaGroupeQuestions::getGroupeQuestions($idFils);
							$pereActu = Arborescence::getPere($_REQUEST['nom'], $groupeQuestion);
							if($pereActu->id != $idPere)
							{
								$_REQUEST['act'] = 'update';
								$_REQUEST['id'] = $groupeQuestion->id;
								$_REQUEST['nom'] = $groupeQuestion->nom;
								$_REQUEST['code'] = $groupeQuestion->code;
								$_REQUEST['idPere'] = $idPere;
								require_once(EVA_METABOXES_PLUGIN_DIR . 'veilleReglementaire/groupeQuestionPersistance.php');
							}
						}
						else
						//Le fils est une question
						{
							$idFils = str_replace('leaf-','', $fils);
							evaQuestion::transfertQuestion($idFils, $idPere, $idPereOriginel);
						}
					}
					break;
					case 'save':
					{
						switch($_REQUEST['choix'])
						{
							case 'titre':
								if($_REQUEST['nom'] != null and $_REQUEST['nom'] != '')
								{
									$retourALaLigne = array("\r\n", "\n", "\r");
									$_REQUEST['nom'] = str_replace($retourALaLigne, "[retourALaLigne]",$_REQUEST['nom']);
									$_REQUEST['extrait'] = null;
									require_once(EVA_METABOXES_PLUGIN_DIR . 'veilleReglementaire/groupeQuestionPersistance.php');
								}
								break;
						}
					}
					break;
					case 'update':
					{
						switch($_REQUEST['choix'])
						{
							case 'titre':
								if($_REQUEST['nom'] != null and $_REQUEST['nom'] != '')
								{ 
									$retourALaLigne = array("\r\n", "\n", "\r");
									$_REQUEST['nom'] = str_replace($retourALaLigne, "[retourALaLigne]",$_REQUEST['nom']);
									$temp = EvaGroupeQuestions::getGroupeQuestions($_REQUEST['id']);
									$temp = Arborescence::getPere($_REQUEST['table'],$temp);
									$_REQUEST['idPere'] = $temp->id;
									require_once(EVA_METABOXES_PLUGIN_DIR . 'veilleReglementaire/groupeQuestionPersistance.php');
								}
								break;
						}
					}
					break;
					case 'addExtrait' :
						$groupeQuestion = EvaGroupeQuestions::getGroupeQuestions($_REQUEST['idGroupeQuestion']);
						if($groupeQuestion->extraitTexte != null AND $groupeQuestion->extraitTexte != '')
						{
							$extraitActuel = $groupeQuestion->extraitTexte . "
							";
						}
					case 'replaceExtrait' :
						if(!isset($extraitActuel))
						{
							$extraitActuel = '';
						}
						$retourALaLigne = array("\r\n", "\n", "\r");
						$_REQUEST['extrait'] = str_replace($retourALaLigne, "[retourALaLigne]",$extraitActuel . $_REQUEST['extrait']);
						$_REQUEST['act'] = "addExtrait";
						unset($extraitActuel);
						require_once(EVA_METABOXES_PLUGIN_DIR . 'veilleReglementaire/groupeQuestionPersistance.php');
					break;
					case 'reloadCombo':
						$nomRacine = (isset($_REQUEST['nomRacine']) && (trim($_REQUEST['nomRacine']) != '') && (is_string($_REQUEST['nomRacine']))) ? eva_tools::IsValid_Variable($_REQUEST['nomRacine']) : '';
						if($nomRacine != '')
						{
							$query = $wpdb->prepare("SELECT * FROM " . TABLE_GROUPE_QUESTION . " WHERE nom = %s", $nomRacine);
							$racine = $wpdb->get_row($query);
							$valeurDefaut = $racine->nom;
							$selection = $_REQUEST['selection'];
							echo evaDisplayInput::afficherComboBoxArborescente($racine, TABLE_GROUPE_QUESTION, $_REQUEST['idSelect'], $_REQUEST['labelSelect'], $_REQUEST['nameSelect'], $valeurDefaut, $selection);
						}
						else
						{
							echo __('Vous devez sp&eacute;cifier un nom', 'evarisk');
						}
					break;
					case 'reloadTableArborescente':
						$racine = EvaGroupeQuestions::getGroupeQuestions($_REQUEST['idRacine']);
						$nomRacine = $_REQUEST['nomRacine'];
						$idTable = $_REQUEST['idTable'];
						echo evaDisplayDesign::getTableArborescence($racine, $_REQUEST['table'], $idTable, $nomRacine);
					break;
				}
				break;
			case TABLE_QUESTION:
				switch($_REQUEST['act'])
				{
					case 'edit':
					case 'save':
						$_REQUEST['code'] = '';
						$retourALaLigne = array("\r\n", "\n", "\r");
						$_REQUEST['enonce'] = str_replace($retourALaLigne, "[retourALaLigne]",$_REQUEST['enonce']);
						require_once(EVA_METABOXES_PLUGIN_DIR . 'veilleReglementaire/questionPersistance.php');
					break;
				}
				break;
			case TABLE_TACHE:
				switch($_REQUEST['act'])
				{
					case 'transfert':
					{
						$fils = $_REQUEST['idElementSrc'];
						$pere = $_REQUEST['idElementDest'];
						$idPere = str_replace('node-' . $_REQUEST['location'] . '-','', $pere);
						$idOrigine = str_replace('node-' . $_REQUEST['location'] . '-','', $_REQUEST['idElementOrigine']);
						$idFils = (string)((int) str_replace('node-' . $_REQUEST['location'] . '-','', $fils));
						if($idFils == str_replace('node-' . $_REQUEST['location'] . '-','', $fils)) //Le fils est une t�che
						{
							$tache = new EvaTask($idFils);
							$tache->load();
							$tache->transfert($idPere);
						}
						else //Le fils est une activit�
						{
							$idFils = str_replace('leaf-','', $fils);
							$activite = new EvaActivity($idFils);
							$activite->load();
							$activite->transfert($idPere);

							/*	Update the action ancestor	*/
							$relatedTask = new EvaTask($idPere);
							$relatedTask->load();
							$relatedTask->computeProgression();
							$relatedTask->save();
							unset($relatedTask);

							/*	Update the action ancestor	*/
							$relatedTask = new EvaTask($idOrigine);
							$relatedTask->load();
							$relatedTask->computeProgression();
							$relatedTask->save();
						}
					}
					break;
					case 'updateProvenance':
					{
						$id = eva_tools::IsValid_Variable($_REQUEST['id']);
						$provenance = eva_tools::IsValid_Variable($_REQUEST['provenance']);
						$provenanceComponents = explode('_-_', $provenance);

						$tache = new EvaTask($id);
						$tache->load();
						$tache->setIdFrom($provenanceComponents[1]);
						$tache->setTableFrom($provenanceComponents[0]);
						$tache->save();

						if($tache->getStatus() != 'error')
						{
							$updateMessage = 'evarisk("#messageh' . $_REQUEST['table'] . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('L\'affectation de la t&acirc;che a correctement &eacute;t&eacute; effectu&eacute;e', 'evarisk') . '</strong></p>') . '");';

							switch($provenanceComponents[0])
							{
								case TABLE_RISQUE:
									/*	Make the link between a corrective action and a risk evaluation	*/
									$query = 
										$wpdb->prepare(
											"SELECT id_evaluation 
											FROM " . TABLE_AVOIR_VALEUR . " 
											WHERE id_risque = '%d' 
												AND Status = 'Valid' 
											ORDER BY id DESC 
											LIMIT 1", 
											$provenanceComponents[1]
										);
									$evaluation = $wpdb->get_row($query);
									$provenanceComponents[0] = TABLE_AVOIR_VALEUR;
									$provenanceComponents[1] = $evaluation->id_evaluation;
								break;
							}
							evaTask::liaisonTacheElement($provenanceComponents[0], $provenanceComponents[1], $id, 'before');
						}
						else
						{
							$updateMessage = 'evarisk("#messageh' . $_REQUEST['table'] . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('L\'affectation de la t&acirc;che n\'a pas &eacute;t&eacute; effectu&eacute;e.', 'evarisk') . '</strong></p>') . '");';
						}
						
						$messageInfo =
							'<script type="text/javascript">
								evarisk(document).ready(function(){
									evarisk("#savingLinkTaskElement").html("");
									evarisk("#savingLinkTaskElement").hide();
									evarisk("#saveLinkTaskElement").show();
									evarisk("#messageh' . $_REQUEST['table'] . '").addClass("updated");
									' . $updateMessage . '
									evarisk("#messageh' . $_REQUEST['table'] . '").show();
									setTimeout(function(){
										evarisk("#messageh' . $_REQUEST['table'] . '").removeClass("updated");
										evarisk("#messageh' . $_REQUEST['table'] . '").hide();
									},7500);
								});
							</script>';
						echo $messageInfo;
					}
					break;
					case 'save':
					case 'update':
					case 'taskDone':
					{
						global $wpdb;
						switch($_REQUEST['act'])
						{
							case 'save':
								$action = __('sauvegard&eacute;e', 'evarisk');
								break;
							case 'update':
								$action = __('mise &agrave; jour', 'evarisk');
								break;
							case 'taskDone':
								$action = __('sold&eacute;e', 'evarisk');
								break;
						}	
						$tache = new EvaTask($_REQUEST['id']);
						$tache->load();
						$tache->setName($_REQUEST['nom_tache']);
						$tache->setDescription($_REQUEST['description']);
						$tache->setIdFrom($_REQUEST['idProvenance']);
						$tache->setTableFrom($_REQUEST['tableProvenance']);
						$tache->setProgressionStatus('notStarted');
						if(($_REQUEST['avancement'] > '0') || ($tache->getProgressionStatus() == 'inProgress'))
						{
							$tache->setProgressionStatus('inProgress');
						}
						$tache->setidResponsable($_REQUEST['responsable_tache']);
						if($_REQUEST['act'] == 'taskDone')
						{
							global $current_user;
							$tache->setidSoldeur($current_user->ID);
							$tache->setProgression($_REQUEST['avancement']);
							$tache->setStartDate($_REQUEST['date_fin']);
							$tache->setFinishDate($_REQUEST['date_debut']);
							$tache->setProgressionStatus('Done');
							$tache->setdateSolde(date('Y-m-d H:i:s'));

							/*	Get the task subelement to set the progression status to DoneByChief	*/
							if($_REQUEST['markAllSubElementAsDone'] == 'true')
							{
								$tache->markAllSubElementAsDone($_REQUEST['avancement'], $_REQUEST['date_fin'], $_REQUEST['date_debut']);
							}
						}
						if($tache->getLeftLimit() == 0)
						{
							$racine = new EvaTask(1);
							$racine->load();
							$tache->setLeftLimit($racine->getRightLimit());
							$tache->setRightLimit(($racine->getRightLimit()) + 1);
							$racine->setRightLimit(($racine->getRightLimit()) + 2);
							$racine->save();
						}
						if($_REQUEST['act'] != 'taskDone')
						{
							$tache->computeProgression();
						}
						$tache->save();
						$tacheMere = new EvaTask();
						$tacheMere->convertWpdb(Arborescence::getPere(TABLE_TACHE, $tache->convertToWpdb()));
						if($_REQUEST['idPere'] != $tacheMere->getId())
						{
							$tache->transfert($_REQUEST['idPere']);
						}
						$messageInfo = '<script type="text/javascript">
								evarisk(document).ready(function(){';
						if($tache->getStatus() != 'error')
						{
							$messageInfo = $messageInfo . '
									actionMessageShow("#message", "' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s a correctement &eacute;t&eacute; %s', 'evarisk') . '</strong></p>', __('de la t&acirc;che', 'evarisk') . ' "' . stripslashes($_REQUEST['nom_tache']) . '"', $action)) . '");
									setTimeout(\'actionMessageHide("#message")\',7500);';
						}
						else
						{
							$messageInfo = $messageInfo . '
									actionMessageShow("#message", "' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s n\'a pas &eacute;t&eacute; %s.', 'evarisk') . '</strong></p>', __('de la t&acirc;che', 'evarisk') . ' "' . stripslashes($_REQUEST['nom_tache']) . '"', $action)) . '");
									setTimeout(\'actionMessageHide("#message")\',7500);';
						}
						$tache->load();
						$messageInfo .= '
									evarisk("#partieEdition").html(evarisk("#loadingImg").html());
									if("' . $_REQUEST['affichage'] . '" == "affichageTable")
									{
										if(evarisk("#filAriane :last-child").is("label"))
											evarisk("#filAriane :last-child").remove();
										evarisk("#filAriane :last-child").after(\'<label>&nbsp;&raquo;&nbsp;&Eacute;dition&nbsp;de&nbsp;' . $_REQUEST['nom_tache'] . '</label>\');
										evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
											"table": "' . TABLE_TACHE . '",
											"id": "' . $tache->getId() . '",
											"page": evarisk("#pagemainPostBoxReference").val(),
											"idPere": evarisk("#identifiantActuellemainPostBox").val(),
											"act": "edit",
											"partie": "right",
											"menu": evarisk("#menu").val(),
											"affichage": "affichageTable",
											"partition": "tout"
										});
									}
									else
									{
										var expanded = new Array();
										evarisk(".expanded").each(function(){expanded.push(evarisk(this).attr("id"));});
										evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
											"table": "' . TABLE_TACHE . '",
											"act": "edit",
											"id": "' . $tache->getId() . '",
											"partie": "right",
											"menu": evarisk("#menu").val(),
											"affichage": "affichageListe",
											"expanded": expanded
										});
										evarisk("#partieGauche").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
										{
											"post": "true", 
											"table": "' . TABLE_TACHE . '",
											"act": "edit",
											"id": "' . $tache->getId() . '",
											"partie": "left",
											"menu": evarisk("#menu").val(),
											"affichage": "affichageListe",
											"expanded": expanded
										});
									}
								});
							</script>';
						echo $messageInfo;
					}
					break;
					case 'delete':
					{
						$tache = new EvaTask($_REQUEST['id']);
						$tache->load();
						$tache->setStatus('Deleted');
						$tache->save();

						$messageInfo = '<script type="text/javascript">';
						if($tache->getStatus() != 'error')
						{
							$messageInfo = $messageInfo . '
								evarisk(document).ready(function(){
									evarisk("#message").addClass("updated");
									evarisk("#message").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s a correctement &eacute;t&eacute; supprim&eacute;e', 'evarisk') . '</strong></p>', __('de la t&acirc;che', 'evarisk') . ' "' . stripslashes($tache->getName()) . '"')) . '");
									evarisk("#message").show();
									setTimeout(function(){
										evarisk("#message").removeClass("updated");
										evarisk("#message").hide();
									},7500);';
						}
						else
						{
							$messageInfo = $messageInfo . '
								evarisk(document).ready(function(){
									evarisk("#message").addClass("updated");
									evarisk("#message").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s n\'a pas &eacute;t&eacute; supprim&eacute;e', 'evarisk') . '</strong></p>', __('de la t&acirc;che', 'evarisk') . ' "' . stripslashes($tache->getName()) . '"')) . '");
									evarisk("#message").show();
									setTimeout(function(){
										evarisk("#message").removeClass("updated");
										evarisk("#message").hide();
									},7500);';
						}
						$messageInfo = $messageInfo . '
									evarisk("#rightEnlarging").show();
									evarisk("#equilize").click();
									if("' . $_REQUEST['affichage'] . '" == "affichageTable")
									{
										if(evarisk("#filAriane :last-child").is("label"))
											evarisk("#filAriane :last-child").remove();
										evarisk("#filAriane :last-child").after("<label>&nbsp;&raquo;&nbsp;&Eacute;dition&nbsp;de&nbsp;' . $_REQUEST['nom_tache'] . '</label>");
										evarisk("#partieEdition").html("");
										evarisk("#partieGauche").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
											"table": "' . TABLE_TACHE . '",
											"id": "' . $tache->getId() . '",
											"page": evarisk("#pagemainPostBoxReference").val(),
											"idPere": evarisk("#identifiantActuellemainPostBox").val(),
											"act": "edit",
											"partie": "left",
											"menu": evarisk("#menu").val(),
											"affichage": "affichageTable",
											"partition": "tout"
										});
									}
									else
									{
										var expanded = new Array();
										evarisk(".expanded").each(function(){expanded.push(evarisk(this).attr("id"));});
										evarisk("#partieEdition").html("");
										evarisk("#partieGauche").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
											"table": "' . TABLE_TACHE . '",
											"act": "edit",
											"id": "' . $tache->getId() . '",
											"partie": "left",
											"menu": evarisk("#menu").val(),
											"affichage": "affichageListe",
											"expanded": expanded
										});
									}
								});
							</script>';
						echo $messageInfo;
					}
					break;
					case 'exportTask':
					{
						$id = eva_tools::IsValid_Variable($_REQUEST['id']);

						$existingPreconisation = '';
						$tache = new EvaTask($id);
						$tache->load();
						$TasksAndSubTasks = $tache->getDescendants();
						$TasksAndSubTasks->addTask($tache);
						$TasksAndSubTasks = $TasksAndSubTasks->getTasks();
						if($TasksAndSubTasks != null AND count($TasksAndSubTasks) > 0)
						{
							foreach($TasksAndSubTasks as $task)
							{
								if($task->id != $tache->id)
								{
									$existingPreconisation .= '* ' . $task->name;
									if($task->description != '')
									{
										$existingPreconisation .= '(' . $task->description . ')';
									}
									$existingPreconisation .= " 
";
								}
								$activities = $task->getActivitiesDependOn();
								$activities = $activities->getActivities();
								if(($activities != null) AND (count($activities) > 0))
								{
									foreach($activities as $activity)
									{
										$existingPreconisation .= '* ' . $activity->name;
										if($activity->description != '')
										{
											$existingPreconisation .= '(' . $activity->description . ')';
										}
										$existingPreconisation .= " 
";
									}
								}
							}
						}

						$dirToSaveExportedFile = EVA_UPLOADS_PLUGIN_DIR . $_REQUEST['table'];
						if(!is_dir($dirToSaveExportedFile))
						{
							eva_tools::make_recursiv_dir($dirToSaveExportedFile);
							eva_tools::changeAccesAuthorisation($dirToSaveExportedFile);
						}
						file_put_contents($dirToSaveExportedFile . '/taskExport.txt' ,$existingPreconisation);
						if(is_file($dirToSaveExportedFile . '/taskExport.txt')){
							echo '<a href="' . str_replace(EVA_UPLOADS_PLUGIN_DIR, EVA_UPLOADS_PLUGIN_URL, $dirToSaveExportedFile) . '/taskExport.txt" title="' . __('Pour le t&eacute;l&eacute;charger, faites un clic droit puis enregistrer sous', 'evarisk') . '" >' . __('T&eacute;l&eacute;charger le fichier g&eacute;n&eacute;r&eacute;', 'evarisk') . '</a>';
						}
					}
					break;
					case 'actualiseProgressionInTree':
					{
						$id = eva_tools::IsValid_Variable($_REQUEST['id']);
						$tache = new EvaTask($id);
						$tache->load();
						$statutProgression = '';
						switch($tache->getProgressionStatus())
						{
							case 'notStarted';
								$statutProgression = __('Non commenc&eacute;e', 'evarisk');
							break;
							case 'inProgress';
								$statutProgression = __('En cours', 'evarisk');
							break;
							case 'Done';
							case 'DoneByChief';
								$statutProgression = __('Sold&eacute;e', 'evarisk');
							break;
						}
						echo $tache->getProgression() . '%&nbsp;(' . $statutProgression . ')';
					}
					break;
					case 'loadDetails':
					{
						$output = '';
						$id = eva_tools::IsValid_Variable($_REQUEST['id']);
						$element_identifier = explode('_t_elt_', $id);

						/*	On r�cup�re les utilisateurs d�j� affect�s � l'�l�ment en cours.	*/
						$linkedUser = '';
						$utilisateursLies = evaUserLinkElement::getAffectedUser($element_identifier[0], $element_identifier[1]);
						if(is_array($utilisateursLies ) && (count($utilisateursLies) > 0)){
							foreach($utilisateursLies as $utilisateur){
								$currentUser = evaUser::getUserInformation($utilisateur->id_user);
								$linkedUser .= '<div class="correctiveActionSelecteduserOP" id="affectedUser' . $tableElement . $utilisateur->id_user . '" title="' . __('Cliquez pour supprimer', 'evarisk') . '" >' . ELEMENT_IDENTIFIER_U . $utilisateur->id_user . '&nbsp;-&nbsp;' . $currentUser[$utilisateur->id_user]['user_lastname'] . ' ' . $currentUser[$utilisateur->id_user]['user_firstname'] . '</div>';
							}
						}
						else{
							$linkedUser = __('Aucun utilisateur affect&eacute;', 'evarisk');
						}

						$description = $name = '';
						switch($element_identifier[0])
						{
							case TABLE_TACHE:
							{
								$postId = $element_identifier[1];
								$tache = new EvaTask($postId);
								$tache->load();
								$name = html_entity_decode($tache->getName(), ENT_NOQUOTES, 'UTF-8');
								$description = $tache->getDescription();
								$contenuInputResponsable = $tache->getidResponsable();
								$contenuInputRealisateur = $tache->getidSoldeur();
								$ProgressionStatus = $tache->getProgressionStatus();
								$startDate = $tache->getStartDate();
								$endDate = $tache->getFinishDate();
								if(($startDate != '') && ($endDate != '') && ($startDate != '0000-00-00') && ($endDate != '0000-00-00')){
									$date = '
	<tr>
		<td colspan="2" >' . __('D&eacute;but', 'evarisk') . '&nbsp;' . mysql2date('d M Y', $startDate, true) . '&nbsp;-&nbsp;' . __('Fin', 'evarisk') . '&nbsp;' . mysql2date('d M Y', $endDate, true) . '&nbsp;<span style="font-size:9px;" >(' . __('Ces dates sont calcul&eacute;es en fonction de sous-t&acirc;ches', 'evarisk') . ')</span></td>
	</tr>';
								}
								$moreInfos .= '
	<tr>
		<td class="correctivActionDetailsFieldName" >' . __('&Eacute;valuation du risque associ&eacute;', 'evarisk') . '</td>
	</tr>
	<tr>
		<td colspan="2">' . Risque::getTableQuotationRisqueAvantApresAC($element_identifier[0], $element_identifier[1], $tache, 'correctivActionFollow') . '</td>
	</tr>';
							}
							break;
							case TABLE_ACTIVITE:
							{
								$postId = $element_identifier[1];
								$tache = new EvaActivity($postId);
								$tache->load();
								$name = html_entity_decode($tache->getName(), ENT_NOQUOTES, 'UTF-8');
								$description = $tache->getDescription();
								$contenuInputResponsable = $tache->getidResponsable();
								$contenuInputRealisateur = $tache->getidSoldeur();
								$ProgressionStatus = $tache->getProgressionStatus();
								$startDate = $tache->getStartDate();
								$endDate = $tache->getFinishDate();
								if(($startDate != '') && ($endDate != '') && ($startDate != '0000-00-00') && ($endDate != '0000-00-00')){
									$date = '
	<tr>
		<td colspan="2" >' . __('D&eacute;but', 'evarisk') . '&nbsp;' . mysql2date('d M Y', $startDate, true) . '&nbsp;-&nbsp;' . __('Fin', 'evarisk') . '&nbsp;' . mysql2date('d M Y', $endDate, true) . '</td>
	</tr>';
								}
								$query = $wpdb->prepare(
									"SELECT photo
									FROM " . TABLE_PHOTO . " AS P
										INNER JOIN " . TABLE_ACTIVITE . " AS A ON (A.idPhotoAvant = P.id)
									WHERE A.id = '%s' ", 
									$postId
								);
								$pictureBefore = $wpdb->get_var($query);
								$pictureBeforeOutput = __('Aucune photo d&eacute;finie', 'evarisk');
								if($pictureBefore != ''){
									$pictureBeforeOutput = '<img src="' . EVA_GENERATED_DOC_URL . $pictureBefore . '" alt="picture before corrective action" style="width:40%;" />';
								}
								$query = $wpdb->prepare(
									"SELECT photo
									FROM " . TABLE_PHOTO . " AS P
										INNER JOIN " . TABLE_ACTIVITE . " AS A ON (A.idPhotoApres = P.id)
									WHERE A.id = '%s' ", 
									$postId
								);
								$pictureAfter = $wpdb->get_var($query);
								$pictureAfterOutput = __('Aucune photo d&eacute;finie', 'evarisk');
								if($pictureAfter != ''){
									$pictureAfterOutput = '<img src="' . EVA_GENERATED_DOC_URL . $pictureAfter . '" alt="picture before corrective action" style="width:40%;" />';
								}

								$moreInfos .= '
	<tr>
		<td class="correctivActionDetailsFieldName" >' . __('Photos', 'evarisk') . '</td>
	</tr>
	<tr>
		<td colspan="2">
			<table summary="correctiv action picture" cellpadding="0" cellspacing="0" id="correctionActionPictureTable" >
				<tr>
					<th>' . __('Photo avant', 'evarisk') . '</td>
					<th>' . __('Photo apr&egraves', 'evarisk') . '</td>
				</tr>
				<tr>
					<td class="correctivActionPicture" >' . $pictureBeforeOutput . '</td>
					<td class="correctivActionPicture" >' . $pictureAfterOutput . '</td>
				</tr>
			</table>
		</td>
	</tr>';
							}
							break;
						}

								$output .= '
<table summary="task details" cellpadding="0" cellspacing="0" >' . $date . '
	<tr>
		<td class="correctivActionDetailsFieldName" >' . __("Nom", 'evarisk') . '</td>
		<td>' . $name . '</td>
	</tr>
	<tr>
		<td class="correctivActionDetailsFieldName" >' . __("Description", 'evarisk') . '</td>
		<td>' . $description . '</td>
	</tr>
	<tr>
		<td class="correctivActionDetailsFieldName" >' . __('Utilisateurs affect&eacute;s', 'evarisk') . '</td>
		<td>' . $linkedUser . '</td>
	</tr>
	<tr>
		<td class="correctivActionDetailsFieldName" >' . __('Suivi de l\'action corrective', 'evarisk') . '</td>';
									$suivi = suivi_activite::tableauSuiviActivite($element_identifier[0], $element_identifier[1]);
									if(trim($suivi) == ''){
										$output .= '
		<td >' . __('Aucun suivi pour cette action', 'evarisk');
									}
									else{
										$output .= '
	</tr>
	<tr>
		<td colspan="2" class="correctiveActionFollow" >' . $suivi;
									}
									$output .= 
		'</td>
	</tr>' . $moreInfos . '
</table>';

						echo $output;
					}
					break;
					case 'closeTask':
					{
						$output = '';
						$tache = new EvaTask($_REQUEST['id']);
						$tache->load();
						$contenuInputTitre = html_entity_decode($tache->getName(), ENT_NOQUOTES, 'UTF-8');
						$contenuInputDescription = $tache->getDescription();
						$idProvenance = $tache->getIdFrom();
						$tableProvenance = $tache->getTableFrom();
						$contenuInputResponsable = $tache->getidResponsable();
						$contenuInputRealisateur = $tache->getidSoldeur();
						$ProgressionStatus = $tache->getProgressionStatus();
						$progression = $tache->getProgression();
						$startDate = $tache->getStartDate();
						$endDate = $tache->getFinishDate();
						{//Date de d�but de l'action
							$contenuAideTitre = "";
							$id = "date_debut";
							$label = '<label for="' . $id . '" >' . ucfirst(sprintf(__("Date de d&eacute;but %s", 'evarisk'), __("de la t&acirc;che",'evarisk'))) . '</label> : <span class="fieldInfo pointer" id="putTodayActionStart" >' . __('Aujourd\'hui', 'evarisk') . '</span>';
							$labelInput = '';
							$nomChamps = "date_debut";
							$output .= $label . EvaDisplayInput::afficherInput('text', $id, $startDate, $contenuAideTitre, $labelInput, $nomChamps, $grise, true, 255, '', 'date') . '';
						}
						{//Date de d�but de l'action
							$contenuAideTitre = "";
							$id = "date_fin";
							$label = '<label for="' . $id . '" >' . ucfirst(sprintf(__("Date de fin %s", 'evarisk'), __("de la t&acirc;che",'evarisk'))) . '</label> : <span class="fieldInfo pointer" id="putTodayActionEnd" >' . __('Aujourd\'hui', 'evarisk') . '</span>';
							$labelInput = '';
							$nomChamps = "date_fin";
							$output .= $label . EvaDisplayInput::afficherInput('text', $id, $endDate, $contenuAideTitre, $labelInput, $nomChamps, $grise, true, 255, '', 'date') . '';
						}
						{//Avancement
							$contenuAideDescription = "";
							$labelInput = __("Avancement", 'evarisk') . ' : ';
							$id = "avancement";
							$nomChamps = "avancement";
							$output .= EvaDisplayInput::afficherInput('text', $id, $progression, $contenuAideDescription, $labelInput, $nomChamps, true, true, 3, '', 'number', '10%') . '<div id="sliderAvancement" ></div>';
						}
						{//Mark all sub element as done
							$output .= '<br/><br/><br/><input type="checkbox" value="markSubAsDone" id="markSubAsDone" name="markSubAsDone" /><label for="markSubAsDone" >' . __('Appliquer ces changements &eacutegalement &agrave; tous les sous-&eacute;l&eacute;ments de l\'&eacute;l&eacute;ment courant', 'evarisk') . '</label>';
						}
						$output .= '
<script type="text/javascript">
	evarisk(document).ready(function(){
		evarisk("#sliderAvancement").slider({
			value:' . $progression . ',
			min: 0,
			max: 100,
			step: 1,
			slide: function(event, ui){
				evarisk( "#' . $id . '" ).val( ui.value );
			}
		});
		evarisk( "#' . $id . '" ).val( evarisk( "#sliderAvancement" ).slider( "value" ) );
		evarisk( "#' . $id . '" ).attr("style",evarisk( "#' . $id . '" ).attr("style") + "border:0px solid #000000;");
		evarisk("#putTodayActionStart").click(function(){
			evarisk("#date_debut").val("' . date('Y-m-d') . '");
		});
		evarisk("#putTodayActionEnd").click(function(){
			evarisk("#date_fin").val("' . date('Y-m-d') . '");
		});
	});
</script>';
						echo $output;
					}
					break;
				}
				break;
			case TABLE_ACTIVITE:
				switch($_REQUEST['act'])
				{
					case 'save':
					case 'update':
					case 'actionDone':
					{
						global $wpdb;
						switch($_REQUEST['act'])
						{
							case 'save':
								$action = __('sauvegard&eacute;e', 'evarisk');
								break;
							case 'update':
							case 'actionDone':
								$action = __('mise &agrave; jour', 'evarisk');
								break;
						}
						$activite = new EvaActivity($_REQUEST['id']);
						$activite->load();
						$activite->setName($_REQUEST['nom_activite']);
						$activite->setDescription($_REQUEST['description']);
						$activite->setRelatedTaskId($_REQUEST['idPere']);
						$activite->setStartDate($_REQUEST['date_debut']);
						$activite->setFinishDate($_REQUEST['date_fin']);
						$activite->setCout($_REQUEST['cout']);
						$activite->setProgression($_REQUEST['avancement']);
						$activite->setProgressionStatus('notStarted');
						if(($_REQUEST['avancement'] > '0') || ($activite->getProgressionStatus() == 'inProgress'))
						{
							$activite->setProgressionStatus('inProgress');
						}
						if(($_REQUEST['avancement'] == '100') || ($_REQUEST['act'] == 'actionDone'))
						{
							$activite->setProgressionStatus('Done');
							global $current_user;
							$activite->setidSoldeur($current_user->ID);
							$activite->setdateSolde(date('Y-m-d H:i:s'));
						}
						$activite->setidResponsable($_REQUEST['responsable_activite']);
						$activite->save();

						/*	Update the action ancestor	*/
						$relatedTask = new EvaTask($activite->getRelatedTaskId());
						$relatedTask->load();
						$relatedTask->getTimeWindow();
						$relatedTask->computeProgression();
						$relatedTask->save();

						/*	Update the task ancestor	*/
						$wpdbTasks = Arborescence::getAncetre(TABLE_TACHE, $relatedTask->convertToWpdb());
						foreach($wpdbTasks as $task)
						{
							unset($ancestorTask);
							$ancestorTask = new EvaTask($task->id);
							$ancestorTask->load();
							$ancestorTask->computeProgression();
							$ancestorTask->save();
							unset($ancestorTask);
						}

						$messageInfo = '<script type="text/javascript">';
						if($activite->getStatus() != 'error')
						{
							$messageInfo .= '
								evarisk(document).ready(function(){
									evarisk("#message").addClass("updated");
									evarisk("#message").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s a correctement &eacute;t&eacute; %s', 'evarisk') . '</strong></p>', __('de l\'action', 'evarisk') . ' "' . stripslashes($_REQUEST['nom_activite']) . '"', $action)) . '");';
						}
						else
						{
							$messageInfo .= '
								evarisk(document).ready(function(){
									evarisk("#message").addClass("updated");
									evarisk("#message").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s n\'a pas &eacute;t&eacute; %s.', 'evarisk') . '</strong></p>', __('de l\'action', 'evarisk') . ' "' . stripslashes($_REQUEST['nom_activite']) . '"', $action)) . '");';
						}
						$activite->load();
						$messageInfo .= '
									evarisk("#message").show();
									setTimeout(function(){
										evarisk("#message").removeClass("updated");
										evarisk("#message").hide();
									},7500);

									evarisk("#rightEnlarging").show();
									evarisk("#equilize").click();
									evarisk("#partieEdition").html(evarisk("#loadingImg").html());
									if("' . $_REQUEST['affichage'] . '" == "affichageTable")
									{
										if(evarisk("#filAriane :last-child").is("label"))
											evarisk("#filAriane :last-child").remove();
										evarisk("#filAriane :last-child").after(\'<label>&nbsp;&raquo;&nbsp;&Eacute;dition&nbsp;de&nbsp;' . $_REQUEST['nom_activite'] . '</label>\');
										evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
											"table": "' . TABLE_ACTIVITE . '",
											"id": "' . $activite->getId() . '",
											"page": evarisk("#pagemainPostBoxReference").val(),
											"idPere": evarisk("#identifiantActuellemainPostBox").val(),
											"act": "edit",
											"partie": "right",
				"menu": evarisk("#menu").val(),
											"affichage": "affichageTable",
											"partition": "tout"
										});
									}
									else
									{
										var expanded = new Array();
										evarisk(".expanded").each(function(){expanded.push(evarisk(this).attr("id"));});
										evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
											"table": "' . TABLE_ACTIVITE . '",
											"act": "edit",
											"id": "' . $activite->getId() . '",
											"partie": "right",
				"menu": evarisk("#menu").val(),
											"affichage": "affichageListe",
											"expanded": expanded
										});
										evarisk("#partieGauche").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
										{
											"post": "true", 
											"table": "' . TABLE_ACTIVITE . '",
											"act": "edit",
											"id": "' . $activite->getId() . '",
											"partie": "left",
											"menu": evarisk("#menu").val(),
											"affichage": "affichageListe",
											"expanded": expanded
										});
									}
								});
							</script>';
						echo $messageInfo;
					}
					break;
					case 'update-FAC':
					{
						$action = __('mise &agrave; jour', 'evarisk');

						$activite = new EvaActivity($_REQUEST['id']);
						$activite->load();
						$activite->setName($_REQUEST['nom_activite']);
						$activite->setDescription($_REQUEST['description']);
						$activite->setRelatedTaskId($_REQUEST['idPere']);
						$activite->setStartDate($_REQUEST['date_debut']);
						$activite->setFinishDate($_REQUEST['date_fin']);
						$activite->setCout($_REQUEST['cout']);
						$activite->setProgression($_REQUEST['avancement']);
						$activite->setProgressionStatus('notStarted');
						if(($_REQUEST['avancement'] > '0') || ($activite->getProgressionStatus() == 'inProgress'))
						{
							$activite->setProgressionStatus('inProgress');
						}
						if(($_REQUEST['avancement'] == '100') || ($_REQUEST['act'] == 'actionDone'))
						{
							$activite->setProgressionStatus('Done');
							global $current_user;
							$activite->setidSoldeur($current_user->ID);
							$activite->setdateSolde(date('Y-m-d H:i:s'));
						}
						$activite->setidResponsable($_REQUEST['responsable_activite']);
						$activite->save();

						/*	Update the action ancestor	*/
						$relatedTask = new EvaTask($activite->getRelatedTaskId());
						$relatedTask->load();
						$relatedTask->getTimeWindow();
						$relatedTask->computeProgression();
						$relatedTask->save();

						/*	Update the task ancestor	*/
						$wpdbTasks = Arborescence::getAncetre(TABLE_TACHE, $relatedTask->convertToWpdb());
						foreach($wpdbTasks as $task)
						{
							unset($ancestorTask);
							$ancestorTask = new EvaTask($task->id);
							$ancestorTask->load();
							$ancestorTask->computeProgression();
							$ancestorTask->save();
							unset($ancestorTask);
						}

						$idRisque = eva_tools::IsValid_Variable($_REQUEST['idProvenance']);
						$risque = Risque::getRisque($idRisque);
						$_REQUEST['idRisque'] = $idRisque;
						$_REQUEST['idDanger'] = $risque[0]->id_danger;
						$_REQUEST['idMethode'] = $risque[0]->id_methode;
						$_REQUEST['description'] = $risque[0]->commentaire;
						$_REQUEST['idElement'] = $risque[0]->id_element;
						$_REQUEST['tableElement'] = $risque[0]->nomTableElement;
						$_REQUEST['act'] = 'save';
						$_REQUEST['histo'] = 'true';
						require_once(EVA_METABOXES_PLUGIN_DIR . 'risque/risquePersistance.php');

						/*	Make the link between a corrective action and a risk evaluation	*/
						$query = 
							$wpdb->prepare(
								"SELECT id_evaluation 
								FROM " . TABLE_AVOIR_VALEUR . " 
								WHERE id_risque = '%d' 
									AND Status = 'Valid' 
								ORDER BY id DESC 
								LIMIT 1", 
								$_REQUEST['idProvenance']
							);
						$evaluation = $wpdb->get_row($query);
						evaTask::liaisonTacheElement(TABLE_AVOIR_VALEUR, $evaluation->id_evaluation, $relatedTask->getId(), 'after');

						$messageInfo = '<script type="text/javascript">
							evarisk(document).ready(function(){';
						if($activite->getStatus() != 'error')
						{
							$messageInfo .= '
								evarisk("#message' . TABLE_RISQUE . '").addClass("updated");
								evarisk("#message' . TABLE_RISQUE . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s a correctement &eacute;t&eacute; %s', 'evarisk') . '</strong></p>', __('de l\'action', 'evarisk') . ' "' . stripslashes($_REQUEST['nom_activite']) . '"', $action)) . '");';
						}
						else
						{
							$messageInfo .= '
								evarisk("#message' . TABLE_RISQUE . '").addClass("updated");
								evarisk("#message' . TABLE_RISQUE . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s n\'a pas &eacute;t&eacute; %s.', 'evarisk') . '</strong></p>', __('de l\'action', 'evarisk') . ' "' . stripslashes($_REQUEST['nom_activite']) . '"', $action)) . '");';
						}
						$messageInfo .= '
								evarisk("#message' . TABLE_RISQUE . '").show();
								setTimeout(function(){
									evarisk("#message' . TABLE_RISQUE . '").removeClass("updated");
									evarisk("#message' . TABLE_RISQUE . '").hide();
								},7500);
								evarisk("#ongletVoirLesRisques").click();
							});
						</script>';
						echo $messageInfo;
					}
					break;
					case 'pictureLoad':
					{
						$tableElement = $_REQUEST['table'];
						$idElement = $_REQUEST['idElement'];
						$repertoireDestination = str_replace('\\', '/', EVA_UPLOADS_PLUGIN_DIR . $tableElement . '/' . $idElement . '/');
						$allowedExtensions = "['jpeg','jpg','png','gif']";
						$multiple = false;
						$photoDefaut = '';
						$gallery = '<table summary="upload picture before and after corrective action" cellpadding="0" cellspacing="0" style="width:100%;" >
							<tr>
								<td id="pictureBeforeContainer" >
									<div id="uploadButtonBefore" >' . evaPhoto::getFormulaireUploadPhoto($_REQUEST['table'], $_REQUEST['idElement'], '', 'pictureBeforeForm', $allowedExtensions, $multiple, str_replace('\\', '/', EVA_LIB_PLUGIN_URL . "actionsCorrectives/activite/uploadPhotoAvant.php"), $photoDefaut, __('Envoyer la photo avant', 'evarisk'), 'loadPictureBeforeAC();') . '</div>
									<div id="pictureBefore" >&nbsp;</div>
								</td>
								<td id="pictureAfterContainer" >
									<div id="uploadButtonAfter" >' . evaPhoto::getFormulaireUploadPhoto($_REQUEST['table'], $_REQUEST['idElement'], '', 'pictureAfterForm', $allowedExtensions, $multiple, str_replace('\\', '/', EVA_LIB_PLUGIN_URL . "actionsCorrectives/activite/uploadPhotoApres.php"), $photoDefaut, __('Envoyer la photo apr&egrave;s', 'evarisk'), 'loadPictureAfterAC();') . '</div>
									<div id="PictureAfter" >&nbsp;</div>
								</td>
							</tr>
						</table>
						<script type="text/javascript" >
							function loadPictureBeforeAC(){
								evarisk(".qq-upload-list").hide();
								evarisk("#pictureBefore").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
									"post":"true", 
									"nom":"loadPictureAC",
									"act":"before",
									"tableProvenance":"' . $tableElement . '", 
									"idProvenance": "' . $idElement . '"
								});
							}
							function loadPictureAfterAC(){
								evarisk(".qq-upload-list").hide();
								evarisk("#PictureAfter").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
									"post":"true", 
									"nom":"loadPictureAC",
									"act":"after",
									"tableProvenance":"' . $tableElement . '", 
									"idProvenance": "' . $idElement . '"
								});
							}
							evarisk(document).ready(function(){
								evarisk("#uploadButtonBefore .qq-upload-button").css("width", "90%");
								evarisk("#uploadButtonAfter .qq-upload-button").css("width", "90%");
							});
						</script>';
						echo $gallery;
					}
					break;
					case 'actualiserAvancement':
					{
						$activites = $_REQUEST['activites'];
						$status = 'Valid';
						foreach($activites as $idActivite => $avancementActivite)
						{
							$activite = new EvaActivity($idActivite);
							$activite->load();
							$activite->setProgression($avancementActivite);
							if($_REQUEST['avancement'] == '100')
							{
								$activite->setProgressionStatus('Done');
								global $current_user;
								$activite->setidSoldeur($current_user->ID);
							}
							$activite->save();
							if($activite->getStatus() == 'error')
							{
								$status = 'error';
							}

							/*	Update the action ancestor	*/
							$relatedTask = new EvaTask($activite->getRelatedTaskId());
							$relatedTask->load();
							$relatedTask->getTimeWindow();
							$relatedTask->computeProgression();
							$relatedTask->save();
								/*	Update the task ancestor	*/
								$wpdbTasks = Arborescence::getAncetre(TABLE_TACHE, $relatedTask->convertToWpdb());
								foreach($wpdbTasks as $task)
								{
									unset($ancestorTask);
									$ancestorTask = new EvaTask($task->id);
									$ancestorTask->load();
									$ancestorTask->computeProgression();
									$ancestorTask->save();
									unset($ancestorTask);
								}
						}
						
						$messageInfo = '<script type="text/javascript">
							evarisk(document).ready(function(){
								evarisk("#message' . $_REQUEST['tableProvenance'] . '").addClass("updated");';
						if($status != 'error')
						{
							$messageInfo = $messageInfo . '
								evarisk("#message' . $_REQUEST['tableProvenance'] . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Les modifications ont correctement &eacute;t&eacute enregistr&eacute;es', 'evarisk') . '</strong></p>') . '");';
						}
						else
						{
							$messageInfo = $messageInfo . '
								evarisk("#message' . $_REQUEST['tableProvenance'] . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Les modifications n\'ont pas toutes &eacute;t&eacute correctement enregistr&eacute;es', 'evarisk') . '</strong></p>"') . '");';
						}
						$messageInfo = $messageInfo . '
									evarisk("#message' . $_REQUEST['tableProvenance'] . '").show();
									setTimeout(function(){
										evarisk("#message' . $_REQUEST['tableProvenance'] . '").removeClass("updated");
										evarisk("#message' . $_REQUEST['tableProvenance'] . '").hide();
									},7500);
									evarisk("#divSuiviAction' . $_REQUEST['tableProvenance'] . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "nom":"suiviAction",	"tableProvenance":"' . $_REQUEST['tableProvenance'] . '", "idProvenance": "' . $_REQUEST['idProvenance'] . '"});
									evarisk("#divSuiviAction' . TABLE_RISQUE . '").html(evarisk("#loadingImg").html());
								});
							</script>';
						echo $messageInfo;
					}
					break;
					case 'delete':
					{
						$activite = new EvaActivity($_REQUEST['id']);
						$activite->load();
						$activite->setStatus('Deleted');
						$activite->save();

						$messageInfo = '<script type="text/javascript">';
						if($activite->getStatus() != 'error')
						{
							$messageInfo = $messageInfo . '
								evarisk(document).ready(function(){
									evarisk("#message").addClass("updated");
									evarisk("#message").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s a correctement &eacute;t&eacute; supprim&eacute;e', 'evarisk') . '</strong></p>', __('de l\'action', 'evarisk') . ' "' . stripslashes($activite->getName()) . '"')) . '");
									evarisk("#message").show();
									setTimeout(function(){
										evarisk("#message").removeClass("updated");
										evarisk("#message").hide();
									},7500);';
						}
						else
						{
							$messageInfo = $messageInfo . '
								evarisk(document).ready(function(){
									evarisk("#message").addClass("updated");
									evarisk("#message").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s n\'a pas &eacute;t&eacute; supprim&eacute;e.', 'evarisk') . '</strong></p>', __('de l\'action', 'evarisk') . ' "' . stripslashes($activite->getName()) . '"')) . '");
									evarisk("#message").show();
									setTimeout(function(){
										evarisk("#message").removeClass("updated");
										evarisk("#message").hide();
									},7500);';
						}
						$messageInfo .= '
									evarisk("#rightEnlarging").show();
									evarisk("#equilize").click();
									if("' . $_REQUEST['affichage'] . '" == "affichageTable")
									{
										if(evarisk("#filAriane :last-child").is("label"))
											evarisk("#filAriane :last-child").remove();
										evarisk("#filAriane :last-child").after("<label>&nbsp;&raquo;&nbsp;&Eacute;dition&nbsp;de&nbsp;' . $_REQUEST['nom_activite'] . '</label>");
										evarisk("#partieEdition").html("");
										evarisk("#partieGauche").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
										{
											"post": "true", 
											"table": "' . TABLE_ACTIVITE . '",
											"id": "' . $activite->getId() . '",
											"page": evarisk("#pagemainPostBoxReference").val(),
											"idPere": evarisk("#identifiantActuellemainPostBox").val(),
											"act": "changementPage",
											"partie": "left",
											"menu": evarisk("#menu").val(),
											"affichage": "affichageTable",
											"partition": "tout"
										});
									}
									else
									{
										var expanded = new Array();
										evarisk(".expanded").each(function(){expanded.push(evarisk(this).attr("id"));});
										evarisk("#partieEdition").html("");
										evarisk("#partieGauche").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
										{
											"post": "true", 
											"table": "' . TABLE_ACTIVITE . '",
											"act": "changementPage",
											"id": "' . $activite->getId() . '",
											"partie": "left",
											"menu": evarisk("#menu").val(),
											"affichage": "affichageListe",
											"expanded": expanded
										});
									}
								});
							</script>';
						echo $messageInfo;
					}
					break;
					case 'setAsBeforePicture':
					{
						$activite = new EvaActivity($_REQUEST['idElement']);
						$activite->load();
						$activite->setidPhotoAvant($_REQUEST['idPhoto']);
						$activite->save();
						$messageInfo = '<script type="text/javascript">
								evarisk(document).ready(function(){
									evarisk("#message' . $_REQUEST['table'] . '_' . $_REQUEST['idElement'] . '").addClass("updated");';
						if($activite->getStatus() != 'error')
						{
							$messageInfo .= '
									evarisk("#message' . $_REQUEST['table'] . '_' . $_REQUEST['idElement'] . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La photo a bien &eacute;t&eacute; d&eacute;finie comme photo avant l\'action', 'evarisk') . '</strong></p>') . '");';
						}
						else
						{
							$messageInfo .= '
									evarisk("#message' . $_REQUEST['table'] . '_' . $_REQUEST['idElement'] . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La photo n\'a pas pu &ecirc;tre d&eacute;finie comme photo avant l\'action', 'evarisk') . '</strong></p>') . '");';
						}
						$messageInfo .= '
									evarisk("#message' . $_REQUEST['table'] . '_' . $_REQUEST['idElement'] . '").show();
									setTimeout(function(){
										evarisk("#message' . $_REQUEST['table'] . '_' . $_REQUEST['idElement'] . '").removeClass("updated");
										evarisk("#message' . $_REQUEST['table'] . '_' . $_REQUEST['idElement'] . '").hide();
									},7500);
									reloadcontainer(\'' . $_REQUEST['table'] . '\', \'' . $_REQUEST['idElement'] . '\', \'' . PICTO_LOADING_ROUND . '\');
								});
						</script>';
						echo $messageInfo;
					}
					break;
					case 'setAsAfterPicture':
					{
						$activite = new EvaActivity($_REQUEST['idElement']);
						$activite->load();
						$activite->setidPhotoApres($_REQUEST['idPhoto']);
						$activite->save();
						$messageInfo = '<script type="text/javascript">
								evarisk(document).ready(function(){
									evarisk("#message' . $_REQUEST['table'] . '_' . $_REQUEST['idElement'] . '").addClass("updated");';
						if($activite->getStatus() != 'error')
						{
							$messageInfo .= '
									evarisk("#message' . $_REQUEST['table'] . '_' . $_REQUEST['idElement'] . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La photo a bien &eacute;t&eacute; d&eacute;finie comme photo apr&egrave;s l\'action', 'evarisk') . '</strong></p>') . '");';
						}
						else
						{
							$messageInfo .= '
									evarisk("#message' . $_REQUEST['table'] . '_' . $_REQUEST['idElement'] . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La photo n\'a pas pu &ecirc;tre d&eacute;finie comme photo apr&egrave;s l\'action', 'evarisk') . '</strong></p>') . '");';
						}
						$messageInfo .= '
									evarisk("#message' . $_REQUEST['table'] . '_' . $_REQUEST['idElement'] . '").show();
									setTimeout(function(){
										evarisk("#message' . $_REQUEST['table'] . '_' . $_REQUEST['idElement'] . '").removeClass("updated");
										evarisk("#message' . $_REQUEST['table'] . '_' . $_REQUEST['idElement'] . '").hide();
									},7500);
									reloadcontainer(\'' . $_REQUEST['table'] . '\', \'' . $_REQUEST['idElement'] . '\', \'' . PICTO_LOADING_ROUND . '\');
								});
						</script>';
						echo $messageInfo;
					}
					break;
					case 'unsetAsBeforePicture':
					{
						$activite = new EvaActivity($_REQUEST['idElement']);
						$activite->load();
						$activite->setidPhotoAvant("0");
						$activite->save();
						$messageInfo = '<script type="text/javascript">
								evarisk(document).ready(function(){
									evarisk("#message' . $_REQUEST['table'] . '_' . $_REQUEST['idElement'] . '").addClass("updated");';
						if($activite->getStatus() != 'error')
						{
							$messageInfo .= '
									evarisk("#message' . $_REQUEST['table'] . '_' . $_REQUEST['idElement'] . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La photo n\'est plus d&eacute;finie comme photo avant l\'action', 'evarisk') . '</strong></p>') . '");';
						}
						else
						{
							$messageInfo .= '
									evarisk("#message' . $_REQUEST['table'] . '_' . $_REQUEST['idElement'] . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La photo est toujours d&eacute;finie comme photo avant l\'action', 'evarisk') . '</strong></p>') . '");';
						}
						$messageInfo .= '
									evarisk("#message' . $_REQUEST['table'] . '_' . $_REQUEST['idElement'] . '").show();
									setTimeout(function(){
										evarisk("#message' . $_REQUEST['table'] . '_' . $_REQUEST['idElement'] . '").removeClass("updated");
										evarisk("#message' . $_REQUEST['table'] . '_' . $_REQUEST['idElement'] . '").hide();
									},7500);
									reloadcontainer(\'' . $_REQUEST['table'] . '\', \'' . $_REQUEST['idElement'] . '\', \'' . PICTO_LOADING_ROUND . '\');
								});
						</script>';
						echo $messageInfo;
					}
					break;
					case 'unsetAsAfterPicture':
					{
						$activite = new EvaActivity($_REQUEST['idElement']);
						$activite->load();
						$activite->setidPhotoApres("0");
						$activite->save();
						$messageInfo = '<script type="text/javascript">
								evarisk(document).ready(function(){
									evarisk("#message' . $_REQUEST['table'] . '_' . $_REQUEST['idElement'] . '").addClass("updated");';
						if($activite->getStatus() != 'error')
						{
							$messageInfo .= '
									evarisk("#message' . $_REQUEST['table'] . '_' . $_REQUEST['idElement'] . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La photo n\'est plus d&eacute;finie comme photo apr&egrave;s l\'action', 'evarisk') . '</strong></p>') . '");';
						}
						else
						{
							$messageInfo .= '
									evarisk("#message' . $_REQUEST['table'] . '_' . $_REQUEST['idElement'] . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La photo est toujours d&eacute;finie comme photo apr&egrave;s l\'action', 'evarisk') . '</strong></p>') . '");';
						}
						$messageInfo .= '
									evarisk("#message' . $_REQUEST['table'] . '_' . $_REQUEST['idElement'] . '").show();
									setTimeout(function(){
										evarisk("#message' . $_REQUEST['table'] . '_' . $_REQUEST['idElement'] . '").removeClass("updated");
										evarisk("#message' . $_REQUEST['table'] . '_' . $_REQUEST['idElement'] . '").hide();
									},7500);
									reloadcontainer(\'' . $_REQUEST['table'] . '\', \'' . $_REQUEST['idElement'] . '\', \'' . PICTO_LOADING_ROUND . '\');
								});
						</script>';
						echo $messageInfo;
					}
					break;
					case 'setActivityInProgress':
					{
						$id = eva_tools::IsValid_Variable($_REQUEST['id']);
						$activite = new EvaActivity($id);
						$activite->load();
						$activite->setProgressionStatus('inProgress');
						$taskId = $activite->getRelatedTaskId();
						$activite->save();

						$updateTaskProgressionInTree = '';
						if(($taskId != '') && ($taskId > 0))
						{
							/*	Update the action ancestor	*/
							$relatedTask = new EvaTask($taskId);
							$relatedTask->load();
							$relatedTask->getTimeWindow();
							$relatedTask->computeProgression();
							$relatedTask->save();
							$updateTaskProgressionInTree .= '
	evarisk(".taskInfoContainer-' . $taskId . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",
	{
		"post": "true", 
		"table": "' . TABLE_TACHE . '",
		"act": "actualiseProgressionInTree",
		"id": "' . $taskId . '"
	});';

							/*	Update the task ancestor	*/
							$wpdbTasks = Arborescence::getAncetre(TABLE_TACHE, $relatedTask->convertToWpdb());
							foreach($wpdbTasks as $task)
							{
								unset($ancestorTask);
								$ancestorTask = new EvaTask($task->id);
								$ancestorTask->load();
								$ancestorTask->computeProgression();
								$ancestorTask->save();
								unset($ancestorTask);
								/*	Don't update the tree information if it is the root task	*/
								if($task->id != 1)
								{
									$updateTaskProgressionInTree .= '
	evarisk(".taskInfoContainer-' . $task->id . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",
	{
		"post": "true", 
		"table": "' . TABLE_TACHE . '",
		"act": "actualiseProgressionInTree",
		"id": "' . $task->id . '"
	});';
								}
							}
						}

						echo '
<script type="text/javascript">
	evarisk(".activityInfoContainer-' . $id . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",
	{
		"post": "true", 
		"table": "' . TABLE_ACTIVITE . '",
		"act": "actualiseProgressionInTree",
		"id": "' . $id . '"
	});' . $updateTaskProgressionInTree . '
</script>';
					}
					break;
					case 'actualiseProgressionInTree':
					{
						$id = eva_tools::IsValid_Variable($_REQUEST['id']);
						$action = new EvaActivity($id);
						$action->load();
						$statutProgression = '';
						switch($action->getProgressionStatus())
						{
							case 'notStarted';
								$statutProgression = __('Non commenc&eacute;e', 'evarisk');
							break;
							case 'inProgress';
								$statutProgression = __('En cours', 'evarisk');
							break;
							case 'Done';
							case 'DoneByChief';
								$statutProgression = __('Sold&eacute;e', 'evarisk');
							break;
						}
						echo $action->getProgression() . '%&nbsp;(' . $statutProgression . ')';
					}
					break;
				}
				break;
			case TABLE_ACTIVITE_SUIVI:
				$tableElement = $_REQUEST['tableElement'];
				$idElement = $_REQUEST['idElement'];
				require_once( EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/suivi_activite.class.php');
				switch($_REQUEST['act'])
				{
					case 'save':
					{
						$messageInfo = 
							'<script type="text/javascript">
								evarisk(document).ready(function(){
									evarisk("#messageInfo' . $tableElement . $idElement . '").addClass("updated");';

						$saveFollow = suivi_activite::saveSuiviActivite($_REQUEST['tableElement'], $_REQUEST['idElement'], $_REQUEST['commentaire']);

						if($saveFollow == 'ok')
						{
							$messageInfo .= '
									evarisk("#messageInfo' . $tableElement . $idElement . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Les modifications ont correctement &eacute;t&eacute enregistr&eacute;es', 'evarisk') . '</strong></p>') . '");';
						}
						else
						{
							$messageInfo .= '
									evarisk("#messageInfo' . $tableElement . $idElement . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Les modifications n\'ont pas toutes &eacute;t&eacute correctement enregistr&eacute;es', 'evarisk') . '</strong></p>"') . '");';
						}

						$messageInfo .= '
									evarisk("#messageInfo' . $tableElement . $idElement . '").show();
									setTimeout(function(){
										evarisk("#messageInfo' . $tableElement . $idElement . '").removeClass("updated");
										evarisk("#messageInfo' . $tableElement . $idElement . '").hide();
									},7500);

									evarisk("#loadsaveActionFollow").html(\'\');
									evarisk("#bttnsaveActionFollow").show();
									evarisk("#loadsaveActionFollow").hide();
								});
							</script>';
						echo $messageInfo . suivi_activite::formulaireAjoutSuivi($tableElement, $idElement);
					}
					break;
				}
			break;
			case TABLE_LIAISON_USER_ELEMENT:
				require_once(EVA_LIB_PLUGIN_DIR . 'users/evaUserLinkElement.class.php');
				switch($_REQUEST['act'])
				{
					case 'save':
						evaUserLinkElement::setLinkUserElement($_REQUEST['tableElement'], $_REQUEST['idElement'], $_REQUEST['utilisateurs']);
					break;
				}
				break;
			case TABLE_DUER:
				$tableElement = $_REQUEST['tableElement'];
				$idElement = $_REQUEST['idElement'];
				switch($_REQUEST['act'])
				{
					case 'generateSummary' :
						$tableElement = $_REQUEST['tableElement'];
						$idElement = $_REQUEST['idElement'];
						echo  eva_documentUnique::getBoxBilan($tableElement, $idElement);
					break;
					case 'voirHistoriqueDocument' :
					{
						$tableElement = $_REQUEST['tableElement'];
						$idElement = $_REQUEST['idElement'];

						$output .= '
<div class="clear" id="summaryGeneratedDocumentSlector" >
	<div class="alignleft selected" id="generatedDUER" >' . __('Document unique', 'evarisk') . '</div>
	<div class="alignleft" id="generatedFGP" >' . __('Fiches de groupement', 'evarisk') . '</div>
	<div class="alignleft" id="generatedFP" >' . __('Fiches de poste', 'evarisk') . '</div>
</div>';

						/*	Start "document unique" part	*/
						$output .= '<div id="generatedDUERContainer" class="generatedDocContainer" ><div class="clear bold" >' . __('Documents unique pour le groupement', 'evarisk') . '</div><div class="DUERContainer" >' . eva_documentUnique::getDUERList($tableElement, $idElement) . '</div></div>';

						/*	Start groupe sheet part	*/
						$output .= '<div class="hide generatedDocContainer" id="generatedFGPContainer" ><div class="clear bold" >' . __('Fiches du groupement courant', 'evarisk') . '</div>
						<div class="FGPContainer" >' . eva_GroupSheet::getGeneratedDocument($tableElement, $idElement, 'list') . '</div><div class="clear" >&nbsp;</div>
						<div class="clear bold" >' . __('Fiches des sous-groupements du groupement courant', 'evarisk') . '</div>
						<div class="FGPContainer" >' . eva_GroupSheet::getGroupSheetCollectionHistory($tableElement, $idElement) . '</div></div>';

						/*	Start work unit sheet part	*/
						$output .= '<div class="hide generatedDocContainer" id="generatedFPContainer" ><div class="clear bold" >' . __('Fiches de poste pour le groupement', 'evarisk') . '</div>
						<div class="FPContainer" >' . eva_WorkUnitSheet::getWorkUnitSheetCollectionHistory($tableElement, $idElement) . '</div></div>

<div><a href="' . LINK_TO_DOWNLOAD_OPEN_OFFICE . '" target="OOffice" >' . __('T&eacute;l&eacute;charger Open Office', 'evarisk') . '</a></div>
<script type="text/javascript" >
	evarisk("#generatedDUER").click(function(){
		evarisk("#summaryGeneratedDocumentSlector div").each(function(){
			evarisk(this).removeClass("selected");
		});
		evarisk(".generatedDocContainer").each(function(){
			evarisk(this).hide();
		});
		evarisk(this).addClass("selected");
		evarisk("#generatedDUERContainer").show();
	});

	evarisk("#generatedFGP").click(function(){
		evarisk("#summaryGeneratedDocumentSlector div").each(function(){
			evarisk(this).removeClass("selected");
		});
		evarisk(".generatedDocContainer").each(function(){
			evarisk(this).hide();
		});
		evarisk(this).addClass("selected");
		evarisk("#generatedFGPContainer").show();
	});
	
	evarisk("#generatedFP").click(function(){
		evarisk("#summaryGeneratedDocumentSlector div").each(function(){
			evarisk(this).removeClass("selected");
		});
		evarisk(".generatedDocContainer").each(function(){
			evarisk(this).hide();
		});
		evarisk(this).addClass("selected");
		evarisk("#generatedFPContainer").show();
	});
	var currentTab = evarisk("#subTabSelector").val();
	if(currentTab != ""){
		evarisk("#generated" + currentTab).click();
	}
	evarisk("#subTabSelector").val("");
</script>';
						echo $output;
					}
					break;
					case 'saveDocumentUnique' :
						require_once(EVA_METABOXES_PLUGIN_DIR . 'documentUnique/documentUniquePersistance.php');
						require_once(EVA_METABOXES_PLUGIN_DIR . 'documentUnique/documentUnique.php');
						$tableElement = $_REQUEST['tableElement'];
						$idElement = $_REQUEST['idElement'];
						echo eva_documentUnique::formulaireGenerationDocumentUnique($tableElement, $idElement) . '<script type="text/javascript" >evarisk(document).ready(function(){evarisk("#ui-datepicker-div").hide();});</script>';
					break;
					case 'loadNewModelForm':
						echo evaDisplayDesign::getNewModelUploadForm($tableElement, $idElement);
					break;
					case 'workSheetUnitCollectionGenerationForm':
					{
						$tableElement = $_REQUEST['tableElement'];
						$idElement = $_REQUEST['idElement'];
						echo eva_WorkUnitSheet::getWorkUnitSheetCollectionGenerationForm($tableElement, $idElement);
					}
					break;
					case 'documentUniqueGenerationForm':
					{
						$tableElement = $_REQUEST['tableElement'];
						$idElement = $_REQUEST['idElement'];
						echo eva_documentUnique::formulaireGenerationDocumentUnique($tableElement, $idElement);
					}
					break;
					case 'reGenerateDUER':
					{
						$tableElement = $_REQUEST['tableElement'];
						$idElement = $_REQUEST['idElement'];
						$idDocument = $_REQUEST['idDocument'];
						eva_gestionDoc::generateSummaryDocument($tableElement, $idElement, 'odt', $idDocument);
						$documentUniqueInfos = eva_documentUnique::getDernierDocumentUnique($tableElement, $idElement, $idDocument);
						$odtFile = 'documentUnique/' . $tableElement . '/' . $idElement . '/' . $documentUniqueInfos->nomDUER . '_V' . $documentUniqueInfos->revisionDUER . '.odt';
						if( is_file(EVA_RESULTATS_PLUGIN_DIR . $odtFile) )
						{
							echo 
'<script type="text/javascript" >
	(function(){
		jQuery("#reGenerateDUER' . $idDocument . 'Container").html(\'' . __('Nouveau', 'evarisk') . '&nbsp;<a href="' . EVA_RESULTATS_PLUGIN_URL . $odtFile . '" target="evaDUEROdt" >Odt</a>\');
	})(evarisk)
</script>';
						}
					}
					break;
					case 'deleteDUER':
					{
						$tableElement = $_REQUEST['tableElement'];
						$idElement = $_REQUEST['idElement'];
						$idDocument = $_REQUEST['idDocument'];
						$query = $wpdb->prepare("UPDATE " . TABLE_DUER . " SET status = 'deleted' WHERE id= %d", $idDocument);
						if( $wpdb->query($query) )
						{
							$messageToOutput = "<img src='" . EVA_MESSAGE_SUCCESS . "' alt='success' class='messageIcone' />" . __('Le document unique &agrave; bien &eacute;t&eacute; supprim&eacute;.', 'evarisk');
							echo 
'<script type="text/javascript" >
	(function(){
		actionMessageShow("#message' . TABLE_DUER . '", "' . $messageToOutput . '");
		setTimeout(\'actionMessageHide("#message' . TABLE_DUER . '")\',5000);
		evarisk("#ongletHistoriqueDocument").click();
	})(evarisk)
</script>';
						}
					}
					break;
					case 'groupementSheetGeneration':
					{
						$tableElement = $_REQUEST['tableElement'];
						$idElement = $_REQUEST['idElement'];
						echo eva_GroupSheet::getGroupSheetGenerationForm($tableElement, $idElement);
					}
					break;
				}
				break;
			case TABLE_FP:
				switch($_REQUEST['act'])
				{
					case 'saveFichePoste':
					case 'generateWorkUnitSheet':
						$tableElement = $_REQUEST['tableElement'];
						$idElement = $_REQUEST['idElement'];
						if($_REQUEST['act'] == 'saveFichePoste')
						{
							$workUnitinformations = eva_UniteDeTravail::getWorkingUnit($idElement);

							$_POST['description'] = $workUnitinformations->description;
							$_POST['telephone'] = $workUnitinformations->telephoneUnite;

							$workUnitAdress = new EvaBaseAddress($workUnitinformations->id_adresse);
							$workUnitAdress->load();
							$_POST['adresse'] = trim($workUnitAdress->getFirstLine() . " " . $workUnitAdress->getSecondLine() . " " . $workUnitAdress->getPostalCode() . " " . $workUnitAdress->getCity());

							require_once(EVA_METABOXES_PLUGIN_DIR . 'ficheDePoste/ficheDePostePersistance.php');
						}
						echo eva_WorkUnitSheet::getWorkUnitSheetGenerationForm($tableElement, $idElement) . '<script type="text/javascript" >evarisk(document).ready(function(){evarisk("#ui-datepicker-div").hide();});</script>';
					break;
					case 'workUnitSheetHisto':
						$tableElement = $_REQUEST['tableElement'];
						$idElement = $_REQUEST['idElement'];
						echo eva_WorkUnitSheet::getGeneratedDocument($tableElement, $idElement, 'list');
					break;
					case 'saveWorkUnitSheetForGroupement':
					{
						$file_to_zip = array();

						$mainTableElement = $tableElement = $_REQUEST['tableElement'];
						$mainIDElement = $idElement = $_REQUEST['idElement'];
						$groupementParent = EvaGroupement::getGroupement($idElement);
						$arbre = arborescence::getCompleteUnitList($tableElement, $idElement);
						$pathToZip = EVA_RESULTATS_PLUGIN_DIR . 'documentUnique/' . $tableElement . '/' . $idElement. '/';
						foreach($arbre as $workUnit)
						{
							$workUnitinformations = eva_UniteDeTravail::getWorkingUnit($workUnit['id']);
							$_POST['description'] = $workUnitinformations->description;
							$_POST['telephone'] = $workUnitinformations->telephoneUnite;

							$workUnitAddress = new EvaBaseAddress($workUnitinformations->id_adresse);
							$workUnitAddress->load();
							$_POST['adresse'] = trim($workUnitAddress->getFirstLine() . " " . $workUnitAddress->getSecondLine() . " " . $workUnitAddress->getPostalCode() . " " . $workUnitAddress->getCity());

							$_POST['tableElement'] = $workUnit['table'];
							$_POST['idElement'] = $workUnit['id'];
							$_POST['nomDuDocument'] = date('Ymd') . '_' . ELEMENT_IDENTIFIER_UT . $workUnit['id'] . '_' . eva_tools::slugify_noaccent(str_replace(' ', '_', $workUnit['nom']));
							$_POST['nomEntreprise'] = $groupementParent->nom;

							include(EVA_METABOXES_PLUGIN_DIR . 'ficheDePoste/ficheDePostePersistance.php');
							$lastDocument = eva_WorkUnitSheet::getGeneratedDocument($tableElement, $idElement, 'last');
							$odtFile = 'ficheDePoste/' . $workUnit['table'] . '/' . $workUnit['id'] . '/' . $lastDocument->name . '_V' . $lastDocument->revision . '.odt';
							if( is_file(EVA_RESULTATS_PLUGIN_DIR . $odtFile) )
							{
								$file_to_zip[] = EVA_RESULTATS_PLUGIN_DIR . $odtFile;
							}
						}

						$saveZipFileActionMessage = '';
						eva_tools::make_recursiv_dir($pathToZip);
						if(count($file_to_zip) > 0)
						{
							/*	ZIP THE FILE	*/
							$zipFileName = date('YmdHis') . '_fichesDePoste.zip';
							$archive = new eva_Zip($zipFileName);
							$archive->setFiles($file_to_zip);
							$archive->compressToPath($pathToZip);
							$saveWorkSheetUnitStatus = eva_gestionDoc::saveNewDoc('fiche_de_poste_groupement', $mainTableElement, $mainIDElement, str_replace(EVA_GENERATED_DOC_DIR, '', $pathToZip . $zipFileName));
							if($saveWorkSheetUnitStatus == 'error')
							{
								$messageInfo = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png\' class=\'messageIcone\' alt=\'error\' />' . __('Une erreur est survenue lors de l\'enregistrement des fiches de postes pour ce groupement', 'evarisk');
							}
							else
							{
								$messageInfo = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png\' class=\'messageIcone\' alt=\'succes\' />' . __('Les fiches de poste ont correctement &eacute;t&eacute; enregistr&eacute;es.', 'evarisk');
							}
						$saveZipFileActionMessage = '
	evarisk(document).ready(function(){
			actionMessageShow("#message' . TABLE_DUER . '", "' . $messageInfo . '");
			setTimeout(\'actionMessageHide("#message' . TABLE_DUER . '")\',7500);
	});';
						}

						echo '
<script type="text/javascript" >
	evarisk("#subTabSelector").val("FP");
	' . $saveZipFileActionMessage . '
	evarisk("#ongletHistoriqueDocument").click();
</script>';
					}
					break;
					case 'voirHistoriqueFicheDePosteGroupement':
					{
						$tableElement = $_REQUEST['tableElement'];
						$idElement = $_REQUEST['idElement'];
						$output = eva_WorkUnitSheet::getWorkUnitSheetCollectionGenerationForm($tableElement, $idElement) . '
<div class="clear" >
	' . eva_WorkUnitSheet::getWorkUnitSheetCollectionHistory($tableElement, $idElement) . '
</div>';
						echo $output;
					}
					break;

					case 'saveFicheGroupement':
						$tableElement = $_REQUEST['tableElement'];
						$idElement = $_REQUEST['idElement'];
						$groupementInformations = EvaGroupement::getGroupement($idElement);
						$_POST['description'] = $groupementInformations->description;
						$_POST['telephone'] = $groupementInformations->telephoneGroupement;

						$groupementAddress = new EvaBaseAddress($groupementInformations->id_adresse);
						$groupementAddress->load();
						$_POST['adresse'] = trim($groupementAddress->getFirstLine() . " " . $groupementAddress->getSecondLine() . " " . $groupementAddress->getPostalCode() . " " . $groupementAddress->getCity());

						require_once(EVA_METABOXES_PLUGIN_DIR . 'ficheDePoste/ficheDeGroupementPersistance.php');
						echo eva_GroupSheet::getGroupSheetGenerationForm($tableElement, $idElement) . '<script type="text/javascript" >evarisk(document).ready(function(){evarisk("#generateFGP").click();});</script>';
					break;
					case 'saveGroupSheetForGroupement':
					{
						$file_to_zip = array();

						$mainTableElement = $tableElement = $_REQUEST['tableElement'];
						$mainIDElement = $idElement = $_REQUEST['idElement'];
						$groupementParent = EvaGroupement::getGroupement($idElement);
						$arbre = arborescence::getCompleteGroupList($tableElement, $idElement);
						$pathToZip = EVA_RESULTATS_PLUGIN_DIR . 'ficheDeGroupement/' . $tableElement . '/' . $idElement. '/';
						foreach($arbre as $group)
						{
							$_POST['tableElement'] = $group['table'];
							$_POST['idElement'] = $group['id'];
							$_POST['nomDuDocument'] = date('Ymd') . '_' . ELEMENT_IDENTIFIER_GP . $group['id'] . '_' . eva_tools::slugify_noaccent(str_replace(' ', '_', $group['nom']));
							$_POST['nomEntreprise'] = $groupementParent->nom;
							
							$_POST['description'] = $groupementParent->description;
							$_POST['telephone'] = $groupementParent->telephoneGroupement;

							$groupementAddress = new EvaBaseAddress($groupementParent->id_adresse);
							$groupementAddress->load();
							$_POST['adresse'] = trim($groupementAddress->getFirstLine() . " " . $groupementAddress->getSecondLine() . " " . $groupementAddress->getPostalCode() . " " . $groupementAddress->getCity());


							include(EVA_METABOXES_PLUGIN_DIR . 'ficheDePoste/ficheDeGroupementPersistance.php');
							$lastDocument = eva_WorkUnitSheet::getGeneratedDocument($tableElement, $idElement, 'last');
							$odtFile = 'ficheDeGroupement/' . $group['table'] . '/' . $group['id'] . '/' . $lastDocument->name . '_V' . $lastDocument->revision . '.odt';
							if( is_file(EVA_RESULTATS_PLUGIN_DIR . $odtFile) )
							{
								$file_to_zip[] = EVA_RESULTATS_PLUGIN_DIR . $odtFile;
							}
						}

						$saveZipFileActionMessage = '';
						eva_tools::make_recursiv_dir($pathToZip);
						if(count($file_to_zip) > 0)
						{
							/*	ZIP THE FILE	*/
							$zipFileName = date('YmdHis') . '_fichesDeGroupement.zip';
							$archive = new eva_Zip($zipFileName);
							$archive->setFiles($file_to_zip);
							$archive->compressToPath($pathToZip);
							$saveWorkSheetUnitStatus = eva_gestionDoc::saveNewDoc('fiches_de_groupement', $mainTableElement, $mainIDElement, str_replace(EVA_GENERATED_DOC_DIR, '', $pathToZip . $zipFileName));
							if($saveWorkSheetUnitStatus == 'error')
							{
								$messageInfo = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png\' class=\'messageIcone\' alt=\'error\' />' . __('Une erreur est survenue lors de l\'enregistrement des fiches de groupement pour ce groupement', 'evarisk');
							}
							else
							{
								$messageInfo = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png\' class=\'messageIcone\' alt=\'succes\' />' . __('Les fiches de groupement ont correctement &eacute;t&eacute; enregistr&eacute;es.', 'evarisk');
							}
						$saveZipFileActionMessage = '
	evarisk(document).ready(function(){
			actionMessageShow("#message' . TABLE_DUER . '", "' . $messageInfo . '");
			setTimeout(\'actionMessageHide("#message' . TABLE_DUER . '")\',7500);
	});';
						}

						echo '
<script type="text/javascript" >
	evarisk("#subTabSelector").val("FGP");
	' . $saveZipFileActionMessage . '
	evarisk("#ongletHistoriqueDocument").click();
</script>';
					}
					break;
				}
			break;
			case TABLE_GED_DOCUMENTS:
				$tableElement = $_REQUEST['tableElement'];
				$idElement = $_REQUEST['idElement'];
				switch($_REQUEST['act'])
				{
					case 'loadDocument':
						$category = $_REQUEST['category'];
						$selection = (isset($_REQUEST['selection']) && ($_REQUEST['selection'] != '') && ($_REQUEST['selection'] != '0')) ? eva_tools::IsValid_Variable($_REQUEST['selection']) : '';
						$documentList = eva_gestionDoc::getDocumentList($tableElement, $idElement, $category, "dateCreation DESC");
						if(count($documentList) > 0)
						{
							$modelList = evaDisplayInput::afficherComboBox($documentList, 'modelToUse' . $tableElement . '', '', 'modelToUse' . $tableElement . '', '', $selection);
							if($selection != '')
							{
								$script = '<script type="text/javascript" >evarisk("#modelToUse' . $tableElement . '").val("' . $selection . '")</script>';
							}
						}
						else
						{
							$modelList = '<span style="color:#FF0000;" >' . __('Aucun mod&eacute;le &agrave; pr&eacute;senter', 'evarisk') . '</span>';
						}
						echo '<div style="margin:12px 24px;" class="bold" ><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="modelToUse" style="width:15px;" />' . __('Mod&egrave;le &agrave; utiliser', 'evarisk') . '<br/>' . $modelList . $script . '</div>';
					break;
					case 'duplicateDocument':
						$idDocument = $_REQUEST['idDocument'];
						eva_gestionDoc::duplicateDocument($tableElement, $idElement, $idDocument);
					break;
					case 'loadExistingDocument':
						echo EvaDisplayDesign::getExistingModelList($tableElement, $idElement);
					break;
				}
				break;
			case TABLE_PRECONISATION:
				switch($_REQUEST['act'])
				{
					case 'loadRecommandationManagementForm':
					{
						$id = (isset($_REQUEST['id']) && ($_REQUEST['id'] != '') && ($_REQUEST['id'] != '0')) ? eva_tools::IsValid_Variable($_REQUEST['id']) : '0';
						if($id <= 0)
						{
							$id_preconisation = $nom_preconisation = $description_preconisation = '';
							$id_categorie_preconisation = (isset($_REQUEST['id_categorie_preconisation']) && ($_REQUEST['id_categorie_preconisation'] != '') && ($_REQUEST['id_categorie_preconisation'] != '0')) ? eva_tools::IsValid_Variable($_REQUEST['id_categorie_preconisation']) : '0';
							$moreRecommandationForm = '';
						}
						else
						{
							$recommandationInfos = evaRecommandation::getRecommandation($id);
							$id_categorie_preconisation = $recommandationInfos->id_categorie_preconisation;
							$id_preconisation = $id;
							$nom_preconisation = html_entity_decode($recommandationInfos->nom, ENT_QUOTES, 'UTF-8');
							$description_preconisation = html_entity_decode($recommandationInfos->description, ENT_QUOTES, 'UTF-8');
							$moreRecommandationForm = 
		'evarisk("#recommandationPictureGalery").show();
		evarisk("#pictureGallery' . TABLE_PRECONISATION . '_' . $id . '").html(evarisk("#loadingImg").html());
		evarisk("#pictureGallery' . TABLE_PRECONISATION . '_' . $id . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
		{
			"post":"true",
			"table":"' . TABLE_PRECONISATION . '",
			"act":"reloadGallery",
			"idElement":"' . $id . '"
		});';
						}
						echo evaRecommandation::recommandationForm($id_categorie_preconisation, $id_preconisation, $nom_preconisation, $description_preconisation) . '
<script type="text/javascript" >
	evarisk(document).ready(function(){
		evarisk("#loadingRecommandationForm").html("");
		evarisk("#loadingRecommandationForm").hide();
		evarisk("#recommandationFormContainer").show();
' . $moreRecommandationForm . '
	});
</script>';
					}
					break;
					case 'saveRecommandation':
					{
						$moreRecommandationScript = '';

						$nom_preconisation = (isset($_REQUEST['nom_preconisation']) && ($_REQUEST['nom_preconisation'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['nom_preconisation']) : '';
						$description_preconisation = (isset($_REQUEST['description_preconisation']) && ($_REQUEST['description_preconisation'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['description_preconisation']) : '';
						$id_preconisation = (isset($_REQUEST['id_preconisation']) && ($_REQUEST['id_preconisation'] != '') && ($_REQUEST['id_preconisation'] != '0')) ? eva_tools::IsValid_Variable($_REQUEST['id_preconisation']) : '0';
						$id_categorie_preconisation = (isset($_REQUEST['id_categorie_preconisation']) && ($_REQUEST['id_categorie_preconisation'] != '') && ($_REQUEST['id_categorie_preconisation'] != '0')) ? eva_tools::IsValid_Variable($_REQUEST['id_categorie_preconisation']) : '0';

						$recommandations_informations = array();
						$recommandations_informations['nom'] = $nom_preconisation;
						$recommandations_informations['description'] = $description_preconisation;

						//Check the value of the recommandation identifier. 
						if(($id_preconisation <= 0) && current_user_can('digi_add_recommandation'))
						{	//	If the value is equal or less than 0 we create a new recommandation
							$recommandations_informations['status'] = 'valid';
							$recommandations_informations['id_categorie_preconisation'] = $id_categorie_preconisation;
							$recommandations_informations['creation_date'] = date('Y-m-d H:i:s');
							$recommandationActionResult = evaRecommandation::saveRecommandation($recommandations_informations);
							$moreRecommandationScript .= 
'	evarisk("#recommandationFormContainer").hide();
	evarisk("#loadingRecommandationForm").html(evarisk("#loadingImg").html());
	evarisk("#loadingRecommandationForm").show();
	evarisk("#recommandationInterfaceContainer").dialog("open");
	evarisk("#recommandationInterfaceContainer").dialog({title:"' . __('&Eacute;diter une pr&eacute;conisation', 'evarisk') . '"});
	evarisk("#recommandationFormContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
	{
		"post":"true",
		"table":"' . TABLE_PRECONISATION . '",
		"act":"loadRecommandationManagementForm",
		"id":"' . $recommandationActionResult . '"
	});';
						}
						elseif(($id_preconisation > 0) && current_user_can('digi_edit_recommandation'))
						{	//	If the value is more than 0 we update the corresponding recommandation
							$recommandationActionResult = evaRecommandation::updateRecommandation($recommandations_informations, $id_preconisation);
						}
						else
						{
							$recommandationActionResult = 'userNotAllowed';
						}

						if($recommandationActionResult == 'error')
						{
							$recommandationActionMessage = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png\' class=\'messageIcone\' alt=\'error\' />' . __('Une erreur est survenue lors de l\'enregistrement de la pr&eacute;conisation. Merci de r&eacute;essayer.', 'evarisk');
						}
						elseif($recommandationActionResult == 'userNotAllowed')
						{
							$recommandationActionMessage = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png\' class=\'messageIcone\' alt=\'error\' />' . __('Vous n\'avez pas les droits n&eacute;cessaire pour effectuer cette action', 'evarisk');
						}
						else
						{
							$recommandationActionMessage = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png\' class=\'messageIcone\' alt=\'succes\' />' . __('La pr&eacute;conisation a correctement &eacute;t&eacute; enregistr&eacute;e.', 'evarisk');
							$moreRecommandationScript .= '
	evarisk("#recommandationTable").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
	{
		"post":"true",
		"table":"' . TABLE_PRECONISATION . '",
		"act":"reloadRecommandationList"
	});
	//Line below is used when we add a new recommandation in a category from the postbox
	evarisk("#recommandationCategory' . $id_categorie_preconisation . '").click();';
						}

						echo '
<script type="text/javascript" >
	actionMessageShow("#message", "' . $recommandationActionMessage . '");
	setTimeout(\'actionMessageHide("#message")\', \'7000\');
' . $moreRecommandationScript . '
</script>';
					}
					break;
					case 'reloadRecommandationList':
					{
						echo evaRecommandation::getRecommandationTable();
					}
					break;
					case 'deleteRecommandation':
					{
						$id = (isset($_REQUEST['id']) && ($_REQUEST['id'] != '') && ($_REQUEST['id'] != '0')) ? eva_tools::IsValid_Variable($_REQUEST['id']) : '';
						$recommandations_informations['status'] = 'deleted';
						if(current_user_can('digi_delete_recommandation'))
						{
							$recommandationActionResult = evaRecommandation::updateRecommandation($recommandations_informations, $id);
						}
						else
						{
							$recommandationActionResult = 'userNotAllowed';
						}
						$moreRecommandationScript = '';
						if($recommandationActionResult == 'error')
						{
							$recommandationActionMessage = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png\' class=\'messageIcone\' alt=\'error\' />' . __('Une erreur est survenue lors de la suppression de la pr&eacute;conisation. Merci de r&eacute;essayer.', 'evarisk');
						}
						elseif($recommandationActionResult == 'userNotAllowed')
						{
							$recommandationActionMessage = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png\' class=\'messageIcone\' alt=\'error\' />' . __('Vous n\'avez pas les droits n&eacute;cessaire pour effectuer cette action', 'evarisk');
						}
						else
						{
							$recommandationActionMessage = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png\' class=\'messageIcone\' alt=\'succes\' />' . __('La pr&eacute;conisation a correctement &eacute;t&eacute; supprim&eacute;e.', 'evarisk');
							$moreRecommandationScript = '
	evarisk("#recommandationTable").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
	{
		"post":"true",
		"table":"' . TABLE_PRECONISATION . '",
		"act":"reloadRecommandationList"
	});';
						}

						echo '
<script type="text/javascript" >
	actionMessageShow("#message", "' . $recommandationActionMessage . '");
	setTimeout(\'actionMessageHide("#message")\', \'7000\');
' . $moreRecommandationScript . '
</script>';
					}
					break;
					case 'loadRecomandationOfCategory':
					{
						$outputMode = (isset($_REQUEST['outputMode']) && ($_REQUEST['outputMode'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['outputMode']) : 'pictos';
						$id_categorie_preconisation = (isset($_REQUEST['id_categorie_preconisation']) && ($_REQUEST['id_categorie_preconisation'] != '') && ($_REQUEST['id_categorie_preconisation'] != '0')) ? eva_tools::IsValid_Variable($_REQUEST['id_categorie_preconisation']) : '';
						echo evaRecommandation::getRecommandationListByCategory($id_categorie_preconisation, $outputMode);
					}
					break;

					case 'saveRecommandationLink':
					{
						$id = (isset($_REQUEST['recommandationId']) && ($_REQUEST['recommandationId'] != '') && ($_REQUEST['recommandationId'] != '0')) ? eva_tools::IsValid_Variable($_REQUEST['recommandationId']) : '';
						$recommandationEfficiency = (isset($_REQUEST['recommandationEfficiency']) && ($_REQUEST['recommandationEfficiency'] != '') && ($_REQUEST['recommandationEfficiency'] != '0')) ? eva_tools::IsValid_Variable($_REQUEST['recommandationEfficiency']) : '0';
						$recommandationComment = (isset($_REQUEST['recommandationComment']) && ($_REQUEST['recommandationComment'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['recommandationComment']) : '';
						$id_element = (isset($_REQUEST['id_element']) && ($_REQUEST['id_element'] != '') && ($_REQUEST['id_element'] != '0')) ? eva_tools::IsValid_Variable($_REQUEST['id_element']) : '';
						$table_element = (isset($_REQUEST['table_element']) && ($_REQUEST['table_element'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['table_element']) : '';

						$recommandation_link_action = (isset($_REQUEST['recommandation_link_action']) && ($_REQUEST['recommandation_link_action'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['recommandation_link_action']) : '';
						$recommandation_link_id = (isset($_REQUEST['recommandation_link_id']) && ($_REQUEST['recommandation_link_id'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['recommandation_link_id']) : '';

						$recommandationsinformations = array();
						$recommandationsinformations['id_preconisation'] = $id;
						$recommandationsinformations['efficacite'] = $recommandationEfficiency;
						$recommandationsinformations['commentaire'] = $recommandationComment;

						if($recommandation_link_action == 'update')
						{
							$recommandationActionResult = evaRecommandation::updateRecommandationAssociation($recommandationsinformations, $recommandation_link_id);
						}
						else
						{
							$recommandationsinformations['id_element'] = $id_element;
							$recommandationsinformations['table_element'] = $table_element;
							$recommandationsinformations['status'] = 'valid';
							$recommandationActionResult = evaRecommandation::saveRecommandationAssociation($recommandationsinformations);
						}

						$moreRecommandationScript = '';
						if($recommandationActionResult == 'error')
						{
							$recommandationActionMessage = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png\' class=\'messageIcone\' alt=\'error\' />' . __('Une erreur est survenue lors de l\'enregistrement de la pr&eacute;conisation. Merci de r&eacute;essayer.', 'evarisk');
						}
						else
						{
							$recommandationActionMessage = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png\' class=\'messageIcone\' alt=\'succes\' />' . __('La pr&eacute;conisation a correctement &eacute;t&eacute; enregistr&eacute;e.', 'evarisk');
							$moreRecommandationScript = '
	evarisk("#ongletAjoutPreconisation").click();
	evarisk("#recommandation_link_action").val("add");
	evarisk("#recommandation_link_id").val("");';
						}

						echo '
<script type="text/javascript" >
	actionMessageShow("#message' . TABLE_PRECONISATION . '-' . $table_element . '", "' . $recommandationActionMessage . '");
	setTimeout(\'actionMessageHide("#message' . TABLE_PRECONISATION . '-' . $table_element . '")\', \'7000\');
' . $moreRecommandationScript . '
</script>';
					}
					break;
					case 'deleteRecommandationLink':
					{
						$id = (isset($_REQUEST['id']) && ($_REQUEST['id'] != '') && ($_REQUEST['id'] != '0')) ? eva_tools::IsValid_Variable($_REQUEST['id']) : '';
						$table_element = (isset($_REQUEST['table_element']) && ($_REQUEST['table_element'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['table_element']) : '';
						$recommandations_informations['status'] = 'deleted';
						$recommandationActionResult = evaRecommandation::updateRecommandationAssociation($recommandations_informations, $id);
						$moreRecommandationScript = '';
						if($recommandationActionResult == 'error')
						{
							$recommandationActionMessage = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png\' class=\'messageIcone\' alt=\'error\' />' . __('Une erreur est survenue lors de la d&eacute;saffectation de la pr&eacute;conisation. Merci de r&eacute;essayer.', 'evarisk');
						}
						else
						{
							$recommandationActionMessage = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png\' class=\'messageIcone\' alt=\'succes\' />' . __('La pr&eacute;conisation a correctement &eacute;t&eacute; d&eacute;saffect&eacute;e.', 'evarisk');
							$moreRecommandationScript = '
	evarisk("#ongletListePreconisation").click();';
						}

						echo '
<script type="text/javascript" >
	actionMessageShow("#message' . TABLE_PRECONISATION . '-' . $table_element .'", "' . $recommandationActionMessage . '");
	setTimeout(\'actionMessageHide("#message' . TABLE_PRECONISATION . '-' . $table_element .'")\', \'7000\');
' . $moreRecommandationScript . '
</script>';
					}
					break;
					case 'loadRecomandationForElement':
					{
						$id_element = (isset($_REQUEST['id_element']) && ($_REQUEST['id_element'] != '') && ($_REQUEST['id_element'] != '0')) ? eva_tools::IsValid_Variable($_REQUEST['id_element']) : '';
						$table_element = (isset($_REQUEST['table_element']) && ($_REQUEST['table_element'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['table_element']) : '';

						echo evaRecommandation::getRecommandationListForElementOutput($table_element, $id_element);
					}
					break;
					case 'loadRecomandationLink':
					{
						$recommandation_link_id = (isset($_REQUEST['recommandation_link_id']) && ($_REQUEST['recommandation_link_id'] != '') && ($_REQUEST['recommandation_link_id'] != '0')) ? eva_tools::IsValid_Variable($_REQUEST['recommandation_link_id']) : '0';
						$recommandation_link_action = (isset($_REQUEST['recommandation_link_action']) && ($_REQUEST['recommandation_link_action'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['recommandation_link_action']) : '';
						$outputMode = (isset($_REQUEST['outputMode']) && ($_REQUEST['outputMode'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['outputMode']) : '';
						$table_element = (isset($_REQUEST['table_element']) && ($_REQUEST['table_element'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['table_element']) : '';
						$id_element = (isset($_REQUEST['id_element']) && ($_REQUEST['id_element'] != '') && ($_REQUEST['id_element'] != '0')) ? eva_tools::IsValid_Variable($_REQUEST['id_element']) : '0';
						$selectRecommandation = '';

						if(($recommandation_link_action == 'update') && ($recommandation_link_id > 0))
						{
							$selectedRecommandation = evaRecommandation::getRecommandationListForElement($table_element, $id_element, $recommandation_link_id);
							$selectRecommandation['id_categorie_preconisation'] = $selectedRecommandation[0]->recommandation_category_id;
							$selectRecommandation['id_preconisation'] = $selectedRecommandation[0]->id_preconisation;
							$selectRecommandation['commentaire_preconisation'] = $selectedRecommandation[0]->commentaire;
							$selectRecommandation['efficacite_preconisation'] = $selectedRecommandation[0]->efficacite;
						}

						echo evaRecommandation::recommandationAssociation($outputMode, $selectRecommandation);;
					}
					break;
				}
				break;
			case TABLE_CATEGORIE_PRECONISATION:
				switch($_REQUEST['act'])
				{
					case 'deleteRecommandationCategory':
					{
						$id = (isset($_REQUEST['id']) && ($_REQUEST['id'] != '') && ($_REQUEST['id'] != '0')) ? eva_tools::IsValid_Variable($_REQUEST['id']) : '';
						$recommandations_informations['status'] = 'deleted';
						if(current_user_can('digi_delete_recommandation_cat'))
						{
							$recommandationActionResult = evaRecommandationCategory::updateRecommandationCategory($recommandations_informations, $id);
						}
						else
						{
							$recommandationActionResult = 'userNotAllowed';
						}
						$moreRecommandationScript = '';
						if($recommandationActionResult == 'error')
						{
							$recommandationActionMessage = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png\' class=\'messageIcone\' alt=\'error\' />' . __('Une erreur est survenue lors de la suppression de la famille de pr&eacute;conisation. Merci de r&eacute;essayer.', 'evarisk');
						}
						elseif($recommandationActionResult == 'userNotAllowed')
						{
							$recommandationActionMessage = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png\' class=\'messageIcone\' alt=\'error\' />' . __('Vous n\'avez pas les droits n&eacute;cessaire pour effectuer cette action', 'evarisk');
						}
						else
						{
							$recommandationActionMessage = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png\' class=\'messageIcone\' alt=\'succes\' />' . __('La famille de pr&eacute;conisation a correctement &eacute;t&eacute; supprim&eacute;e.', 'evarisk');
							$moreRecommandationScript = '
	evarisk("#recommandationTable").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
	{
		"post":"true",
		"table":"' . TABLE_PRECONISATION . '",
		"act":"reloadRecommandationList"
	});';
						}

						echo '
<script type="text/javascript" >
	actionMessageShow("#message", "' . $recommandationActionMessage . '");
	setTimeout(\'actionMessageHide("#message")\', \'7000\');
' . $moreRecommandationScript . '
</script>';
					}
					break;
					case 'saveRecommandationCategorie':
					{
						$moreRecommandationCategoryScript = '';

						$nom_categorie = (isset($_REQUEST['nom_categorie']) && ($_REQUEST['nom_categorie'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['nom_categorie']) : '';
						$impressionRecommandationCategorie = (isset($_REQUEST['impressionRecommandationCategorie']) && ($_REQUEST['impressionRecommandationCategorie'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['impressionRecommandationCategorie']) : '';
						$impressionRecommandation = (isset($_REQUEST['impressionRecommandation']) && ($_REQUEST['impressionRecommandation'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['impressionRecommandation']) : '';
						$tailleimpressionRecommandationCategorie = (isset($_REQUEST['tailleimpressionRecommandationCategorie']) && ($_REQUEST['tailleimpressionRecommandationCategorie'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['tailleimpressionRecommandationCategorie']) : '';
						$tailleimpressionRecommandation = (isset($_REQUEST['tailleimpressionRecommandation']) && ($_REQUEST['tailleimpressionRecommandation'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['tailleimpressionRecommandation']) : '';
						$id_categorie_preconisation = (isset($_REQUEST['id_categorie_preconisation']) && ($_REQUEST['id_categorie_preconisation'] != '') && ($_REQUEST['id_categorie_preconisation'] != '0')) ? eva_tools::IsValid_Variable($_REQUEST['id_categorie_preconisation']) : '0';

						$recommandationCategory_informations = array();
						$recommandationCategory_informations['nom'] = $nom_categorie;
						$recommandationCategory_informations['impressionRecommandation'] = $impressionRecommandation;
						$recommandationCategory_informations['tailleimpressionRecommandation'] = str_replace(',', '.', $tailleimpressionRecommandation);
						$recommandationCategory_informations['impressionRecommandationCategorie'] = $impressionRecommandationCategorie;
						$recommandationCategory_informations['tailleimpressionRecommandationCategorie'] = str_replace(',', '.', $tailleimpressionRecommandationCategorie);

						//Check the value of the recommandation identifier. 
						if(($id_categorie_preconisation <= 0) && current_user_can('digi_edit_recommandation_cat'))
						{	//	If the value is equal or less than 0 we create a new recommandation
							$recommandationCategory_informations['status'] = 'valid';
							$recommandationCategory_informations['creation_date'] = date('Y-m-d H:i:s');
							$recommandationActionResult = evaRecommandationCategory::saveRecommandationCategory($recommandationCategory_informations);
							$moreRecommandationCategoryScript .= '';
						}
						elseif(($id_categorie_preconisation > 0) && current_user_can('digi_edit_recommandation_cat'))
						{	//	If the value is more than 0 we update the corresponding recommandation
							$recommandationActionResult = evaRecommandationCategory::updateRecommandationCategory($recommandationCategory_informations, $id_categorie_preconisation);
						}
						else
						{
							$recommandationActionResult = 'userNotAllowed';
						}

						if($recommandationActionResult == 'error')
						{
							$recommandationCategoryActionMessage = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png\' class=\'messageIcone\' alt=\'error\' />' . __('Une erreur est survenue lors de l\'enregistrement de la famille de pr&eacute;conisation. Merci de r&eacute;essayer.', 'evarisk');
						}
						elseif($recommandationActionResult == 'userNotAllowed')
						{
							$recommandationActionMessage = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png\' class=\'messageIcone\' alt=\'error\' />' . __('Vous n\'avez pas les droits n&eacute;cessaire pour effectuer cette action', 'evarisk');
						}
						else
						{
							$recommandationCategoryActionMessage = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png\' class=\'messageIcone\' alt=\'succes\' />' . __('La famille de pr&eacute;conisation a correctement &eacute;t&eacute; enregistr&eacute;e.', 'evarisk');
							$moreRecommandationCategoryScript .= '
	evarisk("#recommandationTable").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
	{
		"post":"true",
		"table":"' . TABLE_PRECONISATION . '",
		"act":"reloadRecommandationList"
	});';
						}

						echo '
<script type="text/javascript" >
	actionMessageShow("#message", "' . $recommandationCategoryActionMessage . '");
	setTimeout(\'actionMessageHide("#message")\', \'7000\');
' . $moreRecommandationCategoryScript . '
</script>';
					}
					break;
					case 'loadRecommandationCategoryManagementForm':
					{
						$id = (isset($_REQUEST['id']) && ($_REQUEST['id'] != '') && ($_REQUEST['id'] != '0')) ? eva_tools::IsValid_Variable($_REQUEST['id']) : '0';
						if($id <= 0)
						{
							$id_preconisation = $nom_preconisation = $description_preconisation = '';
							$id_categorie_preconisation = (isset($_REQUEST['id_categorie_preconisation']) && ($_REQUEST['id_categorie_preconisation'] != '') && ($_REQUEST['id_categorie_preconisation'] != '0')) ? eva_tools::IsValid_Variable($_REQUEST['id_categorie_preconisation']) : '0';
							$moreRecommandationForm = '';
						}
						else
						{
							$recommandationCategoryInfos = evaRecommandationCategory::getCategoryRecommandation($id);
							$id_categorie_preconisation = $id;
							$moreRecommandationForm = 
		'evarisk("#recommandationCategoryPictureGalery").show();
		evarisk("#pictureGallery' . TABLE_CATEGORIE_PRECONISATION . '_' . $id . '").html(evarisk("#loadingImg").html());
		evarisk("#pictureGallery' . TABLE_CATEGORIE_PRECONISATION . '_' . $id . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
		{
			"post":"true",
			"table":"' . TABLE_CATEGORIE_PRECONISATION . '",
			"act":"reloadGallery",
			"idElement":"' . $id . '"
		});';
						}
						echo evaRecommandationCategory::recommandationCategoryForm($id_categorie_preconisation, $recommandationCategoryInfos) . '
<script type="text/javascript" >
	evarisk(document).ready(function(){
		evarisk("#loadingCategoryRecommandationForm").html("");
		evarisk("#loadingCategoryRecommandationForm").hide();
		evarisk("#recommandationCategoryFormContainer").show();
' . $moreRecommandationForm . '
	});
</script>';
					}
					break;
				}
				break;
			case DIGI_DBT_LIAISON_USER_GROUP:
				switch($_REQUEST['act'])
				{
					case 'save':
						digirisk_groups::setLinkGroupElement($_REQUEST['tableElement'], $_REQUEST['idElement'], $_REQUEST['element']);
					break;
				}
				break;
			case DIGI_DBT_LIAISON_PRODUIT_ELEMENT:
				switch($_REQUEST['act'])
				{
					case 'save':
						digirisk_product::setLinkProductElement($_REQUEST['tableElement'], $_REQUEST['idElement'], $_REQUEST['element']);
					break;
					case 'reloadCategoryChoice':
						echo digirisk_product::affectationPostBoxContent($_REQUEST['tableElement'], $_REQUEST['idElement'], true, $_REQUEST['category']);
					break;
				}
				break;
			case DIGI_DBT_PERMISSION:
				switch($_REQUEST['act'])
				{
					case 'save':
					{
						$actionResponse = '';
						$tableElement = (isset($_REQUEST['tableElement']) && ($_REQUEST['tableElement'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['tableElement']) : '';
						$idElement = (isset($_REQUEST['idElement']) && ($_REQUEST['idElement'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['idElement']) : '';

						$rightType = array('user_see', 'user_edit', 'user_delete', 'user_add_gpt', 'user_add_unit');

						/*	Read the recursiv content to set recursiv right for the selected user	*/
						$recursivRight = '';
						$recursivRightUser = (isset($_REQUEST['user_recursif']) && ($_REQUEST['user_recursif'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['user_recursif']) : '';
						if($recursivRightUser != '')
						{
							$recursivUsers = explode('#!#', $recursivRightUser);
							foreach($recursivUsers as $recursivUser)
							{
								$userRecursiv = explode('!#!', $recursivUser);
								if(isset($userRecursiv[1]) && ($userRecursiv[1] != ''))
								{
									$recursivRight[] = $userRecursiv[1];
								}
							}
						}

						/*	Read the different right for user attribution	*/
						foreach($rightType as $rightName)
						{
							$right = (isset($_REQUEST[$rightName]) && ($_REQUEST[$rightName] != '')) ? eva_tools::IsValid_Variable($_REQUEST[$rightName]) : '';
							$oldRight = (isset($_REQUEST[$rightName . '_old']) && ($_REQUEST[$rightName . '_old'] != '')) ? eva_tools::IsValid_Variable($_REQUEST[$rightName . '_old']) : '';

							if($oldRight != '')
							{
								$userElements = explode('#!#', $oldRight);
								foreach($userElements as $userElement)
								{
									$userRight = explode('!#!', $userElement);
									if(isset($userRight[1]) && ($userRight[1] != ''))
									{
										$oldAssignedRight[$userRight[0]][] = $userRight[1];
									}
								}
							}

							if($right != '')
							{
								$userElements = explode('#!#', $right);
								foreach($userElements as $userElement)
								{
									$userRight = explode('!#!', $userElement);
									if(isset($userRight[1]) && ($userRight[1] != ''))
									{
										$newAssignedRight[$userRight[0]][] = $userRight[1];
										$user = new WP_User($userRight[1]);
										if(!$user->has_cap($userRight[0]))
										{
											$user->add_cap($userRight[0]);
										}

										switch($tableElement)
										{
											case TABLE_GROUPEMENT:
												if(is_array($recursivRight) && in_array($userRight[1], $recursivRight))
												{
													digirisk_permission::addRecursivRight($tableElement, $idElement, $user, str_replace('groupement_' . $idElement, '', $userRight[0]), 'add');
												}
											break;
										}
									}
								}
							}

							$actionResponse .= 'evarisk("#' . $rightName . '_old' . '").val("' . $right . '");
	';

							if(is_array($oldAssignedRight))
							{
								foreach($oldAssignedRight as $permissionName => $permissionUser)
								{
									foreach($permissionUser as $userId)
									{
										if((is_array($newAssignedRight) && isset($newAssignedRight[$permissionName]) && (!in_array($userId, $newAssignedRight[$permissionName]))) || !is_array($newAssignedRight))
										{
											$user = new WP_User($userId);
											if($user->has_cap($permissionName))
											{
												$user->remove_cap($permissionName);
											}

											switch($tableElement)
											{
												case TABLE_GROUPEMENT:
													if(is_array($recursivRight) && in_array($userId, $recursivRight))
													{
														digirisk_permission::addRecursivRight($tableElement, $idElement, $user, str_replace('groupement_' . $idElement, '', $permissionName), 'remove');
													}
												break;
											}
										}
									}
								}

								unset($oldAssignedRight);
								unset($newAssignedRight);
							}
						}

						$message = '<img src=\'' . EVA_MESSAGE_SUCCESS . '\' alt=\'' . $actionResult . '\' class=\'messageIcone\' />' . __('Les droits ont bien &eacute;t&eacute; mis &agrave; jour', 'evarisk');

						echo '
<script type="text/javascript" >
	' . $actionResponse . '
	evarisk("#saveButtonLoading_userRight' . $tableElement . '").hide();
	evarisk("#saveButtonContainer_userRight' . $tableElement . '").show();

	actionMessageShow("#' . $_REQUEST['message'] . '", "' . $message . '");
	setTimeout(\'actionMessageHide("#' . $_REQUEST['message'] . '")\',7500);

	evarisk("#' . $_REQUEST['tableContainer'] . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
		"post": "true", 
		"table": "' . DIGI_DBT_PERMISSION . '",
		"act": "reload_user_right_box",
		"tableElement": "' . $tableElement . '",
		"idElement": "' . $idElement . '"
	});
</script>';
					}
					break;
					case 'reload_user_right_box':
						echo digirisk_permission::generateUserListForRightDatatable($_REQUEST['tableElement'], $_REQUEST['idElement']);
					break;
				}
				break;
			case DIGI_DBT_ACCIDENT:
				switch($_REQUEST['act'])
				{
					case 'save-accident':
					{
						$save_result = '';
						$current_accident = null;
						$tableElement = (isset($_REQUEST['tableElement']) && ($_REQUEST['tableElement'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['tableElement']) : '';
						$idElement = (isset($_REQUEST['idElement']) && ($_REQUEST['idElement'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['idElement']) : '';
						$accident_id = (isset($_REQUEST['accident_id']) && ($_REQUEST['accident_id'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['accident_id']) : 0;
						$accident_form_step = (isset($_REQUEST['accident_form_step']) && ($_REQUEST['accident_form_step'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['accident_form_step']) : 1;

						/*	Create the accident if not existing into database	*/
						if($accident_id <= 0){
							$accident_main_informations['status'] = 'valid';
							$accident_main_informations['creation_date'] = date('Y-m-d H:i:s');
							$accident_main_informations['id_element'] = $idElement;
							$accident_main_informations['table_element'] = $tableElement;
							$accident_main_informations['declaration_state'] = 'in_progress';
							$accident_main_informations['declaration_step'] = 1;
							$save_result = eva_database::save($accident_main_informations, DIGI_DBT_ACCIDENT);
							$accident_id = $wpdb->insert_id;
						}
						else{
							$current_accident = digirisk_accident::getElement($accident_id);
						}

						/*	Define the message by checking the action result	*/
						if($save_result == 'error'){
							$message = '<img src=\'' . EVA_MESSAGE_ERROR . '\' alt=\'' . $save_result . '\' class=\'messageIcone\' />' . __('Une erreur est survenue lors de l\'enregistrement de l\'accident de travail', 'evarisk');
						}
						elseif(in_array($save_result, array('done', 'nothingToUpdate'))){/*	If creation	*/
							$message = '<img src=\'' . EVA_MESSAGE_SUCCESS . '\' alt=\'' . $save_result . '\' class=\'messageIcone\' />' . __('L\'accident de travail a bien &eacute;t&eacute; sauvegard&eacute;', 'evarisk');
						}

						if($accident_form_step >= 1){/*	Save first step => Employer and establishment	*/
							/*	Save employer informations	*/
							$location['employer']['status'] = 'valid';
							$location['employer']['id_accident'] = $accident_id;
							$location['employer']['id_location'] = $_POST['employer']['id'];
							$location['employer']['location_type'] = 'employer';
							$location['employer']['telephone'] = $_POST['employer']['telephone'];
							$location['employer']['name'] = $_POST['employer']['name'];
							$location['employer']['adress_line_1'] = $_POST['employer']['address_1'];
							$location['employer']['adress_line_2'] = $_POST['employer']['address_2'];
							$location['employer']['adress_postal_code'] = $_POST['employer']['postal_code'];
							$location['employer']['adress_city'] = $_POST['employer']['city'];
							if(($current_accident != null) && ($current_accident->declaration_state == 'in_progress') && ($current_accident->employer_location_id > 0)){
								$location['employer']['last_update_date'] = date('Y-m-d H:i:s');
								$save_result = eva_database::update($location['employer'], $current_accident->employer_location_id, DIGI_DBT_ACCIDENT_LOCATION);
							}
							else{
								if(($current_accident != null) && ($current_accident->declaration_state == 'done')){
									$location_employer['last_update_date'] = date('Y-m-d H:i:s');
									$location_employer['status'] = 'moderated';
									$save_result = eva_database::update($location_employer, $current_accident->employer_location_id, DIGI_DBT_ACCIDENT_LOCATION);
								}
								$location['employer']['creation_date'] = date('Y-m-d H:i:s');
								$save_result = eva_database::save($location['employer'], DIGI_DBT_ACCIDENT_LOCATION);
							}

							/*	Save establishment informations	*/
							$location['establishment']['status'] = 'valid';
							$location['establishment']['id_accident'] = $accident_id;
							$location['establishment']['id_location'] = $_POST['establishment']['id'];
							$location['establishment']['location_type'] = 'establishment';
							$location['establishment']['telephone'] = $_POST['establishment']['telephone'];
							$location['establishment']['name'] = $_POST['establishment']['name'];
							$location['establishment']['siret'] = $_POST['establishment']['siret'];
							$location['establishment']['social_activity_number'] = $_POST['establishment']['social_activity_number'];
							$location['establishment']['adress_line_1'] = $_POST['establishment']['address_1'];
							$location['establishment']['adress_line_2'] = $_POST['establishment']['address_2'];
							$location['establishment']['adress_postal_code'] = $_POST['establishment']['postal_code'];
							$location['establishment']['adress_city'] = $_POST['establishment']['city'];
							if(($current_accident != null) && ($current_accident->declaration_state == 'in_progress') && ($current_accident->establishment_location_id > 0)){
								$location['establishment']['last_update_date'] = date('Y-m-d H:i:s');
								$save_result = eva_database::update($location['establishment'], $current_accident->establishment_location_id, DIGI_DBT_ACCIDENT_LOCATION);
							}
							else{
								if(($current_accident != null) && ($current_accident->declaration_state == 'done')){
									$location_establishment['last_update_date'] = date('Y-m-d H:i:s');
									$location_establishment['status'] = 'moderated';
									$save_result = eva_database::update($location_establishment, $current_accident->establishment_location_id, DIGI_DBT_ACCIDENT_LOCATION);
								}
								$location['establishment']['creation_date'] = date('Y-m-d H:i:s');
								$save_result = eva_database::save($location['establishment'], DIGI_DBT_ACCIDENT_LOCATION);
							}

							unset($location);

							/*	Update accident form step	*/
							if($save_result != 'error'){
								$accident_main_informations['declaration_step'] = 2;
								$accident_main_informations['declaration_state'] = 'in_progress';
							}
						}
						if($accident_form_step >= 2){/*	Save second step => Victim	*/
							$victim['status'] = 'valid';
							$victim['id_accident'] = $accident_id;
							$victim['id_user'] = $_POST['accident_user']['victim_id'];
							$victim['victim_seniority'] = $_POST['accident_user']['accident_user_seniority'];
							$accident_main_informations['accident_make_other_victim'] = $_POST['accident_user']['accident_make_other_victims'];
							$user_meta = get_user_meta($_POST['accident_user']['victim_id'], 'digirisk_information', false);
							$victim['victim_meta'] = serialize($user_meta[0]);
							if(($current_accident != null) && ($current_accident->declaration_state == 'in_progress') && ($current_accident->accident_victim_id > 0)){
								$victim['last_update_date'] = date('Y-m-d H:i:s');
								$save_result = eva_database::update($victim, $current_accident->accident_victim_id, DIGI_DBT_ACCIDENT_VICTIM);
							}
							else{
								if(($current_accident != null) && ($current_accident->declaration_state == 'done')){
									$current_victim['last_update_date'] = date('Y-m-d H:i:s');
									$current_victim['status'] = 'moderated';
									$save_result = eva_database::update($current_victim, $current_accident->accident_victim_id, DIGI_DBT_ACCIDENT_VICTIM);
								}
								$victim['creation_date'] = date('Y-m-d H:i:s');
								$save_result = eva_database::save($victim, DIGI_DBT_ACCIDENT_VICTIM);
							}

							unset($victim);

							/*	Update accident form step	*/
							if($save_result != 'error'){
								$accident_main_informations['declaration_step'] = 3;
								$accident_main_informations['declaration_state'] = 'in_progress';
							}
						}
						if($accident_form_step >= 3){/*	Save third step => accident	*/
							$accident['status'] = 'valid';
							$accident['id_accident'] = $accident_id;
							$accident['accident_victim_transported_at'] = $_POST['accident']['accident_victim_transported_at'];
							$accident['accident_place'] = $_POST['accident']['accident_place'];
							$accident['accident_consequence'] = $_POST['accident']['accident_consequence'];
							$accident['accident_victim_work_shedule'] = serialize(array('from1' => sprintf('%02d', $_POST['accident']['accident_victim_work_shedule']['accident_from_hour_1']) . ':' . sprintf('%02d', $_POST['accident']['accident_victim_work_shedule']['accident_from_minute_1']), 'to1' => sprintf('%02d', $_POST['accident']['accident_victim_work_shedule']['accident_to_hour_1']) . ':' . sprintf('%02d', $_POST['accident']['accident_victim_work_shedule']['accident_to_minute_1']), 'from2' => sprintf('%02d', $_POST['accident']['accident_victim_work_shedule']['accident_from_hour_2']) . ':' . sprintf('%02d', $_POST['accident']['accident_victim_work_shedule']['accident_from_minute_2']), 'to2' => sprintf('%02d', $_POST['accident']['accident_victim_work_shedule']['accident_to_hour_2']) . ':' . sprintf('%02d', $_POST['accident']['accident_victim_work_shedule']['accident_to_minute_2'])));
							$accident['accident_declaration'] = serialize($_POST['accident']['accident_declaration']);
							$accident['accident_details'] = $_POST['accident']['accident_details'];
							$accident['accident_hurt_place'] = $_POST['accident']['accident_hurt_place'];
							$accident['accident_hurt_nature'] = $_POST['accident']['accident_hurt_nature'];

							$accident_time = explode(" ", $_POST['accident']['accident_date']);
							$accident['accident_date'] = $accident_time[0];
							$accident['accident_hour'] = $accident_time[1];

							$accident_main_informations['accident_date'] = $accident['accident_date'];
							$accident_main_informations['accident_hour'] = $accident['accident_hour'];
							$accident_main_informations['accident_title'] = $_POST['accident']['accident_title'];
	
							if(($current_accident != null) && ($current_accident->declaration_state == 'in_progress') && ($current_accident->accident_details_id > 0)){
								$accident['last_update_date'] = date('Y-m-d H:i:s');
								$save_result = eva_database::update($accident, $current_accident->accident_details_id, DIGI_DBT_ACCIDENT_DETAILS);
							}
							else{
								if(($current_accident != null) && ($current_accident->declaration_state == 'done')){
									$current_accident_details['last_update_date'] = date('Y-m-d H:i:s');
									$current_accident_details['status'] = 'moderated';
									$save_result = eva_database::update($current_accident_details, $current_accident->accident_details_id, DIGI_DBT_ACCIDENT_DETAILS);
								}
								$accident['creation_date'] = date('Y-m-d H:i:s');
								$save_result = eva_database::save($accident, DIGI_DBT_ACCIDENT_DETAILS);
							}

							unset($accident);

							/*	Update accident form step	*/
							if($save_result != 'error'){
								$accident_main_informations['declaration_step'] = 4;
								$accident_main_informations['declaration_state'] = 'in_progress';
							}
						}
						if($accident_form_step >= 4){/*	Save four step => witnesses	*/
							if(isset($_POST['accident_witness']) && is_array($_POST['accident_witness'])){
								foreach($_POST['accident_witness'] as $witness_index => $witness_infos){
									$user_meta = get_user_meta($witness_infos['user_id'], 'digirisk_information', false);
									$user_main_info = evaUser::getUserInformation($witness_infos['user_id']);
									$accident_witness['status'] = 'valid';
									$accident_witness['third_party_type'] = 'witness';
									$accident_witness['id_user'] = $witness_infos['user_id'];
									$accident_witness['id_accident'] = $accident_id;
									$accident_witness['firstname'] = $user_main_info[$witness_infos['user_id']]['user_firstname'];
									$accident_witness['lastname'] = $user_main_info[$witness_infos['user_id']]['user_lastname'];
									$accident_witness['adress_line_1'] = $user_meta[0]['user_adress'];
									$accident_witness['adress_line_2'] = $user_meta[0]['user_adress_2'];

									if($witness_infos['user_id'] > 0){
										if(($current_accident != null) && ($current_accident->declaration_state == 'in_progress') 
												&& (isset($witness_infos['tparty_id']) && ($witness_infos['tparty_id'] > 0)) ){
											$accident_witness['last_update_date'] = date('Y-m-d H:i:s');
											$save_hurt_result = eva_database::update($accident_witness, $witness_infos['tparty_id'], DIGI_DBT_ACCIDENT_THIRD_PARTY);
										}
										else{
											if(($current_accident != null) && ($current_accident->declaration_state == 'done')){
												$current_accident_witness['last_update_date'] = date('Y-m-d H:i:s');
												$current_accident_witness['status'] = 'moderated';
												$save_result = eva_database::update($current_accident_witness, $witness_infos['tparty_id'], DIGI_DBT_ACCIDENT_THIRD_PARTY);
											}
											$accident_witness['creation_date'] = date('Y-m-d H:i:s');
											$save_hurt_result = eva_database::save($accident_witness, DIGI_DBT_ACCIDENT_THIRD_PARTY);
										}
									}
								}
							}
							$accident_main_informations['police_report'] = $_POST['accident_police_report'];
							$accident_main_informations['police_report_writer'] = $_POST['accident_police_report_writer'];

							unset($accident_witness);

							/*	Update accident form step	*/
							if($save_result != 'error'){
								$accident_main_informations['declaration_step'] = 5;
								$accident_main_informations['declaration_state'] = 'in_progress';
							}
						}
						if($accident_form_step >= 5){/*	Save four step => third party	*/
							if(isset($_POST['accident_third_party']) && is_array($_POST['accident_third_party'])){
								foreach($_POST['accident_third_party'] as $third_party_index => $third_party_infos){
									$user_meta = get_user_meta($third_party_infos['user_id'], 'digirisk_information', false);
									$user_main_info = evaUser::getUserInformation($third_party_infos['user_id']);
									$accident_third_party['status'] = 'valid';
									$accident_third_party['third_party_type'] = 'third_party';
									$accident_third_party['id_user'] = $third_party_infos['user_id'];
									$accident_third_party['id_accident'] = $accident_id;
									$accident_third_party['firstname'] = $user_main_info[$third_party_infos['user_id']]['user_firstname'];
									$accident_third_party['lastname'] = $user_main_info[$third_party_infos['user_id']]['user_lastname'];
									$accident_third_party['adress_line_1'] = $user_meta[0]['user_adress'];
									$accident_third_party['adress_line_2'] = $user_meta[0]['user_adress_2'];

									if($third_party_infos['user_id'] > 0){
										if(($current_accident != null) && ($current_accident->declaration_state == 'in_progress') 
												&& (isset($third_party_infos['tparty_id']) && ($third_party_infos['tparty_id'] > 0)) ){
											$accident_third_party['last_update_date'] = date('Y-m-d H:i:s');
											$save_hurt_result = eva_database::update($accident_third_party, $third_party_infos['tparty_id'], DIGI_DBT_ACCIDENT_THIRD_PARTY);
										}
										else{
											if(($current_accident != null) && ($current_accident->declaration_state == 'done')){
												$current_accident_third_party['last_update_date'] = date('Y-m-d H:i:s');
												$current_accident_third_party['status'] = 'moderated';
												$save_result = eva_database::update($current_accident_third_party, $third_party_infos['tparty_id'], DIGI_DBT_ACCIDENT_THIRD_PARTY);
											}
											$accident_witness['creation_date'] = date('Y-m-d H:i:s');
											$save_hurt_result = eva_database::save($accident_third_party, DIGI_DBT_ACCIDENT_THIRD_PARTY);
										}
									}
								}
							}
							$accident_main_informations['accident_caused_by_third_party'] = $_POST['accident_caused_by_third_party'];

							unset($accident_third_party);

							/*	Update accident form step	*/
							if($save_result != 'error'){
								$accident_main_informations['declaration_step'] = 5;
								$accident_main_informations['declaration_state'] = 'done';
							}
						}

						$accident_main_informations['last_update_date'] = date('Y-m-d H:i:s');
						$save_result = eva_database::update($accident_main_informations, $accident_id, DIGI_DBT_ACCIDENT);
						if(($save_result == 'done') || ($save_result == 'nothingToUpdate')){
							$message = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png\' alt=\'response\' style=\'vertical-align:middle;\' />&nbsp;' . __('L\'accident a &eacute;t&eacute; correctement mis &agrave; jour', 'evarisk');
						}
						elseif($save_result == 'error'){
							$message = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png\' alt=\'response\' style=\'vertical-align:middle;\' />&nbsp;' . __('Une erreur est survenue lors de l\'enregistrement de l\'accident', 'evarisk');
						}

						echo '
<script type="text/javascript" >
	evarisk(document).ready(function(){
		actionMessageShow("#message_accident", "' . $message . '");
		setTimeout(\'actionMessageHide("#message_accident")\',7500);			
		jQuery("#divAccidenContainer").html(jQuery("#loadingImg").html());
		jQuery("#divAccidenContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",{
			"post":"true",
			"table":"' . DIGI_DBT_ACCIDENT . '",
			"act":"load",
			"accident_id": "' . $accident_id . '",
			"idElement":"' . $idElement . '",
			"tableElement":"' . $tableElement . '"
		});
	});
</script>';
					}
					break;

					case 'reloadVoirAccident':
					{
						$tableElement = (isset($_REQUEST['tableElement']) && ($_REQUEST['tableElement'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['tableElement']) : '';
						$idElement = (isset($_REQUEST['idElement']) && ($_REQUEST['idElement'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['idElement']) : '';
						echo digirisk_accident::get_accident_list($tableElement, $idElement);
					}
					break;

					case 'addAccident':
					{
						$tableElement = (isset($_REQUEST['tableElement']) && ($_REQUEST['tableElement'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['tableElement']) : '';
						$idElement = (isset($_REQUEST['idElement']) && ($_REQUEST['idElement'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['idElement']) : '';
						echo digirisk_accident::get_accident_form($tableElement, $idElement);
					}
					break;

					case 'load':
					{
						$tableElement = (isset($_REQUEST['tableElement']) && ($_REQUEST['tableElement'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['tableElement']) : '';
						$idElement = (isset($_REQUEST['idElement']) && ($_REQUEST['idElement'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['idElement']) : '';
						$accident_id = (isset($_REQUEST['accident_id']) && ($_REQUEST['accident_id'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['accident_id']) : '';
						echo digirisk_accident::get_accident_form($tableElement, $idElement, $accident_id);
					}
					break;

					case 'delete_accident':
					{
						$tableElement = (isset($_REQUEST['tableElement']) && ($_REQUEST['tableElement'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['tableElement']) : '';
						$idElement = (isset($_REQUEST['idElement']) && ($_REQUEST['idElement'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['idElement']) : '';
						$accident_id = (isset($_REQUEST['accident_id']) && ($_REQUEST['accident_id'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['accident_id']) : '';

						$accident_main_informations['status'] = 'deleted';
						$accident_main_informations['last_update_date'] = date('Y-m-d H:i:s');
						$save_result = eva_database::update($accident_main_informations, $accident_id, DIGI_DBT_ACCIDENT);
						if(($save_result == 'done') || ($save_result == 'nothingToUpdate')){
							$message = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png\' alt=\'response\' style=\'vertical-align:middle;\' />&nbsp;' . __('L\'accident a &eacute;t&eacute; correctement supprim&eacute;', 'evarisk');
						}
						elseif($save_result == 'error'){
							$message = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png\' alt=\'response\' style=\'vertical-align:middle;\' />&nbsp;' . __('Une erreur est survenue lors de la suppression de l\'accident', 'evarisk');
						}

						echo '
<script type="text/javascript" >
	evarisk(document).ready(function(){
		actionMessageShow("#message_accident", "' . $message . '");
		setTimeout(\'actionMessageHide("#message_accident")\',7500);			
		jQuery("#divAccidenContainer").html(jQuery("#loadingImg").html());
		jQuery("#divAccidenContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",{
			"post":"true",
			"table":"' . DIGI_DBT_ACCIDENT . '",
			"act":"reloadVoirAccident",
			"idElement":"' . $idElement . '",
			"tableElement":"' . $tableElement . '"
		});
	});
</script>';
					}
					break;

					case 'delete_accident_hurt':
					{
						$hurt_id = (isset($_REQUEST['hurt_id']) && ($_REQUEST['hurt_id'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['hurt_id']) : '';
						$line_id = (isset($_REQUEST['line_id']) && ($_REQUEST['line_id'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['line_id']) : '';
						$hurt['status'] = 'deleted';
						$hurt['last_update_date'] = date('Y-m-d H:i:s');
						$save_hurt_result = eva_database::update($hurt, $hurt_id, DIGI_DBT_ACCIDENT_LESION);
						if(($save_hurt_result == 'done') || ($save_hurt_result == 'nothingToUpdate')){
							echo '
<script type="text/javascript" >
	evarisk(document).ready(function(){
		remove_current_line(' . $line_id . ');
	});
</script>';
						}
					}
					break;

					case 'reload_accident_place_part':
					{
						$tableElement = (isset($_REQUEST['tableElement']) && ($_REQUEST['tableElement'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['tableElement']) : '';
						$idElement = (isset($_REQUEST['idElement']) && ($_REQUEST['idElement'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['idElement']) : '';
						$part = (isset($_REQUEST['part']) && ($_REQUEST['part'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['part']) : '';
						$outputPart = digirisk_accident::get_accident_form_part($part, $tableElement, $idElement);
						echo $outputPart['part'] . '
						<script type="text/javascript" >
							evarisk(document).ready(function(){
								jQuery("#accident_form_error_nb").val(parseInt(jQuery("#accident_form_error_nb").val()) + parseInt(' . $outputPart['error'] . '));
								jQuery(".edit_missing_information").click(function(){
									jQuery("#accident_form_error_nb").val(0);
									jQuery("#accident_element_updater").dialog("open");
									jQuery("#accident_element_updater").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",{
										"post": "true", 
										"table": "' . TABLE_GROUPEMENT . '",
										"act": "load_groupement_form",
										"tableElement": "' . TABLE_GROUPEMENT . '",
										"idElement": jQuery(this).attr("id").replace("element_", "")
									});
								});
								if(jQuery("#accident_form_error_nb").val() > 0){
									jQuery("#save_accident").attr("disabled", "disabled");
								}
								else{
									jQuery("#save_accident").attr("disabled", false);
								}
							});
						</script>';
					}
					break;

					case 'previous_step':
					{
						$tableElement = (isset($_REQUEST['tableElement']) && ($_REQUEST['tableElement'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['tableElement']) : '';
						$idElement = (isset($_REQUEST['idElement']) && ($_REQUEST['idElement'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['idElement']) : '';
						$accident_id = (isset($_REQUEST['accident_id']) && ($_REQUEST['accident_id'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['accident_id']) : '';
						$step_to_load = (isset($_REQUEST['step_to_load']) && ($_REQUEST['step_to_load'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['step_to_load']) : '';
						$accident_main_informations['last_update_date'] = date('Y-m-d H:i:s');
						$accident_main_informations['declaration_step'] = $step_to_load;
						$accident_main_informations['declaration_state'] = ($step_to_load < 5) ? 'in_progress' : 'done';
						$save_result = eva_database::update($accident_main_informations, $accident_id, DIGI_DBT_ACCIDENT);
						echo '
<script type="text/javascript" >
	evarisk(document).ready(function(){
		jQuery("#divAccidenContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",{
			"post":"true",
			"table":"' . DIGI_DBT_ACCIDENT . '",
			"act":"load",
			"accident_id": "' . $accident_id . '",
			"idElement":"' . $idElement . '",
			"tableElement":"' . $tableElement . '"
		});
	});
</script>';
					}
					break;
				}
				break;
		}
	}

	if(isset($_REQUEST['nom']))
	{
		switch($_REQUEST['nom'])
		{
			case "installerEvarisk":
			{
					$insertions = $_REQUEST;
					unset($insertions['EPI']);
					if(isset($_REQUEST["EPI"]) && is_array($_REQUEST["EPI"]))
					{
						foreach($_REQUEST["EPI"] as $epi)
						{
							$insertions['EPI'][$epi[1]] = $epi[0];
						}
					}
					foreach($_REQUEST["methodes"] as $methode)
					{
						$insertions['methodes'][$methode[1]] = $methode[0];
					}
					require_once(EVA_MODULES_PLUGIN_DIR . 'installation/creationTables.php');
					require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
					evarisk_creationTables();
					evarisk_insertions($insertions);
					digirisk_permission::digirisk_init_permission();
					echo '<script type="text/javascript">window.top.location.href = "' . admin_url("options-general.php?page=digirisk_options") . '"</script>';
				}
			break;
			case "loadFieldsNewVariable":
			{
					for($i=$_REQUEST['min']; $i<=$_REQUEST['max']; $i++)
					{
						$idInput = 'newVariableAlterValueFor' . $i;
						$nomChamps = 'newVariableAlterValue[' . $i . ']';
						$labelInput = 'Valeur r&eacute;el de la variable quand elle vaut ' . $i . ' : ';
						echo EvaDisplayInput::afficherInput('text', $idInput, '', '', $labelInput, $nomChamps, true, false, 100, '', 'Float');
					}
				}
			break;
			case "veilleSummary":
			{
					$tableElement = eva_tools::IsValid_Variable($_REQUEST['tableElement']);
					$idElement = eva_tools::IsValid_Variable($_REQUEST['idElement']);
					$veilleResult = evaAnswerToQuestion::getAnswersForStats(date('Y-m-d'), $tableElement, $idElement, 2); 
					$myCharts = ' ';
					if( count($veilleResult) > 0)
					{
					foreach($veilleResult as $responseName => $listResponse)
					{
						$myCharts .= '[convertAccentToJS(\''.$responseName.' ('.count($listResponse).')\'),'.count($listResponse).'],';
					}
					$chartContent = trim(substr($myCharts,0,-1));
					
					$messageInfo .= '<script type="text/javascript" language="javascript"> 
						evarisk(document).ready(function(){
							line1 = [' . $chartContent . '];
							plot2 = $.jqplot("resultChart", [line1], {
								seriesDefaults:{renderer:$.jqplot.PieRenderer}, 
								legend:{show:true, escapeHtml:true}
							});
						});
					</script> 
					<div id="resultChart" style="margin-top:20px; margin-left:20px; width:400px; height:300px;">
					</div>';
					};
					echo $messageInfo;
				}
			break;
			case "veilleClicPagination":
			{
				require_once(EVA_METABOXES_PLUGIN_DIR . 'veilleReglementaire/formulaireReponse.php');
				$summary = ($_REQUEST['act'] == 'summary') ? true : false ;
				echo '<div id="plotLocation"></div><div id="interractionVeille">' . getFormulaireReponse($_REQUEST['idElement'], $_REQUEST['tableElement'], $summary) . '</div>';
			}
			break;
			case "veilleClicValidation":
			{
					$questionID = eva_tools::IsValid_Variable($_REQUEST['idQuestion']);
					$tableElement = eva_tools::IsValid_Variable($_REQUEST['tableElement']);
					$idElement = eva_tools::IsValid_Variable($_REQUEST['idElement']);
					$reponse = eva_tools::IsValid_Variable($_REQUEST['reponse']);
					$valeurReponse = eva_tools::IsValid_Variable($_REQUEST['valeur']);
					$observationReponse = eva_tools::IsValid_Variable($_REQUEST['observation']);
					$soumission = eva_tools::IsValid_Variable($_REQUEST['soumission']);
					$limiteValidite = eva_tools::IsValid_Variable($_REQUEST['limiteValidite']);
					$save = EvaAnswerToQuestion::saveNewAnswerToQuestion($questionID, $tableElement, $idElement, date('Y-m-d'), $reponse, $valeurReponse, $observationReponse, $limiteValidite);
					$messageInfo = '';
					// if($soumission != 'totale')
					// {
						if($save == 'ok')
						{
							$messageInfo = 
								'<span id="message" class="updated fade below-h2" style="cursor:pointer;" >
									<strong><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;La r&eacute;ponse a bien &eacute;t&eacute; enregistr&eacute;e.</strong>
								</span>
								<script type="text/javascript" >
									setTimeout(function(){evarisk(\'#observationTropLongue' . $questionID . '\').html("")},5000);
								</script>';
						}
						else
						{
							$messageInfo = 
								'<span id="message" class="updated fade below-h2">
									<p><strong><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="noresponse" style="vertical-align:middle;" />&nbsp;La r&eacute;ponse n\'a pas pu &ecirc;tre enregistr&eacute;e</strong></p>
								</span>
								<script type="text/javascript" >
									setTimeout(function(){evarisk(\'#observationTropLongue' . $questionID . '\').html("")},5000);
								</script>';
						}
					// }
					// else
					// {
						// if($save == 'ok')
						// {
							// $_REQUEST['statusVeille'] = $_REQUEST['statusVeille'] . true;
						// }
						// else
						// {
							// $_REQUEST['statusVeille'] = false;
						// }
					// }
					echo $messageInfo;
				}
			break;
			case "generationPDF" :
			{
					require_once(EVA_METABOXES_PLUGIN_DIR . 'veilleReglementaire/creationPDF.php');
				}
			break;
			case "chargerInfosGeneralesVeille" :
			{
					require_once(EVA_METABOXES_PLUGIN_DIR . 'veilleReglementaire/infoGeneraleVeille.php');
				}
			break;

			case 'updateTrash':
			{
				$tableProvenance = eva_tools::IsValid_Variable($_REQUEST['tableProvenance']);
				$elementToRestore = eva_tools::IsValid_Variable($_REQUEST['elementToRestore']);
				$elementToRestore = explode(',', $elementToRestore);
				$queryResult = $i = 0;
				if(is_array($elementToRestore) && (count($elementToRestore) > 0))
				{
					foreach($elementToRestore as $elements){
						if($elements != '')
						{
							$element = explode('_element_to_restore_', $elements);
							$queryResult += $wpdb->update($element[0], array('Status' => 'Valid'), array('id' => $element[1]));
							$i++;
						}
					}
				}
				if($queryResult == $i){
					$message = __('La restauration des &eacute;l&eacute;ments s&eacute;lectionn&eacute;s a &eacute;t&eacute; effectu&eacute;e avec succ&egrave;s', 'evarisk');
				}
				else{
					$message = __('Des erreurs sont survenues lors de la restauration des &eacute;l&eacute;ments s&eacute;lectionn&eacute;s', 'evarisk');
				}

switch($tableProvenance)
{
	case TABLE_CATEGORIE_PRECONISATION:
	case TABLE_PRECONISATION:
	{
		$actionAfterUpdate = '
		evarisk("#recommandationTable").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
		{
			"post":"true",
			"table":"' . TABLE_PRECONISATION . '",
			"act":"reloadRecommandationList"
		});';
	}
	break;
	case TABLE_METHODE:
	{
		$actionAfterUpdate = '
		evarisk("#methode-filter").submit();';
	}
	break;
	default:
	{
		$actionAfterUpdate = '
		changementPage("left", "' . $tableProvenance . '", 1, 1, "affichageListe", "main");';
	}
	break;
}

				echo '
<script type="text/javascript" >
	evarisk(document).ready(function(){
		evarisk("#trashContainer").dialog("close");

		actionMessageShow("#message", "' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . $message . '</strong></p>') . '");
		setTimeout(\'actionMessageHide("#message")\',7500);
' . $actionAfterUpdate . '
	});
</script>';
			}
			break;
			case 'loadTrash':
			{
				$output = '';

				$main_option = get_option('digirisk_options');
				$tableProvenance = eva_tools::IsValid_Variable($_REQUEST['tableProvenance']);
				$trash_elements = array();
				$i =0;
				$statusFieldValue = 'Deleted';
				switch($tableProvenance)
				{
					case TABLE_GROUPEMENT:
						if(($main_option['digi_activ_trash'] == 'oui') && current_user_can('digi_view_groupement_trash'))
						{
								$trash_elements[$i]['element'] = $tableProvenance;
								$trash_elements[$i]['name'] = __('Groupements', 'evarisk');
								$trash_elements[$i]['prefix_identifier'] = ELEMENT_IDENTIFIER_GP;
								$i++;
						}
						if(($main_option['digi_activ_trash'] == 'oui') && current_user_can('digi_view_unite_trash'))
						{
							$trash_elements[$i]['element'] = TABLE_UNITE_TRAVAIL;
							$trash_elements[$i]['name'] = __('Unit&eacute;s de travail', 'evarisk');
							$trash_elements[$i]['prefix_identifier'] = ELEMENT_IDENTIFIER_UT;
							$i++;
						}
					break;
					case TABLE_TACHE:
						if(($main_option['digi_activ_trash'] == 'oui') && current_user_can('digi_view_task_trash'))
						{
								$trash_elements[$i]['element'] = $tableProvenance;
								$trash_elements[$i]['name'] = __('T&acirc;ches', 'evarisk');
								$trash_elements[$i]['prefix_identifier'] = ELEMENT_IDENTIFIER_T;
								$i++;
						}
						if(($main_option['digi_activ_trash'] == 'oui') && current_user_can('digi_view_action_trash'))
						{
							$trash_elements[$i]['element'] = TABLE_ACTIVITE;
							$trash_elements[$i]['name'] = __('Sous-t&acirc;ches', 'evarisk');
							$trash_elements[$i]['prefix_identifier'] = ELEMENT_IDENTIFIER_ST;
							$i++;
						}
					break;
					case TABLE_CATEGORIE_DANGER:
						if(($main_option['digi_activ_trash'] == 'oui') && current_user_can('digi_view_danger_category_trash'))
						{
								$trash_elements[$i]['element'] = $tableProvenance;
								$trash_elements[$i]['name'] = __('Cat&eacute;gories de danger', 'evarisk');
								$trash_elements[$i]['prefix_identifier'] = ELEMENT_IDENTIFIER_CD;
								$i++;
						}
						if(($main_option['digi_activ_trash'] == 'oui') && current_user_can('digi_view_danger_trash'))
						{
							$trash_elements[$i]['element'] = TABLE_DANGER;
							$trash_elements[$i]['name'] = __('Dangers', 'evarisk');
							$trash_elements[$i]['prefix_identifier'] = ELEMENT_IDENTIFIER_D;
							$i++;
						}
					break;
					case TABLE_METHODE:
						if(($main_option['digi_activ_trash'] == 'oui') && current_user_can('digi_view_method_trash'))
						{
							$trash_elements[$i]['element'] = $tableProvenance;
							$trash_elements[$i]['name'] = __('M&eacute;thodes d\'&eacute;valuation', 'evarisk');
							$trash_elements[$i]['prefix_identifier'] = ELEMENT_IDENTIFIER_ME;
							$i++;
						}
					break;
					case DIGI_DBT_PERMISSION_ROLE:
						if(($main_option['digi_activ_trash'] == 'oui') && current_user_can('digi_view_user_role_trash'))
						{
							$trash_elements[$i]['element'] = $tableProvenance;
							$trash_elements[$i]['name'] = __('R&ocirc;le pour les utilisateurs', 'evarisk');
							$trash_elements[$i]['prefix_identifier'] = ELEMENT_IDENTIFIER_UR;
							$i++;
						}
					break;
					case TABLE_CATEGORIE_PRECONISATION:
						$statusFieldValue = 'deleted';
						if(($main_option['digi_activ_trash'] == 'oui') && current_user_can('digi_view_recommandation_category_trash'))
						{
								$trash_elements[$i]['element'] = $tableProvenance;
								$trash_elements[$i]['name'] = __('Cat&eacute;gories de pr&eacute;conisations', 'evarisk');
								$trash_elements[$i]['prefix_identifier'] = ELEMENT_IDENTIFIER_CP;
								$i++;
						}
						if(($main_option['digi_activ_trash'] == 'oui') && current_user_can('digi_view_recommandation_trash'))
						{
								$trash_elements[$i]['element'] = TABLE_PRECONISATION;
								$trash_elements[$i]['name'] = __('Pr&eacute;conisations', 'evarisk');
								$trash_elements[$i]['prefix_identifier'] = ELEMENT_IDENTIFIER_P;
								$i++;
						}
					break;
				}

				if(is_array($trash_elements) && (count($trash_elements) > 0))
				{
					foreach($trash_elements as $element => $element_definition)
					{
						$subTrashOutput = '';
						/*	Check if there are something to display in trash for the current element	*/
						$query = $wpdb->prepare("SELECT * FROM " . $element_definition['element'] . " WHERE status = '" . $statusFieldValue . "';");
						$trashedElement = $wpdb->get_results($query);
						if(count($trashedElement) > 0)
						{
							$userIsAllowedToUpdateTrash = false;
							unset($lignesDeValeurs);
							$idTable = 'trashedElement' . $element_definition['element'];
							$titres = array(__('Photo', 'evarisk'), __('Nom', 'evarisk'), __('Description', 'evarisk'));
							$classes = array('trashPicColumn', 'trashNameColumn', 'trashDescriptionColumn');
							foreach($trashedElement as $element)
							{
								$columnAdded = false;
								$nameField = 'nom';

								unset($ligne);
								$elementMainPicture = evaPhoto::getMainPhoto($element_definition['element'], $element->id);
								$elementMainPicture = evaPhoto::checkIfPictureIsFile($elementMainPicture, $element_definition['element']);
								$elementPicture = ( $elementMainPicture != '' ) ? '<img class="elementPicture" style="width:' . TAILLE_PICTOS . ';" src="' . $elementMainPicture . '" alt="element picture" />' : __('Pas de photo', 'evarisk');
								$ligne[] = array('value' => $elementPicture, 'class' => '');
								$ligne[] = array('value' => $element_definition['prefix_identifier'] . $element->id . '&nbsp;-&nbsp;' . $element->$nameField, 'class' => '');
								$ligne[] = array('value' => $element->description, 'class' => '');

								/*	Add the different column for each element type	*/
								switch($element_definition['element'])
								{
									case TABLE_GROUPEMENT:
									{
										if(current_user_can('digi_edit_groupement_trash')){
											$userIsAllowedToUpdateTrash = true;
										}
										if(!$columnAdded){
											$titres[] = __('Hi&eacute;rarchie', 'evarisk');
											$classes[] = 'trashParentColumn';
											$titres[] = __('Descendants', 'evarisk');
											$classes[] = 'trashChildrenColumn';
											$columnAdded = true;
										}
										$ancetres = Arborescence::getAncetre($element_definition['element'], $element);
										$miniFilAriane = '         ';
										foreach($ancetres as $ancetre){
											if($ancetre->nom != "Groupement Racine"){
												$miniFilAriane .= $element_definition['prefix_identifier'] . $ancetre->id . '&nbsp;-&nbsp;' . $ancetre->nom . ' &raquo; ';
											}
										}
										$ligne[] = array('value' => substr($miniFilAriane, 0, -9), 'class' => '');
										$miniFilAriane = '         ';
										$descendants = Arborescence::getDescendants($element_definition['element'], $element);
										foreach($descendants as $descendant){
											$miniFilAriane .= ELEMENT_IDENTIFIER_GP . $descendant->id . '&nbsp;-&nbsp;' . $descendant->nom . ' &raquo; ';
										}
										$descendants = EvaGroupement::getUnitesDescendantesDuGroupement($element->id);
										foreach($descendants as $descendant){
											$miniFilAriane .= ELEMENT_IDENTIFIER_UT . $descendant->id . '&nbsp;-&nbsp;' . $descendant->nom . ' &raquo; ';
										}
										$ligne[] = array('value' => substr($miniFilAriane, 0, -9), 'class' => '');
									}
									break;
									case TABLE_UNITE_TRAVAIL:
									{
										if(current_user_can('digi_edit_groupement_trash')){
											$userIsAllowedToUpdateTrash = true;
										}
										if(!$columnAdded){
											$titres[] = __('Hi&eacute;rarchie', 'evarisk');
											$classes[] = 'trashParentColumn';
											$columnAdded = true;
										}
										$directParent = EvaGroupement::getGroupement($element->id_groupement);
										$ancetres = Arborescence::getAncetre(TABLE_GROUPEMENT, $directParent);
										$miniFilAriane = '         ';
										foreach($ancetres as $ancetre){
											if($ancetre->nom != "Groupement Racine"){
												$miniFilAriane .= ELEMENT_IDENTIFIER_GP . $ancetre->id . '&nbsp;-&nbsp;' . $ancetre->nom . ' &raquo; ';
											}
										}
										if($directParent->nom != "Groupement Racine"){
											$miniFilAriane .= ELEMENT_IDENTIFIER_GP . $directParent->id . '&nbsp;-&nbsp;' . $directParent->nom . ' &raquo; ';
										}
										$ligne[] = array('value' => substr($miniFilAriane, 0, -9), 'class' => '');
									}
									break;
									case TABLE_TACHE:
									{
										if(current_user_can('digi_edit_task_trash')){
											$userIsAllowedToUpdateTrash = true;
										}
										if(!$columnAdded){
											$titres[] = __('Hi&eacute;rarchie', 'evarisk');
											$classes[] = 'trashParentColumn';
											$titres[] = __('Descendants', 'evarisk');
											$classes[] = 'trashChildrenColumn';
											$columnAdded = true;
										}
										$ancetres = Arborescence::getAncetre(TABLE_TACHE, $element);
										$miniFilAriane = '         ';
										foreach($ancetres as $ancetre){
											if($ancetre->nom != "Tache Racine"){
												$miniFilAriane .= ELEMENT_IDENTIFIER_T . $ancetre->id . '&nbsp;-&nbsp;' . $ancetre->nom . ' &raquo; ';
											}
										}
										$ligne[] = array('value' => substr($miniFilAriane, 0, -9), 'class' => '');
										$miniFilAriane = '         ';
										$descendants = Arborescence::getDescendants($element_definition['element'], $element);
										foreach($descendants as $descendant){
											$miniFilAriane .= ELEMENT_IDENTIFIER_T . $descendant->id . '&nbsp;-&nbsp;' . $descendant->nom . ' &raquo; ';
										}
										$descendants = EvaTask::getChildren($element->id);
										foreach($descendants as $descendant){
											$miniFilAriane .= ELEMENT_IDENTIFIER_ST . $descendant->id . '&nbsp;-&nbsp;' . $descendant->nom . ' &raquo; ';
										}
										$ligne[] = array('value' => substr($miniFilAriane, 0, -9), 'class' => '');
									}
									break;
									case TABLE_ACTIVITE:
									{
										if(current_user_can('digi_edit_action_trash')){
											$userIsAllowedToUpdateTrash = true;
										}
										if(!$columnAdded){
											$titres[] = __('Hi&eacute;rarchie', 'evarisk');
											$classes[] = 'trashParentColumn';
											$columnAdded = true;
										}
										$directParent = new EvaTask();
										$directParent->setId($element->id_tache);
										$directParent->load();
										$directParent->limiteGauche = $directParent->leftLimit;
										$directParent->limiteDroite = $directParent->rightLimit;
										$ancetres = Arborescence::getAncetre(TABLE_TACHE, $directParent);
										$miniFilAriane = '         ';
										foreach($ancetres as $ancetre){
											if($ancetre->nom != "Tache Racine"){
												$miniFilAriane .= ELEMENT_IDENTIFIER_T . $ancetre->id . '&nbsp;-&nbsp;' . $ancetre->nom . ' &raquo; ';
											}
										}
										if($directParent->nom != "Tache Racine"){
											$miniFilAriane .= ELEMENT_IDENTIFIER_T . $directParent->id . '&nbsp;-&nbsp;' . $directParent->name . ' &raquo; ';
										}
										$ligne[] = array('value' => substr($miniFilAriane, 0, -9), 'class' => '');
									}
									break;
									case TABLE_CATEGORIE_DANGER:
									{
										if(current_user_can('digi_edit_danger_category_trash')){
											$userIsAllowedToUpdateTrash = true;
										}
										if(!$columnAdded){
											$titres[] = __('Hi&eacute;rarchie', 'evarisk');
											$classes[] = 'trashParentColumn';
											$titres[] = __('Descendants', 'evarisk');
											$classes[] = 'trashChildrenColumn';
											$columnAdded = true;
										}
										$ancetres = Arborescence::getAncetre($element_definition['element'], $element);
										$miniFilAriane = '         ';
										foreach($ancetres as $ancetre){
											if($ancetre->nom != "Categorie Racine"){
												$miniFilAriane .= $element_definition['prefix_identifier'] . $ancetre->id . '&nbsp;-&nbsp;' . $ancetre->nom . ' &raquo; ';
											}
										}
										$ligne[] = array('value' => substr($miniFilAriane, 0, -9), 'class' => '');
										$miniFilAriane = '         ';
										$descendants = Arborescence::getDescendants($element_definition['element'], $element);
										foreach($descendants as $descendant){
											$miniFilAriane .= ELEMENT_IDENTIFIER_CD . $descendant->id . '&nbsp;-&nbsp;' . $descendant->nom . ' &raquo; ';
										}
										$descendants = categorieDangers::getChildren($element->id);
										foreach($descendants as $descendant){
											$miniFilAriane .= ELEMENT_IDENTIFIER_D . $descendant->id . '&nbsp;-&nbsp;' . $descendant->nom . ' &raquo; ';
										}
										$ligne[] = array('value' => substr($miniFilAriane, 0, -9), 'class' => '');
									}
									break;
									case TABLE_DANGER:
									{
										if(current_user_can('digi_edit_danger_trash')){
											$userIsAllowedToUpdateTrash = true;
										}
										if(!$columnAdded){
											$titres[] = __('Hi&eacute;rarchie', 'evarisk');
											$classes[] = 'trashParentColumn';
											$columnAdded = true;
										}
										$directParent = categorieDangers::getCategorieDanger($element->id_categorie);
										$ancetres = Arborescence::getAncetre(TABLE_CATEGORIE_DANGER, $directParent);
										$miniFilAriane = '         ';
										foreach($ancetres as $ancetre){
											if($ancetre->nom != "Categorie Racine"){
												$miniFilAriane .= $element_definition['prefix_identifier'] . $ancetre->id . '&nbsp;-&nbsp;' . $ancetre->nom . ' &raquo; ';
											}
										}
										if($directParent->nom != "Categorie Racine"){
											$miniFilAriane .= ELEMENT_IDENTIFIER_CD . $directParent->id . '&nbsp;-&nbsp;' . $directParent->nom . ' &raquo; ';
										}
										$ligne[] = array('value' => substr($miniFilAriane, 0, -9), 'class' => '');
									}
									break;
									case TABLE_METHODE:
									{
										if(current_user_can('digi_edit_method_trash')){
											$userIsAllowedToUpdateTrash = true;
										}
									}
									break;
									case DIGI_DBT_PERMISSION_ROLE:
									{
										if(current_user_can('digi_edit_user_role_trash')){
											$userIsAllowedToUpdateTrash = true;
										}
										$nameField = 'role_name';
									}
									break;
									case TABLE_CATEGORIE_PRECONISATION:
									{
										if(current_user_can('digi_edit_recommandation_category_trash')){
											$userIsAllowedToUpdateTrash = true;
										}
									}
									break;
									case TABLE_PRECONISATION:
									{
										if(current_user_can('digi_edit_recommandation_trash')){
											$userIsAllowedToUpdateTrash = true;
										}
										if(!$columnAdded){
											$titres[] = __('Hi&eacute;rarchie', 'evarisk');
											$classes[] = 'trashParentColumn';
											$columnAdded = true;
										}
										$directParent = evaRecommandationCategory::getCategoryRecommandation($element->id_categorie_preconisation);
										$ligne[] = array('value' => ELEMENT_IDENTIFIER_CP . $directParent->id . '&nbsp;-&nbsp;' . $directParent->nom, 'class' => '');
									}
									break;
								}

								if($userIsAllowedToUpdateTrash){
									$ligne[] = array('value' => '<input type="checkbox" class="alignright elementToRestore" value="' . $element_definition['element'] . '_element_to_restore_' . $element->id . '" />', 'class' => '');
								}

								$lignesDeValeurs[] = $ligne;
								$idLignes[] = 't' . $element_definition['prefix_identifier'] . $element->id;
							}
							if($userIsAllowedToUpdateTrash){
								$titres[] = '';
								$class[] = 'trashActionColumn';
							}
							$script = '
<script type="text/javascript" >
	evarisk(document).ready(function(){
		evarisk("#' . $idTable . ' tfoot").remove();
		evarisk("#' . $idTable . '").dataTable({
			"bPaginate": false,
			"bInfo": false,
			"bLengthChange": false,
			"oLanguage":{
				"sUrl": "' . EVA_INC_PLUGIN_URL . 'js/dataTable/jquery.dataTables.common_translation.txt"
			}
		});
	});
</script>';
							$subTrashOutput .= EvaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script);
						}
						else
						{
							$subTrashOutput .= '<div class="trashContentCenter" >' . __('La corbeille de cet &eacute;l&eacute;ment est vide', 'evarisk') . '</div>';
						}

						/*	Add output for each element	*/
						$output .= '
<fieldset class="elementTrashContainer" >
	<legend>' . sprintf(__('Contenu de la corbeille pour les %s', 'evarisk'), $element_definition['name']) . '</legend>
	' . $subTrashOutput . '
</fieldset>';
					}
				}
				else
				{
					$output ='<div class="trashContentCenter" >' .  __('Aucun &eacute;l&eacute;ment n\'a &eacute;t&eacute; s&eacute;lectionn&eacute;', 'evarisk') . '</div>';
				}

				if($userIsAllowedToUpdateTrash)
				{
					$output .= '
<input type="hidden" value="" name="elementToRestore" id="elementToRestore" />
<input type="button" class="button-secondary updateTrash alignright" id="updateTrash" disabled="disabled" value="' . __('Restaurer la s&eacute;lection', 'evarisk') . '" />
<script type="text/javascript" >
	evarisk(document).ready(function(){
		evarisk(".elementToRestore").click(function(){
			var currentElementToRestore = evarisk("#elementToRestore").val();
			var elementToAdd = evarisk(this).val() + ", ";
			currentElementToRestore = currentElementToRestore.replace(elementToAdd, "");
			if(evarisk(this).is(":checked")){
				currentElementToRestore = currentElementToRestore.replace(elementToAdd, "") + elementToAdd;
			}
			evarisk("#elementToRestore").val(currentElementToRestore);
			if(evarisk("#elementToRestore").val() != ""){
				evarisk("#updateTrash").prop("disabled", "");
				evarisk("#updateTrash").removeClass("button-secondary");
				evarisk("#updateTrash").addClass("button-primary");
			}
			else{
				evarisk("#updateTrash").prop("disabled", "disabled");
				evarisk("#updateTrash").removeClass("button-primary");
				evarisk("#updateTrash").addClass("button-secondary");
			}
		});

		evarisk("#updateTrash").click(function(){
			evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
			{
				"post": "true", 
				"tableProvenance": "' . $tableProvenance . '",
				"nom": "updateTrash",
				"elementToRestore" : evarisk("#elementToRestore").val()
			});
		});
	});
</script>';
				}

				echo $output;
			}
			break;

			case "demandeAction" :
			{
				echo Risque::getTableQuotationRisque($_REQUEST['tableProvenance'], $_REQUEST['idProvenance']) . '<br />';
				
				require_once(EVA_METABOXES_PLUGIN_DIR . 'actionsCorrectives/activite/activite-new.php');
				getActivityGeneralInformationPostBoxBody(array('idElement' => null, 'idPere' => 1, 'affichage' => null, 'idsFilAriane' => null));
				echo 
					'<script type="text/javascript">
						evarisk(document).ready(function(){
							evarisk("#idProvenance_activite").val("' . $_REQUEST['idProvenance'] . '");
							evarisk("#tableProvenance_activite").val("' . $_REQUEST['tableProvenance'] . '");
							evarisk("#save_activite").unbind("click");
							evarisk("#save_activite").click(function(){
								if(evarisk(\'#nom_activite\').is(".form-input-tip")){
									evarisk(\'#nom_activite\').val("");
									evarisk(\'#nom_activite\').removeClass(\'form-input-tip\');
								}
								valeurActuelle = evarisk("#nom_activite").val();
								if(valeurActuelle == ""){
									alert(convertAccentToJS("' . __("Vous n\'avez pas donne de nom a l'action", 'evarisk') . '"));
								}
								else{
									evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
										"nom": "addAction",
										"nom_activite": evarisk("#nom_activite").val(),
										"idPere": 1,
										"description": evarisk("#description_activite").val(),
										"date_debut": evarisk("#date_debut_activite").val(),
										"date_fin": evarisk("#date_fin_activite").val(),
										"cout": evarisk("#cout_activite").val(),
										"avancement": evarisk("#avancement_activite").val(),
										"responsable_activite": evarisk("#responsable_activite").val(),
										"idProvenance": evarisk("#idProvenance_activite").val(),
										"tableProvenance": evarisk("#tableProvenance_activite").val()
									});
								}
							});
						})
					</script>';
				}
			break;
			case "suiviAction" :
			{
				$risques = array();
				$riskList = Risque::getRisque($_REQUEST['idProvenance']);
				if($riskList != null)
				{
					foreach($riskList as $risque)
					{
						$risques[$risque->id][] = $risque; 
					}
				}
				echo actionsCorrectives::output_correctiv_action_by_risk($risques, '
	"bFilter": false,
	"bPaginate": false,
	"bLengthChange": false,') . '
	<script type="text/javascript">
		evarisk("#pic_line' . ELEMENT_IDENTIFIER_R . $_REQUEST['idProvenance'] . '").click();
	</script>';
			}
			break;
			case "addAction" :
			{
					$_POST['parentTaskId'] = evaTask::saveNewTask();
					$actionSave = evaActivity::saveNewActivity();

					$messageInfo = '<script type="text/javascript">
							evarisk(document).ready(function(){
								evarisk("#message' . $_REQUEST['tableProvenance'] . '").addClass("updated");';
					if(($actionSave['task_status'] != 'error') && ($actionSave['action_status'] != 'error'))
					{
						$messageInfo = $messageInfo . '
								evarisk("#message' . $_REQUEST['tableProvenance'] . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s a correctement &eacute;t&eacute; %s', 'evarisk') . '</strong></p>', __('de l\'action corrective', 'evarisk') . ' "' . stripslashes($_REQUEST['nom_activite']) . '"', __('sauvegard&eacute;e', 'evarisk'))) . '");';
					}
					else
					{
						$messageInfo = $messageInfo . '
								evarisk("#message' . $_REQUEST['tableProvenance'] . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s n\'a pas &eacute;t&eacute; %s.', 'evarisk') . '</strong></p>', __('de l\'action corrective', 'evarisk') . ' "' . stripslashes($_REQUEST['nom_activite']) . '"', __('sauvegard&eacute;e', 'evarisk'))) . '");';
					}
					$messageInfo = $messageInfo . '
								evarisk("#message' . $_REQUEST['tableProvenance'] . '").show();
								setTimeout(function(){
									evarisk("#message' . $_REQUEST['tableProvenance'] . '").removeClass("updated");
									evarisk("#message' . $_REQUEST['tableProvenance'] . '").hide();
								},7500);
								evarisk("#ongletVoirLesRisques").click();
							});
						</script>';
					echo $messageInfo;
				}
			break;
			case "loadPictureAC":
			{
					switch($_REQUEST['act'])
					{
						case 'before':
							$query = $wpdb->prepare(
								"SELECT photo
								FROM " . TABLE_PHOTO . " AS P
									INNER JOIN " . TABLE_ACTIVITE . " AS A ON (A.idPhotoAvant = P.id)
								WHERE A.id = '%s' ", 
								$_REQUEST['idProvenance']
							);
							$picture = $wpdb->get_row($query);
							echo '<img src="' . EVA_GENERATED_DOC_URL . $picture->photo . '" alt="picture before corrective action" style="width:40%;" />';
						break;
						case 'after':
							$query = $wpdb->prepare(
								"SELECT photo
								FROM " . TABLE_PHOTO . " AS P
									INNER JOIN " . TABLE_ACTIVITE . " AS A ON (A.idPhotoApres = P.id)
								WHERE A.id = '%s' ", 
								$_REQUEST['idProvenance']
							);
							$picture = $wpdb->get_row($query);
							echo '<img src="' . EVA_GENERATED_DOC_URL . $picture->photo . '" alt="picture after corrective action" style="width:40%;" />';
						break;
					}
				}
			break;
			case "ficheAction" :
			{
				echo Risque::getTableQuotationRisque($_REQUEST['tableProvenance'], $_REQUEST['idProvenance']);

				require_once(EVA_METABOXES_PLUGIN_DIR . 'actionsCorrectives/activite/simple-activite-new.php');
				getSimpleActivityGeneralInformationPostBoxBody(array('idElement' => null, 'idPere' => 1, 'affichage' => null, 'idsFilAriane' => null));				
				echo 
					'<script type="text/javascript">
						evarisk(document).ready(function(){
							evarisk("#idProvenance_activite").val("' . $_REQUEST['idProvenance'] . '");
							evarisk("#tableProvenance_activite").val("' . $_REQUEST['tableProvenance'] . '");
						})
					</script>';
				}
			break;
			case "suiviFicheAction" :
			{
				$tableElement = $_REQUEST['tableElement'];
				$idElement = $_REQUEST['idElement'];
				$risques = array();
				$riskList = Risque::getRisques($tableElement, $idElement, "Valid");
				if($riskList != null)
				{
					foreach($riskList as $risque)
					{
						$risques[$risque->id][] = $risque; 
					}
				}
				echo actionsCorrectives::output_correctiv_action_by_risk($risques);
			}
			break;
			case "addActionPhoto" :
			{
					$_POST['parentTaskId'] = evaTask::saveNewTask();
					$actionSave = evaActivity::saveNewActivity();

					$idRisque = eva_tools::IsValid_Variable($_REQUEST['idProvenance']);
					$risque = Risque::getRisque($idRisque);
					$_REQUEST['idRisque'] = $idRisque;
					$_REQUEST['idDanger'] = $risque[0]->id_danger;
					$_REQUEST['idMethode'] = $risque[0]->id_methode;
					$_REQUEST['description'] = $risque[0]->commentaire;
					$_REQUEST['idElement'] = $risque[0]->id_element;
					$_REQUEST['tableElement'] = $risque[0]->nomTableElement;
					$_REQUEST['act'] = 'save';
					$_REQUEST['histo'] = 'true';
					require_once(EVA_METABOXES_PLUGIN_DIR . 'risque/risquePersistance.php');

					/*	Make the link between a corrective action and a risk evaluation	*/
					$query = 
						$wpdb->prepare(
							"SELECT id_evaluation 
							FROM " . TABLE_AVOIR_VALEUR . " 
							WHERE id_risque = '%d' 
								AND Status = 'Valid' 
							ORDER BY id DESC 
							LIMIT 1", 
							$_REQUEST['idProvenance']
						);
					$evaluation = $wpdb->get_row($query);
					evaTask::liaisonTacheElement(TABLE_AVOIR_VALEUR, $evaluation->id_evaluation, $actionSave['task_id'], 'after');

					$messageInfo = '<script type="text/javascript">
							evarisk(document).ready(function(){
								evarisk("#message' . $_REQUEST['tableProvenance'] . '").addClass("updated");';
					if(($actionSave['task_status'] != 'error') && ($actionSave['action_status'] != 'error'))
					{
						$messageInfo = $messageInfo . '
								evarisk("#message' . $_REQUEST['tableProvenance'] . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s a correctement &eacute;t&eacute; %s', 'evarisk') . '</strong></p>', __('de l\'action corrective', 'evarisk') . ' "' . stripslashes($_REQUEST['nom_activite']) . '"', __('sauvegard&eacute;e', 'evarisk'))) . '");';
					}
					else
					{
						$messageInfo = $messageInfo . '
								evarisk("#message' . $_REQUEST['tableProvenance'] . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s n\'a pas &eacute;t&eacute; %s.', 'evarisk') . '</strong></p>', __('de l\'action corrective', 'evarisk') . ' "' . stripslashes($_REQUEST['nom_activite']) . '"', __('sauvegard&eacute;e', 'evarisk'))) . '");';
					}
					$messageInfo .= '
								evarisk("#message' . $_REQUEST['tableProvenance'] . '").show();
								setTimeout(function(){
									evarisk("#message' . $_REQUEST['tableProvenance'] . '").removeClass("updated");
									evarisk("#message' . $_REQUEST['tableProvenance'] . '").hide();
								},7500);

								evarisk("#id_activite").val("' . $actionSave['action_id'] . '");
								evarisk("#idPere_activite").val("' . $actionSave['task_id'] . '");
								evarisk("#ActionSaveButton").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
									"post":"true",
									"nom":"addActionPhotoSaveButtonReload"
								});

								evarisk("#photosActionsCorrectives").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
									"post":"true",
									"table":"' . TABLE_ACTIVITE . '",
									"act":"pictureLoad",
									"tableElement":evarisk("#tableProvenance_activite").val(),
									"idElement":"' . $actionSave['action_id'] . '"
								});
							});
						</script>';
					echo $messageInfo;
				}
			break;
			case "addActionPhotoSaveButtonReload":
			{
					//Bouton Enregistrer
					$idBouttonEnregistrer = 'save_activite';
					$idTitre = "nom_activite";

					/*	Check if the user in charge of the action are mandatory */
					$idResponsableIsMandatory = digirisk_options::getOptionValue('responsable_Action_Obligatoire');

					$scriptEnregistrementSave = '<script type="text/javascript">
						evarisk(document).ready(function() {
							evarisk(\'#' . $idBouttonEnregistrer . '\').click(function() {
								var variables = new Array();';
					$allVariables = MethodeEvaluation::getAllVariables();
					foreach($allVariables as $variable)
					{
						$scriptEnregistrementSave .= '
								variables["' . $variable->id . '"] = evarisk("#var' . $variable->id . 'FormRisque-FAC").val();';
					}
					$scriptEnregistrementSave .= '
								if(evarisk(\'#' . $idTitre . '\').is(".form-input-tip"))
								{
									document.getElementById(\'' . $idTitre . '\').value=\'\';
									evarisk(\'#' . $idTitre . '\').removeClass(\'form-input-tip\');
								}

								idResponsable = evarisk("#responsable_activite").val();
								idResponsableIsMandatory = "false";
								idResponsableIsMandatory = "' . $idResponsableIsMandatory . '";

								valeurActuelle = evarisk("#' . $idTitre . '").val();
								if(valeurActuelle == "")
								{
									alert(convertAccentToJS("' . __("Vous n\'avez pas donne de nom a l'action", 'evarisk') . '"));
								}
								else if(((idResponsable <= "0") ||(idResponsable == "")) && (idResponsableIsMandatory == "oui"))
								{
									alert(convertAccentToJS("' . __("Vous devez choisir une personne en charge de l\'action", 'evarisk') . '"));
								}
								else
								{
									evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
										"table": "' . TABLE_ACTIVITE . '",
										"act": "update-FAC",
										"id": evarisk("#id_activite").val(),
										"nom_activite": evarisk("#nom_activite").val(),
										"date_debut": evarisk("#date_debut_activite").val(),
										"date_fin": evarisk("#date_fin_activite").val(),
										"idPere": evarisk("#idPere_activite").val(),
										"description": evarisk("#description_activite").val(),
										"affichage": evarisk("#affichage_activite").val(),
										"cout": evarisk("#cout_activite").val(),
										"avancement": evarisk("#avancement_activite").val(),
										"responsable_activite": evarisk("#responsable_activite").val(),
										"idsFilAriane": evarisk("#idsFilAriane_activite").val(),
										"idProvenance": evarisk("#idProvenance_activite").val(),
										"tableProvenance": evarisk("#tableProvenance_activite").val(),
										"variables":variables
									});
								}
							});
						});
						</script>';
					echo EvaDisplayInput::afficherInput('button', $idBouttonEnregistrer, __('Enregistrer', 'evarisk'), null, '', $idBouttonEnregistrer, false, true, '', 'button-primary', '', '', $scriptEnregistrementSave, 'left');
				}
			break;
			case "addAction-FAC" :
			{
					$_POST['parentTaskId'] = evaTask::saveNewTask();
					$actionSave = evaActivity::saveNewActivity();

					$idRisque = eva_tools::IsValid_Variable($_REQUEST['idProvenance']);
					$risque = Risque::getRisque($idRisque);
					$_REQUEST['idRisque'] = $idRisque;
					$_REQUEST['idDanger'] = $risque[0]->id_danger;
					$_REQUEST['idMethode'] = $risque[0]->id_methode;
					$_REQUEST['description'] = $risque[0]->commentaire;
					$_REQUEST['idElement'] = $risque[0]->id_element;
					$_REQUEST['tableElement'] = $risque[0]->nomTableElement;
					$_REQUEST['act'] = 'save';
					$_REQUEST['histo'] = 'true';
					require_once(EVA_METABOXES_PLUGIN_DIR . 'risque/risquePersistance.php');

					/*	Make the link between a corrective action and a risk evaluation	*/
					$query = 
						$wpdb->prepare(
							"SELECT id_evaluation 
							FROM " . TABLE_AVOIR_VALEUR . " 
							WHERE id_risque = '%d' 
								AND Status = 'Valid' 
							ORDER BY id DESC 
							LIMIT 1", 
							$_REQUEST['idProvenance']
						);
					$evaluation = $wpdb->get_row($query);
					evaTask::liaisonTacheElement(TABLE_AVOIR_VALEUR, $evaluation->id_evaluation, $actionSave['task_id'], 'after');

					$messageInfo = 
					'<script type="text/javascript">
						evarisk(document).ready(function(){
							evarisk("#message' . $_REQUEST['tableProvenance'] . '").addClass("updated");';
					if(($actionSave['task_status'] != 'error') && ($actionSave['action_status'] != 'error'))
					{
						$messageInfo .= '
							evarisk("#message' . $_REQUEST['tableProvenance'] . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s a correctement &eacute;t&eacute; %s', 'evarisk') . '</strong></p>', __('de l\'action corrective', 'evarisk') . ' "' . stripslashes($_REQUEST['nom_activite']) . '"', __('sauvegard&eacute;e', 'evarisk'))) . '");';
					}
					else
					{
						$messageInfo .= '
							evarisk("#message' . $_REQUEST['tableProvenance'] . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s n\'a pas &eacute;t&eacute; %s.', 'evarisk') . '</strong></p>', __('de l\'action corrective', 'evarisk') . ' "' . stripslashes($_REQUEST['nom_activite']) . '"', __('sauvegard&eacute;e', 'evarisk'))) . '");';
					}
					$messageInfo .= '
							evarisk("#message' . $_REQUEST['tableProvenance'] . '").show();
							setTimeout(function(){
								evarisk("#message' . $_REQUEST['tableProvenance'] . '").removeClass("updated");
								evarisk("#message' . $_REQUEST['tableProvenance'] . '").hide();
							},7500);
							evarisk("#ongletVoirLesRisques").click();
						});
					</script>';
					echo $messageInfo;
				}
			break;
			case "OLDsuiviAction" :
			{
				switch($_REQUEST['tableProvenance'])
				{
					case TABLE_RISQUE :
						$risque = Risque::getRisque($_REQUEST['idProvenance']);
						{//Cr�ation de la table
							unset($tableauVariables);
							foreach($risque as $ligneRisque)
							{
								$valeurVariables[$ligneRisque->id_variable] = $ligneRisque->valeur;
							}
							$methode = MethodeEvaluation::getMethod($risque[0]->id_methode);
							$listeVariables = MethodeEvaluation::getVariablesMethode($methode->id, $risque[0]->date);
							foreach($listeVariables as $ordre => $variable)
							{
								$tableauVariables[] = array('nom' => $variable->nom, 'valeur' => $valeurVariables[$variable->id]);
							}

							unset($titres,$classes, $idLignes, $lignesDeValeurs);
							$idLignes = null;
							$idTable = 'tableDemandeAction' . $_REQUEST['tableProvenance'] . $_REQUEST['idProvenance'];
							$titres[] = __("Quotation", 'evarisk');
							$titres[] = ucfirst(strtolower(sprintf(__("nom %s", 'evarisk'), __("du danger", 'evarisk'))));
							$titres[] = ucfirst(strtolower(sprintf(__("commentaire %s", 'evarisk'), __("sur le risque", 'evarisk'))));
							$classes[] = 'columnQuotation';
							$classes[] = 'columnNomDanger';
							$classes[] = 'columnCommentaireRisque';

							$idligne = 'risque-' . $risque[0]->id;
							$idLignes[] = $idligne;

							$idMethode = $risque[0]->id_methode;
							$score = Risque::getScoreRisque($risque);
							$quotation = Risque::getEquivalenceEtalon($idMethode, $score, $risque[0]->date);
							$niveauSeuil = Risque::getSeuil($quotation);

							unset($ligneDeValeurs);
							$ligneDeValeurs[] = array('value' => $quotation, 'class' => 'risque' . $niveauSeuil . 'Text');
							$ligneDeValeurs[] = array('value' => $risque[0]->nomDanger, 'class' => '');
							$ligneDeValeurs[] = array('value' => nl2br($risque[0]->commentaire), 'class' => '');
							foreach($tableauVariables as $variable)
							{
								$titres[] = substr($variable['nom'], 0, 3) . '.';
								$classes[] = 'columnVariableRisque';
								$ligneDeValeurs[] = array('value' => $variable['valeur'], 'class' => '');
							}
							$lignesDeValeurs[] = $ligneDeValeurs;

							$lignesDeValeurs = (isset($lignesDeValeurs))?$lignesDeValeurs:null;
							$script = '<script type="text/javascript">
								evarisk(document).ready(function(){
									evarisk("#' . $idTable . ' tfoot").remove();
								});
							</script>';

							echo EvaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script);
						}
						break;
					default :
						echo 'Pensez &agrave; <b>ajouter</b> le <b>cas ' . $_REQUEST['tableProvenance'] . '</b> dans le <b>switch</b> ligne <b>' . __LINE__ . '</b> du fichier "' . dirname(__FILE__) . '\<b>' . basename(__FILE__) . '</b>"<br />';
						break;
				}
				echo '<br />';
				//On r�cup�re les actions relatives � l'�l�ment de provenance.
				$taches = new EvaTaskTable();
				$tacheLike = new EvaTask();
				$tacheLike->setIdFrom($_REQUEST['idProvenance']);
				$tacheLike->setTableFrom($_REQUEST['tableProvenance']);
				$taches->getTasksLike($tacheLike);
				//On demande � l'utilisateur de choisir l'action qui l'int�resse.
				$actionsCorrectives = $taches->getTasks();
				//on construit les Gantts des diff�rentes actions
				if($actionsCorrectives != null && count($actionsCorrectives) > 0)
				{
					foreach($actionsCorrectives as $actionCorrective)
					{
						$tasksGantt = '';
						$idDiv = $actionCorrective->getTableFrom() . $actionCorrective->getIdFrom() . '-' . TABLE_TACHE . $actionCorrective->getId();
						echo 
							'<div id="' . $idDiv . '-choix" class="nomAction" style="cursor:pointer;" ><span >+</span> T' . $actionCorrective->getId() . '&nbsp;-&nbsp;' . $actionCorrective->getName() . '</div>
							<div id="' . $idDiv . '-affichage" class="affichageAction" style="display:none;"></div>';
						$tachesDeLAction = $actionCorrective->getDescendants($actionCorrective);
						$tachesDeLAction = array_merge(array($actionCorrective->getId() => $actionCorrective), $tachesDeLAction->getTasks());
						unset($niveaux);
						$indiceTacheNiveaux = 0;
						
						if($tachesDeLAction != null && count($tachesDeLAction) > 0)
						{
							foreach($tachesDeLAction as $tacheDeLAction)
							{
								$niveaux[] = $tacheDeLAction->getLevel();
								$indiceTacheNiveaux = count($niveaux) - 1;
								$tacheDeLAction->getTimeWindow();
								$tacheDeLAction->computeProgression();
								$tacheDeLAction->save();
									/*	Updte the task ancestor	*/
									$wpdbTasks = Arborescence::getAncetre(TABLE_TACHE, $tacheDeLAction->convertToWpdb());
									foreach($wpdbTasks as $task)
									{
										unset($ancestorTask);
										$ancestorTask = new EvaTask($task->id);
										$ancestorTask->load();
										$ancestorTask->computeProgression();
										$ancestorTask->save();
										unset($ancestorTask);
									}
								$tasksGantt = $tasksGantt . '
									{"id": "T' . $tacheDeLAction->getId() . '", "task": "' . $tacheDeLAction->getName() . '", "progression": "' . $tacheDeLAction->getProgression() . '", "startDate": "' . $tacheDeLAction->getStartDate() . '", "finishDate": "' . $tacheDeLAction->getFinishDate() . '" },';
								$activitesDeLaTache = $tacheDeLAction->getActivitiesDependOn()->getActivities();
								if($activitesDeLaTache != null && count($activitesDeLaTache) > 0)
								{
									foreach($activitesDeLaTache as $activiteDeLaTache)
									{
										$niveaux[] = $niveaux[$indiceTacheNiveaux] + 1;
										$tasksGantt = $tasksGantt . '
											{"id": "A' . $activiteDeLaTache->getId() . '", "task": "' . $activiteDeLaTache->getName() . '", "progression": "' . $activiteDeLaTache->getProgression() . '", "startDate": "' . $activiteDeLaTache->getStartDate() . '", "finishDate": "' . $activiteDeLaTache->getFinishDate() . '" },';
									}
								}
							}
							
							$dateDebut = date('Y-m-d', strtotime('-' . DAY_BEFORE_TODAY_GANTT . ' day'));
							$dateFin = date('Y-m-d', strtotime('+' . DAY_AFTER_TODAY_GANTT . ' day'));
							
							echo '<script type="text/javascript">
								evarisk(document).ready(function(){										
									evarisk("#' . $idDiv . '-affichage").gantt({
										"tasks":[' . $tasksGantt . '],
										"titles":[
											"ID",
											"T&acirc;ches",
											"Avanc.(%)",
											"Date de d&eacute;but",
											"Date de fin"
										],
										"displayStartDate": "' . $dateDebut . '",
										"displayFinishDate": "' . $dateFin . '",
										"language": "fr"
									});
								});
							</script>';
						}
						else
						{
							echo '<script type="text/javascript">
								evarisk(document).ready(function(){										
									evarisk("#' . $idDiv . '-affichage").html("&nbsp;&nbsp;&nbsp;&nbsp;' . __('Action non d&eacute;coup&eacute;e', 'evarisk') . '");
								});
							</script>';
						}
						echo '<script type="text/javascript">
							evarisk(document).ready(function(){			
								evarisk("#' . $idDiv . '-choix").toggle(
									function()
									{
										evarisk("#' . $idDiv . '-affichage").show();
										evarisk(this).children("span:first").html("-");
									},
									function()
									{
										evarisk("#' . $idDiv . '-affichage").hide();
										evarisk(this).children("span:first").html("+");
									}
								);
							});
						</script>';
						{//Bouton enregistrer
							$idBoutonEnregistrer = $idDiv . '-enregistrer';
							$scriptEnregistrement = '<script type="text/javascript">
								evarisk(document).ready(function() {	
									var boutonEnregistrer = evarisk(\'#' . $idBoutonEnregistrer . '\').parent().html();
									evarisk(\'#' . $idBoutonEnregistrer . '\').parent().html("");
									evarisk(\'#' . $idDiv . '-affichage\').append(boutonEnregistrer);
									evarisk(\'#' . $idBoutonEnregistrer . '\').click(function() {
										var idDiv = "' . $idDiv . '";
										var activites = new Array();
										evarisk("#' . $idDiv . '-affichage .ui-gantt-table td:nth-child(3) input").each(function(){
											if(evarisk(this).attr("id") != "")
											{
												activites[evarisk(this).attr("id").substr(idDiv.length + 1)] = evarisk(this).val();
											}
										});
										evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
											"post":"true", 
											"table":"' . TABLE_ACTIVITE . '", 
											"act":"actualiserAvancement", 
											"activites":activites,
											"tableProvenance":"' . $_REQUEST['tableProvenance'] . '",
											"idProvenance": "' . $_REQUEST['idProvenance'] . '"
										});
										return false;
									});
								});
								</script>';
							echo EvaDisplayInput::afficherInput('button', $idBoutonEnregistrer, 'Enregistrer', null, '', $idDiv . 'save', false, false, '', 'button-primary alignright', '', '', $scriptEnregistrement);
						}
						echo '<script type="text/javascript">
								evarisk(document).ready(function(){		
									//Transformation du texte des cases avancement en input
									evarisk("#' . $idDiv . '-affichage .ui-gantt-table td:nth-child(3)").each(function(){
										evarisk(this).html("<input type=\"text\" value=\"" + evarisk(this).html() + "\" maxlength=3 style=\"width:3em;\"/>%");
										if(evarisk(this).parent("tr").children("td:first").html().match("^T")=="T")
										{
											evarisk(this).children("input").prop("disabled","disabled");
										}
										else
										{
											evarisk(this).children("input").attr("id","' . $idDiv . '-" + evarisk(this).parent("tr").children("td:first").html().substr(1, 1));
										}
									});
								});
							</script>';
						//ajout de l'indentation
						foreach($niveaux as $key => $niveau)
						{
							echo '<script type="text/javascript">
									evarisk(document).ready(function(){		
										evarisk("#' . $idDiv . '-affichage .ui-gantt-table tr:nth-child(' . ($key + 1) . ') td:nth-child(2)").css("padding-left", "' . ($niveau * LARGEUR_INDENTATION_GANTT_EN_EM) . 'em");
									});
								</script>';
						}
					}
				}
				else
				{
					switch($_REQUEST['tableProvenance'])
					{
						case TABLE_RISQUE :
							$complement	= __('ce risque', 'evarisk');
							break;
						default :
							$complement	= __('cet &eacute;l&eacute;ment', 'evarisk');
							break;
					}
					echo sprintf(__('Il n\'y a pas d\'action pour %s', 'evarisk'), $complement);
				}
				}
			break;

			case 'saveMarkerNewPosition':
			{
				$newPositions = (isset($_REQUEST['positions']) && (trim($_REQUEST['positions']) != '')) ? eva_tools::IsValid_Variable($_REQUEST['positions']) : '';
				if(trim($newPositions) != ''){
					$newPositionsList = explode('_pos_separator_', $newPositions);
					foreach($newPositionsList as $newPositionsElement){
						if($newPositionsElement != ''){
							/*	Make different operation on the posted datas	*/
							$positionDefinition = explode('-val-', $newPositionsElement);
							$adressIdentifier = str_replace("adressIdentifier", "", $positionDefinition[0]);
							$positionComponent = explode(", ", str_replace('(', '', str_replace(')', '', $positionDefinition[1])));
							
							/*	Load the current adress	*/
							$elementAdress = new EvaBaseAddress($adressIdentifier);
							$elementAdress->load();
							$elementAdress->setLatitude($positionComponent[0]);
							$elementAdress->setLongitude($positionComponent[1]);
							$elementAdress->save();
						}
					}
					echo '
<script type="text/javascript" >
	evarisk(document).ready(function(){
		actionMessageShow("#geoloc_message", "<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png\' alt=\'success\' />' . __('Donn&eacute;es enregistr&eacute;es avec succ&eacute;s', 'evarisk') . '");
		setTimeout(\'actionMessageHide("#geoloc_message")\',5000);
		evarisk("#saveNewPosition").hide();
	});
</script>';
				}
			}
			break;
		}
	}

	//Chargement des meta-boxes
	if(isset($_REQUEST['nomMetaBox']))
		switch($_REQUEST['nomMetaBox'])
		{
			case 'Geolocalisation':
				if($_REQUEST['markers'] != "")
				{
					foreach($_REQUEST['markers'] as $markerImplode)
					{
						$markerNArray = explode('"; "', stripcslashes($markerImplode));
						for($i=0; $i<count($_REQUEST["keys"]); $i++)
						{
							$markerAArray[$_REQUEST["keys"][$i]] = $markerNArray[$i];
						}
						$markers[] = $markerAArray;
					}
				}
				echo EvaGoogleMaps::getGoogleMap($_REQUEST['idGoogleMapsDiv'], $markers);
				break;
		}
}
/*
 * Param�tres pass�s en GET
 */
else
{
	switch($_REQUEST['nom'])
	{
		case TABLE_GROUPEMENT:
			switch($_REQUEST['act'])
			{
				case 'none':
					switch($_REQUEST['affichage'])
					{
						case "affichageTable":
						case "affichageListe":
							$_REQUEST['affichage'] = $_REQUEST['affichage'];
							require_once(EVA_MODULES_PLUGIN_DIR . 'evaluationDesRisques/partieGaucheEvaluationDesRisques.php');
							echo $script . $partieGauche;
							break;
					}
					break;
				case 'reloadScriptDD':
					echo EvaDisplayDesign::getScriptDragAndDrop($_REQUEST['idTable'], $_REQUEST['nom'], $_REQUEST['divDeChargement']);
					break;
			}
			break;
		case TABLE_TACHE:
			switch($_REQUEST['act'])
			{
				case 'none':
					switch($_REQUEST['affichage'])
					{
						case "affichageTable":
						case "affichageListe":
							$_REQUEST['affichage'] = $_REQUEST['affichage'];
							require_once(EVA_MODULES_PLUGIN_DIR . 'evaluationDesRisques/partieGaucheEvaluationDesRisques.php');
							echo $script . $partieGauche;
							break;
					}
					break;
				case 'reloadScriptDD':
					echo EvaDisplayDesign::getScriptDragAndDrop($_REQUEST['idTable'], $_REQUEST['nom'], $_REQUEST['divDeChargement']);
					break;
			}
			break;
		case TABLE_GROUPE_QUESTION:
			switch($_REQUEST['act'])
			{
				case 'delete':
					$_REQUEST['idGroupeQuestion'] = $_REQUEST['id'];
					$_REQUEST['act'] = $_REQUEST['act'];
					require_once(EVA_METABOXES_PLUGIN_DIR . 'veilleReglementaire/groupeQuestionPersistance.php');
			}
			break;
		case TABLE_QUESTION:
			switch($_REQUEST['act'])
			{
				case 'delete':
					$_REQUEST['idQuestion'] = $_REQUEST['id'];
					$_REQUEST['idGroupeQuestions'] = $_REQUEST['idPere'];
					$_REQUEST['act'] = $_REQUEST['act'];
					require_once(EVA_METABOXES_PLUGIN_DIR . 'veilleReglementaire/questionPersistance.php');
			}
			break;
		case TABLE_CATEGORIE_DANGER:
			switch($_REQUEST['act'])
			{
				case 'none':
					switch($_REQUEST['affichage'])
					{
						case "affichageTable":
						case "affichageListe":
							$_REQUEST['affichage'] = $_REQUEST['affichage'];
							require_once(EVA_MODULES_PLUGIN_DIR . 'dangers/partieGaucheDangers.php');
							echo $script . $partieGauche;
							break;
					}
			}
			break;
		case 'dashboardStats':
		{
			switch($_REQUEST['tab'])
			{
				case 'user':
				{
					$userDashboardStats = evaUser::dashBoardStats();

					$idTable = 'userDashBordStats';
					$titres = array( __('Stat index', 'evarisk'), __('Statistique', 'evarisk'), __('Valeur', 'evarisk') );
					if(count($userDashboardStats) > 0)
					{
						foreach($userDashboardStats as $statName => $statValue)
						{
							switch($statName)
							{
								case 'TOTAL_USER':
								{
									$statId = 1;
									$statName = __('Nombre d\'utilisateurs total', 'evarisk');
								}
								break;
								case 'EVALUATED_USER':
								{
									$statId = 2;
									$statName = __('Utilisateur ayant particip&eacute; &agrave; l\'audit', 'evarisk');
								}
								break;
							}
							unset($valeurs);
							$valeurs[] = array('value' => $statId);
							$valeurs[] = array('value' => $statName);
							$valeurs[] = array('value' => $statValue);
							$lignesDeValeurs[] = $valeurs;
							$idLignes[] = 'userDashboardStat' . $statId;
							$outputDatas = true;
						}
					}
					else
					{
						unset($valeurs);
						$valeurs[] = array('value'=>'');
						$valeurs[] = array('value'=>__('Aucun r&eacute;sultat trouv&eacute;', 'evarisk'));
						$valeurs[] = array('value'=>'');
						$lignesDeValeurs[] = $valeurs;
						$idLignes[] = 'userDashboardStatEmpty';
						$outputDatas = false;
					}

					$classes = array('','','');
					$tableOptions = '';

					if($outputDatas)
					{
						$script = 
						'<script type="text/javascript">
							evarisk(document).ready(function() {
								evarisk("#' . $idTable . '").dataTable({
									"bInfo": false,
									"oLanguage": {
										"sSearch": "<span class=\'ui-icon searchDataTableIcon\' >&nbsp;</span>",
										"sEmptyTable": "' . __('Aucune statistique trouv&eacute;e', 'evarisk') . '",
										"sLengthMenu": "' . __('Afficher _MENU_ statistiques', 'evarisk') . '",
										"sInfoEmpty": "' . __('Aucune statistique', 'evarisk') . '",
										"sZeroRecords": "' . __('Aucune statistique trouv&eacute;e', 'evarisk') . '",
										"oPaginate": {
											"sFirst": "' . __('Premi&eacute;re', 'evarisk') . '",
											"sLast": "' . __('Derni&egrave;re', 'evarisk') . '",
											"sNext": "' . __('Suivante', 'evarisk') . '",
											"sPrevious": "' . __('Pr&eacute;c&eacute;dente', 'evarisk') . '"
										}
									},
									"aoColumns":	[
										{"bVisible": false},
										null,
										null
									]
									' . $tableOptions . '
								});
								evarisk("#' . $idTable . '").children("tfoot").remove();
								evarisk("#' . $idTable . '_wrapper").removeClass("dataTables_wrapper");
							});
						</script>';
						echo evaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script);
					}
				}
				break;
				case 'risk':
				{
					$userDashboardStats = (array) Risque::dashBoardStats();
					$userDashboardStats = array_merge($userDashboardStats, Risque::getRiskRange('higher'));

					$idTable = 'riskDashBordStats';
					$titres = array( __('Stat index', 'evarisk'), __('Statistique', 'evarisk'), __('Valeur', 'evarisk') );
					if(count($userDashboardStats) > 0)
					{
						foreach($userDashboardStats as $statName => $statValue)
						{
							switch($statName)
							{
								case 'TOTAL_RISK_NUMBER':
								{
									$statId = 1;
									$statName = __('Nombre total de risque', 'evarisk');
								}
								break;
								case 'HIGHER_RISK':
								{
									$statId = 2;
									$statName = __('Quotation la plus &eacute;lev&eacute;e', 'evarisk');
								}
								break;
								case 'LOWER_RISK':
								{
									$statId = 3;
									$statName = __('Quotation la plus basse', 'evarisk');
								}
								break;
							}
							unset($valeurs);
							$valeurs[] = array('value' => $statId);
							$valeurs[] = array('value' => $statName);
							$valeurs[] = array('value' => $statValue);
							$lignesDeValeurs[] = $valeurs;
							$idLignes[] = 'riskDashboardStat' . $statId;
							$outputDatas = true;
						}
					}
					else
					{
						unset($valeurs);
						$valeurs[] = array('value'=>'');
						$valeurs[] = array('value'=>__('Aucun r&eacute;sultat trouv&eacute;', 'evarisk'));
						$valeurs[] = array('value'=>'');
						$lignesDeValeurs[] = $valeurs;
						$idLignes[] = 'riskDashboardStatEmpty';
						$outputDatas = false;
					}

					$classes = array('','','');
					$tableOptions = '';

					if($outputDatas)
					{
						$script = 
						'<script type="text/javascript">
							evarisk(document).ready(function() {
								evarisk("#' . $idTable . '").dataTable({
									"bInfo": false,
									"oLanguage": {
										"sSearch": "<span class=\'ui-icon searchDataTableIcon\' >&nbsp;</span>",
										"sEmptyTable": "' . __('Aucun risque trouv&eacute;e', 'evarisk') . '",
										"sLengthMenu": "' . __('Afficher _MENU_ risques', 'evarisk') . '",
										"sInfoEmpty": "' . __('Aucune risque', 'evarisk') . '",
										"sZeroRecords": "' . __('Aucune risque trouv&eacute;e', 'evarisk') . '",
										"oPaginate": {
											"sFirst": "' . __('Premi&eacute;re', 'evarisk') . '",
											"sLast": "' . __('Derni&egrave;re', 'evarisk') . '",
											"sNext": "' . __('Suivante', 'evarisk') . '",
											"sPrevious": "' . __('Pr&eacute;c&eacute;dente', 'evarisk') . '"
										}
									},
									"aoColumns":	[
										{"bVisible": false},
										null,
										null
									]
									' . $tableOptions . '
								});
								evarisk("#' . $idTable . '").children("tfoot").remove();
								evarisk("#' . $idTable . '_wrapper").removeClass("dataTables_wrapper");
							});
						</script>';
						echo evaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script);
					}
				}
				break;
				case 'danger':
				{
					echo __('Disponible prochainement', 'evarisk');
				}
				break;
			}
		}
		break;
	}
}