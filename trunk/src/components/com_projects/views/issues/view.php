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
		$this->_data['page_title'] = _LANG_ISSUES;
		$this->_data['page_heading'] = $this->_data['project']->name;
		
		parent::display();
		
		// Append page title to document title
		$document = phpFrame::getDocument('html');
		$document->title .= ' - '.$this->_data['page_title'];
	}
	
	/**
	 * Custom display method triggered by list layout.
	 * 
	 * @return void
	 */
	function displayIssuesList() {
		phpFrame::getPathway()->addItem(_LANG_ISSUES);
	}
	
	function displayIssuesForm() {
		$action = empty($this->_data['row']) ? _LANG_ISSUES_NEW : _LANG_ISSUES_EDIT;
		$this->_data['page_title'] .= ' - '.$action;
		phpFrame::getPathway()->addItem(_LANG_ISSUES, phpFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&action=get_issues&projectid=".$this->_data['project']->id));
		phpFrame::getPathway()->addItem($action);
	}
	
	function displayIssuesDetail() {
		$this->_data['page_title'] .= ' - '.$this->_data['row']->title;
		phpFrame::getPathway()->addItem(_LANG_ISSUES, phpFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&view=issues&projectid=".$this->_data['project']->id));
		phpFrame::getPathway()->addItem($this->_data['row']->title);
	}
}
