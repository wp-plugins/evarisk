<?php
/*
Installation de l'extension
	- Création des tables
	- Initialisation des permissions
*/
require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php');

function evarisk_creationTables()
{// Création des tables lors de l'installation
	
	require_once(EVA_LIB_PLUGIN_DIR . 'version/EvaVersion.class.php');
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	global $wpdb;
	
	
	if(EvaVersion::getVersion('base_evarisk') < 1)
	{
		// On vérifie si la table version n'existe pas
		if( $wpdb->get_var("show tables like '" . TABLE_VERSION . "'") != TABLE_VERSION) {
			// On construit la requete SQL de création de table
			$sql = 
				"CREATE TABLE " . TABLE_VERSION . " (
					id INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
					nom VARCHAR( 255 ) NOT NULL UNIQUE,
					version INT( 10 ) NOT NULL,
					Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid'
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
			// Execution de la requete
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_VERSION . " (id, nom, version) VALUES 
				('1', 'base_evarisk', '0');";
			// Execution de la requete
			$wpdb->query($sql);
		}
	
		{// Various tables
			// On vérifie si la table photo n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_PHOTO . "'") != TABLE_PHOTO) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_PHOTO . " (
						id INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
						idDestination INT( 10 ) NOT NULL,
						tableDestination VARCHAR( 255) NOT NULL,
						photo VARCHAR( 255 ) NOT NULL,
						Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						INDEX ( idDestination, tableDestination )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			
			// On vérifie si la table adresse n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_ADRESSE . "'") != TABLE_ADRESSE) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_ADRESSE . " (
						id INT( 10 ) NOT NULL  AUTO_INCREMENT PRIMARY KEY,
						ligne1 VARCHAR( 32 ) NOT NULL,
						ligne2 VARCHAR( 32 ) NOT NULL,
						ville VARCHAR( 26 ) NOT NULL,
						codePostal VARCHAR( 5 ) NOT NULL,
						longitude FLOAT NOT NULL,
						latitude FLOAT NOT NULL,
						Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid'
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			
			// On vérifie si la table personne n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_PERSONNE . "'") != TABLE_PERSONNE) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_PERSONNE . " (
						id INT( 10 ) NOT NULL  AUTO_INCREMENT PRIMARY KEY,
						nom VARCHAR( 255 ) NOT NULL,
						prenom VARCHAR( 255 ) NOT NULL,
						sexe ENUM( 'Masculin', 'Feminin' ) NOT NULL,
						dateNaissance DATE,
						id_adresse INT( 10 ),
						telephoneFixe VARCHAR( 21 ),
						telephonePortable VARCHAR( 21 ),
						telephonePoste VARCHAR( 21 ),
						couriel VARCHAR( 255 ),
						mainPhoto VARCHAR( 255 ),
						note TEXT,
						personneAPrevenir INT( 10 ),
						Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						INDEX ( id_adresse ),
						INDEX ( personneAPrevenir ),
						FOREIGN KEY (id_adresse) REFERENCES ". TABLE_ADRESSE ." ( id ),
						FOREIGN KEY (personneAPrevenir) REFERENCES ". TABLE_PERSONNE ." ( id )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			
			// On vérifie si la table options n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_OPTION . "'") != TABLE_OPTION) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_OPTION . " (
						id INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
						nom INT( 10 ) NOT NULL,
						valeur VARCHAR( 255) NOT NULL,
						Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						UNIQUE ( nom )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
		}
		{// Compagny hierarchie tables
			// On vérifie si la table groupement n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_GROUPEMENT . "'") != TABLE_GROUPEMENT) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_GROUPEMENT . " (
						id INT( 10 ) NOT NULL  AUTO_INCREMENT PRIMARY KEY,
						nom VARCHAR( 255 ) NOT NULL,
						description TEXT,
						id_adresse INT( 10 ),
						telephoneGroupement VARCHAR( 21 ),
						id_responsable INT(10),
						effectif INT(10),
						mainPhoto VARCHAR( 255 ),
						limiteGauche INT(16) NOT NULL,
						limiteDroite INT(16) NOT NULL,
						Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						UNIQUE ( nom ),
						INDEX ( id_adresse ),
						INDEX ( id_responsable ),
						FOREIGN KEY (id_adresse) REFERENCES ". TABLE_ADRESSE ." ( id ),
						FOREIGN KEY (id_responsable) REFERENCES ". TABLE_PERSONNE ." ( id )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_GROUPEMENT . " (id, nom, limiteGauche, limiteDroite) VALUES 
				('1', 'Groupement Racine', '0', '1');";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On vérifie si la table unité de travail n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_UNITE_TRAVAIL . "'") != TABLE_UNITE_TRAVAIL ) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_UNITE_TRAVAIL . " (
						id INT( 10 ) NOT NULL  AUTO_INCREMENT PRIMARY KEY,
						nom VARCHAR( 255 ) NOT NULL,
						description TEXT,
						id_adresse INT( 10 ),
						telephoneUnite VARCHAR( 21 ),
						id_groupement INT(10),
						id_responsable INT(10),
						effectif INT(10),
						mainPhoto VARCHAR( 255 ),
						Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						UNIQUE ( nom ),
						INDEX ( id_adresse ),
						INDEX ( id_groupement ),
						INDEX ( id_responsable ),
						FOREIGN KEY (id_adresse) REFERENCES ". TABLE_ADRESSE ." ( id ),
						FOREIGN KEY (id_groupement) REFERENCES ". TABLE_GROUPEMENT ." ( id ),
						FOREIGN KEY (id_responsable) REFERENCES ". TABLE_PERSONNE ." ( id )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
		}
		{// Dangers tables
			// On vérifie si la table danger n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_CATEGORIE_DANGER . "'") != TABLE_CATEGORIE_DANGER ) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_CATEGORIE_DANGER . " (
						id INT( 10 ) NOT NULL  AUTO_INCREMENT PRIMARY KEY,
						nom VARCHAR( 255 ) NOT NULL,
						description TEXT,
						limiteGauche INT(16) NOT NULL,
						limiteDroite INT(16) NOT NULL,
						Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						UNIQUE ( nom )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_CATEGORIE_DANGER . " (id, nom, limiteGauche, limiteDroite) VALUES ('1', 'Categorie Racine', '0', '1');";
				$wpdb->query($sql);
			}
			// On vérifie si la table danger n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_DANGER . "'") != TABLE_DANGER ) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_DANGER . " (
						id INT( 10 ) NOT NULL  AUTO_INCREMENT PRIMARY KEY,
						id_categorie INT( 10 ) NOT NULL ,
						code_danger INT( 10 ),
						nom VARCHAR( 255 ) NOT NULL,
						description TEXT,
						Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						UNIQUE ( nom ),
						INDEX ( id_categorie ),
						UNIQUE (id_categorie, code_danger),
						FOREIGN KEY (id_categorie) REFERENCES ". TABLE_CATEGORIE_DANGER ." ( id )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
		}
		{// Methods tables
			// On vérifie si la table opérateur n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_OPERATEUR . "'") != TABLE_OPERATEUR ) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_OPERATEUR . " (
						symbole varchar(20) NOT NULL PRIMARY KEY,
						Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid'
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_OPERATEUR . " (symbole) VALUES 
				('/'),
				('*'),
				('-'),
				('+')
				;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On vérifie si la table variable n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_VARIABLE . "'") != TABLE_VARIABLE ) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_VARIABLE . " (
						id INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						nom VARCHAR( 255 ) NOT NULL,
						min FLOAT UNSIGNED NOT NULL,
						max FLOAT UNSIGNED NOT NULL,
						annotation TEXT,
						Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid'
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On vérifie si la table valeur alternative n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_VALEUR_ALTERNATIVE . "'") != TABLE_VALEUR_ALTERNATIVE ) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_VALEUR_ALTERNATIVE . " (
					id_variable INT(10) NOT NULL ,
					valeur INT(10) NOT NULL ,
					valeurAlternative FLOAT NOT NULL ,
					date DATE NOT NULL ,
					Status ENUM('Valid', 'Moderated', 'Deleted') NOT NULL DEFAULT 'Valid',
					INDEX (id_variable),
					INDEX (id_variable , valeur),
					FOREIGN KEY (id_variable) REFERENCES " . TABLE_VARIABLE . " (id)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On vérifie si la table méthode n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_METHODE . "'") != TABLE_METHODE ) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_METHODE . " (
						id INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						nom VARCHAR( 255 ) NOT NULL,
						Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						UNIQUE (nom)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On vérifie si la table avoir variable n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_AVOIR_VARIABLE . "'") != TABLE_AVOIR_VARIABLE) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_AVOIR_VARIABLE . " (
						id_methode INT( 10 ) NOT NULL,
						id_variable INT( 10 ) NOT NULL,
						ordre INT(10) NOT NULL,
						date DATETIME NOT NULL,
						Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						PRIMARY KEY (id_methode, id_variable, ordre, date),
						INDEX ( id_methode ),
						INDEX ( id_variable ),
						FOREIGN KEY (id_methode) REFERENCES ". TABLE_METHODE ." ( id ),
						FOREIGN KEY (id_variable) REFERENCES ". TABLE_VARIABLE ." ( id )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On vérifie si la table avoir operateur n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_AVOIR_OPERATEUR . "'") != TABLE_AVOIR_OPERATEUR) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_AVOIR_OPERATEUR . " (
						id_methode INT( 10 ) NOT NULL,
						operateur VARCHAR ( 20 ) NOT NULL,
						ordre INT(10) NOT NULL,
						date DATETIME NOT NULL,
						Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						PRIMARY KEY (id_methode, operateur, ordre, date),
						INDEX ( id_methode ),
						INDEX ( operateur ),
						FOREIGN KEY (id_methode) REFERENCES ". TABLE_METHODE ." ( id ),
						FOREIGN KEY (operateur) REFERENCES ". TABLE_OPERATEUR ." ( symbole )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
		}
		{// Risks tables
			// On vérifie si la table risque défini n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_RISQUE . "'") != TABLE_RISQUE ) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_RISQUE . " (
						id INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						id_danger INT(10) NOT NULL,
						id_methode INT ( 10 ) NOT NULL,
						id_element INT ( 10 ) NOT NULL,
						nomTableElement VARCHAR ( 255 ) NOT NULL,
						commentaire TEXT,
						date DATETIME NOT NULL,
						Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						INDEX ( id_danger ),
						INDEX ( id_methode ),
						INDEX ( id_element, nomTableElement ),
						FOREIGN KEY (id_danger) REFERENCES ". TABLE_DANGER ." ( id ),
						FOREIGN KEY (id_methode) REFERENCES ". TABLE_METHODE ." ( id )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On vérifie si la table avoir_valeur n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_AVOIR_VALEUR . "'") != TABLE_AVOIR_VALEUR ) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_AVOIR_VALEUR . " (
						id_risque INT(10) NOT NULL,
						id_variable INT ( 10 ) NOT NULL,
						valeur INT ( 10 ) NOT NULL,
						date DATETIME NOT NULL,
						Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						PRIMARY KEY (id_risque, id_variable,date),
						INDEX ( id_risque ),
						INDEX ( id_variable ),
						FOREIGN KEY (id_risque) REFERENCES ". TABLE_RISQUE ." ( id ),
						FOREIGN KEY (id_variable) REFERENCES ". TABLE_VARIABLE ." ( id )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
		}
		{// Standard tables
			// On vérifie si la table étalon n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_ETALON . "'") != TABLE_ETALON) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_ETALON . " (
						min INT( 10 ) NOT NULL,
						max INT( 10 ) NOT NULL,
						pas INT( 10 ) NOT NULL,
						Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						PRIMARY KEY(min, max, pas)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_ETALON . " (min, max, pas, Status) VALUES 
				('0', '100', '1', 'Valid');";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On vérifie si la table valeur étalon n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_VALEUR_ETALON . "'") != TABLE_VALEUR_ETALON) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_VALEUR_ETALON . " (
						valeur INT( 10 ) NOT NULL PRIMARY KEY,
						niveauSeuil INT( 10 ) NULL DEFAULT NULL,
						Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						UNIQUE (niveauSeuil)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
				for($i=0; $i<=100; $i++)
				{
					if($i%20 == 0)
					{
						$sql = "INSERT INTO " . TABLE_VALEUR_ETALON . " (valeur, niveauSeuil, Status) VALUES 
						('" . $i . "', '" . ($i/20 + 1) . "', 'Valid');";
					}
					else
					{
						$sql = "INSERT INTO " . TABLE_VALEUR_ETALON . " (valeur, niveauSeuil, Status) VALUES 
						('" . $i . "', null, 'Valid');";
					}
					// Execution de la requete
					$wpdb->query($sql);
				}
			}
			// On vérifie si la table équivalence étalon n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_EQUIVALENCE_ETALON . "'") != TABLE_EQUIVALENCE_ETALON) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_EQUIVALENCE_ETALON . " (
						id_methode INT( 10 ) NOT NULL,
						id_valeur_etalon INT( 10 ) NOT NULL,
						date DATETIME NOT NULL,
						valeurMaxMethode FLOAT NULL,
						Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						PRIMARY KEY(id_methode, id_valeur_etalon, date),
						INDEX ( id_methode ),
						INDEX ( id_valeur_etalon ),
						FOREIGN KEY (id_methode) REFERENCES " . TABLE_METHODE . " ( id ),
						FOREIGN KEY (id_valeur_etalon) REFERENCES " . TABLE_VALEUR_ETALON . " ( valeur )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
		}
		{// PPE tables
			// On vérifie si la table des EPI n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_EPI . "'") != TABLE_EPI) {
				// On construit la requete SQL de création de table
				$sql = "
					CREATE TABLE  " . TABLE_EPI . " (
						id INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT  'PPE identifier',
						name VARCHAR( 255 ) NOT NULL COMMENT  'Name of the PPE',
						path VARCHAR( 255 ) NOT NULL COMMENT  'Path to the image from the root of the plugin',
						status ENUM(  'Valid',  'Moderated',  'Deleted' ) NOT NULL DEFAULT  'Valid' COMMENT  'Status of the recording'
					) ENGINE = MyISAM COMMENT =  'Table containing the personal protective equipment (PPE)';";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On vérifie si la table des EPI n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_UTILISE_EPI . "'") != TABLE_UTILISE_EPI) {
				// On construit la requete SQL de création de table
				$sql = "
					CREATE TABLE  " . TABLE_UTILISE_EPI . " (
						ppeId INT( 10 ) NOT NULL COMMENT  'PPE identifier',
						elementId INT( 10 ) NOT NULL COMMENT  'Element identifier',
						elementTable VARCHAR( 255 ) NOT NULL COMMENT  'Element table name',
						PRIMARY KEY (  ppeId ,  elementId ,  elementTable ),
						INDEX (  ppeId ),
						INDEX (  elementId , elementTable ),
						FOREIGN KEY (  ppeId ) REFERENCES  " . TABLE_EPI . " ( id )
					) ENGINE = MyISAM COMMENT =  'Table linking the PPE with those who wear';";
				// Execution de la requete
				$wpdb->query($sql);
			}

		}
		{// Regulatory Watch tables
			// On vérifie si la table texte référenciel n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_TEXTE_REFERENCIEL . "'") != TABLE_TEXTE_REFERENCIEL ) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_TEXTE_REFERENCIEL . " (
						id INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						rubrique VARCHAR( 255 ) NOT NULL,
						datePremiereRatification DATE NOT NULL,
						dateDerniereModification DATE,
						objet TEXT NOT NULL,
						objetCout TEXT NOT NULL,
						texteSousIntro TEXT NOT NULL,
						adresseTexte VARCHAR( 255 ) NOT NULL,
						analysable BOOLEAN,
						loi BOOLEAN,
						Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						UNIQUE (rubrique)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On vérifie si la table groupe question n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_GROUPE_QUESTION . "'") != TABLE_GROUPE_QUESTION ) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_GROUPE_QUESTION . " (
						id INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						nom VARCHAR( 255 ) NOT NULL,
						code VARCHAR( 255 ) NOT NULL,
						extraitTexte TEXT,
						limiteGauche INT(16) NOT NULL,
						limiteDroite INT(16) NOT NULL,
						Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid'
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_GROUPE_QUESTION . " (id, nom, code, extraitTexte, limiteGauche, limiteDroite, Status) VALUES
					('1', 'Groupe Question Racine', '0', NULL , '0', '3', 'Valid'),
					('2', 'Rubrique 2220', '', NULL , '1', '2', 'Valid');";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On vérifie si la table correspond texte référenciel n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_CORRESPOND_TEXTE_REFERENCIEL . "'") != TABLE_CORRESPOND_TEXTE_REFERENCIEL ) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_CORRESPOND_TEXTE_REFERENCIEL . " (
						id_texte_referenciel INT( 10 ) NOT NULL,
						id_groupe_question INT( 10 ) NOT NULL,
						Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						PRIMARY KEY (id_texte_referenciel, id_groupe_question),
						INDEX ( id_texte_referenciel ),
						INDEX ( id_groupe_question ),
						FOREIGN KEY (id_texte_referenciel) REFERENCES ". TABLE_TEXTE_REFERENCIEL ." ( id ),
						FOREIGN KEY (id_groupe_question) REFERENCES ". TABLE_GROUPE_QUESTION ." ( id )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On vérifie si la table question n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_QUESTION . "'") != TABLE_QUESTION ) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_QUESTION . " (
						id INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						code VARCHAR( 255 ) NOT NULL,
						enonce TEXT NOT NULL,
						Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid'
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On vérifie si la table possède question n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_POSSEDE_QUESTION . "'") != TABLE_POSSEDE_QUESTION ) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_POSSEDE_QUESTION . " (
						id_groupe_question INT( 10 ) NOT NULL,
						id_question INT( 10 ) NOT NULL,
						Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						PRIMARY KEY (id_groupe_question, id_question),
						INDEX ( id_groupe_question ),
						INDEX ( id_question ),
						FOREIGN KEY (id_groupe_question) REFERENCES ". TABLE_GROUPE_QUESTION ." ( id ),
						FOREIGN KEY (id_question) REFERENCES ". TABLE_QUESTION ." ( id )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On vérifie si la table réponse n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_REPONSE . "'") != TABLE_REPONSE ) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_REPONSE . " (
						id INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						nom VARCHAR( 255 ) NOT NULL,
						min INT( 10 ),
						max INT( 10 ),
						Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid'
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_REPONSE . " (nom, min, max, Status) VALUES 
				('Oui', NULL, NULL, 'Valid'),
				('Non', NULL, NULL, 'Valid'),
				('NA', NULL, NULL, 'Valid'),
				('NC', NULL, NULL, 'Valid'),
				('%', 0, 100, 'Valid')";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On vérifie si la table accepte réponse n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_ACCEPTE_REPONSE . "'") != TABLE_ACCEPTE_REPONSE ) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_ACCEPTE_REPONSE . " (
						id_question INT( 10 ) NOT NULL,
						id_reponse INT( 10 ) NOT NULL,
						Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						PRIMARY KEY (id_question, id_reponse),
						INDEX ( id_question ),
						INDEX ( id_reponse ),
						FOREIGN KEY (id_question) REFERENCES ". TABLE_QUESTION ." ( id ),
						FOREIGN KEY (id_reponse) REFERENCES ". TABLE_REPONSE ." ( id )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On vérifie si la table réponse question n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_REPONSE_QUESTION . "'") != TABLE_REPONSE_QUESTION ) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_REPONSE_QUESTION . " (
						id_question INT( 10 ) NOT NULL,
						id_element INT( 10 ) NOT NULL,
						nomTableElement VARCHAR( 255 ) NOT NULL,
						id_reponse INT( 10 ) NOT NULL,
						date DATE NOT NULL,
						limiteValidite DATE,
						valeur INT( 10 ),
						observation TEXT,
						Status ENUM( 'Valid', 'Moderated', 'Deleted', 'archived') NOT NULL DEFAULT 'Valid',
						PRIMARY KEY (id_question, id_element, nomTableElement, date),
						INDEX ( id_question ),
						INDEX ( id_element, nomTableElement ),
						INDEX ( id_reponse ),
						FOREIGN KEY (id_question) REFERENCES ". TABLE_QUESTION ." ( id ),
						FOREIGN KEY (id_reponse) REFERENCES ". TABLE_REPONSE ." ( id )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On vérifie si la table concerné par texte referenciel n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_CONCERNE_PAR_TEXTE_REFERENCIEL . "'") != TABLE_CONCERNE_PAR_TEXTE_REFERENCIEL ) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE " . TABLE_CONCERNE_PAR_TEXTE_REFERENCIEL . " (
						id_texte_referenciel INT( 10 ) NOT NULL,
						id_element INT( 10 ) NOT NULL,
						nomTableElement VARCHAR( 255 ) NOT NULL,
						Status ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						PRIMARY KEY (id_texte_referenciel, id_element, nomTableElement),
						INDEX ( id_texte_referenciel ),
						INDEX ( id_element, nomTableElement ),
						FOREIGN KEY (id_texte_referenciel) REFERENCES ". TABLE_TEXTE_REFERENCIEL ." ( id )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
		}

		{//	Eav model tables
			/*	We check that table exists, if it's not the case we create it	*/
			if( $wpdb->get_var("show tables like '" . TABLE_ENTITY . "'") != TABLE_ENTITY) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE IF NOT EXISTS " . TABLE_ENTITY . " (
						entity_type_id smallint(5) unsigned NOT NULL auto_increment,
						entity_type_code varchar(50) collate utf8_unicode_ci NOT NULL default '',
						entity_model varchar(255) collate utf8_unicode_ci NOT NULL,
						attribute_model varchar(255) collate utf8_unicode_ci NOT NULL,
						entity_table varchar(255) collate utf8_unicode_ci NOT NULL default '',
						value_table_prefix varchar(255) collate utf8_unicode_ci NOT NULL default '',
						default_attribute_set_id smallint(5) unsigned NOT NULL default '0',
						PRIMARY KEY  (entity_type_id),
						KEY entity_name (entity_type_code)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
					INSERT INTO wp_eav__entity_type (entity_type_id, entity_type_code, entity_model, attribute_model, entity_table, value_table_prefix, default_attribute_set_id) VALUES(1, 'eva_users', 'evarisk/users', '', 'users', 'users_', 1);";
				// Execution de la requete
				dbDelta($sql);
			}

			/*	We check that table exists, if it's not the case we create it	*/
			if( $wpdb->get_var("show tables like '" . TABLE_ENTITY_ATTRIBUTE_LINK . "'") != TABLE_ENTITY_ATTRIBUTE_LINK) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE IF NOT EXISTS " . TABLE_ENTITY_ATTRIBUTE_LINK . " (
						entity_attribute_id int(10) unsigned NOT NULL auto_increment,
						entity_type_id smallint(5) unsigned NOT NULL default '0',
						attribute_set_id smallint(5) unsigned NOT NULL default '0',
						attribute_group_id smallint(5) unsigned NOT NULL default '0',
						attribute_id smallint(5) unsigned NOT NULL default '0',
						sort_order smallint(6) NOT NULL default '0',
						PRIMARY KEY  (entity_attribute_id),
						UNIQUE KEY attribute_group_id (attribute_group_id,attribute_id),
						KEY attribute_set_id_3 (attribute_set_id,sort_order),
						KEY FK_EAV_ENTITY_ATTRIVUTE_ATTRIBUTE (attribute_id)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				dbDelta($sql);
			}

			/*	We check that table exists, if it's not the case we create it	*/
			if( $wpdb->get_var("show tables like '" . TABLE_ATTRIBUTE_SET . "'") != TABLE_ATTRIBUTE_SET) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE IF NOT EXISTS " . TABLE_ATTRIBUTE_SET . " (
						attribute_set_id smallint(5) unsigned NOT NULL auto_increment,
						entity_type_id smallint(5) unsigned NOT NULL default '0',
						attribute_set_name varchar(255) character set utf8 collate utf8_swedish_ci NOT NULL default '',
						sort_order smallint(6) NOT NULL default '0',
						PRIMARY KEY  (attribute_set_id),
						UNIQUE KEY entity_type_id (entity_type_id,attribute_set_name),
						KEY entity_type_id_2 (entity_type_id,sort_order)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
				INSERT INTO " . TABLE_ENTITY . " (attribute_set_id, entity_type_id, attribute_set_name, sort_order) VALUES(1, 1, 'evariskUserDefault', 1);";
				// Execution de la requete
				dbDelta($sql);
			}

			/*	We check that table exists, if it's not the case we create it	*/
			if( $wpdb->get_var("show tables like '" . TABLE_ATTRIBUTE_OPTION_VALUE . "'") != TABLE_ATTRIBUTE_OPTION_VALUE) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE IF NOT EXISTS " . TABLE_ATTRIBUTE_OPTION_VALUE . " (
						value_id int(10) unsigned NOT NULL auto_increment,
						option_id int(10) unsigned NOT NULL default '0',
						value varchar(255) collate utf8_unicode_ci NOT NULL default '',
						PRIMARY KEY  (value_id),
						KEY FK_ATTRIBUTE_OPTION_VALUE_OPTION (option_id)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Attribute option values';";
				// Execution de la requete
				dbDelta($sql);
			}

			/*	We check that table exists, if it's not the case we create it	*/
			if( $wpdb->get_var("show tables like '" . TABLE_ATTRIBUTE_OPTION . "'") != TABLE_ATTRIBUTE_OPTION) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE IF NOT EXISTS " . TABLE_ATTRIBUTE_OPTION . " (
						option_id int(10) unsigned NOT NULL auto_increment,
						attribute_id smallint(5) unsigned NOT NULL default '0',
						sort_order smallint(5) unsigned NOT NULL default '0',
						PRIMARY KEY  (option_id),
						KEY FK_ATTRIBUTE_OPTION_ATTRIBUTE (attribute_id)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Attributes option (for source model)';";
				// Execution de la requete
				dbDelta($sql);
			}

			/*	We check that table exists, if it's not the case we create it	*/
			if( $wpdb->get_var("show tables like '" . TABLE_ATTRIBUTE_GROUP . "'") != TABLE_ATTRIBUTE_GROUP) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE IF NOT EXISTS " . TABLE_ATTRIBUTE_GROUP . " (
						attribute_group_id smallint(5) unsigned NOT NULL auto_increment,
						attribute_set_id smallint(5) unsigned NOT NULL default '0',
						attribute_group_name varchar(255) collate utf8_unicode_ci NOT NULL default '',
						sort_order smallint(6) NOT NULL default '0',
						default_id smallint(5) unsigned default '0',
						PRIMARY KEY  (attribute_group_id),
						UNIQUE KEY attribute_set_id (attribute_set_id,attribute_group_name),
						KEY attribute_set_id_2 (attribute_set_id,sort_order)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
					INSERT INTO " . TABLE_ATTRIBUTE_GROUP . " (attribute_group_id, attribute_set_id, attribute_group_name, sort_order, default_id) VALUES(1, 1, 'user_profile', 1, 1);";
				// Execution de la requete
				dbDelta($sql);
			}

			/*	We check that table exists, if it's not the case we create it	*/
			if( $wpdb->get_var("show tables like '" . TABLE_ATTRIBUTE . "'") != TABLE_ATTRIBUTE) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE IF NOT EXISTS " . TABLE_ATTRIBUTE . " (
						attribute_id smallint(5) unsigned NOT NULL auto_increment,
						entity_type_id smallint(5) unsigned NOT NULL default '0',
						attribute_status enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
						attribute_code varchar(255) collate utf8_unicode_ci NOT NULL default '',
						attribute_model varchar(255) collate utf8_unicode_ci default NULL,
						backend_model varchar(255) collate utf8_unicode_ci default NULL,
						backend_type enum('static','datetime','decimal','int','text','varchar') collate utf8_unicode_ci NOT NULL default 'static',
						backend_table varchar(255) collate utf8_unicode_ci default NULL,
						frontend_model varchar(255) collate utf8_unicode_ci default NULL,
						frontend_input varchar(50) collate utf8_unicode_ci default NULL,
						frontend_label varchar(255) collate utf8_unicode_ci default NULL,
						frontend_class varchar(255) collate utf8_unicode_ci default NULL,
						source_model varchar(255) collate utf8_unicode_ci default NULL,
						is_global tinyint(1) unsigned NOT NULL default '1',
						is_visible tinyint(1) unsigned NOT NULL default '1',
						is_required tinyint(1) unsigned NOT NULL default '0',
						is_user_defined tinyint(1) unsigned NOT NULL default '0',
						default_value text collate utf8_unicode_ci,
						is_searchable tinyint(1) unsigned NOT NULL default '0',
						is_filterable tinyint(1) unsigned NOT NULL default '0',
						is_comparable tinyint(1) unsigned NOT NULL default '0',
						is_visible_on_front tinyint(1) unsigned NOT NULL default '0',
						is_html_allowed_on_front tinyint(1) unsigned NOT NULL default '0',
						is_unique tinyint(1) unsigned NOT NULL default '0',
						is_filterable_in_search tinyint(1) unsigned NOT NULL default '0',
						used_for_sort_by tinyint(1) unsigned NOT NULL default '0',
						is_configurable tinyint(1) unsigned NOT NULL default '1',
						apply_to varchar(255) collate utf8_unicode_ci NOT NULL,
						position int(11) NOT NULL,
						note varchar(255) collate utf8_unicode_ci NOT NULL,
						is_visible_in_advanced_search tinyint(1) unsigned NOT NULL default '0',
						PRIMARY KEY  (attribute_id),
						UNIQUE KEY entity_type_id (entity_type_id,attribute_code),
						KEY IDX_USED_FOR_SORT_BY (entity_type_id,used_for_sort_by),
						KEY IDX_USED_IN_PRODUCT_LISTING (entity_type_id)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				dbDelta($sql);
			}
		}
		{//	Eav users tables
			/*	We check that table exists, if it's not the case we create it	*/
			if( $wpdb->get_var("show tables like '" . TABLE_EAV_USER_DATETIME . "'") != TABLE_EAV_USER_DATETIME) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE IF NOT EXISTS " . TABLE_EAV_USER_DATETIME . " (
						value_id int(11) NOT NULL auto_increment,
						entity_type_id smallint(8) unsigned NOT NULL default '0',
						attribute_id smallint(5) unsigned NOT NULL default '0',
						entity_id int(10) unsigned NOT NULL default '0',
						value datetime NOT NULL default '0000-00-00 00:00:00',
						PRIMARY KEY  (value_id),
						UNIQUE KEY IDX_ATTRIBUTE_VALUE (entity_id,attribute_id),
						KEY FK_CUSTOMER_DATETIME_ENTITY_TYPE (entity_type_id),
						KEY FK_CUSTOMER_DATETIME_ATTRIBUTE (attribute_id),
						KEY FK_CUSTOMER_DATETIME_ENTITY (entity_id),
						KEY IDX_VALUE (entity_id,attribute_id,value)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				dbDelta($sql);
			}

			/*	We check that table exists, if it's not the case we create it	*/
			if( $wpdb->get_var("show tables like '" . TABLE_EAV_USER_DECIMAL . "'") != TABLE_EAV_USER_DECIMAL) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE IF NOT EXISTS " . TABLE_EAV_USER_DECIMAL . " (
						value_id int(11) NOT NULL auto_increment,
						entity_type_id smallint(8) unsigned NOT NULL default '0',
						attribute_id smallint(5) unsigned NOT NULL default '0',
						entity_id int(10) unsigned NOT NULL default '0',
						value decimal(12,4) NOT NULL default '0.0000',
						PRIMARY KEY  (value_id),
						UNIQUE KEY IDX_ATTRIBUTE_VALUE (entity_id,attribute_id),
						KEY FK_CUSTOMER_DECIMAL_ENTITY_TYPE (entity_type_id),
						KEY FK_CUSTOMER_DECIMAL_ATTRIBUTE (attribute_id),
						KEY FK_CUSTOMER_DECIMAL_ENTITY (entity_id),
						KEY IDX_VALUE (entity_id,attribute_id,value)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				dbDelta($sql);
			}

			/*	We check that table exists, if it's not the case we create it	*/
			if( $wpdb->get_var("show tables like '" . TABLE_EAV_USER_INT . "'") != TABLE_EAV_USER_INT) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE IF NOT EXISTS " . TABLE_EAV_USER_INT . " (
						value_id int(11) NOT NULL auto_increment,
						entity_type_id smallint(8) unsigned NOT NULL default '0',
						attribute_id smallint(5) unsigned NOT NULL default '0',
						entity_id int(10) unsigned NOT NULL default '0',
						value int(11) NOT NULL default '0',
						PRIMARY KEY  (value_id),
						UNIQUE KEY IDX_ATTRIBUTE_VALUE (entity_id,attribute_id),
						KEY FK_CUSTOMER_INT_ENTITY_TYPE (entity_type_id),
						KEY FK_CUSTOMER_INT_ATTRIBUTE (attribute_id),
						KEY FK_CUSTOMER_INT_ENTITY (entity_id),
						KEY IDX_VALUE (entity_id,attribute_id,value)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				dbDelta($sql);
			}

			/*	We check that table exists, if it's not the case we create it	*/
			if( $wpdb->get_var("show tables like '" . TABLE_EAV_USER_TEXT . "'") != TABLE_EAV_USER_TEXT) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE IF NOT EXISTS " . TABLE_EAV_USER_TEXT . " (
						value_id int(11) NOT NULL auto_increment,
						entity_type_id smallint(8) unsigned NOT NULL default '0',
						attribute_id smallint(5) unsigned NOT NULL default '0',
						entity_id int(10) unsigned NOT NULL default '0',
						value text collate utf8_unicode_ci NOT NULL,
						PRIMARY KEY  (value_id),
						UNIQUE KEY IDX_ATTRIBUTE_VALUE (entity_id,attribute_id),
						KEY FK_CUSTOMER_TEXT_ENTITY_TYPE (entity_type_id),
						KEY FK_CUSTOMER_TEXT_ATTRIBUTE (attribute_id),
						KEY FK_CUSTOMER_TEXT_ENTITY (entity_id)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				dbDelta($sql);
			}

			/*	We check that table exists, if it's not the case we create it	*/
			if( $wpdb->get_var("show tables like '" . TABLE_EAV_USER_VARCHAR . "'") != TABLE_EAV_USER_VARCHAR) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE IF NOT EXISTS " . TABLE_EAV_USER_VARCHAR . " (
						value_id int(11) NOT NULL auto_increment,
						entity_type_id smallint(8) unsigned NOT NULL default '0',
						attribute_id smallint(5) unsigned NOT NULL default '0',
						entity_id int(10) unsigned NOT NULL default '0',
						value varchar(255) collate utf8_unicode_ci NOT NULL default '',
						PRIMARY KEY  (value_id),
						UNIQUE KEY IDX_ATTRIBUTE_VALUE (entity_id,attribute_id),
						KEY FK_CUSTOMER_VARCHAR_ENTITY_TYPE (entity_type_id),
						KEY FK_CUSTOMER_VARCHAR_ATTRIBUTE (attribute_id),
						KEY FK_CUSTOMER_VARCHAR_ENTITY (entity_id),
						KEY IDX_VALUE (entity_id,attribute_id,value)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				dbDelta($sql);
			}
		}
		{//	Users groups tables
			/*	We check that table exists, if it's not the case we create it	*/
			if( $wpdb->get_var("show tables like '" . TABLE_EVA_USER_GROUP . "'") != TABLE_EVA_USER_GROUP) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE IF NOT EXISTS " . TABLE_EVA_USER_GROUP . " (
						user_group_id smallint(5) unsigned NOT NULL auto_increment,
						user_group_status enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
						user_group_name varchar(255) collate utf8_unicode_ci NOT NULL,
						user_group_description text collate utf8_unicode_ci NOT NULL,
						PRIMARY KEY  (user_group_id),
						KEY user_group_status (user_group_status)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				dbDelta($sql);
			}

			/*	We check that table exists, if it's not the case we create it	*/
			if( $wpdb->get_var("show tables like '" . TABLE_EVA_USER_GROUP_DETAILS . "'") != TABLE_EVA_USER_GROUP_DETAILS) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE IF NOT EXISTS " . TABLE_EVA_USER_GROUP_DETAILS . " (
						user_group_id smallint(5) unsigned NOT NULL,
						user_id bigint(20) unsigned NOT NULL,
						PRIMARY KEY  (user_group_id,user_id)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				dbDelta($sql);
			}
		}
		{//	Users roles tables
			/*	We check that table exists, if it's not the case we create it	*/
			if( $wpdb->get_var("show tables like '" . TABLE_EVA_USER_GROUP_ROLES_DETAILS . "'") != TABLE_EVA_USER_GROUP_ROLES_DETAILS) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE IF NOT EXISTS " . TABLE_EVA_USER_GROUP_ROLES_DETAILS . " (
						user_group_id int(11) unsigned NOT NULL,
						eva_role_id int(11) unsigned NOT NULL,
						PRIMARY KEY  (user_group_id,eva_role_id)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Liaison entre groupes d''utilisateurs et roles (profil)';";
				// Execution de la requete
				dbDelta($sql);
			}

			/*	We check that table exists, if it's not the case we create it	*/
			if( $wpdb->get_var("show tables like '" . TABLE_EVA_ROLES . "'") != TABLE_EVA_ROLES) {
				// On construit la requete SQL de création de table
				$sql = 
					"CREATE TABLE IF NOT EXISTS " . TABLE_EVA_ROLES . " (
						eva_role_id int(11) unsigned NOT NULL auto_increment,
						eva_role_status enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
						eva_role_label varchar(255) collate utf8_unicode_ci NOT NULL,
						eva_role_name varchar(255) collate utf8_unicode_ci NOT NULL,
						eva_role_description text collate utf8_unicode_ci NOT NULL,
						eva_role_capabilities text collate utf8_unicode_ci NOT NULL,
						PRIMARY KEY  (eva_role_id),
						KEY eva_role_status (eva_role_status),
						KEY eva_role_label (eva_role_label)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Définition des roles pour evarisk';";
				// Execution de la requete
				dbDelta($sql);
			}
		}
	}
	else
	{//Version 1 : cas particulier -- formulaire inscription
		if(EvaVersion::getVersion('base_evarisk') <= 2)
		{//Update de la table des catégorie de dangers 																										/!\	PLUS UTILISE/!\
			// $sql = 'ALTER TABLE ' . TABLE_CATEGORIE_DANGER . ' ADD photo varchar( 255 ) NULL  DEFAULT NULL COMMENT "Image to display in risk assessment" AFTER description';
			// $wpdb->query($sql);
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 3)
		{//Update de la table des textes règlementaires
			$sql = 'ALTER TABLE ' . TABLE_TEXTE_REFERENCIEL . ' ADD affectable BOOLEAN NOT NULL DEFAULT 0 COMMENT "Is the text assignable" AFTER adresseTexte';
			$wpdb->query($sql);
			$sql = 'ALTER TABLE ' . TABLE_TEXTE_REFERENCIEL . ' CHANGE rubrique rubrique VARCHAR( 255 ) NOT NULL COMMENT "The text name"';  
			$wpdb->query($sql);
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 4)
		{//Update de la table des catégorie de dangers (changement de la colonne photo en mainPhoto) 			/!\	PLUS UTILISE /!\
			// $sql = 'ALTER TABLE ' . TABLE_CATEGORIE_DANGER . ' CHANGE photo mainPhoto varchar( 255 ) NULL  DEFAULT NULL COMMENT "Image to display in risk assessment" AFTER description';
			// $wpdb->query($sql);
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 5)
		{//Ajout de la table des tâches
			$sqlCreationTable = 'CREATE TABLE ' . TABLE_TACHE . ' (
					id INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "Task Identifier",
					nom VARCHAR ( 255 ) NOT NULL COMMENT "Task name",
					limiteGauche INT ( 10 ) NOT NULL COMMENT "Left limit to simulate the tree",
					limiteDroite INT ( 10 ) NOT NULL COMMENT "Right limit to simulate the tree",
					description TEXT NULL COMMENT "Task description",
					dateDebut DATE NULL DEFAULT NULL COMMENT "Task start date",
					dateFIN DATE NULL DEFAULT NULL COMMENT "Task finish date",
					avancement INT ( 10 ) NULL DEFAULT NULL COMMENT "Task progression",
					lieu VARCHAR ( 255 ) NULL DEFAULT NULL COMMENT "Task place",
					tableProvenance VARCHAR( 255 ) NULL DEFAULT NULL COMMENT "Table of the element that induces the task",
					idProvenance INT ( 10 ) NULL DEFAULT NULL COMMENT "Identifier of the element that induces the task",
					Status ENUM( \'Valid\', \'Moderated\', \'Deleted\', \'Aborded\' ) NOT NULL DEFAULT \'Valid\' COMMENT "Task status"
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT = "Table containing the task (for corrective actions)";';
			$wpdb->query($sqlCreationTable);
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 6)
		{//Ajout de la table des activités
			$sqlCreationTable = 'CREATE TABLE ' . TABLE_ACTIVITE . ' (
					id INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "Activity Identifier",
					id_tache INT( 10 ) NOT NULL COMMENT "Task which the activity depends on",
					nom VARCHAR ( 255 ) NOT NULL COMMENT "Activity name",
					description TEXT NULL COMMENT "Activity description",
					dateDebut DATE NULL DEFAULT NULL COMMENT "Activity start date",
					dateFin DATE NULL DEFAULT NULL COMMENT "Activity finish date",
					avancement INT ( 10 ) NULL DEFAULT NULL COMMENT "Activity progression",
					lieu VARCHAR ( 255 ) NULL DEFAULT NULL COMMENT "Activity place",
					Status ENUM( \'Valid\', \'Moderated\', \'Deleted\', \'Aborded\' ) NOT NULL DEFAULT \'Valid\' COMMENT "Activity status"
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT = "Table containing the activity (for corrective actions)";';
			$wpdb->query($sqlCreationTable);
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 7)
		{//Update de la table des tâches (changement de la colonne dateFIN en dateFin)
			$sql = 'ALTER TABLE ' . TABLE_TACHE . ' CHANGE dateFIN dateFin DATE NULL DEFAULT NULL COMMENT "Task finish date"';
			$wpdb->query($sql);
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 8)
		{//Update de la table des tâches et activite(ajout des colonnes fisrtInsert et cout)
			$sql = 'ALTER TABLE ' . TABLE_TACHE . ' ADD firstInsert DATETIME NULL DEFAULT NULL COMMENT "Task creation date"';
			$wpdb->query($sql);
			$sql = 'ALTER TABLE ' . TABLE_TACHE . ' ADD cout FLOAT NULL DEFAULT NULL COMMENT "Task realisation cost" AFTER avancement';
			$wpdb->query($sql);
			
			$sql = 'ALTER TABLE ' . TABLE_ACTIVITE . ' ADD firstInsert DATETIME NULL DEFAULT NULL COMMENT "Activity creation date"';
			$wpdb->query($sql);
			$sql = 'ALTER TABLE ' . TABLE_ACTIVITE . ' ADD cout FLOAT NULL DEFAULT NULL COMMENT "Activity realisation cost" AFTER avancement';
			$wpdb->query($sql);
			
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 9)
		{//Ajout de la table des documents uniques
			$sqlCreationTable = 'CREATE TABLE ' . TABLE_DUER . ' (
					id int(10) NOT NULL AUTO_INCREMENT COMMENT "Single document identifier",
					element char(255) collate utf8_unicode_ci NOT NULL COMMENT "The element type the single document will be affected to",
					elementId int(10) unsigned NOT NULL COMMENT "the id of the element associated with the single document",
					referenceDUER varchar(64) NOT NULL default \'\' COMMENT "Single document reference",
					dateGenerationDUER datetime default NULL COMMENT "Single document creation date",
					nomDUER varchar(128) default NULL COMMENT "Document name",
					dateDebutAudit date default NULL COMMENT "Audit start date",
					dateFinAudit date default NULL COMMENT "Audit end date",
					nomSociete varchar(128) default NULL COMMENT "Society name",
					telephoneFixe int(10) default NULL COMMENT "Society phone number",
					telephonePortable int(10) default NULL COMMENT "Society cellular phone number",
					telephoneFax int(10) default NULL COMMENT "Society fax number",
					emetteurDUER varchar(128) default NULL COMMENT "Transmitter of the single document",
					destinataireDUER varchar(128) default NULL COMMENT "Recipient of the single document",
					revisionDUER int(3) default NULL COMMENT "Single document version",
					planDUER varchar(128) default NULL COMMENT "The single document scheme",
					codeHtmlGroupesUtilisateurs text COMMENT "Html code containing the different users\' group concerned by the single document",
					codeHtmlRisqueUnitaire text COMMENT "Html code containing the different risks evaluated in this single document",
					codeHtmlRisquesParUnite text COMMENT "Html code containing the risks\' summary for each work unit",
					methodologieDUER text COMMENT "Methodology used to create the single document",
					sourcesDUER text COMMENT "The different document used to create the single document",
					alerteDUER text COMMENT "Warning about the single document",
					conclusionDUER text COMMENT "conclusion about the sthe single document",
					PRIMARY KEY  (id),
					UNIQUE  (referenceDUER)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT = "Table containing the different single document";';
			$wpdb->query($sqlCreationTable);
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 10)
		{//Ajout de la table de liaison des groupes d'utilisateurs
			$sqlCreationTable = 'CREATE TABLE ' . TABLE_LIAISON_USER_GROUPS . ' (
					id_group INT( 10 ) NOT NULL COMMENT "Group Identifier",
					table_element VARCHAR ( 255 ) NOT NULL COMMENT "Element data base table",
					id_element INT( 10 ) NOT NULL COMMENT "Element identifier in the table",
					date DATETIME NOT NULL COMMENT "Date of the record",
					Status ENUM( \'Valid\', \'Moderated\', \'Deleted\' ) NOT NULL DEFAULT \'Valid\' COMMENT "Bind status"
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT = "Table containing the bind between users group and others tables";';
			$wpdb->query($sqlCreationTable);
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 11)
		{//Ajout des photos par défaut pour les catégories de danger																			/!\	PLUS UTILISE/!\
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 12)
		{//Ajout de la table d'identification des utilisateurs evalues
			$sql = 
				"CREATE TABLE IF NOT EXISTS " . TABLE_LIAISON_USER_EVALUATION . " (
				id_user int(10) unsigned NOT NULL COMMENT 'user identifier',
				table_element varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Element database table',
				id_element int(10) unsigned NOT NULL COMMENT 'Element identifier in the previous table',
				date datetime NOT NULL COMMENT 'date of the record',
				status enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid' COMMENT 'bind status',
					KEY id_user (id_user,id_element,status)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Table containing the bind between users and elements';";
			$wpdb->query($sql);
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 13)
		{//Mise à jour de l'index de la table de liaison entre les utilisateurs et l'évaluation
			$sql = "ALTER TABLE " . TABLE_LIAISON_USER_EVALUATION . " DROP INDEX id_user";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_LIAISON_USER_EVALUATION . " ADD PRIMARY KEY id_user ( id_user , id_element , table_element ) ";
			$wpdb->query($sql);
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 14)
		{//Ajout d'une clé primaire pour la table de liaison entre les groupes d'utilisateurs et les groupements/unite de travail
			$sql = "ALTER TABLE " . TABLE_LIAISON_USER_GROUPS . " ADD PRIMARY KEY ( id_group , table_element , id_element )  ";
			$wpdb->query($sql);
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 15)
		{//Changement de la structure de la table photo, déplacement et renommage du champs status, ajout d'un champs qui définit si c'est la photo principale ou non
			$sql = "ALTER TABLE " . TABLE_PHOTO . " DROP Status";
			$wpdb->query($sql);

			$sql = "ALTER TABLE " . TABLE_PHOTO . " ADD status ENUM('valid','moderated','deleted') NOT NULL DEFAULT 'valid' AFTER id, ADD isMainPicture ENUM('yes','no') NOT NULL DEFAULT 'no' AFTER status;";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_PHOTO . " ADD INDEX ( status )";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_PHOTO . " ADD INDEX ( isMainPicture )";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_PHOTO . " DROP INDEX idDestination";
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 16)
		{//Ajout de la table pour les groupes d'evaluateurs
			$sql = 
				"CREATE TABLE IF NOT EXISTS " . TABLE_EVA_EVALUATOR_GROUP . " (
					evaluator_group_id smallint(5) unsigned NOT NULL auto_increment,
					evaluator_group_status enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
					evaluator_group_name varchar(255) collate utf8_unicode_ci NOT NULL,
					evaluator_group_description text collate utf8_unicode_ci NOT NULL,
					evaluator_group_creation_date datetime NOT NULL,
					evaluator_group_creation_user_id bigint(20) unsigned NOT NULL COMMENT 'The user identifier that create the evaluator group',
					evaluator_group_deletion_date datetime NOT NULL,
					evaluator_group_deletion_user_id bigint(20) unsigned NOT NULL COMMENT 'The user identifier that delete the evaluator group',
					PRIMARY KEY  (evaluator_group_id),
					KEY user_group_status (evaluator_group_status),
					KEY evaluator_group_creation_user_id (evaluator_group_creation_user_id),
					KEY evaluator_group_deletion_user_id (evaluator_group_deletion_user_id)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
			$wpdb->query($sql);

			$sql = 
				"CREATE TABLE IF NOT EXISTS " . TABLE_EVA_EVALUATOR_GROUP_DETAILS . " (
					id int(10) unsigned NOT NULL auto_increment,
					evaluator_group_id smallint(5) unsigned NOT NULL,
					user_id bigint(20) unsigned NOT NULL,
					Status enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
					dateEntree datetime NOT NULL,
					affectationUserId bigint(20) NOT NULL COMMENT 'The user identifier that affect a user to an evaluator group',
					dateSortie datetime NOT NULL,
					desaffectationUserId bigint(20) unsigned NOT NULL COMMENT 'The user identifier that unaffect a user to an evaluator group',
					PRIMARY KEY  (id,evaluator_group_id,user_id),
					KEY Status (Status),
					KEY evaluator_group_id (evaluator_group_id),
					KEY user_id (user_id),
					KEY affectationUserId (affectationUserId),
					KEY desaffectationUserId (desaffectationUserId)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
			$wpdb->query($sql);

			$sql = 
				"CREATE TABLE IF NOT EXISTS " . TABLE_EVA_EVALUATOR_GROUP_BIND . " (
					id int(10) unsigned NOT NULL auto_increment,
					id_group int(10) NOT NULL COMMENT 'Group Identifier',
					table_element varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Element data base table',
					id_element int(10) NOT NULL COMMENT 'Element identifier in the table',
					dateAffectation datetime NOT NULL COMMENT 'Affectation date for the evaluator group to a element',
					affectationUserId bigint(20) NOT NULL COMMENT 'The user identifier that affect a evaluator group to an element',
					dateDesaffectation datetime NOT NULL COMMENT 'Desaffectation date for the evaluator group to a element',
					desaffectationUserId bigint(20) NOT NULL COMMENT 'The user identifier that unaffect a evaluator group to an element',
					Status enum('Valid','Moderated','Deleted') collate utf8_unicode_ci NOT NULL default 'Valid' COMMENT 'Bind status',
					PRIMARY KEY  (id,id_group,table_element,id_element),
					KEY Status (Status),
					KEY affecationUserId (affectationUserId),
					KEY desaffectationUserId (desaffectationUserId)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Table containing the bind between evalutors group and others';";
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 17)
		{//Changement des champs contenant les groupes utilisateurs, la liste des risques par unité et de la liste des risques unitaires dans la table document unique
			$sql = " ALTER TABLE " . TABLE_DUER . " CHANGE codeHtmlGroupesUtilisateurs groupesUtilisateurs TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'A serialise array with the different users'' group'";
			$wpdb->query($sql);
			$sql = " ALTER TABLE " . TABLE_DUER . " CHANGE codeHtmlRisqueUnitaire risquesUnitaires TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'A serialise array with the different single risqs'";
			$wpdb->query($sql);
			$sql = " ALTER TABLE " . TABLE_DUER . " CHANGE codeHtmlRisquesParUnite risquesParUnite TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'A serialise array with the different risqs by unit'";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_DUER . " ADD groupesUtilisateursAffectes TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'A serialise array with the different users'' group affected to the current element' AFTER groupesUtilisateurs ;";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_DUER . " ADD status ENUM( 'valid', 'moderated', 'deleted' ) NOT NULL DEFAULT 'valid' COMMENT 'The document status' AFTER id ;";
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 18)
		{//Changement des noms des tables pour les différents éléments des actions correctives
			$sql = "RENAME TABLE " . TABLE_AC_TACHE . " TO " . TABLE_TACHE . " ;";
			$wpdb->query($sql);
			$sql = "RENAME TABLE " . TABLE_AC_ACTIVITE . " TO " . TABLE_ACTIVITE . " ;";
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 19)
		{//Rajout des champs d'affectation des taches et des actions à un responsable;
		/*	Renomme la table activite en table action	*/
			$sql = "RENAME TABLE " . TABLE_AC_ACTION . " TO " . TABLE_ACTIVITE . " ;";
			$wpdb->query($sql);

		/*	Ajout des champs definissant les responsables des differentes etapes de la realisation des tache */
			$sql = "ALTER TABLE " . TABLE_TACHE . " ADD idCreateur bigint(20) NOT NULL COMMENT 'The identifier of the user who create the task' AFTER idProvenance ;";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_TACHE . " ADD idResponsable bigint(20) NOT NULL COMMENT 'The identifier of the user who is in charge of the task' AFTER idCreateur ;";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_TACHE . " ADD idRealisateur bigint(20) NOT NULL COMMENT 'The identifier of the user who have to make the task' AFTER idResponsable ;";
			$wpdb->query($sql);

		/*	Ajout des champs definissant les responsables des differentes etapes de la realisation des activites */
			$sql = "ALTER TABLE " . TABLE_ACTIVITE . " ADD idCreateur bigint(20) NOT NULL COMMENT 'The identifier of the user who create the action' AFTER id_tache ;";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_ACTIVITE . " ADD idResponsable bigint(20) NOT NULL COMMENT 'The identifier of the user who is in charge of the action' AFTER idCreateur ;";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_ACTIVITE . " ADD idRealisateur bigint(20) NOT NULL COMMENT 'The identifier of the user who have to make the action' AFTER idResponsable ;";
			$wpdb->query($sql);

		/*	Renomme la table Avoir valeur en table risque evaluation	*/
			$sql = "RENAME TABLE " . TABLE_AVOIR_VALEUR_OLD . " TO " . TABLE_AVOIR_VALEUR . " ;";
			$wpdb->query($sql);
		/*	Ajout d'un champs pour identifier l'utilisateur qui a modifie l'évaluation du risque */
			$sql = "ALTER TABLE " . TABLE_AVOIR_VALEUR . " ADD idEvaluateur BIGINT( 20 ) NOT NULL COMMENT 'Allow to know who is the person who change the risk' AFTER valeur ;";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_AVOIR_VALEUR . " ADD INDEX ( idEvaluateur ) ;";
			$wpdb->query($sql);
		}
		if(EvaVersion::getVersion('base_evarisk') <= 20)
		{//Ajout de la table de liaison entre les taches et les historiques des risques
		/*	Ajout dela table de liaison entre une tache et un element du logiciel */
			$sql = 
				"CREATE TABLE " . TABLE_LIAISON_TACHE_ELEMENT . " (
					id INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
					status ENUM( 'valid', 'moderated', 'deleted' ) NOT NULL DEFAULT 'valid',
					date datetime NOT NULL,
					id_tache INT( 10 ) NOT NULL,
					id_element INT( 10 ) NOT NULL,
					table_element CHAR( 255 ) NOT NULL,
					KEY status (status),
					KEY id_tache (id_tache),
					KEY id_element (id_element),
					KEY table_element (table_element)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
			$wpdb->query($sql);

		/*	Modification des types de champs pour les options (Rajout des options createur et responsable par défaut) */
			$sql = 
				"ALTER TABLE " . TABLE_OPTION . " CHANGE nom nom CHAR( 128 ) NOT NULL , CHANGE valeur valeur CHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ";
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 21)
		{//Refonte de la notion des acteurs pour les actions correctives
		/*	Suppression des champs "realisateur" des tables des actions correctives	*/
			$sql = "ALTER TABLE " . TABLE_TACHE . " CHANGE idRealisateur idSoldeur INT(10) NOT NULL COMMENT 'The identifier of the user who close the task' ;";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_ACTIVITE . " CHANGE idRealisateur idSoldeur INT(10) NOT NULL COMMENT 'The identifier of the user who close the action' ;";
			$wpdb->query($sql);

		/*	Ajout de l'identifiant de la personne qui a soldé les taches supérieur	*/	
			$sql = "ALTER TABLE " . TABLE_TACHE . " ADD idSoldeurChef INT(10) NOT NULL COMMENT 'The identifier of the user who close the task by closing parent task' AFTER idSoldeur ;";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_TACHE . " ADD INDEX ( idSoldeurChef );";
			$wpdb->query($sql);
		/*	Ajout de l'identifiant de la personne qui a soldé les taches supérieur	*/	
			$sql = "ALTER TABLE " . TABLE_ACTIVITE . " ADD idSoldeurChef INT(10) NOT NULL COMMENT 'The identifier of the user who close the action by closing parent task' AFTER idSoldeur ;";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_ACTIVITE . " ADD INDEX ( idSoldeurChef );";
			$wpdb->query($sql);

		/*	Ajout du statut solde et solde par le niveau superieur */
			$sql = "ALTER TABLE " . TABLE_TACHE . " ADD ProgressionStatus ENUM( 'inProgress', 'Done', 'DoneByChief' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'inProgress' COMMENT 'Activity status'; ";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_ACTIVITE . " ADD ProgressionStatus ENUM( 'inProgress', 'Done', 'DoneByChief' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'inProgress' COMMENT 'Activity status'; ";
			$wpdb->query($sql);

		/*	Ajout de la date ou la tache ou l'action a ete soldée */
			$sql = "ALTER TABLE " . TABLE_TACHE . " ADD dateSolde datetime NOT NULL; ";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_ACTIVITE . " ADD dateSolde datetime NOT NULL; ";
			$wpdb->query($sql);

		/*	Ajout de la table de liaison entre un utilisateur et un element du logiciel */
			$sql = 
				"CREATE TABLE " . TABLE_LIAISON_USER_ELEMENT . " (
					id int(10) NOT NULL auto_increment,
					status enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
					date_affectation datetime NOT NULL,
					id_attributeur bigint(20) NOT NULL,
					date_desAffectation datetime NOT NULL,
					id_desAttributeur bigint(20) NOT NULL,
					id_user bigint(20) NOT NULL,
					id_element int(10) NOT NULL,
					table_element char(255) collate utf8_unicode_ci NOT NULL,
					PRIMARY KEY  (id),
					UNIQUE KEY uniqueKey (status,id_user,id_element,table_element),
					KEY status (status),
					KEY id_user (id_user),
					KEY id_element (id_element),
					KEY table_element (table_element)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
			$wpdb->query($sql);

		/*	Ajout de la table de suivi de la progression d'une action */
			$sql = 
				"CREATE TABLE " . TABLE_ACTIVITE_SUIVI . " (
					id int(10) unsigned NOT NULL auto_increment,
					status enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
					date datetime NOT NULL,
					id_user bigint(20) NOT NULL,
					id_element int(10) NOT NULL,
					table_element varchar(255) collate utf8_unicode_ci NOT NULL,
					commentaire varchar(255) collate utf8_unicode_ci NOT NULL,
					PRIMARY KEY  (id),
					KEY status (status),
					KEY id_user (id_user),
					KEY id_element (id_element)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Allows to follow an action progress';";
			$wpdb->query($sql);

			/*	Ajout de la notion de moment dans la liaison entre une action corrective et un élément*/
			$sql = "ALTER TABLE " . TABLE_LIAISON_TACHE_ELEMENT . " ADD  wasLinked ENUM(  'before',  'after' ) NOT NULL DEFAULT  'before' COMMENT  'Allows to know if the action was link to the element before or after it realisation' AFTER  status; ";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_LIAISON_TACHE_ELEMENT . " ADD  ADD INDEX (  wasLinked ); ";
			$wpdb->query($sql);

			/*	Changement des clés et index de la table d'évaluation des risques	*/
			$sql = "ALTER TABLE  " . TABLE_AVOIR_VALEUR . " DROP INDEX  id_risque_2 , ADD UNIQUE  unique_key_evaluation (  id_risque ,  id_variable ,  date );";
			$wpdb->query($sql);
			$sql = "ALTER TABLE  " . TABLE_AVOIR_VALEUR . " ADD id INT( 10 ) NOT NULL FIRST;";
			$wpdb->query($sql);
			$sql = "ALTER TABLE  " . TABLE_AVOIR_VALEUR . " ADD INDEX ( id );";
			$wpdb->query($sql);
			$sql = "ALTER TABLE  " . TABLE_AVOIR_VALEUR . " CHANGE  id  id INT( 10 ) NOT NULL AUTO_INCREMENT;";
			$wpdb->query($sql);
			$sql = "ALTER TABLE  " . TABLE_AVOIR_VALEUR . " ADD PRIMARY KEY ( id );";
			$wpdb->query($sql);
			$sql = "ALTER TABLE  " . TABLE_AVOIR_VALEUR . " DROP INDEX ( id );";
			$wpdb->query($sql);
			$sql = "ALTER TABLE  " . TABLE_AVOIR_VALEUR . " ADD id_evaluation INT( 10 ) NOT NULL AFTER  id;";
			$wpdb->query($sql);
			$sql = "ALTER TABLE  " . TABLE_AVOIR_VALEUR . " ADD INDEX ( id_evaluation );";
			$wpdb->query($sql);

			$sql = "ALTER TABLE  " . TABLE_LIAISON_TACHE_ELEMENT . " ADD UNIQUE uniqueKey ( wasLinked , id_tache , id_element , table_element ) ;";
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 22)
		{
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 23)
		{//Ajout de l'option actions correctives avancées + Ajout des champs photos avant et après dans la table des actions corrective

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 24)
		{//Ajout de l'option actions correctives avancées + Ajout des champs photos avant et après dans la table des actions corrective
			$sql = "ALTER TABLE  " . TABLE_ACTIVITE . " ADD idPhotoAvant INT( 10 ) UNSIGNED NOT NULL AFTER idSoldeurChef;";
			$wpdb->query($sql);
			$sql = "ALTER TABLE  " . TABLE_ACTIVITE . " ADD INDEX ( idPhotoAvant );";
			$wpdb->query($sql);

			$sql = "ALTER TABLE  " . TABLE_ACTIVITE . " ADD idPhotoApres INT( 10 ) UNSIGNED NOT NULL AFTER idPhotoAvant;";
			$wpdb->query($sql);
			$sql = "ALTER TABLE  " . TABLE_ACTIVITE . " ADD INDEX ( idPhotoApres );";
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 25)
		{//Insertion des utilisateurs affectés a une unité de travail ou un groupement dans la table de liaison commune

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 26)
		{//Mise en place de la gestion de différents modèles pour la génération du document unique
			/*	Add the model used for the DUER generation	*/
			$sql = "ALTER TABLE " . TABLE_DUER . " ADD id_model INT( 10 ) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'The model used to generate the DUER' AFTER elementId;";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_DUER . " ADD INDEX ( id_model );";
			$wpdb->query($sql);
			$sql = "UPDATE " . TABLE_DUER . " SET id_model = 1;";
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 27)
		{//Mise en place de la gestion de différents modèles pour la génération du document unique
			/*	Add the model used for the DUER generation	*/
			$sql = 
			"CREATE TABLE IF NOT EXISTS " . TABLE_GED_DOCUMENTS . " (
				id int(10) unsigned NOT NULL auto_increment,
				status enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
				dateCreation datetime NOT NULL,
				idCreateur int(10) unsigned NOT NULL,
				dateSuppression datetime NOT NULL,
				idSuppresseur int(10) unsigned NOT NULL,
				id_element int(10) unsigned NOT NULL,
				table_element char(128) collate utf8_unicode_ci NOT NULL,
				categorie varchar(255) collate utf8_unicode_ci NOT NULL,
				nom varchar(255) collate utf8_unicode_ci NOT NULL,
				chemin varchar(255) collate utf8_unicode_ci NOT NULL,
				PRIMARY KEY  (id),
				KEY status (status),
				KEY id_element (id_element),
				KEY table_element (table_element)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Document management';;";
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 28)
		{//Ajout de l'option Risque avancés

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 29)
		{//Deplacement des liaisons entre photos et élément dans une nouvelle table
			$sql = 
			"CREATE TABLE " . TABLE_PHOTO_LIAISON . " (
			 id int(10) unsigned NOT NULL auto_increment,
				status enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
				isMainPicture enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
				idPhoto int(10) unsigned NOT NULL,
				idElement int(10) unsigned NOT NULL,
				tableElement char(255) collate utf8_unicode_ci NOT NULL,
				PRIMARY KEY  (id),
				KEY isMainPicture (isMainPicture),
				KEY idPhoto (idPhoto),
				KEY idElement (idElement),
				KEY status (status),
				KEY tableElement (tableElement)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Contain the different link between picture and element';";
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 30)
		{//Renommage de la valeur du champ "table_element" pour la liaison entre les utilisateurs et une évaluation des risques

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 31)
		{//Ajout du champs permettant de définir une action comme prioritaire
			$sql = "ALTER TABLE " . TABLE_TACHE . " ADD hasPriority ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'no';";
			$wpdb->query($sql);

			$sql = "ALTER TABLE " . TABLE_TACHE . " ADD INDEX(hasPriority);";
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 32)
		{//Ajout des champs définissant le type d'une option ainsi que le nom qui sera affiché dans l'interface
			$sql = "ALTER TABLE " . TABLE_OPTION . " ADD typeOption ENUM( 'ouinon', 'numerique', 'text' ) NOT NULL DEFAULT 'ouinon';";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_OPTION . " ADD nomAffiche CHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER nom;";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_OPTION . " ADD domaine CHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER id;";
			$wpdb->query($sql);

			$sql = "ALTER TABLE " . TABLE_OPTION . " ADD INDEX(typeOption);";
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 33)
		{//Ajout du statut "non-commencé" dans les taches et actions
			$sql = "ALTER TABLE " . TABLE_TACHE . " CHANGE ProgressionStatus ProgressionStatus ENUM( 'notStarted', 'inProgress', 'Done', 'DoneByChief' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'notStarted' COMMENT 'Task status' ";
			$wpdb->query($sql);

			$sql = "ALTER TABLE " . TABLE_ACTIVITE . " CHANGE ProgressionStatus ProgressionStatus ENUM( 'notStarted', 'inProgress', 'Done', 'DoneByChief' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'notStarted' COMMENT 'Activity status' ";
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 34)
		{//Ajout de la table pour stocker l'historique des fiches postes générées
			$sql = "ALTER TABLE " . TABLE_DUER . " CHANGE planDUER planDUER LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'The single document scheme';";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_DUER . " CHANGE groupesUtilisateurs groupesUtilisateurs LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'A serialise array with the different users'' group'";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_DUER . " CHANGE groupesUtilisateursAffectes groupesUtilisateursAffectes LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'A serialise array with the different users'' group affected to the current element'";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_DUER . " CHANGE risquesUnitaires risquesUnitaires LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'A serialise array with the different single risqs'";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_DUER . " CHANGE risquesParUnite risquesParUnite LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'A serialise array with the different risqs by unit'";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_DUER . " CHANGE methodologieDUER methodologieDUER LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'Methodology used to create the single document'";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_DUER . " CHANGE sourcesDUER sourcesDUER LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'The different document used to create the single document'";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_DUER . " CHANGE alerteDUER alerteDUER LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'Warning about the single document'";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_DUER . " CHANGE conclusionDUER conclusionDUER LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'conclusion about the sthe single document'";
			$wpdb->query($sql);

			$sql = "CREATE TABLE " . TABLE_FP . " (
				id int(10) NOT NULL AUTO_INCREMENT,
				creation_date datetime default NULL,
				revision int(3) default NULL COMMENT 'Document version',
				id_element int(10) unsigned NOT NULL COMMENT 'The element''s id associated to the document',
				table_element char(255) collate utf8_unicode_ci NOT NULL COMMENT 'The element''s type associated to the document',
				reference varchar(64) NOT NULL default '',
				name varchar(128) default NULL,
				defaultPicturePath varchar(255) default NULL,
				societyName TEXT default NULL,
				users LONGTEXT COMMENT 'A serialised array containing the different users',
				userGroups LONGTEXT COMMENT 'A serialised array containing the different users group',
				evaluators LONGTEXT COMMENT 'A serialised array containing the different users who were present during evaluation',
				evaluatorsGroups LONGTEXT COMMENT 'A serialised array containing the different evaluators group',
				unitRisk LONGTEXT COMMENT 'A serialised array containing the different risks',
				PRIMARY KEY  (id),
				UNIQUE  (reference)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT = 'Table containing the different single document';";
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 35)
		{//Changements de gestion des stockages des documents générés ainsi que des fichiers envoyés

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
	}
}
?>