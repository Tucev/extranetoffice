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
 * NOTE: Errors should not be raised using error:raise() as the error object uses the table 
 * class itself for storing data into the session. So we store errors internally instead in 
 * property $this->error.
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
	 * String containing error message if any
	 * 
	 * @var string
	 */
	var $error=null;
	
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
	 * @param	string	$exclude A list of key names to exclude from binding process separated by commas.
	 * @return	bool
	 */
	function bind($array, $exclude='') {
		// Process exclude
		if (!empty($exclude)) {
			$exclude = explode(',', $exclude);
		}
		else {
			$exclude = array();
		}
		
		if (is_array($array) && count($array) > 0) {
			foreach ($this->cols as $col) {
				if (array_key_exists($col->Field, $array) && !in_array($col->Field, $exclude)) {
					$col_name = $col->Field;
					$this->$col_name = $array[$col_name];
				}
			}
			
			return true;
		}
		else {
			$this->error = 'Could not bind array to row.';
			return false;
		}
	}
	
	/**
	 * Check integrity of data before we write it to the database
	 * 
	 * @todo	Have to raise errors where appropriate.
	 * @return	bool
	 */
	function check() {
		foreach ($this->cols as $col) {
			$col_name = $col->Field;
			
			// If value is empty and null is allowed or is auto_increment we don't check data type
			if (empty($this->$col_name) && ($col->Null == 'YES' || $col->Extra == 'auto_increment')) {
				continue;
			}
			else {
				if ($this->checkDataType($this->$col_name, $col->Type) === false) {
					$this->error = 'phpFrame: Row check() failed. Column '.$col->Field.' '.$this->$col_name.' is not type '.$col->Type;
					return false;
				}	
			}
		}
		//exit;
		return true;
	}
	
	/**
	 * Check value is valid for a specific MySQL data type
	 * 
	 * @todo	This method is performing some basic checks but needs to check more specific data types.
	 * @param	string	$value The value to validate
	 * @param	string	$type The MySQL data type (int(11), tinyint, varchar(16), ...)
	 * @return bool
	 */
	function checkDataType($value, $type) {
		// Explode MySQL data type into type and length
		$type_array = explode('(', $type);
		$type = strtolower($type_array[0]); // make string lower case
		$length = substr($type_array[1], 0, strlen($type_array[1])-1);
		
		// Make type variation prefix (ie: tinyint to int or longtext to long)
		$prefixes = array('tiny', 'small', 'medium', 'big', 'long');
		$type = str_replace($prefixes, '', $type);
		
		// Perform validation depending on data type
		switch ($type) {
			case 'int' : 
				$isValid = filter::validate($value, 'int');
				break;
				
			case 'float' :
			case 'double' :
			case 'decimal' :
				$isValid = filter::validate($value, 'float');
				break;
				
			case 'char' :
			case 'varchar' :
				if (strlen($value) <= $length) {
					$isValid = filter::validate($value);	
				}
				else {
					$isValid = false;
				}
				break;
			
			case 'text' :
			case 'blob' :
			case 'enum' :
			case 'datetime' :
			case 'date' :
			case 'time' :
			case 'year' :
			case 'timestamp' :
			case 'binary' :
			case 'bool' :
			default : 
				$isValid = filter::validate($value);
				break;
		}
		
		if ($isValid !== false) {
			return true;
		}
		else {
			return false;
		}
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
				$col_name = $this->cols[$i]->Field;
				$col_value = $this->$col_name;
				if ($i>0) $query .= ", ";
				$query .= "'".$col_value."'";
			}
			$query .= ")";
		}
		else {
			$query = "UPDATE `".$this->table_name."` SET ";
			$i=0;
			foreach ($this->cols as $col) {
				if ($col->Field != $this->primary_key) {
					$col_name = $col->Field;
					$col_value = $this->$col_name;
					if (!empty($col_value)) {
						if ($i>0) $query .= ", ";
						$query .= "`".$col_name."` = '".$col_value."'";
						$i++;
					}
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