<?php
	DEFINE('EVA_PLUGIN_VERSION', '5.1.2.9');

	require_once('configDroitAcces.php');
	require_once('configEavModel.php');
	require_once('databaseTable.php');
	require_once('configPagesHooks.php');

	DEFINE('EVA_HOME_URL', WP_PLUGIN_URL . '/' . EVA_PLUGIN_DIR . '/');
	DEFINE('EVA_HOME_DIR', WP_PLUGIN_DIR . '/' . EVA_PLUGIN_DIR . '/');

	DEFINE('EVA_GENERATED_DOC_DIR', WP_CONTENT_DIR . '/uploads/' . EVA_PLUGIN_DIR . '/');
	DEFINE('EVA_GENERATED_DOC_URL', WP_CONTENT_URL . '/uploads/' . EVA_PLUGIN_DIR . '/');

	DEFINE('EVA_INC_PLUGIN_DIR', EVA_HOME_DIR . 'include/');
	DEFINE('EVA_INC_PLUGIN_URL', EVA_HOME_URL . 'include/');
	
	DEFINE('EVA_LIB_PLUGIN_DIR', EVA_INC_PLUGIN_DIR . 'lib/');
	DEFINE('EVA_MODULES_PLUGIN_DIR', EVA_INC_PLUGIN_DIR . 'modules/');
	DEFINE('EVA_METABOXES_PLUGIN_DIR', EVA_MODULES_PLUGIN_DIR . 'metaBoxes/');
	DEFINE('EVA_TEMPLATES_PLUGIN_DIR', EVA_HOME_DIR . 'templates/');
	
	DEFINE('EVA_IMG_PLUGIN_URL', EVA_HOME_URL . 'medias/images/');
	DEFINE('EVA_IMG_ICONES_PLUGIN_URL', EVA_IMG_PLUGIN_URL . 'Icones/');
	DEFINE('EVA_IMG_DIVERS_PLUGIN_URL', EVA_IMG_ICONES_PLUGIN_URL . 'Divers/');
	DEFINE('EVA_IMG_PICTOS_PLUGIN_URL', EVA_IMG_PLUGIN_URL . 'Pictos/');
	DEFINE('EVA_IMG_GOOGLEMAPS_PLUGIN_URL', EVA_IMG_PLUGIN_URL . 'GoogleMapIcons/');
	DEFINE('EVA_LIB_PLUGIN_URL', EVA_INC_PLUGIN_URL . 'lib/');

	DEFINE('EVA_UPLOADS_PLUGIN_DIR', EVA_GENERATED_DOC_DIR . 'uploads/');
	DEFINE('EVA_UPLOADS_PLUGIN_URL', EVA_GENERATED_DOC_URL . 'uploads/');
	DEFINE('EVA_PHOTO_UPLOADS_PLUGIN_URL', EVA_UPLOADS_PLUGIN_URL . 'photos/');
	DEFINE('EVA_TEXTE_VEILLE_UPLOADS_PLUGIN_URL', EVA_UPLOADS_PLUGIN_URL . 'veilleReglementaire/');

	DEFINE('EVA_RESULTATS_PLUGIN_URL', EVA_GENERATED_DOC_URL . 'results/');
	DEFINE('EVA_RESULTATS_PLUGIN_DIR', EVA_GENERATED_DOC_DIR . 'results/');
	DEFINE('EVA_MODELES_PLUGIN_DIR', EVA_UPLOADS_PLUGIN_DIR . 'modeles/');
	DEFINE('EVA_MODELES_PLUGIN_URL', EVA_UPLOADS_PLUGIN_URL . 'modeles/');
	DEFINE('EVA_NOTES_PLUGIN_DIR', EVA_RESULTATS_PLUGIN_DIR . 'notes/');
	DEFINE('EVA_NOTES_PLUGIN_URL', EVA_RESULTATS_PLUGIN_URL . 'notes/');

	/*	Do not delete even if old sufix has been added!!! Used to check if directory are well created on each plugin loading	*/
	DEFINE('EVA_UPLOADS_PLUGIN_OLD_DIR', EVA_HOME_DIR . 'medias/uploads/');
	DEFINE('EVA_RESULTATS_PLUGIN_OLD_DIR', EVA_HOME_DIR . 'medias/results/');

	/**
	* Risk name define variable
	*/
	{
	// DEFINE('EVA_RISQUE_SEUIL_1_NOM', __('Nul', 'evarisk'));
	// DEFINE('EVA_RISQUE_SEUIL_2_NOM', __('Tr&egrave;s limit&eacute;', 'evarisk'));
	// DEFINE('EVA_RISQUE_SEUIL_3_NOM', __('Limit&eacute;', 'evarisk'));
	// DEFINE('EVA_RISQUE_SEUIL_4_NOM', __('Significatif', 'evarisk'));
	// DEFINE('EVA_RISQUE_SEUIL_5_NOM', __('&Eacute;lev&eacute;', 'evarisk'));
	// DEFINE('EVA_RISQUE_SEUIL_6_NOM', __('Tr&egrave;s &eacute;lev&eacute;', 'evarisk'));
	DEFINE('EVA_RISQUE_SEUIL_1_NOM', __('Risque Faible', 'evarisk'));
	DEFINE('EVA_RISQUE_SEUIL_2_NOM', __('Risque &agrave; planifier', 'evarisk'));
	DEFINE('EVA_RISQUE_SEUIL_3_NOM', __('Risque &agrave; traiter', 'evarisk'));
	DEFINE('EVA_RISQUE_SEUIL_4_NOM', __('Risque Inacceptable', 'evarisk'));

	DEFINE('SEUIL_BAS_INACCEPTABLE', '80');
	DEFINE('SEUIL_HAUT_INACCEPTABLE', '100');
	DEFINE('SEUIL_BAS_ATRAITER', '51');
	DEFINE('SEUIL_HAUT_ATRAITER', '79');
	DEFINE('SEUIL_BAS_APLANIFIER', '48');
	DEFINE('SEUIL_HAUT_APLANIFIER', '50');
	DEFINE('SEUIL_BAS_FAIBLE', '0');
	DEFINE('SEUIL_HAUT_FAIBLE', '47');
		
		
	DEFINE('COULEUR_RISQUE_INACCEPTABLE', '#000000');
		DEFINE('COULEUR_TEXTE_RISQUE_INACCEPTABLE', '#FFFFFF');
	DEFINE('COULEUR_RISQUE_ATRAITER', '#FF0100');
		DEFINE('COULEUR_TEXTE_RISQUE_ATRAITER', '#000000');
	DEFINE('COULEUR_RISQUE_APLANIFIER', '#FFCD00');
		DEFINE('COULEUR_TEXTE_RISQUE_APLANIFIER', '#000000');
	DEFINE('COULEUR_RISQUE_FAIBLE', '#FFFFFF');
		DEFINE('COULEUR_TEXTE_RISQUE_FAIBLE', '#000000');

		$typeRisque = array();
		$typeRisque['risq80'] = SEUIL_BAS_INACCEPTABLE;
		$typeRisque['risq51'] = SEUIL_BAS_ATRAITER;
		$typeRisque['risq48'] = SEUIL_BAS_APLANIFIER;
		$typeRisque['risq'] = SEUIL_BAS_FAIBLE;

		$typeRisquePlanAction = array();
		$typeRisquePlanAction['planDactionRisq80'] = SEUIL_BAS_INACCEPTABLE;
		$typeRisquePlanAction['planDactionRisq51'] = SEUIL_BAS_ATRAITER;
		$typeRisquePlanAction['planDactionRisq48'] = SEUIL_BAS_APLANIFIER;
		$typeRisquePlanAction['planDactionRisq'] = SEUIL_BAS_FAIBLE;
	}

	/**
	*	Define the url slug
	*/
	{
		DEFINE('DIGI_URL_SLUG_USER_GROUP', 'digirisk_users_group');
		DEFINE('DIGI_URL_SLUG_MAIN_OPTION', 'digirisk_options');
	}

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

	$linkToDownloadOpenOffice = 'http://download.services.openoffice.org/files/localized/fr/3.2.1/OOo_3.2.1_Win_x86_install-wJRE_fr.exe';
	$locale = get_locale();
	if( !empty( $locale ) && ($locale != 'fr_FR'))
	{
		$linkToDownloadOpenOffice = 'http://download.services.openoffice.org/files/localized/' . $locale . '/3.2.1/OOo_3.2.1_Win_x86_install-wJRE_' . $locale . '.exe';
	}
	DEFINE('LINK_TO_DOWNLOAD_OPEN_OFFICE', $linkToDownloadOpenOffice);

	/**
	*	Define the option possible value
	*/
	$optionYesNoList = array();
	$optionYesNoList['oui'] = __('Oui', 'evarisk');
	$optionYesNoList['non'] = __('Non', 'evarisk');

	require_once(EVA_LIB_PLUGIN_DIR . 'options.class.php');
	require_once('configLogiciel.php');
	require_once('configImages.php');

	/**
	*	Vars to delete when sure that the corresponding version is passed
	*/
	{
		//version 23
		DEFINE('EVA_MODELES_PLUGIN_OLD_DIR', EVA_HOME_DIR . 'medias/modeles/');

		//version 35
		DEFINE('EVA_RESULTATS_PLUGIN_OLD_URL', EVA_HOME_URL . 'medias/results/');
		DEFINE('EVA_UPLOADS_PLUGIN_OLD_URL', EVA_HOME_URL . 'medias/uploads/');
		DEFINE('EVA_MODELES_PLUGIN_OLD_DIR', EVA_UPLOADS_PLUGIN_OLD_DIR . 'modeles/');
		DEFINE('EVA_MODELES_PLUGIN_OLD_URL', EVA_UPLOADS_PLUGIN_OLD_URL . 'modeles/');
	}

?>