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
		$this->projectid =& request::getVar('projectid', 0);
		
		if (!empty($this->projectid)) {
			// get project data from controller
			$controller =& phpFrame::getInstance('projectsController');
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
		require_once COMPONENT_PATH.DS."tables".DS."activitylog.table.php";
		$row =& phpFrame::getInstance("projectsTableActivitylog");
		$row->projectid = $projectid;
		$row->userid = $userid;
		$row->type = $type;
		$row->action = $action;
		$row->title = $title;
		$row->description = $description;
		$row->url = $url;
		
		if (!$row->check()) {
			$this->error[] =& $row->getLastError();
			return false;
		}
		
		if (!$row->store()) {
			$this->error[] =& $row->getLastError();
			return false;
		}
				
		// Send notifications via email
		if ($notify === true) {
			// Test return from _notify method. Raise error if needed and return accordingly.
			if ($this->_notify($row, $assignees) === false) {
				$this->error[] = _LANG_ACTIVITYLOG_NOTIFY_FAILED;
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
	 * @return bool
	 * @todo sanatise address, subject & body
	 */
	function _notify($row, $assignees) {
		$new_mail = new mail();
		
		$user_name = usersHelper::id2name($row->userid);
		$uri =& factory::getURI();
		
		$new_mail->Sender = $this->config->fromaddress;
		$new_mail->Subject = "[".$this->project->name."] ".$row->action." by ".$user_name;
		$body = text::_(sprintf(_LANG_ACTIVITYLOG_NOTIFY_BODY, 
								 $this->project->name, 
								 $row->action." by ".$user_name, 
								 $row->description, 
								 $uri->getBase().$row->url)
						);
		
		// Get assignees email addresses
		if (!is_array($assignees)) {
			$assignees = array($assignees);
		}
		$query = "SELECT firstname, lastname, email FROM #__users WHERE id IN (".implode(',', $assignees).")";
		$this->db->setQuery($query);
		$recipients = $this->db->loadObjectList();
		
		if (is_array($recipients) && count($recipients) > 0) {
			foreach ($recipients as $recipient) {
				if (filter::validate($recipient->email, 'email') === false ){
					$this->error[] = sprintf('EMAIL_INVALID', $recipient);
					return false;
				}
				else {
					$new_mail->AddAddress($recipient->email, usersHelper::fullname_format($firstname, $lastname));
				}
			}				
		}
		
		$new_mail->FromName = $this->config->get('notifications_fromname');
		$new_mail->Body = $body;
								   
		if ($new_mail->Send() !== true) {
			$this->error[] = sprintf('_LANG_EMAIL_NOT_SENT', $recipient);
			return false;
		}
		else {
			return true;
		}
	}
}
?>