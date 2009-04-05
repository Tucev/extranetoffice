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
 * @todo 		check every call to model (saveActivityLog) for possible failure (see save issue) 
 */
class projectsController extends controller {
	var $projectid=null;
	var $project=null;
	var $project_permissions=null;
	var $current_tool=null;
	
	/**
	 * Constructor
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
			$this->project = $modelProjects->getProjectsDetail($this->projectid);
					
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
	 * This method overrides the parent's Execute task method
	 * 
	 * This method executes a given task (runs a named member method).
	 *
	 * @param 	string $task The task to be executed (default is 'display').
	 * @return 	void
	 * @since	1.0
	 */
	public function execute($task) {
		if ($this->project_permissions->is_allowed || empty($this->projectid)) {
			parent::execute($task);
		}
		else {
			error::raise('', 'error', $this->project_permissions->getLastError());
		}
	}
	
	/**
	 * Save project using model and set redirect
	 * 
	 * @return void
	 * @since 	1.0
	 */
	function save_project() {
		// Check for request forgeries
		crypt::checkToken() or exit( 'Invalid Token' );
		
		$user =& factory::getUser();
		$post = request::get('post');
		
		$modelProjects =& $this->getModel('projects');
		$projectid = $modelProjects->saveProject($post);
		
		if ($projectid !== false) {
			error::raise('', 'message', _LANG_PROJECT_SAVED);
			// Redirect depending on "apply" or "save"
			$view = request::getVar('layout', 'list') == 'list' ? 'admin' : 'projects';	
			
			// If NEW project saved correctly we now make project creator a project member
			if (empty($post['id'])) {
				$modelMembers =& $this->getModel('members');
				if (!$modelMembers->saveMember($projectid, $user->id, '1', false)) {
					error::raise('', 'error', $modelMembers->getLastError());
				}
			}
			
			$this->success = true;
		}
		else {
			error::raise('', 'error', $modelProjects->getLastError());
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
			error::raise('', 'message', _LANG_PROJECT_DELETE_SUCCESS);
			$this->success = true;
		}
		else {
			error::raise('', 'error', _LANG_PROJECT_DELETE_ERROR);
		}
		
		$this->setRedirect('index.php?option=com_projects&view=projects&layout=list');
	}
	
	function save_member() {
		// Check for request forgeries
		crypt::checkToken() or exit( 'Invalid Token' );
		
		$projectid = request::getVar('projectid', 0);
		$roleid = request::getVar('roleid', 0);
		$email = request::getVar('email', '');
		
		// if an email address has been passed to invite a new member we do so
		if (!empty($email)) {
			$modelMembers =& $this->getModel('members');
			// Add the user to the system and add as a member of this project
			if ($modelMembers->inviteNewUser($projectid, $roleid) === false) {
				error::raise('', 'error',  $modelMembers->getLastError());
			}
			else {
				error::raise('', 'message',  _LANG_PROJECT_NEW_MEMBER_SAVED);
			}
		}
		else {
			// Add existing users to project
			$userids = request::getVar('userids', '');
			if (empty($userids)) {
				error::raise('', 'error',  _LANG_USERS_NO_SELECTED);
			}
			else {
				$userids_array = explode(',', $userids);
				$modelMembers =& $this->getModel('members');
				$error = false; // initialise var to flag model errors
				foreach ($userids_array as $userid) {
					if ($modelMembers->saveMember($projectid, $userid, $roleid) === false) {
						$error = true;
						error::raise('', 'warning',  $modelMembers->getLastError());
					}
				}
				
				if ($error === false) {
					error::raise('', 'message',  _LANG_PROJECT_NEW_MEMBER_SAVED);	
				}	
			}
		}
		
		$this->setRedirect('index.php?option=com_projects&view=admin&projectid='.$projectid);
	}
	
