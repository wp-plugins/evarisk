<?php

	$_POST['tableElement']=$_POST['table'];
	$_POST['idElement']= isset($_POST['id'])?$_POST['id']:null;
	$menu = $_POST['menu'];
	$tableElement = $_POST['tableElement'];
	$idElement = $_POST['idElement'];
	if(isset($_POST['partie']))
		$partie = $_POST['partie'];
	else
		$partie = 'right';

	if(isset($_POST['partition']))
		$partition = $_POST['partition'];
	else
		$partition = 'tout';

	require_once(EVA_CONFIG);

	switch($menu)
	{
		case 'gestiongrptut':
			switch($tableElement)
			{
				case TABLE_GROUPEMENT:
					require_once(EVA_MODULES_PLUGIN_DIR . 'gestionGroupementUniteTravail/includesGestionGroupementUniteTravail.php');
					require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/groupement/eva_groupement.class.php'); 
					$pageHook = PAGE_HOOK_EVARISK_GROUPEMENTS;
					$markers = array(EvaGroupement::getMarkersGeoLoc($idElement));
					includesGestionGroupementUniteTravail($idElement, $partition);
					break;
				case TABLE_UNITE_TRAVAIL:
					require_once(EVA_MODULES_PLUGIN_DIR . 'gestionGroupementUniteTravail/includesGestionGroupementUniteTravail.php');
					require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/uniteDeTravail/uniteDeTravail.class.php'); 
					$pageHook = PAGE_HOOK_EVARISK_UNITES_DE_TRAVAIL;
					$markers = array(UniteDeTravail::getMarkersGeoLoc($idElement));
					includesGestionGroupementUniteTravail($idElement, $partition);
					break;
			}
		break;

		default:
			switch($tableElement)
			{
				case TABLE_GROUPEMENT:
					require_once(EVA_MODULES_PLUGIN_DIR . 'evaluationDesRisques/includesEvaluationDesRisques.php');
					require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/groupement/eva_groupement.class.php'); 
					$pageHook = PAGE_HOOK_EVARISK_GROUPEMENTS;
					$markers = array(EvaGroupement::getMarkersGeoLoc($idElement));
					includesEvaluationDesRisques($idElement, $partition);
					break;
				case TABLE_UNITE_TRAVAIL:
					require_once(EVA_MODULES_PLUGIN_DIR . 'evaluationDesRisques/includesEvaluationDesRisques.php');
					require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/uniteDeTravail/uniteDeTravail.class.php'); 
					$pageHook = PAGE_HOOK_EVARISK_UNITES_DE_TRAVAIL;
					$markers = array(UniteDeTravail::getMarkersGeoLoc($idElement));
					includesEvaluationDesRisques($idElement, $partition);
					break;
				case TABLE_CATEGORIE_DANGER:
					require_once(EVA_MODULES_PLUGIN_DIR . 'dangers/includesDangers.php');
					$pageHook = PAGE_HOOK_EVARISK_CATEGORIES_DANGERS;
					includesDangers($idElement, $partition);
					$markers = '';
					break;
				case TABLE_DANGER:
					require_once(EVA_MODULES_PLUGIN_DIR . 'dangers/includesDangers.php');
					$pageHook = PAGE_HOOK_EVARISK_DANGERS;
					includesDangers($idElement, $partition);
					$markers = '';
					break;
				case TABLE_TACHE:
					require_once(EVA_MODULES_PLUGIN_DIR . 'actionsCorrectives/includesActionsCorrectives.php');
					$pageHook = PAGE_HOOK_EVARISK_TACHE;
					includesActionsCorrectives($idElement, $partition);
					$markers = '';
					break;
				case TABLE_ACTIVITE:
					require_once(EVA_MODULES_PLUGIN_DIR . 'actionsCorrectives/includesActionsCorrectives.php');
					$pageHook = PAGE_HOOK_EVARISK_ACTIVITE;
					includesActionsCorrectives($idElement, $partition);
					$markers = '';
					break;
			}
		break;
	}

	{//Contenu
		$script = getScriptPartieDroite($pageHook);
		// //Contextual help definition
		// add_contextual_help($pageHook, '<p>Ceci est l\'aide des groupements.</p>');
		// //Creation of contextual menu
		// screen_meta($pageHook);

		echo $script . '<div class="metabox-holder clear">
			<script type="text/javascript">
				$(".inside").each(function(){
					// $(this).css("max-height","400px");
					// $(this).css("overflow","auto");
				});
			</script>';
		//Add the postBoxes
		$idsFilAriane = isset($_POST['idsFilAriane'])?$_POST['idsFilAriane']:null;
		$expanded = isset($_POST['expanded'])?$_POST['expanded']:null;
		$page = isset($_POST['page'])?$_POST['page']:null;
		$idPere = isset($_POST['idPere'])?$_POST['idPere']:null;
		$affichage = isset($_POST['affichage'])?$_POST['affichage']:null;
		
		do_meta_boxes( $pageHook, $partie . 'Side', array('tableElement'=>$tableElement, 'idElement'=>$idElement, 'idPere'=>$idPere, 'affichage'=>$affichage, 'idsFilAriane'=>$idsFilAriane, 'markers' =>$markers, 'page'=>$page, 'expanded'=>$expanded));
		//To preserve postBoxes order
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false);
		//To preserve postBoxes closure state
		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false);
		echo '</div>';
	}
?>