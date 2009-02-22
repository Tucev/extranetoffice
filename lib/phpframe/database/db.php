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
 * MySQL Database class
 * 
 * This class deals with the connection to the MySQL database.
 * 
 * This class uses the singleton design pattern, and it is therefore intantiated 
 * using the getInstance() method.
 * 
 * To make sure that the child model is instantiated using the correct run time
 * class name we pass the class name when invoking the getInstance() method.
 * 
 * Usage example:
 * <code>
 * $db =& phpFrame::getInstance('db');
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
class db extends singleton {
	/**
	 * The MySQL link identifier on success, or FALSE on failure. 
	 *
	 * @var resource
	 */
	var $link=null;
	/**
	 * The query string to be run.
	 *
	 * @var string
	 */
	var $query=null;
	/**
	 * The MySQL record set returned from last query.
	 *
	 * @var resource
	 */
	var $rs=null;
    
	/**
	 * Connect to MySQL server and select database.
	 * 
	 * This methid must be called before we can run any SQL queries.
	 * It connects to the db server and selects the database.
	 *
	 * @param string $db_host The MySQL server hostname.
	 * @param string $db_user The MySQL username.
	 * @param string $db_pass The MySQL password.
	 * @param string $db_name The MySQL database name.
	 */
	public function connect($db_host, $db_user, $db_pass, $db_name) {
		// Connect to database server
		$this->link = mysql_connect($db_host, $db_user, $db_pass);
		// Check if link is valid
		if ($this->link === false) {
			error::raise('', 'error', mysql_error());
			return false;
		}
		
		// Select database. If it fails we destroy link and close connection
		if (!mysql_select_db($db_name)) {
			$this->close();
			$this->link = false;
			error::raise('', 'error', 'Could not select database');
			return false;
		}
		
		return true;
	}
	
	/**
	 * Set the SQL query
	 * 
	 * Set a string as the query to be run.
	 *
	 * @param string $query The SQL query.
	 */
	public function setQuery($query) {
		$config =& factory::getConfig();
		$this->query = str_replace('#__', $config->db_prefix, $query);
	}
	
	/**
	 * Run SQL query and return mysql record set resource.
	 *
	 * @return resource
	 */
	public function query() {
		// Only run query if active link is valid
		if ($this->link === false) {
			return false;
		}
		
		// Run SQL query
		$this->rs = mysql_query($this->query);
		// Check query result is valid
		if ($this->rs === false || mysql_error() != '') {
			error::raise('', 'error', mysql_error().' Query: <code>'.$this->query.'</code>');
			return false;
		}
		
		return $this->rs;
	}
	
	/**
	 * Run query and load single result
	 * 
	 * Run query as set by preceding setQuery() call and return single result.
	 * This method is useful when we expect our query to return a single column
	 * from a singlw row.
	 *
	 * @return string
	 */
	public function loadResult() {
		// Run SQL query
		$this->rs = mysql_query($this->query);
		// Check query result is valid
		if ($this->rs === false) {
			return false;
		}
		
		// Fetch row
		$result = mysql_fetch_row($this->rs);
		// Check row is valid and return
		if ($result !== false) {
			return $result[0];
		}
		else {
			return false;
		}
	}
	
	/**
	 * Run query and load single row as object
	 * 
	 * Run query as set by preceding setQuery() call and return single row as an
	 * object. This method is useful when we expect our query to return a single row.
	 *
	 * @return object
	 */
	public function loadObject() {
		// Run SQL query
		$this->rs = mysql_query($this->query);
		// Check query result is valid
		if ($this->rs === false) {
			return false;
		}
		
		// Fetch row
		$row = mysql_fetch_assoc($this->rs);
		// Check row is valid and return
		if ($row !== false) {
			$row_obj = new standardObject();
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
	 * @return array
	 */
	public function loadObjectList() {
		// Run SQL query
		$this->rs = mysql_query($this->query);
		// Check query result is valid
		if ($this->rs === false) {
			return false;
		}
		
		$rows = array();
		
		// Fetch associative array
		while ($row = mysql_fetch_assoc($this->rs)) {
			$row_obj = new standardObject();
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
	 * @return array
	 */
	public function loadAssoc() {
		// Run SQL query
		$this->rs = mysql_query($this->query);
		// Check query result is valid
		if ($this->rs === false) {
			return false;
		}
		
		$row = mysql_fetch_assoc($this->rs);
		if ($row === false) {
			return false;
		}
		
		return $row;
	}
	
	/**
	 * Get db escaped string
	 *
	 * @param	string	The string to be escaped
	 * @param	boolean	Optional parameter to provide extra escaping
	 * @return	string
	 * @access	public
	 * @abstract
	 */
	public function getEscaped($text, $extra = false) {
		$result = mysql_real_escape_string($text, $this->link);
		if ($extra) {
			$result = addcslashes( $result, '%_' );
		}
		return $result;
	}
	
	/**
	 * Retrieves the number of rows from the latest result set. 
	 * This method after having run a query with statements like SELECT or SHOW that return an actual result set.
	 * To retrieve the number of rows affected by a INSERT, UPDATE, REPLACE or DELETE query, use getAffectedRows(). 
	 * 
	 * @return 	int
	 * @see		getAffectedRows()
	 */
	public function getNumRows() {
		$num_rows = mysql_num_rows($this->rs);
		// Check num_rows is valid
		if ($num_rows === false) {
			error::raise('', 'error', mysql_error().' Query: <code>'.$this->query.'</code>');
			return false;
		}
		
		return $num_rows;
	}
	
	/**
	 * Get the number of affected rows by the last INSERT, UPDATE, REPLACE or DELETE query.
	 * 
	 * @return 	int
	 * @see		getNumRows()
	 */
	public function getAffectedRows() {
		$affected_rows = mysql_affected_rows();
		// Check affected rows is valid
		if ($affected_rows == -1) {
			error::raise('', 'error', mysql_error().' Query: <code>'.$this->query.'</code>');
			return false;
		}
		return $affected_rows;
	}
	
	/**
	 * Close the current MySQL connection
	 *
	 */
	public function close() {
		// Free resultset
		//mysql_free_result();
		// Closing connection
		mysql_close($this->link);
	}
	
}
?>