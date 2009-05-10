<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	environment
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
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
class phpFrame_Environment_Session extends phpFrame_Database_Table {
	/**
	 * The session id
	 * 
	 * @var int
	 */
	protected $id=null;
	/**
	 * The user id
	 * 
	 * @var int
	 */
	protected $userid=null;
	/**
	 * The group id
	 * 
	 * @var int
	 */
	protected $groupid=null;
	/**
	 * The session data array
	 * 
	 * @var array
	 */
	protected $data=array();
	/**
	 * Modified datetime in MySQL format - date("Y-m-d H:i:s")
	 * 
	 * @var string
	 */
	protected $modified=null;
	/**
	 * Session token
	 * 
	 * @var string
	 */
	private $_token=null;
	
	/**
	 * Constructor
	 * 
	 * Invoke the parent (table) constructor and start the session.
	 *
	 */
	protected function __construct() {
		parent::__construct('#__session', 'id');
		$this->_start();
	}
	
	/**
	 * 
	 * @param	object	$user	User object of type phpFrame_User
	 * 
	 * @return	void
	 */
	public function setUser(phpFrame_User $user) {
		$this->userid = $user->id;
		$this->groupid = $user->groupid;
		$this->_write();
	}
	
	/**
	 * Get session user id
	 * 
	 * @return	int
	 */
	public function getUserId() {
		return $this->userid;
	}
	
	/**
	 * Get session group id
	 * 
	 * @return	int
	 */
	public function getGroupId() {
		return $this->groupid;
	}
	
	/**
	 * Is the current session authenticated?
	 * 
	 * @access	public
	 * @return	boolean	Returns TRUE if user is authenticated and FALSE otherwise.
	 * @since	1.0
	 */
	public function isAuth() {
		if ($this->userid > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Is the current session an admin session?
	 * 
	 * @return	boolean
	 */
	public function isAdmin() {
		if ($this->groupid == 1) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Set session variable
	 * 
	 * @access	public
	 * @param string $key The key where to store the value.
	 * @param mixed $value This can be any value, an integer, string, array, object...
	 * @return	void
	 * @since	1.0
	 */
	public function setVar($key, $value) {
		$this->data[$key] = $value;
		$this->_write();
	}
	
	/**
	 * Get session variable
	 *
	 * @access	public
	 * @param	string	$key	The key or session variable name.
	 * @param	mixed	$value	This parameter is used to give a default value in case the variable is empty.
	 * @return	mixed
	 * @since	1.0
	 */
	public function getVar($key, $value="") {
		if (empty($this->data[$key])) {
			$this->data[$key] = $value;
			$this->_write();
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
	public function getToken($force_new = false) {
		$token = $this->_token;

		//create a token
		if ($token === null || $force_new) {
			$token = $this->_createToken(12);
			$this->setVar('token', $token);
		}

		return $token;
	}
	
	/**
	 * Start session
	 * 
	 * Start php session and load application session accordingly.
	 * 
	 * @access	private
	 * @return	void
	 * @since	1.0
	 */
	private function _start() {
		// start php session
		session_start();
		// load session data from db
		$this->_load(session_id());
		// If new session we set the id
		if (!$this->id) {
			$this->id = session_id();
			$this->userid = 0;
			$this->groupid = 0;
			$this->_token = $this->getToken(true);
		}
		// Write session data to db
		$this->_write();
	}
	
	/**
	 * Load session data from database
	 *
	 * @param string $id The php session id
	 * @return	void
	 * @since	1.0
	 */
	private function _load($id) {
		parent::load($id);
		
		// Unserialize data
		if (is_string($this->data)) {
			$this->data = unserialize($this->data);	
			// get session token from data array
			if (!empty($this->data['token'])) {
				$this->_token = $this->data['token'];
			}
		}
	}
	
	/**
	 * Write session to database table
	 * 
	 * @access	private
	 * @return	void
	 * @since	1.0
	 */
	private function _write() {
		// Serialize data array to store in db
		$this->data = serialize($this->data);
		$this->modified = date("Y-m-d H:i:s");
		
		if (!$this->check()) {
			throw new phpFrame_Exception($this->getLastError());
		}
		
		if (!$this->store()) {
			throw new phpFrame_Exception($this->getLastError());
		}
		
		// Unserialize data after storing
		$this->data = unserialize($this->data);
	}
	
	/**
	 * Create a token-string
	 * 
	 * @access	private
	 * @param	int			$length Lenght of string.
	 * @return	string		$id Generated token.
	 * @since	1.0
	 */
	private function _createToken($length = 32) {
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
