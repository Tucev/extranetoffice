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
	/**
	 * The session id
	 * 
	 * @var int
	 */
	var $id=null;
	/**
	 * The user id
	 * 
	 * @var int
	 */
	var $userid=null;
	/**
	 * The group id
	 * 
	 * @var int
	 */
	var $groupid=null;
	/**
	 * The session data array
	 * 
	 * @var array
	 */
	var $data=array();
	/**
	 * Modified datetime in MySQL format - date("Y-m-d H:i:s")
	 * 
	 * @var string
	 */
	var $modified=null;
	/**
	 * Session token
	 * 
	 * @var string
	 */
	var $token=null;
	
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
	 * @return	void
	 * @since	1.0
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
	 * @return	void
	 * @since	1.0
	 */
	function load($id) {
		parent::load($id);
		
		// Unserialize data
		$this->data = unserialize($this->data);
	}
	
	/**
	 * Write session to database table
	 * 
	 * @return	void
	 * @since	1.0
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
	 * @return	void
	 * @since	1.0
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
	 * @since	1.0
	 */
	function getVar($key, $value="") {
		if (empty($this->data[$key])) {
			$this->data[$key] = $value;
			$this->write();
		}
		
		return $this->data[$key];
	}
	
	/**
	 * Get a session token, if a token isn't set yet one will be generated.
	 * 
	 * Tokens are used to secure forms from spamming attacks. Once a token
	 * has been generated the system will check the post request to see if
	 * it is present, if not it will invalidate the session.
	 * 
	 * @access	public
	 * @param	boolean $forceNew If true, force a new token to be created
	 * @return	string
	 * @since	1.0
	 */
	function getToken($forceNew = false) {
		$token = $this->token;

		//create a token
		if ($token === null || $forceNew) {
			$token = $this->_createToken(12);
			$this->setVar('token', $token);
		}

		return $token;
	}
	
	/**
	 * Create a token-string
	 * 
	 * @access	protected
	 * @param	int			$length Lenght of string.
	 * @return	string		$id Generated token.
	 * @since	1.0
	 */
	function _createToken($length = 32) {
		static $chars = '0123456789abcdef';
		$max = strlen( $chars ) - 1;
		$token = '';
		$name = session_name();
		
		for($i=0; $i<$length; ++$i) {
			$token .= $chars[ (rand( 0, $max )) ];
		}

		return md5($token.$name);
	}
}
?>