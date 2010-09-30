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
		require_once(EVA_LIB_PLUGIN_DIR . 'risque/Risque.class.php');
		
		if(((int)$idElement) == 0)
		{
			$script = '<script type="text/javascript">
					$(document).ready(function() {
						$("#postBoxHeaderUniteTravail").hide();
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
				$uniteTravail = UniteDeTravail::getWorkingUnit($idElement);
				$nomUniteTravail = $uniteTravail->nom;
				$scoreRisqueUniteTravail = UniteDeTravail::getScoreRisque($idElement);
				$niveauRisqueUniteTravail = UniteDeTravail::getNiveauRisque($scoreRisqueUniteTravail);
				$seuilRisqueUniteTravail = Risque::getSeuil($scoreRisqueUniteTravail);
				$groupementPere = EvaGroupement::getGroupement($uniteTravail->id_groupement);
				// $responsables = UniteDeTravail::getResponsables($idElement);
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
			$renduPage = '<div id="enTeteDroite">';
			$renduPage = $renduPage . '
					<div id="Informations">
					<div id="nomElement" class="titleDiv">';
			$idTitreWU = 'titreWU' . $idElement;
			$workingUnitsNames = UniteDeTravail::getWorkingUnitsName();
			$workingUnitsNames[] = "";
			
			$valeurActuelleIn = '$("#' . $idTitreWU . '").val ()in {';
			foreach($workingUnitsNames as $workingUnitName)
			{
				$valeurActuelleIn = $valeurActuelleIn . "'" . addslashes($workingUnitName) . "':'', ";
			}
			$valeurActuelleIn = substr($valeurActuelleIn, 0, strlen($valeurActuelleIn) - 2);
			$valeurActuelleIn = $valeurActuelleIn . "}";
			$idButton = 'validChangeTitre';
			$script = '<script type="text/javascript">
						$(document).ready(function(){
							$("#' . $idButton . '").hide();
							$("#' . $idButton . '").click(function(){
								$("#nom_unite_travail").val($("#' . $idTitreWU . '").val());
								$("#save").click();
							});
						})
					</script>';
			$renduPage = $renduPage . EvaDisplayInput::afficherInput('button', 'validChangeTitre', 'Valider', null, null, 'validChangeTitre', false, false, 1,'','','',$script,'right',true);
			$script = '<script type="text/javascript">
						$(document).ready(function(){
							$("#' . $idTitreWU . '").keyup(function(){
								$("#nom_unite_travail").val($("#' . $idTitreWU . '").val());
								if($("#nom_unite_travail").val() != "")
								{
									$("#nom_unite_travail").removeClass("form-input-tip");
								}
								else
								{
									$("#nom_unite_travail").addClass("form-input-tip");							
								}
								if(' . $valeurActuelleIn . ')
								{
									$("#' . $idButton . '").hide();
								}
								else
								{
									$("#' . $idButton . '").show();
								}
							});
						})
					</script>';
			$renduPage = $renduPage . EvaDisplayInput::afficherInput('text', $idTitreWU, $nomUniteTravail, null, null, $idTitreWU, false, false, 255,'titleInfo', '','85%', $script);
			$renduPage = $renduPage . '</div>
					<div class="mainInfosDiv">
						<div class="mainInfos1 alignleft" style="width: 68%">
							<p class="">
								<span id="miniFilAriane">' . $miniFilAriane . '</span><br />
								' . $texteResponsable . ' : <strong>' . $nomResponsables . '</strong><br />
							</p>
						</div>
						<div class="mainInfos2 alignleft" style="width: 30%">
							<p>
								<strong>' . __('Niveau de risque', 'evarisk') . '</strong>
							</p>
							<p class="risqueInfo risque' . $seuilRisqueUniteTravail . 'Info risqueInfo' . TABLE_UNITE_TRAVAIL . $idElement . '">
								' . $niveauRisqueUniteTravail . '
							</p>
						</div>
					</div>
				</div>';
			$renduPage = $renduPage . '</div>
				<br class="clear" />';
			echo $renduPage;
		}
	}
?>