	function remove_member() {
		$projectid = request::getVar('projectid', 0);
		$userid = request::getVar('userid', 0);
		
		$modelMembers = &$this->getModel('members');
		if ($modelMembers->deleteMember($projectid, $userid) === true) {
			error::raise('', 'message', _LANG_PROJECT_MEMBER_DELETE_SUCCESS);
		}
		else {
			error::raise('', 'error', _LANG_PROJECT_MEMBER_DELETE_ERROR);	
		}
		
		$this->setRedirect('index.php?option=com_projects&view=admin&projectid='.$projectid);
	}
	
	function admin_change_member_role() {
		$projectid = request::getVar('projectid', 0);
		$userid = request::getVar('userid', 0);
		$roleid = request::getVar('roleid', 0);
		
		$modelMembers = &$this->getModel('members');
		if (!$modelMembers->changeMemberRole($projectid, $userid, $roleid)) {
			error::raise('', 'error', $modelMembers->getLastError());
		}
		else {
			error::raise('', 'message', _LANG_PROJECT_MEMBER_ROLE_SAVED);
		}
		
		$this->setRedirect('index.php?option=com_projects&view=admin&projectid='.$projectid);
	}
	
	function save_issue() {
		// Check for request forgeries
		crypt::checkToken() or exit( 'Invalid Token' );
		
		// Get request vars
		$post = request::get('post');
		
		// Save issue using issues model
		$modelIssues =& $this->getModel('issues');
		$row = $modelIssues->saveIssue($post);
		if ($row === false) {
			error::raise('', 'error', $modelIssues->getLastError());
		}
		else {
			error::raise( '', 'message',  _LANG_ISSUE_SAVED);
			
			// Prepare data for activity log entry
			$action = empty($post['id']) ? _LANG_ISSUES_ACTION_NEW : _LANG_ISSUES_ACTION_EDIT;
			$title = $row->title;
			$description = sprintf(_LANG_ISSUES_ACTIVITYLOG_DESCRIPTION, $row->title, $row->description);
			$url = route::_("index.php?option=com_projects&view=issues&layout=detail&projectid=".$row->projectid."&issueid=".$row->id);
			$notify = $post['notify'] == 'on' ? true : false;
			
			// Add entry in activity log
			$modelActivityLog =& $this->getModel('activitylog');
			if (!$modelActivityLog->saveActivityLog($row->projectid, $row->created_by, 'issues', $action, $title, $description, $url, $post['assignees'], $notify)) {
				error::raise('', 'error', $modelActivityLog->getLastError());
			}
		}
		
		$this->setRedirect('index.php?option=com_projects&view=issues&projectid='.$post['projectid']);	
	}
	
	function remove_issue() {
		$projectid = request::getVar('projectid', 0);
		$issueid = request::getVar('issueid', 0);
		
		$modelIssues = &$this->getModel('issues');
		if ($modelIssues->deleteIssue($projectid, $issueid) === true) {
			error::raise('', 'message', _LANG_ISSUE_DELETE_SUCCESS);
		}
		else {
			error::raise('', 'error', _LANG_ISSUE_DELETE_ERROR);
		}
		
		$this->setRedirect('index.php?option=com_projects&view=issues&projectid='.$projectid);
	}
	
	function close_issue() {
		$projectid = request::getVar('projectid', 0);
		$issueid = request::getVar('issueid', 0);
		
		$modelIssues = &$this->getModel('issues');
		$row = $modelIssues->closeIssue($projectid, $issueid);
		if ($row === false) {
			error::raise('', 'error', $modelIssues->getLastError());
		}
		else {
			error::raise( '', 'message',  _LANG_ISSUE_CLOSED);
			
			// Prepare data for activity log entry
			$assignees = $modelIssues->getAssignees($row->id, false);
			$action = _LANG_ISSUE_CLOSED;
			$title = $row->title;
			$description = sprintf(_LANG_ISSUES_ACTIVITYLOG_DESCRIPTION, $row->title, $row->description);
			$url = route::_("index.php?option=com_projects&view=issues&layout=detail&projectid=".$row->projectid."&issueid=".$row->id);
			
			// Add entry in activity log
			$modelActivityLog =& $this->getModel('activitylog');
			if (!$modelActivityLog->saveActivityLog($row->projectid, $row->created_by, 'issues', $action, $title, $description, $url, $assignees, true)) {
				error::raise('', 'error', $modelActivityLog->getLastError());
			}
		}
		
		$this->setRedirect('index.php?option=com_projects&view=issues&layout=detail&projectid='.$projectid."&issueid=".$issueid);
	}
	
