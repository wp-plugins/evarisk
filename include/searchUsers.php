<?php

define('DOING_AJAX', true);
define('WP_ADMIN', true);
require_once('../../../../wp-load.php');
require_once(ABSPATH . 'wp-admin/includes/admin.php');
require_once('../evarisk.php');

@header('Content-Type: text/html; charset=' . get_option('blog_charset'));

$q = strtolower($_GET["q"]);
if (!$q) return;

$items = array();

require_once(EVA_LIB_PLUGIN_DIR . 'users/evaUser.class.php');
$listeUtilisateurs = evaUser::getCompleteUserList();
if(is_array($listeUtilisateurs) && (count($listeUtilisateurs) > 0))
{
	foreach($listeUtilisateurs as $utilisateur)
	{
		$items[$utilisateur['user_lastname'] . ' ' . $utilisateur['user_firstname']] = $utilisateur['user_id'];
	}
}

foreach ($items as $key => $value)
{
	if (strpos(strtolower($key), $q) !== false) 
	{
		echo "$key|$value\n";
	}
}

?>