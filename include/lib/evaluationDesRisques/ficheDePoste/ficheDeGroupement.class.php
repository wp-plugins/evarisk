<?php
/**
* Plugin group manager
* 
*	Define the different method to manage the group into the plugin
* @author Evarisk <dev@evarisk.com>
* @version 5.1.3.2
* @package Digirisk
* @subpackage librairies
*/


/**
* Define the different method to manage the group into the plugin
* @package Digirisk
* @subpackage librairies
*/
class eva_GroupSheet
{

	/**
	*	Return the form template for generating a work unit sheet
	*	@return string HTML code of the form
	*/
	function getGroupSheetForm()
	{
		return 
'<table summary="" border="0" cellpadding="0" cellspacing="0" align="center" class="tabcroisillon" style="width:100%;" >
	<tr>
		<td style="width:60%;vertical-align:top;" >
			<table summary="" cellpadding="0" cellspacing="0" border="0" class="tabformulaire" style="width:100%;" >
				<tr>
					<td ><label for="nomFicheDeGroupement">' . __('nom de la fiche', 'evarisk') . '</label></td>
					<td >' . EvaDisplayInput::afficherInput('text', 'nomFicheDeGroupement', '#NOMDOCUMENT#', '', '', 'nomFicheDeGroupement', false, false, 150, '', '', '100%', '', 'left;width:100%;', false) . '</td>
				</tr>
				<tr>
					<td >&nbsp;</td>
					<td style="padding:12px 0px;" >
						<div>
							<input type="checkbox" id="FGPmodelDefaut" checked="checked" name="modelUse" value="modeleDefaut" />
							<label for="FGPmodelDefaut" style="vertical-align:middle;" >' . __('Utiliser le mod&egrave;le par d&eacute;faut', 'evarisk') . '</label>
						</div>
						<div id="modelListForGeneration" style="display:none;" >&nbsp;</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						' . EvaDisplayInput::afficherInput('hidden', 'nomEntreprise', '#NOMENTREPRISE#', '', '', 'nomEntreprise', false, false, 150, '', '', '100%', '', 'left', false) . '
						<input class="button-primary alignright" type="button" id="genereFGP" name="genereFGP" value="' . __('G&eacute;n&eacute;rer la fiche du groupement', 'evarisk') . '" />
						<input class="button-primary alignright" type="button" id="genereFSGP" name="genereFSGP" value="' . __('G&eacute;n&eacute;rer les fiches des sous-groupements', 'evarisk') . '" />
					</td>
				</tr>
			</table>
		</td>
		<td style="width:40%;" id="GroupSheetResultContainer" >&nbsp;</td>
	</tr>
</table>';
	}

