<?php
/*
 * @version v5.0
 */
	//Postbox definition
	$postBoxTitle = __('R&eacute;capitulatif', 'evarisk') . (!empty($_REQUEST['table']) && !empty($_REQUEST['id']) ? Arborescence::display_element_main_infos( $_REQUEST['table'], $_REQUEST['id'] ) : '');
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
		require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/groupement/eva_groupement.class.php');
		require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/uniteDeTravail/uniteDeTravail.class.php');
		require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/documentUnique/documentUnique.class.php');
		require_once(EVA_LIB_PLUGIN_DIR . 'risque/Risque.class.php');

		if(((int)$idElement) == 0)
		{
			$script = '<script type="text/javascript">
					digirisk(document).ready(function() {
						digirisk("#postBoxHeaderUniteTravail").hide();
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
					$scoreRisqueUniteTravail += $risk[2]['value'];
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

			$valeurActuelleIn = 'digirisk("#' . $idTitreWU . '").val ()in {';
			foreach($workingUnitsNames as $workingUnitName)
			{
				$valeurActuelleIn = $valeurActuelleIn . "'" . addslashes($workingUnitName) . "':'', ";
			}
			$valeurActuelleIn = substr($valeurActuelleIn, 0, strlen($valeurActuelleIn) - 2);
			$valeurActuelleIn = $valeurActuelleIn . "}";
			$idButton = 'validChangeTitre';
			$script = '<script type="text/javascript">
						digirisk(document).ready(function(){
							digirisk("#' . $idButton . '").hide();
							digirisk("#' . $idButton . '").click(function(){
								digirisk("#ajax-response").load("' . EVA_INC_PLUGIN_URL . 'ajax.php",
								{
									"post": "true",
									"table": "' . TABLE_UNITE_TRAVAIL . '",
									"act": "updateByField",
									"id": ' . $idElement . ',
									"whatToUpdate": "nom",
									"whatToSet": digirisk("#' . $idTitreWU . '").val()
								});
							});
						})
					</script>';
			if(current_user_can('digi_edit_unite') || current_user_can('digi_edit_unite_' . $idElement))
			{
				$renduPage .= EvaDisplayInput::afficherInput('button', 'validChangeTitre', 'Valider', null, null, 'validChangeTitre', false, false, 1,'','','',$script,'',true);
			}
			$script = '<script type="text/javascript">
						digirisk(document).ready(function(){
							digirisk("#' . $idTitreWU . '").focus(function(){
								digirisk(this).select();
								digirisk("#' . $idTitreWU . '").addClass("titleInfoSelected");
							});
							digirisk("#' . $idTitreWU . '").blur(function(){
								if(!digirisk("#' . $idButton . '").is(":visible")){
									digirisk("#' . $idTitreWU . '").removeClass("titleInfoSelected");
								}
							});
							digirisk("#' . $idTitreWU . '").keyup(function(){
								digirisk("#nom_unite_travail").val(digirisk("#' . $idTitreWU . '").val());
								if(digirisk("#nom_unite_travail").val() != ""){
									digirisk("#nom_unite_travail").removeClass("form-input-tip");
								}
								else{
									digirisk("#nom_unite_travail").addClass("form-input-tip");
								}
								if(' . $valeurActuelleIn . '){
									digirisk("#' . $idButton . '").hide();
								}
								else{
									digirisk("#' . $idButton . '").show();
								}
							});
						})
					</script>';
			$renduPage .= '<div class="alignleft element_identifier_recap" >' . ELEMENT_IDENTIFIER_UT . $idElement . '&nbsp;-&nbsp;</div>';
			if(current_user_can('digi_edit_groupement') || current_user_can('digi_edit_groupement_' . $idElement))
			{
				$renduPage .= EvaDisplayInput::afficherInput('text', $idTitreWU, $nomUniteTravail, null, null, $idTitreWU, false, false, 255,'titleInfo', '','', $script, 'left');
			}
			else
			{
				$renduPage .= $nomUniteTravail;
			}
			$renduPage .= '
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