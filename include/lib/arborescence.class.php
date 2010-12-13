<?php
/**
 * 
 * @author Evarisk
 * @version v5.0
 */
include_once(EVA_CONFIG );
class Arborescence {
	
	static function getIdsFilAriane($table, $element)
	{
		$elementsAncetres = Arborescence::getAncetre($table, $element, "limiteGauche ASC");
		$idsFilAriane = '';
		foreach($elementsAncetres as $elementAncetre)
		{
			$idsFilAriane = $idsFilAriane . '-' . $elementAncetre->id;
		}
		if(strlen($idsFilAriane)>2)
		{
			$idsFilAriane = explode('-', str_replace('-1-', '', $idsFilAriane));
		}
		else
		{
			$idsFilAriane = null;
		}
		return $idsFilAriane;
	}

	static function getElement($table, $id, $where='1')
	{
		global $wpdb;
		$element = $wpdb->get_row( "SELECT * FROM " . $table . " WHERE id=" . $id . " AND " . $where);
		return $element;
	}
	
	static function getRacine($table, $where='1')
	{
		global $wpdb;
		$racine = $wpdb->get_row( "SELECT * FROM " . $table . " table1 WHERE NOT EXISTS(SELECT * FROM " . $table . " table2 WHERE table2.limiteGauche < table1.limiteGauche AND " . $where . ") AND " . $where);
		return $racine;
	}
	
	static function getMaxLimiteDroite($table, $where='1')
	{
		global $wpdb;
		$resultat = $wpdb->get_row( "SELECT * FROM " . $table . " table1 WHERE NOT EXISTS (SELECT * FROM " . $table . " table2 WHERE table1.limiteDroite < table2.limiteDroite AND " . $where . ") AND " . $where);
		return $resultat->limiteDroite;
	}
	
	static function getByLimites($table, $limiteGauche, $limiteDroite, $where='1')
	{
		global $wpdb;
		$resultat = $wpdb->get_results( "SELECT * FROM " . $table . " WHERE " . $where . "  AND Status = 'Valid' AND limiteGauche >= " . $limiteGauche . " AND limiteDroite <= " . $limiteDroite);
		return $resultat;
	}
	
	static function getAncetre($table, $element, $order= "limiteGauche ASC", $where='1')
	{
		global $wpdb;
		$resultat = $wpdb->get_results( "SELECT * FROM " . $table . " WHERE " . $where . "  AND Status = 'Valid' AND limiteGauche < " . $element->limiteGauche . " AND limiteDroite > " . $element->limiteDroite . "	ORDER BY " . $order );
		return $resultat;
	}
	
	static function getDescendants($table, $element, $where='1', $order="id ASC")
	{
		global $wpdb;
		$resultat = $wpdb->get_results( "SELECT * FROM " . $table . " WHERE " . $where . " AND limiteGauche > " . $element->limiteGauche . " AND limiteDroite < " . $element->limiteDroite . " AND Status = 'Valid' ORDER BY " . $order);
		return $resultat;
	}
	