	function reopen_issue() {
		$projectid = request::getVar('projectid', 0);
		$issueid = request::getVar('issueid', 0);
		
		$modelIssues = &$this->getModel('issues');
		$row = $modelIssues->reopenIssue($projectid, $issueid);
		if ($row === false) {
			error::raise('', 'error', $modelIssues->getLastError());
		}
		else {
			error::raise('', 'message', _LANG_ISSUE_REOPENED);
			
			// Prepare data for activity log entry
			$assignees = $modelIssues->getAssignees($row->id, false);
			$action = _LANG_ISSUE_REOPENED;
			$title = $row->title;
			$description = sprintf(_LANG_ISSUES_ACTIVITYLOG_DESCRIPTION, $row->title, $row->description);
			$url = route::_("index.php?option=com_projects&view=issues&layout=detail&projectid=".$row->projectid."&issueid=".$row->id);
			
			// Add entry in activity log
			$modelActivityLog =& $this->getModel('activitylog');
			if (!$modelActivityLog->saveActivityLog($projectid, $row->created_by, 'issues', $action, $title, $description, $url, $assignees, true)) {
				error::raise('', 'error', $modelActivityLog->getLastError());
			}
		}
		
		$this->setRedirect('index.php?option=com_projects&view=issues&layout=detail&projectid='.$projectid."&issueid=".$issueid);
	}
	
	function save_file() {
		// Check for request forgeries
		crypt::checkToken() or exit( 'Invalid Token' );
		
		// Get request vars
		$post = request::get('post');
		
		// Save file using files model
		$modelFiles =& $this->getModel('files');
		$row = $modelFiles->saveFile($post);
		if ($row === false) {
			error::raise('', 'error', $modelFiles->getLastError());
		}
		else {
			error::raise('', 'message', _LANG_FILE_SAVED);
			
			// Prepare data for activity log entry
			$action = _LANG_FILES_ACTION_NEW;
			$title = $row->title;
			$description = sprintf(_LANG_FILES_ACTIVITYLOG_DESCRIPTION, $row->title, $row->filename, $row->revision, $row->changelog);
			$url = route::_("index.php?option=com_projects&view=files&projectid=".$row->projectid."&fileid=".$row->id);
			$notify = $post['notify'] == 'on' ? true : false;
			
			// Add entry in activity log
			$modelActivityLog =& $this->getModel('activitylog');
			if (!$modelActivityLog->saveActivityLog($row->projectid, $row->userid, 'files', $action, $title, $description, $url, $post['assignees'], $notify)) {
				error::raise('', 'error', $modelActivityLog->getLastError());
			}
		}
		
		$this->setRedirect('index.php?option=com_projects&view=files&projectid='.$post['projectid']);
	}
	
	function remove_file() {
		$projectid = request::getVar('projectid', 0);
		$fileid = request::getVar('fileid', 0);
		
		$modelFiles = &$this->getModel('files');
		
		if ($modelFiles->deleteFile($projectid, $fileid) === true) {
			error::raise('', 'message', _LANG_FILE_DELETE_SUCCESS);
		}
		else {
			error::raise('', 'error', _LANG_FILE_DELETE_ERROR);
		}
		
		$this->setRedirect('index.php?option=com_projects&view=files&projectid='.$projectid);
	}
	
	function download_file() {
		$projectid = request::getVar('projectid', 0);
		$fileid = request::getVar('fileid', 0);
		
		$modelProjects =& $this->getModel('files');
		$modelProjects->downloadFile($projectid, $fileid);
	}
	
