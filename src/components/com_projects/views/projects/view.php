<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * projectsViewProjects Class
 * 
 * The methods in this class are invoked by its parent class. See display() 
 * method in 'view' class.
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		PHPFrame_MVC_View
 */
class projectsViewProjects extends PHPFrame_MVC_View {
	/**
	 * Constructor
	 * 
	 * @return 	void
	 * @since	1.0
	 */
	function __construct($layout) {
		// Invoke the parent to set the view name and default layout
		parent::__construct('projects', $layout);
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
		if ($this->_layout != 'list') {
			$this->_document->appendTitle(' - '.$this->_data['page_title']);
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
		$this->_data['page_title'] = _LANG_PROJECTS;
	}
	
	/**
	 * Display project detail layout
	 * 
	 * This method is a custom display method triggered by detail layout.
	 * 
	 * @return void
	 */
	function displayProjectsDetail() {
		$this->_data['page_title'] = _LANG_PROJECTS_HOME;
		$this->_data['page_heading'] = $this->_data['row']->name.' - '._LANG_PROJECTS_HOME;
		$this->getPathway()->addItem(_LANG_PROJECTS_HOME);
	}
}
