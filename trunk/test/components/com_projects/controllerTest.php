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

$application = phpFrame_Application_Factory::getApplication();
$application->auth();

// We empty projects tables before running tests
$db = phpFrame_Application_Factory::getDB();
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
    
    function testSave_project() {
    	// Fake posted form data
    	phpFrame_Environment_Request::setVar(phpFrame_Utils_Crypt::getToken(), '1');
    	phpFrame_Environment_Request::setVar('name', 'This is a test project');
    	phpFrame_Environment_Request::setVar('project_type', '1');
    	phpFrame_Environment_Request::setVar('priority', '1');
    	phpFrame_Environment_Request::setVar('task', 'save_project');
    	
    	$application = phpFrame_Application_Factory::getApplication();
		$application->exec();
    	    	
    	$controller = phpFrame_Application_Factory::getController('com_projects');
    	$this->assertTrue($controller->getSuccess());
    }
    
    function testRemove_project () {
    	phpFrame_Environment_Request::setVar('projectid', '1');
    	phpFrame_Environment_Request::setVar('task', 'remove_project');
    	
    	$application = phpFrame_Application_Factory::getApplication();
    	$application->exec();
    	
    	$controller = phpFrame_Application_Factory::getController('com_projects');
    	$this->assertTrue($controller->getSuccess());
    }
    
}
?>