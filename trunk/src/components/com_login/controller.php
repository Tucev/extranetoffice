<?php
/**
 * @version 	$Id$
 * @package		phpFrame
 * @subpackage	com_login
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * projectsController Class
 * 
 * @package		phpFrame
 * @subpackage 	com_login
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class loginController extends phpFrame_Application_ActionController {
	/**
	 * Constructor
	 * 
	 * @return void
	 */
	protected function __construct() {
		// Invoke parent's constructor to set default action
		parent::__construct('get_login_form');
	}
	
	public function get_login_form() {
		// Get view
		$view = $this->getView('login', '');
		// Display view
		$view->display();
	}
	
	public function login() {
		// Check for request forgeries
		phpFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		// if user is not logged on we attemp to login
		$session = phpFrame::getSession();
		if (!$session->isAuth()) {
			$username = phpFrame::getRequest()->get('username', '');
			$password = phpFrame::getRequest()->get('password', '');
			
			// Get login model
			$model = $this->getModel('login');
			if (!$model->login($username, $password)) {
				$this->_sysevents->setSummary($model->getLastError(), "warning");
			}
			
			$this->_success = true;
		}
		
		$this->setRedirect('index.php');
	}
	
	public function logout() {
		// Logout using model
		$this->getModel('login')->logout();
		
		// Redirect
		$this->setRedirect('index.php');
	}
	
	public function reset_password() {
		// Check for request forgeries
		phpFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		$email = phpFrame::getRequest()->get('email_forgot', '');
		
		// Push model into controller
		$model = $this->getModel('login');
		if (!$model->resetPassword($email)) {
			$this->_sysevents->setSummary($model->getLastError(), "warning");
		}
		else {
			$this->_sysevents->setSummary(_LANG_RESET_PASS_SUCCESS, "success");
			$this->_success = true;
		}
		
		$this->setRedirect('index.php');
	}
	
}
