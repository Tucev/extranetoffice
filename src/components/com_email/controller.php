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
 * emailController Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_email
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class emailController extends phpFrame_Application_Controller {
	/**
	 * Constructor
	 * 
	 * @return	void
	 * @since 	1.0
	 */
	function __construct() {
		// set default request vars
		$this->view = phpFrame_Environment_Request::getVar('view', 'messages');
		$this->layout = phpFrame_Environment_Request::getVar('layout', 'list');
		
		parent::__construct();
	}
	
	function save_account() {
		// Check for request forgeries
		phpFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		$post = phpFrame_Environment_Request::get('post');
		
		$modelAccounts =& $this->getModel('accounts');
		$row = $modelAccounts->saveAccount($post);
		
		if ($row !== false) {
			phpFrame_Application_Error::raise('', 'message', _LANG_EMAIL_ACCOUNT_SAVED);
		}
		else {
			phpFrame_Application_Error::raise('', 'error', $modelAccounts->getLastError());
		}
		
		$this->setRedirect('index.php?option=com_email&view=accounts');
	}
	
	function remove_account() {
		$accountid = phpFrame_Environment_Request::getVar('accountid', 0);
		
		$modelAccounts =& $this->getModel('accounts');
		if (!$modelAccounts->deleteAccount($accountid)) {
			phpFrame_Application_Error::raise('', 'error', $modelAccounts->getLastError());
		}
		else {
			phpFrame_Application_Error::raise('', 'message', _LANG_EMAIL_ACCOUNT_DELETE_SUCCESS);
		}
		
		$this->setRedirect('index.php?option=com_email&view=accounts');
	}
	
	function make_default_account() {
		$accountid = phpFrame_Environment_Request::getVar('accountid', 0);
		
		$modelAccounts =& $this->getModel('accounts');
		if (!$modelAccounts->makeDefault($accountid)) {
			phpFrame_Application_Error::raise('', 'error', $modelAccounts->getLastError());
		}
		else {
			phpFrame_Application_Error::raise('', 'message', _LANG_EMAIL_ACCOUNT_SAVED);
		}
		
		$this->setRedirect('index.php?option=com_email&view=accounts');
	}
	
	function download_attachment() {
		$folder = phpFrame_Environment_Request::getVar('folder', 'INBOX');
		$msgno = phpFrame_Environment_Request::getVar('msgno', 0);
		$strFileName = phpFrame_Environment_Request::getVar('file_name', '');
		$file = phpFrame_Environment_Request::getVar('file', 0);
		
		$modelEmail = &$this->getModel('email');
		$modelEmail->loadUserEmailAccount();
		$modelEmail->openStream($folder);
		$modelEmail->downloadAttachment($msgno, $strFileName, $file);
		//$modelEmail->closeStream(); // Don't need to close stream because script will exit before we get back from previous call.
	}
	
	function add_attachment() {
		
		$modelEmail = &$this->getModel('email');
		$attachment = $modelEmail->addAttachment();
		
		// Save the attachment data in request array so that the view can access it
		phpFrame_Environment_Request::setVar('attachment', $attachment);
		
		parent::display();
	}
	
	function send_email() {
		$recipients = phpFrame_Environment_Request::getVar('recipients', '');
		$cc = phpFrame_Environment_Request::getVar('cc', '');
		$bcc = phpFrame_Environment_Request::getVar('bcc', '');
		$replyto = phpFrame_Environment_Request::getVar('replyto', '');
		$subject = phpFrame_Environment_Request::getVar('subject', '');
		$body = phpFrame_Environment_Request::getVar('body', '');
		$attachments = phpFrame_Environment_Request::getVar('attachments');
		$flag = phpFrame_Environment_Request::getVar('flag', '');
		$folder = phpFrame_Environment_Request::getVar('folder', 'INBOX');
		$save_in_sent = phpFrame_Environment_Request::getVar('save_in_sent', 0);
		
		$modelEmail = &$this->getModel('email');
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
		
		$this->setRedirect('index.php?option=com_intranetoffice&view=email&folder='.$folder);
	}
	
	function move_email() {
		$folder = phpFrame_Environment_Request::getVar('folder', '');
		$mailbox = phpFrame_Environment_Request::getVar('mailbox', '');
		$uid = phpFrame_Environment_Request::getVar('uid', 0); // can contain a list of ids
		
		if (empty($mailbox) || empty($uid)) {
			JError::raiseNotice( '', JText::_( _INTRANETOFFICE_EMAIL_DELETED ) );
			return false;
		}
		else {
			$modelEmail = &$this->getModel('email');
			$modelEmail->loadUserEmailAccount();
			$modelEmail->openStream($folder);
			$modelEmail->moveMessage($uid, $mailbox);
			$modelEmail->closeStream();
			
			JError::raiseNotice( '', JText::_( _INTRANETOFFICE_EMAIL_MOVED ) );
			
			//$this->setRedirect('index.php?option=com_intranetoffice&view=email&folder='.$folder);
			parent::display();
		}
	}
	
	function remove_email() {
		$folder = phpFrame_Environment_Request::getVar('folder', 'INBOX');
		$uid = phpFrame_Environment_Request::getVar('uid', 0); // can contain a list of ids
		$trash = phpFrame_Environment_Request::getVar('trash', 0);
		
		$modelEmail = &$this->getModel('email');
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
		
		$this->setRedirect('index.php?option=com_intranetoffice&view=email&folder='.$folder);
	}
	
	function restore_email() {
		$folder = phpFrame_Environment_Request::getVar('folder', 'INBOX');
		$uid = phpFrame_Environment_Request::getInt('uid');
		
		$modelEmail = &$this->getModel('email');
		$modelEmail->loadUserEmailAccount();
		$modelEmail->openStream($folder);
		$modelEmail->undeleteMessage($uid);
		$modelEmail->closeStream();
		
		$this->setRedirect('index.php?option=com_intranetoffice&view=email&folder='.$folder);
	}
	
	function empty_deleted_items() {
		$folder = phpFrame_Environment_Request::getVar('folder', 'INBOX');
		
		$modelEmail = &$this->getModel('email');
		$modelEmail->loadUserEmailAccount();
		$modelEmail->openStream($folder);
		$modelEmail->emptyDeletedItems();
		$modelEmail->closeStream();
		
		JError::raiseNotice( '', JText::_( _INTRANETOFFICE_EMAIL_DELETED_ITEMS_EMPTIED ) );
		
		$this->setRedirect('index.php?option=com_intranetoffice&view=email&folder='.$folder);
	}
	
	/**
	* This function deletes all messages in Trash folder and then expunges deleted messages
	* It is different from empty_deleted_items() which only flags selected messages in any folder as deleted
	*/
	function empty_email_trash() {
		$folder = phpFrame_Environment_Request::getVar('folder', 'INBOX');
		
		$modelEmail = &$this->getModel('email');
		$modelEmail->loadUserEmailAccount();
		$modelEmail->openStream('Trash');
		$modelEmail->emptyMailbox();
		$modelEmail->emptyDeletedItems();
		$modelEmail->closeStream();
		
		JError::raiseNotice( '', JText::_( _INTRANETOFFICE_EMAIL_DELETED_ITEMS_EMPTIED ) );
		
		$this->setRedirect('index.php?option=com_intranetoffice&view=email&folder='.$folder);
	}
	
	function set_flags() {
		$folder = phpFrame_Environment_Request::getVar('folder', 'INBOX');
		$uid = phpFrame_Environment_Request::getInt('uid');
		$flag = phpFrame_Environment_Request::getVar('flag', '');
		
		$modelEmail = &$this->getModel('email');
		$modelEmail->loadUserEmailAccount();
		$modelEmail->openStream($folder);
		$modelEmail->setFlags($uid, "\\".$flag);
		$modelEmail->closeStream();
		
		$this->setRedirect('index.php?option=com_intranetoffice&view=email&folder='.$folder);
	}
	
	function clear_flags() {
		$folder = phpFrame_Environment_Request::getVar('folder', 'INBOX');
		$uid = phpFrame_Environment_Request::getInt('uid');
		$flag = phpFrame_Environment_Request::getVar('flag', '');
		
		$modelEmail = &$this->getModel('email');
		$modelEmail->loadUserEmailAccount();
		$modelEmail->openStream($folder);
		$modelEmail->clearFlags($uid, "\\".$flag);
		$modelEmail->closeStream();
		
		$this->setRedirect('index.php?option=com_intranetoffice&view=email&folder='.$folder);
	}
	
	function create_mailbox() {
		$new_folder_path = phpFrame_Environment_Request::getVar('new_folder_path', '');
		$new_folder_name = phpFrame_Environment_Request::getVar('new_folder_name', '');
		
		if (!empty($new_folder_path)) {
			$new_folder_name = $new_folder_path.".".$new_folder_name;
		}
		$modelEmail = &$this->getModel('email');
		$modelEmail->loadUserEmailAccount();
		$modelEmail->openStream('');
		$modelEmail->createMailbox($new_folder_name);
		$modelEmail->closeStream();
		
		//$this->setRedirect('index.php?option=com_intranetoffice&view=email&folder='.$folder);
		parent::display();
	}
	
	function rename_mailbox() {
		$old_box = phpFrame_Environment_Request::getVar('old_box', '');
		$new_box = phpFrame_Environment_Request::getVar('new_box', '');
		
		$modelEmail = &$this->getModel('email');
		$modelEmail->loadUserEmailAccount();
		$modelEmail->openStream('');
		$modelEmail->renameMailbox($old_box, $new_box);
		$modelEmail->closeStream();
		
		$errors = JError::getErrors();
		if (count($errors) < 1) {
			JError::raiseNotice( '', JText::_( _INTRANETOFFICE_MAILBOX_RENAMED ) );	
		}
		
		//$this->setRedirect('index.php?option=com_intranetoffice&view=email&folder='.$folder);
		parent::display();
	}
	
	function delete_mailbox() {
		$mailbox = phpFrame_Environment_Request::getVar('mailbox', '');
		$folder = phpFrame_Environment_Request::getVar('folder', '');
		
		$modelEmail = &$this->getModel('email');
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
		$this->setRedirect('index.php?option=com_intranetoffice&view=email&folder='.$folder);
	}
	
}
?>