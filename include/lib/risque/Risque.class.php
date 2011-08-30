<?php
/**
 * 
 * @author Soci&eacute;t&eacute; Evarisk
 * @version v5.0
 */
include_once(EVA_CONFIG);
include_once(EVA_LIB_PLUGIN_DIR . 'methode/methodeEvaluation.class.php');
include_once(EVA_LIB_PLUGIN_DIR . 'methode/eva_variable.class.php');

class Risque {
	
	function getScoreRisque($risque)
	{
		$methode = MethodeEvaluation::getMethod($risque[0]->id_methode);
		$listeVariables = MethodeEvaluation::getVariablesMethode($methode->id, $risque[0]->date);
		unset($listeIdVariables);
		foreach($listeVariables as $ordre => $variable)
		{
			$listeIdVariables['"' . $variable->id . '"'][]=$ordre;
		}
		unset($listeValeurs);
		foreach($risque as $ligneRisque)
		{
			foreach($listeIdVariables['"' . $ligneRisque->id_variable . '"'] as $ordre)
			{
				$listeValeurs[$ordre] = Eva_variable::getValeurAlternative($ligneRisque->id_variable, $ligneRisque->valeur, $risque[0]->date);
			}
		}
		$listeOperateursComplexe = MethodeEvaluation::getOperateursMethode($methode->id, $risque[0]->date);
		unset($listeOperateurs);
		foreach($listeOperateursComplexe as $operateurComplexe)
		{
			$listeOperateurs[] = $operateurComplexe->operateur;
		}
		$listeValeursSimples;
		$listeOperateursSimple;


		//résolution des opération de forte priorité (i.e. * et /)
		$scoreRisque = $listeValeurs[0];
		$numeroValeur = 0;
		if($listeOperateurs != null)
		{
			// invariant de boucle : la valeur $listeValeurs[$numeroValeur] est traité
			foreach($listeOperateurs as $operateur)
			{
				$numeroValeur = $numeroValeur + 1;
				switch($operateur)
				{
					case '*' : 
						$scoreRisque = $scoreRisque * $listeValeurs[$numeroValeur];
						break;
					case '/' : 
						$scoreRisque = $scoreRisque / $listeValeurs[$numeroValeur];
						break;
					//default <=> opérateur de faible priorité (i.e. + et -)
					default : 
						$listeValeursSimples[] = $scoreRisque;
						$listeOperateursSimples[] = $operateur;
						$scoreRisque = $listeValeurs[$numeroValeur];
				}
			}
		}
		//Comme il y a une valeur de plus que d'operateur, on la range à la fin
		$listeValeursSimples[] = $scoreRisque;

		//résolution du score
		$scoreRisque = $listeValeursSimples[0];
		$numeroValeur = 0;
		if(isset($listeOperateursSimples) && ($listeOperateursSimples != null))
		{
			// invariant de boucle : la valeur $listeValeursSimples[$numeroValeur] est traité
			foreach($listeOperateursSimples as $operateur)
			{
				$numeroValeur = $numeroValeur + 1;
				switch($operateur)
				{
					case '+' : 
						$scoreRisque = $scoreRisque + $listeValeursSimples[$numeroValeur];
						break;
					case '-' : 
						$scoreRisque = $scoreRisque - $listeValeursSimples[$numeroValeur];
						break;
					default : break;
				}
			}
		}
		return $scoreRisque;
	}	
	
