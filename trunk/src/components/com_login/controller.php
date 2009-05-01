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
	 * This method overrides the parent constructor to avoid checking for permissions. 
	 * It is the login component, so we do not need to check access levels.
	 * 
	 * @return void
	 */
	function __construct() {
		// set default request vars
		$this->component = phpFrame_Environment_Request::getComponentName();
		$this->action = phpFrame_Environment_Request::getAction('display');
		$this->view = phpFrame_Environment_Request::getViewName('login');
		$this->layout = phpFrame_Environment_Request::getLayout();
		
		parent::__construct();
	}
	
	function login() {
		// Check for request forgeries
		phpFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		// Push model into controller
		$model = $this->getModel('login');
		
		// if user is not logged on we attemp to login
		$session = phpFrame::getSession();
		if (empty($session->userid)) {
			$username = phpFrame_Environment_Request::getVar('username', '');
			$password = phpFrame_Environment_Request::getVar('password', '');
			
			if (!$model->login($username, $password)) {
				$this->_sysevents->setSummary($model->getLastError(), "warning");
			}
			
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
		phpFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		$email = phpFrame_Environment_Request::getVar('email_forgot', '');
		
		// Push model into controller
		$model = $this->getModel('login');
		if (!$model->resetPassword($email)) {
			$this->_sysevents->setSummary($model->getLastError(), "warning");
		}
		else {
			$this->_sysevents->setSummary(_LANG_RESET_PASS_SUCCESS, "success");
		}
		
		$this->setRedirect('index.php');
	}
	
}
?>