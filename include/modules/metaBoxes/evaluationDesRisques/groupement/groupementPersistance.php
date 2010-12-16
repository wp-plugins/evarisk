<?php
/*
 * @version v5.0
 */
require_once(EVA_CONFIG );
require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'arborescence.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'adresse/evaAddress.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/groupement/eva_groupement.class.php' );
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

$search = "Status='Valid' AND nom<>'Groupement Racine'";
$groupement = EvaGroupement::getGroupements($search);
			
if($_POST['act'] == 'save')
{
	$nom = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['nom_groupement']));
	EvaGroupement::saveNewGroupement($nom);
	$_POST['id'] = $wpdb->insert_id;
	$_POST['act'] = 'update';
}
if($_POST['act'] == 'update')
{
	$id_groupement = $_POST['id'];
	$nom = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['nom_groupement']));
	
	$groupementUpdate = EvaGroupement::getGroupementByName($nom);
	$ligne1 = $_POST['adresse_ligne_1'];
	$ligne2 = $_POST['adresse_ligne_2'];
	$ville = $_POST['ville'];
	$codePostal = $_POST['code_postal'];
	$latitude = $_POST['latitude'];
	$longitude = $_POST['longitude'];
	$address = new EvaAddress($groupementUpdate->id_adresse, $ligne1, $ligne2, $codePostal, $ville, $latitude, $longitude, 'Valid');
	$address->save();
	$idAdresse = $address->getId();
	
	$idGroupementPere = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['groupementPere']));
	
	$effectif = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['effectif']));
	if($effectif == '')
	{
		$effectif = null;
	}
	$description = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['description']));
	if($description == '')
	{
		$description = null;
	}
	$telephone = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['telephone']));
	if($telephone == '')
	{
		$telephone = null;
	}
	
	EvaGroupement::updateGroupement($id_groupement, $nom, $description, $telephone, $effectif, $idAdresse, $idGroupementPere);
}
if($_POST['act'] == 'delete')
{
	EvaGroupement::deleteGroupement($_POST['id']);
}