	/**
	*	Output a form with the different field needed to save and generate a new document
	*
	*	@param mixed $tableElement The element type we want to generate a document for
	*	@param integer $idElement The element identifier we want to generate a document for
	*
	*	@return mixed The complete html output of the form
	*/
	function getGroupSheetGenerationForm($tableElement, $idElement)
	{
		$tableElementForDoc = $tableElement . '_FGP';
		unset($formulaireDocumentUniqueParams);
		$formulaireDocumentUniqueParams = array();
		$formulaireDocumentUniqueParams['#DATEFORM1#'] = date('Y-m-d');

		$groupInformations = EvaGroupement::getGroupement($idElement);
		$formulaireDocumentUniqueParams['#NOMDOCUMENT#'] = date('Ymd') . '_GP' . $idElement . '_' . eva_tools::slugify_noaccent(str_replace(' ', '_', $groupInformations->nom));
		$groupementPere = EvaGroupement::getGroupement($groupInformations->id_groupement);
		$ancetres = Arborescence::getAncetre(TABLE_GROUPEMENT, $groupementPere);
		$arborescence = '';
		foreach($ancetres as $ancetre)
		{
			if($ancetre->nom != "Groupement Racine")
			{
				$arborescence .= $ancetre->nom . ' - ';
			}
		}
		if($groupementPere->nom != "Groupement Racine")
		{
			$arborescence .= $groupementPere->nom . ' - ';
		}
		$formulaireDocumentUniqueParams['#NOMENTREPRISE#'] = eva_tools::slugify_noaccent($arborescence) . eva_tools::slugify_noaccent($groupInformations->nom);

		$modelChoice = '';
		$lastGroupSheet = eva_GroupSheet::getGeneratedDocument($tableElement, $idElement, 'last');
		if(($lastGroupSheet->id_model != '') && ($lastGroupSheet->id_model != eva_gestionDoc::getDefaultDocument('fiche_de_groupement')))
		{
			$modelChoice = '
			setTimeout(function(){
				evarisk("#FGPmodelDefaut").click();
			},100);';
		}

		$output = EvaDisplayDesign::feedTemplate(eva_GroupSheet::getGroupSheetForm(), $formulaireDocumentUniqueParams) . '
<script type="text/javascript" >
	evarisk(document).ready(function(){
		evarisk("#genereFGP").click(function(){
			evarisk("#bilanBoxContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",
			{
				"post":"true",
				"table":"' . TABLE_FP . '",
				"act":"saveFicheGroupement",
				"tableElement":"' . $tableElement . '",
				"idElement":"' . $idElement . '",
				"nomDuDocument":evarisk("#nomFicheDeGroupement").val(),
				"nomEntreprise":evarisk("#nomEntreprise").val(),
				"id_model":evarisk("#modelToUse' . $tableElementForDoc . '").val()
			});
			evarisk("#bilanBoxContainer").html(evarisk("#loadingImg").html());
		});
		evarisk("#genereFSGP").click(function(){
			evarisk("#bilanBoxContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",
			{
				"post":"true",
				"table":"' . TABLE_FP . '",
				"act":"saveGroupSheetForGroupement",
				"tableElement":"' . $tableElement . '",
				"idElement":"' . $idElement . '",
				"nomDuDocument":evarisk("#nomFicheDeGroupement").val(),
				"nomEntreprise":evarisk("#nomEntreprise").val(),
				"id_model":evarisk("#modelToUse' . $tableElementForDoc . '").val()
			});
			evarisk("#bilanBoxContainer").html(evarisk("#loadingImg").html());
		});
		evarisk("#FGPmodelDefaut").click(function(){
			clearTimeout();
			setTimeout(function(){
				if(!evarisk("#FGPmodelDefaut").is(":checked")){
					evarisk("#GroupSheetResultContainer").html(\'<img src="' . EVA_IMG_DIVERS_PLUGIN_URL . 'loading.gif" alt="loading" />\');
					evarisk("#GroupSheetResultContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_DUER . '", "act":"loadNewModelForm", "tableElement":"' . $tableElementForDoc . '", "idElement":"' . $idElement . '"});
					evarisk("#modelListForGeneration").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_GED_DOCUMENTS . '", "act":"loadDocument", "tableElement":"' . $tableElementForDoc . '", "idElement":"' . $idElement . '", "category":"fiche_de_groupement", "selection":"' . $lastGroupSheet->id_model . '"});
					evarisk("#modelListForGeneration").show();
				}
				else{
					evarisk("#GroupSheetResultContainer").html("");
					evarisk("#modelListForGeneration").html("");
					evarisk("#modelListForGeneration").hide();
				}
			},500
			);
		});
		' . $modelChoice . '
	});
</script>';

		return $output;
	}

