/**
*
*/
function saveRightForUsers(tableElement, idElement, dbTable, outputMessage, tableContainer)
{

	evarisk("#listeIndividusPourDroits" + tableElement + idElement + "_filter input").focus();
	evarisk("#listeIndividusPourDroits" + tableElement + idElement + "_filter input").select();
	evarisk("#listeIndividusPourDroits" + tableElement + idElement + "_filter input").val("");
	evarisk("#listeIndividusPourDroits" + tableElement + idElement + "_filter input").keyup();

	evarisk("#userRightDetail_see").val("");
	evarisk(".see").each(function(){
		if(evarisk(this).is(":checked")){
			evarisk("#userRightDetail_see").val( evarisk("#userRightDetail_see").val() + evarisk(this).val() + "!#!" + evarisk(this).attr("id").replace("user_see_", "") + "#!#" );
		}
	});

	evarisk("#userRightDetail_recursif").val("");
	evarisk(".recursif").each(function(){
		if(evarisk(this).is(":checked")){
			evarisk("#userRightDetail_recursif").val( evarisk("#userRightDetail_recursif").val() + evarisk(this).val() + "!#!" + evarisk(this).attr("id").replace("user_recursif", "") + "#!#" );
		}
	});

	evarisk("#userRightDetail_delete").val("");
	evarisk(".delete").each(function(){
		if(evarisk(this).is(":checked")){
			evarisk("#userRightDetail_delete").val( evarisk("#userRightDetail_delete").val() + evarisk(this).val() + "!#!" + evarisk(this).attr("id").replace("user_delete_", "") + "#!#" );
		}
	});

	evarisk("#userRightDetail_edit").val("");
	evarisk(".edit").each(function(){
		if(evarisk(this).is(":checked")){
			evarisk("#userRightDetail_edit").val( evarisk("#userRightDetail_edit").val() + evarisk(this).val() + "!#!" + evarisk(this).attr("id").replace("user_edit_", "") + "#!#" );
		}
	});

	evarisk("#userRightDetail_add_gpt").val("");
	evarisk(".add_groupement").each(function(){
		if(evarisk(this).is(":checked")){
			evarisk("#userRightDetail_add_gpt").val( evarisk("#userRightDetail_add_gpt").val() + evarisk(this).val() + "!#!" + evarisk(this).attr("id").replace("user_add_gpt", "") + "#!#" );
		}
	});

	evarisk("#userRightDetail_add_unit").val("");
	evarisk(".add_unite").each(function(){
		if(evarisk(this).is(":checked")){
			evarisk("#userRightDetail_add_unit").val( evarisk("#userRightDetail_add_unit").val() + evarisk(this).val() + "!#!" + evarisk(this).attr("id").replace("user_add_unit", "") + "#!#" );
		}
	});

	evarisk("#ajax-response").load(EVA_AJAX_FILE_URL, 
	{
		"post": "true", 
		"table": dbTable,
		"act": "save",
		"message": outputMessage,
		"tableContainer": tableContainer,

		"user_recursif": evarisk("#userRightDetail_recursif").val(),

		"user_see": evarisk("#userRightDetail_see").val(),
		"user_delete": evarisk("#userRightDetail_delete").val(),
		"user_edit": evarisk("#userRightDetail_edit").val(),
		"user_add_gpt": evarisk("#userRightDetail_add_gpt").val(),
		"user_add_unit": evarisk("#userRightDetail_add_unit").val(),

		"user_see_old": evarisk("#userRightDetail_see_old").val(),
		"user_delete_old": evarisk("#userRightDetail_delete_old").val(),
		"user_edit_old": evarisk("#userRightDetail_edit_old").val(),
		"user_add_gpt_old": evarisk("#userRightDetail_add_gpt_old").val(),
		"user_add_unit_old": evarisk("#userRightDetail_add_unit_old").val(),

		"tableElement": tableElement,
		"idElement": idElement
	});

}