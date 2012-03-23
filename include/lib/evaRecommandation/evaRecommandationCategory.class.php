<?php
/**
 * Recommandation category management file
 * 
 * @author Evarisk
 * @version v5.0
 */
 
/**
 * Recommandation category management class
 * 
 * @author Evarisk
 * @version v5.0
 */
class evaRecommandationCategory
{
	/**
	*
	*/
	function get_recommandation_category_id($informations_to_get = array('id'), $conditions = ''){
		global $wpdb;
		$informations = '';

		$query = $wpdb->prepare("SELECT " . implode(', ', $informations_to_get) . " FROM " . TABLE_CATEGORIE_PRECONISATION . " WHERE 1" . $conditions);

		if(count($informations_to_get) == 1){
			$informations = $wpdb->get_var($query);
		}
		else{
			$informations = $wpdb->get_results($query);
		}

		return $informations;
	}

/**
*	Get the existing recommandation category list
*
*	@return object $CategoryRecommandationList A wordpresse database object containing the complete result list
*/
	function getCategoryRecommandationList(){
		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT RECOMMANDATION_CAT.*, PIC.photo
			FROM " . TABLE_CATEGORIE_PRECONISATION . " AS RECOMMANDATION_CAT
				LEFT JOIN " . TABLE_PHOTO_LIAISON . " AS LINK_ELT_PIC ON ((LINK_ELT_PIC.idElement = RECOMMANDATION_CAT.id) AND (tableElement = '" . TABLE_CATEGORIE_PRECONISATION . "'))
				LEFT JOIN " . TABLE_PHOTO . " AS PIC ON ((PIC.id = LINK_ELT_PIC.idPhoto) AND (LINK_ELT_PIC.isMainPicture = 'yes'))
			WHERE RECOMMANDATION_CAT.status = 'valid'");

		$CategoryRecommandationList = $wpdb->get_results($query);

		return $CategoryRecommandationList;
	}

/**
*	Get a specific recommandation category
*
*	@param integer $categoryRecommandationId The category identifier we want to get information about
*
*	@return object A wordpresse database object containing the recommandation category informations
*/
	function getCategoryRecommandation($categoryRecommandationId)
	{
		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT *
			FROM " . TABLE_CATEGORIE_PRECONISATION . "
			WHERE id = %d ", $categoryRecommandationId);

		return $wpdb->get_row($query);
	}

/**
*	Save a new recommandation category in database
*
*	@param array $categoryRecommandationInformations An array with the different information we want to set for the new recommandation category
*
*	@return string $reponseRequete A message that allows to know if the recommandation category creation has been done correctly or not
*/
	function saveRecommandationCategory($categoryRecommandationInformations)
	{
		global $wpdb;

		$whatToUpdate = eva_database::prepareQuery($categoryRecommandationInformations, 'creation');
		$query = $wpdb->prepare(
			"INSERT INTO " . TABLE_CATEGORIE_PRECONISATION . " 
			(" . implode(', ', $whatToUpdate['fields']) . ")
			VALUES
			(" . implode(', ', $whatToUpdate['values']) . ") "
		);

		if( $wpdb->query($query) )
		{
			$reponseRequete = 'done';
		}
		else
		{
			$reponseRequete = 'error';
		}

		return $reponseRequete;
	}

/**
*	Update an existing recommandation category in database
*
*	@param array $categoryRecommandationInformations An array with the different information we want to set for the recommandation category
*
*	@return string $reponseRequete A message that allows to know if the recommandation category update has been done correctly or not
*/
	function updateRecommandationCategory($categoryRecommandationInformations, $id)
	{
		global $wpdb;
		$reponseRequete = '';

		$whatToUpdate = eva_database::prepareQuery($categoryRecommandationInformations, 'update');
		$query = $wpdb->prepare(
			"UPDATE " . TABLE_CATEGORIE_PRECONISATION . " 
			SET " . implode(', ', $whatToUpdate['values']) . "
			WHERE id = '%s' ",
			$id
		);

		if( $wpdb->query($query) )
		{
			$reponseRequete = 'done';
		}
		elseif( $wpdb->query($query) == 0 )
		{
			$reponseRequete = 'nothingToUpdate';
		}
		else
		{
			$reponseRequete = 'error';
		}

		return $reponseRequete;
	}

/**
*	Get the output for the recommandation categories list for a category
*
*	@param string $outputMode Define the output type we want to get for the recommandation categories list
*
*	@return mixed $categoryListOutput The complete output
*/
	function getCategoryRecommandationListOutput($outputMode = 'pictos', $selectedRecommandationCategory = '')
	{
		$categoryListOutput = '';
		$categoryList = evaRecommandationCategory::getCategoryRecommandationList();
		if($outputMode == 'pictos')
		{
			foreach($categoryList as $category)
			{
				$recommandationMainPicture = evaPhoto::checkIfPictureIsFile($category->photo, TABLE_CATEGORIE_PRECONISATION);
				if(!$recommandationMainPicture)
				{
					$recommandationMainPicture = '';
				}
				else
				{
					$checked =  $selectedClass =  '';
					if(($selectedRecommandationCategory != '') && ($selectedRecommandationCategory == $category->id))
					{
						$checked =  ' checked="checked" ';
						$selectedClass = 'recommandationCategorySelected';
					}
					$recommandationMainPicture = '<div class="alignleft recommandationCategoryBloc ' . $selectedClass . '" ><label for="recommandationCategory' . $category->id . '" ><img class="recommandationDefaultPictosList" src="' . $recommandationMainPicture . '" alt="' . ucfirst(strtolower($category->nom)) . '" title="' . ELEMENT_IDENTIFIER_P . $category->id . '&nbsp;-&nbsp;' . ucfirst(strtolower($category->nom)) . '" /></label><input class="hide recommandationCategory" type="radio" ' . $checked . ' id="recommandationCategory' . $category->id . '" name="recommandationCategory" value="' . $category->id . '" /></div>';
				}
				$categoryListOutput .= $recommandationMainPicture;
				$i++;
			}
		}
		elseif($outputMode == 'selectablelist')
		{
			$categoryListOutput = EvaDisplayInput::afficherComboBox($categoryList, 'recommandationCategory', __('Cat&eacute;gorie', 'evarisk'), 'recommandationCategory', "", "");
		}

		return $categoryListOutput;
	}


/**
*	Get the form to manage recommandation categories
*
*	@return mixed The complete output for the recommandation management form
*/
	function getRecommandationCategoryForm()
	{
?>
<div id="recommandationCategoryInterfaceContainer" class="hide" title="<?php _e('&Eacute;diter une famille de pr&eacute;conisation', 'evarisk'); ?>" >
	<div id="recommandationCategoryFormContainer" class="hide" >&nbsp;</div>
	<div id="loadingCategoryRecommandationForm" class="hide" >&nbsp;</div>
</div>
<?php
	}

/**
*	Get the form to manage recommandation categories
*
*	@return mixed The complete output for the recommandation category management form
*/
	function recommandationCategoryForm($id_categorie_preconisation = '', $recommandationCategoryInfos = '')
	{
		$nom_categorie = $impressionRecommandation = $impressionRecommandationCategorie = $tailleimpressionCategorie = $tailleImpressionPictoUniquement = $tailleImpressionPicto = '';
		if(($recommandationCategoryInfos != '') && (is_object($recommandationCategoryInfos)))
		{
			$nom_categorie = html_entity_decode($recommandationCategoryInfos->nom, ENT_QUOTES, 'UTF-8');
			$impressionRecommandation = html_entity_decode($recommandationCategoryInfos->impressionRecommandation, ENT_QUOTES, 'UTF-8');
			$tailleimpressionRecommandation = html_entity_decode($recommandationCategoryInfos->tailleimpressionRecommandation, ENT_QUOTES, 'UTF-8');
			$impressionRecommandationCategorie = html_entity_decode($recommandationCategoryInfos->impressionRecommandationCategorie, ENT_QUOTES, 'UTF-8');
			$tailleimpressionRecommandationCategorie = html_entity_decode($recommandationCategoryInfos->tailleimpressionRecommandationCategorie, ENT_QUOTES, 'UTF-8');
		}

		$dialogWidth = $basicDialogWidth = 350;
		$showGalery = false;
		if(($id_categorie_preconisation != '') && ($id_categorie_preconisation > 0))
		{
			$dialogWidth = 800;
			$showGalery = true;
		}
?>
<script type="text/javascript" >
	evarisk(document).ready(function(){
		evarisk(".impressionRecommandation").click(function(){
			evarisk("#impressionRecommandationValue").val(evarisk(this).val());
			if(evarisk(this).val() == 'textonly'){
				evarisk("#pictureRecommandationContainer").hide();
			}
			else{
				evarisk("#pictureRecommandationContainer").show();
			}
		});
		evarisk(".impressionRecommandationCategorie").click(function(){
			evarisk("#impressionRecommandationCategorieValue").val(evarisk(this).val());
			if(evarisk(this).val() == 'textonly'){
				evarisk("#pictureRecommandationCategoryContainer").hide();
			}
			else{
				evarisk("#pictureRecommandationCategoryContainer").show();
			}
		});

		evarisk("#recommandationCategoryInterfaceContainer").dialog({
			autoOpen: false,
			height: 360,
			width: <?php _e($dialogWidth); ?>,
			modal:  false,
			buttons:{
<?php 
			if(current_user_can('digi_edit_recommandation_cat'))
			{
?>
				"<?php _e('Enregistrer', 'evarisk'); ?>": function(){
					var formIsValid = true;
						evarisk("#nom_categorie").removeClass("ui-state-error");

					formIsValid = formIsValid && checkLength( evarisk("#nom_categorie"), "", 1, 128, "<?php _e('Le champs nom de la famille de pr&eacute;conisation doit contenir entre !#!minlength!#! et !#!maxlength!#! caract&egrave;res', 'evarisk'); ?>" , evarisk( ".recommandationCategoryFormErrorMessage" ));

					if(formIsValid){
						evarisk("#ajax-response").load("<?php _e(EVA_INC_PLUGIN_URL); ?>ajax.php", 
						{
							"post":"true",
							"table":"<?php _e(TABLE_CATEGORIE_PRECONISATION); ?>",
							"act":"saveRecommandationCategorie",
							"nom_categorie": evarisk("#nom_categorie").val(),
							"impressionRecommandation": evarisk("#impressionRecommandationValue").val(),
							"tailleimpressionRecommandation": evarisk("#tailleimpressionRecommandation").val(),
							"impressionRecommandationCategorie": evarisk("#impressionRecommandationCategorieValue").val(),
							"tailleimpressionRecommandationCategorie": evarisk("#tailleimpressionRecommandationCategorie").val(),
							"id_categorie_preconisation": evarisk("#id_categorie_preconisation").val()
						});
						evarisk(this).dialog( "close" );
					}
				},
<?php
			}
?>
				"<?php _e('Annuler', 'evarisk'); ?>": function(){
					evarisk(this).dialog("close");
				}
			},
			close: function(){
				evarisk("#nom_categorie").val("");
				evarisk("#impressionRecommandationValue").val("");
				evarisk("#tailleimpressionRecommandation").val("");
				evarisk("#impressionRecommandationCategorieValue").val("");
				evarisk("#tailleimpressionRecommandationCategorie").val("");
				evarisk("#id_categorie_preconisation").val("");
			}
		});
	});
</script>
<p class="recommandationCategoryFormErrorMessage">&nbsp;</p>
<form action="" >
	<fieldset>
		<div id="recommandationCategoryForm" class="alignleft recommandationInterfaceContainerPart" >
			<input type="hidden" name="id_categorie_preconisation" id="id_categorie_preconisation" class="recommandationInput" value="<?php _e($id_categorie_preconisation); ?>" />
			<input type="hidden" name="impressionRecommandationCategorieValue" id="impressionRecommandationCategorieValue" class="recommandationInput" value="<?php _e($impressionRecommandationCategorie); ?>" />
			<input type="hidden" name="impressionRecommandationValue" id="impressionRecommandationValue" class="recommandationInput" value="<?php _e($impressionRecommandation); ?>" />
			<label for="nom_categorie" ><?php _e('Nom', 'evarisk'); ?></label>
			<input type="text" name="nom_categorie" id="nom_categorie" class="recommandationInput" value="<?php _e($nom_categorie); ?>" />
				<!--	Option recommandation category picture in document	-->
				<div class="recommandationCategoryOption" ><?php _e('Param&egrave;tres pour l\'impression de la famille de pr&eacute;conisations', 'evarisk'); ?></div>
				<div class="recommandationCategoryOptionChoice" ><input type="radio" class="impressionRecommandationCategorie" name="impressionRecommandationCategorie" id="impressionRecommandationCategorie_textandpicture" value="textandpicture" <?php (($impressionRecommandationCategorie == '') || ($impressionRecommandationCategorie == 'textandpicture')) ? _e('checked = "checked"') : ''; ?> /><label for="impressionRecommandationCategorie_textandpicture" class="recommandationCategoryOptionLabel" ><?php _e('Nom + image', 'evarisk'); ?></label></div>
				<div class="recommandationCategoryOptionChoice" ><input type="radio" class="impressionRecommandationCategorie" name="impressionRecommandationCategorie" id="impressionRecommandationCategorie_textonly" value="textonly" <?php (($impressionRecommandationCategorie != '') && ($impressionRecommandationCategorie == 'textonly')) ? _e('checked = "checked"') : ''; ?> /><label for="impressionRecommandationCategorie_textonly" class="recommandationCategoryOptionLabel" ><?php _e('Nom uniquement', 'evarisk'); ?></label></div>
				<div class="recommandationCategoryOptionChoice" ><input type="radio" class="impressionRecommandationCategorie" name="impressionRecommandationCategorie" id="impressionRecommandationCategorie_pictureonly" value="pictureonly" <?php (($impressionRecommandationCategorie != '') && ($impressionRecommandationCategorie == 'pictureonly')) ? _e('checked = "checked"') : ''; ?> /><label for="impressionRecommandationCategorie_pictureonly" class="recommandationCategoryOptionLabel" ><?php _e('Image uniquement', 'evarisk'); ?></label></div>
				<div class="recommandationCategoryOptionPicSizeContainer" id="pictureRecommandationCategoryContainer" ><label for="tailleimpressionRecommandationCategorie" class="recommandationCategoryOptionLabel" ><?php _e('Taille de l\'image (en cm)', 'evarisk'); ?></label><input type="text" name="tailleimpressionRecommandationCategorie" id="tailleimpressionRecommandationCategorie" value="<?php _e($tailleimpressionRecommandationCategorie); ?>" /></div>
				<div class="clear" >&nbsp;</div>
				<!--	Option recommandation picture in document	-->
				<div class="recommandationCategoryOption" ><?php _e('Param&egrave;tres pour l\'impression des pr&eacute;conisations de cette famille', 'evarisk'); ?></div>
				<div class="recommandationCategoryOptionChoice" ><input type="radio" class="impressionRecommandation" name="impressionRecommandation" id="impressionRecommandation_textandpicture" value="textandpicture" <?php (($impressionRecommandation == '') || ($impressionRecommandation == 'textandpicture')) ? _e('checked = "checked"') : ''; ?> /><label for="impressionRecommandation_textandpicture" class="recommandationCategoryOptionLabel" ><?php _e('Nom + image', 'evarisk'); ?></label></div>
				<div class="recommandationCategoryOptionChoice" ><input type="radio" class="impressionRecommandation" name="impressionRecommandation" id="impressionRecommandation_textonly" value="textonly" <?php (($impressionRecommandation != '') && ($impressionRecommandation == 'textonly')) ? _e('checked = "checked"') : ''; ?> /><label for="impressionRecommandation_textonly" class="recommandationCategoryOptionLabel" ><?php _e('Nom uniquement', 'evarisk'); ?></label></div>
				<div class="recommandationCategoryOptionChoice" ><input type="radio" class="impressionRecommandation" name="impressionRecommandation" id="impressionRecommandation_pictureonly" value="pictureonly" <?php (($impressionRecommandation != '') && ($impressionRecommandation == 'pictureonly')) ? _e('checked = "checked"') : ''; ?> /><label for="impressionRecommandation_pictureonly" class="recommandationCategoryOptionLabel" ><?php _e('Image uniquement', 'evarisk'); ?></label></div>
				<div class="recommandationCategoryOptionPicSizeContainer" id="pictureRecommandationContainer" ><label for="tailleimpressionRecommandation" class="recommandationCategoryOptionLabel" ><?php _e('Taille de l\'image (en cm)', 'evarisk'); ?></label><input type="text" name="tailleimpressionRecommandation" id="tailleimpressionRecommandation" value="<?php _e($tailleimpressionRecommandation); ?>" /></div>
		</div>
<?php
		if($showGalery)
		{
?>
		<div id="recommandationCategoryPictureGalery" class="hide alignright recommandationInterfaceContainerPart" >
			<div id="pictureUploadForm<?php _e(TABLE_CATEGORIE_PRECONISATION); ?>_<?php _e($id_categorie_preconisation); ?>" ><?php _e(evaPhoto::getUploadForm(TABLE_CATEGORIE_PRECONISATION, $id_categorie_preconisation)); ?></div>
			<div id="pictureGallery<?php _e(TABLE_CATEGORIE_PRECONISATION); ?>_<?php _e($id_categorie_preconisation); ?>" >&nbsp;</div>
		</div>
<?php
		}
?>
	</fieldset>
</form>
<?php
	}
}
