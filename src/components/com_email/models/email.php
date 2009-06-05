<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_email
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * emailModelEmail Class
 * 
 * This class depends on php-imap extension.
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_email
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		PHPFrame_Application_Model
 */
class emailModelEmail extends PHPFrame_Application_Model {
	/**
	 * Object containing the email account settings
	 * 
	 * @var object
	 */
	private $_account=null;
	/**
	 * IMAP object
	 * 
	 * @var object of type PHPFrame_Mail_IMAP
	 */
	private $_imap=null;
	
	/**
	 * Constructor
	 *
	 * @since 1.0.1
	 */
	public function __construct($account) {
		$this->_account = $account;
	}
	
	/**
	 * Open IMAP stream
	 * 
	 * @param	string	$folder	The mail folder to connect to. Default id 'INBOX'.	
	 * @return	bool
	 */
	function openStream($folder='INBOX') {
	  	// Set mailbox name depending on server type
	  	if ($this->_account->server_type == 'POP3') {
	    	$this->mbox_name = '{'.$this->_account->imap_host.':'.$this->_account->imap_port.'/pop3}'.$folder;
	  	}
	  	if ($this->_account->server_type == 'IMAP') {
	    	$this->mbox_name = '{'.$this->_account->imap_host.':'.$this->_account->imap_port.'/novalidate-cert}'.$folder;
	  	}
	  		
	  	// Open mailbox stream
	  	$this->stream = @imap_open($this->mbox_name, $this->_account->imap_user, $this->_account->imap_password);
	  	if (!$this->stream) {
	  		$this->_error = imap_last_error();
	  		return $this->_error;
	  	}
	  	
	  	return true;
	}
	
	function closeStream() {
		if (!empty($this->stream)) {
			imap_close($this->stream);
			$this->stream = null;
		}
	}
	
	function getMailboxList() {
		if ($this->stream == null) {
			return false;
		}
		
		$boxes = imap_getmailboxes($this->stream, $this->mbox_name, "*");
	    
		if (is_array($boxes)) {
	        foreach ($boxes as $fkey=>$box) {
	            $mapname = str_replace($this->mbox_name, "", imap_utf7_decode($box->name));
	            if ($mapname[0] != ".") {
	                //$attrs[LATT_]=': NO';
	                $list_boxes[$fkey]['name'] = $box->name;
	                $list_boxes[$fkey]['nameX'] = $mapname;
	                $list_boxes[$fkey]['delimiter'] = $box->delimiter;
	                $list_boxes[$fkey]['attributes'] = $box->attributes;
	                $list_boxes[$fkey]['attr_values'] = $this->mailboxAttributes2Array($box->attributes);
	            }
	        }
	    }
	    else {
	    	PHPFrame_Application_Error::raise(0, 'warning', imap_last_error() );
	  		return false;
	    }
	    
	    // Sort mailboxes using emailHelperIMAP_Sort (taken from the horde framework)
	    require_once(COMPONENT_PATH.DS.'helpers'.DS.'imap_sort.helper.php');
	    $imap_sort = new emailHelperIMAP_Sort('.');
	    $imap_sort->sortMailboxes($list_boxes);
	    
	  	return $list_boxes;
	}
	
	/**
	 * This function creates a new mailbox. Must connect to root folder and then provide relative mailbox name. Dots are DS
	 *
	 * @param string $new_folder_name
	 * @return bool
	 */
	function createMailbox($new_folder_name) {
		if (!imap_createmailbox($this->stream, imap_utf7_encode($this->mbox_name.$new_folder_name))) {
			PHPFrame_Application_Error::raise(0, 'warning', imap_last_error() );
	  		return false;
		}
		else {
			return true;
		}
	}
	
	/**
	 * This function renames a mailbox. Must connect to root folder and then provide relative mailbox names for old and new
	 *
	 * @param string $old_box
	 * @param string $new_box
	 * @return bool
	 */
	function renameMailbox($old_box, $new_box) {
		if (!imap_renamemailbox($this->stream, imap_utf7_encode($this->mbox_name.$old_box), imap_utf7_encode($this->mbox_name.$new_box))) {
			PHPFrame_Application_Error::raise(0, 'warning', imap_last_error() );
	  		return false;
		}
		else {
			return true;
		}
	}
	
