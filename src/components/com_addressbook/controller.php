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
 * addressbookController Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_addressbook
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class addressbookController extends phpFrame_Application_ActionController {
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
		$orderby = phpFrame_Environment_Request::getVar('orderby', 'c.family');
		$orderdir = phpFrame_Environment_Request::getVar('orderdir', 'ASC');
		$limit = phpFrame_Environment_Request::getVar('limit', 25);
		$limitstart = phpFrame_Environment_Request::getVar('limitstart', 0);
		$search = phpFrame_Environment_Request::getVar('search', '');
		
		// Create list filter needed for getContacts()
		$list_filter = new phpFrame_Database_CollectionFilter($orderby, $orderdir, $limit, $limitstart, $search);
		
		// Get contacts using model
		$contacts = $this->getModel('contacts')->getContacts($list_filter);
		
		// Get view
		$view = $this->getView('contacts', 'list');
		// Set view data
		$view->addData('rows', $contacts);
		$view->addData('page_nav', new phpFrame_HTML_Pagination($list_filter));
		// Display view
		$view->display();
	}
	
	public function save_contact() {
		// Check for request forgeries
		phpFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		// Get request vars
		$post = phpFrame_Environment_Request::getPost();
		
		// Save issue using issues model
		$modelContacts = $this->getModel('contacts');
		$row = $modelContacts->saveContact($post);
		if ($row === false) {
			$this->_sysevents->setSummary($modelContacts->getLastError());
		}
		else {
			$this->_sysevents->setSummary(_LANG_CONTACT_SAVED, "success");
			// Set success flag for tests
			$this->_success = true;
		}
		
		$this->setRedirect('index.php?component=com_addressbook');
	}
	
	public function export_contacts() {
		// Get id from request
		$id = phpFrame_Environment_Request::getVar('id', 0);
		
		$modelContacts = $this->getModel('contacts');
		$modelContacts->exportContacts($id);
	}
}
