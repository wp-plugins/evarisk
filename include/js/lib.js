var evarisk = jQuery.noConflict();

function convertAccentToJS(text)
{
	text = text.replace(/&Agrave;/g, "\300");
	text = text.replace(/&Aacute;/g, "\301");
	text = text.replace(/&Acirc;/g, "\302");
	text = text.replace(/&Atilde;/g, "\303");
	text = text.replace(/&Auml;/g, "\304");
	text = text.replace(/&Aring;/g, "\305");
	text = text.replace(/&AElig;/g, "\306");
	text = text.replace(/&Ccedil;/g, "\307");
	text = text.replace(/&Egrave;/g, "\310");
	text = text.replace(/&Eacute;/g, "\311");
	text = text.replace(/&Ecirc;/g, "\312");
	text = text.replace(/&Euml;/g, "\313");
	text = text.replace(/&Igrave;/g, "\314");
	text = text.replace(/&Iacute;/g, "\315");
	text = text.replace(/&Icirc;/g, "\316");
	text = text.replace(/&Iuml;/g, "\317");
	text = text.replace(/&Eth;/g, "\320");
	text = text.replace(/&Ntilde;/g, "\321");
	text = text.replace(/&Ograve;/g, "\322");
	text = text.replace(/&Oacute;/g, "\323");
	text = text.replace(/&Ocirc;/g, "\324");
	text = text.replace(/&Otilde;/g, "\325");
	text = text.replace(/&Ouml;/g, "\326");
	text = text.replace(/&Oslash;/g, "\330");
	text = text.replace(/&Ugrave;/g, "\331");
	text = text.replace(/&Uacute;/g, "\332");
	text = text.replace(/&Ucirc;/g, "\333");
	text = text.replace(/&Uuml;/g, "\334");
	text = text.replace(/&Yacute;/g, "\335");
	text = text.replace(/&THORN;/g, "\336");
	text = text.replace(/&Yuml;/g, "\570");
	text = text.replace(/&szlig;/g, "\337");
	text = text.replace(/&agrave;/g, "\340");
	text = text.replace(/&aacute;/g, "\341");
	text = text.replace(/&acirc;/g, "\342");
	text = text.replace(/&atilde;/g, "\343");
	text = text.replace(/&auml;/g, "\344");
	text = text.replace(/&aring;/g, "\345");
	text = text.replace(/&aelig;/g, "\346");
	text = text.replace(/&ccedil;/g, "\347");
	text = text.replace(/&egrave;/g, "\350");
	text = text.replace(/&eacute;/g, "\351");
	text = text.replace(/&ecirc;/g, "\352");
	text = text.replace(/&euml;/g, "\353");
	text = text.replace(/&igrave;/g, "\354");
	text = text.replace(/&iacute;/g, "\355");
	text = text.replace(/&icirc;/g, "\356");
	text = text.replace(/&iuml;/g, "\357");
	text = text.replace(/&eth;/g, "\360");
	text = text.replace(/&ntilde;/g, "\361");
	text = text.replace(/&ograve;/g, "\362");
	text = text.replace(/&oacute;/g, "\363");
	text = text.replace(/&ocirc;/g, "\364");
	text = text.replace(/&otilde;/g, "\365");
	text = text.replace(/&ouml;/g, "\366");
	text = text.replace(/&oslash;/g, "\370");
	text = text.replace(/&ugrave;/g, "\371");
	text = text.replace(/&uacute;/g, "\372");
	text = text.replace(/&ucirc;/g, "\373");
	text = text.replace(/&uuml;/g, "\374");
	text = text.replace(/&yacute;/g, "\375");
	text = text.replace(/&thorn;/g, "\376");
	text = text.replace(/&yuml;/g, "\377");
	text = text.replace(/&oelig;/g, "\523");
	text = text.replace(/&OElig;/g, "\522");
	return text;
}

function print_r(obj)
{
	win_print_r = window.open('about:blank', 'win_print_r');
	win_print_r.document.write('<html><body>');
	r_print_r(obj, win_print_r);
	win_print_r.document.write('</body></html>');
}

