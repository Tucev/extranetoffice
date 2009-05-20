<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	user
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * User Class
 *
 * @package		phpFrame
 * @subpackage 	user
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class phpFrame_User {
	private $_row=null;
	
	/**
	 * Constructor
	 * 
	 * @return	void
	 * @since	1.0
	 */
	public function __construct() {
		$this->_row = new phpFrame_Database_Row("#__users");
	}
	
	public function __get($key) {
		return $this->get($key);
	}
	
	public function get($key) {
		return $this->_row->get($key);
	}
	
	public function set($key, $value) {
		$this->_row->set($key, $value);
	}
	
	/**
	 * Load user row by id
	 * 
	 * This method overrides the inherited load method.
	 * 
	 * @access	public
	 * @param	int		$id 		The row id.
	 * @param	string	$exclude 	A list of key names to exclude from binding process separated by commas.
	 * @return	mixed	The loaded row object of FALSE on failure.
	 * @since 	1.0
	 */
	public function load($id, $exclude='password') {
		if (!$this->_row->load($id, $exclude)) {
			return false;
		}
		else {
			return $this;	
		}
	}
	
	/**
	 * Store user
	 * 
	 * This method overrides the inherited store method in order to encrypt the password before storing.
	 * 
	 * @access	public
	 * @return	bool	Returns TRUE on success or FALSE on failure.
	 * @since 	1.0
	 */
	public function store() {
		// Before we store new users we check whether email already exists in db
		if (empty($row->id) && $this->_emailExists($row->email)) {
			$this->_error[] = _PHPFRAME_LANG_EMAIL_ALREADY_REGISTERED;
			return false;
		}
		
		// Encrypt password for storage
		if (property_exists($row, 'password') && !is_null($row->password)) {
			$salt = phpFrame_Utils_Crypt::genRandomPassword(32);
			$crypt = phpFrame_Utils_Crypt::getCryptedPassword($row->password, $salt);
			$row->password = $crypt.':'.$salt;
		}
		
		// Invoke row store() method to store row in db
		return $this->_row->store($row);
	}
	
	private function _emailExists($email) {
		$query = "SELECT id FROM #__users WHERE email = '".$email."'";
		phpFrame::getDB()->setQuery($query);
		$id = phpFrame::getDB()->loadResult();
		
		return ($id > 0);
	}
}
