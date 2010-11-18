<?php

require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayInput.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayDesign.class.php');

echo '<div class="wrap">';
$idForm = 'formulaireInstallation';
$formulaireInstallation = '<div id="wrap' . $idForm . '">';
$formulaireInstallation = $formulaireInstallation . EvaDisplayInput::ouvrirForm('POST', $idForm, $idForm);

$idTable = 'evariskSetup';
$insertions[0]['class'] = 'setupTitle';
$insertions[1]['class'] = 'setupAction';
$scriptCB = '
	<script type="text/javascript">
		$(document).ready(function(){
			$(\'#%1$s\').attr("checked", "checked");
			$(\'#%1$s\').addClass("cbSetup");
		});
	</script>';
{//Dangers
	$insertions[0]['value'] = '<p class="titreSetup">' . __('Dangers', 'evarisk') . '</p>';
	$insertions[1]['value'] = '';
	{//Ins&eacute;rer les cat&eacute;gories INRS
		$labelInput = ucfirst(strtolower(sprintf(__("Ins&eacute;rer %s", 'evarisk'),__("les cat&eacute;gories INRS", 'evarisk'))));
		$labelInput[1] = ($labelInput[0] == "&")?ucfirst($labelInput[1]):$labelInput[1];
		$id = "insertionCategorie";
		$nomChamps = "insertionCategorie";
		$script = sprintf($scriptCB, $id);
		$insertions[1]['value'] = $insertions[1]['value'] . EvaDisplayInput::afficherInput('checkbox', $id, '', '', $labelInput, $nomChamps, false, false, 1, '', '', '', $script);
	}
	{//Ins&eacute;rer un danger par d&eacute;faut dans chaque cat&eacute;gorie
		$labelInput = ucfirst(strtolower(sprintf(__("Ins&eacute;rer %s", 'evarisk'),__("un danger par d&eacute;faut dans chaque cat&eacute;gorie", 'evarisk'))));
		$labelInput[1] = ($labelInput[0] == "&")?ucfirst($labelInput[1]):$labelInput[1];
		$id = "insertionDanger";
		$nomChamps = "insertionDanger";
		$script = sprintf($scriptCB, $id);
		$insertions[1]['value'] = $insertions[1]['value'] . EvaDisplayInput::afficherInput('checkbox', $id, '', '', $labelInput, $nomChamps, false, false, 1, '', '', '', $script);
	}
	$lignesDeValeurs[] = $insertions;
}
{//M&eacute;thode
	$insertions[0]['value'] = '<p class="titreSetup">' . __('M&eacute;thodes', 'evarisk') . '</p>';
	$insertions[1]['value'] = '';
	{//Ins&eacute;rer la m&eacute;thode Evarisk
		$labelInput = ucfirst(strtolower(sprintf(__("Ins&eacute;rer %s", 'evarisk'),__("la m&eacute;thode Evarisk", 'evarisk'))));
		$labelInput[1] = ($labelInput[0] == "&")?ucfirst($labelInput[1]):$labelInput[1];
		$id = "insertionMethodeEvarisk";
		$nomChamps = "insertionMethodeEvarisk";
		$script = sprintf($scriptCB, $id);
		$insertions[1]['value'] = $insertions[1]['value'] . EvaDisplayInput::afficherInput('checkbox', $id, '', '', $labelInput, $nomChamps, false, false, 1, '', '', '', $script);
	}
	$lignesDeValeurs[] = $insertions;
}
{//EPI
	$insertions[0]['value'] = '<p class="titreSetup">' . __('&Eacute;quipements de Protection Individuelle', 'evarisk') . '</p>';
	$insertions[1]['value'] = '';
	$idEPI = "insertionEPI";
	$scriptCBEPI = '
		<script type="text/javascript">
			$(document).ready(function(){
				$(\'#%1$s\').addClass("cbEPISetup");
				$(\'#%1$s\').click(function(){
					var check = "";
					$(\'.cbEPISetup\').each(function(){
						if($(this).attr("checked"))
							check = "checked";
					});
					$(\'#' . $idEPI . '\').attr("checked", check);
				});
			});
		</script>';
	{//Détail
		{//Ins&eacute;rer BAB
			$labelInput = ucfirst(__('protection auditive', 'evarisk'));
			$id = "bab";
			$nomChamps = "bab";
			$script = sprintf($scriptCB, $id);
			$script = $script . sprintf($scriptCBEPI, $id);
			$insertions[1]['value'] = $insertions[1]['value'] . EvaDisplayInput::afficherInput('checkbox', $id, '', '', $labelInput, $nomChamps, false, false, 1, '', '', '', $script);
		}
		{//Ins&eacute;rer casque
			$labelInput = ucfirst(__('protection de la t&ecirc;te', 'evarisk'));
			$id = "casque";
			$nomChamps = "casque";
			$script = sprintf($scriptCB, $id);
			$script = $script . sprintf($scriptCBEPI, $id);
			$insertions[1]['value'] = $insertions[1]['value'] . EvaDisplayInput::afficherInput('checkbox', $id, '', '', $labelInput, $nomChamps, false, false, 1, '', '', '', $script);
		}
		{//Ins&eacute;rer chaussures
			$labelInput = ucfirst(__('chaussures de s&eacute;curit&eacute;', 'evarisk'));
			$id = "chaussures";
			$nomChamps = "chaussures";
			$script = sprintf($scriptCB, $id);
			$script = $script . sprintf($scriptCBEPI, $id);
			$insertions[1]['value'] = $insertions[1]['value'] . EvaDisplayInput::afficherInput('checkbox', $id, '', '', $labelInput, $nomChamps, false, false, 1, '', '', '', $script);
		}
		{//Ins&eacute;rer combi
			$labelInput = ucfirst(__('combinaison', 'evarisk'));
			$id = "combi";
			$nomChamps = "combi";
			$script = sprintf($scriptCB, $id);
			$script = $script . sprintf($scriptCBEPI, $id);
			$insertions[1]['value'] = $insertions[1]['value'] . EvaDisplayInput::afficherInput('checkbox', $id, '', '', $labelInput, $nomChamps, false, false, 1, '', '', '', $script);
		}
		{//Ins&eacute;rer gants
			$labelInput = ucfirst(__('protection des mains', 'evarisk'));
			$id = "gants";
			$nomChamps = "gants";
			$script = sprintf($scriptCB, $id);
			$script = $script . sprintf($scriptCBEPI, $id);
			$insertions[1]['value'] = $insertions[1]['value'] . EvaDisplayInput::afficherInput('checkbox', $id, '', '', $labelInput, $nomChamps, false, false, 1, '', '', '', $script);
		}
		{//Ins&eacute;rer harnais
			$labelInput = ucfirst(__('protection anti-chute', 'evarisk'));
			$id = "harnais";
			$nomChamps = "harnais";
			$script = sprintf($scriptCB, $id);
			$script = $script . sprintf($scriptCBEPI, $id);
			$insertions[1]['value'] = $insertions[1]['value'] . EvaDisplayInput::afficherInput('checkbox', $id, '', '', $labelInput, $nomChamps, false, false, 1, '', '', '', $script);
		}
		{//Ins&eacute;rer lunettes
			$labelInput = ucfirst(__('protection des yeux', 'evarisk'));
			$id = "lunettes";
			$nomChamps = "lunettes";
			$script = sprintf($scriptCB, $id);
			$script = $script . sprintf($scriptCBEPI, $id);
			$insertions[1]['value'] = $insertions[1]['value'] . EvaDisplayInput::afficherInput('checkbox', $id, '', '', $labelInput, $nomChamps, false, false, 1, '', '', '', $script);
		}
		{//Ins&eacute;rer masque
			$labelInput = ucfirst(__('protection respiratoire', 'evarisk'));
			$id = "masque";
			$nomChamps = "masque";
			$script = sprintf($scriptCB, $id);
			$script = $script . sprintf($scriptCBEPI, $id);
			$insertions[1]['value'] = $insertions[1]['value'] . EvaDisplayInput::afficherInput('checkbox', $id, '', '', $labelInput, $nomChamps, false, false, 1, '', '', '', $script);
		}
	}
	{//Ins&eacute;rer les EPIs
		$labelInput = '<b>' . ucfirst(strtolower(sprintf(__("Ins&eacute;rer %s", 'evarisk'),__("les &eacute;quipements de protection individuelle", 'evarisk')))) . '</b>';
		$nomChamps = "insertionEPI";
		$script = sprintf($scriptCB, $idEPI);
		$script = $script . '
			<script type="text/javascript">
				$(document).ready(function(){
					$(\'#' . $idEPI . '\').click(function(){
						var check = "";
						if($(this).attr("checked"))
							check = "checked";
						$(\'.cbEPISetup\').each(function(){
							$(this).attr("checked", check);
						});
					});
				});
			</script>';
		$insertions[1]['value'] = EvaDisplayInput::afficherInput('checkbox', $idEPI, '', '', $labelInput, $nomChamps, false, false, 1, '', '', '', $script) . $insertions[1]['value'];
	}
	$lignesDeValeurs[] = $insertions;
}
{//Theme Evarisk
	$insertions[0]['value'] = '<p class="titreSetup">' . __('Th&egrave;me', 'evarisk') . '</p>';
	$insertions[1]['value'] = '';
	{//Ins&eacute;rer la m&eacute;thode Evarisk
		$labelInput = ucfirst(strtolower(__("Activer le th&egrave;me Evarisk. (NB: vous pourrez activer le th&egrave;me ult&eacute;rieurement)", 'evarisk')));
		$labelInput[1] = ($labelInput[0] == "&")?ucfirst($labelInput[1]):$labelInput[1];
		$id = "activationThemeEvarisk";
		$nomChamps = "activationThemeEvarisk";
		$script = sprintf($scriptCB, $id);
		$insertions[1]['value'] = $insertions[1]['value'] . EvaDisplayInput::afficherInput('checkbox', $id, '', '', $labelInput, $nomChamps, false, false, 1, '', '', '', $script);
	}
	$lignesDeValeurs[] = $insertions;
}

