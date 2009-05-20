<?php
/**
 * @version 	$Id$
 * @package		phpFrame
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

$frontcontroller = phpFrame::getFrontController();

require_once 'PHPUnit/Framework.php';

class testUsersController extends PHPUnit_Framework_TestCase {
	function setUp() {
		phpFrame::getRequest()->setComponentName('com_users');
    }
    
	function tearDown() {
		phpFrame::getRequest()->destroy();
     	phpFrame_Base_Singleton::destroyInstance('usersController');
    }
    
    function test_save_user() {
    	// Fake posted form data
    	phpFrame::getRequest()->setAction('save_user');
    	phpFrame::getRequest()->set('id', 62);
    	phpFrame::getRequest()->set('username', 'testuser');
    	phpFrame::getRequest()->set('email', 'testuser@extranetoffice.org');
    	phpFrame::getRequest()->set('firstname', 'test');
    	phpFrame::getRequest()->set('lastname', 'user');
    	phpFrame::getRequest()->set('groupid', '1');
    	phpFrame::getRequest()->set(phpFrame_Utils_Crypt::getToken(), '1');
    	
    	$frontcontroller = phpFrame::getFrontController();
		$frontcontroller->run();
    	    	
    	$controller = phpFrame::getActionController('com_users');
    	$this->assertTrue($controller->getSuccess());
    }
}