	/**
	 * This function deletes the mailbox currently connected
	 *
	 * @return bool
	 */
	function deleteMailbox() {
		if (!imap_deletemailbox($this->stream, imap_utf7_encode($this->mbox_name))) {
			PHPFrame_Application_Error::raise(0, 'warning', imap_last_error() );
	  		return false;
		}
		else {
			return true;
		}
	}
	
	function mailboxExists($mailbox, $create=false) {
		$mbox_list = $this->getMailboxList();
		foreach ($mbox_list as $box) {
			if (in_array($mailbox, $box)) $mailbox_exists = true;
		}
		
		if ($mailbox_exists !== true && $create === true) {
			if ($this->createMailbox($mailbox)) {
				return true;
			}
			else {
				return false;
			}
			
		}
		elseif ($mailbox_exists !== true) {
			return false;
		}
		else {
			return true;
		}
	}
	
	function getMessageList() {
		if (!$this->stream) {
			PHPFrame_Application_Error::raise('', 'warning', _LANG_EMAIL_ERROR_GETTING_MESSAGES_NO_STREAM);
			return false;
		} 
		
	  	// Get request vars
	  	$page = PHPFrame::getRequest()->get('page', 1);
	  	$per_page = PHPFrame::getRequest()->get('per_page', 20);
	  	$order_by = PHPFrame::getRequest()->get('order_by', 'date');
	  	$order_dir = PHPFrame::getRequest()->get('order_dir', 'desc');
	  	
	  	// Check messages 
	  	$check = imap_mailboxmsginfo($this->stream);
	  
	  	// Prepare listing with pagination
	  	$limit = ($per_page * $page);
	  	$start = ($limit - $per_page) + 1;
	  	$start = ($start < 1) ? 1 : $start;
	  	$limit = (($limit - $start) != ($per_page-1)) ? ($start + ($per_page-1)) : $limit;
	  	$limit = ($check->Nmsgs < $limit) ? $check->Nmsgs : $limit;
	 
	    $sorting = array('direction' => array('asc' => 0, 'desc' => 1),
	                    	 'by'        => array('date' => SORTDATE,
	                                         'arrival' => SORTARRIVAL,
	                                         'from' => SORTFROM,
	                                         'subject' => SORTSUBJECT,
	                                         'size' => SORTSIZE));
	    $by = (true === is_int($by = $sorting['by'][$order_by])) 
	                           ? $by 
	                           : $sorting['by']['date'];
	    $direction = (true === is_int($direction = $sorting['direction'][$order_dir])) 
	                           ? $direction 
	                           : $sorting['direction']['desc'];
	 
    	$sorted = imap_sort($this->stream, $by, $direction);
	 
	    $msgs = array_chunk($sorted, $per_page);
	    $msgs = $msgs[$page-1];
	    	
	 
	  	$result = @imap_fetch_overview($this->stream, implode($msgs, ','), 0);
	  	if (false === is_array($result)) { 
	    	return false;
	  	}
	 
	  	//sorting!
	  	if(true === is_array($sorted)) {
	    	$tmp_result = array();
	      	foreach($result as $r) {
	      		// Decode UTF and ISO iso-8859-1 "subject" and "from"
	      		$r->subject = $this->mime_header_decode($r->subject);
	      		$r->from = $this->mime_header_decode($r->from);
	      		
	      		//echo '<pre>'; var_dump($r->subject); echo '</pre>';
	      		
	        	$tmp_result[$r->msgno] = $r;
	 
	        	$result = array();
	        	foreach($msgs as $msgno) {
	          		$result[] = $tmp_result[$msgno];
	        	}
	      	}
	 
	      	$return = array('res' => $result,
	                      'current_page' => $page,
						  'per_page' => $per_page,
						  'start' => $start,
	                      'limit' => $limit,
	                      'sorting' => array('by' => $order_by, 'direction' => $order_dir),
	                      'total' => imap_num_msg($this->stream));
	      	$return['pages'] = ceil($return['total'] / $per_page);
	  	}
	  
	  	$return['check'] = $check;
	  	return $return;
	}
	
