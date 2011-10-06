<?php
/*
Plugin Name: Evarisk
Plugin URI: http://www.evarisk.com/document-unique-logiciel
Description: Avec le plugin "Evarisk" vous pourrez r&eacute;aliser, de fa&ccedil;on simple et intuitive, le ou les documents uniques de vos entreprises et g&eacute;rer toutes les donn&eacute;es li&eacute;es &agrave; la s&eacute;curit&eacute; de votre personnel.
Version: 5.1.4.2
Author: Evarisk
Author URI: http://www.evarisk.com
*/

/**
* Plugin main file.
* 
*	This file is the main file called by wordpress for our plugin use. It define the basic vars and include the different file needed to use the plugin
* @author Evarisk <dev@evarisk.com>
* @version 5.0
* @package Digirisk
*/

/**
*	First thing we define the main directory for our plugin in a super global var	
*/
DEFINE('EVA_PLUGIN_DIR', basename(dirname(__FILE__)));

/**
*	Include the different config for the plugin	
*/
require_once(WP_PLUGIN_DIR . '/' . EVA_PLUGIN_DIR . '/include/config/config.php' );
/**
*	Define the path where to get the config file for the plugin
*/
DEFINE('EVA_CONFIG', EVA_INC_PLUGIN_DIR . 'config/config.php');
/**
*	Include the file which includes the different files used by all the plugin
*/
require_once(EVA_INC_PLUGIN_DIR . 'includes.php');

/**
*	Include tools that will launch different action when plugin will be loaded
*/
require_once(EVA_LIB_PLUGIN_DIR . 'init.class.php' );
/**
*	On plugin loading, call the different element for creation output for our plugin	
*/
add_action('plugins_loaded', array('digirisk_init', 'digirisk_plugin_load'));
