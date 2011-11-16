<?php
/*
 * @version v5.0
 */
 
 
//Postbox definition
$postBoxTitle = __('Informations G&eacute;n&eacute;rales', 'evarisk');
$postBoxId = 'postBoxGeneralInformation';
$postBoxCallbackFunction = 'getActivityGeneralInformationPostBoxBody';
add_meta_box($postBoxId, $postBoxTitle, array('EvaActivity', 'sub_task_creation_form'), PAGE_HOOK_EVARISK_ACTIVITE, 'rightSide', 'default');

?>