function r_print_r(theObj, win_print_r)
{
	if(theObj.constructor == Array ||
		theObj.constructor == Object){
		if (win_print_r == null)
			win_print_r = window.open('about:blank', 'win_print_r');
		}
		for(var p in theObj){
			if(theObj[p].constructor == Array||
				theObj[p].constructor == Object){
				win_print_r.document.write("<li>["+p+"] =>"+typeof(theObj)+"</li>");
				win_print_r.document.write("<ul>")
				r_print_r(theObj[p], win_print_r);
				win_print_r.document.write("</ul>")
			} else {
				win_print_r.document.write("<li>["+p+"] =>"+theObj[p]+"</li>");
			}
		}
	win_print_r.document.write("</ul>");
}

function checkdate (month, day, year) {
    // Returns true(1) if it is a valid date in gregorian calendar  
    // 
    // version: 1006.1915
    // discuss at: http://phpjs.org/functions/checkdate    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Pyerre
    // +   improved by: Theriault
    // *     example 1: checkdate(12, 31, 2000);
    // *     returns 1: true    // *     example 2: checkdate(2, 29, 2001);
    // *     returns 2: false
    // *     example 3: checkdate(03, 31, 2008);
    // *     returns 3: true
    // *     example 4: checkdate(1, 390, 2000);    // *     returns 4: false
    return month > 0 && month < 13 && year > 0 && year < 32768 && day > 0 && day <= (new Date(year, month, 0)).getDate();
}


function changementPage(partie, table, page, idPere, affichage, option)
{
	var partContainer = 'partieEdition';
	if(partie == 'left')
	{
		var partContainer = 'partieGauche';
	}

	evarisk("#" + partContainer).html(	evarisk("#loadingImg").html()	);
	if(affichage == 'affichageTable'){
		evarisk("#" + partContainer).load(EVA_AJAX_FILE_URL, 
		{
			"post": "true", 
			"table": table,
			"act": "changementPage",
			"page": page,
			"idPere": idPere,
			"partie": partie,
			"affichage": affichage,
			"partition": option,
			"menu": evarisk("#menu").val()
		});
	}
	else{
		evarisk("#" + partContainer).load(EVA_AJAX_FILE_URL, 
		{
			"post": "true", 
			"table": table,
			"act": "changementPage",
			"page": page,
			"idPere": idPere,
			"partie": partie,
			"affichage": affichage,
			"expanded": option,
			"menu": evarisk("#menu").val()
		});
	}
}

function commonTabChange(boxId, divId, tabId)
{
	evarisk("#" + boxId + " .tabs").each(function(){
		evarisk(this).removeClass("selected_tab");
	});
	evarisk("#" + boxId + " .eva_tabs_panel").each(function(){
		evarisk(this).hide();
	});
	evarisk(divId).show();
	evarisk(tabId).addClass("selected_tab");
}
function tabChange(divId, tabId)
{
	evarisk("#postBoxRisques .tabs").each(function(){
		evarisk(this).removeClass("selected_tab");
	});
	evarisk("#postBoxRisques .eva_tabs_panel").each(function(){
		evarisk(this).hide();
	});
	evarisk(divId).show();
	evarisk(tabId).addClass("selected_tab");
}
function hideExtraTab()
{
	evarisk("#ongletEditerRisque").css("display","none");
	evarisk("#ongletControlerActionDemandee").css("display","none");
	evarisk("#ongletDemandeActionCorrective" + TABLE_RISQUE).css("display","none");
	evarisk("#divDemandeAction" + TABLE_RISQUE).html("");
	evarisk("#ongletSuiviActionCorrective" + TABLE_RISQUE).css("display","none");
	evarisk("#divSuiviAction" + TABLE_RISQUE).html("");
	evarisk("#ongletFicheActionCorrective" + TABLE_RISQUE).css("display","none");
	evarisk("#divFicheAction" + TABLE_RISQUE).html("");
	evarisk("#ongletHistoRisk" + TABLE_RISQUE).css("display","none");
	evarisk("#divHistoRisk" + TABLE_RISQUE).html("");
}

