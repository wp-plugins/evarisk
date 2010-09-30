<?php
	require_once('configDroitAcces.php');
	require_once('configEavModel.php');
	require_once('configLogiciel.php');
	require_once('configNomTables.php');
	require_once('configPagesHooks.php');

	DEFINE('EVA_HOME_URL', WP_PLUGIN_URL . '/' . EVA_PLUGIN_DIR . '/');
	DEFINE('EVA_HOME_DIR', WP_PLUGIN_DIR . '/' . EVA_PLUGIN_DIR . '/');
	
	DEFINE('EVA_INC_PLUGIN_DIR', EVA_HOME_DIR . 'include/');
	DEFINE('EVA_INC_PLUGIN_URL', EVA_HOME_URL . 'include/');
	
	DEFINE('EVA_LIB_PLUGIN_DIR', EVA_INC_PLUGIN_DIR . 'lib/');
	DEFINE('EVA_MODULES_PLUGIN_DIR', EVA_INC_PLUGIN_DIR . 'modules/');
	DEFINE('EVA_METABOXES_PLUGIN_DIR', EVA_MODULES_PLUGIN_DIR . 'metaBoxes/');
	DEFINE('EVA_TEMPLATES_PLUGIN_DIR', EVA_HOME_DIR . 'templates/');
	DEFINE('EVA_UPLOADS_PLUGIN_DIR', EVA_HOME_DIR . 'medias/uploads/');
	
	DEFINE('EVA_IMG_PLUGIN_URL', EVA_HOME_URL . 'medias/images/');
	DEFINE('EVA_IMG_DIVERS_PLUGIN_URL', EVA_IMG_PLUGIN_URL . 'Divers/');
	DEFINE('EVA_IMG_ICONES_PLUGIN_URL', EVA_IMG_PLUGIN_URL . 'Icones/');
	DEFINE('EVA_IMG_PICTOS_PLUGIN_URL', EVA_IMG_PLUGIN_URL . 'Pictos/');
	DEFINE('EVA_IMG_GOOGLEMAPS_PLUGIN_URL', EVA_IMG_PLUGIN_URL . 'GoogleMapIcons/');
	DEFINE('EVA_IMG_EPI_PLUGIN_URL', EVA_IMG_PLUGIN_URL . 'epi/');
	DEFINE('EVA_LIB_PLUGIN_URL', EVA_INC_PLUGIN_URL . 'lib/');
	
	DEFINE('EVA_UPLOADS_PLUGIN_URL', EVA_HOME_URL . 'medias/uploads/');
	DEFINE('EVA_PHOTO_UPLOADS_PLUGIN_URL', EVA_UPLOADS_PLUGIN_URL . 'photos/');
	DEFINE('EVA_TEXTE_VEILLE_UPLOADS_PLUGIN_URL', EVA_UPLOADS_PLUGIN_URL . 'veilleReglementaire/');
	
	DEFINE('EVA_RESULTATS_PLUGIN_DIR', EVA_HOME_DIR . 'medias/results/');

	/**
	 * Risk name define variable
	 */
	DEFINE('EVA_RISQUE_SEUIL_1_NOM', __('Nul', 'evarisk'));
	DEFINE('EVA_RISQUE_SEUIL_2_NOM', __('Tr&egrave;s limit&eacute;', 'evarisk'));
	DEFINE('EVA_RISQUE_SEUIL_3_NOM', __('Limit&eacute;', 'evarisk'));
	DEFINE('EVA_RISQUE_SEUIL_4_NOM', __('Significatif', 'evarisk'));
	DEFINE('EVA_RISQUE_SEUIL_5_NOM', __('&Eacute;lev&eacute;', 'evarisk'));
	DEFINE('EVA_RISQUE_SEUIL_6_NOM', __('Tr&egrave;s &eacute;lev&eacute;', 'evarisk'));
	
	/**
	 * Others variables
	 */
	DEFINE('EVA_PARAM_FORMULE_MAX', 20);
	DEFINE('EVA_MAX_LONGUEUR_OBSERVATIONS', 30000);
	DEFINE('LARGEUR_GAUCHE', 49);
	DEFINE('NOMBRE_ELEMENTS_AFFICHAGE_GRILLE_EVAL_RISQUES', 3);
	DEFINE('NOMBRE_ELEMENTS_AFFICHAGE_GRILLE_DANGERS', 3);
	DEFINE('DAY_BEFORE_TODAY_GANTT', 14);
	DEFINE('DAY_AFTER_TODAY_GANTT', DAY_BEFORE_TODAY_GANTT);
	DEFINE('LARGEUR_INDENTATION_GANTT_EN_EM', 1.5);
	
	require_once('configImages.php');
?>