	/**
	*	Get the last document generated for a given element
	*
	*	@param mixed $tableElement The element type we want to get the last document for
	*	@param integer $idElement The element identifier we want to get the lat document for
	*
	*	@return mixed $lastDocument An object with all information about the last document
	*/
	function getGeneratedDocument($tableElement, $idElement, $type = 'last', $id = '')
	{
		global $wpdb;
		$lastDocument = array();

		$queryOrder = "";
		switch($type)
		{
			case 'last':
				$queryOrder = "
				ORDER BY id DESC
			LIMIT 1";
			break;
			case 'list':
				$queryOrder = "
				ORDER BY creation_date DESC, revision DESC";
			break;
		}

		$query = $wpdb->prepare(
			"SELECT *
			FROM " . TABLE_FP . "
			WHERE id_element = %d
				AND table_element = %s " . $queryOrder,
			array($idElement, $tableElement, $id));
		if($id != '')
		{
			$query = $wpdb->prepare(
				"SELECT *
				FROM " . TABLE_FP . "
				WHERE id_element = %d
					AND table_element = %s 
					AND id = %d " . $queryOrder,
					array($idElement, $tableElement, $id)
			);
		}
		$lastDocument = $wpdb->get_results($query);

		if( count($lastDocument) > 0 )
		{
			switch($type)
			{
				case 'last':
					$outputListeDocumentUnique = $wpdb->get_row($query);
				break;
				case 'list':
				{
					$listeParDate = array();
					foreach($lastDocument as $index => $document)
					{
						$dateElement = explode(' ', $document->creation_date);
						if($document->name == '')
						{

							$documentName = str_replace('-', '', $dateElement[0]) . '_ficheDePoste_' . eva_tools::slugify_noaccent(str_replace(' ', '_', $document->societyName)) . '_V' . $document->revisionDUER;

							$document->name = $documentName;
						}
						$listeParDate[$dateElement[0]][$document->id]['name'] = $document->name;
						$listeParDate[$dateElement[0]][$document->id]['fileName'] = $document->name . '_V' . $document->revision;
						$listeParDate[$dateElement[0]][$document->id]['revision'] = 'V' . $document->revision;
					}

					if( count($listeParDate) > 0 )
					{
						$outputListeDocumentUnique .= 
							'<table summary="" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;" >
								<thead></thead>
								<tfoot></tfoot>
								<tbody>';
						foreach($listeParDate as $date => $listeDUDate)
						{
							$outputListeDocumentUnique .= '
									<tr>
										<td colspan="3" style="text-decoration:underline;font-weight:bold;" >Le ' . mysql2date('d M Y', $date, true) . '</td>
									</tr>';
							foreach($listeDUDate as $index => $DUER)
							{
								$outputListeDocumentUnique .= '
									<tr>
										<td>&nbsp;&nbsp;&nbsp;- ' . $DUER['name'] . '_' . $DUER['revision'] . '</td>';

								/*	Check if an odt file exist to be downloaded	*/
								$odtFile = 'ficheDeGroupement/' . $tableElement . '/' . $idElement . '/' . $DUER['fileName'] . '.odt';
								if( is_file(EVA_RESULTATS_PLUGIN_DIR . $odtFile) )
								{
								$outputListeDocumentUnique .= '
									<td><a href="' . EVA_RESULTATS_PLUGIN_URL . $odtFile . '" target="evaFGPOdt" >Odt</a></td>';
								}

								$outputListeDocumentUnique .= '
									</tr>';
							}
						}
						$outputListeDocumentUnique .= '
								</tbody>
							</table>';
					}
				}
				break;
			}
		}
		else
		{
			$outputListeDocumentUnique = '<div class="noResultInBox" >' . __('Aucune fiche de groupement n\'a &eacute;t&eacute; g&eacute;n&eacute;r&eacute;e pour le moment', 'evarisk') . '</div>';
		}

		return $outputListeDocumentUnique;
	}

