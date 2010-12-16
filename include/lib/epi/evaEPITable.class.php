<?php
/**
 * This class allows to work on set of PPE (equivalent to multiple rows in data base) 
 *
 * @author Evarisk
 * @version v5.0
 */
require_once(EVA_LIB_PLUGIN_DIR . 'epi/evaBaseEPI.class.php');

class EvaEPITable extends EvaBaseEPI
{	
	/**
	  * Returns an array of EvaEPI with all PPEs which have the same name, the same path and the same status that the calling object is they are not empty.
	  * @return array Array with the PPE objects.
	  */
	function getEPIs ()
	{
		global $wpdb;
		
		{// Variables cleaning
			$name = eva_tools::IsValid_Variable($this->getName());
			$path = eva_tools::IsValid_Variable($this->getPath());
			$status = eva_tools::IsValid_Variable($this->getStatus());
		}
		
		$where = "1";
		{// Where condition creation
			if($name != "")
			{
				$where = $where . " AND name = '" . $name . "'";
			}
			if($path != "")
			{
				$where = $where . " AND path = '" . $path . "'";
			}
			if($status != "")
			{
				$where = $where . " AND status = '" . $status . "'";
			}
		}

		$sql = "
			SELECT * 
			FROM " . TABLE_EPI . " 
			WHERE " . $where;
		$wpdbEPIs = $wpdb->get_results($sql);
		
		unset($EPIs);
		foreach($wpdbEPIs as $wpdbEPI)
		{
			$EPIs[$wpdbEPI->id] = new EvaBaseEPI($wpdbEPI->id, $wpdbEPI->name, $wpdbEPI->path, $wpdbEPI->status);
		}
		return $EPIs;
	}
	
	/**
	  * Returns an array of EvaEPI with all PPE which have the same name, the same path and the same status that the calling object is they are not empty.
	  * @return array Array with the PPE objects.
	  */
	function getEPIsAndUse ($elementTable, $elementIdentifier)
	{
		global $wpdb;
		
		{// Variables cleaning
			$name = eva_tools::IsValid_Variable($this->getName());
			$path = eva_tools::IsValid_Variable($this->getPath());
			$status = eva_tools::IsValid_Variable($this->getStatus());
			$elementTable = eva_tools::IsValid_Variable($elementTable);
			$elementIdentifier = eva_tools::IsValid_Variable($elementIdentifier);
		}
		
		$where = "1";
		{// Where condition creation
			if($name != "")
			{
				$where = $where . " AND name = '" . $name . "'";
			}
			if($path != "")
			{
				$where = $where . " AND path = '" . $path . "'";
			}
			if($status != "")
			{
				$where = $where . " AND status = '" . $status . "'";
			}
		}

		$sql = "
			SELECT EPI.id AS id, EPI.name AS name, EPI.path AS path, EPI.status as status, utiliseEPI.PPEId as utilise
			FROM " . TABLE_EPI . " AS EPI
				LEFT JOIN " . TABLE_UTILISE_EPI . "  AS utiliseEPI ON (utiliseEPI.PPEId = EPI.id AND utiliseEPI.elementTable='" . $elementTable . "' AND  utiliseEPI.elementId=" . $elementIdentifier . ")
			WHERE " . $where;
		$wpdbEPIs = $wpdb->get_results($sql);
		
		unset($EPIs);
		foreach($wpdbEPIs as $wpdbEPI)
		{
			$EPIs[$wpdbEPI->id]['EPI'] = new EvaBaseEPI($wpdbEPI->id, $wpdbEPI->name, $wpdbEPI->path, $wpdbEPI->status);
			$EPIs[$wpdbEPI->id]['utilise'] = ($wpdbEPI->utilise == null) ? false : true; 
		}
		return $EPIs;
	}
}