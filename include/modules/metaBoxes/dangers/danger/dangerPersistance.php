<?php

require_once(EVA_CONFIG );
require_once(EVA_LIB_PLUGIN_DIR . 'arborescence.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'danger/categorieDangers/categorieDangers.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'danger/danger/evaDanger.class.php' );
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

if($_REQUEST['act'] == 'save')
{
	$nom = mysql_real_escape_string(digirisk_tools::IsValid_Variable($_REQUEST['nom_danger']));
	$idCategorieMere = mysql_real_escape_string(digirisk_tools::IsValid_Variable($_REQUEST['categorieMere']));
	
	EvaDanger::saveNewDanger($nom, $idCategorieMere);
	
	$_REQUEST['act'] = 'update';
	$_REQUEST['id'] = $wpdb->insert_id;
}
if($_REQUEST['act'] == 'update')
{
	$id_danger = $_REQUEST['id'];
	$nom = mysql_real_escape_string(digirisk_tools::IsValid_Variable($_POST['nom_danger']));
	$idCategorieMere = mysql_real_escape_string(digirisk_tools::IsValid_Variable($_POST['categorieMere']));
	$description = $_REQUEST['description'];
	
	EvaDanger::updateDanger($id_danger, $nom, $idCategorieMere, $description);
}
if($_REQUEST['act'] == 'delete')
{
	$id_danger = $_REQUEST['id'];
	EvaDanger::deleteDanger($id_danger);
}