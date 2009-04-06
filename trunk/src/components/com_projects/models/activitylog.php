<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
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
class projectsModelActivitylog extends phpFrame_Application_Model {
	/**
	 * Project object
	 *
	 * @var object
	 */
	var $project=null;
	/**
	 * Constructor
	 *
	 * @since 1.0.1
	 */
	function __construct() {
		$this->projectid =& phpFrame_Environment_Request::getVar('projectid', 0);
		
		if (!empty($this->projectid)) {
			// get project data from controller
			$controller =& phpFrame_Base_Singleton::getInstance('projectsController');
			$this->project =& $controller->project;
		}
		
		//TODO: Check permissions
		parent::__construct();
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
	 * @param	int		$projectid
	 * @param	int		$userid
	 * @param	string	$type			Values = 'issues', 'files', 'messages', 'meetings', 'milestones'
	 * @param	string	$description	The log message
	 * @package	string	$url			The url to the item
	 * @param	array	$assignees		An array containing the items assignees.
	 * @return	bool	Returns TRUE on success or FALSE on failure.
	 */
	function saveActivityLog($projectid, $userid, $type, $action, $title, $description, $url, $assignees, $notify) {
		// Store notification in db
		$row =& phpFrame_Base_Singleton::getInstance("projectsTableActivitylog");
		$row->projectid = $projectid;
		$row->userid = $userid;
		$row->type = $type;
		$row->action = $action;
		$row->title = $title;
		$row->description = $description;
		$row->url = $url;
		
		if (!$row->check()) {
			$this->error[] = $row->getLastError();
			return false;
		}
		
		if (!$row->store()) {
			$this->error[] = $row->getLastError();
			return false;
		}
				
		// Send notifications via email
		if ($notify === true && sizeof($assignees) > 0 && $assignees != $userid) {
			return $this->_notify($row, $assignees);
		}
		
		return true;
	}
	
	/**
	 * This function is called when we save an activity log
	 *
	 * @param	object	$row
	 * @param	array	$assignees
	 * @return	bool	Returns TRUE on success or FALSE on failure
	 * @todo	sanatise address, subject & body
	 */
	function _notify($row, $assignees) {
		$uri = phpFrame_Application_Factory::getURI();
		$user_name = phpFrame_User_Helper::id2name($row->userid);
		
		$new_mail = new phpFrame_Mail_Mailer();
		$new_mail->Subject = "[".$this->project->name."] ".$row->action." by ".$user_name;
		$new_mail->Body = phpFrame_HTML_Text::_(sprintf(_LANG_ACTIVITYLOG_NOTIFY_BODY, 
								 $this->project->name, 
								 $row->action." by ".$user_name,
								 $uri->getBase().$row->url, 
								 $row->description)
						);
		
		// Append message id suffix with data to be used when processing replies
		parse_str($row->url, $url_array);
		$pattern = "/".substr($url_array['view'], 0, (strlen($url_array['view'])-1))."id=([0-9]+)/i";
		preg_match($pattern, $row->url, $matches);
		$new_mail->setMessageIdSuffix('o='.phpFrame_Environment_Request::getVar('option').'&p='.$row->projectid.'&t='.$url_array['view'].'&i='.$matches[1]);
		
		// Make sure assignees is an array
		if (!is_array($assignees)) {
			$assignees = array($assignees);
		}
		
		// Get assignees email addresses and exclude the user triggering the notification
		$query = "SELECT firstname, lastname, email ";
		$query .= " FROM #__users ";
		$query .= " WHERE id IN (".implode(',', $assignees).") AND id <> ".$row->userid;
		$this->db->setQuery($query);
		$recipients = $this->db->loadObjectList();
		
		if (is_array($recipients) && count($recipients) > 0) {
			$failed_recipients = array();
			foreach ($recipients as $recipient) {
				if (phpFrame_Utils_Filter::validate($recipient->email, 'email') === false ){
					$failed_recipients[] = $recipient->email;
					continue;
				}
				else {
					$new_mail->AddAddress($recipient->email, phpFrame_User_Helper::fullname_format($recipient->firstname, $recipient->lastname));
					
					// Send email
					if ($new_mail->Send() !== true) {
						$failed_recipients[] = $recipient->email;
						return false;
					}
					
					$new_mail->ClearAllRecipients();
				}
			}
			
			if (count($failed_recipients) > 0) {
				$this->error[] = sprintf(_LANG_EMAIL_NOT_SENT, implode(',', $failed_recipients));
				return false;
			}
			
			return true;
		}
		else {
			$this->error[] = _LANG_ACTIVITYLOG_NO_RECIPIENTS;
			return false;
		}
	}
}
?>