<?php
/**
 * @version 	$Id$
 * @package		PHPFrame
 * @subpackage 	PHPUnit_test_suite
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

define( 'DS', DIRECTORY_SEPARATOR );
define('_ABS_PATH_TEST', str_replace(DS."components".DS."com_users", "", dirname(__FILE__)) );

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

class testUsersController extends PHPUnit_Framework_TestCase {
	function setUp() {
		PHPFrame::getRequest()->setComponentName('com_users');
    }
    
	function tearDown() {
		PHPFrame::getRequest()->destroy();
     	PHPFrame_Base_Singleton::destroyInstance('usersController');
    }
    
    function test_save_user() {
    	// Fake posted form data
    	PHPFrame::getRequest()->setAction('save_user');
    	PHPFrame::getRequest()->set('id', 62);
    	PHPFrame::getRequest()->set('username', 'testuser');
    	PHPFrame::getRequest()->set('email', 'testuser@extranetoffice.org');
    	PHPFrame::getRequest()->set('firstname', 'test');
    	PHPFrame::getRequest()->set('lastname', 'user');
    	PHPFrame::getRequest()->set('groupid', '1');
    	PHPFrame::getRequest()->set(PHPFrame_Utils_Crypt::getToken(), '1');
    	
    	$frontcontroller = PHPFrame::getFrontController();
		$frontcontroller->run();
    	    	
    	$controller = PHPFrame::getActionController('com_users');
    	$this->assertTrue($controller->getSuccess());
    }
}