	/**
	*	Save a new "work unit sheet" in database
	*
	*	@param mixed $tableElement The element type we want to save a new document for
	*	@param integer $idElement The element identifier we want to save a new document for
	*	@param array $informations An array with all information to create the new document. Those informations come from the form
	*
	*	@return array $status An array with the response status, if it's ok or not
	*/
	function saveGroupSheet($tableElement, $idElement, $informations)
	{
		global $wpdb;
		$status = array();

		require_once(EVA_LIB_PLUGIN_DIR . 'photo/evaPhoto.class.php');

		$tableElement = eva_tools::IsValid_Variable($tableElement);
		$idElement = eva_tools::IsValid_Variable($idElement);

		/*	Révision du document, en fonction de l'element et de la date de génération	*/
		$revision = '';
		$query = $wpdb->prepare(
			"SELECT max(revision) AS lastRevision
			FROM " . TABLE_FP . " 
			WHERE table_element = %s
				AND id_element = %d ",
			$tableElement, $idElement);
		$revision = $wpdb->get_row($query);
		$revisionDocument = $revision->lastRevision + 1;

		/*	Génération de la référence du document	*/
		switch($tableElement)
		{
			case TABLE_GROUPEMENT:
				$element = 'gpt';
			break;
			case TABLE_UNITE_TRAVAIL:
				$element = 'ut';
			break;
			default:
				$element = $tableElement;
			break;
		}
		$referenceDocument = str_replace('-', '', $informations['dateCreation']) . '-' . $element . $idElement . '-V' . $revisionDocument;

		/*	Génération du nom du document si aucun nom n'a été envoyé	*/
		if($informations['nomDuDocument'] == '')
		{
			$dateElement = explode(' ', $informations['dateCreation']);

			$documentName = str_replace('-', '', $dateElement[0]) . '_ficheDePoste_' . eva_tools::slugify_noaccent(str_replace(' ', '_', $informations['nomEntreprise']));

			$informations['nomDuDocument'] = $documentName;
		}

		/*	Récupération des informations concernant les utilisateurs et les groupes d'utilisateurs	*/
		$affectedUserTmp = array();
		$affectedUserList = evaUserLinkElement::getAffectedUser($tableElement, $idElement);
		foreach($affectedUserList as $user)
		{
			$affectedUserTmp[] = evaUser::getUserInformation($user->id_user);
		}
		$affectedUser = serialize($affectedUserTmp);
		$affectedUserGroups = serialize(digirisk_groups::getBindGroupsWithInformations($idElement, $tableElement . '_employee'));

		/*	Récupération des informations concernant les évaluateurs et les groupes d'évaluateurs	*/
		$affectedUserTmp = array();
		$affectedUserList = evaUserLinkElement::getAffectedUser($tableElement . '_evaluation', $idElement);
		foreach($affectedUserList as $user)
		{
			$affectedUserTmp[] = evaUser::getUserInformation($user->id_user);
		}
		$affectedEvaluators = serialize($affectedUserTmp);
		$affectedEvaluatorsGroups = serialize(digirisk_groups::getBindGroupsWithInformations($idElement, $tableElement . '_evaluator'));

		/*	Récupération des informations concernant les risques	*/
		$unitRisk = serialize(eva_documentUnique::listRisk($tableElement, $idElement));

		/*	Récupération de la photo par défaut pour l'unité de travail	*/
		$defaultPicture = evaPhoto::getMainPhoto($tableElement, $idElement);
		$defaultPictureToSet = '';
		if($defaultPicture != 'error')
		{
			$defaultPictureToSet = $defaultPicture;
		}
		else
		{
			$defaultPictureToSet = 'noDefaultPicture';
		}

		/*	Vérification du modèle à utiliser pour la génération de la fiche de groupement	*/
		$modelToUse = eva_gestionDoc::getDefaultDocument('fiche_de_groupement');
		if(($informations['id_model'] != 'undefined') && ($informations['id_model'] > 0))
		{
			$modelToUse = $informations['id_model'];
		}

		/*	Enregistrement du document	*/
		$query = $wpdb->prepare("INSERT INTO " . TABLE_FP . " 
				(id, creation_date, revision, id_element, id_model, table_element, reference, name, defaultPicturePath, societyName, users, userGroups, evaluators, evaluatorsGroups, unitRisk) 
			VALUES 
				('', NOW(), %d, %d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)"
			, array($revisionDocument, $idElement, $modelToUse, $tableElement, $referenceDocument, $informations['nomDuDocument'], $defaultPictureToSet, eva_tools::slugify_noaccent($informations['nomEntreprise']), $affectedUser, $affectedUserGroups, $affectedEvaluators, $affectedEvaluatorsGroups, $unitRisk));
		if($wpdb->query($query) === false)
		{
			$status['result'] = 'error'; 
			$status['errors']['query_error'] = __('Une erreur est survenue lors de l\'enregistrement', 'evarisk');
			$status['errors']['query'] = $query;
		}
		else
		{
			$status['result'] = 'ok';
			/*	Save the odt file	*/
			eva_gestionDoc::generateSummaryDocument($tableElement . '_FGP', $idElement, 'odt');
		}

		return $status;
	}

