<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * projectsModelProjects Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		model
 */
class projectsModelActivitylog extends model {
	var $config=null;
	var $user=null;
	var $db=null;
	
	/**
	 * Constructor
	 *
	 * @since 1.0.1
	 */
	function __construct() {
		$this->init();
		parent::__construct();
	}
	
	function init() {
		$this->config =& factory::getConfig();
		$this->user =& factory::getUser();
		$this->db =& factory::getDB();
		
		//TODO: Check permissions
	}
	
	function getActivityLog($projectid=0) {
		$query = "SELECT * ";
		$query .= " FROM #__activitylog ";
		$query .= " WHERE projectid = ".$projectid;
		$query .= " ORDER BY ts DESC ";
		$query .= " LIMIT 0,100";
		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}
	
	/**
	 * This function stores a new activity log entry and notifies the assignees if necessary
	 *
	 * @param int $projectid
	 * @param int $userid
	 * @param string $type	Values = 'project', 'tracker', 'files', 'messages', 'meetings', 'polls', 'members'
	 * @param string $description	The log message
	 * @package string $url	The url to the item
	 * @param array $assignees	An array containing the items assignees
	 * @return bool	True in success otherwise False
	 */
	function saveActivityLog($projectid, $userid, $type, $action, $title, $description, $url, $assignees, $notify) {
		// Store notification in db
		$row = new projectsTableActivitylog();
		$row->projectid = $projectid;
		$row->userid = $userid;
		$row->type = $type;
		$row->action = $action;
		$row->title = $title;
		$row->description = $description;
		$row->url = $url;
		
		if (!$row->check()) {
			JError::raiseError(500, $row->getError() );
			return false;
		}
		
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
			return false;
		}
		
		// Send notifications via email
		if ($notify === true) {
			// Test return from _notify method. Raise error if needed and return accordingly.
			if ($this->_notify($row, $assignees) === false) {
				JError::raiseError( 500, text::_( _LANG_ACTIVITYLOG_NOTIFY_FAILED ) );
				return false;
			}	
		}
		
		return true;
	}
	
	/**
	 * This function is called when we save an activity log
	 *
	 * @param obj $row
	 * @param array $assignees
	 * @return boolean
	 */
	function _notify($row, $assignees) {
		jimport( 'joomla.mail.mail' );
		jimport( 'joomla.mail.helper' );
		$new_mail = new JMail();
		
		$sender = $this->config->get('notifications_fromaddress');
		$project_name = projectsHelperProjects::id2name($row->projectid);
		$user_name = usersHelperUsers::id2name($row->userid);
		$subject = "[".$project_name."] ".$row->action." by ".$user_name;
		$body = text::_(sprintf(_LANG_ACTIVITYLOG_NOTIFY_BODY, 
								 $project_name, 
								 $row->action." by ".$user_name, 
								 $row->description, 
								 $row->url)
						);
		
		// Get assignees email addresses
		if (!is_array($assignees)) {
			$assignees = array($assignees);
		}
		$query = "SELECT email FROM #__users WHERE id IN (".implode(',', $assignees).")";
		$this->db->setQuery($query);
		$recipients = $this->db->loadObjectList();
		
		if (is_array($recipients) && count($recipients) > 0) {
			foreach ($recipients as $recipient) {
				$recipient = trim($recipient->email);
				if (!JMailHelper::isEmailAddress($recipient)) {
					$error	= JText::sprintf('EMAIL_INVALID', $recipient);
					JError::raiseWarning(0, $error );
				}
				else {
					$new_mail->addRecipient($recipient);	
				}
			}				
		}

		if ($error)	{
			return false;
		}
		
		// Clean the email data
		$sender = JMailHelper::cleanAddress($sender);
		$subject = JMailHelper::cleanSubject($subject);
		$body = JMailHelper::cleanBody($body);
		$new_mail->addReplyTo(array($sender, $this->config->get('notifications_fromname')));
		$new_mail->setSender($sender);
		$new_mail->FromName = $this->config->get('notifications_fromname');
		$new_mail->setSubject($subject);
		$new_mail->setBody($body);
		$new_mail->useSMTP($this->config->get('notifications_smtpauth'), 
						   $this->config->get('notifications_smtphost'), 
						   $this->config->get('notifications_smtpusername'), 
						   $this->config->get('notifications_smtppassword'));
		//$new_mail->useSendmail();
		
		//echo '<pre>'; var_dump($new_mail); exit;
						   
		if ($new_mail->Send() !== true) {
			JError::raiseWarning( '', 'EMAIL_NOT_SENT' );
			return false;
		}
		else {
			return true;
		}
	}
}
?>