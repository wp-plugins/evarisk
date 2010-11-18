<?php
require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'version/EvaVersion.class.php');

/**
 * Function adding the menu in wordpress side barr
 */
function evarisk_add_menu() {

	require_once(EVA_MODULES_PLUGIN_DIR . 'installation/initialisationPermissions.php');
	evarisk_init_permission();
	if(EvaVersion::getVersion('base_evarisk') < 1)
	{
		// On crée le menu principal
		add_menu_page( 'Evarisk : ' . __('Installation', 'evarisk'), 'Evarisk', 'Evarisk_:_utiliser_le_plugin', EVA_MODULES_PLUGIN_DIR . '/installation/formulaireInstallation.php' , '', EVA_FAVICON);
		// On propose le formulaire de création
		add_submenu_page(EVA_MODULES_PLUGIN_DIR . '/installation/formulaireInstallation.php','Evarisk : ' . __('Installation', 'evarisk'), __('Installation', 'evarisk'),  'Evarisk_:_utiliser_le_plugin', EVA_MODULES_PLUGIN_DIR . '/installation/formulaireInstallation.php');
	}
	else
	{
		require_once(EVA_MODULES_PLUGIN_DIR . 'installation/creationTables.php');
		evarisk_creationTables();
		// On crée le menu principal
		add_menu_page( 'Evarisk : ' . __('Accueil', 'evarisk'), 'Evarisk', 'Evarisk_:_utiliser_le_plugin', EVA_MODULES_PLUGIN_DIR . '/tableauDeBord/accueil.php' , '', EVA_FAVICON);
		// On renomme l'accueil
		add_submenu_page(EVA_MODULES_PLUGIN_DIR . '/tableauDeBord/accueil.php','Evarisk : ' . __('Accueil', 'evarisk'), __('Accueil', 'evarisk'),  'Evarisk_:_utiliser_le_plugin', EVA_MODULES_PLUGIN_DIR . '/tableauDeBord/accueil.php');

		// On crée le menu des méthodes
		add_submenu_page(EVA_MODULES_PLUGIN_DIR . '/tableauDeBord/accueil.php','Evarisk : ' . __('M&eacute;thodes d\'&eacute;valuation', 'evarisk'), __('M&eacute;thodes d\'&eacute;valuation', 'evarisk'),  'Evarisk_:_voir_les_methodes', EVA_MODULES_PLUGIN_DIR . 'methode/methodeEvaluation.php');

		// On crée le menu des Dangers
		add_submenu_page(EVA_MODULES_PLUGIN_DIR . '/tableauDeBord/accueil.php','Evarisk : ' . __('Dangers', 'evarisk'), __('Dangers', 'evarisk'),  'Evarisk_:_voir_les_dangers', EVA_MODULES_PLUGIN_DIR . 'dangers/dangers.php');

		//	On crée le menu des gestions des groupes d'utilisateurs
		add_submenu_page(EVA_MODULES_PLUGIN_DIR . '/tableauDeBord/accueil.php','Evarisk : ' . __('Groupe d\'utilisateurs', 'evarisk'), __('Groupes  d\'utilisateurs', 'evarisk'), 'Evarisk_:_gerer_groupes_utilisateurs', EVA_MODULES_PLUGIN_DIR . 'groupesUtilisateur/groupes.php');

		//	On crée le menu des gestions des groupes d'utilisateurs
		add_submenu_page(EVA_MODULES_PLUGIN_DIR . '/tableauDeBord/accueil.php','Evarisk : ' . __('Groupe d\'&eacute;valuateurs', 'evarisk'), __('Groupes  d\'&eacute;valuateurs', 'evarisk'), 'Evarisk_:_gerer_groupes_evaluateurs', EVA_MODULES_PLUGIN_DIR . 'groupesEvaluateur/groupes.php');

		// On crée le menu de l'évaluation des risques
		add_submenu_page(EVA_MODULES_PLUGIN_DIR . '/tableauDeBord/accueil.php','Evarisk : ' . __('&Eacute;valuation des risques', 'evarisk'), __('&Eacute;valuation des risques', 'evarisk'),  'Evarisk_:_voir_les_groupements', EVA_MODULES_PLUGIN_DIR . '/evaluationDesRisques/evaluationDesRisques.php');

		// On crée le menu des actions correctives
		// add_submenu_page(EVA_MODULES_PLUGIN_DIR . '/tableauDeBord/accueil.php','Evarisk : '.  __('Actions correctives', 'evarisk'), __('Actions correctives', 'evarisk'),  'Evarisk_:_voir_les_actions', EVA_MODULES_PLUGIN_DIR . 'actionsCorrectives/actionsCorrectives.php');

		// On crée le menu de création de veille réglementaire
		// add_submenu_page(EVA_MODULES_PLUGIN_DIR . '/tableauDeBord/accueil.php','Evarisk : ' . __('Cr&eacute;ation de r&eacute;f&eacute;renciel', 'evarisk'), __('Cr&eacute;ation de r&eacute;f&eacute;renciel', 'evarisk'),  'Evarisk_:_creer_referenciel', EVA_MODULES_PLUGIN_DIR . 'veilleReglementaire/formulaireCreation.php');

		// On crée le menu de gestion des attributs des utilisateurs
		// add_submenu_page('users.php','Evarisk : ' . __('Attributs utilisateurs', 'evarisk'), __('Attributs utilisateurs', 'evarisk'), 'Evarisk_:_gerer_attributs', EVA_MODULES_PLUGIN_DIR . 'eavManagement/attributes.php');

		// On crée le menu d'import d'utilisateurs
		add_submenu_page('users.php','Evarisk : ' . __('Import d\'utilisateurs', 'evarisk'), __('Import d\'utilisateurs', 'evarisk'), 'Evarisk_:_gerer_utilisateurs', EVA_MODULES_PLUGIN_DIR . 'utilisateurs/importUtilisateurs.php');

		// On crée le menu de gestion des roles utilisateurs
		// add_submenu_page(EVA_MODULES_PLUGIN_DIR . '/tableauDeBord/accueil.php','Evarisk : ' . __('Droits d\'acc&egrave;s', 'evarisk'), __('Droits d\'acc&egrave;s', 'evarisk'),  'Evarisk_:_gerer_droit_d_acces', EVA_MODULES_PLUGIN_DIR . 'evaRole/roles.php');
	}
}

function evarisk_add_options() {
}

function eva_add_admin_js()
{
	echo'<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/jquery.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/lib.js"></script>
	<script type="text/javascript" src="http://www.google.com/jsapi"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/dataTable/jquery.dataTables.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/jQueryUI/js/jquery-ui-1.7.2.custom.min.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/jQueryUI/development-bundle/ui/i18n/jquery-ui-i18n.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/jQueryUI/development-bundle/ui/ui.gantt.min.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/treeTable/jquery.treeTable.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/galleriffic/jquery.galleriffic.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/galleriffic/jquery.opacityrollover.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/fieldSelection/jquery-fieldselection.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/uploadify/jquery.uploadify.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/fileUploader/fileuploader.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/users.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/role.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/eav.js"></script>
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
	</style>';
}
?>