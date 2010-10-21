<?php
/**
*	The different utilities to manage entities
*
*	@package 		Evarisk
*	@subpackage eav_entity
* @author			Evarisk team <contact@evarisk.com>
*/

class eav_entity
{

	/**
	*	Get the entity type identifier
	*
	*	@param string $entityCode The entity code(name) defined when entity were create
	*
	*	@return integer $entityId The identifier for the entity
	*/
	function getEntityInformation($entitySearchValue, $entitySearchKey = 'entity_type_code' ,$colName = 'entity_type_id')
	{
		global $wpdb;
		$entityId = 0;

		$query = 
			$wpdb->prepare("SELECT " . $colName . " 
			FROM " . TABLE_ENTITY . "
			WHERE " . $entitySearchKey . " = '%s' ",
			$entitySearchValue);
		$result = $wpdb->get_col($query);
		
		if(count($result) > 0)
			$entityId = $result[0];
		
		return $entityId;
	}

}