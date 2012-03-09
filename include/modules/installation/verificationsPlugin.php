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
	
	/*	V�rifie que le dossier upload soit bien cr��	*/
	eva_tools::copyEntireDirectory(EVA_UPLOADS_PLUGIN_OLD_DIR, EVA_UPLOADS_PLUGIN_DIR);

	/*	V�rifie que le dossier result soit bien cr��	*/
	eva_tools::copyEntireDirectory(EVA_RESULTATS_PLUGIN_OLD_DIR, EVA_RESULTATS_PLUGIN_DIR);

	/*	V�rifie que le dossier temporaire pour la cr�ation des fichiers odt soit bien cr��	*/
	if(!is_dir(EVA_RESULTATS_PLUGIN_DIR . 'tmp')){
		eva_tools::make_recursiv_dir(EVA_RESULTATS_PLUGIN_DIR . 'tmp');
	}

?>