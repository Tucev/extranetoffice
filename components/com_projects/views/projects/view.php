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
 * projectsViewProjects Class
 * 
 * The methods in this class are invoked by its parent class. See display() 
 * method in 'view' class.
 * 
 * Method name to be triggered will be formed as follows:
 * 
 * <code>
 * $tmpl_specific_method = "display".ucfirst(request::getVar('view')).ucfirst($this->tmpl);
 * </code>
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		view, controller
 */
class projectsViewProjects extends view {
	var $page_title=null;
	var $projectid=null;
	
	/**
	 * Constructor
	 * 
	 * @return 	void
	 * @since	1.0
	 */
	function __construct() {
		// Set the view template to load (default value is set in controller)
		$this->layout =& request::getVar('layout');
		
		// Set reference to projectid
		$this->projectid =& request::getVar('projectid', 0);
		
		parent::__construct();
	}
	
	/**
	 * Override view display method
	 * 
	 * This method overrides the parent display() method and appends the page title to the document title.
	 * 
	 * @return	void
	 * @since	1.0
	 */
	function display() {
		parent::display();
		
		// Append page title to document title
		if (request::getVar('layout') != 'list') {
			$document =& factory::getDocument('html');
			$document->title .= ' - '.$this->page_title;
		}
	}
	
	/**
	 * Display project list layout
	 * 
	 * Custom display method triggered by list layout.
	 * 
	 * @return void
	 */
	function displayProjectsList() {
		$this->page_title = _LANG_PROJECTS;
		
		// Push model into the view
		$model =& $this->getModel('projects');
		
		// Get projects and store data in view
		$projects = $model->getProjects();
		$this->rows =& $projects['rows'];
		$this->pageNav =& $projects['pageNav'];
		$this->lists =& $projects['lists'];
	}
	
	/**
	 * Display project detail layout
	 * 
	 * This method is a custom display method triggered by detail layout.
	 * 
	 * @return void
	 */
	function displayProjectsDetail($projectid=0) {
		if (empty($projectid)) {
			$projectid = request::getVar('projectid', 0);
		}
		
		if (empty($projectid)) {
			error::raise('', 'error', 'No project was selected');
			return false;
		}
		else {
			$project_name = projectsHelperProjects::id2name($projectid);
			$this->page_title = $project_name.' - '. _LANG_PROJECTS_HOME;
			$this->addPathwayItem(_LANG_PROJECTS_HOME);
			
			// Get overdue issues
			$modelIssues =& $this->getModel('issues');
			$overdue_issues = $modelIssues->getIssues($projectid, true);
			$this->overdue_issues =& $overdue_issues['rows'];
			
			// Get upcoming milestones
			
			// Get project updates
			$modelActivitylog =& $this->getModel('activitylog');
			$this->activitylog = $modelActivitylog->getActivityLog($projectid);
		}
		
	}
	
	/**
	 * Display project detail layout
	 * 
	 * This method is a custom display method triggered by detail layout.
	 * 
	 * @return void
	 */
	function displayProjectsForm() {
		if (!empty($this->projectid)) {
			$this->page_title = _LANG_PROJECTS_EDIT;
		}
		else {
			$this->page_title = _LANG_PROJECTS_NEW;
			// Set default values for tools
			$this->project->access = '1';
			$this->project->access_issues = '2';
			$this->project->access_messages = '2';
			$this->project->access_milestones = '2';
			$this->project->access_files = '2';
			$this->project->access_meetings = '3';
			$this->project->access_polls = '3';
			$this->project->access_reports = '1';
			$this->project->access_people = '3';
			$this->project->access_admin = '1';
		}
		
		$this->addPathwayItem($this->page_title);
	}
}
?>