	function save_message() {
		// Check for request forgeries
		crypt::checkToken() or exit( 'Invalid Token' );
		
		// Get request vars
		$post = request::get('post');
		
		// Save message using messages model
		$modelMessages =& $this->getModel('messages');
		$row = $modelMessages->saveMessage($post);
		if ($row === false) {
			error::raise('', 'error', $modelMessages->getLastError());
		}
		else {
			error::raise('', 'message', _LANG_MESSAGE_SAVED);
			
			// Prepare data for activity log entry
			$action = _LANG_MESSAGES_ACTION_NEW;
			$title = $row->subject;
			$description = sprintf(_LANG_MESSAGES_ACTIVITYLOG_DESCRIPTION, $row->subject, $row->body);
			$url = route::_("index.php?option=com_projects&view=messages&layout=detail&projectid=".$row->projectid."&messageid=".$row->id);
			$notify = $post['notify'] == 'on' ? true : false;
			
			// Add entry in activity log
			$modelActivityLog =& $this->getModel('activitylog');
			if (!$modelActivityLog->saveActivityLog($row->projectid, $row->userid, 'messages', $action, $title, $description, $url, $post['assignees'], $notify)) {
				error::raise('', 'error', $modelActivityLog->getLastError());
			}
		}
		
		$this->setRedirect('index.php?option=com_projects&view=messages&projectid='.$post['projectid']);
	}
	
	function remove_message() {
		$projectid = request::getVar('projectid', 0);
		$messageid = request::getVar('messageid', 0);
		
		$modelMessages = &$this->getModel('messages');
		
		if ($modelMessages->deleteMessage($projectid, $messageid) === true) {
			error::raise('', 'message', _LANG_MESSAGE_DELETE_SUCCESS);
		}
		else {
			error::raise('', 'error', _LANG_MESSAGE_DELETE_ERROR);
		}
		
		$this->setRedirect('index.php?option=com_projects&view=messages&projectid='.$projectid);
	}
	
	function save_comment() {
		// Check for request forgeries
		crypt::checkToken() or exit( 'Invalid Token' );
		
		// Get request vars
		$post = request::get('post');
		
		// Save comment using comments model
		$modelComments =& $this->getModel('comments');
		$row = $modelComments->saveComment($post);
		if ($row === false) {
			error::raise('', 'error', $modelComments->getLastError());
		}
		else {
			error::raise('', 'message', _LANG_COMMENT_SAVED);
			
			// Prepare data for activity log entry
			$action = _LANG_COMMENTS_ACTION_NEW;
			$title = 'RE: '.$modelComments->itemid2title($row->itemid, $row->type);
			$description = sprintf(_LANG_COMMENTS_ACTIVITYLOG_DESCRIPTION, $title, $row->body);
			switch ($row->type) {
				case 'files' : 
					$url = "index.php?option=com_projects&view=files&layout=detail&projectid=".$row->projectid."&fileid=".$row->itemid;
					break;
				case 'issues' : 
					$url = "index.php?option=com_projects&view=issues&layout=detail&projectid=".$row->projectid."&issueid=".$row->itemid;
					break;
				case 'meetings' : 
					$url = "index.php?option=com_projects&view=meetings&layout=detail&projectid=".$row->projectid."&meetingid=".$row->itemid;
					break;
				case 'messages' : 
					$url = "index.php?option=com_projects&view=messages&layout=detail&projectid=".$row->projectid."&messageid=".$row->itemid;
					break;
				case 'milestones' : 
					$url = "index.php?option=com_projects&view=milestones&layout=detail&projectid=".$row->projectid."&milestoneid=".$row->itemid;
					break;
			}
			$url = route::_($url);
			$notify = $post['notify'] == 'on' ? true : false;
			
			// Add entry in activity log
			$modelActivityLog =& $this->getModel('activitylog');
			$modelActivityLog->saveActivityLog($row->projectid, $row->userid, 'comments', $action, $title, $description, $url, $post['assignees'], $notify);
			
			$close_issue = request::getVar('close_issue', NULL);
			if ($row->type == 'issues' && $close_issue == 'on') {
				$modelIssues =& $this->getModel('issues');
				if (!$modelIssues->closeIssue($row->projectid, $row->itemid)) {
					error::raise('', 'error', $modelIssues->getLastError());
				}
			}	
		}
		
		$this->setRedirect($_SERVER['HTTP_REFERER']);
	}
	