	function getEquivalenceEtalon($idMethode, $score, $date=null)
	{
		global $wpdb;
		
		if($date==null)
		{
			$date=date('Y-m-d H:i:s');
		}
		
		$score = eva_tools::IsValid_Variable($score);
		$idMethode = eva_tools::IsValid_Variable($idMethode);
		$resultat = $wpdb->get_row("SELECT tableEquivalenceEtalon1.id_valeur_etalon equivalenceEtalon
			FROM " . TABLE_EQUIVALENCE_ETALON . " tableEquivalenceEtalon1
			WHERE tableEquivalenceEtalon1.valeurMaxMethode >= " . $score . "
			AND tableEquivalenceEtalon1.id_methode = " . $idMethode . "
			AND tableEquivalenceEtalon1.date <= '" . $date . "'
			AND NOT EXISTS
			(
				SELECT * 
				FROM " . TABLE_EQUIVALENCE_ETALON . " tableEquivalenceEtalon2
				WHERE tableEquivalenceEtalon2.valeurMaxMethode >= " . $score . "
				AND tableEquivalenceEtalon2.id_methode = " . $idMethode . "
				AND tableEquivalenceEtalon2.date <= '" . $date . "'
				AND tableEquivalenceEtalon2.date > tableEquivalenceEtalon1.date
				AND tableEquivalenceEtalon2.id_valeur_etalon < tableEquivalenceEtalon1.id_valeur_etalon
			)");
		return $resultat->equivalenceEtalon;
	}	
	
	function getSeuil($quotation)
	{
		global $wpdb;
		
		$quotation = eva_tools::IsValid_Variable($quotation);
		$resultat = $wpdb->get_row( "
			SELECT tableValeurEtalon1.niveauSeuil niveauSeuil
			FROM " . TABLE_VALEUR_ETALON . " tableValeurEtalon1
			WHERE tableValeurEtalon1.valeur <= " . $quotation . "
			AND tableValeurEtalon1.Status = 'Valid'
			AND tableValeurEtalon1.niveauSeuil != 'NULL'
			AND NOT EXISTS
			(
				SELECT * 
				FROM " . TABLE_VALEUR_ETALON . " tableValeurEtalon2
				WHERE tableValeurEtalon2.valeur >= " . $quotation . "
				AND tableValeurEtalon2.Status = 'Valid'
				AND tableValeurEtalon2.niveauSeuil != 'NULL'
				AND tableValeurEtalon2.valeur < tableValeurEtalon1.valeur
			)
			ORDER BY tableValeurEtalon1.niveauSeuil DESC
			LIMIT 1"
		);
		return (int)$resultat->niveauSeuil;
	}	
	
	function getNiveauRisque($niveauSeuil)
	{
		switch($niveauSeuil)
		{
			case 0:
				$niveauRisque = __(EVA_RISQUE_SEUIL_0_NOM, 'evarisk');
				break;
			case 1:
				$niveauRisque = __(EVA_RISQUE_SEUIL_1_NOM, 'evarisk');
				break;
			case 2:
				$niveauRisque = __(EVA_RISQUE_SEUIL_2_NOM, 'evarisk');
				break;
			case 3:
				$niveauRisque = __(EVA_RISQUE_SEUIL_3_NOM, 'evarisk');
				break;
			case 4:
				$niveauRisque = __(EVA_RISQUE_SEUIL_4_NOM, 'evarisk');
				break;
			case 5:
				$niveauRisque = __(EVA_RISQUE_SEUIL_5_NOM, 'evarisk');
				break;
			case 6:
				$niveauRisque = __(EVA_RISQUE_SEUIL_6_NOM, 'evarisk');
				break;
		}
		return $niveauRisque;
	}
	
	function getRisque($id)
	{
		global $wpdb;
		$id = eva_tools::IsValid_Variable($id);

		$query = $wpdb->prepare("SELECT tableRisque.*,
				tableAvoirValeur.date date, tableAvoirValeur.Status status, tableAvoirValeur.id_risque id_risque, tableAvoirValeur.id_variable id_variable, tableAvoirValeur.valeur valeur, 
				tableDanger.nom nomDanger, tableDanger.id idDanger, tableDanger.description descriptionDanger, tableDanger.id_categorie idCategorie 
			FROM " . TABLE_RISQUE . " tableRisque, 
				" . TABLE_AVOIR_VALEUR . " tableAvoirValeur, 
				" . TABLE_DANGER . " tableDanger 
			WHERE tableRisque.id=" . mysql_real_escape_string($id) . "
				AND tableAvoirValeur.Status = 'Valid'
				AND tableAvoirValeur.id_risque=tableRisque.id
				AND tableRisque.id_danger=tableDanger.id");
		$resultat = $wpdb->get_results($query);

		return $resultat;
	}
	
	function getRisques($nomTableElement = 'all', $idTableElement = 'all', $status='all', $where = '1', $order='tableRisque.id ASC')
	{
		global $wpdb;
		$where = eva_tools::IsValid_Variable($where);
		$order = eva_tools::IsValid_Variable($order);
		if($status=='all')
		{
			$status = '1';
		}
		else
		{
			$status = "tableRisque.Status = '" . $status . "'";
		}

		$tableElement = $idElement = "1";
		if($nomTableElement != 'all')
		{
			$tableElement = "tableRisque.nomTableElement='" . mysql_real_escape_string($nomTableElement) . "' ";
		}
		if($idTableElement != 'all')
		{
			$idElement = "tableRisque.id_element = '" . mysql_real_escape_string($idTableElement) . "' ";
		}

		$query = 
			"SELECT tableRisque.id id, tableRisque.id_danger id_danger, tableRisque.id_methode id_methode, tableRisque.commentaire commentaire, tableRisque.date date,
				tableAvoirValeur.id_risque id_risque, tableAvoirValeur.id_variable id_variable, tableAvoirValeur.valeur valeur, 
				tableDanger.nom nomDanger, tableDanger.id idDanger, tableDanger.description descriptionDanger, tableDanger.id_categorie idCategorie 
			FROM " . TABLE_RISQUE . " tableRisque, 
				" . TABLE_AVOIR_VALEUR . " tableAvoirValeur, 
				" . TABLE_DANGER . " tableDanger 
			WHERE " . $tableElement . "
				AND " . $idElement . "
				AND " . $status . " 
				AND " . mysql_real_escape_string($where) . " 
				AND tableAvoirValeur.id_risque = tableRisque.id 
				AND tableAvoirValeur.Status = 'Valid'
				AND tableRisque.id_danger = tableDanger.id 
			ORDER BY " . mysql_real_escape_string($order);
		$resultat = $wpdb->get_results( $query );

		return $resultat;
	}	

	function getriskLinkToTask($idRisque, $id_tache, $beforeAfter)
	{
		global $wpdb;

		$query = 
			"SELECT 
				R.id id, R.id_danger id_danger, R.id_methode id_methode, R.commentaire commentaire, R.date date,
				VR.id_risque id_risque, VR.id_variable id_variable, VR.valeur valeur, 
				D.nom nomDanger, D.id idDanger, D.description descriptionDanger, D.id_categorie idCategorie
			FROM " . TABLE_RISQUE . " R
				INNER JOIN " . TABLE_DANGER . " D ON (D.id = R.id_danger)
				INNER JOIN " . TABLE_AVOIR_VALEUR . " VR ON (VR.id_risque = R.id)
				INNER JOIN " . TABLE_LIAISON_TACHE_ELEMENT . " LTE ON ((LTE.id_element = VR.id_evaluation) AND (LTE.table_element = '" . TABLE_AVOIR_VALEUR . "') AND (LTE.wasLinked = '" . $beforeAfter . "') AND (LTE.id_tache = '" . $id_tache . "'))
			WHERE VR.id_risque = '" . mysql_real_escape_string($idRisque) . "'
				AND VR.Status != 'Deleted' ";
		$listeVariableAvecValeur = $wpdb->get_results( $query );

		return $listeVariableAvecValeur;
	}

	function getNombreRisques($nomTableElement, $idElement, $status='all', $where = '1', $order='id ASC')
	{
		global $wpdb;
		$where = eva_tools::IsValid_Variable($where);
		$order = eva_tools::IsValid_Variable($order);
		if($status=='all')
		{
			$status = '1';
		}
		else
		{
			$status = "Status = '" . $status . "'";
		}
		$resultat = $wpdb->get_row( "SELECT count(id) nombreRisques FROM " . TABLE_RISQUE . " WHERE id_element=" . mysql_real_escape_string($idElement) . " AND nomTableElement='" . mysql_real_escape_string($nomTableElement) . "' AND " . $status . " AND " . mysql_real_escape_string($where) . " ORDER BY " . mysql_real_escape_string($order));
		return $resultat->nombreRisques;
	}
	
	function saveNewRisk ($idRisque, $idDanger, $idMethode, $tableElement, $idElement, $variables, $description, $histo)
	{
		global $wpdb;
		global $current_user;

		$idDanger = eva_tools::IsValid_Variable($idDanger);
		$idMethode = eva_tools::IsValid_Variable($idMethode);
		$tableElement = eva_tools::IsValid_Variable($tableElement);
		$idElement = eva_tools::IsValid_Variable($idElement);
		$description = eva_tools::IsValid_Variable($description);
		$description = str_replace("[retourALaLigne]","\n", $description);
		$description = str_replace("’","'", $description);
		$histoStatus = 'Valid';

		if($idRisque == '')
		{//Ajout d'un risque
			$sql = "INSERT INTO " . TABLE_RISQUE . " (id_danger, id_methode, id_element, nomTableElement, commentaire, date, Status) VALUES (" . mysql_escape_string($idDanger) . ", " . mysql_escape_string($idMethode) . ", " . mysql_escape_string($idElement) . ", '" . mysql_escape_string($tableElement) . "', '" . mysql_escape_string($description) . "', NOW(), 'Valid')";
			$idRisque = 0;
			if($wpdb->query($sql))
			{
				$idRisque = $wpdb->insert_id;
				echo '
<script type="text/javascript" >
	actionMessageShow("#message' . TABLE_RISQUE . '", "' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le risque a bien &eacute;t&eacute; ajout&eacute;', 'evarisk') . '</strong></p>') . '");
	setTimeout(\'actionMessageHide("#message' . TABLE_RISQUE . '")\',7500);
</script>';
			}
			else
			{
				echo '
<script type="text/javascript" >
	actionMessageShow("#message' . TABLE_RISQUE . '", "' . addslashes('<p><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;<strong>' . __('Le risque n\'a pas pu &ecirc;tre ajout&eacute;', 'evarisk') . '</strong></p>') . '");
	setTimeout(\'actionMessageHide("#message' . TABLE_RISQUE . '")\',7500);
</script>';
			}
		}
		else
		{//Mise à jour d'un risque
			$sql = "
				UPDATE " . TABLE_RISQUE . " 
				SET id_danger = " . mysql_escape_string($idDanger) . ",
					id_methode = " . mysql_escape_string($idMethode) . ",
					id_element = " . mysql_escape_string($idElement) . ",
					nomTableElement = '" . mysql_escape_string($tableElement) . "',
					commentaire = '" . mysql_escape_string($description) . "',
					date = NOW(),
					Status = 'Valid' 
				WHERE id = " . mysql_escape_string($idRisque);
			$wpdb->query($sql);

			if($histo != 'false')
			{
				$sql = "
					UPDATE " . TABLE_AVOIR_VALEUR . " 
					SET Status = 'Moderated' 
					WHERE id_risque = " . mysql_escape_string($idRisque) . "
					AND Status = 'Valid'";
				$wpdb->query($sql);
			}
			else
			{
				$sql = "
					UPDATE " . TABLE_AVOIR_VALEUR . " 
					SET Status = 'Deleted' 
					WHERE id_risque = " . mysql_escape_string($idRisque) . "
					AND Status = 'Valid'";
				$wpdb->query($sql);
			}
		}

		$sql = "SELECT MAX(id_evaluation) + 1 AS newId FROM " . TABLE_AVOIR_VALEUR;
		$newId = $wpdb->get_row($sql);
		if((INT)$newId->newId <= 0)$newId->newId = 1;
		foreach($variables as $idVariable => $valeurVariable)
		{
			if($valeurVariable != 'undefined')
			{
				$idVariable = eva_tools::IsValid_Variable($idVariable);
				$valeurVariable = eva_tools::IsValid_Variable($valeurVariable);
				$sql = "INSERT INTO " . TABLE_AVOIR_VALEUR . " (id_risque, id_evaluation, id_variable, valeur, idEvaluateur, date, Status) VALUES (" . mysql_escape_string($idRisque) . ", " . mysql_real_escape_string($newId->newId) . ", " . mysql_escape_string($idVariable) . ", '" . mysql_escape_string($valeurVariable) . "', '" . mysql_real_escape_string($current_user->ID) . "', NOW(), '" . mysql_real_escape_string($histoStatus) . "')";
				$wpdb->query($sql);
			}
		}

		return $idRisque;
	}

	function deleteRisk($idRisque, $tableElement, $idElement)
	{
		global $wpdb;
		$status = 'error';

		$query = 
			$wpdb->prepare(
				"UPDATE " . TABLE_RISQUE . " 
				SET Status = 'Deleted'
				WHERE id = '%s'
					AND id_element = '%s'
					AND nomTableElement = '%s'"
				, $idRisque, $idElement, $tableElement);
		if($wpdb->query($query))
		{
			$status = 'ok';
		}

		return $status;
	}

	/**
	*	Get a sum of the different risqs for an element (with the subelements)
	*
	*	@param mixed $table The element type we want to get the risqs sum for
	*	@param integer $elementId The element identifier we want to ger the risqs sum for
	*
	*	@return array $info An array with the sum of risqs
	*/
	function getSommeRisque($table, $elementId)
	{
		$temp = Risque::getRisques($table, $elementId, "Valid");
		if($temp != null)
		{
			foreach($temp as $risque)
			{
				$risques['"' . $risque->id . "'"][] = $risque; 
			}
		}
		$sumR = 0;
		if(isset($risques) && ($risques != null))
		{
			foreach($risques as $risque)
			{
				$idMethode = $risque[0]->id_methode;
				$score = Risque::getScoreRisque($risque);
				$sumR += Risque::getEquivalenceEtalon($idMethode, $score, $risque[0]->date);
			}
		}
		$scoreRisqueGroupement = $sumR;
		$info['value'] = $scoreRisqueGroupement;
		$info['class'] = '';

		return $info;
	}

	/**
	*
	*/
	function getTableQuotationRisque($tableElement, $idElement)
	{
		switch($tableElement)
		{
			case TABLE_RISQUE :
				$risque = Risque::getRisque($idElement);
				{//Création de la table
					unset($tableauVariables);
					foreach($risque as $ligneRisque)
					{
						$valeurVariables[$ligneRisque->id_variable] = $ligneRisque->valeur;
					}
					$methode = MethodeEvaluation::getMethod($risque[0]->id_methode);
					$listeVariables = MethodeEvaluation::getVariablesMethode($methode->id, $risque[0]->date);
					foreach($listeVariables as $ordre => $variable)
					{
						$tableauVariables[] = array('nom' => $variable->nom, 'valeur' => $valeurVariables[$variable->id]);
					}

					unset($titres,$classes, $idLignes, $lignesDeValeurs);
					$idLignes = null;
					$idTable = 'tableDemandeAction' . $tableElement . $idElement;
					$titres[] = __("Id.", 'evarisk');
					$titres[] = __("Quotation actuelle", 'evarisk');
					$titres[] = ucfirst(strtolower(sprintf(__("nom %s", 'evarisk'), __("du danger", 'evarisk'))));
					$titres[] = ucfirst(strtolower(sprintf(__("commentaire %s", 'evarisk'), __("sur le risque", 'evarisk'))));
					$classes[] = 'columnRId';
					$classes[] = 'columnQuotation';
					$classes[] = 'columnNomDanger';
					$classes[] = 'columnCommentaireRisque';

					$idligne = 'risque-' . $risque[0]->id;
					$idLignes[] = $idligne;

					$idMethode = $risque[0]->id_methode;
					$score = Risque::getScoreRisque($risque);
					$quotation = Risque::getEquivalenceEtalon($idMethode, $score, $risque[0]->date);
					$niveauSeuil = Risque::getSeuil($quotation);

					unset($ligneDeValeurs);
					$ligneDeValeurs[] = array('value' => ELEMENT_IDENTIFIER_R . $risque[0]->id, 'class' => '');
					$ligneDeValeurs[] = array('value' => $quotation, 'class' => 'risque' . $niveauSeuil . 'Text');
					$ligneDeValeurs[] = array('value' => $risque[0]->nomDanger, 'class' => '');
					$ligneDeValeurs[] = array('value' => nl2br($risque[0]->commentaire), 'class' => '');
					foreach($tableauVariables as $variable)
					{
						$titres[] = substr($variable['nom'], 0, 3) . '.';
						$classes[] = 'columnVariableRisque';
						$ligneDeValeurs[] = array('value' => $variable['valeur'], 'class' => '');
					}
					$lignesDeValeurs[] = $ligneDeValeurs;

					$lignesDeValeurs = (isset($lignesDeValeurs))?$lignesDeValeurs:null;
					$script = '<script type="text/javascript">
						evarisk(document).ready(function(){
							evarisk("#' . $idTable . ' tfoot").remove();
						});
					</script>';

					return EvaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script);
				}
				break;
			default :
				return 'Pensez &agrave; <b>ajouter</b> le <b>cas ' . $tableElement . '</b> dans le <b>switch</b> ligne <b>' . __LINE__ . '</b> du fichier "' . dirname(__FILE__) . '\<b>' . basename(__FILE__) . '</b>"<br />';
				break;
		}
	}

	/**
	*
	*/
	function getTableQuotationRisqueAvantApresAC($tableElement, $idElement, $actionCorrective, $idDiv)
	{
		unset($titres, $classes, $idLignes, $lignesDeValeurs);
		$idLignes = null;
		$idTable = 'tableSuiviAction' . $tableElement . $idElement;
		$titres[] = __(" ", 'evarisk');
		$titres[] = __("Quotation", 'evarisk');
		$classes[] = '';
		$classes[] = 'columnQuotation';

		$idLignes[] = 'risque-' . $actionCorrective->getId();

		{/*	Get the risk before the corrective action	*/
			unset($risqueAvantAC);$risqueAvantAC = array();
			$risqueAvantAC = Risque::getriskLinkToTask($actionCorrective->getIdFrom(), $actionCorrective->getId(), 'before');

			unset($tableauVariables);
			foreach($risqueAvantAC as $ligneRisque)
			{
				$valeurVariables[$ligneRisque->id_variable] = $ligneRisque->valeur;
			}
			$methode = MethodeEvaluation::getMethod($risqueAvantAC[0]->id_methode);
			$listeVariables = MethodeEvaluation::getVariablesMethode($methode->id, $risqueAvantAC[0]->date);
			foreach($listeVariables as $ordre => $variable)
			{
				$tableauVariables[] = array('nom' => $variable->nom, 'valeur' => $valeurVariables[$variable->id]);
			}

			$idMethode = $risqueAvantAC[0]->id_methode;
			$score = Risque::getScoreRisque($risqueAvantAC);
			$quotation = Risque::getEquivalenceEtalon($idMethode, $score, $risqueAvantAC[0]->date);
			$niveauSeuil = Risque::getSeuil($quotation);

			unset($ligneDeValeurs);
			$ligneDeValeurs[] = array('value' => __('Avant', 'evarisk'), 'class' => '');
			if(!is_array($tableauVariables) || (count($tableauVariables) <= 0))
			{
				$ligneDeValeurs[] = array('value' => __('Non &eacute;valu&eacute;', 'evarisk'), 'class' => '');
			}
			else
			{
				$ligneDeValeurs[] = array('value' => $quotation, 'class' => 'risque' . $niveauSeuil . 'Text');
			}
			if(is_array($tableauVariables) && (count($tableauVariables) > 0))
			{
				foreach($tableauVariables as $variable)
				{
					$titres[] = substr($variable['nom'], 0, 3) . '.';
					$classes[] = 'columnVariableRisque';
					$ligneDeValeurs[] = array('value' => $variable['valeur'], 'class' => '');
				}
			}
			$lignesDeValeurs[] = $ligneDeValeurs;
		}
		{/*	Get the risk after the corrective action	*/
			unset($risqueApresAC);$risqueApresAC = array();
			$risqueApresAC = Risque::getriskLinkToTask($actionCorrective->getIdFrom(), $actionCorrective->getId(), 'after');
			unset($tableauVariables);$tableauVariables = array();
			foreach($risqueApresAC as $ligneRisque)
			{
				$valeurVariables[$ligneRisque->id_variable] = $ligneRisque->valeur;
			}

			$methode = MethodeEvaluation::getMethod($risqueApresAC[0]->id_methode);
			$listeVariables = MethodeEvaluation::getVariablesMethode($methode->id, $risqueApresAC[0]->date);
			foreach($listeVariables as $ordre => $variable)
			{
				$tableauVariables[] = array('nom' => $variable->nom, 'valeur' => $valeurVariables[$variable->id]);
			}

			$idMethode = $risqueApresAC[0]->id_methode;
			$score = Risque::getScoreRisque($risqueApresAC);
			$quotation = Risque::getEquivalenceEtalon($idMethode, $score, $risqueApresAC[0]->date);
			$niveauSeuil = Risque::getSeuil($quotation);

			unset($ligneDeValeurs);
			$ligneDeValeurs[] = array('value' => __('Apr&eacute;s', 'evarisk'), 'class' => '');
			if(!is_array($tableauVariables) || (count($tableauVariables) <= 0))
			{
				$ligneDeValeurs[] = array('value' => __('Non &eacute;valu&eacute;', 'evarisk'), 'class' => '');
			}
			else
			{
				$ligneDeValeurs[] = array('value' => $quotation, 'class' => 'risque' . $niveauSeuil . 'Text');
			}
			if(is_array($tableauVariables) && (count($tableauVariables) > 0))
			{
				foreach($tableauVariables as $variable)
				{
					$ligneDeValeurs[] = array('value' => $variable['valeur'], 'class' => '');
				}
			}
			$lignesDeValeurs[] = $ligneDeValeurs;
		}

		$lignesDeValeurs = (isset($lignesDeValeurs)) ? $lignesDeValeurs : null;
		$script = '<script type="text/javascript">
			evarisk(document).ready(function(){
				evarisk("#' . $idTable . ' tfoot").remove();
			});
		</script>';

		return '<div id="' . $idDiv . '-affichage-quotation" class="affichageAction" style="margin:6px 0px;" >' . EvaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script) . '</div>';
	}

	function getRisqueAssociePhoto($idPicture)
	{
		global $wpdb;
		$output = '';

		$query = $wpdb->prepare(
			"SELECT RISQUE.*
			FROM " . TABLE_PHOTO_LIAISON . " AS PICTURE_LINK
				INNER JOIN " . TABLE_RISQUE . " AS RISQUE ON ((RISQUE.id = PICTURE_LINK.idElement) AND (RISQUE.Status = 'Valid'))
			WHERE PICTURE_LINK.idPhoto = '%d'
				AND PICTURE_LINK.tableElement = '%s' 
				AND PICTURE_LINK.status = 'valid' ",
			$idPicture, TABLE_RISQUE
		);
		$queryResult = $wpdb->get_results($query);
		if(count($queryResult) > 0)
		{
			$riskArray = array();
			$i = 0;
			foreach($queryResult as $risk)
			{
				$risque = Risque::getRisque($risk->id);
				$score = Risque::getEquivalenceEtalon($risk->id_methode, Risque::getScoreRisque($risque), $risk->date);

				$output .= '<div class="clear" id="loadRiskId' . $risk->id . '" ><span class="riskAssociatedToPicture" >-&nbsp;' . evaDanger::getDanger($risk->id_danger)->nom . '&nbsp;(' . $score . ')&nbsp;:&nbsp;' . $risk->commentaire . '</span><span id="deleteRiskId' . $risk->id . '" class="ui-icon deleteLinkBetweenRiskAndPicture alignright" title="' . __('Supprimer cette liaison', 'evarisk') . '" >&nbsp;</span></div>';
			}

			$script = '
<script type="text/javascript" >
	evarisk(document).ready(function(){
		evarisk(".riskAssociatedToPicture").unbind("click");
		evarisk(".riskAssociatedToPicture").click(function(){
			evarisk("#formRisque").html(evarisk("#loadingImg").html());
			tabChange("#formRisque", "#ongletAjouterRisque");
			evarisk("#formRisque").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {"post":"true",	"table":"' . TABLE_RISQUE . '", "act":"load", "idRisque": evarisk(this).attr("id").replace("loadRiskId", ""), "idElement":"' . $queryResult[0]->id_element . '", "tableElement":"' . $queryResult[0]->nomTableElement . '"});
		});
		evarisk(".deleteLinkBetweenRiskAndPicture").unbind("click");
		evarisk(".deleteLinkBetweenRiskAndPicture").click(function(){
			if(confirm(convertAccentToJS("' . __('&Ecirc;tes vous sur de vouloir supprimer cette liaison?', 'evarisk') . '"))){
				evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
				{
					"post":"true",
					"table":"' . TABLE_RISQUE . '",
					"tableElement":"' . TABLE_RISQUE . '",
					"idElement":evarisk(this).attr("id").replace("deleteRiskId", ""),
					"act":"unAssociatePicture",
					"idPicture":evarisk(this).parent().parent().closest("div").attr("id").replace("riskAssociatedToPicturepicture_", "").replace("_", "")
				});
			}
		});
		evarisk(".riskAssociatedToPicture").draggable({
			start: function(event, ui){
				draggedObjectFather = evarisk(this).parent().closest("div").attr("id").replace("riskAssociatedToPicture", "");
			}
		});
	});
</script>';
			$output = '<fieldset class="clear" style="white-space:normal;margin:3px 12px;width:90%;" ><legend ><span style="text-decoration:underline;" >' . __('Risques associ&eacute;s &agrave; la photo', 'evarisk') . '</span>&nbsp;:&nbsp;<span class="bold" >' . __('Nom danger(Score) : Commentaire', 'evarisk') . '</span></legend>' . $output . '</fieldset>' . $script;
		}
		else
		{
			$output = '<div style="margin:3px 12px;" >' . __('Aucun risque n\'est associ&eacute; &agrave; cette photo', 'evarisk') . '</div>';
		}

		return $output;
	}

