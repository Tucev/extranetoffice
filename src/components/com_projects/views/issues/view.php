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
	/**
	 * Constructor
	 * 
	 * @return 	void
	 * @since	1.0
	 */
	function __construct($layout) {
		// Invoke the parent to set the view name and default layout
		parent::__construct('issues', $layout);
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
		phpFrame::getPathway()->addItem($this->page_title);
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
		phpFrame::getPathway()->addItem($this->current_tool, phpFrame_Application_Route::_("index.php?component=com_projects&view=issues&projectid=".$this->projectid));
		phpFrame::getPathway()->addItem($action);
	}
	
	function displayIssuesDetail() {
		$modelIssues = $this->getModel('issues');
		$issue = $modelIssues->getIssuesDetail($this->projectid, $this->issueid);
		$this->row =& $issue;
		
		$this->page_title .= ' - '.$issue->title;
		phpFrame::getPathway()->addItem($this->current_tool, phpFrame_Application_Route::_("index.php?component=com_projects&view=issues&projectid=".$this->projectid));
		phpFrame::getPathway()->addItem($issue->title);
	}
}
?>