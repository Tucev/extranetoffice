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
 * Row Class
 * 
 * @package		phpFrame
 * @subpackage 	database
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class phpFrame_Database_Row {
	/**
	 * An assoc array used to cache table primary keys to avoid redundant queries to the database.
	 * This array is shared by all instances at class scope.
	 * 
	 * @var array
	 */
	private static $_primary_keys=array();
	/**
	 * An assoc array used to cache table structures to avoid redundant queries to the database.
	 * This array is shared by all instances at class scope.
	 * 
	 * @var array
	 */
	private static $_structures=array();
	/**
	 * The table name where the row object belongs
	 * 
	 * @var string
	 */
	private $_table_name=null;
	/**
	 * The row's data
	 * 
	 * @var	array
	 */
	private $_data=array();
	
	/**
	 * Constructor
	 * 
	 * @param	string	$table_name
	 * @return	void
	 */
	public function __construct($table_name) {
		$this->_table_name = (string) $table_name;
		
		// Read table structure from db
		if (!isset(self::$_structures[$table_name])) {
			$this->_readStructure();
		}
	}
	
	/**
	 * Magic getter
	 * 
	 * This is called when we try to access a public property and tries to find the key in 
	 * the columns array.
	 * 
	 * This method also enforces that public properties are not mistakenly referenced.
	 * 
	 * @param	$key
	 * @return	string
	 */
	public function __get($key) {
		return $this->get($key);
	}
	
	/**
	 * Get a column value from this row
	 * 
	 * @param	string	$key
	 * @return	string
	 */
	public function get($key) {
		if (!array_key_exists($key, $this->_data)) {
			throw new phpFrame_Exception("Tried to get column '".$key."' that doesn't exist in "
										 .$this->_table_name, 
										 phpFrame_Exception::E_PHPFRAME_WARNING);
		}
		
		return $this->_data[$key];
	}
	
	/**
	 * Set a column value in this row
	 *  
	 * @param	string	$key
	 * @param	string	$value
	 * @return	void
	 */
	public function set($key, $value) {
		if (!$this->hasColumn($key)) {
			throw new phpFrame_Exception("Tried to set column '".$key."' that doesn't exist in "
										 .$this->_table_name, 
										 phpFrame_Exception::E_PHPFRAME_WARNING);
		}
		
		$this->_data[$key] = $value;
	}
	
	/**
	 * Check if row has a given column
	 * 
	 * @param	string	$column_name
	 * @return	bool
	 */
	public function hasColumn($column_name) {
		// Loop through table structure to find key
		foreach (self::$_structures[$this->_table_name] as $structure) {
			if ($structure->Field == $column_name) return true;
		}
		
		return false;
	}
	
	/**
	 * Load row data from database given a row id.
	 * 
	 * @param	mixed	$id Normally an integer, but could be a string
	 * @return	object of type phpFrame_Database_Row
	 */
	public function load($id) {
		$query = "SELECT * FROM `".$this->_table_name;
		$query .= "` WHERE `".self::$_primary_keys[$this->_table_name]."` = '".$id."'";
		
		$db = phpFrame::getDB();
		$this->_db->setQuery($query);
		$array = $this->_db->loadAssoc();
		var_dump($array); exit;
		if (is_array($array) && count($array) > 0) {
			foreach ($array as $key=>$value) {
			
			}
			
			return $this;	
		}
		else {
			return false;
		}
	}
	
	public function loadByQuery($query, $foreign_keys=array()) {
		// Run SQL query
		$rs = phpFrame::getDB()->setQuery($query)->query();
		
		$array = array();
		
		// Id result is valid we populate array
		if ($rs !== false || phpFrame::getDB()->getNumRows() == 1) {
			$array = mysql_fetch_assoc($rs);
		}
		
		// Bind result array to row passing foreign keys aliases to allow as valid keys
		return $this->bind($array, '', $foreign_keys);
	}
	
	/**
	 * Bind array to row
	 * 
	 * @param	array	$array	The array to bind to the object.
	 * @param	string	$exclude 	A list of key names to exclude from binding process separated by commas.
	 * @return	object of type phpFrame_Database_Row
	 */
	public function bind($array, $exclude='', $foreign_keys=array()) {
		// Process exclude
		if (!empty($exclude)) {
			$exclude = explode(',', $exclude);
		}
		else {
			$exclude = array();
		}
		
		if (!is_array($array)) {
			throw new phpFrame_Exception_Database('Argument 1 ($array) has to be of type array.');
		}
		
		if (count($array) > 0) {
			// Rip values using known structure
			foreach (self::$_structures[$this->_table_name] as $col) {
				if (array_key_exists($col->Field, $array) && !in_array($col->Field, $exclude)) {
					$this->_data[$col->Field] = $array[$col->Field];
				}
			}
			
			// Add foreign key values
			foreach ($foreign_keys as $foreign_key) {
				if (array_key_exists($foreign_key, $array) && !in_array($foreign_key, $exclude)) {
					$this->_data[$foreign_key] = $array[$foreign_key];
				}
			}
		}
		
		return $this;
	}
	
	/**
	 * Store row in database
	 * 
	 * @return object of type phpFrame_Database_Row
	 */
	public function store() {
		// Check types and required columns before saving
		$this->_check();
		
		// Do insert or update depending on whether primary key is set
		$id = $this->get(self::$_primary_keys[$this->_table_name]);
		if (is_null($id)) {
			// Insert new record
			$this->_insert();
		}
		else {
			$this->_update();
		}
		
		return $this;
	}
	
	public function delete($id) {}
	
	/**
	 * Read row structure from database
	 * 
	 * @access	private
	 * @return	void
	 * @since 	1.0
	 */
	private function _readStructure() {
		$query = "SHOW COLUMNS FROM `".$this->_table_name."`";
		phpFrame::getDB()->setQuery($query);
		self::$_structures[$this->_table_name] = phpFrame::getDB()->loadObjectList();
		
		if (self::$_structures === false || !is_array(self::$_structures)) {
			throw new phpFrame_Exception_Database($this->_db->getLastError());
		}
		
		// Loop through structure array to find primary key
		foreach (self::$_structures[$this->_table_name] as $structure) {
			if ($structure->Key == 'PRI') {
				self::$_primary_keys[$this->_table_name] = $structure->Field;
			}
		}
	}
	
	/**
	 * This method checks the columns data types and required fields before saving to db.
	 * 
	 * @return	bool
	 */
	private function _check() {
		// Loop through every column in the row
		foreach (self::$_structures[$this->_table_name] as $structure) {
			
			// If assigned value is empty
			if (empty($this->_data[$structure->Field])) {
				// Set default value if any
				if (!is_null($structure->Default)) {
					$this->_data[$structure->Field] = $structure->Default;
				}
				
				// If column is auto_increment we set to null
				if ($structure->Extra == 'auto_increment') {
					$this->_data[$structure->Field] = null;
					continue; // jump to next iteration of the loop to avoid type checking
				}
			}
			
			// Get type and length from input type string
			preg_match('/([a-zA-Z]+)(\((.+)\))?/i', $structure->Type, $matches);
			
			$type = strtolower($matches[1]); // make string lower case
			if (isset($matches[3])) {
				$length = $matches[3];	
			}
			
			// Make type variation prefix (ie: tinyint to int or longtext to long)
			$prefixes = array('tiny', 'small', 'medium', 'big', 'long');
			$type = str_replace($prefixes, '', $type);
			
			// Perform validation depending on data type
			switch ($type) {
				case 'int' : 
					$pattern = '/^-?\d+$/';
					break;
					
				case 'float' :
				case 'double' :
				case 'decimal' :
					$pattern = '/^-?\d+\.?\d+$/';
					break;
					
				case 'char' :
				case 'varchar' :
					$pattern = '/^.{0,'.$length.'}$/';
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
					$pattern = '/^.*$/im';
					break;
			}
			
			if (!preg_match($pattern, $this->_data[$structure->Field])) {
				throw new phpFrame_Exception("Wrong type for column '".$structure->Field."'. "
											 ."Expected '".$structure->Type."' and got '".$this->_data[$structure->Field]."'", 
										 	 phpFrame_Exception::E_PHPFRAME_WARNING);
			}
		}
	}
	
	/**
	 * Insert new record into database
	 * 
	 * @return	void
	 */
	private function _insert() {
		// Build SQL insert query
		$query = "INSERT INTO `".$this->_table_name."` ";
		$query .= " (`".implode("`, `", array_keys($this->_data))."`) ";
		$query .= " VALUES ('".implode("', '", $this->_data)."')";
		//echo $query; exit;
		
		phpFrame::getDB()->setQuery($query);
		$insert_id = phpFrame::getDB()->query();
		
		if ($insert_id === false) {
			throw new phpFrame_Exception(phpFrame::getDB()->getLastError());
		}
		
		$this->_data[self::$_primary_keys[$this->_table_name]] = $insert_id;
	}
	
	/**
	 * Update existing row in database
	 * 
	 * @return	void
	 */
	private function _update() {
		//$this->load($id);
	}
}