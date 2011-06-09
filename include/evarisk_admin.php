<?php
	require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php' );
	require_once(EVA_LIB_PLUGIN_DIR . 'options.class.php' );
	require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayDesign.class.php' );
	require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayInput.class.php' );
	require_once(EVA_LIB_PLUGIN_DIR . 'database.class.php');
	require_once(EVA_LIB_PLUGIN_DIR . 'dashboard/dashboard.class.php');
	require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/evaluationDesRisques.class.php');
	require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/actionsCorrectives.class.php');
	require_once(EVA_LIB_PLUGIN_DIR . 'methode/methodeEvaluation.class.php' );
	require_once(EVA_LIB_PLUGIN_DIR . 'danger/danger.class.php' );
	require_once(EVA_LIB_PLUGIN_DIR . 'users/evaUser.class.php' );
	require_once(EVA_LIB_PLUGIN_DIR . 'veilleReglementaire/veilleReglementaire.class.php' );
	require_once(EVA_LIB_PLUGIN_DIR . 'evaRecommandation/evaRecommandation.class.php' );
	require_once(EVA_LIB_PLUGIN_DIR . 'produits/produits.class.php' );

/**
 * Function adding the menu in wordpress side barr
 */
function evarisk_add_menu() {

	require_once(EVA_MODULES_PLUGIN_DIR . 'installation/verificationsPlugin.php');

	require_once(EVA_MODULES_PLUGIN_DIR . 'installation/initialisationPermissions.php');
	evarisk_init_permission();
	if(digirisk_options::getDbOption('base_evarisk') < 1)
	{
		// On crée le menu principal
		add_menu_page( 'Digirisk : ' . __('Installation', 'evarisk'), __( 'Digirisk', 'evarisk' ), 'Evarisk_:_utiliser_le_plugin', EVA_MODULES_PLUGIN_DIR . '/installation/formulaireInstallation.php' , '', EVA_FAVICON, 3);
		// On propose le formulaire de création
		add_submenu_page(EVA_MODULES_PLUGIN_DIR . '/installation/formulaireInstallation.php','Evarisk : ' . __('Installation', 'evarisk'), __('Installation', 'evarisk'),  'Evarisk_:_utiliser_le_plugin', EVA_MODULES_PLUGIN_DIR . '/installation/formulaireInstallation.php');
	}
	else
	{
		require_once(EVA_MODULES_PLUGIN_DIR . 'installation/creationTables.php');
		evarisk_creationTables();
		// On crée le menu principal
		add_menu_page('Digirisk : ' . __('Accueil', 'evarisk'), __( 'Digirisk', 'evarisk' ), 'Evarisk_:_utiliser_le_plugin', 'digirisk_dashboard', array('dashboard', 'dashboardMainPage'), EVA_FAVICON, 3);

		// On renomme l'accueil
		add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('Tableau de bord', 'evarisk' ), __( 'Tableau de bord', 'evarisk' ), 'Evarisk_:_utiliser_le_plugin', 'digirisk_dashboard', array('dashboard','dashboardMainPage'));

		//	On créé le menu des préconisation
		add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('Pr&eacute;conisations', 'evarisk' ), __( 'Pr&eacute;conisations', 'evarisk' ), 'Evarisk_:_voir_les_preconisations', 'digirisk_recommandation', array('evaRecommandation', 'evaRecommandationMainPage'));

		// On crée le menu des méthodes
		add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('M&eacute;thodes d\'&eacute;valuation', 'evarisk' ), __( 'M&eacute;thodes d\'&eacute;valuation', 'evarisk' ), 'Evarisk_:_voir_les_methodes', 'digirisk_evaluation_method', array('methodeEvaluation','methodeEvaluationMainPage'));

		// On crée le menu des Dangers
		add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('Dangers', 'evarisk' ), __( 'Dangers', 'evarisk' ), 'Evarisk_:_voir_les_dangers', 'digirisk_danger', array('danger','dangerMainPage'));

		// On crée le menu de l'évaluation des risques
		add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('&Eacute;valuation des risques', 'evarisk' ), __( '&Eacute;valuation des risques', 'evarisk' ), 'Evarisk_:_voir_les_groupements', 'digirisk_risk_evaluation', array('evaluationDesRisques','evaluationDesRisquesMainPage'));

		// On crée le menu des actions correctives
		// if(digirisk_options::getOptionValue('action_correctives_avancees') == 'oui')
		// {
		add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('Actions correctives', 'evarisk' ), __( 'Actions correctives', 'evarisk' ), 'Evarisk_:_voir_les_groupements', 'digirisk_correctiv_actions', array('actionsCorrectives','actionsCorrectivesMainPage'));
		// }

		/*	Add the options menu in the options section	*/
		// On créé le menu des options
		// add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('Options', 'evarisk' ), __( 'Options', 'evarisk' ), 'Evarisk_:_editer_les_options', 'digirisk_options', array('digirisk_options','optionMainPage'));
		add_options_page(__('Options du logiciel digirisk', 'evarisk'), __('Digirisk', 'evarisk'), 'Evarisk_:_editer_les_options', 'digirisk_options', array('digirisk_options', 'optionMainPage'));

		// On crée le menu d'import d'utilisateurs
		add_users_page('Digirisk : ' . __('Import d\'utilisateurs pour l\'&eacute;valuation des risques', 'evarisk' ), __('Import Digirisk', 'evarisk'), 'Evarisk_:_gerer_utilisateurs', 'digirisk_import_users', array('evaUser','importUserPage'));

		// On crée le menu de gestion des groupes d'utilisateurs
		add_users_page('Digirisk : ' . __('Gestion des groupes d\'utilisateurs', 'evarisk' ), __('Groupes Digirisk', 'evarisk'), 'Evarisk_:_gerer_utilisateurs', DIGI_URL_SLUG_USER_GROUP, array('evaUser','importUserPage'));

		// On crée le menu de création de veille réglementaire
		// add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('Cr&eacute;ation de r&eacute;f&eacute;renciel', 'evarisk' ), __( 'Cr&eacute;ation de r&eacute;f&eacute;renciel', 'evarisk' ), 'Evarisk_:_creer_referenciel', 'digirisk_referentials', array('veilleReglementaire','veilleReglementaireMainPage'));
	}
}

