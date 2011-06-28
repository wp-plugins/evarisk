<?php
/**
* Product management
* 
*	The product are defined into the wpshop plugin. Methods in this file will be used to access to the product definition, to display product and to store some informations of the product into digirisk database
* @author Evarisk <dev@evarisk.com>
* @version 5.1.2.9
* @package Digirisk
* @subpackage librairies
*/

/**
* The product are defined into the wpshop plugin. Methods in this file will be used to access to the product definition, to display product and to store some informations of the product into digirisk database
* @package Digirisk
* @subpackage librairies
*/
class digirisk_product
{
	/**
	*	Build the product post box output
	*
	*	@param array $params The parameters added by wordpress for each displayed box
	*
	*	@return string The box content with the product to affect and the affected product
	*/
	function getProductPostBox($params)
	{
		/*	Get the entire element list	*/
		$categories = array();
		$categories[] = __('Toutes', 'evarisk');
		/*	Get the list of categories to output. This list is defined by the options set by the administrator	*/
		$digiriskSelectedCategories = unserialize(digirisk_options::getOptionValue('product_categories'));
		if(is_array($digiriskSelectedCategories))
		{
			/*	Read the category list for getting informations about it	*/
			foreach($digiriskSelectedCategories as $digiriskCategories)
			{
				$digiriskCategory = wpshop_attributes::getElementWithAttributeAndValue(WPSHOP_DBT_CATEGORY, wpshop_entities::getEntityIdFromCode('product_category'), 1, 'code', $digiriskCategories, "'valid'");
				/*	For each selected categoriesin digirisk, read informations to build an array with the needed information for the end 	*/
				foreach($digiriskCategory as $categoryId => $category)
				{
					$categories[$categoryId] = $category['attributes']['product_category_name']['value'];
				}
			}
		}
		$input_def['type'] = 'select';
		$input_def['possible_value'] = $categories;
		$input_def['value'] = '';
		$input_def['valueToPut'] = 'index';
		$input_def['name'] = 'digi_wpshop_product_category_selector';
		$categoryListingSelector = digirisk_form::check_input_type($input_def);

		echo '
<div style="display:none;" id="messageInfo_' . $params['tableElement'] . '_' . $params['idElement'] . '_affectProduct" ></div>
' . __('Afficher uniquement les produits de la cat&eacute;gorie', 'evarisk') . '&nbsp:&nbsp;' . $categoryListingSelector . '
<div id="productList' . $params['tableElement'] . '" >' . digirisk_product::affectationPostBoxContent($params['tableElement'], $params['idElement']) . '</div>';
	}

