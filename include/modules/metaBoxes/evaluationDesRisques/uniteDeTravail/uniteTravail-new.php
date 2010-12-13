<?php
/*
 * @version v5.0
 */

//Postbox definition
$postBoxTitle = __('Informations G&eacute;n&eacute;rales', 'evarisk');
$postBoxId = 'postBoxGeneralInformation';
$postBoxCallbackFunction = 'getWorkingUnitGeneralInformationPostBoxBody';
add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_UNITES_DE_TRAVAIL, 'rightSide', 'default');

function getWorkingUnitGeneralInformationPostBoxBody($arguments)
{
	require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/groupement/eva_groupement.class.php' ); 
	require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/uniteDeTravail/uniteDeTravail.class.php' ); 
	require_once(EVA_LIB_PLUGIN_DIR . 'arborescence.class.php' );
	require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayInput.class.php' );
	require_once(EVA_LIB_PLUGIN_DIR . 'evaGoogleMaps.class.php' );
	require_once(EVA_LIB_PLUGIN_DIR . 'adresse/evaAddress.class.php' );
	
	$id = $arguments['idElement'];
	$idPere = $arguments['idPere'];
	$affichage = $arguments['affichage'];
	$idsFilAriane = $arguments['idsFilAriane'];

	{//Initializing
		if($id!=null)
		{	
			$saveOrUpdate = 'update';
			$uniteTravail = UniteDeTravail::getWorkingUnit($id);
			$saufUniteTravail = $uniteTravail->nom;
			$groupementPere = EvaGroupement::getGroupement($uniteTravail->id_groupement);
			$contenuInputTitre = $uniteTravail->nom;
			$contenuInputDescription = $uniteTravail->description;
			if($uniteTravail->id_adresse != 0 AND $uniteTravail->id_adresse != null)
			{
				$address = new EvaAddress($uniteTravail->id_adresse);
				$address->load();
				$contenuInputLigne1 = $address->getFirstLine();
				$contenuInputLigne2 = $address->getSecondLine();
				$contenuInputCodePostal = $address->getPostalCode();
				$contenuInputVille = $address->getCity();
			}
			else
			{
				$contenuInputLigne1 = '';
				$contenuInputLigne2 = '';
				$contenuInputCodePostal = '';
				$contenuInputVille = '';
			}
			$contenuInputTelephone = $uniteTravail->telephoneUnite;
			if($uniteTravail->effectif != 0)
			{
				$contenuInputEffectif = $uniteTravail->effectif;
			}
			else
			{
				$contenuInputEffectif = '';
			}
			$grise = false;
		}
		else
		{	
			$contenuInputTitre = '';
			$contenuInputDescription = '';
			$contenuInputLigne1 = '';
			$contenuInputLigne2 = '';
			$contenuInputCodePostal = '';
			$contenuInputVille = '';
			$contenuInputTelephone = '';
			$contenuInputEffectif = '';
			$saveOrUpdate = 'save';
			$saufUniteTravail = '';
			$groupementPere = EvaGroupement::getGroupement($idPere);
			$grise = true;
		}
	}
	$idForm = 'informationGeneralesUT';
	$uniteDeTravail_new = EvaDisplayInput::ouvrirForm('POST', $idForm, $idForm);
	{//Champs cachés
		$uniteDeTravail_new = $uniteDeTravail_new . EvaDisplayInput::afficherInput('hidden', 'act', '', '', null, 'act', false, false);
		$uniteDeTravail_new = $uniteDeTravail_new . EvaDisplayInput::afficherInput('hidden', 'latitude', '', '', null, 'latitude', false, false);
		$uniteDeTravail_new = $uniteDeTravail_new . EvaDisplayInput::afficherInput('hidden', 'longitude', '', '', null, 'longitude', false, false);
		$uniteDeTravail_new = $uniteDeTravail_new . EvaDisplayInput::afficherInput('hidden', 'affichage', $affichage, '', null, 'affichage', false, false);
		$uniteDeTravail_new = $uniteDeTravail_new . EvaDisplayInput::afficherInput('hidden', 'table', TABLE_UNITE_TRAVAIL, '', null, 'table', false, false);
		$uniteDeTravail_new = $uniteDeTravail_new . EvaDisplayInput::afficherInput('hidden', 'id', $id, '', null, 'id', false, false);
		$uniteDeTravail_new = $uniteDeTravail_new . EvaDisplayInput::afficherInput('hidden', 'idsFilAriane', $idsFilAriane, '', null, 'idsFilAriane', false, false);
	}

	{//Nom de l'unité
		$labelInput = ucfirst(strtolower(sprintf(__("nom %s", 'evarisk'), __("de l'unit&eacute; de travail", 'evarisk')))) . " :";
		$labelInput[1] = ($labelInput[0] == "&")?ucfirst($labelInput[1]):$labelInput[1];
		$nomChamps = "nom_unite_travail";
		$idTitre = "nom_unite_travail";
		$uniteDeTravail_new = $uniteDeTravail_new . EvaDisplayInput::afficherInput('text', $idTitre, $contenuInputTitre, '', $labelInput, $nomChamps, $grise, true, 255, 'titleInput');
	}
	{//Groupement père
		$search = "`Status`='Valid' AND nom<>'Groupement Racine'";
		$order = "nom ASC";
		$groupements = EvaGroupement::getGroupements($search,$order);
		unset($tabGroupement);
		foreach($groupements as $groupement)
		{
			$tabGroupement[] = $groupement->nom;
		}
		if((isset($groupementPere)))
		{
			$selection = $groupementPere->id;
		}
		$nameSelect = "groupementPere";
		$idSelect = "groupementPere";
		$labelSelect = ucfirst(strtolower(sprintf(__("%s p&egrave;re", 'evarisk'), __("groupement", 'evarisk')))) . ' :';
		$labelInput[1] = ($labelInput[0] == "&")?ucfirst($labelInput[1]):$labelInput[1];
		$valeurDefaut = "Aucun";
		$nomRacine = "Groupement Racine";
		$groupementRacine = EvaGroupement::getGroupementByName($nomRacine);
		$uniteDeTravail_new = $uniteDeTravail_new .  '<div style="display:none">';
		$uniteDeTravail_new = $uniteDeTravail_new . EvaDisplayInput::afficherComboBoxArborescente($groupementRacine, TABLE_GROUPEMENT, $idSelect, $labelSelect, $nameSelect, $valeurDefaut, $selection);
		$uniteDeTravail_new = $uniteDeTravail_new .  '</div>';
	}
	{//Description
		$labelInput = ucfirst(strtolower(__("Description", 'evarisk'))) . " :";
		$labelInput[1] = ($labelInput[0] == "&")?ucfirst($labelInput[1]):$labelInput[1];
		$idChamps = "description";
		$nomChamps = "description";
		$rows = 5;
		$uniteDeTravail_new = $uniteDeTravail_new . EvaDisplayInput::afficherInput('textarea', $idChamps, $contenuInputDescription, '', $labelInput, $nomChamps, $grise, DESCRIPTION_UNITE_TRAVAIL_OBLIGATOIRE, $rows);
	}
	{//Adresse
		$uniteDeTravail_new = $uniteDeTravail_new . '<div id="adresseUnite' . $id . '">';
		{//Ligne 1
			$labelInputL1 = ucfirst(strtolower(__("Adresse ligne 1", 'evarisk'))) . " :";
			$labelInputL1[1] = ($labelInputL1[0] == "&")?ucfirst($labelInputL1[1]):$labelInputL1[1];
			$idL1 = "adresse_ligne_1";
			$nomChamps = "adresse_ligne_1";
			$taille = 32;
			$uniteDeTravail_new = $uniteDeTravail_new . EvaDisplayInput::afficherInput('text', $idL1, $contenuInputLigne1, '', $labelInputL1, $nomChamps, $grise, ADRESSE_UNITE_TRAVAIL_OBLIGATOIRE, $taille);
		}
		{//Ligne 2
			$labelInputL2 = ucfirst(strtolower(__("Adresse ligne 2", 'evarisk'))) . " :";
			$labelInputL2[1] = ($labelInputL2[0] == "&")?ucfirst($labelInputL2[1]):$labelInputL2[1];
			$idL2 = "adresse_ligne_2";
			$nomChamps = "adresse_ligne_2";
			$taille = 32;
			$uniteDeTravail_new = $uniteDeTravail_new . EvaDisplayInput::afficherInput('text', $idL2, $contenuInputLigne2,'',  $labelInputL2, $nomChamps, $grise, false, $taille);
		}
		{//Code Postal
			$labelInputCP = ucfirst(strtolower(__("Code Postal", 'evarisk'))) . " :";
			$labelInputCP[1] = ($labelInputCP[0] == "&")?ucfirst($labelInputCP[1]):$labelInputCP[1];
			$idCP = "code_postal";
			$nomChamps = "code_postal";
			$taille = 5; 
			$uniteDeTravail_new = $uniteDeTravail_new . EvaDisplayInput::afficherInput('text', $idCP, $contenuInputCodePostal, '', $labelInputCP, $nomChamps, $grise, ADRESSE_UNITE_TRAVAIL_OBLIGATOIRE, $taille, '', 'Number');
		}
		{//Ville
			$labelInputV = ucfirst(strtolower(__("Ville", 'evarisk'))) . " :";
			$labelInputV[1] = ($labelInputV[0] == "&")?ucfirst($labelInputV[1]):$labelInputV[1];
			$idV = "ville";
			$nomChamps = "ville";
			$taille = 32;
			$uniteDeTravail_new = $uniteDeTravail_new . EvaDisplayInput::afficherInput('text', $idV, $contenuInputVille, '', $labelInputV, $nomChamps, $grise, ADRESSE_UNITE_TRAVAIL_OBLIGATOIRE, $taille);
		}
		$uniteDeTravail_new = $uniteDeTravail_new . '</div>';
	}
	{//Téléphone
		$labelInput = ucfirst(strtolower(__("T&eacute;l&eacute;phone", 'evarisk'))) . " :";
		$labelInput[1] = ($labelInput[0] == "&")?ucfirst($labelInput[1]):$labelInput[1];
		$idChamps = "telephone";
		$nomChamps = "telephone";
		$taille = 21;
		$uniteDeTravail_new = $uniteDeTravail_new . EvaDisplayInput::afficherInput('text', $idChamps, $contenuInputTelephone, '', $labelInput, $nomChamps, $grise, TELEPHONE_UNITE_TRAVAIL_OBLIGATOIRE, $taille, '', 'Number');
	}
	{//Effectif
		// $labelInput = ucfirst(strtolower(__("&Eacute;ffectif", 'evarisk'))) . " :";
		// $labelInput[1] = ($labelInput[0] == "&")?ucfirst($labelInput[1]):$labelInput[1];
		// $idChamps = "effectif";
		// $nomChamps = "effectif";
		// $taille = 10;
		// $uniteDeTravail_new = $uniteDeTravail_new . EvaDisplayInput::afficherInput('text', $idChamps, $contenuInputEffectif, '', $labelInput, $nomChamps, $grise, EFFECTIF_UNITE_TRAVAIL_OBLIGATOIRE, $taille, '', 'Number');
	}

	{//Bouton enregistrer
		$idBouttonEnregistrer = 'save';
		$valeurActuelleIn = "false";
		$workingUnitsNames = UniteDeTravail::getWorkingUnitsName($saufUniteTravail);
		if(count($workingUnitsNames) != 0)
		{
			$valeurActuelleIn = "valeurActuelle in {";
			foreach($workingUnitsNames as $workingUnitName)
			{
				$valeurActuelleIn = $valeurActuelleIn . "'" . addslashes($workingUnitName) . "':'', ";
			}
			$valeurActuelleIn = $valeurActuelleIn . "}";
		}
		$geolocObligatoire = GEOLOC_OBLIGATOIRE?"true":"false";
		$scriptGeolocalisation = evaGoogleMaps::scriptGeoloc($idBouttonEnregistrer, $id, $idL1, $idL2, $idCP, $idV, "latitude", "longitude");
		{//Script relatif à l'enregistrement
			$scriptEnregistrement = '
			<script type="text/javascript">
				$(document).ready(function() {	
					$(\'#' . $idBouttonEnregistrer . '\').click(function() {
						if($(\'#' . $idTitre . '\').is(".form-input-tip"))
						{
							document.getElementById(\'' . $idTitre . '\').value=\'\';
							$(\'#' . $idTitre . '\').removeClass(\'form-input-tip\');
						}
						
						if(' . $geolocObligatoire . ' && !geolocPossible)
						{
							var message = "' . ucfirst(sprintf(__('Vous devez obligatoirement remplir les champs de l\'adresse suivant : %s, %s et %s', 'evarisk'), substr($labelInputL1, 0, strlen($labelInputL1)-2),  substr($labelInputCP, 0, strlen($labelInputCP)-2),  substr($labelInputV, 0, strlen($labelInputV)-2))) . '.";
							alert(convertAccentToJS(message));
						}
						else
						{
							valeurActuelle = $(\'#' . $idTitre . '\').val();
							if(valeurActuelle == "")
							{
								var message = "' . ucfirst(sprintf(__('Vous n\'avez pas donn&eacute; de nom %s', 'evarisk'), __('&agrave; l\'unit&eacute; de travail', 'evarisk'))) . '";
								alert(convertAccentToJS(message));
							}
							else
							{
								if(' . $valeurActuelleIn . ')
								{
									var message = "' . ucfirst(sprintf(__('%s porte d&eacute;j&agrave; ce nom', 'evarisk'), __('une unit&eacute; de travail', 'evarisk'))) . '";
									alert(convertAccentToJS(message));
								}
								else
								{
									$(\'#act\').val("' . $saveOrUpdate . '");
									$("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
										"table": "' . TABLE_UNITE_TRAVAIL . '",
										"act": $(\'#act\').val(),
										"id": $(\'#id\').val(),
										"nom_unite_travail": $(\'#nom_unite_travail\').val(),
										"groupementPere": $(\'#groupementPere :selected\').val(),
										"description": $(\'#description\').val(),
										"adresse_ligne_1": $(\'#adresse_ligne_1\').val(),
										"adresse_ligne_2": $(\'#adresse_ligne_2\').val(),
										"code_postal": $(\'#code_postal\').val(),
										"ville": $(\'#ville\').val(),
										"telephone": $(\'#telephone\').val(),
										"effectif": $(\'#effectif\').val(),
										"effectif": $(\'#effectif\').val(),
										"affichage": $(\'#affichage\').val(),
										"latitude": $(\'#latitude\').val(),
										"longitude": $(\'#longitude\').val(),
										"idsFilAriane": $(\'#idsFilAriane\').val()
									});
								}
							}
						}
						geolocPossible = true;
					});
				});
			</script>';
		}
		$script = $scriptEnregistrement . $scriptGeolocalisation;
		$uniteDeTravail_new = $uniteDeTravail_new . EvaDisplayInput::afficherInput('button', $idBouttonEnregistrer, __('Enregistrer', 'evarisk'), null, '', 'save', false, false, '', 'button-primary alignright', '', '', $script);
	}
	$uniteDeTravail_new = $uniteDeTravail_new . EvaDisplayInput::fermerForm($idForm);
	echo $uniteDeTravail_new;
}
?>