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
require_once(EVA_LIB_PLUGIN_DIR . 'methode/methodeEvaluation.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'epi/evaEPITable.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'epi/evaEPI.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'epi/evaEPI.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'risque/Risque.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'users/evaUserGroup.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'users/evaUserEvaluatorGroup.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'users/evaUser.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'photo/evaPhoto.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'gestionDocumentaire/gestionDoc.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'evaRecommandation/evaRecommandation.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'database.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'Zip/Zip.class.php');

@header('Content-Type: text/html; charset=' . get_option('blog_charset'));

/*
 * Paramètres passés en POST
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
						else //Le fils est une unité
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
						//Le fils est une catégorie
						{
							$categorie = categorieDangers::getCategorieDanger($idFils);
							$pereActu = Arborescence::getPere($_REQUEST['nom'], $categorie);
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
											"table": "' . TABLE_CATEGORIE_DANGER . '",
											"act": "edit",
											"id": "' . $_REQUEST['id'] . '",
											"partie": "right",
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
			case TABLE_UTILISE_EPI:
				{
					global $wpdb;
					$tableElement = eva_tools::IsValid_Variable($_REQUEST['tableElement']);
					$idElement = (int)eva_tools::IsValid_Variable($_REQUEST['idElement']);
					$sql = "DELETE FROM " . TABLE_UTILISE_EPI . " WHERE elementID = " . $idElement . " AND elementTable='" . $tableElement . "';";
					$wpdb->query($sql);
					unset($epiId);
					$insert = '';
					if(count($_REQUEST['epis']) > 0)
						foreach($_REQUEST['epis'] as $epi)
						{
							$epiId = (int)eva_tools::IsValid_Variable($epi);
							$insert = $insert . "(" . $epiId . ", " . $idElement . ", '" . $tableElement . "'), ";
						}
					if($insert != '')
					{
						$insert = substr($insert, 0, strlen($insert) - 2);
						$sql = "INSERT INTO " . TABLE_UTILISE_EPI . " (`ppeId`, `elementID`, `elementTable`) VALUES " . $insert;
						if($wpdb->query($sql))
							$message = '<p><strong><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;Succ&egrave;s de l\'enregistrement</strong></p>';
						else
							$message = '<p><strong><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="noresponse" style="vertical-align:middle;" />&nbsp;Probl&egrave;me lors de l\'enregistrement</strong></p>';
					}
					else
					{
						$message = '<p><strong><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;Pas de probl&egrave;me lors de l\'enregistrement</strong></p>';
					}
					echo '
						<script type="text/javascript">
							evarisk(document).ready(function() {
								actionMessageShow("#messageEPI", "' . addslashes($message) . ');
								setTimeout(\'actionMessageHide("#message")\',7500);
							});
						</script>';
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

						{//Choix de la catégorie de dangers
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

						{//Choix de la méthode
							$methodes = MethodeEvaluation::getMethods('Status="Valid"');
							$script .= '
							evarisk("#' . $currentId . 'methodeFormRisque").change(function(){
								evarisk("#' . $currentId . 'divVariablesFormRisque").html(evarisk("#loadingImg").html());
								evarisk("#' . $currentId . 'divVariablesFormRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_METHODE . '", "act":"reloadVariables", "idMethode":evarisk("#' . $currentId . 'methodeFormRisque").val(), "idRisque": "' . $idRisque . '"});
							});';
							if($risque[0] != null)
							{// Si l'on édite un risque, on sélectionne la bonne méthode
								$idSelection = $risque[0]->id_methode;
							}
							else
							{// Sinon on sélectionne la première méthode
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
							{// Si l'on édite un risque, on remplit l'aire de texte avec sa description
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
							//On récupère les actions relatives à l'élément de provenance.
							$tachesSoldees = new EvaTaskTable();
							$tacheLike = new EvaTask();
							$tacheLike->setIdFrom($idRisque);
							$tacheLike->setTableFrom(TABLE_RISQUE);
							if(options::getOptionValue('affecter_uniquement_tache_soldee_a_un_risque') == 'oui')
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

							if(is_array($tachesAssocieesNonSoldees) && (options::getOptionValue('affecter_uniquement_tache_soldee_a_un_risque') == 'oui'))
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
						$racine = $wpdb->get_row( 'SELECT * FROM ' . TABLE_GROUPE_QUESTION . ' where nom="' . $_REQUEST['nomRacine'] . '"');
						$valeurDefaut = $racine->nom;
						$selection = $_REQUEST['selection'];
						echo evaDisplayInput::afficherComboBoxArborescente($racine, TABLE_GROUPE_QUESTION, $_REQUEST['idSelect'], $_REQUEST['labelSelect'], $_REQUEST['nameSelect'], $valeurDefaut, $selection);
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
						if($idFils == str_replace('node-' . $_REQUEST['location'] . '-','', $fils)) //Le fils est une tâche
						{
							$tache = new EvaTask($idFils);
							$tache->load();
							$tache->transfert($idPere);
						}
						else //Le fils est une activité
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
							$tache->setProgressionStatus('Done');
							$tache->setdateSolde(date('Y-m-d H:i:s'));

							/*	Get the task subelement to set the progression status to DoneByChief	*/
							$tache->markAllSubElementAsDone();
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
						$tache->computeProgression();
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
						// echo evaPhoto::galleryContent($_REQUEST['table'], $_REQUEST['idElement']);
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
									reloadcontainer();
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
									reloadcontainer();
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
									reloadcontainer();
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
									reloadcontainer();
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
			case TABLE_LIAISON_USER_GROUPS:
				switch($_REQUEST['act'])
				{
					case "save":
					{
						$status = evaUserGroup::saveBind($_REQUEST['idGroupe'], $_REQUEST['idElement'], $_REQUEST['tableElement']);

						switch($_REQUEST['tableElement'])
						{
							case TABLE_GROUPEMENT:
								$complement = "au groupement";
								break;
							case TABLE_UNITE_TRAVAIL:
								$complement = "&agrave; l'unit&eacute;";
								break;
						}
						$messageInfo = '<script type="text/javascript">
							evarisk(document).ready(function(){
								evarisk("#message' . TABLE_LIAISON_USER_GROUPS . '").addClass("updated");';
						if($status['result'] != 'error')
						{
							$messageInfo = $messageInfo . '
									evarisk("#message' . TABLE_LIAISON_USER_GROUPS . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le groupe d\'utilisateurs a &eacute;t&eacute; correctement %s %s.', 'evarisk') . '</strong></p>', __('affect&eacute;', 'evarisk'), __($complement, 'evarisk'))) . '");';
						}
						else
						{
							$messageInfo = $messageInfo . '
									evarisk("#message' . TABLE_LIAISON_USER_GROUPS . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le groupe d\'utilisateurs n\'a pas pu &ecirc;tre %s %s.', 'evarisk') . '</strong></p>', __('affect&eacute;', 'evarisk'), __($complement, 'evarisk'))) . '");';
						}
						$messageInfo = $messageInfo . '
									evarisk("#message' . TABLE_LIAISON_USER_GROUPS . '").show();
									setTimeout(function(){
										evarisk("#message' . TABLE_LIAISON_USER_GROUPS . '").removeClass("updated");
										evarisk("#message' . TABLE_LIAISON_USER_GROUPS . '").hide();
									},7500);
									evarisk("#ongletVoirLesRisques").click();
								});
							</script>';
						echo $messageInfo . evaUserGroup::boxGroupesUtilisateursEvaluation($_REQUEST['tableElement'], $_REQUEST['idElement']);
					}
					break;
					case "delete":
					{
						$status = evaUserGroup::deleteBind($_REQUEST['idGroupe'], $_REQUEST['idElement'], $_REQUEST['tableElement']);

						switch($_REQUEST['tableElement'])
						{
							case TABLE_GROUPEMENT:
								$complement = "du groupement";
								break;
							case TABLE_UNITE_TRAVAIL:
								$complement = "du l'unit&eacute;";
								break;
						}
						$messageInfo = '<script type="text/javascript">
							evarisk(document).ready(function(){
								evarisk("#message' . TABLE_LIAISON_USER_GROUPS . '").addClass("updated");';
						if($status['result'] != 'error')
						{
							$messageInfo = $messageInfo . '
									evarisk("#message' . TABLE_LIAISON_USER_GROUPS . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le groupe d\'utilisateurs a &eacute;t&eacute; correctement %s %s.', 'evarisk') . '</strong></p>', __('d&eacute;saffect&eacute;', 'evarisk'), __($complement, 'evarisk'))) . '");';
						}
						else
						{
							$messageInfo = $messageInfo . '
									evarisk("#message' . TABLE_LIAISON_USER_GROUPS . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le groupe d\'utilisateurs n\'a pas pu &ecirc;tre %s %s.', 'evarisk') . '</strong></p>', __('d&eacute;saffect&eacute;', 'evarisk'), __($complement, 'evarisk'))) . '");';
						}
						$messageInfo = $messageInfo . '
									evarisk("#message' . TABLE_LIAISON_USER_GROUPS . '").show();
									setTimeout(function(){
										evarisk("#message' . TABLE_LIAISON_USER_GROUPS . '").removeClass("updated");
										evarisk("#message' . TABLE_LIAISON_USER_GROUPS . '").hide();
									},7500);
									evarisk("#ongletVoirLesRisques").click();
								});
							</script>';
						echo $messageInfo . evaUserGroup::boxGroupesUtilisateursEvaluation($_REQUEST['tableElement'], $_REQUEST['idElement']);
					}
					break;
				}
				break;
			case TABLE_EVA_EVALUATOR_GROUP_BIND:
				switch($_REQUEST['act'])
				{
					case "save":
					{
						$status = evaUserEvaluatorGroup::saveBind($_REQUEST['idGroupe'], $_REQUEST['idElement'], $_REQUEST['tableElement']);

						switch($_REQUEST['tableElement'])
						{
							case TABLE_GROUPEMENT:
								$complement = "au groupement";
								break;
							case TABLE_UNITE_TRAVAIL:
								$complement = "&agrave; l'unit&eacute;";
								break;
						}
						$messageInfo = '<script type="text/javascript">
							evarisk(document).ready(function(){
								evarisk("#message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '").addClass("updated");';
						if($status['result'] != 'error')
						{
							$messageInfo = $messageInfo . '
									evarisk("#message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le groupe d\'&eacute;valuateurs a &eacute;t&eacute; correctement %s %s.', 'evarisk') . '</strong></p>', __('affect&eacute;', 'evarisk'), __($complement, 'evarisk'))) . '");';
						}
						else
						{
							$messageInfo = $messageInfo . '
									evarisk("#message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le groupe d\'&eacute;valuateurs n\'a pas pu &ecirc;tre %s %s.', 'evarisk') . '</strong></p>', __('affect&eacute;', 'evarisk'), __($complement, 'evarisk'))) . '");';
						}
						$messageInfo = $messageInfo . '
									evarisk("#message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '").show();
									setTimeout(function(){
										evarisk("#message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '").removeClass("updated");
										evarisk("#message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '").hide();
									},7500);
									evarisk("#ongletVoirLesRisques").click();
								});
							</script>';
						echo $messageInfo . evaUserEvaluatorGroup::boxGroupesUtilisateursEvaluation($_REQUEST['tableElement'], $_REQUEST['idElement']);
					}
					break;
					case "delete":
					{
						$status = evaUserEvaluatorGroup::deleteBind($_REQUEST['idBind'], $_REQUEST['idGroupe'], $_REQUEST['idElement'], $_REQUEST['tableElement']);

						switch($_REQUEST['tableElement'])
						{
							case TABLE_GROUPEMENT:
								$complement = "du groupement";
								break;
							case TABLE_UNITE_TRAVAIL:
								$complement = "du l'unit&eacute;";
								break;
						}
						$messageInfo = '<script type="text/javascript">
							evarisk(document).ready(function(){
								evarisk("#message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '").addClass("updated");';
						if($status['result'] != 'error')
						{
							$messageInfo = $messageInfo . '
									evarisk("#message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le groupe d\'&eacute;valuateurs a &eacute;t&eacute; correctement %s %s.', 'evarisk') . '</strong></p>', __('d&eacute;saffect&eacute;', 'evarisk'), __($complement, 'evarisk'))) . '");';
						}
						else
						{
							$messageInfo = $messageInfo . '
									evarisk("#message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le groupe d\'&eacute;valuateurs n\'a pas pu &ecirc;tre %s %s.', 'evarisk') . '</strong></p>', __('d&eacute;saffect&eacute;', 'evarisk'), __($complement, 'evarisk'))) . '");';
						}
						$messageInfo = $messageInfo . '
									evarisk("#message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '").show();
									setTimeout(function(){
										evarisk("#message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '").removeClass("updated");
										evarisk("#message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '").hide();
									},7500);
									evarisk("#ongletVoirLesRisques").click();
								});
							</script>';
						echo $messageInfo . evaUserEvaluatorGroup::boxGroupesUtilisateursEvaluation($_REQUEST['tableElement'], $_REQUEST['idElement']);
					}
					break;
				}
				break;
			case TABLE_OPTION:
				switch($_REQUEST['act'])
				{
					case 'editOption':
					{
						$id = (isset($_REQUEST['id'])) ? eva_tools::IsValid_Variable($_REQUEST['id']) : '';
						$id = str_replace('editOption-', '', $id);

						$option = options::getOption($id);

						echo options::editOptionForm($option->typeOption) . '
<script type="text/javascript" >
	evarisk("#idOption").val("' . $id . '");
	evarisk("#valeur").val("' . $option->valeur . '");
	evarisk("#optionName").html("' . $option->nomAffiche . '");
	evarisk(".closeLightBoxContainer").click(function(){emptyOptionForm();});
</script>';
					}
					break;
					case 'update':
					{
						$optionId = eva_tools::IsValid_Variable($_REQUEST['id']);
						$optionValue = eva_tools::IsValid_Variable($_REQUEST['valeur']);

						$update = options::updateOption($optionId, $optionValue);
						if($update)
						{
							$messageInfo = addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Sauvegarde r&eacute;ussie', 'evarisk') . '</strong></p>');
							$actionAfterSuccess = 'evarisk("#optionValueContainer' . $optionId . '").html("' . $optionValue . '");';
						}
						else
						{
							$messageInfo = addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La sauvegarde a &eacute;chou&eacute;e', 'evarisk') . '</strong></p>');
							$actionAfterSuccess = '';
						}
						echo 
'<script type="text/javascript">
	evarisk(document).ready(function(){
		emptyOptionForm();
		actionMessageShow("#messageOption", "' . $messageInfo . '");
		setTimeout(\'actionMessageHide("#messageOption")\',7500);

		' . $actionAfterSuccess . '
	});
</script>';
					}
					break;
					case 'save':
					{
						$optionValue = eva_tools::IsValid_Variable($_REQUEST['value']);
						$optionName = eva_tools::IsValid_Variable($_REQUEST['optionName']);
						$optionShownName = eva_tools::IsValid_Variable($_REQUEST['optionShownName']);
						$optionDomain = eva_tools::IsValid_Variable($_REQUEST['optionDomain']);
						$optionType = eva_tools::IsValid_Variable($_REQUEST['optionType']);
						$optionStatus = eva_tools::IsValid_Variable($_REQUEST['optionStatus']);

						$createOption = options::createOption($optionName, $optionValue, $optionShownName, $optionDomain, $optionType, $optionStatus);
					}
					break;
					case 'updateFromName':
					{
						$optionValue = eva_tools::IsValid_Variable($_REQUEST['value']);
						$optionName = eva_tools::IsValid_Variable($_REQUEST['optionName']);

						$createOption = options::updateOptionFromName($optionName, $optionValue);
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
					case 'voirDocumentUnique' :
						require_once(EVA_METABOXES_PLUGIN_DIR . 'documentUnique/documentUnique.php');
						$tableElement = $_REQUEST['tableElement'];
						$idElement = $_REQUEST['idElement'];
						echo eva_documentUnique::formulaireGenerationDocumentUnique($tableElement, $idElement) . '<script type="text/javascript" >evarisk(document).ready(function(){evarisk("#ui-datepicker-div").hide();});</script>';
					break;
					case 'voirHistoriqueDocumentUnique' :
						require_once(EVA_METABOXES_PLUGIN_DIR . 'documentUnique/documentUnique.php');
						$tableElement = $_REQUEST['tableElement'];
						$idElement = $_REQUEST['idElement'];
						echo eva_documentUnique::getDUERList($tableElement, $idElement);
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
				}
				break;
			case TABLE_FP:
				require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/ficheDePoste/ficheDePoste.class.php');
				switch($_REQUEST['act'])
				{
					case 'saveFichePoste':
					case 'generateWorkUnitSheet':
						if($_REQUEST['act'] == 'saveFichePoste')
						{
							require_once(EVA_METABOXES_PLUGIN_DIR . 'ficheDePoste/ficheDePostePersistance.php');
						}
						$tableElement = $_REQUEST['tableElement'];
						$idElement = $_REQUEST['idElement'];
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
							$_POST['tableElement'] = $workUnit['table'];
							$_POST['idElement'] = $workUnit['id'];
							$_POST['nomDuDocument'] = date('Ymd') . '_UT' . $workUnit['id'] . '_' . eva_tools::slugify_noaccent(str_replace(' ', '_', $workUnit['nom']));
							$_POST['nomEntreprise'] = $groupementParent->nom;

							include(EVA_METABOXES_PLUGIN_DIR . 'ficheDePoste/ficheDePostePersistance.php');
							$lastDocument = eva_WorkUnitSheet::getGeneratedDocument($tableElement, $idElement, 'last');
							$odtFile = 'ficheDePoste/' . $workUnit['table'] . '/' . $workUnit['id'] . '/' . $lastDocument->name . '_V' . $lastDocument->revision . '.odt';
							if( is_file(EVA_RESULTATS_PLUGIN_DIR . $odtFile) )
							{
								$file_to_zip[] = EVA_RESULTATS_PLUGIN_DIR . $odtFile;
							}
						}

						eva_tools::make_recursiv_dir($pathToZip);
						if(count($file_to_zip) > 0)
						{
							/*	ZIP THE FILE	*/
							$archive = new eva_Zip(date('YmdHis') . '_fichesDePoste.zip');
							$archive->setFiles($file_to_zip);
							$archive->compressToPath($pathToZip);
							eva_gestionDoc::saveNewDoc('fiche_de_poste_groupement', $mainTableElement, $mainIDElement, str_replace(EVA_GENERATED_DOC_DIR, '', $pathToZip . date('YmdHis') . '_fichesDePoste.zip'));
						}

						echo '
<script type="text/javascript" >
	evarisk("#divFicheDePoste").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
	{
		"post":"true",
		"table":"' . TABLE_FP . '",
		"act":"voirHistoriqueFicheDePosteGroupement",
		"tableElement":"' . $mainTableElement . '",
		"idElement":' . $mainIDElement . '
	});
</script>';
					}
					break;
					case 'voirHistoriqueFicheDePosteGroupement':
					{
						$tableElement = $_REQUEST['tableElement'];
						$idElement = $_REQUEST['idElement'];
						$output = '
<input type="button" class="clear button-primary alignright" value="' . __('G&eacute;n&eacute;rer les fiches de postes', 'evarisk') . '" id="saveWorkUnitSheetForGroupement" style="margin:0px 0px 18px;" >  
<script type="text/javascript" >
	evarisk("#saveWorkUnitSheetForGroupement").click(function(){
		evarisk("#divFicheDePoste").html(evarisk("#loadingImg").html());
		evarisk("#divFicheDePoste").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
		{
			"post":"true",
			"table":"' . TABLE_FP . '",
			"act":"saveWorkUnitSheetForGroupement",
			"tableElement":"' . $tableElement . '",
			"idElement":' . $idElement . '
		});
	});
