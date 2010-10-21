<?php
/**
 * 
 * @author Soci&eacute;t&eacute; Evarisk
 * @version v5.0
 */
include_once(EVA_CONFIG);

class documentUnique
{

	static function listeRisquePourElement($tableElement, $idElement)
	{
		$lignesDeValeurs = array();
		$temp = Risque::getRisques($tableElement, $idElement, "Valid");
		if($temp != null)
		{
			foreach($temp as $risque)
			{
				$risques['"' . $risque->id . "'"][] = $risque; 
			}
		}
		if($risques != null)
		{
			$i = 0;
			unset($tmpLigneDeValeurs);
			foreach($risques as $risque)
			{
				$idligne = 'ut-' . $risque[0]->id;
				$scriptRisque = $scriptRisque . '<script type="text/javascript">
					$(document).ready(function() {
						$("#' . $idligne . '").dblclick(function(){
							$("#divFormRisque").html(\'<img src="' . PICTO_LOADING . '" />\');
							$("#divFormRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true",  "table":"' . TABLE_RISQUE . '", "act":"load", "idRisque": "' . $risque[0]->id . '", "idElement":"' . $idElement . '", "tableElement":"' . $tableElement . '"});
						});
					});
				</script>';
				$idLignes[] = $idligne;
				
				$idMethode = $risque[0]->id_methode;
				$score = Risque::getScoreRisque($risque);
				$quotation = Risque::getEquivalenceEtalon($idMethode, $score, $risque[0]->date);
				$niveauSeuil = Risque::getSeuil($quotation);

				switch($tableElement)
				{
					case TABLE_GROUPEMENT:
						$element = EvaGroupement::getGroupement($idElement);
						break;
					case TABLE_UNITE_TRAVAIL:
						$element = UniteDeTravail::getWorkingUnit($idElement);
						break;
				}

				$tmpLigneDeValeurs[$quotation][$i][] = array('value' => $element->nom, 'class' => '');
				$tmpLigneDeValeurs[$quotation][$i][] = array('value' => $quotation, 'class' => 'Seuil_' . $niveauSeuil);
				$tmpLigneDeValeurs[$quotation][$i][] = array('value' => $risque[0]->nomDanger, 'class' => '');
				$tmpLigneDeValeurs[$quotation][$i][] = array('value' => nl2br($risque[0]->descriptionDanger), 'class' => '');
				$tmpLigneDeValeurs[$quotation][$i][] = array('value' => nl2br($risque[0]->commentaire), 'class' => '');
				$i++;
			}

			krsort($tmpLigneDeValeurs);
			foreach($tmpLigneDeValeurs as $quotationLigneDeValeur => $contenuLigneDeValeur)
			{
				foreach($contenuLigneDeValeur as $ligneDeValeur)
				{
					$lignesDeValeurs[] = $ligneDeValeur;
				}
			}
		}	
		return $lignesDeValeurs;
	}