	/**
	*	Create the content of the affectation box
	*
	*	@param string $tableElement The element type we want to affect something to
	*	@param string $idElement The element identifier we want to affect something to
	*	@param boolean $showButton Allows to specify if the save button must be shown or not
	*
	*	@return string $output The html code that contains the box content to output
	*/
	function affectationPostBoxContent($tableElement, $idElement, $showButton = true, $categoryToDisplay = '')
	{
		$output = '';
		$alreadyLinked = $alreadyLinkedListOutput = '';
		$idBoutonEnregistrer = 'affectation_' . $tableElement;

		/*	Get the list of element already linked	*/
		$linkedElementList = array();

		$linkedElement = digirisk_product::getBindElement($idElement, $tableElement);
		if(is_array($linkedElement ) && (count($linkedElement) > 0))
		{
			foreach($linkedElement as $element)
			{
				$linkedElementList[$element->id_product] = $element;
				$alreadyLinked .= $element->id_product . ', ';
				$currentElement = wpshop_attributes::getElementWithAttributeAndValue(WPSHOP_DBT_PRODUCT, wpshop_entities::getEntityIdFromCode('product'), 1, 'code', $element->id_product, "'valid'");
				$alreadyLinkedListOutput .= '<div class="selectedelementOP" id="affectedElement' . $tableElement . $element->id_product . '" title="' . __('Cliquez pour supprimer', 'evarisk') . '" >' . $currentElement[$element->id_product]['attributes']['product_name']['value'] . ' (' .  $currentElement[$element->id_product]['reference'] . ')<div class="ui-icon deleteElementFromList" >&nbsp;</div></div>';
			}
		}
		else
		{
			$alreadyLinkedListOutput = '<span id="noElementSelected' . $tableElement . '" class="noElementLinked" >' . __('Aucun produit affect&eacute;', 'evarisk') . '</span>';
		}

		$output = '
<input type="hidden" name="actuallyAffectedList' . $tableElement . '" id="actuallyAffectedList' . $tableElement . '" value="' . $alreadyLinked . '" />
<input type="hidden" name="affectedList' . $tableElement . '" id="affectedList' . $tableElement . '" value="' . $alreadyLinked . '" />

<div class="alignleft affectationCompleteListOutput" >
	<div id="affectedListOutput' . $tableElement . '" class="affectedElementListOutput ui-widget-content clear" >' . $alreadyLinkedListOutput . '</div>
</div>
<div class="alignright" style="width:55%;" >
	<span class="alignright" ><a href="' . admin_url('admin.php?page=' . WPSHOP_URL_SLUG_PRODUCT_LISTING) . '">' . __('Ajouter des produits', 'evarisk') . '</a></span>
	<div class="clear addLinkElementElement" >
		<div class="clear" >
			<span class="searchElementInput ui-icon" >&nbsp;</span>
			<input class="searchElementToAffect" type="text" name="affectedElement' . $tableElement . '" id="affectedElement' . $tableElement . '" value="' . __('Rechercher dans la liste des produits', 'evarisk') . '" />
		</div>
		<div id="completeList' . $tableElement . '" class="completeList clear" >' . digirisk_product::elementListForAffectation($tableElement, $idElement, $categoryToDisplay) . '</div>
	</div>
	<div id="massAction' . $tableElement . '" ><span class="checkAll" >' . __('cochez tout', 'evarisk') . '</span>&nbsp;/&nbsp;<span class="uncheckAll" >' . __('d&eacute;cochez tout', 'evarisk') . '</span></div>
</div>
<div id="elementBlocContainer' . $tableElement . '" class="clear hide" ><div onclick="javascript:elementDeletion(evarisk(this).attr(\'id\'), \'' . $tableElement . '\', \'' . $idBoutonEnregistrer . '\');" class="selectedelementOP" title="' . __('Cliquez pour supprimer', 'evarisk') . '" >#ELEMENTNAME#<span class="ui-icon deleteElementFromList" >&nbsp;</span></div></div>

<script type="text/javascript" >
	(function(){
		/*	Mass action : check / uncheck all	*/
		jQuery("#massAction' . $tableElement . ' .checkAll").unbind("click");
		jQuery("#massAction' . $tableElement . ' .checkAll").click(function(){
			jQuery("#completeList' . $tableElement . ' .buttonActionElementLinkList' . $tableElement . '").each(function(){
				if(jQuery(this).hasClass("elementIsNotLinked")){
					jQuery(this).click();
				}
			});
		});
		jQuery("#massAction' . $tableElement . ' .uncheckAll").unbind("click");
		jQuery("#massAction' . $tableElement . ' .uncheckAll").click(function(){
			jQuery("#completeList' . $tableElement . ' .buttonActionElementLinkList' . $tableElement . '").each(function(){
				if(jQuery(this).hasClass("elementIsLinked")){
					jQuery(this).click();
				}
			});
		});

		/*	Action when click on delete button	*/
		jQuery(".selectedelementOP").click(function(){
			elementDivId = jQuery(this).attr("id").replace("affectedElement' . $tableElement . '", "");
			deleteElementIdFiedList(elementDivId, "' . $tableElement . '");
			checkElementListModification("' . $tableElement . '", "' . $idBoutonEnregistrer . '");
		});

		/*	User Search autocompletion	*/
		jQuery("#affectedElement' . $tableElement . '").click(function(){
			jQuery(this).val("");
		});
		jQuery("#affectedElement' . $tableElement . '").blur(function(){
			jQuery(this).val("' . __('Rechercher dans la liste des produits', 'evarisk') . '");
		});
		jQuery("#affectedElement' . $tableElement . '").autocomplete("' . EVA_INC_PLUGIN_URL . 'liveSearch/searchProducts.php");
		jQuery("#affectedElement' . $tableElement . '").result(function(event, data, formatted){
			cleanElementIdFiedList(data[1], "' . $tableElement . '");
			addElementIdFieldList(data[0], data[1], "' . $tableElement . '");

			checkElementListModification("' . $tableElement . '", "' . $idBoutonEnregistrer . '");

			jQuery("#affectedElement' . $tableElement . '").val("' . __('Rechercher dans la liste des produits', 'evarisk') . '");
		});

		/*	Add the possibility to select only one category to display	*/
		jQuery("#digi_wpshop_product_category_selector").change(function(){
			jQuery("#productList' . $tableElement . '").html(jQuery("#loadingImg").html());
			jQuery("#productList' . $tableElement . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
				"post":"true", 
				"table":"' . DIGI_DBT_LIAISON_PRODUIT_ELEMENT . '",
				"act":"reloadCategoryChoice",
				"tableElement":"' . $tableElement . '",
				"idElement":"' . $idElement . '",
				"category":jQuery(this).val()
			});
		});
	})(evarisk);
</script>';

