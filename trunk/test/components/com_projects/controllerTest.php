<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
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
try { testHelper::freshInstall(); } 
catch (Exception $e) { throw $e; }

// Initialise application
$application = phpFrame::getApplication();
$application->auth();

require_once 'PHPUnit/Framework.php';

class testProjectsController extends PHPUnit_Framework_TestCase {
	function setUp() {
		phpFrame_Environment_Request::setVar('option', 'com_projects');
    }
    
	function tearDown() {
		phpFrame_Environment_Request::destroy();
     	phpFrame_Base_Singleton::destroyInstance('projectsController');
    }
    
    function test_save_project() {
    	// Fake posted form data
    	phpFrame_Environment_Request::setVar('task', 'save_project');
    	phpFrame_Environment_Request::setVar('name', 'This is a test project');
    	phpFrame_Environment_Request::setVar('project_type', '1');
    	phpFrame_Environment_Request::setVar('priority', '1');
    	phpFrame_Environment_Request::setVar(phpFrame_Utils_Crypt::getToken(), '1');
    	
    	$application = phpFrame::getApplication();
		$application->exec();
    	    	
    	$controller = phpFrame::getController('com_projects');
    	$this->assertTrue($controller->getSuccess());
    }
    
    function test_remove_project() {
    	// Add a project to db to then delete it
    	$db = phpFrame::getDB();
    	$query = "INSERT INTO #__projects (id, name, project_type, priority) VALUES (NULL, 'another project', '2', '1')";
		$db->setQuery($query);
		$projectid = $db->query();
		
    	// Fake posted form data
    	phpFrame_Environment_Request::setVar('task', 'remove_project');
    	phpFrame_Environment_Request::setVar('projectid', $projectid);
    	
    	$application = phpFrame::getApplication();
    	$application->exec();
    	
    	$controller = phpFrame::getController('com_projects');
    	$this->assertTrue($controller->getSuccess());
    }
    
    function test_save_member_InviteNewUser() {
    	// Fake posted form data
    	phpFrame_Environment_Request::setVar('task', 'save_member');
    	phpFrame_Environment_Request::setVar('projectid', '1');
    	phpFrame_Environment_Request::setVar('roleid', '1');
    	phpFrame_Environment_Request::setVar('username', 'testuser');
    	phpFrame_Environment_Request::setVar('firstname', 'Test');
    	phpFrame_Environment_Request::setVar('lastname', 'User');
    	phpFrame_Environment_Request::setVar('groupid', '2');
    	phpFrame_Environment_Request::setVar('email', 'notifications.test@extranetoffice.org');
    	phpFrame_Environment_Request::setVar(phpFrame_Utils_Crypt::getToken(), '1');
    	
    	$application = phpFrame::getApplication();
    	$application->exec();
    	
    	$controller = phpFrame::getController('com_projects');
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
    	phpFrame_Environment_Request::setVar('task', 'save_member');
    	phpFrame_Environment_Request::setVar('projectid', '1');
    	phpFrame_Environment_Request::setVar('roleid', '1');
    	phpFrame_Environment_Request::setVar('userids', $userid);
    	phpFrame_Environment_Request::setVar(phpFrame_Utils_Crypt::getToken(), '1');
    	
    	$application = phpFrame::getApplication();
    	$application->exec();
    	
    	$controller = phpFrame::getController('com_projects');
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
    	phpFrame_Environment_Request::setVar('task', 'remove_member');
    	phpFrame_Environment_Request::setVar('projectid', '1');
    	phpFrame_Environment_Request::setVar('userid', $userid);
    	
    	$application = phpFrame::getApplication();
    	$application->exec();
    	
    	$controller = phpFrame::getController('com_projects');
    	$this->assertTrue($controller->getSuccess());
    }
    
    function test_admin_change_member_role() {
    	// Fake posted form data
    	phpFrame_Environment_Request::setVar('task', 'admin_change_member_role');
    	phpFrame_Environment_Request::setVar('projectid', '1');
    	phpFrame_Environment_Request::setVar('userid', '63');
    	phpFrame_Environment_Request::setVar('roleid', '3');
    	
    	$application = phpFrame::getApplication();
    	$application->exec();
    	
    	$controller = phpFrame::getController('com_projects');
    	$this->assertTrue($controller->getSuccess());
    }
    
    function test_save_issue() {
    	// Fake posted form data
    	phpFrame_Environment_Request::setVar('task', 'save_issue');
    	phpFrame_Environment_Request::setVar('projectid', '1');
    	phpFrame_Environment_Request::setVar('title', 'Test issue');
    	phpFrame_Environment_Request::setVar('issue_type', '0');
    	phpFrame_Environment_Request::setVar('priority', '1');
    	phpFrame_Environment_Request::setVar('access', '1');
    	phpFrame_Environment_Request::setVar(phpFrame_Utils_Crypt::getToken(), '1');
    	
    	$application = phpFrame::getApplication();
    	$application->exec();
    	
    	$controller = phpFrame::getController('com_projects');
    	$this->assertTrue($controller->getSuccess());
    }
}
?>