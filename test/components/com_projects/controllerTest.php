<?php
/**
 * @version 	$Id$
 * @package		phpFrame
 * @subpackage 	PHPUnit_test_suite
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

define( 'DS', DIRECTORY_SEPARATOR );
define('_ABS_PATH_TEST', str_replace(DS."components".DS."com_projects", "", dirname(__FILE__)) );

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

class testProjectsController extends PHPUnit_Framework_TestCase {
	function setUp() {
		phpFrame::getRequest()->setComponentName('com_projects');
    }
    
	function tearDown() {
		phpFrame::getRequest()->destroy();
     	phpFrame_Base_Singleton::destroyInstance('projectsController');
    }
    
    function test_save_project() {
    	// Fake posted form data
    	phpFrame::getRequest()->setAction('save_project');
    	phpFrame::getRequest()->set('name', 'This is a test project');
    	phpFrame::getRequest()->set('project_type', '1');
    	phpFrame::getRequest()->set('priority', '1');
    	phpFrame::getRequest()->set(phpFrame_Utils_Crypt::getToken(), '1');
    	
    	$frontcontroller = phpFrame::getFrontController();
		$frontcontroller->run();
    	    	
    	$controller = phpFrame::getActionController('com_projects');
    	$this->assertTrue($controller->getSuccess());
    }
    
    function test_remove_project() {
    	// Add a project to db to then delete it
    	$db = phpFrame::getDB();
    	$query = "INSERT INTO #__projects (id, name, project_type, priority, created_by) VALUES (NULL, 'another project', '2', '1', '1')";
		$projectid = $db->setQuery($query)->query();
		// Add project admin
		$query = "INSERT INTO #__users_roles (id, userid, roleid, projectid) VALUES (NULL, '1', '1', '".$projectid."')";
		$db->setQuery($query)->query();
		
    	// Fake posted form data
    	phpFrame::getRequest()->setAction('remove_project');
    	phpFrame::getRequest()->set('projectid', $projectid);
    	
    	$frontcontroller = phpFrame::getFrontController();
    	$frontcontroller->run();
    	
    	$controller = phpFrame::getActionController('com_projects');
    	$this->assertTrue($controller->getSuccess());
    }
    
    function test_save_member_InviteNewUser() {
    	// Fake posted form data
    	phpFrame::getRequest()->setAction('save_member');
    	phpFrame::getRequest()->set('projectid', '1');
    	phpFrame::getRequest()->set('roleid', '1');
    	phpFrame::getRequest()->set('username', 'testuser');
    	phpFrame::getRequest()->set('firstname', 'Test');
    	phpFrame::getRequest()->set('lastname', 'User');
    	phpFrame::getRequest()->set('groupid', '2');
    	phpFrame::getRequest()->set('email', 'notifications.test@extranetoffice.org');
    	phpFrame::getRequest()->set(phpFrame_Utils_Crypt::getToken(), '1');
    	
    	$frontcontroller = phpFrame::getFrontController();
    	$frontcontroller->run();
    	
    	$controller = phpFrame::getActionController('com_projects');
    	$this->assertTrue($controller->getSuccess());
    	
    }
    
	function test_save_member_ExistingUser() {
		// Add an existing member to be able to assign to project
		$db = phpFrame::getDB();
		$query = "INSERT INTO `#__users` (`id`, `groupid`, `username`, `password`, `email`, `firstname`, `lastname`,`photo`";
		$query .= ", `notifications`, `show_email`, `block`, `created`, `last_visit`, `activation`, `params`, `ts`, `deleted`)";
		$query .= " VALUES (NULL, '2', 'testuser2', '', 'notifications.test@extranetoffice.org', 'test', 'user 2', 'default.png'";
		$query .= ", '1', '1', '0', '', NULL , NULL , NULL , CURRENT_TIMESTAMP, NULL)";
		$db->setQuery($query);
		$userid = $db->query();
		
    	// Fake posted form data
    	phpFrame::getRequest()->setAction('save_member');
    	phpFrame::getRequest()->set('projectid', '1');
    	phpFrame::getRequest()->set('roleid', '1');
    	phpFrame::getRequest()->set('userids', $userid);
    	phpFrame::getRequest()->set(phpFrame_Utils_Crypt::getToken(), '1');
    	
    	$frontcontroller = phpFrame::getFrontController();
    	$frontcontroller->run();
    	
    	$controller = phpFrame::getActionController('com_projects');
    	$this->assertTrue($controller->getSuccess());
    	
    }
    
    function test_remove_member() {
    	// Add an user to then delete it in this test
		$db = phpFrame::getDB();
		$query = "INSERT INTO `#__users` (`id`, `groupid`, `username`, `password`, `email`, `firstname`, `lastname`,`photo`";
		$query .= ", `notifications`, `show_email`, `block`, `created`, `last_visit`, `activation`, `params`, `ts`, `deleted`)";
		$query .= " VALUES (NULL, '2', 'testuser3', '', 'notifications.test@extranetoffice.org', 'test', 'user 3', 'default.png'";
		$query .= ", '1', '1', '0', '', NULL , NULL , NULL , CURRENT_TIMESTAMP, NULL)";
		$db->setQuery($query);
		$userid = $db->query();
		// make user a project member
		$query = "INSERT INTO #__users_roles (id, userid, roleid, projectid) VALUES (NULL, '".$userid."', '2', '1')";
		$db->setQuery($query);
		$db->query();
		
    	// Fake posted form data
    	phpFrame::getRequest()->setAction('remove_member');
    	phpFrame::getRequest()->set('projectid', '1');
    	phpFrame::getRequest()->set('userid', $userid);
    	
    	$frontcontroller = phpFrame::getFrontController();
    	$frontcontroller->run();
    	
    	$controller = phpFrame::getActionController('com_projects');
    	$this->assertTrue($controller->getSuccess());
    }
    
    function test_change_member_role() {
    	// Fake posted form data
    	phpFrame::getRequest()->setAction('change_member_role');
    	phpFrame::getRequest()->set('projectid', '1');
    	phpFrame::getRequest()->set('userid', '63');
    	phpFrame::getRequest()->set('roleid', '3');
    	
    	$frontcontroller = phpFrame::getFrontController();
    	$frontcontroller->run();
    	
    	$controller = phpFrame::getActionController('com_projects');
    	$this->assertTrue($controller->getSuccess());
    }
    /*
    function test_save_issue() {
    	// Fake posted form data
    	phpFrame::getRequest()->setAction('save_issue');
    	phpFrame::getRequest()->set('projectid', '1');
    	phpFrame::getRequest()->set('title', 'Test issue');
    	phpFrame::getRequest()->set('issue_type', '0');
    	phpFrame::getRequest()->set('priority', '1');
    	phpFrame::getRequest()->set('access', '1');
    	phpFrame::getRequest()->set(phpFrame_Utils_Crypt::getToken(), '1');
    	
    	$frontcontroller = phpFrame::getFrontController();
    	$frontcontroller->run();
    	
    	$controller = phpFrame::getActionController('com_projects');
    	$this->assertTrue($controller->getSuccess());
    }
    */
}
