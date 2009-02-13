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

class user extends table {
	var $id=null;
	var $groupid=null;
	var $username=null;
	var $email=null;
	
	function __construct() {
		$db = factory::getDB();
		parent::__construct($db, '#__users', 'id');
	}
	
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
	}
	
	function save() {
		// Encrypt password
		$salt = crypt::genRandomPassword(32);
		$crypt = crypt::getCryptedPassword($password, $salt);
		$encrypted_password = $crypt.':'.$salt;
	}
	
	function getGroup($userid) {
		$query = "SELECT * FROM #__users_groups WHERE userid = '".$userid."'";
		$this->db->setQuery($query);
		$users_groups = $this->db->loadObject();
		return $users_groups->groupid;
	}
}
?>