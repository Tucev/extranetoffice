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
	
	public function login($username, $password) {
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
				$session =& factory::getSession();
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
	
	public function logout() {
		session_regenerate_id(true); // this destroys the session and generates a new session id
		unset($GLOBALS['application']->session);
		unset($GLOBALS['application']->user);
	}
	
	/**
	 * Reset password for user with given email address
	 * 
	 * @param	string	$email	The email address used to select the user.
	 * @return	boolean	Returns TRUE on success or FALSE on failure
	 */
	public function resetPassword($email) {
		// First we check whether there is a user with the passed email address
		$query = "SELECT id FROM #__users WHERE email = '".$email."'";
		$this->db->setQuery($query);
		$userid = $this->db->loadResult();
		
		if (!empty($userid)) {
			// Create standard object to store user properties
			// We do this because we dont want to overwrite the current user object.
			$row = new standardObject();
			$this->user->load($userid, 'password', $row);
			// Generate random password and store in local variable to be used when sending email to user.
			$password = crypt::genRandomPassword();
			// Assign newly generated password to row object (this password will be encrypted when stored).
			$row->password = $password;
			
			if (!$this->user->check($row)) {
				$this->error[] = $this->user->getLastError();
				return false;
			}
			
			if (!$this->user->store($row)) {
				$this->error[] = $this->user->getLastError();
				return false;
			}
			
			// Send notification to new users
			$uri =& factory::getURI();
			
			$new_mail = new mailer();
			$new_mail->AddAddress($row->email, usersHelper::fullname_format($row->firstname, $row->lastname));
			$new_mail->Subject = _LANG_USER_RESET_PASS_NOTIFY_SUBJECT;
			$new_mail->Body = sprintf(_LANG_USER_RESET_PASS_NOTIFY_BODY, 
										 $row->firstname, 
										 $uri->getBase(), 
										 $row->username, 
										 $password
								);
										   
			if ($new_mail->Send() !== true) {
				$this->error[] = sprintf(_LANG_EMAIL_NOT_SENT, $row->email);
				return false;
			}
				
			return true;
		}
		else {
			$this->error[] = _LANG_RESET_PASS_EMAIL_NOT_FOUND;
			return false;
		}
	}
	
}
?>