	function save_meeting() {
		// Check for request forgeries
		crypt::checkToken() or exit( 'Invalid Token' );
		
		// Get request vars
		$post = request::get('post');
		
		// Save file using files model
		$modelMeetings =& $this->getModel('meetings');
		$row = $modelMeetings->saveMeeting($post);
		if ($row === false){
			error::raise('', 'error', $modelMeetings->getLastError());
		}
		else{
			error::raise('', 'message', _LANG_MEETING_SAVED);
			
			// Prepare data for activity log entry
			$action = empty($post['id']) ? _LANG_MEETINGS_ACTION_NEW : _LANG_MEETINGS_ACTION_EDIT;
			$title = $row->name;
			$description = sprintf(_LANG_MEETINGS_ACTIVITYLOG_DESCRIPTION, $row->name, $row->dtstart, $row->dtend, $row->description);
			$url = route::_("index.php?option=com_projects&view=meetings&layout=detail&projectid=".$row->projectid."&meetingid=".$row->id);
			$notify = $post['notify'] == 'on' ? true : false;
			
			// Add entry in activity log
			$modelActivityLog =& $this->getModel('activitylog');
			if (!$modelActivityLog->saveActivityLog($row->projectid, $row->created_by, 'meetings', $action, $title, $description, $url, $post['assignees'], $notify)) {
				error::raise('', 'error', $modelActivityLog->getLastError());
			}	
		}
		
		$this->setRedirect('index.php?option=com_projects&view=meetings&layout=detail&projectid='.$post['projectid']."&meetingid=".$row->id);
	}
	
	function remove_meeting() {
		$projectid = request::getVar('projectid', 0);
		$meetingid = request::getVar('meetingid', 0);
		
		$modelMeetings =& $this->getModel('meetings');
		
		if ($modelMeetings->deleteMeeting($projectid, $meetingid) === true) {
			error::raise('', 'message', _LANG_MEETING_DELETE_SUCCESS);
		}
		else {
			error::raise('', 'error', _LANG_MEETING_DELETE_ERROR);
		}
		
		$this->setRedirect('index.php?option=com_projects&view=meetings&projectid='.$projectid);
	}
	
	function save_slideshow() {
		// Check for request forgeries
		crypt::checkToken() or exit( 'Invalid Token' );
		
		// Get request vars
		$post = request::get('post');
		
		$modelMeetings =& $this->getModel('meetings');
		$row = $modelMeetings->saveSlideshow($post);
		
		$redirect_url = 'index.php?option=com_projects&view=meetings&layout=slideshows_form&projectid='.$post['projectid'].'&meetingid='.$post['meetingid'];
		
		if ($row === false) {
			error::raise('', 'error', $modelMeetings->getLastError());
		}
		else {
			error::raise('', 'message', _LANG_MEETINGS_SLIDESHOW_SAVE_SUCCESS);
			$redirect_url .= '&slideshowid='.$row->id;
		}
		
		$this->setRedirect($redirect_url);
	}
	
	function remove_slideshow() {
		$projectid = request::getVar('projectid', 0);
		$meetingid = request::getVar('meetingid', 0);
		$slideshowid = request::getVar('slideshowid', 0);
		
		$modelMeetings =& $this->getModel('meetings');
		
		if (!$modelMeetings->deleteSlideshow($projectid, $slideshowid)) {
			error::raise('', 'error', $modelMeetings->getLastError());
		}
		else {
			error::raise('', 'message', _LANG_MEETINGS_SLIDESHOW_DELETE_SUCCESS);
		}
		
		$this->setRedirect('index.php?option=com_projects&view=meetings&layout=detail&projectid='.$projectid.'&meetingid='.$meetingid);
	}
	
