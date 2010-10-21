<?php

include_once(EVA_CONFIG);
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
require_once(EVA_LIB_PLUGIN_DIR . 'eva_tools.class.php' );
require_once(EVA_LIB_PLUGIN_DIR . 'methode/methodeEvaluation.class.php' );

function getMethodsName($saufMethode = '')
{
	
	unset($operateur);$operateur;
	$methodes = MethodeEvaluation::getMethods();
	foreach($methodes as $methode)
	{
		if($methode->nom != $saufMethode)
		{
			$tab_methodes[]=$methode->nom;
		}
	}
	return $tab_methodes;
}

if (( isset($_POST['act']) && $_POST['act'] == 'addVariable') || (isset($_POST['AjouterVariable']) && $_POST['AjouterVariable'] == 'Ajouter'))
{
	global $wpdb;
	
	$nom = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['newvarname']));
	$min = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['newvarmin']));
	$max = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['newvarmax']));
	$alterValues = $_POST['newVariableAlterValue'];
	$annotation = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['newvarannotation']));
	$sql = "INSERT INTO " . TABLE_VARIABLE . " (`nom`, `min`, `max`, `annotation`, `Status`) VALUES ('" . $nom . "', '" . $min . "', '" . $max . "', '" . $annotation . "', 'Valid')";
	$wpdb->query($sql);
	$idVariable = $wpdb->insert_id;
	
	$date = date('Y-m-d H:i:s');
	foreach($alterValues as $value => $alterValue)
	{
		$alterValue = eva_tools::IsValid_Variable($alterValue);
		if($alterValue != '')
		{
			$sql = "INSERT INTO " . TABLE_VALEUR_ALTERNATIVE . " (`id_variable`, `valeur`, `valeurAlternative`, `date`, `Status`) VALUES ('" . mysql_real_escape_string($idVariable) . "', '" . mysql_real_escape_string($value) . "', '" . mysql_real_escape_string($alterValue) . "', '" . mysql_real_escape_string($date) . "', 'Valid')";
			$wpdb->query($sql);
		}
	}
	
	$_POST['act'] = 'edit';
}
if (isset($_POST['act']) && $_POST['act'] == 'import')
{
	if (is_uploaded_file($_FILES['import']['tmp_name'])) 
	{
	   echo '<span id="message" class="updated fade below-h2">
			<strong><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'veille-reponse.gif" alt="response" style="vertical-align:middle;" />&nbsp;Le fichier a bien &eacute;t&eacute; charg&eacute;.</strong>
			</span>';
		$temp = preg_replace('/\n/i', '[retourALaLigne]', file_get_contents ($_FILES['import']['tmp_name']));
		$equivalences = explode('[retourALaLigne]', $temp);
		foreach($equivalences as $equivalence)
		{
			$valEchelon = preg_replace('/([\d]+[,\.]?[\d]*);.+/','$1',$equivalence);
			$valMethode = preg_replace('/[\d]+[,\.]?[\d]*;([\d]+[,\.]?[\d]*)/','$1',$equivalence);
			$valMethode = preg_replace('/([\d]+)([,\.]?)([\d]*)/','$1.$3',$valMethode);
			if(substr($valMethode,strlen($valMethode)-2, 1) == '.')
			{
				$valMethode = substr($valMethode,0,strlen($valMethode)-2);
			}
			if(substr(trim($valMethode), strlen(trim($valMethode)) - 1) != ';')
			$_POST['equvalenceEchelon'][$valEchelon] = $valMethode;
		}
	} 
	else 
	{
	   echo '<span id="message" class="updated fade below-h2">
			<strong><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'veille-no-reponse.gif" alt="noresponse" style="vertical-align:middle;" />&nbsp;Le fichier n\'a pas pu &ecirc;tre charg&eacute;.</strong>
			</span>';
	}	
	$_POST['act'] = 'add';
}
if ((isset($_POST['act']) && $_POST['act'] == 'edit') OR (isset($_POST['act']) && $_POST['act'] == 'add') OR (isset($_POST['AjouterNouvelleMethode']) && $_POST['AjouterNouvelleMethode'] == 'Ajouter'))
{
	include_once('methodeEvaluation-new.php');
	displayMethodForm();
}
else
{	
	if((isset($_POST['act']) && $_POST['act'] == 'save') || (isset($_POST['save']) && $_POST['save'] == 'Enregister'))
	{
		global $wpdb;
		
		$nom = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['nom_methode']));
		$sql = "INSERT INTO " . TABLE_METHODE . " (`nom`,`Status`) VALUES ('" . $nom . "', 'Valid')";
		$wpdb->query($sql);
		$_POST['act'] = 'update';
		
		$t= TABLE_METHODE;
		$methode =  $wpdb->get_row( "SELECT * FROM {$t} WHERE nom='" . $nom . "'");
		$id_methode= $methode->id;
		$_POST['id'] = $id_methode;
	}
	if(isset($_POST['act']) && $_POST['act'] == 'update')
	{
		global $wpdb;

		$heureEnregistrement = date('Y-m-d H:i:s');
		$id_methode = $_POST['id'];
		$nom = mysql_real_escape_string(eva_tools::IsValid_Variable($_POST['nom_methode']));
		$sql = "UPDATE " . TABLE_METHODE . " SET `nom`='" . $nom . "' WHERE `id`='" . $_POST['id'] . "'";
		$wpdb->query($sql);
		$ordre=0;
		$sql = "UPDATE " . TABLE_AVOIR_VARIABLE . " SET Status='Deleted' WHERE id_methode=" . $id_methode;
		$wpdb->query($sql);
		$sql = "UPDATE " . TABLE_AVOIR_OPERATEUR . " SET Status='Deleted' WHERE id_methode=" . $id_methode;
		$wpdb->query($sql);
		$sql = "UPDATE " . TABLE_EQUIVALENCE_ETALON . " SET Status='Deleted' WHERE id_methode=" . $id_methode;
		$wpdb->query($sql);
		foreach($_POST['var'] as $temp)
		{
			$ordre = $ordre + 1;
			$nom = mysql_real_escape_string(eva_tools::IsValid_Variable($temp));
			$t= TABLE_VARIABLE;
			$variable =  $wpdb->get_row( "SELECT * FROM " . $t . " WHERE nom='" . $nom . "'");
			$id_variable= $variable->id;
			$sql = "INSERT INTO " . TABLE_AVOIR_VARIABLE . " (`id_methode`, `id_variable`,`ordre`, `date`, `Status`) VALUES (" . $id_methode . ", " . $id_variable . ", " . $ordre .", '" . $heureEnregistrement . "', 'Valid')";
			$wpdb->query($sql);
		}
		$ordre=0;
		if($_POST['op']!=null)
		{
			foreach($_POST['op'] as $temp)
			{
				$ordre = $ordre + 1;
				$operateur = str_replace(' ','',mysql_real_escape_string(eva_tools::IsValid_Variable($temp)));
				$sql = "INSERT INTO " . TABLE_AVOIR_OPERATEUR . " (`id_methode`, `operateur`,`ordre`, `date`, `Status`) VALUES (" . $id_methode . ", '" . $operateur . "', " . $ordre .", '" . $heureEnregistrement . "', 'Valid')";
				$wpdb->query($sql);
			}
		}
		foreach($_POST['equivalent'] as $valeurEtalon => $valeurMaxMethode)
		{
			if($valeurMaxMethode!='')
			{
				$sql = "INSERT INTO " . TABLE_EQUIVALENCE_ETALON . " (`id_methode`, `id_valeur_etalon`, `date`, `valeurMaxMethode`, `Status`) VALUES (" . $id_methode . ", " . $valeurEtalon . ", '" . $heureEnregistrement . "', '" . $valeurMaxMethode . "', 'Valid')";
				$wpdb->query($sql);
			}
		}
	}
	if((isset($_POST['action']) && $_POST['action'] == 'delete') || (isset($_POST['action2']) && $_POST['action2'] == 'delete'))
	{
		global $wpdb;
		
		foreach($_POST['method'] as $temp)
		{
			$id = mysql_real_escape_string(eva_tools::IsValid_Variable($temp));
			$sql = "UPDATE " . TABLE_METHODE . " SET `Status`= 'Deleted' WHERE`id` = ('" . $id . "')";
			$wpdb->query($sql);
		}
	}
