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
 * $tmpl_specific_method = "display".ucfirst(phpFrame_Environment_Request::getViewName()).ucfirst($this->tmpl);
 * </code>
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		phpFrame_Application_View
 */
class projectsViewMessages extends phpFrame_Application_View {
	/**
	 * Constructor
	 * 
	 * @return 	void
	 * @since	1.0
	 */
	function __construct($layout) {
		// Invoke the parent to set the view name and default layout
		parent::__construct('messages', $layout);
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
		
		phpFrame::getPathway()->addItem($this->page_title);
	}
	
	function displayMessagesDetail() {
		$modelMessages = $this->getModel('messages');
		$this->row = $modelMessages->getMessagesDetail($this->projectid, $this->messageid);
		
		$this->page_title .= ' - '.$this->row->subject;
		phpFrame::getPathway()->addItem($this->current_tool, "index.php?component=com_projects&view=messages&projectid=".$this->projectid);
		phpFrame::getPathway()->addItem($this->row->subject);
	}
	
	function displayMessagesForm() {
		$this->page_title .= ' - '._LANG_MESSAGES_NEW;
		phpFrame::getPathway()->addItem($this->current_tool, "index.php?component=com_projects&view=messages&projectid=".$this->projectid);
		phpFrame::getPathway()->addItem(_LANG_MESSAGES_NEW);
	}
}
?>