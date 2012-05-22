<?php
require_once(EVA_CONFIG);
require_once(EVA_LIB_PLUGIN_DIR . 'veilleReglementaire/evaGroupeQuestion.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'veilleReglementaire/evaQuestion.class.php' );
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

switch($_POST['act'])
{
	case 'save' :
		$enonce = mysql_real_escape_string(digirisk_tools::IsValid_Variable($_POST['enonce']));
		$code = mysql_real_escape_string(digirisk_tools::IsValid_Variable($_POST['code']));
		$idGroupeQuestion = mysql_real_escape_string(digirisk_tools::IsValid_Variable($_POST['idGroupeQuestion']));
		EvaQuestion::saveNewQuestion($enonce, $code, $idGroupeQuestion);
		break;
	case 'update' :
		$idQuestion = mysql_real_escape_string(digirisk_tools::IsValid_Variable($_POST['id']));
		$enonce = mysql_real_escape_string(digirisk_tools::IsValid_Variable($_POST['enonce']));
		$code = mysql_real_escape_string(digirisk_tools::IsValid_Variable($_POST['code']));
		EvaQuestion::updateQuestion($idQuestion, $enonce, $code);
		break;
	case 'delete':
		$idQuestion = mysql_real_escape_string(digirisk_tools::IsValid_Variable($_POST['idQuestion']));
		$idGroupeQuestions = mysql_real_escape_string(digirisk_tools::IsValid_Variable($_POST['idGroupeQuestions']));
		EvaQuestion::deleteQuestion($idQuestion, $idGroupeQuestions);
		break;
}