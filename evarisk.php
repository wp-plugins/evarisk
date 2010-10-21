<?php
/*
Plugin Name: Evarisk
Plugin URI: http://www.evarisk.com/document-unique-logiciel
Description: Avec le plugin "Evarisk" vous pourrez r&eacute;aliser, de fa&ccedil;on simple et intuitive, le ou les documents uniques de vos entreprises et g&eacute;rer toutes les donn&eacute;es li&eacute;es &agrave; la s&eacute;curit&eacute; de votre personnel.
Version: 5.0.1
Author: Evarisk
Author URI: http://www.evarisk.com
*/
DEFINE('EVA_PLUGIN_DIR', basename(dirname(__FILE__)));

global $wpdb;
	
require_once('include/config/config.php' );
DEFINE('EVA_CONFIG', EVA_INC_PLUGIN_DIR . 'config/config.php');
require_once('include/evarisk_admin.php' );

// Installation du plugin (fichier evariskInstallEavModel.php)
require_once(EVA_MODULES_PLUGIN_DIR . 'installation/evariskInstallEavModel.php' );
register_activation_hook( __FILE__, 'eavModelInstall' );
register_activation_hook( __FILE__, 'eavModelUsersValueInstall' );
register_activation_hook( __FILE__, 'evaUsersGroup' );
register_activation_hook( __FILE__, 'evaRoles' );

// Fonction d'initialisation du plugin
add_action('plugins_loaded', 'evarisk_init');
function evarisk_init() {
	// Ajout des script de eva_admin
	add_action('admin_head', "eva_add_admin_js");
	// Ajout des options
	add_action('admin_init', 'evarisk_add_options');
	// Ajout du menu utilisateur
	add_action('admin_menu', 'evarisk_add_menu');
	
	$locale = get_locale();
	if( !empty( $locale ))
	{
		$mofile = EVA_HOME_DIR . '/languages/evarisk-' . $locale . '.mo';
		load_textdomain('evarisk', $mofile);
	}
}


	require_once(EVA_LIB_PLUGIN_DIR . 'eav/eav_attribute.class.php');
	$eav_attribute = new eav_attribute();

	$editUser = false;
	$userId = 0;
	$environnementName = '';
	if(strstr($_SERVER['REQUEST_URI'], 'user-edit.php') !== false)
	{
		$editUser = true;

		/*	Get the environnement	name	*/
		$environnementName = 'edit_user_profile';

		/*	Get the user id regarding the environnement	*/
		$userId = $_REQUEST['user_id'];
	}
	if(strstr($_SERVER['REQUEST_URI'], 'profile.php') !== false)
	{
		$editUser = true;

		/*	Get the environnement	name	*/
		$environnementName = 'show_user_profile';

		/*	Get the user id regarding the environnement	*/
		require_once(ABSPATH . WPINC . '/pluggable.php');
		$current_user = wp_get_current_user();
		$userId = $current_user->ID;
	}
	if( $editUser && ($userId > 0) )
	{
		include_once(EVA_LIB_PLUGIN_DIR . 'users/evaUser.class.php');
		$evaUser = new evaUser('user_profile');

		add_action($environnementName, array($evaUser,'evaUserAttributeForm'));

		$evaUser->setUserToUpdate($userId);

		/*	We launch the userUpdate Function - We test in the function if there is a post user if not the case we do nothing	*/
		add_action('init', array($evaUser, 'evaUserUpdateProfile'));
	}

?>