<?php
/**
 * @version     $Id$
 * @package        PHPFrame
 * @subpackage     PHPUnit_test_suite
 * @copyright    Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license        BSD revised. See LICENSE.
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

$frontcontroller = PHPFrame::getFrontController();

require_once 'PHPUnit/Framework.php';

class testProjectsController extends PHPUnit_Framework_TestCase {
    function setUp() {
        PHPFrame::Request()->setComponentName('com_projects');
    }
    
    function tearDown() {
        PHPFrame::Request()->destroy();
         PHPFrame_Base_Singleton::destroyInstance('projectsController');
    }
    
    function test_save_project() {
        // Fake posted form data
        PHPFrame::Request()->setAction('save_project');
        PHPFrame::Request()->set('name', 'This is a test project');
        PHPFrame::Request()->set('project_type', '1');
        PHPFrame::Request()->set('priority', '1');
        PHPFrame::Request()->set(PHPFrame_Utils_Crypt::getToken(), '1');
        
        $frontcontroller = PHPFrame::getFrontController();
        $frontcontroller->run();
                
        $controller = PHPFrame_MVC_ActionController::getInstance('com_projects');
        $this->assertTrue($controller->getSuccess());
    }
    
    function test_remove_project() {
        // Add a project to db to then delete it
        $db = PHPFrame::DB();
        $query = "INSERT INTO #__projects (id, name, project_type, priority, created_by) VALUES (NULL, 'another project', '2', '1', '1')";
        $projectid = $db->query($query);
        // Add project admin
        $query = "INSERT INTO #__users_roles (id, userid, roleid, projectid) VALUES (NULL, '1', '1', '".$projectid."')";
        $db->query($query);
        
        // Fake posted form data
        PHPFrame::Request()->setAction('remove_project');
        PHPFrame::Request()->set('projectid', $projectid);
        
        $frontcontroller = PHPFrame::getFrontController();
        $frontcontroller->run();
        
        $controller = PHPFrame_MVC_ActionController::getInstance('com_projects');
        $this->assertTrue($controller->getSuccess());
    }
    
    function test_save_member_InviteNewUser() {
        // Fake posted form data
        PHPFrame::Request()->setAction('save_member');
        PHPFrame::Request()->set('projectid', '1');
        PHPFrame::Request()->set('roleid', '1');
        PHPFrame::Request()->set('username', 'testuser');
        PHPFrame::Request()->set('firstname', 'Test');
        PHPFrame::Request()->set('lastname', 'User');
        PHPFrame::Request()->set('groupid', '2');
        PHPFrame::Request()->set('email', 'notifications.test@extranetoffice.org');
        PHPFrame::Request()->set(PHPFrame_Utils_Crypt::getToken(), '1');
        
        $frontcontroller = PHPFrame::getFrontController();
        $frontcontroller->run();
        
        $controller = PHPFrame_MVC_ActionController::getInstance('com_projects');
        $this->assertTrue($controller->getSuccess());
        
    }
    
    function test_save_member_ExistingUser() {
        // Add an existing member to be able to assign to project
        $db = PHPFrame::DB();
        $query = "INSERT INTO `#__users` (`id`, `groupid`, `username`, `password`, `email`, `firstname`, `lastname`,`photo`";
        $query .= ", `notifications`, `show_email`, `block`, `created`, `last_visit`, `activation`, `params`, `ts`, `deleted`)";
        $query .= " VALUES (NULL, '2', 'testuser2', '', 'notifications.test@extranetoffice.org', 'test', 'user 2', 'default.png'";
        $query .= ", '1', '1', '0', '', NULL , NULL , NULL , CURRENT_TIMESTAMP, NULL)";
        $userid = $db->query($query);
        
        // Fake posted form data
        PHPFrame::Request()->setAction('save_member');
        PHPFrame::Request()->set('projectid', '1');
        PHPFrame::Request()->set('roleid', '1');
        PHPFrame::Request()->set('userids', $userid);
        PHPFrame::Request()->set(PHPFrame_Utils_Crypt::getToken(), '1');
        
        $frontcontroller = PHPFrame::getFrontController();
        $frontcontroller->run();
        
        $controller = PHPFrame_MVC_ActionController::getInstance('com_projects');
        $this->assertTrue($controller->getSuccess());
        
    }
    
    function test_remove_member() {
        // Add an user to then delete it in this test
        $db = PHPFrame::DB();
        $query = "INSERT INTO `#__users` (`id`, `groupid`, `username`, `password`, `email`, `firstname`, `lastname`,`photo`";
        $query .= ", `notifications`, `show_email`, `block`, `created`, `last_visit`, `activation`, `params`, `ts`, `deleted`)";
        $query .= " VALUES (NULL, '2', 'testuser3', '', 'notifications.test@extranetoffice.org', 'test', 'user 3', 'default.png'";
        $query .= ", '1', '1', '0', '', NULL , NULL , NULL , CURRENT_TIMESTAMP, NULL)";
        $userid = $db->query($query);
        // make user a project member
        $query = "INSERT INTO #__users_roles (id, userid, roleid, projectid) VALUES (NULL, '".$userid."', '2', '1')";
        $db->query($query);
        
        // Fake posted form data
        PHPFrame::Request()->setAction('remove_member');
        PHPFrame::Request()->set('projectid', '1');
        PHPFrame::Request()->set('userid', $userid);
        
        $frontcontroller = PHPFrame::getFrontController();
        $frontcontroller->run();
        
        $controller = PHPFrame_MVC_ActionController::getInstance('com_projects');
        $this->assertTrue($controller->getSuccess());
    }
    
    function test_change_member_role() {
        // Fake posted form data
        PHPFrame::Request()->setAction('change_member_role');
        PHPFrame::Request()->set('projectid', '1');
        PHPFrame::Request()->set('userid', '63');
        PHPFrame::Request()->set('roleid', '3');
        
        $frontcontroller = PHPFrame::getFrontController();
        $frontcontroller->run();
        
        $controller = PHPFrame_MVC_ActionController::getInstance('com_projects');
        $this->assertTrue($controller->getSuccess());
    }
    /*
    function test_save_issue() {
        // Fake posted form data
        PHPFrame::Request()->setAction('save_issue');
        PHPFrame::Request()->set('projectid', '1');
        PHPFrame::Request()->set('title', 'Test issue');
        PHPFrame::Request()->set('issue_type', '0');
        PHPFrame::Request()->set('priority', '1');
        PHPFrame::Request()->set('access', '1');
        PHPFrame::Request()->set(PHPFrame_Utils_Crypt::getToken(), '1');
        
        $frontcontroller = PHPFrame::getFrontController();
        $frontcontroller->run();
        
        $controller = PHPFrame_MVC_ActionController::getInstance('com_projects');
        $this->assertTrue($controller->getSuccess());
    }
    */
}
