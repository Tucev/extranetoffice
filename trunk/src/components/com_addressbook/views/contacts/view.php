<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_addressbook
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * addressbookViewContacts Class
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
 * @subpackage 	com_addressbook
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		phpFrame_Application_View
 */
class addressbookViewContacts extends phpFrame_Application_View {
	var $page_title=null;
	
	function __construct() {
		// Set the view template to load
		$this->layout = phpFrame_Environment_Request::getLayout('list');
		
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
		if (phpFrame_Environment_Request::getLayout() != 'list') {
			$document = phpFrame::getDocument('html');
			$document->title .= ' - '.$this->page_title;
		}
	}
	
	function displayContactsList() {
		$this->page_title = _LANG_ADDRESSBOOK;
		
		// Get request vars
		$search = phpFrame_Environment_Request::getVar('search', '');
		
		// Push model into the view
		$model = $this->getModel('contacts');
		
		// Get invoices and store data in view
		$contacts = $model->getContacts($search);
		$this->rows =& $contacts['rows'];
		$this->pageNav =& $contacts['pageNav'];
	}
	
	function displayContactsDetail() {
		$modelMessages = $this->getModel('messages');
		$this->row = $modelMessages->getMessagesDetail($this->projectid, $this->messageid);
		
		$this->page_title .= ' - '.$this->row->subject;
		$this->addPathwayItem($this->current_tool, "index.php?component=com_projects&view=messages&projectid=".$this->projectid);
		$this->addPathwayItem($this->row->subject);
	}
	
	function displayContactsForm() {
		$this->page_title = _LANG_ADDRESSBOOK_CONTACT_NEW;
		$this->addPathwayItem(_LANG_ADDRESSBOOK_CONTACT_NEW);
	}
}
?>