		if($showButton)
		{
			switch($tableElement)
			{
				case TABLE_GROUPEMENT:
					if(!current_user_can('digi_edit_groupement') && !current_user_can('digi_edit_groupement_' . $idElement))
					{
						$showButton = false;
					}
				break;
				case TABLE_UNITE_TRAVAIL:
					if(!current_user_can('digi_edit_unite') && !current_user_can('digi_edit_unite_' . $idElement))
					{
						$showButton = false;
					}
				break;
			}
		}
		
		if($showButton)
		{//Bouton Enregistrer
			$scriptEnregistrement = '
<script type="text/javascript">
	evarisk(document).ready(function(){
		checkElementListModification("' . $tableElement . '", "' . $idBoutonEnregistrer . '");
		evarisk("#' . $idBoutonEnregistrer . '").click(function(){
			evarisk("#saveButtonLoading' . $tableElement . '").show();
			evarisk("#saveButtonContainer' . $tableElement . '").hide();
			evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
				"post": "true", 
				"table": "' . DIGI_DBT_LIAISON_PRODUIT_ELEMENT . '",
				"act": "save",
				"element": evarisk("#affectedList' . $tableElement . '").val(),
				"tableElement": "' . $tableElement . '",
				"idElement": "' . $idElement . '"
			});
		});
	});
