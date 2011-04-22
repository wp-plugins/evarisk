<?php
/**
 * 
 * 
 * @author Evarisk
 * @version v5.0
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
	function getFormulaireUpload($table, $tableElement, $idElement, $repertoireDestination, $idUpload, $allowedExtensions, $multiple, $actionUpload, $texteBoutton = '')
	{
		require_once(EVA_LIB_PLUGIN_DIR . 'upload.php' );

		$texteBoutton = ($texteBoutton == '') ? __("Envoyer un fichier", "evarisk") : $texteBoutton;
		$actionUpload = ($actionUpload == '') ? EVA_INC_PLUGIN_URL . 'gestionDocumentaire/uploadFile.php' : $actionUpload;
		$repertoireDestination = ($repertoireDestination == '') ? str_replace('\\', '/', EVA_UPLOADS_PLUGIN_DIR . $tableElement . '/' . $idElement . '/') : $repertoireDestination;
		$multiple = $multiple ? 'true' : 'false';

		$formulaireUpload = 
			'<script type="text/javascript">        
				evarisk(document).ready(function(){
					var uploader' . $idUpload . ' = new qq.FileUploader({
						element: document.getElementById("' . $idUpload . '"),
						action: "' . $actionUpload . '",
						allowedExtensions: ' . $allowedExtensions . ',
						multiple: ' . $multiple . ',
						params: {
							"table": "' . $table . '",
							"tableElement": "' . $tableElement . '",
							"idElement": "' . $idElement . '",
							"folder": "' . $repertoireDestination . '",
							"abspath": "' . str_replace("\\", "/", ABSPATH) . '",
							"evarisk": "' . str_replace("\\", "/", EVA_HOME_DIR . "evarisk.php") . '"
						},
						onComplete: function(file, response){
							evarisk(".qq-upload-list").hide();';

		switch($table)
		{
			case TABLE_DUER:
				$formulaireUpload .= '
							evarisk("#modelListForGeneration").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_GED_DOCUMENTS . '", "act":"loadDocument", "tableElement":"' . $tableElement . '", "idElement":' . $idElement . ', "category":"document_unique"});
							evarisk("#moreModelChoice").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_GED_DOCUMENTS . '", "act":"loadExistingDocument", "tableElement":"' . $tableElement . '", "idElement":' . $idElement . '});';
			break;
			case TABLE_FP:
				$formulaireUpload .= '
							evarisk("#modelListForGeneration").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_GED_DOCUMENTS . '", "act":"loadDocument", "tableElement":"' . $tableElement . '", "idElement":' . $idElement . ', "category":"fiche_de_poste"});
							evarisk("#moreModelChoice").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_GED_DOCUMENTS . '", "act":"loadExistingDocument", "tableElement":"' . $tableElement . '", "idElement":' . $idElement . '});';
			break;
		}

		$formulaireUpload .= 
						'}
					});

					evarisk(".qq-upload-button").each(function(){
						evarisk(this).html("' . $texteBoutton . '");
						uploader' . $idUpload . '._button = new qq.UploadButton({
							element: uploader' . $idUpload . '._getElement("button"),
							multiple: ' . $multiple . ',
							onChange: function(input){
								uploader' . $idUpload . '._onInputChange(input);
							}
						});
					});
					evarisk(".qq-upload-drop-area").each(function(){
						evarisk(this).html("<span>' . __("D&eacute;poser les fichiers ici pour les t&eacute;l&eacute;charger", "evarisk") . '</span>");
					});
					setTimeout(function(){
						evarisk(".qq-upload-button").width("100%");
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

	function saveNewDoc($table, $tableElement, $idElement, $fichier)
	{
		global $wpdb;
		global $current_user;
		$status = 'error';

		/*	Determination of the file category	*/
		switch($table)
		{
			case TABLE_DUER:
				$categorie = 'document_unique';
			break;
			case TABLE_FP:
				$categorie = 'fiche_de_poste';
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
				('', 'valid', NOW(), %d, %d, %s, %s, %s, %s)",
			$current_user->ID, $idElement, $tableElement, $categorie, $nomDocument, $cheminDocument);
		if($wpdb->query($query))
		{
			$status = 'ok';
		}

		return $status;
	}

	function duplicateDocument($tableElement, $idElement, $idDocument)
	{
		global $wpdb;

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
					echo '<script type="text/javascript" >evarisk("#modelListForGeneration").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_GED_DOCUMENTS . '", "act":"loadDocument", "tableElement":"' . $tableElement . '", "idElement":' . $idElement . ', "category":"document_unique"});evarisk("#moreModelChoice").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_GED_DOCUMENTS . '", "act":"loadExistingDocument", "tableElement":"' . $tableElement . '", "idElement":' . $idElement . '});</script>';
				}
			}
		}
	}

	function getDocumentList($tableElement, $idElement, $category = "", $order = "nom ASC")
	{
		global $wpdb;

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
		return $wpdb->get_results($query);
	}

	function getCompleteDocumentList($category = "", $morequery = "", $order = "nom ASC")
	{
		global $wpdb;

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

		return $wpdb->get_results($query);
	}

	function getDocumentPath($id)
	{
		global $wpdb;
		$path = '';

		$query = $wpdb->prepare(
			"SELECT chemin, nom
			FROM " . TABLE_GED_DOCUMENTS . "
			WHERE id = %d",
		$id);
		$pathComponents = $wpdb->get_row($query);

		$path = $pathComponents->chemin . $pathComponents->nom;

		return $path;
	}

	function getDefaultDocument($category)
	{
		global $wpdb;

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

		return $documentDefaultId->id;
	}

	/**
	*	Generate an output of summary about the risk on an element. Could be a "single document" or a "work unit sheet"
	*
	*	@param mixed $tableElement The element type we want to generate the document for
	*	@param integer $idElement The element identifier we want to generate the document for
	*	@param mixed $outputType The output we want to get (html, odt, ...)
	*	@param integer $documentId The identifier of a specific document we want to get
	*
	*	@return mixed Depending on the output type we ask for, an html output or a file
	*/
	function generateSummaryDocument($tableElement, $idElement, $outputType, $documentId = '')
	{
		require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/documentUnique/templateDocumentUnique.tpl.php');
		require_once(EVA_LIB_PLUGIN_DIR . 'gestionDocumentaire/gestionDoc.class.php');
		require_once(EVA_LIB_PLUGIN_DIR . 'arborescence/arborescence_special.class.php');

		switch($tableElement)
		{
			case TABLE_GROUPEMENT:
			{
				/**
				*	Get the last summary document generated for the current element OR Get a given generated summary document
				*/
				if($documentId != '')
				{
					$lastDocument = eva_documentUnique::getDernierDocumentUnique($tableElement, $idElement, $documentId);
				}
				else
				{
					$lastDocument = eva_documentUnique::getDernierDocumentUnique($tableElement, $idElement);
				}
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
			}
			break;
			case TABLE_UNITE_TRAVAIL:
			{
				/**
				*	Get the last summary document generated for the current element OR Get a given generated summary document
				*/
				if($documentId != '')
				{
					$lastDocument = eva_WorkUnitSheet::getGeneratedDocument($tableElement, $idElement, 'last', $documentId);
				}
				else
				{
					$lastDocument = eva_WorkUnitSheet::getGeneratedDocument($tableElement, $idElement, 'last');
				}
				$odfModelFile = EVA_MODELES_PLUGIN_DIR . 'ficheDePoste/modeleDefaut.odt';
			}
			break;
		}

		/**
		*	If user ask for an "odt" file we include different librairies and model
		*/
		if($outputType == 'odt')
		{
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
			if($lastDocument->id_model > 1)
			{
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
									' . eva_documentUnique::readListeGroupesUtilisateurs($groupesUtilisateur, 'html') . '
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
									' . eva_documentUnique::readlisteGroupeUtilisateurAffectes($groupesUtilisateursAffectes, '', 'html') . '
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
									' . eva_documentUnique::readBilanParUnite($bilanParUnite, '', 'html') . '
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
		elseif($outputType == 'odt')
		{
			switch($tableElement)
			{
				case TABLE_GROUPEMENT:
				{
					$documentUniqueParam['#NOMENTREPRISE#'] = str_replace('<br />', "
", eva_tools::slugify_noaccent($documentUniqueParam['#NOMENTREPRISE#']));
					$odf->setVars('nomEntreprise', $documentUniqueParam['#NOMENTREPRISE#']);

					$documentUniqueParam['#NOMPRENOMEMETTEUR#'] = str_replace('<br />', "
", eva_tools::slugify_noaccent($documentUniqueParam['#NOMPRENOMEMETTEUR#']));
					$odf->setVars('emetteurDUER', $documentUniqueParam['#NOMPRENOMEMETTEUR#']);

					$documentUniqueParam['#NOMPRENOMDESTINATAIRE#'] = str_replace('<br />', "
", eva_tools::slugify_noaccent($documentUniqueParam['#NOMPRENOMDESTINATAIRE#']));
					$odf->setVars('destinataireDUER', $documentUniqueParam['#NOMPRENOMDESTINATAIRE#']);

					$documentUniqueParam['#TELFIXE#'] = str_replace('<br />', "
", eva_tools::slugify_noaccent($documentUniqueParam['#TELFIXE#']));
					$odf->setVars('telephone', $documentUniqueParam['#TELFIXE#']);

					$documentUniqueParam['#TELMOBILE#'] = str_replace('<br />', "
", eva_tools::slugify_noaccent($documentUniqueParam['#TELMOBILE#']));
					$odf->setVars('portable', $documentUniqueParam['#TELMOBILE#']);

					$documentUniqueParam['#TELFAX#'] = str_replace('<br />', "
", eva_tools::slugify_noaccent($documentUniqueParam['#TELFAX#']));
					$odf->setVars('fax', $documentUniqueParam['#TELFAX#']);

					$odf->setVars('dateGeneration', $documentUniqueParam['#DATE#']);
					$finAudit = '';if(($documentUniqueParam['#FINAUDIT#'] != '') && ($documentUniqueParam['#FINAUDIT#'] != $documentUniqueParam['#DEBUTAUDIT#'])){$finAudit = __(' au ', 'evarisk') . $documentUniqueParam['#FINAUDIT#'];}
					$odf->setVars('dateAudit', $documentUniqueParam['#DEBUTAUDIT#'] . $finAudit);

					$documentUniqueParam['#DISPODESPLANS#'] = str_replace('<br />', "
", eva_tools::slugify_noaccent($documentUniqueParam['#DISPODESPLANS#']));
					if(trim($documentUniqueParam['#DISPODESPLANS#']) == '')
					{
						$documentUniqueParam['#DISPODESPLANS#'] = __('La localisation n\'a pas &eacute;t&eacute; pr&eacute;cis&eacute;e', 'evarisk');
					}
					$odf->setVars('dispoDesPlans', str_replace('<br />', "
", eva_tools::slugify_noaccent($documentUniqueParam['#DISPODESPLANS#'])));

					$odf->setVars('remarqueImportante', str_replace('<br />', "
", eva_tools::slugify_noaccent($documentUniqueParam['#ALERTE#'])));

					$odf->setVars('sources', str_replace('<br />', "
", eva_tools::slugify_noaccent($documentUniqueParam['#SOURCES#'])));

					// $odf->setVars('methodologie', $documentUniqueParam['#METHODOLOGIE#']);

					{/*	Remplissage du template pour les groupes d'utilisateurs	*/
						$listeUserGroupe = array();
						$listeDesGroupes = unserialize($lastDocument->groupesUtilisateurs);
						$listeUserGroupe = eva_documentUnique::readListeGroupesUtilisateurs($listeDesGroupes, 'print');

						$userGroup = $odf->setSegment('groupesUtilisateurs');
						foreach($listeUserGroupe AS $element)
						{
							$element['userGroupName'] = str_replace('<br />', "
", eva_tools::slugify_noaccent($element['userGroupName']));
							$element['userGroupName'] = str_replace('&nbsp;', ' ', $element['userGroupName']);
							$element['userGroupDescription'] = str_replace('<br />', "
", eva_tools::slugify_noaccent($element['userGroupDescription']));
							$element['userGroupDescription'] = str_replace('&nbsp;', ' ', $element['userGroupDescription']);
							$userGroup->nomGroupe(eva_tools::slugify_noaccent($element['userGroupName']));
							$userGroup->descriptionGroupe(eva_tools::slugify_noaccent($element['userGroupDescription']));
							$userGroup->nombreUtilisateursGroupe($element['userGroupTotalUserNumber']);
							$userGroup->merge();
						}
						$odf->mergeSegment($userGroup);
					}

					{/*	Remplissage du template pour les groupes d'utilisateurs affectes	*/
						$listeUserGroupe = array();
						$listeDesGroupesAffectes = unserialize($lastDocument->groupesUtilisateursAffectes);
						$listeUserGroupe = eva_documentUnique::readlisteGroupeUtilisateurAffectes($listeDesGroupesAffectes, '', 'print');

						$userGroupAffected = $odf->setSegment('groupesUtilisateursAffectes');
						foreach($listeUserGroupe AS $element)
						{
							$element['nomElement'] = str_replace('<br />', "
", eva_tools::slugify_noaccent($element['nomElement']));

							$userGroupAffected->nomElement($element['nomElement']);
							$userGroupAffected->listeGroupes(eva_tools::slugify_noaccent($element['listeGroupes']));
							$userGroupAffected->merge();
						}
						$odf->mergeSegment($userGroupAffected);
					}

					{/*	Remplissage du template pour les risques unitaires	*/
						$listeRisques = unserialize($lastDocument->risquesUnitaires);
						$listeRisque = eva_documentUnique::readBilanUnitaire($listeRisques);

						/*	Risques innacceptable	*/
						$risque = $odf->setSegment('risq80');
						if( is_array($listeRisque[SEUIL_BAS_INACCEPTABLE]) )
						{
							foreach($listeRisque[SEUIL_BAS_INACCEPTABLE] AS $elements) {
								foreach($elements AS $element) {
									$element['nomElement'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['nomElement']));
									$element['quotationRisque'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['quotationRisque']));
									$element['nomDanger'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['nomDanger']));
									$element['commentaireRisque'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['commentaireRisque']));

									$risque->setVars('nomElement', $element['nomElement'], true, 'UTF-8');
									$risque->setVars('quotationRisque', $element['quotationRisque'], true, 'UTF-8');
									$risque->setVars('nomDanger', $element['nomDanger'], true, 'UTF-8');
									$risque->setVars('commentaireRisque', $element['commentaireRisque'], true, 'UTF-8');
							
									$risque->merge();
								}
							}
						}
						$odf->mergeSegment($risque);

						/*	Risques a traiter	*/
						$risque = $odf->setSegment('risq51');
						if( is_array($listeRisque[SEUIL_BAS_ATRAITER]) )
						{
							foreach($listeRisque[SEUIL_BAS_ATRAITER] AS $elements) {
								foreach($elements AS $element) {
									$element['nomElement'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['nomElement']));
									$element['quotationRisque'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['quotationRisque']));
									$element['nomDanger'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['nomDanger']));
									$element['commentaireRisque'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['commentaireRisque']));

									$risque->setVars('nomElement', $element['nomElement'], true, 'UTF-8');
									$risque->setVars('quotationRisque', $element['quotationRisque'], true, 'UTF-8');
									$risque->setVars('nomDanger', $element['nomDanger'], true, 'UTF-8');
									$risque->setVars('commentaireRisque', $element['commentaireRisque'], true, 'UTF-8');

									$risque->merge();
								}
							}
						}
						$odf->mergeSegment($risque);

						/*	Risques a planifier	*/
						$risque = $odf->setSegment('risq48');
						if( is_array($listeRisque[SEUIL_BAS_APLANIFIER]) )
						{
							foreach($listeRisque[SEUIL_BAS_APLANIFIER] AS $elements) {
								foreach($elements AS $element) {
									$element['nomElement'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['nomElement']));
									$element['quotationRisque'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['quotationRisque']));
									$element['nomDanger'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['nomDanger']));
									$element['commentaireRisque'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['commentaireRisque']));

									$risque->setVars('nomElement', $element['nomElement'], true, 'UTF-8');
									$risque->setVars('quotationRisque', $element['quotationRisque'], true, 'UTF-8');
									$risque->setVars('nomDanger', $element['nomDanger'], true, 'UTF-8');
									$risque->setVars('commentaireRisque', $element['commentaireRisque'], true, 'UTF-8');

									$risque->merge();
								}
							}
						}
						$odf->mergeSegment($risque);

						/*	Risques faible	*/
						$risque = $odf->setSegment('risq');
						if( is_array($listeRisque[SEUIL_BAS_FAIBLE]) )
						{
							foreach($listeRisque[SEUIL_BAS_FAIBLE] AS $elements) {
								foreach($elements AS $element) {
									$element['nomElement'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['nomElement']));
									$element['quotationRisque'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['quotationRisque']));
									$element['nomDanger'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['nomDanger']));
									$element['commentaireRisque'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['commentaireRisque']));

									$risque->setVars('nomElement', $element['nomElement'], true, 'UTF-8');
									$risque->setVars('quotationRisque', $element['quotationRisque'], true, 'UTF-8');
									$risque->setVars('nomDanger', $element['nomDanger'], true, 'UTF-8');
									$risque->setVars('commentaireRisque', $element['commentaireRisque'], true, 'UTF-8');

									$risque->merge();
								}
							}
						}
						$odf->mergeSegment($risque);
					}

					{/*	Remplissage du template pour les risques par groupement et unité	*/
						$listeGroupement = array();
						$bilanParUnite = unserialize($lastDocument->risquesParUnite);
						$listeGroupement = eva_documentUnique::readBilanParUnite($bilanParUnite, '', 'print');

						$risqueParFiche = $odf->setSegment('risqueFiche');
						foreach($listeGroupement AS $element){
							$element['nomElement'] = str_replace('<br />', "
			", eva_tools::slugify_noaccent($element['nomElement']));

							$risqueParFiche->nomElement($element['nomElement']);
							$risqueParFiche->quotationTotale($element['quotationTotale']);

							$risqueParFiche->merge();
						}
						$odf->mergeSegment($risqueParFiche);
					}

					{/*	Remplissage du template pour les actions correctives	*/
						$ac = arborescence_special::arborescenceActionCorrectives($tableElement, $idElement);
						$lac = arborescence_special::lectureArborescenceAC($ac, $tableElement, $tableElement);
						$odf->setVars('planDaction', str_replace('<br />', "
", eva_tools::slugify_noaccent($lac)));
					}

					$finalDir = EVA_RESULTATS_PLUGIN_DIR . 'documentUnique/' . $tableElement . '/' . $idElement . '/';
					$fileName = str_replace(' ', '',$lastDocument->nomDUER) . '_V' . $lastDocument->revisionDUER;
				}
				break;
				case TABLE_UNITE_TRAVAIL:
				{
					$workUnitinformations = eva_UniteDeTravail::getWorkingUnit($idElement);

					$odf->setVars('referenceUnite', $idElement);
					$odf->setVars('nomUnite', eva_tools::slugify_noaccent($workUnitinformations->nom));

					{/*	Remplissage du template pour les utilisateurs affectes	*/
						$listeUser = array();
						$listeUser = unserialize($lastDocument->users);

						$affectedUsers = $odf->setSegment('utilisateursAffectes');
						foreach($listeUser AS $element)
						{
							foreach($element AS $elementInfos)
							{
								$affectedUsers->setVars('idUtilisateur', $elementInfos['user_id'], true, 'UTF-8');
								$elementInfos['nomUtilisateur'] = str_replace('<br />', "
	", eva_tools::slugify_noaccent($elementInfos['nomUtilisateur']));
								$affectedUsers->setVars('nomUtilisateur', $elementInfos['user_lastname'], true, 'UTF-8');
								$elementInfos['prenomUtilisateur'] = str_replace('<br />', "
	", eva_tools::slugify_noaccent($elementInfos['prenomUtilisateur']));
								$affectedUsers->setVars('prenomUtilisateur', $elementInfos['user_firstname'], true, 'UTF-8');

								$affectedUsers->merge();
							}
						}
						$odf->mergeSegment($affectedUsers);
					}

					{/*	Remplissage du template pour les evaluateurs affectes	*/
						$listeUser = array();
						$listeUser = unserialize($lastDocument->evaluators);

						$affectedEvaluators = $odf->setSegment('utilisateursPresents');
						foreach($listeUser AS $element)
						{
							foreach($element AS $elementInfos)
							{
								$affectedEvaluators->setVars('idUtilisateur', $elementInfos['user_id'], true, 'UTF-8');
								$elementInfos['nomUtilisateur'] = str_replace('<br />', "
	", eva_tools::slugify_noaccent($elementInfos['nomUtilisateur']));
								$affectedEvaluators->setVars('nomUtilisateur', $elementInfos['user_lastname'], true, 'UTF-8');
								$elementInfos['prenomUtilisateur'] = str_replace('<br />', "
	", eva_tools::slugify_noaccent($elementInfos['prenomUtilisateur']));
								$affectedEvaluators->setVars('prenomUtilisateur', $elementInfos['user_firstname'], true, 'UTF-8');

								$affectedEvaluators->merge();
							}
						}
						$odf->mergeSegment($affectedEvaluators);
					}

					{/*	Remplissage du template pour les groupes d'utilisateurs affectes	*/
						$listeDesGroupesAffectes = array();
						$listeDesGroupesAffectes = unserialize($lastDocument->userGroups);

						$userGroupAffected = $odf->setSegment('gpUserAffected');
						foreach($listeDesGroupesAffectes AS $element)
						{
							$groupeName = str_replace('<br />', "
", eva_tools::slugify_noaccent($element[0]->user_group_name));
							$userGroupAffected->setVars('nomGroupe', $groupeName, true, 'UTF-8');
							$userGroupAffected->setVars('idGroupe', $element[0]->user_group_id, true, 'UTF-8');
							$userGroupAffected->setVars('nombreUtilisateur', $element[0]->TOTALUSERNUMBER, true, 'UTF-8');

							$userGroupAffected->merge();
						}
						$odf->mergeSegment($userGroupAffected);
					}

					{/*	Remplissage du template pour les risques unitaires	*/
						$listeRisques = unserialize($lastDocument->unitRisk);
						$listeRisque = eva_documentUnique::readBilanUnitaire($listeRisques);

						/*	Risques faible	*/
						$risque = $odf->setSegment('risq');
						if( is_array($listeRisque[SEUIL_BAS_FAIBLE]) )
						{
							foreach($listeRisque[SEUIL_BAS_FAIBLE] AS $elements)
							{
								foreach($elements AS $element)
								{
									$element['quotationRisque'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['quotationRisque']));
									$element['nomDanger'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['nomDanger']));
									$element['commentaireRisque'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['commentaireRisque']));

									$risque->setVars('quotationRisque', $element['quotationRisque'], true, 'UTF-8');
									$risque->setVars('nomDanger', $element['nomDanger'], true, 'UTF-8');
									$risque->setVars('commentaireRisque', $element['commentaireRisque'], true, 'UTF-8');

									$risque->merge();
								}
							}
						}
						$odf->mergeSegment($risque);

						/*	Risques a planifier	*/
						$risque = $odf->setSegment('risq48');
						if( is_array($listeRisque[SEUIL_BAS_APLANIFIER]) )
						{
							foreach($listeRisque[SEUIL_BAS_APLANIFIER] AS $elements)
							{
								foreach($elements AS $element)
								{
									$element['quotationRisque'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['quotationRisque']));
									$element['nomDanger'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['nomDanger']));
									$element['commentaireRisque'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['commentaireRisque']));

									$risque->setVars('quotationRisque', $element['quotationRisque'], true, 'UTF-8');
									$risque->setVars('nomDanger', $element['nomDanger'], true, 'UTF-8');
									$risque->setVars('commentaireRisque', $element['commentaireRisque'], true, 'UTF-8');

									$risque->merge();
								}
							}
						}
						$odf->mergeSegment($risque);

						/*	Risques a traiter	*/
						$risque = $odf->setSegment('risq51');
						if( is_array($listeRisque[SEUIL_BAS_ATRAITER]) )
						{
							foreach($listeRisque[SEUIL_BAS_ATRAITER] AS $elements)
							{
								foreach($elements AS $element) 
								{
									$element['quotationRisque'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['quotationRisque']));
									$element['nomDanger'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['nomDanger']));
									$element['commentaireRisque'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['commentaireRisque']));

									$risque->setVars('quotationRisque', $element['quotationRisque'], true, 'UTF-8');
									$risque->setVars('nomDanger', $element['nomDanger'], true, 'UTF-8');
									$risque->setVars('commentaireRisque', $element['commentaireRisque'], true, 'UTF-8');

									$risque->merge();
								}
							}
						}
						$odf->mergeSegment($risque);

						/*	Risques innacceptable	*/
						$risque = $odf->setSegment('risq80');
						if( is_array($listeRisque[SEUIL_BAS_INACCEPTABLE]) )
						{
							foreach($listeRisque[SEUIL_BAS_INACCEPTABLE] AS $elements)
							{
								foreach($elements AS $element)
								{
									$element['quotationRisque'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['quotationRisque']));
									$element['nomDanger'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['nomDanger']));
									$element['commentaireRisque'] = str_replace('<br />', "
", eva_tools::slugify_noaccent_no_utf8decode($element['commentaireRisque']));

									$risque->setVars('quotationRisque', $element['quotationRisque'], true, 'UTF-8');
									$risque->setVars('nomDanger', $element['nomDanger'], true, 'UTF-8');
									$risque->setVars('commentaireRisque', $element['commentaireRisque'], true, 'UTF-8');
							
									$risque->merge();
								}
							}
						}
						$odf->mergeSegment($risque);
					}

					{/*	Remplissage du template pour les préconisations afffectées à l'unité de travail	*/
						$listePreconisationsAffectees = array();
						$listePreconisationsAffectees = unserialize($lastDocument->recommandation);

						$afffectedRecommandation = $odf->setSegment('affectedRecommandation');
						foreach($listePreconisationsAffectees AS $recommandationCategory)
						{
							if(($recommandationCategory[0]['impressionRecommandationCategorie'] == 'textandpicture') || ($recommandationCategory[0]['impressionRecommandationCategorie'] == 'textonly'))
							{
								$recommandationCategoryName = str_replace('<br />', "
", eva_tools::slugify_noaccent($recommandationCategory[0]['recommandation_category_name']));
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
								$afffectedRecommandation->recommandations->setVars('recommandationName', str_replace('<br />', "
", eva_tools::slugify_noaccent($recommandation['recommandation_name'])));
								$afffectedRecommandation->recommandations->setVars('recommandationComment', str_replace('<br />', "
", eva_tools::slugify_noaccent($recommandation['commentaire'])));

								if(($recommandationCategory[0]['impressionRecommandation'] == 'pictureonly') || ($recommandationCategory[0]['impressionRecommandation'] == 'textandpicture'))
								{
									$recommandationIcon = evaPhoto::checkIfPictureIsFile($recommandation['photo'], TABLE_PRECONISATION);
									$recommandationIcon = str_replace(EVA_GENERATED_DOC_URL, EVA_GENERATED_DOC_DIR, $recommandationIcon);
									$recommandationIcon = str_replace(EVA_HOME_URL, EVA_HOME_DIR, $recommandationIcon);
									$afffectedRecommandation->recommandations->setImage('recommandationIcon', $recommandationIcon , $recommandationCategory[0]['tailleimpressionRecommandation']);
								}
								else
								{
								$afffectedRecommandation->recommandations->setVars('recommandationIcon', '');
								}

								$afffectedRecommandation->recommandations->merge();
							}

							$afffectedRecommandation->merge();
						}
						$odf->mergeSegment($afffectedRecommandation);
					}

					if(is_file(EVA_GENERATED_DOC_DIR . $lastDocument->defaultPicturePath))
					{
						$odf->setImage('photoDefault', EVA_GENERATED_DOC_DIR . $lastDocument->defaultPicturePath, options::getOptionValue('taille_photo_poste_fiche_de_poste'));
					}
					else
					{
						$odf->setVars('photoDefault', eva_tools::slugify_noaccent(__('Aucun photo d&eacute;finie', 'evarisk')));
					}

					$finalDir = EVA_RESULTATS_PLUGIN_DIR . 'ficheDePoste/' . $tableElement . '/' . $idElement . '/';
					$fileName = str_replace(' ', '',$lastDocument->name) . '_V' . $lastDocument->revision;
				}
				break;
			}

			if(!is_dir($finalDir))
			{
				eva_tools::make_recursiv_dir($finalDir);
			}
			$odf->saveToDisk($finalDir . $fileName . '.odt');
		}

	}

}