	function getRisqueNonAssociePhoto($tableElement, $idElement)
	{
		global $wpdb;
		$output = '';

		$query = $wpdb->prepare(
			"SELECT RISQUE.*
			FROM " . TABLE_RISQUE . " AS RISQUE
			WHERE RISQUE.status = 'Valid'
				AND RISQUE.id_element = '%s'
				AND RISQUE.nomTableElement = '%s'
				AND RISQUE.id NOT IN (
					SELECT idElement 
					FROM " . TABLE_PHOTO_LIAISON . " 
					WHERE tableElement = '%s'
						AND status = 'valid'
				)",
			$idElement, $tableElement, TABLE_RISQUE
		);
		$queryResult = $wpdb->get_results($query);
		if(count($queryResult) > 0)
		{
			$riskArray = array();
			$i = 0;
			foreach($queryResult as $risk)
			{
				$risque = Risque::getRisque($risk->id);
				$score = Risque::getEquivalenceEtalon($risk->id_methode, Risque::getScoreRisque($risque), $risk->date);

				$output .= '<div class="clear riskAssociatedToPicture" id="loadRiskId' . $risk->id . '" >-&nbsp;' . evaDanger::getDanger($risk->id_danger)->nom . '&nbsp;(' . $score . ')&nbsp;:&nbsp;' . $risk->commentaire . '</div>';
			}

			$script = '
<script type="text/javascript" >
	evarisk(document).ready(function(){
		evarisk("#seeRiskToAssociate").click(function(){
			evarisk(".riskAssociatedToPicture").draggable();
			evarisk("#riskToAssociate").toggle();
		});
	});
</script>';
			$output = '
<fieldset class="clear pointer" style="white-space:normal;margin:3px 12px;width:100%;" >
	<legend >
		<div id="seeRiskToAssociate" class="alignleft pointer" >
			<img id="seeRiskToAssociatePic" src="' . PICTO_EXPAND . '" alt="' . __('collapsor', 'evarisk') . '" style="vertical-align:middle;" />
			<span style="vertical-align:middle;" id="addRiskForPictureText' . $currentId . '" >' . __('Risques non associ&eacute;s &agrave; une photo', 'evarisk') . '</span>
		</div>
	</legend>
	<div id="riskToAssociate" style="display:none;" >
		<div class="clear bold" id="riskToAssociateExplanation" >-&nbsp;' . __('Nom danger(Score) : Commentaire', 'evarisk') . '</div>
		' . $output . '
	</div>
</fieldset>' . $script;
		}

		return $output;
	}

/*	START OF RISK STAT	*/
	function getRiskRange($rangeType)
	{
		$completeRiskList = Risque::getRisques('all', 'all', 'Valid', '1', 'tableRisque.id ASC');
		if($completeRiskList != null)
		{
			foreach($completeRiskList as $risque)
			{
				$risques["'" . $risque->id . "'"][] = $risque; 
			}
		}
		$lowerRisk = $higherRisk = 0;
		if(isset($risques) && ($risques != null))
		{
			foreach($risques as $risque)
			{
				$idMethode = $risque[0]->id_methode;
				$score = Risque::getScoreRisque($risque);
				$riskLevel = Risque::getEquivalenceEtalon($idMethode, $score, $risque[0]->date);
				if(($riskLevel > $higherRisk))
				{
					$higherRisk = $riskLevel;
				}
				if(($riskLevel < $lowerRisk))
				{
					$lowerRisk = $riskLevel;
				}
			}
		}

		$riskRange['HIGHER_RISK'] = $higherRisk;
		$riskRange['LOWER_RISK'] = $lowerRisk;

		return $riskRange;
	}

	function dashBoardStats()
	{
		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT 
				(
					SELECT COUNT(id)
					FROM " . TABLE_RISQUE . " 
					WHERE Status = 'Valid'
				) AS TOTAL_RISK_NUMBER
			"
		);

		return $wpdb->get_row($query);
	}
/*	END OF RISK STAT	*/

}