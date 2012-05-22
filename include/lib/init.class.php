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
		add_action('admin_init', array('digirisk_options', 'declare_options'));

		/*	Get the current language to translate the different text in plugin	*/
		$locale = get_locale();
		$moFile = EVA_HOME_DIR . 'languages/evarisk-' . $locale . '.mo';
		if( !empty($locale) && (is_file($moFile)) ){
			load_textdomain('evarisk', $moFile);
		}		
		else{
			load_textdomain('evarisk', EVA_HOME_DIR . 'languages/evarisk-fr_FR.mo');
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
		$main_options = get_option('digirisk_options');
		if($main_options['digi_ac_allow_front_ask'] == 'oui'){
			add_action('wp_print_styles', array('digirisk_init', 'frontend_css'));
			add_action('wp_head', array('digirisk_init', 'frontend_js_output'));
			add_action('init', array('digirisk_init', 'frontend_js'));
		}
	}

	/**
	*	Create the main left menu with different parts
	*/
	function digirisk_menu(){

		/*	Add the options menu in the options section	*/
		add_options_page(__('Options du logiciel digirisk', 'evarisk'), __('Digirisk', 'evarisk'), 'digi_view_options_menu', DIGI_URL_SLUG_MAIN_OPTION, array('digirisk_options', 'optionMainPage'));

		if(digirisk_options::getDbOption('base_evarisk') < 1){
			// On crée le menu principal
			add_menu_page('Digirisk : ' . __('Installation', 'evarisk'), __( 'Digirisk', 'evarisk' ), 'activate_plugins', 'digirisk_installation', array('digirisk_install', 'installation_form'), EVA_FAVICON, 3);
			// On propose le formulaire de création
			add_submenu_page('digirisk_installation', 'Evarisk : ' . __('Installation', 'evarisk'), __('Installation', 'evarisk'),  'activate_plugins', 'digirisk_installation', array('digirisk_install', 'installation_form'));
		}
		else{
			digirisk_install::update_digirisk();

			// On crée le menu principal
			add_menu_page('Digirisk : ' . __('Accueil', 'evarisk'), __( 'Digirisk', 'evarisk' ), 'digi_view_dashboard_menu', 'digirisk_dashboard', array('dashboard', 'dashboardMainPage'), EVA_FAVICON, 3);

			// On renomme l'accueil
			add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('Tableau de bord', 'evarisk' ), __( 'Tableau de bord', 'evarisk' ), 'digi_view_dashboard_menu', 'digirisk_dashboard', array('dashboard','dashboardMainPage'));

			// On crée le menu de l'évaluation des risques
			add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('&Eacute;valuation des risques', 'evarisk' ), __( '&Eacute;valuation des risques', 'evarisk' ), 'digi_view_evaluation_menu', 'digirisk_risk_evaluation', array('evaluationDesRisques','evaluationDesRisquesMainPage'));

			// On crée le menu des actions correctives
			add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('Actions correctives', 'evarisk' ), __( 'Actions correctives', 'evarisk' ), 'digi_view_correctiv_action_menu', 'digirisk_correctiv_actions', array('actionsCorrectives','actionsCorrectivesMainPage'));

			// On crée le menu d'édition des profils utilisateurs
			add_users_page('Digirisk : ' . __('Profil utilisateur', 'evarisk' ), __('Profil Digirisk', 'evarisk'), 'digi_view_user_profil_menu', 'digirisk_users_profil', array('evaUser','digi_user_profil'));

			// On crée le menu d'import d'utilisateurs
			add_users_page('Digirisk : ' . __('Import d\'utilisateurs pour l\'&eacute;valuation des risques', 'evarisk' ), __('Import Digirisk', 'evarisk'), 'digi_view_user_import_menu', 'digirisk_import_users', array('evaUser','importUserPage'));

			// On crée le menu de gestion des groupes d'utilisateurs
			add_users_page('Digirisk : ' . __('Gestion des groupes d\'utilisateurs', 'evarisk' ), __('Groupes Digirisk', 'evarisk'), 'digi_view_user_groups_menu', DIGI_URL_SLUG_USER_GROUP, array('digirisk_groups','elementMainPage'));

			// On crée le menu de gestion des droits des utilisateurs
			add_users_page('Digirisk : ' . __('Gestion des droits des utilisateurs', 'evarisk' ), __('Droits Digirisk', 'evarisk'), 'digi_user_right_management_menu', DIGI_URL_SLUG_USER_RIGHT, array('digirisk_permission','elementMainPage'));

			add_management_page(__('Outils pour le logiciel Digirisk', 'evarisk'), __('Digirisk - Outils', 'evarisk'), 'digi_tools_menu', 'digirisk_tools', array('digirisk_tools', 'main_page'));
			add_management_page(__('Documentation pour le logiciel Digirisk', 'evarisk'), __('Digirisk - Doc', 'evarisk'), 'digi_documentation_management_menu', 'digirisk_doc', array('digirisk_doc', 'mydoc'));

			// On crée le menu de création de veille réglementaire
			// add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('Cr&eacute;ation de r&eacute;f&eacute;renciel', 'evarisk' ), __( 'Cr&eacute;ation de r&eacute;f&eacute;renciel', 'evarisk' ), 'digi_view_regulatory_monitoring_menu', 'digirisk_referentials', array('veilleReglementaire','veilleReglementaireMainPage'));
		}
	}

	/**
	*	Define the javascript to include in each page
	*/
	function digirisk_admin_js(){
		wp_enqueue_script('jquery');

		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('jquery-form');
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_script('jquery-ui-slider');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-ui-droppable');
		wp_enqueue_script('jquery-ui-datepicker');

		wp_enqueue_script('eva_main_js', EVA_INC_PLUGIN_URL . 'js/lib.js', '', EVA_PLUGIN_VERSION);

		wp_enqueue_script('eva_jq_min', EVA_INC_PLUGIN_URL . 'js/jquery-libs/jquery-ui.js', '', EVA_PLUGIN_VERSION);

		wp_enqueue_script('eva_jq_datatable', EVA_INC_PLUGIN_URL . 'js/dataTable/jquery.dataTables.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_jq_treetable', EVA_INC_PLUGIN_URL . 'js/jquery-libs/jquery.treeTable.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_jq_galleriffic', EVA_INC_PLUGIN_URL . 'js/jquery-libs/jquery.galleriffic.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_jq_galover', EVA_INC_PLUGIN_URL . 'js/jquery-libs/jquery.opacityrollover.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_jq_fieldselect', EVA_INC_PLUGIN_URL . 'js/jquery-libs/jquery-fieldselection.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_role_js', EVA_INC_PLUGIN_URL . 'js/role.js', '', EVA_PLUGIN_VERSION);

		wp_enqueue_script('eva_jq_fileupload', EVA_INC_PLUGIN_URL . 'js/jquery-libs/fileuploader.js', '', EVA_PLUGIN_VERSION);

		wp_enqueue_script('eva_google_jsapi', 'http://www.google.com/jsapi', '', EVA_PLUGIN_VERSION);
		/*	Wordpress postbox	*/
		wp_enqueue_script('eva_wp_postbox_js', admin_url() . 'js/postbox.js', '', EVA_PLUGIN_VERSION);

		/*	Jquery plot utilities	*/
		if(!empty($_GET['page']) && ($_GET['page'] == 'digirisk_risk_evaluation')){
			wp_enqueue_script('eva_jqplot', EVA_INC_PLUGIN_URL . 'js/charts/jquery.jqplot.js', '', EVA_PLUGIN_VERSION);
			wp_enqueue_script('eva_jqplot_excanvas', EVA_INC_PLUGIN_URL . 'js/charts/excanvas.js', '', EVA_PLUGIN_VERSION);
			wp_enqueue_script('eva_jqplot_pointLabel', EVA_INC_PLUGIN_URL . 'js/charts/jqplot.pointLabel.min.js', '', EVA_PLUGIN_VERSION);
			wp_enqueue_script('eva_jqplot_cursor', EVA_INC_PLUGIN_URL . 'js/charts/jquery.cursor.min.js', '', EVA_PLUGIN_VERSION);
			wp_enqueue_script('eva_jqplot_dateAxisRenderer', EVA_INC_PLUGIN_URL . 'js/charts/jquery.dateAxisRenderer.min.js', '', EVA_PLUGIN_VERSION);
			wp_enqueue_script('eva_jqplot_highlighter', EVA_INC_PLUGIN_URL . 'js/charts/jquery.highlighter.min.js', '', EVA_PLUGIN_VERSION);
		}
	}
	/**
	*
	*/
	function digirisk_add_admin_js(){
		echo '<script type="text/javascript" >
			var EVA_AJAX_FILE_URL = "' . EVA_INC_PLUGIN_URL . 'ajax.php";
			jQuery.fn.highlight=function(b){function a(e,j){var l=0;if(e.nodeType==3){var k=e.data.toUpperCase().indexOf(j);if(k>=0){var h=document.createElement("span");h.className="highlight";var f=e.splitText(k);var c=f.splitText(j.length);var d=f.cloneNode(true);h.appendChild(d);f.parentNode.replaceChild(h,f);l=1}}else{if(e.nodeType==1&&e.childNodes&&!/(script|style)/i.test(e.tagName)){for(var g=0;g<e.childNodes.length;++g){g+=a(e.childNodes[g],j)}}}return l}return this.each(function(){a(this,b.toUpperCase())})};jQuery.fn.removeHighlight=function(){return this.find("span.highlight").each(function(){this.parentNode.firstChild.nodeName;with(this.parentNode){replaceChild(this.firstChild,this);normalize()}}).end()};
		</script>';
	}
	/**
	*	Admin javascript "frontend" part definition
	*/
	function frontend_js(){
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-form');
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_script('jquery-ui-slider');

		wp_enqueue_script('eva_main_js', EVA_INC_PLUGIN_URL . 'js/lib.js', '', EVA_PLUGIN_VERSION);
		wp_enqueue_script('eva_jq_min', EVA_INC_PLUGIN_URL . 'js/jquery-libs/jquery-ui.js', '', EVA_PLUGIN_VERSION);
	}

	/**
	*	Define the css to include in each page
	*/
	function digirisk_admin_css(){
		wp_register_style('eva_jquery_ui', EVA_INC_PLUGIN_URL . 'css/jquery-libs/jquery-ui.css', '', EVA_PLUGIN_VERSION);
		wp_enqueue_style('eva_jquery_ui');

		wp_register_style('eva_jquery_datatable', EVA_INC_PLUGIN_URL . 'css/jquery-libs/datatable.css', '', EVA_PLUGIN_VERSION);
		wp_enqueue_style('eva_jquery_datatable');
		
		wp_register_style('eva_jquery_treetable', EVA_INC_PLUGIN_URL . 'css/jquery-libs/treeTable.css', '', EVA_PLUGIN_VERSION);
		wp_enqueue_style('eva_jquery_treetable');
		wp_register_style('eva_jquery_fileuploader', EVA_INC_PLUGIN_URL . 'css/jquery-libs/fileuploader.css', '', EVA_PLUGIN_VERSION);
		wp_enqueue_style('eva_jquery_fileuploader');
		wp_register_style('eva_lightbox_css', EVA_INC_PLUGIN_URL . 'css/lightbox.css', '', EVA_PLUGIN_VERSION);
		wp_enqueue_style('eva_lightbox_css');

		if(!empty($_GET['page']) && ($_GET['page'] == 'digirisk_risk_evaluation')){
			wp_register_style('eva_jqplot_css', EVA_INC_PLUGIN_URL . 'css/jquery-libs/jquery.jqplot.css', '', EVA_PLUGIN_VERSION);
			wp_enqueue_style('eva_jqplot_css');
		}

		wp_register_style('eva_main_css', EVA_INC_PLUGIN_URL . 'css/eva.css', '', EVA_PLUGIN_VERSION);
		wp_enqueue_style('eva_main_css');
	}
	/**
	*	Admin javascript "file" part definition
	*/
	function frontend_css(){
		wp_register_style('eva_main_css', EVA_INC_PLUGIN_URL . 'css/eva.css', '', EVA_PLUGIN_VERSION);
		wp_enqueue_style('eva_main_css');
		wp_register_style('eva_jquery_ui', EVA_INC_PLUGIN_URL . 'css/jquery-libs/jquery-ui.css', '', EVA_PLUGIN_VERSION);
		wp_enqueue_style('eva_jquery_ui');
	}

	/**
	*	Admin javascript "frontend" part definition
	*/
	function frontend_js_output(){
		echo '<script type="text/javascript">var EVA_AJAX_FILE_URL = "' . EVA_INC_PLUGIN_URL . 'ajax.php";</script>';
	}

}