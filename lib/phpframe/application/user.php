<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	application
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * User Class
 *
 * @package		phpFrame
 * @subpackage 	application
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class user extends table {
	var $id=null;
	var $groupid=null;
	var $username=null;
	var $email=null;
	var $firstname=null;
	var $lastname=null;
	var $name=null;
	var $name_abbr=null;
	
	/**
	 * Constructor
	 * 
	 * @return	void
	 * @since	1.0
	 */
	function __construct() {
		parent::__construct('#__users', 'id');
	}
	
	/**
	 * Load user row by id
	 * 
	 * This method overrides the inherited load method in order to load the user group as well.
	 * 
	 * @return	The loaded user object
	 * @see		lib/phpframe/database/table#load($id)
	 * @since	1.0
	 */
	function load($id) {
		$query = "SELECT * FROM #__users WHERE id = '".$id."'";
		$this->db->setQuery($query);
		$user = $this->db->loadObject();
		foreach ($this->cols as $col) {
			$col_name = $col->Field;
			$col_value = $user->$col_name;
			$this->$col_name = $col_value;
		}
		$this->groupid = $this->getGroup($id);
		$this->name = $this->firstname.' '.$this->lastname;
		$this->name_abbr = substr($this->firstname, 0, 1).'. '.$this->lastname;
		
		return $this;
	}
	
	/**
	 * Save user to database (work in progress)
	 * 
	 * @todo	This method needs to be finished.
	 * @return 	void
	 * @since	1.0
	 */
	function save() {
		// Encrypt password
		$salt = crypt::genRandomPassword(32);
		$crypt = crypt::getCryptedPassword($password, $salt);
		$encrypted_password = $crypt.':'.$salt;
	}
	
	/**
	 * Get group for given user
	 * 
	 * @param	int	$userid
	 * @return 	int
	 * @since	1.0
	 */
	function getGroup($userid) {
		$query = "SELECT * FROM #__users_groups WHERE userid = '".$userid."'";
		$this->db->setQuery($query);
		$users_groups = $this->db->loadObject();
		return $users_groups->groupid;
	}
}
?>