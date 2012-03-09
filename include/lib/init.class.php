<?php
/**
* Plugin Loader
* 
* Define the different element usefull for the plugin usage. The menus, includes script, start launch script, css, translations
* @author Evarisk <dev@evarisk.com>
* @version 5.1.2.9
* @package Digirisk
* @subpackage librairies
*/

/**
* Define the different element usefull for the plugin usage. The menus, includes script, start launch script, css, translations
* @package Digirisk
* @subpackage librairies
*/
class digirisk_init
{
	/**
	*	Load the different element need to create the plugin environnement
	*/
	function digirisk_plugin_load(){
		/*	Call function to create the main left menu	*/
		add_action('admin_menu', array('digirisk_init', 'digirisk_menu') );

		/* Ajout des options	*/
		add_action('admin_init', array('digirisk_options', 'evarisk_add_options'));

		/*	Get the current language to translate the different text in plugin	*/
		$locale = get_locale();
		$moFile = EVA_HOME_DIR . 'languages/evarisk-' . $locale . '.mo';
		if( !empty($locale) && (is_file($moFile)) ){
			load_textdomain('evarisk', $moFile);
		}

		if((isset($_GET['page']) && (substr($_GET['page'], 0, 9) == 'digirisk_')) || (basename($_SERVER['PHP_SELF']) == 'user-edit.php')){
			// Ajout des script de eva_admin
			add_action('admin_head', array('digirisk_init', 'digirisk_add_admin_js'));
			/*	Include the different javascript	*/
			add_action('admin_init', array('digirisk_init', 'digirisk_admin_js') );
			/*	Include the different css	*/
			add_action('admin_init', array('digirisk_init', 'digirisk_admin_css') );
		}

		/*	Add the right management for users	*/
		if(current_user_can('digi_manage_user_right')){
			add_action('edit_user_profile', array('digirisk_permission', 'user_permission_management'));
		}
		add_action('admin_init', array('digirisk_permission', 'user_permission_set'));

		/* On initialise le formulaire seulement dans la page de création/édition */
		if (isset($_GET['page'],$_GET['action']) && $_GET['page']=='digirisk_doc' && $_GET['action']=='edit') {
			add_action('admin_init', array('digirisk_doc', 'init_wysiwyg'));
		}
		/* On récupère la liste des pages documentées afin de les comparer a la page courante */
		$pages_list = digirisk_doc::get_doc_pages_name_array();
		if (isset($_GET['page']) && in_array($_GET['page'], $pages_list)) {
			add_action('contextual_help', array('digirisk_doc', 'pippin_contextual_help'), 10, 3);
		}

		/*	Include the different css and js for frontend output	*/
		add_action('wp_print_styles', array('digirisk_init', 'frontend_css'));
		add_action('init', array('digirisk_init', 'frontend_js'));
	}

	/**
	*	Create the main left menu with different parts
	*/
	function digirisk_menu(){
		require_once(EVA_MODULES_PLUGIN_DIR . 'installation/verificationsPlugin.php');

		/*	Initialisation des permissions	*/
		digirisk_permission::digirisk_init_permission();

		if(digirisk_options::getDbOption('base_evarisk') < 1)
		{
			require_once(EVA_LIB_PLUGIN_DIR . 'install.class.php');
			// On crée le menu principal
			add_menu_page('Digirisk : ' . __('Installation', 'evarisk'), __( 'Digirisk', 'evarisk' ), 'activate_plugins', 'digirisk_installation', array('digirisk_install', 'installation_form'), EVA_FAVICON, 3);
			// On propose le formulaire de création
			add_submenu_page('digirisk_installation', 'Evarisk : ' . __('Installation', 'evarisk'), __('Installation', 'evarisk'),  'activate_plugins', 'digirisk_installation', array('digirisk_install', 'installation_form'));
			add_options_page(__('Options du logiciel digirisk', 'evarisk'), __('Digirisk', 'evarisk'), 'activate_plugins', DIGI_URL_SLUG_MAIN_OPTION, array('digirisk_options', 'optionMainPage'));
		}
		else
		{
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/creationTables.php');
			evarisk_creationTables();

			// On crée le menu principal
			add_menu_page('Digirisk : ' . __('Accueil', 'evarisk'), __( 'Digirisk', 'evarisk' ), 'digi_view_dashboard_menu', 'digirisk_dashboard', array('dashboard', 'dashboardMainPage'), EVA_FAVICON, 3);

			// On renomme l'accueil
			add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('Tableau de bord', 'evarisk' ), __( 'Tableau de bord', 'evarisk' ), 'digi_view_dashboard_menu', 'digirisk_dashboard', array('dashboard','dashboardMainPage'));

			//	On créé le menu des préconisation
			// add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('Pr&eacute;conisations', 'evarisk' ), __( 'Pr&eacute;conisations', 'evarisk' ), 'digi_view_recommandation_menu', 'digirisk_recommandation', array('evaRecommandation', 'evaRecommandationMainPage'));
			// On crée le menu des méthodes
			// add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('M&eacute;thodes d\'&eacute;valuation', 'evarisk' ), __( 'M&eacute;thodes d\'&eacute;valuation', 'evarisk' ), 'digi_view_method_menu', 'digirisk_evaluation_method', array('methodeEvaluation','methodeEvaluationMainPage'));
			// On crée le menu des Dangers
			// add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('Dangers', 'evarisk' ), __( 'Dangers', 'evarisk' ), 'digi_view_danger_menu', 'digirisk_danger', array('danger','dangerMainPage'));

			// On crée le menu de l'évaluation des risques
			add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('&Eacute;valuation des risques', 'evarisk' ), __( '&Eacute;valuation des risques', 'evarisk' ), 'digi_view_evaluation_menu', 'digirisk_risk_evaluation', array('evaluationDesRisques','evaluationDesRisquesMainPage'));