	/**
	*	Generate a form to save work unit sheet collection for a groupment
	*
	*	@param mixed $tableElement The element type we want to get form for
	*	@param integer $idElement The element identifier we wan to get form for
	*
	*	@return string The hmtl code outputing the form to generate work unit sheet collection for a groupment
	*/
	function getGroupSheetCollectionGenerationForm($tableElement, $idElement)
	{
		$tableElementForDoc = $tableElement . '_FGP';
		$output = '
<table summary="" border="0" cellpadding="0" cellspacing="0" align="center" class="tabcroisillon" style="width:100%;" >
	<tr>
		<td id="documentFormContainer" >
			<div id="workUnitSheetCollectionModelSelector" >
				<div>
					<input type="checkbox" id="modelDefaut" checked="checked" name="modelUse" value="modeleDefaut" />
					<label for="modelDefaut" style="vertical-align:middle;" >' . __('Utiliser le mod&egrave;le par d&eacute;faut', 'evarisk') . '</label>
				</div>
				<div id="modelListForGeneration" style="display:none;" >&nbsp;</div>
			</div>
			<input type="button" class="clear button-primary" value="' . __('G&eacute;n&eacute;rer les fiches de postes', 'evarisk') . '" id="saveGroupSheetForGroupement" />  
		</td>
		<td id="documentModelContainer" >&nbsp;</td>
	</tr>
</table>
<script type="text/javascript" >
	evarisk("#saveGroupSheetForGroupement").click(function(){
		evarisk("#documentFormContainer").html(evarisk("#loadingImg").html());
		evarisk("#documentFormContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
		{
			"post":"true",
			"table":"' . TABLE_FP . '",
			"act":"saveGroupSheetForGroupement",
			"tableElement":"' . $tableElement . '",
			"idElement":' . $idElement . ',
			"id_model":evarisk("#modelToUse' . $tableElementForDoc . '").val()
		});
	});

	evarisk("#modelDefaut").click(function(){
		setTimeout(function(){
			if(!evarisk("#modelDefaut").is(":checked"))
			{
				evarisk("#documentModelContainer").html(\'<img src="' . EVA_IMG_DIVERS_PLUGIN_URL . 'loading.gif" alt="loading" />\');
				evarisk("#documentModelContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_DUER . '", "act":"loadNewModelForm", "tableElement":"' . $tableElementForDoc . '", "idElement":"' . $idElement . '"});
				evarisk("#modelListForGeneration").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_GED_DOCUMENTS . '", "act":"loadDocument", "tableElement":"' . $tableElementForDoc . '", "idElement":"' . $idElement . '", "category":"fiche_de_groupement", "selection":""});
				evarisk("#modelListForGeneration").show();
			}
			else
			{
				evarisk("#documentModelContainer").html("");
				evarisk("#modelListForGeneration").html("");
				evarisk("#modelListForGeneration").hide();
			}
		},600);
	});
</script>';

		return $output;
	}

	/**
	*	Get the history of work unit sheet generated for a given element
	*
	*	@param mixed $tableElement The element type we want to get form for
	*	@param integer $idElement The element identifier we wan to get form for
	*
	*	@return string The html code output with the list of document or a message saying there no document for this element
	*/
	function getGroupSheetCollectionHistory($tableElement, $idElement)
	{
		$output = '';

		$list_FicheDePoste_du_Groupement = eva_gestionDoc::getDocumentList($tableElement, $idElement, 'fiches_de_groupement', "dateCreation DESC");
		if(count($list_FicheDePoste_du_Groupement) > 0)
		{
			foreach($list_FicheDePoste_du_Groupement as $fdpGpt)
			{
				if(is_file(EVA_GENERATED_DOC_DIR . $fdpGpt->chemin . $fdpGpt->nom))
				{
					$output .= '-&nbsp;' . sprintf(__('G&eacute;n&eacute;r&eacute; le %s: <a href="%s" >%s</a>', 'evarisk'), mysql2date('d M Y', $fdpGpt->dateCreation, true), EVA_GENERATED_DOC_URL . $fdpGpt->chemin . $fdpGpt->nom, $fdpGpt->nom) . '<br/>';
				}
			}
		}
		else
		{
			$output .= __('Aucune fiche n\'a &eacute;t&eacute; cr&eacute;e pour le moment', 'evarisk');
		}

		return $output;
	}
	
}