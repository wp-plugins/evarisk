<?php

class suivi_activite
{

	function formulaireAjoutSuivi($tableElement, $idElement)
	{
		$idBouttonEnregistrer = 'saveActionFollow';
		$scriptEnregistrement = 
			'<script type="text/javascript">
				$(document).ready(function() {				
					$("#' . $idBouttonEnregistrer . '").click(function() {
						$("#load' . $idBouttonEnregistrer . '").html(\'<img src="' . PICTO_LOADING_ROUND . '" />\');
						$("#bttn' . $idBouttonEnregistrer . '").hide();
						$("#load' . $idBouttonEnregistrer . '").show();

						$("#load' . $tableElement . $idElement . '").load("' . EVA_INC_PLUGIN_URL . 'ajax.php", {
							"post": "true", 
							"table": "' . TABLE_ACTIVITE_SUIVI . '",
							"act": "save",
							"idElement": "' . $idElement . '",
							"tableElement": "' . $tableElement . '",
							"commentaire": $("#commentaire' . $tableElement . $idElement . '").val()
						});
					});
				});
			</script>';
		
		
		return 
		'<table summary="" cellpadding="0" cellspacing="0" style="width:100%;" >
			<tr>
				<td style="width:80%;" >' . __('Commentaire', 'evarisk') . '</td>
			</tr>
			<tr>
				<td >' . EvaDisplayInput::afficherInput('textarea', 'commentaire' . $tableElement . $idElement, '', '', '', 'commentaire', false, true, 3) . '</td>
				<td rowspan="2" ><div id="bttn' . $idBouttonEnregistrer . '" >' . EvaDisplayInput::afficherInput('button', $idBouttonEnregistrer, __('Enregistrer', 'evarisk'), null, '', $idBouttonEnregistrer, false, true, '', 'button-primary alignright', '', '', $scriptEnregistrement) . '</div><div style="float:right;display:none;" id="load' . $idBouttonEnregistrer . '" ></div></td>
			</tr>
		</table>' . suivi_activite::tableauSuiviActivite($tableElement, $idElement);
	}

	function saveSuiviActivite($tableElement, $idElement, $commentaire)
	{
		global $wpdb;
		global $current_user;
		$result = array();

		$query = $wpdb->prepare(
			"INSERT INTO " . TABLE_ACTIVITE_SUIVI . "
				(id, status, date, id_user, id_element, table_element, commentaire)
			VALUES
				('', 'valid', NOW(), '" . $current_user->ID . "', '" . $idElement . "', '" . $tableElement . "', '" . $commentaire . "')"
		);

		if($wpdb->query($query))
		{
			$result = 'ok';
		}
		else
		{
			$result = 'error';
		}

		return $result;
	}

	function getSuiviActivite($tableElement, $idElement)
	{
		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT * 
			FROM " . TABLE_ACTIVITE_SUIVI . "
			WHERE id_element = '%s'
				AND table_element = '%s' 
			ORDER BY date DESC",
			$idElement, $tableElement
		);

		return $wpdb->get_results($query);
	}

	function tableauSuiviActivite($tableElement, $idElement)
	{
		$listSuivi = suivi_activite::getSuiviActivite($tableElement, $idElement);
		$outputSuivi = '';

		if(count($listSuivi) > 0)
		{
			$idTable = 'tableauSuiviModification' . $tableElement . $idElement;
			$titres = array( __('Suivi modifications', 'evarisk') );
			$classes = array('');

			unset($lignesDeValeurs);
			foreach($listSuivi as $suivi)
			{
				unset($valeurs);
				$user_info = get_userdata($suivi->id_user);
				$user_lastname = '';
				if( (isset($user_info->user_lastname) && ($user_info->user_lastname != '')) )
				{
					$user_lastname = $user_info->user_lastname;
				}
				$user_firstname = $user_info->user_nicename;
				if( (isset($user_info->user_firstname) && ($user_info->user_firstname != '')) )
				{
					$user_firstname = $user_info->user_firstname;
				}

				$valeurs[] = array('value' => sprintf(__('Le <b>%s</b>, <b>%s</b> dit <i>%s</i>', 'evarsik'), eva_tools::transformeDate($suivi->date, 0, 0, 0, true), $user_lastname . ' ' . $user_firstname, $suivi->commentaire));
				$lignesDeValeurs[] = $valeurs;
				$idLignes[] = $tableElement . $idElement . 'suiviModification';
			}

			$scriptTableauSuiviModification = 
			'<script type="text/javascript">
				$(document).ready(function() {
					$("#' . $idTable . '").dataTable({
						"bInfo": false
					});
					$("#' . $idTable . '").children("thead").remove();
					$("#' . $idTable . '").children("tfoot").remove();
				});
			</script>';

			$outputSuivi .= '<hr/>' . evaDisplayDesign::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $scriptTableauSuiviModification);
		}

		return $outputSuivi;
	}

}