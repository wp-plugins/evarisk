<?php
/**
 * 
 * @author Evarisk
 * @version v5.0
 */
include_once(EVA_CONFIG);
require_once(EVA_LIB_PLUGIN_DIR . 'methode/eva_variable.class.php');
require_once(EVA_LIB_PLUGIN_DIR . 'methode/eva_operateur.class.php');
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
require_once(EVA_LIB_PLUGIN_DIR . 'photo/evaPhoto.class.php' );

class MethodeEvaluation {
	
	/**
	 * @var Integer The method identifier
	 */
	var $id;
	/**
	 * @var String The method name
	 */
	var $name;
	
/*
 *	Constructeur et accesseurs
 */
	
	/**
	 * Constructor of the method class
	 * @param $id Integer The identifier to setI
	 * @param $name String The name to set
	 */
	function MethodeEvaluation($id = NULL, $name = '') {
		$this->id = $id;
		$this->name = $name;
	}
	
	/**
	 * Return the method identifier
	 * @return Integer The identifier
	 */
	function getId()
	{
		return $this->id;
	}
	/**
	 * Set the method identifier
	 * @param $id Integer The identifier to set
	 */
	function setId($id)
	{
		$this->id = $id;
	}
	/**
	 * Return the method name
	 * @return String The name
	 */
	function getName()
	{
		return $this->name;
	}
	/**
	 * Set the method name
	 * @param $name String The name to set
	 */
	function setName($name)
	{
		$this->name = $name;
	}
	
/*
 * Autres Methodes
 */
	function methodeEvaluationMainPage()
	{
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
					<strong><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'success_vs.png" alt="response" style="vertical-align:middle;" />&nbsp;Le fichier a bien &eacute;t&eacute; charg&eacute;.</strong>
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
					<strong><img src="' . EVA_IMG_ICONES_PLUGIN_URL . 'error_vs.png" alt="noresponse" style="vertical-align:middle;" />&nbsp;Le fichier n\'a pas pu &ecirc;tre charg&eacute;.</strong>
					</span>';
			}	
			$_POST['act'] = 'add';
		}
		if ( (current_user_can('digi_add_method') || current_user_can('digi_edit_method') || current_user_can('digi_view_detail_method')) && (isset($_POST['act']) && $_POST['act'] == 'edit') OR (isset($_POST['act']) && $_POST['act'] == 'add') OR (isset($_POST['AjouterNouvelleMethode']) && $_POST['AjouterNouvelleMethode'] == 'Ajouter'))
		{
			include_once(EVA_MODULES_PLUGIN_DIR . 'methode/methodeEvaluation-new.php');
			displayMethodForm();
		}
		elseif( (!current_user_can('digi_add_method') || !current_user_can('digi_edit_method') || !current_user_can('digi_view_detail_method')) && (isset($_POST['act']) && $_POST['act'] == 'edit') OR (isset($_POST['act']) && $_POST['act'] == 'add') OR (isset($_POST['AjouterNouvelleMethode']) && $_POST['AjouterNouvelleMethode'] == 'Ajouter'))
		{
			$actionResult = 'userNotAllowed';
		}
		else
		{
			if(current_user_can('digi_add_method') && (isset($_POST['act']) && $_POST['act'] == 'save') || (isset($_POST['save']) && $_POST['save'] == 'Enregister'))
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
			else
			{
				$actionResult = 'userNotAllowed';
			}
			if(current_user_can('digi_edit_method') && isset($_POST['act']) && $_POST['act'] == 'update')
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
			else
			{
				$actionResult = 'userNotAllowed';
			}
			if(current_user_can('digi_delete_method') && (isset($_POST['action']) && $_POST['action'] == 'delete') || (isset($_POST['action2']) && $_POST['action2'] == 'delete'))
			{
				global $wpdb;
				
				foreach($_POST['method'] as $temp)
				{
					$id = mysql_real_escape_string(eva_tools::IsValid_Variable($temp));
					$sql = "UPDATE " . TABLE_METHODE . " SET `Status`= 'Deleted' WHERE`id` = ('" . $id . "')";
					$wpdb->query($sql);
				}
			}
			else
			{
				$actionResult = 'userNotAllowed';
			}
		// Code très légèrement adapté de wordpress
		?>
		<script type="text/javascript">
			evarisk(document).ready(function(){
				evarisk('#table_methode').dataTable({
					"sPaginationType": 'full_numbers', 
					"bAutoWidth": false, 
					"bInfo": false,	
					"aoColumns": [
						{ "bSortable": false },
						{ "bSortable": true, "sType": "html" },
						{ "bSortable": false }
					],
					"aaSorting": [[1,'asc']],
					"oLanguage": {
						"sUrl": "<?php echo EVA_INC_PLUGIN_URL; ?>js/dataTable/jquery.dataTables.common_translation.txt",
						"sEmptyTable": "<?php echo __('Aucun risque trouv&eacute;', 'evarisk'); ?>",
						"sLengthMenu": "<?php echo __('Afficher _MENU_ risques', 'evarisk'); ?>",
						"sInfoEmpty": "<?php echo __('Aucun risque', 'evarisk'); ?>",
						"sZeroRecords": "<?php echo __('Aucun risque trouv&eacute;', 'evarisk'); ?>",
						"oPaginate": {
							"sFirst": "<?php echo __('Premi&eacute;re', 'evarisk'); ?>",
							"sLast": "<?php  echo __('Derni&egrave;re', 'evarisk'); ?>",
							"sNext": "<?php echo __('Suivante', 'evarisk'); ?>",
							"sPrevious": "<?php  echo __('Pr&eacute;c&eacute;dente', 'evarisk'); ?>"
						}
					}
				});
			});
		</script>
		<div class="wrap">
			<div class="icon32"><img alt="evarisk Icon" src=<?php echo EVA_METHODE_ICON ?> title="evariskIcon"/></div>
			<form method="POST" id="methode-filter" name="form">
				<h2>Methode d'evaluation <?php if(current_user_can('digi_add_method')){ ?><input type="submit" class="button add-new-h2" onclick="javascript:document.getElementById('act').value='add'; document.forms.form.submit(); return false;" value="Ajouter" name="AjouterNouvelleMethode"/><?php } ?></h2>
				<input type="hidden" value="" name="act" id="act"/>
				<input type="hidden" value="" name="id" id="id"/>
				
				<div class="tablenav">
					<div class="alignleft actions">
						<select name="action">
							<option selected="selected" value="-1">Actions globales</option>
							<?php if(current_user_can('digi_delete_method')){ ?><option value="delete">Supprimer</option><?php } ?>
						</select>
						<input type="submit" class="button-secondary action" id="doaction" name="doaction" value="Appliquer"/>
					</div>
<?php
				/*	Add trash	*/
				$main_option = get_option('digirisk_options');
				if(($main_option['digi_activ_trash'] == 'oui') && current_user_can('digi_view_method_trash'))
				{
?>
					<img class="trashPicture" src="<?php echo EVA_IMG_ICONES_PLUGIN_URL; ?>trash_vs.png" alt="Trash" title="<?php _e('Acc&eacute;der &agrave; la corbeille', 'evarisk'); ?>" />
					<div id="trashContainer" title="<?php _e('Liste des &eacute;l&eacute;ments supprim&eacute;s', 'evarisk'); ?>" >&nbsp;</div>
					<script type="text/javascript" >
						evarisk(document).ready(function(){
							evarisk("#trashContainer").dialog({
								autoOpen: false,
								modal: true,
								width: 800,
								height: 600,
								close: function(){
									evarisk(this).html("");
								}
							});
							evarisk(".trashPicture").click(function(){
								evarisk("#trashContainer").dialog("open");
								evarisk("#trashContainer").html(evarisk("#loadingImg").html());
								evarisk("#trashContainer").load("<?php echo EVA_INC_PLUGIN_URL; ?>ajax.php", 
								{
									"post": "true", 
									"tableProvenance": "<?php echo TABLE_METHODE ?>",
									"nom": "loadTrash"
								});
							});
						});
					</script>
<?php
				}
?>
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
								</td>
								<td><strong><a <?php if(current_user_can('digi_edit_method') || current_user_can('digi_view_detail_method')){ ?>onclick="javascript:document.getElementById('act').value='edit';document.getElementById('id').value='<?php echo $methode_evaluation->id; ?>';document.forms.form.submit();" <?php } else { ?> class="userForbiddenActionCursor"  <?php } ?>><?php echo stripcslashes($methode_evaluation->nom); ?> </a></strong></td>
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
							<?php if(current_user_can('digi_delete_method')){ ?><option value="delete">Supprimer</option><?php } ?>
						</select>
						<input type="submit" class="button-secondary action" id="doaction2" name="doaction2" value="Appliquer"/>
					</div>
					
					<br class="clear"></br>
				</div>
			</form>
			<div id="ajax-response"></div>
			<div class="clear"></div>
		</div>
		<?php }
	}

	static function getMethod($id)
	{
		global $wpdb;
		$id = (int) $id;
		$t = TABLE_METHODE;
		return $wpdb->get_row( "SELECT * FROM {$t} WHERE id = " . $id);
	}

	static function getMethods($where = "1", $order = "nom ASC") 
	{
		global $wpdb;
		$t = TABLE_METHODE;
		$resultat = $wpdb->get_results( "SELECT * FROM {$t} WHERE " . $where . " ORDER BY " . $order);
		return $resultat;
	}
	
	static function getAllVariables($where = "1", $order = "nom ASC")
	{
		global $wpdb;
		$resultat = eva_Variable::getVariables($where, $order);
		return $resultat;
	}
	
	static function getVariablesMethode($id_methode, $date=null)
	{
		global $wpdb;
		
		if($date==null)
		{
			$date=date('Y-m-d H:i:s');
		}
		$id_methode = (int) $id_methode;
		$tav = TABLE_AVOIR_VARIABLE;
		$tv =  TABLE_VARIABLE ;
		return $wpdb->get_results( "SELECT * 
			FROM " . $tv . ", " . $tav . " t1
			WHERE t1.id_methode=" . $id_methode . " 
			AND t1.date < '" . $date . "'
			AND NOT EXISTS
			(
				SELECT * 
				FROM " . $tav . " t2
				WHERE t2.id_methode=" . $id_methode . " 
				AND t2.date < '" . $date . "'
				AND t1.date < t2.date
			)
			AND id_variable=id 
			ORDER BY ordre ASC");
	}
	
	static function getDistinctVariablesMethode($id_methode, $date=null)
	{
		global $wpdb;
		
		if($date==null)
		{
			$date=date('Y-m-d H:i:s');
		}
		$id_methode = (int) $id_methode;
		$tav = TABLE_AVOIR_VARIABLE;
		$tv =  TABLE_VARIABLE ;
		return $wpdb->get_results( "
			SELECT DISTINCT(nom), id, min, max, annotation
			FROM " . $tv . ", " . $tav . " t1
			WHERE t1.id_methode=" . $id_methode . " 
			AND t1.date < '" . $date . "'
			AND NOT EXISTS
			(
				SELECT * 
				FROM " . $tav . " t2
				WHERE t2.id_methode=" . $id_methode . " 
				AND t2.date < '" . $date . "'
				AND t1.date < t2.date
			)
			AND id_variable=id 
			ORDER BY ordre ASC");
	}
	
	static function getOperateursMethode($id_methode, $date = null){
		global $wpdb;
		
		if($date==null){
			$date = "NOW()";
		}
		$id_methode = (int) $id_methode;
		$t = TABLE_AVOIR_OPERATEUR;
		$query = $wpdb->prepare("SELECT * 
				FROM " . $t . " t1
				WHERE t1.id_methode = %d
				AND t1.date < %s
				AND NOT EXISTS
				(
					SELECT * 
					FROM " . $t . " t2
					WHERE t2.id_methode = %d 
					AND t2.date < %s
					AND t1.date < t2.date
				)
				ORDER BY ordre ASC", $id_methode, $date, $id_methode, $date);
		return $wpdb->get_results($query);
	}
	
	static function getFormule($id, $date=null)
	{
		global $wpdb;
		
		if($date==null)
		{
			$date=date('Y-m-d H:i:s');
		}
		$id = (int) $id;
		$formule = '';
		$t = TABLE_AVOIR_VARIABLE;
		//on récupère les ids des variables
		$id_variables = $wpdb->get_results("
			SELECT * 
			FROM " . $t . " t1
			WHERE t1.id_methode=" . $id . " 
			AND t1.date < '" . $date . "'
			AND NOT EXISTS
			(
				SELECT * 
				FROM " . $t . " t2
				WHERE t2.id_methode=" . $id . " 
				AND t2.date < '" . $date . "'
				AND t1.date < t2.date
			)
			ORDER BY ordre ASC");
		$ordre=0;
		$table_var = TABLE_VARIABLE;
		$table_avoir_op = TABLE_AVOIR_OPERATEUR;
		//pour chaque id
		foreach($id_variables as $id_variable)
		{
			//on recupere la variable...
			$variable = $wpdb->get_row("SELECT * FROM " . $table_var . " WHERE id=" . $id_variable->id_variable);
			//et l'opérateur
			$operateur = $wpdb->get_row("
				SELECT * 
				FROM " . $table_avoir_op . " t1
				WHERE t1.id_methode=" . $id . " 
				AND t1.date < '" . $date . "'
				AND t1.ordre=" . $ordre . "
				AND NOT EXISTS
				(
					SELECT * 
					FROM " . $table_avoir_op . " t2
					WHERE t2.id_methode=" . $id . " 
					AND t2.date < '" . $date . "'
					AND t2.ordre=" . $ordre . "
					AND t1.date < t2.date
				)
				ORDER BY ordre ASC");
			//et on complète la formule
			$operateur = (!isset($operateur) OR $operateur == null)?'':$operateur->operateur;
			$formule = $formule . ' ' . $operateur . ' ' . $variable->nom;
			$ordre = $ordre + 1;
		}
		return $formule;
	}
	
	static function getEtalon()
	{
		global $wpdb;
		$table = TABLE_ETALON;
		$resultat = $wpdb->get_row( "SELECT * FROM " . $table);
		return $resultat;
	}
	
	static function getEquivalentEtalon($idMethode, $valeurEtalon, $date=null)
	{
		global $wpdb;
		
		if($date==null)
		{
			$date=date('Y-m-d H:i:s');
		}
		$table = TABLE_EQUIVALENCE_ETALON;
		$resultat = $wpdb->get_row("
			SELECT * 
			FROM " . $table . " t1
			WHERE t1.id_methode=" . $idMethode . " 
			AND t1.id_valeur_etalon=" . $valeurEtalon . " 
			AND t1.date < '" . $date . "'
			AND NOT EXISTS
			(
				SELECT * 
				FROM " . $table . " t2
				WHERE t2.id_methode=" . $idMethode . " 
				AND t1.id_valeur_etalon=" . $valeurEtalon . " 
				AND t2.date < '" . $date . "'
				AND t1.date < t2.date
			)");
		return $resultat;
	}


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

}