</script>';

			$output .= '<div class="clear" ><div id="saveButtonLoading' . $tableElement . '" style="display:none;" class="clear alignright" ><img src="' . PICTO_LOADING_ROUND . '" alt="loading in progress" /></div><div id="saveButtonContainer' . $tableElement . '" >' . EvaDisplayInput::afficherInput('button', $idBoutonEnregistrer, __('Enregistrer', 'evarisk'), null, '', 'save', false, true, '', 'button-primary alignright', '', '', $scriptEnregistrement) . '</div></div>';
		}

		return $output;
	}
	/**
	*	Build a html table with the existing element list, in order to show them for affectation
	*
	*	@param string $tableElement The type of element we want to affect something to
	*	@param integer $idElement The element identifier we want to affect something to
	*
	*	@return string $elementList_Table The html code that we have to output for displaying the existing element list
	*/
	function elementListForAffectation($tableElement, $idElement, $categoryToDisplay = '')
	{
		$elementList_Table = $script = '';
		$idBoutonEnregistrer = 'affectation_' . $tableElement;
		$tableOptions = '';

		$idTable = 'elementList' . $tableElement . $idElement;
		$titres = array('', ucfirst(strtolower(__('R&eacute;f&eacute;rence', 'evarisk'))), ucfirst(strtolower(__('Nom', 'evarisk'))), ucfirst(strtolower(__('Cat&eacute;gorie', 'evarisk'))));
		$classes = array('addElementButtonDTable','','','');

		/*	Get the element list already linked	*/
		$linkedElementCheckList = array();
		$linkedElementList = digirisk_product::getBindElement($idElement, $tableElement);
		if(is_array($linkedElementList ) && (count($linkedElementList) > 0))
		{
			foreach($linkedElementList as $linkedElement)
			{
				$linkedElementCheckList[$linkedElement->id_product] = $linkedElement;
			}
		}

		$categories = array();
		/*	Get the list of categories to output. This list is defined by the options set by the administrator	*/
		$digiriskSelectedCategories = unserialize(digirisk_options::getOptionValue('product_categories'));
		if(is_array($digiriskSelectedCategories))
		{
			/*	Read the category list for getting informations about it	*/
			foreach($digiriskSelectedCategories as $digiriskCategories)
			{
				$digiriskCategory = wpshop_attributes::getElementWithAttributeAndValue(WPSHOP_DBT_CATEGORY, wpshop_entities::getEntityIdFromCode('product_category'), 1, 'code', $digiriskCategories, "'valid'");
				/*	For each selected categoriesin digirisk, read informations to build an array with the needed information for the end 	*/
				foreach($digiriskCategory as $categoryId => $category)
				{
					if((($categoryToDisplay != '') && ($categoryToDisplay == $categoryId)) || ($categoryToDisplay == '') || ($categoryToDisplay == '0'))
					{
						$categories[$categoryId] = $category['attributes']['product_category_name']['value'];
					}
				}
			}
		}

		/*	Read the categories list	*/
		$productListing = array();
		if(is_array($categories) && (count($categories) > 0))
		{/*	In case that there are categories to output	*/
			/*	Read the categories list for product listing building	*/
			foreach($categories as $categoryId => $categoryName)
			{
				$productOfCategory = wpshop_categories::getProductOfCategory($categoryId);
				if(count($productOfCategory) > 0)
				{/*	If there are products to output	*/
					foreach($productOfCategory as $product)
					{
						$productInformations = wpshop_attributes::getElementWithAttributeAndValue(WPSHOP_DBT_PRODUCT, wpshop_entities::getEntityIdFromCode('product'), 1, 'code', $product->id_product, "'valid'");
						$productListing[$product->id_product]['reference'] = $productInformations[$product->id_product]['reference'];
						$productListing[$product->id_product]['name'] = $productInformations[$product->id_product]['attributes']['product_name']['value'];
						$productListing[$product->id_product]['categories'][] = $categoryName;
					}
				}
			}

			/*	Read the product listing	*/
			if(count($productListing) > 0)
			{
				foreach($productListing as $productID => $productInformations)
				{/*	Product line content	*/
					/*	Define informations for each line	*/
					$idLigne = $tableElement . $idElement . '_elementList_' . $productID;
					$idCbLigne = 'cb_' . $idLigne;
					$moreLineClass = 'elementIsNotLinked';
					if(isset($linkedElementCheckList[$productID]))
					{
						$moreLineClass = 'elementIsLinked';
					}
					/*	Set each line content with the builded output	*/
					$valeurs = array();
					$valeurs[] = array('value'=>'<span id="actionButton' . $tableElement . 'ElementLink' . $productID . '" class="buttonActionElementLinkList' . $tableElement . ' ' . $moreLineClass . '  ui-icon pointer" >&nbsp;</span>');
					$valeurs[] = array('class' => 'digirisk_product_ref_cell', 'value' => $productInformations['reference']);
					$valeurs[] = array('class' => 'digirisk_product_name_cell', 'value' => $productInformations['name']);
					$valeurs[] = array('class' => 'digirisk_product_category_cell', 'value' => implode(', ', $productInformations['categories']));
					$lignesDeValeurs[] = $valeurs;
					$idLignes[] = $idLigne;
				}
				$tableOptions = ',
			"aaSorting": [[2,"asc"]]';
			}
			else
			{/*	In case that no product is available into the selected categories	*/
				/*	Overwrite the datatable titles and class	*/
				$titres = array(ucfirst(strtolower(__('Aucune produit disponible', 'evarisk'))));
				$classes = array('');
				/*	Define the content of the table	*/
				$valeurs = array();
				$valeurs[] = array('class' => 'digirisk_no_category_selected', 'value' => sprintf(__('Pour ajouter des produits %s', 'evarisk'), '<a href="' . admin_url('admin.php?page=' . WPSHOP_URL_SLUG_PRODUCT_LISTING) . '" >' . _('cliquez ici', 'evarisk') . '</a>'));
				$lignesDeValeurs[] = $valeurs;
				$idLignes[] = $idLigne;
			}
		}
		else
		{/*	In case that no categories was selected into options	*/
			/*	Overwrite the datatable titles and class	*/
			$titres = array(ucfirst(strtolower(__('Aucune cat&eacute;gorie s&eacute;lectionn&eacute;e.', 'evarisk'))));
			$classes = array('addElementButtonDTable');
			/*	Define the content of the table	*/
			$valeurs = array();
			$valeurs[] = array('class' => 'digirisk_no_category_selected', 'value' => sprintf(__('Pour ajouter des cat&eacute;gories %s', 'evarisk'), '<a href="' . admin_url('options-general.php?page=' . DIGI_URL_SLUG_MAIN_OPTION) . '" >' . _('cliquez ici', 'evarisk') . '</a>'));
			$lignesDeValeurs[] = $valeurs;
			$idLignes[] = $idLigne;
		}

		/*	Add the js option for the table	*/
		$script = 
'<script type="text/javascript">
	evarisk(document).ready(function(){
		evarisk("#' . $idTable . '").dataTable({
			"bAutoWidth": false,
			"bInfo": false,
			"bPaginate": false,
			"bFilter": false' .
			$tableOptions. '
		});
		evarisk("#' . $idTable . '").children("tfoot").remove();
		evarisk("#' . $idTable . '_wrapper").removeClass("dataTables_wrapper");

		/*	Add the action when clicking on a button in the list	*/
		evarisk("#completeList' . $tableElement . ' .odd, #completeList' . $tableElement . ' .even").click(function(){
			if(evarisk(this).children("td:first").children("span").hasClass("elementIsNotLinked")){
				var currentId = evarisk(this).attr("id").replace("' . $tableElement . $idElement . '_elementList_", "");
				cleanElementIdFiedList(currentId, "' . $tableElement . '");

				var elementContent = evarisk(this).children("td:nth-child(3)").html() + " (" + evarisk(this).children("td:nth-child(2)").html() + ")";

				addElementIdFieldList(elementContent, currentId, "' . $tableElement . '");
			}
			else{
				deleteElementIdFiedList(evarisk(this).attr("id").replace("' . $tableElement . $idElement . '_elementList_", ""), "' . $tableElement . '");
			}
			checkElementListModification("' . $tableElement . '", "' . $idBoutonEnregistrer . '");
		});
	});
</script>';

		/*	Add the tabe result into the output	*/
		$elementList_Table .= evaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script);

		return $elementList_Table;
	}

	/**
	*	Create a link between an element and a group
	*
	*	@param mixed $tableElement The element type we want to create a link to
	*	@param integer $idElement The element identifier we want to create a link to
	*	@param array $element An group list id to create link with the selected element
	*
	*	@return mixed $messageInfo An html output that contain the result message
	*/
	function setLinkProductElement($tableElement, $idElement, $element, $outputMessage = true)
	{
		global $wpdb;
		global $current_user;
		$elementToTreat = "  ";
		$messageInfoContainerIdExt = '_affectProduct';
		$idBoutonEnregistrer = 'affectation_' . $tableElement;

		/*	Get the element list already linked	*/
		$linkedElementCheckList = array();
		$linkedElementList = digirisk_product::getBindElement($idElement, $tableElement);
		if(is_array($linkedElementList ) && (count($linkedElementList) > 0))
		{
			foreach($linkedElementList as $linkedElement)
			{
				$linkedElementCheckList[$linkedElement->id_product] = $linkedElement;
			}
		}

		/*	Transform the new element list to affect into an array	*/
		$newElementList = explode(", ", $element);

		/*	Read the product list already linked for checking if they are again into the list or if we have to delete them form the list	*/
		foreach($linkedElementCheckList as $elements)
		{
			if(is_array($newElementList) && !in_array($elements->id_product, $newElementList))
			{
				$query = $wpdb->prepare(
					"UPDATE " . DIGI_DBT_LIAISON_PRODUIT_ELEMENT . " 
					SET status = 'deleted', 
						date_desAffectation = NOW(), 
						id_desAttributeur = %d 
					WHERE id = %d", 
					$current_user->ID, $elements->id
				);
				$wpdb->query($query);

				if(trim($elements->id_product) != '')
				{
					/*	Save product informations into digirisk database	*/
					digirisk_product::saveProductInformations($elements->id_product);
				}
			}
		}
		if(is_array($newElementList) && (count($newElementList) > 0))
		{
			foreach($newElementList as $elementId)
			{
				if((trim($elementId) != '') && !array_key_exists($elementId, $linkedElementCheckList))
				{
					$elementToTreat .= "('', 'valid', NOW(), '" . $current_user->ID . "', '0000-00-00 00:00:00', '', '" . $elementId . "', '" . $idElement . "', '" . $tableElement . "'), ";
				}
				if(trim($elementId) != '')
				{
					/*	Save product informations into digirisk database	*/
					digirisk_product::saveProductInformations($elementId);
				}
			}
		}

		$message = addslashes('<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Il n\'a aucune modification a apporter', 'evarisk') . '</strong>');
		$endOfQuery = trim(substr($elementToTreat, 0, -2));
		if($endOfQuery != "")
		{
			$query = $wpdb->prepare(
				"REPLACE INTO " . DIGI_DBT_LIAISON_PRODUIT_ELEMENT . "
					(id, status ,date_affectation ,id_attributeur ,date_desAffectation ,id_desAttributeur ,id_product ,id_element ,table_element)
				VALUES 
					" . $endOfQuery
			);
			if($wpdb->query($query))
			{
				$message = addslashes('<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Les modifications ont correctement &eacute;t&eacute enregistr&eacute;es', 'evarisk') . '</strong>');
			}
			else
			{
				$message = addslashes('<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Les modifications n\'ont pas toutes &eacute;t&eacute correctement enregistr&eacute;es', 'evarisk') . '</strong>"');
			}
		}

		if($outputMessage)
		{
			echo 
'<script type="text/javascript">
	evarisk(document).ready(function(){
		actionMessageShow("#messageInfo_' . $tableElement . '_' . $idElement . $messageInfoContainerIdExt . '", "' . $message . '");
		setTimeout(\'actionMessageHide("#messageInfo_' . $tableElement . '_' . $idElement . $messageInfoContainerIdExt . '")\',7500);
		evarisk("#saveButtonLoading' . $tableElement . '").hide();
		evarisk("#saveButtonContainer' . $tableElement . '").show();
		evarisk("#actuallyAffectedList' . $tableElement . '").val(evarisk("#affectedList' . $tableElement . '").val());
		checkElementListModification("' . $tableElement . '", "' . $idBoutonEnregistrer . '");
	});
</script>';
		}
	}

	/**
	*	Get the different element binded
	*
	*	@param integer $idElement The identifier of the element (in its table) we want to bind
	*	@param string $tableElement The table of the element we want to bind
	*
	*	@return object $bindedGroups A wordpress database object with the different groups' id that are binded to the given element
	*/
	function getBindElement($idElement, $tableElement)
	{
		global $wpdb;
		$bindedElement = array();
		
		$idElement = mysql_real_escape_string($idElement);
		$tableElement = mysql_real_escape_string($tableElement);
		
		$query = $wpdb->prepare(
		"SELECT ELT_LINK.* 
		FROM " . DIGI_DBT_LIAISON_PRODUIT_ELEMENT . " AS ELT_LINK
		WHERE ELT_LINK.table_element = '%s' 
			AND ELT_LINK.id_element = %d 
			AND ELT_LINK.status = 'valid' 
		", $tableElement, $idElement);
		$bindedElement = $wpdb->get_results($query);
		
		return $bindedElement;
	}

	/**
	*	Save some basic informations of the product into digirisk database
	*
	*	@param integer $productId The product identifier we want to save into database
	*/
	function saveProductInformations($productId)
	{
		global $wpdb;

		/*	Get informations about the product we want to save into database	*/
		$productInformations = wpshop_attributes::getElementWithAttributeAndValue(WPSHOP_DBT_PRODUCT, wpshop_entities::getEntityIdFromCode('product'), 1, 'code', $productId, "'valid'");
		/*	Get the categories tha product is associated to	*/
		$associatedCategories = wpshop_categories::getAssociatedCategories($productId);

		/*	Get information about existing product in order to check if the product has already been added into database and the last update date of the product to determine if we have to insert it or not	*/
		$query = $wpdb->prepare(
			"SELECT id, product_last_update_date 
			FROM " . DIGI_DBT_PRODUIT . "
			WHERE product_id = %d
			ORDER BY id DESC
			LIMIT 1", $productId);
		$digiProductInformations = $wpdb->get_row($query);

		/*	Check the action that we have to do on the product	*/
		if(is_object($digiProductInformations))
		{
			/*	Check if the product have to be update or not by checking the last update date of the product into wpshop plugin and into digirisk	*/
			if($digiProductInformations->product_last_update_date != $productInformations[$productId]['last_update_date'])
			{
				$action = 'insertproduct';
			}
			else
			{
				$action = 'updateproduct';
			}
		}
		else
		{
			$action = 'insertproduct';
		}

		/*	Execute the action	*/
		if($action == 'updateproduct')
		{
			$newProduct = array();
			$newProduct['last_update_date'] = date('Y-m-d H:i:s');
			$saveAction = eva_database::update($newProduct, $digiProductInformations->id, DIGI_DBT_PRODUIT);
		}
		elseif($action == 'insertproduct')
		{
			$newProduct = array();
			$newProduct['id'] = '';
			$newProduct['status'] = 'valid';
			$newProduct['creation_date'] = date('Y-m-d H:i:s');
			$newProduct['product_id'] = $productId;
			/*	Build the category informations	*/
			$newProduct['category_id'] = $newProduct['category_name'] = '  ';
			foreach($associatedCategories as $categoryId => $category)
			{
				$newProduct['category_id'] .= $categoryId . ', ';
				$newProduct['category_name'] .= $category['category_name'] . ', ';
			}
			$newProduct['category_id'] = substr($newProduct['category_id'], 0, -2);
			$newProduct['category_name'] = substr($newProduct['category_name'], 0, -2);
			$newProduct['product_last_update_date'] = $productInformations[$productId]['last_update_date'];
			$newProduct['product_name'] = $productInformations[$productId]['attributes']['product_name']['value'];
			$saveAction = eva_database::save($newProduct, DIGI_DBT_PRODUIT);
		}
	}

}