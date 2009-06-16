<?php
/**
 * @version 	$Id$
 * @package		PHPFrame
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

$frontcontroller = PHPFrame::getFrontController();

require_once 'PHPUnit/Framework.php';

class testLoginController extends PHPUnit_Framework_TestCase {
	function setUp() {
		PHPFrame::Request()->setComponentName('com_login');
    }
    
	function tearDown() {
		PHPFrame::Request()->destroy();
     	PHPFrame_Base_Singleton::destroyInstance('loginController');
    }
    
    function test_login() {
    	// Manually logut current CLI user
    	$user = new PHPFrame_User();
    	$user->set('id', 0);
    	$user->set('groupid', 0);
    	$session = PHPFrame::Session();
    	$session->setUser($user);
    	
    	// Fake posted form data
    	PHPFrame::Request()->setAction('login');
    	PHPFrame::Request()->set('username', 'admin');
    	PHPFrame::Request()->set('password', 'Passw0rd');
    	PHPFrame::Request()->set(PHPFrame_Utils_Crypt::getToken(), '1');
    	
    	$frontcontroller = PHPFrame::getFrontController();
		$frontcontroller->run();
    	    	
    	$controller = PHPFrame_MVC_ActionController::getInstance('com_login');
    	$this->assertTrue($controller->getSuccess());
    }
    
    function test_reset_password() {
    	// Add a project to db to then delete it
    	$db = PHPFrame::DB();
    	$query = "UPDATE #__users SET email = 'notifications.test@extranetoffice.org' WHERE id = 62";
		$db->query($query);
		
    	// Fake posted form data
    	PHPFrame::Request()->setAction('reset_password');
    	PHPFrame::Request()->set('email_forgot', 'notifications.test@extranetoffice.org');
    	PHPFrame::Request()->set(PHPFrame_Utils_Crypt::getToken(), '1');
    	
    	$frontcontroller = PHPFrame::getFrontController();
    	$frontcontroller->run();
    	
    	$controller = PHPFrame_MVC_ActionController::getInstance('com_login');
    	$this->assertTrue($controller->getSuccess());
    }
}