function eva_admin_js()
{
	if(!wp_script_is('jquery-ui-tabs', 'queue'))
	{
		wp_enqueue_script('jquery-ui-tabs');
	}
	if(!wp_script_is('jquery', 'queue'))
	{
		wp_enqueue_script('jquery');
	}
	wp_enqueue_script('eva_main_js', EVA_INC_PLUGIN_URL . 'js/lib.js', '', EVA_PLUGIN_VERSION);
	wp_enqueue_script('eva_google_jsapi', 'http://www.google.com/jsapi', '', EVA_PLUGIN_VERSION);
	wp_enqueue_script('eva_jq_datatable', EVA_INC_PLUGIN_URL . 'js/dataTable/jquery.dataTables.js', '', EVA_PLUGIN_VERSION);
	wp_enqueue_script('eva_jq_min', EVA_INC_PLUGIN_URL . 'js/jquery-ui-min.js', '', EVA_PLUGIN_VERSION);
	// wp_enqueue_script('eva_jq_min_i18n', EVA_INC_PLUGIN_URL . 'js/jQueryUI/development-bundle/ui/i18n/jquery-ui-i18n.js', '', EVA_PLUGIN_VERSION);
	// wp_enqueue_script('eva_jq_gantt', EVA_INC_PLUGIN_URL . 'js/jQueryUI/development-bundle/ui/ui.gantt.min.js', '', EVA_PLUGIN_VERSION);
	// wp_enqueue_script('eva_jq_editable', EVA_INC_PLUGIN_URL . 'js/jquery.editable.js', '', EVA_PLUGIN_VERSION);
	wp_enqueue_script('eva_jq_treetable', EVA_INC_PLUGIN_URL . 'js/treeTable/jquery.treeTable.js', '', EVA_PLUGIN_VERSION);
	wp_enqueue_script('eva_jq_galleriffic', EVA_INC_PLUGIN_URL . 'js/galleriffic/jquery.galleriffic.js', '', EVA_PLUGIN_VERSION);
	wp_enqueue_script('eva_jq_galover', EVA_INC_PLUGIN_URL . 'js/galleriffic/jquery.opacityrollover.js', '', EVA_PLUGIN_VERSION);
	wp_enqueue_script('eva_jq_fieldselect', EVA_INC_PLUGIN_URL . 'js/fieldSelection/jquery-fieldselection.js', '', EVA_PLUGIN_VERSION);
	wp_enqueue_script('eva_jq_fileupload', EVA_INC_PLUGIN_URL . 'js/fileUploader/fileuploader.js', '', EVA_PLUGIN_VERSION);
	wp_enqueue_script('eva_user_js', EVA_INC_PLUGIN_URL . 'js/users.js', '', EVA_PLUGIN_VERSION);
	wp_enqueue_script('eva_role_js', EVA_INC_PLUGIN_URL . 'js/role.js', '', EVA_PLUGIN_VERSION);
	wp_enqueue_script('eva_eav_js', EVA_INC_PLUGIN_URL . 'js/eav.js', '', EVA_PLUGIN_VERSION);
	wp_enqueue_script('eva_jq_autocomplete', EVA_INC_PLUGIN_URL . 'js/jquery.autocomplete.js', '', EVA_PLUGIN_VERSION);
	wp_enqueue_script('eva_wp_postobox_js', WP_CONTENT_URL . '/../wp-admin/js/postbox.js', '', EVA_PLUGIN_VERSION);
}

