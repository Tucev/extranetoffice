<?php
/**
* @package		ExtranetOffice.Email
* @subpackage	controllers
* @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
* @license		BSD revised. See LICENSE.
* @author 		Luis Montero [e-noise.com]
* @version 		1.0.0
*/

class emailController extends controller {
	function __construct() {
		parent::__construct();
		
		// set default view if none has been set
		$view = request::getVar('view', '');
		if (empty($view)) {
			request::setVar('view', 'list');
		}
	}
	
	function download_attachment() {
		$folder = request::getVar('folder', 'INBOX');
		$msgno = request::getVar('msgno', 0);
		$strFileName = request::getVar('file_name', '');
		$file = request::getVar('file', 0);
		
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
		request::setVar('attachment', $attachment);
		
		parent::display();
	}
	
	function send_email() {
		$recipients = request::getVar('recipients', '');
		$cc = request::getVar('cc', '');
		$bcc = request::getVar('bcc', '');
		$replyto = request::getVar('replyto', '');
		$subject = request::getVar('subject', '');
		$body = request::getVar('body', '');
		$attachments = request::getVar('attachments');
		$flag = request::getVar('flag', '');
		$folder = request::getVar('folder', 'INBOX');
		$save_in_sent = request::getVar('save_in_sent', 0);
		
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
		$folder = request::getVar('folder', '');
		$mailbox = request::getVar('mailbox', '');
		$uid = request::getVar('uid', 0); // can contain a list of ids
		
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
		$folder = request::getVar('folder', 'INBOX');
		$uid = request::getVar('uid', 0); // can contain a list of ids
		$trash = request::getVar('trash', 0);
		
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
		$folder = request::getVar('folder', 'INBOX');
		$uid = request::getInt('uid');
		
		$modelEmail = &$this->getModel('email');
		$modelEmail->loadUserEmailAccount();
		$modelEmail->openStream($folder);
		$modelEmail->undeleteMessage($uid);
		$modelEmail->closeStream();
		
		$this->setRedirect('index.php?option=com_intranetoffice&view=email&folder='.$folder);
	}
	
	function empty_deleted_items() {
		$folder = request::getVar('folder', 'INBOX');
		
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
		$folder = request::getVar('folder', 'INBOX');
		
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
		$folder = request::getVar('folder', 'INBOX');
		$uid = request::getInt('uid');
		$flag = request::getVar('flag', '');
		
		$modelEmail = &$this->getModel('email');
		$modelEmail->loadUserEmailAccount();
		$modelEmail->openStream($folder);
		$modelEmail->setFlags($uid, "\\".$flag);
		$modelEmail->closeStream();
		
		$this->setRedirect('index.php?option=com_intranetoffice&view=email&folder='.$folder);
	}
	
	function clear_flags() {
		$folder = request::getVar('folder', 'INBOX');
		$uid = request::getInt('uid');
		$flag = request::getVar('flag', '');
		
		$modelEmail = &$this->getModel('email');
		$modelEmail->loadUserEmailAccount();
		$modelEmail->openStream($folder);
		$modelEmail->clearFlags($uid, "\\".$flag);
		$modelEmail->closeStream();
		
		$this->setRedirect('index.php?option=com_intranetoffice&view=email&folder='.$folder);
	}
	
	function create_mailbox() {
		$new_folder_path = request::getVar('new_folder_path', '');
		$new_folder_name = request::getVar('new_folder_name', '');
		
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
		$old_box = request::getVar('old_box', '');
		$new_box = request::getVar('new_box', '');
		
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
		$mailbox = request::getVar('mailbox', '');
		$folder = request::getVar('folder', '');
		
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