<?php
/*
 * @version v5.0
 */
require_once(EVA_CONFIG );
require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'arborescence.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'adresse/evaAddress.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/groupement/eva_groupement.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/uniteDeTravail/uniteDeTravail.class.php' );
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		
if($_POST['act'] == 'save')
{
	$nom = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['nom_unite_travail']));
	$idGroupementPere = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['groupementPere']));
	
	$groupement = EvaGroupement::getGroupement($idGroupementPere);
	$groupementDescendant = Arborescence::getDescendants(TABLE_GROUPEMENT, $groupement);
	if(count($groupementDescendant) == 0)
	{
		$workingUnitResult = UniteDeTravail::saveNewWorkingUnit($nom, $idGroupementPere);
		
		$_POST['act'] = 'update';
		$_POST['id'] = $wpdb->insert_id;
	}
	else
	{
echo '
<script type="text/javascript" >
	evarisk("#message").html(\'<img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="error" />' . __('Vous ne pouvez pas ajouter une unit&eacute; de travail au m&ecirc;me niveau qu\\\'un groupement', 'evarisk') . '\');
	evarisk("#message").addClass("updated");
	evarisk("#message").show();
	setTimeout(function(){
		evarisk("#message").removeClass("updated");
		evarisk("#message").hide();
	},7500);
</script>';
	}
}
if($_POST['act'] == 'update')
{
	$id_unite_travail = $_POST['id'];
	$nom = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['nom_unite_travail']));
	$idGroupementPere = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['groupementPere']));
	
	$uniteTravailUpdate = UniteDeTravail::getWorkingUnitByName($nom);
	
	$ligne1 = $_POST['adresse_ligne_1'];
	$ligne2 = $_POST['adresse_ligne_2'];
	$ville = $_POST['ville'];
	$codePostal = $_POST['code_postal'];
	$latitude = $_POST['latitude'];
	$longitude = $_POST['longitude'];
	$address = new EvaAddress($uniteTravailUpdate->id_adresse, $ligne1, $ligne2, $codePostal, $ville, $latitude, $longitude, 'Valid');
	$address->save();
	$idAdresse = $address->getId();
	
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
	
	$workingUnitResult = UniteDeTravail::updateWorkingUnit($id_unite_travail, $nom, $description, $telephone, $effectif, $idAdresse, $idGroupementPere);
}
if($_POST['act'] == 'delete')
{
	$workingUnitResult = UniteDeTravail::deleteWorkingUnit($_POST['id']);
}