<?php
require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'options.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'version/EvaVersion.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayDesign.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayInput.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'database.class.php');

		require_once(EVA_LIB_PLUGIN_DIR . 'dashboard/dashboard.class.php');
		require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/evaluationDesRisques.class.php');
		require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/actionsCorrectives.class.php');
		require_once(EVA_LIB_PLUGIN_DIR . 'methode/methodeEvaluation.class.php' );
		require_once(EVA_LIB_PLUGIN_DIR . 'danger/danger.class.php' );
		require_once(EVA_LIB_PLUGIN_DIR . 'users/evaUserEvaluatorGroup.class.php' );
		require_once(EVA_LIB_PLUGIN_DIR . 'users/evaUserGroup.class.php' );
		require_once(EVA_LIB_PLUGIN_DIR . 'users/evaUser.class.php' );
		require_once(EVA_LIB_PLUGIN_DIR . 'veilleReglementaire/veilleReglementaire.class.php' );
		require_once(EVA_LIB_PLUGIN_DIR . 'evaRecommandation/evaRecommandation.class.php' );

/**
 * Function adding the menu in wordpress side barr
 */
function evarisk_add_menu() {

	require_once(EVA_MODULES_PLUGIN_DIR . 'installation/verificationsPlugin.php');

	require_once(EVA_MODULES_PLUGIN_DIR . 'installation/initialisationPermissions.php');
	evarisk_init_permission();
	if(EvaVersion::getVersion('base_evarisk') < 1)
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

		// On créé le menu des options
		add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('Options', 'evarisk' ), __( 'Options', 'evarisk' ), 'Evarisk_:_editer_les_options', 'digirisk_options', array('options','optionMainPage'));

		//	On créé le menu des préconisation
		// add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('Pr&eacute;conisations', 'evarisk' ), __( 'Pr&eacute;conisations', 'evarisk' ), 'Evarisk_:_voir_les_preconisations', 'digirisk_recommandation', array('evaRecommandation', 'evaRecommandationMainPage'));

		// On crée le menu des méthodes
		add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('M&eacute;thodes d\'&eacute;valuation', 'evarisk' ), __( 'M&eacute;thodes d\'&eacute;valuation', 'evarisk' ), 'Evarisk_:_voir_les_methodes', 'digirisk_evaluation_method', array('methodeEvaluation','methodeEvaluationMainPage'));

		// On crée le menu des Dangers
		add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('Dangers', 'evarisk' ), __( 'Dangers', 'evarisk' ), 'Evarisk_:_voir_les_dangers', 'digirisk_danger', array('danger','dangerMainPage'));

		//	On crée le menu des gestions des groupes d'utilisateurs
		add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('Groupe d\'utilisateurs', 'evarisk' ), __( 'Groupe d\'utilisateurs', 'evarisk' ), 'Evarisk_:_gerer_groupes_utilisateurs', 'digirisk_users_group', array('evaUserGroup','evaUserGroupMainPage'));

		//	On crée le menu des gestions des groupes d'évaluateurs
		add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('Groupe d\'&eacute;valuateurs', 'evarisk' ), __( 'Groupe d\'&eacute;valuateurs', 'evarisk' ), 'Evarisk_:_gerer_groupes_evaluateurs', 'digirisk_evaluators_group', array('evaUserEvaluatorGroup','evaUserEvaluatorGroupMainPage'));

		// On crée le menu de l'évaluation des risques
		add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('&Eacute;valuation des risques', 'evarisk' ), __( '&Eacute;valuation des risques', 'evarisk' ), 'Evarisk_:_voir_les_groupements', 'digirisk_risk_evaluation', array('evaluationDesRisques','evaluationDesRisquesMainPage'));

		// On crée le menu des actions correctives
		// if(options::getOptionValue('action_correctives_avancees') == 'oui')
		// {
		add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('Actions correctives', 'evarisk' ), __( 'Actions correctives', 'evarisk' ), 'Evarisk_:_voir_les_groupements', 'digirisk_correctiv_actions', array('actionsCorrectives','actionsCorrectivesMainPage'));
		// }


		// On crée le menu d'import d'utilisateurs
		add_users_page('Digirisk : ' . __('Import d\'utilisateurs', 'evarisk' ), __( 'Import d\'utilisateurs', 'evarisk' ), 'Evarisk_:_gerer_utilisateurs', 'digirisk_import_users', array('evaUser','importUserPage'));

		// On crée le menu de création de veille réglementaire
		// add_submenu_page('digirisk_dashboard', 'Digirisk : ' . __('Cr&eacute;ation de r&eacute;f&eacute;renciel', 'evarisk' ), __( 'Cr&eacute;ation de r&eacute;f&eacute;renciel', 'evarisk' ), 'Evarisk_:_creer_referenciel', 'digirisk_referentials', array('veilleReglementaire','veilleReglementaireMainPage'));

		// On crée le menu de gestion des attributs des utilisateurs
		// add_submenu_page('users.php','Digirisk : ' . __('Attributs utilisateurs', 'evarisk'), __('Attributs utilisateurs', 'evarisk'), 'Evarisk_:_gerer_attributs', EVA_MODULES_PLUGIN_DIR . 'eavManagement/attributes.php');
		// On crée le menu de gestion des roles utilisateurs
		// add_submenu_page('digirisk_dashboard','Digirisk : ' . __('Droits d\'acc&egrave;s', 'evarisk'), __('Droits d\'acc&egrave;s', 'evarisk'),  'Evarisk_:_gerer_droit_d_acces', EVA_MODULES_PLUGIN_DIR . 'evaRole/roles.php');
	}

	

}

