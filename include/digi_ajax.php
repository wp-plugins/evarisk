<?php
/**
 * Ajax request management file
 *
 * @author Evarisk <dev@evarisk.com>
 * @version 5.1.6.6
 * @package evarisk
 * @subpackage include
 */

/*	Check if file is include. No direct access possible with file url	*/
if ( !defined( 'EVA_PLUGIN_VERSION' ) ) {
	die( __('Access is not allowed by this way', 'evarisk') );
}

function digi_ajax_repair_db() {
	check_ajax_referer( 'digi_repair_db_per_version', 'digi_ajax_nonce' );
	$bool = false;
	$version_id = isset($_POST['version_id']) ? intval(digirisk_tools::IsValid_Variable($_POST['version_id'])) : null;

	if ( !empty($version_id) ) {
		$bool = digirisk_install::repair_database( $version_id );
	}

	echo json_encode(array($bool, $version_id));
	die();
}
add_action('wp_ajax_digi_ajax_repair_db', 'digi_ajax_repair_db');