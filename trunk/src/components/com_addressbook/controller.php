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
	function __construct() {
		// set default request vars
		$this->view = phpFrame_Environment_Request::getView('contacts');
		
		// It is important we invoke the parent's constructor before 
		// running permission check as we need the available views loaded first.
		parent::__construct();
	}
	
	function save_contact() {
		// Check for request forgeries
		phpFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		// Get request vars
		$post = phpFrame_Environment_Request::getPost();
		
		// Save issue using issues model
		$modelContacts = $this->getModel('contacts');
		$row = $modelContacts->saveContact($post);
		if ($row === false) {
			phpFrame_Application_Error::raise('', 'error', $modelContacts->getLastError());
		}
		else {
			phpFrame_Application_Error::raise( '', 'message',  _LANG_CONTACT_SAVED);
			
			// Set success flag for tests
			$this->success = true;
		}
		
		$this->setRedirect('index.php?component=com_addressbook');
	}
}
?>