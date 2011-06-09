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

$elementList = wpshop_attributes::getElementWithAttributeAndValue(WPSHOP_DBT_PRODUCT, wpshop_entities::getEntityIdFromCode('product'), 1, 'code', '', "'valid'");
// echo '<pre>';print_r($elementList);echo '</pre>';
if(is_array($elementList) && (count($elementList) > 0))
{
	foreach($elementList as $elementId => $element)
	{
		$items[$element['attributes']['product_name']['value'] . ' (' . $element['reference'] . ')'] = $elementId;
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