	function upload_slide() {
		// Check for request forgeries
		crypt::checkToken() or exit( 'Invalid Token' );
		
		// Get request vars
		$post = request::get('post');
		
		$modelMeetings =& $this->getModel('meetings');
		$row = $modelMeetings->uploadSlide($post);
		
		$tmpl = request::getVar('tmpl', '');
		if ($row === false) {
			if ($tmpl == 'component') {
				echo '0';
				exit;
			}
			else {
				error::raise('', 'error', $modelMeetings->getLastError());
			}
		}
		else {
			if ($tmpl == 'component') {
				echo $row->id;
				exit;
			}
			else {
				error::raise('', 'message', _LANG_MEETINGS_SLIDE_UPLOAD_SUCCESS);
			}
		}
		
		$this->setRedirect('index.php?option=com_projects&view=meetings&projectid='.$projectid);
	}
	
	function remove_slide() {
		$projectid = request::getVar('projectid', 0);
		$slideid = request::getVar('slideid', 0);
		$meetingid = request::getVar('meetingid', 0);
		$slideshowid = request::getVar('slideshowid', 0);
		
		$modelMeetings =& $this->getModel('meetings');
		
		if (!$modelMeetings->deleteSlide($projectid, $slideid)) {
			error::raise('', 'error', $modelMeetings->getLastError());
		}
		else {
			error::raise('', 'message', _LANG_MEETINGS_SLIDE_DELETE_SUCCESS);
		}
		
		$this->setRedirect('index.php?option=com_projects&view=meetings&layout=slideshows_form&projectid='.$projectid.'&meetingid='.$meetingid.'&slideshowid='.$slideshowid);
	}
	
	function save_meetings_files() {
		$projectid = request::getVar('projectid', 0);
		$meetingid = request::getVar('meetingid', 0);
		$fileids = request::getVar('fileids', 0);
		
		if (is_array($fileids)) {
			foreach ($fileids as $key=>$value) {
				$fileids_array[] = $key;
			}
		}
		
		$modelMeetings =& $this->getModel('meetings');
		
		if (!$modelMeetings->saveFiles($meetingid, $fileids_array)) {
			error::raise('', 'error', $modelMeetings->getLastError());
		}
		else {
			error::raise('', 'message', _LANG_MEETINGS_FILES_SAVE_SUCCESS);
		}
		
		$this->setRedirect('index.php?option=com_projects&view=meetings&layout=detail&projectid='.$projectid.'&meetingid='.$meetingid);
	}
	
	function save_milestone() {
		// Check for request forgeries
		crypt::checkToken() or exit( 'Invalid Token' );
		
		// Get request vars
		$post = request::get('post');
		
		// Save file using files model
		$modelMilestones =& $this->getModel('milestones');
		$row = $modelMilestones->saveMilestone($post);
		if ($row === false) {
			error::raise('', 'error', $modelMilestones->getLastError());
		}
		else {
			error::raise('', 'message', _LANG_MILESTONE_SAVED);
			
			// Prepare data for activity log entry
			$action = empty($post['id']) ? _LANG_MILESTONES_ACTION_NEW : _LANG_MILESTONES_ACTION_EDIT;
			$title = $row->title;
			$description = sprintf(_LANG_MILESTONES_ACTIVITYLOG_DESCRIPTION, $row->title, $row->due_date, $row->description);
			$url = route::_("index.php?option=com_projects&view=milestones&layout=detail&projectid=".$row->projectid."&milestoneid=".$row->id);
			$notify = $post['notify'] == 'on' ? true : false;
			
			// Add entry in activity log
			$modelActivityLog =& $this->getModel('activitylog');
			if (!$modelActivityLog->saveActivityLog($row->projectid, $row->created_by, 'milestones', $action, $title, $description, $url, $post['assignees'], $notify)) {
				error::raise('', 'error', $modelActivityLog->getLastError());
			}
		}
		
		$this->setRedirect('index.php?option=com_projects&view=milestones&layout=detail&projectid='.$post['projectid']."&milestoneid=".$row->id);
	}
	
