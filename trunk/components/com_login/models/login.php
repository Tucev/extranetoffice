<?php
/**
* @package		ExtranetOffice
* @subpackage	com_login
* @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
* @license		BSD revised. See LICENSE.
* @author 		Luis Montero [e-noise.com]
* @version 		1.0.0
*/

class loginModelLogin extends model {
	
	function login($username, $password) {
		$db =& factory::getDB();
		$query = "SELECT id, password FROM #__users WHERE username = '".$username."'";
		$db->setQuery($query);
		$credentials = $db->loadObject();
		
		// User exists
		if ($credentials->id) {
			$user =& factory::getUser();
			$user->load($credentials->id);
			
			// check password
			$parts	= explode( ':', $credentials->password );
			$crypt	= $parts[0];
			$salt	= @$parts[1];
			$testcrypt = crypt::getCryptedPassword($password, $salt);
			if ($crypt == $testcrypt) {
				// Store user data in session
				$session = factory::getSession();
				$session->userid = $user->id;
				$session->groupid = $user->groupid;
				$session->write();
				return true;
			} else {
				// Wrong password
				error::raise('401', 'error', "Authorisation failed: Wrong password");
				return false;
			}
		}
		else {
			// Username not found
			error::raise('401', 'error', "Authorisation failed: Username not found");
			return false;
		}
	}
	
	function logout() {
		session_regenerate_id(true); // this destroys the session and generates a new session id
		unset($GLOBALS['application']->session);
		unset($GLOBALS['application']->user);
	}
	
}
?>