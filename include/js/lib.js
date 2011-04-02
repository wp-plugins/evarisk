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
	evarisk("#ongletDemandeActionCorrective" + TABLE_RISQUE).css("display","none");
	evarisk("#ongletSuiviActionCorrective" + TABLE_RISQUE).css("display","none");
	evarisk("#ongletFicheActionCorrective" + TABLE_RISQUE).css("display","none");
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

function actionMessageShow(idToShow, messageToShow)
{
	evarisk(idToShow).show();
	evarisk(idToShow).addClass("updated");
	evarisk(idToShow).html(messageToShow);
}
function actionMessageHide(idToHide)
{
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
		tips.removeClass( "ui-state-highlight", 1500 );
	}, 500 );
}
function checkLength( o, n, min, max, msg, errorContainer )
{
	if ( o.val().length > max || o.val().length < min ) {
		o.addClass( "ui-state-error" );
		updateTips( msg.replace("term", n).replace("minlength", min).replace("maxlength", max), errorContainer );
		return false;
	} else {
		return true;
	}
}
function checkRegexp( o, regexp, n, errorContainer  )
{
	if ( !( regexp.test( o.val() ) ) ) {
		o.addClass( "ui-state-error" );
		updateTips( n, errorContainer );
		return false;
	} else {
		return true;
	}
}
		