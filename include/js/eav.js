$(function()
{
	countElementExistant = 0;

	$('#addChoice').click(function(){

		$("#listeDeChoix li").each(function(){
			countElementExistant++;
		});

		//<img class="imgSupprimeChoix" onclick="javascript:deleteOptionField(\'line' + countElementExistant + '\');" src="' + EVA_IMG_DIVERS_PLUGIN_URL + 'cancel.png" alt="supprimerChoix" />

		$('#listeDeChoix').append('<li id="line' + countElementExistant + '" ><input type="text" name="newDropDownChoice[]" value="" /></li>');
	});

});

function deleteOptionField(idToDelete)
{
	$('#' + idToDelete).remove();
}