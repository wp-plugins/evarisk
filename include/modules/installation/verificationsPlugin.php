<?php
/**
*	Plugin verification file
*
*	Check if there are structural errors into the plugin.
* @author Evarisk <dev@evarisk.com>
* @version 1.0
* @package evarisk
* @subpackage modules
*/
	
	/*	Vérifie que le dossier upload soit bien créé	*/
	eva_tools::copyEntireDirectory(EVA_UPLOADS_PLUGIN_OLD_DIR, EVA_UPLOADS_PLUGIN_DIR);

	/*	Vérifie que le dossier result soit bien créé	*/
	eva_tools::copyEntireDirectory(EVA_RESULTATS_PLUGIN_OLD_DIR, EVA_RESULTATS_PLUGIN_DIR);

	/*	Vérifie que le dossier temporaire pour la création des fichiers odt soit bien créé	*/
	if(!is_dir(EVA_RESULTATS_PLUGIN_DIR . 'tmp')){
		eva_tools::make_recursiv_dir(EVA_RESULTATS_PLUGIN_DIR . 'tmp');
	}

?>