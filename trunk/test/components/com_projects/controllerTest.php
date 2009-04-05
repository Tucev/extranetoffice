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

// Instantiate application
$application =& phpFrame::getInstance('application');
$application->auth();

class testProjectsController extends PHPUnit_Framework_TestCase {
	
	private $controller = null;

	function setUp() {
		$_POST['option'] = $_REQUEST['option'] = 'com_projects';
    }
    
	function tearDown() {
		unset($_POST);
     	unset($_REQUEST);
    }
    
    function testSave_project() {
    	// Fake posted form data
    	$_POST[crypt::getToken()] = $_REQUEST[crypt::getToken()] = '1';
    	$_POST['name'] = $_REQUEST['name'] = 'This is a test project';
    	$_POST['project_type'] = $_REQUEST['project_type'] = '1';
    	$_POST['priority'] = $_REQUEST['priority'] = '1';
    	$_POST['task'] = $_REQUEST['task'] = 'save_project';
    	
    	$application = factory::getApplication();
    	$application->exec();
    	
    	$controller =& phpFrame::getInstance('projectsController');
    	$this->assertTrue($controller->getSuccess());
    }
    
    function testRemove_project () {
    	$_POST['projectid'] = $_REQUEST['projectid'] = '1';
    	
    	$_POST['task'] = $_REQUEST['task'] = 'remove_project';
    	
    	$application = factory::getApplication();
    	$application->exec();
    	
    	$controller =& phpFrame::getInstance('projectsController');
    	$this->assertTrue($controller->getSuccess());
    }
    
}
?>