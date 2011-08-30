<?php
/**
 * Recommandation management file
 * 
 * @author Evarisk
 * @version v5.0
 */

require_once(EVA_LIB_PLUGIN_DIR . 'evaRecommandation/evaRecommandationCategory.class.php');

/**
 * Recommandation management class
 * 
 * @author Evarisk
 * @version v5.0
 */
class evaRecommandation
{

/**************************************************************************************************************************************/
/******************************					GETTERS																								*****************************************/
/**************************************************************************************************************************************/

/**
*	Generate the output for the table recommandation management page
*
*	@return mixed The html output for the table
*/
	function getRecommandationTable()
	{
		unset($titres,$classes, $idLignes, $lignesDeValeurs);
		$idLignes = null;
		$idTable = 'evaRecommandation';
		$titres[] = __("Cat&eacute;gorie de la pr&eacute;conisation", 'evarisk');
		$titres[] = __("Ic&ocirc;ne", 'evarisk');
		$titres[] = __("Intitul&eacute;", 'evarisk');
		$titres[] = __("Description", 'evarisk');
		$titres[] = __("Actions", 'evarisk');
		$classes[] = '';
		$classes[] = 'recommandationIconColumn';
		$classes[] = '';
		$classes[] = '';
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

				$recommandationCategoryMainPicture = '';
				$mainPicture = evaPhoto::getMainPhoto(TABLE_CATEGORIE_PRECONISATION, $recommandation->recommandation_category_id);
				if($mainPicture != 'error')
				{
					if(is_file(EVA_HOME_DIR . $mainPicture))
					{
						$recommandationCategoryMainPicture = '<img class="recommandationCategoryDefaultPictosList" style="width:' . TAILLE_PICTOS . ';" src="' . EVA_HOME_URL . $mainPicture . '" alt="' . sprintf(__('Photo par d&eacute;faut pour %s', 'evarisk'), $recommandation->recommandation_category_name) . '" />';
					}
					elseif(is_file(EVA_GENERATED_DOC_DIR . $mainPicture))
					{
						$recommandationCategoryMainPicture = '<img class="recommandationCategoryDefaultPictosList" style="width:' . TAILLE_PICTOS . ';" src="' . EVA_GENERATED_DOC_URL . $mainPicture . '" alt="' . sprintf(__('Photo par d&eacute;faut pour %s', 'evarisk'), $recommandation->recommandation_category_name) . '" />';
					}
				}
				$recommandationCategoryButtonForCurrentUser = '';
				if(current_user_can('digi_delete_recommandation_cat'))
				{
					$recommandationCategoryButtonForCurrentUser .= '<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'delete_vs.png" alt="' . __('Supprimer cette cat&eacute;gorie de pr&eacute;conisation', 'evarisk') . '" title="' . __('Supprimer cette cat&eacute;gorie de pr&eacute;conisation', 'evarisk') . '" class="alignright deleteRecommandationCategory" onclick="javascript:deleteRecommandationCategory(\'' . $recommandation->recommandation_category_id . '\', \'' . TABLE_CATEGORIE_PRECONISATION . '\', \'' . __('&Ecirc;tes vous s&ucirc;r de vouloir supprimer cet &eacute;l&eacute;ment?', 'evarisk') . '\');" />';
				}
				if(current_user_can('digi_edit_recommandation_cat'))
				{
					$recommandationCategoryButtonForCurrentUser .= '<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'edit_vs.png" alt="' . __('&Eacute;diter cette cat&eacute;gorie de pr&eacute;conisation', 'evarisk') . '" title="' . __('&Eacute;diter cette cat&eacute;gorie de pr&eacute;conisation', 'evarisk') . '" class="alignright editRecommandationCategory" onclick="javascript:editRecommandationCategory(\'' . $recommandation->recommandation_category_id . '\', \'' . TABLE_CATEGORIE_PRECONISATION . '\');" />';
				}
				elseif(current_user_can('digi_view_detail_recommandation_cat'))
				{
					$recommandationCategoryButtonForCurrentUser .= '<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'view_vs.png" alt="' . __('Voir cette cat&eacute;gorie de pr&eacute;conisation', 'evarisk') . '" title="' . __('Voir cette cat&eacute;gorie de pr&eacute;conisation', 'evarisk') . '" class="alignright editRecommandationCategory" onclick="javascript:editRecommandationCategory(\'' . $recommandation->recommandation_category_id . '\', \'' . TABLE_CATEGORIE_PRECONISATION . '\');" />';
				}
				$lignesDeValeurs[$i][] = array('value' => ELEMENT_IDENTIFIER_CP . $recommandation->recommandation_category_id . '&nbsp;-&nbsp;' . $recommandationCategoryMainPicture . '&nbsp;&nbsp;' . ucfirst($recommandation->recommandation_category_name). $recommandationCategoryButtonForCurrentUser, 'class' => '');

				if(($recommandation->nom == NULL))
				{
					$lignesDeValeurs[$i][] = array('value' => '', 'class' => '');
					$lignesDeValeurs[$i][] = array('value' => __('Aucune pr&eacute;conisation pour cette cat&eacute;gorie.', ''), 'class' => '');
					$lignesDeValeurs[$i][] = array('value' => '', 'class' => '');
					$lignesDeValeurs[$i][] = array('value' => '', 'class' => '');
				}
				elseif(($recommandation->nom == 'Add'))
				{
					$lignesDeValeurs[$i][] = array('value' => '', 'class' => '');
					// $lignesDeValeurs[$i][] = array('value' => '<span id="recoCat-add-' . $recommandation->recommandation_category_id . '" class="addNewRecommandation pointer alignright" >' . __('Ajouter une pr&eacute;conisation', '') . '</span>', 'class' => '');
					$lignesDeValeurs[$i][] = array('value' => '', 'class' => '');
					$lignesDeValeurs[$i][] = array('value' => '', 'class' => '');
					if(current_user_can('digi_add_recommandation'))
					{
						$lignesDeValeurs[$i][] = array('value' => '<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'add_vs.png" alt="' . __('Ajouter une pr&eacute;conisation', 'evarisk') . '" title="' . __('Ajouter une pr&eacute;conisation', 'evarisk') . '" class="alignright addRecommandation" id="recoCat' . $recommandation->recommandation_category_id . '" />', 'class' => '');
					}
					else
					{
						$lignesDeValeurs[$i][] = array('value' => '', 'class' => '');
					}
				}
				else
				{
					$recommandationMainPicture = evaPhoto::checkIfPictureIsFile($recommandation->photo, TABLE_PRECONISATION);
					if(!$recommandationMainPicture)
					{
						$recommandationMainPicture = '';
					}
					else
					{
						$recommandationMainPicture = '<img class="recommandationDefaultPictosList" style="width:' . TAILLE_PICTOS . ';" src="' . $recommandationMainPicture . '" alt="' . sprintf(__('Photo par d&eacute;faut pour %s', 'evarisk'), $recommandation->nom) . '" />';
					}
					$lignesDeValeurs[$i][] = array('value' => $recommandationMainPicture, 'class' => '');
					$lignesDeValeurs[$i][] = array('value' => '<span class="pointer recommandationNameManagement" >' . ELEMENT_IDENTIFIER_P . $recommandation->id . '&nbsp;-&nbsp;' . ucfirst($recommandation->nom) . '</span>', 'class' => '');
					$lignesDeValeurs[$i][] = array('value' => ucfirst($recommandation->description), 'class' => '');
					/*	Check user right for output creation	*/
					$recommandationButtonForCurrentUser = '';
					if(current_user_can('digi_delete_recommandation'))
					{
						$recommandationButtonForCurrentUser .= '<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'delete_vs.png" alt="' . __('Supprimer cette pr&eacute;conisation', 'evarisk') . '" title="' . __('Supprimer cette pr&eacute;conisation', 'evarisk') . '" class="alignright deleteRecommandation" />';
					}
					if(current_user_can('digi_edit_recommandation'))
					{
						$recommandationButtonForCurrentUser .= '<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'edit_vs.png" alt="' . __('&Eacute;diter cette pr&eacute;conisation', 'evarisk') . '" title="' . __('&Eacute;diter cette pr&eacute;conisation', 'evarisk') . '" class="alignright editRecommandation" />';
					}
					elseif(current_user_can('digi_view_detail_recommandation'))
					{
						$recommandationButtonForCurrentUser .= '<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'view_vs.png" alt="' . __('Voir cette pr&eacute;conisation', 'evarisk') . '" title="' . __('Voir cette pr&eacute;conisation', 'evarisk') . '" class="alignright editRecommandation" />';
					}					
					
					$lignesDeValeurs[$i][] = array('value' => $recommandationButtonForCurrentUser, 'class' => '');
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
			$lignesDeValeurs[$i][] = array('value' => '', 'class' => '');
			$lignesDeValeurs[$i][] = array('value' => '<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'add_vs.png" alt="' . __('Ajouter une cat&eacute;gorie de pr&eacute;conisation', 'evarisk') . '" title="' . __('Ajouter une cat&eacute;gorie de pr&eacute;conisation', 'evarisk') . '" class="alignright addRecommandationCategory" />', 'class' => '');
		}

		$script = '<script type="text/javascript">
			evarisk(document).ready(function(){
				evarisk("#ui-dialog").hide();
				evarisk("#' . $idTable . ' tfoot").remove();
				evarisk("#' . $idTable . '").dataTable({
					"fnDrawCallback": function(oSettings){
						if (oSettings.aiDisplay.length == 0){
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
					"aoColumns":[ 
						{ "bVisible": false },
						null,
						null,
						null,
						null
					],
					"bPaginate": false,
					"bInfo": false,
					"bLengthChange": false,
					"oLanguage":{
						"sUrl": "' . EVA_INC_PLUGIN_URL . 'js/dataTable/jquery.dataTables.common_translation.txt"
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
					evarisk("#recommandationFormContainer").hide();
					evarisk("#loadingRecommandationForm").html(evarisk("#loadingImg").html());
					evarisk("#loadingRecommandationForm").show();
					evarisk("#recommandationInterfaceContainer").dialog("open");
					evarisk("#recommandationInterfaceContainer").dialog({title:"' . __('Ajouter une pr&eacute;conisation', 'evarisk') . '"});
					evarisk("#recommandationFormContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
					{
						"post":"true",
						"table":"' . TABLE_PRECONISATION . '",
						"act":"loadRecommandationManagementForm",
						"id_categorie_preconisation":evarisk(this).attr("id").replace("recoCat", "").replace("recoCat-add-", "")
					});
				});';
		if(current_user_can('digi_edit_recommandation') || current_user_can('digi_view_detail_recommandation'))
		{
			$script .= '
				evarisk(".editRecommandation, .recommandationDefaultPictosList, .recommandationNameManagement").click(function(){
					evarisk("#recommandationFormContainer").hide();
					evarisk("#loadingRecommandationForm").html(evarisk("#loadingImg").html());
					evarisk("#loadingRecommandationForm").show();
					evarisk("#recommandationInterfaceContainer").dialog("open");
					evarisk("#recommandationInterfaceContainer").dialog({title:"' . __('&Eacute;diter une pr&eacute;conisation', 'evarisk') . '"});
					evarisk("#recommandationFormContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
					{
						"post":"true",
						"table":"' . TABLE_PRECONISATION . '",
						"act":"loadRecommandationManagementForm",
						"id":evarisk(this).parent("td").parent("tr").attr("id").replace("recommandation-id-", "")
					});
				});';
		}
		$script .= '	
			});
		</script>';

		return EvaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script);
	}

/**
*	Get the complete list of existing recommandation with an extra result for the recommandation add line
*
*	@return object A wordpress database object with the complete result list
*/
	function getRecommandationList()
	{
		global $wpdb;

		$query = $wpdb->prepare(
			"(SELECT '' as id, '' as status, '' as id_categorie_preconisation, '' as creation_date, 'Add' as nom, '' as description, RECOMMANDATION_CAT.nom AS recommandation_category_name, RECOMMANDATION_CAT.id AS recommandation_category_id, '' as photo
			FROM " . TABLE_CATEGORIE_PRECONISATION . " AS RECOMMANDATION_CAT
			WHERE RECOMMANDATION_CAT.status = 'valid')
			UNION
			(SELECT RECOMMANDATION.*, RECOMMANDATION_CAT.nom AS recommandation_category_name, RECOMMANDATION_CAT.id AS recommandation_category_id, PIC.photo
			FROM " . TABLE_CATEGORIE_PRECONISATION . " AS RECOMMANDATION_CAT
				LEFT JOIN " . TABLE_PRECONISATION . " AS RECOMMANDATION ON ((RECOMMANDATION.id_categorie_preconisation = RECOMMANDATION_CAT.id) AND (RECOMMANDATION.status = 'valid'))
				LEFT JOIN " . TABLE_PHOTO_LIAISON . " AS LINK_ELT_PIC ON ((LINK_ELT_PIC.idElement = RECOMMANDATION.id) AND (LINK_ELT_PIC.tableElement = '" . TABLE_PRECONISATION . "') AND (LINK_ELT_PIC.isMainPicture = 'yes') AND (LINK_ELT_PIC.status = 'valid'))
				LEFT JOIN " . TABLE_PHOTO . " AS PIC ON ((PIC.id = LINK_ELT_PIC.idPhoto) AND (PIC.status = 'valid'))
			WHERE RECOMMANDATION_CAT.status = 'valid' )");
		$recommandationList = $wpdb->get_results($query);

		return $recommandationList;
	}

/**
*	Get a specific recommandation
*
*	@param integer $recommandationId The recommandation identifier we want to get
*
*	@return object A wordpress database object with the complete result
*/
	function getRecommandation($recommandationId)
	{
		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT *
			FROM " . TABLE_PRECONISATION . "
			WHERE id = %d ", $recommandationId);

		return $wpdb->get_row($query);
	}

/**
*	Get the output for the recommandation list for a category
*
*	@param integer $recommandationCategoryId The recommandation category identifier we want to get the recommandation for
*	@param string $outputMode Define the output type we want to get for the recommandation list
*
*	@return mixed $recommandationListOutput The complete output
*/
	function getRecommandationListByCategory($recommandationCategoryId, $outputMode = 'pictos', $selectedRecommandation = '')
	{
		global $wpdb;
		$recommandationListOutput = '';

		$query = $wpdb->prepare(
			"SELECT RECOMMANDATION.*, PIC.photo
			FROM " . TABLE_PRECONISATION . " AS RECOMMANDATION 
				LEFT JOIN " . TABLE_PHOTO_LIAISON . " AS LINK_ELT_PIC ON ((LINK_ELT_PIC.idElement = RECOMMANDATION.id) AND (tableElement = '" . TABLE_PRECONISATION . "') AND (LINK_ELT_PIC.isMainPicture = 'yes') AND (LINK_ELT_PIC.status = 'valid'))
				LEFT JOIN " . TABLE_PHOTO . " AS PIC ON ((PIC.id = LINK_ELT_PIC.idPhoto) AND (PIC.status = 'valid'))
			WHERE RECOMMANDATION.status = 'valid'
				AND RECOMMANDATION.id_categorie_preconisation = %d", $recommandationCategoryId);

		$recommandationList = $wpdb->get_results($query);

		if(count($recommandationList) <= 0)
		{
			$recommandationListOutput = __('Il n\'y a aucune pr&eacute;conisation pour cette cat&eacute;gorie', 'evarisk');
		}
		else
		{
			if($outputMode == 'pictos')
			{
				$i = 0;
				$selectedId = '';
				foreach($recommandationList as $recommandation)
				{
					$recommandationMainPicture = evaPhoto::checkIfPictureIsFile($recommandation->photo, TABLE_PRECONISATION);
					if(!$recommandationMainPicture)
					{
						$recommandationMainPicture = '';
						
						$recommandationMainPicture = '<img class="recommandationDefaultPictosList" style="width:' . TAILLE_PICTOS . ';" src="' . EVA_RECOMMANDATION_ICON . '" alt="' . sprintf(__('Photo par d&eacute;faut pour %s', 'evarisk'), $recommandation->nom) . '" />';
					}
					else
					{
						$checked = $selectedClass = '';
						if(($selectedRecommandation != '') && ($selectedRecommandation == $recommandation->id))
						{
							$checked =  ' checked="checked" ';
							$selectedClass = 'recommandationSelected';
							$selectedId = 'evarisk("#recommandation_id").val("' . $recommandation->id . '");
		evarisk("#recommandationNameIndication").html("' . __('pour ', 'evarisk') . ucfirst(strtolower($recommandation->nom)) . '");';
						}
						elseif($i == 0)
						{
							$checked =  ' checked="checked" ';
							$selectedClass = 'recommandationSelected default';
							$selectedId = 'evarisk("#recommandation_id").val("' . $recommandation->id . '");
		evarisk("#recommandationNameIndication").html("' . __('pour ', 'evarisk') . ucfirst(strtolower($recommandation->nom)) . '");';
						}
						$recommandationMainPicture = '<div class="alignleft recommandationBloc ' . $selectedClass . '" ><input class="recommandation" type="radio" ' . $checked . ' id="recommandation' . $recommandation->id . '" name="recommandation" value="' . $recommandation->id . '" /><label for="recommandation' . $recommandation->id . '" ><img class="recommandationDefaultPictosList" src="' . $recommandationMainPicture . '" alt="' . ucfirst(strtolower($recommandation->nom)) . '" title="' . ELEMENT_IDENTIFIER_P . $recommandation->id . '&nbsp;-&nbsp;' . ucfirst(strtolower($recommandation->nom)) . '" /></label></div>';
					}
					$recommandationListOutput .= $recommandationMainPicture;
					$i++;
				}
			}
			elseif($outputMode == 'selectablelist')
			{
				$recommandationListOutput = EvaDisplayInput::afficherComboBox($recommandationList, 'recommandation', __('Pr&eacute;conisations', 'evarisk'), 'recommandation', "", "");
			}
		}

		$recommandationListOutput .= '<div class="alignleft recommandationBloc addRecommandationBox" ><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'add_vs.png" alt="' . __('Ajouter une pr&eacute;conisation', 'evarisk') . '" title="' . __('Ajouter une pr&eacute;conisation pour la cat&eacute:gorie en cours', 'evarisk') . '" class="addRecommandation" id="recoCat-add-' . $recommandationCategoryId . '" /></div>';

		$recommandationListOutput .= '
<script type="text/javascript" >
	evarisk(document).ready(function(){
		' . $selectedId . '

		evarisk(".recommandation").click(function(){
			evarisk(".recommandationBloc").each(function(){
				evarisk(this).removeClass("recommandationSelected");
			});
			evarisk(this).parent("div").addClass("recommandationSelected");
			evarisk("#recommandation_id").val(evarisk(this).attr("id").replace("recommandation", ""));
			evarisk("#recommandationNameIndication").html("' . __('pour ', 'evarisk') . '" + evarisk(this).parent("div").children("label").children("img").attr("title"));
		});

		evarisk(".addRecommandation").click(function(){
			evarisk("#recommandationFormContainer").hide();
			evarisk("#loadingRecommandationForm").html(evarisk("#loadingImg").html());
			evarisk("#loadingRecommandationForm").show();
			evarisk("#recommandationInterfaceContainer").dialog("open");
			evarisk("#recommandationInterfaceContainer").dialog({title:"' . __('Ajouter une pr&eacute;conisation', 'evarisk') . '"});
			evarisk("#recommandationFormContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
			{
				"post":"true",
				"table":"' . TABLE_PRECONISATION . '",
				"act":"loadRecommandationManagementForm",
				"id_categorie_preconisation":evarisk(this).attr("id").replace("recoCat-add-", ""),
				"reloadAfterEdition":"true",
				"placeToClickForReload":"recommandationCategory" + evarisk(this).attr("id").replace("recoCat-add-", "")
			});
		});
	});
</script>';

		return $recommandationListOutput;
	}

/**
*	Get the output for the recommandation list for a given element
*
*	@param string $table_element The element type we want to get the recommandation list for
*	@param integer $id_element The element id we want to get the recommandation list for
*
*	@return mixed $recommandationListOutput The complete output
*/
	function getRecommandationListForElement($table_element, $id_element, $recommandationLinkId = '')
	{
		global $wpdb;

		$moreQuery = "";
		if($recommandationLinkId != '')
		{
			$moreQuery = " 
				AND LINK_RECO_ELMT.id = '" . $recommandationLinkId . "' ";
		}

		$query = $wpdb->prepare(
			"SELECT LINK_RECO_ELMT.*, CAT_RECO.nom AS recommandation_category_name, CAT_RECO.id AS recommandation_category_id, CAT_RECO.impressionRecommandationCategorie AS impressionRecommandationCategorie, CAT_RECO.tailleimpressionRecommandationCategorie AS tailleimpressionRecommandationCategorie, CAT_RECO.impressionRecommandation AS impressionRecommandation, CAT_RECO.tailleimpressionRecommandation AS tailleimpressionRecommandation, RECO.nom AS recommandation_name, PIC.photo
			FROM " . TABLE_LIAISON_PRECONISATION_ELEMENT . " AS LINK_RECO_ELMT
				INNER JOIN " . TABLE_PRECONISATION . " AS RECO ON ((RECO.id = LINK_RECO_ELMT.id_preconisation) AND (RECO.status = 'valid'))
					LEFT JOIN " . TABLE_PHOTO_LIAISON . " AS LINK_ELT_PIC ON ((LINK_ELT_PIC.idElement = RECO.id) AND (tableElement = '" . TABLE_PRECONISATION . "') AND (LINK_ELT_PIC.isMainPicture = 'yes') AND (LINK_ELT_PIC.status = 'valid'))
					LEFT JOIN " . TABLE_PHOTO . " AS PIC ON ((PIC.id = LINK_ELT_PIC.idPhoto) AND (PIC.status = 'valid'))
				INNER JOIN " . TABLE_CATEGORIE_PRECONISATION . " AS CAT_RECO ON ((CAT_RECO.id = RECO.id_categorie_preconisation) AND (CAT_RECO.status = 'valid'))
			WHERE LINK_RECO_ELMT.status = 'valid'
				AND LINK_RECO_ELMT.table_element = %s
				AND LINK_RECO_ELMT.id_element = %d" . $moreQuery . "
			ORDER BY recommandation_category_id",
			$table_element, $id_element);;

		return	$wpdb->get_results($query);
	}



/**************************************************************************************************************************************/
/******************************					SETTERS																								*****************************************/
/**************************************************************************************************************************************/

/**
*	Save a new recommandation association in database
*
*	@param array $recommandationsinformations An array with the different information we want to set for the new recommandation association
*
*	@return string $reponseRequete A message that allows to know if the recommandation creation has been done correctly or not
*/
	function saveRecommandationAssociation($recommandationsinformations)
	{
		global $wpdb;

		$whatToUpdate = eva_database::prepareQuery($recommandationsinformations, 'creation');
		$query = $wpdb->prepare(
			"INSERT INTO " . TABLE_LIAISON_PRECONISATION_ELEMENT . " 
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
*	Update an existing recommandation association in database
*
*	@param array $recommandationsinformations An array with the different information we want to set for the recommandation association
*
*	@return string $reponseRequete A message that allows to know if the recommandation update has been done correctly or not
*/
	function updateRecommandationAssociation($recommandationsinformations, $id)
	{
		global $wpdb;
		$reponseRequete = '';

		$whatToUpdate = eva_database::prepareQuery($recommandationsinformations, 'update');
		$query = $wpdb->prepare(
			"UPDATE " . TABLE_LIAISON_PRECONISATION_ELEMENT . " 
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
*	Save a new recommandation in database
*
*	@param array $recommandationsinformations An array with the different information we want to set for the new recommandation
*
*	@return string $reponseRequete A message that allows to know if the recommandation creation has been done correctly or not
*/
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
			$reponseRequete = $wpdb->insert_id;
		}
		else
		{
			$reponseRequete = 'error';
		}

		return $reponseRequete;
	}

/**
*	Update an existing recommandation in database
*
*	@param array $recommandationsinformations An array with the different information we want to set for the recommandation
*
*	@return string $reponseRequete A message that allows to know if the recommandation update has been done correctly or not
*/
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



/**************************************************************************************************************************************/
/******************************					OUTPUT																								*****************************************/
/**************************************************************************************************************************************/

/**
*	Get the content of the recommandation postbox
*
*	@param array $arguments An array with the different element that specify the postbox location and other postbox's information
*
*	@return mixed The complete output for the postbox
*/
	function getRecommandationsPostBoxBody($arguments)
	{
		$idElement = $arguments['idElement'];
		$tableElement = $arguments['tableElement'];

		$outputMode = 'pictos';

		echo evaRecommandation::getRecommandationForm() . '<div id="message' . TABLE_PRECONISATION . '-' . $tableElement . '" class="hide" >&nbsp;</div>
<input type="hidden" id="recommandation_link_action" name="recommandation_link_action" value="add" />
<input type="hidden" id="recommandation_link_id" name="recommandation_link_id" value="" />
<input type="hidden" id="id_element_recommandation" name="id_element_recommandation" value="' . $idElement . '" />
<input type="hidden" id="table_element_recommandation" name="table_element_recommandation" value="' . $tableElement . '" />
<ul class="eva_tabs">
	<li id="ongletAjoutPreconisation" class="tabs selected_tab" ><label tabindex="1">' . ucfirst(strtolower( __('Ajout d\'une pr&eacute;conisation', 'evarisk'))) . '</label></li>
	<li id="ongletListePreconisation" class="tabs" ><label tabindex="2">' . ucfirst(strtolower( __('Pr&eacute;conisation affect&eacute;es', 'evarisk'))) . '</label></li>
</ul>
<div id="divAjoutPreconisation" class="eva_tabs_panel" >' . evaRecommandation::recommandationAssociation($outputMode, '', $arguments) . '</div>
<div id="divListePreconisation" class="eva_tabs_panel hide" >&nbsp;</div>
<script type="text/javascript">
	evarisk(document).ready(function(){
		evarisk("#ongletAjoutPreconisation").click(function(){
			commonTabChange("postBoxRecommandations", "#divAjoutPreconisation", "#ongletListePreconisation");
			evarisk("#recommandation_link_action").val("add");
			evarisk("#recommandation_link_id").val("");
			evarisk("#divAjoutPreconisation").html(evarisk("#loadingImg").html());
			evarisk("#divAjoutPreconisation").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
			{
				"post":"true",
				"table":"' . TABLE_PRECONISATION . '",
				"act":"loadRecomandationLink",
				"table_element":"' . $tableElement . '",
				"id_element":"' . $idElement . '",
				"outputMode":"' . $outputMode . '",
				"recommandation_link_action":evarisk("#recommandation_link_action").val(),
				"recommandation_link_id":evarisk("#recommandation_link_id").val(),
			});
		});
		evarisk("#ongletListePreconisation").click(function(){
			commonTabChange("postBoxRecommandations", "#divListePreconisation", "#ongletAjoutPreconisation");
			evarisk("#recommandation_link_action").val("add");
			evarisk("#recommandation_link_id").val("");
			evarisk("#divListePreconisation").html(evarisk("#loadingImg").html());
			evarisk("#divListePreconisation").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
			{
				"post":"true",
				"table":"' . TABLE_PRECONISATION . '",
				"act":"loadRecomandationForElement",
				"table_element":"' . $tableElement . '",
				"id_element":"' . $idElement . '"
			});
		});
	});
</script>';
	}

/**
*	Get the different component for the recommandation adding
*/
	function recommandationAssociation($outputMode, $selectedRecommandation = '', $arguments = '')
	{
		$recommandationAssociationOutput = $efficacite_preconisation_script = $efficatiteForm = '';
		$recommandationContainer = '&nbsp;';
		$recommandationContainerClass = 'hide';
		$saveRecommandationAssociationButton = __('Enregistrer', 'evarisk');

		if(is_array($selectedRecommandation))
		{
			$recommandationContainer = evaRecommandation::getRecommandationListByCategory($selectedRecommandation['id_categorie_preconisation'], $outputMode, $selectedRecommandation['id_preconisation']);
			$recommandationContainerClass = '';
			$saveRecommandationAssociationButton = __('Mettre &agrave; jour', 'evarisk') ;
		}

		$selectedRecommandationCategory = (is_array($selectedRecommandation) && (isset($selectedRecommandation['id_categorie_preconisation'])) && ($selectedRecommandation['id_categorie_preconisation'] != '')) ? eva_tools::IsValid_Variable($selectedRecommandation['id_categorie_preconisation']) : '';
		$commentaire_preconisation = (is_array($selectedRecommandation) && (isset($selectedRecommandation['commentaire_preconisation'])) && ($selectedRecommandation['commentaire_preconisation'] != '')) ? eva_tools::IsValid_Variable($selectedRecommandation['commentaire_preconisation']) : '';
		$efficacite_preconisation = (is_array($selectedRecommandation) && (isset($selectedRecommandation['efficacite_preconisation'])) && ($selectedRecommandation['efficacite_preconisation'] != '')) ? eva_tools::IsValid_Variable($selectedRecommandation['efficacite_preconisation']) : '0';

		if(digirisk_options::getOptionValue('recommandation_efficiency_activ') == 'oui')
		{
			$efficatiteForm = 
'<label for="efficacite_preconisation">' . __('Efficacit&eacute; (%)', 'evarisk') . '</label>
<input type="text" class="sliderValue" disabled="disabled" id="efficacite_preconisation" name="efficacite_preconisation" value="' . $efficacite_preconisation . '" /><div id="slider-efficacite_preconisation" class="slider_variable"></div>
<div class="clear" >&nbsp;</div>';
			$efficacite_preconisation = 'evarisk("#efficacite_preconisation").val()';
			$efficacite_preconisation_script = '
		evarisk("#slider-efficacite_preconisation").slider({
			range:"min",
			value:' . $efficacite_preconisation . ',
			min:0,
			max:100,
			slide:function(event, ui){
				evarisk("#efficacite_preconisation").val(ui.value);
			}
		});
		evarisk("#efficacite_preconisation").val(evarisk("#slider-efficacite_preconisation").slider("value"));';
		}

		$recommandationAssociationOutput = '
<div class="recommandationCategoryExplanation" >' . __('Choisissez une famille de pr&eacute;conisation', 'evarisk') . '</div>
' . evaRecommandationCategory::getCategoryRecommandationListOutput($outputMode, $selectedRecommandationCategory) . '
<div class="clear ' . $recommandationContainerClass . '" id="associationFormContainer" >
	<div id="recommandationContainer" >' . $recommandationContainer . '</div>
	<div class="clear" >&nbsp;</div>
	<div id="recommandationFormContent" >
		' . $efficatiteForm . '
		<label for="commentaire_preconisation" >' . __('Commentaire', 'evarisk') . '&nbsp;<span id="recommandationNameIndication" >&nbsp;</span></label>
		<textarea id="commentaire_preconisation" name="commentaire_preconisation" rows="3" cols="10" class="recommandationInput" >' . $commentaire_preconisation . '</textarea>
		<div class="clear" >&nbsp;</div>';
		if(current_user_can('digi_edit_unite') || current_user_can('digi_edit_unite_' . $arguments['idElement']))
		{
			$recommandationAssociationOutput .= '
		<input type="button" class="button-primary alignright" id="saveRecommandationLink" name="saveRecommandationLink" value="' . $saveRecommandationAssociationButton . '" />';
		}
		$recommandationAssociationOutput .= '
		<input type="hidden" id="recommandation_id" name="recommandation_id" value="1" />
	</div>
</div>';

		$recommandationAssociationOutput .= '
<script type="text/javascript" >
	evarisk(document).ready(function(){
' . $efficacite_preconisation_script . '
		evarisk(".recommandationCategory").click(function(){
			evarisk(".recommandationCategoryBloc").each(function(){
				evarisk(this).removeClass("recommandationCategorySelected");
			});
			evarisk(this).parent("div").addClass("recommandationCategorySelected");
			evarisk("#recommandationContainer").html(evarisk("#loadingImg").html());
			evarisk("#recommandationContainer").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
			{
				"post":"true",
				"table":"' . TABLE_PRECONISATION . '",
				"act":"loadRecomandationOfCategory",
				"outputMode":"' . $outputMode . '",
				"id_categorie_preconisation": evarisk(this).val()
			});
			evarisk("#associationFormContainer").show();
			evarisk("#commentaire_preconisation").val("");
			evarisk("#efficacite_preconisation").val("");
			evarisk("#recommandation_link_action").val("save");
			evarisk("#recommandation_link_id").val("");
			evarisk("#recommandationNameIndication").html("&nbsp;");
			evarisk("#saveRecommandationLink").val("' . __('Enregistrer', 'evarisk') . '");
		});

		evarisk("#saveRecommandationLink").click(function(){
			evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
			{
				"post": "true",
				"table": "' . TABLE_PRECONISATION . '",
				"act": "saveRecommandationLink",
				"recommandation_link_action": evarisk("#recommandation_link_action").val(),
				"recommandation_link_id": evarisk("#recommandation_link_id").val(),
				"recommandationId": evarisk("#recommandation_id").val(),
				"recommandationEfficiency": ' . $efficacite_preconisation . ',
				"recommandationComment": evarisk("#commentaire_preconisation").val(),
				"id_element": evarisk("#id_element_recommandation").val(),
				"table_element": evarisk("#table_element_recommandation").val()
			});
		});
	});
</script>';
		return $recommandationAssociationOutput;
	}

/**
*	Get the output for the recommandation list for a given element
*
*	@param string $table_element The element type we want to get the recommandation list for
*	@param integer $id_element The element id we want to get the recommandation list for
*
*	@return mixed $recommandationListOutput The complete output
*/
	function getRecommandationListForElementOutput($table_element, $id_element)
	{
		$recommandationList = evaRecommandation::getRecommandationListForElement($table_element, $id_element);
		$outputMode = 'pictos';

		unset($titres, $classes, $idLignes, $lignesDeValeurs);
		$idLignes = null;
		$idTable = 'evaRecommandationList' . $table_element . '-' . $id_element;
		$titres[] = __("Cat&eacute;gorie de la pr&eacute;conisation", 'evarisk');
		$titres[] = __("Ic&ocirc;ne", 'evarisk');
		$titres[] = __("Intitul&eacute;", 'evarisk');
		$titres[] = __("Commentaire", 'evarisk');
		if(digirisk_options::getOptionValue('recommandation_efficiency_activ') == 'oui')
		{
			$titres[] = __("Efficacit&eacute;", 'evarisk');
		}
		$titres[] = __("Actions", 'evarisk');
		$classes[] = '';
		$classes[] = 'recommandationIconColumn';
		$classes[] = '';
		$classes[] = '';
		if(digirisk_options::getOptionValue('recommandation_efficiency_activ') == 'oui')
		{
			$classes[] = 'recommandationEfficiencyColumn';
		}
		$classes[] = 'recommandationActionColumn';

		unset($ligneDeValeurs);
		$i=0;
		if(count($recommandationList) > 0)
		{
			foreach($recommandationList as $recommandation)
			{
				$idLignes[] = 'recommandationLink-id-' . $recommandation->id;

				$recommandationCategoryMainPicture = '';
				$mainPicture = evaPhoto::getMainPhoto(TABLE_CATEGORIE_PRECONISATION, $recommandation->recommandation_category_id);
				if($mainPicture != 'error')
				{
					if(is_file(EVA_HOME_DIR . $mainPicture))
					{
						$recommandationCategoryMainPicture = '<img class="recommandationCategoryDefaultPictosList" style="width:' . TAILLE_PICTOS . ';" src="' . EVA_HOME_URL . $mainPicture . '" alt="' . sprintf(__('Photo par d&eacute;faut pour %s', 'evarisk'), $recommandation->recommandation_category_name) . '" />';
					}
					elseif(is_file(EVA_GENERATED_DOC_DIR . $mainPicture))
					{
						$recommandationCategoryMainPicture = '<img class="recommandationCategoryDefaultPictosList" style="width:' . TAILLE_PICTOS . ';" src="' . EVA_GENERATED_DOC_URL . $mainPicture . '" alt="' . sprintf(__('Photo par d&eacute;faut pour %s', 'evarisk'), $recommandation->recommandation_category_name) . '" />';
					}
				}
				$lignesDeValeurs[$i][] = array('value' => $recommandationCategoryMainPicture . '&nbsp;&nbsp;' . ELEMENT_IDENTIFIER_CP . $recommandation->recommandation_category_id . '&nbsp;-&nbsp;' . ucfirst($recommandation->recommandation_category_name), 'class' => '');

				$recommandationMainPicture = evaPhoto::checkIfPictureIsFile($recommandation->photo, TABLE_PRECONISATION);
				$recommandationMainPicture = !$recommandationMainPicture ? '' : '<img class="recommandationDefaultPictosList" style="width:' . TAILLE_PICTOS . ';" src="' . $recommandationMainPicture . '" alt="' . sprintf(__('Photo par d&eacute;faut pour %s', 'evarisk'), $recommandation->recommandation_name) . '" />';

				$lignesDeValeurs[$i][] = array('value' => $recommandationMainPicture, 'class' => '');
				$lignesDeValeurs[$i][] = array('value' => '<span class="pointer recommandationNameManagement" >' . ELEMENT_IDENTIFIER_P . $recommandation->id_preconisation . '&nbsp;-&nbsp;' . ucfirst($recommandation->recommandation_name) . '</span>', 'class' => '');
				$lignesDeValeurs[$i][] = array('value' => ucfirst($recommandation->commentaire), 'class' => '');
				if(digirisk_options::getOptionValue('recommandation_efficiency_activ') == 'oui')
				{
					$lignesDeValeurs[$i][] = array('value' => ucfirst($recommandation->efficacite), 'class' => '');
				}
				if(current_user_can('digi_edit_unite') || current_user_can('digi_edit_unite_' . $arguments['idElement']))
				{
					$lignesDeValeurs[$i][] = array('value' => '<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'delete_vs.png" alt="' . __('Supprimer cette pr&eacute;conisation', 'evarisk') . '" title="' . __('Supprimer cette pr&eacute;conisation', 'evarisk') . '" class="alignright deleteRecommandationLink" /><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'edit_vs.png" alt="' . __('&Eacute;diter cette pr&eacute;conisation', 'evarisk') . '" title="' . __('&Eacute;diter cette pr&eacute;conisation', 'evarisk') . '" class="alignright editRecommandationLink" />', 'class' => '');
				}
				else
				{
					$lignesDeValeurs[$i][] = array('value' => '&nbsp;', 'class' => '');
				}
				$i++;
			}
		}
		else
		{
			$idLignes[] = 'recommandationEmpty';
			$lignesDeValeurs[$i][] = array('value' => '', 'class' => '');
			$lignesDeValeurs[$i][] = array('value' => '', 'class' => '');
			$lignesDeValeurs[$i][] = array('value' => __('Aucune pr&eacute;conisation n\'a &eacute;t&eacute; affect&eacute;e &agrave; cet &eacute;l&eacute;ment', 'evarisk'), 'class' => '');
			if(digirisk_options::getOptionValue('recommandation_efficiency_activ') == 'oui')
			{
				$lignesDeValeurs[$i][] = array('value' => '', 'class' => '');
			}
			$lignesDeValeurs[$i][] = array('value' => '', 'class' => '');
			$lignesDeValeurs[$i][] = array('value' => '', 'class' => '');
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
						null,';
			if(digirisk_options::getOptionValue('recommandation_efficiency_activ') == 'oui')
			{
				$script .= '
						null,';
			}
			$script .= '
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

				evarisk(".deleteRecommandationLink").click(function(){
					if(confirm(convertAccentToJS("' . __('&Ecirc;tes vous s&ucirc;r de vouloir supprimer cet &eacute;l&eacute;ment?', 'evarisk') . '"))){
						evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
						{
							"post":"true",
							"table":"' . TABLE_PRECONISATION . '",
							"act":"deleteRecommandationLink",
							"table_element":evarisk("#table_element_recommandation").val(),
							"id":evarisk(this).parent("td").parent("tr").attr("id").replace("recommandationLink-id-", "")
						});
					}
				});

				evarisk(".editRecommandationLink").click(function(){
					evarisk("#recommandation_link_action").val("update");
					evarisk("#recommandation_link_id").val(evarisk(this).parent("td").parent("tr").attr("id").replace("recommandationLink-id-", ""));
					evarisk("#divAjoutPreconisation").html("");
					evarisk("#divListePreconisation").html(evarisk("#loadingImg").html());
					evarisk("#divListePreconisation").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
					{
						"post":"true",
						"table":"' . TABLE_PRECONISATION . '",
						"act":"loadRecomandationLink",
						"table_element":"' . $table_element . '",
						"id_element":"' . $id_element . '",
						"outputMode":"' . $outputMode . '",
						"recommandation_link_action":evarisk("#recommandation_link_action").val(),
						"recommandation_link_id":evarisk("#recommandation_link_id").val(),
					});
				});

			});
		</script>';

		return EvaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script);
	}

/**
*	Generate the output for the recommandation management page
*
*	@return mixed The complete html output
*/
	function evaRecommandationMainPage()
	{
		_e(EvaDisplayDesign::afficherDebutPage(__('Pr&eacute;conisations', 'evarisk'), EVA_RECOMMANDATION_ICON, __('Pr&eacute;conisations', 'evarisk'), __('Pr&eacute;conisations', 'evarisk'), TABLE_PRECONISATION, false, '', false));				/*	Add trash	*/
				$main_option = get_option('digirisk_options');
				if(($main_option['digi_activ_trash'] == 'oui') && current_user_can('digi_view_method_trash'))
				{
?>
					<img class="trashPicture" src="<?php echo EVA_IMG_ICONES_PLUGIN_URL; ?>trash_vs.png" alt="Trash" title="<?php _e('Acc&eacute;der &agrave; la corbeille', 'evarisk'); ?>" />
					<div id="trashContainer" title="<?php _e('Liste des &eacute;l&eacute;ments supprim&eacute;s', 'evarisk'); ?>" >&nbsp;</div>
					<script type="text/javascript" >
						evarisk(document).ready(function(){
							evarisk("#trashContainer").dialog({
								autoOpen: false,
								modal: true,
								width: 800,
								height: 600,
								close: function(){
									evarisk(this).html("");
								}
							});
							evarisk(".trashPicture").click(function(){
								evarisk("#trashContainer").dialog("open");
								evarisk("#trashContainer").html(evarisk("#loadingImg").html());
								evarisk("#trashContainer").load("<?php echo EVA_INC_PLUGIN_URL; ?>ajax.php", 
								{
									"post": "true", 
									"tableProvenance": "<?php echo TABLE_CATEGORIE_PRECONISATION ?>",
									"nom": "loadTrash"
								});
							});
						});
					</script>
<?php
				}
?>
<div class="hide" id="loadingImg" ><center><img class="margin36" src="<?php echo _e(PICTO_LOADING_ROUND); ?>" alt="loading..." /></center></div>
<?php echo evaRecommandation::getRecommandationForm(); ?>
<?php echo evaRecommandationCategory::getRecommandationCategoryForm(); ?>
<div id="recommandationTable" >
<?php
		_e(evaRecommandation::getRecommandationTable());
?>
</div>
<?php
		_e(EvaDisplayDesign::afficherFinPage());
	}


/**
*	Get the form to manage recommandation
*
*	@return mixed The complete output for the recommandation management form
*/
	function getRecommandationForm()
	{
?>
<div id="recommandationInterfaceContainer" class="hide" title="<?php _e('Pr&eacute;conisation', 'evarisk'); ?>" >
	<div id="recommandationFormContainer" class="hide" >&nbsp;</div>
	<div id="loadingRecommandationForm" class="hide" >&nbsp;</div>
</div>
<?php
	}

/**
*
*/
	function recommandationForm($id_categorie_preconisation = '', $id_preconisation = '', $nom_preconisation = '', $description_preconisation = '')
	{
		$dialogWidth = $basicDialogWidth = 350;
		$showGalery = false;
		if(($id_preconisation != '') && ($id_preconisation > 0))
		{
			$dialogWidth = 700;
			$showGalery = true;
		}
?>
<script type="text/javascript" >
	evarisk(document).ready(function(){
		evarisk("#recommandationInterfaceContainer").dialog({
			autoOpen: false,
			height: 350,
			width: <?php _e($dialogWidth); ?>,
			modal:  false,
			buttons:{
<?php 
			if(current_user_can('digi_edit_recommandation'))
			{
?>
				"<?php _e('Enregistrer', 'evarisk'); ?>": function(){
					var formIsValid = true;
						evarisk("#nom_preconisation").removeClass("ui-state-error");

					formIsValid = formIsValid && checkLength( evarisk("#nom_preconisation"), "", 1, 128, "<?php _e('Le champs nom de la pr&eacute;conisation doit contenir entre !#!minlength!#! et !#!maxlength!#! caract&egrave;res', 'evarisk'); ?>" , evarisk(".recommandationFormErrorMessage"));

					if(formIsValid){
						evarisk("#ajax-response").load("<?php _e(EVA_INC_PLUGIN_URL); ?>ajax.php", 
						{
							"post":"true",
							"table":"<?php _e(TABLE_PRECONISATION); ?>",
							"act":"saveRecommandation",
							"nom_preconisation": evarisk("#nom_preconisation").val(),
							"description_preconisation": evarisk("#description_preconisation").val(),
							"id_categorie_preconisation": evarisk("#id_categorie_preconisation").val(),
							"id_preconisation": evarisk("#id_preconisation").val()
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
				evarisk("#nom_preconisation").val("");
				evarisk("#description_preconisation").val("");
				evarisk("#id_categorie_preconisation").val("");
				evarisk("#id_preconisation").val("");
				evarisk("#nom_preconisation").removeClass("ui-state-error");
				evarisk("#recommandationInterfaceContainer").dialog({
					title: "<?php _e('Pr&eacute;conisation', 'evarisk'); ?>"
				});
				evarisk("#recommandationPictureGalery").html("");
				evarisk("#recommandationPictureGalery").hide();
				evarisk("#recommandationForm").html("");
				evarisk("#recommandationForm").hide();
				evarisk("#recommandationForm").removeClass("recommandationInterfaceContainerPart");
				evarisk("#loadingRecommandationForm").html("");
				evarisk("#loadingRecommandationForm").hide();
				evarisk(this).dialog({width:<?php _e($basicDialogWidth); ?>});
			}
		});
	});
</script>
<p class="recommandationFormErrorMessage ">&nbsp;</p>
<form action="" >
	<fieldset>
		<div id="recommandationForm" class="alignleft recommandationInterfaceContainerPart" >
			<input type="hidden" name="id_categorie_preconisation" id="id_categorie_preconisation" class="recommandationInput" value="<?php _e($id_categorie_preconisation); ?>" />
			<input type="hidden" name="id_preconisation" id="id_preconisation" class="recommandationInput" value="<?php _e($id_preconisation); ?>" />
			<label for="nom_preconisation" ><?php _e('Nom', 'evarisk'); ?></label>
			<input type="text" name="nom_preconisation" id="nom_preconisation" class="recommandationInput" value="<?php _e($nom_preconisation); ?>" />
			<label for="description_preconisation" ><?php _e('Description', 'evarisk'); ?></label>
			<textarea rows="3" cols="10" name="description_preconisation" id="description_preconisation" class="recommandationInput" ><?php _e($description_preconisation); ?></textarea>
		</div>
<?php
		if($showGalery)
		{
?>
		<div id="recommandationPictureGalery" class="hide alignright recommandationInterfaceContainerPart" >
			<div id="pictureUploadForm<?php _e(TABLE_PRECONISATION); ?>_<?php _e($id_preconisation); ?>" ><?php _e(evaPhoto::getUploadForm(TABLE_PRECONISATION, $id_preconisation)); ?></div>
			<div id="pictureGallery<?php _e(TABLE_PRECONISATION); ?>_<?php _e($id_preconisation); ?>" >&nbsp;</div>
		</div>
<?php
		}
?>
	</fieldset>
</form>
<?php
	}

}