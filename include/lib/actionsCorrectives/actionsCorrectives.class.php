<?php

class actionsCorrectives
{

	function actionsCorrectivesMainPage()
	{
		$messageInfo = '';

		$_POST['table'] = TABLE_TACHE;
		$titrePage = __("Actions Correctives", 'evarisk');
		$icone = PICTO_LTL_ACTION;
		$titreIcone = "Icone actions correctives";
		$altIcon = "Icone AC";
		$titreFilAriane= __("Actions correctives", 'evarisk');
		if(!isset($_POST['affichage']))
		{
			$_POST['affichage'] = "affichageListe";
		}
		include_once(EVA_LIB_PLUGIN_DIR . 'classicalPage.php' );	
		// On enl�ve le choix de l'affichage
		?>
		<script type="text/javascript">
			evarisk(document).ready(function(){
				evarisk('#choixAffichage').hide();
			});
		</script>
		<?php
			if(isset($_GET['elt']) && ($_GET['elt'] != ''))
			{
				echo
					'<script type="text/javascript">
						evarisk(document).ready(function(){
							setTimeout(function(){
								evarisk("#' . $_GET['elt'] . '").click();
							},3000);
						})
					</script>';
			}
	}

}