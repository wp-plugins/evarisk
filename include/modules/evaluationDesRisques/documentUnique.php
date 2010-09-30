<?php
/*
 *
 * @author Evarisk
 * @version v5.0
 */
define('DOING_AJAX', true);
define('WP_ADMIN', true);
require_once('../../../../../../wp-load.php');
require_once(ABSPATH . 'wp-admin/includes/admin.php');
require_once('../../../evarisk.php');
require_once(EVA_LIB_PLUGIN_DIR . 'evaGoogleMaps.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayDesign.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayInput.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/documentUnique/documentUnique.class.php' );

// @header('Content-Type: application/pdf; charset=' . get_option('blog_charset'));
@header('Content-Type: text/html; charset=' . get_option('blog_charset'));

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" ><html>
<head>
	<title>EVARISK - Document Unique</title>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/jquery.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/lib.js"></script>
	<script type="text/javascript" src="http://www.google.com/jsapi"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/dataTable/jquery.dataTables.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/jQueryUI/js/jquery-ui-1.7.2.custom.min.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/jQueryUI/development-bundle/ui/i18n/jquery-ui-i18n.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/jQueryUI/development-bundle/ui/ui.gantt.min.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/treeTable/jquery.treeTable.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/galleriffic/jquery.galleriffic.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/galleriffic/jquery.opacityrollover.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/fieldSelection/jquery-fieldselection.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/uploadify/jquery.uploadify.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/fileUploader/fileuploader.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/users.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/role.js"></script>
	<script type="text/javascript" src="' . EVA_INC_PLUGIN_URL . 'js/eav.js"></script>
	<script type="text/javascript" src="' . EVA_PLUGIN_DIR . '../../../wp-admin/js/postbox.js"></script>
	<link rel="stylesheet" media="print, screen" type="text/css" href="' . EVA_INC_PLUGIN_URL . 'css/dataTable/demo_table_jui.css" />
	<link rel="stylesheet" media="print, screen" type="text/css" href="' . EVA_INC_PLUGIN_URL . 'css/jQueryUI/smoothness/jquery-ui-1.7.2.custom.css" />
	<link rel="stylesheet" media="print, screen" type="text/css" href="' . EVA_INC_PLUGIN_URL . 'css/eva.css" />
	<link rel="stylesheet" media="print, screen" type="text/css" href="' . EVA_INC_PLUGIN_URL . 'css/eav.css" />
	<link rel="stylesheet" media="print, screen" type="text/css" href="' . EVA_INC_PLUGIN_URL . 'css/documentUnique.css" />
	<link rel="stylesheet" media="print, screen" type="text/css" href="' . WP_PLUGIN_URL . '/../../wp-admin/load-styles.php?c=1&dir=ltr&load=global,wp-admin&ver=4198bec071152ccaf39ba26fd81dcd63" />
</head>
<body >' . documentUnique::generationDocumentUnique(TABLE_GROUPEMENT, $_GET['id'], 'html') . '</body></html>';