function selectRowInTreeTable(tableId)
{
	// Make visible that a row is clicked
	evarisk("table#" + tableId + " tbody tr").click(function() {
		evarisk("tr.selected").removeClass("selected"); // Deselect currently selected rows
		evarisk("tr.edited").removeClass("edited"); // Deselect currently selected rows
		evarisk(this).addClass("selected");
	});

	// Make sure row is selected when span is clicked
	evarisk("table#" + tableId + " tbody tr span").click(function() {
		evarisk(evarisk(this).parents("tr")[0]).trigger("click");
	});
}
function reInitTreeTable()
{
	evarisk("#rightEnlarging").show();
	evarisk("#equilize").click();
	var expanded = new Array();
	evarisk(".expanded").each(function(){
		expanded.push(evarisk(this).attr("id"));
	});
	return expanded;
}

function initialiseClassicalPage()
{
	if(evarisk("#rightSide-sortables").html() == ""){
		evarisk("#rightEnlarging").hide();
	}
	else{
		evarisk("#rightEnlarging").show();
	}
	if(evarisk("#leftSide-sortables").html() == ""){
		evarisk("#leftEnlarging").hide();
	}
	else{
		evarisk("#leftEnlarging").show();
	}
}
function initialiseEditedElementInGridMode(idToEdit)
{
	evarisk("#tablemainPostBox tbody tr:nth-child(3)").each(function(){
		for(var i=1; i<=evarisk(this).children("td").length; i++)
		{
			if(evarisk(this).children("td:nth-child(" + i + ")").children("img").attr("id") == idToEdit)
			{												
				evarisk(this).prevAll("tr:not(tr:first-child)").andSelf().children("td:nth-child(" + i + ")").addClass("edited");
				// 3 * i car nomInfo + : + info
				evarisk(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i) + ")").addClass("edited");
				evarisk(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i - 1) + ")").addClass("edited");
				evarisk(this).prevAll("tr:first-child").children("td:nth-child(" + (3 * i - 2) + ")").addClass("edited");
			}
		}
	});
}

function actionMessageShow(idToShow, messageToShow){
	evarisk(idToShow).show();
	evarisk(idToShow).addClass("updated");
	evarisk(idToShow).html(messageToShow);
}
function actionMessageHide(idToHide){
	evarisk(idToHide).hide();
	evarisk(idToHide).html("");
	evarisk(idToHide).removeClass("updated");
}

function emptyOptionForm()
{
	evarisk("#actionOption").val("save");
	evarisk("#idOption").val("");
	evarisk("#nomOption").val("");
	evarisk("#type").val("");
	evarisk("#optionName").html("");
	evarisk("#optionEdition").html("");
	evarisk("#light").hide();
	evarisk("#fade").hide();
}
function goTo(ancre)
{
	var speed = 1000;
	jQuery("html,body").animate({scrollTop:jQuery(ancre).offset().top},speed,"swing",function(){
		if(ancre != "body")
				window.location.hash = ancre;
		else
				window.location.hash = "#";
		jQuery(ancre).attr("tabindex","-1");
		jQuery(ancre).focus();
		jQuery(ancre).removeAttr("tabindex");
	});
}

function updateTips( t, container )
{
	container
		.text( t )
		.addClass( "ui-state-highlight" );
	setTimeout(function() {
		container.removeClass( "ui-state-highlight", 1500 );
	}, 500 );
	setTimeout(function() {
		container.text( "" );
	}, 5000 );
}
function checkLength( o, n, min, max, msg, errorContainer )
{
	if ( o.val().length > max || o.val().length < min ){
		o.addClass( "ui-state-error" );
		updateTips( convertAccentToJS(msg.replace("!#!term!#!", n).replace("!#!minlength!#!", min).replace("!#!maxlength!#!", max)), errorContainer );
		return false;
	}
	else{
		return true;
	}
}
function checkRegexp( o, regexp, n, errorContainer  )
{
	if ( !( regexp.test( o.val() ) ) ){
		o.addClass( "ui-state-error" );
		updateTips( n, errorContainer );
		return false;
	}
	else{
		return true;
	}
}