	static function getPere($table, $element, $where='Status=\'Valid\'')
	{
		global $wpdb;
		$resultat = $wpdb->get_row( "
		SELECT *
		FROM " . $table . " table1
		WHERE " . $where . "
		AND table1.limiteGauche < " . $element->limiteGauche . "
		AND table1.limiteDroite > " . $element->limiteDroite . "
		AND NOT
		EXISTS (
			SELECT *
			FROM " . $table . " table2
			WHERE " . $where . "
			AND table2.limiteGauche < " . $element->limiteGauche . "
			AND table2.limiteDroite > " . $element->limiteDroite . "
			AND table1.limiteGauche < table2.limiteGauche
		)
");
		return $resultat;
	}
	
	static function getFils($table, $element, $order="id ASC", $numeroPage=null, $nombreElementsParPage=null, $where='Status=\'Valid\'')
	{	
		global $wpdb;
		$limit = "";
		if($nombreElementsParPage != null)
		{
			$limit = "
		LIMIT " . ($numeroPage - 1) . ", " . $nombreElementsParPage;
		}
		$resultat = $wpdb->get_results(
		"SELECT * 
		FROM " . $table . " table1 
		WHERE " . $where . "
		AND table1.limiteGauche > " . $element->limiteGauche . " 
		AND table1.limiteDroite < " . $element->limiteDroite . " 
		AND NOT EXISTS ( 
			SELECT * 
			FROM " . $table . " table2 
			WHERE " . $where . "
			AND table2.limiteGauche > " . $element->limiteGauche . " 
			AND table2.limiteDroite < " . $element->limiteDroite . " 
			AND table1.limiteGauche > table2.limiteGauche 
			AND table1.limiteDroite < table2.limiteDroite
		)
		ORDER BY " . $order . $limit);
		return $resultat;
	}
	
	/**
	  * Transfer an element in a tree
	  * @param string $table Table name of the element to transfer
	  * @param Element_of_a_tree $racine The element to transfer tree  root
	  * @param Element_of_a_tree $elementFils The element to transfer
	  * @param Element_of_a_tree $elementDestination The element to transfer father-to-be
	  */
	static function deplacerElements($table, $racine, $elementFils, $elementDestination)
	{
		global $wpdb;
		
		unset($tableauElements);
		$limiteGauche = $elementFils->limiteGauche;
		$limiteDroite = $elementFils->limiteDroite;
		$ecart = $limiteDroite - $limiteGauche + 1;
		$limiteDroiteDestination;
		$tableauElements;
		
		/*
		  * On extrait l'élément fils et ses descendants de l'arbre :
		  *	- On déplace l'élément fils et ses descendants avant 0
		  */
		// $limiteDroite majore les limites de l'élément fils et de ses descendants
		$decrement = $limiteDroite + 1;
		// Tout élément dont les limites sont comprises entre $limiteGauche et $limiteDroite est soit  l'élément fils, soit un de ses descendants
		$elements = Arborescence::getByLimites($table, $limiteGauche,$limiteDroite);
		// Pour chaque élément, on déduit le decrement de ses limites
		foreach($elements as $element)
		{
			$sql = "UPDATE " . $table . " SET `limiteGauche`= '" . ($element->limiteGauche  - $decrement ) . "', `limiteDroite`= '" . ($element->limiteDroite - $decrement ) . "' WHERE`id` = '" . $element->id . "'";
			$wpdb->query($sql);
		}
		
		/*
		  * On referme l'arbre :
		  *	- On tri les limites des éléments de l'arbre par ordre croissant
		  *	- On affecte les valeurs de 0 à 2*(nombre d'éléments de l'abre) -1 dans l'ordre
		  */
		// Tout élément dont les limites sont comprises entre la  limite gauche et la limite droite de la racine est un élément de l'arbre
		$elements = Arborescence::getByLimites($table, $racine->limiteGauche, $racine->limiteDroite);
		for($i=0; $i<count($elements); $i++)
		{
			$tableauElements[$i*2][0] = $elements[$i]->limiteGauche;
			$tableauElements[$i*2][1] = 'g';
			$tableauElements[$i*2][2] = $elements[$i]->id;
			$tableauElements[$i*2+1][0] = $elements[$i]->limiteDroite;
			$tableauElements[$i*2+1][1] = 'd';
			$tableauElements[$i*2+1][2] = $elements[$i]->id;
		}
		// On tri le tableau $tableauElements à partir de l'élément 0 de chaque indice
		sort($tableauElements);
		for($i=0; $i<count($tableauElements); $i++)
		{
			if($tableauElements[$i][1] == 'g')
			{
				$sql = "UPDATE " . $table . " SET `limiteGauche`= '" . $i . "' WHERE`id` = '" . $tableauElements[$i][2] . "'";
			}
			else
			{
				if($tableauElements[$i][2] == $elementDestination->id)
				{
					$limiteDroiteDestination = $i;
				}
				$sql = "UPDATE " . $table . " SET `limiteDroite`= '" . $i . "' WHERE`id` = '" . $tableauElements[$i][2] . "'";
			}
			$wpdb->query($sql);
		}
		
		/*
		  * On réintègre l'élément fils :
		  *	- On ouvre l'abre à l'emplacement où l'on veut inserer l'élément fils et ses descendants
		  *	- On insère l'élément fils et ses descendants dans l'arbre
		  */
		// Tout élément dont les limites sont comprises entre $limiteDroiteDestination et la limite droite de la racine est un élément dont les deux limites doivent être déplacé de $ecart
		$elements = Arborescence::getByLimites($table, $limiteDroiteDestination, $racine->limiteDroite);
		foreach($elements as $element)
		{
			$sql = "UPDATE " . $table . " SET `limiteGauche`= '" . ($element->limiteGauche + $ecart) . "', `limiteDroite`= '" . ($element->limiteDroite + $ecart) . "' WHERE`id` = '" . $element->id . "'";
			$wpdb->query($sql);
		}
		// Tout élément dont la limite droite est comprise entre $limiteDroiteDestination et la limite droite de la racine
		// et la limite gauche est avant $limiteDroiteDestination est un élément dont la limite droite doit être déplacé de $ecart
		foreach($elements as $element)
		{
			$elementsApres[] = $element->id;
		}
		$elements = Arborescence::getByLimites($table, $racine->limiteGauche, ($limiteDroiteDestination - 1));
		foreach($elements as $element)
		{
			$elementsAvant[] = $element->id;
		}
		$elementsArbre = Arborescence::getByLimites($table, $racine->limiteGauche, $racine->limiteDroite);
		foreach($elementsArbre as $elementArbre)
		{
			// Tout élément qui n'est ni avant ni après $limiteDroiteDestination la chevauche (donc un élémént à modifier)
			if($elementsAvant != null && $elementsApres != null)
			{
				if((!(in_array($elementArbre->id, $elementsAvant))) && (!(in_array($elementArbre->id, $elementsApres))))
				{
					$sql = "UPDATE " . $table . " SET `limiteDroite`= '" . ($elementArbre->limiteDroite + $ecart) . "' WHERE`id` = '" . $elementArbre->id . "'";
					$wpdb->query($sql);
				}
			}
			elseif($elementsAvant != null)
			{
				if(!(in_array($elementArbre->id, $elementsAvant)))
				{
					$sql = "UPDATE " . $table . " SET `limiteDroite`= '" . ($elementArbre->limiteDroite + $ecart) . "' WHERE`id` = '" . $elementArbre->id . "'";
					$wpdb->query($sql);
				}
			}
			elseif($elementsApres != null)
			{
				if(!(in_array($elementArbre->id, $elementsApres)))
				{
					$sql = "UPDATE " . $table . " SET `limiteDroite`= '" . ($elementArbre->limiteDroite + $ecart) . "' WHERE`id` = '" . $elementArbre->id . "'";
					$wpdb->query($sql);
				}
			}
			else
			{
				$sql = "UPDATE " . $table . " SET `limiteDroite`= '" . ($elementArbre->limiteDroite + $ecart) . "' WHERE`id` = '" . $elementArbre->id . "'";
				$wpdb->query($sql);
			}
		}
		
		// On récupère l'élément fils et ses descendants
		$elements = Arborescence::getByLimites($table, ($limiteGauche - $decrement ), ($limiteDroite - $decrement ));
		$ecartAuNouvelEmplacement = ($limiteDroiteDestination - ($limiteDroite - $decrement ) + $ecart - 1);
		foreach($elements as $element)
		{
			$sql = "UPDATE " . $table . " SET `limiteGauche`= '" . ($element->limiteGauche + $ecartAuNouvelEmplacement) . "', `limiteDroite`= '" . ($element->limiteDroite + $ecartAuNouvelEmplacement) . "' WHERE`id` = '" . $element->id . "'";
			$wpdb->query($sql);
		}
	}


	/**
	*	Get all the tree for a given element
	*
	*	@param mixed $tableElement The element type we want to get the tree
	*	@param integer $idElement The element identifier we want to get the tree
	*
	*	@see completeTreeRecursive()
	*
	*	@return array $completeTree An array with the tree for the element we are on (with all the descendant)
	*/
	static function completeTree($tableElement, $idElement)
	{
		$completeTree = array();

		$racine = Arborescence::getElement($tableElement,$idElement);
		$idTable = $tableElement . '-' . $idElement;

		$elements = Arborescence::getFils(TABLE_GROUPEMENT, $racine, "nom ASC");
		$subElements = EvaGroupement::getUnitesDuGroupement($racine->id);
		$sousTable = TABLE_UNITE_TRAVAIL;

		/*	add the actual element	*/
		$completeTree[$tableElement.'-'.$idElement]['table'] = TABLE_GROUPEMENT;
		$completeTree[$tableElement.'-'.$idElement]['id'] = $racine->id;
		$completeTree[$tableElement.'-'.$idElement]['nom'] = $racine->nom;
		$completeTree[$tableElement.'-'.$idElement]['content'] = array();

		if((count($elements) > 0) || (count($subElements) > 0))
		{
			$completeTree[$tableElement.'-'.$idElement]['content'] = Arborescence::completeTreeRecursive($elements, $racine, $tableElement);
		}

		return $completeTree;
	}

	/**
	*	Get recursively the risqs for an element. (NB: this method is called by bilanParUnite())
	*	
	*	@param mixed $elementsFils An object with the different children of the element we are looking for the risqs
	*	@param mixed $elementPere An object with the parent of the element we are looking for the risqs
	*	@param mixed $table The element type we want to get descendant and risqs
	*	@param mixed $idTable An identifier for the array to keep the link between all elements
	*
	*	@param array $sousElements An array with all sub elements (with they risqs) for a given element
	*/
	static function completeTreeRecursive($elementsFils, $elementPere, $table, $idTable = '')
	{
		$sousElements = $sousElementsEnfants = array();

		if(count($elementsFils) != 0)
		{
			foreach ($elementsFils as $element )
			{
				$idTable = $element->id;
				$sousElements[$idTable]['table'] = TABLE_GROUPEMENT;
				$sousElements[$idTable]['id'] = $element->id;
				$sousElements[$idTable]['nom'] = $element->nom;
				$sousElements[$idTable]['content'] = array();

				$elements_fils = Arborescence::getFils(TABLE_GROUPEMENT, $element, "nom ASC");
				$subElements = EvaGroupement::getUnitesDuGroupement($element->id);
				$trouveElement = count($elements_fils) + count($subElements);			
				if($trouveElement)
				{
					$sousElements[$idTable]['content'] = Arborescence::completeTreeRecursive($elements_fils, $element, $table, $idTable);
				}
			}
		}

		$subElements = EvaGroupement::getUnitesDuGroupement($elementPere->id);
		$i = 0;
		if($idTable == '')
		{
			$idTable = $elementPere->id;
		}
		foreach($subElements as $subElement)
		{
			$sousElements[$idTable][$i]['table'] = TABLE_UNITE_TRAVAIL;
			$sousElements[$idTable][$i]['id'] = $subElement->id;
			$sousElements[$idTable][$i]['nom'] = $subElement->nom;
			$i++;
		}

		return $sousElements;
	}

}