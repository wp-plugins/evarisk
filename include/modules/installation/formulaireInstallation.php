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
		evarisk(document).ready(function(){
			evarisk(\'#%1$s\').attr("checked", "checked");
			evarisk(\'#%1$s\').addClass("cbSetup");
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
			evarisk(document).ready(function(){
				evarisk(\'#%1$s\').addClass("cbEPISetup");
				evarisk(\'#%1$s\').click(function(){
					var check = "";
					evarisk(\'.cbEPISetup\').each(function(){
						if(evarisk(this).attr("checked"))
							check = "checked";
					});
					evarisk(\'#' . $idEPI . '\').attr("checked", check);
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
				evarisk(document).ready(function(){
					evarisk(\'#' . $idEPI . '\').click(function(){
						var check = "";
						if(evarisk(this).attr("checked"))
							check = "checked";
						evarisk(\'.cbEPISetup\').each(function(){
							evarisk(this).attr("checked", check);
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


/*	Add the autoinstall option for the executable version of the soft 	*/
$autoLaunch = '';


$classes = null;
$idLignes = null;
$formulaireInstallation = $formulaireInstallation . EvaDisplayDesign::getTable($idTable, null, $lignesDeValeurs, $classes, $idLignes);
{//Bouton installer Evarisk
	$allVariables = MethodeEvaluation::getAllVariables();
	$idBouttonEnregistrer = 'installerEvarisk';

/*	Autoinstall at launch if the good parameter is passe din the url	*/
	$autoLaunch =  
'var autoInstall = false;';
if(STANDALONEVERSION)
{
	$autoLaunch =  
'var autoInstall = true;';
}

	$scriptEnregistrement = '<script type="text/javascript">
		evarisk(document).ready(function() {

			' . $autoLaunch . '
		
			evarisk("#' . $idBouttonEnregistrer . '").click(function() {
				var epi = new Array();
				epi[0] = new Array();
				epi[0][0] = evarisk(\'#bab\').attr("checked");
				epi[0][1] = "bab";
				epi[1] = new Array();
				epi[1][0] = evarisk(\'#casque\').attr("checked");
				epi[1][1] = "casque";
				epi[2] = new Array();
				epi[2][0] = evarisk(\'#chaussures\').attr("checked");
				epi[2][1] = "chaussures";
				epi[3] = new Array();
				epi[3][0] = evarisk(\'#combi\').attr("checked");
				epi[3][1] = "combi";
				epi[4] = new Array();
				epi[4][0] = evarisk(\'#gants\').attr("checked");
				epi[4][1] = "gants";
				epi[5] = new Array();
				epi[5][0] = evarisk(\'#harnais\').attr("checked");
				epi[5][1] = "harnais";
				epi[6] = new Array();
				epi[6][0] = evarisk(\'#lunettes\').attr("checked");
				epi[6][1] = "lunettes";
				epi[7] = new Array();
				epi[7][0] = evarisk(\'#masque\').attr("checked");
				epi[7][1] = "masque";
				var methodes = new Array();
				methodes[0] = new Array();
				methodes[0][0] = evarisk(\'#insertionMethodeEvarisk\').attr("checked");
				methodes[0][1] = "evarisk";
				var goOnInstall = false;
				if( !evarisk("#activationThemeEvarisk").is(":checked") || (autoInstall))
				{
					goOnInstall = true;
				}
				else if(evarisk("#activationThemeEvarisk").is(":checked") && confirm("' . __('Etes vous sur de vouloir activer le theme Evarisk pour votre Blog?\nNB: Si vous avez un theme personnalise celui sera remplace par le theme Evarisk. Il restera disponible dans la liste des themes.', 'evarisk') . '"))
				{
					goOnInstall = true;
				}

				if(goOnInstall)
				{
					evarisk("#installLoading").html(\'<img src="' . PICTO_LOADING_ROUND . '" alt="loading..." />\');
					evarisk("#installButton").html("' . __('Installation en cours. Merci de patienter', 'evarisk') . '");
					evarisk("#wrap' . $idForm . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
						"post": "true", 
						"nom": "installerEvarisk", 
						"categorieDangers": evarisk("#insertionCategorie").attr("checked"),
						"danger": evarisk("#insertionDanger").attr("checked"),
						"methodes": methodes,
						"theme": evarisk("#activationThemeEvarisk").attr("checked"),
						"EPIs": evarisk("#insertionEPI").attr("checked"),
						"EPI": epi
					});
				}
			});
		});
		</script>';
	$formulaireInstallation .= 
'	<div id="installLoading" class="alignright" >&nbsp;</div>
	<div id="installButton" class="alignright" >' . EvaDisplayInput::afficherInput('button', $idBouttonEnregistrer, __('Installer Evarisk', 'evarisk'), null, '', 'save', false, false, '', 'button-primary alignright', '', '', $scriptEnregistrement) . '</div>
</div>';
}
	
$formulaireInstallation = $formulaireInstallation . EvaDisplayInput::fermerForm($idForm);
echo $formulaireInstallation;

echo '</div>';
/*	Autoinstall at launch if the good parameter is passe din the url	*/
if(STANDALONEVERSION)
{
	echo
'<script type="text/javascript">
		evarisk(document).ready(function() {
			evarisk("#' . $idBouttonEnregistrer . '").click();
		});
</script>';
}