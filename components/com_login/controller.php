<?php
/**
* @package		ExtranetOffice
* @subpackage	com_login
* @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
* @license		BSD revised. See LICENSE.
* @author 		Luis Montero [e-noise.com]
* @version 		1.0.0
*/

class loginController extends controller {
	function __construct() {
		// set default view if none has been set
		$view = request::getVar('view', '');
		if (empty($view)) {
			request::setVar('view', 'login');
		}
	}
	
	function login() {
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
	
}
?>