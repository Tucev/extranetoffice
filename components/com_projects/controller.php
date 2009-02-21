<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * projectsController Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class projectsController extends controller {
	var $projectid=null;
	var $project=null;
	var $project_permissions=null;
	var $current_tool=null;
	
	function __construct() {
		// set default request vars
		$this->view = request::getVar('view', 'projects');
		$this->layout = request::getVar('layout', 'list');
		$this->projectid = request::getVar('projectid', 0);
		
		// It is important we invoke the parent's constructor before 
		// running permission check as we need the available views loaded first.
		parent::__construct();
		
		if (!empty($this->projectid)) {
			// Load the project data
			$modelProjects =& $this->getModel('projects');
			$this->project = $modelProjects->getProjects($this->projectid);
					
			// Do security check with custom permission model for projects
			$this->project_permissions =& $this->getModel('permissions');
			$this->project_permissions->checkProjectAccess($this->project, $this->views_available);
			
			// Add pathway item
			$this->addPathwayItem($this->project->name, 'index.php?option=com_projects&view=projects&layout=detail&projectid='.$this->project->id);
			
			// Append page component name to document title
			$document =& factory::getDocument('html');
			if (!empty($document->title)) $document->title .= ' - ';
			$document->title .= $this->project->name;
		}
	}
}
?>