</script>
<div class="clear" >';
						$ficheDePoste_du_Groupement = eva_gestionDoc::getDocumentList($tableElement, $idElement, 'fiche_de_poste_groupement', "dateCreation DESC");
						if(count($ficheDePoste_du_Groupement) > 0)
						{
							foreach($ficheDePoste_du_Groupement as $fdpGpt)
							{
								if(is_file(EVA_GENERATED_DOC_DIR . $fdpGpt->chemin . $fdpGpt->nom))
								{
									$output .= '-&nbsp;' . sprintf(__('G&eacute;n&eacute;r&eacute; le %s: <a href="%s" >%s</a>', 'evarisk'), eva_tools::transformeDate($fdpGpt->dateCreation), EVA_GENERATED_DOC_URL . $fdpGpt->chemin . $fdpGpt->nom, $fdpGpt->nom) . '<br/>';
								}
							}
						}
						else
						{
							$output .= __('Aucune fiche n\'a &eacute;t&eacute; cr&eacute;e pour le moment', 'evarisk');
						}
						$output .= '
</div>';
						echo $output;
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
					case 'loadInformation':
					{
						$id = (isset($_REQUEST['id']) && ($_REQUEST['id'] != '') && ($_REQUEST['id'] != '0')) ? eva_tools::IsValid_Variable($_REQUEST['id']) : '';
						$recommandationInfos = evaRecommandation::getRecommandation($id);
						echo '
<script type="text/javascript" >
	evarisk(document).ready(function(){
		evarisk("#ui-dialog-title-recommandationForm").html("' . sprintf(__('&Eacute;diter la pr&eacute;conisation %s', 'evarisk'), $recommandationInfos->nom) . '");
		evarisk("#loadingRecommandationForm").html("");
		evarisk("#loadingRecommandationForm").hide();
		evarisk("#id_preconisation").val("' . $id . '");
		evarisk("#nom_preconisation").val("' . $recommandationInfos->nom . '");
		evarisk("#description_preconisation").val("' . $recommandationInfos->description . '");
		evarisk("#recommandationFormContent").show();
	});
</script>';
					}
					break;
					case 'saveRecommandation':
					{
						$nom_preconisation = (isset($_REQUEST['nom_preconisation']) && ($_REQUEST['nom_preconisation'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['nom_preconisation']) : '';
						$description_preconisation = (isset($_REQUEST['description_preconisation']) && ($_REQUEST['description_preconisation'] != '')) ? eva_tools::IsValid_Variable($_REQUEST['description_preconisation']) : '';
						$id_preconisation = (isset($_REQUEST['id_preconisation']) && ($_REQUEST['id_preconisation'] != '') && ($_REQUEST['id_preconisation'] != '0')) ? eva_tools::IsValid_Variable($_REQUEST['id_preconisation']) : '0';
						$id_categorie_preconisation = (isset($_REQUEST['id_categorie_preconisation']) && ($_REQUEST['id_categorie_preconisation'] != '') && ($_REQUEST['id_categorie_preconisation'] != '0')) ? eva_tools::IsValid_Variable($_REQUEST['id_categorie_preconisation']) : '0';

						$recommandations_informations = array();
						$recommandations_informations['nom'] = $nom_preconisation;
						$recommandations_informations['description'] = $description_preconisation;

						//Check the value of the recommandation identifier. 
						if($id_preconisation <= 0)
						{	//	If the value is equal or less than 0 we create a new recommandation
							$recommandations_informations['status'] = 'valid';
							$recommandations_informations['id_categorie_preconisation'] = $id_categorie_preconisation;
							$recommandations_informations['creation_date'] = date('Y-m-d H:i:s');
							$recommandationActionResult = evaRecommandation::saveRecommandation($recommandations_informations);
						}
						else
						{	//	If the value is more than 0 we update the corresponding recommandation
							$recommandationActionResult = evaRecommandation::updateRecommandation($recommandations_informations, $id_preconisation);
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
					case 'reloadRecommandationList':
					{
						echo evaRecommandation::getRecommandationTable();
					}
					break;
					case 'deleteRecommandation':
					{
						$id = (isset($_REQUEST['id']) && ($_REQUEST['id'] != '') && ($_REQUEST['id'] != '0')) ? eva_tools::IsValid_Variable($_REQUEST['id']) : '';
						$recommandations_informations['status'] = 'deleted';
						$recommandationActionResult = evaRecommandation::updateRecommandation($recommandations_informations, $id);
						$moreRecommandationScript = '';
						if($recommandationActionResult == 'error')
						{
							$recommandationActionMessage = '<img src=\'' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png\' class=\'messageIcone\' alt=\'error\' />' . __('Une erreur est survenue lors de la suppression de la pr&eacute;conisation. Merci de r&eacute;essayer.', 'evarisk');
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
					foreach($_REQUEST["EPI"] as $epi)
					{
						$insertions['EPI'][$epi[1]] = $epi[0];
					}
					foreach($_REQUEST["methodes"] as $methode)
					{
						$insertions['methodes'][$methode[1]] = $methode[0];
					}
					require_once(EVA_MODULES_PLUGIN_DIR . 'installation/creationTables.php');
					require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
					require_once(EVA_MODULES_PLUGIN_DIR . 'installation/initialisationPermissions.php');
					evarisk_creationTables();
					evarisk_insertions($insertions);
					evarisk_init_permission();
					echo '<script type="text/javascript">window.top.location.href = "' . admin_url("plugins.php") . '"</script>';
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
				echo Risque::getTableQuotationRisque($_REQUEST['tableProvenance'], $_REQUEST['idProvenance']) . '<br />';

				$taches = new EvaTaskTable();
				$tacheLike = new EvaTask();
				$tacheLike->setIdFrom($_REQUEST['idProvenance']);
				$tacheLike->setTableFrom($_REQUEST['tableProvenance']);
				$taches->getTasksLike($tacheLike);
				//On demande à l'utilisateur de choisir l'action qui l'intéresse.
				$actionsCorrectives = $taches->getTasks();
				//On construit les Gantts des différentes actions
				$output = '';
				if(($actionsCorrectives != null) && (count($actionsCorrectives) > 0))
				{
					foreach($actionsCorrectives as $actionCorrective)
					{
						$tasksGantt = $moreSuiviOn = $moreSuiviOff = '';
						$idDiv = $actionCorrective->getTableFrom() . $actionCorrective->getIdFrom() . '-' . TABLE_TACHE . $actionCorrective->getId();
						$output = '<div id="' . $idDiv . '-choix" class="nomAction" style="cursor:pointer;" ><span >+</span> ' . $actionCorrective->getName() . '</div>';

						switch($actionCorrective->getTableFrom())
						{
							case TABLE_RISQUE:
								$output .= Risque::getTableQuotationRisqueAvantApresAC($_REQUEST['tableProvenance'], $_REQUEST['idProvenance'], $actionCorrective, $idDiv);
								$moreSuiviOn = 'evarisk("#' . $idDiv . '-affichage-quotation").show();';
								$moreSuiviOff = 'evarisk("#' . $idDiv . '-affichage-quotation").hide();';
							break;
						}

						echo $output . '<div id="' . $idDiv . '-affichage" class="affichageAction" style="display:none;"></div>';
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
										' . $moreSuiviOn . '
										evarisk(this).children("span:first").html("-");
									},
									function()
									{
										evarisk("#' . $idDiv . '-affichage").hide();
										' . $moreSuiviOff . '
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

							if( (($actionCorrective->getProgressionStatus() == 'notStarted') || ($actionCorrective->getProgressionStatus() == 'inProgress')) && (options::getOptionValue('possibilite_Modifier_Tache_Soldee') == 'non') )
							{
								echo EvaDisplayInput::afficherInput('button', $idBoutonEnregistrer, 'Enregistrer', null, '', $idDiv . 'save', false, false, '', 'button-primary alignright', '', '', $scriptEnregistrement);
							}
						}
						echo '<script type="text/javascript">
								evarisk(document).ready(function(){
									//Transformation du texte des cases avancement en input
									evarisk("#' . $idDiv . '-affichage .ui-gantt-table td:nth-child(3)").each(function(){
										evarisk(this).html("<input type=\"text\" value=\"" + evarisk(this).html() + "\" maxlength=3 style=\"width:3em;\"/>%");
										if(evarisk(this).parent("tr").children("td:first").html().match("^T")=="T")
										{
											evarisk(this).children("input").attr("disabled","disabled");
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
					$output = $script = '';
					if(count($risques) > 0)
					{
						foreach($risques as $idRisque => $infosRisque)
						{
							/*	Get the different corrective actions for the actual risk	*/
							$actionsCorrectives = '';
							$taches = new EvaTaskTable();
							$tacheLike = new EvaTask();
							$tacheLike->setIdFrom($idRisque);
							$tacheLike->setTableFrom(TABLE_RISQUE);
							$taches->getTasksLike($tacheLike);
							$tachesActionsCorrectives = $taches->getTasks();
							if(count($tachesActionsCorrectives) > 0)
							{
								$hasActions = true;
								$spacer = '';
								foreach($tachesActionsCorrectives as $taskDefinition)
								{
									$actionsCorrectives .= '<div style="padding:3px;margin:0px 0px 12px 0px;background-color:#EEEEEE;" >';
									$idDiv = $taskDefinition->getTableFrom() . $taskDefinition->getIdFrom() . '-' . TABLE_TACHE . $taskDefinition->getId();

									/*	Get the task children	*/
									$tacheActionCorrective = $taskDefinition->getDescendants($taskDefinition);
									$tacheActionCorrective = array_merge(array($taskDefinition->getId() => $taskDefinition), $tacheActionCorrective->getTasks());

									/*	Get the task's actions	*/
									$activitesDeLaTache = $taskDefinition->getActivitiesDependOn()->getActivities();

								/*	Start output	*/
									/*	If there are more than one action in the task output the task	*/
									if((count($activitesDeLaTache) > 1))
									{
										$actionsCorrectives .= $spacer . '<span>' . $taskDefinition->name . '</span><br/>';
										$spacer = '&nbsp;&nbsp;';
									}
									else
									{
										$hasActions = false;
									}

									if(($activitesDeLaTache != null) && (count($activitesDeLaTache) > 0))
									{
										foreach($activitesDeLaTache as $activiteDeLaTache)
										{
											$actionsCorrectives .= 
												'<div style="margin-bottom:12px;" >
													<div class="alignleft" >' . $activiteDeLaTache->name . '</div>
													<div class="alignright" >';

											if($activiteDeLaTache->startDate != '0000-00-00')
											{
												$actionsCorrectives .= '<span class="bold" >' . __('D&eacute;but', 'evarisk') . '</span>&nbsp;:&nbsp;' . eva_tools::transformeDate($activiteDeLaTache->startDate);
											}
											if($activiteDeLaTache->finishDate != '0000-00-00')
											{
												$actionsCorrectives .= '<br/><span class="bold" >' . __('Fin', 'evarisk') . '</span>&nbsp;:&nbsp;' . eva_tools::transformeDate($activiteDeLaTache->finishDate);
											}

											$actionsCorrectives .= '
													</div>
												</div>' . 
												Risque::getTableQuotationRisqueAvantApresAC($tableElement, $idElement, $taskDefinition, $idDiv) . 
											'<div style="margin:12px 0px;" >
												<div style="margin:3px 0px;" ><span class="bold" >' . __('Avancement', 'evarisk') . '&nbsp;:&nbsp;</span>' . $activiteDeLaTache->progression . '%</div>
												<div style="margin:3px 0px;" ><span class="bold" >' . __('Description', 'evarisk') . '&nbsp;:&nbsp;</span>' . $activiteDeLaTache->description . '</div>
												<div style="margin:3px 0px;" ><span class="bold" >' . __('Co&ucirc;t', 'evarisk') . '&nbsp;:&nbsp;</span>' . $activiteDeLaTache->cout . '</div>
												<div style="display:table;margin:12px 0px;" >
													<div class="alignleft" style="width:45%;" >
														<div class="bold" >' . __('Photo avant', 'evarisk') . '&nbsp;:&nbsp;</div>';

											$noPictureBefore = true;
											if($activiteDeLaTache->idPhotoAvant > 0)
											{
												$infosPhoto = evaPhoto::getPhotos(TABLE_ACTIVITE, $activiteDeLaTache->id, " PICTURE.id = '" . $activiteDeLaTache->idPhotoAvant . "' ");
												if(is_file(EVA_GENERATED_DOC_DIR . $infosPhoto[0]->photo))
												{
											$actionsCorrectives .= '
														<img src="' . EVA_GENERATED_DOC_URL . $infosPhoto[0]->photo . '" alt="before corrective action picture" class="pictureThumbs" />';
													$noPictureBefore = false;
												}
											}
											if($noPictureBefore)
											{
											$actionsCorrectives .= __('Aucune n\'a &eacute;t&eacute; d&eacute;finie', 'evarisk');
											}
											$actionsCorrectives .= '
													</div>
													<div class="alignleft" style="width:45%;margin-left:6px;" >
														<div class="bold" >' . __('Photo apr&egrave;s', 'evarisk') . '&nbsp;:&nbsp;</div>';
											$noPictureAfter = true;
											if($activiteDeLaTache->idPhotoApres > 0)
											{
												$infosPhoto = evaPhoto::getPhotos(TABLE_ACTIVITE, $activiteDeLaTache->id, " PICTURE.id = '" . $activiteDeLaTache->idPhotoApres . "' ");
												if(is_file(EVA_GENERATED_DOC_DIR . $infosPhoto[0]->photo))
												{
											$actionsCorrectives .= '
														<img src="' . EVA_GENERATED_DOC_URL . $infosPhoto[0]->photo . '" alt="after corrective action picture" class="pictureThumbs" />';
													$noPictureAfter = false;
												}
											}
											if($noPictureAfter)
											{
											$actionsCorrectives .= __('Aucune n\'a &eacute;t&eacute; d&eacute;finie', 'evarisk');
											}

											$actionsCorrectives .= '
													</div>
												</div>
											</div>
											<hr/>';
										}
									}

									$actionsCorrectives .= '</div>';
								}
							}
							else
							{
								$hasActions = false;
							}

							$output .= 
								'<div style="display:table;margin:12px 0px;" >
									<div class="pointer infoRisqueActuel" id="correctiveAction' . $idRisque . '" >' . Risque::getTableQuotationRisque(TABLE_RISQUE, $idRisque) . '</div>';
							if($hasActions)
							{
							$output .= 
									'<div id="moreCorrectiveAction' . $idRisque . '" ><img id="pictMoreAC' . $idRisque . '" src="' . EVA_IMG_DIVERS_PLUGIN_URL . 'toggle-expand-dark.png" style="vertical-align:middle;" alt="moreInfoOnAC" /><span class="pointer" style="vertical-align:middle;" >' . __('Voir les actions associ&eacute;es &agrave; ce risque', 'evarisk') . '</span></div>
									<div id="correctiveActionContent' . $idRisque . '" style="display:none;" >' . $actionsCorrectives . '</div>';
							}
							else
							{
								$output .= 
									'<div style="width:80%;font-style:italic;margin:3px auto;" >' . __('Il n\'y a aucune action corrective pour ce risque', 'evarisk') . '</div>';
							}
							$output .= 
									'</div>';
							$script .= 
									'evarisk("#moreCorrectiveAction' . $idRisque . '").click(function(){
										evarisk("#correctiveActionContent' . $idRisque . '").toggle();
										if(evarisk("#correctiveActionContent' . $idRisque . '").css("display") == "none"){
											evarisk("#pictMoreAC' . $idRisque . '").attr("src", "' . EVA_IMG_DIVERS_PLUGIN_URL . 'toggle-expand-dark.png");
										}
										else{
											evarisk("#pictMoreAC' . $idRisque . '").attr("src", "' . EVA_IMG_DIVERS_PLUGIN_URL . 'toggle-collapse-dark.png");
										}
									});';
						}
						echo $output . 
							'<script type="text/javascript" >
								evarisk(document).ready(function(){
									' . $script . '
								});
							</script>';
					}
					else
					{
						echo __('Il n\'y a aucun risque pour cette &eacute;l&eacute;ment', 'evarisk');
					}
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
					$messageInfo = $messageInfo . '
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
					$idResponsableIsMandatory = options::getOptionValue('responsable_Action_Obligatoire');

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
						{//Création de la table
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
				//On récupère les actions relatives à l'élément de provenance.
				$taches = new EvaTaskTable();
				$tacheLike = new EvaTask();
				$tacheLike->setIdFrom($_REQUEST['idProvenance']);
				$tacheLike->setTableFrom($_REQUEST['tableProvenance']);
				$taches->getTasksLike($tacheLike);
				//On demande à l'utilisateur de choisir l'action qui l'intéresse.
				$actionsCorrectives = $taches->getTasks();
				//on construit les Gantts des différentes actions
				if($actionsCorrectives != null && count($actionsCorrectives) > 0)
				{
					foreach($actionsCorrectives as $actionCorrective)
					{
						$tasksGantt = '';
						$idDiv = $actionCorrective->getTableFrom() . $actionCorrective->getIdFrom() . '-' . TABLE_TACHE . $actionCorrective->getId();
						echo 
							'<div id="' . $idDiv . '-choix" class="nomAction" style="cursor:pointer;" ><span >+</span> ' . $actionCorrective->getName() . '</div>
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
											evarisk(this).children("input").attr("disabled","disabled");
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
 * Paramètres passés en GET
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