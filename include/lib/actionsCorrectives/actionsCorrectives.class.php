<?php

class actionsCorrectives
{

	/**
	*	Get activity (sub-task) link with a given risk list
	*
	*	@param string $table_element The element type we want to check to correctiv action linked with
	*	@param integer $id_element The element identifier we want to check to correctiv action linked with
	*	@param array $risks Optionnal An array with the risk list to get directly correctiv action linked
	*	@param array $constraint Optionnal An array with the different constraint to check before getting correctiv actions
	*
	*	@return array $correctiv_actions An array with the list of linked correctiv actions 
	*/
	function get_activity_associated_to_risk($table_element = '', $id_element = '', $risks = '', $constraint = ''){
		$correctiv_actions = array();

		if($risks == ''){
			$riskList = Risque::getRisques($table_element, $id_element, "Valid");
			if($riskList != null){
				foreach($riskList as $risque){
					$risks[$risque->id][] = $risque; 
				}
			}
		}

		if(is_array($risks) && (count($risks) > 0)){
			foreach($risks as $idRisque => $infosRisque){
				$actionsCorrectives = '';
				$taches = new EvaTaskTable();
				$tacheLike = new EvaTask();
				$tacheLike->setIdFrom($idRisque);
				$tacheLike->setTableFrom(TABLE_RISQUE);
				if(is_array($constraint) && (count($constraint) > 0)){
					foreach($constraint as $constraint_name => $constraint_value){
						switch($constraint_name){
							case 'ProgressionStatus':
								$tacheLike->setProgressionStatus($constraint_value);
							break;
							case 'hasPriority':
								// $tacheLike->sethasPriority($constraint_value);
							break;
						}
					}
				}
				$taches->getTasksLike($tacheLike);
				$correctiv_actions[] = $taches->getTasks();
			}
		}

		return $correctiv_actions;
	}

	/**
	*	Check the progression status in order to output the correct progression term
	*
	*	@param string $progression_status The progression status taken directly from database
	*
	* @return string $statutProgression A translated term for the current progression_status
	*/
	function check_progression_status_for_output($progression_status){
		$statutProgression = '-';

		switch($progression_status)
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

		return $statutProgression;
	}

