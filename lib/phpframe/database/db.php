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
 * $db =& db::getInstance('db');
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
	 * The MySQL error msg
	 * 
	 * @var string
	 */
	var $error=null;
    
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
		$this->link = mysql_connect($db_host, $db_user, $db_pass) or die('Could not connect: ' . mysql_error());
		mysql_select_db($db_name) or die('Could not select database');
	}
	
	/**
	 * Set the SQL query
	 * 
	 * Set a string as the query to be run.
	 *
	 * @param string $query The SQL query.
	 */
	public function setQuery($query) {
		$this->query = str_replace('#__', 'eo_', $query);
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
		if (!$this->rs = mysql_query($this->query)) {
			$this->error = mysql_error();
			return false;
		}
		
		$result = mysql_fetch_row($this->rs);
		return $result[0];
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
		if (!$this->rs = mysql_query($this->query)) {
			$this->error = mysql_error();
			return false;
		}
		
		$row = mysql_fetch_assoc($this->rs);
		$row_obj = new standardObject();
		if (is_array($row)) {
			foreach ($row as $key=>$value) {
				$row_obj->$key = $value;
			}
		}
		
		return $row_obj;
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
		if (!$this->rs = mysql_query($this->query)) {
			$this->error = mysql_error();
			return false;
		}
		
		$rows = array();
		
		while ($row = mysql_fetch_assoc($this->rs)) {
			$row_obj = new standardObject();
			if (is_array($row)) {
				foreach ($row as $key=>$value) {
					$row_obj->$key = $value;
				}
				$rows[] = $row_obj;	
			}
		}
		
		return $rows;
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
	 * Run SQL query and return mysql record set resource.
	 *
	 * @return resource
	 */
	public function query() {
		if (!$this->rs = mysql_query($this->query)) {
			$this->error = mysql_error();
			return false;
		}
		
		return $this->rs;
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
		if (!$num_rows = mysql_num_rows($this->rs)) {
			$this->error = mysql_error();
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
		if (!$affected_rows = mysql_affected_rows()) {
			$this->error = mysql_error();
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