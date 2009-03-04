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
 * projectsViewMessages Class
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
class projectsViewMessages extends view {
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
		$this->layout =& request::getVar('layout');
		
		// Set reference to projectid
		$this->projectid =& request::getVar('projectid', 0);
		
		// Set reference to project object loaded in controller
		if (!empty($this->projectid)) {
			$controller =& phpFrame::getInstance('projectsController');
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
	function displayMessagesList() {
		$this->page_title = _LANG_MESSAGES;
		$this->page_heading = $this->project->name;
		$this->addPathwayItem($this->page_title);
		
		$document =& factory::getDocument('html');
		$document->addScript('lib/thickbox/thickbox-compressed.js');
		$document->addStyleSheet('lib/thickbox/thickbox.css');
		
		$modelMessages =& $this->getModel('messages');
		$messages = $modelMessages->getMessages($this->projectid);
		$this->rows =& $messages['rows'];
		$this->pageNav =& $messages['pageNav'];
		$this->lists =& $messages['lists'];
	}
	
	function displayMessagesDetail() {
		$this->addPathwayItem($this->current_tool, route::_("index.php?option=com_projects&view=projects&layout=".$this->current_tool."&projectid=".$this->projectid));
		
		$modelMessages =& $this->getModel('messages');
		$message = $modelMessages->getMessagesDetail($this->projectid, $this->messageid);
		$this->assignRef('row', $message);
		
		$this->page_title .= ' - '.$message->subject;
		$this->addPathwayItem($message->subject);
	}
	
	function displayMessagesForm() {
		$this->page_title .= ' - '._LANG_MESSAGES_NEW;
		$this->page_heading = $this->project->name;
		$this->addPathwayItem($this->current_tool, route::_("index.php?option=com_projects&view=projects&layout=".$this->current_tool."&projectid=".$this->projectid));
		$this->addPathwayItem(_LANG_MESSAGES_NEW);
	}
}
?>