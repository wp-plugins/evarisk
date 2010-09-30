<?php

function getMainPostBoxBody($arguments)
{
	include_once(EVA_CONFIG );
	require_once(EVA_LIB_PLUGIN_DIR . 'arborescence.class.php' );
	require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayDesign.class.php' );

	$postBoxId = 'mainPostBox';
	$idPere = $arguments['idPere'];
	$table = $arguments['tableElement'];
	switch($table)
	{
		case TABLE_GROUPEMENT:
			$sousTable = TABLE_UNITE_TRAVAIL;
			break;
		case TABLE_UNITE_TRAVAIL:
			$table = TABLE_GROUPEMENT;
			$sousTable = TABLE_UNITE_TRAVAIL;
			break;
		case TABLE_CATEGORIE_DANGER:
			$sousTable = TABLE_DANGER;
			break;
		case TABLE_DANGER:
			$table = TABLE_CATEGORIE_DANGER;
			$sousTable = TABLE_DANGER;
			break;
		case TABLE_TACHE:
			$sousTable = TABLE_ACTIVITE;
			break;
		case TABLE_ACTIVITE:
			$table = TABLE_TACHE;
			$sousTable = TABLE_ACTIVITE;
			break;
	}
	$elementPere = ($idPere != 0)?Arborescence::getElement($table, $idPere):Arborescence::getRacine($table);
	if($arguments['affichage'] == 'affichageTable')
	{
		$page = ((int)$arguments['page'] < 1)?1:(int)$arguments['page'];
			
		$listeElements = Arborescence::getFils($table, $elementPere);
		if(!isset($arguments['tableElement']))
			$arguments['tableElement'] = null;
		if(!isset($arguments['idElement']))
			$arguments['idElement'] = null;
		$liste = EvaDisplayDesign::creerListe($table, $page, $elementPere->id, $postBoxId, $arguments['tableElement'], $arguments['idElement']);
		$mainPostBoxBody = '
			<div id="containerElement' . $postBoxId . '" class="containerElement">
				<div id="listePrimaire' . $postBoxId . '" class="listeEnLigne">' . $liste . '
				</div>
			<div class="clear"></div>
		';
		
		
		//Ajout des boutons "ajouter" et "remonter"
		switch($table)
		{
			case TABLE_GROUPEMENT:
				$first = __("Nouveau groupement", 'evarisk');
				$second = __("Nouvelle unit&eacute; de travail", 'evarisk');
				break;
			case TABLE_CATEGORIE_DANGER:
				$first = __("Nouvelle cat&eacute;gorie de danger", 'evarisk');
				$second = __("Nouveau danger", 'evarisk');
				break;
		}
		$scriptEvaluationRisques = '
			<script type="text/javascript">
				$(document).ready(function(){
					//On affiche le fil d\'ariane
					$(\'#filAriane\').show();
				
					//On change le titre de la metaBox
					if(' . $elementPere->id . ' != 1)
						$(\'#' . $postBoxId . ' h3 span\').html("' . addslashes($elementPere->nom) . '");					
					
					$("#' . $postBoxId . ' .handlediv").after(\'<div id="favorite-actions-' . $postBoxId . '" class="alignright favorite-actions"><div id="favorite-toggle-' . $postBoxId . '" class="favorite-toggle alignright"></div><div id="favorite-first-' . $postBoxId . '" class="favorite-first"><a href="#" onclick="return false;">' . __('Ajouter', 'evarisk') . '...</a></div><div id="favorite-inside-' . $postBoxId . '" style="display: none;" class="favorite-inside"><div class="favorite-action"><a id="favorite-first-link-' . $postBoxId . '" href="#">' . $first . '</a></div><div class="favorite-action"><a id="favorite-second-link-' . $postBoxId . '" href="#">' . $second . '</a></div></div></div>\');
			
					$(\'#favorite-inside-' . $postBoxId . '\').css("width", $(\'#favorite-actions-' . $postBoxId . '\').innerWidth() -4 + "px");
					$(\'#favorite-toggle-' . $postBoxId . '\').hover(function() {
						$(\'#favorite-first-' . $postBoxId . '\').addClass("slide-down");
						$(\'#favorite-inside-' . $postBoxId . '\').addClass("slideDown");
						$(\'#favorite-inside-' . $postBoxId . '\').slideDown(100);
					});
					$(\'#favorite-first-' . $postBoxId . '\').click(function() {
						$(\'#favorite-first-' . $postBoxId . '\').addClass("slide-down");
						$(\'#favorite-inside-' . $postBoxId . '\').addClass("slideDown");
						$(\'#favorite-inside-' . $postBoxId . '\').slideDown(100);
					});
					var timeoutFavoriteActions;
					$(\'#favorite-actions-' . $postBoxId . '\').hover(function() {
						clearTimeout(timeoutFavoriteActions);
					},function() {
						timeoutFavoriteActions = setTimeout 
						( 
							function() 
							{ 
								//document.getElementById(\'favorite-inside-' . $postBoxId . '\').style.display = "none";
								$(\'#favorite-inside-' . $postBoxId . '\').slideUp(100);
								setTimeout 
								( 
									function() 
									{ 
										$(\'#favorite-first-' . $postBoxId . '\').removeClass("slide-down");
										$(\'#favorite-inside-' . $postBoxId . '\').removeClass("slideDown");
									}, 
									100
								);
							}, 
							500 
						);
					});
					$(\'#favorite-first-link-' . $postBoxId . '\').click(function() {
						$(\'#rightEnlarging\').show();
						$(\'#equilize\').click();
						
						$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
							"table": "' . $table . '",
							"act": "add",
							"page": $(\'#page' . $postBoxId . 'Reference\').val(),
							"idPere": $(\'#identifiantActuelle' . $postBoxId . '\').val(),
							"partie": "right",
				"menu": $("#menu").val(),
							"affichage": "affichageTable",
							"partition": "tout"
						});
						$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
							"table": "' . $table . '",
							"act": "add",
							"page": $(\'#page' . $postBoxId . 'Reference\').val(),
							"idPere": $(\'#identifiantActuelle' . $postBoxId . '\').val(),
							"partie": "left",
				"menu": $("#menu").val(),
							"affichage": "affichageTable",
							"partition": "tout"
						});
						
						$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
						$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
						return false;
					});
					$(\'#favorite-second-link-' . $postBoxId . '\').click(function() {
						$(\'#rightEnlarging\').show();
						$(\'#equilize\').click();
						
						$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
							"table": "' . $sousTable . '",
							"act": "add",
							"page": $(\'#page' . $postBoxId . 'Reference\').val(),
							"idPere": $(\'#identifiantActuelle' . $postBoxId . '\').val(),
							"partie": "right",
				"menu": $("#menu").val(),
							"affichage": "affichageTable",
							"partition": "tout"
						});
						$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
							"table": "' . $sousTable . '",
							"act": "add",
							"page": $(\'#page' . $postBoxId . 'Reference\').val(),
							"idPere": $(\'#identifiantActuelle' . $postBoxId . '\').val(),
							"partie": "left",
				"menu": $("#menu").val(),
							"affichage": "affichageTable",
							"partition": "tout"
						});
						
						$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
						$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
						return false;
					});
					
			
					$("#favorite-actions-' . $postBoxId . '").after(\'<div id="' . $postBoxId . 'Pere" title="' . __("Cliquer pour remonter d\'un niveau.", "evarisk") . '" class="flechePere"><img alt="pere" src="' . PICTO_FLECHE_PERE . '" title=""/></div>\');
					if($("#filAriane a:last").attr("id") == $(\'#filAriane :first-child\').attr("id"))
					{
						$(\'#' . $postBoxId . 'Pere\').hide();
					}
					else
					{
						$(\'#' . $postBoxId . 'Pere\').show();
					}
					$(\'#' . $postBoxId . 'Pere\').unbind("click");
					$(\'#' . $postBoxId . 'Pere\').click(function() {
						if($(\'#filAriane :last-child\').attr("id") != $(\'#filAriane :first-child\').attr("id"))
						{
							$(\'#page' . $postBoxId . 'Reference\').val(1);
							if($(\'#filAriane :last-child\').is("label"))
							{
								$(\'#filAriane :last-child\').remove();
							}
							$(\'#filAriane :last-child\').remove();
							$(\'#filAriane :last-child\').remove();
							$(\'#' . $postBoxId . ' h3 span\').html($(\'#filAriane :last-child\').html());
							var id = $(\'#filAriane :last-child\').attr("id");
							var reg = new  RegExp("(element)", "g");
							var id = id.replace(reg, "");
							$(\'#identifiantActuelle' . $postBoxId . '\').val(id);
							$(\'#rightEnlarging\').hide();
							$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
								"table": "' . $table . '",
								"act": "changementPage",
								"page": $(\'#page' . $postBoxId . 'Reference\').val(),
								"idPere": $(\'#identifiantActuelle' . $postBoxId . '\').val(),
								"partie": "right",
				"menu": $("#menu").val(),
								"affichage": "affichageTable",
								"partition": "main"
							});
							$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
								"table": "' . $table . '",
								"act": "changementPage",
								"page": $(\'#page' . $postBoxId . 'Reference\').val(),
								"idPere": $(\'#identifiantActuelle' . $postBoxId . '\').val(),
								"partie": "left",
				"menu": $("#menu").val(),
								"affichage": "affichageTable",
								"partition": "main"
							});
							$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
							$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
							return false;
						}
					});
				});
			</script>';
	}
	else
	{
		$racine = Arborescence::getRacine($table);
		$nomTable = "mainTable";
		switch($table)
		{
			case TABLE_GROUPEMENT:
				$enTeteTable = __("Groupements", 'evarisk');
				break;
			case TABLE_UNITE_TRAVAIL:
				$table = TABLE_GROUPEMENT;
				$enTeteTable = __("Groupements", 'evarisk');
				break;
			case TABLE_CATEGORIE_DANGER:
				$enTeteTable = __("Cat&eacute;gories", 'evarisk');
				break;
			case TABLE_DANGER:
				$table = TABLE_CATEGORIE_DANGER;
				$enTeteTable = __("Cat&eacute;gories", 'evarisk');
				break;
			case TABLE_TACHE:
				$enTeteTable = __("Actions correctives", 'evarisk');
				break;
			case TABLE_ACTIVITE:
				$table = TABLE_TACHE;
				$enTeteTable = __("Actions correctives", 'evarisk');
				break;
		}
		$scriptAfterEvaluationRisques = '<script type="text/javascript">
			$(document).ready(function() {
				$(\'#' . $postBoxId . ' .inside\').each(function(){$(this).addClass("noPadding");});
				$(\'#filAriane\').hide();';
		if(count($arguments['expanded']) > 0)
			foreach($arguments['expanded'] as $expanded)
			{
				$scriptAfterEvaluationRisques= $scriptAfterEvaluationRisques . '
					$("#' . $expanded . ' span").click();';
			}
		$scriptAfterEvaluationRisques= $scriptAfterEvaluationRisques . '
			});
		</script>';
		$mainPostBoxBody = EvaDisplayDesign::getTableArborescence($racine, $table, $nomTable, $enTeteTable);
	}
	if(isset($scriptEvaluationRisques))
		echo $scriptEvaluationRisques;
	echo $mainPostBoxBody;
	if(isset($scriptAfterEvaluationRisques))
		echo $scriptAfterEvaluationRisques;
}
?>