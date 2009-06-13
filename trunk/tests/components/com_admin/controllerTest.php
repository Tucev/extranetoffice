<?php
/**
 * @version 	$Id$
 * @package		PHPFrame
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

$frontcontroller = PHPFrame::getFrontController();

require_once 'PHPUnit/Framework.php';

class testAdminController extends PHPUnit_Framework_TestCase {
	function setUp() {
		PHPFrame::Request()->setComponentName('com_admin');
    }
    
	function tearDown() {
		PHPFrame::Request()->destroy();
     	PHPFrame_Base_Singleton::destroyInstance('adminController');
    }
    
    function test_save_user_new() {
    	// Fake posted form data
    	PHPFrame::Request()->setAction('save_user');
    	PHPFrame::Request()->set('id', '');
    	PHPFrame::Request()->set('username', 'testuser');
    	PHPFrame::Request()->set('email', 'anotheruser@extranetoffice.org');
    	PHPFrame::Request()->set('firstname', 'test');
    	PHPFrame::Request()->set('lastname', 'user');
    	PHPFrame::Request()->set('groupid', '2');
    	PHPFrame::Request()->set(PHPFrame_Utils_Crypt::getToken(), '1');
    	
    	$frontcontroller = PHPFrame::getFrontController();
		$frontcontroller->run();
    	    	
    	$controller = PHPFrame::getActionController('com_admin');
    	$this->assertTrue($controller->getSuccess());
    }
    
	function test_save_user_existing() {
    	// Fake posted form data
    	PHPFrame::Request()->setAction('save_user');
    	PHPFrame::Request()->set('id', 62);
    	PHPFrame::Request()->set('username', 'testuser');
    	PHPFrame::Request()->set('email', 'testuser@extranetoffice.org');
    	PHPFrame::Request()->set('firstname', 'test');
    	PHPFrame::Request()->set('lastname', 'user');
    	PHPFrame::Request()->set('groupid', '2');
    	PHPFrame::Request()->set(PHPFrame_Utils_Crypt::getToken(), '1');
    	
    	$frontcontroller = PHPFrame::getFrontController();
		$frontcontroller->run();
    	    	
    	$controller = PHPFrame::getActionController('com_admin');
    	$this->assertTrue($controller->getSuccess());
    }
}