// Code très légèrement adapté de wordpress
?>

<script type="text/javascript">
<!--
$(document).ready(function() {
	$('#table_methode').dataTable({"sPaginationType": 'full_numbers', "bAutoWidth": false, "aoColumns": [
{ "bSortable": false },
{ "bSortable": true, "sType": "html" },
{ "bSortable": false }],
"aaSorting": [[1,'asc']]});
} )
//-->
</script>
<div class="wrap">
	<div class="icon32"><img alt="evarisk Icon" src=<?php echo EVA_METHODE_ICON ?> title="evariskIcon"/></div>
	<form method="POST" id="methode-filter" name="form">
		<h2>Methode d'evaluation <input type="submit" class="button add-new-h2" onclick="javascript:document.getElementById('act').value='add'; document.forms.form.submit(); return false;" value="Ajouter" name="AjouterNouvelleMethode"/> </h2>
		<input type="hidden" value="" name="act" id="act"/>
		<input type="hidden" value="" name="id" id="id"/>
		
		<div class="tablenav">
			<div class="alignleft actions">
				<select name="action">
					<option selected="selected" value="-1">Actions globales</option>
					<option value="delete">Supprimer</option>
				</select>
				<input type="submit" class="button-secondary action" id="doaction" name="doaction" value="Appliquer"/>
			</div>
			
			<div class="clear"></div>
		</div>
		
		<div class="clear"></div>
		
		<table id="table_methode" cellspacing="0" class="widefat post fixed">
			<thead>
				<tr>
					<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"/></th>
					<th style="" class="manage-column column-me-nom" id="name" scope="col">Nom</th>
					<th style="" class="manage-column column-me-formule" id="formule" scope="col">Formule</th>
				</tr>
			</thead>
		
			<tfoot>
				<tr>
					<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"/></th>
					<th style="" class="manage-column column-me-nom" id="name" scope="col">Nom</th>
					<th style="" class="manage-column column-me-formule" id="formule" scope="col">Formule</th>
				</tr>
			</tfoot>
		
			<tbody>
				<?php 
				//Partie spécifique gérant les lignes de la table
				$search = "`Status`='Valid'";
				$s = (isset($_POST['s']))?$_POST['s']:null;
				if($s != null)
				{
					$search = " AND nom like '%" . mysql_real_escape_string($s) . "%'";
				}
				$methodes_evaluation = MethodeEvaluation::getMethods($search);
				$i=0;
				foreach ($methodes_evaluation as $methode_evaluation ) :		
					$formule = MethodeEvaluation::getFormule($methode_evaluation->id);
					?>
					<tr id="ut-<?php echo $methode_evaluation->id . '"'; if(($i%2) == 0) {echo ' class="alternate"';}?> valign="top">
						<td class="check-column" scope="row">
							<input type="checkbox" value="<?php echo $methode_evaluation->id; ?>" name="method[]"/>
						</th>
						<td><strong><a onclick="javascript:document.getElementById('act').value='edit';document.getElementById('id').value='<?php echo $methode_evaluation->id; ?>';document.forms.form.submit();"><?php echo stripcslashes($methode_evaluation->nom); ?> </a></strong></td>
						<td><strong><?php echo $formule ?><strong></td>
					</tr>
					<?php
					$i++;
				endforeach;
				//Fin de la partie spécifique gérant les lignes de la table
				?>
			</tbody>
		</table>
		<div class="tablenav">
			<div class="alignleft actions">
				<select name="action2">
					<option selected="selected" value="-1">Actions globales</option>
					<option value="delete">Supprimer</option>
				</select>
				<input type="submit" class="button-secondary action" id="doaction2" name="doaction2" value="Appliquer"/>
			</div>
			
			<br class="clear"></br>
		</div>
	</form>
	<div id="ajax-response"></div>
	<div class="clear"></div>
</div>
<?php } ?>