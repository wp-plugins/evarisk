$(function(){

	/*	Click sur Tout cocher*/
	$('#cocheTout').click(function(){
		var cases = $("#capabilities").find(':checkbox');
		cases.attr('checked', true);
	});

	/*	Click sur Tout d�cocher*/
	$('#deCocheTout').click(function(){
		var cases = $("#capabilities").find(':checkbox');
		cases.attr('checked', false);
	});

});