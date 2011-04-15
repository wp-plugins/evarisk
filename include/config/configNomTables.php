<?php
	
	/**
	 * Tables name define variables
	 */
	DEFINE('PREFIXE_EVARISK', $wpdb->prefix . "eva__");
	// Tables étalon
	DEFINE('TABLE_ETALON', PREFIXE_EVARISK . "etalon");
	DEFINE('TABLE_VALEUR_ETALON', PREFIXE_EVARISK . "valeur_etalon");
	DEFINE('TABLE_EQUIVALENCE_ETALON', PREFIXE_EVARISK . "equivalence_etalon");
	// Tables méthode
	DEFINE('TABLE_METHODE', PREFIXE_EVARISK . "methode");
	DEFINE('TABLE_OPERATEUR', PREFIXE_EVARISK . "operateur");
	DEFINE('TABLE_VARIABLE', PREFIXE_EVARISK . "variable");
	DEFINE('TABLE_VALEUR_ALTERNATIVE', PREFIXE_EVARISK . "valeur_alternative");
	DEFINE('TABLE_AVOIR_VARIABLE', PREFIXE_EVARISK . "avoir_variable");
	DEFINE('TABLE_AVOIR_OPERATEUR', PREFIXE_EVARISK . "avoir_operateur");
	// Table risque
	DEFINE('TABLE_FP', PREFIXE_EVARISK . "ged_documents_fiche_de_poste");
	DEFINE('TABLE_DUER', PREFIXE_EVARISK . "ged_documents_document_unique");
	DEFINE('TABLE_RISQUE', PREFIXE_EVARISK . "risque");
	DEFINE('TABLE_AVOIR_VALEUR', PREFIXE_EVARISK . "risque_evaluation");
	//	Tables Ged
	DEFINE('TABLE_GED_DOCUMENTS', PREFIXE_EVARISK . "ged_documents");
	// Tables diverses
	DEFINE('TABLE_PHOTO', PREFIXE_EVARISK . "photo");
	DEFINE('TABLE_PHOTO_LIAISON', PREFIXE_EVARISK . "liaison_photo_element");
	DEFINE('TABLE_ADRESSE', PREFIXE_EVARISK . "adresse");
	DEFINE('TABLE_PERSONNE', PREFIXE_EVARISK . "personne");
	DEFINE('TABLE_OPTION', PREFIXE_EVARISK . "option");
	DEFINE('TABLE_VERSION', PREFIXE_EVARISK . "version");
	// Tables hierarchie
	DEFINE('TABLE_GROUPEMENT', PREFIXE_EVARISK . "groupement");
	DEFINE('TABLE_UNITE_TRAVAIL', PREFIXE_EVARISK . "unite_travail");
	// Tables danger
	DEFINE('TABLE_CATEGORIE_DANGER', PREFIXE_EVARISK . "categorie_danger");
	DEFINE('TABLE_DANGER', PREFIXE_EVARISK . "danger");
	// Tables actions correctives
	DEFINE('TABLE_TACHE', PREFIXE_EVARISK . "actions_correctives_tache");
	DEFINE('TABLE_ACTIVITE', PREFIXE_EVARISK . "actions_correctives_actions");
	DEFINE('TABLE_ACTIVITE_SUIVI', PREFIXE_EVARISK . "actions_correctives_suivi");
	DEFINE('TABLE_LIAISON_TACHE_ELEMENT', PREFIXE_EVARISK . "liaison_tache_element");
	
	// Veille référencielle
	DEFINE('PREFIXE_VEILLE', PREFIXE_EVARISK . "veille_");
	// Table veille référencielle
	DEFINE('TABLE_TEXTE_REFERENCIEL', PREFIXE_VEILLE . "texte_referenciel");
	DEFINE('TABLE_CORRESPOND_TEXTE_REFERENCIEL', PREFIXE_VEILLE . "correspond_texte_referenciel");
	DEFINE('TABLE_GROUPE_QUESTION', PREFIXE_VEILLE . "groupe_question");
	DEFINE('TABLE_POSSEDE_QUESTION', PREFIXE_VEILLE . "possede_question");
	DEFINE('TABLE_QUESTION', PREFIXE_VEILLE . "question");
	DEFINE('TABLE_ACCEPTE_REPONSE', PREFIXE_VEILLE . "accepte_reponse");
	DEFINE('TABLE_REPONSE', PREFIXE_VEILLE . "reponse");
	DEFINE('TABLE_REPONSE_QUESTION', PREFIXE_VEILLE . "reponse_question");
	DEFINE('TABLE_CONCERNE_PAR_TEXTE_REFERENCIEL', PREFIXE_VEILLE . "concerne_par_texte_referenciel");
	
	// Modele EAV
	DEFINE('PREFIXE_EAV', $wpdb->prefix . "eav__");
	// Table entités
	DEFINE('TABLE_ENTITY', PREFIXE_EAV . "entity_type");
	// Table liaison entités / attributs
	DEFINE('TABLE_ENTITY_ATTRIBUTE_LINK', PREFIXE_EAV . "entity_attribute_link");
	// Table attributes set
	DEFINE('TABLE_ATTRIBUTE_SET', PREFIXE_EAV . "attribute_set");
	// Table attributs
	DEFINE('TABLE_ATTRIBUTE', PREFIXE_EAV . "attribute");
	// Table groupes attributs
	DEFINE('TABLE_ATTRIBUTE_GROUP', PREFIXE_EAV . "attribute_group");
	// Table attributs option
	DEFINE('TABLE_ATTRIBUTE_OPTION', PREFIXE_EAV . "attribute_option");
	// Table attributs option value
	DEFINE('TABLE_ATTRIBUTE_OPTION_VALUE', PREFIXE_EAV . "attribute_option_value");
	
	// Table attributs value
	DEFINE('TABLE_ATTRIBUTE_VALUE', PREFIXE_EVARISK . "%sentity_%s");

	// Tables valeurs users
	DEFINE('TABLE_EAV_USER_DATETIME', PREFIXE_EVARISK . "users_entity_datetime");
	DEFINE('TABLE_EAV_USER_DECIMAL', PREFIXE_EVARISK . "users_entity_decimal");
	DEFINE('TABLE_EAV_USER_INT', PREFIXE_EVARISK . "users_entity_int");
	DEFINE('TABLE_EAV_USER_TEXT', PREFIXE_EVARISK . "users_entity_text");
	DEFINE('TABLE_EAV_USER_VARCHAR', PREFIXE_EVARISK . "users_entity_varchar");

	DEFINE('TABLE_EVA_USER_GROUP', PREFIXE_EVARISK . "users_group");
	DEFINE('TABLE_EVA_USER_GROUP_DETAILS', PREFIXE_EVARISK . "users_group_details");
	DEFINE('TABLE_LIAISON_USER_GROUPS', PREFIXE_EVARISK . "users_group_bind");
	DEFINE('TABLE_EVA_EVALUATOR_GROUP_BIND', PREFIXE_EVARISK . "evaluators_group_bind");

	DEFINE('TABLE_EVA_ROLES', PREFIXE_EVARISK . "roles");
	DEFINE('TABLE_EVA_USER_GROUP_ROLES_DETAILS', PREFIXE_EVARISK . "users_group_roles_details");

	DEFINE('TABLE_EVA_EVALUATOR_GROUP', PREFIXE_EVARISK . "evaluators_group");
	DEFINE('TABLE_EVA_EVALUATOR_GROUP_DETAILS', PREFIXE_EVARISK . "evaluators_group_details");


	DEFINE('TABLE_LIAISON_USER_ELEMENT', PREFIXE_EVARISK . "liaison_utilisateur_element");

	DEFINE('TABLE_CATEGORIE_PRECONISATION', PREFIXE_EVARISK . "preconisation_categorie");
	DEFINE('TABLE_PRECONISATION', PREFIXE_EVARISK . "preconisation");
	DEFINE('TABLE_LIAISON_PRECONISATION_ELEMENT', PREFIXE_EVARISK . "liaison_preconisation_element");



	//	TABLES PLUS UTILISEES A PARTIR DE LA VERSION > 18
	DEFINE('TABLE_AC_TACHE', PREFIXE_EVARISK . "tache");
	DEFINE('TABLE_AC_ACTIVITE', PREFIXE_EVARISK . "activite");
	DEFINE('TABLE_AC_ACTION', PREFIXE_EVARISK . "actions_correctives_activite");
	DEFINE('TABLE_AVOIR_VALEUR_OLD', PREFIXE_EVARISK . "avoir_valeur");

	//	TABLES PLUS UTILISEES A PARTIR DE LA VERSION > 25
	DEFINE('TABLE_LIAISON_USER_EVALUATION', PREFIXE_EVARISK . "users_evaluation_bind");

	//	TABLES PLUS UTILISEES A PARTIR DE LA VERSION > 37
	DEFINE('TABLE_DUER_OLD', PREFIXE_EVARISK . "document_unique");

	//	TABLES PLUS UTILISEES A PARTIR DE LA VERSION > 39
	// Tables EPI
	DEFINE('TABLE_EPI', PREFIXE_EVARISK . "ppe");
	DEFINE('TABLE_UTILISE_EPI', PREFIXE_EVARISK . "use_ppe");	
?>