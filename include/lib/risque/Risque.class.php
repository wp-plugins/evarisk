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
	
	static function getScoreRisque($risque)
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
	
	static function getEquivalenceEtalon($idMethode, $score, $date=null)
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
	
	static function getSeuil($quotation)
	{
		global $wpdb;
		
		$quotation = eva_tools::IsValid_Variable($quotation);
		$resultat = $wpdb->get_row( "
			SELECT tableValeurEtalon1.niveauSeuil niveauSeuil
			FROM " . TABLE_VALEUR_ETALON . " tableValeurEtalon1
			WHERE tableValeurEtalon1.valeur >= " . $quotation . "
			AND tableValeurEtalon1.Status = 'Valid'
			AND tableValeurEtalon1.niveauSeuil <> 'NULL'
			AND NOT EXISTS
			(
				SELECT * 
				FROM " . TABLE_VALEUR_ETALON . " tableValeurEtalon2
				WHERE tableValeurEtalon2.valeur >= " . $quotation . "
				AND tableValeurEtalon2.Status = 'Valid'
				AND tableValeurEtalon2.niveauSeuil <> 'NULL'
				AND tableValeurEtalon2.valeur < tableValeurEtalon1.valeur
			)"
		);
		return $resultat->niveauSeuil;
	}	
	
	static function getNiveauRisque($niveauSeuil)
	{
		switch($niveauSeuil)
		{
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
	
	static function getRisque($id)
	{
		global $wpdb;
		$id = eva_tools::IsValid_Variable($id);
		$resultat = $wpdb->get_results( "
			SELECT tableRisque.id id, tableRisque.id_danger id_danger, tableRisque.id_methode id_methode, tableRisque.commentaire commentaire, tableRisque.date date,
				tableAvoirValeur.id_risque id_risque, tableAvoirValeur.id_variable id_variable, tableAvoirValeur.valeur valeur, 
				tableDanger.nom nomDanger, tableDanger.id idDanger, tableDanger.description descriptionDanger, tableDanger.id_categorie idCategorie 
			FROM " . TABLE_RISQUE . " tableRisque, " . TABLE_AVOIR_VALEUR . " tableAvoirValeur, " . TABLE_DANGER . " tableDanger 
			WHERE tableRisque.id=" . mysql_real_escape_string($id) . "
			AND tableAvoirValeur.id_risque=tableRisque.id
			AND tableRisque.id_danger=tableDanger.id");
		return $resultat;
	}
	
	static function getRisques($nomTableElement, $idElement, $status='all', $where = '1', $order='tableRisque.id ASC')
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
		$resultat = $wpdb->get_results( "SELECT tableRisque.id id, tableRisque.id_danger id_danger, tableRisque.id_methode id_methode, tableRisque.commentaire commentaire, tableRisque.date date,tableAvoirValeur.id_risque id_risque, tableAvoirValeur.id_variable id_variable, tableAvoirValeur.valeur valeur, tableDanger.nom nomDanger, tableDanger.id idDanger, tableDanger.description descriptionDanger, tableDanger.id_categorie idCategorie FROM " . TABLE_RISQUE . " tableRisque, " . TABLE_AVOIR_VALEUR . " tableAvoirValeur, " . TABLE_DANGER . " tableDanger WHERE tableRisque.id_element=" . mysql_real_escape_string($idElement) . " AND tableRisque.nomTableElement='" . mysql_real_escape_string($nomTableElement) . "' AND " . $status . " AND " . mysql_real_escape_string($where) . " AND tableAvoirValeur.id_risque = tableRisque.id AND tableRisque.id_danger = tableDanger.id ORDER BY " . mysql_real_escape_string($order));
		return $resultat;
	}
	
	static function getNombreRisques($nomTableElement, $idElement, $status='all', $where = '1', $order='id ASC')
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
	
	static function saveNewRisk ($idRisque, $idDanger, $idMethode, $tableElement, $idElement, $variables, $description)
	{
		global $wpdb;
		global $current_user;

		$idDanger = eva_tools::IsValid_Variable($idDanger);
		$idMethode = eva_tools::IsValid_Variable($idMethode);
		$tableElement = eva_tools::IsValid_Variable($tableElement);
		$idElement = eva_tools::IsValid_Variable($idElement);
		$description = eva_tools::IsValid_Variable($description);
		$description = str_replace("[retourALaLigne]","\n", $description);

		if($idRisque == '')
		{//Ajout d'un risque
			$sql = "INSERT INTO " . TABLE_RISQUE . " (id_danger, id_methode, id_element, nomTableElement, commentaire, date, Status) VALUES (" . mysql_escape_string($idDanger) . ", " . mysql_escape_string($idMethode) . ", " . mysql_escape_string($idElement) . ", '" . mysql_escape_string($tableElement) . "', '" . mysql_escape_string($description) . "', NOW(), 'Valid')";
			$wpdb->query($sql);
			$idRisque = $wpdb->insert_id;
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

			$sql = "
				UPDATE " . TABLE_AVOIR_VALEUR . " 
				SET Status = 'Deleted' 
				WHERE id_risque = " . mysql_escape_string($idRisque) . "
				AND Status = 'Valid'";
			$wpdb->query($sql);
		}

		foreach($variables as $idVariable => $valeurVariable)
		{
			if($valeurVariable != 'undefined')
			{
				$idVariable = eva_tools::IsValid_Variable($idVariable);
				$valeurVariable = eva_tools::IsValid_Variable($valeurVariable);
				$sql = "INSERT INTO " . TABLE_AVOIR_VALEUR . " (id_risque, id_variable, valeur, idEvaluateur, date, Status) VALUES (" . mysql_escape_string($idRisque) . ", " . mysql_escape_string($idVariable) . ", '" . mysql_escape_string($valeurVariable) . "', '" . mysql_real_escape_string($current_user->ID) . "', NOW(), 'Valid')";
				$wpdb->query($sql);
			}
		}

	}

	static function deleteRisk($idRisque, $tableElement, $idElement)
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
	static function getSommeRisque($table, $elementId)
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
}