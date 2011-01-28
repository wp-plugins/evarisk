<?php
/*
 * @version v5.0
 */
	//Postbox definition
	$postBoxTitle = __('R&eacute;capitulatif', 'evarisk');
	$postBoxId = 'postBoxHeaderGroupement';
	$postBoxCallbackFunction = 'getHeaderGroupementPostBoxBody';
	add_meta_box($postBoxId, $postBoxTitle, $postBoxCallbackFunction, PAGE_HOOK_EVARISK_GROUPEMENTS, 'rightSide', 'default');
	 
	function getHeaderGroupementPostBoxBody($arguments)
	{
		$tableElement = $arguments['tableElement'];
		$idElement = $arguments['idElement'];
		
		require_once(EVA_CONFIG);
		require_once(EVA_LIB_PLUGIN_DIR . 'arborescence.class.php');
		require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayDesign.class.php');
		require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayInput.class.php');
		require_once(EVA_LIB_PLUGIN_DIR . 'evaGoogleMaps.class.php' );
		require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/groupement/eva_groupement.class.php'); 
		require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/uniteDeTravail/uniteDeTravail.class.php'); 
		require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/documentUnique/documentUnique.class.php'); 
		require_once(EVA_LIB_PLUGIN_DIR . 'risque/Risque.class.php');

		if(((int)$idElement) == 0)
		{
			$script = '<script type="text/javascript">
					evarisk(document).ready(function() {
						evarisk("#postBoxHeaderGroupement").hide();
					});
				</script>';
			echo $script;
		}
		else
		{//En-tête
			$responsables = null;
			if($idElement!=null)
			{	
				$groupement = EvaGroupement::getGroupement($idElement);
				$nomGroupement = $groupement->nom;
				$groupementPere = Arborescence::getPere($tableElement, $groupement);
				// $responsables[] = '';

				$scoreRisqueGroupement = 0;
				$riskAndSubRisks = documentUnique::listRisk($tableElement, $idElement);
				foreach($riskAndSubRisks as $risk)
				{
					$scoreRisqueGroupement += $risk[1]['value'];
				}
				$nombreRisqueGroupement = count($riskAndSubRisks);
			}
			else
			{
				$nomGroupement = __('Nouveau groupement', 'evarisk');
				$groupementPere = EvaGroupement::getGroupement($argument['idPere']);
			}
			$ancetres = Arborescence::getAncetre(TABLE_GROUPEMENT, $groupementPere);
			$miniFilAriane = '';
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
			$nomResponsables = substr($nomResponsables, 0, strlen($nomResponsables) - 2);
			if(count($responsables) > 1)
			{
				$texteResponsable = __('Responsables', 'evarisk');
			}
			else
			{
				$texteResponsable = __('Responsable', 'evarisk');
			}
			if($groupementPere->nom != "Groupement Racine")
				$miniFilAriane = $miniFilAriane . $groupementPere->nom;
			$renduPage = '<div id="enTeteDroite">
				<div id="Informations">
					<div id="nomElement" class="titleDiv">';
			$idTitreGp = 'titreGp' . $idElement;
			$groupsNames = EvaGroupement::getGroupementsName();
			$groupsNames[] = "";
			$valeurActuelleIn = 'false';
			$valeurActuelleIn = 'evarisk("#' . $idTitreGp . '").val() in {';
			foreach($groupsNames as $groupName)
			{
				$valeurActuelleIn .= "'" . addslashes($groupName) . "':'', ";
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
									"table": "' . TABLE_GROUPEMENT . '",
									"act": "updateByField",
									"id": ' . $idElement . ',
									"whatToUpdate": "nom",
									"whatToSet": evarisk("#' . $idTitreGp . '").val()
								});
							});
						})
					</script>';
			$renduPage .= EvaDisplayInput::afficherInput('button', 'validChangeTitre', __('Enregistrer'), null, null, 'validChangeTitre', false, false, 1,'','','',$script,'right',true);
			$script = '<script type="text/javascript">
						evarisk(document).ready(function(){
							evarisk("#' . $idTitreGp . '").focus(function(){
								evarisk(this).select();
								evarisk("#' . $idTitreGp . '").addClass("titleInfoSelected");
							});
							evarisk("#' . $idTitreGp . '").blur(function(){
								if(!evarisk("#' . $idButton . '").is(":visible")){
									evarisk("#' . $idTitreGp . '").removeClass("titleInfoSelected");
								}
							});
							evarisk("#' . $idTitreGp . '").keyup(function(){
								evarisk("#nom_groupement").val(evarisk("#' . $idTitreGp . '").val());
								if(evarisk("#nom_groupement").val() != ""){
									evarisk("#nom_groupement").removeClass("form-input-tip");
								}
								else{
									evarisk("#nom_groupement").addClass("form-input-tip");							
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
			$renduPage .= EvaDisplayInput::afficherInput('text', $idTitreGp, $nomGroupement, null, null, $idTitreGp, false, false, 255,'titleInfo', 'alignright','85%', $script, 'left') . '
					</div>
					<div class="mainInfosDiv">
						<div class="mainInfos1 alignleft" style="width: 68%">
							<p>
								<span id="miniFilAriane">' . __('Hi&eacute;rarchie', 'evarisk') . ' : ' . $miniFilAriane . '</span><br />
								' . $texteResponsable . ' : <strong>' . $nomResponsables . '</strong><br />
							</p>
						</div>
						<div class="alignleft" style="width: 30%">
							<p>
								<span class="bold" >' . __('Somme des risques', 'evarisk') . '</span>&nbsp;:&nbsp;<span id="riskSum' . $tableElement . $idElement . '" >' . $scoreRisqueGroupement . '</span><br/>
								<span class="bold" >' . __('Nombre de risques', 'evarisk') . '</span>&nbsp;:&nbsp;<span id="riskNb' . $tableElement . $idElement . '" >' . $nombreRisqueGroupement . '</span>
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