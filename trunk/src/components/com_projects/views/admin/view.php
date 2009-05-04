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
 * projectsViewAdmin Class
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
class projectsViewAdmin extends phpFrame_Application_View {
	/**
	 * Constructor
	 * 
	 * @return 	void
	 * @since	1.0
	 */
	function __construct($layout) {
		// Invoke the parent to set the view name and default layout
		parent::__construct('admin', $layout);
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
		$document = phpFrame::getDocument('html');
		$document->title .= ' - '.$this->page_title;
	}
	
	/**
	 * Custom display method triggered by list layout.
	 * 
	 * @return	void
	 * @since	1.0
	 */
	function displayAdminList() {
		$this->page_title = _LANG_ADMIN;
		$this->page_heading = $this->project->name.' - '._LANG_ADMIN;
		phpFrame::getPathway()->addItem(_LANG_ADMIN);
		
		// Push model into the view
		$model = $this->getModel('members');
		$this->members = $model->getMembers($this->projectid);
	}
	
	/**
	 * Display project form layout
	 * 
	 * This method is a custom display method triggered by detail layout.
	 * 
	 * @return void
	 */
	function displayAdminForm() {
		if (!empty($this->projectid)) {
			$this->page_title = _LANG_PROJECTS_EDIT;
		}
		else {
			$this->page_title = _LANG_PROJECTS_NEW;
			// Set default values for tools
			$this->project = new stdClass();
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
		
		phpFrame::getPathway()->addItem($this->page_title);
	}
	
	/**
	 * @todo This method needs to be ported to extranetoffice from intranetoffice
	 */
	function displayAdminMemberRole() {
		$this->page_title = projectsHelperProjects::id2name($this->projectid).' - '. _LANG_ADMIN;
		phpFrame::getPathway()->addItem(_LANG_PROJECTS_MEMBERS);
		
		$this->_userid = phpFrame_Environment_Request::getVar('userid', 0);
		
		// Push model into the view
		$model = $this->getModel('members');
		if (!empty($this->_userid)) {
			$this->members = $model->getMembers($this->projectid, $this->_userid);	
		}
	}
	
	/**
	 * Set view properties for new project members form
	 * 
	 * @return	void
	 * @since	1.0
	 */
	function displayAdminMemberForm() {
		$this->page_title = _LANG_ADMIN.' - '._LANG_PROJECTS_ADD_MEMBER;
		$this->page_heading = $this->project->name.' - '._LANG_ADMIN;
		phpFrame::getPathway()->addItem(_LANG_ADMIN, 'index.php?component=com_projects&view=admin&projectid='.$this->projectid);
		phpFrame::getPathway()->addItem(_LANG_PROJECTS_ADD_MEMBER);
	}
}
?>