function evarisk_add_options() {
}

function eva_add_admin_js()
{
	echo'<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/jquery.js"></script>
	<script type="text/javascript" >var evarisk = jQuery.noConflict();</script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/lib.js"></script>
	<script type="text/javascript" src="http://www.google.com/jsapi"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/dataTable/jquery.dataTables.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/jquery-ui-min.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/jQueryUI/development-bundle/ui/i18n/jquery-ui-i18n.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/jQueryUI/development-bundle/ui/ui.gantt.min.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/treeTable/jquery.treeTable.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/galleriffic/jquery.galleriffic.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/galleriffic/jquery.opacityrollover.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/fieldSelection/jquery-fieldselection.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/fileUploader/fileuploader.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/users.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/role.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/eav.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/jquery.editable.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/jquery.autocomplete.js"></script>
	<script type="text/javascript" src="' . EVA_PLUGIN_DIR . '../../../wp-admin/js/postbox.js"></script>

	
	<style type="text/css" title="currentStyle">
		@import "' . EVA_INC_PLUGIN_URL . 'css/dataTable/demo_table_jui.css";
		@import "' . EVA_INC_PLUGIN_URL . 'css/jQueryUI/smoothness/jquery-ui-1.7.2.custom.css";
		@import "' . EVA_INC_PLUGIN_URL . 'css/treeTable/treeTable.css";
		@import "' . EVA_INC_PLUGIN_URL . 'css/fileUploader/fileuploader.css";
		@import "' . EVA_INC_PLUGIN_URL . 'css/eva.css";
		@import "' . EVA_INC_PLUGIN_URL . 'css/utilisateur.css";
		@import "' . EVA_INC_PLUGIN_URL . 'css/eav.css";
		@import "' . EVA_INC_PLUGIN_URL . 'css/role.css";
		@import "' . EVA_INC_PLUGIN_URL . 'css/documentUnique.css";
		@import "' . EVA_INC_PLUGIN_URL . 'css/jquery.autocomplete.css";
		@import "' . EVA_INC_PLUGIN_URL . 'css/lightbox.css";
	</style>

	<script type="text/javascript" >
		var EVA_AJAX_FILE_URL = "' . EVA_INC_PLUGIN_URL . 'ajax.php";
	</script>
';
}
?>