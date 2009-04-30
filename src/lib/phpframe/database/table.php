<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	database
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Table Class
 * 
 * This class implements the phpFrame_Base_Singleton design pattern. There will be many implementations 
 * of the table class. The table class is an abstract class so it will be used to implement 
 * specific database tables and each of the child instances will need to be a phpFrame_Base_Singleton.
 * 
 * @package		phpFrame
 * @subpackage 	database
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @abstract 
 */
abstract class phpFrame_Database_Table extends phpFrame_Base_Singleton {
	/**
	 * Reference to the database object
	 * 
	 * @var object
	 */
	protected $_db=null;
	/**
	 * The table name (this has to be the same as the MySQL table name, except for the table prefix).<br />
	 * ie: eo_users in the database would be used as #__users in this class
	 * 
	 * @var string
	 */
	protected $_table_name=null;
	/**
	 * The table's primary key column
	 * 
	 * @var string
	 */
	protected $_primary_key=null;
	/**
	 * Columns info
	 * 
	 * @var array
	 */
	protected $_cols=array();
	/**
	 * Array containing error message strings if any
	 * 
	 * @var array
	 */
	protected $_error=array();
	
	/**
	 * Contructor
	 * 
	 * @param	string	$table_name The table name in the database.
	 * @param	string	$primary_key The column name of the table's primary key.
	 * @return	void
	 * @since 	1.0
	 */
	public function __construct($table_name, $primary_key) {
		$this->_db = phpFrame::getDB();
		$this->_table_name = $table_name;
		$this->_primary_key = $primary_key;
		
		$this->_getColumns();
		
		// If there are no columns it is probably because the table doesnt't exist.
		if (count($this->_cols) > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Get columns for table in database and store column info in $this->_cols.
	 * 
	 * @access	private
	 * @return	void
	 * @since 	1.0
	 */
	private function _getColumns() {
		$query = "SHOW COLUMNS FROM `".$this->_table_name."`";
		$this->_db->setQuery($query);
		$this->_cols = $this->_db->loadObjectList();
		if ($this->_cols === false) {
			$this->_error[] = $this->_db->getLastError();
			return false;
		}
		
		// If no cols found set $this->_cols to empty array to avoid problems with 
		// foreach loops in other methods that use this property.
		if (!is_array($this->_cols)) {
			$this->_cols = array();
		}
	}
	
	/**
	 * Load row by id and return row object.
	 * 
	 * @access	public
	 * @param	int		$id 		The row id.
	 * @param	string	$exclude 	A list of key names to exclude from binding process separated by commas.
	 * @param	object	&$row 		The table row object use for binding. This parameter is passed by reference.
	 * 								This parameter is optional. If omitted the current instance is used ($this).
	 * @return	mixed	The loaded row object of FALSE on failure.
	 * @since 	1.0
	 */
	public function load($id, $exclude='', &$row=null) {
		// Process exclude
		if (!empty($exclude)) {
			$exclude = explode(',', $exclude);
		}
		else {
			$exclude = array();
		}
		
		if (is_null($row)) {
			$row =& $this;
		}
		
		$query = "SELECT * FROM `".$this->_table_name."` WHERE `".$this->_primary_key."` = '".$id."'";
		$this->_db->setQuery($query);
		$array = $this->_db->loadAssoc();
		
		if (is_array($array) && count($array) > 0) {
			foreach ($this->_cols as $col) {
				if (array_key_exists($col->Field, $array) && !in_array($col->Field, $exclude)) {
					$col_name = $col->Field;
					$row->$col_name = $array[$col_name];	
				}
			}
			
			return $row;	
		}
		else {
			return false;
		}
	}
	
	/**
	 * Bind array to row object
	 * 
	 * @access	public
	 * @param	array	$array		The array to bind to the object.
	 * @param	string	$exclude 	A list of key names to exclude from binding process separated by commas.
	 * @param	object	&$row 		The table row object use for binding. This parameter is passed by reference.
	 * 								This parameter is optional. If omitted the current instance is used ($this).
	 * @return	mixed	The processed row object or FALSE on failure.
	 * @since 	1.0
	 */
	public function bind($array, $exclude='', &$row=null) {
		// Process exclude
		if (!empty($exclude)) {
			$exclude = explode(',', $exclude);
		}
		else {
			$exclude = array();
		}
		
		if (is_null($row)) {
			$row =& $this;
		}
		
		if (is_array($array) && count($array) > 0) {
			foreach ($this->_cols as $col) {
				if (array_key_exists($col->Field, $array) && !in_array($col->Field, $exclude)) {
					$col_name = $col->Field;
					$row->$col_name = $array[$col_name];
				}
			}
			
			return true;
		}
		else {
			$this->_error[] = 'phpFrame: table::bind(). Could not bind array to row.';
			return false;
		}
	}
	
	/**
	 * Check integrity of data before we write it to the database
	 * 
	 * @access	public
	 * @param	object	&$row The table row object to check. This parameter is passed by reference.
	 * 					This parameter is optional. If omitted the current instance is used ($this).
	 * @return	bool	TRUE on success and FALSE on failure.
	 * @since 	1.0
	 */
	public function check(&$row=null) {
		// Set row to $this if not passed in call.
		if (is_null($row)) {
			$row =& $this;
		}
		
		// Loop through every column in table to check data types
		foreach ($this->_cols as $col) {
			$col_name = $col->Field;
			
			// If row doesn't have property for this column and it is an existing record we continue
			$primary_key_column = $this->_primary_key;
			// If value is empty and it is an existing record or null is allowed or is auto_increment we don't check data type
			if (empty($row->$col_name) && (!empty($row->$primary_key_column) || ($col->Null == 'YES' || $col->Extra == 'auto_increment')) ) {
				continue;
			}
			else {
				if ($this->checkDataType($row->$col_name, $col->Type) === false) {
					$this->_error[] = 'phpFrame: table::check() failed. Column '.$col->Field.' '.$row->$col_name.' is not type '.$col->Type;
					return false;
				}	
			}
		}
		
		return true;
	}
	
	/**
	 * Check value is valid for a specific MySQL data type
	 * 
	 * @todo	This method is performing some basic checks but needs to check more specific data types.
	 * @access	public
	 * @param	string	$value The value to validate
	 * @param	string	$type The MySQL data type (int(11), tinyint, varchar(16), ...)
	 * @return	bool	Returns TRUE on success or FALSE on failure.
	 * @since 	1.0
	 */
	public function checkDataType($value, $type) {
		// Explode MySQL data type into type and length
		$type_array = explode('(', $type);
		$type = strtolower($type_array[0]); // make string lower case
		if (sizeof($type_array) > 1) {
			$length = substr($type_array[1], 0, strlen($type_array[1])-1);	
		}
		
		// Make type variation prefix (ie: tinyint to int or longtext to long)
		$prefixes = array('tiny', 'small', 'medium', 'big', 'long');
		$type = str_replace($prefixes, '', $type);
		
		// Perform validation depending on data type
		switch ($type) {
			case 'int' : 
				$isValid = phpFrame_Utils_Filter::validate($value, 'int');
				break;
				
			case 'float' :
			case 'double' :
			case 'decimal' :
				$isValid = phpFrame_Utils_Filter::validate($value, 'float');
				break;
				
			case 'char' :
			case 'varchar' :
				if (strlen($value) <= $length) {
					$isValid = phpFrame_Utils_Filter::validate($value);	
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
				$isValid = phpFrame_Utils_Filter::validate($value);
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
	 * @access	public
	 * @param	object	&$row 	The table row object to store. This parameter is passed by reference.
	 * 							This parameter is optional. If omitted the current instance is used ($this).
	 * @return	bool	Returns TRUE on success or FALSE on failure.
	 * @since 	1.0
	 */
	public function store(&$row=null) {
		// Set row to $this if not passed in call.
		if (is_null($row)) {
			$row =& $this;
		}
		
		// Get the name of the primary key column
		$primary_key = $this->_primary_key;
		
		// Check whether row exists in table
		$row_exists = $this->rowExists($row->$primary_key);
		
		// Build either INSERT or UPDATE query depending on whether row 
		// with given id already exists.
		if ($row_exists === false) {
			$query = "INSERT INTO `".$this->_table_name."` (";
			for ($i=0; $i<count($this->_cols); $i++) {
				if ($i>0) { 
					$query .= ", ";
				}
				$query .= "`".$this->_cols[$i]->Field."`";
			}
			$query .= ") VALUES (";
			
			for ($i=0; $i<count($this->_cols); $i++) {
				$col_name = $this->_cols[$i]->Field;
				$col_value = $row->$col_name;
				if ($i>0) $query .= ", ";
				// If value is empty and there is a default value for the column we use that.
				if (is_null($col_value) && !is_null($this->_cols[$i]->Default)) {
					if ($this->_cols[$i]->Default == 'CURRENT_TIMESTAMP') {
						$col_value = date("Y-m-d H:i:s");
					}
					else {
						$col_value = $this->_cols[$i]->Default;
					}
				}
				$query .= "'".$col_value."'";
			}
			$query .= ")";
		}
		else {
			$query = "UPDATE `".$this->_table_name."` SET ";
			$i=0;
			foreach ($this->_cols as $col) {
				if ($col->Field != $this->_primary_key) {
					$col_name = $col->Field;
					if (property_exists($row, $col_name) && !is_null($row->$col_name)) {
						if ($i>0) $query .= ", ";
						$query .= "`".$col_name."` = '".$row->$col_name."'";
						$i++;
					}
				}
			}
			$query .= " WHERE `".$this->_primary_key."` = '".$row->$primary_key."'";
		}
		
		$this->_db->setQuery($query);
		$insert_id = $this->_db->query();
		
		if ($insert_id === false){
			$this->_error[] = $this->_db->getLastError(); 
			return false;
		}
		
		// Store new row id for new entries
		if ($row_exists === false && !empty($insert_id)) {
			$row->$primary_key = $insert_id;
		}
		
		return true;
	}
	
	/**
	 * Delete a table row by id
	 * 
	 * @access	public
	 * @param	mixed	$id The row id. Normally a string or an integer.
	 * @return	bool	Returns TRUE on success or FALSE on failure.
	 * @since 	1.0
	 */
	public function delete($id) {
		$query = "DELETE FROM `".$this->_table_name."` WHERE `".$this->_primary_key."` = '".$id."'";
		$this->_db->setQuery($query);
		if ($this->_db->query() === true) {
			return true;
		}
		else {
			$this->_error[] = 'phpFrame: table::delete() failed. Query: '.$query;
			return false;
		}
	}
	
	/**
	 * Check whether a row exists with the passed id.
	 * 
	 * @access	public
	 * @param	int		$id	The row id.
	 * @return	bool	Returns TRUE on success or FALSE on failure.
	 * @since 	1.0
	 */
	public function rowExists($id) {
		if (!empty($id)) {
			$query = "SELECT `".$this->_primary_key."` FROM `".$this->_table_name."` WHERE `".$this->_primary_key."` = '".$id."'";
			$this->_db->setQuery($query);
			$result = $this->_db->loadResult();
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
	
	/**
	 * Get last error in current instance
	 * 
	 * This method returns a string with the error message or FALSE if no errors.
	 * 
	 * @access	public
	 * @return	mixed	Returns a string with the last error message or FALSE if no errors.
	 * @since 	1.0
	 */
	public function getLastError() {
		if (is_array($this->_error) && count($this->_error) > 0) {
			return end($this->_error);
		}
		else {
			return false;
		}
	}
}
?>