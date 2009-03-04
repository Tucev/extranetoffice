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
 * projectsViewMilestones Class
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
class projectsViewMilestones extends view {
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
		$this->milestoneid =& request::getVar('milestoneid', 0);
		
		// Set reference to project object loaded in controller
		if (!empty($this->projectid)) {
			$controller =& phpFrame::getInstance('projectsController');
			$this->project =& $controller->project;
		}
		
		$this->current_tool = _LANG_MILESTONES;
		
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
		$document =& factory::getDocument('html');
		$document->title .= ' - '.$this->page_title;
	}
	
	/**
	 * Custom display method triggered by list layout.
	 * 
	 * @return void
	 */
	function displayMilestonesList() {
		$this->page_title = _LANG_MILESTONES;
		$this->page_heading = $this->project->name;
		$this->addPathwayItem($this->page_title);
		
		$modelMilestones =& $this->getModel('milestones');
		$milestones = $modelMilestones->getMilestones($this->projectid);
		$this->rows =& $milestones['rows'];
		$this->pageNav =& $milestones['pageNav'];
		$this->lists =& $milestones['lists'];
	}
	
	function displayMilestonesDetail() {
		$this->addPathwayItem($this->current_tool, route::_("index.php?option=com_projects&view=projects&layout=".$this->current_tool."&projectid=".$this->projectid));	
	
		$modelMilestones =& $this->getModel('milestones');
		$this->row = $modelMilestones->getMilestonesDetail($this->projectid, $this->milestoneid);
		
		$this->page_title .= ' - '.$this->row->title;
		$this->addPathwayItem($this->row->title);
	}
	
	function displayMilestonesForm() {
		if (!empty($this->milestoneid)) {
			$action = _LANG_MILESTONES_EDIT;
			$modelMilestones =& $this->getModel('milestones');
			$this->row = $modelMilestones->getMilestonesDetail($this->projectid, $this->milestoneid);
		}
		else {
			$action = _LANG_MILESTONES_NEW;
			// default values	
		}
		
		$this->page_title .= ' - '.$action;
		$this->page_heading = $this->project->name;
		$this->addPathwayItem($this->current_tool, route::_("index.php?option=com_projects&view=projects&layout=".$this->current_tool."&projectid=".$this->projectid));
		$this->addPathwayItem($action);
	}
}
?>