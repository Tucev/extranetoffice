<?php
/**
 * @version 	$Id$
 * @package		phpFrame
 * @subpackage	com_login
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

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
			$user = phpFrame::getUser();
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
		phpFrame::getDB()->setQuery($query);
		$userid = phpFrame::getDB()->loadResult();
		
		if (!empty($userid)) {
			// Create standard object to store user properties
			// We do this because we dont want to overwrite the current user object.
			$row = new phpFrame_Base_StdObject();
			$this->_user->load($userid, 'password', $row);
			// Generate random password and store in local variable to be used when sending email to user.
			$password = phpFrame_Utils_Crypt::genRandomPassword();
			// Assign newly generated password to row object (this password will be encrypted when stored).
			$row->password = $password;
			
			if (!$this->_user->check($row)) {
				$this->_error[] = $this->_user->getLastError();
				return false;
			}
			
			if (!$this->_user->store($row)) {
				$this->_error[] = $this->_user->getLastError();
				return false;
			}
			
			// Send notification to new users
			$uri = phpFrame::getURI();
			
			$new_mail = new phpFrame_Mail_Mailer();
			$new_mail->AddAddress($row->email, phpFrame_User_Helper::fullname_format($row->firstname, $row->lastname));
			$new_mail->Subject = _LANG_USER_RESET_PASS_NOTIFY_SUBJECT;
			$new_mail->Body = sprintf(_LANG_USER_RESET_PASS_NOTIFY_BODY, 
										 $row->firstname, 
										 $uri->getBase(), 
										 $row->username, 
										 $password
								);
										   
			if ($new_mail->Send() !== true) {
				$this->_error[] = sprintf(_LANG_EMAIL_NOT_SENT, $row->email);
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
?>