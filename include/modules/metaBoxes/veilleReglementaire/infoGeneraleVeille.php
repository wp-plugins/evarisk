<?php

require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayInput.class.php' );

$numeroRubrique = $_POST['numeroRubrique'];
$tableElement = $_POST['tableElement'];
$idElement = $_POST['idElement'];

	$infoGeneraleVeille = EvaDisplayInput::ouvrirForm('POST', 'infoGeneraleVeilleForm', 'infoGeneraleVeilleForm');
	
	$labelInput = ucfirst(strtolower(sprintf(__("pr&eacute;nom %s", 'evarisk'),__("de l'inspecteur de la veille", 'evarisk')))) . ' : ';
	$labelInput[1] = ($labelInput[0] == "&")?ucfirst($labelInput[1]):$labelInput[1];
	$nomChamps = "prenomInspecteur";
	$idChamps = "prenomInspecteur";
	$infoGeneraleVeille = $infoGeneraleVeille . EvaDisplayInput::afficherInput('text', $idChamps, '', '', $labelInput, $nomChamps, false, NOM_APPROBATEUR_VEILLE_OBLIGATOIRE, 60);
	
	$labelInput = ucfirst(strtolower(sprintf(__("nom (de famille)%s", 'evarisk'),__("de l'inspecteur de la veille", 'evarisk')))) . ' : ';
	$labelInput[1] = ($labelInput[0] == "&")?ucfirst($labelInput[1]):$labelInput[1];
	$nomChamps = "nomInspecteur";
	$idChamps = "nomInspecteur";
	$infoGeneraleVeille = $infoGeneraleVeille . EvaDisplayInput::afficherInput('text', $idChamps, '', '', $labelInput, $nomChamps, false, PRENOM_APPROBATEUR_VEILLE_OBLIGATOIRE, 60);
	
	$labelInput = ucfirst(strtolower(sprintf(__("pr&eacute;nom %s", 'evarisk'),__("de l'approbateur de la veille", 'evarisk')))) . ' : ';
	$labelInput[1] = ($labelInput[0] == "&")?ucfirst($labelInput[1]):$labelInput[1];
	$nomChamps = "prenomApprobateur";
	$idChamps = "prenomApprobateur";
	$infoGeneraleVeille = $infoGeneraleVeille . EvaDisplayInput::afficherInput('text', $idChamps, '', '', $labelInput, $nomChamps, false, NOM_APPROBATEUR_VEILLE_OBLIGATOIRE, 60);
	
	$labelInput = ucfirst(strtolower(sprintf(__("nom (de famille)%s", 'evarisk'),__("de l'approbateur de la veille", 'evarisk')))) . ' : ';
	$labelInput[1] = ($labelInput[0] == "&")?ucfirst($labelInput[1]):$labelInput[1];
	$nomChamps = "nomApprobateur";
	$idChamps = "nomApprobateur";
	$infoGeneraleVeille = $infoGeneraleVeille . EvaDisplayInput::afficherInput('text', $idChamps, '', '', $labelInput, $nomChamps, false, PRENOM_APPROBATEUR_VEILLE_OBLIGATOIRE, 60);
	
	$labelInput = __("Liste des arr&ecirc;t&eacute;s pr&eacute;fectoraux relatifs &agrave; l'installation concern&eacute;e", 'evarisk') . ' : ';
	$idChamps = "arretesPrefectoraux";
	$nomChamps = "arretesPrefectoraux";
	$rows = 5;
	$infoGeneraleVeille = $infoGeneraleVeille . EvaDisplayInput::afficherInput('textarea', $idChamps, '', '', $labelInput, $nomChamps, false, LISTE_ARRETES_VEILLE_OBLIGATOIRE, $rows);
	
	$labelInput = __("Contr&ocirc;leur ayant effectu&eacute; le dernier contr&ocirc;le", 'evarisk') . ' : ';
	$nomChamps = "controleurDernierControle";
	$idChamps = "controleurDernierControle";
	$infoGeneraleVeille = $infoGeneraleVeille . EvaDisplayInput::afficherInput('text', $idChamps, '', '', $labelInput, $nomChamps, false, INSPECTEUR_DERNIER_CONTROLE_VEILLE_OBLIGATOIRE, 121);
	
	$labelInput = __("Organisme ayant effectu&eacute; le dernier contr&ocirc;le", 'evarisk') . ' : ';
	$nomChamps = "organismeDernierControle";
	$idChamps = "organismeDernierControle";
	$infoGeneraleVeille = $infoGeneraleVeille . EvaDisplayInput::afficherInput('text', $idChamps, '', '', $labelInput, $nomChamps, false, ORGANISME_DERNIER_CONTROLE_VEILLE_OBLIGATOIRE, 255);
	
	$labelInput = __("Date du dernier contr&ocirc;le", 'evarisk') . ' : ';
	$nomChamps = "dateDernierControle";
	$idChamps = "dateDernierControle";
	$infoGeneraleVeille = $infoGeneraleVeille . EvaDisplayInput::afficherInput('text', $idChamps, '', '', $labelInput, $nomChamps, false, DATE_DERNIER_CONTROLE_VEILLE_OBLIGATOIRE, 10, '', 'date');
	
	$labelInput = __("Date du d&eacute;but de l'audit", 'evarisk') . ' : ';
	$nomChamps = "dateAudit";
	$idChamps = "dateAudit";
	$infoGeneraleVeille = $infoGeneraleVeille . EvaDisplayInput::afficherInput('text', $idChamps, '', '', $labelInput, $nomChamps, false, DATE_DEBUT_AUDIT_VEILLE_OBLIGATOIRE, 10, '', 'date');
	
	$labelInput = __("Date de d&eacute;claration de l'installation", 'evarisk') . ' : ';
	$nomChamps = "dateDeclarationInstallation";
	$idChamps = "dateDeclarationInstallation";
	$infoGeneraleVeille = $infoGeneraleVeille . EvaDisplayInput::afficherInput('text', $idChamps, '', '', $labelInput, $nomChamps, false, DATE_DECLARATION_INSTALLATION_VEILLE_OBLIGATOIRE, 10, 'date');
	
	$labelInput = __("Date de mise en service de l'installation", 'evarisk') . ' : ';
	$nomChamps = "dateMiseServiceInstallation";
	$idChamps = "dateMiseServiceInstallation";
	$infoGeneraleVeille = $infoGeneraleVeille . EvaDisplayInput::afficherInput('text', $idChamps, '', '', $labelInput, $nomChamps, false, DATE_MISE_SERVICE_INSTALLATION_VEILLE_OBLIGATOIRE, 10, '', 'date');
	
	$labelInput = __("Grand groupe auquel appartient l'installation", 'evarisk') . ' : ';
	$nomChamps = "groupeAuQuelInstallationAppartient";
	$idChamps = "groupeAuQuelInstallationAppartient";
	$infoGeneraleVeille = $infoGeneraleVeille . EvaDisplayInput::afficherInput('text', $idChamps, '', '', $labelInput, $nomChamps, false, GRAND_GROUP_VEILLE_OBLIGATOIRE, 255);

	$idBouttonEnregistrer = 'lancerGenerationPDF';
	$scriptGenererPDF = '<script type="text/javascript">
		function genererPDF()
		{
			var ligne1 = "";
			var ligne2 = "";
			var code_postal = "";
			var ville = "";
			if(evarisk(\'#adresse_ligne_1\').val() != "" && evarisk(\'#adresse_ligne_1\').val() != "Adresse ligne 1")
			{
				ligne1 = evarisk(\'#adresse_ligne_1\').val() + "\n";
			}
			if(evarisk(\'#adresse_ligne_2\').val() != "" && evarisk(\'#adresse_ligne_2\').val() != "Adresse ligne 2")
			{
				ligne2 = evarisk(\'#adresse_ligne_2\').val() + "\n";
			}
			if(evarisk(\'#code_postal\').val() != "" && evarisk(\'#code_postal\').val() != "Code postal")
			{
				code_postal = evarisk(\'#code_postal\').val();
			}
			if(evarisk(\'#ville\').val() != "" && evarisk(\'#ville\').val() != "Ville")
			{
				ville = evarisk(\'#ville\').val();
			}
			var adresse = ligne1 + ligne2 + code_postal + " " + ville;
			var site = (evarisk(\'#nomElement\').html()).trim();
			var nomClient = ((evarisk(\'#miniFilAriane\').html()).split(\'&raquo;\')[0]).trim();
			var tableElement = "' . $tableElement . '";
			var idElement = "' .  $idElement . '";
			var numeroRubrique = "' .  $numeroRubrique . '";
			var prenomApprobateur = evarisk(\'#prenomApprobateur\').val();
			var nomApprobateur = evarisk(\'#nomApprobateur\').val();
			var prenomInspecteur = evarisk(\'#prenomInspecteur\').val();
			var nomInspecteur = evarisk(\'#nomInspecteur\').val();
			var arretesPrefectoraux = (evarisk(\'#arretesPrefectoraux\').val()).trim();
			var controleurDernierControle = evarisk(\'#controleurDernierControle\').val();
			var organismeDernierControle = evarisk(\'#organismeDernierControle\').val();
			var dateDernierControle = evarisk(\'#dateDernierControle\').val();
			var dateAudit = evarisk(\'#dateAudit\').val();
			var dateDeclarationInstallation = evarisk(\'#dateDeclarationInstallation\').val();
			var dateMiseServiceInstallation = evarisk(\'#dateMiseServiceInstallation\').val();
			var groupeAuQuelInstallationAppartient = evarisk(\'#groupeAuQuelInstallationAppartient\').val();
			var effectif = 0;
			if(evarisk(\'#effectif\').val() != "" && evarisk(\'#effectif\').val() != "Effectif" && evarisk(\'#effectif\').val() != "&Eacute;ffectif")
			{
				effectif = evarisk(\'#effectif\').val();
			}
			evarisk(\'#ajax-response\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {\'post\':\'true\', \'nom\':\'generationPDF\', \'tableElement\':tableElement, \'idElement\':idElement, \'nomClient\':nomClient, \'site\':site, \'adresse\':adresse, \'numeroRubrique\':numeroRubrique, \'effectif\':effectif, \'prenomApprobateur\':prenomApprobateur, \'nomApprobateur\':nomApprobateur, \'arretesPrefectoraux\':arretesPrefectoraux, \'controleurDernierControle\':controleurDernierControle, \'organismeDernierControle\':organismeDernierControle, \'dateDernierControle\':dateDernierControle, \'dateAudit\':dateAudit, \'dateDeclarationInstallation\':dateDeclarationInstallation, \'dateMiseServiceInstallation\':dateMiseServiceInstallation, \'groupeAuQuelInstallationAppartient\':groupeAuQuelInstallationAppartient});
		}
		evarisk(document).ready(function() {
			evarisk(\'#' . $idBouttonEnregistrer . '\').click(function() {
				genererPDF();
			});
		});
	</script>';
	$infoGeneraleVeille = $infoGeneraleVeille . EvaDisplayInput::afficherInput('button', $idBouttonEnregistrer, 'G�n�rer le PDF', null, '', $idBouttonEnregistrer, false, false, '', 'button-primary alignright', '', '', $scriptGenererPDF);
	$infoGeneraleVeille = $infoGeneraleVeille . EvaDisplayInput::fermerForm('infoGeneraleVeilleForm');
	echo $infoGeneraleVeille;
?>