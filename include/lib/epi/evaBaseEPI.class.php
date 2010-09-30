<?php
/**
 * Class to represent a PPE directly connect with the data base
 *
 * @author Evarisk
 * @version v5.0
 */
include_once(EVA_CONFIG);
class EvaBaseEPI
{
	/**
	 * @var int The PPE identifier
	 */
	var $id;
	/**
	 * @var string Name of the PP
	 */
	var $name;
	/**
	 * @var string Path to the image from the root of the plugin
	 */
	var $path;
	/**
	 * @var string The PPE status
	 */
	var $status;
	
/*
 *	Constructeur et accesseurs
 */
	/**
	 * Constructor of the PPE class
	 * @param int $id The identifier to set
	 * @param string $name The name to set
	 * @param string $path The path to set
	 * @param string $status The status to set
	 */
	function EvaBaseEPI($id = null, $name = '', $path = '', $status = 'Valid')
	{
		$this->id = $id;
		$this->name = $name;
		if($path != '')
			$this->path = EVA_HOME_URL . $path;
		$this->status = $status;
	}
	
	/**
	 * Returns the PPE identifier
	 * @return int The identifier
	 */
	function getId()
	{
		return $this->id;
	}
	
	/**
	 * Set the PPE identifier
	 * @param int $id The identifier to set
	 */
	function setId($id)
	{
		$this->id = $id;
	}
	
	/**
	 * Returns the PPE name
	 * @return string The name
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 * Set the PPE name
	 * @param string $firstLine The name to set
	 */
	function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * Returns the PPE path
	 * @return string The path
	 */
	function getPath()
	{
		return $this->path;
	}

	/**
	 * Set the PPE path
	 * @param string $path The path to set
	 */
	function setPath($path)
	{
		if(substr($path, 0, strlen(EVA_HOME_URL)) != EVA_HOME_URL)
			$this->path = EVA_HOME_URL . $path;
		$this->path = $path;
	}
	
	/**
	 * Returns the PPE status
	 * @return string The status
	 */
	function getStatus()
	{
		return $this->status;
	}

	/**
	 * Set the PPE status
	 * @param string $status The city to status
	 */
	function setStatus($status)
	{
		$this->status = $status;
	}
	
/*
 * Persistance
 */

	/**
	 * Save or update the PPE in data base
	 */
	function save()
	{
		global $wpdb;
		
		{//Variables cleaning
			$id = (int) eva_tools::IsValid_Variable($this->getId());
			$name = eva_tools::IsValid_Variable($this->getName());
			$path = eva_tools::IsValid_Variable($this->getPath());
			$status = eva_tools::IsValid_Variable($this->getStatus());
		}
		
		//Query creation
		if($id == 0)
		{// Insert in data base
			$sql = "INSERT INTO " . TABLE_EPI . " (`name`, `path`, `status`) VALUES ('" . mysql_real_escape_string($name) . "', '" . mysql_real_escape_string($path) . "', '" . mysql_real_escape_string($status) . "')";
		}
		else
		{//Update of the data base
			$sql = "UPDATE " . TABLE_EPI . " set `name`='" . mysql_real_escape_string($name) . "', `path`='" . mysql_real_escape_string($path) . "', `status`='" . mysql_real_escape_string($status) . "' WHERE `id`=" . mysql_real_escape_string($id);
		}
		
		//Query execution
		if($wpdb->query($sql))
		{//Their is no trouble
			$id = $wpdb->insert_id;
			if($this->getId() == null)
			{
				$this->setId($id);
			}
		}
		else
		{//Their is some troubles
			$this->setStatus("error");
		}
	}
	
	
	/**
	 * Load the PPE with identifier key
	 */
	function load()
	{
		global $wpdb;
		$id = (int) eva_tools::IsValid_Variable($this->getId());
		if($id != 0)
		{
			$wpdbEPI = $wpdb->get_row( "SELECT * FROM " . TABLE_EPI . " WHERE id = " . $id);
			
			if($wpdbEPI != null)
			{
				$this->setId($wpdbEPI->id);
				$this->setName($wpdbEPI->name);
				$this->setPath($wpdbEPI->path);
				$this->setStatus($wpdbEPI->status);
			}
		}
	}
}