	static function bilanRisque($tableElement, $idElement, $typeBilan = 'ligne')
	{
		unset($titres, $classes, $idLignes, $lignesDeValeurs);
		$lignesDeValeurs = array();

		if($tableElement == TABLE_GROUPEMENT)
		{
			/*	Recuperation des unites du groupement	*/
			$listeUnitesDeTravail = EvaGroupement::getUnitesEtGroupementDescendants($idElement);
			if(is_array($listeUnitesDeTravail))
			{
				foreach($listeUnitesDeTravail as $key => $uniteDefinition)
				{
					/*	Recuperation des risques associes a l'unite	*/
					$lignesDeValeurs = array_merge($lignesDeValeurs, documentUnique::listeRisquePourElement($uniteDefinition['table'], $uniteDefinition['value']->id));
				}
			}

			/*	Recuperation des risques associes au groupement	*/
			$lignesDeValeurs = array_merge($lignesDeValeurs, documentUnique::listeRisquePourElement($tableElement, $idElement));

			/*	Si on veut le bilan par ligne	*/
			if($typeBilan == 'ligne')
			{
				{//Cr&eacute;ation de la table	
					{//Script de d&eacute;finition de la dataTable pour la somme des risques par ligne
						$idTable = 'tableBilanRisqueUnitaire' . $tableElement . $idElement . $typeBilan;
						$titres[] = __("&Eacute;l&eacute;ment", 'evarisk');
						$titres[] = __("Quotation", 'evarisk');
						$titres[] = ucfirst(strtolower(sprintf(__("nom %s", 'evarisk'), __("du danger", 'evarisk'))));
						$titres[] = ucfirst(strtolower(sprintf(__("description %s", 'evarisk'), __("du danger", 'evarisk'))));
						$titres[] = ucfirst(strtolower(sprintf(__("commentaire %s", 'evarisk'), __("sur le risque", 'evarisk'))));
						$classes[] = 'columnQuotation';
						$classes[] = 'columnQuotation';
						$classes[] = 'columnNomDanger';
						$classes[] = 'columnDescriptionDanger';
						$classes[] = 'columnCommentaireRisque';

						$scriptVoirRisque = $scriptRisque . '
						<script type="text/javascript">
						$(document).ready(function() {
							$("#' . $idTable . '").dataTable(
							{
							"bPaginate": false, 
							"bLengthChange": false,
							"bAutoWidth": false,
							"bFilter": false,
							"bInfo": false,
							"aoColumns": 
							[
								{ "bSortable": true},
								{ "bSortable": false, "sType": "numeric"},
								{ "bSortable": false},
								{ "bSortable": false},
								{ "bSortable": false}
							],
								"aaSorting": [[1,"desc"]]});
						});
						</script>';

						$recapitulatifRisque = EvaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $scriptVoirRisque);
					}
					
					return $recapitulatifRisque;
				}
			}

			/*	Si on veut le bilan par unit&eacute; de travail	*/
			elseif($typeBilan == 'unite')
			{
				$racine = Arborescence::getRacine($tableElement);
				$nomTable = "bilanRisqueTable";
				$enTeteTable = __("Risques par unit&eacute;", 'evarisk');
				$scriptAfterEvaluationRisques = '<script type="text/javascript">
					$(document).ready(function() {
						$(\'#' . $postBoxId . ' .inside\').each(function(){$(this).addClass("noPadding");});
					});
				</script>';
				$recapitulatifRisque = EvaDisplayDesign::getTableArborescence($racine, TABLE_DUER, $nomTable, $enTeteTable, false, false);
				return $recapitulatifRisque;
			}
			
			/*	OLD VERSION PER UNIT	Si on veut le bilan par unit&eacute; de travail	*/
			else
			{
				$tmpLigneValeurs = array();

				if(is_array($lignesDeValeurs))
				{
					$i = 0;
					foreach($lignesDeValeurs as $key => $ligne)
					{
						if(!isset($tmpLigneValeurs[$ligne[0]['value']]))
						{
							$tmpLigneValeurs[$ligne[0]['value']][1]['value'] = 0;
							$tmpLigneValeurs[$ligne[0]['value']][2]['value'] = 0;
						}
						$tmpLigneValeurs[$ligne[0]['value']][0]['value'] = $ligne[0]['value'];
						$tmpLigneValeurs[$ligne[0]['value']][0]['class'] = '';

						$tmpLigneValeurs[$ligne[0]['value']][1]['value'] += $ligne[1]['value'];
						$tmpLigneValeurs[$ligne[0]['value']][1]['class'] = '';

						$tmpLigneValeurs[$ligne[0]['value']][2]['value'] += 1;
						$tmpLigneValeurs[$ligne[0]['value']][2]['class'] = '';

						$i++;
					}

					unset($lignesDeValeurs);$lignesDeValeurs = array();
					foreach($tmpLigneValeurs as $nomUnite => $contenuUnite)
					{
						$lignesDeValeurs[] = $contenuUnite;
					}
				}

				{//Cr&eacute;ation de la table	
					{//Script de d&eacute;finition de la dataTable pour la somme des risques par ligne
						$idTable = 'tableBilanRisque' . $tableElement . $idElement . $typeBilan;
						$titres[] = __("&Eacute;l&eacute;ment", 'evarisk');
						$titres[] = __("Quotation", 'evarisk');
						$titres[] = ucfirst(strtolower(__("Nombre de danger", 'evarisk')));
						$classes[] = 'columnQuotation';
						$classes[] = 'columnQuotation';
						$classes[] = 'columnNomDanger';

						if(!$noScript)
						{
							$scriptVoirRisque = $scriptRisque . '
							<script type="text/javascript">
							$(document).ready(function() {
								$("#' . $idTable . '").dataTable(
								{
								"bPaginate": false, 
								"bLengthChange": false,
								"bAutoWidth": false,
								"bFilter": false,
								"bInfo": false,
								"aoColumns": 
								[
									{ "bSortable": false},
									{ "bSortable": false, "sType": "numeric"},
									{ "bSortable": false}
								],
								"aaSorting": [[1,"desc"]]});
							});
							</script>';
						}
						else
						{
							$scriptVoirRisque = '';
						}

						$recapitulatifRisque = EvaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $scriptVoirRisque);
					}
					
					return $recapitulatifRisque;
				}
			}
		}
		else
		{
			return __('Imprimer la fiche de l\'unit&eacute;','evarisk');
		}
	}

	static function prepareGenerationDocumentUnique($tableElement, $idElement)
	{
		require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/documentUnique/templateDocumentUnique.tpl.php');
		if($tableElement == TABLE_GROUPEMENT)
		{
			$lastDocumentUnique = documentUnique::getDernierDocumentUnique($tableElement, $idElement);

			unset($formulaireDocumentUniqueParams);
			$formulaireDocumentUniqueParams['#DATEFORM1#'] = date('Y-m-d');
			$formulaireDocumentUniqueParams['#DATEDEBUT1#'] = date('Y-m-d');
			$formulaireDocumentUniqueParams['#DATEFIN1#'] = date('Y-m-d');
			$formulaireDocumentUniqueParams['#NOMENTREPRISE#'] = (isset($lastDocumentUnique->nomSociete) && ($lastDocumentUnique->nomSociete != '')) ? $lastDocumentUnique->nomSociete : '';
			$formulaireDocumentUniqueParams['#TELFIXE#'] = (isset($lastDocumentUnique->telephoneFixe) && ($lastDocumentUnique->telephoneFixe != '')) ? $lastDocumentUnique->telephoneFixe : '';
			$formulaireDocumentUniqueParams['#TELPORTABLE#'] = (isset($lastDocumentUnique->telephonePortable) && ($lastDocumentUnique->telephonePortable != '')) ? $lastDocumentUnique->telephonePortable : '';
			$formulaireDocumentUniqueParams['#TELFAX#'] = (isset($lastDocumentUnique->telephoneFax) && ($lastDocumentUnique->telephoneFax != '')) ? $lastDocumentUnique->telephoneFax : '';
			$formulaireDocumentUniqueParams['#EMETTEUR#'] = (isset($lastDocumentUnique->emetteurDUER) && ($lastDocumentUnique->emetteurDUER != '')) ? $lastDocumentUnique->emetteurDUER : '';
			$formulaireDocumentUniqueParams['#DESTINATAIRE#'] = (isset($lastDocumentUnique->destinataireDUER) && ($lastDocumentUnique->destinataireDUER != '')) ? $lastDocumentUnique->destinataireDUER : '';
			$formulaireDocumentUniqueParams['#NOMDOCUMENT#'] = '';
			$formulaireDocumentUniqueParams['#METHODOLOGIE#'] = (isset($lastDocumentUnique->methodologieDUER) && ($lastDocumentUnique->methodologieDUER != '')) ? $lastDocumentUnique->methodologieDUER : ($methodologieParDefaut);
			$formulaireDocumentUniqueParams['#SOURCES#'] = (isset($lastDocumentUnique->sourcesDUER) && ($lastDocumentUnique->sourcesDUER != '')) ? $lastDocumentUnique->sourcesDUER : ($sourcesParDefaut);

			return 
			'<script type="text/javascript">
				$(document).ready(function() {
					$("#dateCreation").datepicker();
					$("#dateCreation").datepicker("option", {dateFormat: "yy-mm-dd"});

					$("#dateDebutAudit").datepicker();
					$("#dateDebutAudit").datepicker("option", {dateFormat: "yy-mm-dd"});

					$("#dateFinAudit").datepicker();
					$("#dateFinAudit").datepicker("option", {dateFormat: "yy-mm-dd"});

					$("#genererDUER").click(function(){
						$("#divDocumentUnique").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
							"post":"true", 
							"table":"' . TABLE_RISQUE . '", 
							"act":"saveDocumentUnique", 
							"tableElement":"' . $tableElement . '",
							"idElement":"' . $idElement . '", 
							"dateCreation":$("#dateCreation").val(), 
							"dateDebutAudit":$("#dateDebutAudit").val(), 
							"dateFinAudit":$("#dateFinAudit").val(), 
							"nomEntreprise":$("#nomEntreprise").val(),
							"telephoneFixe":$("#telephoneFixe").val(),
							"telephonePortable":$("#telephonePortable").val(),
							"numeroFax":$("#numeroFax").val(),
							"emetteur":$("#emetteur").val(),
							"destinataire":$("#destinataire").val(),
							"nomDuDocument":$("#nomDuDocument").val(),
							"methodologie":$("#methodologie").val(),
							"sources":$("#sources").val()
						});
						$("#divDocumentUnique").html(\'<img src="' . PICTO_LOADING . '" />\');
						$("#divDocumentUnique").html(\'<img src="' . PICTO_LOADING . '" />\');
					});
				});
			</script>' . EvaDisplayDesign::feedTemplate(EvaDisplayDesign::getFormulaireGenerationDUER(), $formulaireDocumentUniqueParams);
		}
		else
		{
			return __('Imprimer la fiche de l\'unit&eacute;','evarisk');
		}
	}

	static function generationDocumentUnique($tableElement, $idElement, $outputType)
	{
		require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/documentUnique/templateDocumentUnique.tpl.php');
		if($outputType == 'pdf')
		{
			require_once('../../../../../../../PetitBoulot/Evarisk_tcpdf/tcpdf/config/lang/eng.php');
			require_once('../../../../../../../PetitBoulot/Evarisk_tcpdf/tcpdf/tcpdf.php');

			// require_once(EVA_LIB_PLUGIN_DIR . 'tcpdf/config/lang/fra.php');
			// require_once(EVA_LIB_PLUGIN_DIR . 'tcpdf/config/configPDF.php');
			// require_once(EVA_LIB_PLUGIN_DIR . 'tcpdf/tcpdf.php');

			// create new PDF document
			$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

			// set document information
			$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('Evarisk');
			$pdf->SetTitle('Document Unique');
			$pdf->SetSubject('TCPDF Tutorial');
			$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

			// set default header data
			$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 021', PDF_HEADER_STRING);

			// set header and footer fonts
			$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
			$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

			// set default monospaced font
			$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

			//set margins
			$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
			$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
			$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

			//set auto page breaks
			$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

			//set image scale factor
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

			//set some language-dependent strings
			$pdf->setLanguageArray($l);

			// ---------------------------------------------------------

			// set font
			$pdf->SetFont('helvetica', '', 9);

			// add a page
			$pdf->AddPage();

			// create some HTML content
			$html = '<h1>Example of HTML text flow</h1>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. <em>Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur?</em> <em>Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</em><br /><br /><b>A</b> + <b>B</b> = <b>C</b> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>B</i> = <i>A</i> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>A</i> = <i>B</i> -&gt; &nbsp;&nbsp; <b>A</b> + <b>B</b> = <b>C</b> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>B</i> = <i>A</i> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>A</i> = <i>B</i> -&gt; &nbsp;&nbsp; <b>A</b> + <b>B</b> = <b>C</b> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>B</i> = <i>A</i> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>A</i> = <i>B</i> -&gt; &nbsp;&nbsp; <b>A</b> + <b>B</b> = <b>C</b> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>B</i> = <i>A</i> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>A</i> = <i>B</i> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <b>A</b> + <b>B</b> = <b>C</b> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>B</i> = <i>A</i> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>A</i> = <i>B</i> -&gt; &nbsp;&nbsp; <b>A</b> + <b>B</b> = <b>C</b> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>B</i> = <i>A</i> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>A</i> = <i>B</i> -&gt; &nbsp;&nbsp; <b>A</b> + <b>B</b> = <b>C</b> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>B</i> = <i>A</i> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>A</i> = <i>B</i> -&gt; &nbsp;&nbsp; <b>A</b> + <b>B</b> = <b>C</b> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>B</i> = <i>A</i> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>A</i> = <i>B</i><br /><br /><b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u>';

			// output the HTML content
			$pdf->writeHTML($html, true, 0, true, 0);

		}

		if($tableElement == TABLE_GROUPEMENT)
		{
			$lastDocumentUnique = documentUnique::getDernierDocumentUnique($tableElement, $idElement);
			$documentUnique = '';
			$nbPageTotal = 1;

			/*	Ajout du sommaire	*/
			$nbPageTotal++;
			unset($documentUniqueParam);
			$documentUniqueParam['#PAGE#'] = $nbPageTotal . '/#NBPAGETOTAL#';
			$documentUniqueParam['#CONTENTPAGE#'] = EvaDisplayDesign::feedTemplate($sommaireDocumentUnique, $pageParam);
			$output = EvaDisplayDesign::feedTemplate($templatePageDocumentUnique, $documentUniqueParam);
			$output = str_replace("
","",str_replace("	","",$output));

			if($outputType == 'html')
			{
				$documentUnique .= $output;
			}
			elseif($outputType == 'pdf')
			{
				// add a page
				$pdf->AddPage();

				// create some HTML content
				$html = EvaDisplayDesign::feedTemplate($sommaireDocumentUnique, $pageParam);

				// output the HTML content
				$pdf->writeHTML($html, true, 0, true, 0);
			}

			/*	Chapitre Administratif	*/
			$nbPageTotal++;
			unset($documentUniqueParam);
			$documentUniqueParam['#PAGE#'] = $nbPageTotal . '/#NBPAGETOTAL#';
			$documentUniqueParam['#CONTENTPAGE#'] = EvaDisplayDesign::feedTemplate($ChapitreAdministratif, $pageParam);
			if($outputType == 'html')
			{
				$documentUnique .= EvaDisplayDesign::feedTemplate($templatePageDocumentUnique, $documentUniqueParam);
			}
			elseif($outputType == 'pdf')
			{
			
			}

			/*	Localisatino et remarques importantes	*/
			$nbPageTotal++;
			unset($documentUniqueParam);
			$documentUniqueParam['#PAGE#'] = $nbPageTotal . '/#NBPAGETOTAL#';
			$documentUniqueParam['#CONTENTPAGE#'] = EvaDisplayDesign::feedTemplate($localisationRemarques, $pageParam);
			if($outputType == 'html')
			{
				$documentUnique .= EvaDisplayDesign::feedTemplate($templatePageDocumentUnique, $documentUniqueParam);
			}
			elseif($outputType == 'pdf')
			{
			
			}

			/*	Chapitre evaluation	*/
			$nbPageTotal++;
			unset($documentUniqueParam);
			$documentUniqueParam['#PAGE#'] = $nbPageTotal . '/#NBPAGETOTAL#';
			$documentUniqueParam['#CONTENTPAGE#'] = EvaDisplayDesign::feedTemplate($chapitreEvaluation, $pageParam);
			if($outputType == 'html')
			{
				$documentUnique .= EvaDisplayDesign::feedTemplate($templatePageDocumentUnique, $documentUniqueParam);
			}
			elseif($outputType == 'pdf')
			{
			
			}

			/*	Methode d'evaluation et quantification	*/
			$nbPageTotal++;
			unset($documentUniqueParam);
			$documentUniqueParam['#PAGE#'] = $nbPageTotal . '/#NBPAGETOTAL#';
			$documentUniqueParam['#CONTENTPAGE#'] = EvaDisplayDesign::feedTemplate($methodeEvaluationQuantification, $pageParam);
			if($outputType == 'html')
			{
				$documentUnique .= EvaDisplayDesign::feedTemplate($templatePageDocumentUnique, $documentUniqueParam);
			}
			elseif($outputType == 'pdf')
			{
			
			}

			/*	Groupes d'utilisateurs	*/
			$nbPageTotal++;
			unset($documentUniqueParam);
			$documentUniqueParam['#PAGE#'] = $nbPageTotal . '/#NBPAGETOTAL#';
			unset($pageParam);
			$pageParam['#GROUPESUTILISATEURS#'] = $lastDocumentUnique->codeHtmlGroupesUtilisateurs;
			$documentUniqueParam['#CONTENTPAGE#'] = EvaDisplayDesign::feedTemplate($groupesUtilisateurs, $pageParam);
			if($outputType == 'html')
			{
				$documentUnique .= EvaDisplayDesign::feedTemplate($templatePageDocumentUnique, $documentUniqueParam);
			}
			elseif($outputType == 'pdf')
			{
			
			}

			/*	Unites de travail	*/
			$nbPageTotal++;
			unset($documentUniqueParam);
			$documentUniqueParam['#PAGE#'] = $nbPageTotal . '/#NBPAGETOTAL#';
			$documentUniqueParam['#CONTENTPAGE#'] = EvaDisplayDesign::feedTemplate($unitesDeTravail, $pageParam);
			if($outputType == 'html')
			{
				$documentUnique .= EvaDisplayDesign::feedTemplate($templatePageDocumentUnique, $documentUniqueParam);
			}
			elseif($outputType == 'pdf')
			{
			
			}

			/*	FER : Fiche d'Evaluation des Risques	*/
			$nbPageTotal++;
			unset($documentUniqueParam);
			$documentUniqueParam['#PAGE#'] = $nbPageTotal . '/#NBPAGETOTAL#';
			$documentUniqueParam['#CONTENTPAGE#'] = EvaDisplayDesign::feedTemplate($ficheDEvaluationDesRisques, $pageParam);
			if($outputType == 'html')
			{
				$documentUnique .= EvaDisplayDesign::feedTemplate($templatePageDocumentUnique, $documentUniqueParam);
			}
			elseif($outputType == 'pdf')
			{
			
			}

			/*	Introduction risques unitaires	*/
			$nbPageTotal++;
			unset($documentUniqueParam);
			$documentUniqueParam['#PAGE#'] = $nbPageTotal . '/#NBPAGETOTAL#';
			$documentUniqueParam['#CONTENTPAGE#'] = EvaDisplayDesign::feedTemplate($introductionRisquesUnitaires, $pageParam);
			if($outputType == 'html')
			{
				$documentUnique .= EvaDisplayDesign::feedTemplate($templatePageDocumentUnique, $documentUniqueParam);
			}
			elseif($outputType == 'pdf')
			{
			
			}

			/*	Risques unitaires	*/
			$nbPageTotal++;
			unset($documentUniqueParam);
			$documentUniqueParam['#PAGE#'] = $nbPageTotal . '/#NBPAGETOTAL#';
			unset($pageParam);
			$pageParam['#RISQUEUNITAIRE#'] = $lastDocumentUnique->codeHtmlRisqueUnitaire;
			$documentUniqueParam['#CONTENTPAGE#'] = EvaDisplayDesign::feedTemplate($risquesUnitaires, $pageParam);
			if($outputType == 'html')
			{
				$documentUnique .= EvaDisplayDesign::feedTemplate($templatePageDocumentUnique, $documentUniqueParam);
			}
			elseif($outputType == 'pdf')
			{
			
			}

			/*	Introduction risques par unite	*/
			$nbPageTotal++;
			unset($documentUniqueParam);
			$documentUniqueParam['#PAGE#'] = $nbPageTotal . '/#NBPAGETOTAL#';
			$documentUniqueParam['#CONTENTPAGE#'] = EvaDisplayDesign::feedTemplate($introductionRisquesParUnite, $pageParam);
			if($outputType == 'html')
			{
				$documentUnique .= EvaDisplayDesign::feedTemplate($templatePageDocumentUnique, $documentUniqueParam);
			}
			elseif($outputType == 'pdf')
			{
			
			}

			/*	Risques par unite	*/
			$nbPageTotal++;
			unset($documentUniqueParam);
			$documentUniqueParam['#PAGE#'] = $nbPageTotal . '/#NBPAGETOTAL#';
			unset($pageParam);
			$pageParam['#RISQUEPARUNITE#'] = $lastDocumentUnique->codeHtmlRisquesParUnite;
			$documentUniqueParam['#CONTENTPAGE#'] = EvaDisplayDesign::feedTemplate($risquesParUnite, $pageParam);
			if($outputType == 'html')
			{
				$documentUnique .= EvaDisplayDesign::feedTemplate($templatePageDocumentUnique, $documentUniqueParam);
			}
			elseif($outputType == 'pdf')
			{
			
			}

			/*	Le plan d'action	*/
			$nbPageTotal++;
			unset($documentUniqueParam);
			$documentUniqueParam['#PAGE#'] = $nbPageTotal . '/#NBPAGETOTAL#';
			$documentUniqueParam['#CONTENTPAGE#'] = EvaDisplayDesign::feedTemplate($planDAction, $pageParam);
			if($outputType == 'html')
			{
				$documentUnique .= EvaDisplayDesign::feedTemplate($templatePageDocumentUnique, $documentUniqueParam);
			}
			elseif($outputType == 'pdf')
			{
			
			}

		}
		else
		{
			return __('Imprimer la fiche de l\'unit&eacute;','evarisk');
		}

		/*	Affichage du document unique final avec le nombre de page total calcule	*/
		unset($documentUniqueParam);
		$documentUniqueParam['#NBPAGETOTAL#'] = $nbPageTotal;

		$documentUniqueParam['#NUMREF#'] = $lastDocumentUnique->referenceDUER;
		$documentUniqueParam['#NOMENTREPRISE#'] = $lastDocumentUnique->nomSociete;
		$documentUniqueParam['#DEBUTAUDIT#'] = date('d/m/Y', strtotime($lastDocumentUnique->dateDebutAudit));
		$documentUniqueParam['#FINAUDIT#'] = date('d/m/Y', strtotime($lastDocumentUnique->dateFinAudit));
		$documentUniqueParam['#DATE#'] = date('d/m/Y', strtotime($lastDocumentUnique->dateGenerationDUER));
		$documentUniqueParam['#NOMPRENOMEMETTEUR#'] = $lastDocumentUnique->emetteurDUER;
		$documentUniqueParam['#NOMPRENOMDESTINATAIRE#'] = $lastDocumentUnique->destinataireDUER  ;
		$documentUniqueParam['#TELFIXE#'] = $lastDocumentUnique->telephoneFixe ;
		$documentUniqueParam['#TELMOBILE#'] = $lastDocumentUnique->telephonePortable ;
		$documentUniqueParam['#TELFAX#'] = $lastDocumentUnique->telephoneFax ;

		$documentUniqueParam['#NOMDOCUMENT#'] = $lastDocumentUnique->nomDUER;
		$documentUniqueParam['#REVISION#'] = $lastDocumentUnique->revisionDUER;

		$documentUniqueParam['#METHODOLOGIE#'] = $lastDocumentUnique->methodologieDUER;
		$documentUniqueParam['#SOURCES#'] = $lastDocumentUnique->sourcesDUER;

		$documentUniqueParam['#DISPODESPLANS#'] = '';
		$documentUniqueParam['#PLANS#'] = $lastDocumentUnique->planDUER;
		$documentUniqueParam['#ALERTE#'] = $lastDocumentUnique->alerteDUER;

		$completeOutput = EvaDisplayDesign::feedTemplate($premiereDeCouvertureDocumentUnique . $documentUnique, $documentUniqueParam);

		if($outputType == 'html')
		{
			return $completeOutput;
		}
		elseif($outputType == 'pdf')
		{
			$pdf->lastPage();
			$pdf->Output($lastDocumentUnique->nomDUER, 'I');
		}
	}

	static function saveNewDocumentUnique ($tableElement, $idElement, $informationDocumentUnique)
	{
		global $wpdb;
		$status = array();

		require_once(EVA_LIB_PLUGIN_DIR . 'users/evaUserGroup.class.php');

		$tableElement = eva_tools::IsValid_Variable($tableElement);
		$idElement = eva_tools::IsValid_Variable($idElement);

		{	/*	Calcul du pourcentage de participant &agrave; l'&eacute;valuation */
			$pourcentageParticipant = 0;
			if(evaUserGroup::getUserNumberInWorkUnit($idElement, $tableElement) > 0)
			{
				$pourcentageParticipant = (count(evaUser::getBindUsers($idElement, $tableElement)) * 100) / evaUserGroup::getUserNumberInWorkUnit($idElement, $tableElement);
			}
			if($pourcentageParticipant >= 75)
			{
				$alerte = __("Le pr&eacute;sent document a &eacute;t&eacute; r&eacute;alis&eacute; pour permettre au chef d'entreprise d'avoir une vision des risques hi&eacute;rarchis&eacute;s dans son &eacute;tablissement. Lors de l'&eacute;valuation, " . $pourcentageParticipant . "% des salari&eacute;s de l'entreprise ont particip&eacute; &agrave; la d&eacute;marche d'&eacute;valuation des risques. Nous consid&eacute;rons que le quota des 75% des salari&eacute;s impliqu&eacute;s dans la d&eacute;marche a donc &eacute;t&eacute; atteint. Ce ratio est significatif de la participation du personnel, gage de r&eacute;ussite de la d&eacute;marche.");
			}
			else
			{
				$alerte = __("La tranche des 75% des salari&eacute;s &eacute;valu&eacute;s n'a pas &eacute;t&eacute; atteinte, puisque seul " . $pourcentageParticipant . "% de ces derniers ont &eacute;t&eacute;s impliqu&eacute;s, et la participation du personnel n'est donc pas suffisament significative.");
			}
		}

		{	/*	R&eacute;vision du document unique, en fonction de l'element et de la date de g&eacute;n&eacute;ration	*/
			$revision = '';
			$query = 
				"SELECT max(revisionDUER) AS lastRevision
				FROM " . TABLE_DUER . " 
				WHERE element = '" . mysql_escape_string($tableElement) . "' 
					AND elementId = '" . mysql_escape_string($idElement) . "' 
					AND dateGenerationDUER = '" . mysql_escape_string($informationDocumentUnique['dateCreation']) . "' ";
			$revision = $wpdb->get_row($query);
			$revisionDocumentUnique = $revision->lastRevision + 1;
		}

		{	/*	G&eacute;n&eacute;ration de la r&eacute;f&eacute;rence du document unique	*/
			$referenceDUER = substr($informationDocumentUnique['emetteur'], 0, 1) . str_replace('-', '', $informationDocumentUnique['dateCreation']) . '-V' . $revisionDocumentUnique;
		}

		{	/*	Enregistrement d'un document unique	*/
			$query = 
				"INSERT INTO " . TABLE_DUER . " 
					(id, element, elementId, referenceDUER, dateGenerationDUER, nomDUER, dateDebutAudit, dateFinAudit, nomSociete, telephoneFixe, telephonePortable, telephoneFax, emetteurDUER, destinataireDUER, revisionDUER, planDUER, codeHtmlGroupesUtilisateurs, codeHtmlRisqueUnitaire, codeHtmlRisquesParUnite, methodologieDUER, sourcesDUER, alerteDUER, conclusionDUER) 
				VALUES	
					('', '" . mysql_escape_string($tableElement) . "', '" . mysql_escape_string($idElement) . "', '" . mysql_escape_string($referenceDUER) . "', '" . mysql_escape_string($informationDocumentUnique['dateCreation']) . "', '" . mysql_escape_string($informationDocumentUnique['nomDuDocument']) . "', '" . mysql_escape_string($informationDocumentUnique['dateDebutAudit']) . "', '" . mysql_escape_string($informationDocumentUnique['dateFinAudit']) . "', '" . mysql_escape_string($informationDocumentUnique['nomEntreprise']) . "', '" . mysql_escape_string($informationDocumentUnique['telephoneFixe']) . "', '" . mysql_escape_string($informationDocumentUnique['telephonePortable']) . "', '" . mysql_escape_string($informationDocumentUnique['numeroFax']) . "', '" . mysql_escape_string($informationDocumentUnique['emetteur']) . "', '" . mysql_escape_string($informationDocumentUnique['destinataire']) . "', '" . mysql_escape_string($revisionDocumentUnique) . "', '', '" . mysql_escape_string(evaUserGroup::afficheListeGroupeDU($tableElement, $idElement, true)) . "', '" . mysql_escape_string(documentUnique::bilanRisque($tableElement, $idElement, 'ligne')) . "', '" . mysql_escape_string(documentUnique::bilanRisque($tableElement, $idElement, 'unite')) . "', '" . ($informationDocumentUnique['methodologie']) . "', '" . ($informationDocumentUnique['sources']) . "', '" . mysql_escape_string($alerte) . "', '')";
			if($wpdb->query($query) === false)
			{
				$status['result'] = 'error'; 
				$status['errors']['query_error'] = __('Une erreur est survenue lors de l\'enregistrement', 'evarisk');
			}
			else
			{
				$status['result'] = 'ok';
			}
		}

		return $status;
	}

	static function getDernierDocumentUnique($tableElement, $idElement)
	{
		global $wpdb;
		$lastDocumentUnique = array();

		$query = 
			"SELECT * 
			FROM " . TABLE_DUER . "
			WHERE element = '" . mysql_escape_string($tableElement) . "'
				AND elementId = '" . mysql_escape_string($idElement) . "'
			ORDER BY id DESC
			LIMIT 1";
		$lastDocumentUnique = $wpdb->get_row($query);

		return $lastDocumentUnique;
	}

}