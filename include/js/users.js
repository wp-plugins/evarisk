$(function()
{
	$("#usersList li").draggable({
		appendTo: "body",
		helper: "clone"
	});
	$("#groupContent ol").droppable({
		activeClass: "ui-state-default",
		hoverClass: "ui-state-hover",
		accept: ":not(.nouser)",
		drop: function(event, ui) {
			var blocId = ui.draggable.attr('id');
			addUserToGroup(blocId, ui);
		}
	});
	$("#groupContent ol").sortable({
		items: "li:not(.placeholder)",
		sort: function() {
			$(this).removeClass("ui-state-default");
		}
	});
});


function addUserToGroup(blocId, event)
{
	var countElementExistant = 0;
	var countElementCache = 0;

	$("#groupContent ol").find(".placeholder").hide();

	var listContent;
	if(typeof(event) !== 'string')
	{
		listContent = event.draggable.text();
	}
	else
	{
		listContent = event;
	}

	$("<li id='" + blocId + "_added' ></li>").text(listContent).appendTo("#groupContent ol");
	$("<img id='" + blocId + "_del' onclick='javascript:deleteUserFromGroup(\"" + blocId + "\");' src='" + EVA_IMG_PICTOS_PLUGIN_URL + "delete.PNG' alt='delete' />").appendTo("#" + blocId + "_added");
	$('#'+blocId).hide();
	$('#groupUserList').val($('#groupUserList').val() + blocId + ",");

	$("#usersList li").each(function(){
		countElementExistant++;
		if($(this).attr('style') == 'display: none;')
		{
			countElementCache++;
		}
	});

	if(countElementExistant == countElementCache)
	{
		$("#nouser").show();
	}
}


function deleteUserFromGroup(blocId)
{
	var countElement = 0;

	$('#' + blocId).show();
	$('#' + blocId + '_added').remove();
	$('#groupUserList').val($('#groupUserList').val().replace(blocId+",",""));

	$("#groupContent li").each(function(){
		countElement++;
	});

	if(countElement == 1)
	{
		$('#groupUserList').val("");
		$("#groupContent ol").find(".placeholder").show();
	}
	else
	{
		$("#nouser").hide();
	}
}