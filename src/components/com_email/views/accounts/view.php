<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_email
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * emailViewAccounts Class
 * 
 * The methods in this class are invoked by its parent class. See display() 
 * method in 'view' class.
 * 
 * Method name to be triggered will be formed as follows:
 * 
 * <code>
 * $tmpl_specific_method = "display".ucfirst(phpFrame_Environment_Request::getView()).ucfirst($this->tmpl);
 * </code>
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_email
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		view, controller
 */
class emailViewAccounts extends phpFrame_Application_View {
	var $page_title=null;
	
	/**
	 * Constructor
	 * 
	 * @return 	void
	 * @since	1.0
	 */
	function __construct() {
		// Set the view template to load (default value is set in controller)
		$this->layout = phpFrame_Environment_Request::getLayout();
		
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
		$document = phpFrame::getDocument('html');
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
		$this->rows = $modelAccounts->getAccounts($this->user->id);
		
		$this->page_title = _LANG_EMAIL_ACCOUNTS;
		$this->addPathwayItem(_LANG_EMAIL_ACCOUNTS);
	}
	
	/**
	 * Display accounts form layout
	 * 
	 * Custom display method triggered by form layout.
	 * 
	 * @return void
	 */
	function displayAccountsForm() {
		$accountid = phpFrame_Environment_Request::getVar('accountid', 0);
		
		if (!empty($accountid)) {
			$modelAccounts = $this->getModel('accounts');
			$rows = $modelAccounts->getAccounts($this->user->id, $accountid);
			$this->row = $rows[0];
		}
		else {
			$this->row->fromname = $this->user->firstname." ".$this->user->lastname;
			$this->row->imap_port = 143;
			$this->row->smtp_auth = 1;
			$this->row->smtp_port = 25;
		}
		
		$this->page_title = _LANG_EMAIL_ACCOUNTS;
		$this->addPathwayItem(_LANG_EMAIL_ACCOUNTS);
	}
}
?>