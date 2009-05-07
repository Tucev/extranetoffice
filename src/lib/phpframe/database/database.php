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
 * MySQL Database class
 * 
 * This class deals with the connection to the MySQL database.
 * 
 * This class uses the phpFrame_Base_Singleton design pattern, and it is intantiated 
 * using the main phpFrame factory class.
 * 
 * Usage example:
 * <code>
 * $db = phpFrame::getDB();
 * $query = "SELECT * FROM #__components";
 * $db->setQuery($query);
 * $array = $db->loadObjectList();
 * echo '<pre>'; var_dump($array); echo '</pre>';
 * </code>
 * 
 * The snippet above will get the current instance of the db object (we assume that 
 * the db object has already been instantiated and the connection has already been 
 * established, as this is done by the application on load), run a query, return the 
 * result as an array of objects and then dump the raw data to the screen.
 * 
 * @package		phpFrame
 * @subpackage 	database
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class phpFrame_Database extends phpFrame_Base_Singleton {
	/**
	 * The MySQL link identifier on success, or FALSE on failure. 
	 * 
	 * @access	private
	 * @var		resource
	 */
	private $_link=null;
	/**
	 * The query string to be run.
	 * 
	 * @access	private
	 * @var		string
	 */
	private $_query=null;
	/**
	 * The MySQL record set returned from last query.
	 *
	 * @access	private
	 * @var		resource
	 */
	private $_rs=null;
	/**
	 * Array containing error messages if any
	 * 
	 * @access	private
	 * @var		array
	 */
	private $_error=array();
    
	/**
	 * Constructor
	 * 
	 * The constructor connects to the MySQL server and selects the database.
	 * 
	 * 
	 * @access	protected
	 * @param 	string 	$db_host 	The MySQL server hostname. It uses the value set in inc/config.php by default.
	 * @param 	string 	$db_user 	The MySQL username. It uses the value set in inc/config.php by default.
	 * @param 	string 	$db_pass 	The MySQL password. It uses the value set in inc/config.php by default.
	 * @param 	string 	$db_name	The MySQL database name. It uses the value set in inc/config.php by default.
	 * @since	1.0
	 */
	protected function __construct($db_host=config::DB_HOST, $db_user=config::DB_USER, $db_pass=config::DB_PASS, $db_name=config::DB_NAME) {
		// Connect to database server
		// We catch PHP errors (converted into exceptions) and rethrow them as database exceptions
		try {
			$this->_link = @mysql_connect($db_host, $db_user, $db_pass);
		}
		catch (phpFrame_Exception_Error $e) {
			throw new phpFrame_Exception_Database('Could not connect to database.');
		}
		
		// Check if link is valid
		if (!$this->_link) {
			throw new phpFrame_Exception_Database('Could not connect to database.');
		}
		
		// Select database. If it fails we destroy link and close connection
		if (!mysql_select_db($db_name)) {
			$this->close();
			$this->_link = false;
			throw new phpFrame_Exception_Database('Could not select database.');
		}	
	}
	
	/**
	 * Set the SQL query
	 * 
	 * Set a string as the query to be run.
	 * 
	 * @access	public
	 * @param 	string 	$query 	The SQL query.
	 * @return	void
	 * @since	1.0
	 */
	public function setQuery($query) {
		$this->_query = str_replace('#__', config::DB_PREFIX, $query);
	}
	
	/**
	 * Run SQL query and return mysql record set resource.
	 * 
	 * It returns a mysql result resource or if it is an INSERT query it 
	 * returns the insert id. It returns FALSE on failure.
	 * 
	 * @access	public
	 * @return 	mixed	Returns a mysql result resource or int for INSERT queries or FALSE on failure.
	 * @since	1.0
	 */
	public function query() {
		// Only run query if active link is valid
		if ($this->_link === false) {
			return false;
		}
		
		// Run SQL query
		$this->_rs = mysql_query($this->_query);
		
		// Check query result is valid
		if ($this->_rs === false) {
			throw new phpFrame_Exception_Database('Query failed', $this->_query);
		}
		
		// If it is an INSERT query we return the insert id
		if (stripos($this->_query, 'INSERT') !== false) {
			return mysql_insert_id();
		}
		
		return $this->_rs;
	}
	
	/**
	 * Run query and load single result
	 * 
	 * Run query as set by preceding setQuery() call and return single result.
	 * This method is useful when we expect our query to return a single column
	 * from a singlw row.
	 * 
	 * @access	public
	 * @return	mixed	Returns a string with the single result or FALSE on failure.
	 * @since	1.0
	 */
	public function loadResult() {
		// Run SQL query
		$this->_rs = $this->query($this->_query);
		// Check query result is valid
		if ($this->_rs === false) {
			return false;
		}
		
		// Fetch row
		$result = mysql_fetch_row($this->_rs);
		// Check row is valid and return
		if ($result !== false) {
			return $result[0];
		}
		else {
			return false;
		}
	}
	
	/**
	 * Run query and load single value for each row
	 * 
	 * @access	public
	 * @return 	mixed	Returns an array containing single column for each row or FALSE on failure.
	 * @since	1.0
	 */
	public function loadResultArray() {
		// Run SQL query
		$this->_rs = $this->query($this->_query);
		// Check query result is valid
		if ($this->_rs === false) {
			return false;
		}
		
		$rows = array();
		
		// Fetch associative array
		while ($row = mysql_fetch_array($this->_rs)) {
			if (is_array($row) && count($row) > 0) {
				$rows[] = $row[0];	
			}
		}
		
		return $rows;
	}
	
	/**
	 * Run query and load single row as object
	 * 
	 * Run query as set by preceding setQuery() call and return single row as an
	 * object. This method is useful when we expect our query to return a single row.
	 * 
	 * @access	public
	 * @return	mixed	Returns a row object or FALSE if query fails.
	 * @since	1.0
	 */
	public function loadObject() {
		// Run SQL query
		$this->_rs = $this->query($this->_query);
		// Check query result is valid
		if ($this->_rs === false) {
			return false;
		}
		
		// Fetch row
		$row = mysql_fetch_assoc($this->_rs);
		// Check row is valid and return
		if ($row !== false) {
			$row_obj = new phpFrame_Base_StdObject();
			if (is_array($row) && count($row) > 0) {
				foreach ($row as $key=>$value) {
					$row_obj->$key = $value;
				}
				return $row_obj;
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
	 * Run query and load array of row objects
	 * 
	 * Run query as set by preceding setQuery() call and return array or rows as
	 * objects. This method is useful when we expect our query to return multiple 
	 * rows.
	 *
	 * @access	public
	 * @return	mixed	An array of row objects or FALSE if query fails.
	 * @since	1.0
	 */
	public function loadObjectList() {
		// Run SQL query
		$this->_rs = $this->query($this->_query);
		// Check query result is valid
		if ($this->_rs === false) {
			return false;
		}
		
		$rows = array();
		
		// Fetch associative array
		while ($row = mysql_fetch_assoc($this->_rs)) {
			$row_obj = new phpFrame_Base_StdObject();
			if (is_array($row) && count($row) > 0) {
				foreach ($row as $key=>$value) {
					$row_obj->$key = $value;
				}
				$rows[] = $row_obj;	
			}
		}
		
		return $rows;
	}
	
	/**
	 * Run query and load single row as associative array
	 * 
	 * Run query as set by preceding setQuery() call and return single row as an
	 * associative array. This method is useful when we expect our query to return a single row.
	 *
	 * @access	public
	 * @return	mixed	Returns an associative array with the row data or FALSE on failure.
	 * @since	1.0
	 */
	public function loadAssoc() {
		// Run SQL query
		$this->_rs = $this->query($this->_query);
		// Check query result is valid
		if ($this->_rs === false) {
			return false;
		}
		
		$row = mysql_fetch_assoc($this->_rs);
		if ($row === false) {
			return false;
		}
		
		return $row;
	}
	
	/**
	 * Get db escaped string
	 * 
	 * @access	public
	 * @param	string	The string to be escaped.
	 * @param	bool	Optional parameter to provide extra escaping.
	 * @return	string	Returns the escaped string.
	 * @since	1.0
	 */
	public function getEscaped($text, $extra = false) {
		$result = mysql_real_escape_string($text, $this->_link);
		if ($extra) {
			$result = addcslashes( $result, '%_' );
		}
		return $result;
	}
	
	/**
	 * Get number of rows from the latest result set.
	 *  
	 * This method after having run a query with statements like SELECT or SHOW that return an actual result set.
	 * To retrieve the number of rows affected by a INSERT, UPDATE, REPLACE or DELETE query, use getAffectedRows(). 
	 * 
	 * @access	public
	 * @return 	mixed	Returns an int with the number of rows from the latest result set or FALSE on failure.
	 * @see		getAffectedRows()
	 * @since	1.0
	 */
	public function getNumRows() {
		$num_rows = mysql_num_rows($this->_rs);
		// Check num_rows is valid
		if ($num_rows === false) {
			$this->_error[] = 'phpFrame: db::getNumRows(). MySQL Error: '.mysql_error();
			return false;
		}
		
		return $num_rows;
	}
	
	/**
	 * Get the number of affected rows by the last INSERT, UPDATE, REPLACE or DELETE query.
	 * 
	 * @access	public
	 * @return 	mixed	Returns an int with the number of affected rows or FALSE on failure.
	 * @see		getNumRows()
	 * @since	1.0
	 */
	public function getAffectedRows() {
		$affected_rows = mysql_affected_rows();
		// Check affected rows is valid
		if ($affected_rows == -1) {
			$this->_error[] = 'phpFrame: db::getAffectedRows(). MySQL Error: '.mysql_error();
			return false;
		}
		return $affected_rows;
	}
	
	/**
	 * Close the current MySQL connection
	 * 
	 * Using this method isn't usually necessary, as non-persistent open links are 
	 * automatically closed at the end of the script's execution.
	 * 
	 * @access	public
	 * @return 	bool	Returns TRUE on success or FALSE on failure.
	 * @since	1.0
	 */
	public function close() {
		// Free resultset
		//mysql_free_result();
		// Closing connection
		return mysql_close($this->_link);
	}
	
	/**
	 * Get last error in model
	 * 
	 * @access	public
	 * @return	mixed	Returns a string with the last error message or FALSE if no errors.
	 * @since	1.0
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