			// On crée le menu des actions correctives
			// if(digirisk_options::getOptionValue('action_correctives_avancees') == 'oui')
			// {
			add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('Actions correctives', 'evarisk' ), __( 'Actions correctives', 'evarisk' ), 'digi_view_correctiv_action_menu', 'digirisk_correctiv_actions', array('actionsCorrectives','actionsCorrectivesMainPage'));
			// }

			/*	Add the options menu in the options section	*/
			add_options_page(__('Options du logiciel digirisk', 'evarisk'), __('Digirisk - Options', 'evarisk'), 'digi_view_options_menu', DIGI_URL_SLUG_MAIN_OPTION, array('digirisk_options', 'optionMainPage'));
			//	On créé le menu des préconisation
			add_options_page('Digirisk : ' . __('Pr&eacute;conisations', 'evarisk' ), __('Digirisk - Pr&eacute;conisations', 'evarisk'), 'digi_view_recommandation_menu', 'digirisk_recommandation', array('evaRecommandation', 'evaRecommandationMainPage'));
			// On crée le menu des méthodes
			add_options_page('Digirisk : ' . __('M&eacute;thodes d\'&eacute;valuation', 'evarisk' ), __('Digirisk - M&eacute;thodes d\'&eacute;valuation', 'evarisk'), 'digi_view_method_menu', 'digirisk_evaluation_method', array('methodeEvaluation', 'methodeEvaluationMainPage'));
			// On crée le menu des Dangers
			add_options_page('Digirisk : ' . __('Dangers', 'evarisk' ), __('Digirisk - Dangers', 'evarisk'), 'digi_view_danger_menu', 'digirisk_danger', array('danger', 'dangerMainPage'));

			// On crée le menu d'import d'utilisateurs
			add_users_page('Digirisk : ' . __('Import d\'utilisateurs pour l\'&eacute;valuation des risques', 'evarisk' ), __('Import Digirisk', 'evarisk'), 'digi_view_user_import_menu', 'digirisk_import_users', array('evaUser','importUserPage'));

			// On crée le menu de gestion des groupes d'utilisateurs
			add_users_page('Digirisk : ' . __('Gestion des groupes d\'utilisateurs', 'evarisk' ), __('Groupes Digirisk', 'evarisk'), 'digi_view_user_groups_menu', DIGI_URL_SLUG_USER_GROUP, array('digirisk_groups','elementMainPage'));

			// On crée le menu de gestion des droits des utilisateurs
			add_users_page('Digirisk : ' . __('Gestion des droits des utilisateurs', 'evarisk' ), __('Droits Digirisk', 'evarisk'), 'digi_user_right_management_menu', DIGI_URL_SLUG_USER_RIGHT, array('digirisk_permission','elementMainPage'));

			// add_management_page(__('Outils pour le logiciel Digirisk', 'evarisk'), __('Digirisk - Outils', 'evarisk'), 'digi_tools_menu', 'digirisk_tools', array('digirisk_tools', 'main_page'));
			add_management_page(__('Documentation pour le logiciel Digirisk', 'evarisk'), __('Digirisk - Doc', 'evarisk'), 'digi_documentation_management_menu', 'digirisk_doc', array('digirisk_doc', 'mydoc'));

			// On crée le menu de création de veille réglementaire
			// add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('Cr&eacute;ation de r&eacute;f&eacute;renciel', 'evarisk' ), __( 'Cr&eacute;ation de r&eacute;f&eacute;renciel', 'evarisk' ), 'digi_view_regulatory_monitoring_menu', 'digirisk_referentials', array('veilleReglementaire','veilleReglementaireMainPage'));
		}
	}

	/**
	*	Define the javascript to include in each page
	*/
	function digirisk_admin_js(){
		/*	Check the wp version in order to include the good jquery librairy. Causes issue because of wp core update	*/
		global $wp_version;

		if($wp_version < '3.2'){
			wp_enqueue_script('eva_jq', EVA_INC_PLUGIN_URL . 'js/jquery1.6.1.js', '', EVA_PLUGIN_VERSION);
		}
		elseif(!wp_script_is('jquery', 'queue')){
			wp_enqueue_script('jquery');
		}

		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('jquery-form');

		wp_enqueue_script('eva_jq_min', EVA_INC_PLUGIN_URL . 'js/jquery-ui-min.js', '', EVA_PLUGIN_VERSION);

		wp_enqueue_script('eva_main_js', EVA_INC_PLUGIN_URL . 'js/lib.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_google_jsapi', 'http://www.google.com/jsapi', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_jq_datatable', EVA_INC_PLUGIN_URL . 'js/dataTable/jquery.dataTables.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_jq_treetable', EVA_INC_PLUGIN_URL . 'js/treeTable/jquery.treeTable.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_jq_galleriffic', EVA_INC_PLUGIN_URL . 'js/galleriffic/jquery.galleriffic.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_jq_galover', EVA_INC_PLUGIN_URL . 'js/galleriffic/jquery.opacityrollover.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_jq_fieldselect', EVA_INC_PLUGIN_URL . 'js/fieldSelection/jquery-fieldselection.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_jq_fileupload', EVA_INC_PLUGIN_URL . 'js/fileUploader/fileuploader.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_role_js', EVA_INC_PLUGIN_URL . 'js/role.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_jq_autocomplete', EVA_INC_PLUGIN_URL . 'js/jquery.autocomplete.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_jq_timepicker',  EVA_INC_PLUGIN_URL . 'js/jquery-ui-timepicker.js');

		/*	Wordpress postbox	*/
		wp_enqueue_script('eva_wp_postbox_js', admin_url() . 'js/postbox.js', '', EVA_PLUGIN_VERSION);

		/*	Jquery plot utilities	*/
		wp_enqueue_script('eva_jqplot', EVA_INC_PLUGIN_URL . 'js/charts/jquery.jqplot.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_jqplot_excanvas', EVA_INC_PLUGIN_URL . 'js/charts/excanvas.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_jqplot_pointLabel', EVA_INC_PLUGIN_URL . 'js/charts/jqplot.pointLabel.min.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_jqplot_cursor', EVA_INC_PLUGIN_URL . 'js/charts/jquery.cursor.min.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_jqplot_dateAxisRenderer', EVA_INC_PLUGIN_URL . 'js/charts/jquery.dateAxisRenderer.min.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_jqplot_highlighter', EVA_INC_PLUGIN_URL . 'js/charts/jquery.highlighter.min.js', '', EVA_PLUGIN_VERSION);
	}
	/**
	*	Admin javascript "frontend" part definition
	*/
	function frontend_js(){
		/*	Check the wp version in order to include the good jquery librairy. Causes issue because of wp core update	*/
		global $wp_version;

		if($wp_version < '3.2'){
			wp_enqueue_script('eva_jq', EVA_INC_PLUGIN_URL . 'js/jquery1.6.1.js', '', EVA_PLUGIN_VERSION);
		}
		elseif(!wp_script_is('jquery', 'queue')){
			wp_enqueue_script('jquery');
		}

		wp_enqueue_script('jquery-form');

		wp_enqueue_script('eva_jq_min', EVA_INC_PLUGIN_URL . 'js/jquery-ui-min.js', '', EVA_PLUGIN_VERSION);

		wp_enqueue_script('eva_main_js', EVA_INC_PLUGIN_URL . 'js/lib.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_jq_datatable', EVA_INC_PLUGIN_URL . 'js/dataTable/jquery.dataTables.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_jq_treetable', EVA_INC_PLUGIN_URL . 'js/treeTable/jquery.treeTable.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_jq_galleriffic', EVA_INC_PLUGIN_URL . 'js/galleriffic/jquery.galleriffic.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_jq_galover', EVA_INC_PLUGIN_URL . 'js/galleriffic/jquery.opacityrollover.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_jq_fieldselect', EVA_INC_PLUGIN_URL . 'js/fieldSelection/jquery-fieldselection.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_jq_fileupload', EVA_INC_PLUGIN_URL . 'js/fileUploader/fileuploader.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_jq_autocomplete', EVA_INC_PLUGIN_URL . 'js/jquery.autocomplete.js', '', EVA_PLUGIN_VERSION);
	}

	/**
	*	Define the css to include in each page
	*/
	function digirisk_admin_css(){
		wp_register_style('eva_jquery_datatable', EVA_INC_PLUGIN_URL . 'css/dataTable/demo_table_jui.css', '', EVA_PLUGIN_VERSION);
		wp_enqueue_style('eva_jquery_datatable');
		wp_register_style('eva_jquery_custom', EVA_INC_PLUGIN_URL . 'css/jquery-ui-1.7.2.custom.css', '', EVA_PLUGIN_VERSION);
		wp_enqueue_style('eva_jquery_custom');
		wp_register_style('eva_jquery_treetable', EVA_INC_PLUGIN_URL . 'css/treeTable/treeTable.css', '', EVA_PLUGIN_VERSION);
		wp_enqueue_style('eva_jquery_treetable');
		wp_register_style('eva_jquery_fileuploader', EVA_INC_PLUGIN_URL . 'css/fileUploader/fileuploader.css', '', EVA_PLUGIN_VERSION);
		wp_enqueue_style('eva_jquery_fileuploader');
		wp_register_style('eva_main_css', EVA_INC_PLUGIN_URL . 'css/eva.css', '', EVA_PLUGIN_VERSION);
		wp_enqueue_style('eva_main_css');
		wp_register_style('eva_duer_css', EVA_INC_PLUGIN_URL . 'css/documentUnique.css', '', EVA_PLUGIN_VERSION);
		wp_enqueue_style('eva_duer_css');
		wp_register_style('eva_autocomplete_css', EVA_INC_PLUGIN_URL . 'css/jquery.autocomplete.css', '', EVA_PLUGIN_VERSION);
		wp_enqueue_style('eva_autocomplete_css');
		wp_register_style('eva_lightbox_css', EVA_INC_PLUGIN_URL . 'css/lightbox.css', '', EVA_PLUGIN_VERSION);
		wp_enqueue_style('eva_lightbox_css');
		wp_register_style('eva_jqplot_css', EVA_INC_PLUGIN_URL . 'css/charts/jquery.jqplot.css', '', EVA_PLUGIN_VERSION);
		wp_enqueue_style('eva_jqplot_css');
	}
	/**
	*	Admin javascript "file" part definition
	*/
	function frontend_css(){
		wp_register_style('eva_jquery_datatable', EVA_INC_PLUGIN_URL . 'css/dataTable/demo_table_jui.css', '', EVA_PLUGIN_VERSION);
		wp_enqueue_style('eva_jquery_datatable');
		wp_register_style('eva_jquery_custom', EVA_INC_PLUGIN_URL . 'css/jquery-ui-1.7.2.custom.css', '', EVA_PLUGIN_VERSION);
		wp_enqueue_style('eva_jquery_custom');
		wp_register_style('eva_jquery_fileuploader', EVA_INC_PLUGIN_URL . 'css/fileUploader/fileuploader.css', '', EVA_PLUGIN_VERSION);
		wp_enqueue_style('eva_jquery_fileuploader');
		wp_register_style('eva_main_css', EVA_INC_PLUGIN_URL . 'css/eva.css', '', EVA_PLUGIN_VERSION);
		wp_enqueue_style('eva_main_css');
		wp_register_style('eva_autocomplete_css', EVA_INC_PLUGIN_URL . 'css/jquery.autocomplete.css', '', EVA_PLUGIN_VERSION);
		wp_enqueue_style('eva_autocomplete_css');
	}



	/**
	*
	*/
	function digirisk_add_admin_js(){
		echo '<script type="text/javascript" >
			var EVA_AJAX_FILE_URL = "' . EVA_INC_PLUGIN_URL . 'ajax.php";
		</script>';
	}

}