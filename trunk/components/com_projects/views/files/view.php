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
 * projectsViewFiles Class
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
class projectsViewFiles extends view {
	var $page_title=null;
	var $projectid=null;
	var $project=null;
	
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
		
		// Set reference to project object loaded in controller
		if (!empty($this->projectid)) {
			$controller =& phpFrame::getInstance('projectsController');
			$this->project =& $controller->project;
		}
		
		$this->current_tool = _LANG_FILES;
		
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
	function displayFilesList() {
		$this->page_title = _LANG_FILES;
		$this->page_heading = $this->project->name;
		$this->addPathwayItem($this->page_title);
				
		$modelFiles =& $this->getModel('files');
		$files = $modelFiles->getFiles($this->projectid);
		$this->rows =& $files['rows'];
		$this->pageNav =& $files['pageNav'];
		$this->lists =& $files['lists'];
	}
	
	function displayFilesForm() {
		$parentid = request::getVar('parentid', 0);
		
		$this->page_title .= ' - '._LANG_FILES_NEW;
		$this->page_heading = $this->project->name;
		$this->addPathwayItem($this->current_tool, route::_("index.php?option=com_projects&view=projects&layout=".$this->current_tool."&projectid=".$this->projectid));
		$this->addPathwayItem(_LANG_FILES_NEW);
		
		$modelFiles =& $this->getModel('files');
		$this->row = $modelFiles->getFilesDetail($this->projectid, $parentid);
	}
	
	function displayFilesDetail() {
		$fileid = request::getVar('fileid', 0);
		
		$this->page_heading = $this->project->name;
		$this->addPathwayItem($this->current_tool, route::_("index.php?option=com_projects&view=projects&layout=".$this->current_tool."&projectid=".$this->projectid));	
		
		$modelFiles =& $this->getModel('files');
		$this->row = $modelFiles->getFilesDetail($this->projectid, $fileid);
		
		$this->page_title .= ' - '.$this->row->title;
		$this->addPathwayItem($this->row->title);
	}
	
}
?>