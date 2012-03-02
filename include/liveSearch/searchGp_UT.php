<?php

define('DOING_AJAX', true);
define('WP_ADMIN', true);
require_once('../../../../../wp-load.php');
require_once(ABSPATH . 'wp-admin/includes/admin.php');
require_once('../../evarisk.php');
require_once(EVA_INC_PLUGIN_DIR . 'includes.php' );

@header('Content-Type: text/html; charset=' . get_option('blog_charset'));

$table_element = (isset($_GET['table_element']) && (trim($_GET['table_element']) != '')) ? strtolower(eva_tools::IsValid_Variable($_GET['table_element'])) : '';
$id_element = (isset($_GET['id_element']) && (trim($_GET['id_element']) != '')) ? strtolower(eva_tools::IsValid_Variable($_GET['id_element'])) : '0';
$q = strtolower($_GET["q"]);
if (!$q) return;

$items = array();

$more_unit_query = "";
if(($table_element == TABLE_UNITE_TRAVAIL) && ($id_element > 0)){
	$more_unit_query = " AND UT.id != '" . $id_element . "'";
}
$query = $wpdb->prepare("
SELECT CONCAT('" . TABLE_UNITE_TRAVAIL . "-_-', UT.id) AS id, CONCAT('" . ELEMENT_IDENTIFIER_UT . "', UT.id, ' - ', UT.nom) AS name
FROM " . TABLE_UNITE_TRAVAIL . " AS UT
WHERE UT.Status = 'valid'" . $more_unit_query . "
ORDER BY UT.nom");
$unit_list = $wpdb->get_results($query);
foreach($unit_list as $unit){
	$items[$unit->name] = $unit->id;
}

$more_grpt_query = "";
if(($table_element == TABLE_GROUPEMENT) && ($id_element > 0)){
	$more_grpt_query = " AND GP.id != '" . $id_element . "'";
}
$query = $wpdb->prepare("
SELECT CONCAT('" . TABLE_GROUPEMENT . "-_-', GP.id) AS id, CONCAT('" . ELEMENT_IDENTIFIER_GP . "', GP.id, ' - ', GP.nom) AS name
FROM " . TABLE_GROUPEMENT . " AS GP
WHERE GP.Status = 'valid'
	AND nom != 'Groupement Racine'" . $more_grpt_query . "
ORDER BY GP.nom");
$groupement_list = $wpdb->get_results($query);
foreach($groupement_list as $gpt){
	$items[$gpt->name] = $gpt->id;
}

foreach ($items as $key => $value){
	if (strpos(strtolower($key), $q) !== false){
		echo "$key|$value\n";
	}
}

?>