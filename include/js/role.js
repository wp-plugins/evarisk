evarisk(function(){

	/*	Click sur Tout cocher*/
	evarisk('#cocheTout').click(function(){
		var cases = evarisk("#capabilities").find(':checkbox');
		cases.attr('checked', true);
	});

	/*	Click sur Tout décocher*/
	evarisk('#deCocheTout').click(function(){
		var cases = evarisk("#capabilities").find(':checkbox');
		cases.attr('checked', false);
	});

});