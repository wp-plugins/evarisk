<?php

require_once(EVA_CONFIG );
require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'arborescence.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'danger/categorieDangers/categorieDangers.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'danger/danger/evaDanger.class.php' );
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

if($_POST['act'] == 'save')
{
	$nom = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['nom_danger']));
	$idCategorieMere = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['categorieMere']));
	
	EvaDanger::saveNewDanger($nom, $idCategorieMere);
	
	$_POST['act'] = 'update';
	$_POST['id'] = $wpdb->insert_id;
}
if($_POST['act'] == 'update')
{
	$id_danger = $_POST['id'];
	$nom = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['nom_danger']));
	$idCategorieMere = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['categorieMere']));
	$description = $_POST['description'];
	
	EvaDanger::updateDanger($id_danger, $nom, $idCategorieMere, $description);
}
if($_POST['act'] == 'delete')
{
	$id_danger = $_POST['id'];
	EvaDanger::deleteDanger($id_danger);
}