<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	PHPUnit_test_suite
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

define( 'DS', DIRECTORY_SEPARATOR );
define('_ABS_PATH_TEST', str_replace(DS."components".DS."com_login", "", dirname(__FILE__)) );

// Require test helper class
require_once _ABS_PATH_TEST.DS."test.helper.php";
// Prepare application for tests. This sets constants and include config and autoloader
testHelper::prepareApplication();
// Reset installation before running tests. This will reset database and filesystem.
try { 
	testHelper::freshInstall(); 
} 
catch (Exception $e) { 
	throw $e; 
}

$frontcontroller = phpFrame::getFrontController();

require_once 'PHPUnit/Framework.php';

class testLoginController extends PHPUnit_Framework_TestCase {
	function setUp() {
		phpFrame_Environment_Request::setComponentName('com_login');
    }
    
	function tearDown() {
		phpFrame_Environment_Request::destroy();
     	phpFrame_Base_Singleton::destroyInstance('loginController');
    }
    
    function test_login() {
    	// Manually logut current CLI user
    	$user = phpFrame::getUser();
    	$user->id = 0;
    	$user->groupid = 0;
    	$session = phpFrame::getSession();
    	$session->setUser($user);
    	
    	// Fake posted form data
    	phpFrame_Environment_Request::setAction('login');
    	phpFrame_Environment_Request::setVar('username', 'admin');
    	phpFrame_Environment_Request::setVar('password', 'Passw0rd');
    	phpFrame_Environment_Request::setVar(phpFrame_Utils_Crypt::getToken(), '1');
    	
    	$frontcontroller = phpFrame::getFrontController();
		$frontcontroller->run();
    	    	
    	$controller = phpFrame::getActionController('com_login');
    	$this->assertTrue($controller->getSuccess());
    }
    
    function test_reset_password() {
    	// Add a project to db to then delete it
    	$db = phpFrame::getDB();
    	$query = "UPDATE #__users SET email = 'notifications.test@extranetoffice.org' WHERE id = 62";
		$db->setQuery($query);
		$db->query();
		
    	// Fake posted form data
    	phpFrame_Environment_Request::setAction('reset_password');
    	phpFrame_Environment_Request::setVar('email_forgot', 'notifications.test@extranetoffice.org');
    	phpFrame_Environment_Request::setVar(phpFrame_Utils_Crypt::getToken(), '1');
    	
    	$frontcontroller = phpFrame::getFrontController();
    	$frontcontroller->run();
    	
    	$controller = phpFrame::getActionController('com_login');
    	$this->assertTrue($controller->getSuccess());
    }
}
