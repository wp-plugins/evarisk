<?php

function evarisk_insertions($insertions = null)
{
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	global $wpdb;
	

	if(digirisk_options::getDbOption('base_evarisk') < 1)
	{
		$dateInstallation = date('Y-m-d H:i:s');
		{// Tables dangers
			//Cat&eacute;gorie de dangers
			if($insertions['categorieDangers'] == 'true')
			{
				__('autres', 'evarisk');
				__('chute de plain-pied', 'evarisk');
				__('chute de hauteur', 'evarisk');
				__('manutention manuelle', 'evarisk');
				__('manutention m&eacute;canique', 'evarisk');
				__('circulation, d&eacute;placements', 'evarisk');
				__('effondrements, chute d\'objet', 'evarisk');
				__('machines et outils', 'evarisk');
				__('nuisances sonores', 'evarisk');
				__('produits chimiques, d&eacute;chets', 'evarisk');
				__('incendie, explosion', 'evarisk');
				__('electricit&eacute;', 'evarisk');
				__('eclairage', 'evarisk');
				__('travail sur &eacute;cran', 'evarisk');
				__('ambiances climatiques', 'evarisk');
				__('manque d\'hygi&egrave;ne', 'evarisk');
				__('soci&eacute;t&eacute; ext&eacute;rieure', 'evarisk');
				__('manque de formation', 'evarisk');
				$sql = "INSERT INTO " . TABLE_CATEGORIE_DANGER . " (id, nom, limiteGauche, limiteDroite) VALUES 
					('2', 'Chute de plain-pied', '1', '2'),
					('3', 'Chute de hauteur', '3', '4'),
					('4', 'Manutention manuelle', '5', '6'),
					('5', 'Manutention m&eacute;canique', '7', '8'),
					('6', 'Circulation, d&eacute;placements', '9', '10'),
					('7', 'Effondrements, chute d\'objet', '11', '12'),
					('8', 'Machines et outils', '13', '14'),
					('9', 'Nuisances sonores', '15', '16'),
					('10', 'Produits chimiques, d&eacute;chets', '17', '18'),
					('11', 'Incendie, explosion', '19', '20'),
					('12', 'Electricit&eacute;', '21', '22'),
					('13', 'Eclairage', '23', '24'),
					('14', 'Travail sur &eacute;cran', '25', '26'),
					('15', 'Ambiances climatiques', '27', '28'),
					('16', 'Manque d\'hygi&egrave;ne', '29', '30'),
					('17', 'Soci&eacute;t&eacute; ext&eacute;rieure', '31', '32'),
					('18', 'Manque de formation', '33', '34'),
					('19', 'Autres', '35', '36');";
				$wpdb->query($sql);
				$sql = "UPDATE " . TABLE_CATEGORIE_DANGER . " set limiteDroite = 37 WHERE id = 1";
				$wpdb->query($sql);
			}
			//Danger
			if($insertions['danger'] == 'true')
			{
				__('Divers chute de plain-pied', 'evarisk');
				__('Divers chute de hauteur', 'evarisk');
				__('Divers manutention manuelle', 'evarisk');
				__('Divers manutention m&eacute;canique', 'evarisk');
				__('Divers circulation, d&eacute;placements', 'evarisk');
				__('Divers effondrements, chute d\'objet', 'evarisk');
				__('Divers machines et outils', 'evarisk');
				__('Divers nuisances sonores', 'evarisk');
				__('Divers produits chimiques, d&eacute;chets', 'evarisk');
				__('Divers incendie, explosion', 'evarisk');
				__('Divers electricit&eacute;', 'evarisk');
				__('Divers eclairage', 'evarisk');
				__('Divers travail sur &eacute;cran', 'evarisk');
				__('Divers ambiances climatiques', 'evarisk');
				__('Divers manque d\'hygi&egrave;ne', 'evarisk');
				__('Divers soci&eacute;t&eacute; ext&eacute;rieure', 'evarisk');
				__('Divers manque de formation', 'evarisk');
				__('Divers autres', 'evarisk');
				$sql = "INSERT INTO " . TABLE_DANGER . " (id, id_categorie, nom) VALUES 
					('NULL', '2', 'Divers chute de plain-pied'),
					('NULL', '3', 'Divers chute de hauteur'),
					('NULL', '4', 'Divers manutention manuelle'),
					('NULL', '5', 'Divers manutention m&eacute;canique'),
					('NULL', '6', 'Divers circulation, d&eacute;placements'),
					('NULL', '7', 'Divers effondrements, chute d\'objet'),
					('NULL', '8', 'Divers machines et outils'),
					('NULL', '9', 'Divers nuisances sonores'),
					('NULL', '10', 'Divers produits chimiques, d&eacute;chets'),
					('NULL', '11', 'Divers incendie, explosion'),
					('NULL', '12', 'Divers electricit&eacute;'),
					('NULL', '13', 'Divers eclairage'),
					('NULL', '14', 'Divers travail sur &eacute;cran'),
					('NULL', '15', 'Divers ambiances climatiques'),
					('NULL', '16', 'Divers manque d\'hygi&egrave;ne'),
					('NULL', '17', 'Divers soci&eacute;t&eacute; ext&eacute;rieure'),
					('NULL', '18', 'Divers manque de formation'),
					('NULL', '19', 'Divers autres');";
				$wpdb->query($sql);
			}
		}
		{// Tables m&eacute;thodes
			if($insertions['methodes']['evarisk'] == 'true')
			{
				//M&eacute;thodes
				$sql = "INSERT INTO " . TABLE_METHODE . " (id, nom) VALUES ('1', 'Evarisk')";
				$wpdb->query($sql);
				//Variables
				__('Gravite', 'evarisk');
				__('0 : Pas de blessure possible\n1 : Blessure l&eacute;g&egrave;re\n2 : ITT<5 jours ou effet r&eacute;versible\n3 : ITT>5jours ou effet irr&eacute;versible\n4 : Menace sur la vie', 'evarisk');
				__('Exposition', 'evarisk');
				__('0 : Jamais en contact\n1 : Rare, 1 fois par an\n2 : Inhabituelle, 1 fois par mois\n3 : Occasionnelle, 1 fois par semaine\n4 : Fr&eacute;quente, 1 fois par jour', 'evarisk');
				__('Occurence', 'evarisk');
				__('1 : Jamais arriv&eacute;\n2 : Est d&eacute;j&agrave; arriv&eacute; dans des circonstances exeptionnelles\n3 : D&eacute;j&agrave; produit 2 fois\n4 : Se produit tous les mois', 'evarisk');
				__('Formation', 'evarisk');
				__('1 : Pr&eacute;vention r&eacute;guli&egrave;re\n2 : Formation individuelle obligatoire\n3 : Formation obligatoire non r&eacute;alis&eacute;e\n4 : Pas de formation ni de pr&eacute;vention', 'evarisk');
				__('Protection', 'evarisk');
				__('Protection', '1', '4', '1 : Intrins&egrave;que\n2 : Collective\n3 : Individuelle\n4 : Rien', 'evarisk');
				$sql = "INSERT INTO " . TABLE_VARIABLE . " (id, nom, min, max, annotation) VALUES 
					('1', 'Gravite', '0', '4', '0 : Pas de blessure possible\n1 : Blessure l&eacute;g&egrave;re\n2 : ITT<5 jours ou effet r&eacute;versible\n3 : ITT>5jours ou effet irr&eacute;versible\n4 : Menace sur la vie'),
					('2', 'Exposition', '0', '4', '0 : Jamais en contact\n1 : Rare, 1 fois par an\n2 : Inhabituelle, 1 fois par mois\n3 : Occasionnelle, 1 fois par semaine\n4 : Fr&eacute;quente, 1 fois par jour'),
					('3', 'Occurence', '1', '4', '1 : Jamais arriv&eacute;\n2 : Est d&eacute;j&agrave; arriv&eacute; dans des circonstances exeptionnelles\n3 : D&eacute;j&agrave; produit 2 fois\n4 : Se produit tous les mois'),
					('4', 'Formation', '1', '4', '1 : Pr&eacute;vention r&eacute;guli&egrave;re\n2 : Formation individuelle obligatoire\n3 : Formation obligatoire non r&eacute;alis&eacute;e\n4 : Pas de formation ni de pr&eacute;vention'),
					('5', 'Protection', '1', '4', '1 : Intrins&egrave;que\n2 : Collective\n3 : Individuelle\n4 : Rien');";
				$wpdb->query($sql);
				//Avoir variable
				$sql = "INSERT INTO " . TABLE_AVOIR_VARIABLE . " (id_methode, id_variable, ordre, date) VALUES 
					('1', '1', '1', '" . $dateInstallation . "'),
					('1', '2', '2', '" . $dateInstallation . "'),
					('1', '3', '3', '" . $dateInstallation . "'),
					('1', '4', '4', '" . $dateInstallation . "'),
					('1', '5', '5', '" . $dateInstallation . "');";
				$wpdb->query($sql);
				//Avoir op&eacute;rateur
				$sql = "INSERT INTO " . TABLE_AVOIR_OPERATEUR . " (id_methode, operateur, ordre, date) VALUES 
					('1', '*', '1', '" . $dateInstallation . "'),
					('1', '*', '2', '" . $dateInstallation . "'),
					('1', '*', '3', '" . $dateInstallation . "'),
					('1', '*', '4', '" . $dateInstallation . "');";
				$wpdb->query($sql);
				//Equivalence etalon
				$sql = "INSERT INTO " . TABLE_EQUIVALENCE_ETALON . " (id_methode, id_valeur_etalon, date, valeurMaxMethode, Status) VALUES
					(1, 0, '" . $dateInstallation . "', 0, 'Valid'),
					(1, 1, '" . $dateInstallation . "', 1, 'Valid'),
					(1, 2, '" . $dateInstallation . "', 2, 'Valid'),
					(1, 3, '" . $dateInstallation . "', 3, 'Valid'),
					(1, 4, '" . $dateInstallation . "', 4, 'Valid'),
					(1, 5, '" . $dateInstallation . "', 5, 'Valid'),
					(1, 6, '" . $dateInstallation . "', 6, 'Valid'),
					(1, 7, '" . $dateInstallation . "', 8, 'Valid'),
					(1, 8, '" . $dateInstallation . "', 9, 'Valid'),
					(1, 10, '" . $dateInstallation . "', 12, 'Valid'),
					(1, 12, '" . $dateInstallation . "', 16, 'Valid'),
					(1, 14, '" . $dateInstallation . "', 18, 'Valid'),
					(1, 17, '" . $dateInstallation . "', 24, 'Valid'),
					(1, 20, '" . $dateInstallation . "', 27, 'Valid'),
					(1, 25, '" . $dateInstallation . "', 32, 'Valid'),
					(1, 27, '" . $dateInstallation . "', 36, 'Valid'),
					(1, 31, '" . $dateInstallation . "', 48, 'Valid'),
					(1, 35, '" . $dateInstallation . "', 54, 'Valid'),
					(1, 40, '" . $dateInstallation . "', 64, 'Valid'),
					(1, 42, '" . $dateInstallation . "', 72, 'Valid'),
					(1, 44, '" . $dateInstallation . "', 81, 'Valid'),
					(1, 46, '" . $dateInstallation . "', 96, 'Valid'),
					(1, 48, '" . $dateInstallation . "', 108, 'Valid'),
					(1, 50, '" . $dateInstallation . "', 128, 'Valid'),
					(1, 55, '" . $dateInstallation . "', 144, 'Valid'),
					(1, 60, '" . $dateInstallation . "', 162, 'Valid'),
					(1, 66, '" . $dateInstallation . "', 192, 'Valid'),
					(1, 70, '" . $dateInstallation . "', 216, 'Valid'),
					(1, 77, '" . $dateInstallation . "', 243, 'Valid'),
					(1, 80, '" . $dateInstallation . "', 256, 'Valid'),
					(1, 84, '" . $dateInstallation . "', 288, 'Valid'),
					(1, 87, '" . $dateInstallation . "', 324, 'Valid'),
					(1, 90, '" . $dateInstallation . "', 384, 'Valid'),
					(1, 92, '" . $dateInstallation . "', 432, 'Valid'),
					(1, 95, '" . $dateInstallation . "', 512, 'Valid'),
					(1, 96, '" . $dateInstallation . "', 576, 'Valid'),
					(1, 98, '" . $dateInstallation . "', 768, 'Valid'),
					(1, 100, '" . $dateInstallation . "', 1024, 'Valid');";
				$wpdb->query($sql);
			}
		}
		{// Table EPI
			//DELETE FROM VERSION 44
		}
		{// Theme Evarisk
			eva_tools::copyEntireDirectory(EVA_HOME_DIR . 'evariskthemeplugin', WP_CONTENT_DIR . '/themes/Evarisk');

			if($insertions['theme'] == 'true')
			{
				switch_theme('Evarisk', 'Evarisk');
			}
		}
		digirisk_options::updateDbOption('base_evarisk', 1);
	}

	switch(digirisk_options::getDbOption('base_evarisk'))
	{
		case 1:
		{
			$sql = 'UPDATE  ' . TABLE_CATEGORIE_DANGER . ' SET photo = "medias/images/Pictos/categorieDangers/autre_PictoCategorie.png" WHERE id =19 AND nom="Autres"';
			$wpdb->query($sql);
			$sql = 'UPDATE ' . TABLE_CATEGORIE_DANGER . ' SET photo = "medias/images/Pictos/categorieDangers/chutePP_PictoCategorie.png" WHERE id =2 AND nom="Chute de plain-pied"';
			$wpdb->query($sql);
			$sql = 'UPDATE ' . TABLE_CATEGORIE_DANGER . ' SET photo = "medias/images/Pictos/categorieDangers/chuteH_PictoCategorie.png" WHERE id = 3 AND nom="Chute de hauteur"';
			$wpdb->query($sql);
			$sql = 'UPDATE ' . TABLE_CATEGORIE_DANGER . ' SET photo = "medias/images/Pictos/categorieDangers/manutentionMa_PictoCategorie.png" WHERE id = 4 AND nom="Manutention manuelle"';
			$wpdb->query($sql);
			$sql = 'UPDATE ' . TABLE_CATEGORIE_DANGER . ' SET photo = "medias/images/Pictos/categorieDangers/manutentionMe_PictoCategorie.png" WHERE id = 5 AND nom="Manutention m&eacute;canique"';
			$wpdb->query($sql);
			$sql = 'UPDATE ' . TABLE_CATEGORIE_DANGER . ' SET photo = "medias/images/Pictos/categorieDangers/circulation_PictoCategorie.png" WHERE id = 6 AND nom="Circulation, d&eacute;placements"';
			$wpdb->query($sql);
			$sql = 'UPDATE ' . TABLE_CATEGORIE_DANGER . ' SET photo = "medias/images/Pictos/categorieDangers/effondrement_PictoCategorie.png" WHERE id = 7 AND nom="Effondrements, chute d\'objet"';
			$wpdb->query($sql);
			$sql = 'UPDATE ' . TABLE_CATEGORIE_DANGER . ' SET photo = "medias/images/Pictos/categorieDangers/machine_PictoCategorie.png" WHERE id = 8 AND nom="Machines et outils"';
			$wpdb->query($sql);
			$sql = 'UPDATE ' . TABLE_CATEGORIE_DANGER . ' SET photo = "medias/images/Pictos/categorieDangers/nuisances_PictoCategorie.png" WHERE id = 9 AND nom="Nuisances sonores"';
			$wpdb->query($sql);
			$sql = 'UPDATE ' . TABLE_CATEGORIE_DANGER . ' SET photo = "medias/images/Pictos/categorieDangers/produitsC_PictoCategorie.png" WHERE id = 10 AND nom="Produits chimiques, d&eacute;chets"';
			$wpdb->query($sql);
			$sql = 'UPDATE ' . TABLE_CATEGORIE_DANGER . ' SET photo = "medias/images/Pictos/categorieDangers/incendies_PictoCategorie.png" WHERE id = 11 AND nom="Incendie, explosion"';
			$wpdb->query($sql);
			$sql = 'UPDATE ' . TABLE_CATEGORIE_DANGER . ' SET photo = "medias/images/Pictos/categorieDangers/electricite_PictoCategorie.png" WHERE id = 12 AND nom="Electricit&eacute;"';
			$wpdb->query($sql);
			$sql = 'UPDATE ' . TABLE_CATEGORIE_DANGER . ' SET photo = "medias/images/Pictos/categorieDangers/eclairage_PictoCategorie.png" WHERE id = 13 AND nom="Eclairage"';
			$wpdb->query($sql);
			$sql = 'UPDATE ' . TABLE_CATEGORIE_DANGER . ' SET photo = "medias/images/Pictos/categorieDangers/travailEcran_PictoCategorie.png" WHERE id = 14 AND nom="Travail sur &eacute;cran"';
			$wpdb->query($sql);
			$sql = 'UPDATE ' . TABLE_CATEGORIE_DANGER . ' SET photo = "medias/images/Pictos/categorieDangers/climat_PictoCategorie.png" WHERE id = 15 AND nom="Ambiances climatiques"';
			$wpdb->query($sql);
			$sql = 'UPDATE ' . TABLE_CATEGORIE_DANGER . ' SET photo = "medias/images/Pictos/categorieDangers/manqueHygiene_PictoCategorie.png" WHERE id = 16 AND nom="Manque d\'hygi&egrave;ne"';
			$wpdb->query($sql);
			$sql = 'UPDATE ' . TABLE_CATEGORIE_DANGER . ' SET photo = "medias/images/Pictos/categorieDangers/societeExt_PictoCategorie.png" WHERE id = 17 AND nom="Soci&eacute;t&eacute; ext&eacute;rieure"';
			$wpdb->query($sql);
			$sql = 'UPDATE ' . TABLE_CATEGORIE_DANGER . ' SET photo = "medias/images/Pictos/categorieDangers/manqueFormation_PictoCategorie.png" WHERE id = 18 AND nom="Manque de formation";';
			$wpdb->query($sql);
			
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 2:
		{
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 3:
		{
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 4:
		{
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 5:
		{
			$sqlInsertion = 'INSERT INTO ' . TABLE_TACHE . ' (nom, limiteGauche, limiteDroite) VALUES ("Tache Racine", 0, 1);';
			$wpdb->query($sqlInsertion);
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 6:
		{
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 7:
		{
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 8:
		{
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 9:
		{
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 10:
		{
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 11:
		{
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 12:
		{
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 13:
		{
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 14:
		{
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 15:
		{
			$sql = "INSERT INTO " . TABLE_PHOTO . " (idDestination, tableDestination, photo, isMainPicture) VALUES ('2', '" . TABLE_CATEGORIE_DANGER . "', 'medias/images/Pictos/categorieDangers/chutePP_PictoCategorie.png', 'yes');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO . " (idDestination, tableDestination, photo, isMainPicture) VALUES ('3', '" . TABLE_CATEGORIE_DANGER . "', 'medias/images/Pictos/categorieDangers/chuteH_PictoCategorie.png', 'yes');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO . " (idDestination, tableDestination, photo, isMainPicture) VALUES ('4', '" . TABLE_CATEGORIE_DANGER . "', 'medias/images/Pictos/categorieDangers/manutentionMa_PictoCategorie.png', 'yes');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO . " (idDestination, tableDestination, photo, isMainPicture) VALUES ('5', '" . TABLE_CATEGORIE_DANGER . "', 'medias/images/Pictos/categorieDangers/manutentionMe_PictoCategorie.png', 'yes');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO . " (idDestination, tableDestination, photo, isMainPicture) VALUES ('6', '" . TABLE_CATEGORIE_DANGER . "', 'medias/images/Pictos/categorieDangers/circulation_PictoCategorie.png', 'yes');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO . " (idDestination, tableDestination, photo, isMainPicture) VALUES ('7', '" . TABLE_CATEGORIE_DANGER . "', 'medias/images/Pictos/categorieDangers/effondrement_PictoCategorie.png', 'yes');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO . " (idDestination, tableDestination, photo, isMainPicture) VALUES ('8', '" . TABLE_CATEGORIE_DANGER . "', 'medias/images/Pictos/categorieDangers/machine_PictoCategorie.png', 'yes');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO . " (idDestination, tableDestination, photo, isMainPicture) VALUES ('9', '" . TABLE_CATEGORIE_DANGER . "', 'medias/images/Pictos/categorieDangers/nuisances_PictoCategorie.png', 'yes');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO . " (idDestination, tableDestination, photo, isMainPicture) VALUES ('10', '" . TABLE_CATEGORIE_DANGER . "', 'medias/images/Pictos/categorieDangers/produitsC_PictoCategorie.png', 'yes');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO . " (idDestination, tableDestination, photo, isMainPicture) VALUES ('11', '" . TABLE_CATEGORIE_DANGER . "', 'medias/images/Pictos/categorieDangers/incendies_PictoCategorie.png', 'yes');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO . " (idDestination, tableDestination, photo, isMainPicture) VALUES ('12', '" . TABLE_CATEGORIE_DANGER . "', 'medias/images/Pictos/categorieDangers/electricite_PictoCategorie.png', 'yes')";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO . " (idDestination, tableDestination, photo, isMainPicture) VALUES ('13', '" . TABLE_CATEGORIE_DANGER . "', 'medias/images/Pictos/categorieDangers/eclairage_PictoCategorie.png', 'yes');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO . " (idDestination, tableDestination, photo, isMainPicture) VALUES ('14', '" . TABLE_CATEGORIE_DANGER . "', 'medias/images/Pictos/categorieDangers/travailEcran_PictoCategorie.png', 'yes');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO . " (idDestination, tableDestination, photo, isMainPicture) VALUES ('15', '" . TABLE_CATEGORIE_DANGER . "', 'medias/images/Pictos/categorieDangers/climat_PictoCategorie.png', 'yes');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO . " (idDestination, tableDestination, photo, isMainPicture) VALUES ('16', '" . TABLE_CATEGORIE_DANGER . "', 'medias/images/Pictos/categorieDangers/manqueHygiene_PictoCategorie.png', 'yes')";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO . " (idDestination, tableDestination, photo, isMainPicture) VALUES ('17', '" . TABLE_CATEGORIE_DANGER . "', 'medias/images/Pictos/categorieDangers/societeExt_PictoCategorie.png', 'yes');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO . " (idDestination, tableDestination, photo, isMainPicture) VALUES ('18', '" . TABLE_CATEGORIE_DANGER . "', 'medias/images/Pictos/categorieDangers/manqueFormation_PictoCategorie.png', 'yes');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO . " (idDestination, tableDestination, photo, isMainPicture) VALUES ('19', '" . TABLE_CATEGORIE_DANGER . "', 'medias/images/Pictos/categorieDangers/autre_PictoCategorie.png', 'yes');";
			$wpdb->query($sql);
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 16:
		{
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 17:
		{
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, isMainPicture, idDestination, tableDestination, photo) VALUES('', 'valid', 'yes', 1, '" . TABLE_METHODE . "', 'medias/uploads/wp_eva__methode/1/tabcoeff.gif');";
			$wpdb->query($sql);
			$sql = "UPDATE " . TABLE_DUER . " SET groupesUtilisateursAffectes = groupesUtilisateurs ";
			$wpdb->query($sql);
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 18:
		{
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 19:
		{
			$sql = "UPDATE " . TABLE_AVOIR_VALEUR . " SET idEvaluateur = '1' ";
			$wpdb->query($sql);

			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 20:
		{
			$sql = "UPDATE " . TABLE_AVOIR_VALEUR . " SET Status = 'Moderated' WHERE Status != 'Valid' ";
			$wpdb->query($sql);

			//Add options for task and sub task responsible
			//DELETE FROM VERSION 44

			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 21:
		{
			//Add options
			//DELETE FROM VERSION 44

			$sql = "SELECT GROUP_CONCAT( id ) as LIST FROM " . TABLE_AVOIR_VALEUR . " GROUP BY id_risque, DATE";
			$listToUpdate = $wpdb->get_results($sql);
			foreach($listToUpdate as $listID)
			{
				$sql = "SELECT MAX(id_evaluation) + 1 AS newId FROM " . TABLE_AVOIR_VALEUR;
				$newId = $wpdb->get_row($sql);

				$sql = $wpdb->prepare("UPDATE " . TABLE_AVOIR_VALEUR . " SET id_evaluation = '%d' WHERE id IN (" . $listID->LIST . ")", $newId->newId);
				$wpdb->query($sql);
			}

			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 22:
		{
			$sql = "UPDATE  " . TABLE_VALEUR_ETALON . " SET niveauSeuil = NULL;";
			$wpdb->query($sql);
			$sql = "UPDATE  " . TABLE_VALEUR_ETALON . " SET niveauSeuil = 1 WHERE valeur = 0;";
			$wpdb->query($sql);
			$sql = "UPDATE  " . TABLE_VALEUR_ETALON . " SET niveauSeuil = 2 WHERE valeur = 48;";
			$wpdb->query($sql);
			$sql = "UPDATE  " . TABLE_VALEUR_ETALON . " SET niveauSeuil = 3 WHERE valeur = 51;";
			$wpdb->query($sql);
			$sql = "UPDATE  " . TABLE_VALEUR_ETALON . " SET niveauSeuil = 4 WHERE valeur = 80;";
			$wpdb->query($sql);

			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 23:
		{
			//Add option for advanced correctiv actions
			//DELETE FROM VERSION 44

			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 24:
		{
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 25:
		{
			$sql = $wpdb->prepare("INSERT INTO " . TABLE_LIAISON_USER_ELEMENT . " SELECT '', status, date, 1, '', 0, id_user, id_element, table_element FROM " . TABLE_LIAISON_USER_EVALUATION);
			$wpdb->query($sql);

			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 26:
		{
			/*	Move the directory containing the different models	*/
			if(is_dir(EVA_MODELES_PLUGIN_OLD_DIR) && !is_dir(EVA_MODELES_PLUGIN_DIR))
			{
				rename(EVA_MODELES_PLUGIN_OLD_DIR, EVA_MODELES_PLUGIN_DIR);
			}
			
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 27:
		{
			$sql = "INSERT INTO " . TABLE_GED_DOCUMENTS . " (id, status, dateCreation, idCreateur, dateSuppression, idSuppresseur, id_element, table_element, categorie, nom, chemin) VALUES('', 'valid', NOW(), 1, '0000-00-00 00:00:00', 0, 0, 'all', 'document_unique', 'modeleDefaut.odt', 'medias/uploads/modeles/documentUnique/');";
			$wpdb->query($sql);

			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 28:
		{
			//Add option for advanced risk
			//DELETE FROM VERSION 44

			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}		
		case 29:
		{
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " SELECT '', status, isMainPicture, id, idDestination, tableDestination FROM " . TABLE_PHOTO . ";";
			$wpdb->query($sql);
			$sql = "ALTER TABLE " . TABLE_PHOTO . " DROP isMainPicture,  DROP idDestination, DROP tableDestination;;";
			$wpdb->query($sql);

			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 30:
		{
			$sql = "UPDATE " . TABLE_LIAISON_USER_ELEMENT . " SET table_element = '" . TABLE_UNITE_TRAVAIL . "_evaluation' WHERE table_element='" . TABLE_UNITE_TRAVAIL . "' ;";
			$wpdb->query($sql);
			$sql = "UPDATE " . TABLE_LIAISON_USER_ELEMENT . " SET table_element = '" . TABLE_GROUPEMENT . "_evaluation' WHERE table_element='" . TABLE_GROUPEMENT . "' ;";
			$wpdb->query($sql);

			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 31:
		{
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 32:
		{
			//Change option table field definition
			//DELETE FROM VERSION 44

			//Add options for correctiv actions
			//DELETE FROM VERSION 44

			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 33:
		{
			$sql = "UPDATE " . TABLE_TACHE . " SET ProgressionStatus = 'notStarted' WHERE avancement = '0' ";
			$wpdb->query($sql);

			$sql = "UPDATE " . TABLE_ACTIVITE . " SET ProgressionStatus = 'notStarted' WHERE avancement = '0' ";
			$wpdb->query($sql);

			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 34:
		{
			$sql = "INSERT INTO " . TABLE_GED_DOCUMENTS . " (id, status, dateCreation, idCreateur, dateSuppression, idSuppresseur, id_element, table_element, categorie, nom, chemin) VALUES('', 'valid', NOW(), 1, '0000-00-00 00:00:00', 0, 0, 'all', 'fiche_de_poste', 'modeleDefaut.odt', 'medias/uploads/modeles/ficheDePoste/');";
			$wpdb->query($sql);

			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 35:
		{
			$sql = "UPDATE " . TABLE_GED_DOCUMENTS . " SET chemin = REPLACE(chemin, 'medias/uploads/', 'uploads/');";
			$wpdb->query($sql);
			$sql = "UPDATE " . TABLE_GED_DOCUMENTS . " SET chemin = REPLACE(chemin, 'medias/results/', 'results/');";
			$wpdb->query($sql);

			$sql = "UPDATE " . TABLE_PHOTO . " SET photo = REPLACE(photo, 'medias/uploads/', 'uploads/');";
			$wpdb->query($sql);
			$sql = "UPDATE " . TABLE_PHOTO . " SET photo = REPLACE(photo, 'medias/results/', 'results/');";
			$wpdb->query($sql);

			eva_tools::make_recursiv_dir(EVA_GENERATED_DOC_DIR);
			/*	Move the directory containing the different models	*/
			if(is_dir(EVA_UPLOADS_PLUGIN_OLD_DIR) && !is_dir(EVA_UPLOADS_PLUGIN_DIR))
			{
				eva_tools::copyEntireDirectory(EVA_UPLOADS_PLUGIN_OLD_DIR, EVA_UPLOADS_PLUGIN_DIR);
			}
			/*	Move the directory containing the different models	*/
			if(is_dir(EVA_RESULTATS_PLUGIN_OLD_DIR) && !is_dir(EVA_RESULTATS_PLUGIN_DIR))
			{
				eva_tools::copyEntireDirectory(EVA_RESULTATS_PLUGIN_OLD_DIR, EVA_RESULTATS_PLUGIN_DIR);
			}

			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 36:
		{
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
			$digiriskOptions['creation_sous_tache_preconisation'] = 'non';
			$digiriskOptions['export_only_priority_task'] = 'oui';
			$digiriskOptions['export_tasks'] = 'non';
			$digiriskOptions['taille_photo_poste_fiche_de_poste'] = '8';
			//Add option for the work unit sheet picture size
			//DELETE FROM VERSION 44

			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}		
		case 37:
		{
			$sql = "UPDATE " . TABLE_GED_DOCUMENTS . " SET parDefaut = 'oui' WHERE id_element = '0' AND table_element = 'all';";
			$wpdb->query($sql);

			require_once(EVA_LIB_PLUGIN_DIR . 'gestionDocumentaire/gestionDoc.class.php');
			$sql = "UPDATE " . TABLE_FP . " SET id_model = '" . eva_gestionDoc::getDefaultDocument('fiche_de_poste') . "';";
			$wpdb->query($sql);

			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 38:
		{
			$sql = "INSERT INTO " . TABLE_CATEGORIE_PRECONISATION . " (id, status, creation_date, nom) VALUES ('1', 'valid', NOW(), 'obligation');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_CATEGORIE_PRECONISATION . " (id, status, creation_date, nom) VALUES ('2', 'valid', NOW(), 'interdiction');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_CATEGORIE_PRECONISATION . " (id, status, creation_date, nom) VALUES ('3', 'valid', NOW(), 'recommandation');";
			$wpdb->query($sql);

			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 39:
		{
			/*	Add a new recommandation category representing PPE (EPI)	*/
			$sql = "INSERT INTO " . TABLE_CATEGORIE_PRECONISATION . " (id, status, creation_date, nom, impressionRecommandation, tailleimpressionRecommandation) VALUES ('4', 'valid', NOW(), '&eacute;quipements de protection individuelle', 'pictureonly', '2');";
			$wpdb->query($sql);

			/*	Add a new recommandation category representing Recommandation	*/
			$sql = "INSERT INTO " . TABLE_CATEGORIE_PRECONISATION . " (id, status, creation_date, nom) VALUES ('5', 'valid', NOW(), 'recommandations');";
			$wpdb->query($sql);

			/*	Add the default picture for each recommandation categories	*/
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/obligations/obligationGenerale_s.png');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', 1, '" . TABLE_CATEGORIE_PRECONISATION . "');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/interdictions/interdictionGenerale_s.png');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', 2, '" . TABLE_CATEGORIE_PRECONISATION . "');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/avertissements/dangerGeneral_s.png');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', 3, '" . TABLE_CATEGORIE_PRECONISATION . "');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/obligations/preconisationEPI_s.png');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', 4, '" . TABLE_CATEGORIE_PRECONISATION . "');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/recommandations/recommandationsGenerale_s.png');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', 5, '" . TABLE_CATEGORIE_PRECONISATION . "');";
			$wpdb->query($sql);

			{/*	Add the different basic epi obligation	*/
			$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '1', NOW(), 'Divers obligation');";
			$wpdb->query($sql);
			$lastRecommandation = $wpdb->insert_id;
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/obligations/obligationGenerale_s.png');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
			$wpdb->query($sql);

			$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '4', NOW(), 'Divers protection individuelle');";
			$wpdb->query($sql);
			$lastRecommandation = $wpdb->insert_id;
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/obligations/preconisationEPI_s.png');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
			$wpdb->query($sql);

			$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '4', NOW(), 'Protection obligatoire de la vue');";
			$wpdb->query($sql);
			$lastRecommandation = $wpdb->insert_id;
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/obligations/obligationVue_s.png');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
			$wpdb->query($sql);

			$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '4', NOW(), 'Protection obligatoire de la t&ecirc;te');";
			$wpdb->query($sql);
			$lastRecommandation = $wpdb->insert_id;
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/obligations/obligationTete_s.png');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
			$wpdb->query($sql);

			$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '4', NOW(), 'Protection obligatoire de l\'ou&iuml;e');";
			$wpdb->query($sql);
			$lastRecommandation = $wpdb->insert_id;
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/obligations/obligationOuie_s.png');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
			$wpdb->query($sql);

			$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '4', NOW(), 'Protection obligatoire des voies respiratoires');";
			$wpdb->query($sql);
			$lastRecommandation = $wpdb->insert_id;
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/obligations/obligationVoiesRespiratoires_s.png');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
			$wpdb->query($sql);

			$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '4', NOW(), 'Protection obligatoire des pieds');";
			$wpdb->query($sql);
			$lastRecommandation = $wpdb->insert_id;
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/obligations/obligationPieds_s.png');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
			$wpdb->query($sql);

			$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '4', NOW(), 'Protection obligatoire des mains');";
			$wpdb->query($sql);
			$lastRecommandation = $wpdb->insert_id;
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/obligations/obligationMains_s.png');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
			$wpdb->query($sql);

			$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '4', NOW(), 'Protection obligatoire du corps');";
			$wpdb->query($sql);
			$lastRecommandation = $wpdb->insert_id;
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/obligations/obligationCorps_s.png');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
			$wpdb->query($sql);

			$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '4', NOW(), 'Protection obligatoire de la figure');";
			$wpdb->query($sql);
			$lastRecommandation = $wpdb->insert_id;
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/obligations/obligationFigure_s.png');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
			$wpdb->query($sql);

			$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '4', NOW(), 'Protection individuelle obligatoire contre les chutes');";
			$wpdb->query($sql);
			$lastRecommandation = $wpdb->insert_id;
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/obligations/obligationChute_s.png');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
			$wpdb->query($sql);

			$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '4', NOW(), 'Protection obligatoire pour pi&eacute;tons');";
			$wpdb->query($sql);
			$lastRecommandation = $wpdb->insert_id;
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/obligations/obligationPietons_s.png');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
			$wpdb->query($sql);
			}

			{/*	Add the different basic prohibition	*/
			$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '2', NOW(), 'Divers interdiction');";
			$wpdb->query($sql);
			$lastRecommandation = $wpdb->insert_id;
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/interdictions/interdictionGenerale_s.png');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
			$wpdb->query($sql);

			$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '2', NOW(), 'D&eacute;fense de fumer');";
			$wpdb->query($sql);
			$lastRecommandation = $wpdb->insert_id;
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/interdictions/interdictionFumer_s.png');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
			$wpdb->query($sql);

			$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '2', NOW(), 'Flamme nue interdite et d&eacute;fense de fumer');";
			$wpdb->query($sql);
			$lastRecommandation = $wpdb->insert_id;
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/interdictions/interdictionFlammeNue_s.png');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
			$wpdb->query($sql);

			$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '2', NOW(), 'Interdit aux pi&eacute;tons');";
			$wpdb->query($sql);
			$lastRecommandation = $wpdb->insert_id;
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/interdictions/interdictionPietons_s.png');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
			$wpdb->query($sql);

			$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '2', NOW(), 'D&eacute;fense d\'&eacute;teindre avec de l\'eau');";
			$wpdb->query($sql);
			$lastRecommandation = $wpdb->insert_id;
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/interdictions/interdictionEteindreAvecEau_s.png');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
			$wpdb->query($sql);

			$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '2', NOW(), 'Eau non potable');";
			$wpdb->query($sql);
			$lastRecommandation = $wpdb->insert_id;
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/interdictions/interdictionEauNonPotable_s.png');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
			$wpdb->query($sql);

			$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '2', NOW(), 'Entr&eacute;e interdite aux personnes non autoris&eacute;es');";
			$wpdb->query($sql);
			$lastRecommandation = $wpdb->insert_id;
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/interdictions/interdictionPersonnesNonAutorisees_s.png');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
			$wpdb->query($sql);

			$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '2', NOW(), 'Interdit aux v&eacute;hicules de manutention');";
			$wpdb->query($sql);
			$lastRecommandation = $wpdb->insert_id;
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/interdictions/interdictionVehiculesManutention_s.png');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
			$wpdb->query($sql);

			$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '2', NOW(), 'Ne pas toucher');";
			$wpdb->query($sql);
			$lastRecommandation = $wpdb->insert_id;
			$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/interdictions/interdictionToucher_s.png');";
			$wpdb->query($sql);
			$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
			$wpdb->query($sql);
			}

			{/*	Add the different basic warning	*/
				$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '3', NOW(), 'Divers danger');";
				$wpdb->query($sql);
				$lastRecommandation = $wpdb->insert_id;
				$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/avertissements/dangerGeneral_s.png');";
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
				$wpdb->query($sql);

				$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '3', NOW(), 'Emplacement o&ugrave; une atmosph&egrave;re explosible peut se pr&eacute;senter');";
				$wpdb->query($sql);
				$lastRecommandation = $wpdb->insert_id;
				$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/avertissements/dangerEX_s.png');";
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
				$wpdb->query($sql);

				$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '3', NOW(), 'Mati&egrave;res inflammables ou haute temp&eacute;rature');";
				$wpdb->query($sql);
				$lastRecommandation = $wpdb->insert_id;
				$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/avertissements/dangerMatieresInflammables_s.png');";
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
				$wpdb->query($sql);

				$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '3', NOW(), 'Mati&egrave;res explosives
Risque d'explosion');";
				$wpdb->query($sql);
				$lastRecommandation = $wpdb->insert_id;
				$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/avertissements/dangerMatieresExplosives_s.png');";
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
				$wpdb->query($sql);

				$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '3', NOW(), 'Mati&egrave;res toxiques');";
				$wpdb->query($sql);
				$lastRecommandation = $wpdb->insert_id;
				$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/avertissements/dangerMatieresToxiques_s.png');";
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
				$wpdb->query($sql);

				$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '3', NOW(), 'Mati&egrave;res corrosives');";
				$wpdb->query($sql);
				$lastRecommandation = $wpdb->insert_id;
				$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/avertissements/dangerMatieresCorrosives_s.png');";
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
				$wpdb->query($sql);

				$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '3', NOW(), 'Mati&egrave;res radioactives
radiations ionisantes');";
				$wpdb->query($sql);
				$lastRecommandation = $wpdb->insert_id;
				$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/avertissements/dangerRayonnementIonisantes_s.png');";
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
				$wpdb->query($sql);

				$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '3', NOW(), 'Charges suspendues');";
				$wpdb->query($sql);
				$lastRecommandation = $wpdb->insert_id;
				$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/avertissements/dangerChargesSuspendues_s.png');";
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
				$wpdb->query($sql);

				$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '3', NOW(), 'V&eacute;hicules de manutention');";
				$wpdb->query($sql);
				$lastRecommandation = $wpdb->insert_id;
				$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/avertissements/dangerVehiculeManutention_s.png');";
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
				$wpdb->query($sql);

				$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '3', NOW(), 'Danger &eacute;lectrique');";
				$wpdb->query($sql);
				$lastRecommandation = $wpdb->insert_id;
				$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/avertissements/dangerElectrique_s.png');";
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
				$wpdb->query($sql);

				$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '3', NOW(), 'Rayonnement laser');";
				$wpdb->query($sql);
				$lastRecommandation = $wpdb->insert_id;
				$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/avertissements/dangerRayonnementLaser_s.png');";
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
				$wpdb->query($sql);

				$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '3', NOW(), 'Mati&egrave;res comburantes');";
				$wpdb->query($sql);
				$lastRecommandation = $wpdb->insert_id;
				$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/avertissements/dangerMatieresComburantes_s.png');";
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
				$wpdb->query($sql);

				$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '3', NOW(), 'Radiations non ionisantes');";
				$wpdb->query($sql);
				$lastRecommandation = $wpdb->insert_id;
				$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/avertissements/dangerRayonnementNonIonisantes_s.png');";
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
				$wpdb->query($sql);

				$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '3', NOW(), 'Champ magn&eacute;tique
important');";
				$wpdb->query($sql);
				$lastRecommandation = $wpdb->insert_id;
				$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/avertissements/dangerChampMagnetiqueImportant_s.png');";
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
				$wpdb->query($sql);

				$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '3', NOW(), 'Tr&eacute;buchement');";
				$wpdb->query($sql);
				$lastRecommandation = $wpdb->insert_id;
				$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/avertissements/dangerTrebuchement_s.png');";
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
				$wpdb->query($sql);

				$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '3', NOW(), 'Chute avec d&eacute;nivellation');";
				$wpdb->query($sql);
				$lastRecommandation = $wpdb->insert_id;
				$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/avertissements/dangerChuteDenivele_s.png');";
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
				$wpdb->query($sql);

				$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '3', NOW(), 'Risque biologique');";
				$wpdb->query($sql);
				$lastRecommandation = $wpdb->insert_id;
				$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/avertissements/dangerRisqueBiologique_s.png');";
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
				$wpdb->query($sql);

				$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '3', NOW(), 'Basse temp&eacute;rature');";
				$wpdb->query($sql);
				$lastRecommandation = $wpdb->insert_id;
				$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/avertissements/dangerBasseTemperature_s.png');";
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
				$wpdb->query($sql);

				$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '3', NOW(), 'Mati&egrave;res nocives
