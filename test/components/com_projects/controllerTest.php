<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_projects test suite
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

// Set constants
define("_EXEC", true);
define( 'DS', DIRECTORY_SEPARATOR );
define('_ABS_PATH_TEST', str_replace(DS."components".DS."com_projects", "", dirname(__FILE__)) );
define('_ABS_PATH', str_replace(DS."test", DS."src",_ABS_PATH_TEST) );

// Include test config
require_once _ABS_PATH_TEST.DS."inc".DS."config.php";
// Include autoloader
require_once _ABS_PATH.DS."inc".DS."autoload.php";

// Initialise application
$application = phpFrame_Application_Factory::getApplication();
$application->auth();

// We empty projects tables before running tests
$db = phpFrame_Application_Factory::getDB();
$query = "DELETE FROM #__users WHERE id > 62";
$db->setQuery($query);
$db->query();
$query = "TRUNCATE TABLE #__projects";
$db->setQuery($query);
$db->query();
$query = "TRUNCATE TABLE #__users_roles";
$db->setQuery($query);
$db->query();

require_once 'PHPUnit/Framework.php';

class testProjectsController extends PHPUnit_Framework_TestCase {
	function setUp() {
		phpFrame_Environment_Request::setVar('option', 'com_projects');
    }
    
	function tearDown() {
		phpFrame_Environment_Request::destroy();
     	phpFrame::destroyInstance('projectsController');
    }
    
    function test_save_project() {
    	// Fake posted form data
    	phpFrame_Environment_Request::setVar('task', 'save_project');
    	phpFrame_Environment_Request::setVar('name', 'This is a test project');
    	phpFrame_Environment_Request::setVar('project_type', '1');
    	phpFrame_Environment_Request::setVar('priority', '1');
    	phpFrame_Environment_Request::setVar(phpFrame_Utils_Crypt::getToken(), '1');
    	
    	$application = phpFrame_Application_Factory::getApplication();
		$application->exec();
    	    	
    	$controller = phpFrame_Application_Factory::getController('com_projects');
    	$this->assertTrue($controller->getSuccess());
    }
    
    function test_remove_project() {
    	// Add a project to db to then delete it
    	$db = phpFrame_Application_Factory::getDB();
    	$query = "INSERT INTO #__projects (id, name, project_type, priority) VALUES (NULL, 'another project', '2', '1')";
		$db->setQuery($query);
		$projectid = $db->query();
		
    	// Fake posted form data
    	phpFrame_Environment_Request::setVar('task', 'remove_project');
    	phpFrame_Environment_Request::setVar('projectid', $projectid);
    	
    	$application = phpFrame_Application_Factory::getApplication();
    	$application->exec();
    	
    	$controller = phpFrame_Application_Factory::getController('com_projects');
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
    	
    	$application = phpFrame_Application_Factory::getApplication();
    	$application->exec();
    	
    	$controller = phpFrame_Application_Factory::getController('com_projects');
    	$this->assertTrue($controller->getSuccess());
    	
    }
    
	function test_save_member_ExistingUser() {
		// Add an existing member to be able to assign to project
		$db = phpFrame_Application_Factory::getDB();
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
    	
    	$application = phpFrame_Application_Factory::getApplication();
    	$application->exec();
    	
    	$controller = phpFrame_Application_Factory::getController('com_projects');
    	$this->assertTrue($controller->getSuccess());
    	
    }
    
    function test_remove_member() {
    	// Add an user to then delete it in this test
		$db = phpFrame_Application_Factory::getDB();
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
    	
    	$application = phpFrame_Application_Factory::getApplication();
    	$application->exec();
    	
    	$controller = phpFrame_Application_Factory::getController('com_projects');
    	$this->assertTrue($controller->getSuccess());
    }
    
    function test_admin_change_member_role() {
    	// Fake posted form data
    	phpFrame_Environment_Request::setVar('task', 'remove_member');
    	phpFrame_Environment_Request::setVar('projectid', '1');
    	phpFrame_Environment_Request::setVar('userid', '63');
    	phpFrame_Environment_Request::setVar('roleid', '3');
    	
    	$application = phpFrame_Application_Factory::getApplication();
    	$application->exec();
    	
    	$controller = phpFrame_Application_Factory::getController('com_projects');
    	$this->assertTrue($controller->getSuccess());
    }
}
?>