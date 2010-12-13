<?php
/*
 * @version v5.0
 */
require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php');
 
//Postbox definition
$postBoxTitle = __('Informations G&eacute;n&eacute;rales', 'evarisk');
$postBoxId = 'postBoxGeneralInformation';
$postBoxCallbackFunction = 'getGroupGeneralInformationPostBoxBody';
add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_GROUPEMENTS, 'rightSide', 'default');

function getGroupGeneralInformationPostBoxBody($arguments)
{
	require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/groupement/eva_groupement.class.php' ); 
	require_once(EVA_LIB_PLUGIN_DIR . 'arborescence.class.php' );
	require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayInput.class.php' );
	require_once(EVA_LIB_PLUGIN_DIR . 'evaGoogleMaps.class.php' );
	require_once(EVA_LIB_PLUGIN_DIR . 'adresse/evaAddress.class.php' );

	$postId = '';
	if($arguments['idElement']!=null)
	{	
		$saveOrUpdate = 'update';
		$postId = $arguments['idElement'];
		$groupement = EvaGroupement::getGroupement($arguments['idElement']);
		$saufGroupement = $groupement->nom;
		$contenuInputTitre = $groupement->nom;
		$contenuInputDescription = $groupement->description;
		if($groupement->id_adresse != 0 AND $groupement->id_adresse != null)
		{
			$address = new EvaAddress($groupement->id_adresse);
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
		$contenuInputTelephone = $groupement->telephoneGroupement;
		if($groupement->effectif != 0)
		{
			$contenuInputEffectif = $groupement->effectif;
		}
		else
		{
			$contenuInputEffectif = '';
		}
		$grise = false;
		$groupementPere = Arborescence::getPere(TABLE_GROUPEMENT, $groupement);
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
		$saufGroupement = '';
		$grise = true;
		$groupementPere = EvaGroupement::getGroupement($arguments['idPere']);
	}

	$idForm = 'informationGeneralesGroupement';
	$groupement_new = EvaDisplayInput::ouvrirForm('POST', $idForm, $idForm);
	{//Champs cachés
		$groupement_new = $groupement_new . EvaDisplayInput::afficherInput('hidden', 'act', '', '', null, 'act', false, false);
		$groupement_new = $groupement_new . EvaDisplayInput::afficherInput('hidden', 'latitude', '', '', null, 'latitude', false, false);
		$groupement_new = $groupement_new . EvaDisplayInput::afficherInput('hidden', 'longitude', '', '', null, 'longitude', false, false);
		$groupement_new = $groupement_new . EvaDisplayInput::afficherInput('hidden', 'affichage', $arguments['affichage'], '', null, 'affichage', false, false);
		$groupement_new = $groupement_new . EvaDisplayInput::afficherInput('hidden', 'table', TABLE_GROUPEMENT, '', null, 'table', false, false);
		$groupement_new = $groupement_new . EvaDisplayInput::afficherInput('hidden', 'id', $postId, '', null, 'id', false, false);
		$groupement_new = $groupement_new . EvaDisplayInput::afficherInput('hidden', 'idsFilAriane', $arguments['idsFilAriane'], '', null, 'idsFilAriane', false, false);
	}

	$contenuAideTitre = "";
	$contenuAideDescription = "";
	$contenuAideLigne1 = "";
	$contenuAideLigne2 = "";
	$contenuAideCodePostal = "";
	$contenuAideVille = "";
	$contenuAideTelephone = "";
	$contenuAideEffectif = "";
	{//Nom du groupement
		$labelInput = ucfirst(strtolower(sprintf(__("nom %s", 'evarisk'), __("du groupement", 'evarisk')))) . ' :';
		$labelInput[1] = ($labelInput[0] == "&")?ucfirst($labelInput[1]):$labelInput[1];
		$nomChamps = "nom_groupement";
		$idTitre = "nom_groupement";
		$groupement_new = $groupement_new . EvaDisplayInput::afficherInput('text', $idTitre, $contenuInputTitre, '', $labelInput, $nomChamps, $grise, true, 255, 'titleInput');
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
		$groupement_new = $groupement_new .  '<div style="display:none">';
		$groupement_new = $groupement_new . EvaDisplayInput::afficherComboBoxArborescente($groupementRacine, TABLE_GROUPEMENT, $idSelect, $labelSelect, $nameSelect, $valeurDefaut, $selection);
		$groupement_new = $groupement_new .  '</div>';
	}
	{//Description
		$labelInput = ucfirst(strtolower(__("Description", 'evarisk'))) . " :";
		$labelInput[1] = ($labelInput[0] == "&")?ucfirst($labelInput[1]):$labelInput[1];
		$id = "description";
		$nomChamps = "description";
		$rows = 5;
		$groupement_new = $groupement_new . EvaDisplayInput::afficherInput('textarea', $id, $contenuInputDescription, $contenuAideDescription, $labelInput, $nomChamps, $grise, DESCRIPTION_GROUPEMENT_OBLIGATOIRE, $rows);
	}
	{//Adresse
		$groupement_new = $groupement_new . '<div id="adresseUnite' . $postId . '">';
		{//Ligne 1
			$labelInputL1 = ucfirst(strtolower(__("Adresse ligne 1", 'evarisk'))) . " :";
			$labelInputL1[1] = ($labelInputL1[0] == "&")?ucfirst($labelInputL1[1]):$labelInputL1[1];
			$idL1 = "adresse_ligne_1";
			$nomChamps = "adresse_ligne_1";
			$taille = 32;
			$groupement_new = $groupement_new . EvaDisplayInput::afficherInput('text', $idL1, $contenuInputLigne1, $contenuAideLigne1, $labelInputL1, $nomChamps, $grise, ADRESSE_GROUPEMENT_OBLIGATOIRE, $taille);
		}
		{//Ligne 2
			$labelInputL2 = ucfirst(strtolower(__("Adresse ligne 2", 'evarisk'))) . " :";
			$labelInputL2[1] = ($labelInputL2[0] == "&")?ucfirst($labelInputL2[1]):$labelInputL2[1];
			$idL2 = "adresse_ligne_2";
			$nomChamps = "adresse_ligne_2";
			$taille = 32;
			$groupement_new = $groupement_new . EvaDisplayInput::afficherInput('text', $idL2, $contenuInputLigne2,$contenuAideLigne2,  $labelInputL2, $nomChamps, $grise, false, $taille);
		}
		{//Code Postal
			$labelInputCP = ucfirst(strtolower(__("Code Postal", 'evarisk'))) . " :";
			$labelInputCP[1] = ($labelInputCP[0] == "&")?ucfirst($labelInputCP[1]):$labelInputCP[1];
			$idCP = "code_postal";
			$nomChamps = "code_postal";
			$taille = 5; 
			$groupement_new = $groupement_new . EvaDisplayInput::afficherInput('text', $idCP, $contenuInputCodePostal, $contenuAideCodePostal, $labelInputCP, $nomChamps, $grise, ADRESSE_GROUPEMENT_OBLIGATOIRE, $taille, '', 'Number');
		}
		{//Ville
			$labelInputV = ucfirst(strtolower(__("Ville", 'evarisk'))) . " :";
			$labelInputV[1] = ($labelInputV[0] == "&")?ucfirst($labelInputV[1]):$labelInputV[1];
			$idV = "ville";
			$nomChamps = "ville";
			$taille = 32;
			$groupement_new = $groupement_new . EvaDisplayInput::afficherInput('text', $idV, $contenuInputVille, $contenuAideVille, $labelInputV, $nomChamps, $grise, ADRESSE_GROUPEMENT_OBLIGATOIRE, $taille);
		}
		$groupement_new = $groupement_new . '</div>';
	}
	{//Téléphone
		$labelInput = ucfirst(strtolower(__("T&eacute;l&eacute;phone", 'evarisk'))) . " :";
		$labelInput[1] = ($labelInput[0] == "&")?ucfirst($labelInput[1]):$labelInput[1];
		$id = "telephone";
		$nomChamps = "telephone";
		$taille = 21;
		$groupement_new = $groupement_new . EvaDisplayInput::afficherInput('text', $id, $contenuInputTelephone, $contenuAideTelephone, $labelInput, $nomChamps, $grise, TELEPHONE_GROUPEMENT_OBLIGATOIRE, $taille, '', 'Number');
	}
	{//Effectif
		// $labelInput = ucfirst(strtolower(__("&Eacute;ffectif", 'evarisk'))) . " :";
		// $labelInput[1] = ($labelInput[0] == "&")?ucfirst($labelInput[1]):$labelInput[1];
		// $id = "effectif";
		// $nomChamps = "effectif";
		// $taille = 10;
		// $groupement_new = $groupement_new . EvaDisplayInput::afficherInput('text', $id, $contenuInputEffectif, $contenuAideEffectif, $labelInput, $nomChamps, $grise, EFFECTIF_GROUPEMENT_OBLIGATOIRE, $taille, '', 'Number');
	}

	{//Bouton enregistrer
		$idBouttonEnregistrer = 'save';
		$geolocObligatoire = GEOLOC_OBLIGATOIRE?"true":"false";
		$scriptGeolocalisation = evaGoogleMaps::scriptGeoloc($idBouttonEnregistrer, $postId, $idL1, $idL2, $idCP, $idV, "latitude", "longitude");
		
		$groupsNames = EvaGroupement::getGroupementsName($saufGroupement);
		$valeurActuelleIn = 'false';
		if(count($groupsNames) != 0)
		{
			$valeurActuelleIn = "valeurActuelle in {";
			foreach($groupsNames as $groupName)
			{
				$valeurActuelleIn = $valeurActuelleIn . "'" . addslashes($groupName) . "':'', ";
			}
			$valeurActuelleIn = $valeurActuelleIn . "}";
		}
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
								var message = "' . ucfirst(sprintf(__('Vous n\'avez pas donn&eacute; de nom %s', 'evarisk'), __('au groupement', 'evarisk'))) . '";
								alert(convertAccentToJS(message));
							}
							else
							{
								if(' . $valeurActuelleIn . ')
								{
									var message = "' . ucfirst(sprintf(__('%s porte d&eacute;j&agrave; ce nom', 'evarisk'), __('un groupement', 'evarisk'))) . '";
									alert(convertAccentToJS(message));
								}
								else
								{
									$(\'#act\').val("' . $saveOrUpdate . '");
									$("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
										"table": "' . TABLE_GROUPEMENT . '",
										"act": $(\'#act\').val(),
										"id": $(\'#id\').val(),
										"nom_groupement": $(\'#nom_groupement\').val(),
										"groupementPere": $(\'#groupementPere :selected\').val(),
										"description": $(\'#description\').val(),
										"adresse_ligne_1": $(\'#adresse_ligne_1\').val(),
										"adresse_ligne_2": $(\'#adresse_ligne_2\').val(),
										"code_postal": $(\'#code_postal\').val(),
										"ville": $(\'#ville\').val(),
										"telephone": $(\'#telephone\').val(),
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
		$groupement_new = $groupement_new . EvaDisplayInput::afficherInput('button', $idBouttonEnregistrer, __('Enregistrer', 'evarisk'), null, '', 'save', false, false, '', 'button-primary alignright', '', '', $script);
	}
	$groupement_new = $groupement_new . EvaDisplayInput::fermerForm($idForm);
	echo $groupement_new;
}
?>