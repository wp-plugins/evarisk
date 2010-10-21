<?php

require_once(EVA_CONFIG);
require_once(EVA_LIB_PLUGIN_DIR . 'methode/methodeEvaluation.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'methode/eva_operateur.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayDesign.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayInput.class.php');
include_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php' );

	
function getMethodForm()
{
	$display = EvaDisplayInput::ouvrirForm('POST', 'methodForm', 'methodForm');
	{//Methode
		{//Initializing
			$methode_new = '';
			$icone = EVA_METHODE_ICON;
			$altIcon = "General Option Icon";
			$titreIcone = "generalOptionIcon";			
			if($_POST['id']!=null)
			{
				$saveOrUpdate = 'update';
				$postId = $_POST['id'];
				$methode_evaluation = MethodeEvaluation::getMethod($postId);
				$titrePage = "Modifier la m&eacute;thode d'&eacute;valuation  : " . $methode_evaluation->nom;;
				$saufMethode = $methode_evaluation->nom;
				$variablesMethode = MethodeEvaluation::getVariablesMethode($methode_evaluation->id);
				$nbInput=count($variablesMethode);
				foreach($variablesMethode as $variableMethode)
				{
					$varMethodeIds[]=$variableMethode->id;
					$varMethodeNames[]=$variableMethode->nom;
				}
				$operateursMethode = MethodeEvaluation::getOperateursMethode($methode_evaluation->id);
				foreach($operateursMethode as $operateurMethode)
				{
					$opsMethode[]=$operateurMethode->operateur;
				}
				$contenuInputTitre = $methode_evaluation->nom;
				$grise = false;
			}
			else
			{
				$postId = '';
				$nbInput=1;
				$contenuInputTitre = '';
				$grise = true;
				$saveOrUpdate = 'save';
				$saufMethode = '';
				$titrePage = "Ajouter une nouvelle m&eacute;thode d'&eacute;valuation";
			}

			if(isset($_POST['nom_methode']))
			{// On récupère le nom en cas d'import ou d'ajout de variable
				$contenuInputTitre = $_POST['nom_methode'];
				$grise = false;
			}

			unset($operateur);$operateur;
			$ops = Eva_Operateur::getOperators();
			foreach($ops as $op)
			{
				$operateur[]=$op->symbole;
				$operateurIndexSymbole[$op->symbole] = $op;
			}
			$opsJava ="' ";
			$opsJava = $opsJava  .  implode("', ' ", $operateur);
			$opsJava = $opsJava  .  " '";

			unset($variable, $variableIndexId);$variableIndexId;$variable;
			$vars = MethodeEvaluation::getAllVariables();
			foreach($vars as $var)
			{
				$variableIndexId[$var->id] = $var;
				$variable[]=$var->nom;
			}
			$varsJava = "' ";
			$varsJava = $varsJava  .  implode(" ', ' ", $variable);
			$varsJava = $varsJava  .  " '";
		}
		$script = '<script type="text/javascript">
				$(document).ready(function() {				
					nbInput = ' . $nbInput . ';
				});
			</script>';
		$display = $display . $script;
		{//Hidden fields
			$methode_new = $methode_new . EvaDisplayInput::afficherInput('hidden', 'act', '', '', null, 'act', false, false);
			$methode_new = $methode_new . EvaDisplayInput::afficherInput('hidden', 'id', $postId, '', null, 'id', false, false);
		}
		{//Methode name
			$contenuAideTitre = "";
			$labelInput = "Nom de la M&eacute;thode d'&eacute;valuation : ";
			$nomChamps = "nom_methode";
			$idTitre = "nom_methode";
			$methode_new = $methode_new . EvaDisplayInput::afficherInput('text', $idTitre, $contenuInputTitre, $contenuAideTitre, $labelInput, $nomChamps, $grise, true, 255, 'titleInput');
		}
		{//Adding/removing form fields
			{//Adding form fields
				$idInput = "addingFormFields";
				$contenuInput = "";
				$labelInput = "Nombre de variables &agrave; ajouter &agrave; la formule : ";
				$nomChamps = "addingFormFields";
				$adding = EvaDisplayInput::afficherInput('text', $idInput, $contenuInput, "", $labelInput, $nomChamps, $grise, false, 3, '', 'Number', '', '', 'right');
				
				$idBouton = 'ajoutChamps';
				$texteBouton = "Ajouter les variables";
				{//Adding script
					$script = '<script type="text/javascript">
						$(document).ready(function() {
							$(\'#' . $idBouton . '\').click(function(){
								var nbChampsAjout = document.getElementById(\'' . $idInput . '\').value;
								var DivToAdd = document.getElementById(\'formulediv\');
								if(nbChampsAjout != parseInt(nbChampsAjout)){alert(\'Veuillez saisir un nombre (ex: 1)\');}
								else if(nbChampsAjout <= 0){alert(\'Veuillez indiquer le nombre de champs a ajouter (ex: 1)\');}
								else if(nbChampsAjout > ' . EVA_PARAM_FORMULE_MAX . '){alert(\'Veuillez indiquer un nombre inf&eacute;rieur ou &eacute;gale &agrave; ' . EVA_PARAM_FORMULE_MAX . ' (ex: 1)\');}
								else{
									for(var i = 0 ; i < nbChampsAjout; i++){
										nbInput++;
										tempSelect = document.createElement(\'select\');
										tempSelect.setAttribute("name","op[]");
										tempSelect.setAttribute("id","op_" + (nbInput-1));
										var ops = new Array(' . $opsJava . ');
										ops.reverse();
										max = ops.length;
										for(j=0;j < max;j++)
										{
											op = ops.pop();
											tempOption = document.createElement(\'option\');
											tempOption.setAttribute("value",op);
											tempOption.appendChild(document.createTextNode(op));
											tempSelect.appendChild(tempOption);
										}
										DivToAdd.appendChild(tempSelect);
										tempInput = document.createElement(\'select\');
										var vars = new Array(' . $varsJava . ');
										vars.reverse();
										max = vars.length;
										for(j=0;j < max;j++)
										{
											vari = vars.pop();
											tempOption = document.createElement(\'option\');
											tempOption.setAttribute("value",vari);
											tempOption.appendChild(document.createTextNode(vari));
											tempInput.appendChild(tempOption);
										}
										tempInput.setAttribute("name","var[]");
										tempInput.setAttribute("id","var_" + nbInput);
										tempInput.setAttribute("autocomplete","off"); 
										DivToAdd.appendChild(tempInput);
									}
								}
								$("#' . $idInput . '").val("");
							});
						});
						</script>';
				}
				$adding = $adding . EvaDisplayInput::afficherInput('button', $idBouton, $texteBouton, null, '', $idBouton, false, true, '', 'alignright', '', '', $script);
			}
			{//Removing form fields
				$idInput = "removingFormFields";
				$contenuInput = "";
				$labelInput = "Nombre de variables &agrave; retirer &agrave; la formule : ";
				$nomChamps = "removingFormFields";
				$removing = EvaDisplayInput::afficherInput('text', $idInput, $contenuInput, "", $labelInput, $nomChamps, $grise, false, 3, '', 'Number', '', '', 'right');
				
				$idBouton = 'retirerChamps';
				$texteBouton = "Retirer les variables";
				{//Removing script
					$script = '<script type="text/javascript">
						$(document).ready(function() {
							$(\'#' . $idBouton . '\').click(function(){
								var nbChampsRetrait = document.getElementById(\'' . $idInput . '\').value;
								var d = document.getElementById(\'formulediv\');
								if(nbChampsRetrait != parseInt(nbChampsRetrait)){alert(\'Veuillez saisir un nombre\');}
								else if(nbChampsRetrait <= 0){alert(\'Veuillez indiquer le nombre de champs a retirer\');}
								else if(nbChampsRetrait >= nbInput){alert("Veuillez indiquer un nombre inférieur ou égale aux nombre de champs.\nP.S. : il doit en rester au moins 1.");}
								else
								{ 
									for(var i = 0 ; i < nbChampsRetrait; i++)
									{
										var oldvar = document.getElementById("var_" + nbInput); 
										d.removeChild(oldvar); 
										var oldop = document.getElementById("op_" + (nbInput-1)); 
										d.removeChild(oldop); 
										nbInput = nbInput - 1 ;
									}
								}
								$("#' . $idInput . '").val("");
							});
						});
						</script>';
				}
				$removing = $removing . EvaDisplayInput::afficherInput('button', $idBouton, $texteBouton, null, '', $idBouton, false, true, '', 'alignright', '', '', $script);
			}
		}
		$methode_new = $methode_new . evaDisplayDesign::splitEcran($adding, $removing, 50, 2);
		$methode_new = $methode_new . '<div id="filAriane"></div><div id="formulediv" style="clear:both;"><br />';
		{//Form initializing
			//We got the first variable
			foreach($variableIndexId as $premiereVariable)
			{
				$valeurVariable1 = (isset($varMethodeIds))?$variableIndexId[$varMethodeIds[0]]:$premiereVariable;
				break;
			}
			$methode_new = $methode_new . EvaDisplayInput::afficherComboBox($vars, 'var_1', null, 'var[]', '', $valeurVariable1, $variable);
			if(isset($variablesMethode) && count($variablesMethode)>0)
			{
				for($i=1; $i<count($variablesMethode); $i++)
				{
					$methode_new = $methode_new . EvaDisplayInput::afficherComboBox($ops, 'op_' . $i, null, 'op[]', '', $operateurIndexSymbole[$opsMethode[($i-1)]], $operateur, $operateur);
					$methode_new = $methode_new . EvaDisplayInput::afficherComboBox($vars, 'var_' . ($i + 1), null, 'var[]', '', $variableIndexId[$varMethodeIds[$i]], $variable);
				}
			}
		}
		$methode_new = $methode_new . '</div>';
		{//Save Button (top)
			$idBouttonEnregistrer = 'save';
			$methodes = getMethodsName($saufMethode);
			$valeurActuelleIn = "false";
			if(count($methodes) != 0)
			{
				$valeurActuelleIn = "valeurActuelle in {";
				foreach($methodes as $methode)
				{
					$valeurActuelleIn = $valeurActuelleIn . "'" . addslashes($methode) . "':'', ";
				}
				$valeurActuelleIn = $valeurActuelleIn . "}";
			}
			$scriptEnregistrement = '<script type="text/javascript">
				$(document).ready(function() {				
					$(\'#' . $idBouttonEnregistrer . '\').click(function() {
						if($(\'#' . $idTitre . '\').is(".form-input-tip"))
						{
							document.getElementById(\'' . $idTitre . '\').value=\'\';
							$(\'#' . $idTitre . '\').removeClass(\'form-input-tip\');
						}
						valeurActuelle = $(\'#' . $idTitre . '\').val();
						if(valeurActuelle == "")
						{
							alert("Vous n\'avez pas donne de nom a la méthode");
						}
						else
						{
							if(' . $valeurActuelleIn . ')
							{
								alert("Une méthode porte déjà ce nom");
							}
							else
							{
								$(\'#act\').val("' . $saveOrUpdate . '");
								document.forms.methodForm.submit();
							}
						}	
					});
				});
				</script>';
			$methode_new = $methode_new . EvaDisplayInput::afficherInput('button', $idBouttonEnregistrer, 'Enregistrer', null, '', $idBouttonEnregistrer, false, true, '', 'button-primary alignright', '', '', $scriptEnregistrement);
		}
		if($postId > 0)
		{//Galerie photo pour expliquer la méthode
			$methode_new .= EvaDisplayDesign::ouvrirMetaboxHolder();
			unset($postBoxes);
			$postBox['id'] = 'galeriePhotoMethodePostBox';
			$postBox['titre']= __('Photo explicative de la m&eacute;thode', 'evarisk');
			$postBox['pagination'] = false;
			$postBox['corps'] = evaPhoto::galleryContent(TABLE_METHODE, $postId);
			$postBoxes[] = $postBox;
			$methode_new .= EvaDisplayDesign::displayPostBoxes($postBoxes) . EvaDisplayDesign::fermerMetaboxHolder() . '<!-- /galeriePhoto -->';
		}
		{//Import
			$methode_new = $methode_new .  EvaDisplayInput::afficherInput($type='FILE', $id='import', $contenuInput='', $contenuAide='', $labelInput='Importer les valeurs &eacute;quivalentes &agrave; partir d\'un .csv', $nomChamps='import', $grise=false, false, $taille=255);
			
			$id='validImport';
			$contenuInput='Importer';
			$contenuAide='';
			$labelInput='';
			$nomChamps='validImport';
			$scriptValidImport = '<script type="text/javascript">
					$(document).ready(function() {
						$(\'#' . $id . '\').click(function() {
							$(\'#act\').val(\'import\');
							document.forms.methodForm.submit();
						});
					});
				</script>';
			$methode_new = $methode_new . EvaDisplayInput::afficherInput($type='button', $id='validImport', $contenuInput='Importer', $contenuAide='', $labelInput='', $nomChamps='validImport', $grise=false, false, $taille=15, $classe='', $limitation='', $width='', $scriptValidImport);
		}
		{//Equivalency
			$etalon = methodeEvaluation::getEtalon();
			unset($postBoxes);
			$methode_new = $methode_new . EvaDisplayDesign::ouvrirMetaboxHolder();
			$postBox['id']='equivEchelon';
			$postBox['titre']='&Eacute;quivalence avec l\'&eacute;talon';
			$postBox['pagination']=false;
			
			$postBox['corps'] = '<div id="equivalence">';
			$j = 0;
			for($i=$etalon->min; $i<= $etalon->max; $i = $i + $etalon->pas)
			{
				$contenuInput = '';
				//We import the values from the .csv or from the data base if there is no import
				if(isset($_POST['equvalenceEchelon']))
				{
					if(isset($_POST['equvalenceEchelon'][$i]))
						$contenuInput = $_POST['equvalenceEchelon'][$i];
				}
				else if($_POST['id']!=null)
				{
					$equivalent = methodeEvaluation::getEquivalentEtalon($_POST['id'], $i);
					$contenuInput = (isset($equivalent))?$equivalent->valeurMaxMethode:'';
				}
				//We create the field
				$postBox['corps'] = $postBox['corps'] . EvaDisplayInput::afficherInput($type='text', $id='eqiv' . $i, $contenuInput, $contenuAide='', $labelInput='Valeur maximale de la methode &eacute;quivalent &agrave; ' . $i . ' : ', $nomChamps='equivalent[' . $i . ']', $grise=false, false, $taille=15, $classe='', $limitation='Number', $width='50%', $script='');
				$j++;
			}
			$postBox['corps'] = $postBox['corps'] . '</div><!-- /equivalence -->';
			$postBoxes[]=$postBox;
			$methode_new = $methode_new . EvaDisplayDesign::displayPostBoxes($postBoxes);
			$methode_new = $methode_new . EvaDisplayDesign::fermerMetaboxHolder();
		}
		{//Save Button (bottom)
			$idBouttonEnregistrerBas = 'save2';
			$scriptEnregistrement = '<script type="text/javascript">
				$(document).ready(function() {				
					$(\'#' . $idBouttonEnregistrerBas . '\').click(function() {
						$(\'#' . $idBouttonEnregistrer . '\').click();
					});
				});
				</script>';
			$methode_new = $methode_new . EvaDisplayInput::afficherInput('button', $idBouttonEnregistrerBas, 'Enregistrer', null, '', $idBouttonEnregistrer, false, true, '', 'button-primary alignright', '', '15em', $scriptEnregistrement);
		}
	}
	{//Variable
		$variablesSide = EvaDisplayDesign::ouvrirMetaboxHolder();
		unset($postBoxes);
		$postBox['id']='variables';
		$postBox['titre']='Variables';
		$postBox['pagination']=false;
		$postBox['corps'] = '';
		
		{//Table
			unset($titres,$classes, $lignesDeValeurs, $idLignes);
			$scriptVariables = '';
			$idTable = 'tableVariable';
			$titres[] = 'Nom';
			$titres[] = 'Min';
			$titres[] = 'Max';
			$classes[] = 'variableName';
			$classes[] = 'variableMin';
			$classes[] = 'variableMax';
			$variables = MethodeEvaluation::getAllVariables();
			foreach($variables as $variable)
			{
				unset($ligneDeValeurs);
				$idLigne = 'ut-' . $variable->id;
				$scriptVariables = $scriptVariables . '<script type="text/javascript">
					$(document).ready(function() {
						$("#' . $idLigne . '").click(function(){});
					});
				</script>';
				$idLignes[] = $idLigne;
				$ligneDeValeurs[] = array('value' => $variable->nom, 'class' => '');
				$ligneDeValeurs[] = array('value' => $variable->min, 'class' => '');
				$ligneDeValeurs[] = array('value' => $variable->max, 'class' => '');
				$lignesDeValeurs[] = $ligneDeValeurs;
			}
			$scriptTable = $scriptVariables . '<script type="text/javascript">
					$(document).ready(function() {
						$("#' . $idTable . '").dataTable({
							"sPaginationType": "full_numbers", 
							"bAutoWidth": false, 
							"aoColumns": [
								{ "bSortable": true, "sType": "html" },
								{ "bSortable": false },
								{ "bSortable": false }],
							"aaSorting": [[0,"asc"]]
						});
					});
				</script>';
			$postBox['corps'] = $postBox['corps'] . EvaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $scriptTable);				
		}
		{//New variable display switcher
			$script = '
				<script type="text/javascript">
					$(document).ready(function() {
						$("#variable-add-toggle").click(function(){
							$("#variable-add").toggleClass("hide-if-js");
						});
					});
				</script>';
			$postBox['corps'] = $postBox['corps'] . $script . '<br class="clear" /><div class="wp-hidden-children" id="variable-adder">
				<h4><a class="hide-if-no-js" id="variable-add-toggle">+ Ajouter une nouvelle variable</a></h4>
				<div class="hide-if-js" id="variable-add">';
		}
		{//New variable name
			$idInputNom = 'newvarname';
			$nomChamps = 'newvarname';
			$labelInput = 'Nom de la nouvelle variable : ';
			$postBox['corps'] = $postBox['corps'] . EvaDisplayInput::afficherInput('text', $idInputNom, '', '', $labelInput, $nomChamps, true, false, 100);
		}
		{//New variable minimum
			$idInputMin = 'newvarmin';
			$nomChamps = 'newvarmin';
			$labelInput = 'Minimum de la nouvelle variable : ';
			$postBox['corps'] = $postBox['corps'] . EvaDisplayInput::afficherInput('text', $idInputMin, '', '', $labelInput, $nomChamps, true, false, 100,'','Number');
		}
		{//New variable maximum
			$idInputMax = 'newvarmax';
			$nomChamps = 'newvarmax';
			$labelInput = 'Maximum de la nouvelle variable : ';
			$postBox['corps'] = $postBox['corps'] . EvaDisplayInput::afficherInput('text', $idInputMax, '', '', $labelInput, $nomChamps, true, false, 100,'','Number');
		}
		{//New variable description
			$idInput = 'newvarannotation';
			$nomChamps = 'newvarannotation';
			$labelInput = 'Annotation sur la nouvelle variable : ';
			$postBox['corps'] = $postBox['corps'] . EvaDisplayInput::afficherInput('textarea', $idInput, '', '', $labelInput, $nomChamps, true, false, 5);
		}
		{//New variable discrete values
			$idInput = 'checkValues';
			$nomChamps = 'checkValues';
			$script = '
				<script type="text/javascript">
					$(document).ready(function() {
						$("#'. $idInput . '").click(function(){
							$("#valeursDiscretes").toggleClass("hide-if-js");
						});
						$("#'. $idInputMin . '").blur(function(){
							$("#valeursDiscretes").html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
							$("#valeursDiscretes").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "nom":"loadFieldsNewVariable", "min":$("#'. $idInputMin . '").val(), "max":$("#'. $idInputMax . '").val()});
						});
						$("#'. $idInputMax . '").blur(function(){
							$("#'. $idInputMin . '").blur();
						});
					});
				</script>';
			$labelInput = 'Valeur diff&eacute;rente du chiffre';
			$postBox['corps'] = $postBox['corps'] . EvaDisplayInput::afficherInput('checkbox', $idInput, '', '', $labelInput, $nomChamps, true, false, 1, '', '', '3%',$script);
			$postBox['corps'] = $postBox['corps'] . '<div class="hide-if-js" id="valeursDiscretes">';
			$postBox['corps'] = $postBox['corps'] . '</div>';
		}
		{//New variable submit button
			$idButton = "AjouterVariable";
			$script = '
				<script type="text/javascript">
					$(document).ready(function() {
						$("#' . $idButton . '").click(function(){
							var submit = true;
							if($("#' . $idInputNom . '").is(".form-input-tip"))
							{
								$("#' . $idInputNom . '").val("");
								alert("Veuillez donner un nom à votre variable.");
								submit = false;
							}
							if($("#' . $idInputMin . '").val() > $("#' . $idInputMax . '").val())
							{
								alert("La valeur maximale doit être supérieure à la valeur minimale.");
								submit = false;
							}
							if(submit)
							{
								$("#act").val("addVariable");
								document.forms.methodForm.submit();
							}
						});
					});
				</script>';
			$postBox['corps'] = $postBox['corps'] . EvaDisplayInput::afficherInput('button', $idButton, 'Ajouter', null, '', $idButton, false, false, '', 'alignright', '', '', $script);
		}
		$postBox['corps'] = $postBox['corps'] . '</div>
			</div>';
		$postBoxes[] = $postBox;
		$variablesSide = $variablesSide . evaDisplayDesign::displayPostBoxes($postBoxes);
		$variablesSide = $variablesSide . EvaDisplayDesign::fermerMetaboxHolder();
	}
	$display = $display . evaDisplayDesign::splitEcran($methode_new, $variablesSide, 69);
	$display = $display . EvaDisplayInput::fermerForm('methodForm');
	return $display;
}

function displayMethodForm()
{
	$display = getMethodForm();
	echo $display;
}
?>