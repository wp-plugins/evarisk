<?php
/**
* Include every file we need in the plugin.
*
*	It avoid to include files in every script and allows to make changes on the filename easily. And to know wich file is included and were it is located
* @author Evarisk <dev@evarisk.com>
* @version 5.1.2.9
* @package Digirisk
*/

require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php' );

require_once(EVA_LIB_PLUGIN_DIR . 'options.class.php' );

require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayDesign.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'evaDisplayInput.class.php' );

require_once(EVA_LIB_PLUGIN_DIR . 'database.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'form.class.php');

require_once(EVA_LIB_PLUGIN_DIR . 'dashboard/dashboard.class.php');

require_once(EVA_LIB_PLUGIN_DIR . 'evaluationDesRisques/evaluationDesRisques.class.php');

require_once(EVA_LIB_PLUGIN_DIR . 'actionsCorrectives/actionsCorrectives.class.php');

require_once(EVA_LIB_PLUGIN_DIR . 'methode/methodeEvaluation.class.php' );

require_once(EVA_LIB_PLUGIN_DIR . 'danger/danger.class.php' );

require_once(EVA_LIB_PLUGIN_DIR . 'users/evaUser.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'users/evaUserLinkElement.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'users/groups.class.php' );

require_once(EVA_LIB_PLUGIN_DIR . 'veilleReglementaire/veilleReglementaire.class.php' );

require_once(EVA_LIB_PLUGIN_DIR . 'evaRecommandation/evaRecommandation.class.php' );

require_once(EVA_LIB_PLUGIN_DIR . 'produits/produits.class.php' );