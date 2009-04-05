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
define('_ABS_PATH_TEST', str_replace("components/com_projects", "", dirname(__FILE__)) );
define('_ABS_PATH', str_replace(DS."test",DS."src",_ABS_PATH_TEST) );
define("COMPONENT_PATH", _ABS_PATH.DS.'components'.DS.'com_projects');

// Include test config
require_once _ABS_PATH_TEST.DS."inc".DS."config.php";
// Include phpFrame
require_once _ABS_PATH.DS."lib".DS."phpframe".DS."phpframe.php";
// Include controller to test
require_once COMPONENT_PATH.DS.'controller.php';

$option = "com_projects";

$application =& phpFrame::getInstance('phpFrame_Application');
$application->auth();
// Set component option in application
$application->option = phpFrame_Environment_Request::getVar('option', $option);
// Get component info
$components =& phpFrame::getInstance('phpFrame_Application_Components');
$application->component_info = $components->loadByOption($this->option);
// load modules before we execute controller task to make modules available to components
$application->modules =& phpFrame::getInstance('phpFrame_Application_Modules');

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
		$_POST['option'] = $_REQUEST['option'] = 'com_projects';
    }
    
	function tearDown() {
		unset($_POST);
     	unset($_REQUEST);
     	phpFrame::destroyInstance('projectsController');
    }
    
    function testSave_project() {
    	// Fake posted form data
    	$_POST[phpFrame_Utils_Crypt::getToken()] = $_REQUEST[phpFrame_Utils_Crypt::getToken()] = '1';
    	$_POST['name'] = $_REQUEST['name'] = 'This is a test project';
    	$_POST['project_type'] = $_REQUEST['project_type'] = '1';
    	$_POST['priority'] = $_REQUEST['priority'] = '1';
    	$_POST['task'] = $_REQUEST['task'] = 'save_project';
    	
    	// Initialise permissions
		$application->permissions =& phpFrame::getInstance('phpFrame_Application_Permissions');
    	    	
    	$controller =& phpFrame::getInstance('projectsController');
    	$this->assertTrue($controller->getSuccess());
    }
    
    function testRemove_project () {
    	$_POST['projectid'] = $_REQUEST['projectid'] = '1';
    	$_POST['task'] = $_REQUEST['task'] = 'remove_project';
    	
    	$application = phpFrame_Application_Factory::getApplication();
    	$application->exec();
    	
    	$controller =& phpFrame::getInstance('projectsController');
    	$this->assertTrue($controller->getSuccess());
    }
    
}
?>