	/**
	*	Create an output with the different risk associated to an element and the different correctiv actions associated to the risks
	*
	*	@param array $risques The list of risks associated to the current element
	*	@param string $dataTableOptions Allows to define option for the outputed table
	*
	*	@return string A table with the risks list
	*/
	function output_correctiv_action_by_risk($risques, $dataTableOptions = ''){
		if(count($risques) > 0){
			$idTable = 'suiviActionsCorrectiveElement';
			$titres = array('', __('Id.', 'evarisk'), __('Quotation', 'evarisk'), __('Danger', 'evarisk'), __('Commentaire', 'evarisk'));
			$classes = array('columnCollapser', 'columnRId', 'columnQuotation', 'columnNomDanger', 'columnCommentaireRisque');
			foreach($risques as $idRisque => $infosRisque){
				$tachesActionsCorrectives = actionsCorrectives::get_activity_associated_to_risk('', '', array($idRisque => ''), '');

				unset($valeurs);
				if((count($tachesActionsCorrectives[0]) > 0) || (count($risques) == 1)){
					$valeurs[] = array('value' => '<img id="pic_line' . ELEMENT_IDENTIFIER_R . $idRisque . '" src="' . EVA_IMG_ICONES_PLUGIN_URL . 'details_open.png" alt="open_close_row" class="open_close_row" />', 'class' => '');
					$valeurs[] = array('value' => ELEMENT_IDENTIFIER_R . $idRisque, 'class' => '');
						$idMethode = $infosRisque[0]->id_methode;
						$score = Risque::getScoreRisque($infosRisque);
						$quotation = Risque::getEquivalenceEtalon($idMethode, $score, $infosRisque[0]->date);
						$niveauSeuil = Risque::getSeuil($quotation);
						$valeurs[] = array('value' => $quotation, 'class' => 'risque' . $niveauSeuil . 'Text');
					$valeurs[] = array('value' => $infosRisque[0]->nomDanger, 'class' => '');
					$valeurs[] = array('value' => $infosRisque[0]->commentaire, 'class' => '');

					foreach($infosRisque as $variable){
						$var = eva_Variable::getVariable($variable->id_variable);
						if(!isset($t[$var->nom])){
							$titres[] = substr($var->nom, 0, 3) . '.';
							$classes[] = 'columnVariableRisque';
							$t[$var->nom] = 1;
						}
						$valeurs[] = array('value' => $variable->valeur, 'class' => '');
					}

					$idLignes[] = ELEMENT_IDENTIFIER_R . $idRisque . '_suiviActionCorrectives';
					$lignesDeValeurs[] = $valeurs;
				}
			}

			$scriptTableauSuiviModification = '
<script type="text/javascript">
var oTable;

/* Formating function for row details */
function fnFormatDetails ( nTr ){
var aData = oTable.fnGetData( nTr );
var sOut = "<div id=\'" + aData[1] + "\' >&nbsp;</div>";

return sOut;
}

evarisk(document).ready(function(){
oTable = evarisk("#' . $idTable . '").dataTable({
	"aaSorting": [[2, "desc"]],
	"bInfo": false,' . $dataTableOptions . '
	"oLanguage":{
		"sUrl": "' . EVA_INC_PLUGIN_URL . 'js/dataTable/jquery.dataTables.common_translation.txt"
	}
});
evarisk("#' . $idTable . ' tfoot").remove();

evarisk(".open_close_row").click(function(){
	var nTr = this.parentNode.parentNode;
	if ( this.src.match("details_close") ){
		/* This row is already open - close it */
		this.src = "' . EVA_IMG_ICONES_PLUGIN_URL . 'details_open.png";
		oTable.fnClose( nTr );
	}
	else{
		/* Open this row */
		this.src = "' . EVA_IMG_ICONES_PLUGIN_URL . 'details_close.png";
		oTable.fnOpen( nTr, fnFormatDetails(nTr), "details" );
		var containerId = evarisk(this).attr("id").replace("pic_line", "");
		evarisk("#" + containerId).html(evarisk("#loadingImg").html());
		evarisk("#" + containerId).load("' . EVA_INC_PLUGIN_URL . 'ajax.php",{
			"post":"true", 
			"table":"' . TABLE_RISQUE . '", 
			"act":"loadAssociatedTask",
			"idRisque": containerId.replace("' . ELEMENT_IDENTIFIER_R . '", ""),
			"extra":"correctiv_action_follow"
		});
	}
});
});
</script>';
			return evaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $scriptTableauSuiviModification);
		}
		else
		{
			return __('Il n\'y a aucun risque pour cet &eacute;l&eacute;ment', 'evarisk');
		}
	}


	/**
	*	Create the output for main correctiv action page
	*/
	function actionsCorrectivesMainPage(){
		$messageInfo = '';

		$_POST['table'] = TABLE_TACHE;
		$titrePage = __("Actions Correctives", 'evarisk');
		$icone = PICTO_LTL_ACTION;
		$titreIcone = "Icone actions correctives";
		$altIcon = "Icone AC";
		$titreFilAriane= __("Actions correctives", 'evarisk');
		if(!isset($_POST['affichage'])){
			$_POST['affichage'] = "affichageListe";
		}
		include_once(EVA_LIB_PLUGIN_DIR . 'classicalPage.php' );	
		// On enlève le choix de l'affichage
?>
		<script type="text/javascript">
			evarisk(document).ready(function(){
				evarisk('#choixAffichage').hide();
			});
		</script>
<?php
		if(isset($_GET['elt']) && ($_GET['elt'] != '')){
			echo
				'<script type="text/javascript">
					evarisk(document).ready(function(){
						setTimeout(function(){
							evarisk("#' . $_GET['elt'] . '").click();
						},3000);
					})
				</script>';
		}
	}

}