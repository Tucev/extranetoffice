<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

define("COMPONENT_PATH", _ABS_PATH.DS.'components'.DS.'com_projects');
require_once COMPONENT_PATH.DS.'models'.DS.'projects.php';

class testProjects extends PHPUnit_Framework_TestCase {
	
	private $projects = null;

	function setUp() {
    	$this->projects = phpFrame::getInstance('projectsModelProjects');
    }
    
    function testGetProjects() {
    		
    }
    
	function testSaveProject() {
		$post = array();
		$post['name'] = 'This is a test project';
		$post['project_type'] = '1';
		$post['priority'] = '1';
    	$this->assertType('int', $this->projects->saveProject($post));
    }
    
	function testDeleteProject() {
    		
    }
    
	function tearDown() {
     	unset($this->projects);
    }
}
?>