$classes = null;
$idLignes = null;
$formulaireInstallation = $formulaireInstallation . EvaDisplayDesign::getTable($idTable, null, $lignesDeValeurs, $classes, $idLignes);
{//Bouton installer Evarisk
	$allVariables = MethodeEvaluation::getAllVariables();
	$idBouttonEnregistrer = 'installerEvarisk';
	$scriptEnregistrement = '<script type="text/javascript">
		$(document).ready(function() {	
			$(\'#' . $idBouttonEnregistrer . '\').click(function() {
				var epi = new Array();
				epi[0] = new Array();
				epi[0][0] = $(\'#bab\').attr("checked");
				epi[0][1] = "bab";
				epi[1] = new Array();
				epi[1][0] = $(\'#casque\').attr("checked");
				epi[1][1] = "casque";
				epi[2] = new Array();
				epi[2][0] = $(\'#chaussures\').attr("checked");
				epi[2][1] = "chaussures";
				epi[3] = new Array();
				epi[3][0] = $(\'#combi\').attr("checked");
				epi[3][1] = "combi";
				epi[4] = new Array();
				epi[4][0] = $(\'#gants\').attr("checked");
				epi[4][1] = "gants";
				epi[5] = new Array();
				epi[5][0] = $(\'#harnais\').attr("checked");
				epi[5][1] = "harnais";
				epi[6] = new Array();
				epi[6][0] = $(\'#lunettes\').attr("checked");
				epi[6][1] = "lunettes";
				epi[7] = new Array();
				epi[7][0] = $(\'#masque\').attr("checked");
				epi[7][1] = "masque";
				var methodes = new Array();
				methodes[0] = new Array();
				methodes[0][0] = $(\'#insertionMethodeEvarisk\').attr("checked");
				methodes[0][1] = "evarisk";
				var goOnInstall = false;
				if(!$("#activationThemeEvarisk").is(":checked"))
				{
					goOnInstall = true;
				}
				else if($("#activationThemeEvarisk").is(":checked") && confirm("Etes vous sur de vouloir activer le theme Evarisk pour votre Blog?\nNB: Si vous avez un theme personnalise celui sera remplace par le theme Evarisk. Il restera disponible dans la liste des themes."))
				{
					goOnInstall = true;
				}

				if(goOnInstall)
				{
					$("#installLoading").html(\'<img src="' . EVA_IMG_DIVERS_PLUGIN_URL . 'loading.gif" />\');
					$("#wrap' . $idForm . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
						"post": "true", 
						"nom": "installerEvarisk", 
						"categorieDangers": $(\'#insertionCategorie\').attr("checked"),
						"danger": $(\'#insertionDanger\').attr("checked"),
						"methodes": methodes,
						"theme": $(\'#activationThemeEvarisk\').attr("checked"),
						"EPIs": $(\'#insertionEPI\').attr("checked"),
						"EPI": epi
					});
				}
			});
		});
		</script>';
	$formulaireInstallation = $formulaireInstallation . '<div id="installLoading" class="alignright" >&nbsp;</div>' . EvaDisplayInput::afficherInput('button', $idBouttonEnregistrer, __('Installer Evarisk', 'evarisk'), null, '', 'save', false, false, '', 'button-primary alignright', '', '', $scriptEnregistrement);
	$formulaireInstallation = $formulaireInstallation . '</div>';
}
	
$formulaireInstallation = $formulaireInstallation . EvaDisplayInput::fermerForm($idForm);
echo $formulaireInstallation;

echo '</div>';