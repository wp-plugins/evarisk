<?php
/**
 * Include the meta-boxes files for corrective actions
 *
 * @author Evarisk
 * @version v5.0
 */
	
	function includesActionsCorrectives($idElement, $chargement = 'tout')
	{		
		require_once(EVA_LIB_PLUGIN_DIR . 'scriptPartieDroite.php');
		require_once(EVA_METABOXES_PLUGIN_DIR . 'actionsCorrectives/actionsCorrectivesPostBox.php');
		if($chargement == 'tout')
		{
			require_once(EVA_METABOXES_PLUGIN_DIR . 'actionsCorrectives/tache/tache-new.php');
			require_once(EVA_METABOXES_PLUGIN_DIR . 'actionsCorrectives/activite/activite-new.php');
			if(((int)$idElement) != 0)
			{
				require_once(EVA_METABOXES_PLUGIN_DIR . 'hierarchie/hierarchiePostBox.php');
				require_once(EVA_METABOXES_PLUGIN_DIR . 'galeriePhotos/galeriePhotos.php');
				require_once(EVA_METABOXES_PLUGIN_DIR . 'utilisateurs/liaisonUtilisateursElement.php');
				require_once(EVA_METABOXES_PLUGIN_DIR . 'actionsCorrectives/suiviModification.php');
				if(file_exists(EVA_TEMPLATES_PLUGIN_DIR . 'includesActionsCorrectivesPerso.php'))
					include_once(EVA_TEMPLATES_PLUGIN_DIR . 'includesActionsCorrectivesPerso.php');
			}
		}
	}