/*	Picture galery functions	*/
function reloadcontainer(tableElement, idElement, PICTO_LOADING_ROUND)
{
	evarisk("#pictureGallery" + tableElement + "_" + idElement).html('<img src="' + PICTO_LOADING_ROUND + '" alt="loading" />');
	evarisk("#pictureGallery" + tableElement + "_" + idElement).load(EVA_AJAX_FILE_URL, {
		"post": "true",
		"table": tableElement,
		"idElement": idElement,
		"act": "reloadGallery"
	});
}
function showGallery(tableElement, idElement, PICTO_LOADING_ROUND)
{
	evarisk("#pictureGallery" + tableElement + "_" + idElement).html('<img src="' + PICTO_LOADING_ROUND + '" alt="loading" />');
	evarisk("#pictureGallery" + tableElement + "_" + idElement).load(EVA_AJAX_FILE_URL, {
		"post": "true",
		"table": tableElement,
		"idElement": idElement,
		"act": "showGallery"
	});
}

/*	Recommandation functions	*/
function deleteRecommandationCategory(recommandationCategoryId, tableElement, alertMessage)
{
	if(confirm(convertAccentToJS(alertMessage))){
		evarisk("#ajax-response").load(EVA_AJAX_FILE_URL, {
			"post":"true",
			"table":tableElement,
			"act":"deleteRecommandationCategory",
			"id":recommandationCategoryId
		});
	}
}
function editRecommandationCategory(recommandationCategoryId, tableElement)
{
	evarisk("#recommandationCategoryFormContainer").hide();
	evarisk("#loadingCategoryRecommandationForm").html(evarisk("#loadingImg").html());
	evarisk("#loadingCategoryRecommandationForm").show();
	evarisk("#recommandationCategoryInterfaceContainer").dialog("open");
	evarisk("#recommandationCategoryFormContainer").load(EVA_AJAX_FILE_URL, 
	{
		"post":"true",
		"table":tableElement,
		"act":"loadRecommandationCategoryManagementForm",
		"id":recommandationCategoryId
	});
}

/*	User link functions	*/
function userDeletion(userId, tableElement){
	id = userId.replace("affectedUser" + tableElement, "");
	deleteUserIdFiedList(id, tableElement);
	checkUserListModification(tableElement, "save_group" + tableElement);
}
function addUserIdFieldList(name, id, tableElement){
	evarisk("#noUserSelected" + tableElement).remove();
	evarisk("#userListOutput" + tableElement).attr("scrollTop",0);

	evarisk(evarisk("#userBlocContainer").html()).prependTo("#userListOutput" + tableElement);
	evarisk("#userListOutput" + tableElement + " div:first").attr("id", "affectedUser" + tableElement + id);
	evarisk("#affectedUser" + tableElement + id).html(evarisk("#affectedUser" + tableElement + id).html().replace("#USERNAME#", name));
}
function checkUserListModification(tableElement, idButton){
	var actualUserList = evarisk("#actuallyAffectedUserIdList" + tableElement).val();
	var userList = evarisk("#affectedUserIdList" + tableElement).val();

	if(actualUserList == userList){
		evarisk("#" + idButton).prop("disabled", "disabled");
		evarisk("#" + idButton).addClass("button-secondary");
		evarisk("#" + idButton).removeClass("button-primary");
	}
	else{
		evarisk("#" + idButton).prop("disabled", "");
		evarisk("#" + idButton).removeClass("button-secondary");
		evarisk("#" + idButton).addClass("button-primary");
	}
}
function cleanUserIdFiedList(id, tableElement){
	var actualAffectedUserList = evarisk("#affectedUserIdList" + tableElement).val().replace(" " + id + ", ", "");
	evarisk("#affectedUserIdList" + tableElement).val( actualAffectedUserList + id + ", ");

	if(evarisk("#affectedUser" + tableElement + id)){
		evarisk("#affectedUser" + tableElement + id).remove();
	}

	evarisk("#actionButton" + tableElement + "UserLink" + id).addClass("userIsLinked");
	evarisk("#actionButton" + tableElement + "UserLink" + id).removeClass("userIsNotLinked");
}
function deleteUserIdFiedList(id, tableElement){
	var actualAffectedUserList = evarisk("#affectedUserIdList" + tableElement).val().replace(id + ", ", "");
	evarisk("#affectedUserIdList" + tableElement).val( actualAffectedUserList );
	evarisk("#affectedUser" + tableElement + id).remove();

	evarisk("#actionButton" + tableElement + "UserLink" + id).removeClass("userIsLinked");
	evarisk("#actionButton" + tableElement + "UserLink" + id).addClass("userIsNotLinked");
}

