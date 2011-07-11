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
require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/documentUnique/documentUnique.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'danger/categorieDangers/categorieDangers.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'danger/danger/evaDanger.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'veilleReglementaire/evaQuestion.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'veilleReglementaire/evaGroupeQuestion.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/tache/evaTask.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'evaNotes.class.php' );

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
	static function afficherDebutPage($titrePage, $icone, $titreIcone, $altIcon, $table, $boutonAjouter=true, $messageInfo='', $choixAffichage=false, $affichageNotes = true)
	{
		$debutPage = '<div class="wrap">
			<div class="icon32"><img alt="' . $altIcon . '" src="' . $icone . '"title="' . $titreIcone . '"/></div>
			<h2 class="alignleft" >' . $titrePage;
		if($boutonAjouter)
		{
			$debutPage .= ' <a class="button add-new-h2" onclick="javascript:document.getElementById(\'act\').value=\'add\'; document.forms.form.submit();">' . __('Ajouter', 'evarisk') . '</a>';
		}
		$debutPage .= '
		</h2>
';
		if($affichageNotes)
		{
			$debutPage .= evaNotes::noteDialogMaker() . '
		<div id="champsCaches" class="clear" ></div>
		<script type="text/javascript">
			evarisk(document).ready(function(){
				setTimeout(function(){
						evarisk("#message").hide();
				}, 10000);

' . evaNotes::noteDialogScriptMaker() . '
			});
		</script>';
		}
		if($choixAffichage)
		{
			$racine = Arborescence::getRacine($table);
			$idPere = $racine->id;
			$debutPage = $debutPage . '<script type="text/javascript">
				evarisk(document).ready(function() {
					evarisk(\'#affichageTable\').click(function() {
						evarisk(\'#affichageListe\').removeClass(\'selectedAffichage\');
						evarisk(\'#affichageTable\').addClass(\'selectedAffichage\');
						evarisk(\'#identifiantActuellemainPostBox\').val(1);
						evarisk("#pagemainPostBoxReference").val(1);
						
						while(evarisk(\'#filAriane :last-child\').attr("id") != "element1")
						{
							evarisk(\'#filAriane :last-child\').remove();
						}

						changementPage("right", "' . $table . '", evarisk("#pagemainPostBoxReference").val(), evarisk("#identifiantActuellemainPostBox").val(), "affichageTable", "main");
						changementPage("left", "' . $table . '", evarisk("#pagemainPostBoxReference").val(), evarisk("#identifiantActuellemainPostBox").val(), "affichageTable", "main");
					});
					evarisk(\'#affichageListe\').click(function() {
						evarisk(\'#affichageTable\').removeClass(\'selectedAffichage\');
						evarisk(\'#affichageListe\').addClass(\'selectedAffichage\');
						evarisk(\'#identifiantActuellemainPostBox\').val(1);
						evarisk("#pagemainPostBoxReference").val(1);

						changementPage("right", "' . $table . '", evarisk("#pagemainPostBoxReference").val(), evarisk("#identifiantActuellemainPostBox").val(), "affichageListe", "main");
						changementPage("left", "' . $table . '", evarisk("#pagemainPostBoxReference").val(), evarisk("#identifiantActuellemainPostBox").val(), "affichageListe", "main");
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
		$debutPage .= '<div id="message" class="fade below-h2 evaMessage">' . $messageInfo . '</div>';
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
		$filAriane = '<div ' . $display . ' id="filAriane"><a href="#" id="element1">' . $titreFilAriane . '</a></div>';
		$script = '<script type="text/javascript">
				evarisk(document).ready(function() {
					evarisk(\'#element1\').click(function() {
						evarisk("#equilize").click();
												evarisk(\'#identifiantActuelle' . $idPostBox . '\').val(1);
						evarisk(\'#page' . $idPostBox . 'Reference\').val(1);
						while(evarisk(\'#filAriane :last-child\').attr("id") != "element1")
						{
							evarisk(\'#filAriane :last-child\').remove();
						}
						changementPage("right", "' . $table . '", evarisk("#page' . $idPostBox . 'Reference").val(), evarisk("#identifiantActuelle' . $idPostBox . '").val(), "affichageTable", "main");
						changementPage("left", "' . $table . '", evarisk("#page' . $idPostBox . 'Reference").val(), evarisk("#identifiantActuelle' . $idPostBox . '").val(), "affichageTable", "main");
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
		evarisk(document).ready(function() {
			evarisk(\'#identifiantActuelle' . $idPostBox . '\').val(' . $element->id . ');
			evarisk(\'#page' . $idPostBox . 'Reference\').val(1);
			evarisk(\'#' . $idPostBox . ' h3 span\').html("' . addslashes($element->nom) . '");
			
			evarisk(\'#filAriane :last-child\').after(\'<label>&nbsp;&raquo;&nbsp;&Eacute;dition&nbsp;de&nbsp;' . addslashes($element->nom) . '</label>\');
			evarisk(\'#element' . $element->id . '\').click(function() {
				evarisk(\'#identifiantActuelle' . $idPostBox . '\').val("' . $element->id . '");
				evarisk(\'#page' . $idPostBox . 'Reference\').val(1);
				while(evarisk(\'#filAriane :last-child\').attr("id") != "element' . $element->id . '")
				{
					evarisk(\'#filAriane :last-child\').remove();
				}					
				changementPage("right", "' . $table . '", evarisk("#page' . $idPostBox . 'Reference").val(), evarisk("#identifiantActuelle' . $idPostBox . '").val(), "affichageTable", "main");
				changementPage("left", "' . $table . '", evarisk("#page' . $idPostBox . 'Reference").val(), evarisk("#identifiantActuelle' . $idPostBox . '").val(), "affichageTable", "main");
				return false;
			});
		});
	</script>';
					}
				}
				$script = $script . '
<script type="text/javascript">
	evarisk(document).ready(function() {
		evarisk(\'#element' . $element->id . '\').click();
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
				evarisk(document).ready(function() {
					evarisk(\'#leftEnlarging' . $numero . '\').click(function() {
						evarisk(\'#partieEdition' . $numero . '\').hide();
						evarisk(\'#partieGauche' . $numero . '\').show();
						evarisk(\'#partieGauche' . $numero . '\').css(\'width\', \'98%\');
						adminMenu.fold();
						evarisk("#enlarging' . $numero . ' .ui-slider-range").css("width","100%");
						evarisk("#enlarging' . $numero . ' .ui-slider-handle").css("left","100%");
					});
					evarisk(\'#rightEnlarging' . $numero . '\').click(function() {
						evarisk(\'#partieGauche' . $numero . '\').hide();
						evarisk(\'#partieEdition' . $numero . '\').show();
						evarisk(\'#partieEdition' . $numero . '\').css(\'width\', \'98%\');
						adminMenu.fold();
						evarisk("#enlarging' . $numero . ' .ui-slider-range").css("width","0%");
						evarisk("#enlarging' . $numero . ' .ui-slider-handle").css("left","0%");
					});
					evarisk(\'#equilize' . $numero . '\').click(function() {
						evarisk(\'#partieGauche' . $numero . '\').show();
						evarisk(\'#partieEdition' . $numero . '\').show();
						evarisk(\'#partieEdition' . $numero . '\').css(\'width\', \'49%\');
						evarisk(\'#partieGauche' . $numero . '\').css(\'width\', \'49%\');
						evarisk("#enlarging' . $numero . ' .ui-slider-range").css("width","50%");
						evarisk("#enlarging' . $numero . ' .ui-slider-handle").css("left","50%");
					});
				});
			</script>';
						
						
		$script = '<script type="text/javascript">
				evarisk(document).ready(function() {
					evarisk("#enlarging' . $numero . ' .ui-slider-horizontal").css("width","100px");
					evarisk("#enlarging' . $numero . '").slider({
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
							evarisk(\'#partieEdition' . $numero . '\').show();
							evarisk(\'#partieGauche' . $numero . '\').show();
							evarisk("#partieGauche' . $numero . '").css("width", largeurGauche  + "%");
							evarisk("#partieEdition' . $numero . '").css("width", largeurDroite  + "%");
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
				evarisk(document).ready(function() {
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
	evarisk(document).ready(function() {
		evarisk(\'#' . $idPostBox . 'Fleche\').click(function() {
			evarisk(\'#' . $idPostBox . '\').toggleClass(\'closed\');
		});
		if(document.getElementById(\'filAriane\').lastChild == document.getElementById(\'filAriane\').firstChild)
		{
			evarisk(\'#' . $idPostBox . 'Pere\').addClass(\'hidden\');
		}
		else
		{
			evarisk(\'#' . $idPostBox . 'Pere\').removeClass(\'hidden\');
		}
		evarisk(\'#' . $idPostBox . 'Pere\').click(function() {
			if(evarisk(\'#filAriane :last-child\') != evarisk(\'#filAriane :first-child\'))
			{
				evarisk(\'#page' . $idPostBox . 'Reference\').val(1);
				if(evarisk(\'#filAriane :last-child\').is("label"))
				{
					evarisk(\'#filAriane :last-child\').remove();
				}
				evarisk(\'#filAriane :last-child\').remove();
				evarisk(\'#filAriane :last-child\').remove();
				evarisk(\'#' . $idPostBox . ' h3 span\').html(evarisk(\'#filAriane :last-child\').html());
				var id = evarisk(\'#filAriane :last-child\').attr("id");
				var reg = new  RegExp("(element)", "g");
				var id = id.replace(reg, "");
				evarisk(\'#identifiantActuelle' . $idPostBox . '\').val(id);

				changementPage("right", "' . $table . '", evarisk("#page' . $idPostBox . 'Reference").val(), evarisk("#identifiantActuelle' . $idPostBox . '").val(), "affichageTable", "main");
				changementPage("left", "' . $table . '", evarisk("#page' . $idPostBox . 'Reference").val(), evarisk("#identifiantActuelle' . $idPostBox . '").val(), "affichageTable", "main");
				return false;
			}
		});
		if(document.getElementById(\'favorite-inside-' . $idPostBox . '\') != null)
		{
			document.getElementById(\'favorite-inside-' . $idPostBox . '\').style.width = evarisk(\'#favorite-actions-' . $idPostBox . '\').innerWidth() -4 + "px";
		}
		evarisk(\'#favorite-toggle-' . $idPostBox . '\').hover(function() {
			evarisk(\'#favorite-first-' . $idPostBox . '\').addClass("slide-down");
			evarisk(\'#favorite-inside-' . $idPostBox . '\').addClass("slideDown");
			evarisk(\'#favorite-inside-' . $idPostBox . '\').slideDown(100);
		});
		evarisk(\'#favorite-first-' . $idPostBox . '\').click(function() {
			evarisk(\'#favorite-first-' . $idPostBox . '\').addClass("slide-down");
			evarisk(\'#favorite-inside-' . $idPostBox . '\').addClass("slideDown");
			evarisk(\'#favorite-inside-' . $idPostBox . '\').slideDown(100);
		});
		var timeoutFavoriteActions;
		evarisk(\'#favorite-actions-' . $idPostBox . '\').hover(function() {
			clearTimeout(timeoutFavoriteActions);
		},function() {
			timeoutFavoriteActions = setTimeout 
			( 
				function() 
				{ 
					//document.getElementById(\'favorite-inside-' . $idPostBox . '\').style.display = "none";
					evarisk(\'#favorite-inside-' . $idPostBox . '\').slideUp(100);
					setTimeout 
					( 
						function() 
						{ 
							evarisk(\'#favorite-first-' . $idPostBox . '\').removeClass("slide-down");
							evarisk(\'#favorite-inside-' . $idPostBox . '\').removeClass("slideDown");
						}, 
						100
					);
				}, 
				500 
			);
		});
		
		evarisk(\'#favorite-first-link-' . $idPostBox . '\').click(function() {
			evarisk("#rightEnlarging").show();
			evarisk("#equilize").click();
			
			evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
				"table": "' . $table . '",
				"act": "add",
				"page": evarisk(\'#page' . $idPostBox . 'Reference\').val(),
				"idPere": evarisk(\'#identifiantActuelle' . $idPostBox . '\').val(),
				"partie": "right",
				"menu": evarisk("#menu").val(),
				"affichage": "affichageTable",
				"partition": "tout"
			});
			evarisk("#partieGauche").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
				"table": "' . $table . '",
				"act": "add",
				"page": evarisk(\'#page' . $idPostBox . 'Reference\').val(),
				"idPere": evarisk(\'#identifiantActuelle' . $idPostBox . '\').val(),
				"partie": "left",
				"menu": evarisk("#menu").val(),
				"affichage": "affichageTable",
				"partition": "tout"
			});
			
			evarisk("#partieEdition").html(evarisk("#loadingImg").html());
			evarisk("#partieGauche").html(evarisk("#loadingImg").html());
			return false;
		});
		
		evarisk(\'#favorite-second-link-' . $idPostBox . '\').click(function() {
			evarisk("#rightEnlarging").show();
			evarisk("#equilize").click();
			
			evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
				"table": "' . $table2 . '",
				"act": "add",
				"page": evarisk(\'#page' . $idPostBox . 'Reference\').val(),
				"idPere": evarisk(\'#identifiantActuelle' . $idPostBox . '\').val(),
				"partie": "right",
				"menu": evarisk("#menu").val(),
				"affichage": "affichageTable",
				"partition": "tout"
			});
			evarisk("#partieGauche").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
				"table": "' . $table2 . '",
				"act": "add",
				"page": evarisk(\'#page' . $idPostBox . 'Reference\').val(),
				"idPere": evarisk(\'#identifiantActuelle' . $idPostBox . '\').val(),
				"partie": "left",
				"menu": evarisk("#menu").val(),
				"affichage": "affichageTable",
				"partition": "tout"
			});
			
			evarisk("#partieEdition").html(evarisk("#loadingImg").html());
			evarisk("#partieGauche").html(evarisk("#loadingImg").html());
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
						<h3 class="handle"><span>' . $titrePostBox . '</span></h3>
						<div class="inside" id="' . $idPostBox . 'Container" >' . $corpsPostBox . '</div>
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
	evarisk(document).ready(function() {
		evarisk(\'#element1\').unbind("click");
		evarisk(\'#element1\').click(function() {
			evarisk(\'#identifiantActuelle' . $id . '\').val(1);
			evarisk(\'#page' . $id . 'Reference\').val(1);
			while(evarisk(\'#filAriane :last-child\').attr("id") != "element1")
			{
				evarisk(\'#filAriane :last-child\').remove();
			}

			changementPage("right", "' . $table . '", evarisk("#page' . $id . 'Reference").val(), evarisk("#identifiantActuelle' . $id . '").val(), "affichageTable", "main");
			changementPage("left", "' . $table . '", evarisk("#page' . $id . 'Reference").val(), evarisk("#identifiantActuelle' . $id . '").val(), "affichageTable", "main");
			evarisk(\'#' . $id . ' h3 span\').html(evarisk(\'#filAriane :first-child\').html());
			return false;
		});
	});
	evarisk(document).ready(function() {	
		evarisk(\'#page' . $id . '\').keypress(function(event) {
			if (event.which && (event.which < 48 || event.which >57) && event.which != 8 && event.which != 13) {
				event.preventDefault();
			}
		});
		evarisk(\'#page' . $id . '\').keyup(function() {
			evarisk(\'#page' . $id . 'Reference\').val(evarisk(\'#page' . $id . '\').val())
		});
		
		var page = evarisk(\'#page' . $id . 'Reference\').val();
		evarisk(\'#page' . $id . '\').val(page);
		evarisk(\'#formPagination' . $id . '\').click(function(event) {
			if(evarisk(event.target).is(\'.button\'))
			{
				if(evarisk("#filAriane :last-child").is("label"))
					evarisk("#filAriane :last-child").remove();
				switch((event.target).id)
				{
					case "first' . $id . '":
						page = 1;
						break;
					case "previous' . $id . '":
						page = parseInt(evarisk(\'#page' . $id . 'Reference\').val()) - 1;
						if(page < 1)
						{
							page = 1;	
						}
						break;
					case "next' . $id . '":
						page = parseInt(evarisk(\'#page' . $id . 'Reference\').val()) + 1;
						if(page > evarisk(\'#pageMax' . $id . '\').value)
						{
							page = evarisk(\'#pageMax' . $id . '\').val();	
						}
						break;
					case "last' . $id . '":
						page = parseInt(evarisk(\'#pageMax' . $id . '\').val());
						break;
					case "text' . $id . '":
						if(parseInt(evarisk(\'#page' . $id . 'Reference\').val()) > parseInt(evarisk(\'#pageMax' . $id . '\').val()))
						{
							page = parseInt(evarisk(\'#pageMax' . $id . '\').val());
						}
						else
						{
							if(parseInt(evarisk(\'#page' . $id . 'Reference\').val()) < 1)
							{
								page = 1;	
							}
							else
							{
								if(evarisk(\'#page' . $id . 'Reference\').val() != "")
								{
									page = parseInt(evarisk(\'#page' . $id . 'Reference\').val());
								}
							}
						}
						break;
				}
				evarisk(\'#page' . $id . 'Reference\').val(page);

				changementPage("right", "' . $table . '", evarisk("#page' . $id . 'Reference").val(), evarisk("#identifiantActuelle' . $id . '").val(), "affichageTable", "main");
				changementPage("left", "' . $table . '", evarisk("#page' . $id . 'Reference").val(), evarisk("#identifiantActuelle' . $id . '").val(), "affichageTable", "main");

				evarisk(\'#page' . $id . '\').val(page);
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
	function getTableArborescence($racine, $table, $idTable, $nomRacine, $draggable = true, $outputAction = true)
	{
		$elements = $monCorpsTable = $class = $infoRacine = '';
		switch($table)
		{
			case TABLE_GROUPEMENT:
			{
				$elements = Arborescence::getFils($table, $racine, "nom ASC");
				$sousTable = TABLE_UNITE_TRAVAIL;
				$subElements = EvaGroupement::getUnitesDuGroupement($racine->id);
				$divDeChargement = 'message';
				$titreInfo = __("Somme des risques", 'evarisk');
				$actionSize = 5;
				$actions = '
							<td class="noPadding addMain" id="addMain' . $racine->id . '">';
				if(current_user_can('digi_add_groupement'))
				{
					$actions .= 
							'<img style="width:' . TAILLE_PICTOS_ARBRE . ';"  src="' . PICTO_LTL_ADD_GROUPEMENT . '" alt="' . sprintf(__('Ajouter %s', 'evarisk'), __('un groupement', 'evarisk')) . '" title="' . sprintf(__('Ajouter %s', 'evarisk'), __('un groupement', 'evarisk')) . '" />';
				}
				else
				{
					$actions .= '&nbsp;';
				}
				$actions .= '
							</td>';
			}
			break;
			case TABLE_CATEGORIE_DANGER:
			{
				$elements = Arborescence::getFils($table, $racine, "nom ASC");
				$sousTable = TABLE_DANGER;
				$subElements = categorieDangers::getDangersDeLaCategorie($racine->id);
				$divDeChargement = 'message';
				$titreInfo = null;
				$actionSize = 4;
				$actions = '
							<td class="noPadding addMain" id="addMain' . $racine->id . '">';
				if(current_user_can('digi_add_danger_category'))
				{
					$actions .= '<img style="width:' . TAILLE_PICTOS_ARBRE . ';" src="' . PICTO_LTL_ADD_CATEGORIE_DANGER . '" alt="' . sprintf(__('Ajouter %s', 'evarisk'), __('une cat&eacute;gorie de dangers', 'evarisk')) . '" title="' . sprintf(__('Ajouter %s', 'evarisk'), __('une cat&eacute;gorie de dangers', 'evarisk')) . '" />';
				}
				else
				{
					$actions .= '&nbsp;';
				}
				$actions .= '</td>';
				$addMainPicture = '<img style=\'width:' . TAILLE_PICTOS_ARBRE . ';\' src=\'' .PICTO_LTL_ADD_CATEGORIE_DANGER . '\' alt=\'' . sprintf(__('Ajouter %s', 'evarisk'), __('une cat&eacute;gorie de dangers', 'evarisk')) . '\' title=\'' . sprintf(__('Ajouter %s', 'evarisk'), __('une cat&eacute;gorie de dangers', 'evarisk')) . '\' />';
				$addSecondaryPicture = '<img style=\'width:' . TAILLE_PICTOS_ARBRE . ';\' src=\'' .PICTO_LTL_ADD_DANGER . '\' alt=\'' . sprintf(__('Ajouter %s', 'evarisk'), __('un danger', 'evarisk')) . '\' title=\'' . sprintf(__('Ajouter %s', 'evarisk'), __('un danger', 'evarisk')) . '\' />';
			}
			break;
			case TABLE_TACHE:
			{
				$elements = Arborescence::getFils($table, $racine, "nom ASC");
				$sousTable = TABLE_ACTIVITE;
        $tacheRacine = new EvaTask($racine->id);
        $tacheRacine->load();
				$subElements = $tacheRacine->getWPDBActivitiesDependOn();
				$divDeChargement = 'message';
				$titreInfo = __("Avancement", 'evarisk');
				$actionSize = 4;
				$actions = '
							<td class="noPadding addMain" id="addMain' . $racine->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';" src="' . PICTO_LTL_ADD_TACHE . '" alt="' . sprintf(__('Ajouter %s', 'evarisk'), __('une t&acirc;che', 'evarisk')) . '" title="' . sprintf(__('Ajouter %s', 'evarisk'), __('une t&acirc;che', 'evarisk')) . '" /></td>
							<!-- <td class="noPadding addSecondary" id="addSecondary' . $racine->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';" src="' . PICTO_LTL_ADD_ACTIVITE . '" alt="' . sprintf(__('Ajouter %s', 'evarisk'), __('une action', 'evarisk')) . '" title="' . sprintf(__('Ajouter %s', 'evarisk'), __('une action', 'evarisk')) . '" /></td> -->';
				$addMainPicture = '<img style=\'width:' . TAILLE_PICTOS_ARBRE . ';\' src=\'' .PICTO_LTL_ADD_TACHE . '\' alt=\'' . sprintf(__('Ajouter %s', 'evarisk'), __('une t&acirc;che', 'evarisk')) . '\' title=\'' . sprintf(__('Ajouter %s', 'evarisk'), __('une t&acirc;che', 'evarisk')) . '\' />';
				$addSecondaryPicture = '<img style=\'width:' . TAILLE_PICTOS_ARBRE . ';\' src=\'' .PICTO_LTL_ADD_ACTIVITE . '\' alt=\'' . sprintf(__('Ajouter %s', 'evarisk'), __('une action', 'evarisk')) . '\' title=\'' . sprintf(__('Ajouter %s', 'evarisk'), __('une action', 'evarisk')) . '\' />';
			}
			break;
			case TABLE_GROUPE_QUESTION:
			{
				$elements = Arborescence::getFils($table, $racine, "code ASC");
				$sousTable = TABLE_QUESTION;
				$subElements = EvaGroupeQuestions::getQuestionsDuGroupeQuestions($racine->id);
				$divDeChargement = 'ajax-response';
				$titreInfo = null;
				$actionSize = 3;
				$actions = '
							<td class="noPadding addMain" id="add-node-' . $racine->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';" src="' . PICTO_INSERT . '" alt="' . __('Inserer sous le titre', 'evarisk') . '" title="' . __('Inserer sous le titre', 'evarisk') . '" /></td>
							<td class="noPadding addSecondary" id="edit-node-' . $racine->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';display:none;" id="img_edit_racine" src="' . PICTO_EDIT . '" alt="' . __('Modifier le titre', 'evarisk') . '" title="' . __('Modifier le titre', 'evarisk') . '" /></td>
							<td></td>';
				$addMainPicture = '<img style=\'width:' . TAILLE_PICTOS_ARBRE . ';\' src=\'' .PICTO_INSERT . '\' alt=\'' . __('Inserer sous le titre', 'evarisk') . '\' title=\'' . __('Inserer sous le titre', 'evarisk') . '\' />';
				$addSecondaryPicture = '';
			}
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
		}

		$draggableScript = '';
		if($draggable)
		{
		$draggableScript .= '
		//	Draggable interface
		var draggedObject;
		var draggedObjectFather;
	
		// Configure draggable nodes
		evarisk("#' . $idTable . ' .noeudArbre, #' . $idTable . ' .feuilleArbre").draggable({
			start: function(event, ui) {
				draggedObject = event.target.id;
				var classNames = event.target.className.split(\' \');
				draggedObjectFather = "temp";
				for(key in classNames) {
					if(classNames[key].match("child-of-")) {
						draggedObjectFather = evarisk("#" + classNames[key].substring(9));
						draggedObjectFather = draggedObjectFather.attr("id");
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
		
		var dropFunction = function(event, ui){
			// Call jQuery treeTable plugin to move the branch
			evarisk(evarisk(ui.draggable)).appendBranchTo(this);
			var dropLocation = event.target.id;

			evarisk("#equilize").click();
			evarisk("#' . $divDeChargement . '").addClass("updated");
			evarisk("#' . $divDeChargement . '").html("' . __('Transfert en cours...', 'evarisk') . '");
			evarisk("#' . $divDeChargement . '").show();
			evarisk("#' . $divDeChargement . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",{
				"post":"true",
				"table":"' . $table . '",
				"location":"' . $idTable . '",
				"act":"transfert",
				"idElementSrc":draggedObject,
				"idElementOrigine":draggedObjectFather,
				"idElementDest":dropLocation
			});
			setTimeout(
				function(){
					evarisk("#' . $idTable . ' tr.parent").each(function(){
						var childNodes = evarisk("table#' . $idTable . ' tbody tr.child-of-" + evarisk(this).attr("id"));
						if(childNodes.length > 0){
							evarisk(this).removeClass("sansFils");
							evarisk(this).addClass("aFils");
							var premierFils = evarisk("table#' . $idTable . ' tbody tr.child-of-" + evarisk(this).attr("id") + ":first").attr("id");
							if(premierFils != premierFils.replace(/node-' . $idTable . '-/g,"")){
								evarisk(this).addClass("aFilsNoeud");
								evarisk(this).droppable( "option", "accept", ".noeudArbre" );
								evarisk("#' . $idTable . ' #addSecondary" + evarisk(this).attr("id").replace(/node-' . $idTable . '-/g,"") + " img").hide();
								evarisk("#' . $idTable . ' #addMain" + evarisk(this).attr("id").replace(/node-' . $idTable . '-/g,"") + " img").show();
							}
							else{
								evarisk(this).addClass("aFilsFeuille");
								evarisk(this).droppable( "option", "accept", ".feuilleArbre" );
								evarisk("#' . $idTable . ' #addMain" + evarisk(this).attr("id").replace(/node-' . $idTable . '-/g,"") + " img").hide();
								evarisk("#' . $idTable . ' #addSecondary" + evarisk(this).attr("id").replace(/node-' . $idTable . '-/g,"") + " img").show();
							}
						}
						else{
							evarisk(this).removeClass("aFilsNoeud");
							evarisk(this).removeClass("aFilsFeuille");
							evarisk(this).removeClass("aFils");
							evarisk(this).addClass("sansFils");
							evarisk(this).droppable("option", "accept", ".noeudArbre, .feuilleArbre");
							evarisk("#' . $idTable . ' #addSecondary" + evarisk(this).attr("id").replace(/node-' . $idTable . '-/g,"") + " img").show();
							evarisk("#' . $idTable . ' #addMain" + evarisk(this).attr("id").replace(/node-' . $idTable . '-/g,"") + " img").show();
						}
					});
					evarisk(document).ajaxStop(function(){
						evarisk("#' . $divDeChargement . '").removeClass("updated");
					});
				}, 
				10 
			);
		}

		// evarisk("#tdRacine' . $idTable . '").droppable({
			// "over":function(){
				// evarisk(this).html("' . __('D&eacute;poser ici pour mettre &agrave; la racine de votre arborescence', 'evarisk') . '");
			// },
			// "out":function(){
				// evarisk(this).html("");
			// },
			// "drop":function(event, ui){
				// evarisk(this).html("");
			// }
		// });

		overFunction = function(event, ui){
			// Make the droppable branch expand when a draggable node is moved over it.
			if(this.id != evarisk(ui.draggable.parents("tr")[0]).id && !evarisk(this).is(".expanded")){
				var overObject = evarisk(this);
				setTimeout(function(){
					if(overObject.is(".accept")){
						overObject.expand();
					}
				}, 
					500 
				);
		  }
		}
		evarisk("#' . $idTable . ' .aFilsNoeud, #' . $idTable . ' .racineArbre").droppable({
			accept: "#' . $idTable . ' .noeudArbre",
			drop: dropFunction,
			hoverClass: "accept",
			over: overFunction			  
		});
		evarisk("#' . $idTable . ' .aFilsFeuille").droppable({
			accept: "#' . $idTable . ' .feuilleArbre",
			drop: dropFunction,
			hoverClass: "accept",
			over: overFunction
		});
		evarisk("#' . $idTable . ' .sansFils").droppable({
			accept: "#' . $idTable . ' .feuilleArbre, #' . $idTable . ' .noeudArbre",
			drop: dropFunction,
			hoverClass: "accept",
			over: overFunction
		});';
		}

		$tableArborescente .= '
				<table id="' . $idTable . '" cellspacing="0" class="widefat post fixed">
					<thead>
						<tr>
							<th >' . $nomRacine . '</th>';
		if($titreInfo != null)
		{
			$tableArborescente .= '<th class="infoList">' . $titreInfo . '</th>';
			$infoRacine = EvaDisplayDesign::getInfoArborescence($table, $racine->id);
			$infoRacine = '
							<td id ="info-' . $racine->id . '" class="' . $infoRacine['class'] . '"></td>';
		}
		if($outputAction)
		{
			$tableArborescente .= '
							<th colspan="' . $actionSize . '" class="actionButtonList">' . __('Actions', 'evarisk') . '</th>';
		}
			$tableArborescente .= '
						</tr>
					</thead>
					<tbody>
						<tr id="node-' . $idTable . '-' . $racine->id . '" class="parent racineArbre">
							<td id="tdRacine' . $idTable . '">&nbsp;</td>' . $infoRacine;
		if($outputAction)
		{
			$tableArborescente .= $actions;
		}
		$tableArborescente .= '
						</tr>
						' . $monCorpsTable . '
					</tbody>
				</table>';

		$script = '
<script type="text/javascript">
	evarisk(document).ready(function(){
		//	Change the simple table in treetable
		evarisk("#' . $idTable . '").treeTable();	
		selectRowInTreeTable("' . $idTable . '");

		var span = document.getElementById("tdRacine' . $idTable . '").firstChild;
		evarisk("#' . $idTable . ' #node-' . $idTable . '-' . $racine->id . '").toggleBranch();
		document.getElementById("tdRacine' . $idTable . '").removeChild(span);

		evarisk("#' . $idTable . ' tr.parent").each(function(){
			var childNodes = evarisk("table#' . $idTable . ' tbody tr.child-of-" + evarisk(this).attr("id"));
			if(childNodes.length > 0){
				evarisk(this).addClass("aFils");				
				var premierFils = evarisk("table#' . $idTable . ' tbody tr.child-of-" + evarisk(this).attr("id") + ":first").attr("id");
				if(premierFils != premierFils.replace(/node/g,"")){
					evarisk(this).addClass("aFilsNoeud");
				}
				else{
					evarisk(this).addClass("aFilsFeuille");
				}
			}
			else{
				evarisk(this).removeClass("aFils");
				evarisk(this).addClass("sansFils");
			}
		});

		' . $draggableScript . '

		evarisk("#' . $idTable . ' .addMain img").click(function(){
			var nodeId = evarisk(this).parent("td").parent("tr").attr("id").replace("node-' . $idTable . '-", "");
			var expanded = reInitTreeTable();';
		if($table == TABLE_GROUPEMENT)
		{
		$script .= '
			evarisk("#menu").val("gestiongrptut");';
		}
			$script .= '
			evarisk("#partieEdition").html(evarisk("#loadingImg").html());
			evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
			{
				"post": "true", 
				"table": "' . $table . '",
				"act": "add",
				"page": evarisk("#pagemainPostBoxReference").val(),
				"idPere": nodeId,
				"partie": "right",
				"menu": evarisk("#menu").val(),
				"affichage": "affichageListe",
				"partition": "tout",
				"expanded": expanded
			});
		});

		evarisk("#' . $idTable . ' .addSecondary img").click(function(){
			var nodeId = evarisk(this).parent("td").parent("tr").attr("id").replace("node-' . $idTable . '-", "");
			var expanded = reInitTreeTable();';
		if($table == TABLE_GROUPEMENT)
		{
		$script .= '
			evarisk("#menu").val("gestiongrptut");';
		}
			$script .= '
			evarisk("#partieEdition").html(evarisk("#loadingImg").html());
			evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
			{
				"post": "true", 
				"table": "' . $sousTable . '",
				"act": "add",
				"page": evarisk("#pagemainPostBoxReference").val(),
				"idPere": nodeId,
				"partie": "right",
				"menu": evarisk("#menu").val(),
				"affichage": "affichageListe",
				"partition": "tout",
				"expanded": expanded
			});
		});';

		if($table == TABLE_GROUPE_QUESTION)
		{
			$script .= '

		evarisk("#' . $idTable . ' #add-node-' . $racine->id . '").click(function(){
			for (var i=0;i<document.regulatoryWatchForm.titrePere.options.length;i++) 
			{
				if (document.regulatoryWatchForm.titrePere.options[i].value == ' . $racine->id . ')
					document.regulatoryWatchForm.titrePere.options[i].selected = true;
			}
			evarisk("#traiter").click();
		});';
		}

		switch($table)
		{
			case TABLE_CATEGORIE_DANGER :
			case TABLE_GROUPEMENT :
			case TABLE_TACHE :
			{	/*	Tree leaf	*/
				$script .= '
		//	The user click on the delete button of a leaf
		evarisk("#' . $idTable . ' .delete-leaf").click(function(){
			var leafId = evarisk(this).parent("tr").attr("id").replace("leaf-", "");
			evarisk("#menu").val("gestiongrptut");
			var expanded = reInitTreeTable();
			if(confirm("' . __('Etes vous sur de vouloir supprimer cet element?', 'evarisk') . '")){
				evarisk("#partieEdition").html("");
				evarisk("#partieGauche").html(evarisk("#loadingImg").html());
				evarisk("#partieGauche").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
				{
					"post": "true", 
					"table": "' . $sousTable . '",
					"act": "delete",
					"id": leafId,
					"partie": "left",
					"menu": evarisk("#menu").val(),
					"affichage": "affichageListe",
					"partition": "tout",
					"expanded": expanded
				});
			}
		});

		//	The user click on the edit button of a leaf
		evarisk("#' . $idTable . ' .edit-leaf").click(function(){
			var leafId = evarisk(this).parent("tr").attr("id").replace("leaf-", "");
			selectRowInTreeTable("' . $idTable . '");
			evarisk("#menu").val("gestiongrptut");
			var expanded = reInitTreeTable();
			evarisk("#partieEdition").html(evarisk("#loadingImg").html());
			evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
				"post": "true", 
				"table": "' . $sousTable . '",
				"act": "edit",
				"id": leafId,
				"partie": "right",
				"menu": evarisk("#menu").val(),
				"affichage": "affichageListe",
				"expanded": expanded
			});
		});

		//	The user click on the name of the leaf
		evarisk(".nomFeuilleArbre, .treeTableInfoColumn").click(function(){
			var leafId = evarisk(this).parent("tr").attr("id").replace("leaf-", "");
			selectRowInTreeTable("' . $idTable . '");
			evarisk("#menu").val("risq");
			var expanded = reInitTreeTable();
			evarisk("#partieEdition").html(evarisk("#loadingImg").html());
			evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
				"table": "' . $sousTable . '",
				"act": "edit",
				"id": leafId,
				"partie": "right",
				"menu": evarisk("#menu").val(),
				"affichage": "affichageListe",
				"expanded": expanded
			});
		});

		//	The user click on the edit button of a leaf
		evarisk("#' . $idTable . ' .risk-leaf").click(function(){
			var leafId = evarisk(this).parent("td").parent("tr").attr("id").replace("leaf-", "");
			selectRowInTreeTable("' . $idTable . '");
			evarisk("#menu").val("risq");
			var expanded = reInitTreeTable();
			evarisk("#partieEdition").html(evarisk("#loadingImg").html());
			evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
				"table": "' . $sousTable . '",
				"act": "edit",
				"id": leafId,
				"partie": "right",
				"menu": evarisk("#menu").val(),
				"affichage": "affichageListe",
				"expanded": expanded
			});
		});';
			}
			{	/*	Tree node	*/
				$script .= '
		//	The user click on the delete button of a node
		evarisk("#' . $idTable . ' .delete-node").click(function(){
			var nodeId = evarisk(this).parent("tr").attr("id").replace("node-' . $idTable . '-", "").replace("-name", "");
			evarisk("#menu").val("gestiongrptut");
			var expanded = reInitTreeTable();
			if(confirm("' . __('Etes vous sur de vouloir supprimer cet element?\r\nATTENTION: si cet element possede des sous elements, ils seront inaccessibles', 'evarisk') . '")){
				evarisk("#partieGauche").html(evarisk("#loadingImg").html());
				evarisk("#partieGauche").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
				{
					"post": "true", 
					"table": "' . $table . '",
					"act": "delete",
					"id": nodeId,
					"partie": "left",
					"menu": evarisk("#menu").val(),
					"affichage": "affichageListe",
					"partition": "tout",
					"expanded": expanded
				});
			}
		});
		//	The user click on the delete button of a node
		evarisk("#' . $idTable . ' .edit-node").click(function(){
			var nodeId = evarisk(this).parent("tr").attr("id").replace("node-' . $idTable . '-", "").replace("-name", "");
			selectRowInTreeTable("' . $idTable . '");
			evarisk("#menu").val("gestiongrptut");
			var expanded = reInitTreeTable();
			evarisk("#partieEdition").html(evarisk("#loadingImg").html());
			evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
			{
				"post": "true", 
				"table": "' . $table . '",
				"act": "edit",
				"id": nodeId,
				"partie": "right",
				"menu": evarisk("#menu").val(),
				"affichage": "affichageListe",
				"partition": "tout",
				"expanded": expanded
			});
		});
		//	The user click on the delete button of a node
		evarisk("#' . $idTable . ' .risq-node").click(function(){
			var nodeId = evarisk(this).parent("td").parent("tr").attr("id").replace("node-' . $idTable . '-", "").replace("-name", "");
			selectRowInTreeTable("' . $idTable . '");
			evarisk("#menu").val("risq");
			var expanded = reInitTreeTable();
			evarisk("#partieEdition").html(evarisk("#loadingImg").html());
			evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
			{
				"post": "true", 
				"table": "' . $table . '",
				"act": "edit",
				"id": nodeId,
				"partie": "right",
				"menu": evarisk("#menu").val(),
				"affichage": "affichageListe",
				"partition": "tout",
				"expanded": expanded
			});
		});
		//	The user click on the delete button of a node
		evarisk("#' . $idTable . ' .nomNoeudArbre, #' . $idTable . ' .treeTableGroupInfoColumn").click(function(e){
			if(!evarisk(e.target).hasClass("expander")){
				if(evarisk(e.target).hasClass("nomNoeudArbre")){
					var nodeId = evarisk(this).attr("id").replace("node-' . $idTable . '-", "").replace("-name", "");
				}
				else if(evarisk(e.target).hasClass("treeTableGroupInfoColumn")){
					var nodeId = evarisk(this).parent("tr").attr("id").replace("node-' . $idTable . '-", "").replace("-name", "");
				}
				selectRowInTreeTable("' . $idTable . '");
				evarisk("#menu").val("risq");
				var expanded = reInitTreeTable();
				evarisk("#partieEdition").html(evarisk("#loadingImg").html());
				evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
				{
					"post": "true", 
					"table": "' . $table . '",
					"act": "edit",
					"id": nodeId,
					"partie": "right",
					"menu": evarisk("#menu").val(),
					"affichage": "affichageListe",
					"partition": "tout",
					"expanded": expanded
				});
			}
		});';
			}
			break;
			case TABLE_QUESTION :
			if(false){	/*	Tree leaf	*/
				$script = '
				<script type="text/javascript">
					evarisk(document).ready(function()  
					{
						evarisk("#' . $idTable . ' #delete-leaf-' . $subElement->id . '").click(function(){
							var adresse = "' . EVA_INC_PLUGIN_URL . 'ajax.php?nom=' . $sousTable . '&id=' . $subElement->id . '&idPere=' . $elementPere->id . '&act=delete";
							evarisk("#ajax-response").html(evarisk("#loadingImg").html());
							evarisk("#ajax-response").load(adresse);
						});
					});
				</script>';
			}
			if(false){	/*	Tree node	*/
				$script = '
					<script type="text/javascript">
						evarisk(document).ready(function()  
						{
							evarisk("#' . $idTable . ' #delete-node-' . $element->id . '").click(function(){
								var adresse = "' . EVA_INC_PLUGIN_URL . 'ajax.php?nom=' . $table . '&id=' . $element->id . '&act=delete";
								evarisk("#ajax-response").html(evarisk("#loadingImg").html());
								evarisk("#ajax-response").load(adresse);
							});
							evarisk("#' . $idTable . ' #add-node-' . $element->id . '").click(function(){
								for (var i=0;i<document.regulatoryWatchForm.titrePere.options.length;i++) 
								{
									if (document.regulatoryWatchForm.titrePere.options[i].value == ' . $element->id . ')
										document.regulatoryWatchForm.titrePere.options[i].selected = true;
								}
								evarisk("#traiter").click();
							});
							evarisk("#' . $idTable . ' #edit-node-' . $element->id . '").click(function(){
								for (var i=0;i<document.regulatoryWatchForm.titrePere.options.length;i++) 
								{
									if (document.regulatoryWatchForm.titrePere.options[i].value == ' . $element->id . ')
										document.regulatoryWatchForm.titrePere.options[i].selected = true;
								}
								if(evarisk("#codeTitre").val() == "")
								{
									evarisk("#codeTitre").val(' . $element->code . ')
								}
								evarisk("#updateVeille").click();
							});
						});
					</script>';
			}
			break;
		}
		$script .= '
	
	});
</script>';

		return $tableArborescente . $script;
	}

	/**
	 * Returns the inner table of the list view tree with scripts that allow you to display the right part by clicking on the elements.
	 * This recursive function path tree from the father element to his leaves.
	 * @param array[Element_of_a_tree] $elementsFils Array of all the elements son of the father element.
	 * @param Element_of_a_tree $elementPere Father element.
	 * @param string $table Father element table name.
	 * @return string HTML code of the inner table.
	 */
	function getCorpsTableArborescence($elementsFils, $elementPere, $table, $titreInfo, $idTable)
	{
		$monCorpsTable = $monCorpsSubElements = '';
		$ddFeuilleClass = 'feuilleArbre';
		$nomFeuilleClass = 'nomFeuilleArbre';

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
			$ddFeuilleClass = 'feuilleArbre';
			$nomFeuilleClass = 'nomFeuilleArbre';
			switch($table)
			{
				case TABLE_CATEGORIE_DANGER :
					$tdSubEdit = '
							<td colspan="2">&nbsp;</td>
							<td class="noPadding edit-leaf" id="edit-leaf' . $subElement->id . '">';
					if(current_user_can('digi_edit_danger'))
					{
						$tdSubEdit .= '<img style="width:' . TAILLE_PICTOS_ARBRE . ';" src="' . PICTO_EDIT . '" alt="' . sprintf(__('&Eacute;diter %s', 'evarisk'), __('le danger', 'evarisk')) . '" title="' . sprintf(__('&Eacute;diter %s', 'evarisk'), __('le danger', 'evarisk')) . '" />';
					}
					else
					{
						$tdSubEdit .= '&nbsp;';
					}
					$tdSubEdit .= '</td>';
					$tdSubDelete = '<td class="noPadding delete-leaf" id="delete-leaf' . $subElement->id . '">';
					if(current_user_can('digi_delete_danger'))
					{
						$tdSubDelete .= '<img style="width:' . TAILLE_PICTOS_ARBRE . ';" src="' . PICTO_DELETE . '" alt="' . sprintf(__('Supprimer %s', 'evarisk'), __('le danger', 'evarisk')) . '" title="' . sprintf(__('Supprimer %s', 'evarisk'), __('le danger', 'evarisk')) . '" />';
					}
					else
					{
						$tdSubDelete .= '&nbsp;';
					}
					$tdSubDelete .= '</td>';
					$subAffichage = '<span class="italic" >D' . $subElement->id . ' - </span>' . $subElement->nom;
					$subActions = $tdSubEdit . $tdSubDelete;
					if(!current_user_can('digi_move_danger'))
					{
						$ddFeuilleClass = '';
					}
					if(!current_user_can('digi_view_detail_danger'))
					{
						$nomFeuilleClass = 'userForbiddenActionCursor';
					}
					break;
				case TABLE_GROUPEMENT :
					$affichagePictoEvalRisque = (!AFFICHAGE_PICTO_EVAL_RISQUE) ? 'display:none;' : '';
					$tdSubEdit = '
							<td colspan="2">&nbsp;</td>';
					if(current_user_can('digi_edit_unite') || current_user_can('digi_edit_unite_' . $subElement->id))
					{
						$tdSubEdit .= '
							<td style="' . $affichagePictoEvalRisque . '" class="noPadding risk-leaf" id="risq-leaf' . $subElement->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';" src="' .PICTO_LTL_EVAL_RISK . '" alt="' . sprintf(__('Risques %s', 'evarisk'), __('de l\'unit&eacute; de travail', 'evarisk')) . '" title="' . sprintf(__('Risques %s', 'evarisk'), __('de l\'unit&eacute; de travail', 'evarisk')) . '" /></td><td class="noPadding edit-leaf" id="edit-leaf' . $subElement->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';" src="' . PICTO_EDIT . '" alt="' . sprintf(__('&Eacute;diter %s', 'evarisk'), __('l\'unit&eacute; de travail', 'evarisk')) . '" title="' . sprintf(__('&Eacute;diter %s', 'evarisk'), __('l\'unit&eacute; de travail', 'evarisk')) . '" /></td>';
					}
					elseif(current_user_can('digi_view_detail_unite') || current_user_can('digi_view_detail_unite_' . $subElement->id))
					{
						$tdSubEdit .= '
							<td style="' . $affichagePictoEvalRisque . '" class="noPadding risk-leaf" id="risq-leaf' . $subElement->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';" src="' .PICTO_LTL_EVAL_RISK . '" alt="' . sprintf(__('Risques %s', 'evarisk'), __('de l\'unit&eacute; de travail', 'evarisk')) . '" title="' . sprintf(__('Risques %s', 'evarisk'), __('de l\'unit&eacute; de travail', 'evarisk')) . '" /></td><td class="noPadding edit-leaf" id="edit-leaf' . $subElement->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';" src="' . PICTO_VIEW . '" alt="' . sprintf(__('Voir %s', 'evarisk'), __('l\'unit&eacute; de travail', 'evarisk')) . '" title="' . sprintf(__('Voir %s', 'evarisk'), __('l\'unit&eacute; de travail', 'evarisk')) . '" /></td>';
					}
					else
					{
						$tdSubEdit .= '<td class="noPadding" >&nbsp;</td><td class="noPadding" >&nbsp;</td>';
					}

					if(current_user_can('digi_delete_unite') || current_user_can('digi_delete_unite_' . $subElement->id))
					{
						$tdSubDelete = '<td class="noPadding delete-leaf" id="delete-leaf' . $subElement->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';"  src="' . PICTO_DELETE . '" alt="' . sprintf(__('Supprimer %s', 'evarisk'), __('l\'unit&eacute; de travail', 'evarisk')) . '" title="' . sprintf(__('Supprimer %s', 'evarisk'), __('l\'unit&eacute; de travail', 'evarisk')) . '" />';
					}
					else
					{
						$tdSubDelete = '<td class="noPadding" >&nbsp;';
					}
					$tdSubDelete .= '</td>';

					$subAffichage = '<span class="italic" >UT' . $subElement->id . ' - </span>' . $subElement->nom;
					$subActions = $tdSubEdit . $tdSubDelete;
					if(!current_user_can('digi_move_unite'))
					{
						$ddFeuilleClass = '';
					}
					if(!current_user_can('digi_view_detail_unite') && !current_user_can('digi_view_detail_unite_' . $subElement->id)
						&& !current_user_can('digi_edit_unite') && !current_user_can('digi_edit_unite_' . $subElement->id))
					{
						$nomFeuilleClass = 'userForbiddenActionCursor';
					}
					break;
				case TABLE_TACHE :
					$tdSubEdit = '
							<td colspan="2">&nbsp;</td>
							<td class="noPadding edit-leaf" id="edit-leaf' . $subElement->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';" src="' . PICTO_EDIT . '" alt="' . sprintf(__('&Eacute;diter %s', 'evarisk'), __('l\'action', 'evarisk')) . '" title="' . sprintf(__('&Eacute;diter %s', 'evarisk'), __('l\'action', 'evarisk')) . '" /></td>';
					$tdSubDelete = '<td class="noPadding delete-leaf" id="delete-leaf' . $subElement->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';"  src="' . PICTO_DELETE . '" alt="' . sprintf(__('Supprimer %s', 'evarisk'), __('l\'action', 'evarisk')) . '" title="' . sprintf(__('Supprimer %s', 'evarisk'), __('l\'action', 'evarisk')) . '" /></td>';
					$subAffichage = '<span class="italic" >ST' . $subElement->id . ' - </span>' . $subElement->nom;
					$subActions = $tdSubEdit . $tdSubDelete;
					break;
				case TABLE_GROUPE_QUESTION :
					$tdSubDelete = '
							<td colspan="2">&nbsp;</td>
							<td class="noPadding delete-leaf" id="delete-leaf-' . $subElement->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';" src="' . PICTO_DELETE . '" alt="' . sprintf(__('&Eacute;ffacer %s', 'evarisk'), __('la question', 'evarisk')) . '" title="' . sprintf(__('&Eacute;ffacer %s', 'evarisk'), __('la question', 'evarisk')) . '" /></td>';
					$subAffichage = 'Q' . $subElement->id . ' : ' . ucfirst($subElement->enonce);
					$subActions = $tdSubDelete;
					break;
			}
			$info = EvaDisplayDesign::getInfoArborescence($sousTable, $subElement->id);
			$monCorpsSubElements .= '
				<tr id="leaf-' . $subElement->id . '" class="cursormove child-of-node-' . $idTable . '-' . $elementPere->id . ' ' . $ddFeuilleClass . '">
					<td id="leaf-' . $subElement->id . '-name" class="' . $nomFeuilleClass . '" >' . $subAffichage . '</td>';
				if($titreInfo != null)
				{
					$monCorpsSubElements = $monCorpsSubElements . '<td class="' . $info['class'] . '">' . $info['value'] . '</td>';
				}
				$monCorpsSubElements .= $subActions . '
				</tr>';
		}

		if(count($elementsFils) != 0)
		{
			foreach ($elementsFils as $element )
			{
				$elements_fils = '';
				$elements_fils = Arborescence::getFils($table, $element, "nom ASC");
				$elements_pere = Arborescence::getPere($table, $element, " Status = 'Deleted' ");
				$ddNoeudClass = 'noeudArbre';
				$nomNoeudClass = 'nomNoeudArbre';

				if(count($elements_pere) <= 0)
				{
					switch($table)
					{
						case TABLE_CATEGORIE_DANGER :
							$sousTable = TABLE_DANGER;
							$subElements = categorieDangers::getDangersDeLaCategorie($element->id);
						break;
						case TABLE_GROUPEMENT :
							$sousTable = TABLE_UNITE_TRAVAIL;
							$subElements = EvaGroupement::getUnitesDuGroupement($element->id);
						break;
						case TABLE_TACHE :
							$sousTable = TABLE_ACTIVITE;
							$tache = new EvaTask($element->id);
							$tache->load();
							$subElements = $tache->getWPDBActivitiesDependOn();
						break;
						case TABLE_GROUPE_QUESTION :
							$sousTable = TABLE_QUESTION;
							$subElements = EvaGroupeQuestions::getQuestionsDuGroupeQuestions($element->id);
						break;
					}
					$trouveElement = count($elements_fils) + count($subElements);

					switch($table)
					{
						case TABLE_CATEGORIE_DANGER :
							$affichage = '<span class="italic" >CD' . $element->id . '</span> - ' . $element->nom;
							$tdAddMainStyle = 'display:none;';
							$tdAddSecondaryStyle = 'display:none;';
							if(count($elements_fils) > 0)
							{
								$tdAddMainStyle = '';
							}
							elseif(count($subElements) > 0)
							{
								$tdAddSecondaryStyle = '';
							}
							elseif((count($elements_fils) == 0) && (count($subElements) == 0))
							{
								$tdAddMainStyle = '';
								$tdAddSecondaryStyle = '';
							}
							
							if(current_user_can('digi_add_danger_category'))
							{
								$tdAddMain = '<td class="noPadding addMain" id="addMain' . $element->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';' . $tdAddMainStyle . '" src="' .PICTO_LTL_ADD_CATEGORIE_DANGER . '" alt="' . sprintf(__('Ajouter %s', 'evarisk'), __('une cat&eacute;gorie de dangers', 'evarisk')) . '" title="' . sprintf(__('Ajouter %s', 'evarisk'), __('une cat&eacute;gorie de dangers', 'evarisk')) . '" /></td><td id="addMain' . $element->id . 'Alt" style="display:none;">';
							}
							else
							{
								$tdAddMain = '<td class="noPadding >&nbsp;';
							}
							$tdAddMain .= '</td>';

							if(current_user_can('digi_add_danger'))
							{
								$tdAddSecondary = '<td class="noPadding addSecondary" id="addSecondary' . $element->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';' . $tdAddSecondaryStyle . '" src="' .PICTO_LTL_ADD_DANGER . '" alt="' . sprintf(__('Ajouter %s', 'evarisk'), __('un danger', 'evarisk')) . '" title="' . sprintf(__('Ajouter %s', 'evarisk'), __('un danger', 'evarisk')) . '" /></td><td id="addSecondary' . $element->id . 'Alt" style="display:none;">';
							}
							else
							{
								$tdAddSecondary = '<td class="noPadding >&nbsp;';
							}
							$tdAddSecondary .= '</td>';

							if(current_user_can('digi_edit_danger_category'))
							{
								$tdEdit = '<td class="noPadding edit-node" id="edit-node' . $element->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';" src="' .PICTO_EDIT . '" alt="' . sprintf(__('&Eacute;diter %s', 'evarisk'), __('la cat&eacute;gorie de dangers', 'evarisk')) . '" title="' . sprintf(__('&Eacute;diter %s', 'evarisk'), __('la cat&eacute;gorie de dangers', 'evarisk')) . '" />';
							}
							else
							{
								$tdEdit = '<td class="noPadding >&nbsp;';
							}
							$tdEdit .= '</td>';

							if(current_user_can('digi_delete_danger_category'))
							{
								$tdDelete = '<td class="noPadding delete-node" id="delete-node' . $element->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';" src="' .PICTO_DELETE . '" alt="' . sprintf(__('Supprimer %s', 'evarisk'), __('la cat&eacute;gorie de dangers', 'evarisk')) . '" title="' . sprintf(__('Supprimer %s', 'evarisk'), __('la cat&eacute;gorie de dangers', 'evarisk')) . '" />';
							}
							else
							{
								$tdDelete = '<td class="noPadding >&nbsp;';
							}
							$tdDelete .= '</td>';

							if(!current_user_can('digi_move_danger_category'))
							{
								$ddNoeudClass = '';
							}
							if(!current_user_can('digi_view_detail_danger_category'))
							{
								$nomNoeudClass = 'userForbiddenActionCursor';
							}

							$actions = '
								' . $tdAddMain . '
								' . $tdAddSecondary . '
								' . $tdEdit . '
								' . $tdDelete;
						break;
						case TABLE_GROUPEMENT :
							$affichage = '<span class="italic" >GP' . $element->id . '</span> - ' . $element->nom;
							$tdAddMainStyle = 'display:none;';
							$tdAddSecondaryStyle = 'display:none;';
							if(count($elements_fils) > 0)
							{
								$tdAddMainStyle = '';
							}
							elseif(count($subElements) > 0)
							{
								$tdAddSecondaryStyle = '';
							}
							elseif((count($elements_fils) == 0) && (count($subElements) == 0))
							{
								$tdAddMainStyle = '';
								$tdAddSecondaryStyle = '';
							}

							/*	Boutons d'ajouts d'un groupement ou d'une unité	*/
							if(current_user_can('digi_add_groupement') || current_user_can('digi_add_groupement_groupement_' . $element->id))
							{
								$tdAddMain = '<td class="noPadding addMain" id="addMain' . $element->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';' . $tdAddMainStyle . '" src="' .PICTO_LTL_ADD_GROUPEMENT . '" alt="' . sprintf(__('Ajouter %s', 'evarisk'), __('un groupement', 'evarisk')) . '" title="' . sprintf(__('Ajouter %s', 'evarisk'), __('un groupement', 'evarisk')) . '" />';
							}
							else
							{
								$tdAddMain = '<td class="noPadding" >&nbsp;';
							}
							$tdAddMain .= '</td>';
							if(current_user_can('digi_add_unite') || current_user_can('digi_add_unite_groupement_' . $element->id))
							{
								$tdAddSecondary = '<td class="noPadding addSecondary" id="addSecondary' . $element->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';' . $tdAddSecondaryStyle . '" src="' .PICTO_LTL_ADD_UNIT . '" alt="' . sprintf(__('Ajouter %s', 'evarisk'), __('une unit&eacute; de travail', 'evarisk')) . '" title="' . sprintf(__('Ajouter %s', 'evarisk'), __('une unit&eacute; de travail', 'evarisk')) . '" />';
							}
							else
							{
								$tdAddSecondary = '<td class="noPadding" >&nbsp;';
							}
							$tdAddSecondary .= '</td>';

							if(current_user_can('digi_edit_groupement') || current_user_can('digi_edit_groupement_' . $element->id))
							{
								$affichagePictoEvalRisque = (!AFFICHAGE_PICTO_EVAL_RISQUE) ? 'display:none;' : '';
								$tdEdit = '<td style="' . $affichagePictoEvalRisque . '" class="noPadding risq-node" id="risq-node' . $element->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';" src="' .PICTO_LTL_EVAL_RISK . '" alt="' . sprintf(__('Risques %s', 'evarisk'), __('du groupement', 'evarisk')) . '" title="' . sprintf(__('Risques %s', 'evarisk'), __('du groupement', 'evarisk')) . '" /></td><td class="noPadding edit-node" id="edit-node' . $element->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';" src="' .PICTO_EDIT . '" alt="' . sprintf(__('&Eacute;diter %s', 'evarisk'), __('le groupement', 'evarisk')) . '" title="' . sprintf(__('&Eacute;diter %s', 'evarisk'), __('le groupement', 'evarisk')) . '" /></td>';
							}
							elseif(current_user_can('digi_view_detail_groupement') || current_user_can('digi_view_detail_groupement_' . $element->id))
							{
								$affichagePictoEvalRisque = (!AFFICHAGE_PICTO_EVAL_RISQUE) ? 'display:none;' : '';
								$tdEdit = '<td style="' . $affichagePictoEvalRisque . '" class="noPadding risq-node" id="risq-node' . $element->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';" src="' .PICTO_LTL_EVAL_RISK . '" alt="' . sprintf(__('Risques %s', 'evarisk'), __('du groupement', 'evarisk')) . '" title="' . sprintf(__('Risques %s', 'evarisk'), __('du groupement', 'evarisk')) . '" /></td><td class="noPadding edit-node" id="edit-node' . $element->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';" src="' . PICTO_VIEW . '" alt="' . sprintf(__('Voir %s', 'evarisk'), __('le groupement', 'evarisk')) . '" title="' . sprintf(__('Voir %s', 'evarisk'), __('le groupement', 'evarisk')) . '" /></td>';
							}
							else
							{
								$tdEdit = '<td class="noPadding" >&nbsp;</td><td class="noPadding" >&nbsp;</td>';
							}

							/*	Bouton de suppression d'un groupement */
							if(current_user_can('digi_delete_groupement') || current_user_can('digi_delete_groupement_' . $element->id))
							{
								$tdDelete = '<td class="noPadding delete-node" id="delete-node' . $element->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';" src="' . PICTO_DELETE . '" alt="' . sprintf(__('Supprimer %s', 'evarisk'), __('le groupement', 'evarisk')) . '" title="' . sprintf(__('Supprimer %s', 'evarisk'), __('le groupement', 'evarisk')) . '" />';
							}
							else
							{
								$tdDelete = '<td class="noPadding" >&nbsp;';
							}
							$tdDelete .= '</td>';

							if(!current_user_can('digi_move_groupement'))
							{
								$ddNoeudClass = '';
							}
							if(!current_user_can('digi_view_detail_groupement') && !current_user_can('digi_view_detail_groupement_' . $element->id)
									&& !current_user_can('digi_edit_groupement') && !current_user_can('digi_edit_groupement_' . $element->id))
							{
								$nomNoeudClass = 'userForbiddenActionCursor';
							}

							/*	Ajout des différents boutons à l'interface	*/
							$actions = '
								' . $tdAddMain . '
								' . $tdAddSecondary . '
								' . $tdEdit . '
								' . $tdDelete;
						break;
						case TABLE_TACHE :
							$affichage = '<span class="italic" >T' . $element->id . '</span> - ' . $element->nom;
							$tdAddMainStyle = 'display:none;';
							$tdAddSecondaryStyle = 'display:none;';
							if(count($elements_fils) > 0)
							{
								$tdAddMainStyle = '';
							}
							elseif(count($subElements) > 0)
							{
								$tdAddSecondaryStyle = '';
							}
							elseif((count($elements_fils) == 0) && (count($subElements) == 0))
							{
								$tdAddMainStyle = '';
								$tdAddSecondaryStyle = '';
							}
							$tdAddMain = '<td class="noPadding addMain"  id="addMain' . $element->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';' . $tdAddMainStyle . '" src="' .PICTO_LTL_ADD_TACHE . '" alt="' . sprintf(__('Ajouter %s', 'evarisk'), __('une t&acirc;che', 'evarisk')) . '" title="' . sprintf(__('Ajouter %s', 'evarisk'), __('une t&acirc;che', 'evarisk')) . '" /></td><td id="addMain' . $element->id . 'Alt" style="display:none;"></td>';
							$tdAddSecondary = '<td class="noPadding addSecondary" id="addSecondary' . $element->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';' . $tdAddSecondaryStyle . '" src="' .PICTO_LTL_ADD_ACTIVITE . '" alt="' . sprintf(__('Ajouter %s', 'evarisk'), __('une action', 'evarisk')) . '" title="' . sprintf(__('Ajouter %s', 'evarisk'), __('une action', 'evarisk')) . '" /></td><td id="addSecondary' . $element->id . 'Alt" style="display:none;"></td>';
							$tdEdit = '<td class="noPadding edit-node" id="edit-node' . $element->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';"src="' .PICTO_EDIT . '" alt="' . sprintf(__('&Eacute;diter %s', 'evarisk'), __('le groupement', 'evarisk')) . '" title="' . sprintf(__('&Eacute;diter %s', 'evarisk'), __('la t&acirc;che', 'evarisk')) . '" /></td>';
							$tdDelete = '<td class="noPadding delete-node" id="delete-node' . $element->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';" src="' . PICTO_DELETE . '" alt="Effacer le titre" title="' . sprintf(__('&Eacute;ffacer %s', 'evarisk'), __('la t&acirc;che', 'evarisk')) . '" /></td>';
							$actions = '
								' . $tdAddMain . '
								' . $tdAddSecondary . '
								' . $tdEdit . '
								' . $tdDelete;
						break;
						case TABLE_GROUPE_QUESTION :
							$affichage = '<span class="italic" >GQ' . $element->id . '</span> - ' . $element->code . '-' . ucfirst($element->nom);
							$tdAdd = '<td class="noPadding addMain" id="add-node-' . $element->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';" src="'.PICTO_INSERT.'" alt="' . __('Inserer sous le titre', 'evarisk') . '" title="Inserer sous le titre" /></td>';
							$tdEdit = '<td class="noPadding edit-node" id="edit-node-' . $element->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';" src="' . PICTO_EDIT . '" alt="Modifier le titre" title="' . __('Modifier le titre', 'evarisk') . '" /></td>';
							$tdDelete = '<td class="noPadding delete-node" id="delete-node-' . $element->id . '"><img style="width:' . TAILLE_PICTOS_ARBRE . ';" src="' . PICTO_DELETE . '" alt="Effacer le titre" title="' . sprintf(__('&Eacute;ffacer %s', 'evarisk'), __('le titre', 'evarisk')) . '" />';
							$actions = $tdAdd . $tdEdit . $tdDelete;
						break;
					}
					$info = '';
					if($titreInfo != null)
					{
						$info = EvaDisplayDesign::getInfoArborescence($table, $element->id);
						$info = '<td id="info-' . $element->id . '" class="' . $info['class'] . '">' . $info['value'] . '</td>';
					}
					$monCorpsTable .= '
						<tr id="node-' . $idTable . '-' . $element->id . '" class="child-of-node-' . $idTable . '-' . $elementPere->id . ' ' . $ddNoeudClass . ' parent">
							<td id="node-' . $idTable . '-' . $element->id . '-name" class="' . $nomNoeudClass . '" >' . $affichage . '</td>
							' . $info . $actions . '
						</tr>';

					if($trouveElement)
					{
						$monCorpsTable .= EvaDisplayDesign::getCorpsTableArborescence($elements_fils, $element, $table, $titreInfo, $idTable);
					}
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
				$info['class'] = 'treeTableInfoColumn';
				if(!current_user_can('digi_view_detail_danger'))
				{
					$info['class'] = 'userForbiddenActionCursor';
				}
				break;
			case TABLE_CATEGORIE_DANGER :
				$info['value'] = '';
				$info['class'] = 'treeTableGroupInfoColumn';
				if(!current_user_can('digi_view_detail_danger_category'))
				{
					$info['class'] = 'userForbiddenActionCursor';
				}
				break;
			case TABLE_TACHE :
				$tache = new EvaTask($elementId);
				$tache->load();
				$statutProgression = '';
				switch($tache->getProgressionStatus())
				{
					case 'notStarted';
						$statutProgression = __('Non commenc&eacute;e', 'evarisk');
					break;
					case 'inProgress';
						$statutProgression = __('En cours', 'evarisk');
					break;
					case 'Done';
					case 'DoneByChief';
						$statutProgression = __('Sold&eacute;e', 'evarisk');
					break;
				}
				$info['value'] = $tache->getProgression() . '%&nbsp;(' . $statutProgression . ')';
				$info['class'] = 'treeTableGroupInfoColumn taskInfoContainer-' . $elementId;
				break;
			case TABLE_ACTIVITE :
				$action = new EvaActivity($elementId);
				$action->load();
				$statutProgression = '';
				switch($action->getProgressionStatus())
				{
					case 'notStarted';
						$statutProgression = __('Non commenc&eacute;e', 'evarisk');
					break;
					case 'inProgress';
						$statutProgression = __('En cours', 'evarisk');
					break;
					case 'Done';
					case 'DoneByChief';
						$statutProgression = __('Sold&eacute;e', 'evarisk');
					break;
				}
				$info['value'] = $action->getProgression() . '%&nbsp;(' . $statutProgression . ')';
				$info['class'] = 'treeTableInfoColumn activityInfoContainer-' . $elementId;
				break;
			case TABLE_GROUPEMENT :
				$scoreRisqueGroupement = 0;
				$riskAndSubRisks = eva_documentUnique::listRisk(TABLE_GROUPEMENT, $elementId);
				foreach($riskAndSubRisks as $risk)
				{
					$scoreRisqueGroupement += $risk[1]['value'];
				}
				$info['value'] = '<span id="LeftRiskSum' . $table . $elementId . '" >' . $scoreRisqueGroupement . '</span>&nbsp;-&nbsp;<span id="LeftRiskNb' . $table . $elementId . '" >' . count($riskAndSubRisks) . '</span> ' . __('risque(s)', 'evarisk');
				$info['class'] = 'treeTableGroupInfoColumn';
				if(!current_user_can('digi_view_detail_groupement') && !current_user_can('digi_view_detail_groupement_' . $elementId)
									&& !current_user_can('digi_edit_groupement') && !current_user_can('digi_edit_groupement_' . $elementId))
				{
					$info['class'] = 'userForbiddenActionCursor';
				}
				break;
			case TABLE_UNITE_TRAVAIL :
				$scoreRisqueGroupement = 0;
				$riskAndSubRisks = eva_documentUnique::listRisk(TABLE_UNITE_TRAVAIL, $elementId);
				foreach($riskAndSubRisks as $risk)
				{
					$scoreRisqueGroupement += $risk[1]['value'];
				}
				$info['value'] = '<span id="LeftRiskSum' . $table . $elementId . '" >' . $scoreRisqueGroupement . '</span>&nbsp;-&nbsp;<span id="LeftRiskNb' . $table . $elementId . '" >' . count($riskAndSubRisks) . '</span> ' . __('risque(s)', 'evarisk');
				$info['class'] = 'treeTableInfoColumn';
				if(!current_user_can('digi_view_detail_unite') && !current_user_can('digi_view_detail_unite_' . $elementId)
									&& !current_user_can('digi_edit_unite') && !current_user_can('digi_edit_unite_' . $elementId))
				{
					$info['class'] = 'userForbiddenActionCursor';
				}
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
			$barreTitre .= '<th class="' . $classes[$i] . '" scope="col">' . $titres[$i] . '</th>';
		}
		if($barreTitre != '')
		{
			$barreTitre = '<tr valign="top">' . $barreTitre . '</tr>';
		}
		for($numeroLigne=0; $numeroLigne<count($lignesDeValeurs); $numeroLigne++)
		{
			$ligneDeValeurs = $lignesDeValeurs[$numeroLigne];
			$corpsTable .= '<tr id="' . $idLignes[$numeroLigne] . '" valign="top" >';
			for($i=0; $i<count($ligneDeValeurs); $i++)
			{
				$corpsTable .= '
					<td class="' . $classes[$i] . ' ' . $ligneDeValeurs[$i]['class'] . '">' . $ligneDeValeurs[$i]['value'] . '</td>';
			}
			$corpsTable .= '</tr>';
		}
		$table = $script . '<table id="' . $idTable . '" cellspacing="0" class="widefat post fixed" >
				<thead>
						' . $barreTitre . '
				</thead>
				<tfoot>
						' . $barreTitre . '
				</tfoot>
				<tbody >'
				 . $corpsTable . 
				'
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
	evarisk(document).ready(function() {
		evarisk(\'#page' . $idPostBox . 'Reference\').val(' . $page . ');
		evarisk(\'#page' . $idPostBox . '\').val(' . $page . ');
		if(document.getElementById(\'filAriane\').lastChild == document.getElementById(\'filAriane\').firstChild)
		{
			evarisk(\'#' . $idPostBox . 'Pere\').addClass(\'hidden\');
		}
		else
		{
			evarisk(\'#' . $idPostBox . 'Pere\').removeClass(\'hidden\');
		}
		evarisk(\'#pageMax' . $idPostBox . '\').val(' . $pageMax . ');
		if(evarisk(\'#pageMax' . $idPostBox . '\').val() == 0)
		{
			evarisk(\'#pageMax' . $idPostBox . '\').val(1);
		}
		if(' . $page . ' <= 1)
		{
			evarisk(\'#first' . $idPostBox . '\').attr("disabled", "disabled");
			evarisk(\'#previous' . $idPostBox . '\').attr("disabled", "disabled");
		}
		else
		{
			evarisk(\'#first' . $idPostBox . '\').attr("disabled", "");
			evarisk(\'#previous' . $idPostBox . '\').attr("disabled", "");
		}
		if(parseInt(evarisk(\'#pageMax' . $idPostBox . '\').val()) == ' . $page . ')
		{
			evarisk(\'#last' . $idPostBox . '\').attr("disabled", "disabled");
			evarisk(\'#next' . $idPostBox . '\').attr("disabled", "disabled");
		}
		else
		{
			evarisk(\'#last' . $idPostBox . '\').attr("disabled", "");
			evarisk(\'#next' . $idPostBox . '\').attr("disabled", "");
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
		evarisk(document).ready(function() {
			evarisk("#favorite-actions-mainPostBox").hide();
			evarisk("#favorite-second-link-' . $idPostBox . '").show();
			evarisk("#favorite-first-link-' . $idPostBox . '").show();
			evarisk(\'#infoLi\').html("' . $messageAbsence . '\"" + evarisk(\'#filAriane\').children(\'a:last\').html() + "\".<br />" +
		"<a href=\"#\" id=\"addMain\"><img src=\"' . $mainSrc . '\" alt=\"mainAdd\" title=\"' . $mainAdd . '\" />' . $mainAdd . '</a><br />" +
		"<a href=\"#\" id=\"addSecondary\"><img src=\"' . $secondarySrc . '\" alt=\"secondaryAdd\" title=\"' . $secondaryAdd . '\" />' . $secondaryAdd . '</a>");
			evarisk("#addMain").click(function(){
				evarisk(\'#favorite-first-link-' . $idPostBox . '\').click();
				return false;
			});
			evarisk("#addSecondary").click(function(){
				evarisk(\'#favorite-second-link-' . $idPostBox . '\').click();
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
			evarisk("#rightEnlarging").show();
			evarisk("#equilize").click();
			evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
				"table": "' . $_POST['table'] . '",
				"id": "' . $_POST['idPere'] . '",
				"page": evarisk(\'#page' . $idPostBox . 'Reference\').val(),
				"idPere": evarisk(\'#identifiantActuelle' . $idPostBox . '\').val(),
				"act": "edit",
				"partie": "right",
				"menu": evarisk("#menu").val(),
				"affichage": "affichageTable",
				"partition": "tout"
			});
			evarisk("#partieGauche").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
				"table": "' . $_POST['table'] . '",
				"id": "' . $_POST['idPere'] . '",
				"page": evarisk(\'#page' . $idPostBox . 'Reference\').val(),
				"idPere": evarisk(\'#identifiantActuelle' . $idPostBox . '\').val(),
				"act": "edit",
				"partie": "left",
				"menu": evarisk("#menu").val(),
				"affichage": "affichageTable",
				"partition": "tout"
			});
			evarisk("#partieEdition").html(evarisk("#loadingImg").html());
			evarisk("#partieGauche").html(evarisk("#loadingImg").html());
			return false;';
		foreach($elements as $elementObject)
		{
			$table = $elementObject['table'];
			$idElement = $elementObject['id'];
			$chargement = '
				evarisk("#rightEnlarging").show();
				evarisk("#equilize").click();
				evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
					"table": "' . $table . '",
					"id": "' . $idElement . '",
					"page": evarisk(\'#page' . $idPostBox . 'Reference\').val(),
					"idPere": evarisk(\'#identifiantActuelle' . $idPostBox . '\').val(),
					"act": "edit",
					"partie": "right",
					"menu": evarisk("#menu").val(),
					"affichage": "affichageTable",
					"partition": "tout"
				});
				evarisk("#partieGauche").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
					"table": "' . $_POST['table'] . '",
					"id": "' . $_POST['idPere'] . '",
					"page": evarisk(\'#page' . $idPostBox . 'Reference\').val(),
					"idPere": evarisk(\'#identifiantActuelle' . $idPostBox . '\').val(),
					"act": "edit",
					"partie": "left",
					"menu": evarisk("#menu").val(),
					"affichage": "affichageTable",
					"partition": "tout"
				});
				evarisk("#partieEdition").html(evarisk("#loadingImg").html());
				evarisk("#partieGauche").html(evarisk("#loadingImg").html());
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
					$element = eva_UniteDeTravail::getWorkingUnit($idElement);
					$defaultPicto = DEFAULT_WORKING_UNIT_PICTO;
					$infosElement = eva_UniteDeTravail::getWorkingUnitInfos($idElement);
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
				if(evarisk(\'#filAriane :last-child\').is("label"))
					evarisk(\'#filAriane :last-child\').remove();
				evarisk(\'#filAriane\').append(\'<label>&nbsp;&raquo;&nbsp;</label><a href="#" id="element' . $element->id . '" class="elementFilAriane">' . addslashes($element->nom) . '</a>\');
				evarisk(\'#page' . $idPostBox . 'Reference\').val(1);
				evarisk(document).ready(function() {
					evarisk(\'#element' . $element->id . '\').click(function() {
						evarisk(\'#identifiantActuelle' . $idPostBox . '\').val("' . $element->id . '");
						evarisk(\'#page' . $idPostBox . 'Reference\').val(1);
						while(evarisk(\'#filAriane :last-child\').attr("id") != "element' . $element->id . '")
						{
							evarisk(\'#filAriane :last-child\').remove();
						}

						changementPage("right", "' . $table . '", evarisk("#page' . $idPostBox . 'Reference").val(), evarisk("#identifiantActuelle' . $idPostBox . '").val(), "affichageTable", "main");
						changementPage("left", "' . $table . '", evarisk("#page' . $idPostBox . 'Reference").val(), evarisk("#identifiantActuelle' . $idPostBox . '").val(), "affichageTable", "main");

						evarisk(\'#' . $idPostBox . ' h3 span\').html("' . addslashes($element->nom) . '");
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
					evarisk(document).ready(function() {
						if(' . $aFilsNoeud . ')
						{
							evarisk("#favorite-second-link-' . $idPostBox . '").hide();
							evarisk("#favorite-first-link-' . $idPostBox . '").show();
						}
						if(' . $aFilsFeuille . ')
						{
							evarisk("#favorite-first-link-' . $idPostBox . '").hide();
							evarisk("#favorite-second-link-' . $idPostBox . '").show();
						}
						var timeoutDbl' . $table . $idElement . '_0;
						var timeoutDbl' . $table . $idElement . '_1;
						var nbClic = 0;
						evarisk(\'#photo' . $table . $idElement . '\').dblclick(function() {
							clearTimeout(timeoutDbl' . $table . $idElement . '_0);
							clearTimeout(timeoutDbl' . $table . $idElement . '_1);
							if((evarisk("#select' . $table . $idElement . '").attr("id")) != undefined)
								evarisk("#select' . $table . $idElement . '").click();
							else
								evarisk("#edit' . $table . $idElement . '").click();
						});
						evarisk(\'#photo' . $table . $idElement . '\').parent().click(function(event){
							if(nbClic == 0)
							{
								timeoutDbl' . $table . $idElement . '_0 = setTimeout 
								( 
									function() 
									{ 
										evarisk("#edit' . $table . $idElement . '").click();
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
										evarisk("#edit' . $table . $idElement . '").click();
									}, 
									300 
								);
							}
							nbClic = (nbClic + 1)%2;
							return("false");
						});
						evarisk(\'#edit' . $table . $idElement . '\').click(function() {
							evarisk("#menu").val(\'gestiongrptut\');
							if(evarisk(\'#filAriane :last-child\').is("label"))
								evarisk(\'#filAriane :last-child\').remove();
							evarisk(\'#filAriane :last-child\').after(\'<label>&nbsp;&raquo;&nbsp;&Eacute;dition&nbsp;de&nbsp;' . $element->nom . '</label>\');
							' . $chargement . '
						});
						evarisk(\'#risq' . $table . $idElement . '\').click(function() {
							evarisk("#menu").val(\'risq\');
							if(evarisk(\'#filAriane :last-child\').is("label"))
								evarisk(\'#filAriane :last-child\').remove();
							evarisk(\'#filAriane :last-child\').after(\'<label>&nbsp;&raquo;&nbsp;&Eacute;valuation des risques de ' . $element->nom . '</label>\');
							' . $chargement . '
						});
						evarisk(\'#addMain' . $table . $idElement . '\').click(function() {
							evarisk(\'#identifiantActuelle' . $idPostBox . '\').val("' . $element->id . '");
							evarisk(\'#page' . $idPostBox . 'Reference\').val(1);
							evarisk("#rightEnlarging").show();
							evarisk("#equilize").click();
							evarisk("#menu").val(\'gestiongrptut\');
							' . $scriptFilAriane . '
							evarisk("#filAriane :last-child").after("<label>&nbsp;&raquo;&nbsp;Ajout d\'un nouveau groupement &agrave; ' . $element->nom . '</label>");
							
							evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
								"table": "' . $table . '",
								"act": "add",
								"page": evarisk(\'#page' . $idPostBox . 'Reference\').val(),
								"idPere": ' . $element->id . ',
								"partie": "right",
								"menu": evarisk("#menu").val(),
								"affichage": "affichageTable",
								"partition": "tout"
							});
							evarisk("#partieGauche").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
								"table": "' . $table . '",
								"act": "add",
								"page": evarisk(\'#page' . $idPostBox . 'Reference\').val(),
								"idPere": ' . $element->id . ',
								"partie": "left",
								"menu": evarisk("#menu").val(),
								"affichage": "affichageTable",
								"partition": "tout"
							});
							
							evarisk("#partieEdition").html(evarisk("#loadingImg").html());
							evarisk("#partieGauche").html(evarisk("#loadingImg").html());
							return false;
						});
						evarisk(\'#addSecondary' . $table . $idElement . '\').click(function() {
							evarisk(\'#identifiantActuelle' . $idPostBox . '\').val("' . $element->id . '");
							evarisk(\'#page' . $idPostBox . 'Reference\').val(1);
							evarisk("#rightEnlarging").show();
							evarisk("#equilize").click();
							evarisk("#menu").val(\'gestiongrptut\');
							' . $scriptFilAriane . '
							evarisk("#filAriane :last-child").after("<label>&nbsp;&raquo;&nbsp;Ajout d\'une nouvelle unit&eacute; de travail &agrave; ' . $element->nom . '</label>");
							
							evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
								"table": "' . $sousTable . '",
								"act": "add",
								"page": 1,
								"idPere": ' . $element->id . ',
								"partie": "right",
								"menu": evarisk("#menu").val(),
								"affichage": "affichageTable",
								"partition": "tout"
							});
							evarisk("#partieGauche").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
								"table": "' . $sousTable . '",
								"act": "add",
								"page": evarisk(\'#page' . $idPostBox . 'Reference\').val(),
								"idPere": evarisk(\'#identifiantActuelle' . $idPostBox . '\').val(),
								"partie": "left",
								"menu": evarisk("#menu").val(),
								"affichage": "affichageTable",
								"partition": "tout"
							});
							
							evarisk("#partieEdition").html(evarisk("#loadingImg").html());
							evarisk("#partieGauche").html(evarisk("#loadingImg").html());
							return false;
						});
						evarisk(\'#select' . $table . $idElement . '\').click(function() {
							if(!(evarisk(this).attr("disabled")))
							{
								if(evarisk(\'#filAriane :last-child\').is("label"))
									evarisk(\'#filAriane :last-child\').remove();
								evarisk(\'#identifiantActuelle' . $idPostBox . '\').val(' . $idElement . ');
								' . $scriptFilAriane . '

								changementPage("right", "' . $table . '", evarisk("#page' . $idPostBox . 'Reference").val(), evarisk("#identifiantActuelle' . $idPostBox . '").val(), "affichageTable", "main");
								changementPage("left", "' . $table . '", evarisk("#page' . $idPostBox . 'Reference").val(), evarisk("#identifiantActuelle' . $idPostBox . '").val(), "affichageTable", "main");

								evarisk(\'#' . $idPostBox . ' h3 span\').html("' . addslashes($element->nom) . '");
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
			evarisk("#rightEnlarging").show();
			evarisk("#equilize").click();
			evarisk("#partieEdition").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
				"table": "' . $mainTable . '",
				"id": "' . $_POST['idPere'] . '",
				"page": evarisk(\'#page' . $idPostBox . 'Reference\').val(),
				"idPere": evarisk(\'#identifiantActuelle' . $idPostBox . '\').val(),
				"act": "edit",
				"partie": "right",
				"menu": evarisk("#menu").val(),
				"affichage": "affichageTable",
				"partition": "tout"
			});
			evarisk("#partieGauche").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post": "true", 
				"table": "' . $mainTable . '",
				"id": "' . $_POST['idPere'] . '",
				"page": evarisk(\'#page' . $idPostBox . 'Reference\').val(),
				"idPere": evarisk(\'#identifiantActuelle' . $idPostBox . '\').val(),
				"act": "edit",
				"partie": "left",
				"menu": evarisk("#menu").val(),
				"affichage": "affichageTable",
				"partition": "tout"
			});
			evarisk("#partieEdition").html(evarisk("#loadingImg").html());
			evarisk("#partieGauche").html(evarisk("#loadingImg").html());
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
					evarisk("#favorite-actions-mainPostBox").hide();
					evarisk(document).ready(function() {
						evarisk(\'#edit' . $_POST['table'] . $_POST['idPere'] . '\').click(function() {
							evarisk("#menu").val(\'gestiongrptut\');
							if(evarisk(\'#filAriane :last-child\').is("label"))
								evarisk(\'#filAriane :last-child\').remove();
							evarisk(\'#filAriane :last-child\').after(\'<label>&nbsp;&raquo;&nbsp;&Eacute;dition&nbsp;de&nbsp;' . $elementPere->nom . '</label>\');
							' . $chargement . '
						});
						evarisk(\'#risq' . $_POST['table'] . $_POST['idPere'] . '\').click(function() {
							evarisk("#menu").val(\'risq\');
							if(evarisk(\'#filAriane :last-child\').is("label"))
								evarisk(\'#filAriane :last-child\').remove();
							evarisk(\'#filAriane :last-child\').after(\'<label>&nbsp;&raquo;&nbsp;&Eacute;valuation des risques de ' . $elementPere->nom . '</label>\');
							' . $chargement . '
						});
						evarisk(\'#addMain' . $_POST['table'] . $_POST['idPere'] . '\').click(function() {
							evarisk("#rightEnlarging").show();
							evarisk("#equilize").click();
							evarisk("#menu").val(\'gestiongrptut\');
							if(evarisk(\'#filAriane :last-child\').is("label"))
								evarisk(\'#filAriane :last-child\').remove();
							evarisk("#filAriane :last-child").after("<label>&nbsp;&raquo;&nbsp;Ajout d\'un nouveau groupement &agrave; ' . $elementPere->nom . '</label>");
							evarisk("#favorite-first-link-mainPostBox").click();
						});
						evarisk(\'#addSecondary' . $_POST['table'] . $_POST['idPere'] . '\').click(function() {
							evarisk("#rightEnlarging").show();
							evarisk("#equilize").click();
							evarisk("#menu").val(\'gestiongrptut\');
							if(evarisk(\'#filAriane :last-child\').is("label"))
								evarisk(\'#filAriane :last-child\').remove();
							evarisk("#filAriane :last-child").after("<label>&nbsp;&raquo;&nbsp;Ajout d\'une nouvelle unit&eacute; de travail &agrave; ' . $elementPere->nom . '</label>");
							evarisk("#favorite-second-link-mainPostBox").click();
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
					evarisk("#favorite-actions-mainPostBox").hide();
					evarisk(document).ready(function() {
						evarisk(\'#edit' . $_POST['table'] . $_POST['idPere'] . '\').click(function() {
							evarisk("#menu").val(\'gestiongrptut\');
							if(evarisk(\'#filAriane :last-child\').is("label"))
								evarisk(\'#filAriane :last-child\').remove();
							evarisk(\'#filAriane :last-child\').after(\'<label>&nbsp;&raquo;&nbsp;&Eacute;dition&nbsp;de&nbsp;' . $elementPere->nom . '</label>\');
							' . $chargement . '
						});
						evarisk(\'#addMain' . $_POST['table'] . $_POST['idPere'] . '\').click(function() {
							evarisk("#rightEnlarging").show();
							evarisk("#equilize").click();
							evarisk("#menu").val(\'gestiongrptut\');
							if(evarisk(\'#filAriane :last-child\').is("label"))
								evarisk(\'#filAriane :last-child\').remove();
							evarisk("#filAriane :last-child").after("<label>&nbsp;&raquo;&nbsp;Ajout d\'une nouvelle cat&eacute;gorie de dangers &agrave; ' . $elementPere->nom . '</label>");
							evarisk("#favorite-first-link-mainPostBox").click();
						});
						evarisk(\'#addSecondary' . $_POST['table'] . $_POST['idPere'] . '\').click(function() {
							evarisk("#rightEnlarging").show();
							evarisk("#equilize").click();
							evarisk("#menu").val(\'gestiongrptut\');
							if(evarisk(\'#filAriane :last-child\').is("label"))
								evarisk(\'#filAriane :last-child\').remove();
							evarisk("#filAriane :last-child").after("<label>&nbsp;&raquo;&nbsp;Ajout d\'un nouveau danger &agrave; ' . $elementPere->nom . '</label>");
							evarisk("#favorite-second-link-mainPostBox").click();
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
				evarisk(document).ready(function() {
					evarisk("#' . $idTable . ' tr:first").append(
						"<td rowspan=' . (count($lignesDeValeurs) - 1) . ' style=\"width:' . $largeurDerniereColonne . '%;\">" + ' . $listeBoutons . '
						"</td>"
					);
					evarisk("#' . $idTable . ' tr:first").append();
					evarisk("#' . $idTable . ' .nomElement, #' . $idTable . ' .photoElement, #' . $idTable . ' .boutonsInfoElement").each(function(){
						evarisk(this).attr("colspan", "3");
					});
					evarisk("#' . $idTable . ' .nomInfoElement").each(function(){
						evarisk(this).css("width","' . $largeurNomInfo . '%");
					});
					evarisk("#' . $idTable . ' .deuxPoints").each(function(){
						evarisk(this).css("width","' . $largeurDeuxPoints . '%");
					});
					evarisk("#' . $idTable . ' .valeurInfoElement").each(function(){
						evarisk(this).css("width","' . $largeurValeurInfo . '%");
					});
					evarisk("#' . $idTable . ' .pagination").each(function(){
						evarisk(this).attr("colspan", "' . ($nombreElements * 3 + 1) . '");
					});
					evarisk("#' . $idPostBox . ' .inside").each(function(){
						evarisk(this).css("padding", "0");
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
				'<table summary="" border="0" cellpadding="0" cellspacing="0" align="center" class="tabcroisillon" style="width:100%;" >
					<tr>
						<td style="width:60%;" >' . 
							EvaDisplayInput::ouvrirForm('POST', 'infosGenerationDU', 'infosGenerationDU') . 
							'<table summary="" cellpadding="0" cellspacing="0" border="0" class="tabformulaire" style="width:100%;" >
								<tr>
									<td ><label for="dateCreation">' . __('date de cr&eacute;ation', 'evarisk') . '</label></td>
									<td >' . EvaDisplayInput::afficherInput('text', 'dateCreation', '#DATEFORM1#', '', '', 'dateCreation', false, false, 150, '', 'Date', '100%', '', 'left;width:100%;', false) . '</td>
								</tr>
								<tr>
									<td ><label for="dateDebutAudit">' . __('date de d&eacute;but d\'audit', 'evarisk') . '</label></td>
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
								<tr>
									<td  ><label for="remarque_important">' . __('remarque importante', 'evarisk') . '</label></td>
									<td style="text-align:center;"><textarea id="remarque_important" name="remarque_important" class="textarea14" style="width:100%" ;>#REMARQUEIMPORTANT#</textarea></td>
								</tr> 
								<tr>
									<td  ><label for="localisation">' . __('localisation', 'evarisk') . '</label></td>
									<td style="text-align:center;"><textarea id="localisation" name="localisation" class="textarea14" style="width:100%" ;>#LOCALISATION#</textarea></td>
								</tr> 
								<tr>
									<td  ><label for="sources">' . __('sources', 'evarisk') . '</label></td>
									<td style="text-align:center;"><textarea id="sources" name="sources" class="textarea14" style="width:100%" ;>#SOURCES#</textarea></td>
								</tr> 
		<!-- 
								<tr>
									<td  colspan="2" ><label for="methodologie">' . __('m&eacute;thodologie', 'evarisk') . '</label></td>
								</tr>
								<tr>
									<td colspan="2" style="text-align:center;"><textarea id="methodologie" name="methodologie" class="textarea">#METHODOLOGIE#</textarea></td>
								</tr>
		-->
								<tr>
									<td >&nbsp;</td>
									<td style="padding:12px 0px;" >
										<div>
											<input type="checkbox" id="modelDefaut" checked="checked" name="modelUse" value="modeleDefaut" />
											<label for="modelDefaut" style="vertical-align:middle;" >' . __('Utiliser le mod&egrave;le par d&eacute;faut', 'evarisk') . '</label>
										</div>
										<div id="modelListForGeneration" style="display:none;" >&nbsp;</div>
									</td>
								</tr>
								<tr>
									<td colspan="2"><input class="button-primary alignright" type="button" id="genererDUER" name="genererDUER" value="' . __('G&eacute;n&eacute;rer le document unique', 'evarisk') . '" /></td>
								</tr>
							</table>' .
							EvaDisplayInput::fermerForm('infosGenerationDU') .
						'</td>
						<td style="width:40%;" id="documentUniqueResultContainer" >&nbsp;</td>
					</tr>
				</table>';
	}

	/**
	*	Return the form template to upload a new DUER model
	*	@return string HTML code of the form
	*/
	static function getNewModelUploadForm($tableElement, $idElement)
	{
		$idUpload = 'model' . $tableElement;
		$allowedExtensions = "['odt']";
		$multiple = false;
		$actionUpload = str_replace('\\', '/', EVA_LIB_PLUGIN_URL . "gestionDocumentaire/uploadFile.php");
		switch($tableElement)
		{
			case TABLE_GROUPEMENT:
			{
				$repertoireDestination = str_replace('\\', '/', EVA_MODELES_PLUGIN_DIR . 'documentUnique/');
				$defaultModelLink = EVA_MODELES_PLUGIN_URL . 'documentUnique/modeleDefaut.odt';
				$table = TABLE_DUER;
			}
			break;
			case TABLE_GROUPEMENT . '_FP' :
			{
				$repertoireDestination = str_replace('\\', '/', EVA_MODELES_PLUGIN_DIR . 'ficheDePoste/');
				$defaultModelLink = EVA_MODELES_PLUGIN_URL . 'ficheDePoste/modeleDefaut.odt';
				$table = TABLE_FP;
			}
			break;
			case TABLE_GROUPEMENT . '_FGP' :
			{
				$repertoireDestination = str_replace('\\', '/', EVA_MODELES_PLUGIN_DIR . 'ficheDeGroupement/');
				$defaultModelLink = EVA_MODELES_PLUGIN_URL . 'ficheDeGroupement/modeleDefaut_groupement.odt';
				$table = TABLE_FP;
			}
			break;
			case TABLE_UNITE_TRAVAIL:
			{
				$repertoireDestination = str_replace('\\', '/', EVA_MODELES_PLUGIN_DIR . 'ficheDePoste/');
				$defaultModelLink = EVA_MODELES_PLUGIN_URL . 'ficheDePoste/modeleDefaut.odt';
				$table = TABLE_FP;
			}
			break;
			default:
			{
				sprintf(__('Le cas % n\'a pas &eacute;t&eacute; pr&eacute;vu dans %s &agrave; la ligne %s', 'evarisk'), $tableElement, __FILE__, __LINE__);
			}
			break;
		}

		$newModelForm = 
			'<div style="margin:0px auto;width:92%;" >
				<div id="moreModelChoice" >
					' . EvaDisplayDesign::getExistingModelList($tableElement, $idElement) . '
				</div>
				<div style="margin:6px 0px;" class="bold" >' . __('Ajouter un nouveau mod&egrave;le', 'evarisk') . '&nbsp:</div>
				<ol>
					<li><a class="bold" href="' . $defaultModelLink . '" >' . __('T&eacute;l&eacute;chargez le mod&egrave;le par d&eacute;faut', 'evarisk') . '</a></li>
					<li>
						<span class="bold" >' . __('Modifiez le mod&egrave;le', 'evarisk') . '</span>
						<ul>
							<li style="text-align:justify;" >' . __('Apportez vos modifications au fichier t&eacute;l&eacute;charg&eacute;', 'evarisk') . '<br/><span style="color:red;" >' . __('Attention &agrave; ne pas supprimer ou modifier les parties du document qui sont sous la forme "{UnTexte}"', 'evarisk') . '</span></li>
						</ul>
					</li>
					<li>
						<span class="bold" >' . __('Envoyez votre mod&egrave;le', 'evarisk') . '</span>
						<ul>
							<li>' . eva_gestionDoc::getFormulaireUpload($table, $tableElement, $idElement, $repertoireDestination, $idUpload, $allowedExtensions, $multiple, $actionUpload, __('S&eacute;lectionner le mod&egrave;le &agrave; envoyer', 'evarisk')) . '</li>
						</ul>
					</li>
				</ol>
			</div>';

		return $newModelForm;
	}

	/**
	*	Return the combo box with the different existing model
	*	@return string $moreModelChoice HTML code containing the different existing model
	*/
	function getExistingModelList($tableElement, $idElement)
	{
		switch($tableElement)
		{
			case TABLE_GROUPEMENT:
			{
				$documentType = 'document_unique';
			}
			break;
			case TABLE_GROUPEMENT . '_FP':
			{
				$documentType = 'fiche_de_poste';
			}
			break;
			case TABLE_UNITE_TRAVAIL:
			{
				$documentType = 'fiche_de_poste';
			}
			break;
		}
		$moreModelChoice = '';
		$documentList = eva_gestionDoc::getCompleteDocumentList($documentType, 
		"	AND SUBSTRING(chemin FROM 1 FOR 8) != 'results/'
			AND id NOT IN (
				SELECT id 
				FROM " . TABLE_GED_DOCUMENTS . " 
				WHERE id_element = '" . $idElement . "' 
					AND table_element = '" . $tableElement . "'
			)
			AND nom NOT IN (
				SELECT nom 
				FROM " . TABLE_GED_DOCUMENTS . " 
				WHERE id_element = '" . $idElement . "' 
					AND table_element = '" . $tableElement . "'
			)			", "dateCreation DESC");
		if(count($documentList) > 0)
		{
			$moreModelChoice = 
				'<div style="margin:6px 0px;" class="bold" >' . __('Affecter un mod&egrave;le existant', 'evarisk') . '&nbsp:</div>
				<ol>
					<li>
						' . __('S&eacute;lectionner un mod&eacute;le', 'evarisk') . '
						<div style="margin:3px 24px;" class="bold" >
							' . evaDisplayInput::afficherComboBox($documentList, 'modelToDuplicate', '', 'modelToDuplicate', '', '') . '
							<input type="button" value="' . __('Affecter', 'evarisk') . '" class="button-primary" id="duplicateModel" name="duplicateModel" />
							<script type="text/javascript" >
								evarisk("#duplicateModel").click(function(){
									evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
										"post": "true", 
										"table": "' . TABLE_GED_DOCUMENTS . '",
										"act": "duplicateDocument",
										"tableElement": "' . $tableElement . '",
										"idElement": "' . $idElement . '",
										"idDocument": evarisk("#modelToDuplicate").val()
									});
								});
							</script>
						</div>
					</li>
				</ol>
				<div style="display:table;width:70%;margin:12px auto;" ><hr style="width:35%;" class="alignleft" /><span class="alignleft" style="text-align:center;width:15%;margin:0px 12px;" >' . __('ou', 'evarisk') . '</span><hr style="width:35%;" class="alignleft" /></div>';
		}
		return $moreModelChoice;
	}

}