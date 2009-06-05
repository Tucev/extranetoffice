<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * projectsViewAdmin Class
 * 
 * The methods in this class are invoked by its parent class. See display() 
 * method in 'view' class.
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		PHPFrame_Application_View
 */
class projectsViewAdmin extends PHPFrame_Application_View {
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
		$document = PHPFrame::getDocument('html');
		$document->title .= ' - '.$this->_data['page_title'];
	}
	
	/**
	 * Custom display method triggered by list layout.
	 * 
	 * @return	void
	 * @since	1.0
	 */
	function displayAdminList() {
		$this->_data['page_title'] = _LANG_ADMIN;
		$this->_data['page_heading'] = $this->_data['project']->name.' - '._LANG_ADMIN;
		PHPFrame::getPathway()->addItem(_LANG_ADMIN);
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
			$this->_data['page_title'] = _LANG_PROJECTS_EDIT;
		}
		else {
			$this->_data['page_title'] = _LANG_PROJECTS_NEW;
		}
		
		PHPFrame::getPathway()->addItem($this->_data['page_title']);
	}
	
	/**
	 * @todo This method needs to be ported to extranetoffice from intranetoffice
	 */
	function displayAdminMemberRole() {
		$this->_data['page_title'] = projectsHelperProjects::id2name($this->projectid).' - '. _LANG_ADMIN;
		PHPFrame::getPathway()->addItem(_LANG_PROJECTS_MEMBERS);
	}
	
	/**
	 * Set view properties for new project members form
	 * 
	 * @return	void
	 * @since	1.0
	 */
	function displayAdminMemberForm() {
		$this->_data['page_title'] = _LANG_ADMIN.' - '._LANG_PROJECTS_ADD_MEMBER;
		$this->_data['page_heading'] = $this->_data['project']->name.' - '._LANG_ADMIN;
		PHPFrame::getPathway()->addItem(_LANG_ADMIN, 'index.php?component=com_projects&view=admin&projectid='.$this->_data['project']->id);
		PHPFrame::getPathway()->addItem(_LANG_PROJECTS_ADD_MEMBER);
	}
}
