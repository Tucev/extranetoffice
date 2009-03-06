<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_email
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * emailViewMessages Class
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
 * @subpackage 	com_email
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		view, controller
 */
class emailViewMessages extends view {
	var $page_title=null;
	var $accountid=null;
	var $folder=null;
	
	/**
	 * Constructor
	 * 
	 * @return 	void
	 * @since	1.0
	 */
	function __construct() {
		// Set the view template to load (default value is set in controller)
		$this->layout =& request::getVar('layout');
		
		// Set reference to account id if passed in request
		$this->accountid =& request::getVar('accountid', 0);
		
		// Set reference to current mail folder if passed in request
		$this->folder =& request::getVar('folder', 'INBOX');
		
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
		if (request::getVar('layout') != 'list') {
			$document =& factory::getDocument('html');
			$document->title .= ' - '.$this->page_title;
		}
	}
	
	/**
	 * Display messages list layout
	 * 
	 * Custom display method triggered by list layout.
	 * 
	 * @return void
	 */
	function displayMessagesList() {
		$this->page_title = _LANG_EMAIL;

		$model =& $this->getModel('email');
		if ($model->loadUserEmailAccount() === false) {
			error::raise(0, 'warning', _LANG_EMAIL_NO_ACCOUNT );
			return;
		}
		
		// Connect to incoming mail server
		if ($model->openStream($this->folder) !== true) {
			error::raise(0, 'warning', $model->error );
			return;
		}
		
		// Get messages from inbox
		$this->messages = $model->getMessageList();
		// Close connection
		$model->closeStream();
			
		// Get mailboxes outside of inbox
		if ($model->openStream('') !== true) {
			error::raise(0, 'warning', $model->error );
			return;
		}	
		$this->boxes = $model->getMailboxList();
		$model->closeStream();
			
		// Set the page to auto refresh every set amount of time (in seconds)
		//$document =& factory::getDocument('html');
		//$document->setMetaData('refresh', '120', true);
	}
	
	/**
	 * Display message detail layout
	 * 
	 * This method is a custom display method triggered by detail layout.
	 * 
	 * @return void
	 */
	function displayMessagesDetail($uid=0) {
		$this->page_title = _LANG_EMAIL_MESSAGE_DETAIL;
		$this->addPathwayItem($this->page_title);
		
		if (empty($uid)) {
			$uid = request::getVar('uid', 0);
		}
		
		if (empty($uid)) {
			error::raise('', 'error', 'No message was selected');
			return false;
		}
		else {
			// Get message details
			$model =& $this->getModel('email');
			$model->loadUserEmailAccount();
		
			$model->openStream($this->folder);
			$message = $model->getMessageDetail($uid);
			$model->closeStream();
			$this->message =& $message;
		}
		
	}
	
	/**
	 * Display project detail layout
	 * 
	 * This method is a custom display method triggered by detail layout.
	 * 
	 * @return void
	 */
	function displayMessagesForm() {
		$this->page_title = _LANG_EMAIL_NEW;
		
		$model =& $this->getModel('email');
		$model->loadUserEmailAccount();
		$this->account =& $model->account;
		
		$this->addPathwayItem($this->page_title);
	}
}
?>