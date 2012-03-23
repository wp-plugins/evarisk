<?php
	include_once(EVA_CONFIG );
	require_once(EVA_LIB_PLUGIN_DIR . 'arborescence.class.php' );
	require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayDesign.class.php' );
	$affichage = $_POST['affichage'];

	$renduPage = EvaDisplayDesign::afficherDebutPage($titrePage, $icone, $titreIcone, $altIcon, $_POST['table'], false, $messageInfo, true);
	$script = '
		<script type="text/javascript">
			evarisk(document).ready(function() {
				evarisk("#champsCaches").html(evarisk("#hiddenFieldToShow").html());';
	if($affichage == 'affichageListe')
	{
		$script .= '
			evarisk("#affichageListe").addClass("selectedAffichage");
			evarisk("#filAriane").hide();';
	}
	else
	{
		$racine = arborescence::getRacine($_POST['table']);
		$script .= '
			evarisk("#affichageTable").addClass("selectedAffichage");
			while(document.getElementById("filAriane").lastChild.id != "element' . $racine->id . '")
			{
				document.getElementById("filAriane").removeChild(
					document.getElementById("filAriane").lastChild
				);
			}
			evarisk("#filAriane").show();';
	}
	$script .= '
				// changementPage("right", "' . $_POST['table'] . '", 1, 1, "' . $affichage . '", "main");
				changementPage("left", "' . $_POST['table'] . '", 1, 1, "' . $affichage . '", "main");
      });
		</script>';

	$renduPage .= 
		'<div class="hide" id="loadingImg" ><div class="main_loading_pic_container" ><img src="' . PICTO_LOADING . '" alt="loading..." /></div></div>
		<div class="digirisk_hide" id="loading_round_pic" ><div class="main_loading_pic_container" ><img src="' . admin_url('images/loading.gif') . '" alt="loading..." /></div></div>
		<div style="display:none;" id="hiddenFieldToShow" ><input type="hidden" id="pagemainPostBoxReference" value="1" /><input type="hidden" id="identifiantActuellemainPostBox" value="1" /></div>'
	 . $script
	 . EvaDisplayDesign::getFilAriane($affichage, $titreFilAriane, $_POST['table'], 'mainPostBox', null)
	 . EvaDisplayDesign::ouvrirMetaboxHolder()
	 . EvaDisplayDesign::splitEcran('', '')
	 . EvaDisplayDesign::fermerMetaboxHolder()
	 . EvaDisplayDesign::afficherFinPage();

	echo $renduPage;
