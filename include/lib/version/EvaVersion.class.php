<?php
/**
 * 
 *
 * @author Evarisk
 * @version v5.0
 */
include_once(EVA_CONFIG);
require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php');

class EvaVersion
{
	function getVersion($nom)
	{
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		global $wpdb;
		$nom = eva_tools::IsValid_Variable($nom);
		if( $wpdb->get_var("show tables like '" . TABLE_VERSION . "'") == TABLE_VERSION)
		{
			$query = $wpdb->prepare("SELECT version version
				FROM " . TABLE_VERSION . "
				WHERE nom = %s", $nom);
			$resultat = $wpdb->get_row($query);
			return $resultat->version;
		}
		else
		{
			return -1;
		}
	}
	
	function updateVersion($nom, $version)
	{
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		global $wpdb;
		$nom = eva_tools::IsValid_Variable($nom);
		$version = eva_tools::IsValid_Variable($version);
		
		$sql = "UPDATE " . TABLE_VERSION . " SET version=" . $version . " WHERE nom='" . $nom . "';";
		dbDelta($sql);
	}
}
?>