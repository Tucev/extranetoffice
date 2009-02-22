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
	/**
	 * Reference to the database object
	 * 
	 * @var object
	 */
	var $db=null;
	/**
	 * The table name (this has to be the same as the MySQL table name, except for the table prefix).<br />
	 * ie: eo_users in the database would be used as #__users in this class
	 * 
	 * @var string
	 */
	var $table_name=null;
	/**
	 * The table's primary key column
	 * 
	 * @var string
	 */
	var $primary_key=null;
	/**
	 * Columns info
	 * 
	 * @var array
	 */
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
	function __construct($table_name, $primary_key) {
		$this->db =& factory::getDB();
		$this->table_name = $table_name;
		$this->primary_key = $primary_key;
		
		$this->getColumns();
		
		// If there are no columns it is probably because the table doesnt't exist.
		if (count($this->cols) > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Get columns for table in database and store column info in $this->cols.
	 * 
	 * @return	void
	 * @since 	1.0
	 */
	function getColumns() {
		$query = "SHOW COLUMNS FROM `".$this->table_name."`";
		$this->db->setQuery($query);
		$this->cols = $this->db->loadObjectList();
		// If no cols found set $this->cols to empty array to avoid problems with 
		// foreach loops in other methods that use this property.
		if (!is_array($this->cols)) {
			$this->cols = array();
		}
	}
	
	/**
	 * Load row by id and return row object.
	 * 
	 * @param	int	$id The row id.
	 * @return	object
	 * @since 	1.0
	 */
	function load($id) {
		$query = "SELECT * FROM `".$this->table_name."` WHERE `".$this->primary_key."` = '".$id."'";
		$this->db->setQuery($query);
		$row = $this->db->loadAssoc();
		
		if (is_array($row) && count($row) > 0) {
			foreach ($this->cols as $col) {
				$col_name = $col->Field;
				$col_value = $row[$col_name];
				$this->$col_name = $col_value;
			}
			
			return $this;	
		}
		else {
			return false;
		}
	}
	
	/**
	 * Bind array to row object
	 * 
	 * @param	array	$array
	 * @return	bool
	 */
	function bind($array) {
		if (is_array($array) && count($array) > 0) {
			foreach ($this->cols as $col) {
				if (array_key_exists($col->Field, $array)) {
					$col_name = $col->Field;
					$this->$col_name = $array[$col_name];
				}
			}
			
			return true;
		}
		else {
			error::raise('', 'error', 'Could not bind array to row' );
			return false;
		}
	}
	
	/**
	 * Check integrity of data before we write it to the database
	 * 
	 * @todo	This function is not checking the data types yet. Have to work on it, its a mess at the moment...
	 * @todo	Have to raise errors where appropriate.
	 * @return	bool
	 */
	function check() {
		foreach ($this->cols as $col) {
			$col_name = $col->Field;
			
			if (strpos($col->Type, 'int') !== false) {
				$this->$col_name = $this->$col_name;
				if ((!is_int($this->$col_name) || $col->Null == 'YES' && $this->$col_name == null) && $col->Extra != 'auto_increment') {
					//echo 'false';
					//return false;
				}
			}
			elseif (strpos($col->Type, 'float') !== false || strpos($col->Type, 'double') !== false || strpos($col->Type, 'decimal') !== false) {
				$this->$col_name = (float) $this->$col_name;
				if (!is_float($this->$col_name)) {
					//return false;
				}
			}
			elseif (strpos($col->Type, 'varchar') !== false || strpos($col->Type, 'text') !== false) {
				$this->$col_name = (string) $this->$col_name;
				if (!is_string($this->$col_name)) {
					//return false;
				}
			}
			elseif (strpos($col->Type, 'blob') !== false) {
				
			}
			elseif (strpos($col->Type, 'enum') !== false) {
				
			}
			elseif (strpos($col->Type, 'datetime') !== false) {
				
			}
			elseif (strpos($col->Type, 'date') !== false) {
				
			}
			elseif (strpos($col->Type, 'time') !== false) {
				
			}
			elseif (strpos($col->Type, 'year') !== false) {
				
			}
			elseif (strpos($col->Type, 'timestamp') !== false) {
				
			}
			elseif (strpos($col->Type, 'binary') !== false) {
				
			}
			elseif (strpos($col->Type, 'bool') !== false) {
				
			}
		
			//echo '<pre>'; var_dump($this); echo '</pre><hr />';
		}
		//exit;
		return true;
	}
	
	/**
	 * Store current row to database.
	 * 
	 * If new row inserts a new entry in db table, otherwise it updates existing row.
	 * 
	 * @todo	Have to raise errors where appropriate.
	 * @return	void
	 * @since 	1.0
	 */
	function store() {
		$primary_key = $this->primary_key;
		$row_exists = $this->rowExists($this->$primary_key);
		
		// Build either INSERT or UPDATE query depending on whether row 
		// with given id already exists.
		if ($row_exists === false) {
			$query = "INSERT INTO `".$this->table_name."` (";
			for ($i=0; $i<count($this->cols); $i++) {
				if ($i>0) { 
					$query .= ", ";
				}
				$query .= "`".$this->cols[$i]->Field."`";
			}
			$query .= ") VALUES (";
			
			for ($i=0; $i<count($this->cols); $i++) {
				if ($i>0) { 
					$query .= ", ";
				}
				$col_name = $this->cols[$i]->Field;
				$col_value = $this->$col_name;
				$query .= "'".$col_value."'";
			}
			$query .= ")";
		}
		else {
			$query = "UPDATE `".$this->table_name."` SET ";
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
			$query .= " WHERE `".$this->primary_key."` = '".$this->$primary_key."'";
		}
		
		$this->db->setQuery($query);
		$insert_id = $this->db->query();
		
		// Store new row id for new entries
		if ($row_exists === false && !empty($insert_id)) {
			$this->$primary_key = $insert_id;
		}
	}
	
	/**
	 * Check whether a row exists with the passed id.
	 * 
	 * @param	int	$id The row id.
	 * @return	bool
	 * @since 	1.0
	 */
	function rowExists($id) {
		if (!empty($id)) {
			$query = "SELECT `".$this->primary_key."` FROM `".$this->table_name."` WHERE `".$this->primary_key."` = '".$id."'";
			$this->db->setQuery($query);
			$result = $this->db->loadResult();
			if ($result != false && !empty($result)) {
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
}
?>