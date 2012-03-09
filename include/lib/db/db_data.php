<?php
/**
* Plugin database start content definition file.
* 
*	This file contains the different definitions for the database content.
* @author Evarisk <dev@evarisk.com>
* @version 5.1.4.9
* @package digirisk
* @subpackage librairies-db
*/

$digirisk_eav_content = array();
$digirisk_eav_content_update = array();
$digirisk_db_content_add = array();
$digirisk_db_content_update = array();
$digirisk_db_options_add = array();
$digirisk_db_options_update = array();
$digirisk_db_version = 0;

{/*	Version 0	*/ 	/*																TOOOOOOOOOOODOOOOOOOOOOOOOOOOOOOOO								*/
	$digirisk_db_version = 0;
	

	$digirisk_linked_content[$digirisk_db_version][][TABLE_METHODE] = array('nom' => __('Chute de plain-pied', 'evarisk'), 'dependant' => array(TABLE_PHOTO => array('medias/uploads/wp_eva__methode/1/tabcoeff.gif')));

	$digirisk_linked_content[$digirisk_db_version][][TABLE_CATEGORIE_DANGER] = array('nom' => __('Chute de plain-pied', 'evarisk'), 'dependant' => array(TABLE_DANGER => array('nom' => __('Divers chute de plain-pied' , 'evarisk')), TABLE_PHOTO => array('medias/images/Pictos/categorieDangers/chutePP_PictoCategorie.png')));
	$digirisk_linked_content[$digirisk_db_version][][TABLE_CATEGORIE_DANGER] = array('nom' => __('Chute de hauteur', 'evarisk'), 'dependant' => array(TABLE_DANGER => array('nom' => __('Divers chute de hauteur' , 'evarisk')), TABLE_PHOTO => array('medias/images/Pictos/categorieDangers/chuteH_PictoCategorie.png')));
	$digirisk_linked_content[$digirisk_db_version][][TABLE_CATEGORIE_DANGER] = array('nom' => __('Manutention manuelle', 'evarisk'), 'dependant' => array(TABLE_DANGER => array('nom' => __('Divers manutention manuelle' , 'evarisk')), TABLE_PHOTO => array('medias/images/Pictos/categorieDangers/manutentionMa_PictoCategorie.png')));
	$digirisk_linked_content[$digirisk_db_version][][TABLE_CATEGORIE_DANGER] = array('nom' => __('Manutention m&eacute;canique', 'evarisk'), 'dependant' => array(TABLE_DANGER => array('nom' => __('Divers manutention m&eacute;canique' , 'evarisk')), TABLE_PHOTO => array('medias/images/Pictos/categorieDangers/manutentionMe_PictoCategorie.png')));
	$digirisk_linked_content[$digirisk_db_version][][TABLE_CATEGORIE_DANGER] = array('nom' => __('Circulation, d&eacute;placements', 'evarisk'), 'dependant' => array(TABLE_DANGER => array('nom' => __('Divers circulation, d&eacute;placements' , 'evarisk')), TABLE_PHOTO => array('medias/images/Pictos/categorieDangers/circulation_PictoCategorie.png')));
	$digirisk_linked_content[$digirisk_db_version][][TABLE_CATEGORIE_DANGER] = array('nom' => __('Effondrements, chute d\'objet', 'evarisk'), 'dependant' => array(TABLE_DANGER => array('nom' => __('Divers effondrements, chute d\'objet' , 'evarisk')), TABLE_PHOTO => array('medias/images/Pictos/categorieDangers/effondrement_PictoCategorie.png')));
	$digirisk_linked_content[$digirisk_db_version][][TABLE_CATEGORIE_DANGER] = array('nom' => __('Machines et outils', 'evarisk'), 'dependant' => array(TABLE_DANGER => array('nom' => __('Divers machines et outils' , 'evarisk')), TABLE_PHOTO => array('medias/images/Pictos/categorieDangers/machine_PictoCategorie.png')));
	$digirisk_linked_content[$digirisk_db_version][][TABLE_CATEGORIE_DANGER] = array('nom' => __('Nuisances sonores', 'evarisk'), 'dependant' => array(TABLE_DANGER => array('nom' => __('Divers nuisances sonores' , 'evarisk')), TABLE_PHOTO => array('medias/images/Pictos/categorieDangers/nuisances_PictoCategorie.png')));
	$digirisk_linked_content[$digirisk_db_version][][TABLE_CATEGORIE_DANGER] = array('nom' => __('Produits chimiques, d&eacute;chets', 'evarisk'), 'dependant' => array(TABLE_DANGER => array('nom' => __('Divers produits chimiques, d&eacute;chets' , 'evarisk')), TABLE_PHOTO => array('medias/images/Pictos/categorieDangers/produitsC_PictoCategorie.png')));
	$digirisk_linked_content[$digirisk_db_version][][TABLE_CATEGORIE_DANGER] = array('nom' => __('Incendie, explosion', 'evarisk'), 'dependant' => array(TABLE_DANGER => array('nom' => __('Divers incendie, explosion' , 'evarisk')), TABLE_PHOTO => array('medias/images/Pictos/categorieDangers/incendies_PictoCategorie.png')));
	$digirisk_linked_content[$digirisk_db_version][][TABLE_CATEGORIE_DANGER] = array('nom' => __('Electricit&eacute;', 'evarisk'), 'dependant' => array(TABLE_DANGER => array('nom' => __('Divers electricit&eacute;' , 'evarisk')), TABLE_PHOTO => array('medias/images/Pictos/categorieDangers/electricite_PictoCategorie.png')));
	$digirisk_linked_content[$digirisk_db_version][][TABLE_CATEGORIE_DANGER] = array('nom' => __('Eclairage', 'evarisk'), 'dependant' => array(TABLE_DANGER => array('nom' => __('Divers eclairage' , 'evarisk')), TABLE_PHOTO => array('medias/images/Pictos/categorieDangers/eclairage_PictoCategorie.png')));
	$digirisk_linked_content[$digirisk_db_version][][TABLE_CATEGORIE_DANGER] = array('nom' => __('Travail sur &eacute;cran', 'evarisk'), 'dependant' => array(TABLE_DANGER => array('nom' => __('Divers travail sur &eacute;cran' , 'evarisk')), TABLE_PHOTO => array('medias/images/Pictos/categorieDangers/travailEcran_PictoCategorie.png')));
	$digirisk_linked_content[$digirisk_db_version][][TABLE_CATEGORIE_DANGER] = array('nom' => __('Ambiances climatiques', 'evarisk'), 'dependant' => array(TABLE_DANGER => array('nom' => __('Divers ambiances climatiques' , 'evarisk')), TABLE_PHOTO => array('medias/images/Pictos/categorieDangers/climat_PictoCategorie.png')));
	$digirisk_linked_content[$digirisk_db_version][][TABLE_CATEGORIE_DANGER] = array('nom' => __('Manque d\'hygi&egrave;ne', 'evarisk'), 'dependant' => array(TABLE_DANGER => array('nom' => __('Divers manque d\'hygi&egrave;ne' , 'evarisk')), TABLE_PHOTO => array('medias/images/Pictos/categorieDangers/manqueHygiene_PictoCategorie.png')));
	$digirisk_linked_content[$digirisk_db_version][][TABLE_CATEGORIE_DANGER] = array('nom' => __('Soci&eacute;t&eacute; ext&eacute;rieure', 'evarisk'), 'dependant' => array(TABLE_DANGER => array('nom' => __('Divers soci&eacute;t&eacute; ext&eacute;rieure' , 'evarisk')), TABLE_PHOTO => array('medias/images/Pictos/categorieDangers/societeExt_PictoCategorie.png')));
	$digirisk_linked_content[$digirisk_db_version][][TABLE_CATEGORIE_DANGER] = array('nom' => __('Manque de formation', 'evarisk'), 'dependant' => array(TABLE_DANGER => array('nom' => __('Divers manque de formation' , 'evarisk')), TABLE_PHOTO => array('medias/images/Pictos/categorieDangers/manqueFormation_PictoCategorie.png')));
	$digirisk_linked_content[$digirisk_db_version][][TABLE_CATEGORIE_DANGER] = array('nom' => __('Autres', 'evarisk'), 'dependant' => array(TABLE_DANGER => array('nom' => __('Divers autres' , 'evarisk')), TABLE_PHOTO => array('medias/images/Pictos/categorieDangers/autre_PictoCategorie.png')));

}

{/*	Version 5	*/
	$digirisk_db_version = 5;

	$digirisk_db_content_add[$digirisk_db_version][TABLE_TACHE][] = array('Status' => 'Valid', 'nom' => __('Tache Racine', 'evarisk'), 'limiteGauche' => 0, 'limiteDroite' => 1, 'firstInsert' => current_time('mysql', 0));
}

{/*	Version 15	*/
	$digirisk_db_version = 15;

	$digirisk_db_update[$digirisk_db_version][TABLE_PHOTO][] = "UPDATE " . TABLE_PHOTO . " SET status = S";
}