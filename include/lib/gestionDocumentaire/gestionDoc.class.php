<?php
/**
* Plugin document manager
*
*	Define the different method to manage the different document into the plugin
* @author Evarisk <dev@evarisk.com>
* @version 5.0
* @package Digirisk
* @subpackage librairies
*/


/**
* Define the different method to manage the different document into the plugin
* @package Digirisk
* @subpackage librairies
*/
class eva_gestionDoc {
	/**
	* Return an upload form
	*
	* @param string $tableElement Table of the element which is the photo relative to.
	* @param int $idElement Identifier in the table of the element which is the photo relative to.
	* @param string $repertoireDestination Repository of the uploaded file.
	* @param string $idUpload HTML div identifier.
	* @param string $allowedExtensions Allowed extensions for the upload (ex:"['jpeg','png']"). All extensions is written "[]".
	* @param bool $multiple Can the user upload multiple files in one time ?
	* @param string $actionUpload The url of the file call when the user press on upload button.
	*
	* @return string The upload form with eventually a thumbnail.
	*/
	function getFormulaireUpload($table, $tableElement, $idElement, $repertoireDestination, $idUpload, $allowedExtensions, $multiple, $actionUpload, $texteBoutton = ''){
		require_once(EVA_LIB_PLUGIN_DIR . 'upload.php' );

		$texteBoutton = ($texteBoutton == '') ? __("Envoyer un fichier", "evarisk") : $texteBoutton;
		$actionUpload = ($actionUpload == '') ? EVA_INC_PLUGIN_URL . 'gestionDocumentaire/uploadFile.php' : $actionUpload;
		$repertoireDestination = ($repertoireDestination == '') ? str_replace('\\', '/', EVA_UPLOADS_PLUGIN_DIR . $tableElement . '/' . $idElement . '/') : $repertoireDestination;
		$multiple = $multiple ? 'true' : 'false';

		$formulaireUpload =
			'<script type="text/javascript">
				digirisk(document).ready(function(){
					var uploader' . $idUpload . ' = new qq.FileUploader({
						element: document.getElementById("' . $idUpload . '"),
						action: "' . $actionUpload . '",
						allowedExtensions: ' . $allowedExtensions . ',
						multiple: ' . $multiple . ',
						params: {
							"table": "' . $table . '",
							"tableElement": "' . $tableElement . '",
							"idElement": "' . $idElement . '",
							"folder": "' . $repertoireDestination . '"
						},
						onComplete: function(file, response){
							digirisk(".qq-upload-list").hide();';

		switch($table){
			case TABLE_ACTIVITE:
			case TABLE_TACHE:
				$formulaireUpload .= '
							jQuery(".digi_correctiv_action_document_list").html(jQuery("#loadingImg").html());
							jQuery(".digi_correctiv_action_document_list").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_GED_DOCUMENTS . '", "act":"load_associated_document_list", "tableElement":"' . $tableElement . '", "idElement":' . $idElement . ', "category":"' . $tableElement . '"});';
			break;
			case TABLE_DUER:
				$formulaireUpload .= '
							digirisk("#modelListForGeneration").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_GED_DOCUMENTS . '", "act":"load_model_combobox", "tableElement":"' . $tableElement . '", "idElement":' . $idElement . ', "category":"document_unique"});
							digirisk("#moreModelChoice").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_GED_DOCUMENTS . '", "act":"loadExistingDocument", "tableElement":"' . $tableElement . '", "idElement":' . $idElement . '});';
			break;
			case TABLE_FP:
				$category = 'fiche_de_poste';
				if($tableElement == TABLE_GROUPEMENT . '_FGP'){
					$category = 'fiche_de_groupement';
				}
				$formulaireUpload .= '
							digirisk("#modelListForGeneration").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_GED_DOCUMENTS . '", "act":"load_model_combobox", "tableElement":"' . $tableElement . '", "idElement":' . $idElement . ', "category":"' . $category . '"});
							digirisk("#moreModelChoice").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_GED_DOCUMENTS . '", "act":"loadExistingDocument", "tableElement":"' . $tableElement . '", "idElement":' . $idElement . '});';
			break;
		}

		$formulaireUpload .=
						'}
					});

					digirisk("#' . $idUpload . ' .qq-upload-button").each(function(){
						digirisk(this).html("' . $texteBoutton . '");
						uploader' . $idUpload . '._button = new qq.UploadButton({
							element: uploader' . $idUpload . '._getElement("button"),
							multiple: ' . $multiple . ',
							onChange: function(input){
								uploader' . $idUpload . '._onInputChange(input);
							}
						});
					});
					digirisk(".qq-upload-drop-area").each(function(){
						digirisk(this).html("<span>' . __("D&eacute;poser les fichiers ici pour les t&eacute;l&eacute;charger", "evarisk") . '</span>");
					});
					setTimeout(function(){
						digirisk(".qq-upload-button").width("100%");
					}
					, "300");
				});
			</script>
			<div id="' . $idUpload . '" class="divUpload">
				<noscript>
					<p>' . __("Vous devez activer le javascript pour pouvoir envoyer un fichier", "evarisk") . '</p>
				</noscript>
			</div>';

		return $formulaireUpload;
	}

	/**
	*	Save a new document into database
	*
	*	@param string $table Used to determine the category fo the new document
	*	@param string $tableElement The type of the element the document will be associated to
	*	@param integer $idElement The identifier of the element the document will be associated to
	*	@param string $fichier The complete path of the file
	*
	*	@return string $result The result of the saving
	*/
	function saveNewDoc($table, $tableElement, $idElement, $fichier)
	{
		global $wpdb;
		global $current_user;
		$result = 'error';

		/*	Determination of the file category	*/
		switch($table)
		{
			case TABLE_DUER:
				$categorie = 'document_unique';
			break;
			case TABLE_FP:
				$categorie = 'fiche_de_poste';
				if($tableElement == TABLE_GROUPEMENT . '_FGP')
				{
					$categorie = 'fiche_de_groupement';
				}
			break;
			default:
				$categorie = $table;
			break;
		}

		/*	Determination of the file name	*/
		$nomDocument = basename($fichier);

		/*	Determination of the file directory	*/
		$cheminDocument = str_replace(str_replace('\\', '/', EVA_GENERATED_DOC_DIR), '', dirname($fichier)) . '/';

		$query = $wpdb->prepare(
			"INSERT INTO " . TABLE_GED_DOCUMENTS . "
				(id, status, dateCreation, idCreateur, id_element, table_element, categorie, nom, chemin)
			VALUES
				('', 'valid', %s, %d, %d, %s, %s, %s, %s)",
			current_time('mysql', 0), $current_user->ID, $idElement, $tableElement, $categorie, $nomDocument, $cheminDocument);
		if($wpdb->query($query)){
			$last_insert_document = $wpdb->insert_id;
			switch($tableElement){
				case TABLE_ACTIVITE:
				case TABLE_TACHE:
					/*	Log modification on element and notify user if user subscribe	*/
					digirisk_user_notification::log_element_modification($tableElement, $idElement, 'doc_add', '', $last_insert_document);
				break;
			}

			$result = 'ok';
		}

		return $result;
	}

	/**
	*	Create a new document from another existing one
	*
	*	@param string $tableElement The type of the element to associate the duplicated document to
	*	@param integer $idElement The identifier of the element to associate the duplicated document to
	*	@param integer $idDocument The identifier of the document to duplicate
	*
	*	@return string $result The result of the duplication
	*/
	function duplicateDocument($tableElement, $idElement, $idDocument)
	{
		global $wpdb;
		$result = '';

		/*	Preparing the filed list to duplicate the document	*/
		$query = $wpdb->prepare(
			"SHOW COLUMNS FROM " . TABLE_GED_DOCUMENTS
		);
		$columnList = $wpdb->get_results($query);
		$columns = "  ";
		foreach($columnList as $column)
		{
			if($column->Key != 'PRI')
			{
				$columns .= $column->Field . ", ";
			}
		}

		$columns = substr($columns, 0, -2);
		if(trim($columns) != "")
		{
			$query = $wpdb->prepare(
				"INSERT INTO " . TABLE_GED_DOCUMENTS . "
				SELECT '', " . $columns . " FROM " . TABLE_GED_DOCUMENTS . " WHERE id = '%s' ",
				$idDocument
			);
			if($wpdb->query($query))
			{
				$query = $wpdb->prepare(
					"UPDATE " . TABLE_GED_DOCUMENTS . " SET table_element = '%s', id_element = '%s' WHERE id = '%s' ",
					$tableElement, $idElement, $wpdb->insert_id);
				if($wpdb->query($query))
				{
					$result = '<script type="text/javascript" >digirisk("#modelListForGeneration").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_GED_DOCUMENTS . '", "act":"load_model_combobox", "tableElement":"' . $tableElement . '", "idElement":' . $idElement . ', "category":"document_unique"});digirisk("#moreModelChoice").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_GED_DOCUMENTS . '", "act":"loadExistingDocument", "tableElement":"' . $tableElement . '", "idElement":' . $idElement . '});</script>';
				}
			}
		}

		return $result;
	}

	/**
	*	Return the associated document list for a given element
	*
	*	@param string $tableElement The type of the element we want to get the document list for
	*	@param integer $idElement The identifier of the element we want to get the document list for
	*	@param string $category optionnal The category of document to get
	*	@param string $order optionnal Allow to specify in which order the document will be returned
	*
	*	@return array|object $documentList Return the document list for the selected element in case that there are document associated
	*/
	function getDocumentList($tableElement, $idElement, $category = "", $order = "nom ASC"){
		global $wpdb;
		$documentList = array();

		$morequery = "";
		if($category != "")
		{
			$morequery = "
				AND categorie = '" . $category . "' ";
		}

		$query = $wpdb->prepare(
			"SELECT * FROM
			" . TABLE_GED_DOCUMENTS . "
			WHERE table_element = %s
				AND id_element = %d
				AND status = 'valid'
				" . $morequery . "
			ORDER BY " . $order,
			$tableElement, $idElement);
		$documentList = $wpdb->get_results($query);

		return $documentList;
	}

	/**
	 *	Get the last document generated for a given element
	 *
	 *	@param mixed $tableElement The element type we want to get the last document for
	 *	@param integer $idElement The element identifier we want to get the lat document for
	 *
	 *	@return mixed $lastDocument An object with all information about the last document
	 */
	function getGeneratedDocument($tableElement, $idElement, $type = 'last', $id = '', $document_type = '') {
		global $wpdb;
		$lastDocument = array();

		$queryOrder = "";
		$query_params = array($idElement, $tableElement);

		if ( !empty($id) ) {
			$queryOrder .= "
				AND id = %d";
			$query_params[] = $id;
		}

		if ( !empty($document_type) ) {
			$queryOrder .= "
				AND document_type = %s";
			$query_params[] = $document_type;
		}

		switch ($type) {
			case 'last':
				$queryOrder .= "
				ORDER BY id DESC
			LIMIT 1";
				break;
			case 'list':
				$queryOrder .= "
				ORDER BY creation_date DESC, revision DESC";
				break;
		}

		$query = $wpdb->prepare(
				"SELECT *
			FROM " . TABLE_FP . "
			WHERE id_element = %d
				AND table_element = %s " . $queryOrder,
				$query_params
		);
		$lastDocument = $wpdb->get_results($query);

		if ( count($lastDocument) > 0 ) {

			switch ( $document_type ) {
				case 'fiche_de_groupement':
						$document_prefix = 'ficheDeGroupement';
					break;
				case 'fiche_de_poste':
						$document_prefix = 'ficheDePoste';
					break;
				case 'listing_des_risques':
						$document_prefix = 'listingRisque';
					break;
			}

			switch ($type) {
				case 'last':
					$outputListeDocumentUnique = $wpdb->get_row($query);
					break;
				case 'list':
						$listeParDate = array();
						foreach ($lastDocument as $index => $document) {
							$dateElement = explode(' ', $document->creation_date);
							if ($document->name == '') {
								$document->name = str_replace('-', '', $dateElement[0]) . '_' . $document_prefix . '_' . digirisk_tools::slugify_noaccent(str_replace(' ', '_', $document->societyName)) . '_V' . $document->revisionDUER;
							}
							$listeParDate[$dateElement[0]][$document->id]['name'] = $document->name;
							$listeParDate[$dateElement[0]][$document->id]['fileName'] = $document->name . '_V' . $document->revision;
							$listeParDate[$dateElement[0]][$document->id]['revision'] = 'V' . $document->revision;
						}

						if ( count($listeParDate) > 0 ) {
							$outputListeDocumentUnique .=
							'<table summary="" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;" >';
							foreach ($listeParDate as $date => $listeDUDate) {
								$outputListeDocumentUnique .= '
									<tr>
										<td colspan="3" style="text-decoration:underline;font-weight:bold;" >Le ' . mysql2date('d M Y', $date, true) . '</td>
									</tr>';
								foreach ($listeDUDate as $index => $DUER) {
									$outputListeDocumentUnique .= '
									<tr>
										<td>&nbsp;&nbsp;&nbsp;- (' . ELEMENT_IDENTIFIER_FP . $index . ')&nbsp;&nbsp;' . $DUER['name'] . '_' . $DUER['revision'] . '</td>';

									/*	Check if an odt file exist to be downloaded	*/
									$odtFile = $document_prefix . '/' . $tableElement . '/' . $idElement . '/' . $DUER['fileName'] . '.odt';
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
							</table>';
						}
					break;
			}
		}
		else {
			$outputListeDocumentUnique = '<div class="noResultInBox" >' . __('Aucune fiche n\'a &eacute;t&eacute; g&eacute;n&eacute;r&eacute;e pour le moment', 'evarisk') . '</div>';
		}

		return $outputListeDocumentUnique;
	}

	/**
	*	Return the complete list of existing document. It is possible to specify the category of the document list we want
	*
	*	@param string $category optionnal The category we want to get the document list for
	*	@param string $morequery optionnal An additionnal possibilities to filter the document list
	*	@param string $order optionnal Allow to specify in which order the document will be returned
	*
	*	@return array|object $documentList Return the document list for the selected element in case that there are document associated
	*/
	function getCompleteDocumentList($category = "", $morequery = "", $order = "nom ASC")
	{
		global $wpdb;
		$documentList = array();;

		if($category != "")
		{
			$morequery .= "
				AND categorie = '" . $category . "' ";
		}

		$query = $wpdb->prepare(
			"SELECT * FROM
			" . TABLE_GED_DOCUMENTS . "
			WHERE status = 'valid'
				" . $morequery . "
				AND table_element != 'all'
			GROUP BY chemin, nom
			ORDER BY " . $order,
			$tableElement, $idElement);
		$documentList = $wpdb->get_results($query);

		return $documentList;
	}

	/**
	*	Get the path for a given document
	*
	*	@param integer $idDocument The document identifier we want to get the path for
	*
	*	@return string $path The path of the selected document
	*/
	function getDocumentPath($idDocument)
	{
		global $wpdb;
		$path = '';

		$query = $wpdb->prepare(
			"SELECT chemin, nom
			FROM " . TABLE_GED_DOCUMENTS . "
			WHERE id = %d",
		$idDocument);
		$pathComponents = $wpdb->get_row($query);

		$path = $pathComponents->chemin . $pathComponents->nom;

		return $path;
	}

	/**
	*	Get the default document for a given category
	*
	*	@param string $category The document category we want to get the default document for
	*
	*	@return integer $idDocument The identifier of the default document for the category
	*/
	function getDefaultDocument($category)
	{
		global $wpdb;
		$idDocument = 0;

		$query = $wpdb->prepare(
			"SELECT id
			FROM " . TABLE_GED_DOCUMENTS . "
			WHERE parDefaut = 'oui'
				AND status = 'valid'
				AND categorie = '" . $category . "'
			ORDER BY dateCreation
			LIMIT 1"
		);
		$documentDefaultId = $wpdb->get_row($query);
		if(!empty($documentDefaultId))
			$idDocument = $documentDefaultId->id;

		return $idDocument;
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
	function save_element_sheet($tableElement, $idElement, $informations) {
		global $wpdb;
		$status = array();

		require_once(EVA_LIB_PLUGIN_DIR . 'photo/evaPhoto.class.php');

		$tableElement = digirisk_tools::IsValid_Variable($tableElement);
		$idElement = digirisk_tools::IsValid_Variable($idElement);

		$query_params = array($tableElement, $idElement);
		if (!empty($informations['document_type'])) {
			$query_extra_params = "
				AND document_type = %s";
			$query_params[] = $informations['document_type'];
		}

		/*
		 * Retrieve last revision for the document to generate
		 */
		$revision = '';
		$query = $wpdb->prepare(
			"SELECT max(revision) AS lastRevision
			FROM " . TABLE_FP . "
			WHERE table_element = %s
				AND id_element = %d " . $query_extra_params,
		$query_params);
		$revision = $wpdb->get_row($query);
		$revisionDocument = $revision->lastRevision + 1;

		/*
		 * Generate a reference for the document
		 */
		switch ($tableElement) {
			case TABLE_GROUPEMENT:
			case TABLE_GROUPEMENT . '_RS':
				$element = 'gpt';
				$current_element = EvaGroupement::getGroupement($idElement);
				$element_identifier = ELEMENT_IDENTIFIER_GP;
				break;
			case TABLE_UNITE_TRAVAIL:
			case TABLE_UNITE_TRAVAIL . '_RS':
				$element = 'ut';
				$current_element = eva_UniteDeTravail::getWorkingUnit($idElement);
				$element_identifier = ELEMENT_IDENTIFIER_UT;
				break;
			default:
				$element = $tableElement;
				break;
		}
		$referenceDocument = str_replace('-', '', $informations['dateCreation']) . '-' . $element . $idElement . '-V' . $revisionDocument;

		/*
		 * Retrieve informations about users and groups associated to an element
		 */
		$affectedUserTmp = array();
		$affectedUserList = evaUserLinkElement::getAffectedUser($tableElement, $idElement);
		foreach ($affectedUserList as $user) {
			$affectedUserTmp[] = evaUser::getUserInformation($user->id_user);
		}
		$affectedUser = serialize($affectedUserTmp);
		$affectedUserGroups = serialize(digirisk_groups::getBindGroupsWithInformations($idElement, $tableElement . '_employee'));

		/*
		 * Retrieve informations about evaluators users and groups asociated to an element
		 */
		$affectedUserTmp = array();
		$affectedUserList = evaUserLinkElement::getAffectedUser($tableElement . '_evaluation', $idElement);
		foreach ($affectedUserList as $user) {
			$affectedUserTmp[] = evaUser::getUserInformation($user->id_user);
		}
		$affectedEvaluators = serialize($affectedUserTmp);
		$affectedEvaluatorsGroups = serialize(digirisk_groups::getBindGroupsWithInformations($idElement, $tableElement . '_evaluator'));

		/*
		 * Get the main picture for current element
		 */
		$defaultPicture = evaPhoto::getMainPhoto($tableElement, $idElement);
		$defaultPictureToSet = '';
		if ($defaultPicture != 'error') {
			$defaultPictureToSet = $defaultPicture;
		}
		else {
			$defaultPictureToSet = 'noDefaultPicture';
		}

		/*
		 * Default element
		 */
		$element = $tableElement;

		/*
		 * Get risk list for current element
		 */
		$unitRisk = serialize(eva_documentUnique::listRisk($tableElement, $idElement, (!empty($informations['sheet_output_type']) ? $informations['sheet_output_type'] : ''), $informations['recursiv_mode']));

		/*
		 * Check element type
		 */
		if ( $informations['sheet_type'] == 'digi_groupement' ) {
			$recommandation = '';
			$element = $tableElement . '_FGP';
			$model_shape = 'fiche_de_groupement';
			$document_final_name = '_ficheDeGroupement_';
		}
		else if ( $informations['sheet_type'] == 'digi_unite_travail' ) {
			/*
			 * Get recommandations associated to the element
			 */
			$recommandationList = array();
			$affectedRecommandation = evaRecommandation::getRecommandationListForElement($tableElement, $idElement);
			$i = $oldIdRecommandationCategory = 0;
			foreach ($affectedRecommandation as $recommandation) {
				if ($oldIdRecommandationCategory != $recommandation->recommandation_category_id) {
					$i = 0;
					$oldIdRecommandationCategory = $recommandation->recommandation_category_id;
				}
				$recommandationCategoryMainPicture = evaPhoto::getMainPhoto(TABLE_CATEGORIE_PRECONISATION, $recommandation->recommandation_category_id);
				$recommandationCategoryMainPicture = evaPhoto::checkIfPictureIsFile($recommandationCategoryMainPicture, TABLE_CATEGORIE_PRECONISATION);
				if ($recommandationCategoryMainPicture != false) {
					$recommandationList[$recommandation->recommandation_category_id][$i]['recommandation_category_photo'] = str_replace(EVA_HOME_URL, '', str_replace(EVA_GENERATED_DOC_URL, '', $recommandationCategoryMainPicture));
				}
				else {
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
			$model_shape = 'fiche_de_poste';
			$document_final_name = '_ficheDePoste_';
		}
		else if ( $informations['sheet_type'] == 'digi_risk_listing' ) {
			$recommandation = '';
			$element = $tableElement . '_RS';
			$model_shape = 'listing_des_risques';
			$document_final_name = '_listingRisque_';
		}

		/*
		 * Check the model to use for document
		 */
		$modelToUse = eva_gestionDoc::getDefaultDocument($model_shape);
		if ( ($informations['id_model'] != 'undefined') && ($informations['id_model'] > 0) ) {
			$modelToUse = $informations['id_model'];
		}

		/*
		 * Generate document name from given parameters
		 */
		if ( $informations['nomDuDocument'] == '' ) {
			$dateElement = explode(' ', $informations['dateCreation']);
			$documentName = str_replace('-', '', $dateElement[0]) . $document_final_name . digirisk_tools::slugify_noaccent(str_replace(' ', '_', (!empty($informations['nomEntreprise']) ? $informations['nomEntreprise'] : $current_element->nom)));
			$informations['nomDuDocument'] = $documentName;
		}

		/**
		 * Enregistrement du document
		 */
		$new_sheet_params = array();
		$new_sheet_params['id'] 					= '';
		$new_sheet_params['creation_date'] 			= current_time('mysql', 0);
		$new_sheet_params['revision'] 				= $revisionDocument;
		$new_sheet_params['id_element'] 			= $idElement;
		$new_sheet_params['id_model'] 				= $modelToUse;
		$new_sheet_params['table_element'] 			= $tableElement;
		$new_sheet_params['reference'] 				= $referenceDocument;
		$new_sheet_params['name'] 					= $informations['nomDuDocument'];
		$new_sheet_params['description'] 			= $informations['description'];
		$new_sheet_params['adresse']				= $informations['adresse'];
		$new_sheet_params['telephone'] 				= $informations['telephone'];
		$new_sheet_params['defaultPicturePath'] 	= $defaultPictureToSet;
		$new_sheet_params['societyName'] 			= digirisk_tools::slugify_noaccent($informations['nomEntreprise']);
		$new_sheet_params['users'] 					= $affectedUser;
		$new_sheet_params['userGroups'] 			= $affectedUserGroups;
		$new_sheet_params['evaluators'] 			= $affectedEvaluators;
		$new_sheet_params['evaluatorsGroups'] 		= $affectedEvaluatorsGroups;
		$new_sheet_params['unitRisk'] 				= $unitRisk;
		$new_sheet_params['recommandation'] 		= $recommandation;
		$new_sheet_params['document_type'] 			= $model_shape;
		$new_sheet = $wpdb->insert(TABLE_FP, $new_sheet_params);
		if ( $new_sheet === false ) {
			$status['result'] = 'error';
			$status['errors']['query_error'] = __("Une erreur est survenue lors de l'enregistrement", 'evarisk');
			$status['errors']['query'] = $query;
		}
		else {
			$status['result'] = 'ok';
			/*	Save the odt file	*/
			eva_gestionDoc::generateSummaryDocument($element, $idElement, 'odt');
		}

		return $status;
	}

	/**
	*	Generate an output of summary about the risk on an element. Could be a "single document" or a "work unit sheet"
	*
	*	@param mixed $tableElement The element type we want to generate the document for
	*	@param integer $idElement The element identifier we want to generate the document for
	*	@param mixed $outputType The output we want to get (html, odt, ...)
	*	@param integer $idDocument The identifier of a specific document we want to get
	*
	*	@return mixed Depending on the output type we ask for, an html output or a file
	*/
	function generateSummaryDocument($tableElement, $idElement, $outputType, $idDocument = '') {
		global $typeRisque;
		global $typeRisquePlanAction;
		require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/documentUnique/templateDocumentUnique.tpl.php');
		require_once(EVA_LIB_PLUGIN_DIR . 'gestionDocumentaire/gestionDoc.class.php');
		require_once(EVA_LIB_PLUGIN_DIR . 'arborescence/arborescence_special.class.php');

		switch ($tableElement) {
			case TABLE_GROUPEMENT:
				/**
				*	Get the last summary document generated for the current element OR Get a given generated summary document
				*/
				$lastDocument = eva_documentUnique::getDernierDocumentUnique($tableElement, $idElement, $idDocument);
				/**
				*	Store the different informations about the last generated summary document in an array for more usability
				*/
				unset($documentUniqueParam);
				$documentUniqueParam['#NUMREF#'] = $lastDocument->referenceDUER;
				$documentUniqueParam['#NOMENTREPRISE#'] = $lastDocument->nomSociete;
				$documentUniqueParam['#DEBUTAUDIT#'] = date('d/m/Y', strtotime($lastDocument->dateDebutAudit));
				$documentUniqueParam['#FINAUDIT#'] = date('d/m/Y', strtotime($lastDocument->dateFinAudit));
				$documentUniqueParam['#DATE#'] = date('d/m/Y', strtotime($lastDocument->dateGenerationDUER));
				$documentUniqueParam['#NOMPRENOMEMETTEUR#'] = $lastDocument->emetteurDUER;
				$documentUniqueParam['#NOMPRENOMDESTINATAIRE#'] = $lastDocument->destinataireDUER  ;
				$documentUniqueParam['#TELFIXE#'] = $lastDocument->telephoneFixe ;
				$documentUniqueParam['#TELMOBILE#'] = $lastDocument->telephonePortable ;
				$documentUniqueParam['#TELFAX#'] = $lastDocument->telephoneFax ;
				$documentUniqueParam['#NOMDOCUMENT#'] = $lastDocument->nomDUER;
				$documentUniqueParam['#REVISION#'] = $lastDocument->revisionDUER;
				$documentUniqueParam['#METHODOLOGIE#'] = $lastDocument->methodologieDUER;
				$documentUniqueParam['#SOURCES#'] = $lastDocument->sourcesDUER;
				$documentUniqueParam['#DISPODESPLANS#'] = $lastDocument->planDUER;
				$documentUniqueParam['#ALERTE#'] = $lastDocument->alerteDUER;

				$odfModelFile = EVA_MODELES_PLUGIN_DIR . 'documentUnique/modeleDefaut.odt';
				break;
			case TABLE_UNITE_TRAVAIL:
				/**
				*	Get the last summary document generated for the current element OR Get a given generated summary document
				*/
				$lastDocument = eva_gestionDoc::getGeneratedDocument($tableElement, $idElement, 'last', $idDocument, 'fiche_de_poste');
				$odfModelFile = EVA_MODELES_PLUGIN_DIR . 'ficheDePoste/modeleDefaut.odt';
				break;
			case TABLE_GROUPEMENT . '_FGP' :
				/**
				*	Get the last summary document generated for the current element OR Get a given generated summary document
				*/
				$lastDocument = eva_gestionDoc::getGeneratedDocument(str_replace('_FGP', '', $tableElement), $idElement, 'last', $idDocument, 'fiche_de_groupement');
				$odfModelFile = EVA_MODELES_PLUGIN_DIR . 'ficheDeGroupement/modeleDefaut_groupement.odt';
				break;
			case TABLE_GROUPEMENT . '_RS' :
			case TABLE_UNITE_TRAVAIL . '_RS' :
				/**
				*	Get the last summary document generated for the current element OR Get a given generated summary document
				*/
				$lastDocument = eva_gestionDoc::getGeneratedDocument(str_replace('_RS', '', $tableElement), $idElement, 'last', $idDocument, 'listing_des_risques');
				$odfModelFile = EVA_MODELES_PLUGIN_DIR . 'listingRisque/modeleDefault_listing_risque.odt';
				break;
		}

		/**
		*	If user ask for an "odt" file we include different librairies and model
		*/
		if($outputType == 'odt') {
			require_once(EVA_LIB_PLUGIN_DIR . 'odtPhpLibrary/odf.php');

			$config = array(
				'PATH_TO_TMP' => EVA_RESULTATS_PLUGIN_DIR . 'tmp'
			);
			/**
			*	Get the default model regarding on the element type we are on
			*/
			$odf = new odf($odfModelFile, $config);
			/**
			*	Get the last used model
			*/
			if($lastDocument->id_model > 1) {
				$pathToModelFile = eva_gestionDoc::getDocumentPath($lastDocument->id_model);
				$odf = new odf(EVA_GENERATED_DOC_DIR . $pathToModelFile, $config);
			}
		}

		/**
		*	Generate a html output
		*/
		if($outputType == 'html')
		{
			switch($tableElement)
			{
				case TABLE_GROUPEMENT:
				{
					$documentUnique = '';
					$nbPageTotal = 1;

					/*	Ajout du sommaire	*/
					$nbPageTotal++;
					unset($documentUniqueParam);
					$documentUniqueParam['#PAGE#'] = $nbPageTotal . '/#NBPAGETOTAL#';
					$documentUniqueParam['#CONTENTPAGE#'] = EvaDisplayDesign::feedTemplate($sommaireDocumentUnique, $pageParam);
					$output = EvaDisplayDesign::feedTemplate($templatePageDocumentUnique, $documentUniqueParam);
					$output = str_replace("
		","",str_replace("	","",$output));
					if($outputType == 'html')
					{
						$documentUnique .= $output;
					}

					/*	Chapitre Administratif	*/
					$nbPageTotal++;
					unset($documentUniqueParam);
					$documentUniqueParam['#PAGE#'] = $nbPageTotal . '/#NBPAGETOTAL#';
					$documentUniqueParam['#CONTENTPAGE#'] = EvaDisplayDesign::feedTemplate($ChapitreAdministratif, $pageParam);
					if($outputType == 'html')
					{
						$documentUnique .= EvaDisplayDesign::feedTemplate($templatePageDocumentUnique, $documentUniqueParam);
					}

					/*	Localisation et remarques importantes	*/
					$nbPageTotal++;
					unset($documentUniqueParam);
					$documentUniqueParam['#PAGE#'] = $nbPageTotal . '/#NBPAGETOTAL#';
					$documentUniqueParam['#CONTENTPAGE#'] = EvaDisplayDesign::feedTemplate($localisationRemarques, $pageParam);
					if($outputType == 'html')
					{
						$documentUnique .= EvaDisplayDesign::feedTemplate($templatePageDocumentUnique, $documentUniqueParam);
					}

					/*	Chapitre evaluation	*/
					$nbPageTotal++;
					unset($documentUniqueParam);
					$documentUniqueParam['#PAGE#'] = $nbPageTotal . '/#NBPAGETOTAL#';
					$documentUniqueParam['#CONTENTPAGE#'] = EvaDisplayDesign::feedTemplate($chapitreEvaluation, $pageParam);
					if($outputType == 'html')
					{
						$documentUnique .= EvaDisplayDesign::feedTemplate($templatePageDocumentUnique, $documentUniqueParam);
					}

					/*	Methode d'evaluation et quantification	*/
					$nbPageTotal++;
					unset($documentUniqueParam);
					$documentUniqueParam['#PAGE#'] = $nbPageTotal . '/#NBPAGETOTAL#';
					$documentUniqueParam['#CONTENTPAGE#'] = EvaDisplayDesign::feedTemplate($methodeEvaluationQuantification, $pageParam);
					if($outputType == 'html')
					{
						$documentUnique .= EvaDisplayDesign::feedTemplate($templatePageDocumentUnique, $documentUniqueParam);
					}

					/*	Groupes d'utilisateurs	*/
					$nbPageTotal++;
					unset($documentUniqueParam);
					$documentUniqueParam['#PAGE#'] = $nbPageTotal . '/#NBPAGETOTAL#';
					unset($pageParam);

					/*	Groupes Existant	*/
					$groupesUtilisateur = unserialize($lastDocument->groupesUtilisateurs);
					if( is_array($groupesUtilisateur) )
					{
					$listeGroupeUtilisateur =
							'<table summary="userGroupsSummary' . $tableElement . '-' . $idElement . '" cellpadding="0" cellspacing="0" class="widefat post fixed">
								<thead>
									<tr>
										<th>' . __('Nom du groupe', 'evarisk') . '</th>
										<th>' . __('Description du groupe', 'evarisk') . '</th>
										<th>' . __('Nombre d\'utilisateur du groupe', 'evarisk') . '</th>
									</tr>
								</thead>
								<tfoot></tfoot>
								<tbody>
									' . digirisk_groups::outputGroupListing($groupesUtilisateur, 'html') . '
								</tbody>
							</table>';
					}
					else
					{
						$listeGroupeUtilisateur = $lastDocument->groupesUtilisateurs;
					}
					$pageParam['#GROUPESUTILISATEURS#'] = $listeGroupeUtilisateur;

					/*	Groupes affectes	*/
					$groupesUtilisateursAffectes = unserialize($lastDocument->groupesUtilisateursAffectes);
					if( is_array($groupesUtilisateursAffectes) )
					{
					$listeGroupeUtilisateur =
							'<table summary="userGroupsSummary' . $tableElement . '-' . $idElement . '" cellpadding="0" cellspacing="0" class="widefat post fixed">
								<thead>
									<tr>
										<th>' . __('&Eacute;l&eacute;ment', 'evarisk') . '</th>
										<th>' . __('Groupes utilisateurs (m&eacute;tiers)', 'evarisk') . '</th>
									</tr>
								</thead>
								<tfoot></tfoot>
								<tbody>
									' . eva_documentUnique::readExportedDatas($groupesUtilisateursAffectes, 'affectedUserGroup', '', 'html') . '
								</tbody>
							</table>';
					}
					else
					{
						$listeGroupeUtilisateur = $lastDocument->groupesUtilisateursAffectes;
					}
					$pageParam['#GROUPESUTILISATEURSAFFECTES#'] = $listeGroupeUtilisateur;
					$documentUniqueParam['#CONTENTPAGE#'] = EvaDisplayDesign::feedTemplate($groupesUtilisateurs, $pageParam);
					if($outputType == 'html')
					{
						$documentUnique .= EvaDisplayDesign::feedTemplate($templatePageDocumentUnique, $documentUniqueParam);
					}

					/*	Unites de travail	*/
					$nbPageTotal++;
					unset($documentUniqueParam);
					$documentUniqueParam['#PAGE#'] = $nbPageTotal . '/#NBPAGETOTAL#';
					$documentUniqueParam['#CONTENTPAGE#'] = EvaDisplayDesign::feedTemplate($unitesDeTravail, $pageParam);
					if($outputType == 'html')
					{
						$documentUnique .= EvaDisplayDesign::feedTemplate($templatePageDocumentUnique, $documentUniqueParam);
					}

					/*	FER : Fiche d'Evaluation des Risques	*/
					$nbPageTotal++;
					unset($documentUniqueParam);
					$documentUniqueParam['#PAGE#'] = $nbPageTotal . '/#NBPAGETOTAL#';
					$documentUniqueParam['#CONTENTPAGE#'] = EvaDisplayDesign::feedTemplate($ficheDEvaluationDesRisques, $pageParam);
					if($outputType == 'html')
					{
						$documentUnique .= EvaDisplayDesign::feedTemplate($templatePageDocumentUnique, $documentUniqueParam);
					}

					/*	Introduction risques unitaires	*/
					$nbPageTotal++;
					unset($documentUniqueParam);
					$documentUniqueParam['#PAGE#'] = $nbPageTotal . '/#NBPAGETOTAL#';
					$documentUniqueParam['#CONTENTPAGE#'] = EvaDisplayDesign::feedTemplate($introductionRisquesUnitaires, $pageParam);
					if($outputType == 'html')
					{
						$documentUnique .= EvaDisplayDesign::feedTemplate($templatePageDocumentUnique, $documentUniqueParam);
					}

					/*	Risques unitaires	*/
					$nbPageTotal++;
					unset($documentUniqueParam);
					$documentUniqueParam['#PAGE#'] = $nbPageTotal . '/#NBPAGETOTAL#';
					unset($pageParam);
					$pageParam['#LIGNESRISQUESUNITAIRES#'] = '';
						$getRisquesUnitaires = unserialize($lastDocument->risquesUnitaires);
						$listeRisqueUnitaire = eva_documentUnique::readBilanUnitaire($getRisquesUnitaires);
						if( is_array($getRisquesUnitaires) )
						{
							krsort($listeRisqueUnitaire);
							foreach($listeRisqueUnitaire as $categorieRisque => $risques)
							{
								foreach($risques as $niveauRisque => $risque)
								{
									foreach($risque as $identifiantRisque => $risqueInformations)
									{
										unset($paramLigneRisqueUnitaire);
										$paramLigneRisqueUnitaire['#NOMELEMENT#'] = $risqueInformations['nomElement'];
										$couleurRisque = COULEUR_RISQUE_FAIBLE;
										$couleurTexteRisque = COULEUR_TEXTE_RISQUE_FAIBLE;
										if($categorieRisque >= SEUIL_BAS_INACCEPTABLE)
										{
											$couleurRisque = COULEUR_RISQUE_INACCEPTABLE;
											$couleurTexteRisque = COULEUR_TEXTE_RISQUE_INACCEPTABLE;
										}
										elseif(($categorieRisque >= SEUIL_BAS_ATRAITER) && ($categorieRisque <= SEUIL_HAUT_ATRAITER))
										{
											$couleurRisque = COULEUR_RISQUE_ATRAITER;
											$couleurTexteRisque = COULEUR_TEXTE_RISQUE_ATRAITER;
										}
										elseif(($categorieRisque >= SEUIL_BAS_APLANIFIER) && ($categorieRisque <= SEUIL_HAUT_APLANIFIER))
										{
											$couleurRisque = COULEUR_RISQUE_APLANIFIER;
											$couleurTexteRisque = COULEUR_TEXTE_RISQUE_APLANIFIER;
										}
										$paramLigneRisqueUnitaire['#QUOTATIONCOLOR#'] = $couleurRisque;
										$paramLigneRisqueUnitaire['#QUOTATIONTEXTCOLOR#'] = $couleurTexteRisque;
										$paramLigneRisqueUnitaire['#QUOTATION#'] = $risqueInformations['quotationRisque'];
										$paramLigneRisqueUnitaire['#NOMDANGER#'] = $risqueInformations['nomDanger'];
										$paramLigneRisqueUnitaire['#COMMENTAIRE#'] = $risqueInformations['commentaireRisque'];
										$pageParam['#LIGNESRISQUESUNITAIRES#'] .= EvaDisplayDesign::feedTemplate($risquesUnitairesLignes, $paramLigneRisqueUnitaire);
									}
								}
							}
							$pageParam['#IDTABLE#'] = $tableElement . '-' . $idElement;
							$documentUniqueParam['#CONTENTPAGE#'] = EvaDisplayDesign::feedTemplate($risquesUnitaires, $pageParam);
						}
						else
						{
							$documentUniqueParam['#CONTENTPAGE#'] = $lastDocument->risquesUnitaires;
						}
					if($outputType == 'html')
					{
						$documentUnique .= EvaDisplayDesign::feedTemplate($templatePageDocumentUnique, $documentUniqueParam);
					}

					/*	Introduction risques par unite	*/
					$nbPageTotal++;
					unset($documentUniqueParam);
					$documentUniqueParam['#PAGE#'] = $nbPageTotal . '/#NBPAGETOTAL#';
					$documentUniqueParam['#CONTENTPAGE#'] = EvaDisplayDesign::feedTemplate($introductionRisquesParUnite, $pageParam);
					if($outputType == 'html')
					{
						$documentUnique .= EvaDisplayDesign::feedTemplate($templatePageDocumentUnique, $documentUniqueParam);
					}

					/*	Risques par unite	*/
					$nbPageTotal++;
					unset($documentUniqueParam);
					$documentUniqueParam['#PAGE#'] = $nbPageTotal . '/#NBPAGETOTAL#';
					unset($pageParam);
					$bilanParUnite = unserialize($lastDocument->risquesParUnite);
					if( is_array($bilanParUnite) )
					{
					$risqueParUniteDeTravail =
							'<table summary="risqsSummary' . $tableElement . '-' . $idElement . '" cellpadding="0" cellspacing="0" class="widefat post fixed">
								<thead>
									<tr>
										<th>' . __('&Eacute;l&eacute;ment', 'evarisk') . '</th>
										<th>' . __('Somme des quotations', 'evarisk') . '</th>
									</tr>
								</thead>
								<tfoot></tfoot>
								<tbody>
									' . eva_documentUnique::readExportedDatas($bilanParUnite, 'riskByElement', '', 'html') . '
								</tbody>
							</table>';
					}
					else
					{
						$risqueParUniteDeTravail = $lastDocument->risquesParUnite;
					}
					$pageParam['#RISQUEPARUNITE#'] = $risqueParUniteDeTravail;
					$documentUniqueParam['#CONTENTPAGE#'] = EvaDisplayDesign::feedTemplate($risquesParUnite, $pageParam);
					if($outputType == 'html')
					{
						$documentUnique .= EvaDisplayDesign::feedTemplate($templatePageDocumentUnique, $documentUniqueParam);
					}

					/*	Le plan d'action	*/
					$nbPageTotal++;
					unset($documentUniqueParam);
					$documentUniqueParam['#PAGE#'] = $nbPageTotal . '/#NBPAGETOTAL#';
					$documentUniqueParam['#CONTENTPAGE#'] = EvaDisplayDesign::feedTemplate($planDAction, $pageParam);
					if($outputType == 'html')
					{
						$documentUnique .= EvaDisplayDesign::feedTemplate($templatePageDocumentUnique, $documentUniqueParam);
					}

					$documentUniqueParam['#NBPAGETOTAL#'] = $nbPageTotal;
					$completeOutput = EvaDisplayDesign::feedTemplate($premiereDeCouvertureDocumentUnique . $documentUnique, $documentUniqueParam);
				}
				break;
			}

			return $completeOutput;
		}
		/**
		*	Generate the odt file
		*/
		else if ($outputType == 'odt') {
			ini_set("memory_limit","256M");
			switch ($tableElement) {
				case TABLE_GROUPEMENT:
				{
					$documentUniqueParam['#NOMENTREPRISE#'] = str_replace('<br />', "
", digirisk_tools::slugify_noaccent($documentUniqueParam['#NOMENTREPRISE#']));
					$odf->setVars('nomEntreprise', $documentUniqueParam['#NOMENTREPRISE#']);

					$documentUniqueParam['#NOMPRENOMEMETTEUR#'] = str_replace('<br />', "
", digirisk_tools::slugify_noaccent($documentUniqueParam['#NOMPRENOMEMETTEUR#']));
					$odf->setVars('emetteurDUER', $documentUniqueParam['#NOMPRENOMEMETTEUR#']);

					$documentUniqueParam['#NOMPRENOMDESTINATAIRE#'] = str_replace('<br />', "
", digirisk_tools::slugify_noaccent($documentUniqueParam['#NOMPRENOMDESTINATAIRE#']));
					$odf->setVars('destinataireDUER', $documentUniqueParam['#NOMPRENOMDESTINATAIRE#']);

					$documentUniqueParam['#TELFIXE#'] = str_replace('<br />', "
", digirisk_tools::slugify_noaccent($documentUniqueParam['#TELFIXE#']));
					$odf->setVars('telephone', $documentUniqueParam['#TELFIXE#']);

					$documentUniqueParam['#TELMOBILE#'] = str_replace('<br />', "
", digirisk_tools::slugify_noaccent($documentUniqueParam['#TELMOBILE#']));
					$odf->setVars('portable', $documentUniqueParam['#TELMOBILE#']);

					$documentUniqueParam['#TELFAX#'] = str_replace('<br />', "
", digirisk_tools::slugify_noaccent($documentUniqueParam['#TELFAX#']));
					$odf->setVars('fax', $documentUniqueParam['#TELFAX#']);

					$odf->setVars('dateGeneration', $documentUniqueParam['#DATE#']);
					$finAudit = '';if(($documentUniqueParam['#FINAUDIT#'] != '') && ($documentUniqueParam['#FINAUDIT#'] != $documentUniqueParam['#DEBUTAUDIT#'])){$finAudit = __(' au ', 'evarisk') . $documentUniqueParam['#FINAUDIT#'];}
					$odf->setVars('dateAudit', $documentUniqueParam['#DEBUTAUDIT#'] . $finAudit);

					$documentUniqueParam['#DISPODESPLANS#'] = str_replace('<br />', "
", digirisk_tools::slugify_noaccent($documentUniqueParam['#DISPODESPLANS#']));
					if (trim($documentUniqueParam['#DISPODESPLANS#']) == '') {
						$documentUniqueParam['#DISPODESPLANS#'] = __('La localisation n\'a pas &eacute;t&eacute; pr&eacute;cis&eacute;e', 'evarisk');
					}

					$odf->setVars('methodologie', str_replace('<br />', "
", digirisk_tools::slugify_noaccent($documentUniqueParam['#METHODOLOGIE#'])));
					$odf->setVars('dispoDesPlans', str_replace('<br />', "
", digirisk_tools::slugify_noaccent($documentUniqueParam['#DISPODESPLANS#'])));

					$odf->setVars('remarqueImportante', str_replace('<br />', "
", digirisk_tools::slugify_noaccent($documentUniqueParam['#ALERTE#'])));

					$odf->setVars('sources', str_replace('<br />', "
", digirisk_tools::slugify_noaccent($documentUniqueParam['#SOURCES#'])));

					{/*	Remplissage du template pour les groupes d'utilisateurs	*/
						$listeUserGroupe = array();
						$listeDesGroupes = unserialize($lastDocument->groupesUtilisateurs);
						$listeUserGroupe = digirisk_groups::outputGroupListing($listeDesGroupes, 'print');

						$userGroup = $odf->setSegment('groupesUtilisateurs');
						if($userGroup)
						{
							foreach($listeUserGroupe AS $element)
							{
								$element['userGroupName'] = str_replace('<br />', "
	", digirisk_tools::slugify_noaccent($element['userGroupName']));
								$element['userGroupName'] = str_replace('&nbsp;', '�', $element['userGroupName']);
								$element['userGroupDescription'] = str_replace('<br />', "
	", digirisk_tools::slugify_noaccent($element['userGroupDescription']));
								$element['userGroupDescription'] = str_replace('&nbsp;', '�', $element['userGroupDescription']);
								$userGroup->nomGroupe(digirisk_tools::slugify_noaccent($element['userGroupName']));
								$userGroup->descriptionGroupe(digirisk_tools::slugify_noaccent($element['userGroupDescription']));
								$userGroup->nombreUtilisateursGroupe($element['userGroupTotalUserNumber']);
								$userGroup->listeUtilisateur($element['userGroupUsers']);
								$userGroup->merge();
							}
							$odf->mergeSegment($userGroup);
						}
					}

					{/*	Remplissage du template pour les groupes d'utilisateurs affectes	*/
						$listeUserGroupe = array();
						$listeDesGroupesAffectes = unserialize($lastDocument->groupesUtilisateursAffectes);
						$listeUserGroupe = eva_documentUnique::readExportedDatas($listeDesGroupesAffectes, 'affectedUserGroup', '', 'print');

						$userGroupAffected = $odf->setSegment('groupesUtilisateursAffectes');
						if($userGroupAffected)
						{
							foreach($listeUserGroupe AS $element)
							{
								$element['nomElement'] = str_replace('<br />', "
	", digirisk_tools::slugify_noaccent($element['nomElement']));

								$userGroupAffected->nomElement($element['nomElement']);
								$userGroupAffected->listeGroupes(digirisk_tools::slugify_noaccent($element['listeGroupes']));
								$userGroupAffected->merge();
							}
							$odf->mergeSegment($userGroupAffected);
						}
					}

					/*	Remplissage du template pour les risques unitaires	*/
					$listeRisques = unserialize($lastDocument->risquesUnitaires);
					$listeRisque = eva_documentUnique::readBilanUnitaire($listeRisques);

					/*	Lecture des types de risques existants	*/
					foreach ($typeRisque as $riskTypeIdentifier => $riskTypeValue) {
						$risque = $odf->setSegment($riskTypeIdentifier);
						if($risque) {
							$odf->mergeSegment(self::transform_risk_listing ($listeRisque[$riskTypeValue], $risque));
						}
					}

					{/*	Remplissage du template pour les risques par groupement et unit�	*/
						$listeGroupement = array();
						$bilanParUnite = unserialize($lastDocument->risquesParUnite);
						$listeGroupement = eva_documentUnique::readExportedDatas($bilanParUnite, 'riskByElement', '', 'print');

						$risqueParFiche = $odf->setSegment('risqueFiche');
						if($risqueParFiche)
						{
							foreach($listeGroupement AS $element)
							{
								$element['nomElement'] = str_replace('<br />', "
				", digirisk_tools::slugify_noaccent($element['nomElement']));

								$risqueParFiche->nomElement($element['nomElement']);
								$risqueParFiche->quotationTotale($element['quotationTotale']);

								$risqueParFiche->merge();
							}
							$odf->mergeSegment($risqueParFiche);
						}
					}

					{/*	Remplissage du template pour le plan d'action	*/
						$planDaction = unserialize($lastDocument->plan_d_action);
						$storedPlanDaction = eva_documentUnique::readBilanUnitaire($planDaction['affected'], 'plan_d_action');

						/*	Lecture des types de risques existants pour construction du plan d'action	*/
						foreach($typeRisquePlanAction as $riskTypeIdentifier => $riskTypeValue){
							$planDactionR = $odf->setSegment($riskTypeIdentifier);
							if($planDactionR){
								if( is_array($storedPlanDaction[$riskTypeValue]) ){
									foreach($storedPlanDaction[$riskTypeValue] AS $elements){
										foreach($elements AS $element){
											$element['nomElement'] = str_replace('<br />', "
		", digirisk_tools::slugify_noaccent_no_utf8decode($element['nomElement']));
											$element['identifiantRisque'] = str_replace('<br />', "
		", digirisk_tools::slugify_noaccent_no_utf8decode($element['identifiantRisque']));
											$element['quotationRisque'] = str_replace('<br />', "
		", digirisk_tools::slugify_noaccent_no_utf8decode($element['quotationRisque']));
											$element['nomDanger'] = str_replace('<br />', "
		", digirisk_tools::slugify_noaccent_no_utf8decode($element['nomDanger']));
											$element['actionPrevention'] = str_replace('<br />', "
		", digirisk_tools::slugify_noaccent_no_utf8decode($element['actionPrevention']));

											$planDactionR->setVars('nomElement', $element['nomElement'], true, 'UTF-8');
											$planDactionR->setVars('identifiantRisque', $element['identifiantRisque'], true, 'UTF-8');
											$planDactionR->setVars('quotationRisque', $element['quotationRisque'], true, 'UTF-8');
											$planDactionR->setVars('nomDanger', $element['nomDanger'], true, 'UTF-8');
											$planDactionR->setVars('actionPrevention', $element['actionPrevention'], true, 'UTF-8');

											$planDactionR->merge();
										}
									}
								}
								$odf->mergeSegment($planDactionR);
							}
						}

						$planDactionUA = $odf->setSegment('planDaction');
						if($planDactionUA){
							if( is_array($planDaction['unaffected']) ){
								foreach($planDaction['unaffected'] AS $element){
									// foreach($elements AS $element){
										$element['idAction'] = str_replace('<br />', "
	", digirisk_tools::slugify_noaccent_no_utf8decode($element['idAction']));
										$element['nomAction'] = str_replace('<br />', "
	", digirisk_tools::slugify_noaccent_no_utf8decode($element['nomAction']));
										$element['descriptionAction'] = str_replace('<br />', "
	", digirisk_tools::slugify_noaccent_no_utf8decode($element['descriptionAction']));
										$element['ajoutAction'] = str_replace('<br />', "
	", digirisk_tools::slugify_noaccent_no_utf8decode($element['ajoutAction']));
										$element['responsableAction'] = str_replace('<br />', "
	", digirisk_tools::slugify_noaccent_no_utf8decode($element['responsableAction']));
										$element['affectationAction'] = str_replace('<br />', "
	", digirisk_tools::slugify_noaccent_no_utf8decode($element['affectationAction']));

										$planDactionUA->setVars('idAction', $element['idAction'], true, 'UTF-8');
										$planDactionUA->setVars('nomAction', $element['nomAction'], true, 'UTF-8');
										$planDactionUA->setVars('descriptionAction', $element['descriptionAction'], true, 'UTF-8');
										$planDactionUA->setVars('ajoutAction', $element['ajoutAction'], true, 'UTF-8');
										$planDactionUA->setVars('responsableAction', $element['responsableAction'], true, 'UTF-8');
										$planDactionUA->setVars('affectationAction', $element['affectationAction'], true, 'UTF-8');

										$planDactionUA->merge();
									// }
								}
							}
							$odf->mergeSegment($planDactionUA);
						}
					}

					$finalDir = EVA_RESULTATS_PLUGIN_DIR . 'documentUnique/' . $tableElement . '/' . $idElement . '/';
					$fileName = str_replace(' ', '',$lastDocument->nomDUER) . '_V' . $lastDocument->revisionDUER;
				}
				break;
				case TABLE_GROUPEMENT . '_FGP':
				case TABLE_UNITE_TRAVAIL:
				{
					if ($tableElement == TABLE_GROUPEMENT . '_FGP') {
						$workUnitinformations = EvaGroupement::getGroupement($idElement);

						$odf->setVars('reference', ELEMENT_IDENTIFIER_GP . $idElement);
						$odf->setVars('nom', digirisk_tools::slugify_noaccent($workUnitinformations->nom));
						$finalDir = EVA_RESULTATS_PLUGIN_DIR . 'ficheDeGroupement/' . TABLE_GROUPEMENT . '/' . $idElement . '/';
					}
					else {
						$workUnitinformations = eva_UniteDeTravail::getWorkingUnit($idElement);

						$odf->setVars('referenceUnite', ELEMENT_IDENTIFIER_UT . $idElement);
						$odf->setVars('nomUnite', digirisk_tools::slugify_noaccent($workUnitinformations->nom));
						$odf->setVars('reference', ELEMENT_IDENTIFIER_UT . $idElement);
						$odf->setVars('nom', digirisk_tools::slugify_noaccent($workUnitinformations->nom));
						$finalDir = EVA_RESULTATS_PLUGIN_DIR . 'ficheDePoste/' . $tableElement . '/' . $idElement . '/';
					}

					$odf->setVars('description', str_replace('<br />', "
", digirisk_tools::slugify_noaccent($lastDocument->description)));
					$odf->setVars('telephone', str_replace('<br />', "
", digirisk_tools::slugify_noaccent($lastDocument->telephone)));
					$odf->setVars('adresse', str_replace('<br />', "
", digirisk_tools::slugify_noaccent($lastDocument->adresse)));

					{/*	Remplissage du template pour les utilisateurs affectes	*/
						$listeUser = array();
						$listeUser = unserialize($lastDocument->users);

						$affectedUsers = $odf->setSegment('utilisateursAffectes');
						if($affectedUsers)
						{
							foreach($listeUser AS $element)
							{
								foreach($element AS $elementInfos)
								{
									$affectedUsers->setVars('idUtilisateur', ELEMENT_IDENTIFIER_U . $elementInfos['user_id'], true, 'UTF-8');
									$elementInfos['nomUtilisateur'] = str_replace('<br />', "
		", digirisk_tools::slugify_noaccent($elementInfos['nomUtilisateur']));
									$affectedUsers->setVars('nomUtilisateur', $elementInfos['user_lastname'], true, 'UTF-8');
									$elementInfos['prenomUtilisateur'] = str_replace('<br />', "
		", digirisk_tools::slugify_noaccent($elementInfos['prenomUtilisateur']));
									$affectedUsers->setVars('prenomUtilisateur', $elementInfos['user_firstname'], true, 'UTF-8');

									$affectedUsers->merge();
								}
							}
							$odf->mergeSegment($affectedUsers);
						}
					}

					{/*	Remplissage du template pour les evaluateurs affectes	*/
						$listeUser = array();
						$listeUser = unserialize($lastDocument->evaluators);

						$affectedEvaluators = $odf->setSegment('utilisateursPresents');
						if($affectedEvaluators)
						{
							foreach($listeUser AS $element)
							{
								foreach($element AS $elementInfos)
								{
									$affectedEvaluators->setVars('idUtilisateur', ELEMENT_IDENTIFIER_U . $elementInfos['user_id'], true, 'UTF-8');
									$elementInfos['nomUtilisateur'] = str_replace('<br />', "
		", digirisk_tools::slugify_noaccent($elementInfos['nomUtilisateur']));
									$affectedEvaluators->setVars('nomUtilisateur', $elementInfos['user_lastname'], true, 'UTF-8');
									$elementInfos['prenomUtilisateur'] = str_replace('<br />', "
		", digirisk_tools::slugify_noaccent($elementInfos['prenomUtilisateur']));
									$affectedEvaluators->setVars('prenomUtilisateur', $elementInfos['user_firstname'], true, 'UTF-8');

									$affectedEvaluators->merge();
								}
							}
							$odf->mergeSegment($affectedEvaluators);
						}
					}

					/*	Remplissage du template pour les groupes d'utilisateurs affectes	*/
						$listeDesGroupesAffectes = array();
						$listeDesGroupesAffectes = unserialize($lastDocument->userGroups);

						$userGroupAffected = $odf->setSegment('gpUserAffected');
						if ($userGroupAffected) {
							$odf->mergeSegment( self::transform_users_group($listeDesGroupesAffectes, $userGroupAffected) );
						}

					/*	Remplissage du template pour les risques unitaires	*/
						$listeRisques = unserialize($lastDocument->unitRisk);
						$listeRisque = eva_documentUnique::readBilanUnitaire($listeRisques);

						/*	Lecture des types de risques existants	*/
						foreach ($typeRisque as $riskTypeIdentifier => $riskTypeValue) {
							$risque = $odf->setSegment($riskTypeIdentifier);
							if($risque) {
								$odf->mergeSegment(self::transform_risk_listing($listeRisque[$riskTypeValue], $risque));
							}
						}

					{/*	Remplissage du template pour les pr�conisations afffect�es � l'unit� de travail	*/
						$listePreconisationsAffectees = array();
						$listePreconisationsAffectees = unserialize($lastDocument->recommandation);

						$afffectedRecommandation = $odf->setSegment('affectedRecommandation');
						if($afffectedRecommandation)
						{
							foreach($listePreconisationsAffectees AS $recommandationCategory)
							{
								if(($recommandationCategory[0]['impressionRecommandationCategorie'] == 'textandpicture') || ($recommandationCategory[0]['impressionRecommandationCategorie'] == 'textonly'))
								{
									$recommandationCategoryName = str_replace('<br />', "
	", digirisk_tools::slugify_noaccent($recommandationCategory[0]['recommandation_category_name']));
								}
								else
								{
									$recommandationCategoryName = '';
								}
								$afffectedRecommandation->setVars('recommandationCategoryName', $recommandationCategoryName, true, 'UTF-8');

								if(($recommandationCategory[0]['impressionRecommandationCategorie'] == 'textandpicture') || ($recommandationCategory[0]['impressionRecommandationCategorie'] == 'pictureonly'))
								{
									$recommandationCategoryIcon = evaPhoto::checkIfPictureIsFile($recommandationCategory[0]['recommandation_category_photo'], TABLE_CATEGORIE_PRECONISATION);
									$recommandationCategoryIcon = str_replace(EVA_GENERATED_DOC_URL, EVA_GENERATED_DOC_DIR, $recommandationCategoryIcon);
									$recommandationCategoryIcon = str_replace(EVA_HOME_URL, EVA_HOME_DIR, $recommandationCategoryIcon);
									$afffectedRecommandation->setImage('recommandationCategoryIcon', $recommandationCategoryIcon , $recommandationCategory[0]['tailleImpressionPictoCategorie']);
								}
								else
								{
									$afffectedRecommandation->setVars('recommandationCategoryIcon', '', true, 'UTF-8');
								}

								foreach($recommandationCategory as $recommandation)
								{
									if($recommandationCategory[0]['impressionRecommandation'] == 'pictureonly')
									{
										$recommandation['recommandation_name'] = '';
										$recommandation['commentaire'] = '';
									}

									if($recommandation['commentaire'] != '')
									{
										$recommandation['commentaire'] = " : " . $recommandation['commentaire'] . "
	";
									}
									$afffectedRecommandation->recommandations->setVars('identifiantRecommandation', digirisk_tools::slugify_noaccent(ELEMENT_IDENTIFIER_P . $recommandation['id_preconisation']));
									$afffectedRecommandation->recommandations->setVars('recommandationName', str_replace('<br />', "
	", digirisk_tools::slugify_noaccent($recommandation['recommandation_name'])));
									$afffectedRecommandation->recommandations->setVars('recommandationComment', str_replace('<br />', "
	", digirisk_tools::slugify_noaccent($recommandation['commentaire'])));

									if (($recommandationCategory[0]['impressionRecommandation'] == 'pictureonly') || ($recommandationCategory[0]['impressionRecommandation'] == 'textandpicture')) {
										$recommandationIcon = evaPhoto::checkIfPictureIsFile($recommandation['photo'], TABLE_PRECONISATION);
										$recommandationIcon = str_replace(EVA_GENERATED_DOC_URL, EVA_GENERATED_DOC_DIR, $recommandationIcon);
										$recommandationIcon = str_replace(EVA_HOME_URL, EVA_HOME_DIR, $recommandationIcon);
										$afffectedRecommandation->recommandations->setImage('recommandationIcon', $recommandationIcon , $recommandationCategory[0]['tailleimpressionRecommandation']);
									}
									else {
										$afffectedRecommandation->recommandations->setVars('recommandationIcon', '');
									}

									$afffectedRecommandation->recommandations->merge();
								}

								$afffectedRecommandation->merge();
							}
							$odf->mergeSegment($afffectedRecommandation);
						}
					}

					if(is_file(EVA_GENERATED_DOC_DIR . $lastDocument->defaultPicturePath)){
						$odf->setImage('photoDefault', EVA_GENERATED_DOC_DIR . $lastDocument->defaultPicturePath, digirisk_options::getOptionValue('taille_photo_poste_fiche_de_poste'));
					}
					else{
						$odf->setVars('photoDefault', digirisk_tools::slugify_noaccent(__('Aucun photo d&eacute;finie', 'evarisk')));
					}

					$fileName = str_replace(' ', '',$lastDocument->name) . '_V' . $lastDocument->revision;
				}
				break;
				case TABLE_GROUPEMENT . '_RS' :
				case TABLE_UNITE_TRAVAIL . '_RS' :
						$listing_risk_params = array();
						if ($tableElement == TABLE_GROUPEMENT . '_RS') {
							$current_element = EvaGroupement::getGroupement($idElement);
							$element_identifier = ELEMENT_IDENTIFIER_GP;
							$element_directory = TABLE_GROUPEMENT;
						}
						else {
							$current_element = eva_UniteDeTravail::getWorkingUnit($idElement);
							$element_identifier = ELEMENT_IDENTIFIER_UT;
							$element_directory = TABLE_UNITE_TRAVAIL;
						}
						$odf->setVars('reference', $element_identifier . $idElement);
						$odf->setVars('nom', digirisk_tools::slugify_noaccent($current_element->nom));
						$finalDir = EVA_RESULTATS_PLUGIN_DIR . 'listingRisque/' . $element_directory . '/' . $idElement . '/';

						/*	Remplissage du template pour les risques unitaires	*/
						$listeRisques = unserialize($lastDocument->unitRisk);
						$listeRisque = eva_documentUnique::readBilanUnitaire($listeRisques, 'risk_summary');

						/*	Lecture des types de risques existants	*/
						foreach ($typeRisque as $riskTypeIdentifier => $riskTypeValue) {
							$risque = $odf->setSegment($riskTypeIdentifier);
							if($risque) {
								$odf->mergeSegment(self::transform_risk_listing($listeRisque[$riskTypeValue], $risque));
							}
						}

						$fileName = str_replace(' ', '', $lastDocument->name) . '_V' . $lastDocument->revision;
					break;
			}

			if(!is_dir($finalDir)){
				digirisk_tools::make_recursiv_dir($finalDir);
			}
			$odf->saveToDisk($finalDir . $fileName . '.odt');
		}
	}

	/**
	 * Transform output for users group
	 *
	 * @param array $liste_groupes The users group list to read and to put into
	 * @param object $odf_element The element part to fill into document
	 * @return object
	 */
	function transform_users_group($liste_groupes, $odf_element) {

		foreach ($liste_groupes AS $element) {
			$element['name'] = str_replace('<br />', "
	", digirisk_tools::slugify_noaccent($element['name']));
			$element['name'] = str_replace('&nbsp;', '�', $element['name']);
			$element['description'] = str_replace('<br />', "
	", digirisk_tools::slugify_noaccent($element['description']));
			$element['description'] = str_replace('&nbsp;', '�', $element['description']);
			$odf_element->idGroupe(digirisk_tools::slugify_noaccent(ELEMENT_IDENTIFIER_GPU . $element['id']));
			$odf_element->nomGroupe(digirisk_tools::slugify_noaccent($element['name']));
			$odf_element->descriptionGroupe(digirisk_tools::slugify_noaccent($element['description']));
			$userList = '';
			if ($element['userList'] == '') {
				$element['userNumber'] = '0';
			}
			else {
				if (substr($element['userList'], -1) == ',') {
					$element['userList'] = substr($element['userList'], 0, -1);
				}
				$groupUsers = explode(',', $element['userList']);
				$element['userNumber'] = count($groupUsers);
				foreach ($groupUsers as $user) {
					if ($user > 0) {
						$userInformations = evaUser::getUserInformation($user);
						$userList .= $userInformations[$user]['user_lastname'] . ' ' . $userInformations[$user]['user_firstname'] . ', ';
					}
				}
			}
			$odf_element->nombreUtilisateur($element['userNumber']);
			$odf_element->listeUtilisateur($userList);
			$odf_element->merge();
		}

		return $odf_element;
	}

	/**
	 * Generate risk listing
	 *
	 * @param array $listeRisque The element list to use for filling document
	 * @param object $risque The part of output document to fill
	 *
	 * @return object The generated output
	 */
	function transform_risk_listing ($listeRisque, $risque) {
		if ( is_array($listeRisque) ) {
			foreach ($listeRisque AS $elements) {
				foreach ($elements AS $element) {
					$element['nomElement'] = str_replace('<br />', "
", digirisk_tools::slugify_noaccent_no_utf8decode($element['nomElement']));
					$element['identifiantRisque'] = str_replace('<br />', "
", digirisk_tools::slugify_noaccent_no_utf8decode($element['identifiantRisque']));
					$element['quotationRisque'] = str_replace('<br />', "
", digirisk_tools::slugify_noaccent_no_utf8decode($element['quotationRisque']));
					$element['nomDanger'] = str_replace('<br />', "
", digirisk_tools::slugify_noaccent_no_utf8decode($element['nomDanger']));
					$element['commentaireRisque'] = str_replace('<br />', "
", digirisk_tools::slugify_noaccent_no_utf8decode($element['commentaireRisque']));
					$element['actionPrevention'] = str_replace('<br />', "
", digirisk_tools::slugify_noaccent_no_utf8decode($element['actionPrevention']));
					$element['methodeElement'] = str_replace('<br />', "
", digirisk_tools::slugify_noaccent_no_utf8decode($element['methodeElement']));

					$risque->setVars('nomElement', $element['nomElement'], true, 'UTF-8');
					$risque->setVars('identifiantRisque', $element['identifiantRisque'], true, 'UTF-8');
					$risque->setVars('quotationRisque', $element['quotationRisque'], true, 'UTF-8');
					$risque->setVars('nomDanger', $element['nomDanger'], true, 'UTF-8');
					$risque->setVars('commentaireRisque', $element['commentaireRisque'], true, 'UTF-8');
					$risque->setVars('actionPrevention', $element['actionPrevention'], true, 'UTF-8');
					$risque->setVars('methodeElement', $element['methodeElement'], true, 'UTF-8');

					if(is_file(EVA_GENERATED_DOC_DIR . $element['photoAssociee'])){
						$risque->setImage('photoAssociee', EVA_GENERATED_DOC_DIR . $element['photoAssociee'], digirisk_options::getOptionValue('taille_photo_poste_fiche_de_poste'));
					}
					else{
						$risque->setVars('photoAssociee', digirisk_tools::slugify_noaccent(__('Aucun photo d&eacute;finie', 'evarisk')));
					}

					$risque->merge();
				}
			}
		}

		return $risque;
	}

	/**
	*	Allows to affect documents to corrective actions
	*/
	function document_box_caller(){
		$postBoxTitle = __('Documents', 'evarisk');
		$postBoxId = 'postBoxDocument';
		add_meta_box($postBoxId, $postBoxTitle, array('eva_gestionDoc', 'correctiv_action_document_box'), PAGE_HOOK_EVARISK_TACHE, 'rightSide', 'default');
		add_meta_box($postBoxId, $postBoxTitle, array('eva_gestionDoc', 'correctiv_action_document_box'), PAGE_HOOK_EVARISK_ACTIVITE, 'rightSide', 'default');
	}

	/**
	*	Define a box for associating document to corrective action
	*
	*	@return string $box_content The html output for the current box
	*/
	function correctiv_action_document_box($arguments){
		$box_content = '';

		/*	Check if allwoed extension are set into option 	*/
		$digi_ac_allowed_ext = digirisk_options::getOptionValue('digi_ac_allowed_ext', 'digirisk_options');
		if(is_array($digi_ac_allowed_ext) && (count($digi_ac_allowed_ext) > 0)){
			$idUpload = 'correctiv_action_document_' . $arguments['tableElement'];
			$allowedExtensions = "['" . implode("', '", $digi_ac_allowed_ext) . "']";
			$multiple = true;
			$actionUpload = str_replace('\\', '/', EVA_LIB_PLUGIN_URL . "gestionDocumentaire/uploadFile.php");

			$display_button = true;
			switch($arguments['tableElement']){
				case TABLE_TACHE:{
					$repertoireDestination = '';
					$table = $arguments['tableElement'];

					$currentTask = new EvaTask($arguments['idElement']);
					$currentTask->load();
					$ProgressionStatus = $currentTask->getProgressionStatus();

					if((($ProgressionStatus == 'Done') || ($ProgressionStatus == 'DoneByChief')) && (digirisk_options::getOptionValue('possibilite_Modifier_Tache_Soldee') == 'non') ){
						$display_button = false;
						$box_content .= '
			<div class="alignright button-primary clear" id="TaskSaveButton" >
				' . __('Cette t&acirc;che est sold&eacute;e, vous ne pouvez pas ajouter de photos', 'evarisk') . '
			</div>';
					}
				}break;
				case TABLE_ACTIVITE:{
					$repertoireDestination = '';
					$table = $arguments['tableElement'];

					$current_action = new EvaActivity($arguments['idElement']);
					$current_action->load();
					$ProgressionStatus = $current_action->getProgressionStatus();

					if((($ProgressionStatus == 'Done') || ($ProgressionStatus == 'DoneByChief')) && (digirisk_options::getOptionValue('possibilite_Modifier_Action_Soldee') == 'non') ){
						$display_button = false;
						$box_content .= '
			<div class="alignright button-primary clear" id="TaskSaveButton" >
				' . __('Cette t&acirc;che est sold&eacute;e, vous ne pouvez pas ajouter de photos', 'evarisk') . '
			</div>';
					}
				}break;
			}

			if($display_button){
				$box_content = eva_gestionDoc::getFormulaireUpload($table, $arguments['tableElement'], $arguments['idElement'], $repertoireDestination, $idUpload, $allowedExtensions, $multiple, $actionUpload, __('S&eacute;lectionner un document &agrave; envoyer', 'evarisk')) . sprintf(__('Liste des extensions autoris&eacute;es %s', 'evarisk'), implode(", ", $digi_ac_allowed_ext));
			}
		}
		else{
			$box_content = sprintf(__('Vous n\'avez pas s&eacute;lectionn&eacute; les extensions &agrave; autoriser. %s', 'evarisk'), '<a href="' . admin_url("options-general.php?page=" . DIGI_URL_SLUG_MAIN_OPTION . "#digirisk_options_correctivaction") . '" >' . __('Ajouter des extensions', 'evarisk') . '</a>');
		}

		$box_content .= '<hr/>';

		/*	Get document already associated to current element	*/
		$correctiv_action_associated_doc = eva_gestionDoc::get_associated_document_list($arguments['tableElement'], $arguments['idElement']);
		$box_content .= '
<div >
	<div class="hide digi_' . $arguments['tableElement'] . '_associated_document" >&nbsp;</div>
	<div class="digi_correctiv_action_document_list" >' . $correctiv_action_associated_doc . '</div>
</div>';

		echo $box_content;
	}

	/**
	*	Function allowing to display the complete list of asscoiated coument for a given element
	*/
	function get_associated_document_list($tableElement, $idElement, $document_category = "", $document_order = "dateCreation DESC"){
		$document_list_output = '';

		$associated_document_list = eva_gestionDoc::getDocumentList($tableElement, $idElement, $document_category, $document_order);
		if(count($associated_document_list) > 0){
		$document_list_output .= '
<table summary="Document list associated to ' . $tableElement . '" cellpadding="0" cellspacing="0" class="associated_document_list" >';
			foreach($associated_document_list as $doc){
				$doc_creator = evaUser::getUserInformation($doc->idCreateur);
				$document_list_output .= '
	<tr id="associated_document_line_' . $doc->id . '" >
		<td>
			&nbsp;&nbsp;&nbsp;- (' . ELEMENT_IDENTIFIER_DOC . $doc->id . ')&nbsp;&nbsp;' . $doc->nom . '
		</td>
		<td class="associated_document_list_download_link" >';
				if(is_file(EVA_GENERATED_DOC_DIR . $doc->chemin . $doc->nom)){
					$document_list_output .= '
			<div class="alignright delete_document_button_container" ><img id="delete_document_' . $doc->id . '" src="' . EVA_IMG_ICONES_PLUGIN_URL . 'delete_vs.png" alt="' . __('Supprimer ce document', 'evarisk') . '" title="' . __('Supprimer ce document', 'evarisk') . '" class="alignright delete_associated_document" /></div>
			<span class="ui-icon alignright element_associated_document_info" id="infos_element_associated_document' . $doc->id . '" >&nbsp;</span><a href="' . EVA_GENERATED_DOC_URL . $doc->chemin . $doc->nom . '" target="associated_document_dl_file" >' . __('T&eacute;l&eacute;charger ce fichier', 'evarisk') . '</a>
			<div class="clear hide associated_document_infos_container alignright" id="element_associated_document' . $doc->id . '" >' . sprintf(__('Ajout&eacute; le %s &agrave; %s par %s', 'evarisk'), mysql2date('d F Y', $doc->dateCreation, true), mysql2date('H:i', $doc->dateCreation, true), $doc_creator[$doc->idCreateur]['user_lastname'] . '&nbsp;' . $doc_creator[$doc->idCreateur]['user_firstname']) . '</div>';
				}
				else{
					$document_list_output .= __('Impossible de trouver le fichier sur le disque', 'evarisk');
				}
				$document_list_output .=
		'</td>
	</tr>';
			}
			$document_list_output .= '
</table>
<script type="text/javascript" >
	digirisk(document).ready(function(){
		jQuery(".element_associated_document_info").click(function(){
			jQuery("#" + jQuery(this).attr("id").replace("infos_", "")).toggle();
		});

		jQuery(".delete_associated_document").click(function(){
			if(confirm(digi_html_accent_for_js("' . __('&Ecirc;tes vous s&ucirc;r de vouloir supprimer ce document?', 'evarisk') . '"))){
				jQuery("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
					"post":"true",
					"table":"' . TABLE_GED_DOCUMENTS . '",
					"tableElement":"' . $tableElement . '",
					"idElement":"' . $idElement . '",
					"act":"delete_document",
					"idDocument":jQuery(this).attr("id").replace("delete_document_", "")
				});
			}
		});
	});
</script>';
		}
		else{
			$document_list_output .= __('Aucun document n\'a &eacute;t&eacute; associ&eacute; pour le moment', 'evarisk');
		}

		return $document_list_output;
	}

	/**
	 *	Generate a form to save work unit sheet collection for a groupment
	 *
	 *	@param mixed $tableElement The element type we want to get form for
	 *	@param integer $idElement The element identifier we wan to get form for
	 *
	 *	@return string The hmtl code outputing the form to generate work unit sheet collection for a groupment
	 */
	function getRiskListingGenerationForm($tableElement, $idElement) {
		$tableElementForDoc = $tableElement . '_RS';
// 		<div id="workUnitSheetCollectionModelSelector" >
// 		<div>
// 		<input type="checkbox" id="modelDefaut" checked="checked" name="modelUse" value="modeleDefaut" />
// 		<label for="modelDefaut" style="vertical-align:middle;" >' . __('Utiliser le mod&egrave;le par d&eacute;faut', 'evarisk') . '</label>
// 		</div>
// 		<div id="modelListForGeneration" style="display:none;" >&nbsp;</div>
// 		</div>

		$output = '
<table summary="" border="0" cellpadding="0" cellspacing="0" align="center" class="tabcroisillon" style="width:100%;" >
	<tr>
		<td id="documentFormContainer" >';
		if ( $tableElement == TABLE_GROUPEMENT ) {
			$output .= '<div><input type="checkbox" checked="checked" class="clear" value="yes" id="recursiv_mode" name="recursiv_mode" /> <label for="recursiv_mode" >' . __('Lister les risques de mani&egrave;re r&eacute;cursive', 'evarisk') . '</label></div>';
		}
		$output .= '<input type="button" class="clear button-primary" value="' . __('G&eacute;n&eacute;rer la synth&egrave;se des risques', 'evarisk') . '" id="save_list_risk" />
		</td>
		<td id="documentModelContainer" >&nbsp;</td>
	</tr>
</table>
<script type="text/javascript" >
	digirisk("#save_list_risk").click(function() {
		var recursiv_mode = false;
		if (jQuery("#recursiv_mode").is(":checked")) {
			recursiv_mode = true;
		}
		digirisk("#documentFormContainer").html(digirisk("#loadingImg").html());
		digirisk("#documentFormContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
			"post":"true",
			"table":"' . TABLE_GED_DOCUMENTS . '",
			"act":"save_list_risk",
			"recursiv_mode": recursiv_mode,
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
	function getRiskListingGenerationHistory($tableElement, $idElement) {
		$output = '';

		$ficheDePoste_du_Groupement = eva_gestionDoc::getDocumentList($tableElement, $idElement, 'fiche_de_poste_groupement', "dateCreation DESC");
		if (count($ficheDePoste_du_Groupement) > 0) {
			foreach ($ficheDePoste_du_Groupement as $fdpGpt) {
				if (is_file(EVA_GENERATED_DOC_DIR . $fdpGpt->chemin . $fdpGpt->nom)) {
					$output .= '-&nbsp;' . sprintf(__('G&eacute;n&eacute;r&eacute; le %s: (%s) <a href="%s" >%s</a>', 'evarisk'), mysql2date('d M Y', $fdpGpt->dateCreation, true), ELEMENT_IDENTIFIER_GFP . $fdpGpt->id, EVA_GENERATED_DOC_URL . $fdpGpt->chemin . $fdpGpt->nom, $fdpGpt->nom) . '<br/>';
				}
			}
		}
		else {
			$output .= __('Aucune fiche n\'a &eacute;t&eacute; cr&eacute;e pour le moment', 'evarisk');
		}

		return $output;
	}
}