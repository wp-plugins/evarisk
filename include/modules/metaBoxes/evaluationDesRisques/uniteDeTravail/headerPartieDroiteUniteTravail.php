<?php
/*
 * @version v5.0
 */
	//Postbox definition
	$postBoxTitle = __('R&eacute;capitulatif', 'evarisk');
	$postBoxId = 'postBoxHeaderUniteTravail';
	$postBoxCallbackFunction = 'getHeaderUniteTravailPostBoxBody';
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_UNITES_DE_TRAVAIL, 'rightSide', 'default');
	 
	function getHeaderUniteTravailPostBoxBody($arguments)
	{
		$tableElement = $arguments['tableElement'];
		$idElement = $arguments['idElement'];
		
		include_once(EVA_CONFIG);
		require_once(EVA_LIB_PLUGIN_DIR . 'arborescence.class.php');
		require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayDesign.class.php');
		require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php' );
		require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/groupement/eva_groupement.class.php'); 
		require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/uniteDeTravail/uniteDeTravail.class.php'); 
		require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/documentUnique/documentUnique.class.php'); 
		require_once(EVA_LIB_PLUGIN_DIR . 'risque/Risque.class.php');
		
		if(((int)$idElement) == 0)
		{
			$script = '<script type="text/javascript">
					evarisk(document).ready(function() {
						evarisk("#postBoxHeaderUniteTravail").hide();
					});
				</script>';
			echo $script;
		}
		else
		{
			$nomUniteTravail = __('Nouvelle unit&eacute; de travail', 'evarisk');
			$responsables = null;
			if($idElement!=null)
			{	
				$uniteTravail = eva_UniteDeTravail::getWorkingUnit($idElement);
				$nomUniteTravail = $uniteTravail->nom;
				$groupementPere = EvaGroupement::getGroupement($uniteTravail->id_groupement);
				// $responsables = eva_UniteDeTravail::getResponsables($idElement);

				$scoreRisqueUniteTravail = 0;
				$riskAndSubRisks = eva_documentUnique::listRisk($tableElement, $idElement);
				foreach($riskAndSubRisks as $risk)
				{
					$scoreRisqueUniteTravail += $risk[1]['value'];
				}
				$nombreRisqueUniteTravail = count($riskAndSubRisks);
			}
			else
			{
				$groupementPere = EvaGroupement::getGroupement($argument['idPere']);
			}
			$ancetres = Arborescence::getAncetre(TABLE_GROUPEMENT, $groupementPere);
			$miniFilAriane = __('Hi&eacute;rarchie', 'evarisk') . ' : ';
			foreach($ancetres as $ancetre)
			{
				if($ancetre->nom != "Groupement Racine")
				{
					$miniFilAriane = $miniFilAriane . $ancetre->nom . ' &raquo; ';
				}
			}
			$nomResponsables = '';
			if(count($responsables) > 0)
			{
				foreach($responsables as $responsable)
				{
						$nomResponsables = $nomResponsables . $responsable->prenom . ' ' . $responsable->nom . ', ';
				}
			}
			if(count($responsables) > 1)
			{
				$texteResponsable = __('Responsables', 'evarisk');
			}
			else
			{
				$texteResponsable = __('Responsable', 'evarisk');
			}
			$nomResponsables = substr($nomResponsables, 0, strlen($nomResponsables) - 2);
			if($groupementPere->nom != "Groupement Racine")
				$miniFilAriane = $miniFilAriane . $groupementPere->nom;
			$renduPage = 
			'<div id="enTeteDroite">
				<div id="Informations">
					<div id="nomElement" class="titleDiv">';
			$idTitreWU = 'titreWU' . $idElement;
			$workingUnitsNames = eva_UniteDeTravail::getWorkingUnitsName();
			$workingUnitsNames[] = "";
			
			$valeurActuelleIn = 'evarisk("#' . $idTitreWU . '").val ()in {';
			foreach($workingUnitsNames as $workingUnitName)
			{
				$valeurActuelleIn = $valeurActuelleIn . "'" . addslashes($workingUnitName) . "':'', ";
			}
			$valeurActuelleIn = substr($valeurActuelleIn, 0, strlen($valeurActuelleIn) - 2);
			$valeurActuelleIn = $valeurActuelleIn . "}";
			$idButton = 'validChangeTitre';
			$script = '<script type="text/javascript">
						evarisk(document).ready(function(){
							evarisk("#' . $idButton . '").hide();
							evarisk("#' . $idButton . '").click(function(){
								evarisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", 
								{
									"post": "true", 
									"table": "' . TABLE_UNITE_TRAVAIL . '",
									"act": "updateByField",
									"id": ' . $idElement . ',
									"whatToUpdate": "nom",
									"whatToSet": evarisk("#' . $idTitreWU . '").val()
								});
							});
						})
					</script>';
			$renduPage .= EvaDisplayInput::afficherInput('button', 'validChangeTitre', 'Valider', null, null, 'validChangeTitre', false, false, 1,'','','',$script,'',true);
			$script = '<script type="text/javascript">
						evarisk(document).ready(function(){
							evarisk("#' . $idTitreWU . '").focus(function(){
								evarisk(this).select();
								evarisk("#' . $idTitreWU . '").addClass("titleInfoSelected");
							});
							evarisk("#' . $idTitreWU . '").blur(function(){
								if(!evarisk("#' . $idButton . '").is(":visible")){
									evarisk("#' . $idTitreWU . '").removeClass("titleInfoSelected");
								}
							});
							evarisk("#' . $idTitreWU . '").keyup(function(){
								evarisk("#nom_unite_travail").val(evarisk("#' . $idTitreWU . '").val());
								if(evarisk("#nom_unite_travail").val() != ""){
									evarisk("#nom_unite_travail").removeClass("form-input-tip");
								}
								else{
									evarisk("#nom_unite_travail").addClass("form-input-tip");							
								}
								if(' . $valeurActuelleIn . '){
									evarisk("#' . $idButton . '").hide();
								}
								else{
									evarisk("#' . $idButton . '").show();
								}
							});
						})
					</script>';
			$renduPage .= EvaDisplayInput::afficherInput('text', $idTitreWU, $nomUniteTravail, null, null, $idTitreWU, false, false, 255,'titleInfo', '','', $script, 'left') . '
					</div>
					<div class="mainInfosDiv">
						<div class="mainInfos1 alignleft" style="width: 68%">
							<p class="">
								<span id="miniFilAriane">' . $miniFilAriane . '</span><br />
								' . $texteResponsable . ' : <strong>' . $nomResponsables . '</strong><br />
							</p>
						</div>
						<div class="mainInfos2 alignleft" style="width: 30%">
							<p>
								<span class="bold" >' . __('Somme des risques', 'evarisk') . '</span>&nbsp;:&nbsp;<span id="riskSum' . $tableElement . $idElement . '" >' . $scoreRisqueUniteTravail . '</span><br/>
								<span class="bold" >' . __('Nombre de risques', 'evarisk') . '</span>&nbsp;:&nbsp;<span id="riskNb' . $tableElement . $idElement . '" >' . $nombreRisqueUniteTravail . '</span>
							</p>
						</div>
					</div>
				</div>
			</div>
			<br class="clear" />';

			echo $renduPage;
		}
	}

?>