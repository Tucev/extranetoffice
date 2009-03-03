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

		// Attach scripts and stylesheets
		$document =& factory::getDocument('html');
		$document->addScript('lib/contextmenu/webtoolkit.contextmenu.js');
		$document->addStyleSheet('lib/contextmenu/webtoolkit.contextmenu.css');
	
		$model =& $this->getModel('email');
		$model->loadUserEmailAccount();
		
		// Get messages from inbox
		$model->openStream($this->folder);
		$messages = $model->getMessageList();
		$model->closeStream();
		$this->messages =& $messages;
			
		// Get mailboxes outside of inbox
		$model->openStream('');		
		$boxes = $model->getMailboxList();
		$model->closeStream();
		$this->boxes =& $boxes;
			
		// Set the page to auto refresh every set amount of time (in seconds)
		$document =& factory::getDocument('html');
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
		
		// Include jQuery and thickbox
		$document =& factory::getDocument('html');
		$document->addScript('lib/jquery/jquery-1.3.1.min.js');
		$document->addScript('lib/thickbox/thickbox-compressed.js');
		$document->addStyleSheet('lib/thickbox/thickbox.css');
		
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