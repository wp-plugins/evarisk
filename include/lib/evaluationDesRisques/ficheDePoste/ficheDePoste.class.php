<?php
/**
 *
 * @author Soci&eacute;t&eacute; Evarisk
 * @version v5.0
 */

class eva_WorkUnitSheet
{

	/**
	*	Return the form template for generating a work unit sheet
	*	@return string HTML code of the form
	*/
	function getWorkUnitSheetForm() {
		return
'<table summary="" border="0" cellpadding="0" cellspacing="0" align="center" class="tabcroisillon" style="width:100%;" >
	<tr>
		<td style="width:60%;vertical-align:top;" >
			<table summary="" cellpadding="0" cellspacing="0" border="0" class="tabformulaire" style="width:100%;" >
				<tr>
					<td ><label for="nomFicheDePoste">' . __('nom de la fiche', 'evarisk') . '</label></td>
					<td >' . EvaDisplayInput::afficherInput('text', 'nomFicheDePoste', '#NOMDOCUMENT#', '', '', 'nomFicheDePoste', false, false, 150, '', '', '100%', '', 'left', false) . '</td>
				</tr>
				<tr>
					<td >&nbsp;</td>
					<td style="padding:12px 0px;" >
						<div>
							<input type="checkbox" id="FPmodelDefaut" checked="checked" name="modelUse" value="modeleDefaut" />
							<label for="FPmodelDefaut" style="vertical-align:middle;" >' . __('Utiliser le mod&egrave;le par d&eacute;faut', 'evarisk') . '</label>
						</div>
						<div id="modelListForGeneration" style="display:none;" >&nbsp;</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						' . EvaDisplayInput::afficherInput('hidden', 'nomEntreprise', '#NOMENTREPRISE#', '', '', 'nomEntreprise', false, false, 150, '', '', '100%', '', 'left', false) . '
						<input class="button-primary alignright" type="button" id="genererFP" name="genererFP" value="' . __('g&eacute;n&eacute;rer', 'evarisk') . '" />
					</td>
				</tr>
			</table>
		</td>
		<td style="width:40%;" id="workUnitSheetResultContainer" >&nbsp;</td>
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
	function getWorkUnitSheetGenerationForm($tableElement, $idElement) {
		unset($formulaireDocumentUniqueParams);
		$formulaireDocumentUniqueParams = array();
		$formulaireDocumentUniqueParams['#DATEFORM1#'] = date('Y-m-d');

		$workUnitinformations = eva_UniteDeTravail::getWorkingUnit($idElement);
		$formulaireDocumentUniqueParams['#NOMDOCUMENT#'] = date('Ymd') . '_' . ELEMENT_IDENTIFIER_UT . $idElement . '_' . digirisk_tools::slugify_noaccent(str_replace(' ', '_', $workUnitinformations->nom));
		$groupementPere = EvaGroupement::getGroupement($workUnitinformations->id_groupement);
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
		$formulaireDocumentUniqueParams['#NOMENTREPRISE#'] = digirisk_tools::slugify_noaccent($arborescence) . digirisk_tools::slugify_noaccent($workUnitinformations->nom);

		$modelChoice = '';
		$lastWorkUnitSheet = eva_WorkUnitSheet::getGeneratedDocument($tableElement, $idElement, 'last');
		if(($lastWorkUnitSheet->id_model != '') && ($lastWorkUnitSheet->id_model != eva_gestionDoc::getDefaultDocument('fiche_de_poste')))
		{
			$modelChoice = '
			setTimeout(function(){
				digirisk("#FPmodelDefaut").click();
			},100);';
		}

		$output = EvaDisplayDesign::feedTemplate(eva_WorkUnitSheet::getWorkUnitSheetForm(), $formulaireDocumentUniqueParams) . '
<script type="text/javascript" >
	digirisk(document).ready(function(){
		digirisk("#genererFP").click(function(){
			digirisk("#divImpressionFicheDePoste").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",
			{
				"post":"true",
				"table":"' . TABLE_FP . '",
				"act":"saveFichePoste",
				"tableElement":"' . $tableElement . '",
				"idElement":"' . $idElement . '",
				"nomDuDocument":digirisk("#nomFicheDePoste").val(),
				"nomEntreprise":digirisk("#nomEntreprise").val(),
				"id_model":digirisk("#modelToUse' . $tableElement . '").val()
			});
			digirisk("#divImpressionFicheDePoste").html(digirisk("#loadingImg").html());
		});
		digirisk("#FPmodelDefaut").click(function(){
			clearTimeout();
			setTimeout(function(){
				if(!digirisk("#FPmodelDefaut").is(":checked")){
					digirisk("#workUnitSheetResultContainer").html(\'<img src="' . EVA_IMG_DIVERS_PLUGIN_URL . 'loading.gif" alt="loading" />\');
					digirisk("#workUnitSheetResultContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_DUER . '", "act":"loadNewModelForm", "tableElement":"' . $tableElement . '", "idElement":"' . $idElement . '"});
					digirisk("#modelListForGeneration").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_GED_DOCUMENTS . '", "act":"load_model_combobox", "tableElement":"' . $tableElement . '", "idElement":"' . $idElement . '", "category":"fiche_de_poste", "selection":"' . $lastWorkUnitSheet->id_model . '"});
					digirisk("#modelListForGeneration").show();
				}
				else{
					digirisk("#workUnitSheetResultContainer").html("");
					digirisk("#modelListForGeneration").html("");
					digirisk("#modelListForGeneration").hide();
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
	function getGeneratedDocument($tableElement, $idElement, $type = 'last', $id = '') {
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
			array($idElement, $tableElement, $id)
		);
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

							$documentName = str_replace('-', '', $dateElement[0]) . '_ficheDePoste_' . digirisk_tools::slugify_noaccent(str_replace(' ', '_', $document->societyName)) . '_V' . $document->revisionDUER;

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
										<td>&nbsp;&nbsp;&nbsp;- (' . ELEMENT_IDENTIFIER_FP . $index . ')&nbsp;&nbsp;' . $DUER['name'] . '_' . $DUER['revision'] . '</td>';

								/*	Check if an odt file exist to be downloaded	*/
								$odtFile = 'ficheDePoste/' . $tableElement . '/' . $idElement . '/' . $DUER['fileName'] . '.odt';
								if( is_file(EVA_RESULTATS_PLUGIN_DIR . $odtFile) )
								{
								$outputListeDocumentUnique .= '
									<td><a href="' . EVA_RESULTATS_PLUGIN_URL . $odtFile . '" target="evaFPOdt" >Odt</a></td>';
								}

								$outputListeDocumentUnique .= '
									</tr>';
							}
						}
						$outputListeDocumentUnique .= '
									<tr>
										<td style="padding:18px;" ><a href="' . LINK_TO_DOWNLOAD_OPEN_OFFICE . '" target="OOffice" >' . __('T&eacute;l&eacute;charger Open Office', 'evarisk') . '</a></td>
									</tr>
								</tbody>
							</table>';
					}
				}
				break;
			}
		}
		else
		{
			$outputListeDocumentUnique = '<div class="noResultInBox" >' . __('Aucune fiche de poste n\'a &eacute;t&eacute; g&eacute;n&eacute;r&eacute;e pour le moment', 'evarisk') . '</div>';
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
	function saveWorkUnitSheet($tableElement, $idElement, $informations) {
		$status = array();

		require_once(EVA_LIB_PLUGIN_DIR . 'photo/evaPhoto.class.php');

		global $wpdb;
		$tableElement = digirisk_tools::IsValid_Variable($tableElement);
		$idElement = digirisk_tools::IsValid_Variable($idElement);

		/*	R�vision du document, en fonction de l'element et de la date de g�n�ration	*/
		$revision = '';
		$query = $wpdb->prepare(
			"SELECT max(revision) AS lastRevision
			FROM " . TABLE_FP . "
			WHERE table_element = %s
				AND id_element = %d ",
			$tableElement, $idElement);
		$revision = $wpdb->get_row($query);
		$revisionDocument = $revision->lastRevision + 1;

		/*	G�n�ration de la r�f�rence du document	*/
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

		/*	G�n�ration du nom du document si aucun nom n'a �t� envoy�	*/
		if($informations['nomDuDocument'] == '')
		{
			$dateElement = explode(' ', $informations['dateCreation']);

			$documentName = str_replace('-', '', $dateElement[0]) . '_ficheDePoste_' . digirisk_tools::slugify_noaccent(str_replace(' ', '_', $informations['nomEntreprise']));

			$informations['nomDuDocument'] = $documentName;
		}

		/*	R�cup�ration des informations concernant les utilisateurs et les groupes d'utilisateurs	*/
		$affectedUserTmp = array();
		$affectedUserList = evaUserLinkElement::getAffectedUser($tableElement, $idElement);
		foreach($affectedUserList as $user)
		{
			$affectedUserTmp[] = evaUser::getUserInformation($user->id_user);
		}
		$affectedUser = serialize($affectedUserTmp);
		$affectedUserGroups = serialize(digirisk_groups::getBindGroupsWithInformations($idElement, $tableElement . '_employee'));

		/*	R�cup�ration des informations concernant les �valuateurs et les groupes d'�valuateurs	*/
		$affectedUserTmp = array();
		$affectedUserList = evaUserLinkElement::getAffectedUser($tableElement . '_evaluation', $idElement);
		foreach($affectedUserList as $user)
		{
			$affectedUserTmp[] = evaUser::getUserInformation($user->id_user);
		}
		$affectedEvaluators = serialize($affectedUserTmp);
		$affectedEvaluatorsGroups = serialize(digirisk_groups::getBindGroupsWithInformations($idElement, $tableElement . '_evaluator'));

		/*	R�cup�ration des informations concernant les risques	*/
		$unitRisk = serialize(eva_documentUnique::listRisk($tableElement, $idElement));

		/*	R�cup�ration de la photo par d�faut pour l'unit� de travail	*/
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

		/*	V�rification du mod�le � utiliser pour la g�n�ration de la fiche de poste	*/
		$modelToUse = eva_gestionDoc::getDefaultDocument('fiche_de_poste');
		if(($informations['id_model'] != 'undefined') && ($informations['id_model'] > 0))
		{
			$modelToUse = $informations['id_model'];
		}

		/*	R�cup�ration des pr�conisations affect�es � l'unit� actuelle	*/
		$recommandationList = array();
		$affectedRecommandation = evaRecommandation::getRecommandationListForElement($tableElement, $idElement);
		$i = $oldIdRecommandationCategory = 0;
		foreach($affectedRecommandation as $recommandation)
		{
			if($oldIdRecommandationCategory != $recommandation->recommandation_category_id)
			{
				$i = 0;
				$oldIdRecommandationCategory = $recommandation->recommandation_category_id;
			}
			$recommandationCategoryMainPicture = evaPhoto::getMainPhoto(TABLE_CATEGORIE_PRECONISATION, $recommandation->recommandation_category_id);
			$recommandationCategoryMainPicture = evaPhoto::checkIfPictureIsFile($recommandationCategoryMainPicture, TABLE_CATEGORIE_PRECONISATION);
			if($recommandationCategoryMainPicture != false)
			{
				$recommandationList[$recommandation->recommandation_category_id][$i]['recommandation_category_photo'] = str_replace(EVA_HOME_URL, '', str_replace(EVA_GENERATED_DOC_URL, '', $recommandationCategoryMainPicture));
			}
			else
			{
				$recommandationList[$recommandation->recommandation_category_id][$i]['recommandation_category_photo'] = 'noDefaultPicture';
			}
			$recommandationList[$recommandation->recommandation_category_id][$i]['id_preconisation'] = $recommandation->id_preconisation;
			$recommandationList[$recommandation->recommandation_category_id][$i]['efficacite'] = $recommandation->efficacite;
			$recommandationList[$recommandation->recommandation_category_id][$i]['commentaire'] = $recommandation->commentaire;
			$recommandationList[$recommandation->recommandation_category_id][$i]['recommandation_category_name'] = $recommandation->recommandation_category_name;
			$recommandationList[$recommandation->recommandation_category_id][$i]['recommandation_name'] = $recommandation->recommandation_name;
			$recommandationList[$recommandation->recommandation_category_id][$i]['impressionRecommandationCategorie'] = $recommandation->impressionRecommandationCategorie;
			$recommandationList[$recommandation->recommandation_category_id][$i]['tailleimpressionRecommandationCategorie'] = $recommandation->tailleimpressionRecommandationCategorie;
			$recommandationList[$recommandation->recommandation_category_id][$i]['impressionRecommandation'] = $recommandation->impressionRecommandation;
			$recommandationList[$recommandation->recommandation_category_id][$i]['tailleimpressionRecommandation'] = $recommandation->tailleimpressionRecommandation;
			$recommandationList[$recommandation->recommandation_category_id][$i]['photo'] = $recommandation->photo;
			$i++;
		}
		$recommandation = serialize($recommandationList);

		/*	Enregistrement du document	*/
		$query = $wpdb->prepare(
			"INSERT INTO " . TABLE_FP . "
				(id, creation_date, revision, id_element, id_model, table_element, reference, name, description, adresse, telephone, defaultPicturePath, societyName, users, userGroups, evaluators, evaluatorsGroups, unitRisk, recommandation)
			VALUES
				('', %s, %d, %d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)"
			, array(current_time('mysql', 0), $revisionDocument, $idElement, $modelToUse, $tableElement, $referenceDocument, $informations['nomDuDocument'], $informations['description'], $informations['adresse'], $informations['telephone'], $defaultPictureToSet, digirisk_tools::slugify_noaccent($informations['nomEntreprise']), $affectedUser, $affectedUserGroups, $affectedEvaluators, $affectedEvaluatorsGroups, $unitRisk, $recommandation)
		);
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
			eva_gestionDoc::generateSummaryDocument($tableElement, $idElement, 'odt');
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
	function getWorkUnitSheetCollectionGenerationForm($tableElement, $idElement) {
		$tableElementForDoc = $tableElement . '_FP';
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
			<input type="button" class="clear button-primary" value="' . __('G&eacute;n&eacute;rer les fiches de postes', 'evarisk') . '" id="saveWorkUnitSheetForGroupement" />
		</td>
		<td id="documentModelContainer" >&nbsp;</td>
	</tr>
</table>
<script type="text/javascript" >
	digirisk("#saveWorkUnitSheetForGroupement").click(function() {
		digirisk("#documentFormContainer").html(digirisk("#loadingImg").html());
		digirisk("#documentFormContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
			"post":"true",
			"table":"' . TABLE_FP . '",
			"act":"saveWorkUnitSheetForGroupement",
			"tableElement":"' . $tableElement . '",
			"idElement":' . $idElement . ',
			"id_model":digirisk("#modelToUse' . $tableElementForDoc . '").val()
		});
	});

	digirisk("#modelDefaut").click(function(){
		setTimeout(function(){
			if (!digirisk("#modelDefaut").is(":checked")) {
				digirisk("#documentModelContainer").html(\'<img src="' . EVA_IMG_DIVERS_PLUGIN_URL . 'loading.gif" alt="loading" />\');
				digirisk("#documentModelContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_DUER . '", "act":"loadNewModelForm", "tableElement":"' . $tableElementForDoc . '", "idElement":"' . $idElement . '"});
				digirisk("#modelListForGeneration").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_GED_DOCUMENTS . '", "act":"load_model_combobox", "tableElement":"' . $tableElementForDoc . '", "idElement":"' . $idElement . '", "category":"fiche_de_poste", "selection":""});
				digirisk("#modelListForGeneration").show();
			}
			else {
				digirisk("#documentModelContainer").html("");
				digirisk("#modelListForGeneration").html("");
				digirisk("#modelListForGeneration").hide();
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
	function getWorkUnitSheetCollectionHistory($tableElement, $idElement)
	{
		$output = '';

		$ficheDePoste_du_Groupement = eva_gestionDoc::getDocumentList($tableElement, $idElement, 'fiche_de_poste_groupement', "dateCreation DESC");
		if(count($ficheDePoste_du_Groupement) > 0)
		{
			foreach($ficheDePoste_du_Groupement as $fdpGpt)
			{
				if(is_file(EVA_GENERATED_DOC_DIR . $fdpGpt->chemin . $fdpGpt->nom))
				{
					$output .= '-&nbsp;' . sprintf(__('G&eacute;n&eacute;r&eacute; le %s: (%s) <a href="%s" >%s</a>', 'evarisk'), mysql2date('d M Y', $fdpGpt->dateCreation, true), ELEMENT_IDENTIFIER_GFP . $fdpGpt->id, EVA_GENERATED_DOC_URL . $fdpGpt->chemin . $fdpGpt->nom, $fdpGpt->nom) . '<br/>';
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