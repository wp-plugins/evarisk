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
	$id_methode_eva = $_REQUEST['selectionMethode'];
        $penibilite = $_REQUEST['choix_penibilite'];
        $dangerDefaut = $_REQUEST['choix_danger'];
        
        $tableauChoix  = array();
        if($dangerDefaut != "")
        {
            $tableauChoix[] = $dangerDefaut;
        }
        
        if($penibilite != "")
        {
            $tableauChoix[] = $penibilite;
        }
        else
        {
           $id_methode_eva = 0; 
        }
        
        $tabToSave = serialize($tableauChoix);
        
	EvaDanger::updateDanger($id_danger, $nom, $idCategorieMere, $description, $tabToSave, $id_methode_eva);
}
if($_REQUEST['act'] == 'delete')
{
	$id_danger = $_REQUEST['id'];
	EvaDanger::deleteDanger($id_danger);
}