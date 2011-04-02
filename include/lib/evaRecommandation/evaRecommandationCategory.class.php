<?php

class evaRecommandationCategory
{

	function getCategoryRecommandationList()
	{
		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT RECOMMANDATION_CAT.*
			FROM " . TABLE_CATEGORIE_PRECONISATION . " AS RECOMMANDATION_CAT
			WHERE RECOMMANDATION_CAT.status = 'valid'");

		$CategoryRecommandationList = $wpdb->get_results($query);

		return $CategoryRecommandationList;
	}

	function getCategoryRecommandation($categoryRecommandationId)
	{
		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT *
			FROM " . TABLE_CATEGORIE_PRECONISATION . "
			WHERE id = %d ", $recommandationId);

		return $wpdb->get_row($query);
	}

	function saveRecommandation($categoryRecommandationInformations)
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

	function updateRecommandation($categoryRecommandationInformations, $id)
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

	function getRecommandationCategoryForm()
	{
?>
<div id="recommandationCategoryForm" class="hide" title="<?php _e('Cat&eacute;gorie de pr&eacute;conisation', 'evarisk'); ?>" >
	<script type="text/javascript" >
		evarisk(document).ready(function(){
			var nom_categorie = evarisk("#nom_categorie"), recommandationCategoryFields = evarisk( [] ).add( nom_categorie ), tips = evarisk( ".recommandationFormErrorMessage" );
			evarisk("#recommandationCategoryForm").dialog({
				autoOpen: false,
				height: 300,
				width: 350,
				modal: true,
				buttons:{
					// "' . __('Enregistrer', 'evarisk') . '": function(){
						
					// },
					Cancel: function(){
						evarisk(this).dialog("close");
					}
				},
				close: function() {
					recommandationCategoryFields.val("");
				}
			});
		});
	</script>
	<p class="recommandationFormErrorMessage">&nbsp;</p>
	<form action="" >
	<fieldset>
		<label for="nom_categorie" ><?php _e('Nom', 'evarisk'); ?></label>
		<input type="text" name="nom_categorie" id="nom_categorie" class="recommandationInput" value="" />
	</fieldset>
	</form>
</div>
<?php
	}
}
