<?php
include_once(EVA_CONFIG );
require_once(EVA_LIB_PLUGIN_DIR . 'arborescence.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayDesign.class.php' );

	
	$renduPage = EvaDisplayDesign::afficherDebutPage($titrePage, $icone, $titreIcone, $altIcon, $_POST['table'], false, $messageInfo, true);
	$script = '
		<script type="text/javascript">
			$(document).ready(function() {
				$(\'#champsCaches\').html(\'<input type="hidden" id="pagemainPostBoxReference" value=1 /><input type="hidden" id="identifiantActuellemainPostBox" value=1 />\');';
	$affichage = $_POST['affichage'];
	if($affichage == 'affichageListe')
	{
		$script = $script . '
			$(\'#affichageListe\').addClass("selectedAffichage");
			$(\'#filAriane\').hide();';
	}
	else
	{
		$racine = arborescence::getRacine($_POST['table']);
		$script = $script . '
			$(\'#affichageTable\').addClass("selectedAffichage");
			while(document.getElementById(\'filAriane\').lastChild.id != "element' . $racine->id . '")
			{
				document.getElementById(\'filAriane\').removeChild(
					document.getElementById(\'filAriane\').lastChild);
			}
			$(\'#filAriane\').show();';
	}
	$script = $script . '
        $(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
          "table": "' . $_POST['table'] . '",
          "act": "changementPage",
          "page": 1,
          "idPere": 1,
          "partie": "right",
          "affichage": "' . $affichage . '",
          "partition": "main",
					"menu": $("#menu").val()
        });
        $(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
          "table": "' . $_POST['table'] . '",
          "act": "changementPage",
          "page": 1,
          "idPere": 1,
          "partie": "left",
          "affichage": "' . $affichage . '",
          "partition": "main",
					"menu": $("#menu").val()
        });
        $(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
        $(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
      });
		</script>';
	$renduPage = $script . $renduPage . EvaDisplayDesign::getFilAriane($affichage, $titreFilAriane, $_POST['table'], 'mainPostBox', null);
	$renduPage = $renduPage . EvaDisplayDesign::ouvrirMetaboxHolder();
	$renduPage = $renduPage .EvaDisplayDesign::splitEcran('', '');
	$renduPage = $renduPage . EvaDisplayDesign::fermerMetaboxHolder();
	$renduPage = $renduPage . EvaDisplayDesign::afficherFinPage();
	echo $renduPage;
?>
