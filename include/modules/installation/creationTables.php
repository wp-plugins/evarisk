<?php
/*
Installation de l'extension
	- Cr�ation des tables
	- Initialisation des permissions
*/
require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php');

function evarisk_creationTables()
{// Cr�ation des tables lors de l'installation
	
	require_once(EVA_LIB_PLUGIN_DIR . 'version/EvaVersion.class.php');
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	global $wpdb;
	
	
	if(EvaVersion::getVersion('base_evarisk') < 1)
	{
		// On v�rifie si la table version n'existe pas
		if( $wpdb->get_var("show tables like '" . TABLE_VERSION . "'") != TABLE_VERSION) {
			// On construit la requete SQL de cr�ation de table
			$sql = 
				"CREATE TABLE " . TABLE_VERSION . " (
					`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
					`nom` VARCHAR( 255 ) NOT NULL UNIQUE,
					`version` INT( 10 ) NOT NULL,
					`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid'
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
			// Execution de la requete
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_VERSION . " (`id`, `nom`, `version`) VALUES 
				('1', 'base_evarisk', '0');";
			// Execution de la requete
			$wpdb->query($sql);
		}
	
		{// Various tabes
			// On v�rifie si la table photo n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_PHOTO . "'") != TABLE_PHOTO) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_PHOTO . " (
						`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
						`idDestination` INT( 10 ) NOT NULL,
						`tableDestination` VARCHAR( 255) NOT NULL,
						`photo` VARCHAR( 255 ) NOT NULL,
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						INDEX ( `idDestination`, `tableDestination` )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			
			// On v�rifie si la table adresse n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_ADRESSE . "'") != TABLE_ADRESSE) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_ADRESSE . " (
						`id` INT( 10 ) NOT NULL  AUTO_INCREMENT PRIMARY KEY,
						`ligne1` VARCHAR( 32 ) NOT NULL,
						`ligne2` VARCHAR( 32 ) NOT NULL,
						`ville` VARCHAR( 26 ) NOT NULL,
						`codePostal` VARCHAR( 5 ) NOT NULL,
						`longitude` FLOAT NOT NULL,
						`latitude` FLOAT NOT NULL,
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid'
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			
			// On v�rifie si la table personne n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_PERSONNE . "'") != TABLE_PERSONNE) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_PERSONNE . " (
						`id` INT( 10 ) NOT NULL  AUTO_INCREMENT PRIMARY KEY,
						`nom` VARCHAR( 255 ) NOT NULL,
						`prenom` VARCHAR( 255 ) NOT NULL,
						`sexe` ENUM( 'Masculin', 'Feminin' ) NOT NULL,
						`dateNaissance` DATE,
						`id_adresse` INT( 10 ),
						`telephoneFixe` VARCHAR( 21 ),
						`telephonePortable` VARCHAR( 21 ),
						`telephonePoste` VARCHAR( 21 ),
						`couriel` VARCHAR( 255 ),
						`mainPhoto` VARCHAR( 255 ),
						`note` TEXT,
						`personneAPrevenir` INT( 10 ),
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						INDEX ( `id_adresse` ),
						INDEX ( `personneAPrevenir` ),
						FOREIGN KEY (`id_adresse`) REFERENCES `". TABLE_ADRESSE ."` ( `id` ),
						FOREIGN KEY (`personneAPrevenir`) REFERENCES `". TABLE_PERSONNE ."` ( `id` )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			
			// On v�rifie si la table options n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_OPTION . "'") != TABLE_OPTION) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_OPTION . " (
						`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
						`nom` INT( 10 ) NOT NULL,
						`valeur` VARCHAR( 255) NOT NULL,
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						UNIQUE ( `nom` )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
		}
		{// Compagny hierarchie tables
			// On v�rifie si la table groupement n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_GROUPEMENT . "'") != TABLE_GROUPEMENT) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_GROUPEMENT . " (
						`id` INT( 10 ) NOT NULL  AUTO_INCREMENT PRIMARY KEY,
						`nom` VARCHAR( 255 ) NOT NULL,
						`description` TEXT,
						`id_adresse` INT( 10 ),
						`telephoneGroupement` VARCHAR( 21 ),
						`id_responsable` INT(10),
						`effectif` INT(10),
						`mainPhoto` VARCHAR( 255 ),
						`limiteGauche` INT(16) NOT NULL,
						`limiteDroite` INT(16) NOT NULL,
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						UNIQUE ( `nom` ),
						INDEX ( `id_adresse` ),
						INDEX ( `id_responsable` ),
						FOREIGN KEY (`id_adresse`) REFERENCES `". TABLE_ADRESSE ."` ( `id` ),
						FOREIGN KEY (`id_responsable`) REFERENCES `". TABLE_PERSONNE ."` ( `id` )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_GROUPEMENT . " (`id`, `nom`, `limiteGauche`, `limiteDroite`) VALUES 
				('1', 'Groupement Racine', '0', '1');";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On v�rifie si la table unit� de travail n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_UNITE_TRAVAIL . "'") != TABLE_UNITE_TRAVAIL ) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_UNITE_TRAVAIL . " (
						`id` INT( 10 ) NOT NULL  AUTO_INCREMENT PRIMARY KEY,
						`nom` VARCHAR( 255 ) NOT NULL,
						`description` TEXT,
						`id_adresse` INT( 10 ),
						`telephoneUnite` VARCHAR( 21 ),
						`id_groupement` INT(10),
						`id_responsable` INT(10),
						`effectif` INT(10),
						`mainPhoto` VARCHAR( 255 ),
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						UNIQUE ( `nom` ),
						INDEX ( `id_adresse` ),
						INDEX ( `id_groupement` ),
						INDEX ( `id_responsable` ),
						FOREIGN KEY (`id_adresse`) REFERENCES `". TABLE_ADRESSE ."` ( `id` ),
						FOREIGN KEY (`id_groupement`) REFERENCES `". TABLE_GROUPEMENT ."` ( `id` ),
						FOREIGN KEY (`id_responsable`) REFERENCES `". TABLE_PERSONNE ."` ( `id` )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
		}
		{// Dangers tables
			// On v�rifie si la table danger n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_CATEGORIE_DANGER . "'") != TABLE_CATEGORIE_DANGER ) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_CATEGORIE_DANGER . " (
						`id` INT( 10 ) NOT NULL  AUTO_INCREMENT PRIMARY KEY,
						`nom` VARCHAR( 255 ) NOT NULL,
						`description` TEXT,
						`limiteGauche` INT(16) NOT NULL,
						`limiteDroite` INT(16) NOT NULL,
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						UNIQUE ( `nom` )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_CATEGORIE_DANGER . " (`id`, `nom`, `limiteGauche`, `limiteDroite`) VALUES ('1', 'Categorie Racine', '0', '1');";
				$wpdb->query($sql);
			}
			// On v�rifie si la table danger n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_DANGER . "'") != TABLE_DANGER ) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_DANGER . " (
						`id` INT( 10 ) NOT NULL  AUTO_INCREMENT PRIMARY KEY,
						`id_categorie` INT( 10 ) NOT NULL ,
						`code_danger` INT( 10 ),
						`nom` VARCHAR( 255 ) NOT NULL,
						`description` TEXT,
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						UNIQUE ( `nom` ),
						INDEX ( `id_categorie` ),
						UNIQUE (`id_categorie`, `code_danger`),
						FOREIGN KEY (`id_categorie`) REFERENCES `". TABLE_CATEGORIE_DANGER ."` ( `id` )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
		}
		{// Methods tables
			// On v�rifie si la table op�rateur n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_OPERATEUR . "'") != TABLE_OPERATEUR ) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_OPERATEUR . " (
						`symbole` varchar(20) NOT NULL PRIMARY KEY,
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid'
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_OPERATEUR . " (`symbole`) VALUES 
				('/'),
				('*'),
				('-'),
				('+')
				;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On v�rifie si la table variable n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_VARIABLE . "'") != TABLE_VARIABLE ) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_VARIABLE . " (
						`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						`nom` VARCHAR( 255 ) NOT NULL,
						`min` FLOAT UNSIGNED NOT NULL,
						`max` FLOAT UNSIGNED NOT NULL,
						`annotation` TEXT,
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid'
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On v�rifie si la table valeur alternative n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_VALEUR_ALTERNATIVE . "'") != TABLE_VALEUR_ALTERNATIVE ) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE `" . TABLE_VALEUR_ALTERNATIVE . "` (
					`id_variable` INT(10) NOT NULL ,
					`valeur` INT(10) NOT NULL ,
					`valeurAlternative` FLOAT NOT NULL ,
					`date` DATE NOT NULL ,
					`Status` ENUM('Valid', 'Moderated', 'Deleted') NOT NULL DEFAULT 'Valid',
					INDEX (`id_variable`),
					INDEX (`id_variable` , `valeur`),
					FOREIGN KEY (`id_variable`) REFERENCES `" . TABLE_VARIABLE . "` (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On v�rifie si la table m�thode n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_METHODE . "'") != TABLE_METHODE ) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_METHODE . " (
						`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						`nom` VARCHAR( 255 ) NOT NULL,
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						UNIQUE (`nom`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On v�rifie si la table avoir variable n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_AVOIR_VARIABLE . "'") != TABLE_AVOIR_VARIABLE) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_AVOIR_VARIABLE . " (
						`id_methode` INT( 10 ) NOT NULL,
						`id_variable` INT( 10 ) NOT NULL,
						`ordre` INT(10) NOT NULL,
						`date` DATETIME NOT NULL,
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						PRIMARY KEY (`id_methode`, `id_variable`, `ordre`, `date`),
						INDEX ( `id_methode` ),
						INDEX ( `id_variable` ),
						FOREIGN KEY (`id_methode`) REFERENCES `". TABLE_METHODE ."` ( `id` ),
						FOREIGN KEY (`id_variable`) REFERENCES `". TABLE_VARIABLE ."` ( `id` )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On v�rifie si la table avoir operateur n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_AVOIR_OPERATEUR . "'") != TABLE_AVOIR_OPERATEUR) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_AVOIR_OPERATEUR . " (
						`id_methode` INT( 10 ) NOT NULL,
						`operateur` VARCHAR ( 20 ) NOT NULL,
						`ordre` INT(10) NOT NULL,
						`date` DATETIME NOT NULL,
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						PRIMARY KEY (`id_methode`, `operateur`, `ordre`, `date`),
						INDEX ( `id_methode` ),
						INDEX ( `operateur` ),
						FOREIGN KEY (`id_methode`) REFERENCES `". TABLE_METHODE ."` ( `id` ),
						FOREIGN KEY (`operateur`) REFERENCES `". TABLE_OPERATEUR ."` ( `symbole` )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
		}
		{// Risks tables
			// On v�rifie si la table risque d�fini n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_RISQUE . "'") != TABLE_RISQUE ) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_RISQUE . " (
						`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						`id_danger` INT(10) NOT NULL,
						`id_methode` INT ( 10 ) NOT NULL,
						`id_element` INT ( 10 ) NOT NULL,
						`nomTableElement` VARCHAR ( 255 ) NOT NULL,
						`commentaire` TEXT,
						`date` DATETIME NOT NULL,
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						INDEX ( `id_danger` ),
						INDEX ( `id_methode` ),
						INDEX ( `id_element`, `nomTableElement` ),
						FOREIGN KEY (`id_danger`) REFERENCES ". TABLE_DANGER ." ( `id` ),
						FOREIGN KEY (`id_methode`) REFERENCES ". TABLE_METHODE ." ( `id` )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On v�rifie si la table avoir_valeur n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_AVOIR_VALEUR . "'") != TABLE_AVOIR_VALEUR ) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_AVOIR_VALEUR . " (
						`id_risque` INT(10) NOT NULL,
						`id_variable` INT ( 10 ) NOT NULL,
						`valeur` INT ( 10 ) NOT NULL,
						`date` DATETIME NOT NULL,
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						PRIMARY KEY (`id_risque`, `id_variable`,`date`),
						INDEX ( `id_risque` ),
						INDEX ( `id_variable` ),
						FOREIGN KEY (`id_risque`) REFERENCES ". TABLE_RISQUE ." ( `id` ),
						FOREIGN KEY (`id_variable`) REFERENCES ". TABLE_VARIABLE ." ( `id` )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
		}
		{// Standard tables
			// On v�rifie si la table �talon n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_ETALON . "'") != TABLE_ETALON) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_ETALON . " (
						`min` INT( 10 ) NOT NULL,
						`max` INT( 10 ) NOT NULL,
						`pas` INT( 10 ) NOT NULL,
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						PRIMARY KEY(`min`, `max`, `pas`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_ETALON . " (`min`, `max`, `pas`, `Status`) VALUES 
				('0', '100', '1', 'Valid');";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On v�rifie si la table valeur �talon n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_VALEUR_ETALON . "'") != TABLE_VALEUR_ETALON) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_VALEUR_ETALON . " (
						`valeur` INT( 10 ) NOT NULL PRIMARY KEY,
						`niveauSeuil` INT( 10 ) NULL DEFAULT NULL,
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						UNIQUE (`niveauSeuil`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
				for($i=0; $i<=100; $i++)
				{
					if($i%20 == 0)
					{
						$sql = "INSERT INTO " . TABLE_VALEUR_ETALON . " (`valeur`, `niveauSeuil`, `Status`) VALUES 
						('" . $i . "', '" . ($i/20 + 1) . "', 'Valid');";
					}
					else
					{
						$sql = "INSERT INTO " . TABLE_VALEUR_ETALON . " (`valeur`, `niveauSeuil`, `Status`) VALUES 
						('" . $i . "', null, 'Valid');";
					}
					// Execution de la requete
					$wpdb->query($sql);
				}
			}
			// On v�rifie si la table �quivalence �talon n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_EQUIVALENCE_ETALON . "'") != TABLE_EQUIVALENCE_ETALON) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_EQUIVALENCE_ETALON . " (
						`id_methode` INT( 10 ) NOT NULL,
						`id_valeur_etalon` INT( 10 ) NOT NULL,
						`date` DATETIME NOT NULL,
						`valeurMaxMethode` FLOAT NULL,
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						PRIMARY KEY(`id_methode`, `id_valeur_etalon`, `date`),
						INDEX ( `id_methode` ),
						INDEX ( `id_valeur_etalon` ),
						FOREIGN KEY (`id_methode`) REFERENCES `" . TABLE_METHODE . "` ( `id` ),
						FOREIGN KEY (`id_valeur_etalon`) REFERENCES `" . TABLE_VALEUR_ETALON . "` ( `valeur` )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
		}
		{// PPE tables
			// On v�rifie si la table des EPI n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_EPI . "'") != TABLE_EPI) {
				// On construit la requete SQL de cr�ation de table
				$sql = "
					CREATE TABLE  `" . TABLE_EPI . "` (
						`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT  'PPE identifier',
						`name` VARCHAR( 255 ) NOT NULL COMMENT  'Name of the PPE',
						`path` VARCHAR( 255 ) NOT NULL COMMENT  'Path to the image from the root of the plugin',
						`status` ENUM(  'Valid',  'Moderated',  'Deleted' ) NOT NULL DEFAULT  'Valid' COMMENT  'Status of the recording'
					) ENGINE = MyISAM COMMENT =  'Table containing the personal protective equipment (PPE)';";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On v�rifie si la table des EPI n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_UTILISE_EPI . "'") != TABLE_UTILISE_EPI) {
				// On construit la requete SQL de cr�ation de table
				$sql = "
					CREATE TABLE  `" . TABLE_UTILISE_EPI . "` (
						`ppeId` INT( 10 ) NOT NULL COMMENT  'PPE identifier',
						`elementId` INT( 10 ) NOT NULL COMMENT  'Element identifier',
						`elementTable` VARCHAR( 255 ) NOT NULL COMMENT  'Element table name',
						PRIMARY KEY (  `ppeId` ,  `elementId` ,  `elementTable` ),
						INDEX (  `ppeId` ),
						INDEX (  `elementId` , `elementTable` ),
						FOREIGN KEY (  `ppeId` ) REFERENCES  `" . TABLE_EPI . "` ( `id` )
					) ENGINE = MyISAM COMMENT =  'Table linking the PPE with those who wear';";
				// Execution de la requete
				$wpdb->query($sql);
			}

		}
		{// Regulatory Watch tables
			// On v�rifie si la table texte r�f�renciel n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_TEXTE_REFERENCIEL . "'") != TABLE_TEXTE_REFERENCIEL ) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_TEXTE_REFERENCIEL . " (
						`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						`rubrique` VARCHAR( 255 ) NOT NULL,
						`datePremiereRatification` DATE NOT NULL,
						`dateDerniereModification` DATE,
						`objet` TEXT NOT NULL,
						`objetCout` TEXT NOT NULL,
						`texteSousIntro` TEXT NOT NULL,
						`adresseTexte` VARCHAR( 255 ) NOT NULL,
						`analysable` BOOLEAN,
						`loi` BOOLEAN,
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						UNIQUE (`rubrique`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On v�rifie si la table groupe question n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_GROUPE_QUESTION . "'") != TABLE_GROUPE_QUESTION ) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_GROUPE_QUESTION . " (
						`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						`nom` VARCHAR( 255 ) NOT NULL,
						`code` VARCHAR( 255 ) NOT NULL,
						`extraitTexte` TEXT,
						`limiteGauche` INT(16) NOT NULL,
						`limiteDroite` INT(16) NOT NULL,
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid'
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
				$sql = "INSERT INTO `" . TABLE_GROUPE_QUESTION . "` (`id`, `nom`, `code`, `extraitTexte`, `limiteGauche`, `limiteDroite`, `Status`) VALUES
					('1', 'Groupe Question Racine', '0', NULL , '0', '3', 'Valid'),
					('2', 'Rubrique 2220', '', NULL , '1', '2', 'Valid');";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On v�rifie si la table correspond texte r�f�renciel n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_CORRESPOND_TEXTE_REFERENCIEL . "'") != TABLE_CORRESPOND_TEXTE_REFERENCIEL ) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_CORRESPOND_TEXTE_REFERENCIEL . " (
						`id_texte_referenciel` INT( 10 ) NOT NULL,
						`id_groupe_question` INT( 10 ) NOT NULL,
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						PRIMARY KEY (`id_texte_referenciel`, `id_groupe_question`),
						INDEX ( `id_texte_referenciel` ),
						INDEX ( `id_groupe_question` ),
						FOREIGN KEY (`id_texte_referenciel`) REFERENCES ". TABLE_TEXTE_REFERENCIEL ." ( `id` ),
						FOREIGN KEY (`id_groupe_question`) REFERENCES ". TABLE_GROUPE_QUESTION ." ( `id` )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On v�rifie si la table question n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_QUESTION . "'") != TABLE_QUESTION ) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_QUESTION . " (
						`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						`code` VARCHAR( 255 ) NOT NULL,
						`enonce` TEXT NOT NULL,
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid'
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On v�rifie si la table poss�de question n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_POSSEDE_QUESTION . "'") != TABLE_POSSEDE_QUESTION ) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_POSSEDE_QUESTION . " (
						`id_groupe_question` INT( 10 ) NOT NULL,
						`id_question` INT( 10 ) NOT NULL,
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						PRIMARY KEY (`id_groupe_question`, `id_question`),
						INDEX ( `id_groupe_question` ),
						INDEX ( `id_question` ),
						FOREIGN KEY (`id_groupe_question`) REFERENCES ". TABLE_GROUPE_QUESTION ." ( `id` ),
						FOREIGN KEY (`id_question`) REFERENCES ". TABLE_QUESTION ." ( `id` )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On v�rifie si la table r�ponse n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_REPONSE . "'") != TABLE_REPONSE ) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_REPONSE . " (
						`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						`nom` VARCHAR( 255 ) NOT NULL,
						`min` INT( 10 ),
						`max` INT( 10 ),
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid'
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_REPONSE . " (`nom`, `min`, `max`, `Status`) VALUES 
				('Oui', NULL, NULL, 'Valid'),
				('Non', NULL, NULL, 'Valid'),
				('NA', NULL, NULL, 'Valid'),
				('NC', NULL, NULL, 'Valid'),
				('%', 0, 100, 'Valid')";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On v�rifie si la table accepte r�ponse n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_ACCEPTE_REPONSE . "'") != TABLE_ACCEPTE_REPONSE ) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_ACCEPTE_REPONSE . " (
						`id_question` INT( 10 ) NOT NULL,
						`id_reponse` INT( 10 ) NOT NULL,
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						PRIMARY KEY (`id_question`, `id_reponse`),
						INDEX ( `id_question` ),
						INDEX ( `id_reponse` ),
						FOREIGN KEY (`id_question`) REFERENCES ". TABLE_QUESTION ." ( `id` ),
						FOREIGN KEY (`id_reponse`) REFERENCES ". TABLE_REPONSE ." ( `id` )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On v�rifie si la table r�ponse question n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_REPONSE_QUESTION . "'") != TABLE_REPONSE_QUESTION ) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_REPONSE_QUESTION . " (
						`id_question` INT( 10 ) NOT NULL,
						`id_element` INT( 10 ) NOT NULL,
						`nomTableElement` VARCHAR( 255 ) NOT NULL,
						`id_reponse` INT( 10 ) NOT NULL,
						`date` DATE NOT NULL,
						`limiteValidite` DATE,
						`valeur` INT( 10 ),
						`observation` TEXT,
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted', 'archived') NOT NULL DEFAULT 'Valid',
						PRIMARY KEY (`id_question`, `id_element`, `nomTableElement`, `date`),
						INDEX ( `id_question` ),
						INDEX ( `id_element`, `nomTableElement` ),
						INDEX ( `id_reponse` ),
						FOREIGN KEY (`id_question`) REFERENCES ". TABLE_QUESTION ." ( `id` ),
						FOREIGN KEY (`id_reponse`) REFERENCES ". TABLE_REPONSE ." ( `id` )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
			// On v�rifie si la table concern� par texte referenciel n'existe pas
			if( $wpdb->get_var("show tables like '" . TABLE_CONCERNE_PAR_TEXTE_REFERENCIEL . "'") != TABLE_CONCERNE_PAR_TEXTE_REFERENCIEL ) {
				// On construit la requete SQL de cr�ation de table
				$sql = 
					"CREATE TABLE " . TABLE_CONCERNE_PAR_TEXTE_REFERENCIEL . " (
						`id_texte_referenciel` INT( 10 ) NOT NULL,
						`id_element` INT( 10 ) NOT NULL,
						`nomTableElement` VARCHAR( 255 ) NOT NULL,
						`Status` ENUM( 'Valid', 'Moderated', 'Deleted' ) NOT NULL DEFAULT 'Valid',
						PRIMARY KEY (`id_texte_referenciel`, `id_element`, `nomTableElement`),
						INDEX ( `id_texte_referenciel` ),
						INDEX ( `id_element`, `nomTableElement` ),
						FOREIGN KEY (`id_texte_referenciel`) REFERENCES ". TABLE_TEXTE_REFERENCIEL ." ( `id` )
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				// Execution de la requete
				$wpdb->query($sql);
			}
		}
	}
	else
	{//Version 1 : cas particulier -- formulaire inscription
		if(EvaVersion::getVersion('base_evarisk') <= 2)
		{//Update de la table des cat�gorie de dangers 																										/!\	PLUS UTILISE/!\
			// $sql = 'ALTER TABLE ' . TABLE_CATEGORIE_DANGER . ' ADD photo varchar( 255 ) NULL  DEFAULT NULL COMMENT "Image to display in risk assessment" AFTER description';
			// $wpdb->query($sql);
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 3)
		{//Update de la table des textes r�glementaires
			$sql = 'ALTER TABLE ' . TABLE_TEXTE_REFERENCIEL . ' ADD affectable BOOLEAN NOT NULL DEFAULT 0 COMMENT "Is the text assignable" AFTER adresseTexte';
			$wpdb->query($sql);
			$sql = 'ALTER TABLE ' . TABLE_TEXTE_REFERENCIEL . ' CHANGE rubrique rubrique VARCHAR( 255 ) NOT NULL COMMENT "The text name"';  
			$wpdb->query($sql);
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 4)
		{//Update de la table des cat�gorie de dangers (changement de la colonne photo en mainPhoto) 			/!\	PLUS UTILISE /!\
			// $sql = 'ALTER TABLE ' . TABLE_CATEGORIE_DANGER . ' CHANGE photo mainPhoto varchar( 255 ) NULL  DEFAULT NULL COMMENT "Image to display in risk assessment" AFTER description';
			// $wpdb->query($sql);
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 5)
		{//Ajout de la table des t�ches
			$sqlCreationTable = 'CREATE TABLE ' . TABLE_TACHE . ' (
					`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "Task Identifier",
					`nom` VARCHAR ( 255 ) NOT NULL COMMENT "Task name",
					`limiteGauche` INT ( 10 ) NOT NULL COMMENT "Left limit to simulate the tree",
					`limiteDroite` INT ( 10 ) NOT NULL COMMENT "Right limit to simulate the tree",
					`description` TEXT NULL COMMENT "Task description",
					`dateDebut` DATE NULL DEFAULT NULL COMMENT "Task start date",
					`dateFIN` DATE NULL DEFAULT NULL COMMENT "Task finish date",
					`avancement` INT ( 10 ) NULL DEFAULT NULL COMMENT "Task progression",
					`lieu` VARCHAR ( 255 ) NULL DEFAULT NULL COMMENT "Task place",
					`tableProvenance` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT "Table of the element that induces the task",
					`idProvenance` INT ( 10 ) NULL DEFAULT NULL COMMENT "Identifier of the element that induces the task",
					`Status` ENUM( \'Valid\', \'Moderated\', \'Deleted\', \'Aborded\' ) NOT NULL DEFAULT \'Valid\' COMMENT "Task status"
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT = "Table containing the task (for corrective actions)";';
			$wpdb->query($sqlCreationTable);
			$sqlInsertion = 'INSERT INTO ' . TABLE_TACHE . ' (`nom`, `limiteGauche`, `limiteDroite`) VALUES ("Tache Racine", 0, 1);';
			$wpdb->query($sqlInsertion);
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 6)
		{//Ajout de la table des activit�s
			$sqlCreationTable = 'CREATE TABLE ' . TABLE_ACTIVITE . ' (
					`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "Activity Identifier",
					`id_tache` INT( 10 ) NOT NULL COMMENT "Task which the activity depends on",
					`nom` VARCHAR ( 255 ) NOT NULL COMMENT "Activity name",
					`description` TEXT NULL COMMENT "Activity description",
					`dateDebut` DATE NULL DEFAULT NULL COMMENT "Activity start date",
					`dateFin` DATE NULL DEFAULT NULL COMMENT "Activity finish date",
					`avancement` INT ( 10 ) NULL DEFAULT NULL COMMENT "Activity progression",
					`lieu` VARCHAR ( 255 ) NULL DEFAULT NULL COMMENT "Activity place",
					`Status` ENUM( \'Valid\', \'Moderated\', \'Deleted\', \'Aborded\' ) NOT NULL DEFAULT \'Valid\' COMMENT "Activity status"
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT = "Table containing the activity (for corrective actions)";';
			$wpdb->query($sqlCreationTable);
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 7)
		{//Update de la table des t�ches (changement de la colonne dateFIN en dateFin)
			$sql = 'ALTER TABLE ' . TABLE_TACHE . ' CHANGE dateFIN dateFin DATE NULL DEFAULT NULL COMMENT "Task finish date"';
			$wpdb->query($sql);
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 8)
		{//Update de la table des t�ches et activite(ajout des colonnes fisrtInsert et cout)
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
					`id` int(10) NOT NULL AUTO_INCREMENT COMMENT "Single document identifier",
					`element` char(255) collate utf8_unicode_ci NOT NULL COMMENT "The element type the single document will be affected to",
					`elementId` int(10) unsigned NOT NULL COMMENT "the id of the element associated with the single document",
					`referenceDUER` varchar(64) NOT NULL default \'\' COMMENT "Single document reference",
					`dateGenerationDUER` datetime default NULL COMMENT "Single document creation date",
					`nomDUER` varchar(128) default NULL COMMENT "Document name",
					`dateDebutAudit` date default NULL COMMENT "Audit start date",
					`dateFinAudit` date default NULL COMMENT "Audit end date",
					`nomSociete` varchar(128) default NULL COMMENT "Society name",
					`telephoneFixe` int(10) default NULL COMMENT "Society phone number",
					`telephonePortable` int(10) default NULL COMMENT "Society cellular phone number",
					`telephoneFax` int(10) default NULL COMMENT "Society fax number",
					`emetteurDUER` varchar(128) default NULL COMMENT "Transmitter of the single document",
					`destinataireDUER` varchar(128) default NULL COMMENT "Recipient of the single document",
					`revisionDUER` int(3) default NULL COMMENT "Single document version",
					`planDUER` varchar(128) default NULL COMMENT "The single document scheme",
					`codeHtmlGroupesUtilisateurs` text COMMENT "Html code containing the different users\' group concerned by the single document",
					`codeHtmlRisqueUnitaire` text COMMENT "Html code containing the different risks evaluated in this single document",
					`codeHtmlRisquesParUnite` text COMMENT "Html code containing the risks\' summary for each work unit",
					`methodologieDUER` text COMMENT "Methodology used to create the single document",
					`sourcesDUER` text COMMENT "The different document used to create the single document",
					`alerteDUER` text COMMENT "Warning about the single document",
					`conclusionDUER` text COMMENT "conclusion about the sthe single document",
					PRIMARY KEY  (`id`),
					UNIQUE  (`referenceDUER`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT = "Table containing the different single document";';
			$wpdb->query($sqlCreationTable);
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 10)
		{//Ajout de la table de liaison des groupes d'utilisateurs
			$sqlCreationTable = 'CREATE TABLE ' . TABLE_LIAISON_USER_GROUPS . ' (
					`id_group` INT( 10 ) NOT NULL COMMENT "Group Identifier",
					`table_element` VARCHAR ( 255 ) NOT NULL COMMENT "Element data base table",
					`id_element` INT( 10 ) NOT NULL COMMENT "Element identefier in the table",
					`date` DATETIME NOT NULL COMMENT "Date of the record",
					`Status` ENUM( \'Valid\', \'Moderated\', \'Deleted\' ) NOT NULL DEFAULT \'Valid\' COMMENT "Bind status"
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT = "Table containing the bind between users group and others tables";';
			$wpdb->query($sqlCreationTable);
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 11)
		{//Ajout des photos par d�faut pour les cat�gories de danger																			/!\	PLUS UTILISE/!\
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 12)
		{//Ajout de la table d'identification des utilisateurs evalues
			$sql = 
				"CREATE TABLE IF NOT EXISTS " . TABLE_LIAISON_USER_EVALUATION . " (
				`id_user` int(10) unsigned NOT NULL COMMENT 'user identifier',
				`table_element` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Element database table',
				`id_element` int(10) unsigned NOT NULL COMMENT 'Element identifier in the previous table',
				`date` datetime NOT NULL COMMENT 'date of the record',
				`status` enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid' COMMENT 'bind status',
					KEY `id_user` (`id_user`,`id_element`,`status`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Table containing the bind between users and elements';";
			$wpdb->query($sql);
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 13)
		{//Mise � jour de l'index de la table de liaison entre les utilisateurs et l'�valuation
			$sql = "ALTER TABLE " . TABLE_LIAISON_USER_EVALUATION . " DROP INDEX `id_user`";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_LIAISON_USER_EVALUATION . " ADD PRIMARY KEY `id_user` ( `id_user` , `id_element` , `table_element` ) ";
			$wpdb->query($sql);
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 14)
		{//Ajout d'une cl� primaire pour la table de liaison entre les groupes d'utilisateurs et les groupements/unite de travail
			$sql = "ALTER TABLE " . TABLE_LIAISON_USER_GROUPS . " ADD PRIMARY KEY ( `id_group` , `table_element` , `id_element` )  ";
			$wpdb->query($sql);
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 15)
		{//Changement de la structure de la table photo, d�placement et renommage du champs status, ajout d'un champs qui d�finit si c'est la photo principale ou non
			$sql = "ALTER TABLE " . TABLE_PHOTO . " DROP `Status`";
			$wpdb->query($sql);

			$sql = "ALTER TABLE " . TABLE_PHOTO . " ADD `status` ENUM('valid','moderated','deleted') NOT NULL DEFAULT 'valid' AFTER `id`, ADD `isMainPicture` ENUM('yes','no') NOT NULL DEFAULT 'no' AFTER `status`;";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_PHOTO . " ADD INDEX ( `status` )";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_PHOTO . " ADD INDEX ( `isMainPicture` )";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_PHOTO . " DROP INDEX `idDestination`";
			$wpdb->query($sql);
			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 16)
		{//Ajout de la table pour les groupes d'evaluateurs
			$sql = 
				"CREATE TABLE IF NOT EXISTS " . TABLE_EVA_EVALUATOR_GROUP . " (
					`evaluator_group_id` smallint(5) unsigned NOT NULL auto_increment,
					`evaluator_group_status` enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
					`evaluator_group_name` varchar(255) collate utf8_unicode_ci NOT NULL,
					`evaluator_group_description` text collate utf8_unicode_ci NOT NULL,
					`evaluator_group_creation_date` datetime NOT NULL,
					`evaluator_group_creation_user_id` bigint(20) unsigned NOT NULL COMMENT 'The user identifier that create the evaluator group',
					`evaluator_group_deletion_date` datetime NOT NULL,
					`evaluator_group_deletion_user_id` bigint(20) unsigned NOT NULL COMMENT 'The user identifier that delete the evaluator group',
					PRIMARY KEY  (`evaluator_group_id`),
					KEY `user_group_status` (`evaluator_group_status`),
					KEY `evaluator_group_creation_user_id` (`evaluator_group_creation_user_id`),
					KEY `evaluator_group_deletion_user_id` (`evaluator_group_deletion_user_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
			$wpdb->query($sql);

			$sql = 
				"CREATE TABLE IF NOT EXISTS " . TABLE_EVA_EVALUATOR_GROUP_DETAILS . " (
					`id` int(10) unsigned NOT NULL auto_increment,
					`evaluator_group_id` smallint(5) unsigned NOT NULL,
					`user_id` bigint(20) unsigned NOT NULL,
					`Status` enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
					`dateEntree` datetime NOT NULL,
					`affectationUserId` bigint(20) NOT NULL COMMENT 'The user identifier that affect a user to an evaluator group',
					`dateSortie` datetime NOT NULL,
					`desaffectationUserId` bigint(20) unsigned NOT NULL COMMENT 'The user identifier that unaffect a user to an evaluator group',
					PRIMARY KEY  (`id`,`evaluator_group_id`,`user_id`),
					KEY `Status` (`Status`),
					KEY `evaluator_group_id` (`evaluator_group_id`),
					KEY `user_id` (`user_id`),
					KEY `affectationUserId` (`affectationUserId`),
					KEY `desaffectationUserId` (`desaffectationUserId`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
			$wpdb->query($sql);

			$sql = 
				"CREATE TABLE IF NOT EXISTS " . TABLE_EVA_EVALUATOR_GROUP_BIND . " (
					`id` int(10) unsigned NOT NULL auto_increment,
					`id_group` int(10) NOT NULL COMMENT 'Group Identifier',
					`table_element` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Element data base table',
					`id_element` int(10) NOT NULL COMMENT 'Element identefier in the table',
					`dateAffectation` datetime NOT NULL COMMENT 'Affectation date for the evaluator group to a element',
					`affectationUserId` bigint(20) NOT NULL COMMENT 'The user identifier that affect a evaluator group to an element',
					`dateDesaffectation` datetime NOT NULL COMMENT 'Desaffectation date for the evaluator group to a element',
					`desaffectationUserId` bigint(20) NOT NULL COMMENT 'The user identifier that unaffect a evaluator group to an element',
					`Status` enum('Valid','Moderated','Deleted') collate utf8_unicode_ci NOT NULL default 'Valid' COMMENT 'Bind status',
					PRIMARY KEY  (`id`,`id_group`,`table_element`,`id_element`),
					KEY `Status` (`Status`),
					KEY `affecationUserId` (`affectationUserId`),
					KEY `desaffectationUserId` (`desaffectationUserId`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Table containing the bind between evalutors group and others';";
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
		if(EvaVersion::getVersion('base_evarisk') <= 17)
		{//Changement des champs contenant les groupes utilisateurs, la liste des risques par unit� et de la liste des risques unitaires dans la table document unique
			$sql = " ALTER TABLE " . TABLE_DUER . " CHANGE `codeHtmlGroupesUtilisateurs` `groupesUtilisateurs` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'A serialise array with the different users'' group'";
			$wpdb->query($sql);
			$sql = " ALTER TABLE " . TABLE_DUER . " CHANGE `codeHtmlRisqueUnitaire` `risquesUnitaires` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'A serialise array with the different single risqs'";
			$wpdb->query($sql);
			$sql = " ALTER TABLE " . TABLE_DUER . " CHANGE `codeHtmlRisquesParUnite` `risquesParUnite` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'A serialise array with the different risqs by unit'";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_DUER . " ADD `groupesUtilisateursAffectes` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'A serialise array with the different users'' group affected to the current element' AFTER `groupesUtilisateurs` ;";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_DUER . " ADD `status` ENUM( 'valid', 'moderated', 'deleted' ) NOT NULL DEFAULT 'valid' COMMENT 'The document status' AFTER `id` ;";
			$wpdb->query($sql);

			require_once(EVA_MODULES_PLUGIN_DIR . 'installation/insertions.php');
			evarisk_insertions();
		}
	}
}
?>