<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_addressbook
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * addressbookViewContacts Class
 * 
 * The methods in this class are invoked by its parent class. See display() 
 * method in 'view' class.
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_addressbook
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		phpFrame_Application_View
 */
class addressbookViewContacts extends phpFrame_Application_View {
	/**
	 * Constructor
	 * 
	 * @return void
	 */
	function __construct($layout) {
		// Invoke the parent to set the view name and default layout
		parent::__construct('contacts', $layout);
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
			$document = phpFrame::getDocument('html');
			$document->title .= ' - '.$this->_data['page_title'];
		}
	}
	
	function displayContactsList() {
		$this->_data['page_title'] = _LANG_ADDRESSBOOK;
	}
	
	function displayContactsForm() {
		$this->_data['page_title'] = _LANG_ADDRESSBOOK_CONTACT_NEW;
		// Add items to pathway object
		phpFrame::getPathway()->addItem(_LANG_ADDRESSBOOK_CONTACT_NEW);
	}
}
