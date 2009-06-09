<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_addressbook
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * addressbookController Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_addressbook
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class addressbookController extends PHPFrame_Application_ActionController {
	/**
	 * Constructor
	 * 
	 * @return	void
	 * @since 	1.0
	 */
	protected function __construct() {
		// Invoke parent's constructor to set default action
		parent::__construct('get_contacts');
	}
	
	public function get_contacts() {
		// Get request data
		$orderby = PHPFrame::getRequest()->get('orderby', 'c.family');
		$orderdir = PHPFrame::getRequest()->get('orderdir', 'ASC');
		$limit = PHPFrame::getRequest()->get('limit', 25);
		$limitstart = PHPFrame::getRequest()->get('limitstart', 0);
		$search = PHPFrame::getRequest()->get('search', '');
		
		// Create list filter needed for getContacts()
		$list_filter = new PHPFrame_Database_CollectionFilter($orderby, $orderdir, $limit, $limitstart, $search);
		
		// Get contacts using model
		$contacts = $this->getModel('contacts')->getContacts($list_filter);
		
		// Get view
		$view = $this->getView('contacts', 'list');
		// Set view data
		$view->addData('rows', $contacts);
		$view->addData('page_nav', new PHPFrame_HTML_Pagination($list_filter));
		// Display view
		$view->display();
	}
	
	public function save_contact() {
		// Check for request forgeries
		PHPFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		// Get request vars
		$post = PHPFrame::getRequest()->getPost();
		
		// Save issue using issues model
		$modelContacts = $this->getModel('contacts');
		$row = $modelContacts->saveContact($post);
		if ($row === false) {
			$this->sysevents->setSummary($modelContacts->getLastError());
		}
		else {
			$this->sysevents->setSummary(_LANG_CONTACT_SAVED, "success");
			// Set success flag for tests
			$this->_success = true;
		}
		
		$this->setRedirect('index.php?component=com_addressbook');
	}
	
	public function export_contacts() {
		// Get id from request
		$id = PHPFrame::getRequest()->get('id', 0);
		
		$modelContacts = $this->getModel('contacts');
		$modelContacts->exportContacts($id);
	}
}
