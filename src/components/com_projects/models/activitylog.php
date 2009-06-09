<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * projectsModelActivitylog Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		PHPFrame_Application_Model
 */
class projectsModelActivitylog extends PHPFrame_Application_Model {
	/**
	 * A reference the project this activity log belongs to
	 * 
	 * @var	object
	 */
	private $_project=null;
	
	/**
	 * Constructor
	 *
	 * @since 1.0.1
	 */
	function __construct($project) {
		$this->_project = $project;
	}
	
	/**
	 * Get a collection of activitylog rows
	 * 
	 * @param	object	$list_filter	Object of type PHPFrame_Database_CollectionFilter
	 * @return	object of type PHPFrame_Database_RowCollection
	 */
	function getCollection(PHPFrame_Database_CollectionFilter $list_filter) {
		$query = "SELECT * ";
		$query .= " FROM #__activitylog ";
		$query .= " WHERE projectid = ".$this->_project->id;
		
		// Run query to get total rows before applying filter
		$list_filter->setTotal(PHPFrame::getDB()->query($query)->rowCount());
		
		$query .= $list_filter->getOrderByStmt();
		$query .= $list_filter->getLimitStmt();
		
		return new PHPFrame_Database_RowCollection($query);
	}
	
	/**
	 * This function stores a new activity log entry and notifies the assignees if necessary
	 *
	 * @param	string	$type			Values = 'issues', 'files', 'messages', 'meetings', 'milestones'
	 * @param	string	$description	The log message
	 * @package	string	$url			The url to the item
	 * @param	array	$assignees		An array containing the items assignees.
	 * @return	bool	Returns TRUE on success or FALSE on failure.
	 */
	function insertRow($type, $action, $title, $description, $url, $assignees, $notify) {
		// Store notification in db
		$row = new PHPFrame_Database_Row('#__activitylog');
		$row->set('projectid', $this->_project->id);
		$row->set('userid', PHPFrame::getUser()->id);
		$row->set('type', $type);
		$row->set('action', $action);
		$row->set('title', $title);
		$row->set('description', $description);
		$row->set('url', $url);
		// Store row in db (this will throw exceptions on errors)
		$row->store();
		
		// Send notifications via email
		if ($notify === true && is_array($assignees) && sizeof($assignees) > 0) {
			return $this->_notify($row, $assignees);
		}
		
		return true;
	}
	
	public function deleteRow() {
		echo "I have to delete an activitylog row... Please finish me!!!"; exit;
	}
	
	/**
	 * This function is called when we save an activity log
	 *
	 * @param	object	$row
	 * @param	array	$assignees
	 * @return	bool	Returns TRUE on success or FALSE on failure
	 * @todo	sanatise address, subject & body
	 */
	private function _notify($row, $assignees) {
		$uri = PHPFrame::getURI();
		$user_name = PHPFrame_User_Helper::id2name($row->userid);
		
		$new_mail = new PHPFrame_Mail_Mailer();
		$new_mail->Subject = "[".$this->_project->name."] ".$row->action." by ".$user_name;
		$new_mail->Body = PHPFrame_HTML_Text::_(sprintf(_LANG_ACTIVITYLOG_NOTIFY_BODY, 
								 $this->_project->name, 
								 $row->action." by ".$user_name,
								 PHPFrame_Utils_Rewrite::rewriteURL($row->url, false), 
								 $row->description)
						);
		
		// Append message id suffix with data to be used when processing replies
		parse_str($row->url, $url_array);
		
		// Find tool keyword (file, issue, message, ...)
		preg_match('/action=get_([a-zA-Z]+)/i', $row->url, $tool_matches);
		
		// Find item id usings tool keyword + id (fileid, issueid, ...)
		$pattern = '/'.$tool_matches[1].'id=([0-9]+)/i';
		preg_match($pattern, $row->url, $matches);
		
		$new_mail->setMessageIdSuffix('c='.PHPFrame::getRequest()->getComponentName().'&p='.$row->projectid.'&t='.$tool_matches[1].'s&i='.$matches[1]);
		
		// Get assignees email addresses and exclude the user triggering the notification
		$query = "SELECT firstname, lastname, email ";
		$query .= " FROM #__users ";
		$query .= " WHERE id IN (".implode(',', $assignees).") AND id <> ".$row->userid;
		$recipients = PHPFrame::getDB()->loadObjectList($query);
		
		if (is_array($recipients) && count($recipients) > 0) {
			$failed_recipients = array();
			foreach ($recipients as $recipient) {
				if (PHPFrame_Utils_Filter::validate($recipient->email, 'email') === false ){
					$failed_recipients[] = $recipient->email;
					continue;
				}
				else {
					$new_mail->AddAddress($recipient->email, PHPFrame_User_Helper::fullname_format($recipient->firstname, $recipient->lastname));
					
					// Send email
					if ($new_mail->Send() !== true) {
						$failed_recipients[] = $recipient->email;
						continue;
					}
					
					$new_mail->ClearAllRecipients();
				}
			}
			
			if (count($failed_recipients) > 0) {
				$this->_error[] = sprintf(_LANG_EMAIL_NOT_SENT, implode(',', $failed_recipients));
				return false;
			}
		}
		else {
			$this->_error[] = _LANG_ACTIVITYLOG_NO_RECIPIENTS;
			return false;
		}
		
		return true;
	}
}
