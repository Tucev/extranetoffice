<?php
/**
 * @version 	$Id$
 * @package		phpFrame
 * @subpackage	com_login
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * loginModelLogin Class
 * 
 * @package		phpFrame
 * @subpackage 	com_login
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		phpFrame_Application_Model
 */
class loginModelLogin extends phpFrame_Application_Model {
	/**
	 * Constructor
	 * 
	 * @return	void
	 */
	public function __construct() {}
	
	/**
	 * Log in
	 * 
	 * @param	string	$username
	 * @param	string	$password
	 * @return	boolean
	 */
	public function login($username, $password) {
		$db = phpFrame::getDB();
		$query = "SELECT id, password FROM #__users WHERE username = '".$username."'";
		$db->setQuery($query);
		$credentials = $db->loadObject();
		
		// User exists
		if (is_object($credentials) && isset($credentials->id)) {
			$user = new phpFrame_User();
			$user->load($credentials->id);
			
			// check password
			$parts	= explode( ':', $credentials->password );
			$crypt	= $parts[0];
			$salt	= @$parts[1];
			$testcrypt = phpFrame_Utils_Crypt::getCryptedPassword($password, $salt);
			if ($crypt == $testcrypt) {
				// Store user data in session
				$session = phpFrame::getSession();
				$session->setUser($user);
				return true;
			} else {
				// Wrong password
				$this->_error[] = "Authorisation failed: Wrong password";
				return false;
			}
		}
		else {
			// Username not found
			$this->_error[] = "Authorisation failed: Username not found";
			return false;
		}
	}
	
	public function logout() {
		phpFrame::getSession()->destroy();
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
		phpFrame::getDB()->setQuery($query);
		$userid = phpFrame::getDB()->loadResult();
		
		if (!empty($userid)) {
			// Create user object
			$user = new phpFrame_User();
			$user->load($userid, 'password');
			// Generate random password and store in local variable to be used when sending email to user.
			$password = phpFrame_Utils_Crypt::genRandomPassword();
			// Assign newly generated password to row object (this password will be encrypted when stored).
			$user->set('password', $password);
			
			if (!$user->store()) {
				$this->_error[] = $user->getLastError();
				return false;
			}
			
			// Send notification to new users
			$uri = phpFrame::getURI();
			
			$new_mail = new phpFrame_Mail_Mailer();
			$new_mail->AddAddress($user->email, phpFrame_User_Helper::fullname_format($user->firstname, $user->lastname));
			$new_mail->Subject = _LANG_USER_RESET_PASS_NOTIFY_SUBJECT;
			$new_mail->Body = sprintf(_LANG_USER_RESET_PASS_NOTIFY_BODY, 
										 $user->firstname, 
										 $uri->getBase(), 
										 $user->username, 
										 $password
								);
										   
			if ($new_mail->Send() !== true) {
				$this->_error[] = sprintf(_LANG_EMAIL_NOT_SENT, $user->email);
				return false;
			}
			
			return true;
		}
		else {
			$this->_error[] = _LANG_RESET_PASS_EMAIL_NOT_FOUND;
			return false;
		}
	}
	
}