	/**
	 * This methos retrieves message headers, body and attachment info 
	 * and returns an array with all message data
	 *
	 * @return array
	 */
	function getMessageDetail($uid) {
		$msgno = imap_msgno($this->stream, $uid);
		$mailHeader = imap_headerinfo($this->stream, $msgno);
		
		//echo '<pre>'; var_dump($mailHeader); echo '<pre>'; exit;
		
		$return['uid'] = $uid;
		$return['msgno'] = $msgno;
		$return['date'] = date("D j M Y H:i:s", strtotime($mailHeader->date));
		$return['subject'] = $this->mime_header_decode(strip_tags($mailHeader->subject));
		$return['in_reply_to'] = $mailHeader->in_reply_to;
		$return['message_id'] = $mailHeader->message_id;
		$return['references'] = $mailHeader->references;
		$return['to'] = $mailHeader->toaddress;
		$return['to_address'] = $mailHeader->to[0]->mailbox."@".$mailHeader->to[0]->host;
		$return['to_name'] = $mailHeader->to[0]->personal;
		$return['from'] = $this->mime_header_decode($mailHeader->fromaddress);
		$return['from_address'] = $this->mime_header_decode($mailHeader->from[0]->mailbox."@".$mailHeader->from[0]->host);
		$return['fromname'] = $this->mime_header_decode($mailHeader->from[0]->personal);
		$return['reply_toaddress'] = $mailHeader->reply_to[0]->mailbox."@".$mailHeader->reply_to[0]->host;
		$return['body'] = $this->getBody($msgno, 'web');
		$return['attachments'] = $this->getAttachments($msgno, "*");
		
		return $return;
	}
	
	/**
	 * Send e-mail message
	 *
	 * @param string $recipients
	 * @param string $subject
	 * @param string $body
	 * @param mixed $cc	Either a string or array of strings [e-mail address(es)]
	 * @param mixed $bcc	Either a string or array of strings [e-mail address(es)]
	 * @param array $replyto	Either an array or multi-array of form: array( [0] => E-Mail Address [1] => Name )
	 * @return unknown
	 */
	function sendMessage($recipients, $subject, $body, $cc='', $bcc='', $replyto='', $attachments) {
		$sender = $this->_account->email_address;
		
		jimport( 'joomla.mail.mail' );
		jimport( 'joomla.mail.helper' );
		$new_mail = new JMail();
		
		// get recipients from string, exlpode into array and check
		$recipients_array = explode(';', $recipients);
		if (is_array($recipients_array) && count($recipients_array) > 0) {
			foreach ($recipients_array as $recipient) {
				$recipient = trim($recipient);
				if (!JMailHelper::isEmailAddress($recipient)) {
					$error	= JText::sprintf('EMAIL_INVALID', $recipient);
					PHPFrame_Application_Error::raise(0, 'warning', $error );
				}
				else {
					$new_mail->addRecipient($recipient);	
				}
			}				
		}
		
		// Check sender email address
		if ( !$sender || !JMailHelper::isEmailAddress($sender) ) {
			$error	= JText::sprintf('EMAIL_INVALID', $sender);
			PHPFrame_Application_Error::raise(0, 'warning', $error );
		}

		if ($error)	{
			return false;
		}
		
		// Clean the email data
		$subject = JMailHelper::cleanSubject($subject);
		$body = JMailHelper::cleanBody($body);
		$sender = JMailHelper::cleanAddress($sender);
		
		// Add data to mail object
		if (!empty($attachments)) {
			$attachments = explode(',', $attachments);
			if (is_array($attachments) && count($attachments) > 0) {
				// Add full path to attachments
				for ($i=0; $i<count($attachments); $i++) {
					$attachments_with_full_path[$i] = $this->iOfficeConfig->get('filesystem').DS."email".DS."attachments".DS.$attachments[$i];
				}
				$new_mail->addAttachment($attachments_with_full_path);
			}
		}
		
		
		if (!empty($cc)) {
			$new_mail->addCC($cc);
		}
		if (!empty($bcc)) {
			$new_mail->addBCC($bcc);
		}
		if (!empty($replyto)) {
			$new_mail->addReplyTo($replyto);
		}
		$new_mail->setSender($sender);
		$new_mail->FromName = $this->_account->fromname;
		$new_mail->setSubject($subject);
		$new_mail->setBody($body);
		$new_mail->useSMTP(true, $this->_account->smtp_host, $this->_account->smtp_user, $this->_account->smtp_password);
		//$new_mail->useSendmail();
		
		if ($new_mail->Send() !== true) {
			PHPFrame_Application_Error::raise( '', 'warning', 'EMAIL_NOT_SENT' );
			return false;
		}
		
		// Delete attachments
		if (is_array($attachments_with_full_path) && count($attachments_with_full_path) > 0) {
			foreach ($attachments_with_full_path as $attachment) {
				unlink($attachment);
			}
		}
		
		return true;
	}
	