	function remove_milestone() {
		$projectid = request::getVar('projectid', 0);
		$milestoneid = request::getVar('milestoneid', 0);
		
		$modelMilestones =& $this->getModel('milestones');
		
		if ($modelMilestones->deleteMilestone($projectid, $milestoneid) === true) {
			error::raise('', 'message', _LANG_MILESTONE_DELETE_SUCCESS);
		}
		else {
			error::raise('', 'error', _LANG_MILESTONE_DELETE_ERROR);
		}
		
		$this->setRedirect('index.php?option=com_projects&view=milestones&projectid='.$projectid);
	}
	
	function process_incoming_email() {
		// Get models
		$modelComments =& $this->getModel('comments');
		
		// Get mail messages with project comments
		$messages = $modelComments->fetchCommentsFromEmail();
		
		if (is_array($messages) && count($messages) > 0) {
			foreach ($messages as $message) {
				// Check whether the email address belongs to a project member
				if (!empty($message->data['p']) && !empty($message->data['fromaddress'])) {
					// Set the project id
					request::setVar('projectid', $message->data['p']);
					$this->projectid = $message->data['p'];
					
					$userid = usersHelper::email2id($message->data['fromaddress']);
					
					// Load the project data
					$modelProjects =& $this->getModel('projects');
					$this->project = $modelProjects->getProjectsDetail($this->projectid, $userid);
					
					$modelMembers =& $this->getModel('members');
					$roleid = $modelMembers->isMember($message->data['p'], $userid);
					if (!empty($roleid)) {
						$post = array();
						$post['projectid'] = $message->data['p'];
						$post['userid'] = $userid;
						$post['type'] = $message->data['t'];
						$post['itemid'] = $message->data['i'];
						$post['body'] = trim(substr($message->body['PLAIN'], 0, strpos($message->body['PLAIN'], '--- Reply ABOVE THIS LINE to post a comment to the project ---')), " >\n");
						
						$row = $modelComments->saveComment($post);
						if ($row === false) {
							error::raise('', 'error', $modelComments->getLastError());
						}
						else {
							error::raise('', 'message', _LANG_COMMENT_SAVED);
							
							// Prepare data for activity log entry
							$action = _LANG_COMMENTS_ACTION_NEW;
							$title = 'RE: '.$modelComments->itemid2title($row->itemid, $row->type);
							$description = sprintf(_LANG_COMMENTS_ACTIVITYLOG_DESCRIPTION, $title, $row->body);
							switch ($row->type) {
								case 'files' : 
									$url = "index.php?option=com_projects&view=files&layout=detail&projectid=".$row->projectid."&fileid=".$row->itemid;
									break;
								case 'issues' : 
									$url = "index.php?option=com_projects&view=issues&layout=detail&projectid=".$row->projectid."&issueid=".$row->itemid;
									break;
								case 'meetings' : 
									$url = "index.php?option=com_projects&view=meetings&layout=detail&projectid=".$row->projectid."&meetingid=".$row->itemid;
									break;
								case 'messages' : 
									$url = "index.php?option=com_projects&view=messages&layout=detail&projectid=".$row->projectid."&messageid=".$row->itemid;
									break;
								case 'milestones' : 
									$url = "index.php?option=com_projects&view=milestones&layout=detail&projectid=".$row->projectid."&milestoneid=".$row->itemid;
									break;
							}
							$url = route::_($url);
							
							// Get assignees
							$itemModel =& $this->getModel($row->type);
							$assignees = $itemModel->getAssignees($row->itemid, false);
							
							// Add entry in activity log
							$modelActivityLog =& $this->getModel('activitylog');
							$modelActivityLog->project =& $this->project;
							$delete_uids = array();
							if (!$modelActivityLog->saveActivityLog($row->projectid, $row->userid, 'comments', $action, $title, $description, $url, $assignees, true)) {
								error::raise('', 'error', $modelActivityLog->getLastError());
							}
							else {
								$delete_uids[] = $message->uid;
							}
						}
						
						// Delete message from mailbox
						$config =& factory::getConfig();
						$imap = new imap($config->imap_host, $config->imap_port, $config->imap_user, $config->imap_password);
						$imap->deleteMessage(implode(',', $delete_uids));
						$imap->expunge();
						$imap->close();
								
						unset($post);
					}			
				}
			}
			//exit;
		}
	}
}
?>