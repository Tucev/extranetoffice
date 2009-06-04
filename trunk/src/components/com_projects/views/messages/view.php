<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * projectsViewMessages Class
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
		$this->_data['page_title'] = _LANG_MESSAGES;
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
	function displayMessagesList() {
		phpFrame::getPathway()->addItem($this->_data['page_title']);
	}
	
	function displayMessagesDetail() {
		$this->_data['page_title'] .= ' - '.$this->_data['row']->subject;
		phpFrame::getPathway()->addItem(_LANG_MESSAGES, "index.php?component=com_projects&action=get_messages&projectid=".$this->_data['project']->id);
		phpFrame::getPathway()->addItem($this->_data['row']->subject);
	}
	
	function displayMessagesForm() {
		$this->_data['page_title'] .= ' - '._LANG_MESSAGES_NEW;
		phpFrame::getPathway()->addItem(_LANG_MESSAGES, "index.php?component=com_projects&action=get_messages&projectid=".$this->_data['project']->id);
		phpFrame::getPathway()->addItem(_LANG_MESSAGES_NEW);
	}
}
