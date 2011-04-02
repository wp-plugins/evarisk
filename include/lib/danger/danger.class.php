<?php

class danger
{

	function dangerMainPage()
	{
		$messageInfo = '';

		$_POST['table'] = TABLE_CATEGORIE_DANGER;
		$titrePage = __("Dangers", 'evarisk');
		$icone = EVA_DANGER_ICON;
		$titreIcone = "Danger Categorie Icon";
		$altIcon = "Danger Categorie Icon";
		$titreFilAriane= __("Cat&eacute;gories", 'evarisk');
		if(!isset($_POST['affichage']))
		{
			$_POST['affichage'] = "affichageListe";
		}
		include_once(EVA_LIB_PLUGIN_DIR . 'classicalPage.php' );	
	}

}