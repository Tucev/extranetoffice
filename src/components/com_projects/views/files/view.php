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
 * projectsViewFiles Class
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
class projectsViewFiles extends phpFrame_Application_View {
	/**
	 * Constructor
	 * 
	 * @return 	void
	 * @since	1.0
	 */
	function __construct($layout) {
		// Invoke the parent to set the view name and default layout
		parent::__construct('files', $layout);
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
		$this->_data['page_title'] = _LANG_FILES;
		$this->page_heading = $this->project->name;
		
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
	function displayFilesList() {
		phpFrame::getPathway()->addItem($this->_data['page_title']);
	}
	
	function displayFilesForm() {
		$parentid = phpFrame_Environment_Request::getVar('parentid', 0);
		
		$modelFiles = $this->getModel('files');
		$this->row = $modelFiles->getFilesDetail($this->projectid, $parentid);
		
		$this->page_title .= ' - '._LANG_FILES_NEW;
		phpFrame::getPathway()->addItem($this->current_tool, phpFrame_Application_Route::_("index.php?component=com_projects&view=files&projectid=".$this->projectid));
		phpFrame::getPathway()->addItem(_LANG_FILES_NEW);
	}
	
	function displayFilesDetail() {
		$fileid = phpFrame_Environment_Request::getVar('fileid', 0);
		
		$modelFiles = $this->getModel('files');
		$this->row = $modelFiles->getFilesDetail($this->projectid, $fileid);
		
		$this->page_title .= ' - '.$this->row->title;
		phpFrame::getPathway()->addItem($this->current_tool, phpFrame_Application_Route::_("index.php?component=com_projects&view=files&projectid=".$this->projectid));
		phpFrame::getPathway()->addItem($this->row->title);
	}
	
}
?>