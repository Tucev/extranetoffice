<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * projectsViewIssues Class
 * 
 * The methods in this class are invoked by its parent class. See display() 
 * method in 'view' class.
 * 
 * Method name to be triggered will be formed as follows:
 * 
 * <code>
 * $tmpl_specific_method = "display".ucfirst(phpFrame_Environment_Request::getViewName()).ucfirst($this->tmpl);
 * </code>
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		phpFrame_Application_View
 */
class projectsViewIssues extends phpFrame_Application_View {
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
		$this->layout = phpFrame_Environment_Request::getLayout();
		
		// Set reference to projectid
		$this->projectid = phpFrame_Environment_Request::getVar('projectid', 0);
		$this->issueid = phpFrame_Environment_Request::getVar('issueid', 0);
		
		// Set reference to project object loaded in controller
		if (!empty($this->projectid)) {
			$controller =& phpFrame_Base_Singleton::getInstance('projectsController');
			$this->project =& $controller->project;
		}
		
		$this->current_tool = _LANG_ISSUES;
		
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
		$this->page_title = _LANG_ISSUES;
		$this->page_heading = $this->project->name;
		
		parent::display();
		
		// Append page title to document title
		$document = phpFrame::getDocument('html');
		$document->title .= ' - '.$this->page_title;
	}
	
	/**
	 * Custom display method triggered by list layout.
	 * 
	 * @return void
	 */
	function displayIssuesList() {
		// Push model into the view
		$modelIssues = $this->getModel('issues');
		$issues = $modelIssues->getIssues($this->projectid);
		$this->rows =& $issues['rows'];
		$this->pageNav =& $issues['pageNav'];
		$this->lists =& $issues['lists'];
		
		$this->addPathwayItem($this->page_title);
	}
	
	function displayIssuesForm() {
		if (!empty($this->issueid)) {
			$action = _LANG_ISSUES_EDIT;
			$modelIssues = $this->getModel('issues');
			$issue = $modelIssues->getIssuesDetail($this->projectid, $this->issueid);
			$this->row =& $issue;
		}
		else {
			$action = _LANG_ISSUES_NEW;
			// default values
			$this->issue->access = 1;	
		}
		
		$this->page_title .= ' - '.$action;
		$this->addPathwayItem($this->current_tool, phpFrame_Application_Route::_("index.php?component=com_projects&view=issues&projectid=".$this->projectid));
		$this->addPathwayItem($action);
	}
	
	function displayIssuesDetail() {
		$modelIssues = $this->getModel('issues');
		$issue = $modelIssues->getIssuesDetail($this->projectid, $this->issueid);
		$this->row =& $issue;
		
		$this->page_title .= ' - '.$issue->title;
		$this->addPathwayItem($this->current_tool, phpFrame_Application_Route::_("index.php?component=com_projects&view=issues&projectid=".$this->projectid));
		$this->addPathwayItem($issue->title);
	}
}
?>