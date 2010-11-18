<?php
require_once(EVA_CONFIG);
require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'photo/evaPhoto.class.php');
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

switch($_POST['act'])
{
	case 'save' :
		$sql = "insert into wp_eva__photo (id, idDestination, tableDestination, photo) VALUES ('', '1', 'dec', 'matofo')";
		$wpdb->query($sql);
		break;
	case 'edit' :
		$tableElement = $_POST['tableElementPhoto'];
		$idElement = $_POST['idElementPhoto'];
		$photo = $_POST['photo'];
		$description = $_POST['description'];
		EvaPhoto::updatePhoto($tableElement, $idElement, $photo, $description);
		break;
	case 'delete':
		break;
}