<?php

require_once(EVA_LIB_PLUGIN_DIR . 'evaRecommandation/evaRecommandationCategory.class.php');
class evaRecommandation
{

	function evaRecommandationMainPage()
	{
		_e(EvaDisplayDesign::afficherDebutPage(__('Pr&eacute;conisations', 'evarisk'), EVA_OPTIONS_ICON, __('Pr&eacute;conisations', 'evarisk'), __('Pr&eacute;conisations', 'evarisk'), TABLE_PRECONISATION, false, '', false));
?>
<div id="ajax-response" ></div>
<div class="hide" id="loadingImg" ><center><img class="margin36" src="<?php echo _e(PICTO_LOADING_ROUND); ?>" alt="loading..." /></center></div>
<?php echo evaRecommandation::getRecommandationForm() . evaRecommandationCategory::getRecommandationCategoryForm(); ?>
<div id="recommandationTable" >
<?php
		_e(evaRecommandation::getRecommandationTable());
?>
</div>
<?php
	}

	function getRecommandationTable()
	{
		unset($titres,$classes, $idLignes, $lignesDeValeurs);
		$idLignes = null;
		$idTable = 'evaRecommandation';
		$titres[] = __("Cat&eacute;gorie de la pr&eacute;conisation", 'evarisk');
		$titres[] = __("Intitul&eacute;", 'evarisk');
		$titres[] = __("Description", 'evarisk');
		// $titres[] = __("Ic&ocirc;ne", 'evarisk');
		$titres[] = __("Actions", 'evarisk');
		$classes[] = '';
		$classes[] = '';
		$classes[] = '';
		// $classes[] = '';
		$classes[] = 'recommandationActionColumn';

		$recommandationList = evaRecommandation::getRecommandationList();
		unset($ligneDeValeurs);
		$i=0;
		if(count($recommandationList) > 0)
		{
			foreach($recommandationList as $recommandation)
			{
				if($recommandation->id <= 0)
				{
					$idLignes[] = 'recommandation' . rand();
				}
				else
				{
					$idLignes[] = 'recommandation-id-' . $recommandation->id;
				}

				$lignesDeValeurs[$i][] = array('value' => ucfirst($recommandation->recommandation_category_name), 'class' => '');
				/**
				*	Can not make it working for the first version
				 . '<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'delete_vs.png" alt="' . __('Supprimer cette cat&eacute;gorie de pr&eacute;conisation', 'evarisk') . '" title="' . __('Supprimer cette cat&eacute;gorie de pr&eacute;conisation', 'evarisk') . '" class="alignright deleteRecommandationCategory" /><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'edit_vs.png" alt="' . __('&Eacute;diter cette cat&eacute;gorie de pr&eacute;conisation', 'evarisk') . '" title="' . __('&Eacute;diter cette cat&eacute;gorie de pr&eacute;conisation', 'evarisk') . '" class="alignright editRecommandationCategory" />'
				*/

				if(($recommandation->nom == NULL))
				{
					$lignesDeValeurs[$i][] = array('value' => __('Aucune pr&eacute;conisation pour cette cat&eacute;gorie.', ''), 'class' => '');
					$lignesDeValeurs[$i][] = array('value' => '', 'class' => '');
					// $lignesDeValeurs[$i][] = array('value' => '', 'class' => $recommandation->nom);
					$lignesDeValeurs[$i][] = array('value' => '', 'class' => '');
				}
				elseif(($recommandation->nom == 'Add'))
				{
					$lignesDeValeurs[$i][] = array('value' => '<span id="recoCat' . $recommandation->recommandation_category_id . '" class="addNewRecommandation pointer" >' . __('Ajouter une pr&eacute;conisation', '') . '</span>', 'class' => '');
					$lignesDeValeurs[$i][] = array('value' => '', 'class' => '');
					// $lignesDeValeurs[$i][] = array('value' => '', 'class' => $recommandation->nom);
					$lignesDeValeurs[$i][] = array('value' => '<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'add_vs.png" alt="' . __('Ajouter une pr&eacute;conisation', 'evarisk') . '" title="' . __('Ajouter une pr&eacute;conisation', 'evarisk') . '" class="alignright addRecommandation" id="recoCat' . $recommandation->recommandation_category_id . '" />', 'class' => '');
				}
				else
				{
					$lignesDeValeurs[$i][] = array('value' => ucfirst($recommandation->nom), 'class' => '');
					$lignesDeValeurs[$i][] = array('value' => ucfirst($recommandation->description), 'class' => '');
					// $lignesDeValeurs[$i][] = array('value' => '', 'class' => $recommandation->nom);
					$lignesDeValeurs[$i][] = array('value' => '<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'delete_vs.png" alt="' . __('Supprimer cette pr&eacute;conisation', 'evarisk') . '" title="' . __('Supprimer cette pr&eacute;conisation', 'evarisk') . '" class="alignright deleteRecommandation" /><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'edit_vs.png" alt="' . __('&Eacute;diter cette pr&eacute;conisation', 'evarisk') . '" title="' . __('&Eacute;diter cette pr&eacute;conisation', 'evarisk') . '" class="alignright editRecommandation" />', 'class' => '');
				}
				$i++;
			}
		}
		else
		{
			$idLignes[] = 'recommandation' . $recommandation->id;
			$lignesDeValeurs[$i][] = array('value' => '', 'class' => '');
			$lignesDeValeurs[$i][] = array('value' => __('Aucune cat&eacute;gorie n\'a &eacute;t&eacute; trouv&eacute;e', 'evarisk'), 'class' => '');
			$lignesDeValeurs[$i][] = array('value' => '', 'class' => '');
			// $lignesDeValeurs[$i][] = array('value' => '', 'class' => '');
			$lignesDeValeurs[$i][] = array('value' => '<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'add_vs.png" alt="' . __('Ajouter une cat&eacute;gorie de pr&eacute;conisation', 'evarisk') . '" title="' . __('Ajouter une cat&eacute;gorie de pr&eacute;conisation', 'evarisk') . '" class="alignright addRecommandationCategory" />', 'class' => '');
		}

		$script = '<script type="text/javascript">
			evarisk(document).ready(function(){
				evarisk("#' . $idTable . ' tfoot").remove();
				oTable = evarisk("#' . $idTable . '").dataTable({
					"fnDrawCallback": function ( oSettings ) {
						if ( oSettings.aiDisplay.length == 0 ){
							return;
						}
						var nTrs = evarisk("#' . $idTable . ' tbody tr");
						var iColspan = nTrs[0].getElementsByTagName("td").length;
						var sLastGroup = "";
						var ntrsLength = nTrs.length;
						for(i=0; i < ntrsLength; i++){
							var iDisplayIndex = oSettings._iDisplayStart + i;
							var sGroup = oSettings.aoData[ oSettings.aiDisplay[iDisplayIndex] ]._aData[0];
							if ( sGroup != sLastGroup ){
								var nGroup = document.createElement( "tr" );
								var nCell = document.createElement( "td" );
								nCell.colSpan = iColspan;
								nCell.className = "group";
								nCell.innerHTML = sGroup;
								nGroup.appendChild( nCell );
								nTrs[i].parentNode.insertBefore( nGroup, nTrs[i] );
								sLastGroup = sGroup;
							}
						}
					},
					"aoColumns": [ 
						{ "bVisible":    false },
						null,
						null,
						null
					],
					"bPaginate": false,
					"bInfo": false,
					"bLengthChange": false,
					"oLanguage": {
						"sUrl": "' . EVA_INC_PLUGIN_URL . 'js/dataTable/jquery.dataTables.common_translation.txt"
					}
				});

				evarisk(".deleteRecommandationCategory").click(function(){
					if(confirm(convertAccentToJS("' . __('&Ecirc;tes vous s&ucirc;r de vouloir supprimer cet cat&eacute;gorie de pr&eacute;conisation? \r\n!!!ATTENTION!!! Toutes les pr&eacute;conisation de cette cat&eacute;gorie ne seront plus accessible', 'evarisk') . '"))){
						evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
						{
							"post":"true",
							"table":"' . TABLE_CATEGORIE_PRECONISATION . '",
							"act":"deleteCategoryRecommandation",
							"id":evarisk(this).parent("td").parent("tr").attr("id").replace("recommandation-id-", "")
						});
					}
				});

				evarisk(".deleteRecommandation").click(function(){
					if(confirm(convertAccentToJS("' . __('&Ecirc;tes vous s&ucirc;r de vouloir supprimer cet &eacute;l&eacute;ment?', 'evarisk') . '"))){
						evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
						{
							"post":"true",
							"table":"' . TABLE_PRECONISATION . '",
							"act":"deleteRecommandation",
							"id":evarisk(this).parent("td").parent("tr").attr("id").replace("recommandation-id-", "")
						});
					}
				});
				evarisk(".addRecommandation, .addNewRecommandation").click(function(){
					evarisk("#recommandationForm").dialog("open");
					evarisk("#id_categorie_preconisation").val(evarisk(this).attr("id").replace("recoCat", ""));
				});
				evarisk(".editRecommandation").click(function(){
					evarisk("#recommandationFormContent").hide();
					evarisk("#loadingRecommandationForm").html(evarisk("#loadingImg").html());
					evarisk("#loadingRecommandationForm").show();
					evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
					{
						"post":"true",
						"table":"' . TABLE_PRECONISATION . '",
						"act":"loadInformation",
						"id":evarisk(this).parent("td").parent("tr").attr("id").replace("recommandation-id-", "")
					});
					evarisk("#recommandationForm").dialog("open");
				});
			});
		</script>';

		return EvaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script);
	}

	function getRecommandationList()
	{
		global $wpdb;

		$query = $wpdb->prepare(
			"(SELECT RECOMMANDATION.*, RECOMMANDATION_CAT.nom AS recommandation_category_name, RECOMMANDATION_CAT.id AS recommandation_category_id
			FROM " . TABLE_CATEGORIE_PRECONISATION . " AS RECOMMANDATION_CAT
				LEFT JOIN " . TABLE_PRECONISATION . " AS RECOMMANDATION ON ((RECOMMANDATION.id_categorie_preconisation = RECOMMANDATION_CAT.id) AND (RECOMMANDATION.status = 'valid'))
			WHERE RECOMMANDATION_CAT.status = 'valid' )
				UNION
			(SELECT '', '', '', '', 'Add', '', RECOMMANDATION_CAT.nom AS recommandation_category_name, RECOMMANDATION_CAT.id AS recommandation_category_id
			FROM " . TABLE_CATEGORIE_PRECONISATION . " AS RECOMMANDATION_CAT
			WHERE RECOMMANDATION_CAT.status = 'valid')");
		$recommandationList = $wpdb->get_results($query);

		return $recommandationList;
	}

	function getRecommandation($recommandationId)
	{
		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT *
			FROM " . TABLE_PRECONISATION . "
			WHERE id = %d ", $recommandationId);

		return $wpdb->get_row($query);
	}

	function saveRecommandation($recommandationsinformations)
	{
		global $wpdb;

		$whatToUpdate = eva_database::prepareQuery($recommandationsinformations, 'creation');
		$query = $wpdb->prepare(
			"INSERT INTO " . TABLE_PRECONISATION . " 
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

	function updateRecommandation($recommandationsinformations, $id)
	{
		global $wpdb;
		$reponseRequete = '';

		$whatToUpdate = eva_database::prepareQuery($recommandationsinformations, 'update');
		$query = $wpdb->prepare(
			"UPDATE " . TABLE_PRECONISATION . " 
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

	function getRecommandationsPostBoxBody($arguments)
	{
		$idElement = $arguments['idElement'];
		$tableElement = $arguments['tableElement'];

		$categoryList = evaRecommandationCategory::getCategoryRecommandationList();
		echo EvaDisplayInput::afficherComboBox($categoryList, 'recommandationCategory', __('Cat&eacute;gorie', 'evarisk'), 'recommandationCategory', "", "") . '
		<div id="recommandationFormContent" >
			<label for="efficacite_preconisation">' . $variable->nom . '</label>
			<input type="text" class="sliderValue" disabled="disabled" id="efficacite_preconisation" name="efficacite_preconisation" /><div id="slider-efficacite_preconisation" class="slider_variable"></div>
			<label for="commentaire_preconisation" >' . __('Commentaire', 'evarisk') . '</label>
			<textarea id="commentaire_preconisation" name="" rows="3" cols="10" class="recommandationInput" ></textarea>
		</div>
		<script type="text/javascript">
			evarisk(document).ready(function(){
				evarisk("#slider-efficacite_preconisation").slider({
					range:"min",
					value:0,
					min:0,
					max:100,
					slide:function(event, ui){
						evarisk("#efficacite_preconisation").val(ui.value);
					}
				});
				evarisk("#efficacite_preconisation").val(evarisk("#slider-efficacite_preconisation").slider("value"));
			});
		</script>';
	}

	function getRecommandationForm()
	{
?>
<div id="recommandationForm" class="hide" title="<?php _e('Pr&eacute;conisation', 'evarisk'); ?>" >
	<script type="text/javascript" >
	evarisk(document).ready(function(){
		var nom_preconisation = evarisk("#nom_preconisation"), description_preconisation = evarisk("#description_preconisation"), id_preconisation = evarisk("#id_preconisation"), id_categorie_preconisation = evarisk("#id_categorie_preconisation"), recommandationFields = evarisk( [] ).add( nom_preconisation ).add( description_preconisation ).add( id_preconisation ).add( id_categorie_preconisation );
		var tips = evarisk(".recommandationFormErrorMessage");
		evarisk("#recommandationForm").dialog({
			autoOpen: false,
			height: 300,
			width: 350,
			modal: true,
			buttons:{
				"<?php _e('Enregistrer', 'evarisk'); ?>": function(){
					var formIsValid = true;

					formIsValid = formIsValid && checkLength( nom_preconisation, "username", 1, 128, "<?php _e('Le nom de la pr&eacute;conisation ne peut pas &ecirc;tre vide', 'evarisk'); ?>" );

					if(formIsValid){
						evarisk("#ajax-response").load("<?php _e(EVA_INC_PLUGIN_URL); ?>ajax.php", 
						{
							"post":"true",
							"table":"<?php _e(TABLE_PRECONISATION); ?>",
							"act":"saveRecommandation",
							"nom_preconisation": nom_preconisation.val(),
							"description_preconisation": description_preconisation.val(),
							"id_categorie_preconisation": id_categorie_preconisation.val(),
							"id_preconisation": id_preconisation.val()
						});
						evarisk(this).dialog( "close" );
					}
				},
				Cancel: function(){
					evarisk(this).dialog("close");
				}
			},
			close: function() {
				recommandationFields.val("");
				evarisk("#ui-dialog-title-recommandationForm").html("<?php _e('Ajouter une pr&eacute;conisation', 'evarisk'); ?>");
			}
		});
	});
	</script>
	<p class="recommandationFormErrorMessage ">&nbsp;</p>
	<form action="" >
	<fieldset>
		<div id="recommandationFormContent" >
			<input type="hidden" name="id_categorie_preconisation" id="id_categorie_preconisation" class="recommandationInput" value="" />
			<input type="hidden" name="id_preconisation" id="id_preconisation" class="recommandationInput" value="" />
			<label for="nom_preconisation" ><?php _e('Nom', 'evarisk'); ?></label>
			<input type="text" name="nom_preconisation" id="nom_preconisation" class="recommandationInput" value="" />
			<label for="description_preconisation" ><?php _e('Description', 'evarisk'); ?></label>
			<textarea rows="3" cols="10" name="description_preconisation" id="description_preconisation" class="recommandationInput" ></textarea>
		</div>
		<div id="loadingRecommandationForm" class="hide" ></div>
	</fieldset>
	</form>
</div>
<?php
	}

}
