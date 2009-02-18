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
 * projectsViewIssues Class
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
class projectsViewIssues extends view {
	var $page_title=null;
	var $projectid=null;
	
	function __construct() {
		// Set the view template to load (default value is set in controller)
		$this->layout =& request::getVar('layout');
		
		// Set reference to projectid
		$this->projectid =& request::getVar('projectid', 0);
		
		parent::__construct();
	}
	
	function displayIssues() {
		$this->doBreadcrumbs($this->page_subheading);
		
		// Push model into the view
		$modelIssues = new iOfficeModelIssues();
		$issues = $modelIssues->getIssues($this->projectid);
		$this->assignRef('rows', $issues['rows']);
		$this->assignRef('pageNav', $issues['pageNav']);
		$this->assignRef('lists', $issues['lists']);
	}
	
	function displayIssuesForm() {
		if (!empty($this->issueid)) {
			$action = _LANG_ISSUES_EDIT;
			$modelIssues = new iOfficeModelIssues();
			$issue = $modelIssues->getIssuesDetail($this->projectid, $this->issueid);
			$this->assignRef('row', $issue);
		}
		else {
			$action = _LANG_ISSUES_NEW;
			// default values
			$this->issue->access = 1;	
		}
		
		$this->page_title .= ' - '.$action;
		$this->doBreadcrumbs($this->page_subheading, route::_("index.php?option=com_projects&view=projects&layout=".$this->current_tool."&projectid=".$this->projectid));
		$this->doBreadcrumbs($action);
	}
	
	function displayIssuesDetail() {
		$this->doBreadcrumbs($this->page_subheading, route::_("index.php?option=com_projects&view=projects&layout=".$this->current_tool."&projectid=".$this->projectid));
		
		$modelIssues = new iOfficeModelIssues();
		$issue = $modelIssues->getIssuesDetail($this->projectid, $this->issueid);
		$this->assignRef('row', $issue);
		
		$this->page_title .= ' - '.$issue->title;
		$this->doBreadcrumbs($issue->title);
	}
}
?>