ou irritantes');";
				$wpdb->query($sql);
				$lastRecommandation = $wpdb->insert_id;
				$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/avertissements/dangerMatieresNocivesIrritantes_s.png');";
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
				$wpdb->query($sql);
			}

			{/*	Add the different basic recommandation	*/
				$sql = "INSERT INTO " . TABLE_PRECONISATION . " (id, status, id_categorie_preconisation, creation_date, nom) VALUES ('', 'valid', '5', NOW(), 'Divers recommandations');";
				$wpdb->query($sql);
				$lastRecommandation = $wpdb->insert_id;
				$sql = "INSERT INTO " . TABLE_PHOTO . " (id, status, photo) VALUES ('', 'valid', 'medias/images/Pictos/preconisations/recommandations/recommandationsGenerale_s.png');";
				$wpdb->query($sql);
				$sql = "INSERT INTO " . TABLE_PHOTO_LIAISON . " (id, status, isMainPicture, idPhoto, idElement, tableElement) VALUES ('', 'valid', 'yes', '" . $wpdb->insert_id . "', '" . $lastRecommandation . "', '" . TABLE_PRECONISATION . "');";
				$wpdb->query($sql);
			}

			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 40:
		{
			//Add option recommandation efficiency activation
			//DELETE FROM VERSION 44

			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 41:
		{
			{/*	Replace the old ppe management with the new one	*/
				$sql = "SELECT * FROM " . TABLE_UTILISE_EPI;
				$epi_utilise_results = $wpdb->get_results($sql);

				$query = "  ";
				foreach($epi_utilise_results as $epi_utilise)
				{
					switch($epi_utilise->ppeId)
					{
						case 1:
							$newEpiId = 5;
						break;
						case 2:
							$newEpiId = 4;
						break;
						case 3:
							$newEpiId = 7;
						break;
						case 4:
							$newEpiId = 9;
						break;
						case 5:
							$newEpiId = 8;
						break;
						case 6:
							$newEpiId = 11;
						break;
						case 7:
							$newEpiId = 3;
						break;
						case 8:
							$newEpiId = 6;
						break;
					}
					$query .= "('', 'valid', '" . $newEpiId . "', '', '" . $epi_utilise->elementId . "', '" . $epi_utilise->elementTable . "', ''), ";
				}

				$query = trim(substr($query, 0, -2));
				if($query != "")
				{
					$query = "INSERT INTO " . TABLE_LIAISON_PRECONISATION_ELEMENT . " (id, status, id_preconisation, efficacite, id_element, table_element, commentaire) VALUES " . $query;
					$wpdb->query($query);
				}
			}

			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 42:
		{
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 43:
		{
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}		
		case 44:
		{/*	Transfer user group db table into new scheme		*/
			if(($wpdb->get_var("show tables like '" . TABLE_EVA_USER_GROUP . "'") == TABLE_EVA_USER_GROUP) && ($wpdb->get_var("show tables like '" . TABLE_EVA_EVALUATOR_GROUP . "'") == TABLE_EVA_EVALUATOR_GROUP))
			{/*	Transfert des anciens groupes dans la nouvelle table	*/
				/*	Groupes d'employé	*/
				$query = $wpdb->prepare("SELECT * FROM " . TABLE_EVA_USER_GROUP);
				$employeeGroups = $wpdb->get_results($query);
				$subQuery = "  ";
				foreach($employeeGroups as $employeeGroup)
				{
					$subQuery .= "('', '" . $wpdb->escape($employeeGroup->user_group_id) . "', '" . $wpdb->escape($employeeGroup->user_group_status) . "', 'employee', NOW(), 1, '', '', '" . $wpdb->escape($employeeGroup->user_group_name) . "', '" . $wpdb->escape($employeeGroup->user_group_description) . "'), ";
				}
				/*	Groupes	d'evaluateur	*/
				$query = $wpdb->prepare("SELECT * FROM " . TABLE_EVA_EVALUATOR_GROUP);
				$employeeGroups = $wpdb->get_results($query);
				foreach($employeeGroups as $employeeGroup)
				{
					$subQuery .= "('', '" . $wpdb->escape($employeeGroup->evaluator_group_id) . "', '" . $wpdb->escape($employeeGroup->evaluator_group_status) . "', 'evaluator', '" . $wpdb->escape($employeeGroup->evaluator_group_creation_date) . "', '" . $wpdb->escape($employeeGroup->evaluator_group_creation_user_id) . "', '" . $wpdb->escape($employeeGroup->evaluator_group_deletion_date) . "', '" . $wpdb->escape($employeeGroup->evaluator_deletion_user_id) . "', '" . $wpdb->escape($employeeGroup->evaluator_group_name) . "', '" . $wpdb->escape($employeeGroup->evaluator_group_description) . "'), ";
				}
				/*	Transfert dans la nouvelle table	*/
				$subQuery = trim(substr($subQuery, 0, -2));
				if($subQuery != "")
				{
					$query = 
						"INSERT INTO " . DIGI_DBT_USER_GROUP . " 
							(id, old_id, status, group_type, creation_date, creation_user_id, deletion_date, deletion_user_id, name, description) 
						VALUES 
							" . $subQuery;
					$wpdb->query($query);
				}

				/*	Transfert les tables non utilisées vers la "trash" section	*/
				$query = $wpdb->prepare("RENAME TABLE " . TABLE_EVA_USER_GROUP . " TO " . TRASH_DIGI_DBT_USER_GROUP);
				$wpdb->query($query);
				$query = $wpdb->prepare("RENAME TABLE " . TABLE_EVA_EVALUATOR_GROUP . " TO " . TRASH_DIGI_DBT_EVALUATOR_GROUP);
				$wpdb->query($query);
			}

			if(($wpdb->get_var("show tables like '" . TABLE_LIAISON_USER_GROUPS . "'") == TABLE_LIAISON_USER_GROUPS) && ($wpdb->get_var("show tables like '" . TABLE_EVA_EVALUATOR_GROUP_BIND . "'") == TABLE_EVA_EVALUATOR_GROUP_BIND))
			{/*	Transfert la liaison des anciens groupes vers les nouveaux	*/
				/*	Groupes d'employé	*/
				$query = $wpdb->prepare("SELECT * FROM " . TABLE_LIAISON_USER_GROUPS);
				$employeeGroupsLink = $wpdb->get_results($query);
				$subQuery = "  ";
				foreach($employeeGroupsLink as $employeeGroupLink)
				{
					$query = $wpdb->prepare("SELECT id FROM " . DIGI_DBT_USER_GROUP . " WHERE old_id = %d AND group_type = %s", $employeeGroupLink->id_group, 'employee');
					$newGroupId = $wpdb->get_row($query);
					$subQuery .= "('', '" . $wpdb->escape($employeeGroupLink->Status) . "', '" . $wpdb->escape($employeeGroupLink->date) . "', 1, '', '', '" . $wpdb->escape($newGroupId->id) . "', '" . $wpdb->escape($employeeGroupLink->id_element) . "', '" . $wpdb->escape($employeeGroupLink->table_element) . "_employee'), ";
				}
				/*	Groupes d'evaluateurs	*/
				$query = $wpdb->prepare("SELECT * FROM " . TABLE_EVA_EVALUATOR_GROUP_BIND);
				$evaluatorsGroupsLink = $wpdb->get_results($query);
				foreach($evaluatorsGroupsLink as $evaluatorsGroupLink)
				{
					$query = $wpdb->prepare("SELECT id FROM " . DIGI_DBT_USER_GROUP . " WHERE old_id = %d AND group_type = %s", $evaluatorsGroupLink->id_group, 'evaluator');
					$newGroupId = $wpdb->get_row($query);
					$subQuery .= "('', '" . $wpdb->escape($evaluatorsGroupLink->Status) . "', '" . $wpdb->escape($evaluatorsGroupLink->dateAffectation) . "', '" . $wpdb->escape($evaluatorsGroupLink->affectationUserId) . "', '" . $wpdb->escape($evaluatorsGroupLink->dateDesaffectation) . "', '" . $wpdb->escape($evaluatorsGroupLink->desaffectationUserId) . "', '" . $wpdb->escape($newGroupId->id) . "', '" . $wpdb->escape($evaluatorsGroupLink->id_element) . "', '" . $wpdb->escape($evaluatorsGroupLink->table_element) . "_evaluator'), ";
				}
				/*	Transfert dans la nouvelle table	*/
				$subQuery = trim(substr($subQuery, 0, -2));
				if($subQuery != "")
				{
					$query = 
						"INSERT INTO " . DIGI_DBT_LIAISON_USER_GROUP . " 
							(id, status, date_affectation, id_attributeur, date_desAffectation, id_desAttributeur, id_group, id_element, table_element) 
						VALUES 
							" . $subQuery;
					$wpdb->query($query);
				}

				/*	Transfert les tables non utilisées vers la "trash" section	*/
				$query = $wpdb->prepare("RENAME TABLE " . TABLE_LIAISON_USER_GROUPS . " TO " . TRASH_DIGI_DBT_LIAISON_USER_GROUPS);
				$wpdb->query($query);
				$query = $wpdb->prepare("RENAME TABLE " . TABLE_EVA_EVALUATOR_GROUP_BIND . " TO " . TRASH_DIGI_DBT_EVALUATOR_GROUP_BIND);
				$wpdb->query($query);
			}

			if(($wpdb->get_var("show tables like '" . TABLE_EVA_USER_GROUP_DETAILS . "'") == TABLE_EVA_USER_GROUP_DETAILS) && ($wpdb->get_var("show tables like '" . TABLE_EVA_EVALUATOR_GROUP_DETAILS . "'") == TABLE_EVA_EVALUATOR_GROUP_DETAILS))
			{/*	Transfert des utilisateurs vers la table de liaison utilisateur element	*/
				/*	Groupes d'employé	*/
				$query = $wpdb->prepare("SELECT * FROM " . TABLE_EVA_USER_GROUP_DETAILS);
				$employeeGroupsDetail = $wpdb->get_results($query);
				$subQuery = "  ";
				foreach($employeeGroupsDetail as $employeeGroupDetail)
				{
					$query = $wpdb->prepare("SELECT id FROM " . DIGI_DBT_USER_GROUP . " WHERE old_id = %d AND group_type = %s", $employeeGroupDetail->user_group_id, 'employee');
					$newGroupId = $wpdb->get_row($query);
					$subQuery .= "('', 'valid', NOW(), 1, '', '', '" . $wpdb->escape($employeeGroupDetail->user_id) . "', '" . $wpdb->escape($newGroupId->id) . "', '" . DIGI_DBT_USER_GROUP . "'), ";
				}
				/*	Groupes d'evaluateurs	*/
				$query = $wpdb->prepare("SELECT * FROM " . TABLE_EVA_EVALUATOR_GROUP_DETAILS);
				$evaluatorsGroupsDetail = $wpdb->get_results($query);
				foreach($evaluatorsGroupsDetail as $evaluatorsGroupDetail)
				{
					$query = $wpdb->prepare("SELECT id FROM " . DIGI_DBT_USER_GROUP . " WHERE old_id = %d AND group_type = %s", $evaluatorsGroupDetail->evaluator_group_id, 'evaluator');
					$newGroupId = $wpdb->get_row($query);
					$subQuery .= "('', '" . $wpdb->escape($evaluatorsGroupDetail->Status) . "', '" . $wpdb->escape($evaluatorsGroupDetail->dateEntree) . "', '" . $wpdb->escape($evaluatorsGroupDetail->affectationUserId) . "', '" . $wpdb->escape($evaluatorsGroupDetail->dateSortie) . "', '" . $wpdb->escape($evaluatorsGroupDetail->desaffectationUserId) . "', '" . $wpdb->escape($evaluatorsGroupDetail->user_id) . "', '" . $wpdb->escape($newGroupId->id) . "', '" . DIGI_DBT_USER_GROUP . "'), ";
				}
				/*	Transfert dans la nouvelle table	*/
				$subQuery = trim(substr($subQuery, 0, -2));
				if($subQuery != "")
				{
					$query = 
						"INSERT INTO " . TABLE_LIAISON_USER_ELEMENT . " 
							(id, status, date_affectation, id_attributeur, date_desAffectation, id_desAttributeur, id_user, id_element, table_element) 
						VALUES 
							" . $subQuery;
					$wpdb->query($query);
				}

				/*	Transfert les tables non utilisées vers la "trash" section	*/
				$query = $wpdb->prepare("RENAME TABLE " . TABLE_EVA_USER_GROUP_DETAILS . " TO " . TRASH_DIGI_DBT_USER_GROUP_DETAILS);
				$wpdb->query($query);
				$query = $wpdb->prepare("RENAME TABLE " . TABLE_EVA_EVALUATOR_GROUP_DETAILS . " TO " . TRASH_DIGI_DBT_EVALUATOR_GROUP_DETAILS);
				$wpdb->query($query);
			}

			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 45:
		{

			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 46:
		{/*	User permission	initialisation	*/

			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 47:
		{

			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}	
		case 48:
		{
			$query = $wpdb->prepare(
				"UPDATE " . TABLE_CATEGORIE_PRECONISATION . " SET nom = 'avertissements' WHERE id='3' ");
			$wpdb->query($query);
			$query = $wpdb->prepare(
				"UPDATE " . TABLE_CATEGORIE_PRECONISATION . " SET nom = 'obligations' WHERE id='1' ");
			$wpdb->query($query);
			$query = $wpdb->prepare(
				"UPDATE " . TABLE_CATEGORIE_PRECONISATION . " SET nom = 'interdictions' WHERE id='2' ");
			$wpdb->query($query);

			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 49:
		{
			$sql = "INSERT INTO " . TABLE_GED_DOCUMENTS . " (id, status, parDefaut, dateCreation, idCreateur, dateSuppression, idSuppresseur, id_element, table_element, categorie, nom, chemin) VALUES('', 'valid', 'oui', NOW(), 1, '0000-00-00 00:00:00', 0, 0, 'all', 'fiche_de_groupement', 'modeleDefaut_groupement.odt', 'uploads/modeles/ficheDeGroupement/');";
			$wpdb->query($sql);

			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}	
		case 50:
		{
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}	
		case 51:
		{
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 52:
		{
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}
		case 53:
		{
			
			digirisk_options::updateDbOption('base_evarisk', (digirisk_options::getDbOption('base_evarisk') + 1));
			break;
		}	
	}

}