function eva_admin_css()
{
	wp_register_style('eva_jquery_datatable', EVA_INC_PLUGIN_URL . 'css/dataTable/demo_table_jui.css', '', EVA_PLUGIN_VERSION);
	wp_enqueue_style('eva_jquery_datatable');
	wp_register_style('eva_jquery_custom', EVA_INC_PLUGIN_URL . 'css/jQueryUI/smoothness/jquery-ui-1.7.2.custom.css', '', EVA_PLUGIN_VERSION);
	wp_enqueue_style('eva_jquery_custom');
	wp_register_style('eva_jquery_treetable', EVA_INC_PLUGIN_URL . 'css/treeTable/treeTable.css', '', EVA_PLUGIN_VERSION);
	wp_enqueue_style('eva_jquery_treetable');
	wp_register_style('eva_jquery_fileuploader', EVA_INC_PLUGIN_URL . 'css/fileUploader/fileuploader.css', '', EVA_PLUGIN_VERSION);
	wp_enqueue_style('eva_jquery_fileuploader');
	wp_register_style('eva_main_css', EVA_INC_PLUGIN_URL . 'css/eva.css', '', EVA_PLUGIN_VERSION);
	wp_enqueue_style('eva_main_css');
	wp_register_style('eva_role_css', EVA_INC_PLUGIN_URL . 'css/role.css', '', EVA_PLUGIN_VERSION);
	wp_enqueue_style('eva_role_css');
	wp_register_style('eva_duer_css', EVA_INC_PLUGIN_URL . 'css/documentUnique.css', '', EVA_PLUGIN_VERSION);
	wp_enqueue_style('eva_duer_css');
	wp_register_style('eva_autocomplete_css', EVA_INC_PLUGIN_URL . 'css/jquery.autocomplete.css', '', EVA_PLUGIN_VERSION);
	wp_enqueue_style('eva_autocomplete_css');
	wp_register_style('eva_lightbox_css', EVA_INC_PLUGIN_URL . 'css/lightbox.css', '', EVA_PLUGIN_VERSION);
	wp_enqueue_style('eva_lightbox_css');
}

function eva_add_admin_js()
{
	echo '<script type="text/javascript" >
		var EVA_AJAX_FILE_URL = "' . EVA_INC_PLUGIN_URL . 'ajax.php";
	</script>';
}

?>