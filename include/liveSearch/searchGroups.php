<?php

define('DOING_AJAX', true);
define('WP_ADMIN', true);
require_once('../../../../../wp-load.php');
require_once(ABSPATH . 'wp-admin/includes/admin.php');
require_once('../../evarisk.php');
require_once(EVA_INC_PLUGIN_DIR . 'includes.php' );

@header('Content-Type: text/html; charset=' . get_option('blog_charset'));

$q = strtolower($_GET["q"]);
if (!$q) return;

$items = array();

$elementList = digirisk_groups::getElement('', "'valid'");
if(is_array($elementList) && (count($elementList) > 0))
{
	foreach($elementList as $element)
	{
		$items[$element->name] = $element->id;
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