/*	Element link functions	*/
function elementDeletion(elementId, tableElement, idButton){
	id = elementId.replace("affectedElement" + tableElement, "");
	deleteElementIdFiedList(id, tableElement);
	checkElementListModification(tableElement, idButton);
}
function addElementIdFieldList(name, id, tableElement){
	evarisk("#noElementSelected" + tableElement).remove();
	evarisk("#affectedListOutput" + tableElement).attr("scrollTop",0);

	evarisk(evarisk("#elementBlocContainer" + tableElement).html()).prependTo("#affectedListOutput" + tableElement);

	evarisk("#affectedListOutput" + tableElement + " div:first").attr("id", "affectedElement" + tableElement + id);
	evarisk("#affectedElement" + tableElement + id).html(evarisk("#affectedElement" + tableElement + id).html().replace("#ELEMENTNAME#", name));
}
function checkElementListModification(tableElement, idButton){
	var actuallyAffectedList = evarisk("#actuallyAffectedList" + tableElement).val();
	var affectedList = evarisk("#affectedList" + tableElement).val();

	if(actuallyAffectedList == affectedList){
		evarisk("#" + idButton).prop("disabled", "disabled");
		evarisk("#" + idButton).addClass("button-secondary");
		evarisk("#" + idButton).removeClass("button-primary");
	}
	else{
		evarisk("#" + idButton).prop("disabled", "");
		evarisk("#" + idButton).removeClass("button-secondary");
		evarisk("#" + idButton).addClass("button-primary");
	}
}
function cleanElementIdFiedList(id, tableElement){
	var actualAffectedUserList = evarisk("#affectedList" + tableElement).val().replace(" " + id + ", ", "");
	evarisk("#affectedList" + tableElement).val( actualAffectedUserList + id + ", ");

	if(evarisk("#affectedElement" + tableElement + id)){
		evarisk("#affectedElement" + tableElement + id).remove();
	}

	evarisk("#actionButton" + tableElement + "ElementLink" + id).addClass("elementIsLinked");
	evarisk("#actionButton" + tableElement + "ElementLink" + id).removeClass("elementIsNotLinked");
}
function deleteElementIdFiedList(id, tableElement){
	var actualAffectedUserList = evarisk("#affectedList" + tableElement).val().replace(id + ", ", "");
	evarisk("#affectedList" + tableElement).val( actualAffectedUserList );
	evarisk("#affectedElement" + tableElement + id).remove();

	evarisk("#actionButton" + tableElement + "ElementLink" + id).removeClass("elementIsLinked");
	evarisk("#actionButton" + tableElement + "ElementLink" + id).addClass("elementIsNotLinked");
}


