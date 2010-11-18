<?php

require_once(EVA_CONFIG );
require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'arborescence.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'danger/categorieDangers/categorieDangers.class.php' );
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

$search = "`Status`='Valid' AND nom<>'Categorie Racine'";
$categories_danger = categorieDangers::getCategoriesDanger($search);
			
if($_POST['act'] == 'save')
{
	$nom = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['nom_categorie']));
	categorieDangers::saveNewCategorie($nom);
	
	$_POST['act'] = 'update';
	$_POST['id'] = $wpdb->insert_id;
}
if($_POST['act'] == 'update')
{
	$id_categorie = $_POST['id'];
	$nom = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['nom_categorie']));
	$description = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['description']));
	$idCategorieMere = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['categorieMere']));
	categorieDangers::updateCategorie($id_categorie, $nom, $description, $idCategorieMere);
}
if($_POST['act'] == 'delete')
{
	$id_categorie = $_POST['id'];
	categorieDangers::deleteCategorie($id_categorie);
}