	/**
	 * Save message in current connnection's mailbox
	 *
	 * @param string $from
	 * @param string $to
	 * @param string $subject
	 * @param string $body
	 * @param string $date
	 * @return bool
	 */
	function appendMessage($from, $to, $subject, $body, $date) {
		$body = JMailHelper::cleanBody($body);
		
		$message = "From: ".$from."\r\n"
                 . "To: ".$to."\r\n"
                 . "Subject: ".$subject."\r\n"
                 . "Date: ".$date."  \r\n"
                 . "\r\n\r\n"
                 . $body."\r\n";
        
		if (imap_append($this->stream, $this->mbox_name, $message, $options)) {
			PHPFrame_Application_Error::raiseNotice( '', _LANG_EMAIL_MESSAGE_SAVED );
			return true;
		}
		else {
			PHPFrame_Application_Error::raise( '', 'warning', _LANG_EMAIL_MESSAGE_NOT_SAVED );
			return false;
		}
	}
	
	function moveMessage($uid, $mailbox) {
		if (strpos($uid, ',') !== false) {
			$uids_array = explode(',', $uid);
			$msgno = '';
			for ($i=0; $i<count($uids_array); $i++) {
				if ($i>0) $msgno .= ",";
				$msgno .= imap_msgno($this->stream, $uids_array[$i]);
			}
		}
		else {
			$msgno = imap_msgno($this->stream, $uid);
		}
		
		if (!empty($msgno)) {
			imap_mail_move($this->stream, $msgno, imap_utf7_encode($mailbox));
			imap_expunge($this->stream);
		}
	}
	
	/**
	 * This function deletes a message from the current mailbox
	 *
	 * @param int $uid Can contain a list of ids separated by commas
	 */
	function deleteMessage($uid) {
		if (strpos($uid, ',') !== false) {
			$uids_array = explode(',', $uid);
			$msgno = '';
			for ($i=0; $i<count($uids_array); $i++) {
				if ($i>0) $msgno .= ",";
				$msgno .= imap_msgno($this->stream, $uids_array[$i]);
			}
		}
		else {
			$msgno = imap_msgno($this->stream, $uid);
		}
		
		if (!empty($msgno)) {
			imap_delete($this->stream, $msgno);
		}
	}
	
	function undeleteMessage($uid) {
		$msgno = imap_msgno($this->stream, $uid);
		if (!empty($msgno)) {
			imap_undelete($this->stream, $msgno);
		}
	}
	
	/**
	 * This method deletes all messages in given mail folder
	 *
	 */
	function emptyMailbox() {
		imap_delete($this->stream, "1:*");
	}

	function emptyDeletedItems() {
		@imap_expunge($this->stream);
	}
	
	/**
	 * Sets flags for message or messages
	 *
	 * @param int $uid
	 * @param string $flag Values: \Seen, \Answered, \Flagged, \Deleted, and \Draft
	 */
	function setFlags($uid, $flag) {
		//TODO: imap_setflag_full can handle a list of uids or even a range. Will have to implement flagging several messages in one call.
		imap_setflag_full($this->stream, $uid, $flag, ST_UID);
	}
	
	/**
	 * Clear flags for message or messages
	 *
	 * @param int $uid
	 * @param string $flag Values: \Seen, \Answered, \Flagged, \Deleted, and \Draft
	 */
	function clearFlags($uid, $flag) {
		imap_clearflag_full($this->stream, $uid, $flag, ST_UID);
	}
	
