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
 * projectsController Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class projectsController extends controller {
	var $projectid=null;
	var $project=null;
	var $project_permissions=null;
	var $current_tool=null;
	
	/**
	 * Construct
	 * 
	 * @return	void
	 * @since 	1.0
	 */
	function __construct() {
		// set default request vars
		$this->view = request::getVar('view', 'projects');
		$this->layout = request::getVar('layout', 'list');
		$this->projectid = request::getVar('projectid', 0);
		
		// It is important we invoke the parent's constructor before 
		// running permission check as we need the available views loaded first.
		parent::__construct();
		
		if (!empty($this->projectid)) {
			// Load the project data
			$modelProjects =& $this->getModel('projects');
			$this->project = $modelProjects->getProjects($this->projectid);
					
			// Do security check with custom permission model for projects
			$this->project_permissions =& $this->getModel('permissions');
			$this->project_permissions->checkProjectAccess($this->project, $this->views_available);
			
			// Add pathway item
			$this->addPathwayItem($this->project->name, 'index.php?option=com_projects&view=projects&layout=detail&projectid='.$this->project->id);
			
			// Append page component name to document title
			$document =& factory::getDocument('html');
			if (!empty($document->title)) $document->title .= ' - ';
			$document->title .= $this->project->name;
		}
	}
	
	/**
	 * Save project using model and set redirect
	 * 
	 * @return void
	 * @since 	1.0
	 */
	function save_project() {
		$modelProjects =& $this->getModel('projects');
		$projectid = $modelProjects->saveProject();
		
		if ($projectid !== false) {
			// Redirect depending on "apply" or "save"
			$view = request::getVar('layout', 'list') == 'list' ? 'admin' : 'projects';
		}
		else {
			// Redirect back to form if save failed
			request::setVar('layout', 'form');
			$view = 'projects';
		}
		
		$this->setRedirect('index.php?option=com_projects&view='.$view.'&layout='.request::getVar('layout', 'list').'&projectid='.$projectid);
	}
	
	/**
	 * Delete project and all its associated data.
	 * 
	 * @return void
	 */
	function remove_project() {
		// get model
		$modelProjects =& $this->getModel('projects');
		
		if ($modelProjects->deleteProject($this->projectid) === true) {
			error::raise('', 'message', _LANG_PROJECT_DELETED);	
		}
		
		$this->setRedirect('index.php?option=com_projects&view=projects&type=list');
	}
	
	function save_member() {
		// Check for request forgeries
		request::checkToken() or jexit( 'Invalid Token' );
		
		$projectid = request::getVar('projectid', 0);
		$userid = request::getVar('userid', 0);
		$roleid = request::getVar('roleid', 0);
		$invite_member_email = request::getVar('invite_member_email', '');
		
		if (!empty($invite_member_email)) {
			$name = request::getVar('name', 0);
			$new_username = request::getVar('new_username', 0);
			
			$modelUsers =& $this->getModel('users');
			$modelUsers->inviteUser($name, $new_username, $invite_member_email, $projectid, $roleid);
		}
		else {
			// Check if the request was made from the new member form, this sends the username in the HTTP request
			$username = request::getVar('username', '');
			if (empty($userid) && !empty($username)) {
				// Translate username to user id and set column
				$userid = iOfficeHelperUsers::username2id($username);
			}

			$modelProjects =& $this->getModel('projects');
			$modelProjects->saveMember($projectid, $userid, $roleid);

			error::raise('', 'error',  _LANG_PROJECT_NEW_MEMBER_SAVED);	
		}
		
		$this->setRedirect('index.php?option=com_projects&view=projects&type=admin&projectid='.$projectid);
	}
	
	function remove_member() {
		$projectid = request::getVar('projectid', 0);
		$userid = request::getVar('userid', 0);
		
		$modelProjects = &$this->getModel('projects');
		$modelProjects->deleteMember($projectid, $userid);
		
		error::raise('', 'error', _LANG_PROJECT_MEMBER_DELETED);
		
		$this->setRedirect('index.php?option=com_projects&view=projects&type=admin&projectid='.$projectid);
	}
	
	function admin_change_member_role() {
		$projectid = request::getVar('projectid', 0);
		$userid = request::getVar('userid', 0);
		$roleid = request::getVar('roleid', 0);
		
		$modelProjects = &$this->getModel('projects');
		$modelProjects->changeMemberRole($projectid, $userid, $roleid);
		
		error::raise('', 'error', _LANG_PROJECT_MEMBER_ROLE_SAVED);
		
		parent::display();
	}
	
	function save_issue() {
		// Get request vars
		$projectid = request::getVar('projectid', 0);
		$assignees = request::getVar('assignees', 0);
		$notify = request::getVar('notify', false);
		$issueid = request::getVar('id', 0);
		
		// Save issue using issues model
		$modelIssues =& $this->getModel('issues');
		$row = $modelIssues->saveIssue($projectid, $issueid);
		
		// Add entry in activity log
		$modelActivityLog =& $this->getModel('activitylog');
		$action = empty($issueid) ? _LANG_ISSUES_ACTION_NEW : _LANG_ISSUES_ACTION_EDIT;
		$title = $row->title;
		$description = sprintf(_LANG_ISSUES_ACTIVITYLOG_DESCRIPTION, $row->title, $row->description);
		$url = JRoute::_(JURI::Base()."index.php?option=com_projects&view=projects&type=issues_detail&projectid=".$projectid."&issueid=".$row->id);
		if ($notify == 'on') { $notify = true; }
		$modelActivityLog->saveActivityLog($projectid, $row->created_by, 'issues', $action, $title, $description, $url, $assignees, $notify);
		
		error::raise( '', 'error',  text::_( _LANG_ISSUE_SAVED ) );
		
		$this->setRedirect('index.php?option=com_projects&view=projects&type=issues_detail&projectid='.$projectid."&issueid=".$row->id);
	}
	
	function remove_issue() {
		$projectid = request::getVar('projectid', 0);
		$issueid = request::getVar('issueid', 0);
		
		$modelIssues = &$this->getModel('issues');
		$modelIssues->deleteIssue($projectid, $issueid);
		
		error::raise('', 'error', _LANG_ISSUE_DELETED);
		
		$this->setRedirect('index.php?option=com_projects&view=projects&type=issues&projectid='.$projectid);
	}
	
	function close_issue() {
		$projectid = request::getVar('projectid', 0);
		$issueid = request::getVar('issueid', 0);
		
		$modelIssues = &$this->getModel('issues');
		$row = $modelIssues->closeIssue($projectid, $issueid);
		
		// Get assignees
		$assignees = $modelIssues->getAssignees($issueid, false);
		
		// Add entry in activity log
		$modelActivityLog =& $this->getModel('activitylog');
		$action = _LANG_ISSUE_CLOSED;
		$title = $row->title;
		$description = sprintf(_LANG_ISSUES_ACTIVITYLOG_DESCRIPTION, $row->title, $row->description);
		$url = JRoute::_(JURI::Base()."index.php?option=com_projects&view=projects&type=issues_detail&projectid=".$projectid."&issueid=".$row->id);
		$notify = true;
		$modelActivityLog->saveActivityLog($projectid, $row->created_by, 'issues', $action, $title, $description, $url, $assignees, $notify);
		
		error::raise( '', 'error',  text::_( _LANG_ISSUE_CLOSED ) );
		
		$this->setRedirect('index.php?option=com_projects&view=projects&type=issues_detail&projectid='.$projectid."&issueid=".$issueid);
	}
	
	function reopen_issue() {
		$projectid = request::getVar('projectid', 0);
		$issueid = request::getVar('issueid', 0);
		
		$modelIssues = &$this->getModel('issues');
		$row = $modelIssues->reopenIssue($projectid, $issueid);
		
		// Get assignees
		$assignees = $modelIssues->getAssignees($issueid, false);
		
		// Add entry in activity log
		$modelActivityLog =& $this->getModel('activitylog');
		$action = _LANG_ISSUE_REOPENED;
		$title = $row->title;
		$description = sprintf(_LANG_ISSUES_ACTIVITYLOG_DESCRIPTION, $row->title, $row->description);
		$url = JRoute::_(JURI::Base()."index.php?option=com_projects&view=projects&type=issues_detail&projectid=".$projectid."&issueid=".$row->id);
		$notify = true;
		$modelActivityLog->saveActivityLog($projectid, $row->created_by, 'issues', $action, $title, $description, $url, $assignees, $notify);
		
		error::raise('', 'error', LANG_ISSUE_REOPENED);
		
		$this->setRedirect('index.php?option=com_projects&view=projects&type=issues_detail&projectid='.$projectid."&issueid=".$issueid);
	}
	
	function save_file() {
		// Get request vars
		$projectid = request::getVar('projectid', 0);
		$assignees = request::getVar('assignees', 0);
		$notify = request::getVar('notify', false);
		
		// Save file using files model
		$modelFiles =& $this->getModel('files');
		$row = $modelFiles->saveFile($projectid);
		
		// Add entry in activity log
		$modelActivityLog =& $this->getModel('activitylog');
		$action = _LANG_FILES_ACTION_NEW;
		$title = $row->title;
		$description = sprintf(_LANG_FILES_ACTIVITYLOG_DESCRIPTION, $row->title, $row->filename, $row->revision, $row->changelog);
		$url = JRoute::_(JURI::Base()."index.php?option=com_projects&task=download_file&projectid=".$projectid."&fileid=".$row->id);
		if ($notify == 'on') { $notify = true; }
		$modelActivityLog->saveActivityLog($projectid, $row->userid, 'files', $action, $title, $description, $url, $assignees, $notify);
		
		error::raise('', 'error', _LANG_FILE_SAVED);
		
		$this->setRedirect('index.php?option=com_projects&view=projects&type=files&projectid='.$projectid);
	}
	
	function remove_file() {
		$projectid = request::getVar('projectid', 0);
		$fileid = request::getVar('fileid', 0);
		
		$modelFiles = &$this->getModel('files');
		
		if ($modelFiles->deleteFile($projectid, $fileid) === true) {
			error::raise('', 'error', _LANG_FILE_DELETED);
		}
		
		$this->setRedirect('index.php?option=com_projects&view=projects&type=files&projectid='.$projectid);
	}
	
	function download_file() {
		$projectid = request::getVar('projectid', 0);
		$fileid = request::getVar('fileid', 0);
		
		$modelProjects = &$this->getModel('files');
		$modelProjects->downloadFile($projectid, $fileid);
	}
	
	function save_message() {
		// Get request vars
		$projectid = request::getVar('projectid', 0);
		$assignees = request::getVar('assignees', 0);
		$notify = request::getVar('notify', false);
		
		// Save message using messages model
		$modelMessages =& $this->getModel('messages');
		$row = $modelMessages->saveMessage($projectid);
		
		// Add entry in activity log
		$modelActivityLog =& $this->getModel('activitylog');
		$action = _LANG_MESSAGES_ACTION_NEW;
		$title = $row->subject;
		$description = sprintf(_LANG_MESSAGES_ACTIVITYLOG_DESCRIPTION, $row->subject, $row->body);
		$url = JRoute::_(JURI::Base()."index.php?option=com_projects&view=projects&type=messages_detail&projectid=".$projectid."&messageid=".$row->id);
		if ($notify == 'on') { $notify = true; }
		$modelActivityLog->saveActivityLog($projectid, $row->userid, 'messages', $action, $title, $description, $url, $assignees, $notify);
		
		error::raise('', 'error', _LANG_MESSAGE_SAVED);
		
		$this->setRedirect('index.php?option=com_projects&view=projects&type=messages&projectid='.$projectid);
	}
	
	function remove_message() {
		$projectid = request::getVar('projectid', 0);
		$messageid = request::getVar('messageid', 0);
		
		$modelMessages = &$this->getModel('messages');
		
		if ($modelMessages->deleteMessage($projectid, $messageid) === true) {
			error::raise('', 'error', _LANG_MESSAGE_DELETED);
		}
		
		$this->setRedirect('index.php?option=com_projects&view=projects&type=messages&projectid='.$projectid);
	}
	
	function save_comment() {
		// Get request vars
		$projectid = request::getVar('projectid', 0);
		$assignees = request::getVar('assignees', 0);
		$notify = request::getVar('notify', false);
		
		// Save file using files model
		$modelComments =& $this->getModel('comments');
		$row = $modelComments->saveComment($projectid);
		
		// Add entry in activity log
		$modelActivityLog =& $this->getModel('activitylog');
		$action = _LANG_COMMENTS_ACTION_NEW;
		$title = 'RE: '.$modelComments->itemid2title($row->itemid, $row->type);
		$description = sprintf(_LANG_COMMENTS_ACTIVITYLOG_DESCRIPTION, $title, $row->body);

		switch ($row->type) {
			case 'issues' : 
				$url = "index.php?option=com_projects&view=projects&type=issues_detail&projectid=".$projectid."&issueid=".$row->itemid;
				break;
			case 'messages' : 
				$url = "index.php?option=com_projects&view=projects&type=messages_detail&projectid=".$projectid."&messageid=".$row->itemid;
				break;
			case 'files' : 
				$url = "index.php?option=com_projects&view=projects&type=files_detail&projectid=".$projectid."&fileid=".$row->itemid;
				break;
		}
		$url = JRoute::_(JURI::Base().$url);
		
		if ($notify == 'on') { $notify = true; }
		$modelActivityLog->saveActivityLog($projectid, $row->userid, 'comments', $action, $title, $description, $url, $assignees, $notify);
		
		error::raise('', 'error', _LANG_COMMENT_SAVED);
		
		$close_issue = request::getVar('close_issue', NULL);
		if ($row->type == 'issues' && $close_issue == 'on') {
			$this->close_issue();
		}
		else {
			$this->setRedirect($url);
		}
	}
	
	function save_meeting() {
		// Get request vars
		$projectid = request::getVar('projectid', 0);
		$assignees = request::getVar('assignees', 0);
		$notify = request::getVar('notify', false);
		$meetingid = request::getVar('id', 0);
		
		// Save file using files model
		$modelMeetings =& $this->getModel('meetings');
		$row = $modelMeetings->saveMeeting($projectid, $meetingid);
		
		// Add entry in activity log
		$modelActivityLog =& $this->getModel('activitylog');
		$action = _LANG_MEETINGS_ACTION_NEW;
		$title = $row->name;
		$description = sprintf(_LANG_MEETINGS_ACTIVITYLOG_DESCRIPTION, $row->name, $row->dtstart, $row->dtend);
		$url = JRoute::_(JURI::Base()."index.php?option=com_projects&view=projects&type=meetings_detail&projectid=".$projectid."&meetingid=".$row->id);
		if ($notify == 'on') { $notify = true; }
		$modelActivityLog->saveActivityLog($projectid, $row->created_by, 'meetings', $action, $title, $description, $url, $assignees, $notify);
		
		error::raise('', 'error', _LANG_MEETING_SAVED);
		
		$this->setRedirect('index.php?option=com_projects&view=projects&type=meetings_detail&projectid='.$projectid."&meetingid=".$row->id);
	}
	
	function remove_meeting() {
		$projectid = request::getVar('projectid', 0);
		$meetingid = request::getVar('meetingid', 0);
		
		$modelMeetings = &$this->getModel('meetings');
		
		if ($modelMeetings->deleteMeeting($projectid, $meetingid) === true) {
			error::raise('', 'error', _LANG_MEETING_DELETED);
		}
		
		$this->setRedirect('index.php?option=com_projects&view=projects&type=meetings&projectid='.$projectid);
	}
	
	function save_milestone() {
		// Get request vars
		$projectid = request::getVar('projectid', 0);
		$assignees = request::getVar('assignees', 0);
		$notify = request::getVar('notify', false);
		
		// Save file using files model
		$modelMilestones =& $this->getModel('milestones');
		$row = $modelMilestones->saveMilestone($projectid);
		
		// Add entry in activity log
		$modelActivityLog =& $this->getModel('activitylog');
		$action = _LANG_MILESTONES_ACTION_NEW;
		$title = $row->title;
		$description = sprintf(_LANG_MILESTONES_ACTIVITYLOG_DESCRIPTION, $row->title, $row->due_date);
		$url = JRoute::_(JURI::Base()."index.php?option=com_projects&view=projects&type=milestones_detail&projectid=".$projectid."&milestoneid=".$row->id);
		if ($notify == 'on') { $notify = true; }
		$modelActivityLog->saveActivityLog($projectid, $row->created_by, 'milestones', $action, $title, $description, $url, $assignees, $notify);
		
		error::raise('', 'error', _LANG_MILESTONE_SAVED);
		
		$this->setRedirect('index.php?option=com_projects&view=projects&type=milestones_detail&projectid='.$projectid."&milestoneid=".$row->id);
	}
	
	function remove_milestone() {
		$projectid = request::getVar('projectid', 0);
		$milestoneid = request::getVar('milestoneid', 0);
		
		$modelMilestones = &$this->getModel('milestones');
		
		if ($modelMilestones->deleteMilestone($projectid, $milestoneid) === true) {
			error::raise('', 'error', _LANG_MILESTONE_DELETED);
		}
		
		$this->setRedirect('index.php?option=com_projects&view=projects&type=milestones&projectid='.$projectid);
	}
}
?>