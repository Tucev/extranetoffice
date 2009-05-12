<?php
/**
 * @version 	$Id$
 * @package		phpFrame
 * @subpackage 	PHPUnit_test_suite
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

define( 'DS', DIRECTORY_SEPARATOR );
define('_ABS_PATH_TEST', str_replace(DS."components".DS."com_admin", "", dirname(__FILE__)) );

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

class testAdminController extends PHPUnit_Framework_TestCase {
	function setUp() {
		phpFrame_Environment_Request::setComponentName('com_admin');
    }
    
	function tearDown() {
		phpFrame_Environment_Request::destroy();
     	phpFrame_Base_Singleton::destroyInstance('adminController');
    }
    
    function test_save_user_new() {
    	// Fake posted form data
    	phpFrame_Environment_Request::setAction('save_user');
    	phpFrame_Environment_Request::setVar('id', '');
    	phpFrame_Environment_Request::setVar('username', 'testuser');
    	phpFrame_Environment_Request::setVar('email', 'anotheruser@extranetoffice.org');
    	phpFrame_Environment_Request::setVar('firstname', 'test');
    	phpFrame_Environment_Request::setVar('lastname', 'user');
    	phpFrame_Environment_Request::setVar('groupid', '2');
    	phpFrame_Environment_Request::setVar(phpFrame_Utils_Crypt::getToken(), '1');
    	
    	$frontcontroller = phpFrame::getFrontController();
		$frontcontroller->run();
    	    	
    	$controller = phpFrame::getActionController('com_admin');
    	$this->assertTrue($controller->getSuccess());
    }
    
	function test_save_user_existing() {
    	// Fake posted form data
    	phpFrame_Environment_Request::setAction('save_user');
    	phpFrame_Environment_Request::setVar('id', 62);
    	phpFrame_Environment_Request::setVar('username', 'testuser');
    	phpFrame_Environment_Request::setVar('email', 'testuser@extranetoffice.org');
    	phpFrame_Environment_Request::setVar('firstname', 'test');
    	phpFrame_Environment_Request::setVar('lastname', 'user');
    	phpFrame_Environment_Request::setVar('groupid', '2');
    	phpFrame_Environment_Request::setVar(phpFrame_Utils_Crypt::getToken(), '1');
    	
    	$frontcontroller = phpFrame::getFrontController();
		$frontcontroller->run();
    	    	
    	$controller = phpFrame::getActionController('com_admin');
    	$this->assertTrue($controller->getSuccess());
    }
}