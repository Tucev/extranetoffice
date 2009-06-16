<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_email
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * emailViewAccounts Class
 * 
 * The methods in this class are invoked by its parent class. See display() 
 * method in 'view' class.
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_email
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		PHPFrame_MVC_View
 */
class emailViewAccounts extends PHPFrame_MVC_View {
	var $page_title=null;
	
	/**
	 * Constructor
	 * 
	 * @return 	void
	 * @since	1.0
	 */
	function __construct($layout) {
		// Invoke the parent to set the view name and default layout
		parent::__construct('accounts', $layout);
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
		$document = PHPFrame::getDocument('html');
		$document->title .= ' - '.$this->page_title;
	}
	
	/**
	 * Display accounts list layout
	 * 
	 * Custom display method triggered by list layout.
	 * 
	 * @return void
	 */
	function displayAccountsList() {
		$modelAccounts = $this->getModel('accounts');
		$this->rows = $modelAccounts->getAccounts($this->_user->id);
		
		$this->_data['page_title'] = _LANG_EMAIL_ACCOUNTS;
		$this->getPathway()->addItem(_LANG_EMAIL_ACCOUNTS);
	}
	
	/**
	 * Display accounts form layout
	 * 
	 * Custom display method triggered by form layout.
	 * 
	 * @return void
	 */
	function displayAccountsForm() {
		$accountid = PHPFrame::Request()->get('accountid', 0);
		
		if (!empty($accountid)) {
			$modelAccounts = $this->getModel('accounts');
			$rows = $modelAccounts->getAccounts($this->_user->id, $accountid);
			$this->row = $rows[0];
		}
		else {
			$this->row->fromname = $this->_user->firstname." ".$this->_user->lastname;
			$this->row->imap_port = 143;
			$this->row->smtp_auth = 1;
			$this->row->smtp_port = 25;
		}
		
		$this->_data['page_title'] = _LANG_EMAIL_ACCOUNTS;
		$this->getPathway()->addItem(_LANG_EMAIL_ACCOUNTS);
	}
}
?>