<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice.Projects
 * @subpackage	controllers
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * projectsController Class
 * 
 * @package		ExtranetOffice.Projects
 * @subpackage 	controllers
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class projectsController extends controller {
	var $projectid=null;
	var $project=null;
	var $current_tool=null;
	
	function __construct() {
		// set default view and layout if none has been set
		$this->view = request::getVar('view', 'projects');
		$this->layout = request::getVar('layout', 'list');
		
		$this->projectid = request::getVar('projectid', 0);
		
		if (!empty($this->projectid)) {
			// Load the project data
			$modelProjects =& $this->getModel('projects');
			$projects = $modelProjects->getProjects($this->projectid);
			$this->project =& $projects['rows'][0];
					
			// Do security check
			$project_permissions =& $this->getModel('permissions');
			
			$this->current_tool = $project_permissions->checkProjectAccess($this->project);
			
			//echo '<pre>'; var_dump($project_permissions); echo '</pre>';	
		}
		
		parent::__construct();
	}
}
?>