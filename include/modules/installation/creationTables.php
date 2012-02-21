<?php
/*
Installation de l'extension
	- Création des tables
	- Initialisation des permissions
*/

function evarisk_creationTables()
{// Création des tables lors de l'installation
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	global $wpdb;
	
	if(digirisk_options::getDbOption('base_evarisk') < 1)
	{
		// On vérifie si la table version n'existe pas
		//DELETE FROM VERSION 44

		// On ajoute la version de la base de données dans une option de wordpress
		add_option('digirisk_db_option', array('base_evarisk' => 0));
	
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
			//DELETE FROM VERSION 44
			
			// On vérifie si la table options n'existe pas
			//DELETE FROM VERSION 44
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
			//DELETE FROM VERSION 44
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
			//DELETE FROM VERSION 44
		}
		{//	Eav users tables
			//DELETE FROM VERSION 44
		}
		{//	Users groups tables
			//DELETE FROM VERSION 44
		}
		{//	Users roles tables
			//DELETE FROM VERSION 44
		}

		/*	Add the permission table	*/
		//DELETE FROM VERSION 60
	}
	else
	{//Version 1 : cas particulier -- formulaire inscription
		if(digirisk_options::getDbOption('base_evarisk') <= 2)
		{//Update de la table des catégorie de dangers 																										/!\	PLUS UTILISE/!\
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 3)
		{//Update de la table des textes règlementaires
			$sql = 'ALTER TABLE ' . TABLE_TEXTE_REFERENCIEL . ' ADD affectable BOOLEAN NOT NULL DEFAULT 0 COMMENT "Is the text assignable" AFTER adresseTexte';
			$wpdb->query($sql);
			$sql = 'ALTER TABLE ' . TABLE_TEXTE_REFERENCIEL . ' CHANGE rubrique rubrique VARCHAR( 255 ) NOT NULL COMMENT "The text name"';  
			$wpdb->query($sql);
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 4)
		{//Update de la table des catégorie de dangers (changement de la colonne photo en mainPhoto) 			/!\	PLUS UTILISE /!\
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 5)
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
		if(digirisk_options::getDbOption('base_evarisk') <= 6)
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
		if(digirisk_options::getDbOption('base_evarisk') <= 7)
		{//Update de la table des tâches (changement de la colonne dateFIN en dateFin)
			$sql = 'ALTER TABLE ' . TABLE_TACHE . ' CHANGE dateFIN dateFin DATE NULL DEFAULT NULL COMMENT "Task finish date"';
			$wpdb->query($sql);
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 8)
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
		if(digirisk_options::getDbOption('base_evarisk') <= 9)
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
		if(digirisk_options::getDbOption('base_evarisk') <= 10)
		{//Ajout de la table de liaison des groupes d'utilisateurs 																				/!\	PLUS UTILISE/!\
			//DELETE FROM VERSION 44

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 11)
		{//Ajout des photos par défaut pour les catégories de danger																			/!\	PLUS UTILISE/!\
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 12)
		{//Ajout de la table d'identification des utilisateurs evalues 																		/!\	PLUS UTILISE/!\
			//DELETE FROM VERSION 44

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 13)
		{//Mise à jour de l'index de la table de liaison entre les utilisateurs et l'évaluation 					/!\	PLUS UTILISE/!\
			//DELETE FROM VERSION 44

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 14)
		{//Ajout d'une clé primaire pour la table de liaison entre les groupes d'utilisateurs et les groupements/unite de travail 						/!\	PLUS UTILISE/!\
			//DELETE FROM VERSION 44

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 15)
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
		if(digirisk_options::getDbOption('base_evarisk') <= 16)
		{//Ajout de la table pour les groupes d'evaluateurs 																							/!\	PLUS UTILISE/!\
			//DELETE FROM VERSION 44

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 17)
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
		if(digirisk_options::getDbOption('base_evarisk') <= 18)
		{//Changement des noms des tables pour les différents éléments des actions correctives
			$sql = "RENAME TABLE " . TABLE_AC_TACHE . " TO " . TABLE_TACHE . " ;";
			$wpdb->query($sql);
			$sql = "RENAME TABLE " . TABLE_AC_ACTIVITE . " TO " . TABLE_ACTIVITE . " ;";
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 19)
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
		if(digirisk_options::getDbOption('base_evarisk') <= 20)
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
			//DELETE FROM VERSION 44

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 21)
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
		if(digirisk_options::getDbOption('base_evarisk') <= 22)
		{
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 23)
		{//Ajout de l'option actions correctives avancées + Ajout des champs photos avant et après dans la table des actions corrective

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 24)
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
		if(digirisk_options::getDbOption('base_evarisk') <= 25)
		{//Insertion des utilisateurs affectés a une unité de travail ou un groupement dans la table de liaison commune

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 26)
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
		if(digirisk_options::getDbOption('base_evarisk') <= 27)
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
		if(digirisk_options::getDbOption('base_evarisk') <= 28)
		{//Ajout de l'option Risque avancés

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 29)
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
		if(digirisk_options::getDbOption('base_evarisk') <= 30)
		{//Renommage de la valeur du champ "table_element" pour la liaison entre les utilisateurs et une évaluation des risques

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 31)
		{//Ajout du champs permettant de définir une action comme prioritaire
			$sql = "ALTER TABLE " . TABLE_TACHE . " ADD hasPriority ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'no';";
			$wpdb->query($sql);

			$sql = "ALTER TABLE " . TABLE_TACHE . " ADD INDEX(hasPriority);";
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 32)
		{//Ajout des champs définissant le type d'une option ainsi que le nom qui sera affiché dans l'interface 																	/!\	PLUS UTILISE/!\
			//DELETE FROM VERSION 44

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 33)
		{//Ajout du statut "non-commencé" dans les taches et actions
			$sql = "ALTER TABLE " . TABLE_TACHE . " CHANGE ProgressionStatus ProgressionStatus ENUM( 'notStarted', 'inProgress', 'Done', 'DoneByChief' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'notStarted' COMMENT 'Task status' ";
			$wpdb->query($sql);

			$sql = "ALTER TABLE " . TABLE_ACTIVITE . " CHANGE ProgressionStatus ProgressionStatus ENUM( 'notStarted', 'inProgress', 'Done', 'DoneByChief' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'notStarted' COMMENT 'Activity status' ";
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 34)
		{//Changement du type de champs pour stocker les différentes informations du DUER + Ajout de la table pour stocker l'historique des fiches postes générées
			$sql = "ALTER TABLE " . TABLE_DUER . " CHANGE planDUER planDUER LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'The single document scheme';";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_DUER . " CHANGE groupesUtilisateurs groupesUtilisateurs LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'A serialise array with the different users group'";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_DUER . " CHANGE groupesUtilisateursAffectes groupesUtilisateursAffectes LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'A serialise array with the different users group affected to the current element'";
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
		if(digirisk_options::getDbOption('base_evarisk') <= 35)
		{//Changements de gestion des stockages des documents générés ainsi que des fichiers envoyés

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 36)
		{//Ajout de l'option pour configurer la taille de la photo dans les fiches de poste

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 37)
		{//Deplacement de la table d'historique des documents unique générés avec le "groupe" de la GED + Ajout du champs contenant le model a utiliser pour les fiches de postes
			$sql = "RENAME TABLE " . TABLE_DUER_OLD . " TO " . TABLE_DUER . " ;";
			$wpdb->query($sql);

			$sql = "ALTER TABLE " . TABLE_GED_DOCUMENTS . " ADD parDefaut ENUM('oui','non') NOT NULL DEFAULT 'non' COMMENT 'Define if the document is the deault document for the category' AFTER status;";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_GED_DOCUMENTS . " ADD INDEX ( parDefaut );";
			$wpdb->query($sql);

			$sql = "ALTER TABLE " . TABLE_FP . " ADD id_model INT( 10 ) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'The model used to generate the document' AFTER creation_date;";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_FP . " ADD INDEX ( id_model );";
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 38)
		{//Ajout des preconisation
			$sql = "CREATE TABLE " . TABLE_CATEGORIE_PRECONISATION . " (
				id int(10) NOT NULL AUTO_INCREMENT,
				status enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
				creation_date datetime default NULL,
				nom varchar(128) default NULL,
				PRIMARY KEY (id),
				INDEX status (status)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT = 'Table containing the different recommendation categories';";
			$wpdb->query($sql);

			$sql = "CREATE TABLE " . TABLE_PRECONISATION . " (
				id int(10) NOT NULL AUTO_INCREMENT,
				status enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
				id_categorie_preconisation int(10) unsigned NOT NULL,
				creation_date datetime default NULL,
				nom varchar(128) default NULL,
				description TEXT default NULL,
				PRIMARY KEY (id),
				INDEX status (status),
				INDEX id_categorie_preconisation (id_categorie_preconisation)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT = 'Table containing the different recommendation';";
			$wpdb->query($sql);

			$sql = 
				"CREATE TABLE " . TABLE_LIAISON_PRECONISATION_ELEMENT . " (
					id int(10) NOT NULL auto_increment,
					status enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
					id_preconisation int(10) NOT NULL,
					efficacite int(3) NOT NULL,
					id_element int(10) NOT NULL,
					table_element char(255) collate utf8_unicode_ci NOT NULL,
					commentaire TEXT default NULL,
					PRIMARY KEY (id),
					KEY status (status),
					KEY id_preconisation (id_preconisation),
					KEY id_element (id_element),
					KEY table_element (table_element)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 39)
		{//Insertions des icones pour les preconisations et des preconisations par défaut existante
			/*	Change the name of the category from "recommandation" to "avertissement"	*/
			$sql = "UPDATE " . TABLE_CATEGORIE_PRECONISATION . " SET nom = 'avertissement' WHERE id = '3';";
			$wpdb->query($sql);

			$sql = "ALTER TABLE " . TABLE_CATEGORIE_PRECONISATION . " ADD impressionRecommandationCategorie ENUM( 'textandpicture', 'textonly', 'pictureonly' ) NOT NULL DEFAULT 'textandpicture' COMMENT 'Define the way to print the recommandation category into the different document' AFTER creation_date ,
ADD INDEX ( impressionRecommandationCategorie ) ;";
			$wpdb->query($sql);

			$sql = "ALTER TABLE " . TABLE_CATEGORIE_PRECONISATION . " ADD tailleimpressionRecommandationCategorie FLOAT( 3,2 ) NOT NULL DEFAULT '2' COMMENT 'Define the end size of the category picture into the different printed document' AFTER impressionRecommandationCategorie ;";
			$wpdb->query($sql);

			$sql = "ALTER TABLE " . TABLE_CATEGORIE_PRECONISATION . " ADD impressionRecommandation ENUM( 'textandpicture', 'textonly', 'pictureonly' ) NOT NULL DEFAULT 'textandpicture'  COMMENT 'Define the way to print the recommandation into the different document' AFTER tailleimpressionRecommandationCategorie ,
ADD INDEX ( impressionRecommandation ) ;";
			$wpdb->query($sql);

			$sql = "ALTER TABLE " . TABLE_CATEGORIE_PRECONISATION . " ADD tailleimpressionRecommandation FLOAT( 3,2 ) NOT NULL DEFAULT '0.8' COMMENT 'Define the end size of the recommandation picture into the different printed document' AFTER impressionRecommandation ;";
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 40)
		{//Ajout de l'option pour activer ou non le champs Efficacité dans les préconisations

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 41)
		{//Remplace l'ancienne version de la gestion des EPI par les préconisations

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 42)
		{//Change la longueur du champs recommandation des fiches de postes
			$sql = "ALTER TABLE " . TABLE_FP . " ADD recommandation LONGTEXT NULL ";
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 43)
		{//Modifie le type du champs planDUER
			$sql = "ALTER TABLE " . TABLE_DUER . " CHANGE planDUER planDUER TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'The single document scheme'";
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 44)
		{
			{/*	Changement du mode de gestion des groupes d'utilisateurs et des groupes d'évaluateurs	*/
				$sql = 
					"CREATE TABLE IF NOT EXISTS " . DIGI_DBT_USER_GROUP . " (
						id int(10) unsigned NOT NULL auto_increment,
						old_id int(10) unsigned NOT NULL,
						status enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
						group_type enum('employee','evaluator') collate utf8_unicode_ci NOT NULL default 'employee',
						last_update_date datetime NOT NULL,
						creation_date datetime NOT NULL,
						creation_user_id bigint(20) unsigned NOT NULL COMMENT 'The user identifier that create the group',
						deletion_date datetime NOT NULL,
						deletion_user_id bigint(20) unsigned NOT NULL COMMENT 'The user identifier that delete the group',
						name varchar(255) collate utf8_unicode_ci NOT NULL,
						description text collate utf8_unicode_ci NOT NULL,
						PRIMARY KEY  (id),
						KEY status (status),
						KEY creation_user_id (creation_user_id),
						KEY deletion_user_id (deletion_user_id)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				$wpdb->query($sql);

				/*	Ajout de la table de liaison entre un groupe d'utilisateur et un element du logiciel */
				$sql = 
					"CREATE TABLE " . DIGI_DBT_LIAISON_USER_GROUP . " (
						id int(10) NOT NULL auto_increment,
						status enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
						date_affectation datetime NOT NULL,
						id_attributeur bigint(20) NOT NULL,
						date_desAffectation datetime NOT NULL,
						id_desAttributeur bigint(20) NOT NULL,
						id_group int(10) NOT NULL,
						id_element int(10) NOT NULL,
						table_element char(255) collate utf8_unicode_ci NOT NULL,
						PRIMARY KEY (id),
						KEY status (status),
						KEY id_group (id_group),
						KEY id_element (id_element),
						KEY table_element (table_element)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				$wpdb->query($sql);
			}

			{/*	Transfert des tables des epis non utilisees vers la corbeille	*/
				$query = $wpdb->prepare("RENAME TABLE " . TABLE_UTILISE_EPI . " TO " . TRASH_DIGI_DBT_UTILISE_EPI);
				$wpdb->query($query);
				$query = $wpdb->prepare("RENAME TABLE " . TABLE_EPI . " TO " . TRASH_DIGI_DBT_EPI);
				$wpdb->query($query);
			}

			{/*	Transfert des tables des epis non utilisees vers la corbeille	*/
				$query = $wpdb->prepare("RENAME TABLE " . TABLE_LIAISON_USER_EVALUATION . " TO " . TRASH_DIGI_DBT_LIAISON_USER_EVALUATION);
				$wpdb->query($query);
			}

			{/*	Transfert des tables des valeurs des utilisateurs pour le modele eav non utilisees vers la corbeille	*/
				$query = $wpdb->prepare("RENAME TABLE " . TABLE_EAV_USER_DATETIME . " TO " . TRASH_DIGI_DBT_EAV_USER_DATETIME);
				$wpdb->query($query);
				$query = $wpdb->prepare("RENAME TABLE " . TABLE_EAV_USER_DECIMAL . " TO " . TRASH_DIGI_DBT_EAV_USER_DECIMAL);
				$wpdb->query($query);
				$query = $wpdb->prepare("RENAME TABLE " . TABLE_EAV_USER_INT . " TO " . TABLE_TABLE_EAV_USER_INT);
				$wpdb->query($query);
				$query = $wpdb->prepare("RENAME TABLE " . TABLE_EAV_USER_TEXT . " TO " . TRASH_DIGI_DBT_EAV_USER_TEXT);
				$wpdb->query($query);
				$query = $wpdb->prepare("RENAME TABLE " . TABLE_EAV_USER_VARCHAR . " TO " . TRASH_DIGI_DBT_EAV_USER_VARCHAR);
				$wpdb->query($query);
				$query = $wpdb->prepare("RENAME TABLE " . TABLE_EVA_ROLES . " TO " . TRASH_DIGI_DBT_EVA_ROLES);
				$wpdb->query($query);
				$query = $wpdb->prepare("RENAME TABLE " . TABLE_EVA_USER_GROUP_ROLES_DETAILS . " TO " . TRASH_DIGI_DBT_EVA_USER_GROUP_ROLES_DETAILS);
				$wpdb->query($query);
			}

			{/*	Transfert des tables du modele eav non utilisees vers la corbeille	*/
				$query = $wpdb->prepare("RENAME TABLE " . TABLE_ENTITY . " TO " . TRASH_DIGI_DBT_ENTITY);
				$wpdb->query($query);
				$query = $wpdb->prepare("RENAME TABLE " . TABLE_ENTITY_ATTRIBUTE_LINK . " TO " . TRASH_DIGI_DBT_ENTITY_ATTRIBUTE_LINK);
				$wpdb->query($query);
				$query = $wpdb->prepare("RENAME TABLE " . TABLE_ATTRIBUTE_SET . " TO " . TRASH_DIGI_DBT_ATTRIBUTE_SET);
				$wpdb->query($query);
				$query = $wpdb->prepare("RENAME TABLE " . TABLE_ATTRIBUTE . " TO " . TRASH_DIGI_DBT_ATTRIBUTE);
				$wpdb->query($query);
				$query = $wpdb->prepare("RENAME TABLE " . TABLE_ATTRIBUTE_GROUP . " TO " . TRASH_DIGI_DBT_ATTRIBUTE_GROUP);
				$wpdb->query($query);
				$query = $wpdb->prepare("RENAME TABLE " . TABLE_ATTRIBUTE_OPTION . " TO " . TRASH_DIGI_DBT_ATTRIBUTE_OPTION);
				$wpdb->query($query);
				$query = $wpdb->prepare("RENAME TABLE " . TABLE_ATTRIBUTE_OPTION_VALUE . " TO " . TRASH_DIGI_DBT_ATTRIBUTE_OPTION_VALUE);
				$wpdb->query($query);
			}

			{/*	Transfert de la table des personnes non utilisee vers la corbeille	*/
				$query = $wpdb->prepare("RENAME TABLE " . TABLE_PERSONNE . " TO " . TRASH_DIGI_DBT_PERSONNE);
				$wpdb->query($query);
			}

			if( $wpdb->get_var("show tables like '" . TABLE_VERSION . "'") == TABLE_VERSION )
			{/*	Deplacement de la gestion des version	*/
				add_option('digirisk_db_option', array('base_evarisk' => digirisk_options::getDbOption('base_evarisk')));

				/*	Transfert de la table de version non utilisee vers la corbeille	*/
				$query = $wpdb->prepare("RENAME TABLE " . TABLE_VERSION . " TO " . TRASH_DIGI_DBT_VERSION);
				$wpdb->query($query);
			}

			if( $wpdb->get_var("show tables like '" . TABLE_OPTION . "'") == TABLE_OPTION )
			{/*	Deplacement de la gestion des options	*/
				$optionToStore = array();

				/*	Récupération de la liste des options existantes pour le transfert	*/
				$query = $wpdb->prepare("
					SELECT * 
					FROM " . TABLE_OPTION);
				$optionsList = $wpdb->get_results($query);
				foreach($optionsList as $option)
				{
					$optionToStore[$option->nom] = $option->valeur;
				}
				/*	Ajout de l'entrée dans la table option avec toutes les valeurs des options	*/
				add_option('digirisk_options', $optionToStore);

				/*	Transfert de la table de version non utilisée vers la corbeille	*/
				$query = $wpdb->prepare("RENAME TABLE " . TABLE_OPTION . " TO " . TRASH_DIGI_DBT_OPTION);
				$wpdb->query($query);
			}
			else
			{/*	Add the option for the storage into wordpress option database table	*/
				$digiriskOptions = array();
				$digiriskOptions['responsable_Tache_Obligatoire'] = 'non';
				$digiriskOptions['responsable_Action_Obligatoire'] = 'non';
				$digiriskOptions['possibilite_Modifier_Tache_Soldee'] = 'non';
				$digiriskOptions['possibilite_Modifier_Action_Soldee'] = 'non';
				$digiriskOptions['avertir_Solde_Action_Non_100'] = 'oui';
				$digiriskOptions['avertir_Solde_Tache_Ayant_Action_Non_100'] = 'oui';
				$digiriskOptions['affecter_uniquement_tache_soldee_a_un_risque'] = 'oui';
				$digiriskOptions['action_correctives_avancees'] = 'non';
				$digiriskOptions['risques_avances'] = 'non';
				$digiriskOptions['export_only_priority_task'] = 'oui';
				$digiriskOptions['export_tasks'] = 'non';
				$digiriskOptions['taille_photo_poste_fiche_de_poste'] = '8';
				$digiriskOptions['recommandation_efficiency_activ'] = 'non';
				add_option('digirisk_options', $digiriskOptions);
			}

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 45)
		{
			{/*	Ajout des preconisations et actions correctives dans les documents unique	*/
				$sql = "ALTER TABLE " . TABLE_DUER . " ADD plan_d_action LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL";
				$wpdb->query($sql);
			}

			/*	On vérifie si la table produits n'existe pas */
			if( $wpdb->get_var("show tables like '" . DIGI_DBT_PRODUIT . "'") != DIGI_DBT_PRODUIT)
			{
				$query = 
					"CREATE TABLE " . DIGI_DBT_PRODUIT . " (
						id INT( 10 ) NOT NULL AUTO_INCREMENT,
						status enum('valid', 'moderated', 'deleted') collate utf8_unicode_ci NOT NULL default 'valid',
						creation_date datetime default NULL,
						last_update_date datetime default NULL,
						product_id int(10) unsigned,
						product_last_update_date datetime default NULL,
						category_id CHAR(255) NOT NULL,
						category_name CHAR(255) NOT NULL,
						product_name CHAR(255) NOT NULL,
						PRIMARY KEY (id),
						KEY status (status),
						KEY product_id (product_id)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				$wpdb->query($query);
			}
			/*	On vérifie si la table de liaison entre les produits et les éléments n'existe pas	*/
			if( $wpdb->get_var("show tables like '" . DIGI_DBT_LIAISON_PRODUIT_ELEMENT . "'") != DIGI_DBT_LIAISON_PRODUIT_ELEMENT)
			{
				$query = 
				"CREATE TABLE " . DIGI_DBT_LIAISON_PRODUIT_ELEMENT . " (
					id int(10) NOT NULL auto_increment,
					status enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
					date_affectation datetime NOT NULL,
					id_attributeur bigint(20) NOT NULL,
					date_desAffectation datetime NOT NULL,
					id_desAttributeur bigint(20) NOT NULL,
					id_product bigint(20) NOT NULL,
					id_element int(10) NOT NULL,
					table_element char(255) collate utf8_unicode_ci NOT NULL,
					PRIMARY KEY  (id),
					KEY status (status),
					KEY id_product (id_product),
					KEY id_element (id_element),
					KEY table_element (table_element)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				$wpdb->query($query);
			}

			/*	On supprime la clé unique de la table de liaison entre les utilisateurs et les éléments pour améliorer l'historisation	*/
			$query = "ALTER TABLE " . TABLE_LIAISON_USER_ELEMENT . " DROP INDEX uniqueKey";
			$wpdb->query($query);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 46)
		{
			//DELETE FROM VERSION 44
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 47)
		{
			/*	On vérifie si la table de liaison entre les produits et les éléments n'existe pas	*/
			if( $wpdb->get_var("show tables like '" . DIGI_DBT_PERMISSION_ROLE . "'") != DIGI_DBT_PERMISSION_ROLE)
			{
				$query = 
				"CREATE TABLE " . DIGI_DBT_PERMISSION_ROLE . " (
					id int(10) NOT NULL auto_increment,
					status enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
					creation_date datetime NOT NULL,
					creation_user_id bigint(20) NOT NULL,
					deletion_date datetime NOT NULL,
					deletion_user_id bigint(20) NOT NULL,
					last_update_date datetime NOT NULL,
					role_internal_name char(255) collate utf8_unicode_ci NOT NULL,
					role_name char(255) collate utf8_unicode_ci NOT NULL,
					PRIMARY KEY (id),
					KEY status (status)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				$wpdb->query($query);
			}

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 48)
		{

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 49)
		{

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 50)
		{
			$query = $wpdb->prepare("ALTER TABLE " . TABLE_GROUPEMENT . " DROP INDEX nom, ADD creation_date DATETIME NOT NULL , ADD lastupdate_date DATETIME NOT NULL ");
			$wpdb->query($query);;

			$query = $wpdb->prepare("ALTER TABLE " . TABLE_UNITE_TRAVAIL . " DROP INDEX nom, ADD creation_date DATETIME NOT NULL , ADD lastupdate_date DATETIME NOT NULL ");
			$wpdb->query($query);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 51)
		{
			$sql = "RENAME TABLE " . TABLE_FP_OLD . " TO " . TABLE_FP . " ;";
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 52)
		{
			$digirisk_tree_options['digi_tree_recreation_dialog'] = 'non';
			$digirisk_tree_options['digi_tree_recreation_default'] = 'recreate';
			add_option('digirisk_tree_options', $digirisk_tree_options, '', 'yes');

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 53)
		{
			$sql = $wpdb->prepare("ALTER TABLE " . TABLE_FP . " ADD description varchar(255) AFTER name, ADD adresse varchar(255) AFTER description, ADD telephone varchar(255) AFTER adresse;");
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 54)
		{
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 55)
		{
			$main_options = get_option('digirisk_options');
			$main_options['digi_activ_trash'] = 'non';
			update_option('digirisk_options', $main_options);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 56)
		{
			$query = $wpdb->prepare("ALTER TABLE " . TABLE_DUER . " CHANGE telephoneFixe telephoneFixe CHAR( 30 ) NULL DEFAULT NULL COMMENT 'Society phone number';");
			$wpdb->query($query);
			$query = $wpdb->prepare("ALTER TABLE " . TABLE_DUER . " CHANGE telephonePortable telephonePortable CHAR( 30 ) NULL DEFAULT NULL COMMENT 'Society cellular phone number';");
			$wpdb->query($query);
			$query = $wpdb->prepare("ALTER TABLE " . TABLE_DUER . " CHANGE telephoneFax telephoneFax CHAR( 30 ) NULL DEFAULT NULL COMMENT 'Society fax number';");
			$wpdb->query($query);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 57)
		{
			$product_options = get_option('digirisk_product_options');
			$product_options['digi_product_uncategorized_field'] = 'oui';
			update_option('digirisk_product_options', $product_options);

			$query = $wpdb->prepare("ALTER TABLE " . DIGI_DBT_PRODUIT . " ADD product_description longtext AFTER product_name;");
			$wpdb->query($query);
			$query = $wpdb->prepare("ALTER TABLE " . DIGI_DBT_PRODUIT . " ADD product_metadata longtext AFTER product_description;");
			$wpdb->query($query);

			/*	Add the table that will receive the product attachment definition		*/
			$query = 
				"CREATE TABLE " . DIGI_DBT_PRODUIT_ATTACHEMENT . " (
					id INT( 10 ) NOT NULL AUTO_INCREMENT,
					status enum('valid', 'moderated', 'deleted') collate utf8_unicode_ci NOT NULL default 'valid',
					creation_date datetime default NULL,
					last_update_date datetime default NULL,
					product_attachment_id int(10) unsigned,
					product_id int(10) unsigned,
					product_attachment_last_update_date datetime default NULL,
					product_attachment_name varchar(200) NOT NULL,
					product_attachment_title text NOT NULL,
					product_attachment_mime_type varchar(100) NOT NULL,
					product_attachment_metadata longtext NOT NULL,
					PRIMARY KEY (id),
					KEY status (status),
					KEY product_id (product_id),
					KEY product_attachment_id (product_attachment_id)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
			$wpdb->query($query);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 58)
		{
			/*	Create a table for accident	*/
			$query = 
				"CREATE TABLE IF NOT EXISTS " . DIGI_DBT_ACCIDENT . " (
					id INT( 10 ) NOT NULL AUTO_INCREMENT,
					status enum('valid', 'moderated', 'deleted') collate utf8_unicode_ci NOT NULL default 'valid',
					creation_date datetime default NULL,
					last_update_date datetime default NULL,
					id_element INT(10) UNSIGNED NOT NULL,
					table_element char(255) collate utf8_unicode_ci NOT NULL,
					accident_caused_by_third_party enum('oui', 'non') collate utf8_unicode_ci NOT NULL default 'non',
					accident_make_other_victim enum('oui', 'non') collate utf8_unicode_ci NOT NULL default 'non',
					police_report enum('oui', 'non') collate utf8_unicode_ci NOT NULL default 'non',
					declaration_state enum('in_progress', 'done') collate utf8_unicode_ci NOT NULL default 'in_progress',
					declaration_step INT(2) UNSIGNED NOT NULL,
					accident_date DATE default NULL,
					accident_hour TIME default NULL,
					accident_title char(255) collate utf8_unicode_ci NOT NULL,
					police_report_writer char(255) collate utf8_unicode_ci NOT NULL,
					PRIMARY KEY (id),
					KEY status (status),
					KEY declaration_state (declaration_state),
					KEY declaration_step (declaration_step),
					KEY id_element (id_element),
					KEY table_element (table_element)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
			$wpdb->query($query);

			/*	Create a table for accident victim	*/
			$query = 
				"CREATE TABLE IF NOT EXISTS " . DIGI_DBT_ACCIDENT_VICTIM . " (
					id INT( 10 ) NOT NULL AUTO_INCREMENT,
					status enum('valid', 'moderated', 'deleted') collate utf8_unicode_ci NOT NULL default 'valid',
					creation_date datetime default NULL,
					last_update_date datetime default NULL,
					id_accident INT( 10 ) UNSIGNED NOT NULL,
					id_user BIGINT( 20 ) UNSIGNED NOT NULL,
					victim_seniority date default NULL,
					victim_meta TEXT NOT NULL,
					PRIMARY KEY (id),
					KEY status (status),
					KEY id_accident (id_accident)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
			$wpdb->query($query);

			/*	Create a table for accident details	*/
			$query = 
				"CREATE TABLE IF NOT EXISTS " . DIGI_DBT_ACCIDENT_DETAILS . " (
					id INT( 10 ) NOT NULL AUTO_INCREMENT,
					status enum('valid', 'moderated', 'deleted') collate utf8_unicode_ci NOT NULL default 'valid',
					creation_date datetime default NULL,
					last_update_date datetime default NULL,
					accident_date DATE default NULL,
					accident_hour TIME default NULL,
					id_accident bigint(20) unsigned NOT NULL default '0',
					accident_victim_transported_at char(65) collate utf8_unicode_ci NOT NULL,
					accident_place char(80) collate utf8_unicode_ci NOT NULL,
					accident_consequence char(80) collate utf8_unicode_ci NOT NULL,
					accident_hurt_place char(255) collate utf8_unicode_ci NOT NULL,
					accident_hurt_nature char(255) collate utf8_unicode_ci NOT NULL,
					accident_victim_work_shedule char(255) collate utf8_unicode_ci NOT NULL,
					accident_details TEXT NOT NULL,
					accident_declaration TEXT NOT NULL,
					PRIMARY KEY (id),
					KEY status (status),
					KEY id_accident (id_accident)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
			$wpdb->query($query);

			/*	Create a table for accident third party	*/
			$query = 
				"CREATE TABLE IF NOT EXISTS " . DIGI_DBT_ACCIDENT_THIRD_PARTY . " (
					id INT(10) unsigned NOT NULL auto_increment,
					status enum('valid', 'moderated', 'deleted') collate utf8_unicode_ci NOT NULL default 'valid',
					creation_date datetime default NULL,
					last_update_date datetime default NULL,
					id_accident bigint(20) unsigned NOT NULL default '0',
					id_user BIGINT( 20 ) UNSIGNED NOT NULL,
					third_party_type enum('witness', 'third_party') collate utf8_unicode_ci NOT NULL default 'witness',
					firstname varchar(255) default NULL,
					lastname varchar(255) default NULL,
					insurance_corporation varchar(255) default NULL,
					adress_line_1 varchar(255) default NULL,
					adress_line_2 varchar(255) default NULL,
					PRIMARY KEY (id),
					KEY id_accident (id_accident),
					KEY id_user (id_user)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8  COLLATE=utf8_unicode_ci;";
			$wpdb->query($query);

			/*	Create a table for accident location	*/
			$query = 
				"CREATE TABLE IF NOT EXISTS " . DIGI_DBT_ACCIDENT_LOCATION . " (
					id INT(10) unsigned NOT NULL auto_increment,
					status enum('valid', 'moderated', 'deleted') collate utf8_unicode_ci NOT NULL default 'valid',
					creation_date datetime default NULL,
					last_update_date datetime default NULL,
					id_accident bigint(20) unsigned NOT NULL default '0',
					id_location INT( 20 ) UNSIGNED NOT NULL,
					location_type enum('employer', 'establishment') collate utf8_unicode_ci NOT NULL default 'employer',
					siret char(15) default NULL,
					siren char(15) default NULL,
					social_activity_number char(15) default NULL,
					adress_postal_code char(15) default NULL,
					adress_city char(26) default NULL,
					adress_line_1 char(32) default NULL,
					adress_line_2 char(32) default NULL,
					telephone char(21) default NULL,
					name varchar(255) default NULL,
					PRIMARY KEY (id),
					KEY id_accident (id_accident),
					KEY id_location (id_location)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8  COLLATE=utf8_unicode_ci;";
			$wpdb->query($query);

			/*	Add missing field to the groupment table	*/
			$sql = "ALTER TABLE " . TABLE_GROUPEMENT . " ADD typeGroupement ENUM('none', 'employer') NOT NULL DEFAULT 'none' AFTER id, ADD siren char(15) NOT NULL, ADD siret char(15) NOT NULL, ADD social_activity_number char(15) NOT NULL";
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 59)
		{
			/* Table : documentation */	
			$queryDoc = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix.digirisk_doc::prefix . '__documentation (
				doc_id int(11) unsigned NOT NULL AUTO_INCREMENT,
				doc_page_name varchar(255) NOT NULL,
				doc_url varchar(255) NOT NULL,
				doc_html text NOT NULL,
				doc_creation_date datetime NOT NULL,
				PRIMARY KEY ( doc_id )
			) ENGINE=MyISAM';
			$resultDoc = $wpdb->query($queryDoc);

			/* Mise à jour de la table documentation */
			$sql = 'ALTER TABLE ' . $wpdb->prefix.digirisk_doc::prefix . '__documentation ADD doc_active ENUM( "active", "deleted" ) default "active"';
			$wpdb->query($sql);
	
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 60)
		{//		Suppression de la table de gestion des droits
			$sql = "RENAME TABLE " . DIGI_DBT_PERMISSION . " TO " . TRASH_DIGI_DBT_PERMISSION . " ;";
			$wpdb->query($sql);
	
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 61)
		{//		Suppression de la table de gestion des droits
			$sql = "ALTER TABLE " . TABLE_LIAISON_TACHE_ELEMENT . " CHANGE wasLinked wasLinked ENUM( 'before', 'after', 'demand' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'before' COMMENT 'Allows to know if the action was link to the element before or after it realisation';";
			$wpdb->query($sql);
	
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 62)
		{//		Suppression de la table de gestion des droits
			$sql = "ALTER TABLE " . TABLE_TACHE . " ADD efficacite TINYINT(3) unsigned NOT NULL;";
			$wpdb->query($sql);

			$sql = "ALTER TABLE " . TABLE_TACHE . " ADD INDEX(efficacite);";
			$wpdb->query($sql);
	
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}		
		if(digirisk_options::getDbOption('base_evarisk') <= 63)
		{//		Suppression de la table de gestion des droits
			$sql = "ALTER TABLE  " . TABLE_TACHE . " ADD idPhotoAvant INT( 10 ) UNSIGNED NOT NULL AFTER cout;";
			$wpdb->query($sql);
			$sql = "ALTER TABLE  " . TABLE_TACHE . " ADD INDEX ( idPhotoAvant );";
			$wpdb->query($sql);

			$sql = "ALTER TABLE  " . TABLE_TACHE . " ADD idPhotoApres INT( 10 ) UNSIGNED NOT NULL AFTER idPhotoAvant;";
			$wpdb->query($sql);
			$sql = "ALTER TABLE  " . TABLE_TACHE . " ADD INDEX ( idPhotoApres );";
			$wpdb->query($sql);
	
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(digirisk_options::getDbOption('base_evarisk') <= 64)
		{//		Add an options for allowed extension in correctiv action element / Add table allowing to store messages send to user from correctiv action
			/*	Define the available notification by element */
			$t = DIGI_DBT_ELEMENT_NOTIFICATION;
			$query = 
"CREATE TABLE {$t} (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`status` enum('valid', 'moderated', 'deleted') NOT NULL DEFAULT 'valid',
	`creation_date` datetime NOT NULL,
	`last_update_date` datetime NOT NULL,
	`table_element` char(255) collate utf8_unicode_ci NOT NULL,
	`action` char(255) collate utf8_unicode_ci NOT NULL,
	`message_subject` char(255) CHARACTER SET utf8 NOT NULL,
	`message_to_send` text CHARACTER SET utf8 NOT NULL,
	PRIMARY KEY (`id`),
	KEY status (status),
	KEY table_element (table_element),
	KEY action (action)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$wpdb->query($query);

			/*	Ajout de la table de liaison entre un utilisateur et un element du logiciel */
			$t = DIGI_DBT_LIAISON_USER_NOTIFICATION_ELEMENT;
			$query = "
CREATE TABLE {$t} (
	id int(10) NOT NULL auto_increment,
	status enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
	date_affectation datetime NOT NULL,
	id_attributeur bigint(20) NOT NULL,
	date_desAffectation datetime NOT NULL,
	id_desAttributeur bigint(20) NOT NULL,
	id_user bigint(20) NOT NULL,
	id_notification int(10) NOT NULL,
	id_element int(10) NOT NULL,
	table_element char(255) collate utf8_unicode_ci NOT NULL,
	PRIMARY KEY  (id),
	KEY status (status),
	KEY id_user (id_user),
	KEY id_notification (id_notification),
	KEY id_element (id_element),
	KEY table_element (table_element)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
			$wpdb->query($query);

			/*	Messages send to user */
			$t = DIGI_DBT_MESSAGES;
			$query = 
"CREATE TABLE {$t} (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`status` enum('valid', 'moderated', 'deleted', 'archived') NOT NULL DEFAULT 'valid',
	`send_status` enum('sent','resent') NOT NULL DEFAULT 'sent',
	`creation_date` datetime NOT NULL,
	`last_dispatch_date` datetime NOT NULL,
	`user_id` bigint(20) unsigned NOT NULL,
	`id_notification` int(10) NOT NULL,
	`id_element` int(10) NOT NULL,
	`table_element` char(255) collate utf8_unicode_ci NOT NULL,
	`title` char(255) NOT NULL,
	`user_email` varchar(255) NOT NULL,
	`message` text CHARACTER SET utf8 NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$wpdb->query($query);

			/*	Message history of send message */
			$t = DIGI_DBT_HISTORIC;
			$query = 
"CREATE TABLE {$t}	(
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`status` enum('valid', 'moderated', 'deleted') NOT NULL DEFAULT 'valid',
	`creation_date` datetime NOT NULL,
	`message_id` int(11) unsigned NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$wpdb->query($query);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
	}
}

