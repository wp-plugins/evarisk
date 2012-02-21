<?php
	require_once('databaseTable.php');
	require_once('configPagesHooks.php');

{/*	Define the different path for the plugin	*/
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
}

{/*	Define the risk level information	*/
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

{/*	Define url	*/
	DEFINE('DIGI_URL_SLUG_USER_GROUP', 'digirisk_users_group');
	DEFINE('DIGI_URL_SLUG_MAIN_OPTION', 'digirisk_options');
	DEFINE('DIGI_URL_SLUG_USER_RIGHT', 'digirisk_user_right');
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
	if( !empty( $locale ) && ($locale != 'fr_FR')){
		$linkToDownloadOpenOffice = 'http://download.services.openoffice.org/files/localized/' . $locale . '/3.2.1/OOo_3.2.1_Win_x86_install-wJRE_' . $locale . '.exe';
	}
	DEFINE('LINK_TO_DOWNLOAD_OPEN_OFFICE', $linkToDownloadOpenOffice);

	/**
	*	Define the option possible value
	*/
	$optionYesNoList = array();
	$optionYesNoList['oui'] = __('Oui', 'evarisk');
	$optionYesNoList['non'] = __('Non', 'evarisk');

	/**
	*	Define the option possible value
	*/
	$optionUserGender = array();
	$optionUserGender['F'] = __('Femme', 'evarisk');
	$optionUserGender['H'] = __('Homme', 'evarisk');

	/**
	*	Define the option possible value
	*/
	$optionUserNationality = array();
	$optionUserNationality['FR'] = __('Fran&ccedil;aise', 'evarisk');
	$optionUserNationality['CEE'] = __('C.E.E', 'evarisk');
	$optionUserNationality['OTHER'] = __('Autre', 'evarisk');

	/**
	*	Define the option possible value
	*/
	$optionAccidentDeclarationType = array();
	$optionAccidentDeclarationType['found'] = __('constat&eacute;', 'evarisk');
	$optionAccidentDeclarationType['known'] = __('connu', 'evarisk');
	$optionAccidentDeclarationType['registered'] = __('Inscrit au registre d\'infirmerie', 'evarisk');
	$optionAccidentDeclarationBy = array();
	$optionAccidentDeclarationBy['employer'] = __('par l\'employeur', 'evarisk');
	$optionAccidentDeclarationBy['attendants'] = __('par ses pr&eacute;pos&eacute;s', 'evarisk');
	$optionAccidentDeclarationBy['victim'] = __('D&eacute;crit par la victime', 'evarisk');
	/**
	*	Define the option possible value
	*/
	$optionAciddentConsequence = array();
	$optionAciddentConsequence['without_work_stop'] = __('Sans arr&ecirc;t de travail', 'evarisk');
	$optionAciddentConsequence['with_work_stop'] = __('Avec arr&ecirc;t de travail', 'evarisk');
	$optionAciddentConsequence['death'] = __('D&eacute;c&egrave;s', 'evarisk');
	/**
	*	Define information about the work accident document
	*/
	DEFINE('CERFA_ACCIDENT_TRAVAIL_IDENTIFIER', '50261#01');
	DEFINE('CERFA_ACCIDENT_TRAVAIL_LINK', 'http://www.ameli.fr/fileadmin/user_upload/formulaires/S6200.pdf');

	/**
	*	Define the option possible value
	*/
	$optionExistingTreeElementList = array();
	$optionExistingTreeElementList['recreate'] = __('Cr&eacute;er un nouveau', 'evarisk');
	$optionExistingTreeElementList['reactiv'] = __('R&eacute;-activer', 'evarisk');

	/**
	*	Define the list of hour and minute
	*/
	$digi_hour = $digi_minute = array();
	for($i=0;$i<24;$i++){
		$digi_hour[$i] = sprintf('%02d', $i);
	}
	for($i=0;$i<60;$i++){
		$digi_minute[$i] = sprintf('%02d', $i);
	}


	/**
	*	Define the different mandatory field for user to ve valid for work accident
	*/
	$userWorkAccidentMandatoryFields = array('user_imatriculation', 'user_imatriculation_key', 'user_birthday', 'user_gender', 'user_nationnality', 'user_adress', /* 'user_adress_2', */ 'user_hiring_date', 'user_profession', 'user_professional_qualification');

	/**
	*	Define the different existing element type
	*/
	$treeElementList = array(__('Cat&eacute;gories de pr&eacute;conisations', 'evarisk') => 'CP', __('Pr&eacute;conisations', 'evarisk') => 'P', __('M&eacute;thodes d\'&eacute;valuation', 'evarisk') => 'ME', __('Cat&eacute;gories de dangers', 'evarisk') => 'CD', __('Dangers', 'evarisk') => 'D', __('Groupements', 'evarisk') => 'GP', __('Unit&eacute;s de travail', 'evarisk') => 'UT', __('Actions correctives', 'evarisk') => 'T', __('Sous-actions correctives', 'evarisk') => 'ST', __('Risques', 'evarisk') => 'R', __('Utilisateurs', 'evarisk') => 'U', __('Groupes d\'utilisateurs', 'evarisk') => 'GPU', __('R&ocirc;les des utilisateurs', 'evarisk') => 'UR', __('Groupes de questions', 'evarisk') => 'GQ', __('Questions', 'evarisk') => 'Q', __('Produits', 'evarisk') => 'PDT', __('Cat&eacute;gorie de produits', 'evarisk') => 'CPDT', __('Documents unique', 'evarisk') => 'DU', __('Fiches de groupement', 'evarisk') => 'FGP', __('Groupes de fiches de groupement', 'evarisk') => 'GFGP', __('Fiches de poste', 'evarisk') => 'FP', __('Groupes de fiches de poste', 'evarisk') => 'GFP', __('Accident de travail', 'evarisk') => 'AT', __('Documents', 'evarisk') => 'DOC');
	$digirisk_tree_options = get_option('digirisk_tree_options');
	$identifierList = unserialize($digirisk_tree_options['digi_tree_element_identifier']);
	foreach($treeElementList as $elementName => $elementDefault){
		$optionValue = $elementDefault;
		if(isset($identifierList[$elementDefault]) && (trim($identifierList[$elementDefault]) != '')){
			$optionValue = $identifierList[$elementDefault];
		}
		DEFINE('ELEMENT_IDENTIFIER_' . $elementDefault, $optionValue);
	}

	require_once(EVA_LIB_PLUGIN_DIR . 'options.class.php');
	require_once('configLogiciel.php');
	require_once('configImages.php');

	/*	Define if we output a name or a picture into the column header for the right management	*/
	DEFINE('SHOW_PICTURE_FOR_RIGHT_HEADER_COLUMN', true);
	DEFINE('SHOW_PICTURE_FOR_RIGHT_HEADER_MASS_SELECTOR_COLUMN', true);

	/*	Define the path to wp-shop plugin	*/
	DEFINE('DIGI_WPSHOP_PLUGIN_MAINFILE', 'wpshop/wpshop.php');

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