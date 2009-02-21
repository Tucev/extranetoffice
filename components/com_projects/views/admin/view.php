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
 * projectsViewAdmin Class
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
class projectsViewAdmin extends view {
	var $page_title=null;
	var $projectid=null;
	var $project=null;
	var $tools=null;
	
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
		
		if (!empty($this->projectid)) {
			// get project data from controller
			$controller =& phpFrame::getInstance('projectsController');
			$this->project =& $controller->project;
			$this->project_permissions =& $controller->project_permissions;
			$this->page_title = projectsHelperProjects::id2name($this->projectid);
		}
		
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
	function displayAdminList() {
		$this->page_title .= ' - '._LANG_ADMIN;
		$this->addPathwayItem(_LANG_ADMIN);
		
		$document =& factory::getDocument('html');
		$document->addScript('lib/jquery/jquery-1.3.1.min.js');
		$document->addScript('lib/thickbox/thickbox-compressed.js');
		$document->addStyleSheet('lib/thickbox/thickbox.css');
		
		// Push model into the view
		$model =& $this->getModel('projects');
		$this->members = $model->getMembers($this->projectid);
	}
	
	/**
	 * @todo This method needs to be ported to extranetoffice from intranetoffice
	 */
	function displayAdminMemberRole() {
		$this->page_title = projectsHelperProjects::id2name($this->projectid).' - '. _LANG_ADMIN;
		$this->addPathwayItem(_LANG_PROJECTS_MEMBERS);
		
		$this->userid = request::getVar('userid', 0);
		
		// Push model into the view
		$model =& $this->getModel();
		if (!empty($this->userid)) {
			$this->members = $model->getMembers($this->projectid, $this->userid);	
		}
	}
	
	/**
	 * @todo This method needs to be ported to extranetoffice from intranetoffice
	 */
	function displayMemberForm() {
		$this->page_title = projectsHelperProjects::id2name($this->projectid).' - '. _LANG_PROJECTS_MEMBERS;
		$this->addPathwayItem(_LANG_PROJECTS_MEMBERS);
		
		$userid = request::getVar('userid', 0);
		
		// Push model into the view
		$model =& $this->getModel();
		if (!empty($userid)) {
			$this->members = $model->getMembers($this->projectid, $userid);	
		}
	}
}
?>