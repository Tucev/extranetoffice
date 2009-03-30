<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_login
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * projectsController Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_login
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class loginController extends controller {
	/**
	 * Constructor
	 * 
	 * This method overrides the parent constructor to avoid checking for permissions. 
	 * It is the login component, so we do not need to check access levels.
	 * 
	 * @return void
	 */
	function __construct() {
		// set default request vars
		$this->option = request::getVar('option');
		$this->task = request::getVar('task', 'display');
		$this->view = request::getVar('view', 'login');
		$this->layout = request::getVar('layout');
		
		// Override the permission check
		$this->permissions->is_allowed = true;
	}
	
	function login() {
		// Check for request forgeries
		crypt::checkToken() or exit( 'Invalid Token' );
		
		// Push model into controller
		$model = $this->getModel('login');
		
		// if user is not logged on we attemp to login
		$session =& factory::getSession();
		if (empty($session->userid)) {
			$username = request::getVar('username', '');
			$password = request::getVar('password', '');
			$model->login($username, $password);
			$this->setRedirect('index.php');
		}	
	}
	
	function logout() {
		// Push model into controller
		$model = $this->getModel('login');
		$model->logout();
		$this->setRedirect('index.php');
	}
	
	function reset_password() {
		// Check for request forgeries
		crypt::checkToken() or exit( 'Invalid Token' );
		
		$email = request::getVar('email_forgot', '');
		
		// Push model into controller
		$model = $this->getModel('login');
		if (!$model->resetPassword($email)) {
			error::raise('', 'warning', $model->getLastError());
		}
		else {
			error::raise('', 'message', _LANG_RESET_PASS_SUCCESS);
		}
		
		$this->setRedirect('index.php');
	}
	
}
?>