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
 * projectsViewMessages Class
 * 
 * The methods in this class are invoked by its parent class. See display() 
 * method in 'view' class.
 * 
 * Method name to be triggered will be formed as follows:
 * 
 * <code>
 * $tmpl_specific_method = "display".ucfirst(phpFrame_Environment_Request::getVar('view')).ucfirst($this->tmpl);
 * </code>
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		view, controller
 */
class projectsViewMessages extends phpFrame_Application_View {
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
		$this->layout = phpFrame_Environment_Request::getVar('layout');
		
		// Set reference to projectid
		$this->projectid = phpFrame_Environment_Request::getVar('projectid', 0);
		$this->messageid = phpFrame_Environment_Request::getVar('messageid', 0);
		
		// Set reference to project object loaded in controller
		if (!empty($this->projectid)) {
			$controller =& phpFrame_Base_Singleton::getInstance('projectsController');
			$this->project =& $controller->project;
		}
		
		$this->current_tool = _LANG_MESSAGES;
		
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
		$this->page_title = _LANG_MESSAGES;
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
	function displayMessagesList() {
		$modelMessages = $this->getModel('messages');
		$messages = $modelMessages->getMessages($this->projectid);
		$this->rows =& $messages['rows'];
		$this->pageNav =& $messages['pageNav'];
		$this->lists =& $messages['lists'];
		
		$this->addPathwayItem($this->page_title);
	}
	
	function displayMessagesDetail() {
		$modelMessages = $this->getModel('messages');
		$this->row = $modelMessages->getMessagesDetail($this->projectid, $this->messageid);
		
		$this->page_title .= ' - '.$this->row->subject;
		$this->addPathwayItem($this->current_tool, "index.php?option=com_projects&view=messages&projectid=".$this->projectid);
		$this->addPathwayItem($this->row->subject);
	}
	
	function displayMessagesForm() {
		$this->page_title .= ' - '._LANG_MESSAGES_NEW;
		$this->addPathwayItem($this->current_tool, "index.php?option=com_projects&view=messages&projectid=".$this->projectid);
		$this->addPathwayItem(_LANG_MESSAGES_NEW);
	}
}
?>