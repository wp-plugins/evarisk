<?php
$messageInfo = '';

$_POST['table'] = TABLE_TACHE;
$titrePage = __("Actions Correctives", 'evarisk');
$icone = EVA_ACTIONS_CORRECTIVES_ICON;
$titreIcone = "Icone actions correctives";
$altIcon = "Icone AC";
$titreFilAriane= __("Actions correctives", 'evarisk');
if(!isset($_POST['affichage']))
{
	$_POST['affichage'] = "affichageListe";
}
include_once(EVA_LIB_PLUGIN_DIR . 'classicalPage.php' );	
// On enlève le choix de l'affichage
?>
<script type="text/javascript">
  $(document).ready(function(){
    $('#choixAffichage').hide();
  });
</script>