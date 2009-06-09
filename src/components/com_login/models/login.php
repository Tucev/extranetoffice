<?php
/**
 * @version 	$Id$
 * @package		PHPFrame
 * @subpackage	com_login
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * loginModelLogin Class
 * 
 * @package		PHPFrame
 * @subpackage 	com_login
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		PHPFrame_Application_Model
 */
class loginModelLogin extends PHPFrame_Application_Model {
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
		$db = PHPFrame::getDB();
		$query = "SELECT id, password FROM #__users WHERE username = '".$username."'";
		$credentials = $db->loadObject($query);
		
		// User exists
		if (is_object($credentials) && isset($credentials->id)) {
			$user = new PHPFrame_User();
			$user->load($credentials->id);
			
			// check password
			$parts	= explode( ':', $credentials->password );
			$crypt	= $parts[0];
			$salt	= @$parts[1];
			$testcrypt = PHPFrame_Utils_Crypt::getCryptedPassword($password, $salt);
			if ($crypt == $testcrypt) {
				// Store user data in session
				$session = PHPFrame::getSession();
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
		PHPFrame::getSession()->destroy();
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
		$userid = PHPFrame::getDB()->loadResult($query);
		
		if (!empty($userid)) {
			// Create user object
			$user = new PHPFrame_User();
			$user->load($userid, 'password');
			// Generate random password and store in local variable to be used when sending email to user.
			$password = PHPFrame_Utils_Crypt::genRandomPassword();
			// Assign newly generated password to row object (this password will be encrypted when stored).
			$user->set('password', $password);
			
			if (!$user->store()) {
				$this->_error[] = $user->getLastError();
				return false;
			}
			
			// Send notification to new users
			$uri = PHPFrame::getURI();
			
			$new_mail = new PHPFrame_Mail_Mailer();
			$new_mail->AddAddress($user->email, PHPFrame_User_Helper::fullname_format($user->firstname, $user->lastname));
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
