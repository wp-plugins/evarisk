<?php
require_once(EVA_CONFIG);
require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'veilleReglementaire/evaGroupeQuestion.class.php' );
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

$search = "Status='Valid' AND nom<>'Groupe Question Racine'";
$groupeQuestions = EvaGroupeQuestions::getGroupesQuestions($search);

switch($_POST['act'])
{
		
	case 'addExtrait' :
		$id = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['idGroupeQuestion']));
		$groupeQuestions = EvaGroupeQuestions::getGroupeQuestions($id);
		$idGroupeQuestion = $groupeQuestions->id;
		$extrait = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['extrait']));
		EvaGroupeQuestions::updateExtraitGroupeQuestions($idGroupeQuestion, $extrait);
		break;
	
	case 'save' :
		$nom = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['nom']));
		EvaGroupeQuestions::saveNewGroupeQuestions($nom);
		$nouveauGroupeQuestions = EvaGroupeQuestions::getGroupeQuestionsByName($nom);
		$_POST['id'] =  $nouveauGroupeQuestions->id;
	case 'update' :
		$idGroupeQuestion = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['id']));
		$nom = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['nom']));
		$code = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['code']));
		$idGroupeQuestionPere = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['idPere']));
		if($_POST['extrait'] != null)
		{
			$extrait = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['extrait']));
		}
		EvaGroupeQuestions::updateGroupeQuestions($idGroupeQuestion, $nom, $code, $idGroupeQuestionPere, $extrait);
		break;
	case 'delete':
		$idGroupeQuestion = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['idGroupeQuestion']));
		EvaGroupeQuestions::deleteGroupeQuestions($idGroupeQuestion);
		break;
}