<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_email
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * emailController Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_email
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class emailController extends PHPFrame_Application_ActionController {
	/**
	 * Constructor
	 * 
	 * @return	void
	 * @since 	1.0
	 */
	protected function __construct() {
		// Invoke parent's constructor to set default action
		parent::__construct('get_messages');
	}
	
	public function get_messages() {
		// Get request vars
		$accountid = PHPFrame::getRequest()->get('accountid', 0);
		$folder = PHPFrame::getRequest()->get('folder', 'INBOX');
		
		// Get account details
		$account = $this->getModel('accounts')->getAccount($accountid);
		
		if ($account === false) {
			$this->_sysevents->setSummary(_LANG_EMAIL_NO_ACCOUNT);
			return;
		}
		
		// Connect to incoming mail server
		$emailModel = $this->getModel('email', array($account));
		//var_dump($emailModel); exit;
		if ($emailModel->openStream($folder) !== true) {
			$this->_sysevents->setSummary($emailModel->getLastError());
			return;
		}
		
		// Get messages from inbox
		$messages = $emailModel->getMessageList();
		var_dump($messages); exit;
		// Close connection
		$model->closeStream();
			
		// Get mailboxes outside of inbox
		if ($model->openStream('') !== true) {
			PHPFrame_Application_Error::raise(0, 'warning', $model->error );
			return;
		}	
		$this->boxes = $model->getMailboxList();
		$model->closeStream();
	}
	
	public function download_attachment() {
		$folder = PHPFrame::getRequest()->get('folder', 'INBOX');
		$msgno = PHPFrame::getRequest()->get('msgno', 0);
		$strFileName = PHPFrame::getRequest()->get('file_name', '');
		$file = PHPFrame::getRequest()->get('file', 0);
		
		$modelEmail = $this->getModel('email');
		$modelEmail->loadUserEmailAccount();
		$modelEmail->openStream($folder);
		$modelEmail->downloadAttachment($msgno, $strFileName, $file);
		//$modelEmail->closeStream(); // Don't need to close stream because script will exit before we get back from previous call.
	}
	
	public function add_attachment() {
		
		$modelEmail = $this->getModel('email');
		$attachment = $modelEmail->addAttachment();
		
		// Save the attachment data in request array so that the view can access it
		PHPFrame::getRequest()->set('attachment', $attachment);
		
		parent::display();
	}
	
	public function send_email() {
		$recipients = PHPFrame::getRequest()->get('recipients', '');
		$cc = PHPFrame::getRequest()->get('cc', '');
		$bcc = PHPFrame::getRequest()->get('bcc', '');
		$replyto = PHPFrame::getRequest()->get('replyto', '');
		$subject = PHPFrame::getRequest()->get('subject', '');
		$body = PHPFrame::getRequest()->get('body', '');
		$attachments = PHPFrame::getRequest()->get('attachments');
		$flag = PHPFrame::getRequest()->get('flag', '');
		$folder = PHPFrame::getRequest()->get('folder', 'INBOX');
		$save_in_sent = PHPFrame::getRequest()->get('save_in_sent', 0);
		
		$modelEmail = $this->getModel('email');
		$modelEmail->loadUserEmailAccount();
		
		if ($modelEmail->sendMessage($recipients, $subject, $body, $cc, $bcc, $replyto, $attachments) === true) {
			JError::raiseNotice( '', JText::_( _INTRANETOFFICE_EMAIL_SENT ) );
			// Mark the original message as replied or forwarded if needed
			if (!empty($flag) && strpos($flag, "|") > 0) {
				$flag_array = explode("|", $flag);
				$uid = $flag_array[0];
				$type = $flag_array[1];
				if ($type = 'reply' || $type = 'reply_all') {
					$flag = "\\Answered";
				}
				$modelEmail->openStream($folder);
				$modelEmail->setFlags($uid, $flag);
				$modelEmail->closeStream();
			}
			
			// Save e-mail in "Sent" folder if requested
			if ($save_in_sent == 1) {
				// Check if Trash folder exists and if not we create it
				$modelEmail->openStream('');
				$modelEmail->mailboxExists('Sent', true);
				$modelEmail->closeStream();
				
				$modelEmail->openStream('Sent');
				$modelEmail->appendMessage($modelEmail->settings->email_address, $recipients, $subject, $body, date("Y-M-d H:i:s"));
				$modelEmail->closeStream();
			}
		}
		else {
			JError::raiseError( '', JText::_( $modelEmail->error_msg ) );
		}
		
		$this->setRedirect('index.php?component=com_intranetoffice&view=email&folder='.$folder);
	}
	
	public function move_email() {
		$folder = PHPFrame::getRequest()->get('folder', '');
		$mailbox = PHPFrame::getRequest()->get('mailbox', '');
		$uid = PHPFrame::getRequest()->get('uid', 0); // can contain a list of ids
		
		if (empty($mailbox) || empty($uid)) {
			JError::raiseNotice( '', JText::_( _INTRANETOFFICE_EMAIL_DELETED ) );
			return false;
		}
		else {
			$modelEmail = $this->getModel('email');
			$modelEmail->loadUserEmailAccount();
			$modelEmail->openStream($folder);
			$modelEmail->moveMessage($uid, $mailbox);
			$modelEmail->closeStream();
			
			JError::raiseNotice( '', JText::_( _INTRANETOFFICE_EMAIL_MOVED ) );
			
			//$this->setRedirect('index.php?component=com_intranetoffice&view=email&folder='.$folder);
			parent::display();
		}
	}
	
	public function remove_email() {
		$folder = PHPFrame::getRequest()->get('folder', 'INBOX');
		$uid = PHPFrame::getRequest()->get('uid', 0); // can contain a list of ids
		$trash = PHPFrame::getRequest()->get('trash', 0);
		
		$modelEmail = $this->getModel('email');
		$modelEmail->loadUserEmailAccount();
		
		if ($trash == 1) {
			// Check if Trash folder exists and if not we create it
			$modelEmail->openStream('');
			$modelEmail->mailboxExists('Trash', true);
			$modelEmail->closeStream();
			
			$modelEmail->openStream($folder);
			$modelEmail->moveMessage($uid, 'Trash');
			$modelEmail->closeStream();
		}
		else {
			$modelEmail->openStream($folder);
			$modelEmail->deleteMessage($uid);
			$modelEmail->closeStream();
		}
		
		JError::raiseNotice( '', JText::_( _INTRANETOFFICE_EMAIL_DELETED ) );
		
		$this->setRedirect('index.php?component=com_intranetoffice&view=email&folder='.$folder);
	}
	
	public function restore_email() {
		$folder = PHPFrame::getRequest()->get('folder', 'INBOX');
		$uid = PHPFrame_Environment_Request::getInt('uid');
		
		$modelEmail = $this->getModel('email');
		$modelEmail->loadUserEmailAccount();
		$modelEmail->openStream($folder);
		$modelEmail->undeleteMessage($uid);
		$modelEmail->closeStream();
		
		$this->setRedirect('index.php?component=com_intranetoffice&view=email&folder='.$folder);
	}
	
	public function empty_deleted_items() {
		$folder = PHPFrame::getRequest()->get('folder', 'INBOX');
		
		$modelEmail = $this->getModel('email');
		$modelEmail->loadUserEmailAccount();
		$modelEmail->openStream($folder);
		$modelEmail->emptyDeletedItems();
		$modelEmail->closeStream();
		
		JError::raiseNotice( '', JText::_( _INTRANETOFFICE_EMAIL_DELETED_ITEMS_EMPTIED ) );
		
		$this->setRedirect('index.php?component=com_intranetoffice&view=email&folder='.$folder);
	}
	
	/**
	* This function deletes all messages in Trash folder and then expunges deleted messages
	* It is different from empty_deleted_items() which only flags selected messages in any folder as deleted
	*/
	public function empty_email_trash() {
		$folder = PHPFrame::getRequest()->get('folder', 'INBOX');
		
		$modelEmail = $this->getModel('email');
		$modelEmail->loadUserEmailAccount();
		$modelEmail->openStream('Trash');
		$modelEmail->emptyMailbox();
		$modelEmail->emptyDeletedItems();
		$modelEmail->closeStream();
		
		JError::raiseNotice( '', JText::_( _INTRANETOFFICE_EMAIL_DELETED_ITEMS_EMPTIED ) );
		
		$this->setRedirect('index.php?component=com_intranetoffice&view=email&folder='.$folder);
	}
	
	public function set_flags() {
		$folder = PHPFrame::getRequest()->get('folder', 'INBOX');
		$uid = PHPFrame_Environment_Request::getInt('uid');
		$flag = PHPFrame::getRequest()->get('flag', '');
		
		$modelEmail = $this->getModel('email');
		$modelEmail->loadUserEmailAccount();
		$modelEmail->openStream($folder);
		$modelEmail->setFlags($uid, "\\".$flag);
		$modelEmail->closeStream();
		
		$this->setRedirect('index.php?component=com_intranetoffice&view=email&folder='.$folder);
	}
	
	public function clear_flags() {
		$folder = PHPFrame::getRequest()->get('folder', 'INBOX');
		$uid = PHPFrame_Environment_Request::getInt('uid');
		$flag = PHPFrame::getRequest()->get('flag', '');
		
		$modelEmail = $this->getModel('email');
		$modelEmail->loadUserEmailAccount();
		$modelEmail->openStream($folder);
		$modelEmail->clearFlags($uid, "\\".$flag);
		$modelEmail->closeStream();
		
		$this->setRedirect('index.php?component=com_intranetoffice&view=email&folder='.$folder);
	}
	
	public function create_mailbox() {
		$new_folder_path = PHPFrame::getRequest()->get('new_folder_path', '');
		$new_folder_name = PHPFrame::getRequest()->get('new_folder_name', '');
		
		if (!empty($new_folder_path)) {
			$new_folder_name = $new_folder_path.".".$new_folder_name;
		}
		$modelEmail = $this->getModel('email');
		$modelEmail->loadUserEmailAccount();
		$modelEmail->openStream('');
		$modelEmail->createMailbox($new_folder_name);
		$modelEmail->closeStream();
		
		//$this->setRedirect('index.php?component=com_intranetoffice&view=email&folder='.$folder);
		parent::display();
	}
	
	public function rename_mailbox() {
		$old_box = PHPFrame::getRequest()->get('old_box', '');
		$new_box = PHPFrame::getRequest()->get('new_box', '');
		
		$modelEmail = $this->getModel('email');
		$modelEmail->loadUserEmailAccount();
		$modelEmail->openStream('');
		$modelEmail->renameMailbox($old_box, $new_box);
		$modelEmail->closeStream();
		
		$errors = JError::getErrors();
		if (count($errors) < 1) {
			JError::raiseNotice( '', JText::_( _INTRANETOFFICE_MAILBOX_RENAMED ) );	
		}
		
		//$this->setRedirect('index.php?component=com_intranetoffice&view=email&folder='.$folder);
		parent::display();
	}
	
	public function delete_mailbox() {
		$mailbox = PHPFrame::getRequest()->get('mailbox', '');
		$folder = PHPFrame::getRequest()->get('folder', '');
		
		$modelEmail = $this->getModel('email');
		$modelEmail->loadUserEmailAccount();
		$modelEmail->openStream($mailbox);
		$modelEmail->deleteMailbox();
		$modelEmail->closeStream();
		
		$errors = JError::getErrors();
		if (count($errors) < 1) {
			JError::raiseNotice( '', JText::_( _INTRANETOFFICE_MAILBOX_DELETED ) );	
		}
		
		// If we have deleted the folder we were looking at we redirect to INBOX
		if ($folder == $mailbox) {
			$folder == 'INBOX';
		}
		$this->setRedirect('index.php?component=com_intranetoffice&view=email&folder='.$folder);
	}
	
	public function save_account() {
		// Check for request forgeries
		PHPFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		$post = PHPFrame::getRequest()->getPost();
		
		$modelAccounts = $this->getModel('accounts');
		$row = $modelAccounts->saveAccount($post);
		
		if ($row !== false) {
			PHPFrame_Application_Error::raise('', 'message', _LANG_EMAIL_ACCOUNT_SAVED);
		}
		else {
			PHPFrame_Application_Error::raise('', 'error', $modelAccounts->getLastError());
		}
		
		$this->setRedirect('index.php?component=com_email&view=accounts');
	}
	
	public function remove_account() {
		$accountid = PHPFrame::getRequest()->get('accountid', 0);
		
		$modelAccounts = $this->getModel('accounts');
		if (!$modelAccounts->deleteAccount($accountid)) {
			PHPFrame_Application_Error::raise('', 'error', $modelAccounts->getLastError());
		}
		else {
			PHPFrame_Application_Error::raise('', 'message', _LANG_EMAIL_ACCOUNT_DELETE_SUCCESS);
		}
		
		$this->setRedirect('index.php?component=com_email&view=accounts');
	}
	
	public function make_default_account() {
		$accountid = PHPFrame::getRequest()->get('accountid', 0);
		
		$modelAccounts = $this->getModel('accounts');
		if (!$modelAccounts->makeDefault($accountid)) {
			PHPFrame_Application_Error::raise('', 'error', $modelAccounts->getLastError());
		}
		else {
			PHPFrame_Application_Error::raise('', 'message', _LANG_EMAIL_ACCOUNT_SAVED);
		}
		
		$this->setRedirect('index.php?component=com_email&view=accounts');
	}
}
