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
{//EPI 																										/!\	PLUS UTILISE/!\
	//DELETE FROM VERSION 44
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
$formulaireInstallation = $formulaireInstallation . EvaDisplayDesign::getTable($idTable, null, $lignesDeValeurs, $classes, $idLignes, '');
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
						"theme": evarisk("#activationThemeEvarisk").attr("checked")
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