function createGroupement(action, table){
	evarisk("#act").val(action);
	evarisk("#ajax-response").load(EVA_AJAX_FILE_URL, {
		"post": "true", 
		"table": table,
		"act": evarisk("#act").val(),
		"id": evarisk("#id").val(),
		"typeGroupement": evarisk("#typeGroupement").val(),
		"nom_groupement": evarisk("#nom_groupement").val(),
		"groupementPere": evarisk("#groupementPere :selected").val(),
		"description": evarisk("#description").val(),
		"adresse_ligne_1": evarisk("#adresse_ligne_1").val(),
		"adresse_ligne_2": evarisk("#adresse_ligne_2").val(),
		"code_postal": evarisk("#code_postal").val(),
		"ville": evarisk("#ville").val(),
		"telephone": evarisk("#telephone").val(),
		"effectif": evarisk("#effectif").val(),
		"affichage": evarisk("#affichage").val(),
		"latitude": evarisk("#latitude").val(),
		"longitude": evarisk("#longitude").val(),
		"siren": evarisk("#siren").val(),
		"siret": evarisk("#siret").val(),
		"social_activity_number": evarisk("#social_activity_number").val(),
		"idsFilAriane": evarisk("#idsFilAriane").val()
	});
}
function createUniteTravail(action, table){
	evarisk("#act").val(action);
	evarisk("#ajax-response").load(EVA_AJAX_FILE_URL, {
		"post": "true", 
		"table": table,
		"act": evarisk("#act").val(),
		"id": evarisk("#id").val(),
		"nom_unite_travail": evarisk("#nom_unite_travail").val(),
		"groupementPere": evarisk("#groupementPere :selected").val(),
		"description": evarisk("#description").val(),
		"adresse_ligne_1": evarisk("#adresse_ligne_1").val(),
		"adresse_ligne_2": evarisk("#adresse_ligne_2").val(),
		"code_postal": evarisk("#code_postal").val(),
		"ville": evarisk("#ville").val(),
		"telephone": evarisk("#telephone").val(),
		"effectif": evarisk("#effectif").val(),
		"effectif": evarisk("#effectif").val(),
		"affichage": evarisk("#affichage").val(),
		"latitude": evarisk("#latitude").val(),
		"longitude": evarisk("#longitude").val(),
		"idsFilAriane": evarisk("#idsFilAriane").val()
	});
}


/**
*	Add utilities for page shape selection. show/hide right/left part, enlarge/shrink right/left part
*/
function main_page_shape_selector(){
	jQuery("#leftEnlarging").click(function() {
		jQuery("#digirisk_right_container").hide();
		jQuery("#digirisk_left_container").show();
		jQuery("#digirisk_left_container").css("width", "99%");
		adminMenu.fold();
		jQuery("#enlarging .ui-slider-range").css("width","100%");
		jQuery("#enlarging .ui-slider-handle").css("left","100%");
	});
	evarisk("#rightEnlarging").click(function() {
		jQuery("#digirisk_left_container").hide();
		jQuery("#digirisk_right_container").show();
		jQuery("#digirisk_right_container").css("width", "99%");
		adminMenu.fold();
		jQuery("#enlarging .ui-slider-range").css("width","0%");
		jQuery("#enlarging .ui-slider-handle").css("left","0%");
	});
	evarisk("#equilize").click(function() {
		jQuery("#digirisk_left_container").show();
		jQuery("#digirisk_right_container").show();
		jQuery("#digirisk_right_container").css("width", "50%");
		jQuery("#digirisk_left_container").css("width", "49%");
		jQuery("#enlarging .ui-slider-range").css("width","50%");
		jQuery("#enlarging .ui-slider-handle").css("left","50%");
	});
	evarisk("#enlarging .ui-slider-horizontal").css("width","100px");
	evarisk("#enlarging").slider({
		range: "min",
		value: 50,
		min: 25,
		max:  75,
		slide: function(event, ui) {
			var largeurGauche = ui.value - 1;
			var largeurDroite = 99 - largeurGauche;
			if(largeurGauche == 24 || largeurDroite == 24){
				adminMenu.fold();
			}
			evarisk("#digirisk_right_container").show();
			evarisk("#digirisk_left_container").show();
			evarisk("#digirisk_left_container").css("width", largeurGauche  + "%");
			evarisk("#digirisk_right_container").css("width", largeurDroite  + "%");
		}
	});
}
