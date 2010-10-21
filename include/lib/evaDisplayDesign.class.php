<?php
/**
 * This class contains the methods allowing to dipslay a classical screen of the Evarisk pluggin
 * @author Evarisk
 * @version v5.0
 */
require_once(EVA_CONFIG );
require_once(EVA_LIB_PLUGIN_DIR . 'arborescence.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/groupement/eva_groupement.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/uniteDeTravail/uniteDeTravail.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'danger/categorieDangers/categorieDangers.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'danger/danger/evaDanger.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'veilleReglementaire/evaQuestion.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'veilleReglementaire/evaGroupeQuestion.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/tache/evaTask.class.php' );

class EvaDisplayDesign {
	/**
	 * Returns the header display of a classical HTML page.
	 * @see afficherFinPage
	 * @param string $titrePage Title of the page.
	 * @param string $icone Path of the icon.
	 * @param string $titreIcone Title attribute of the icon.
	 * @param string $altIcon Alt attribute of the icon.
	 * @param string $table Table where the page is link.
	 * @param bool $boutonAjouter Must the page have a button "Add" next to the title ?
	 * @param string $messageInfo The information message.
	 * @param bool $choixAffichage Must the page offer a choice of display ?
	 * @return string HTML code of the header display.
	 */
	static function afficherDebutPage($titrePage, $icone, $titreIcone, $altIcon, $table, $boutonAjouter=true, $messageInfo='', $choixAffichage=false)
	{
		$debutPage = '<div class="wrap">
			<div class="icon32"><img alt="' . $altIcon . '" src="' . $icone . '"title="' . $titreIcone . '"/></div>
			<h2>' . $titrePage;
		if($boutonAjouter)
		{
			$debutPage = $debutPage . ' <a class="button add-new-h2" onclick="javascript:document.getElementById(\'act\').value=\'add\'; document.forms.form.submit();">' . __('Ajouter', 'evarisk') . '</a>';
		}
		$debutPage = $debutPage . '</h2>';
		$debutPage = $debutPage . '<div id="champsCaches" class=""></div>
			<script type="text/javascript">
				$(document).ready(function() {
					setTimeout 
					( 
						function() 
						{ 
							$("#message").hide();
						}, 
						10000
					);
				});
			</script>';
		if($choixAffichage)
		{
			$racine = Arborescence::getRacine($table);
			$idPere = $racine->id;
			$debutPage = $debutPage . '<script type="text/javascript">
				$(document).ready(function() {
					$(\'#affichageTable\').click(function() {
						$(\'#affichageListe\').removeClass(\'selectedAffichage\');
						$(\'#affichageTable\').addClass(\'selectedAffichage\');
						$(\'#identifiantActuellemainPostBox\').val(1);
						$(\'#pagemainPostBoxReference\').val(1);
						
						while($(\'#filAriane :last-child\').attr("id") != "element1")
						{
							$(\'#filAriane :last-child\').remove();
						}
						
						$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
							"table": "' . $table . '",
							"act": "changementPage",
							"page": $(\'#pagemainPostBoxReference\').val(),
							"idPere": $(\'#identifiantActuellemainPostBox\').val(),
							"partie": "right",
"menu": $("#menu").val(),
							"affichage": "affichageTable",
							"partition": "main"
						});
						$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
							"table": "' . $table . '",
							"act": "changementPage",
							"page": $(\'#pagemainPostBoxReference\').val(),
							"idPere": $(\'#identifiantActuellemainPostBox\').val(),
							"partie": "left",
"menu": $("#menu").val(),
							"affichage": "affichageTable",
							"partition": "main"
						});
				
						$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
						$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
					});
					$(\'#affichageListe\').click(function() {
						$(\'#affichageTable\').removeClass(\'selectedAffichage\');
						$(\'#affichageListe\').addClass(\'selectedAffichage\');
						$(\'#identifiantActuellemainPostBox\').val(1);
						$(\'#pagemainPostBoxReference\').val(1);
						
						$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
							"table": "' . $table . '",
							"act": "changementPage",
							"page": $(\'#pagemainPostBoxReference\').val(),
							"idPere": $(\'#identifiantActuellemainPostBox\').val(),
							"partie": "right",
"menu": $("#menu").val(),
							"affichage": "affichageListe",
							"partition": "main"
						});
						$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
							"table": "' . $table . '",
							"act": "changementPage",
							"page": $(\'#pagemainPostBoxReference\').val(),
							"idPere": $(\'#identifiantActuellemainPostBox\').val(),
							"partie": "left",
"menu": $("#menu").val(),
							"affichage": "affichageListe",
							"partition": "main"
						});
				
						$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
						$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
					});
				});
			</script>';
			$debutPage = $debutPage . '
		<!--	<div id="choixAffichage">
			<span class="textAffichage">' . __('Affichage', 'evarisk') . '</span> : 
<a id="affichageTable" onclick="return false;"><img alt="' . __('Affichage en grille' , 'evarisk') . '" src="' . PICTO_GRILLE . '" title="' . __('Affichage en grille' , 'evarisk') . '"/> ' . __('Grille' , 'evarisk') . '</a> -
<a id="affichageListe" onclick="return false;"><img alt="' . __('Affichage en liste' , 'evarisk') . '" src="' . PICTO_LISTE . '" title="' . __('Affichage en liste' , 'evarisk') . '"/> ' . __('Liste' , 'evarisk') . '</a>
			</div> -->
			<div id="choseEnlarging" class="choseEnlarging" style="text-align: center">
				<span style="" id="rightEnlarging" class="rightEnlarging"></span>
				<div id="enlarging" class="enlarging"></div>
				<span style="display:none;" id="equilize" class="enlarging"></span>
				<span style="" id="leftEnlarging" class="leftEnlarging"></span>
			</div>';
		}
		$debutPage = $debutPage . '<div id="message" class="fade below-h2">' . $messageInfo . '</div>';
		return $debutPage;
	}

	/**
	 * Closes the "div" tag open in the header display  of a classical HTML page.
	 * @see afficherDebutPage
	 * @return  the closure.
	 */
	static function afficherFinPage()
	{
		return '
				<div class="clear" id="ajax-response"></div>
				<div class="clear"></div>
			</div>';
	}

	/**
	 *  Returns the HTML code for the site wire.
	 * @param string $titreFilAriane Displayed name for the first site wire element
	 * @param string $idsFilAriane Identifiers of the elements to add to the site wire
	 * @param string $table Identifiers of the elements to add to the site wire
	 * @param string $idPostBox Identifiers of the postbox whitch the wire is link
	 * @return string HTML code for the site wire.
	 */
	static function getFilAriane($affichage, $titreFilAriane, $table, $idPostBox, $idsFilAriane='')
	{
		$display = '';
		if($affichage == 'affichageListe')
		{
			$display = 'style="display: none"';
		}
		$filAriane = '<div ' . $display . 'id="filAriane"><a href=# id="element1">' . $titreFilAriane . '</a></div>';
		$script = '<script type="text/javascript">
				$(document).ready(function() {
					$(\'#element1\').click(function() {
						$(\'#equilize\').click();
												$(\'#identifiantActuelle' . $idPostBox . '\').val(1);
						$(\'#page' . $idPostBox . 'Reference\').val(1);
						while($(\'#filAriane :last-child\').attr("id") != "element1")
						{
							$(\'#filAriane :last-child\').remove();
						}
						
						$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
							"table": "' . $table . '",
							"act": "changementPage",
							"page": $(\'#page' . $idPostBox . 'Reference\').val(),
							"idPere": $(\'#identifiantActuelle' . $idPostBox . '\').val(),
							"partie": "right",
							"menu": $("#menu").val(),
							"affichage": "affichageTable",
							"partition": "main"
						});
						$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
							"table": "' . $table . '",
							"act": "changementPage",
							"page": $(\'#page' . $idPostBox . 'Reference\').val(),
							"idPere": $(\'#identifiantActuelle' . $idPostBox . '\').val(),
							"partie": "left",
							"menu": $("#menu").val(),
							"affichage": "affichageTable",
							"partition": "main"
						});
				
						$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
						$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
						return false;
					});
				});
			</script>';
			if($idsFilAriane != null)
			{
				$element = '';
				if($idsFilAriane != '-1')
				{
					foreach($idsFilAriane as $idFilAriane)
					{
						switch($table)
						{
							case TABLE_GROUPEMENT:
								$element = EvaGroupement::getGroupement($idFilAriane);
								break;
							case TABLE_CATEGORIE_DANGER:
								$element = categorieDangers::getCategorieDanger($idFilAriane);
								break;
						}
						$script = $script .'<script type="text/javascript">
		$(document).ready(function() {
			$(\'#identifiantActuelle' . $idPostBox . '\').val(' . $element->id . ');
			$(\'#page' . $idPostBox . 'Reference\').val(1);
			$(\'#' . $idPostBox . ' h3 span\').html("' . addslashes($element->nom) . '");
			
			$(\'#filAriane :last-child\').after(\'<label>&nbsp;&raquo;&nbsp;&Eacute;dition&nbsp;de&nbsp;' . addslashes($element->nom) . '</label>\');
			$(\'#element' . $element->id . '\').click(function() {
				$(\'#identifiantActuelle' . $idPostBox . '\').val("' . $element->id . '");
				$(\'#page' . $idPostBox . 'Reference\').val(1);
				while($(\'#filAriane :last-child\').attr("id") != "element' . $element->id . '")
				{
					$(\'#filAriane :last-child\').remove();
				}					
				
				$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
					"table": "' . $table . '",
					"act": "changementPage",
					"page": $(\'#page' . $idPostBox . 'Reference\').val(),
					"idPere": $(\'#identifiantActuelle' . $idPostBox . '\').val(),
					"partie": "right",
					"menu": $("#menu").val(),
					"affichage": "affichageTable",
					"partition": "main"
				});
				$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
					"table": "' . $table . '",
					"act": "changementPage",
					"page": $(\'#page' . $idPostBox . 'Reference\').val(),
					"idPere": $(\'#identifiantActuelle' . $idPostBox . '\').val(),
					"partie": "left",
					"menu": $("#menu").val(),
					"affichage": "affichageTable",
					"partition": "main"
				});
				
				$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
				$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
				return false;
			});
		});
	</script>';
					}
				}
				$script = $script . '
<script type="text/javascript">
	$(document).ready(function() {
		$(\'#element' . $element->id . '\').click();
	});
</script>';
			}
		return $script . $filAriane;
	}

	/**
	 * Open a "div" with the metabox-holder class.
	 * @see fermerMetaboxHolder
	 * @return  the "div".
	 */
	static function ouvrirMetaboxHolder()
	{
		$metaboxHolder = '
				<div class="metabox-holder">';
		return $metaboxHolder;
	}

	/**
	 * Closes the "div" open by the ouvrirMetaboxHolder function.
	 * @see ouvrirMetaboxHolder
	 * @return  the closure.
	 */
	static function fermerMetaboxHolder()
	{
		$metaboxHolder = '
				</div>';
		return $metaboxHolder;
	}

	/**
	 * Returns the HTML code of a splited in two screen .
	 * @param string $partieGauche The left body part.
	 * @param string $partieDroite The right body part.
	 * @param int $largeurGauche The left body percentage width.
	 * @param string $numero A string to add to the id of the element of splited screen.
	 * @return string HTML code of the splited screen.
	 */
	static function splitEcran($partieGauche, $partieDroite, $largeurGauche = LARGEUR_GAUCHE, $numero = '')
	{
		$splitEcran = '
			<script type="text/javascript">
				$(document).ready(function() {
					$(\'#leftEnlarging' . $numero . '\').click(function() {
						$(\'#partieEdition' . $numero . '\').hide();
						$(\'#partieGauche' . $numero . '\').show();
						$(\'#partieGauche' . $numero . '\').css(\'width\', \'98%\');
						adminMenu.fold();
						$("#enlarging' . $numero . ' .ui-slider-range").css("width","100%");
						$("#enlarging' . $numero . ' .ui-slider-handle").css("left","100%");
					});
					$(\'#rightEnlarging' . $numero . '\').click(function() {
						$(\'#partieGauche' . $numero . '\').hide();
						$(\'#partieEdition' . $numero . '\').show();
						$(\'#partieEdition' . $numero . '\').css(\'width\', \'98%\');
						adminMenu.fold();
						$("#enlarging' . $numero . ' .ui-slider-range").css("width","0%");
						$("#enlarging' . $numero . ' .ui-slider-handle").css("left","0%");
					});
					$(\'#equilize' . $numero . '\').click(function() {
						$(\'#partieGauche' . $numero . '\').show();
						$(\'#partieEdition' . $numero . '\').show();
						$(\'#partieEdition' . $numero . '\').css(\'width\', \'49%\');
						$(\'#partieGauche' . $numero . '\').css(\'width\', \'49%\');
						$("#enlarging' . $numero . ' .ui-slider-range").css("width","50%");
						$("#enlarging' . $numero . ' .ui-slider-handle").css("left","50%");
					});
				});
			</script>';
						
						
		$script = '<script type="text/javascript">
				$(document).ready(function() {
					$("#enlarging' . $numero . ' .ui-slider-horizontal").css("width","100px");
					$("#enlarging' . $numero . '").slider({
						range: "min",
						value: 50,
						min: 25,
						max:  75,
						slide: function(event, ui) {
							var largeurGauche = ui.value - 1;
							var largeurDroite = 98 - largeurGauche;
							if(largeurGauche == 24 || largeurDroite == 24)
							{
								adminMenu.fold();
							}
							$(\'#partieEdition' . $numero . '\').show();
							$(\'#partieGauche' . $numero . '\').show();
							$("#partieGauche' . $numero . '").css("width", largeurGauche  + "%");
							$("#partieEdition' . $numero . '").css("width", largeurDroite  + "%");
						}
					});
				});
			</script>';
						
						
		$splitEcran = $script . $splitEcran . '		<div id="partieGauche' . $numero . '" style="width:' . $largeurGauche . '%;" class="postbox-container">';
		$splitEcran = $splitEcran . $partieGauche;
		$splitEcran = $splitEcran .'		
					</div>';
		$splitEcran = $splitEcran .'		<div id="partieEdition' . $numero . '" style="width:' . (98 - $largeurGauche) . '%;" class="alignleft partieDroite postbox-container">
						' . $partieDroite . '
					</div>';
		return $splitEcran;
	}

	/**
	 * Returns the HTML code of all postboxes of the array.
	 * @see ajouterPostbox
	 * @param array $postBoxes Array of all postboxes to display.
	 * @return string HTML code the postboxes.
	 */
	static function displayPostBoxes($postBoxes, $idDiv='')
	{
		$display = '
						<div id="' . $idDiv . '" class="">';
		foreach($postBoxes as $postBox)
		{
			$id = (isset($postBox['id']))?$postBox['id']:'';
			$titre = (isset($postBox['titre']))?$postBox['titre']:'titre';
			$corps = (isset($postBox['corps']))?$postBox['corps']:'';
			$pagination = (isset($postBox['pagination']))?$postBox['pagination']:false;
			$table = (isset($postBox['table']))?$postBox['table']:'';
			$display = $display . EvaDisplayDesign::ajouterPostbox($id,$titre,$corps,$pagination,$table);
		}
		if($postBox['pagination'])
		{
			$display = $display . '
			<script type="text/javascript">
				$(document).ready(function() {
				});
			</script';
		}
		$display = $display .'			
						</div>';
		return $display;
	}

	/**
	 * Returns the HTML code of a postbox with the scripts for.
	 * If the postbox need a paging system, it is link to the site wire
	 * For the good working of the page, maximum one postbox per page should have a paging system
	 * @param string $idPostBox Id attribute for the postbox.
	 * @param string $titrePostBox Title to be displayed in the postbox.
	 * @param string $corpsPostBox Postbox body HTML code.
	 * @param bool $pagination Need the postbox a paging system ?
	 * @param string $table Postbox-relative table name.
	 * @return string HTML code the postboxes.
	 */
	static function ajouterPostbox($idPostBox, $titrePostBox, $corpsPostBox, $pagination, $table)
	{
		switch($table)
		{
			case TABLE_GROUPEMENT:
				$table2 = TABLE_UNITE_TRAVAIL;
				break;
			case TABLE_CATEGORIE_DANGER:
				$table2 = TABLE_DANGER;
				break;
			default:
				$table2 = '';
				break;
		}
		$scriptPostBox = '<script type="text/javascript">
	$(document).ready(function() {
		$(\'#' . $idPostBox . 'Fleche\').click(function() {
			$(\'#' . $idPostBox . '\').toggleClass(\'closed\');
		});
		if(document.getElementById(\'filAriane\').lastChild == document.getElementById(\'filAriane\').firstChild)
		{
			$(\'#' . $idPostBox . 'Pere\').addClass(\'hidden\');
		}
		else
		{
			$(\'#' . $idPostBox . 'Pere\').removeClass(\'hidden\');
		}
		$(\'#' . $idPostBox . 'Pere\').click(function() {
			if($(\'#filAriane :last-child\') != $(\'#filAriane :first-child\'))
			{
				$(\'#page' . $idPostBox . 'Reference\').val(1);
				if($(\'#filAriane :last-child\').is("label"))
				{
					$(\'#filAriane :last-child\').remove();
				}
				$(\'#filAriane :last-child\').remove();
				$(\'#filAriane :last-child\').remove();
				$(\'#' . $idPostBox . ' h3 span\').html($(\'#filAriane :last-child\').html());
				var id = $(\'#filAriane :last-child\').attr("id");
				var reg = new  RegExp("(element)", "g");
				var id = id.replace(reg, "");
				$(\'#identifiantActuelle' . $idPostBox . '\').val(id);
								
				$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
					"table": "' . $table . '",
					"act": "changementPage",
					"page": $(\'#page' . $idPostBox . 'Reference\').val(),
					"idPere": $(\'#identifiantActuelle' . $idPostBox . '\').val(),
					"partie": "right",
					"menu": $("#menu").val(),
					"affichage": "affichageTable",
					"partition": "main"
				});
				$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
					"table": "' . $table . '",
					"act": "changementPage",
					"page": $(\'#page' . $idPostBox . 'Reference\').val(),
					"idPere": $(\'#identifiantActuelle' . $idPostBox . '\').val(),
					"partie": "left",
					"menu": $("#menu").val(),
					"affichage": "affichageTable",
					"partition": "main"
				});
				
				$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
				$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
				return false;
			}
		});
		if(document.getElementById(\'favorite-inside-' . $idPostBox . '\') != null)
		{
			document.getElementById(\'favorite-inside-' . $idPostBox . '\').style.width = $(\'#favorite-actions-' . $idPostBox . '\').innerWidth() -4 + "px";
		}
		$(\'#favorite-toggle-' . $idPostBox . '\').hover(function() {
			$(\'#favorite-first-' . $idPostBox . '\').addClass("slide-down");
			$(\'#favorite-inside-' . $idPostBox . '\').addClass("slideDown");
			$(\'#favorite-inside-' . $idPostBox . '\').slideDown(100);
		});
		$(\'#favorite-first-' . $idPostBox . '\').click(function() {
			$(\'#favorite-first-' . $idPostBox . '\').addClass("slide-down");
			$(\'#favorite-inside-' . $idPostBox . '\').addClass("slideDown");
			$(\'#favorite-inside-' . $idPostBox . '\').slideDown(100);
		});
		var timeoutFavoriteActions;
		$(\'#favorite-actions-' . $idPostBox . '\').hover(function() {
			clearTimeout(timeoutFavoriteActions);
		},function() {
			timeoutFavoriteActions = setTimeout 
			( 
				function() 
				{ 
					//document.getElementById(\'favorite-inside-' . $idPostBox . '\').style.display = "none";
					$(\'#favorite-inside-' . $idPostBox . '\').slideUp(100);
					setTimeout 
					( 
						function() 
						{ 
							$(\'#favorite-first-' . $idPostBox . '\').removeClass("slide-down");
							$(\'#favorite-inside-' . $idPostBox . '\').removeClass("slideDown");
						}, 
						100
					);
				}, 
				500 
			);
		});
		
		$(\'#favorite-first-link-' . $idPostBox . '\').click(function() {
			$(\'#rightEnlarging\').show();
			$(\'#equilize\').click();
			
			$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
				"table": "' . $table . '",
				"act": "add",
				"page": $(\'#page' . $idPostBox . 'Reference\').val(),
				"idPere": $(\'#identifiantActuelle' . $idPostBox . '\').val(),
				"partie": "right",
				"menu": $("#menu").val(),
				"affichage": "affichageTable",
				"partition": "tout"
			});
			$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
				"table": "' . $table . '",
				"act": "add",
				"page": $(\'#page' . $idPostBox . 'Reference\').val(),
				"idPere": $(\'#identifiantActuelle' . $idPostBox . '\').val(),
				"partie": "left",
				"menu": $("#menu").val(),
				"affichage": "affichageTable",
				"partition": "tout"
			});
			
			$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
			$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
			return false;
		});
		
		$(\'#favorite-second-link-' . $idPostBox . '\').click(function() {
			$(\'#rightEnlarging\').show();
			$(\'#equilize\').click();
			
			$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
				"table": "' . $table2 . '",
				"act": "add",
				"page": $(\'#page' . $idPostBox . 'Reference\').val(),
				"idPere": $(\'#identifiantActuelle' . $idPostBox . '\').val(),
				"partie": "right",
				"menu": $("#menu").val(),
				"affichage": "affichageTable",
				"partition": "tout"
			});
			$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
				"table": "' . $table2 . '",
				"act": "add",
				"page": $(\'#page' . $idPostBox . 'Reference\').val(),
				"idPere": $(\'#identifiantActuelle' . $idPostBox . '\').val(),
				"partie": "left",
				"menu": $("#menu").val(),
				"affichage": "affichageTable",
				"partition": "tout"
			});
			
			$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
			$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
			return false;
		});
	});
</script>';
		$postBox = '<div class="postbox " id="' . $idPostBox . '">' . $scriptPostBox . '
						<div id="' . $idPostBox . 'Fleche" title="Cliquer pour inverser." class="handlediv"></div>';
		if($pagination)
		{
			$postBox = $postBox . '
						<div id="' . $idPostBox . 'Pere" title="' . __('Cliquer pour remonter d\'un niveau.', 'evarisk') . '" class="flechePere"><img alt="pere" src="' . PICTO_FLECHE_PERE . '" title="' . $titreIcone . '"/></div>';
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
			$postBox = $postBox . '
						<div id="favorite-actions-' . $idPostBox . '" class="alignright favorite-actions">
							<div id="favorite-toggle-' . $idPostBox . '" class="favorite-toggle alignright"></div>
							<div id="favorite-first-' . $idPostBox . '" class="favorite-first"><a href="#" onclick="return false;">' . __('Ajouter', 'evarisk') . '...</a>
							</div>
							<div id="favorite-inside-' . $idPostBox . '" style="display: none;" class="favorite-inside">
								<div class="favorite-action"><a id="favorite-first-link-' . $idPostBox . '" href="#">' . $first . '</a></div>
								<div class="favorite-action"><a id="favorite-second-link-' . $idPostBox . '" href="#">' . $second . '</a></div>
							</div>
						</div>';
		}
		$postBox = $postBox . '
						<h3 class="hndle"><span>' . $titrePostBox . '</span></h3>
						<div class="inside" style="">' . $corpsPostBox . '</div>
					</div>';
		return $postBox;
	}

	/**
	 * Returns the HTML code for the paging system with the scripts for.
	 * @param array $id Identifier of the post witch is link the paging system.
	 * @param string $pageMax Paging system maximum page.
	 * @param string $table Paging system-relative table name.
	 * @return string HTML code of the table.
	 */
	static function afficherPagination($id, $pageMax, $table)
	{	
		$pagination = '
<script type="text/javascript">
	$(document).ready(function() {
		$(\'#element1\').unbind("click");
		$(\'#element1\').click(function() {
			$(\'#identifiantActuelle' . $id . '\').val(1);
			$(\'#page' . $id . 'Reference\').val(1);
			while($(\'#filAriane :last-child\').attr("id") != "element1")
			{
				$(\'#filAriane :last-child\').remove();
			}
			
			$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
				"table": "' . $table . '",
				"act": "changementPage",
				"page": $(\'#page' . $id . 'Reference\').val(),
				"idPere": $(\'#identifiantActuelle' . $id . '\').val(),
				"partie": "right",
				"menu": $("#menu").val(),
				"affichage": "affichageTable",
				"partition": "main"
			});
			$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
				"table": "' . $table . '",
				"act": "changementPage",
				"page": $(\'#page' . $id . 'Reference\').val(),
				"idPere": $(\'#identifiantActuelle' . $id . '\').val(),
				"partie": "left",
				"menu": $("#menu").val(),
				"affichage": "affichageTable",
				"partition": "main"
			});
			
			$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
			$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
			$(\'#' . $id . ' h3 span\').html($(\'#filAriane :first-child\').html());
			return false;
		});
	});
	$(document).ready(function() {	
		$(\'#page' . $id . '\').keypress(function(event) {
			if (event.which && (event.which < 48 || event.which >57) && event.which != 8 && event.which != 13) {
				event.preventDefault();
			}
		});
		$(\'#page' . $id . '\').keyup(function() {
			$(\'#page' . $id . 'Reference\').val($(\'#page' . $id . '\').val())
		});
		
		var page = $(\'#page' . $id . 'Reference\').val();
		$(\'#page' . $id . '\').val(page);
		$(\'#formPagination' . $id . '\').click(function(event) {
			if($(event.target).is(\'.button\'))
			{
				if($("#filAriane :last-child").is("label"))
					$("#filAriane :last-child").remove();
				switch((event.target).id)
				{
					case "first' . $id . '":
						page = 1;
						break;
					case "previous' . $id . '":
						page = parseInt($(\'#page' . $id . 'Reference\').val()) - 1;
						if(page < 1)
						{
							page = 1;	
						}
						break;
					case "next' . $id . '":
						page = parseInt($(\'#page' . $id . 'Reference\').val()) + 1;
						if(page > $(\'#pageMax' . $id . '\').value)
						{
							page = $(\'#pageMax' . $id . '\').val();	
						}
						break;
					case "last' . $id . '":
						page = parseInt($(\'#pageMax' . $id . '\').val());
						break;
					case "text' . $id . '":
						if(parseInt($(\'#page' . $id . 'Reference\').val()) > parseInt($(\'#pageMax' . $id . '\').val()))
						{
							page = parseInt($(\'#pageMax' . $id . '\').val());
						}
						else
						{
							if(parseInt($(\'#page' . $id . 'Reference\').val()) < 1)
							{
								page = 1;	
							}
							else
							{
								if($(\'#page' . $id . 'Reference\').val() != "")
								{
									page = parseInt($(\'#page' . $id . 'Reference\').val());
								}
							}
						}
						break;
				}
				$(\'#page' . $id . 'Reference\').val(page);
				
				$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
					"table": "' . $table . '",
					"act": "changementPage",
					"page": $(\'#page' . $id . 'Reference\').val(),
					"idPere": $(\'#identifiantActuelle' . $id . '\').val(),
					"partie": "right",
					"menu": $("#menu").val(),
					"affichage": "affichageTable",
					"partition": "main"
				});
				$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
					"table": "' . $table . '",
					"act": "changementPage",
					"page": $(\'#page' . $id . 'Reference\').val(),
					"idPere": $(\'#identifiantActuelle' . $id . '\').val(),
					"partie": "left",
					"menu": $("#menu").val(),
					"affichage": "affichageTable",
					"partition": "main"
				});
				
				$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
				$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
				$(\'#page' . $id . '\').val(page);
				return false;
			}
		});
	});
</script>
		<form method="POST" id="formPagination' . $id . '" name="formPagination' . $id . '">
			<input type="submit" onclick="return false;" id="text' . $id . '" class="button alignright hidden" />
			<input type="submit" onclick="return false;" id="last' . $id . '" value=">>" class="button alignright" />
			<input type="submit" onclick="return false;" id="next' . $id . '" value=">" class="button alignright" />
			<input type="text" value=' . $pageMax . ' name="pageMax' . $id . '" id="pageMax' . $id . '" class="alignright textPagination" readonly="readonly"/>
			<span class="alignright">/</span>
			<input type="text" value=1 name="page' . $id . '" id="page' . $id . '" class="alignright textPagination" />
			<input type="submit" onclick="return false;" id="previous' . $id . '" value="<" class="button alignright" />
			<input type="submit" onclick="return false;" id="first' . $id . '" value="<<" class="button alignright" />
		<form>
		<div class="clear"></div><br />';
		return $pagination;
	}

	/**
	 * Returns the list view tree table with scripts that allow you to display the right part by clicking on the elements and the drag and drop.
	 * @see getCorpsTableArborescence
	 * @param Element_of_a_tree $racine Root element of the table.
	 * @param string $table Table name.
	 * @param int $idTable HTML Id attribute for the table.
	 * @param string $nomRacine Text to be displayed in the root of the table.
	 * @return string HTML code of the table.
	 */
	static function getTableArborescence($racine, $table, $idTable, $nomRacine, $draggable = true, $outputAction = true)
	{
		$elements = '';
		$monCorpsTable = '';
		$class = '';
		$infoRacine = '';
		switch($table)
		{
			case TABLE_GROUPEMENT:
				$elements = Arborescence::getFils($table, $racine, "nom ASC");
				$sousTable = TABLE_UNITE_TRAVAIL;
				$subElements = EvaGroupement::getUnitesDuGroupement($racine->id);
				$divDeChargement = 'message';
				$titreInfo = __("Niveau de risque", 'evarisk');
				$actionSize = 5;
				$actions = '
							<td class="noPadding"  id="addMain' . $racine->id . '"><img style="max-width: 100%;" src="' . PICTO_LTL_ADD_GROUPEMENT . '" alt="' . sprintf(__('Ajouter %s', 'evarisk'), __('un groupement', 'evarisk')) . '" title="' . sprintf(__('Ajouter %s', 'evarisk'), __('un groupement', 'evarisk')) . '" /></td>
							<td id="addMain' . $racine->id . 'Alt"></td>
							<td class="noPadding"  id="addSecondary' . $racine->id . '"><img style="max-width: 100%;" src="' . PICTO_LTL_ADD_UNIT . '" alt="' . sprintf(__('Ajouter %s', 'evarisk'), __('une unit&eacute; de travail', 'evarisk')) . '" title="' . sprintf(__('Ajouter %s', 'evarisk'), __('une unit&eacute; de travail', 'evarisk')) . '" /></td>
							<td id="addSecondary' . $racine->id . 'Alt"></td>';
				break;
			case TABLE_CATEGORIE_DANGER:
				$elements = Arborescence::getFils($table, $racine, "nom ASC");
				$sousTable = TABLE_DANGER;
				$subElements = categorieDangers::getDangersDeLaCategorie($racine->id);
				$divDeChargement = 'message';
				$titreInfo = null;
				$actionSize = 4;
				$actions = '
							<td class="noPadding"  id="addMain' . $racine->id . '"><img style="max-width: 100%;" src="' . PICTO_LTL_ADD_CATEGORIE_DANGER . '" alt="' . sprintf(__('Ajouter %s', 'evarisk'), __('une cat&eacute;gorie de dangers', 'evarisk')) . '" title="' . sprintf(__('Ajouter %s', 'evarisk'), __('une cat&eacute;gorie de dangers', 'evarisk')) . '" /></td>
							<td id="addMain' . $racine->id . 'Alt"></td>
							<td class="noPadding"  id="addSecondary' . $racine->id . '"><img style="max-width: 100%;" src="' . PICTO_LTL_ADD_DANGER . '" alt="' . sprintf(__('Ajouter %s', 'evarisk'), __('un danger', 'evarisk')) . '" title="' . sprintf(__('Ajouter %s', 'evarisk'), __('un danger', 'evarisk')) . '" /></td>
							<td id="addSecondary' . $racine->id . 'Alt"></td>';
				break;
			case TABLE_TACHE:
				$elements = Arborescence::getFils($table, $racine, "nom ASC");
				$sousTable = TABLE_ACTIVITE;
        $tacheRacine = new EvaTask($racine->id);
        $tacheRacine->load();
				$subElements = $tacheRacine->getWPDBActivitiesDependOn();
				$divDeChargement = 'message';
				$titreInfo = null;
				$actionSize = 3;
				$actions = '
							<td></td>
							<td class="noPadding"  id="addMain' . $racine->id . '"><img style="max-width: 100%;" src="' . PICTO_LTL_ADD_TACHE . '" alt="' . sprintf(__('Ajouter %s', 'evarisk'), __('une t&acirc;che', 'evarisk')) . '" title="' . sprintf(__('Ajouter %s', 'evarisk'), __('une t&acirc;che', 'evarisk')) . '" /></td>
							<td id="addMain' . $racine->id . 'Alt" style="display:none;"></td>
							<td class="noPadding"  id="addSecondary' . $racine->id . '"><img style="max-width: 100%;" src="' . PICTO_LTL_ADD_ACTIVITE . '" alt="' . sprintf(__('Ajouter %s', 'evarisk'), __('une action', 'evarisk')) . '" title="' . sprintf(__('Ajouter %s', 'evarisk'), __('une action', 'evarisk')) . '" /></td>
							<td id="addSecondary' . $racine->id . 'Alt" style="display:none;"></td>';
				break;
			case TABLE_GROUPE_QUESTION:
				$elements = Arborescence::getFils($table, $racine, "code ASC");
				$sousTable = TABLE_QUESTION;
				$subElements = EvaGroupeQuestions::getQuestionsDuGroupeQuestions($racine->id);
				$divDeChargement = 'ajax-response';
				$titreInfo = null;
				$actionSize = 3;
				$actions = '
							<td class="noPadding"  id="add-node-' . $racine->id . '"><img style="max-width: 100%;" src="' . PICTO_INSERT . '" alt="' . __('Inserer sous le titre', 'evarisk') . '" title="' . __('Inserer sous le titre', 'evarisk') . '" /></td>
							<td class="noPadding"   id="edit-node-' . $racine->id . '"><img style="display:none;" id="img_edit_racine" src="' . PICTO_EDIT . '" alt="' . __('Modifier le titre', 'evarisk') . '" title="' . __('Modifier le titre', 'evarisk') . '" /></td>
							<td></td>';
				break;
		}
		$trouveElement = count($elements);
		if($trouveElement)
		{
			$monCorpsTable = EvaDisplayDesign::getCorpsTableArborescence($elements, $racine, $table, $titreInfo, $idTable);
		}
		$monCorpsSubElements = '';
		foreach($subElements as $subElement)
		{
			switch($table)
			{
				case TABLE_GROUPEMENT:
					$subAffichage = $subElement->nom;
					break;
				case TABLE_CATEGORIE_DANGER:
					$subAffichage = $subElement->nom;
					break;
				case TABLE_TACHE:
					$subAffichage = $subElement->nom;
					break;
				case TABLE_GROUPE_QUESTION:
					$subAffichage = $subElement->enonce;
					break;
			}
			$info = '';
			if($titreInfo != null)
			{
				$info =  EvaDisplayDesign::getInfoArborescence($sousTable, $subElement->id);
				$info = '
					<td id ="info-' . $subElement->id . '" class="' . $info['class'] . '">' . $info['value'] . '</td>';
			}
		}

		{//treetable
		$tableArborescente = '
<script type="text/javascript">
	$(document).ready(function()  {
		$("#' . $idTable . '").treeTable();	

		// Make visible that a row is clicked
		$("table#' . $idTable . ' tbody tr").mousedown(function() {
		  $("tr.selected").removeClass("selected"); // Deselect currently selected rows
		  $(this).addClass("selected");
		});

		// Make sure row is selected when span is clicked
		$("table#' . $idTable . ' tbody tr span").mousedown(function() {
		  $($(this).parents("tr")[0]).trigger("mousedown");
		});

		var span = document.getElementById("tdRacine' . $idTable . '").firstChild;
		$("#' . $idTable . ' #node-' . $idTable . '-' . $racine->id . '").toggleBranch();
		document.getElementById("tdRacine' . $idTable . '").removeChild(span);

		$("#' . $idTable . ' tr.parent").each(function(){
			var childNodes = $("table#' . $idTable . ' tbody tr.child-of-" + $(this).attr("id"));
			if(childNodes.length > 0) {
				$(this).addClass("aFils");
				var premierFils = $("table#' . $idTable . ' tbody tr.child-of-" + $(this).attr("id") + ":first").attr("id");
				if(premierFils != premierFils.replace(/node/g,""))
				{
					$(this).addClass("aFilsNoeud");
					$("#' . $idTable . ' #addSecondary" + $(this).attr("id").replace(/node-' . $idTable . '-/g,"")).hide();
					$("#' . $idTable . ' #addSecondary" + $(this).attr("id").replace(/node-' . $idTable . '-/g,"") + "Alt").show();
					$("#' . $idTable . ' #addMain" + $(this).attr("id").replace(/node-' . $idTable . '-/g,"") + "Alt").hide();
					$("#' . $idTable . ' #addMain" + $(this).attr("id").replace(/node-' . $idTable . '-/g,"") + "").show();
				}
				else
				{
					$(this).addClass("aFilsFeuille");
					$("#' . $idTable . ' #addMain" + $(this).attr("id").replace(/node-' . $idTable . '-/g,"")).hide();
					$("#' . $idTable . ' #addMain" + $(this).attr("id").replace(/node-' . $idTable . '-/g,"") + "Alt").show();
					$("#' . $idTable . ' #addSecondary" + $(this).attr("id").replace(/node-' . $idTable . '-/g,"") + "Alt").hide();
					$("#' . $idTable . ' #addSecondary" + $(this).attr("id").replace(/node-' . $idTable . '-/g,"") + "").show();
				}
			}
			else
			{
				$(this).removeClass("aFils");
				$(this).addClass("sansFils");
			}
		});
		
		$("#' . $idTable . ' #addMain' . $racine->id . '").click(function(){
			var idPere = ' . $racine->id . ';
			$(\'#rightEnlarging\').show();
			$(\'#equilize\').click();
			
			var expanded = new Array();
			$(\'.expanded\').each(function(){expanded.push($(this).attr("id"));});
			
			$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
				"table": "' . $table . '",
				"act": "add",
				"page": $(\'#pagemainPostBoxReference\').val(),
				"idPere": idPere,
				"partie": "right",
"menu": $("#menu").val(),
				"affichage": "affichageListe",
				"partition": "tout",
				"expanded": expanded
			});
			$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
				"table": "' . $table . '",
				"act": "add",
				"page": $(\'#pagemainPostBoxReference\').val(),
				"idPere": idPere,
				"partie": "left",
"menu": $("#menu").val(),
				"affichage": "affichageListe",
				"partition": "tout",
				"expanded": expanded
			});
			
			$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
			$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
			return false;
		});
		
		$("#' . $idTable . ' #addSecondary' . $racine->id . '").click(function(){
			var idPere = ' . $racine->id . ';
			$(\'#rightEnlarging\').show();
			$(\'#equilize\').click();
			
			var expanded = new Array();
			$(\'.expanded\').each(function(){expanded.push($(this).attr("id"));});
			$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
				"table": "' . $sousTable . '",
				"act": "add",
				"page": $(\'#pagemainPostBoxReference\').val(),
				"idPere": idPere,
				"partie": "right",
"menu": $("#menu").val(),
				"affichage": "affichageListe",
				"partition": "tout",
				"expanded": expanded
			});
			$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
				"table": "' . $sousTable . '",
				"act": "add",
				"page": $(\'#pagemainPostBoxReference\').val(),
				"idPere": idPere,
				"partie": "left",
"menu": $("#menu").val(),
				"affichage": "affichageListe",
				"partition": "tout",
				"expanded": expanded
			});
			
			$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
			$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
			return false;
		});
		
		$("#' . $idTable . ' #add-node-' . $racine->id . '").click(function(){
			for (var i=0;i<document.regulatoryWatchForm.titrePere.options.length;i++) 
			{
				if (document.regulatoryWatchForm.titrePere.options[i].value == ' . $racine->id . ')
					document.regulatoryWatchForm.titrePere.options[i].selected = true;
			}
			$("#traiter").click();
		});
	});
</script> ';
		}

		if($draggable)
		{
		$tableArborescente .= '
<script type="text/javascript">
	$(document).ready(function(){
		var draggedObject;
		var draggedObjectFather;
	
		// Configure draggable nodes
		$("#' . $idTable . ' .noeudArbre, #' . $idTable . ' .feuilleArbre").draggable({
			start: function(event, ui) {
				draggedObject = event.target.id;
				var classNames = event.target.className.split(\' \');
				draggedObjectFather = "temp";
				for(key in classNames) {
					if(classNames[key].match("child-of-")) {
						draggedObjectFather = $("#" + classNames[key].substring(9));
						draggedObjectFather = draggedObjectFather.attr(\'id\');
					}
				}
			},
			helper: "clone",
			opacity: .75,
			refreshPositions: true,
			revert: "invalid",
			revertDuration: 300,
			scroll: true
		});
		
		dropFunction = function(event, ui)
		{ 
			// Call jQuery treeTable plugin to move the branch
			$($(ui.draggable)).appendBranchTo(this);
			var dropLocation = event.target.id;
			
			var adresse = "' . EVA_INC_PLUGIN_URL . 'ajax.php?nom=' . $table . '&location=' . $idTable . '&act=transfert&idElementSrc=" + draggedObject + "&idElementOrigine=" + draggedObjectFather + "&idElementDest=" + dropLocation;
			$(\'#equilize\').click();
			$(\'#' . $divDeChargement . '\').show();
			$(\'#' . $divDeChargement . '\').addClass("updated");
			$(\'#' . $divDeChargement . '\').load(adresse);
			$(\'#' . $divDeChargement . '\').html("Transfert en cours ...");
			setTimeout 
			( 
				function() 
				{
					$("#' . $idTable . ' tr.parent").each(function(){
						var childNodes = $("table#' . $idTable . ' tbody tr.child-of-" + $(this).attr("id"));
						if(childNodes.length > 0) {
							$(this).removeClass("sansFils");
							$(this).addClass("aFils");
							var premierFils = $("table#' . $idTable . ' tbody tr.child-of-" + $(this).attr("id") + ":first").attr("id");
							if(premierFils != premierFils.replace(/node-' . $idTable . '-/g,""))
							{
								$(this).addClass("aFilsNoeud");
								$(this).droppable( "option", "accept", \'.noeudArbre\' );
								$("#' . $idTable . ' #addSecondary" + $(this).attr("id").replace(/node-' . $idTable . '-/g,"")).hide();
								$("#' . $idTable . ' #addSecondary" + $(this).attr("id").replace(/node-' . $idTable . '-/g,"") + "Alt").show();
								$("#' . $idTable . ' #addMain" + $(this).attr("id").replace(/node-' . $idTable . '-/g,"") + "Alt").hide();
								$("#' . $idTable . ' #addMain" + $(this).attr("id").replace(/node-' . $idTable . '-/g,"") + "").show();
							}
							else
							{
								$(this).addClass("aFilsFeuille");
								$(this).droppable( "option", "accept", \'.feuilleArbre\' );
								$("#' . $idTable . ' #addMain" + $(this).attr("id").replace(/node-' . $idTable . '-/g,"")).hide();
								$("#' . $idTable . ' #addMain" + $(this).attr("id").replace(/node-' . $idTable . '-/g,"") + "Alt").show();
								$("#' . $idTable . ' #addSecondary" + $(this).attr("id").replace(/node-' . $idTable . '-/g,"") + "Alt").hide();
								$("#' . $idTable . ' #addSecondary" + $(this).attr("id").replace(/node-' . $idTable . '-/g,"") + "").show();
							}
						}
						else
						{
							$(this).removeClass("aFilsNoeud");
							$(this).removeClass("aFilsFeuille");
							$(this).removeClass("aFils");
							$(this).addClass("sansFils");
							$(this).droppable( "option", "accept", \'.noeudArbre, .feuilleArbre\' );
							$("#' . $idTable . ' #addSecondary" + $(this).attr("id").replace(/node-' . $idTable . '-/g,"") + "Alt").hide();
							$("#' . $idTable . ' #addMain" + $(this).attr("id").replace(/node-' . $idTable . '-/g,"") + "Alt").hide();
							$("#' . $idTable . ' #addSecondary" + $(this).attr("id").replace(/node-' . $idTable . '-/g,"")).show();
							$("#' . $idTable . ' #addMain" + $(this).attr("id").replace(/node-' . $idTable . '-/g,"")).show();
						}
					});
					$(document).ajaxStop(function(){
						$(\'#' . $divDeChargement . '\').removeClass("updated");
					});
				}, 
				10 
			);
		}
		
		overFunction = function(event, ui)
		{ 
			// Make the droppable branch expand when a draggable node is moved over it.
			if(this.id != $(ui.draggable.parents("tr")[0]).id && !$(this).is(".expanded")) {
			var overObject = $(this);
			setTimeout 
			( 
				function() 
				{ 
					if(overObject.is(".accept"))
					{
						overObject.expand();
					}
				}, 
				500 
			);
		  }
		}
		
		$("#' . $idTable . ' .aFilsNoeud, #' . $idTable . ' .racineArbre").droppable({
			accept: "#' . $idTable . ' .noeudArbre",
			drop: dropFunction,
			hoverClass: "accept",
			over: overFunction			  
		});

		$("#' . $idTable . ' .aFilsFeuille").droppable({
			accept: "#' . $idTable . ' .feuilleArbre",
			drop: dropFunction,
			hoverClass: "accept",
			over: overFunction
		});

		$("#' . $idTable . ' .sansFils").droppable({
			accept: "#' . $idTable . ' .feuilleArbre, #' . $idTable . ' .noeudArbre",
			drop: dropFunction,
			hoverClass: "accept",
			over: overFunction
		});
	});
</script>';
		}

		$tableArborescente .= '
				<table id="' . $idTable . '" cellspacing="0" class="widefat post fixed">
					<thead>
						<tr>
							<th>' . $nomRacine . '</th>';
		if($titreInfo != null)
		{
			$tableArborescente = $tableArborescente . '	<th class="infoList">' . $titreInfo . '</th>';
			$infoRacine = EvaDisplayDesign::getInfoArborescence($table, $racine->id);
			$infoRacine = '
				<td id ="info-' . $racine->id . '" class="' . $infoRacine['class'] . '"></td>';
		}
		$tableArborescente = $tableArborescente;
		if($outputAction)
		{
			$tableArborescente .= '
							<th colspan=' . $actionSize . ' class="actionButtonList">' . __('Actions', 'evarisk') . '</th>';
		}
			$tableArborescente .= '</tr>
					</thead>
					
					<tfoot>
					</tfoot>
					
					<tbody>
						<tr id="node-' . $idTable . '-' . $racine->id . '" class="' . $class . ' parent racineArbre">
							<td id="tdRacine' . $idTable . '">';

			if($draggable)
			{
			$tableArborescente .= $nomRacine ;
			}
			else
			{
			$tableArborescente .= '&nbsp;';
			}

			$tableArborescente .=  '</td>' . $infoRacine;

		if($outputAction)
		{
			$tableArborescente .= $actions;
		}
		$tableArborescente .= '
						</tr>
						' . $monCorpsTable . '
					</tbody>
				</table>';	
		return $tableArborescente;
	}

	/**
	 * Returns the inner table of the list view tree with scripts that allow you to display the right part by clicking on the elements.
	 * This recursive function path tree from the father element to his leaves.
	 * @param array[Element_of_a_tree] $elementsFils Array of all the elements son of the father element.
	 * @param Element_of_a_tree $elementPere Father element.
	 * @param string $table Father element table name.
	 * @return string HTML code of the inner table.
	 */
	static function getCorpsTableArborescence($elementsFils, $elementPere, $table, $titreInfo, $idTable)
	{
		$monCorpsTable = '';
		$monCorpsSubElements = '';
		switch($table)
		{
			case TABLE_CATEGORIE_DANGER :
				$sousTable = TABLE_DANGER;
				$subElements = categorieDangers::getDangersDeLaCategorie($elementPere->id);
				$actionSize = 4;
				break;
			case TABLE_GROUPEMENT :
				$sousTable = TABLE_UNITE_TRAVAIL;
				$subElements = EvaGroupement::getUnitesDuGroupement($elementPere->id);
				$actionSize = 4;
				break;
			case TABLE_TACHE :
				$sousTable = TABLE_ACTIVITE;
				$tacheMere = new EvaTask($elementPere->id);
				$tacheMere->load();
				$subElements = $tacheMere->getWPDBActivitiesDependOn();
				$actionSize = 3;
				break;
			case TABLE_GROUPE_QUESTION :
				$sousTable = TABLE_QUESTION;
				$subElements = EvaGroupeQuestions::getQuestionsDuGroupeQuestions($elementPere->id);
				$actionSize = 3;
				break;
		}
		$monCorpsSubElements = '';
		foreach($subElements as $subElement)
		{
			switch($table)
			{
				case TABLE_CATEGORIE_DANGER :
					$tdSubEdit = '
							<td></td>
							<td></td><td class="noPadding" id="edit-leaf' . $subElement->id . '"><img style="max-width: 100%;" src="' . PICTO_EDIT . '" alt="' . sprintf(__('&Eacute;diter %s', 'evarisk'), __('le danger', 'evarisk')) . '" title="' . sprintf(__('&Eacute;diter %s', 'evarisk'), __('le danger', 'evarisk')) . '" /></td>';
					$tdSubDelete = '<td class="noPadding" id="delete-leaf' . $subElement->id . '"><img style="max-width: 100%;" src="' . PICTO_DELETE . '" alt="' . sprintf(__('Supprimer %s', 'evarisk'), __('le danger', 'evarisk')) . '" title="' . sprintf(__('Supprimer %s', 'evarisk'), __('le danger', 'evarisk')) . '" /></td>';
					break;
				case TABLE_GROUPEMENT :
					$tdSubEdit = '
							<td></td><td></td><td class="noPadding" id="edit-leaf' . $subElement->id . '"><img style="max-width: 100%;" src="' . PICTO_EDIT . '" alt="' . sprintf(__('&Eacute;diter %s', 'evarisk'), __('l\'unit&eacute; de travail', 'evarisk')) . '" title="' . sprintf(__('&Eacute;diter %s', 'evarisk'), __('l\'unit&eacute; de travail', 'evarisk')) . '" /></td><td style="display:none;" class="noPadding" id="risq-leaf' . $subElement->id . '"><img src="' .PICTO_LTL_EVAL_RISK . '" alt="' . sprintf(__('Risques %s', 'evarisk'), __('de l\'unit&eacute; de travail', 'evarisk')) . '" title="' . sprintf(__('Risques %s', 'evarisk'), __('de l\'unit&eacute; de travail', 'evarisk')) . '" /></td>';
					$tdSubDelete = '<td class="noPadding" id="delete-work-unit' . $subElement->id . '"><img style="max-width: 100%;" src="' . PICTO_DELETE . '" alt="' . sprintf(__('Supprimer %s', 'evarisk'), __('l\'unit&eacute; de travail', 'evarisk')) . '" title="' . sprintf(__('Supprimer %s', 'evarisk'), __('l\'unit&eacute; de travail', 'evarisk')) . '" /></td>';
					break;
				case TABLE_TACHE :
					$tdSubEdit = '<td class="noPadding" colspan=' . $actionSize . ' id="edit-leaf' . $subElement->id . '"><img style="max-width: 100%;" src="' . PICTO_EDIT . '" alt="' . sprintf(__('&Eacute;diter %s', 'evarisk'), __('l\'action', 'evarisk')) . '" title="' . sprintf(__('&Eacute;diter %s', 'evarisk'), __('l\'action', 'evarisk')) . '" /></td>';
					break;
				case TABLE_GROUPE_QUESTION :
					$tdSubDelete = '<td></td><td></td><td id="delete-leaf-' . $subElement->id . '"><img src="' . PICTO_DELETE . '" alt="' . sprintf(__('&Eacute;ffacer %s', 'evarisk'), __('la question', 'evarisk')) . '" title="' . sprintf(__('&Eacute;ffacer %s', 'evarisk'), __('la question', 'evarisk')) . '" /></td>';
					break;
			}
			switch($table)
			{
				case TABLE_CATEGORIE_DANGER :
				case TABLE_GROUPEMENT :
				case TABLE_TACHE :
					$subAffichage = $subElement->nom;
					$script = '
						<script type="text/javascript">
							$(document).ready(function()
							{
								$("#' . $idTable . ' #delete-leaf' . $subElement->id . '").unbind("click");
								$("#' . $idTable . ' #delete-work-unit' . $subElement->id . '").unbind("click");
								$("#' . $idTable . ' #risq-leaf' . $subElement->id . '").unbind("click");
								$("#' . $idTable . ' #edit-leaf' . $subElement->id . '").unbind("click");
								$("#' . $idTable . ' #leaf-' . $subElement->id . '").unbind("click");
								$("#' . $idTable . ' #leaf-' . $subElement->id . ' td:first-child").click(function(event){
									if(!$(event.target).is("span"))
										$("#' . $idTable . ' #risq-leaf' . $subElement->id . '").click();
								});
								$("#' . $idTable . ' #leaf-' . $subElement->id . ' td:first-child").dblclick(function(event){
									if(!$(event.target).is("span"))
										$("#' . $idTable . ' #edit-leaf' . $subElement->id . '").click();
								});
								$("#' . $idTable . ' #edit-leaf' . $subElement->id . '").click(function(){
									$("#menu").val(\'gestiongrptut\');
									$(\'#rightEnlarging\').show();
									$(\'#equilize\').click();
									var expanded = new Array();
									$(\'.expanded\').each(function(){expanded.push($(this).attr("id"));});
									$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
										"table": "' . $sousTable . '",
										"act": "edit",
										"id": "' . $subElement->id . '",
										"partie": "right",
										"menu": $("#menu").val(),
										"affichage": "affichageListe",
										"affichage": "affichageListe",
										"expanded": expanded
									});
									$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
										"table": "' . $sousTable . '",
										"act": "edit",
										"id": "' . $subElement->id . '",
										"partie": "left",
										"menu": $("#menu").val(),
										"affichage": "affichageListe",
										"expanded": expanded
									});
									$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
									// $(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
								});
								$("#' . $idTable . ' #risq-leaf' . $subElement->id . '").click(function(){
									$("#menu").val(\'risq\');
									$(\'#rightEnlarging\').show();
									$(\'#equilize\').click();
									var expanded = new Array();
									$(\'.expanded\').each(function(){expanded.push($(this).attr("id"));});
									$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
										"table": "' . $sousTable . '",
										"act": "edit",
										"id": "' . $subElement->id . '",
										"partie": "right",
										"menu": $("#menu").val(),
										"affichage": "affichageListe",
										"affichage": "affichageListe",
										"expanded": expanded
									});
									$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
										"table": "' . $sousTable . '",
										"act": "edit",
										"id": "' . $subElement->id . '",
										"partie": "left",
										"menu": $("#menu").val(),
										"affichage": "affichageListe",
										"expanded": expanded
									});
									$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
									// $(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
								});
							
								$("#' . $idTable . ' #delete-work-unit' . $subElement->id . '").click(function(){
									$("#menu").val(\'gestiongrptut\');
									$(\'#rightEnlarging\').show();
									$(\'#equilize\').click();
									var expanded = new Array();
									$(\'.expanded\').each(function(){expanded.push($(this).attr("id"));});

									if(confirm("' . __('Etes vous sur de vouloir supprimer cet element?', 'evarisk') . '")){
										$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
										$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', 
										{
											"post": "true", 
											"table": "' . $sousTable . '",
											"act": "delete",
											"id": "' . $subElement->id . '",
											"partie": "left",
											"menu": $("#menu").val(),
											"affichage": "affichageListe",
											"partition": "tout",
											"expanded": expanded
										});
									}
								});
							
								$("#' . $idTable . ' #delete-leaf' . $subElement->id . '").click(function(){
									$("#menu").val(\'gestiongrptut\');
									$(\'#rightEnlarging\').show();
									$(\'#equilize\').click();
									var expanded = new Array();
									$(\'.expanded\').each(function(){expanded.push($(this).attr("id"));});

									if(confirm("' . __('Etes vous sur de vouloir supprimer cet element?', 'evarisk') . '")){
										$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
										$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', 
										{
											"post": "true", 
											"table": "' . $sousTable . '",
											"act": "delete",
											"id": "' . $subElement->id . '",
											"partie": "left",
											"menu": $("#menu").val(),
											"affichage": "affichageListe",
											"partition": "tout",
											"expanded": expanded
										});
									}
								});

							});
						</script>';
					$subActions = $script . $tdSubEdit . $tdSubDelete;
					break;
				case TABLE_GROUPE_QUESTION :
					$subAffichage = 'Q' . $subElement->id . ' : ' . ucfirst($subElement->enonce);
					$script = '
						<script type="text/javascript">
							$(document).ready(function()  
							{
								$("#' . $idTable . ' #delete-leaf-' . $subElement->id . '").click(function(){
									var adresse = "' . EVA_INC_PLUGIN_URL . 'ajax.php?nom=' . $sousTable . '&id=' . $subElement->id . '&idPere=' . $elementPere->id . '&act=delete";
									$(\'#ajax-response\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
									$(\'#ajax-response\').load(adresse);
								});
							});
						</script>';
					$subActions = $script . $tdSubDelete;
					break;
			}
			$info = EvaDisplayDesign::getInfoArborescence($sousTable, $subElement->id);
			$monCorpsSubElements = $monCorpsSubElements . $script . '
				<tr id="leaf-' . $subElement->id . '" class="test child-of-node-' . $idTable . '-' . $elementPere->id . ' feuilleArbre">
					<td class="nomFeuilleArbre" >' . $subAffichage . '</td>';
				if($titreInfo != null)
				{
					$monCorpsSubElements = $monCorpsSubElements . '<td class="' . $info['class'] . '">' . $info['value'] . '</td>';
				}
				$monCorpsSubElements = $monCorpsSubElements . $subActions . '
				</tr>';
		}
		if(count($elementsFils) != 0)
		{
			foreach ($elementsFils as $element )
			{
				switch($table)
				{
					case TABLE_CATEGORIE_DANGER :
						$tdAddMain = '<td class="noPadding"  id="addMain' . $element->id . '"><img style="max-width: 100%;" src="' .PICTO_LTL_ADD_CATEGORIE_DANGER . '" alt="' . sprintf(__('Ajouter %s', 'evarisk'), __('une cat&eacute;gorie de dangers', 'evarisk')) . '" title="' . sprintf(__('Ajouter %s', 'evarisk'), __('une cat&eacute;gorie de dangers', 'evarisk')) . '" /></td><td id="addMain' . $element->id . 'Alt" style="display:none;"></td>';
						$tdAddSecondary = '<td class="noPadding"  id="addSecondary' . $element->id . '"><img style="max-width: 100%;" src="' .PICTO_LTL_ADD_DANGER . '" alt="' . sprintf(__('Ajouter %s', 'evarisk'), __('un danger', 'evarisk')) . '" title="' . sprintf(__('Ajouter %s', 'evarisk'), __('un danger', 'evarisk')) . '" /></td><td id="addSecondary' . $element->id . 'Alt" style="display:none;"></td>';
						$tdEdit = '<td class="noPadding"   id="edit-node' . $element->id . '"><img src="' .PICTO_EDIT . '" alt="' . sprintf(__('&Eacute;diter %s', 'evarisk'), __('la cat&eacute;gorie de dangers', 'evarisk')) . '" title="' . sprintf(__('&Eacute;diter %s', 'evarisk'), __('la cat&eacute;gorie de dangers', 'evarisk')) . '" /></td>';
						$tdDelete = '<td class="noPadding"   id="delete-node' . $element->id . '"><img src="' .PICTO_DELETE . '" alt="' . sprintf(__('Supprimer %s', 'evarisk'), __('la cat&eacute;gorie de dangers', 'evarisk')) . '" title="' . sprintf(__('Supprimer %s', 'evarisk'), __('la cat&eacute;gorie de dangers', 'evarisk')) . '" /></td>';
						break;
					case TABLE_GROUPEMENT :
						$tdAddMain = '<td class="noPadding" id="addMain' . $element->id . '"><img style="max-width: 100%;" src="' .PICTO_LTL_ADD_GROUPEMENT . '" alt="' . sprintf(__('Ajouter %s', 'evarisk'), __('un groupement', 'evarisk')) . '" title="' . sprintf(__('Ajouter %s', 'evarisk'), __('un groupement', 'evarisk')) . '" /></td><td id="addMain' . $element->id . 'Alt" style="display:none;"></td>';
						$tdAddSecondary = '<td class="noPadding" id="addSecondary' . $element->id . '"><img style="max-width: 100%;" src="' .PICTO_LTL_ADD_UNIT . '" alt="' . sprintf(__('Ajouter %s', 'evarisk'), __('une unit&eacute; de travail', 'evarisk')) . '" title="' . sprintf(__('Ajouter %s', 'evarisk'), __('une unit&eacute; de travail', 'evarisk')) . '" /></td><td id="addSecondary' . $element->id . 'Alt" style="display:none;"></td>';
						$tdEdit = '<td class="noPadding"  id="edit-node' . $element->id . '"><img src="' .PICTO_EDIT . '" alt="' . sprintf(__('&Eacute;diter %s', 'evarisk'), __('le groupement', 'evarisk')) . '" title="' . sprintf(__('&Eacute;diter %s', 'evarisk'), __('le groupement', 'evarisk')) . '" /></td><td class="noPadding" style="display:none;" id="risq-node' . $element->id . '"><img src="' .PICTO_LTL_EVAL_RISK . '" alt="' . sprintf(__('Risques %s', 'evarisk'), __('du groupement', 'evarisk')) . '" title="' . sprintf(__('Risques %s', 'evarisk'), __('du groupement', 'evarisk')) . '" /></td>';
						$tdDelete = '<td class="noPadding" id="delete-node' . $element->id . '"><img src="' . PICTO_DELETE . '" alt="' . sprintf(__('Supprimer %s', 'evarisk'), __('le groupement', 'evarisk')) . '" title="' . sprintf(__('Supprimer %s', 'evarisk'), __('le groupement', 'evarisk')) . '" /></td>';
						break;
					case TABLE_TACHE :
						$tdAddMain = '<td class="noPadding"  id="addMain' . $element->id . '"><img style="max-width: 100%;" src="' .PICTO_LTL_ADD_TACHE . '" alt="' . sprintf(__('Ajouter %s', 'evarisk'), __('une t&acirc;che', 'evarisk')) . '" title="' . sprintf(__('Ajouter %s', 'evarisk'), __('une t&acirc;che', 'evarisk')) . '" /></td><td id="addMain' . $element->id . 'Alt" style="display:none;"></td>';
						$tdAddSecondary = '<td class="noPadding"  id="addSecondary' . $element->id . '"><img style="max-width: 100%;" src="' .PICTO_LTL_ADD_ACTIVITE . '" alt="' . sprintf(__('Ajouter %s', 'evarisk'), __('une action', 'evarisk')) . '" title="' . sprintf(__('Ajouter %s', 'evarisk'), __('une action', 'evarisk')) . '" /></td><td id="addSecondary' . $element->id . 'Alt" style="display:none;"></td>';
						$tdEdit = '<td class="noPadding"   id="edit-node' . $element->id . '"><img src="' .PICTO_EDIT . '" alt="' . sprintf(__('&Eacute;diter %s', 'evarisk'), __('le groupement', 'evarisk')) . '" title="' . sprintf(__('&Eacute;diter %s', 'evarisk'), __('la t&acirc;che', 'evarisk')) . '" /></td>';
						break;
					case TABLE_GROUPE_QUESTION :
						$tdAdd = '<td class="noPadding"  id="add-node-' . $element->id . '"><img src="'.PICTO_INSERT.'" alt="' . __('Inserer sous le titre', 'evarisk') . '" title="Inserer sous le titre" /></td>';
						$tdEdit = '<td class="noPadding"   id="edit-node-' . $element->id . '"><img style="max-width: 100%;" src="' . PICTO_EDIT . '" alt="Modifier le titre" title="' . __('Modifier le titre', 'evarisk') . '" /></td>';
						$tdDelete = '<td id="delete-node-' . $element->id . '"><img src="' . PICTO_DELETE . '" alt="Effacer le titre" title="' . sprintf(__('&Eacute;ffacer %s', 'evarisk'), __('le titre', 'evarisk')) . '" />';
						break;
				}
				switch($table)
				{
					case TABLE_CATEGORIE_DANGER :
					case TABLE_GROUPEMENT :
					case TABLE_TACHE :
						$affichage = $element->nom;
						$script = '
					<script type="text/javascript">
						$(document).ready(function()  {
							$("#' . $idTable . ' #edit-node' . $element->id . '").unbind("click");
							$("#' . $idTable . ' #risq-node' . $element->id . '").unbind("click");
							$("#' . $idTable . ' #delete-node' . $element->id . '").unbind("click");
							$("#' . $idTable . ' #node-' . $idTable . '-' . $element->id . ' td:first-child").click(function(event){
								if(!$(event.target).is("span"))
									$("#' . $idTable . ' #risq-node' . $element->id . '").click();
							});
							$("#' . $idTable . ' #edit-node' . $element->id . '").click(function(){
								$("#menu").val(\'gestiongrptut\');
								$(\'#rightEnlarging\').show();
								$(\'#equilize\').click();
								var expanded = new Array();
								$(\'.expanded\').each(function(){expanded.push($(this).attr("id"));});
								$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
									"table": "' . $table . '",
									"act": "edit",
									"id": "' . $element->id . '",
									"partie": "right",
"menu": $("#menu").val(),
									"affichage": "affichageListe",
									"partition": "tout",
									"expanded": expanded
								});
								$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
									"table": "' . $table . '",
									"act": "edit",
									"id": "' . $element->id . '",
									"partie": "left",
"menu": $("#menu").val(),
									"affichage": "affichageListe",
									"partition": "tout",
									"expanded": expanded
								});
								$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
								// $(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
							});
							$("#' . $idTable . ' #risq-node' . $element->id . '").click(function(){
								$("#menu").val(\'risq\');
								$(\'#rightEnlarging\').show();
								$(\'#equilize\').click();
								var expanded = new Array();
								$(\'.expanded\').each(function(){expanded.push($(this).attr("id"));});
								$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
									"table": "' . $table . '",
									"act": "edit",
									"id": "' . $element->id . '",
									"partie": "right",
"menu": $("#menu").val(),
									"affichage": "affichageListe",
									"partition": "tout",
									"expanded": expanded
								});
								$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
									"table": "' . $table . '",
									"act": "edit",
									"id": "' . $element->id . '",
									"partie": "left",
"menu": $("#menu").val(),
									"affichage": "affichageListe",
									"partition": "tout",
									"expanded": expanded
								});
								$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
							});
							$("#' . $idTable . ' #delete-node' . $element->id . '").click(function(){
								$("#menu").val(\'gestiongrptut\');
								$(\'#rightEnlarging\').show();
								$(\'#equilize\').click();
								var expanded = new Array();
								$(\'.expanded\').each(function(){expanded.push($(this).attr("id"));});

								if(confirm("' . __('Etes vous sur de vouloir supprimer cet element?\r\nATTENTION: si cet element possede des sous elements, ils seront inaccessibles', 'evarisk') . '")){
									$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
									$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', 
									{
										"post": "true", 
										"table": "' . $table . '",
										"act": "delete",
										"id": "' . $element->id . '",
										"partie": "left",
										"menu": $("#menu").val(),
										"affichage": "affichageListe",
										"partition": "tout",
										"expanded": expanded
									});
								}
							});

              $("#' . $idTable . ' #addMain' . $element->id . '").unbind("click");
							$("#' . $idTable . ' #addMain' . $element->id . '").click(function(){
								';
							if($table == TABLE_GROUPEMENT)
							{
								$script .= '$("#menu").val("gestiongrptut");';
							}
							$script .=	
								'var nomPere = ' . $element->id . ';
								$(\'#rightEnlarging\').show();
								$(\'#equilize\').click();
								var expanded = new Array();
								$(\'.expanded\').each(function(){expanded.push($(this).attr("id"));});
								$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
									"table": "' . $table . '",
									"act": "add",
									"idPere": nomPere,
									"partie": "right",
"menu": $("#menu").val(),
									"affichage": "affichageListe",
									"partition": "tout",
									"expanded": expanded
								});
								$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
									"table": "' . $table . '",
									"act": "add",
									"idPere": nomPere,
									"partie": "left",
"menu": $("#menu").val(),
									"affichage": "affichageListe",
									"partition": "tout",
									"expanded": expanded
								});
								$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
								// $(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
								return false;
							});
							
              $("#' . $idTable . ' #addSecondary' . $element->id . '").unbind("click");
							$("#' . $idTable . ' #addSecondary' . $element->id . '").click(function(){
								';
							if($table == TABLE_GROUPEMENT)
							{
								$script .= '$("#menu").val("gestiongrptut");';
							}
							$script .=	
								'
								var nomPere = ' . $element->id . ';
								$(\'#rightEnlarging\').show();
								$(\'#equilize\').click();
								var expanded = new Array();
								$(\'.expanded\').each(function(){expanded.push($(this).attr("id"));});
								$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
									"table": "' . $sousTable . '",
									"act": "add",
									"idPere": nomPere,
									"partie": "right",
"menu": $("#menu").val(),
									"affichage": "affichageListe",
									"partition": "tout",
									"expanded": expanded
								});
								$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
									"table": "' . $sousTable . '",
									"act": "add",
									"idPere": nomPere,
									"partie": "left",
"menu": $("#menu").val(),
									"affichage": "affichageListe",
									"partition": "tout",
									"expanded": expanded
								});
								$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
								// $(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
								return false;
							});
						});
					</script>';
						$actions = $script . '
							' . $tdAddMain . '
							' . $tdAddSecondary . '
							' . $tdEdit . '
							' . $tdDelete;
						break;
					case TABLE_GROUPE_QUESTION :
						$affichage = $element->code . '-' . ucfirst($element->nom);
						$script = '
							<script type="text/javascript">
								$(document).ready(function()  
								{
									$("#' . $idTable . ' #delete-node-' . $element->id . '").click(function(){
										var adresse = "' . EVA_INC_PLUGIN_URL . 'ajax.php?nom=' . $table . '&id=' . $element->id . '&act=delete";
										$(\'#ajax-response\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
										$(\'#ajax-response\').load(adresse);
									});
									$("#' . $idTable . ' #add-node-' . $element->id . '").click(function(){
										for (var i=0;i<document.regulatoryWatchForm.titrePere.options.length;i++) 
										{
											if (document.regulatoryWatchForm.titrePere.options[i].value == ' . $element->id . ')
												document.regulatoryWatchForm.titrePere.options[i].selected = true;
										}
										$("#traiter").click();
									});
									$("#' . $idTable . ' #edit-node-' . $element->id . '").click(function(){
										for (var i=0;i<document.regulatoryWatchForm.titrePere.options.length;i++) 
										{
											if (document.regulatoryWatchForm.titrePere.options[i].value == ' . $element->id . ')
												document.regulatoryWatchForm.titrePere.options[i].selected = true;
										}
										if($("#codeTitre").val() == "")
										{
											$("#codeTitre").val(' . $element->code . ')
										}
										$("#updateVeille").click();
									});
								});
							</script>';
						$actions = $script . $tdAdd . $tdEdit . $tdDelete;
						break;
				}
				if($titreInfo != null)
				{
					$info = EvaDisplayDesign::getInfoArborescence($table, $element->id);
					$info = '<td id ="info-' . $element->id . '" class="' . $info['class'] . '">' . $info['value'] . '</td>';
				}
				else
				{
					$info = '';
				}
				$class = 'child-of-node-' . $idTable . '-' . $elementPere->id . '';
				$monCorpsTable = $monCorpsTable . '
					<tr id="node-' . $idTable . '-' . $element->id . '" class="' . $class . ' noeudArbre parent">
						<td class="nomNoeudArbre" >' . $affichage . '</td>
						' . $info .  $actions . '
					</tr>';
				
				$elements_fils = '';
				switch($table)
				{
					case TABLE_CATEGORIE_DANGER :
						$elements_fils = Arborescence::getFils($table, $element, "nom ASC");
						$sousTable = TABLE_DANGER;
						$subElements = categorieDangers::getDangersDeLaCategorie($element->id);
						break;
					case TABLE_GROUPEMENT :
						$elements_fils = Arborescence::getFils($table, $element, "nom ASC");
						$sousTable = TABLE_UNITE_TRAVAIL;
						$subElements = EvaGroupement::getUnitesDuGroupement($element->id);
						break;
					case TABLE_TACHE :
						$elements_fils = Arborescence::getFils($table, $element, "nom ASC");
            $sousTable = TABLE_ACTIVITE;
            $tache = new EvaTask($element->id);
            $tache->load();
            $subElements = $tache->getWPDBActivitiesDependOn();
						break;
					case TABLE_GROUPE_QUESTION :
						$elements_fils = Arborescence::getFils($table, $element, "code ASC");
						$sousTable = TABLE_QUESTION;
						$subElements = EvaGroupeQuestions::getQuestionsDuGroupeQuestions($element->id);
						break;
				}
				$trouveElement = count($elements_fils) + count($subElements);			
				if($trouveElement)
				{
					$monCorpsTable = $monCorpsTable . EvaDisplayDesign::getCorpsTableArborescence($elements_fils, $element, $table, $titreInfo, $idTable);
				}
			}
		}
		return $monCorpsTable . $monCorpsSubElements;
	}

	/**
	 * Returns information on an element to be displayed in the list view.
	 * @param string $table Element table name.
	 * @param int $elementId Identifier of the element.
	 * @return string The information.
	 */
	static function getInfoArborescence($table, $elementId)
	{
		switch($table)
		{
			case TABLE_DANGER :
				$info['value'] = '';
				$info['class'] = '';
				break;
			case TABLE_CATEGORIE_DANGER :
				$info['value'] = '';
				$info['class'] = '';
				break;
			case TABLE_TACHE :
				$info['value'] = '';
				$info['class'] = '';
				break;
			case TABLE_ACTIVITE :
				$info['value'] = '';
				$info['class'] = '';
				break;
			case TABLE_GROUPEMENT :
				$scoreRisqueGroupement = EvaGroupement::getScoreRisque($elementId);
				$niveauRisqueGroupement = EvaGroupement::getNiveauRisque($scoreRisqueGroupement);
				$seuilRisqueGroupement = Risque::getSeuil($scoreRisqueGroupement);
				$info['value'] = $niveauRisqueGroupement;
				$info['class'] = 'risque' . $seuilRisqueGroupement . 'Text';
				break;
			case TABLE_UNITE_TRAVAIL :
				$scoreRisqueUniteTravail = UniteDeTravail::getScoreRisque($elementId);
				$niveauRisqueUniteTravail = UniteDeTravail::getNiveauRisque($scoreRisqueUniteTravail);
				$seuilRisqueUniteTravail = Risque::getSeuil($scoreRisqueUniteTravail);
				$info['value'] = $niveauRisqueUniteTravail;
				$info['class'] = 'risque' . $seuilRisqueUniteTravail . 'Text';
				break;
		}
		return $info;
	}

	/*
	 * 
	 */
	static function getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script = '')
	{
		$barreTitre = '';
		$corpsTable = '';
		for($i=0; $i<count($titres); $i++)
		{
			$barreTitre = $barreTitre . '<th class="' . $classes[$i] . '" scope="col">' . $titres[$i] . '</th>';
		}
		if($barreTitre != '')
		{
			$barreTitre = '<tr valign="top">' . $barreTitre . '</tr>';
		}
		for($numeroLigne=0; $numeroLigne<count($lignesDeValeurs); $numeroLigne++)
		{
			$ligneDeValeurs = $lignesDeValeurs[$numeroLigne];
			$corpsTable = $corpsTable . '<tr id="' . $idLignes[$numeroLigne] . '" valign="top">';
			for($i=0; $i<count($ligneDeValeurs); $i++)
			{
				$corpsTable = $corpsTable . '
					<td class="' . $classes[$i] . ' ' . $ligneDeValeurs[$i]['class'] . '">' . $ligneDeValeurs[$i]['value'] . '</td>';
			}
			$corpsTable = $corpsTable . '</tr>';
		}
		$table = $script . '<table id="' . $idTable . '" cellspacing="0" class="widefat post fixed">
				<thead>
						' . $barreTitre . '
				</thead>
				<tfoot>
						' . $barreTitre . '
				</tfoot>
				<tbody>';
		$table = $table . $corpsTable;
		$table = $table . '
				</tbody>
			</table>';
		return $table;
	}

	/**
	 * Returns the  HTML list of the three elements to be dipslayed
	 * @see insererSociete
	 * @param string $table Elements to return table name.
	 * @param string $page Page of the paging.
	 * @param string $id Identifier of the element in wicth are the elements of the list.
	 * @param string $idPostBox Identifier of the Postbox in wicth the list is displayed.
	 * @return string HTML code of the list.
	 */
	static function creerListe($table, $page, $id, $idPostBox, $tableFils = null, $idFils = null)
	{
		switch($table)
		{
			case TABLE_GROUPEMENT:
				$element = EvaGroupement::getGroupement($id);
				$listeElems = Arborescence::getFils($table, $element, "nom ASC");
				unset($listeElements);
				$indiceListeElements = 0;
				$indiceFils = -1;
				foreach($listeElems as $elem)
				{
					if($tableFils == TABLE_GROUPEMENT && $elem->id == $idFils)
						$indiceFils = $indiceListeElements;
					$listeElements[] = array('id'=>$elem->id, 'table'=>TABLE_GROUPEMENT);
					$indiceListeElements ++;
				}
				$listeUnites = EvaGroupement::getUnitesDuGroupement($id);
				foreach($listeUnites as $elem)
				{
					if($tableFils == TABLE_UNITE_TRAVAIL && $elem->id == $idFils)
						$indiceFils = $indiceListeElements;
					$listeElements[] = array('id'=>$elem->id, 'table'=>TABLE_UNITE_TRAVAIL);
					$indiceListeElements ++;
				}
				$messageAbsence = __('Aucun groupement ni aucune unit&eacute; de travail n\'existe dans', 'evarisk');
				$mainAdd = __('Ajouter un nouveau groupement', 'evarisk');
				$mainSrc = PICTO_ADD_GROUPEMENT;
				$secondaryAdd = __('Ajouter une nouvelle unit&eacute; de travail', 'evarisk');
				$secondarySrc = PICTO_ADD_UNIT;
				$nombreElements = NOMBRE_ELEMENTS_AFFICHAGE_GRILLE_EVAL_RISQUES;
				break;
			case TABLE_CATEGORIE_DANGER:
				$element = categorieDangers::getCategorieDanger($id);
				$listeElems = Arborescence::getFils($table, $element, "nom ASC");
				unset($listeElements);
				$indiceListeElements = 0;
				$indiceFils = -1;
				unset($listeElements);
				foreach($listeElems as $elem)
				{
					if($tableFils == TABLE_CATEGORIE_DANGER && $elem->id == $idFils)
						$indiceFils = $indiceListeElements;
					$listeElements[] = array('id'=>$elem->id, 'table'=>TABLE_CATEGORIE_DANGER);
					$indiceListeElements ++;
				}
				$listeUnites = categorieDangers::getDangersDeLaCategorie($id);
				foreach($listeUnites as $elem)
				{
					if($tableFils == TABLE_DANGER && $elem->id == $idFils)
						$indiceFils = $indiceListeElements;
					$listeElements[] = array('id'=>$elem->id, 'table'=>TABLE_DANGER);
					$indiceListeElements ++;
				}
				$messageAbsence = __('Aucune categorie ni aucun danger n\'existe dans ', 'evarisk');
				$mainAdd = __('Ajouter une nouvelle cat&eacute;gorie de dangers.', 'evarisk');
				$mainSrc = PICTO_ADD_CATEGORIE_DANGER;
				$secondaryAdd = __('Ajouter un nouveau danger.', 'evarisk');
				$secondarySrc = PICTO_ADD_DANGER;
				$nombreElements = NOMBRE_ELEMENTS_AFFICHAGE_GRILLE_DANGERS;
				break;
		}
		if($indiceFils != -1)
			$page = ceil(($indiceFils + 1) / NOMBRE_ELEMENTS_AFFICHAGE_GRILLE_EVAL_RISQUES);
		$indice = ($page - 1) * $nombreElements;
		$pageMax = isset($listeElements)?ceil(count($listeElements)/$nombreElements):1;
		$liste  = '
<script type="text/javascript">
	$(document).ready(function() {
		$(\'#page' . $idPostBox . 'Reference\').val(' . $page . ');
		$(\'#page' . $idPostBox . '\').val(' . $page . ');
		if(document.getElementById(\'filAriane\').lastChild == document.getElementById(\'filAriane\').firstChild)
		{
			$(\'#' . $idPostBox . 'Pere\').addClass(\'hidden\');
		}
		else
		{
			$(\'#' . $idPostBox . 'Pere\').removeClass(\'hidden\');
		}
		$(\'#pageMax' . $idPostBox . '\').val(' . $pageMax . ');
		if($(\'#pageMax' . $idPostBox . '\').val() == 0)
		{
			$(\'#pageMax' . $idPostBox . '\').val(1);
		}
		if(' . $page . ' <= 1)
		{
			$(\'#first' . $idPostBox . '\').attr("disabled", "disabled");
			$(\'#previous' . $idPostBox . '\').attr("disabled", "disabled");
		}
		else
		{
			$(\'#first' . $idPostBox . '\').attr("disabled", "");
			$(\'#previous' . $idPostBox . '\').attr("disabled", "");
		}
		if(parseInt($(\'#pageMax' . $idPostBox . '\').val()) == ' . $page . ')
		{
			$(\'#last' . $idPostBox . '\').attr("disabled", "disabled");
			$(\'#next' . $idPostBox . '\').attr("disabled", "disabled");
		}
		else
		{
			$(\'#last' . $idPostBox . '\').attr("disabled", "");
			$(\'#next' . $idPostBox . '\').attr("disabled", "");
		}
	});
</script>';
		if(isset($listeElements[$indice]))
		{				
			$liste = $liste . EvaDisplayDesign::getTableMainDisplay(array_slice($listeElements, $indice, $nombreElements), $idPostBox, ceil(count($listeElements)/3));
		}
		else
		{
			$liste  = $liste . '
	<script type="text/javascript">
		$(document).ready(function() {
			$("#favorite-actions-mainPostBox").hide();
			$("#favorite-second-link-' . $idPostBox . '").show();
			$("#favorite-first-link-' . $idPostBox . '").show();
			$(\'#infoLi\').html("' . $messageAbsence . '\"" + $(\'#filAriane\').children(\'a:last\').html() + "\".<br />" +
		"<a href=\"#\" id=\"addMain\"><img src=\"' . $mainSrc . '\" alt=\"mainAdd\" title=\"' . $mainAdd . '\" />' . $mainAdd . '</a><br />" +
		"<a href=\"#\" id=\"addSecondary\"><img src=\"' . $secondarySrc . '\" alt=\"secondaryAdd\" title=\"' . $secondaryAdd . '\" />' . $secondaryAdd . '</a>");
			$("#addMain").click(function(){
				$(\'#favorite-first-link-' . $idPostBox . '\').click();
				return false;
			});
			$("#addSecondary").click(function(){
				$(\'#favorite-second-link-' . $idPostBox . '\').click();
				return false;
			});
		});
	</script>';
			$liste = $liste . '<p id="infoLi" class="paragraphe" style="width:98%;"></p>';
		}
		return  $liste;
	}
	
	/**
	 * Returns the table of elements with their main photo some of their informations and the scripts for use.
	 * @see getTableInfos
	 * @param array $elements Array of elements (key : 'table' => element table name, key : 'id' => element id name).
	 * @param string $idPostBox Postbox in wicth the table is displayed.
	 * @return string HTML code of the table.
	 */
	static function getTableMainDisplay($elements, $idPostBox, $pageMaxPostBox)
	{
		$nombreElements = 0;
		$script = '';
		$chargement = '
			$(\'#rightEnlarging\').show();
			$(\'#equilize\').click();
			$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
				"table": "' . $_POST['table'] . '",
				"id": "' . $_POST['idPere'] . '",
				"page": $(\'#page' . $idPostBox . 'Reference\').val(),
				"idPere": $(\'#identifiantActuelle' . $idPostBox . '\').val(),
				"act": "edit",
				"partie": "right",
				"menu": $("#menu").val(),
				"affichage": "affichageTable",
				"partition": "tout"
			});
			$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
				"table": "' . $_POST['table'] . '",
				"id": "' . $_POST['idPere'] . '",
				"page": $(\'#page' . $idPostBox . 'Reference\').val(),
				"idPere": $(\'#identifiantActuelle' . $idPostBox . '\').val(),
				"act": "edit",
				"partie": "left",
				"menu": $("#menu").val(),
				"affichage": "affichageTable",
				"partition": "tout"
			});
			$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
			$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
			return false;';
		foreach($elements as $elementObject)
		{
			$table = $elementObject['table'];
			$idElement = $elementObject['id'];
			$chargement = '
				$(\'#rightEnlarging\').show();
				$(\'#equilize\').click();
				$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
					"table": "' . $table . '",
					"id": "' . $idElement . '",
					"page": $(\'#page' . $idPostBox . 'Reference\').val(),
					"idPere": $(\'#identifiantActuelle' . $idPostBox . '\').val(),
					"act": "edit",
					"partie": "right",
					"menu": $("#menu").val(),
					"affichage": "affichageTable",
					"partition": "tout"
				});
				$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
					"table": "' . $_POST['table'] . '",
					"id": "' . $_POST['idPere'] . '",
					"page": $(\'#page' . $idPostBox . 'Reference\').val(),
					"idPere": $(\'#identifiantActuelle' . $idPostBox . '\').val(),
					"act": "edit",
					"partie": "left",
					"menu": $("#menu").val(),
					"affichage": "affichageTable",
					"partition": "tout"
				});
				$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
				$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
				return false;';
			$ligneEditer[] = array('value' => '<a id="edit' . $table . $idElement . '" class="button boutonInfos"><img alt="" title="' . __('&Eacute;diter', 'evarisk') . '" src="' . PICTO_EDIT . '"/>' . __('&Eacute;diter', 'evarisk') . '</a>', 'class' => 'boutonsInfoElement');
			switch($table)
			{
				case TABLE_GROUPEMENT:
					$mainTable = TABLE_GROUPEMENT;
					$sousTable = TABLE_UNITE_TRAVAIL;
					$element = EvaGroupement::getGroupement($idElement);
					$defaultPicto = DEFAULT_GROUP_PICTO;
					$infosElement = EvaGroupement::getInfosGroupement($idElement);
					$ligneSelect[] = array('value' => '<a id="select' . $table . $idElement . '" class="button boutonInfos">' . __('Parcourir', 'evarisk') . '</a>', 'class' => 'boutonsInfoElement');
					$ligneEditer[count($ligneEditer)-1]['value'] = $ligneEditer[count($ligneEditer)-1]['value'] . '<a id="risq' . $table . $idElement . '" class="button boutonInfos"><img alt="" title="' . __('Risques', 'evarisk') . '" src="' . PICTO_LTL_EVAL_RISK . '"/>' . __('Risques', 'evarisk') . '</a>';
					$nbFilsElement = count(Arborescence::getFils($table, $element));
					$nbUnitesElement = count(EvaGroupement::getUnitesDuGroupement($idElement));
					
					if($nbFilsElement >= 0 and $nbUnitesElement == 0)
					{
						$ligneAddMain[] = array('class' => 'boutonsInfoElement', 'value' => '<a id="addMain' . $table . $idElement . '" class="button boutonInfos"><img alt="" title="' . __('Aj. groupement', 'evarisk') . '" src="' . PICTO_LTL_ADD_GROUPEMENT . '"/>' . __('Aj. gpmt', 'evarisk') . '</a>');
					}
					else
					{
						$ligneAddMain[] = array('class' => 'boutonsInfoElement', 'value' => '');
					}
					if($nbUnitesElement >= 0 and $nbFilsElement == 0)
					{
						$ligneAddSecondary[] = array('class' => 'boutonsInfoElement', 'value' => '<a id="addSecondary' . $table . $idElement . '" class="button boutonInfos"><img alt="" title="' . __('Aj. unit&eacute;', 'evarisk') . '" src="' . PICTO_LTL_ADD_UNIT . '"/>' . __('Aj. unit&eacute;', 'evarisk') . '</a>');
					}
					else
					{
						$ligneAddSecondary[] = array('class' => 'boutonsInfoElement', 'value' => '');
					}
					$aFilsNoeud = 'true';
					$aFilsFeuille = 'false';
					$nombreElementsAffichage = NOMBRE_ELEMENTS_AFFICHAGE_GRILLE_EVAL_RISQUES;
					break;
				case TABLE_UNITE_TRAVAIL:
					$mainTable = TABLE_GROUPEMENT;
					$sousTable = TABLE_UNITE_TRAVAIL;
					$element = UniteDeTravail::getWorkingUnit($idElement);
					$defaultPicto = DEFAULT_WORKING_UNIT_PICTO;
					$infosElement = UniteDeTravail::getWorkingUnitInfos($idElement);
					$ligneSelect[] = array('value' => '', 'class' => 'boutonsInfoElement');
					$ligneEditer[count($ligneEditer)-1]['value'] = $ligneEditer[count($ligneEditer)-1]['value'] . '<a id="risq' . $table . $idElement . '" class="button boutonInfos"><img alt="" title="' . __('Risques', 'evarisk') . '" src="' . PICTO_LTL_EVAL_RISK . '"/>' . __('Risques', 'evarisk') . '</a>';
					$aFilsNoeud = 'false';
					$aFilsFeuille = 'true';
					$nombreElementsAffichage = NOMBRE_ELEMENTS_AFFICHAGE_GRILLE_EVAL_RISQUES;
					break;
				case TABLE_CATEGORIE_DANGER:
					$mainTable = TABLE_CATEGORIE_DANGER;
					$sousTable = TABLE_DANGER;
					$element = categorieDangers::getCategorieDanger($idElement);
					$defaultPicto = DEFAULT_DANGER_CATEGORIE_PICTO;
					$ligneSelect[] = array('value' => '<a id="select' . $table . $idElement . '" class="button boutonInfos">' . __('S&eacute;lectionner', 'evarisk') . '</a>', 'class' => 'boutonsInfoElement');
					$infosElement = null;
					$nbFilsElement = count(Arborescence::getFils($table, $element));
					$nbUnitesElement = count(categorieDangers::getDangersDeLaCategorie($idElement));
					
					if($nbFilsElement >= 0 and $nbUnitesElement == 0)
					{
						$ligneAddMain[] = array('class' => 'boutonsInfoElement', 'value' => '<a id="addMain' . $table . $idElement . '" class="button boutonInfos"><img alt="" title="' . __('Aj. groupement', 'evarisk') . '" src="' . PICTO_LTL_ADD_CATEGORIE_DANGER . '"/>' . __('Aj. gpmt', 'evarisk') . '</a>');
					}
					else
					{
						$ligneAddMain[] = array('class' => 'boutonsInfoElement', 'value' => '');
					}
					if($nbUnitesElement >= 0 and $nbFilsElement == 0)
					{
						$ligneAddSecondary[] = array('class' => 'boutonsInfoElement', 'value' => '<a id="addSecondary' . $table . $idElement . '" class="button boutonInfos"><img alt="" title="' . __('Aj. unit&eacute;', 'evarisk') . '" src="' . PICTO_LTL_ADD_DANGER . '"/>' . __('Aj. danger', 'evarisk') . '</a>');
					}
					else
					{
						$ligneAddSecondary[] = array('class' => 'boutonsInfoElement', 'value' => '');
					}
					$aFilsNoeud = 'true';
					$aFilsFeuille = 'false';
					$nombreElementsAffichage = NOMBRE_ELEMENTS_AFFICHAGE_GRILLE_DANGERS;
					break;
				case TABLE_DANGER:
					$mainTable = TABLE_CATEGORIE_DANGER;
					$sousTable = TABLE_DANGER;
					$element = EvaDanger::getDanger($idElement);
					$defaultPicto = DEFAULT_DANGER_PICTO;
					$infosElement = null;
					$ligneSelect[] = array('value' => '', 'class' => 'boutonsInfoElement');
					$aFilsNoeud = 'false';
					$aFilsFeuille = 'true';
					$nombreElementsAffichage = NOMBRE_ELEMENTS_AFFICHAGE_GRILLE_DANGERS;
					break;
			}
			$scriptFilAriane = '
				if($(\'#filAriane :last-child\').is("label"))
					$(\'#filAriane :last-child\').remove();
				$(\'#filAriane\').append(\'<label>&nbsp;&raquo;&nbsp;</label><a href="#" id="element' . $element->id . '" class="elementFilAriane">' . addslashes($element->nom) . '</a>\');
				$(\'#page' . $idPostBox . 'Reference\').val(1);
				$(document).ready(function() {
					$(\'#element' . $element->id . '\').click(function() {
						$(\'#identifiantActuelle' . $idPostBox . '\').val("' . $element->id . '");
						$(\'#page' . $idPostBox . 'Reference\').val(1);
						while($(\'#filAriane :last-child\').attr("id") != "element' . $element->id . '")
						{
							$(\'#filAriane :last-child\').remove();
						}
																
						$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
							"table": "' . $table . '",
							"act": "changementPage",
							"page": $(\'#page' . $idPostBox . 'Reference\').val(),
							"idPere": $(\'#identifiantActuelle' . $idPostBox . '\').val(),
							"partie": "right",
							"menu": $("#menu").val(),
							"affichage": "affichageTable",
							"partition": "main"
						});
						$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
							"table": "' . $table . '",
							"act": "changementPage",
							"page": $(\'#page' . $idPostBox . 'Reference\').val(),
							"idPere": $(\'#identifiantActuelle' . $idPostBox . '\').val(),
							"partie": "left",
							"menu": $("#menu").val(),
							"affichage": "affichageTable",
							"partition": "main"
						});
						
						$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
						$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
						
						$(\'#' . $idPostBox . ' h3 span\').html("' . addslashes($element->nom) . '");
						return false;
					});
				});';

			$indiceLigneValeur = 1;
			$lignesDeValeurs[$indiceLigneValeur++][] = array('value' => $element->nom, 'class' => 'nomElement');
			// $photoElement = EVA_HOME_URL . $element->mainPhoto;
			// if($photoElement == EVA_HOME_URL)
			// {
				// $photoElement = $defaultPicto;
			// }
			$photoElement = evaPhoto::getMainPhoto($table, $idElement);		
			switch($table)
			{
				case TABLE_CATEGORIE_DANGER:
					$photoElement = ($photoElement != 'error') ? (EVA_HOME_URL . $photoElement) : DEFAULT_DANGER_CATEGORIE_PICTO;
				break;
				case TABLE_GROUPEMENT:
					$photoElement = ($photoElement != 'error') ? (EVA_HOME_URL . $photoElement) : DEFAULT_GROUP_PICTO;
				break;
				case TABLE_UNITE_TRAVAIL:
					$photoElement = ($photoElement != 'error') ? (EVA_HOME_URL . $photoElement) : DEFAULT_WORKING_UNIT_PICTO;
				break;
			}

			$lignesDeValeurs[$indiceLigneValeur++][] = array('value' => '<img id="photo' . $table . $idElement . '" src="' . $photoElement . '" alt="mainPhoto" title="mainPhoto" />', 'class' => 'photoElement');
			for($i=$indiceLigneValeur; $i<(count($infosElement)+$indiceLigneValeur); $i++)
			{
				$infoElement = $infosElement[$i - $indiceLigneValeur];
				$lignesDeValeurs[$i][] = array('value' => $infoElement['nom'], 'class' => 'nomInfoElement');
				$lignesDeValeurs[$i][] = array('value' => ':', 'class' => 'deuxPoints');
				$lignesDeValeurs[$i][] = array('value' => $infoElement['valeur'], 'class' => 'valeurInfoElement ' . $infoElement['classeValeur']);
			}
			$nombreElements = $nombreElements + 1;
			
			$script = $script .'
				<script type="text/javascript">
					$(document).ready(function() {
						if(' . $aFilsNoeud . ')
						{
							$("#favorite-second-link-' . $idPostBox . '").hide();
							$("#favorite-first-link-' . $idPostBox . '").show();
						}
						if(' . $aFilsFeuille . ')
						{
							$("#favorite-first-link-' . $idPostBox . '").hide();
							$("#favorite-second-link-' . $idPostBox . '").show();
						}
						var timeoutDbl' . $table . $idElement . '_0;
						var timeoutDbl' . $table . $idElement . '_1;
						var nbClic = 0;
						$(\'#photo' . $table . $idElement . '\').dblclick(function() {
							clearTimeout(timeoutDbl' . $table . $idElement . '_0);
							clearTimeout(timeoutDbl' . $table . $idElement . '_1);
							if(($("#select' . $table . $idElement . '").attr("id")) != undefined)
								$("#select' . $table . $idElement . '").click();
							else
								$("#edit' . $table . $idElement . '").click();
						});
						$(\'#photo' . $table . $idElement . '\').parent().click(function(event){
							if(nbClic == 0)
							{
								timeoutDbl' . $table . $idElement . '_0 = setTimeout 
								( 
									function() 
									{ 
										$("#edit' . $table . $idElement . '").click();
									}, 
									300 
								);
							}
							else
							{
								timeoutDbl' . $table . $idElement . '_1 = setTimeout 
								( 
									function() 
									{ 
										$("#edit' . $table . $idElement . '").click();
									}, 
									300 
								);
							}
							nbClic = (nbClic + 1)%2;
							return("false");
						});
						$(\'#edit' . $table . $idElement . '\').click(function() {
							$("#menu").val(\'gestiongrptut\');
							if($(\'#filAriane :last-child\').is("label"))
								$(\'#filAriane :last-child\').remove();
							$(\'#filAriane :last-child\').after(\'<label>&nbsp;&raquo;&nbsp;&Eacute;dition&nbsp;de&nbsp;' . $element->nom . '</label>\');
							' . $chargement . '
						});
						$(\'#risq' . $table . $idElement . '\').click(function() {
							$("#menu").val(\'risq\');
							if($(\'#filAriane :last-child\').is("label"))
								$(\'#filAriane :last-child\').remove();
							$(\'#filAriane :last-child\').after(\'<label>&nbsp;&raquo;&nbsp;&Eacute;valuation des risques de ' . $element->nom . '</label>\');
							' . $chargement . '
						});
						$(\'#addMain' . $table . $idElement . '\').click(function() {
							$(\'#identifiantActuelle' . $idPostBox . '\').val("' . $element->id . '");
							$(\'#page' . $idPostBox . 'Reference\').val(1);
							$(\'#rightEnlarging\').show();
							$(\'#equilize\').click();
							$("#menu").val(\'gestiongrptut\');
							' . $scriptFilAriane . '
							$("#filAriane :last-child").after("<label>&nbsp;&raquo;&nbsp;Ajout d\'un nouveau groupement &agrave; ' . $element->nom . '</label>");
							
							$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
								"table": "' . $table . '",
								"act": "add",
								"page": $(\'#page' . $idPostBox . 'Reference\').val(),
								"idPere": ' . $element->id . ',
								"partie": "right",
								"menu": $("#menu").val(),
								"affichage": "affichageTable",
								"partition": "tout"
							});
							$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
								"table": "' . $table . '",
								"act": "add",
								"page": $(\'#page' . $idPostBox . 'Reference\').val(),
								"idPere": ' . $element->id . ',
								"partie": "left",
								"menu": $("#menu").val(),
								"affichage": "affichageTable",
								"partition": "tout"
							});
							
							$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
							$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
							return false;
						});
						$(\'#addSecondary' . $table . $idElement . '\').click(function() {
							$(\'#identifiantActuelle' . $idPostBox . '\').val("' . $element->id . '");
							$(\'#page' . $idPostBox . 'Reference\').val(1);
							$(\'#rightEnlarging\').show();
							$(\'#equilize\').click();
							$("#menu").val(\'gestiongrptut\');
							' . $scriptFilAriane . '
							$("#filAriane :last-child").after("<label>&nbsp;&raquo;&nbsp;Ajout d\'une nouvelle unit&eacute; de travail &agrave; ' . $element->nom . '</label>");
							
							$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
								"table": "' . $sousTable . '",
								"act": "add",
								"page": 1,
								"idPere": ' . $element->id . ',
								"partie": "right",
								"menu": $("#menu").val(),
								"affichage": "affichageTable",
								"partition": "tout"
							});
							$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
								"table": "' . $sousTable . '",
								"act": "add",
								"page": $(\'#page' . $idPostBox . 'Reference\').val(),
								"idPere": $(\'#identifiantActuelle' . $idPostBox . '\').val(),
								"partie": "left",
								"menu": $("#menu").val(),
								"affichage": "affichageTable",
								"partition": "tout"
							});
							
							$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
							$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
							return false;
						});
						$(\'#select' . $table . $idElement . '\').click(function() {
							if(!($(this).attr("disabled")))
							{
								if($(\'#filAriane :last-child\').is("label"))
									$(\'#filAriane :last-child\').remove();
								$(\'#identifiantActuelle' . $idPostBox . '\').val(' . $idElement . ');
								' . $scriptFilAriane . '

								$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
									"table": "' . $table . '",
									"act": "changementPage",
									"page": $(\'#page' . $idPostBox . 'Reference\').val(),
									"idPere": $(\'#identifiantActuelle' . $idPostBox . '\').val(),
									"partie": "right",
									"menu": $("#menu").val(),
									"affichage": "affichageTable",
									"partition": "main"
								});
								$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
									"table": "' . $table . '",
									"act": "changementPage",
									"page": $(\'#page' . $idPostBox . 'Reference\').val(),
									"idPere": $(\'#identifiantActuelle' . $idPostBox . '\').val(),
									"partie": "left",
									"menu": $("#menu").val(),
									"affichage": "affichageTable",
									"partition": "main"
								});
								
								$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
								$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
								
								$(\'#' . $idPostBox . ' h3 span\').html("' . addslashes($element->nom) . '");
								return false;
							}
						});
					});
				</script>';
		}
		for($i = 0; $i<$nombreElements; $i++)
		{
			$lignesDeValeurs[0][] = array('value' => null, 'class' => 'nomInfoElement');
			$lignesDeValeurs[0][] = array('value' => null, 'class' => 'deuxPoints');
			$lignesDeValeurs[0][] = array('value' => null, 'class' => 'valeurInfoElement');
		}
		
		$lignesDeValeurs[] = $ligneEditer;
		$lignesDeValeurs[] = $ligneAddMain;
		$lignesDeValeurs[] = $ligneAddSecondary;
		$lignesDeValeurs[] = $ligneSelect;
		$lignesDeValeurs[][] = array('value' => EvaDisplayDesign::afficherPagination($idPostBox, $pageMaxPostBox, $mainTable), 'class' => 'pagination');
		
		$idTable = 'table' . $idPostBox;
		$titres = null;
		$classes = null;
		$idLignes = null;
		$largeur = 98 / $nombreElementsAffichage;
		$largeurNomInfo = $largeur * 5 / 11 ;
		$largeurValeurInfo = $largeur * 3 / 11 ;
		$largeurDeuxPoints = $largeur * 1 / 11 ;
		$largeurDerniereColonne = $largeur * 2 / 11 ;
		$listeBoutons = '';
		
		$chargement = '
			$(\'#rightEnlarging\').show();
			$(\'#equilize\').click();
			$(\'#partieEdition\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
				"table": "' . $mainTable . '",
				"id": "' . $_POST['idPere'] . '",
				"page": $(\'#page' . $idPostBox . 'Reference\').val(),
				"idPere": $(\'#identifiantActuelle' . $idPostBox . '\').val(),
				"act": "edit",
				"partie": "right",
				"menu": $("#menu").val(),
				"affichage": "affichageTable",
				"partition": "tout"
			});
			$(\'#partieGauche\').load(\'' . EVA_INC_PLUGIN_URL . 'ajax.php\', {"post": "true", 
				"table": "' . $mainTable . '",
				"id": "' . $_POST['idPere'] . '",
				"page": $(\'#page' . $idPostBox . 'Reference\').val(),
				"idPere": $(\'#identifiantActuelle' . $idPostBox . '\').val(),
				"act": "edit",
				"partie": "left",
				"menu": $("#menu").val(),
				"affichage": "affichageTable",
				"partition": "tout"
			});
			$(\'#partieEdition\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
			$(\'#partieGauche\').html(\'<center><img src="' . PICTO_LOADING . '" /></center>\');
			return false;';
		//ajout des éléments de la barre lattérale/side-bar
		switch($_POST['table'])
		{
			case TABLE_GROUPEMENT :
			case TABLE_UNITE_TRAVAIL :
			{
				$elementPere = Arborescence::getPere(TABLE_GROUPEMENT ,$element);
				if($elementPere == null)
				{
					$elementPere = EvaGroupement::getGroupement($element->id_groupement);
				}
				if($_POST['idPere'] == 1)
				{// On est à la racine
					$listeBoutons = '
						"<img src=\"' . PICTO_LTL_ADD_GROUPEMENT . '\" id=\"addMain' . $_POST['table'] . $_POST['idPere'] . '\"/><br />" +
					';
					$elementPere->nom = "la racine";
				}
				else
				{// On n'est pas à la racine
					// On regarde le bouton editer qui existe forcément
					// pour pouvoir deviner si l'on a à faire à un ensemble de sous-groupement
					// ou à un ensemble de d'unités 
					if(substr($lignesDeValeurs[8][0]['value'], strlen('<a id="edit'), strlen(TABLE_GROUPEMENT)) == TABLE_GROUPEMENT)
					{// C'est un ensemble de sous-groupement
						$listeBoutons = '
							"<img src=\"' . PICTO_LTL_ADD_GROUPEMENT . '\" id=\"addMain' . $_POST['table'] . $_POST['idPere'] . '\"/><br />" +
						';
					}
					else
					{// C'est un ensemble d'unités
						$listeBoutons = '
							"<img src=\"' . PICTO_LTL_ADD_UNIT . '\" id=\"addSecondary' . $_POST['table'] . $_POST['idPere'] . '\"/><br />" +
						';
					}
					$listeBoutons = $listeBoutons . '
						"<img src=\"' . PICTO_EDIT . '\" id=\"edit' . $_POST['table'] . $_POST['idPere'] . '\"/><br />" +
						"<img src=\"' . PICTO_LTL_EVAL_RISK . '\" id=\"risq' . $_POST['table'] . $_POST['idPere'] . '\"/><br />" +
					';
				}
				$scriptColDroite = '<script type="text/javascript">
					$("#favorite-actions-mainPostBox").hide();
					$(document).ready(function() {
						$(\'#edit' . $_POST['table'] . $_POST['idPere'] . '\').click(function() {
							$("#menu").val(\'gestiongrptut\');
							if($(\'#filAriane :last-child\').is("label"))
								$(\'#filAriane :last-child\').remove();
							$(\'#filAriane :last-child\').after(\'<label>&nbsp;&raquo;&nbsp;&Eacute;dition&nbsp;de&nbsp;' . $elementPere->nom . '</label>\');
							' . $chargement . '
						});
						$(\'#risq' . $_POST['table'] . $_POST['idPere'] . '\').click(function() {
							$("#menu").val(\'risq\');
							if($(\'#filAriane :last-child\').is("label"))
								$(\'#filAriane :last-child\').remove();
							$(\'#filAriane :last-child\').after(\'<label>&nbsp;&raquo;&nbsp;&Eacute;valuation des risques de ' . $elementPere->nom . '</label>\');
							' . $chargement . '
						});
						$(\'#addMain' . $_POST['table'] . $_POST['idPere'] . '\').click(function() {
							$(\'#rightEnlarging\').show();
							$(\'#equilize\').click();
							$("#menu").val(\'gestiongrptut\');
							if($(\'#filAriane :last-child\').is("label"))
								$(\'#filAriane :last-child\').remove();
							$("#filAriane :last-child").after("<label>&nbsp;&raquo;&nbsp;Ajout d\'un nouveau groupement &agrave; ' . $elementPere->nom . '</label>");
							$("#favorite-first-link-mainPostBox").click();
						});
						$(\'#addSecondary' . $_POST['table'] . $_POST['idPere'] . '\').click(function() {
							$(\'#rightEnlarging\').show();
							$(\'#equilize\').click();
							$("#menu").val(\'gestiongrptut\');
							if($(\'#filAriane :last-child\').is("label"))
								$(\'#filAriane :last-child\').remove();
							$("#filAriane :last-child").after("<label>&nbsp;&raquo;&nbsp;Ajout d\'une nouvelle unit&eacute; de travail &agrave; ' . $elementPere->nom . '</label>");
							$("#favorite-second-link-mainPostBox").click();
						});
					});
				</script>';
				break;
			}
			case TABLE_CATEGORIE_DANGER :
			case TABLE_DANGER :
			{
				$elementPere = Arborescence::getPere(TABLE_CATEGORIE_DANGER ,$element);
				if($elementPere == null)
				{
					$elementPere = categorieDangers::getCategorieDanger($element->id_categorie);
				}
				if($_POST['idPere'] == 1)
				{// On est à la racine
					$listeBoutons = '
						"<img src=\"' . PICTO_LTL_ADD_CATEGORIE_DANGER . '\" id=\"addMain' . $_POST['table'] . $_POST['idPere'] . '\"/><br />" +
					';
					$elementPere->nom = "la racine";
				}
				else
				{// On n'est pas à la racine
					// On regarde le bouton editer qui existe forcément
					// pour pouvoir deviner si l'on a à faire à un ensemble de catégories
					// ou à un ensemble de de dangers
					if(substr($lignesDeValeurs[count($lignesDeValeurs) - 3][0]['value'], strlen('<a id="edit'), strlen(TABLE_CATEGORIE_DANGER)) == TABLE_CATEGORIE_DANGER)
					{// C'est un ensemble de catégories
						$listeBoutons = '
							"<img src=\"' . PICTO_LTL_ADD_CATEGORIE_DANGER . '\" id=\"addMain' . $_POST['table'] . $_POST['idPere'] . '\"/><br />" +
						';
					}
					else
					{// C'est un ensemble de dangers
						$listeBoutons = '
							"<img src=\"' . PICTO_LTL_ADD_DANGER . '\" id=\"addSecondary' . $_POST['table'] . $_POST['idPere'] . '\"/><br />" +
						';
					}
					$listeBoutons = $listeBoutons . '
						"<img src=\"' . PICTO_EDIT . '\" id=\"edit' . $_POST['table'] . $_POST['idPere'] . '\"/><br />" +
					';
				}
				$scriptColDroite = '<script type="text/javascript">
					$("#favorite-actions-mainPostBox").hide();
					$(document).ready(function() {
						$(\'#edit' . $_POST['table'] . $_POST['idPere'] . '\').click(function() {
							$("#menu").val(\'gestiongrptut\');
							if($(\'#filAriane :last-child\').is("label"))
								$(\'#filAriane :last-child\').remove();
							$(\'#filAriane :last-child\').after(\'<label>&nbsp;&raquo;&nbsp;&Eacute;dition&nbsp;de&nbsp;' . $elementPere->nom . '</label>\');
							' . $chargement . '
						});
						$(\'#addMain' . $_POST['table'] . $_POST['idPere'] . '\').click(function() {
							$(\'#rightEnlarging\').show();
							$(\'#equilize\').click();
							$("#menu").val(\'gestiongrptut\');
							if($(\'#filAriane :last-child\').is("label"))
								$(\'#filAriane :last-child\').remove();
							$("#filAriane :last-child").after("<label>&nbsp;&raquo;&nbsp;Ajout d\'une nouvelle cat&eacute;gorie de dangers &agrave; ' . $elementPere->nom . '</label>");
							$("#favorite-first-link-mainPostBox").click();
						});
						$(\'#addSecondary' . $_POST['table'] . $_POST['idPere'] . '\').click(function() {
							$(\'#rightEnlarging\').show();
							$(\'#equilize\').click();
							$("#menu").val(\'gestiongrptut\');
							if($(\'#filAriane :last-child\').is("label"))
								$(\'#filAriane :last-child\').remove();
							$("#filAriane :last-child").after("<label>&nbsp;&raquo;&nbsp;Ajout d\'un nouveau danger &agrave; ' . $elementPere->nom . '</label>");
							$("#favorite-second-link-mainPostBox").click();
						});
					});
				</script>';
				break;
			}
			default :
			{
				echo 'Pensez &agrave; <b>ajouter</b> le <b>cas ' . $_POST['table'] . '</b> dans le <b>switch</b> ligne <b>' . __LINE__ . '</b> du fichier "' . dirname(__FILE__) . '\<b>' . basename(__FILE__) . '</b>" pour avoir la liste de boutons.<br />';
				break;
			}
		}
		$scriptTable = '<script type="text/javascript">
				$(document).ready(function() {
					$("#' . $idTable . ' tr:first").append(
						"<td rowspan=' . (count($lignesDeValeurs) - 1) . ' style=\"width:' . $largeurDerniereColonne . '%;\">" + ' . $listeBoutons . '
						"</td>"
					);
					$("#' . $idTable . ' tr:first").append();
					$("#' . $idTable . ' .nomElement, #' . $idTable . ' .photoElement, #' . $idTable . ' .boutonsInfoElement").each(function(){
						$(this).attr("colspan", "3");
					});
					$("#' . $idTable . ' .nomInfoElement").each(function(){
						$(this).css("width","' . $largeurNomInfo . '%");
					});
					$("#' . $idTable . ' .deuxPoints").each(function(){
						$(this).css("width","' . $largeurDeuxPoints . '%");
					});
					$("#' . $idTable . ' .valeurInfoElement").each(function(){
						$(this).css("width","' . $largeurValeurInfo . '%");
					});
					$("#' . $idTable . ' .pagination").each(function(){
						$(this).attr("colspan", "' . ($nombreElements * 3 + 1) . '");
					});
					$("#' . $idPostBox . ' .inside").each(function(){
						$(this).css("padding", "0");
					});
				});
			</script>';
		$tableMainDisplay = EvaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $scriptTable);
		
		$tableMainDisplay = $script . $tableMainDisplay . $scriptColDroite;
		return $tableMainDisplay;
	}

	/**
	*	Allow to output a template filled with external vars
	*
	*	@param mixed $template The html template with the different element to replace
	*	@param array $param An array with the different value to replace elements in the template
	*
	*	@return mixed $template The html template filled with the different element
	*/
	static function feedTemplate($template, $param)
	{
    if(is_array($param))
		{
			foreach($param as $key => $value)
			{
				if(!is_array($value))
				{
					$template=str_replace($key, $value, $template);
				}
				else
				{
					$template=str_replace($key, "<div style=\"border:1px solid red;\" >$key = <pre>".print_r($value)."</pre></div>", $template);
				}
			}
			return $template;
    }
		else
		{
        return str_replace($param, $value, $template);
    }
	}

	/**
	*	Return the form template for generating the single document
	*	@return string HTML code of the form
	*/
	static function getFormulaireGenerationDUER()
	{
		return 
			EvaDisplayInput::ouvrirForm('POST', 'infosGenerationDU', 'infosGenerationDU') .
				'<table summary="" border="0" cellpadding="0" cellspacing="0" align="center" class="tabcroisillon" style="width:100%;" >
					<tr>
						<td style="width:60%;" >
							<table summary="" cellpadding="0" cellspacing="0" border="0" class="tabformulaire" style="width:100%;" >
								<tr>
									<td ><label for="dateCreation">' . __('date de cr&eacute;ation', 'evarisk') . '</label></td>
									<td >' . EvaDisplayInput::afficherInput('text', 'dateCreation', '#DATEFORM1#', '', '', 'dateCreation', false, false, 150, '', 'Date', '100%', '', 'left;width:100%;', false) . '</td>
								</tr>
								<tr>
									<td ><label for="dateDebutAudit">' . _('date de d&eacute;but d\'audit', 'evarisk') . '</label></td>
									<td >' . EvaDisplayInput::afficherInput('text', 'dateDebutAudit', '#DATEDEBUT1#', '', '', 'dateDebutAudit', false, false, 150, '', 'Date', '100%', '', 'left;width:100%;', false) . '</td>
								</tr>
								<tr>
									<td ><label for="dateFinAudit">' . __('date de fin d\'audit', 'evarisk') . '</label></td>
									<td >' . EvaDisplayInput::afficherInput('text', 'dateFinAudit', '#DATEFIN1#', '', '', 'dateFinAudit', false, false, 150, '', 'Date', '100%', '', 'left;width:100%;', false) . '</td>
								</tr>
								<tr>
									<td ><label for="nomEntreprise">' . __('nom de l\'entreprise', 'evarisk') . '</label></td>
									<td >' . EvaDisplayInput::afficherInput('text', 'nomEntreprise', '#NOMENTREPRISE#', '', '', 'nomEntreprise', false, false, 150, '', '', '100%', '', 'left;width:100%;', false) . '</td>
								</tr>
								<tr>
									<td ><label for="telephoneFixe">' . __('t&eacute;l&eacute;phone fixe', 'evarisk') . '</label></td>
									<td >' . EvaDisplayInput::afficherInput('text', 'telephoneFixe', '#TELFIXE#', '', '', 'telephoneFixe', false, false, 150, '', '', '100%', '', 'left;width:100%;', false) . '</td>
								</tr>
								<tr>
									<td ><label for="telephonePortable">' . __('t&eacute;l&eacute;phone portable', 'evarisk') .'</label></td>
									<td >' . EvaDisplayInput::afficherInput('text', 'telephonePortable', '#TELPORTABLE#', '', '', 'telephonePortable', false, false, 150, '', '', '100%', '', 'left;width:100%;', false) . '</td>
								</tr>
								<tr>
									<td ><label for="numeroFax">' . __('num&eacute;ro de fax', 'evarisk') . '</label></td>
									<td >' . EvaDisplayInput::afficherInput('text', 'numeroFax', '#TELFAX#', '', '', 'numeroFax', false, false, 150, '', '', '100%', '', 'left;width:100%;', false) . '</td>
								</tr>
								<tr>
									<td ><label for="emetteur">' . __('&eacute;metteur', 'evarisk') . '</label></td>
									<td >' . EvaDisplayInput::afficherInput('text', 'emetteur', '#EMETTEUR#', '', '', 'emetteur', false, false, 150, '', '', '100%', '', 'left;width:100%;', false) . '</td>
								</tr>
								<tr>
									<td ><label for="destinataire">' . __('destinataire', 'evarisk') . '</label></td>
									<td >' . EvaDisplayInput::afficherInput('text', 'destinataire', '#DESTINATAIRE#', '', '', 'destinataire', false, false, 150, '', '', '100%', '', 'left;width:100%;', false) . '</td>
								</tr>
								<tr>
									<td ><label for="nomDuDocument">' . __('nom du document', 'evarisk') . '</label></td>
									<td >' . EvaDisplayInput::afficherInput('text', 'nomDuDocument', '#NOMDOCUMENT#', '', '', 'nomDuDocument', false, false, 150, '', '', '100%', '', 'left;width:100%;', false) . '</td>
								</tr>
		<!-- 
								<tr>
									<td  colspan="2" ><label for="methodologie">' . __('m&eacute;thodologie', 'evarisk') . '</label></td>
								</tr>
								<tr>
									<td colspan="2" style="text-align:center;"><textarea id="methodologie" name="methodologie" class="textarea">#METHODOLOGIE#</textarea></td>
								</tr>
								<tr>
									<td  ><label for="sources">' . __('sources', 'evarisk') . '</label></td>
									<td style="text-align:center;"><textarea id="sources" name="sources" class="textarea14" style="width:100%" ;>#SOURCES#</textarea></td>
								</tr> 
		-->
								<tr>
									<td colspan="2"><input type="button" id="genererDUER" name="genererDUER" value="' . __('g&eacute;n&eacute;rer', 'evarisk') . '" /></td>
								</tr>
							</table>
						</td>
						<td style="width:40%;" ><div style="float:right;width:80%;" id="documentUniqueResult" >&nbsp;</div></td>
					</tr>
				</table>' .
			EvaDisplayInput::fermerForm('infosGenerationDU');
	}

}