evarisk(function()
{
	evarisk("#usersList li").draggable({
		appendTo: "body",
		helper: "clone"
	});
	evarisk("#groupContent ol").droppable({
		activeClass: "ui-state-default",
		hoverClass: "ui-state-hover",
		accept: ":not(.nouser)",
		drop: function(event, ui) {
			var blocId = ui.draggable.attr('id');
			addUserToGroup(blocId, ui);
		}
	});
	evarisk("#groupContent ol").sortable({
		items: "li:not(.placeholder)",
		sort: function() {
			evarisk(this).removeClass("ui-state-default");
		}
	});
});


function addUserToGroup(blocId, event)
{
	var countElementExistant = 0;
	var countElementCache = 0;

	evarisk("#groupContent ol").find(".placeholder").hide();

	var listContent;
	if(typeof(event) !== 'string')
	{
		listContent = event.draggable.text();
	}
	else
	{
		listContent = event;
	}

	evarisk("<li id='" + blocId + "_added' ></li>").text(listContent).appendTo("#groupContent ol");
	evarisk("<img id='" + blocId + "_del' onclick='javascript:deleteUserFromGroup(\"" + blocId + "\");' src='" + PICTO_DELETE + "' alt='delete' />").appendTo("#" + blocId + "_added");
	evarisk('#'+blocId).hide();
	evarisk('#groupUserList').val(evarisk('#groupUserList').val() + blocId + ",");

	evarisk("#usersList li").each(function(){
		countElementExistant++;
		if(evarisk(this).attr('style') == 'display: none;')
		{
			countElementCache++;
		}
	});

	if(countElementExistant == countElementCache)
	{
		evarisk("#nouser").show();
	}
}


function deleteUserFromGroup(blocId)
{
	var countElement = 0;

	evarisk('#' + blocId).show();
	evarisk('#' + blocId + '_added').remove();
	evarisk('#groupUserList').val(evarisk('#groupUserList').val().replace(blocId+",",""));

	evarisk("#groupContent li").each(function(){
		countElement++;
	});

	if(countElement == 1)
	{
		evarisk('#groupUserList').val("");
		evarisk("#groupContent ol").find(".placeholder").show();
	}
	else
	{
		evarisk("#nouser").hide();
	}
}