	/**
	 * This function uploads a file attachment passed as in an HTTP request and adds it to the current message
	 *
	 * @param array $attachment
	 */
	function addAttachment() {
		//TODO: Have to catch errors and look at file permissions
		$upload_target = $this->iOfficeConfig->get('filesystem').DS."email".DS."attachments".DS;
		if (!is_dir($upload_target)) {
			mkdir($upload_target, 0771);
		}
		$accept = $this->iOfficeConfig->get('upload_accept');
		$max_upload_size = (1024*1024*$this->iOfficeConfig->get('max_upload_size'));
		
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'lib'.DS.'enoise'.DS.'upload.php');
		$attachment = enoiseUpload::uploadFile('attachment', $upload_target, $accept, $max_upload_size);
		
		return $attachment;
	}
	
	/**
	 * Download individual attachments for the specified msg in the current connection
	 *
	 * @param int $msgno
	 * @param string $strFileName
	 * @param int $file	This is the part number used to fetch the file contents
	 */
	function downloadAttachment($msgno, $strFileName, $file) {
   		$strFileType = strrev(substr(strrev($strFileName),0,4));
   		$fileContent = imap_fetchbody($this->stream, $msgno, $file+2);
		$ContentType = "application/octet-stream";
	   
	   	if ($strFileType == ".asf") 
	   		$ContentType = "video/x-ms-asf";
	   	if ($strFileType == ".avi")
	   		$ContentType = "video/avi";
	   	if ($strFileType == ".doc")
	   		$ContentType = "application/msword";
	   	if ($strFileType == ".zip")
	   		$ContentType = "application/zip";
	   	if ($strFileType == ".xls")
	   		$ContentType = "application/vnd.ms-excel";
	   	if ($strFileType == ".gif")
	   		$ContentType = "image/gif";
	   	if ($strFileType == ".jpg" || $strFileType == "jpeg")
	   		$ContentType = "image/jpeg";
	   	if ($strFileType == ".wav")
	   		$ContentType = "audio/wav";
	   	if ($strFileType == ".mp3")
	   		$ContentType = "audio/mpeg3";
	   	if ($strFileType == ".mpg" || $strFileType == "mpeg")
	   		$ContentType = "video/mpeg";
	   	if ($strFileType == ".rtf")
	   		$ContentType = "application/rtf";
	   	if ($strFileType == ".htm" || $strFileType == "html")
	   		$ContentType = "text/html";
	   	if ($strFileType == ".xml") 
	   		$ContentType = "text/xml";
	   	if ($strFileType == ".xsl") 
	   		$ContentType = "text/xsl";
	   	if ($strFileType == ".css") 
	   		$ContentType = "text/css";
	   	if ($strFileType == ".php") 
	   		$ContentType = "text/php";
	   	if ($strFileType == ".asp") 
	   		$ContentType = "text/asp";
	   	if ($strFileType == ".pdf")
	   		$ContentType = "application/pdf";
	   	
		header("Content-Type: $ContentType"); 		
		header("Content-Disposition: attachment; filename=\"".$strFileName."\"");
		header("Content-Transfer-Encoding: binary");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Expires: 0");
		ob_clean();
		flush();
		
		if (substr($ContentType,0,4) == "text") {
			echo imap_qprint($fileContent);
		} 
		else {
			echo imap_base64($fileContent);
		}
		
		exit;    
	}
	
	/**
	 * This function is used to handle a problem with imap_utf8.
	 *
	 * @param unknown_type $string
	 * @return unknown
	 */
	function mime_header_decode($string) {
  		$array = imap_mime_header_decode($string);
  		$charset = $array[0]->charset;
  		$text = $array[0]->text;
  		
  		if ($charset == "iso-8859-1") {
  			//TODO: This function is having trouble with spanish characters. Must find solution
  			return $this->decode_ISO88591($string);
  		}
  		elseif ($charset == "utf-8") {
  			return $text;
  		}
  		else {
  			return $text;	
  		}
	}
	
	/**
	 * This function was supposed to decode iso-8859-1 strings to fix problem with spanish characters but doesn't seem to work properly
	 *
	 * @param unknown_type $string
	 * @return unknown
	 */
	function decode_ISO88591($string) {    
		$string = str_replace("=?iso-8859-1?q?", "", $string);
	  	$string = str_replace("=?iso-8859-1?Q?", "", $string);
	  	$string = str_replace("?=", "", $string);
	
	  	$charHex = array("0", "1", "2", "3", "4", "5", "6", "7",
	                  "8", "9", "A", "B", "C", "D", "E", "F");
	       
	  	for ($z=0; $z<sizeof($charHex); $z++) {
	  		for ($i=0; $i<sizeof($charHex); $i++) {
	      		$string = str_replace(("=".($charHex[$z].$charHex[$i])),
	                         chr(hexdec($charHex[$z].$charHex[$i])),
	                         $string);
	    	}
	  	}
	  	
	  	return($string);
	}

	/**
     * This function retrieves the body for the specify msg in the context of the current connection
     *
     * @param int $msgno
     * @param string $format	The format for the returned string. values = 'auto', 'plain', 'html', 'web' (web formats the string to display inside a web page, it handles both text and html parts)
     * @return string
     */
    function getBody($msgno, $format='auto') {
   		// GET HTML BODY
   		if ($format != 'plain') {
	   		$dataHtml = $this->get_part($this->stream, $msgno, "TEXT/HTML");
	   		// Return false if format was explicitly requested as html and no data is available
	   		if (empty($dataHtml) && $format == 'html') {
	   			return false;
	   		}
	   		// return html data
	   		elseif (!empty($dataHtml) && $format == 'html') {
	   			return $dataHtml;
	   		}
	   		// return html formatted for display inside websites
	   		elseif (!empty($dataHtml) && $format == 'web') {
	   			return $this->transformHTML($dataHtml);
	   		}
   		}
   		
   		// GET TEXT BODY
   		if ($format == 'plain' || empty($dataHtml)) {
   			$dataTxt = $this->get_part($this->stream, $msgno, "TEXT/PLAIN");
   			// Return false if no data is available and requested strctly plain
	   		if (empty($dataTxt) && $format == 'plain') {
	   			return false;
	   		}
	   		// Return plain text
   			elseif (!empty($dataTxt) && $format == 'plain') {
	   			return $dataTxt;
	   		}
	   		elseif (!empty($dataTxt) && $format == 'web') {
	   			return $this->format_plain_body($dataTxt);
	   		}
   		}
   		
   		if ($format == 'auto' && !empty($dataTxt)) {
   			return $dataTxt;
   		}
   		elseif ($format == 'auto' && !empty($dataHtml)) {
   			return $dataHtml;
   		}

   		return false;
   	}
   	
   	function format_plain_body($msgBody) {
   		$msgBody = ereg_replace("\n","<br>",$msgBody);
	   	$msgBody = preg_replace("/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i","$1http://$2", $msgBody);
	   	$msgBody = preg_replace("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i","<A TARGET=\"_blank\" HREF=\"$1\">$1</A>", $msgBody);
	   	$msgBody = preg_replace("/([\w-?&;#~=\.\/]+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?))/i","<A HREF=\"mailto:$1\">$1</A>",$msgBody);
	   	return "<html><head><title>Messagebody</title></head><body bgcolor=\"white\">$msgBody</body></html>";
   	}
   	
	function get_mime_type(&$structure) {
		$primary_mime_type = array("TEXT", "MULTIPART","MESSAGE", "APPLICATION", "AUDIO","IMAGE", "VIDEO", "OTHER");
   		if($structure->subtype) {
   			return $primary_mime_type[(int) $structure->type] . '/' .$structure->subtype;
   		}
   		return "TEXT/PLAIN";
   	}
   	
   	/**
   	 * This function gets given part for given msg in current connection
   	 *
   	 * @param unknown_type $stream
   	 * @param unknown_type $msg_number
   	 * @param unknown_type $mime_type
   	 * @param unknown_type $structure
   	 * @param unknown_type $part_number
   	 * @return unknown
   	 */
	function get_part($stream, $msg_number, $mime_type, $structure = false, $part_number = false) {
   		if (!$structure) {
   			$structure = imap_fetchstructure($stream, $msg_number);
   		}
   		if ($structure) {
   			if ($mime_type == $this->get_mime_type($structure)) {
   				if (!$part_number) {
   					$part_number = "1";
   				}
   				$text = imap_fetchbody($stream, $msg_number, $part_number);
   				if ($structure->encoding == 3) {
   					return imap_base64($text);
   				} 
   				else if($structure->encoding == 4) {
   					return imap_qprint($text);
   				} 
   				else {
   					return $text;
   			}
   		}
   
		if ($structure->type == 1) /* multipart */ {
   			while (list($index, $sub_structure) = each($structure->parts)) {
   				if ($part_number) {
   					$prefix = $part_number . '.';
   				}
   				$data = $this->get_part($stream, $msg_number, $mime_type, $sub_structure, $prefix . ($index + 1));
   				if ($data) {
   					return $data;
   				}
   			} // END OF WHILE
   		} // END OF MULTIPART
   	} // END OF STRUTURE
   	return false;
	} // END OF FUNCTION
   	
	/**
	 * Transform html body to display inside web page
	 *
	 * @param string $str
	 * @return string
	 */
	function transformHTML($str) {
		if ((strpos($str,"<HTML") < 0) || (strpos($str,"<html") < 0)) {
	  		$makeHeader = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\"></head>\n";
	   		if ((strpos($str,"<BODY") < 0) || (strpos($str,"<body") < 0)) {
	   			$makeBody = "\n<body>\n";
	   			$str = $makeHeader . $makeBody . $str ."\n</body></html>";
	   		} 
	   		else {
	   			$str = $makeHeader . $str ."\n</html>";
	   		}
	   	} 
	   	else {
	   		$str = "<meta http-equiv=\"Content-Type\" content=\"text/html;    charset=iso-8859-1\">\n". $str;
	   	}
		return $str;
	}
	
	/**
	 * Gets attachments for a given message
	 *
	 * @param int $msgno
	 * @param string $disposition	Default is "attachment". To retrieve all use "*"
	 * @return array
	 */
	function getAttachments($msgno, $disposition="") {
		$struct = imap_fetchstructure($this->stream, $msgno);
   		$contentParts = count($struct->parts);
   
   		if ($contentParts >= 2) {
	   		for ($i=2;$i<=$contentParts;$i++) {
   				$att[$i-2] = imap_bodystruct($this->stream, $msgno, $i);
   			}
   			
   			for ($k=0; $k<sizeof($att); $k++) {
   				if ($att[$k]->disposition == "attachment" || $att[$k]->disposition == "inline" || $disposition == "*") {
	   				// Only add to array if there is a filename
	   				if (is_array($att[$k]->dparameters) && count($att[$k]->dparameters) > 0 && ($att[$k]->disposition == "attachment" || $att[$k]->disposition == "inline")) {
	   					foreach ($att[$k]->dparameters as $dparameters) {
		   					if (strpos($dparameters->attribute, 'filename') !== false) {
		   						//TODO: Need to find a way to decode file names correctly
		   						$attachments[$k]['file_name'] = $dparameters->value;
		   						$attachments[$k]['file_size'] = $att[$k]->bytes;
				   				$attachments[$k]['file_type'] = $att[$k]->type;
				   				$attachments[$k]['file_subtype'] = $att[$k]->subtype;
				   				$attachments[$k]['file_disposition'] = $att[$k]->disposition;
				   				// Include parameters in array
		   						$attachments[$k]['dparameters'] = $att[$k]->dparameters;
		   						break;
		   					}
		   				}
		   			}
   				}
   			}
   		}
   		
   		return $attachments;
	}
	
	/**
	 * Parse mailbox attributes into formatted array
	 * This function is used by getMailboxList() in this class
	 *
	 * @param unknown_type $p_attributes
	 * @return unknown
	 */
	function mailboxAttributes2Array($p_attributes) {
	    $attrs[LATT_HASNOCHILDREN]='false';
	    $attrs[LATT_HASCHILDREN]='false';
	    $attrs[LATT_REFERRAL]='false';
	    $attrs[LATT_UNMARKED]='false';
	    $attrs[LATT_MARKED]='false';
	    $attrs[LATT_NOSELECT]='false';
	    $attrs[LATT_NOINFERIORS]='false';
	    $attrsX = $attrs;
	    
	    foreach($attrs as $attrkey=>$attrval) {
	        if ($p_attributes & $attrkey) {
	            $attrsX[$attrkey] = 'true';
	            $p_attributes -= $attrkey;
	        }
	    }
	    
	    return $attrsX;
	}
}
?>