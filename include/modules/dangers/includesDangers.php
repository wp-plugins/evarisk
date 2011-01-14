<?php
/**
 * Include the meta-boxes files for dangers and dangers categories
 *
 * @author Evarisk
 * @version v5.0
 */
	
	function includesDangers($idElement, $chargement = 'tout')
	{		
		require_once(EVA_LIB_PLUGIN_DIR . 'scriptPartieDroite.php');
		require_once(EVA_METABOXES_PLUGIN_DIR . 'dangers/dangersPostBox.php');
		if($chargement == 'tout')
		{
			require_once(EVA_METABOXES_PLUGIN_DIR . 'dangers/categorieDangers/categorieDangers-new.php');
			require_once(EVA_METABOXES_PLUGIN_DIR . 'dangers/danger/danger-new.php');
			if(((int)$idElement) != 0)
			{
				require_once(EVA_METABOXES_PLUGIN_DIR . 'dangers/categorieDangers/categorieDangersMainPhoto.php');
				if(file_exists(EVA_TEMPLATES_PLUGIN_DIR . 'includesDangersPerso.php'))
					include_once(EVA_TEMPLATES_PLUGIN_DIR . 'includesDangersPerso.php');
			}
		}
	}