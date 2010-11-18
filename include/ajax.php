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

@header('Content-Type: text/html; charset=' . get_option('blog_charset'));

/*
 * Paramètres passés en POST
 */
if($_POST['post'] == 'true')
{
	if(isset($_POST['table']))
		switch($_POST['table'])
		{
			case TABLE_GROUPEMENT:
				switch($_POST['act'])
				{
					case 'save':
					case 'update':
						global $wpdb;
						switch($_POST['act'])
						{
							case 'save':
								$action = __('sauvegard&eacute;e', 'evarisk');
								break;
							case 'update':
								$action = __('mise &agrave; jour', 'evarisk');
								break;
						}	
						require_once(EVA_METABOXES_PLUGIN_DIR . 'evaluationDesRisques/groupement/groupementPersistance.php');		
						$messageInfo = '
							<script type="text/javascript">
								$(document).ready(function(){
									$("#message").addClass("updated");
									$("#message").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s a correctement &eacute;t&eacute; %s', 'evarisk') . '</strong></p>', __('du groupement', 'evarisk') . ' "' . stripslashes($_POST['nom_groupement']) . '"', $action)) . '");
									$("#message").show();
									setTimeout(function(){
										$("#message").removeClass("updated");
										$("#message").hide();
									},7500);
									
									$(\'#rightEnlarging\').show();
									$(\'#equilize\').click();
									if("' . $_POST['affichage'] . '" == "affichageTable")
									{
										if($(\'#filAriane :last-child\').is("label"))
											$(\'#filAriane :last-child\').remove();
										$(\'#filAriane :last-child\').after(\'<label>&nbsp;&raquo;&nbsp;&Eacute;dition&nbsp;de&nbsp;' . $_POST['nom_groupement'] . '</label>\');
										$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
											"table": "' . TABLE_GROUPEMENT . '",
											"id": "' . $_POST['id'] . '",
											"page": $(\'#pagemainPostBoxReference\').val(),
											"idPere": $(\'#identifiantActuellemainPostBox\').val(),
											"act": "edit",
											"partie": "right",
				"menu": $("#menu").val(),
											"affichage": "affichageTable",
											"partition": "tout"
										});
										$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
											"table": "' . TABLE_GROUPEMENT . '",
											"id": "' . $_POST['id'] . '",
											"page": $(\'#pagemainPostBoxReference\').val(),
											"idPere": $(\'#identifiantActuellemainPostBox\').val(),
											"act": "edit",
											"partie": "left",
				"menu": $("#menu").val(),
											"affichage": "affichageTable",
											"partition": "tout"
										});
									}
									else
									{
										var expanded = new Array();
										$(\'.expanded\').each(function(){expanded.push($(this).attr("id"));});
										$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
											"table": "' . TABLE_GROUPEMENT . '",
											"act": "edit",
											"id": "' . $_POST['id'] . '",
											"partie": "right",
				"menu": $("#menu").val(),
											"affichage": "affichageListe",
											"expanded": expanded
										});
										$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
											"table": "' . TABLE_GROUPEMENT . '",
											"act": "edit",
											"id": "' . $_POST['id'] . '",
											"partie": "left",
				"menu": $("#menu").val(),
											"affichage": "affichageListe",
											"expanded": expanded
										});
									}
									$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
									$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
								});
							</script>';
						echo $messageInfo;
						break;
					case 'edit':
					case 'add':
					case 'changementPage':
						echo '
							<script type="text/javascript">
								$(document).ready(function() {
									if($(\'#rightSide-sortables\').html() == "")
										$(\'#rightEnlarging\').hide();
									else
										$(\'#rightEnlarging\').show();
									if($(\'#leftSide-sortables\').html() == "")
										$(\'#leftEnlarging\').hide();
									else
										$(\'#leftEnlarging\').show();
									
									if("' . $_POST['affichage'] . '" == "affichageTable")
									{
										$("#tablemainPostBox tbody tr:nth-child(3)").each(function(){
											for(var i=1; i<=$(this).children("td").length; i++)
											{
												if($(this).children("td:nth-child(" + i + ")").children("img").attr("id") == "photo' . $_POST['table'] . $_POST['id'] . '")
												{												
													$(this).prevAll("tr:not(tr:first-child)").andSelf().children("td:nth-child(" + i + ")").addClass("edited");
													// 3 * i car nomInfo + : + info
													$(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i) + ")").addClass("edited");
													$(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i - 1) + ")").addClass("edited");
													$(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i - 2) + ")").addClass("edited");
												}
											}
										});
									}
									else
									{
										$("#node-' . $_GET['location'] . '-' . $_POST['id'] . '").addClass("edited");
									}
								});
							</script>';
						require_once(EVA_MODULES_PLUGIN_DIR . 'partieDroite.php');
					break;
					case 'delete':
						require_once(EVA_METABOXES_PLUGIN_DIR . 'evaluationDesRisques/groupement/groupementPersistance.php');		
						echo '
							<script type="text/javascript">
								$(document).ready(function() {
									if($(\'#rightSide-sortables\').html() == "")
										$(\'#rightEnlarging\').hide();
									else
										$(\'#rightEnlarging\').show();
									if($(\'#leftSide-sortables\').html() == "")
										$(\'#leftEnlarging\').hide();
									else
										$(\'#leftEnlarging\').show();
									$(\'#partieEdition\').html(" ");

								});
							</script>';
						require_once(EVA_MODULES_PLUGIN_DIR . 'partieDroite.php');
					break;
					
					case 'defaultPictureSelection':
						echo evaPhoto::setMainPhotoAction($_POST['table'], $_POST['idElement'], $_POST['idPhoto']);
					break;
					case 'DeleteDefaultPictureSelection':
						echo evaPhoto::setMainPhotoAction($_POST['table'], $_POST['idElement'], $_POST['idPhoto'], 'no');
					break;
					case 'deletePicture':
						echo evaPhoto::deletePictureAction($_POST['table'], $_POST['idElement'], $_POST['idPicture']);
					break;
					case 'reloadGallery':
						$script = 
						'<script type="text/javascript">
							$(document).ready(function(){
								$(".qq-upload-list").hide();
							});
						</script>';
						echo $script . evaPhoto::outputGallery($_POST['table'], $_POST['idElement']);
					break;
					case 'showGallery':
						echo evaPhoto::getGallery($_POST['table'], $_POST['idElement']);
					break;
				}
				break;
			case TABLE_UNITE_TRAVAIL:
				switch($_POST['act'])
				{
					case 'save':
					case 'update':
						switch($_POST['act'])
						{
							case 'save':
								$action = __('sauvegard&eacute;e', 'evarisk');
								break;
							case 'update':
								$action = __('mise &agrave; jour', 'evarisk');
								break;
						}	
						require_once(EVA_METABOXES_PLUGIN_DIR . 'evaluationDesRisques/uniteDeTravail/uniteTravailPersistance.php' );
						$messageInfo = '
							<script type="text/javascript">
								$(document).ready(function(){
									$("#message").addClass("updated");
									$("#message").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s a correctement &eacute;t&eacute; %s', 'evarisk') . '</strong></p>', __('de l\'unit&eacute; de travail', 'evarisk') . ' "' . stripslashes($_POST['nom_unite_travail']) . '"', $action)) . '");
									$("#message").show();
									setTimeout(function(){
										$("#message").removeClass("updated");
										$("#message").hide();
									},7500);
									
									$(\'#rightEnlarging\').show();
									$(\'#equilize\').click();
									if("' . $_POST['affichage'] . '" == "affichageTable")
									{
										if($(\'#filAriane :last-child\').is("label"))
											$(\'#filAriane :last-child\').remove();
										$(\'#filAriane :last-child\').after(\'<label>&nbsp;&raquo;&nbsp;&Eacute;dition&nbsp;de&nbsp;' . $_POST['nom_unite_travail'] . '</label>\');
										if($(\'#filAriane :last-child\').is("label"))
											$(\'#filAriane :last-child\').remove();
										$(\'#rightEnlarging\').show();
										$(\'#equilize\').click();
										$(\'#filAriane :last-child\').after(\'<label>&nbsp;&raquo;&nbsp;&Eacute;dition&nbsp;de&nbsp;' . $_POST['nom_unite_travail'] . '</label>\');
										$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
											"table": "' . TABLE_UNITE_TRAVAIL . '",
											"id": "' . $_POST['id'] . '",
											"page": $(\'#pagemainPostBoxReference\').val(),
											"idPere": $(\'#identifiantActuellemainPostBox\').val(),
											"act": "edit",
											"partie": "right",
				"menu": $("#menu").val(),
											"affichage": "affichageTable",
											"partition": "tout"
										});
										$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
											"table": "' . TABLE_UNITE_TRAVAIL . '",
											"id": "' . $_POST['id'] . '",
											"page": $(\'#pagemainPostBoxReference\').val(),
											"idPere": $(\'#identifiantActuellemainPostBox\').val(),
											"act": "edit",
											"partie": "left",
				"menu": $("#menu").val(),
											"affichage": "affichageTable",
											"partition": "tout"
										});
									}
									else
									{
										var expanded = new Array();
										$(\'.expanded\').each(function(){expanded.push($(this).attr("id"));});
										$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
											"table": "' . TABLE_UNITE_TRAVAIL . '",
											"act": "edit",
											"id": "' . $_POST['id'] . '",
											"partie": "right",
				"menu": $("#menu").val(),
											"affichage": "affichageListe",
											"expanded": expanded
										});
										$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
											"table": "' . TABLE_UNITE_TRAVAIL . '",
											"act": "edit",
											"id": "' . $_POST['id'] . '",
											"partie": "left",
				"menu": $("#menu").val(),
											"affichage": "affichageListe",
											"expanded": expanded
										});
									}
									$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
									$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
								});
							</script>';
						echo $messageInfo;
						break;
					case 'edit':
					case 'add':
						echo '
							<script type="text/javascript">
								$(document).ready(function() {
									if($(\'#rightSide-sortables\').html() == "")
										$(\'#rightEnlarging\').hide();
									else
										$(\'#rightEnlarging\').show();
									if($(\'#leftSide-sortables\').html() == "")
										$(\'#leftEnlarging\').hide();
									else
										$(\'#leftEnlarging\').show();
									
									if("' . $_POST['affichage'] . '" == "affichageTable")
									{
										$("#tablemainPostBox tbody tr:nth-child(3)").each(function(){
											for(var i=1; i<=$(this).children("td").length; i++)
											{
												if($(this).children("td:nth-child(" + i + ")").children("img").attr("id") == "photo' . $_POST['table'] . $_POST['id'] . '")
												{												
													$(this).prevAll("tr:not(tr:first-child)").andSelf().children("td:nth-child(" + i + ")").addClass("edited");
													// 3 * i car nomInfo + : + info
													$(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i) + ")").addClass("edited");
													$(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i - 1) + ")").addClass("edited");
													$(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i - 2) + ")").addClass("edited");
												}
											}
										});
									}
									else
									{
										$("#leaf-' . $_POST['id'] . '").addClass("edited");
									}
								});
							</script>';
						require_once(EVA_MODULES_PLUGIN_DIR . 'partieDroite.php');
						break;
					case 'delete':
						require_once(EVA_METABOXES_PLUGIN_DIR . 'evaluationDesRisques/uniteDeTravail/uniteTravailPersistance.php' );
						echo '
							<script type="text/javascript">
								$(document).ready(function() {
									if($(\'#rightSide-sortables\').html() == "")
										$(\'#rightEnlarging\').hide();
									else
										$(\'#rightEnlarging\').show();
									if($(\'#leftSide-sortables\').html() == "")
										$(\'#leftEnlarging\').hide();
									else
										$(\'#leftEnlarging\').show();
									
									$(\'#partieEdition\').html(" ");
								});
							</script>';
						require_once(EVA_MODULES_PLUGIN_DIR . 'partieDroite.php');
					break;
					
					case 'defaultPictureSelection':
						echo evaPhoto::setMainPhotoAction($_POST['table'], $_POST['idElement'], $_POST['idPhoto']);
					break;
					case 'DeleteDefaultPictureSelection':
						echo evaPhoto::setMainPhotoAction($_POST['table'], $_POST['idElement'], $_POST['idPhoto'], 'no');
					break;
					case 'deletePicture':
						echo evaPhoto::deletePictureAction($_POST['table'], $_POST['idElement'], $_POST['idPicture']);
					break;
					case 'reloadGallery':
						$script = 
						'<script type="text/javascript">
							$(document).ready(function(){
								$(".qq-upload-list").hide();
							});
						</script>';
						echo $script . evaPhoto::outputGallery($_POST['table'], $_POST['idElement']);
					break;
					case 'showGallery':
						echo evaPhoto::getGallery($_POST['table'], $_POST['idElement']);
					break;
				}
				break;
			case TABLE_CATEGORIE_DANGER:
				switch($_POST['act'])
				{
					case 'reloadComboDangers':
						$idElement = eva_tools::IsValid_Variable($_POST['idElement']);
						$dangers = categorieDangers::getDangersDeLaCategorie($idElement, 'Status="Valid"');
						$script = '';
						if($dangers[0]->id != null)
						{
							$script .= '
								<script type="text/javascript">
									$(document).ready(function(){
										$("#needDangerCategory").show();';
										if(count($dangers) > 1)
										{
											$script .= 
										'
										$("#divDangerFormRisque").show();
										$("#boutonAvanceRisque").children("span:first").html("-");';
										}
										else
										{
											$script .= 
										'
										$("#divDangerFormRisque").hide();
										$("#boutonAvanceRisque").children("span:first").html("+");';
										}
							$script .= 
									'})
								</script>';
						}
						else
						{
							$script .= '
								<script type="text/javascript">
									$(document).ready(function(){
										$("#needDangerCategory").hide();
									})
								</script>';
						}
						echo $script . EvaDisplayInput::afficherComboBox($dangers, 'dangerFormRisque', __('Dangers de la cat&eacute;gorie', 'evarisk') . ' : ', 'danger', '', $dangers[0]->id);
						break;
					case 'save':
					case 'update':
						switch($_POST['act'])
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
								$(document).ready(function(){
									$("#message").addClass("updated");
									$("#message").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s a correctement &eacute;t&eacute; %s', 'evarisk') . '</strong></p>', __('de la cat&eacute;gorie de dangers', 'evarisk') . ' "' . stripslashes($_POST['nom_categorie']) . '"', $action)) . '");
									$("#message").show();
									setTimeout(function(){
										$("#message").removeClass("updated");
										$("#message").hide();
									},7500);
									
									$(\'#rightEnlarging\').show();
									$(\'#equilize\').click();
									if("' . $_POST['affichage'] . '" == "affichageTable")
									{
										if($(\'#filAriane :last-child\').is("label"))
											$(\'#filAriane :last-child\').remove();
										$(\'#filAriane :last-child\').after(\'<label>&nbsp;&raquo;&nbsp;&Eacute;dition&nbsp;de&nbsp;' . $_POST['nom_categorie'] . '</label>\');
										$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
											"table": "' . TABLE_CATEGORIE_DANGER . '",
											"id": "' . $_POST['id'] . '",
											"page": $(\'#pagemainPostBoxReference\').val(),
											"idPere": $(\'#identifiantActuellemainPostBox\').val(),
											"act": "edit",
											"partie": "right",
				"menu": $("#menu").val(),
											"affichage": "affichageTable",
											"partition": "tout"
										});
										$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
											"table": "' . TABLE_CATEGORIE_DANGER . '",
											"id": "' . $_POST['id'] . '",
											"page": $(\'#pagemainPostBoxReference\').val(),
											"idPere": $(\'#identifiantActuellemainPostBox\').val(),
											"act": "edit",
											"partie": "left",
				"menu": $("#menu").val(),
											"affichage": "affichageTable",
											"partition": "tout"
										});
									}
									else
									{
										var expanded = new Array();
										$(\'.expanded\').each(function(){expanded.push($(this).attr("id"));});
										$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
											"table": "' . TABLE_CATEGORIE_DANGER . '",
											"act": "edit",
											"id": "' . $_POST['id'] . '",
											"partie": "right",
				"menu": $("#menu").val(),
											"affichage": "affichageListe",
											"expanded": expanded
										});
										$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
											"table": "' . TABLE_CATEGORIE_DANGER . '",
											"act": "edit",
											"id": "' . $_POST['id'] . '",
											"partie": "left",
				"menu": $("#menu").val(),
											"affichage": "affichageListe",
											"expanded": expanded
										});
									}
									$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
									$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
								});
							</script>';
						echo $messageInfo;
						break;
					case 'edit':
					case 'add':
					case 'changementPage':
						echo '
							<script type="text/javascript">
								$(document).ready(function() {
									if($(\'#rightSide-sortables\').html() == "")
										$(\'#rightEnlarging\').hide();
									else
										$(\'#rightEnlarging\').show();
									if($(\'#leftSide-sortables\').html() == "")
										$(\'#leftEnlarging\').hide();
									else
										$(\'#leftEnlarging\').show();
									
									if("' . $_POST['affichage'] . '" == "affichageTable")
									{
										$("#tablemainPostBox tbody tr:nth-child(3)").each(function(){
											for(var i=1; i<=$(this).children("td").length; i++)
											{
												if($(this).children("td:nth-child(" + i + ")").children("img").attr("id") == "photo' . $_POST['table'] . $_POST['id'] . '")
												{												
													$(this).prevAll("tr:not(tr:first-child)").andSelf().children("td:nth-child(" + i + ")").addClass("edited");
													// 3 * i car nomInfo + : + info
													$(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i) + ")").addClass("edited");
													$(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i - 1) + ")").addClass("edited");
													$(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i - 2) + ")").addClass("edited");
												}
											}
										});
									}
									else
									{
										$("#node-' . $_GET['location'] . '-' . $_POST['id'] . '").addClass("edited");
									}
								});
							</script>';
						require_once(EVA_MODULES_PLUGIN_DIR . 'partieDroite.php');
					break;
					case 'delete':
						require_once(EVA_METABOXES_PLUGIN_DIR . 'dangers/categorieDangers/categorieDangersPersistance.php');	
						echo '
							<script type="text/javascript">
								$(document).ready(function() {
									if($(\'#rightSide-sortables\').html() == "")
										$(\'#rightEnlarging\').hide();
									else
										$(\'#rightEnlarging\').show();
									if($(\'#leftSide-sortables\').html() == "")
										$(\'#leftEnlarging\').hide();
									else
										$(\'#leftEnlarging\').show();
									
									if("' . $_POST['affichage'] . '" == "affichageTable")
									{
										$("#tablemainPostBox tbody tr:nth-child(3)").each(function(){
											for(var i=1; i<=$(this).children("td").length; i++)
											{
												if($(this).children("td:nth-child(" + i + ")").children("img").attr("id") == "photo' . $_POST['table'] . $_POST['id'] . '")
												{												
													$(this).prevAll("tr:not(tr:first-child)").andSelf().children("td:nth-child(" + i + ")").addClass("edited");
													// 3 * i car nomInfo + : + info
													$(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i) + ")").addClass("edited");
													$(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i - 1) + ")").addClass("edited");
													$(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i - 2) + ")").addClass("edited");
												}
											}
										});
									}
									else
									{
										$("#node-' . $_GET['location'] . '-' . $_POST['id'] . '").addClass("edited");
									}
								});
							</script>';
						require_once(EVA_MODULES_PLUGIN_DIR . 'partieDroite.php');
					break;

					case 'defaultPictureSelection':
						echo evaPhoto::setMainPhotoAction($_POST['table'], $_POST['idElement'], $_POST['idPhoto']);
					break;
					case 'DeleteDefaultPictureSelection':
						echo evaPhoto::setMainPhotoAction($_POST['table'], $_POST['idElement'], $_POST['idPhoto'], 'no');
					break;
					case 'deletePicture':
						echo evaPhoto::deletePictureAction($_POST['table'], $_POST['idElement'], $_POST['idPicture']);
					break;
					case 'reloadGallery':
						$script = 
						'<script type="text/javascript">
							$(document).ready(function(){
								$(".qq-upload-list").hide();
							});
						</script>';
						echo $script . evaPhoto::outputGallery($_POST['table'], $_POST['idElement']);
					break;
					case 'showGallery':
						echo evaPhoto::getGallery($_POST['table'], $_POST['idElement']);
					break;
				}
				break;
			case TABLE_DANGER:
				switch($_POST['act'])
				{
					case 'save':
					case 'update':
						switch($_POST['act'])
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
								$(document).ready(function(){
									$("#message").addClass("updated");
									$("#message").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s a correctement &eacute;t&eacute; %s', 'evarisk') . '</strong></p>', __('du danger', 'evarisk') . ' "' . stripslashes($_POST['nom_danger']) . '"', $action)) . '");
									$("#message").show();
									setTimeout(function(){
										$("#message").removeClass("updated");
										$("#message").hide();
									},7500);
									
									$(\'#rightEnlarging\').show();
									$(\'#equilize\').click();
									$(\'#filAriane :last-child\').after(\'<label>&nbsp;&raquo;&nbsp;&Eacute;dition&nbsp;de&nbsp;' . $_POST['nom_danger'] . '</label>\');
									
									if("' . $_POST['affichage'] . '" == "affichageTable")
									{
										if($(\'#filAriane :last-child\').is("label"))
											$(\'#filAriane :last-child\').remove();
										$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
											"table": "' . TABLE_DANGER . '",
											"id": "' . $_POST['id'] . '",
											"page": $(\'#pagemainPostBoxReference\').val(),
											"idPere": $(\'#identifiantActuellemainPostBox\').val(),
											"act": "edit",
											"partie": "right",
				"menu": $("#menu").val(),
											"affichage": "affichageTable",
											"partition": "tout"
										});
										$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
											"table": "' . TABLE_DANGER . '",
											"id": "' . $_POST['id'] . '",
											"page": $(\'#pagemainPostBoxReference\').val(),
											"idPere": $(\'#identifiantActuellemainPostBox\').val(),
											"act": "edit",
											"partie": "left",
				"menu": $("#menu").val(),
											"affichage": "affichageTable",
											"partition": "tout"
										});
									}
									else
									{
										var expanded = new Array();
										$(\'.expanded\').each(function(){expanded.push($(this).attr("id"));});
										$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
											"table": "' . TABLE_DANGER . '",
											"act": "edit",
											"id": "' . $_POST['id'] . '",
											"partie": "right",
				"menu": $("#menu").val(),
											"affichage": "affichageListe",
											"expanded": expanded
										});
										$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
											"table": "' . TABLE_DANGER . '",
											"act": "edit",
											"id": "' . $_POST['id'] . '",
											"partie": "left",
				"menu": $("#menu").val(),
											"affichage": "affichageListe",
											"expanded": expanded
										});
									}
									$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
									$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
								});
							</script>';
						echo $messageInfo;
						break;
					case 'edit':
					case 'add':
						echo '
							<script type="text/javascript">
								$(document).ready(function() {
									if($(\'#rightSide-sortables\').html() == "")
										$(\'#rightEnlarging\').hide();
									else
										$(\'#rightEnlarging\').show();
									if($(\'#leftSide-sortables\').html() == "")
										$(\'#leftEnlarging\').hide();
									else
										$(\'#leftEnlarging\').show();
									
									if("' . $_POST['affichage'] . '" == "affichageTable")
									{
										$("#tablemainPostBox tbody tr:nth-child(3)").each(function(){
											for(var i=1; i<=$(this).children("td").length; i++)
											{
												if($(this).children("td:nth-child(" + i + ")").children("img").attr("id") == "photo' . $_POST['table'] . $_POST['id'] . '")
												{												
													$(this).prevAll("tr:not(tr:first-child)").andSelf().children("td:nth-child(" + i + ")").addClass("edited");
													// 3 * i car nomInfo + : + info
													$(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i) + ")").addClass("edited");
													$(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i - 1) + ")").addClass("edited");
													$(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i - 2) + ")").addClass("edited");
												}
											}
										});
									}
									else
									{
										$("#leaf-' . $_POST['id'] . '").addClass("edited");
									}
								});
							</script>';
						require_once(EVA_MODULES_PLUGIN_DIR . 'partieDroite.php');
						break;
					case 'delete':
						require_once(EVA_METABOXES_PLUGIN_DIR . 'dangers/danger/dangerPersistance.php');	
						echo '
							<script type="text/javascript">
								$(document).ready(function() {
									if($(\'#rightSide-sortables\').html() == "")
										$(\'#rightEnlarging\').hide();
									else
										$(\'#rightEnlarging\').show();
									if($(\'#leftSide-sortables\').html() == "")
										$(\'#leftEnlarging\').hide();
									else
										$(\'#leftEnlarging\').show();
									
									if("' . $_POST['affichage'] . '" == "affichageTable")
									{
										$("#tablemainPostBox tbody tr:nth-child(3)").each(function(){
											for(var i=1; i<=$(this).children("td").length; i++)
											{
												if($(this).children("td:nth-child(" + i + ")").children("img").attr("id") == "photo' . $_POST['table'] . $_POST['id'] . '")
												{												
													$(this).prevAll("tr:not(tr:first-child)").andSelf().children("td:nth-child(" + i + ")").addClass("edited");
													// 3 * i car nomInfo + : + info
													$(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i) + ")").addClass("edited");
													$(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i - 1) + ")").addClass("edited");
													$(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i - 2) + ")").addClass("edited");
												}
											}
										});
									}
									else
									{
										$("#node-' . $_GET['location'] . '-' . $_POST['id'] . '").addClass("edited");
									}
								});
							</script>';
						require_once(EVA_MODULES_PLUGIN_DIR . 'partieDroite.php');
					break;
				}
				break;
			case TABLE_UTILISE_EPI:
				{
					global $wpdb;
					$tableElement = eva_tools::IsValid_Variable($_POST['tableElement']);
					$idElement = (int)eva_tools::IsValid_Variable($_POST['idElement']);
					$sql = "DELETE FROM " . TABLE_UTILISE_EPI . " WHERE elementID = " . $idElement . " AND elementTable='" . $tableElement . "';";
					$wpdb->query($sql);
					unset($epiId);
					$insert = '';
					if(count($_POST['epis']) > 0)
						foreach($_POST['epis'] as $epi)
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
							$(document).ready(function() {
								
								$(\'#messageEPI\').show();
								$(\'#messageEPI\').addClass("updated");
								$(\'#messageEPI\').html("' . addslashes($message) . '");
								setTimeout(function(){
									$(\'#messageEPI\').hide();
									$(\'#messageEPI\').removeClass("updated");
									},7500
								);
							});
						</script>';
				}
				break;
			case TABLE_RISQUE:
				switch($_POST['act'])
				{
					case 'save':
						$retourALaLigne = array("\r\n", "\n", "\r");
						$_POST['description'] = str_replace($retourALaLigne, "[retourALaLigne]",$_POST['description']);
						$tableElement = $_POST['tableElement'];
						$idElement = $_POST['idElement'];
						require_once(EVA_METABOXES_PLUGIN_DIR . 'risque/risquePersistance.php');
						require_once(EVA_METABOXES_PLUGIN_DIR . 'risque/risque.php');
						echo getFormulaireCreationRisque($tableElement, $idElement);
					break;
					case 'delete':
						require_once(EVA_METABOXES_PLUGIN_DIR . 'risque/risquePersistance.php');
					break;
					case 'load':
						require_once(EVA_METABOXES_PLUGIN_DIR . 'risque/risque.php');
						echo '<script type="text/javascript">
						$(document).ready(function() {
							$("#ongletAjouterRisque").click();
						});
					</script>' . getFormulaireCreationRisque($_POST['tableElement'], $_POST['idElement'], $_POST['idRisque']);
					break;
					case 'reloadVoirRisque' :
						require_once(EVA_METABOXES_PLUGIN_DIR . 'risque/risque.php');
						$tableElement = $_POST['tableElement'];
						$idElement = $_POST['idElement'];
						echo getVoirRisque($tableElement, $idElement);
					break;
					case 'voirRisqueLigne' :
						require_once(EVA_METABOXES_PLUGIN_DIR . 'documentUnique/documentUnique.php');
						$tableElement = $_POST['tableElement'];
						$idElement = $_POST['idElement'];
						echo documentUnique::bilanRisque($tableElement, $idElement, 'ligne');
					break;
					case 'voirRisqueUnite' :
						require_once(EVA_METABOXES_PLUGIN_DIR . 'documentUnique/documentUnique.php');
						$tableElement = $_POST['tableElement'];
						$idElement = $_POST['idElement'];
						echo documentUnique::bilanRisque($tableElement, $idElement, 'unite');
					break;
					case 'voirDocumentUnique' :
						require_once(EVA_METABOXES_PLUGIN_DIR . 'documentUnique/documentUnique.php');
						$tableElement = $_POST['tableElement'];
						$idElement = $_POST['idElement'];
						echo documentUnique::formulaireGenerationDocumentUnique($tableElement, $idElement);
					break;
					case 'voirHistoriqueDocumentUnique' :
						require_once(EVA_METABOXES_PLUGIN_DIR . 'documentUnique/documentUnique.php');
						$tableElement = $_POST['tableElement'];
						$idElement = $_POST['idElement'];
						echo documentUnique::getDUERList($tableElement, $idElement);
					break;
					case 'saveDocumentUnique' :
						require_once(EVA_METABOXES_PLUGIN_DIR . 'documentUnique/documentUniquePersistance.php');
						require_once(EVA_METABOXES_PLUGIN_DIR . 'documentUnique/documentUnique.php');
						$tableElement = $_POST['tableElement'];
						$idElement = $_POST['idElement'];
						echo documentUnique::formulaireGenerationDocumentUnique($tableElement, $idElement);
					break;
				}
				break;
			case TABLE_METHODE:
				switch($_POST['act'])
				{
					case 'reloadVariables':
						$idMethode = eva_tools::IsValid_Variable($_POST['idMethode']);
						$idRisque = eva_tools::IsValid_Variable($_POST['idRisque']);
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
								$affichage = $affichage . '<script type="text/javascript">
									$(document).ready(function() {';
								{//Gestion du script du slider
									$affichage = $affichage . '
										$("#slider-range-min' . $variable->id . '").slider({
											range: "min",
											value: ' . $valeurInitialVariable . ',
											min:	' . $variable->min . ',
											max:	' . $variable->max . ',
											slide: function(event, ui) {
												$("#var' . $variable->id . 'FormRisque").val(ui.value);
											}
										});
										$("#var' . $variable->id . 'FormRisque").val($("#slider-range-min' . $variable->id . '").slider("value"));';
								}
								{//Gestion du script de la description
									$affichage = $affichage . '
										$("#plusVar' . $variable->id . 'FormRisque").click(function(){
											$("#explicationVariable' . $variable->id . 'FormRisque").toggleClass("hidden");
											if($("#explicationVariable' . $variable->id . 'FormRisque").is(".hidden"))
											{
												$("#plusVar' . $variable->id . 'FormRisque img").attr("src", "' . PICTO_EXPAND . '");
												$("#plusVar' . $variable->id . 'FormRisque img").attr("alt", " + ");
												$("#plusVar' . $variable->id . 'FormRisque img").attr("title", "Voir les explications");
											}
											else
											{
												$("#plusVar' . $variable->id . 'FormRisque img").attr("src", "' . PICTO_COLLAPSE . '");
												$("#plusVar' . $variable->id . 'FormRisque img").attr("alt", " - ");
												$("#plusVar' . $variable->id . 'FormRisque img").attr("title", "Cacher les explications");
											}
										});';
								}
								$affichage = $affichage . '
									});
								</script>';
							}
							{//Affichage de la variable
								$affichage = $affichage . '
									<span id="plusVar' . $variable->id . 'FormRisque" class="plusVariable"><img src="' . PICTO_EXPAND . '" alt=" + " title="Voir les explications" /><label for="var' . $variable->id . 'FormRisque">' . $variable->nom . ' :</label></span>
									<input type="text" class="sliderValue" disabled="disabled" id="var' . $variable->id . 'FormRisque" name="variables[]" />
									<div id="explicationVariable' . $variable->id . 'FormRisque" class="explicationVariable hidden">' . nl2br($variable->annotation) . '</div>
									<div id="slider-range-min' . $variable->id . '" class="slider_variable"></div>';
							}
						}

						/*	START - Get the explanation picture if exist - START	*/
						$methodExplanationPicture = '';
						$defaultPicture = evaPhoto::getMainPhoto(TABLE_METHODE, $idMethode);
						if(($defaultPicture != '') && (is_file(EVA_HOME_DIR . $defaultPicture)))
						{
							$methodExplanationPicture = '<img src="' . EVA_HOME_URL . $defaultPicture . '" alt="" style="width:100%;" />';
						}
						/*	END - Get the explanation picture if exist - END	*/

						echo '<div class="alignleft" style="width:30%;" >' . $affichage . '</div><div class="alignright" style="width:70%;" >' . $methodExplanationPicture . '</div>';
					break;
					case 'defaultPictureSelection':
						echo evaPhoto::setMainPhotoAction($_POST['table'], $_POST['idElement'], $_POST['idPhoto']);
					break;
					case 'DeleteDefaultPictureSelection':
						echo evaPhoto::setMainPhotoAction($_POST['table'], $_POST['idElement'], $_POST['idPhoto'], 'no');
					break;
					case 'deletePicture':
						echo evaPhoto::deletePictureAction($_POST['table'], $_POST['idElement'], $_POST['idPicture']);
					break;
					case 'reloadGallery':
						$script = 
						'<script type="text/javascript">
							$(document).ready(function(){
								$(".qq-upload-list").hide();
							});
						</script>';
						echo $script . evaPhoto::outputGallery($_POST['table'], $_POST['idElement']);
					break;
					case 'showGallery':
						echo evaPhoto::getGallery($_POST['table'], $_POST['idElement']);
					break;
				}
			break;
			case TABLE_GROUPE_QUESTION:
				switch($_POST['act'])
				{
					case 'save':
						switch($_POST['choix'])
						{
							case 'titre':
								if($_POST['nom'] != null and $_POST['nom'] != '')
								{
									$retourALaLigne = array("\r\n", "\n", "\r");
									$_POST['nom'] = str_replace($retourALaLigne, "[retourALaLigne]",$_POST['nom']);
									$_POST['extrait'] = null;
									require_once(EVA_METABOXES_PLUGIN_DIR . 'veilleReglementaire/groupeQuestionPersistance.php');
								}
								break;
						}
						break;
						
					case 'update':
						switch($_POST['choix'])
						{
							case 'titre':
								if($_POST['nom'] != null and $_POST['nom'] != '')
								{ 
									$retourALaLigne = array("\r\n", "\n", "\r");
									$_POST['nom'] = str_replace($retourALaLigne, "[retourALaLigne]",$_POST['nom']);
									$temp = EvaGroupeQuestions::getGroupeQuestions($_POST['id']);
									$temp = Arborescence::getPere($_POST['table'],$temp);
									$_POST['idPere'] = $temp->id;
									require_once(EVA_METABOXES_PLUGIN_DIR . 'veilleReglementaire/groupeQuestionPersistance.php');
								}
								break;
						}
						break;
						
					case 'addExtrait' :
						$groupeQuestion = EvaGroupeQuestions::getGroupeQuestions($_POST['idGroupeQuestion']);
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
						$_POST['extrait'] = str_replace($retourALaLigne, "[retourALaLigne]",$extraitActuel . $_POST['extrait']);
						$_POST['act'] = "addExtrait";
						unset($extraitActuel);
						require_once(EVA_METABOXES_PLUGIN_DIR . 'veilleReglementaire/groupeQuestionPersistance.php');
						break;
					case 'reloadCombo':
						$racine = $wpdb->get_row( 'SELECT * FROM ' . TABLE_GROUPE_QUESTION . ' where nom="' . $_POST['nomRacine'] . '"');
						$valeurDefaut = $racine->nom;
						$selection = $_POST['selection'];
						echo evaDisplayInput::afficherComboBoxArborescente($racine, TABLE_GROUPE_QUESTION, $_POST['idSelect'], $_POST['labelSelect'], $_POST['nameSelect'], $valeurDefaut, $selection);
						break;
					case 'reloadTableArborescente':
						$racine = EvaGroupeQuestions::getGroupeQuestions($_POST['idRacine']);
						$nomRacine = $_POST['nomRacine'];
						$idTable = $_POST['idTable'];
						echo evaDisplayDesign::getTableArborescence($racine, $_POST['table'], $idTable, $nomRacine);
						break;
				}
				break;
			case TABLE_QUESTION:
				switch($_POST['act'])
				{
					case 'edit':
					case 'save':
						$_POST['code'] = '';
						$retourALaLigne = array("\r\n", "\n", "\r");
						$_POST['enonce'] = str_replace($retourALaLigne, "[retourALaLigne]",$_POST['enonce']);
						require_once(EVA_METABOXES_PLUGIN_DIR . 'veilleReglementaire/questionPersistance.php');
					break;
				}
				break;
			case TABLE_PHOTO:
				switch($_POST['act'])
				{
					case 'save':
					case 'edit':
						$retourALaLigne = array("\r\n", "\n", "\r");
						$_POST['description'] = str_replace($retourALaLigne, "[retourALaLigne]",$_POST['description']);
						require_once(EVA_MODULES_PLUGIN_DIR . 'photo/photoPersistance.php');
					break;
				}
				break;
			case TABLE_TACHE:
				switch($_POST['act'])
				{
					case 'edit':
					case 'add':
					case 'changementPage':
						echo '
							<script type="text/javascript">
								$(document).ready(function() {
									if($(\'#rightSide-sortables\').html() == "")
										$(\'#rightEnlarging\').hide();
									else
										$(\'#rightEnlarging\').show();
									if($(\'#leftSide-sortables\').html() == "")
										$(\'#leftEnlarging\').hide();
									else
										$(\'#leftEnlarging\').show();
									
									if("' . $_POST['affichage'] . '" == "affichageTable")
									{
										$("#tablemainPostBox tbody tr:nth-child(3)").each(function(){
											for(var i=1; i<=$(this).children("td").length; i++)
											{
												if($(this).children("td:nth-child(" + i + ")").children("img").attr("id") == "photo' . $_POST['table'] . $_POST['id'] . '")
												{												
													$(this).prevAll("tr:not(tr:first-child)").andSelf().children("td:nth-child(" + i + ")").addClass("edited");
													// 3 * i car nomInfo + : + info
													$(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i) + ")").addClass("edited");
													$(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i - 1) + ")").addClass("edited");
													$(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i - 2) + ")").addClass("edited");
												}
											}
										});
									}
									else
									{
										$("#node-' . $_GET['location'] . '-' . $_POST['id'] . '").addClass("edited");
									}
								});
							</script>';
						require_once(EVA_MODULES_PLUGIN_DIR . 'partieDroite.php');
					break;
					case 'save':
					case 'update':
						global $wpdb;
						switch($_POST['act'])
						{
							case 'save':
								$action = __('sauvegard&eacute;e', 'evarisk');
								break;
							case 'update':
								$action = __('mise &agrave; jour', 'evarisk');
								break;
						}	
						$tache = new EvaTask($_POST['id']);
						$tache->load();
						$tache->setName($_POST['nom_tache']);
						$tache->setDescription($_POST['description']);
						$tache->setIdFrom($_POST['idProvenance']);
						$tache->setTableFrom($_POST['tableProvenance']);
						if($tache->getLeftLimit() == 0)
						{
							$racine = new EvaTask(1);
							$racine->load();
							$tache->setLeftLimit($racine->getRightLimit());
							$tache->setRightLimit(($racine->getRightLimit()) + 1);
							$racine->setRightLimit(($racine->getRightLimit()) + 2);
							$racine->save();
						}
						$tache->save();
						$tacheMere = new EvaTask();
						$tacheMere->convertWpdb(Arborescence::getPere(TABLE_TACHE, $tache->convertToWpdb()));
						if($_POST['idPere'] != $tacheMere->getId())
						{
							$tache->transfert($_POST['idPere']);
						}
						$messageInfo = '<script type="text/javascript">';
						if($tache->getStatus() != 'error')
						{
							$messageInfo = $messageInfo . '
								$(document).ready(function(){
									$("#message").addClass("updated");
									$("#message").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s a correctement &eacute;t&eacute; %s', 'evarisk') . '</strong></p>', __('de la t&acirc;che', 'evarisk') . ' "' . stripslashes($_POST['nom_tache']) . '"', $action)) . '");
									$("#message").show();
									setTimeout(function(){
										$("#message").removeClass("updated");
										$("#message").hide();
									},7500);';
						}
						else
						{
							$messageInfo = $messageInfo . '
								$(document).ready(function(){
									$("#message").addClass("updated");
									$("#message").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s n\'a pas &eacute;t&eacute; %s.', 'evarisk') . '</strong></p>', __('de la t&acirc;che', 'evarisk') . ' "' . stripslashes($_POST['nom_tache']) . '"', $action)) . '");
									$("#message").show();
									setTimeout(function(){
										$("#message").removeClass("updated");
										$("#message").hide();
									},7500);';
						}
						$messageInfo = $messageInfo . '
									$(\'#rightEnlarging\').show();
									$(\'#equilize\').click();
									if("' . $_POST['affichage'] . '" == "affichageTable")
									{
										if($(\'#filAriane :last-child\').is("label"))
											$(\'#filAriane :last-child\').remove();
										$(\'#filAriane :last-child\').after(\'<label>&nbsp;&raquo;&nbsp;&Eacute;dition&nbsp;de&nbsp;' . $_POST['nom_tache'] . '</label>\');
										$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
											"table": "' . TABLE_TACHE . '",
											"id": "' . $tache->getId() . '",
											"page": $(\'#pagemainPostBoxReference\').val(),
											"idPere": $(\'#identifiantActuellemainPostBox\').val(),
											"act": "edit",
											"partie": "right",
				"menu": $("#menu").val(),
											"affichage": "affichageTable",
											"partition": "tout"
										});
										$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
											"table": "' . TABLE_TACHE . '",
											"id": "' . $tache->getId() . '",
											"page": $(\'#pagemainPostBoxReference\').val(),
											"idPere": $(\'#identifiantActuellemainPostBox\').val(),
											"act": "edit",
											"partie": "left",
				"menu": $("#menu").val(),
											"affichage": "affichageTable",
											"partition": "tout"
										});
									}
									else
									{
										var expanded = new Array();
										$(\'.expanded\').each(function(){expanded.push($(this).attr("id"));});
										$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
											"table": "' . TABLE_TACHE . '",
											"act": "edit",
											"id": "' . $tache->getId() . '",
											"partie": "right",
				"menu": $("#menu").val(),
											"affichage": "affichageListe",
											"expanded": expanded
										});
										$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
											"table": "' . TABLE_TACHE . '",
											"act": "edit",
											"id": "' . $tache->getId() . '",
											"partie": "left",
				"menu": $("#menu").val(),
											"affichage": "affichageListe",
											"expanded": expanded
										});
									}
									$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
									$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
								});
							</script>';
						echo $messageInfo;
						break;
				}
				break;
			case TABLE_ACTIVITE:
				switch($_POST['act'])
				{
					case 'edit':
					case 'add':
					case 'changementPage':
						echo '
							<script type="text/javascript">
								$(document).ready(function() {
									if($(\'#rightSide-sortables\').html() == "")
										$(\'#rightEnlarging\').hide();
									else
										$(\'#rightEnlarging\').show();
									if($(\'#leftSide-sortables\').html() == "")
										$(\'#leftEnlarging\').hide();
									else
										$(\'#leftEnlarging\').show();
									
									if("' . $_POST['affichage'] . '" == "affichageTable")
									{
										$("#tablemainPostBox tbody tr:nth-child(3)").each(function(){
											for(var i=1; i<=$(this).children("td").length; i++)
											{
												if($(this).children("td:nth-child(" + i + ")").children("img").attr("id") == "photo' . $_POST['table'] . $_POST['id'] . '")
												{												
													$(this).prevAll("tr:not(tr:first-child)").andSelf().children("td:nth-child(" + i + ")").addClass("edited");
													// 3 * i car nomInfo + : + info
													$(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i) + ")").addClass("edited");
													$(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i - 1) + ")").addClass("edited");
													$(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i - 2) + ")").addClass("edited");
												}
											}
										});
									}
									else
									{
										$("#leaf-' . $_POST['id'] . '").addClass("edited");
									}
								});
							</script>';
						require_once(EVA_MODULES_PLUGIN_DIR . 'partieDroite.php');
					break;
					case 'save':
					case 'update':
						global $wpdb;
						switch($_POST['act'])
						{
							case 'save':
								$action = __('sauvegard&eacute;e', 'evarisk');
								break;
							case 'update':
								$action = __('mise &agrave; jour', 'evarisk');
								break;
						}	
						$activite = new EvaActivity($_POST['id']);
						$activite->load();
						$activite->setName($_POST['nom_activite']);
						$activite->setDescription($_POST['description']);
						$activite->setRelatedTaskId($_POST['idPere']);
						$activite->setStartDate($_POST['date_debut']);
						$activite->setFinishDate($_POST['date_fin']);
						$activite->setProgression($_POST['avancement']);
						$activite->save();
						$messageInfo = '<script type="text/javascript">';
						if($activite->getStatus() != 'error')
						{
							$messageInfo = $messageInfo . '
								$(document).ready(function(){
									$("#message").addClass("updated");
									$("#message").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s a correctement &eacute;t&eacute; %s', 'evarisk') . '</strong></p>', __('de l\'action', 'evarisk') . ' "' . stripslashes($_POST['nom_activite']) . '"', $action)) . '");
									$("#message").show();
									setTimeout(function(){
										$("#message").removeClass("updated");
										$("#message").hide();
									},7500);';
						}
						else
						{
							$messageInfo = $messageInfo . '
								$(document).ready(function(){
									$("#message").addClass("updated");
									$("#message").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s n\'a pas &eacute;t&eacute; %s.', 'evarisk') . '</strong></p>', __('de l\'action', 'evarisk') . ' "' . stripslashes($_POST['nom_activite']) . '"', $action)) . '");
									$("#message").show();
									setTimeout(function(){
										$("#message").removeClass("updated");
										$("#message").hide();
									},7500);';
						}
						$messageInfo = $messageInfo . '
									$(\'#rightEnlarging\').show();
									$(\'#equilize\').click();
									if("' . $_POST['affichage'] . '" == "affichageTable")
									{
										if($(\'#filAriane :last-child\').is("label"))
											$(\'#filAriane :last-child\').remove();
										$(\'#filAriane :last-child\').after(\'<label>&nbsp;&raquo;&nbsp;&Eacute;dition&nbsp;de&nbsp;' . $_POST['nom_activite'] . '</label>\');
										$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
											"table": "' . TABLE_ACTIVITE . '",
											"id": "' . $activite->getId() . '",
											"page": $(\'#pagemainPostBoxReference\').val(),
											"idPere": $(\'#identifiantActuellemainPostBox\').val(),
											"act": "edit",
											"partie": "right",
				"menu": $("#menu").val(),
											"affichage": "affichageTable",
											"partition": "tout"
										});
										$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
											"table": "' . TABLE_ACTIVITE . '",
											"id": "' . $activite->getId() . '",
											"page": $(\'#pagemainPostBoxReference\').val(),
											"idPere": $(\'#identifiantActuellemainPostBox\').val(),
											"act": "edit",
											"partie": "left",
				"menu": $("#menu").val(),
											"affichage": "affichageTable",
											"partition": "tout"
										});
									}
									else
									{
										var expanded = new Array();
										$(\'.expanded\').each(function(){expanded.push($(this).attr("id"));});
										$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
											"table": "' . TABLE_ACTIVITE . '",
											"act": "edit",
											"id": "' . $activite->getId() . '",
											"partie": "right",
				"menu": $("#menu").val(),
											"affichage": "affichageListe",
											"expanded": expanded
										});
										$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
											"table": "' . TABLE_ACTIVITE . '",
											"act": "edit",
											"id": "' . $activite->getId() . '",
											"partie": "left",
				"menu": $("#menu").val(),
											"affichage": "affichageListe",
											"expanded": expanded
										});
									}
									$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
									$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
								});
							</script>';
						echo $messageInfo;
						break;
					case 'actualiserAvancement':
						$activites = $_POST['activites'];
						$status = 'Valid';
						foreach($activites as $idActivite => $avancementActivite)
						{
							$activite = new EvaActivity($idActivite);
							$activite->load();
							$activite->setProgression($avancementActivite);
							$activite->save();
							if($activite->getStatus() == 'error')
							{
								$status = 'error';
							}
						}
						
						$messageInfo = '<script type="text/javascript">
							$(document).ready(function(){
								$("#message' . $_POST['tableProvenance'] . '").addClass("updated");';
						if($status != 'error')
						{
							$messageInfo = $messageInfo . '
								$("#message' . $_POST['tableProvenance'] . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Les modifications ont correctement &eacute;t&eacute enregistr&eacute;es', 'evarisk') . '</strong></p>') . '");';
						}
						else
						{
							$messageInfo = $messageInfo . '
								$("#message' . $_POST['tableProvenance'] . '").html("' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Les modifications n\'ont pas toutes &eacute;t&eacute correctement enregistr&eacute;es', 'evarisk') . '</strong></p>"') . '");';
						}
						$messageInfo = $messageInfo . '
									$("#message' . $_POST['tableProvenance'] . '").show();
									setTimeout(function(){
										$("#message' . $_POST['tableProvenance'] . '").removeClass("updated");
										$("#message' . $_POST['tableProvenance'] . '").hide();
									},7500);
									$("#divSuiviAction' . $_POST['tableProvenance'] . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "nom":"suiviAction",	"tableProvenance":"' . $_POST['tableProvenance'] . '", "idProvenance": "' . $_POST['idProvenance'] . '"});
									$("#divSuiviAction' . TABLE_RISQUE . '").html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
								});
							</script>';
						echo $messageInfo;
						break;
				}
				break;
			case TABLE_LIAISON_USER_GROUPS:
				switch($_POST['act'])
				{
					case "save":
						$status = evaUserGroup::saveBind($_POST['idGroupe'], $_POST['idElement'], $_POST['tableElement']);

						switch($_POST['tableElement'])
						{
							case TABLE_GROUPEMENT:
								$complement = "au groupement";
								break;
							case TABLE_UNITE_TRAVAIL:
								$complement = "&agrave; l'unit&eacute;";
								break;
						}
						$messageInfo = '<script type="text/javascript">
							$(document).ready(function(){
								$("#message' . TABLE_LIAISON_USER_GROUPS . '").addClass("updated");';
						if($status['result'] != 'error')
						{
							$messageInfo = $messageInfo . '
									$("#message' . TABLE_LIAISON_USER_GROUPS . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le groupe d\'utilisateurs a &eacute;t&eacute; correctement %s %s.', 'evarisk') . '</strong></p>', __('affect&eacute;', 'evarisk'), __($complement, 'evarisk'))) . '");';
						}
						else
						{
							$messageInfo = $messageInfo . '
									$("#message' . TABLE_LIAISON_USER_GROUPS . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le groupe d\'utilisateurs n\'a pas pu &ecirc;tre %s %s.', 'evarisk') . '</strong></p>', __('affect&eacute;', 'evarisk'), __($complement, 'evarisk'))) . '");';
						}
						$messageInfo = $messageInfo . '
									$("#message' . TABLE_LIAISON_USER_GROUPS . '").show();
									setTimeout(function(){
										$("#message' . TABLE_LIAISON_USER_GROUPS . '").removeClass("updated");
										$("#message' . TABLE_LIAISON_USER_GROUPS . '").hide();
									},7500);
									$("#ongletVoirLesRisques").click();
								});
							</script>';
						echo $messageInfo . evaUserGroup::boxGroupesUtilisateursEvaluation($_POST['tableElement'], $_POST['idElement']);
					break;
					case "delete":
						$status = evaUserGroup::deleteBind($_POST['idGroupe'], $_POST['idElement'], $_POST['tableElement']);

						switch($_POST['tableElement'])
						{
							case TABLE_GROUPEMENT:
								$complement = "du groupement";
								break;
							case TABLE_UNITE_TRAVAIL:
								$complement = "du l'unit&eacute;";
								break;
						}
						$messageInfo = '<script type="text/javascript">
							$(document).ready(function(){
								$("#message' . TABLE_LIAISON_USER_GROUPS . '").addClass("updated");';
						if($status['result'] != 'error')
						{
							$messageInfo = $messageInfo . '
									$("#message' . TABLE_LIAISON_USER_GROUPS . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le groupe d\'utilisateurs a &eacute;t&eacute; correctement %s %s.', 'evarisk') . '</strong></p>', __('d&eacute;saffect&eacute;', 'evarisk'), __($complement, 'evarisk'))) . '");';
						}
						else
						{
							$messageInfo = $messageInfo . '
									$("#message' . TABLE_LIAISON_USER_GROUPS . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le groupe d\'utilisateurs n\'a pas pu &ecirc;tre %s %s.', 'evarisk') . '</strong></p>', __('d&eacute;saffect&eacute;', 'evarisk'), __($complement, 'evarisk'))) . '");';
						}
						$messageInfo = $messageInfo . '
									$("#message' . TABLE_LIAISON_USER_GROUPS . '").show();
									setTimeout(function(){
										$("#message' . TABLE_LIAISON_USER_GROUPS . '").removeClass("updated");
										$("#message' . TABLE_LIAISON_USER_GROUPS . '").hide();
									},7500);
									$("#ongletVoirLesRisques").click();
								});
							</script>';
						echo $messageInfo . evaUserGroup::boxGroupesUtilisateursEvaluation($_POST['tableElement'], $_POST['idElement']);
					break;
				}
				break;
			case TABLE_LIAISON_USER_EVALUATION:
				switch($_POST['act'])
				{
					case 'save':
						$status = evaUser::saveUserEvaluationBind($_POST['idsUsers'], $_POST['idElement'], $_POST['tableElement']);

						switch($_POST['tableElement'])
						{
							case TABLE_GROUPEMENT:
								$complement = "au groupement";
								break;
							case TABLE_UNITE_TRAVAIL:
								$complement = "&agrave; l'unit&eacute;";
								break;
						}
						$messageInfo = '<script type="text/javascript">
							$(document).ready(function(){
								$("#message' . TABLE_LIAISON_USER_EVALUATION . '").addClass("updated");';
						if($status['result'] != 'error')
						{
							$messageInfo = $messageInfo . '
									$("#message' . TABLE_LIAISON_USER_EVALUATION . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('L\'utilisateur a &eacute;t&eacute; correctement %s %s.', 'evarisk') . '</strong></p>', __('affect&eacute;', 'evarisk'), __($complement, 'evarisk'))) . '");';
						}
						else
						{
							$messageInfo = $messageInfo . '
									$("#message' . TABLE_LIAISON_USER_EVALUATION . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('L\'utilisateurs n\'a pas pu &ecirc;tre %s %s.', 'evarisk') . '</strong></p>', __('affect&eacute;', 'evarisk'), __($complement, 'evarisk'))) . '");';
						}
						$messageInfo = $messageInfo . '
									$("#message' . TABLE_LIAISON_USER_EVALUATION . '").show();
									setTimeout(function(){
										$("#message' . TABLE_LIAISON_USER_EVALUATION . '").removeClass("updated");
										$("#message' . TABLE_LIAISON_USER_EVALUATION . '").hide();
									},7500);
								});
							</script>';

						echo $messageInfo . evaUser::boxUtilisateursEvalues($_POST);
					break;
					case 'delete':
						$status = evaUser::deleteUserEvaluationBind($_POST['idsUsers'], $_POST['idElement'], $_POST['tableElement']);

						switch($_POST['tableElement'])
						{
							case TABLE_GROUPEMENT:
								$complement = "du groupement";
								break;
							case TABLE_UNITE_TRAVAIL:
								$complement = "de l'unit&eacute;";
								break;
						}
						$messageInfo = '<script type="text/javascript">
							$(document).ready(function(){
								$("#message' . TABLE_LIAISON_USER_EVALUATION . '").addClass("updated");';
						if($status['result'] != 'error')
						{
							$messageInfo = $messageInfo . '
									$("#message' . TABLE_LIAISON_USER_EVALUATION . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Les utilisateurs ont &eacute;t&eacute; correctement %s %s.', 'evarisk') . '</strong></p>', __('supprim&eacute;s', 'evarisk'), __($complement, 'evarisk'))) . '");';
						}
						else
						{
							$messageInfo = $messageInfo . '
									$("#message' . TABLE_LIAISON_USER_EVALUATION . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Les utilisateurs n\'ont pas pu &ecirc;tre %s %s.', 'evarisk') . '</strong></p>', __('supprim&eacute;s', 'evarisk'), __($complement, 'evarisk'))) . '");';
						}
						$messageInfo = $messageInfo . '
									$("#message' . TABLE_LIAISON_USER_EVALUATION . '").show();
									setTimeout(function(){
										$("#message' . TABLE_LIAISON_USER_EVALUATION . '").removeClass("updated");
										$("#message' . TABLE_LIAISON_USER_EVALUATION . '").hide();
									},7500);
								});
							</script>';

						echo $messageInfo . evaUser::boxUtilisateursEvalues($_POST);
					break;
				}
				break;
			case TABLE_EVA_EVALUATOR_GROUP_BIND:
				switch($_POST['act'])
				{
					case "save":
						$status = evaUserEvaluatorGroup::saveBind($_POST['idGroupe'], $_POST['idElement'], $_POST['tableElement']);

						switch($_POST['tableElement'])
						{
							case TABLE_GROUPEMENT:
								$complement = "au groupement";
								break;
							case TABLE_UNITE_TRAVAIL:
								$complement = "&agrave; l'unit&eacute;";
								break;
						}
						$messageInfo = '<script type="text/javascript">
							$(document).ready(function(){
								$("#message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '").addClass("updated");';
						if($status['result'] != 'error')
						{
							$messageInfo = $messageInfo . '
									$("#message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le groupe d\'&eacute;valuateurs a &eacute;t&eacute; correctement %s %s.', 'evarisk') . '</strong></p>', __('affect&eacute;', 'evarisk'), __($complement, 'evarisk'))) . '");';
						}
						else
						{
							$messageInfo = $messageInfo . '
									$("#message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le groupe d\'&eacute;valuateurs n\'a pas pu &ecirc;tre %s %s.', 'evarisk') . '</strong></p>', __('affect&eacute;', 'evarisk'), __($complement, 'evarisk'))) . '");';
						}
						$messageInfo = $messageInfo . '
									$("#message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '").show();
									setTimeout(function(){
										$("#message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '").removeClass("updated");
										$("#message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '").hide();
									},7500);
									$("#ongletVoirLesRisques").click();
								});
							</script>';
						echo $messageInfo . evaUserEvaluatorGroup::boxGroupesUtilisateursEvaluation($_POST['tableElement'], $_POST['idElement']);
					break;
					case "delete":
						$status = evaUserEvaluatorGroup::deleteBind($_POST['idBind'], $_POST['idGroupe'], $_POST['idElement'], $_POST['tableElement']);

						switch($_POST['tableElement'])
						{
							case TABLE_GROUPEMENT:
								$complement = "du groupement";
								break;
							case TABLE_UNITE_TRAVAIL:
								$complement = "du l'unit&eacute;";
								break;
						}
						$messageInfo = '<script type="text/javascript">
							$(document).ready(function(){
								$("#message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '").addClass("updated");';
						if($status['result'] != 'error')
						{
							$messageInfo = $messageInfo . '
									$("#message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le groupe d\'&eacute;valuateurs a &eacute;t&eacute; correctement %s %s.', 'evarisk') . '</strong></p>', __('d&eacute;saffect&eacute;', 'evarisk'), __($complement, 'evarisk'))) . '");';
						}
						else
						{
							$messageInfo = $messageInfo . '
									$("#message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le groupe d\'&eacute;valuateurs n\'a pas pu &ecirc;tre %s %s.', 'evarisk') . '</strong></p>', __('d&eacute;saffect&eacute;', 'evarisk'), __($complement, 'evarisk'))) . '");';
						}
						$messageInfo = $messageInfo . '
									$("#message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '").show();
									setTimeout(function(){
										$("#message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '").removeClass("updated");
										$("#message' . TABLE_EVA_EVALUATOR_GROUP_BIND . '").hide();
									},7500);
									$("#ongletVoirLesRisques").click();
								});
							</script>';
						echo $messageInfo . evaUserEvaluatorGroup::boxGroupesUtilisateursEvaluation($_POST['tableElement'], $_POST['idElement']);
					break;
				}
				break;
		}
	if(isset($_POST['nom']))
		switch($_POST['nom'])
		{
			case "installerEvarisk":
				{
					$insertions = $_POST;
					unset($insertions['EPI']);
					foreach($_POST["EPI"] as $epi)
					{
						$insertions['EPI'][$epi[1]] = $epi[0];
					}
					foreach($_POST["methodes"] as $methode)
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
					for($i=$_POST['min']; $i<=$_POST['max']; $i++)
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
					$tableElement = eva_tools::IsValid_Variable($_POST['tableElement']);
					$idElement = eva_tools::IsValid_Variable($_POST['idElement']);
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
						$(document).ready(function(){
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
					$summary = ($_POST['act'] == 'summary') ? true : false ;
					echo '<div id="plotLocation"></div><div id="interractionVeille">' . getFormulaireReponse($_POST['idElement'], $_POST['tableElement'], $summary) . '</div>';
				}
				break;
			case "veilleClicValidation":
				{
					$questionID = eva_tools::IsValid_Variable($_POST['idQuestion']);
					$tableElement = eva_tools::IsValid_Variable($_POST['tableElement']);
					$idElement = eva_tools::IsValid_Variable($_POST['idElement']);
					$reponse = eva_tools::IsValid_Variable($_POST['reponse']);
					$valeurReponse = eva_tools::IsValid_Variable($_POST['valeur']);
					$observationReponse = eva_tools::IsValid_Variable($_POST['observation']);
					$soumission = eva_tools::IsValid_Variable($_POST['soumission']);
					$limiteValidite = eva_tools::IsValid_Variable($_POST['limiteValidite']);
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
									setTimeout(function(){$(\'#observationTropLongue' . $questionID . '\').html("")},5000);
								</script>';
						}
						else
						{
							$messageInfo = 
								'<span id="message" class="updated fade below-h2">
									<p><strong><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="noresponse" style="vertical-align:middle;" />&nbsp;La r&eacute;ponse n\'a pas pu &ecirc;tre enregistr&eacute;e</strong></p>
								</span>
								<script type="text/javascript" >
									setTimeout(function(){$(\'#observationTropLongue' . $questionID . '\').html("")},5000);
								</script>';
						}
					// }
					// else
					// {
						// if($save == 'ok')
						// {
							// $_POST['statusVeille'] = $_POST['statusVeille'] . true;
						// }
						// else
						// {
							// $_POST['statusVeille'] = false;
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
					switch($_POST['tableProvenance'])
					{
						case TABLE_RISQUE :
							$risque = Risque::getRisque($_POST['idProvenance']);

							
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
								$idTable = 'tableDemandeAction' . $_POST['tableProvenance'] . $_POST['idProvenance'];
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
									$(document).ready(function(){
										$("#' . $idTable . ' tfoot").remove();
									});
								</script>';
								
								echo EvaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script);
							}
							break;
						default :
							echo 'Pensez &agrave; <b>ajouter</b> le <b>cas ' . $_POST['tableProvenance'] . '</b> dans le <b>switch</b> ligne <b>' . __LINE__ . '</b> du fichier "' . dirname(__FILE__) . '\<b>' . basename(__FILE__) . '</b>"<br />';
							break;
					}
					echo '<br />';
					require_once(EVA_METABOXES_PLUGIN_DIR . 'actionsCorrectives/activite/activite-new.php');
					getActivityGeneralInformationPostBoxBody(array('idElement' => null, 'idPere' => 1, 'affichage' => null, 'idsFilAriane' => null));
					echo '<script type="text/javascript">
						$(document).ready(function(){
							$("#idProvenance_activite").val("' . $_POST['idProvenance'] . '");
							$("#tableProvenance_activite").val("' . $_POST['tableProvenance'] . '");
							$("#save_activite").unbind("click");
							$("#save_activite").click(function(){
								if($(\'#nom_activite\').is(".form-input-tip"))
								{
									$(\'#nom_activite\').val("");
									$(\'#nom_activite\').removeClass(\'form-input-tip\');
								}
								valeurActuelle = $("#nom_activite").val();
								if(valeurActuelle == "")
								{
									alert(convertAccentToJS("' . __("Vous n\'avez pas donne de nom a l'action", 'evarisk') . '"));
								}
								else
								{						
									$(\'#ajax-response\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {\'post\': \'true\', 
										\'nom\': \'addAction\',
										\'nom_activite\': $(\'#nom_activite\').val(),
										\'idPere\': 1,
										\'description\': $(\'#description_activite\').val(),
										\'date_debut\': $("#date_debut_activite").val(),
										\'date_fin\': $("#date_fin_activite").val(),
										\'cout\': $("#cout_activite").val(),
										\'avancement\': $("#avancement_activite").val(),
										\'idProvenance\': $(\'#idProvenance_activite\').val(),
										\'tableProvenance\': $(\'#tableProvenance_activite\').val()
									});
								}
							});
						})
					</script>';
				}
				break;
			case "addAction" :
				{
					$tache = new EvaTask();
					$tache->setName($_POST['nom_activite']);
					$tache->setDescription($_POST['description']);
					$tache->setCost($_POST['cout']);
					$tache->setIdFrom($_POST['idProvenance']);
					$tache->setTableFrom($_POST['tableProvenance']);
					$racine = new EvaTask(1);
					$racine->load();
					$tache->setLeftLimit($racine->getRightLimit());
					$tache->setRightLimit(($racine->getRightLimit()) + 1);
					$racine->setRightLimit(($racine->getRightLimit()) + 2);
					$racine->save();
					$tache->save();
					$activite = new EvaActivity();
					$activite->setName($_POST['nom_activite']);
					$activite->setDescription($_POST['description']);
					$activite->setStartDate($_POST['date_debut']);
					$activite->setFinishDate($_POST['date_fin']);
					$activite->setCost($_POST['cout']);
					$activite->setProgression($_POST['avancement']);
					$activite->setRelatedTaskId($tache->getId());
					$activite->save();
					
					$messageInfo = '<script type="text/javascript">
							$(document).ready(function(){
								$("#message' . $_POST['tableProvenance'] . '").addClass("updated");';
					if($tache->getStatus() != 'error' AND $activite->getStatus() != 'error')
					{
						$messageInfo = $messageInfo . '
								$("#message' . $_POST['tableProvenance'] . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s a correctement &eacute;t&eacute; %s', 'evarisk') . '</strong></p>', __('de l\'action corrective', 'evarisk') . ' "' . stripslashes($_POST['nom_activite']) . '"', __('sauvegard&eacute;e', 'evarisk'))) . '");';
					}
					else
					{
						$messageInfo = $messageInfo . '
								$("#message' . $_POST['tableProvenance'] . '").html("' . addslashes(sprintf('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="no-response" style="vertical-align:middle;" />&nbsp;<strong>' . __('La fiche %s n\'a pas &eacute;t&eacute; %s.', 'evarisk') . '</strong></p>', __('de l\'action corrective', 'evarisk') . ' "' . stripslashes($_POST['nom_activite']) . '"', __('sauvegard&eacute;e', 'evarisk'))) . '");';
					}
					$messageInfo = $messageInfo . '
								$("#message' . $_POST['tableProvenance'] . '").show();
								setTimeout(function(){
									$("#message' . $_POST['tableProvenance'] . '").removeClass("updated");
									$("#message' . $_POST['tableProvenance'] . '").hide();
								},7500);
								$("#ongletVoirLesRisques").click();
							});
						</script>';
					echo $messageInfo;
				}
				break;
			case "suiviAction" :
				{
					switch($_POST['tableProvenance'])
					{
						case TABLE_RISQUE :
							$risque = Risque::getRisque($_POST['idProvenance']);
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
								$idTable = 'tableDemandeAction' . $_POST['tableProvenance'] . $_POST['idProvenance'];
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
									$(document).ready(function(){
										$("#' . $idTable . ' tfoot").remove();
									});
								</script>';
								
								echo EvaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script);
							}
							break;
						default :
							echo 'Pensez &agrave; <b>ajouter</b> le <b>cas ' . $_POST['tableProvenance'] . '</b> dans le <b>switch</b> ligne <b>' . __LINE__ . '</b> du fichier "' . dirname(__FILE__) . '\<b>' . basename(__FILE__) . '</b>"<br />';
							break;
					}
					echo '<br />';
					//On récupère les actions relatives à l'élément de provenance.
					$taches = new EvaTaskTable();
					$tacheLike = new EvaTask();
					$tacheLike->setIdFrom($_POST['idProvenance']);
					$tacheLike->setTableFrom($_POST['tableProvenance']);
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
									$(document).ready(function(){										
										$("#' . $idDiv . '-affichage").gantt({
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
									$(document).ready(function(){										
										$("#' . $idDiv . '-affichage").html("&nbsp;&nbsp;&nbsp;&nbsp;' . __('Action non d&eacute;coup&eacute;e', 'evarisk') . '");
									});
								</script>';
							}
							echo '<script type="text/javascript">
								$(document).ready(function(){			
									$("#' . $idDiv . '-choix").toggle(
										function()
										{
											$("#' . $idDiv . '-affichage").show();
											$(this).children("span:first").html("-");
										},
										function()
										{
											$("#' . $idDiv . '-affichage").hide();
											$(this).children("span:first").html("+");
										}
									);
								});
							</script>';
							{//Bouton enregistrer
								$idBoutonEnregistrer = $idDiv . '-enregistrer';
								$scriptEnregistrement = '<script type="text/javascript">
									$(document).ready(function() {	
										var boutonEnregistrer = $(\'#' . $idBoutonEnregistrer . '\').parent().html();
										$(\'#' . $idBoutonEnregistrer . '\').parent().html("");
										$(\'#' . $idDiv . '-affichage\').append(boutonEnregistrer);
										$(\'#' . $idBoutonEnregistrer . '\').click(function() {
											var idDiv = "' . $idDiv . '";
											var activites = new Array();
											$("#' . $idDiv . '-affichage .ui-gantt-table td:nth-child(3) input").each(function(){
												if($(this).attr("id") != "")
												{
													activites[$(this).attr("id").substr(idDiv.length + 1)] = $(this).val();
												}
											});
											$("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
												"post":"true", 
												"table":"' . TABLE_ACTIVITE . '", 
												"act":"actualiserAvancement", 
												"activites":activites,
												"tableProvenance":"' . $_POST['tableProvenance'] . '",
												"idProvenance": "' . $_POST['idProvenance'] . '"
											});
											return false;
										});
									});
									</script>';
								echo EvaDisplayInput::afficherInput('button', $idBoutonEnregistrer, 'Enregistrer', null, '', $idDiv . 'save', false, false, '', 'button-primary alignright', '', '', $scriptEnregistrement);
							}
							echo '<script type="text/javascript">
									$(document).ready(function(){		
										//Transformation du texte des cases avancement en input
										$("#' . $idDiv . '-affichage .ui-gantt-table td:nth-child(3)").each(function(){
											$(this).html("<input type=\"text\" value=\"" + $(this).html() + "\" maxlength=3 style=\"width:3em;\"/>%");
											if($(this).parent("tr").children("td:first").html().match("^T")=="T")
											{
												$(this).children("input").attr("disabled","disabled");
											}
											else
											{
												$(this).children("input").attr("id","' . $idDiv . '-" + $(this).parent("tr").children("td:first").html().substr(1, 1));
											}
										});
									});
								</script>';
							//ajout de l'indentation
							foreach($niveaux as $key => $niveau)
							{
								echo '<script type="text/javascript">
										$(document).ready(function(){		
											$("#' . $idDiv . '-affichage .ui-gantt-table tr:nth-child(' . ($key + 1) . ') td:nth-child(2)").css("padding-left", "' . ($niveau * LARGEUR_INDENTATION_GANTT_EN_EM) . 'em");
										});
									</script>';
							}
						}
					}
					else
					{
						switch($_POST['tableProvenance'])
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
	//Chargement des meta-boxes
	if(isset($_POST['nomMetaBox']))
		switch($_POST['nomMetaBox'])
		{
			case 'Geolocalisation':
				if($_POST['markers'] != "")
				{
					foreach($_POST['markers'] as $markerImplode)
					{
						$markerNArray = explode('"; "', stripcslashes($markerImplode));
						for($i=0; $i<count($_POST["keys"]); $i++)
						{
							$markerAArray[$_POST["keys"][$i]] = $markerNArray[$i];
						}
						$markers[] = $markerAArray;
					}
				}
				echo EvaGoogleMaps::getGoogleMap($_POST['idGoogleMapsDiv'], $markers);
				break;
		}
}
/*
 * Paramètres passés en GET
 */
else
{
	switch($_GET['nom'])
	{
		case TABLE_GROUPEMENT:
			switch($_GET['act'])
			{
				case 'transfert':
					$fils = $_GET['idElementSrc'];
					$pere = $_GET['idElementDest'];
					$idPere = str_replace('node-' . $_GET['location'] . '-','', $pere);
					$idFils = (string)((int) str_replace('node-' . $_GET['location'] . '-','', $fils));
					$groupementPere = EvaGroupement::getGroupement($idPere);
					if($idFils == str_replace('node-' . $_GET['location'] . '-','', $fils)) //Le fils est un groupement
					{
						$groupement = EvaGroupement::getGroupement($idFils);
						$pereActu = Arborescence::getPere($_GET['nom'], $groupement);
						if($pereActu->id != $idPere)
						{
							$_POST['act'] = 'update';
							$_POST['id'] = $groupement->id;
							$_POST['nom_groupement'] = $groupement->nom;
							$_POST['description'] = $groupement->description;
							$_POST['telephone'] = $groupement->telephoneGroupement;
							$_POST['effectif'] = $groupement->effectif;
							
							$address = new EvaAddress($groupement->id_adresse);
							
							$address->load();
							$contenuInputLigne1 = $address->getFirstLine();
							$contenuInputLigne2 = $address->getSecondLine();
							$contenuInputCodePostal = $address->getPostalCode();
							$contenuInputVille = $address->getCity();
							
							$_POST['adresse_ligne_1'] = $address->getFirstLine();
							$_POST['adresse_ligne_2'] = $address->getSecondLine();
							$_POST['code_postal'] = $address->getPostalCode();
							$_POST['ville'] = $address->getCity();
							$_POST['longitude'] = $address->getLongitude();
							$_POST['latitude'] = $address->getLatitude();
							$_POST['groupementPere'] = $idPere;
							require_once(EVA_METABOXES_PLUGIN_DIR . 'evaluationDesRisques/groupement/groupementPersistance.php');
						}
					}
					else //Le fils est une unité
					{
						$idFils = str_replace('leaf-','', $fils);
						uniteDeTravail::transfertUnit($idFils, $idPere);
					}
					break;
				case 'none':
					switch($_GET['affichage'])
					{
						case "affichageTable":
						case "affichageListe":
							$_POST['affichage'] = $_GET['affichage'];
							require_once(EVA_MODULES_PLUGIN_DIR . 'evaluationDesRisques/partieGaucheEvaluationDesRisques.php');
							echo $script . $partieGauche;
							break;
					}
					break;
				case 'reloadScriptDD':
					echo EvaDisplayDesign::getScriptDragAndDrop($_GET['idTable'], $_GET['nom'], $_GET['divDeChargement']);
					break;
			}
			break;
		case TABLE_TACHE:
			switch($_GET['act'])
			{
				case 'transfert':
					$fils = $_GET['idElementSrc'];
					$pere = $_GET['idElementDest'];
					$idPere = str_replace('node-' . $_GET['location'] . '-','', $pere);
					$idFils = (string)((int) str_replace('node-' . $_GET['location'] . '-','', $fils));
					if($idFils == str_replace('node-' . $_GET['location'] . '-','', $fils)) //Le fils est une tâche
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
					}
					break;
				case 'none':
					switch($_GET['affichage'])
					{
						case "affichageTable":
						case "affichageListe":
							$_POST['affichage'] = $_GET['affichage'];
							require_once(EVA_MODULES_PLUGIN_DIR . 'evaluationDesRisques/partieGaucheEvaluationDesRisques.php');
							echo $script . $partieGauche;
							break;
					}
					break;
				case 'reloadScriptDD':
					echo EvaDisplayDesign::getScriptDragAndDrop($_GET['idTable'], $_GET['nom'], $_GET['divDeChargement']);
					break;
			}
			break;
		case TABLE_GROUPE_QUESTION:
			switch($_GET['act'])
			{
				case 'transfert':
					$fils = $_GET['idElementSrc'];
					$pere = $_GET['idElementDest'];
					$pereOriginel = $_GET['idElementOrigine'];
					$idPere = str_replace('node-' . $_GET['location'] . '-','', $pere);
					$idFils = (string)((int) str_replace('node-' . $_GET['location'] . '-','', $fils));
					$idPereOriginel = str_replace('node-' . $_GET['location'] . '-','', $pereOriginel);
					if($idFils == str_replace('node-' . $_GET['location'] . '-','', $fils))
					//Le fils est un groupe de questions
					{
						$idFils = str_replace('node-' . $_GET['location'] . '-','', $fils);
						$groupeQuestion = evaGroupeQuestions::getGroupeQuestions($idFils);
						$pereActu = Arborescence::getPere($_GET['nom'], $groupeQuestion);
						if($pereActu->id != $idPere)
						{
							$_POST['act'] = 'update';
							$_POST['id'] = $groupeQuestion->id;
							$_POST['nom'] = $groupeQuestion->nom;
							$_POST['code'] = $groupeQuestion->code;
							$_POST['idPere'] = $idPere;
							require_once(EVA_METABOXES_PLUGIN_DIR . 'veilleReglementaire/groupeQuestionPersistance.php');
						}
					}
					else
					//Le fils est une question
					{
						$idFils = str_replace('leaf-','', $fils);
						evaQuestion::transfertQuestion($idFils, $idPere, $idPereOriginel);
					}
					break;
				case 'delete':
					$_POST['idGroupeQuestion'] = $_GET['id'];
					$_POST['act'] = $_GET['act'];
					require_once(EVA_METABOXES_PLUGIN_DIR . 'veilleReglementaire/groupeQuestionPersistance.php');
					break;
			}
		case TABLE_QUESTION:
			switch($_GET['act'])
			{
				case 'delete':
					$_POST['idQuestion'] = $_GET['id'];
					$_POST['idGroupeQuestions'] = $_GET['idPere'];
					$_POST['act'] = $_GET['act'];
					require_once(EVA_METABOXES_PLUGIN_DIR . 'veilleReglementaire/questionPersistance.php');
					break;
			}
		case TABLE_CATEGORIE_DANGER:
			switch($_GET['act'])
			{
				case 'transfert':
					$fils = $_GET['idElementSrc'];
					$pere = $_GET['idElementDest'];
					$idFils = (string)((int) str_replace('node-' . $_GET['location'] . '-','', $fils));
					$idPere = str_replace('node-' . $_GET['location'] . '-','', $pere);
					if($idFils == str_replace('node-' . $_GET['location'] . '-','', $fils))
					//Le fils est une catégorie
					{
						$categorie = categorieDangers::getCategorieDanger($idFils);
						$pereActu = Arborescence::getPere($_GET['nom'], $categorie);
						if($pereActu->id != $idPere)
						{
							$_POST['act'] = 'update';
							$_POST['id'] = $idFils;
							$_POST['nom_categorie'] = $categorie->nom;
							$_POST['categorieMere'] = $idPere;
							require_once(EVA_METABOXES_PLUGIN_DIR . 'dangers/categorieDangers/categorieDangersPersistance.php');
						}
					}
					else
					//Le fils est un danger
					{
						$idFils = str_replace('leaf-','', $fils);
						evaDanger::transfertDanger($idFils, $idPere);
					}
					break;
				case 'none':
					switch($_GET['affichage'])
					{
						case "affichageTable":
						case "affichageListe":
							$_POST['affichage'] = $_GET['affichage'];
							require_once(EVA_MODULES_PLUGIN_DIR . 'dangers/partieGaucheDangers.php');
							echo $script . $partieGauche;
							break;
					}
			}
			break;
			
	}
}