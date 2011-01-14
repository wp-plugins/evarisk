<?php
/**
 * 
 * 
 * @author Evarisk
 * @version v5.0
 */

class gestionDoc {

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
							evarisk("#modelListForDUERGeneration").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_GED_DOCUMENTS . '", "act":"loadDocument", "tableElement":"' . $tableElement . '", "idElement":' . $idElement . ', "category":"document_unique"});
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
			default:
				$categorie = $table;
			break;
		}

		/*	Determination of the file name	*/
		$nomDocument = basename($fichier);

		/*	Determination of the file directory	*/
		$cheminDocument = dirname($fichier) . '/';

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
					echo '<script type="text/javascript" >evarisk("#modelListForDUERGeneration").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_GED_DOCUMENTS . '", "act":"loadDocument", "tableElement":"' . $tableElement . '", "idElement":' . $idElement . ', "category":"document_unique"});evarisk("#moreModelChoice").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true", "table":"' . TABLE_GED_DOCUMENTS . '", "act":"loadExistingDocument", "tableElement":"' . $tableElement . '", "idElement":' . $idElement . '});</script>';
				}
			}
		}
	}

	function getDocumentList($tableElement, $idElement, $category = "")
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
			ORDER BY nom ASC",
			$tableElement, $idElement);
		return $wpdb->get_results($query);
	}

	function getCompleteDocumentList($category = "", $morequery = "")
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
			ORDER BY nom ASC",
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

}