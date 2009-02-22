<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	database
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Table Class
 * 
 * This class implements the singleton design pattern. There will be many implementations 
 * of the table class. The table class is an abstract class so it will be used to implement 
 * specific database tables and each of the child instances will need to be a singleton.
 * 
 * @package		phpFrame
 * @subpackage 	database
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @abstract 
 */
abstract class table extends singleton {
	var $db=null;
	var $table_name=null;
	var $primary_key=null;
	var $cols=array();
	
	/**
	 * Contructor
	 * 
	 * @param	object	$db The database object (passed by reference).
	 * @param	string	$table_name The table name in the database.
	 * @param	string	$primary_key The column name of the table's primary key.
	 * @return	void
	 * @since 	1.0
	 */
	function __construct(&$db, $table_name, $primary_key) {
		$this->db =& $db;
		$this->table_name = $table_name;
		$this->primary_key = $primary_key;
		
		$this->getColumns();
	}
	
	/**
	 * Get columns for table in database and store column info in $this->cols.
	 * 
	 * @return	void
	 * @since 	1.0
	 */
	function getColumns() {
		$query = "SHOW COLUMNS FROM ".$this->table_name;
		$this->db->setQuery($query);
		$this->cols = $this->db->loadObjectList();
	}
	
	/**
	 * Load row by id and return row object.
	 * 
	 * @param	int	$id The row id.
	 * @return	object
	 * @since 	1.0
	 */
	function load($id) {
		$query = "SELECT * FROM ".$this->table_name." WHERE ".$this->primary_key." = '".$id."'";
		$this->db->setQuery($query);
		$row = $this->db->loadObject();
		foreach ($this->cols as $col) {
			$col_name = $col->Field;
			$col_value = $row->$col_name;
			$this->$col_name = $col_value;
		}
		
		return $row;
	}
	
	/**
	 * Store current row to database.
	 * 
	 * @return	void
	 * @since 	1.0
	 */
	function store() {
		$primary_key = $this->primary_key;
		
		if (!$this->rowExists($this->id)) {
			$query = "INSERT INTO ".$this->table_name." (";
			$i=0;
			foreach ($this->cols as $col) {
				if ($i>0) { 
					$query .= ", ";
				}
				$query .= $col->Field;
				$i++;
			}
			$query .= ") VALUES (";
			$i=0;
			foreach ($this->cols as $col) {
				if ($i>0) { 
					$query .= ", ";
				}
				$col_name = $col->Field;
				$col_value = $this->$col_name;
				$query .= "'".$col_value."'";
				$i++;
			}
			$query .= ")";
		}
		else {
			$query = "UPDATE ".$this->table_name." SET ";
			$i=0;
			foreach ($this->cols as $col) {
				if ($col->Field != $this->primary_key) {
					if ($i>0) { 
						$query .= ", ";
					}
					$col_name = $col->Field;
					$col_value = $this->$col_name;
					$query .= "`".$col_name."` = '".$col_value."'";
					$i++;
				}
			}
			$query .= " WHERE ".$this->primary_key." = '".$this->$primary_key."'";
		}
		
		$this->db->setQuery($query);
		$this->db->query();
	}
	
	/**
	 * Check whether a row exists with the passed id.
	 * 
	 * @param	int	$id The row id.
	 * @return	bool
	 * @since 	1.0
	 */
	function rowExists($id) {
		$query = "SELECT ".$this->primary_key." FROM ".$this->table_name." WHERE ".$this->primary_key." = '".$id."'";
		$this->db->setQuery($query);
		$result = $this->db->loadResult();
		if ($result) {
			return true;
		}
		else {
			return false;
		}
	}
}
?>