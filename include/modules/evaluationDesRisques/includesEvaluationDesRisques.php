<?php
/**
 * Include the meta-boxes files for groups and working units
 *
 * @author Evarisk
 * @version v5.0
 */
	
	function includesEvaluationDesRisques($idElement, $chargement = 'tout')
	{		
		require_once(EVA_LIB_PLUGIN_DIR . 'scriptPartieDroite.php');
		require_once(EVA_METABOXES_PLUGIN_DIR . 'evaluationDesRisques/evaluationRisquesPostBox.php');
		if($chargement == 'tout')
		{
			if(((int)$idElement) != 0)
			{
				require_once(EVA_METABOXES_PLUGIN_DIR . 'evaluationDesRisques/groupement/headerPartieDroiteGroupement.php');
				require_once(EVA_METABOXES_PLUGIN_DIR . 'evaluationDesRisques/uniteDeTravail/headerPartieDroiteUniteTravail.php');
				require_once(EVA_METABOXES_PLUGIN_DIR . 'risque/risque.php');
				//require_once(EVA_METABOXES_PLUGIN_DIR . 'actionsCorrectives/actionCorrective.php' );
				// require_once(EVA_METABOXES_PLUGIN_DIR . 'accidentDeTravail/accidentDeTravail.php' );
				// require_once(EVA_METABOXES_PLUGIN_DIR . 'veilleReglementaire/veilleReglementairePostBox.php');
				require_once(EVA_METABOXES_PLUGIN_DIR . 'utilisateurs/liaisonUtilisateursElement.php');
				require_once(EVA_METABOXES_PLUGIN_DIR . 'utilisateurs/groupesEvaluateurs.php' );
				require_once(EVA_METABOXES_PLUGIN_DIR . 'utilisateurs/utilisateurs.php' );
				require_once(EVA_METABOXES_PLUGIN_DIR . 'documentUnique/documentUnique.php' );
				require_once(EVA_METABOXES_PLUGIN_DIR . 'epi/epi.php' );
				if(file_exists(EVA_TEMPLATES_PLUGIN_DIR . 'includesEvaluationDesRisquesPerso.php'))
					include_once(EVA_TEMPLATES_PLUGIN_DIR . 'includesEvaluationDesRisquesPerso.php');
			}
		}
	}