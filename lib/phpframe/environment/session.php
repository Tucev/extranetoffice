<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	environment
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Session Class
 * 
 * @package		phpFrame
 * @subpackage 	environment
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class session extends table {
	var $id=null;
	var $userid=null;
	var $groupid=null;
	var $data=array();
	var $modified=null;
	
	/**
	 * Constructor
	 * 
	 * Invoke the parent (table) constructor and start the session.
	 *
	 */
	function __construct() {
		$db = factory::getDB();
		parent::__construct($db, '#__session', 'id');
		
		$this->start();
	}
	
	/**
	 * Start session
	 * 
	 * Start php session and load application session accordingly.
	 *
	 */
	function start() {
		// start php session
		session_start();
		// load session data from db
		$this->load(session_id());
		// If new session we set the id
		if (!$this->id) {
			$this->id = session_id();
		}
		
		// Write session data to db
		$this->write();
	}
	
	/**
	 * Load session data from database
	 *
	 * @param string $id The php session id
	 */
	function load($id) {
		parent::load($id);
		
		// Unserialize data
		$this->data = unserialize($this->data);
	}
	
	/**
	 * Write session to database table
	 *
	 */
	function write() {
		// Serialize data array to store in db
		$this->data = serialize($this->data);
		$this->modified = date("Y-m-d H:i:s");
		$this->store();
		
		// Unserialize data after storing
		$this->data = unserialize($this->data);
	}
	
	/**
	 * Set session variable
	 *
	 * @param string $key The key where to store the value.
	 * @param mixed $value This can be any value, an integer, string, array, object...
	 */
	function setVar($key, $value) {
		$this->data[$key] = $value;
		$this->write();
	}
	
	/**
	 * Get session variable
	 *
	 * @param string $key The key or session variable name.
	 * @param mixed $value This parameter is used to give a default value in case the variable is empty.
	 * @return mixed
	 */
	function getVar($key, $value="") {
		if (empty($this->data[$key])) {
			$this->data[$key] = $value;
			$this->write();
		}
		
		return $this->data[$key];
	}
}
?>