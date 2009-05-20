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
 * projectsController Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class projectsController extends phpFrame_Application_ActionController {
	/**
	 * Array with available project tools
	 * 
	 * @todo	This need to be refactored so that tools are loaded polymorphically as plugins of a common interface.
	 * @var		array
	 */
	private $_tools=array('admin', 'files', 'issues', 'meetings', 'messages', 'milestones', 'people');
	/**
	 * The selected project row object if any
	 * 
	 * @var	object
	 */
	private $_project=null;
	/**
	 * The project-wide permissions object
	 * 
	 * @var	object
	 */
	private $_permissions=null;
	
	/**
	 * Constructor
	 * 
	 * @return	void
	 * @since 	1.0
	 */
	protected function __construct() {
		// Invoke parent's constructor to set default action
		// It is important we invoke the parent's constructor before 
		// running permission check as we need the available views loaded first.
		parent::__construct('get_projects');
		
		// Get reference to custom permissions model for project tools
		$this->_permissions = projectsModelPermissions::getInstance();
		
		$projectid = phpFrame::getRequest()->get('projectid');
		if (!empty($projectid)) {
			// Load the project data
			$modelProjects = $this->getModel('projects');
			$this->_project = $modelProjects->getRow($projectid);
			
			// Add pathway item
			phpFrame::getPathway()->addItem($this->_project->name, 'index.php?component=com_projects&action=get_project_detail&projectid='.$this->_project->id);
			
			// Append page component name to document title
			$document = phpFrame::getDocument('html');
			if (!empty($document->title)) $document->title .= ' - ';
			$document->title .= $this->_project->name;
		}
	}
	
	public function getTools() {
		return $this->_tools;
	}
	
	public function getProject() {
		return $this->_project;
	}
	
	public function getProjectPermissions() {
		return $this->_permissions;
	}
	
	
	/*
	 * Controller actions below
	 */
	
	
	public function get_projects() {
		// Get request data
		$orderby = phpFrame::getRequest()->get('orderby', 'p.created ');
		$orderdir = phpFrame::getRequest()->get('orderdir', 'DESC');
		$limit = phpFrame::getRequest()->get('limit', 25);
		$limitstart = phpFrame::getRequest()->get('limitstart', 0);
		$search = phpFrame::getRequest()->get('search', '');
		
		// Create list filter needed for getProjects()
		$list_filter = new phpFrame_Database_CollectionFilter($orderby, $orderdir, $limit, $limitstart, $search);
		
		// Get projects using model
		$projects = $this->getModel('projects')->getCollection($list_filter);
		
		// Get view
		$view = $this->getView('projects', 'list');
		// Set view data
		$view->addData('rows', $projects);
		$view->addData('page_nav', new phpFrame_HTML_Pagination($list_filter));
		// Display view
		$view->display();
	}
	
	public function get_project_detail() {
		if (!$this->_authorise("projects")) return;
		
		// Get request data
		$projectid = phpFrame::getRequest()->get('projectid', 0);
		
		// Get overdue issues
		$issues_filter = new phpFrame_Database_CollectionFilter('i.dtstart', 'DESC');
		$overdue_issues = $this->getModel('issues')->getIssues($issues_filter, $projectid, true);
		
		// Get upcoming milestones
		
		// Get project updates
		$activitylog_filter = new phpFrame_Database_CollectionFilter('ts', 'DESC', 25);
		$activitylog_model = $this->getModel('activitylog', array($this->_project));
		$activitylog = $activitylog_model->getCollection($activitylog_filter);
		
		// Get view
		$view = $this->getView('projects', 'detail');
		// Set view data
		$view->addData('row', $this->_project);
		$view->addData('overdue_issues', $overdue_issues);
		$view->addData('activitylog', $activitylog);
		$view->addData('roleid', $this->_permissions->getRoleId());
		// Display view
		$view->display();
	}
	
	public function get_project_form() {
		if (!$this->_authorise("admin")) return;
		
		$projectid = phpFrame::getRequest()->get('projectid', 0);
		// Set default values for tools access
		if (empty($projectid)) {
			$project = new stdClass();
			$project->access = '1';
			$project->access_issues = '2';
			$project->access_messages = '2';
			$project->access_milestones = '2';
			$project->access_files = '2';
			$project->access_meetings = '3';
			$project->access_polls = '3';
			$project->access_reports = '1';
			$project->access_people = '3';
			$project->access_admin = '1';
		}
		else {
			$project = $this->_project;
		}
		
		// Get view
		$view = $this->getView('admin', 'form');
		// Set view data
		$view->addData('project', $project);
		// Display view
		$view->display();
	}
	
	/**
	 * Save project using model and set redirect
	 * 
	 * @return void
	 * @since 	1.0
	 */
	public function save_project() {
		if (isset($this->_project->id) && !$this->_authorise("admin")) return;
		
		// Check for request forgeries
		phpFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		$post = phpFrame::getRequest()->getPost();
		
		// Save project using model
		$project = $this->getModel('projects')->saveRow($post);
		
		if ($project instanceof phpFrame_Database_Row && $project->get('id') > 0) {
			$this->_sysevents->setSummary(_LANG_PROJECT_DELETE_SUCCESS, "success");
			
			// If NEW project saved correctly we now make project creator a project member
			if (empty($post['id'])) {
				$modelMembers = $this->getModel('members');
				if (!$modelMembers->saveMember($project->get('id'), phpFrame::getSession()->getUserId(), '1', false)) {
					$this->_sysevents->setSummary($modelMembers->getLastError());
				}
			}
			
			$this->_success = true;
			
			$this->setRedirect("index.php?component=com_projects&action=get_admin&projectid=".$project->get('id'));
		}
		else {
			$this->_sysevents->setSummary(_LANG_PROJECT_SAVE_ERROR);
			// Redirect back to form
			$this->setRedirect("index.php?component=com_projects&action=get_project_form");
		}
	}
	
	/**
	 * Delete project and all its associated data.
	 * 
	 * @return void
	 */
	public function remove_project() {
		if (!$this->_authorise("admin")) return;
		
		// get model
		$modelProjects = $this->getModel('projects');
		
		try {
			$modelProjects->deleteRow($this->_project->id);
			$this->_sysevents->setSummary(_LANG_PROJECT_DELETE_SUCCESS, "success");
			$this->_success = true;
		}
		catch (phpFrame_Exception $e) {
			var_dump($e);
			$this->_sysevents->setSummary(_LANG_PROJECT_DELETE_ERROR);
		}
		
		$this->setRedirect('index.php?component=com_projects');
	}
	
	public function get_admin() {
		if (!$this->_authorise("admin")) return;
		
		$projectid = phpFrame::getRequest()->get('projectid', 0);
		// Push model into the view
		$members = $this->getModel('members')->getMembers($projectid);
		
		// Get view
		$view = $this->getView('admin', 'list');
		// Set view data
		$view->addData('project', $this->_project);
		$view->addData('tools', $this->_tools);
		$view->addData('members', $members);
		// Display view
		$view->display();
	}
	
	public function get_member_form() {
		if (!$this->_authorise("admin")) return;
		
		// Get view
		$view = $this->getView('admin', 'member_form');
		// Set view data
		$view->addData('project', $this->_project);
		// Display view
		$view->display();
	}
	
	public function save_member() {
		if (!$this->_authorise("admin")) return;
		
		// Check for request forgeries
		phpFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		$projectid = phpFrame::getRequest()->get('projectid', 0);
		$roleid = phpFrame::getRequest()->get('roleid', 0);
		$email = phpFrame::getRequest()->get('email', '');
		$post = phpFrame::getRequest()->getPost();
		
		// if an email address has been passed to invite a new member we do so
		if (!empty($email)) {
			$modelMembers = $this->getModel('members');
			// Add the user to the system and add as a member of this project
			if ($modelMembers->inviteNewUser($post, $projectid, $roleid) === false) {
				$this->_sysevents->setSummary($modelMembers->getLastError());
			}
			else {
				$this->_sysevents->setSummary(_LANG_PROJECT_NEW_MEMBER_SAVED, "success");
				$this->_success = true;
			}
		}
		else {
			// Add existing users to project
			$userids = phpFrame::getRequest()->get('userids', '');
			if (empty($userids)) {
				$this->_sysevents->setSummary(_LANG_USERS_NO_SELECTED);
			}
			else {
				$userids_array = explode(',', $userids);
				$modelMembers = $this->getModel('members');
				$error = false; // initialise var to flag model errors
				foreach ($userids_array as $userid) {
					if ($modelMembers->saveMember($projectid, $userid, $roleid) === false) {
						$error = true;
						$this->_sysevents->setSummary($modelMembers->getLastError());
					}
				}
				
				if ($error === false) {
					$this->_sysevents->setSummary(_LANG_PROJECT_NEW_MEMBER_SAVED, "success");
					$this->_success = true;
				}	
			}
		}
		
		$this->setRedirect('index.php?component=com_projects&action=get_admin&projectid='.$projectid);
	}
	
	public function remove_member() {
		if (!$this->_authorise("admin")) return;
		
		$projectid = phpFrame::getRequest()->get('projectid', 0);
		$userid = phpFrame::getRequest()->get('userid', 0);
		
		$modelMembers = $this->getModel('members');
		if ($modelMembers->deleteMember($projectid, $userid) === true) {
			$this->_sysevents->setSummary(_LANG_PROJECT_MEMBER_DELETE_SUCCESS, "success");
			$this->_success = true;
		}
		else {
			$this->_sysevents->setSummary(_LANG_PROJECT_MEMBER_DELETE_ERROR);	
		}
		
		$this->setRedirect('index.php?component=com_projects&action=get_admin&projectid='.$projectid);
	}
	
	public function get_member_role_form() {
		if (!$this->_authorise("admin")) return;
		
		$userid = phpFrame::getRequest()->get('userid', 0);
		
		if (!empty($userid)) {
			$model = $this->getModel('members');
			$members = $model->getMembers($this->_project->id, $userid);	
		}
		
		// Get view
		$view = $this->getView('admin', 'member_role');
		// Set view data
		$view->addData('project', $this->_project);
		$view->addData('members', $members);
		// Display view
		$view->display();
	}
	
	public function change_member_role() {
		if (!$this->_authorise("admin")) return;
		
		$projectid = phpFrame::getRequest()->get('projectid', 0);
		$userid = phpFrame::getRequest()->get('userid', 0);
		$roleid = phpFrame::getRequest()->get('roleid', 0);
		
		$modelMembers = $this->getModel('members');
		if (!$modelMembers->changeMemberRole($projectid, $userid, $roleid)) {
			$this->_sysevents->setSummary($modelMembers->getLastError());
		}
		else {
			$this->_sysevents->setSummary(_LANG_PROJECT_MEMBER_ROLE_SAVED, "success");
			$this->_success = true;
		}
		
		$this->setRedirect('index.php?component=com_projects&action=get_admin&projectid='.$projectid);
	}
	
	public function get_people() {
		
		$members = $this->getModel('members')->getMembers($this->_project->id);
		
		// Get view
		$view = $this->getView('people', 'list');
		// Set view data
		$view->addData('project', $this->_project);
		$view->addData('rows', $members);
		// Display view
		$view->display();
	}
	
	public function get_issues() {
		if (!$this->_authorise("issues")) return;
		
		// Get request data
		$orderby = phpFrame::getRequest()->get('orderby', 'i.dtstart');
		$orderdir = phpFrame::getRequest()->get('orderdir', 'DESC');
		$limit = phpFrame::getRequest()->get('limit', 25);
		$limitstart = phpFrame::getRequest()->get('limitstart', 0);
		$search = phpFrame::getRequest()->get('search', '');
		
		// Create list filter needed for getIssues()
		$list_filter = new phpFrame_Database_CollectionFilter($orderby, $orderdir, $limit, $limitstart, $search);
		
		// Get issues using model
		$issues = $this->getModel('issues')->getIssues($list_filter, $this->_project->id);
		
		// Get view
		$view = $this->getView('issues', 'list');
		// Set view data
		$view->addData('project', $this->_project);
		$view->addData('rows', $issues);
		$view->addData('page_nav', new phpFrame_HTML_Pagination($list_filter));
		// Display view
		$view->display();
	}
	
	public function get_issue_detail() {
		if (!$this->_authorise("issues")) return;
		
		// Get request data
		$issueid = phpFrame::getRequest()->get('issueid', 0);
		
		// Get issue using model
		$issue = $this->getModel('issues')->getIssuesDetail($this->_project->id, $issueid);
		
		// Get view
		$view = $this->getView('issues', 'detail');
		// Set view data
		$view->addData('project', $this->_project);
		$view->addData('row', $issue);
		// Display view
		$view->display();
	}
	
	public function get_issue_form() {
		if (!$this->_authorise("issues")) return;
		
		// Get request data
		$issueid = phpFrame::getRequest()->get('issueid', 0);
		
		// Get view
		$view = $this->getView('issues', 'form');
		// Set view data
		$view->addData('project', $this->_project);
			
		if ($issueid != 0) {		
			// Get issue using model
			$issue = $this->getModel('issues')->getIssuesDetail($this->_project->id, $issueid);
			$view->addData('row', $issue);
		}
		else {
			$issue = new stdClass();
			$issue->access = 1;
			$view->addData('row', $issue);
		}
		
		// Display view
		$view->display();
	}
	
	public function save_issue() {
		if (!$this->_authorise("issues")) return;
		
		// Check for request forgeries
		phpFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		// Get request vars
		$post = phpFrame::getRequest()->getPost();
		
		// Save issue using issues model
		$modelIssues = $this->getModel('issues');
		$row = $modelIssues->saveIssue($post);

		if ($row === false) {
			$this->_sysevents->setSummary($modelIssues->getLastError());
		}
		else {
			$this->_sysevents->setSummary(_LANG_ISSUE_SAVED, "success");
			
			// Prepare data for activity log entry
			$action = empty($post['id']) ? _LANG_ISSUES_ACTION_NEW : _LANG_ISSUES_ACTION_EDIT;
			$title = $row->title;
			$description = sprintf(_LANG_ISSUES_ACTIVITYLOG_DESCRIPTION, $row->title, $row->description);
			$url = "index.php?component=com_projects&action=get_issue_detail&projectid=".$row->projectid."&issueid=".$row->id;
			$notify = $post['notify'] == 'on' ? true : false;
			
			// Add entry in activity log
			$modelActivityLog = $this->getModel('activitylog', array($this->_project));
			if (!$modelActivityLog->insertRow('issues', $action, $title, $description, $url, $post['assignees'], $notify)) {
				$this->_sysevents->setSummary($modelActivityLog->getLastError());
			}
			else {
				$this->_success = true;
			}
		}
		
		$this->setRedirect("index.php?component=com_projects&action=get_issue_detail&projectid=".$post['projectid']."&issueid=".$row->id);
	}
	
	public function remove_issue() {
		if (!$this->_authorise("issues")) return;
		
		$projectid = phpFrame::getRequest()->get('projectid', 0);
		$issueid = phpFrame::getRequest()->get('issueid', 0);
		
		$modelIssues = &$this->getModel('issues');
		if ($modelIssues->deleteIssue($projectid, $issueid) === true) {
			$this->_sysevents->setSummary(_LANG_ISSUE_DELETE_SUCCESS, "success");
		}
		else {
			$this->_sysevents->setSummary(_LANG_ISSUE_DELETE_ERROR);
		}
		
		$this->setRedirect('index.php?component=com_projects&action=get_issues&projectid='.$projectid);
	}
	
	public function close_issue() {
		if (!$this->_authorise("issues")) return;
		
		$projectid = phpFrame::getRequest()->get('projectid', 0);
		$issueid = phpFrame::getRequest()->get('issueid', 0);
		
		$modelIssues = &$this->getModel('issues');
		$row = $modelIssues->closeIssue($projectid, $issueid);
		if ($row === false) {
			$this->_sysevents->setSummary($modelIssues->getLastError());
		}
		else {
			$this->_sysevents->setSummary(_LANG_ISSUE_CLOSED, "success");
			
			// Prepare data for activity log entry
			$assignees = $modelIssues->getAssignees($row->id, false);
			$action = _LANG_ISSUE_CLOSED;
			$title = $row->title;
			$description = sprintf(_LANG_ISSUES_ACTIVITYLOG_DESCRIPTION, $row->title, $row->description);
			$url = "index.php?component=com_projects&action=get_issue_detail&projectid=".$row->projectid."&issueid=".$row->id;
			
			// Add entry in activity log
			$modelActivityLog = $this->getModel('activitylog');
			if (!$modelActivityLog->insertRow($row->projectid, $row->created_by, 'issues', $action, $title, $description, $url, $assignees, true)) {
				$this->_sysevents->setSummary($modelActivityLog->getLastError());
			}
		}
		
		$this->setRedirect('index.php?component=com_projects&action=get_issue_detail&projectid='.$projectid."&issueid=".$issueid);
	}
	
	public function reopen_issue() {
		if (!$this->_authorise("issues")) return;
		
		$projectid = phpFrame::getRequest()->get('projectid', 0);
		$issueid = phpFrame::getRequest()->get('issueid', 0);
		
		$modelIssues = &$this->getModel('issues');
		$row = $modelIssues->reopenIssue($projectid, $issueid);
		if ($row === false) {
			$this->_sysevents->setSummary($modelIssues->getLastError());
		}
		else {
			$this->_sysevents->setSummary(_LANG_ISSUE_REOPENED, "success");
			
			// Prepare data for activity log entry
			$assignees = $modelIssues->getAssignees($row->id, false);
			$action = _LANG_ISSUE_REOPENED;
			$title = $row->title;
			$description = sprintf(_LANG_ISSUES_ACTIVITYLOG_DESCRIPTION, $row->title, $row->description);
			$url = "index.php?component=com_projects&action=get_issue_detail&projectid=".$row->projectid."&issueid=".$row->id;
			
			// Add entry in activity log
			$modelActivityLog = $this->getModel('activitylog');
			if (!$modelActivityLog->insertRow($projectid, $row->created_by, 'issues', $action, $title, $description, $url, $assignees, true)) {
				$this->_sysevents->setSummary($modelActivityLog->getLastError());
			}
		}
		
		$this->setRedirect('index.php?component=com_projects&action=get_issue_detail&projectid='.$projectid."&issueid=".$issueid);
	}
	
	public function get_files() {
		if (!$this->_authorise("files")) return;
		
		// Get request data
		$projectid = phpFrame::getRequest()->get('projectid', 0);
		$orderby = phpFrame::getRequest()->get('orderby', 'f.ts');
		$orderdir = phpFrame::getRequest()->get('orderdir', 'DESC');
		$limit = phpFrame::getRequest()->get('limit', 25);
		$limitstart = phpFrame::getRequest()->get('limitstart', 0);
		$search = phpFrame::getRequest()->get('search', '');
		
		// Create list filter needed for getFiles()
		$list_filter = new phpFrame_Database_CollectionFilter($orderby, $orderdir, $limit, $limitstart, $search);
		
		// Get files using model
		$files = $this->getModel('files')->getFiles($list_filter, $projectid);
		
		// Get view
		$view = $this->getView('files', 'list');
		// Set view data
		$view->addData('project', $this->_project);
		$view->addData('rows', $files);
		$view->addData('page_nav', new phpFrame_HTML_Pagination($list_filter));
		// Display view
		$view->display();
	}
	
	public function get_file_detail() {
		if (!$this->_authorise("files")) return;
		
		$fileid = phpFrame::getRequest()->get('fileid', 0);
		
		$files = $this->getModel('files')->getFilesDetail($this->_project->id, $fileid);
		
		// Get view
		$view = $this->getView('files', 'detail');
		// Set view data
		$view->addData('project', $this->_project);
		$view->addData('row', $files);
		// Display view
		$view->display();
	}
	
	public function get_file_form() {
		if (!$this->_authorise("files")) return;
		
		$parentid = phpFrame::getRequest()->get('parentid', 0);
		
		if ($parentid > 0) {
			$files = $this->getModel('files')->getFilesDetail($this->_project->id, $parentid);
		}
		
		// Get view
		$view = $this->getView('files', 'form');
		// Set view data
		$view->addData('project', $this->_project);
		$view->addData('row', $files);
		// Display view
		$view->display();
	}
	
	public function save_file() {
		if (!$this->_authorise("files")) return;
		
		// Check for request forgeries
		phpFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		// Get request vars
		$post = phpFrame::getRequest()->getPost();
		
		// Save file using files model
		$modelFiles = $this->getModel('files');
		$row = $modelFiles->saveFile($post);
		
		if ($row === false) {
			$this->_sysevents->setSummary($modelFiles->getLastError());
		}
		else {
			$this->_sysevents->setSummary(_LANG_FILE_SAVED, "success");
			
			// Prepare data for activity log entry
			$action = _LANG_FILES_ACTION_NEW;
			$title = $row->title;
			$description = sprintf(_LANG_FILES_ACTIVITYLOG_DESCRIPTION, $row->title, $row->filename, $row->revision, $row->changelog);
			$url = "index.php?component=com_projects&action=get_file_detail&projectid=".$row->projectid."&fileid=".$row->id;
			$notify = $post['notify'] == 'on' ? true : false;
			
			// Add entry in activity log
			$modelActivityLog = $this->getModel('activitylog');
			if (!$modelActivityLog->insertRow($row->projectid, $row->userid, 'files', $action, $title, $description, $url, $post['assignees'], $notify)) {
				$this->_sysevents->setSummary($modelActivityLog->getLastError());
			}
		}
		
		$this->setRedirect('index.php?component=com_projects&action=get_files&projectid='.$post['projectid']);
	}
	
	public function remove_file() {
		if (!$this->_authorise("files")) return;
		
		$projectid = phpFrame::getRequest()->get('projectid', 0);
		$fileid = phpFrame::getRequest()->get('fileid', 0);
		
		$modelFiles = &$this->getModel('files');
		
		if ($modelFiles->deleteFile($projectid, $fileid) === true) {
			$this->_sysevents->setSummary(_LANG_FILE_DELETE_SUCCESS, "success");
		}
		else {
			$this->_sysevents->setSummary(_LANG_FILE_DELETE_ERROR);
		}
		
		$this->setRedirect('index.php?component=com_projects&action=get_files&projectid='.$projectid);
	}
	
	public function download_file() {
		if (!$this->_authorise("files")) return;
		
		$projectid = phpFrame::getRequest()->get('projectid', 0);
		$fileid = phpFrame::getRequest()->get('fileid', 0);
		
		$modelProjects = $this->getModel('files');
		$modelProjects->downloadFile($projectid, $fileid);
	}
	
	public function get_messages() {
		if (!$this->_authorise("messages")) return;
		
		// Get request data
		$projectid = phpFrame::getRequest()->get('projectid', 0);
		$orderby = phpFrame::getRequest()->get('orderby', 'm.date_sent');
		$orderdir = phpFrame::getRequest()->get('orderdir', 'DESC');
		$limit = phpFrame::getRequest()->get('limit', 25);
		$limitstart = phpFrame::getRequest()->get('limitstart', 0);
		$search = phpFrame::getRequest()->get('search', '');
		
		// Create list filter needed for getMessages()
		$list_filter = new phpFrame_Database_CollectionFilter($orderby, $orderdir, $limit, $limitstart, $search);
		
		// Get messages using model
		$messages = $this->getModel('messages')->getMessages($list_filter, $projectid);
		
		// Get view
		$view = $this->getView('messages', 'list');
		// Set view data
		$view->addData('project', $this->_project);
		$view->addData('rows', $messages);
		$view->addData('page_nav', new phpFrame_HTML_Pagination($list_filter));
		// Display view
		$view->display();
	}
	
	public function get_message_detail() {
		if (!$this->_authorise("messages")) return;
		
		// Get request data
		$messageid = phpFrame::getRequest()->get('messageid', 0);
		
		// Get message using model
		$message = $this->getModel('messages')->getMessagesDetail($this->_project->id, $messageid);
		
		// Get view
		$view = $this->getView('messages', 'detail');
		// Set view data
		$view->addData('project', $this->_project);
		$view->addData('row', $message);
		// Display view
		$view->display();
	}
	
	public function get_message_form() {
		if (!$this->_authorise("messages")) return;
		
		// Get view
		$view = $this->getView('messages', 'form');
		// Set view data
		$view->addData('project', $this->_project);
		// Display view
		$view->display();
	}
	
	public function save_message() {
		if (!$this->_authorise("messages")) return;
		
		// Check for request forgeries
		phpFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		// Get request vars
		$post = phpFrame::getRequest()->getPost();
		
		// Save message using messages model
		$modelMessages = $this->getModel('messages');
		$row = $modelMessages->saveMessage($post);
		if ($row === false) {
			$this->_sysevents->setSummary($modelMessages->getLastError());
		}
		else {
			$this->_sysevents->setSummary(_LANG_MESSAGE_SAVED, "success");
			
			// Prepare data for activity log entry
			$action = _LANG_MESSAGES_ACTION_NEW;
			$title = $row->subject;
			$description = sprintf(_LANG_MESSAGES_ACTIVITYLOG_DESCRIPTION, $row->subject, $row->body);
			$url = "index.php?component=com_projects&action=get_message_detail&projectid=".$row->projectid."&messageid=".$row->id;
			$notify = $post['notify'] == 'on' ? true : false;
			
			// Add entry in activity log
			$modelActivityLog = $this->getModel('activitylog');
			if (!$modelActivityLog->insertRow($row->projectid, $row->userid, 'messages', $action, $title, $description, $url, $post['assignees'], $notify)) {
				$this->_sysevents->setSummary($modelActivityLog->getLastError());
			}
		}
		
		$this->setRedirect('index.php?component=com_projects&action=get_messages&projectid='.$post['projectid']);
	}
	
	public function remove_message() {
		if (!$this->_authorise("messages")) return;
		
		$projectid = phpFrame::getRequest()->get('projectid', 0);
		$messageid = phpFrame::getRequest()->get('messageid', 0);
		
		$modelMessages = &$this->getModel('messages');
		
		if ($modelMessages->deleteMessage($projectid, $messageid) === true) {
			$this->_sysevents->setSummary(_LANG_MESSAGE_DELETE_SUCCESS, "success");
		}
		else {
			$this->_sysevents->setSummary(_LANG_MESSAGE_DELETE_ERROR);
		}
		
		$this->setRedirect('index.php?component=com_projects&action=get_messages&projectid='.$projectid);
	}
	
	public function save_comment() {
		if (!$this->_authorise(phpFrame::getRequest()->get('type'))) return;
		
		// Check for request forgeries
		phpFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		// Get request vars
		$post = phpFrame::getRequest()->getPost();
		
		// Save comment using comments model
		$modelComments = $this->getModel('comments');
		$row = $modelComments->saveComment($post);
		if ($row === false) {
			$this->_sysevents->setSummary($modelComments->getLastError());
		}
		else {
			$this->_sysevents->setSummary(_LANG_COMMENT_SAVED, "success");
			
			// Prepare data for activity log entry
			$action = _LANG_COMMENTS_ACTION_NEW;
			$title = 'RE: '.$modelComments->itemid2title($row->itemid, $row->type);
			$description = sprintf(_LANG_COMMENTS_ACTIVITYLOG_DESCRIPTION, $title, $row->body);
			switch ($row->type) {
				case 'files' : 
					$url = "index.php?component=com_projects&action=get_file_detail&projectid=".$row->projectid."&fileid=".$row->itemid;
					break;
				case 'issues' : 
					$url = "index.php?component=com_projects&action=get_issue_detail&projectid=".$row->projectid."&issueid=".$row->itemid;
					break;
				case 'meetings' : 
					$url = "index.php?component=com_projects&action=get_meeting_detail&projectid=".$row->projectid."&meetingid=".$row->itemid;
					break;
				case 'messages' : 
					$url = "index.php?component=com_projects&action=get_message_detail&projectid=".$row->projectid."&messageid=".$row->itemid;
					break;
				case 'milestones' : 
					$url = "index.php?component=com_projects&action=get_milestone_detail&projectid=".$row->projectid."&milestoneid=".$row->itemid;
					break;
			}
			
			$notify = $post['notify'] == 'on' ? true : false;
			
			// Add entry in activity log
			$modelActivityLog = $this->getModel('activitylog');
			$modelActivityLog->insertRow($row->projectid, $row->userid, 'comments', $action, $title, $description, $url, $post['assignees'], $notify);
			
			$close_issue = phpFrame::getRequest()->get('close_issue', NULL);
			if ($row->type == 'issues' && $close_issue == 'on') {
				$modelIssues = $this->getModel('issues');
				if (!$modelIssues->closeIssue($row->projectid, $row->itemid)) {
					$this->_sysevents->setSummary($modelIssues->getLastError());
				}
			}	
		}
		
		$this->setRedirect($url);
	}
	
	public function get_meetings() {
		if (!$this->_authorise("meetings")) return;
		
		// Get request data
		$orderby = phpFrame::getRequest()->get('orderby', 'm.created');
		$orderdir = phpFrame::getRequest()->get('orderdir', 'DESC');
		$search = phpFrame::getRequest()->get('search', '');
		$search = strtolower( $search );
		$limitstart = phpFrame::getRequest()->get('limitstart', 0);
		$limit = phpFrame::getRequest()->get('limit', 20);
		
		// Create list filter needed for getMeetings()
		$list_filter = new phpFrame_Database_CollectionFilter($orderby, $orderdir, $limit, $limitstart, $search);
		
		// Get meetings using model
		$meetings = $this->getModel('meetings')->getMeetings($list_filter, $this->_project->id);
		
		// Get view
		$view = $this->getView('meetings', 'list');
		// Set view data
		$view->addData('project', $this->_project);
		$view->addData('rows', $meetings);
		$view->addData('page_nav', new phpFrame_HTML_Pagination($list_filter));
		// Display view
		$view->display();
	}
	
	public function get_meeting_detail() {
		if (!$this->_authorise("meetings")) return;
		
		$meetingid = phpFrame::getRequest()->get('meetingid', 0);
		
		$meeting = $this->getModel('meetings')->getMeetingsDetail($this->_project->id, $meetingid);
		
		// Get view
		$view = $this->getView('meetings', 'detail');
		// Set view data
		$view->addData('project', $this->_project);
		$view->addData('row', $meeting);
		// Display view
		$view->display();
	}
	
	public function get_meeting_form() {
		if (!$this->_authorise("meetings")) return;
		
		// Get request data
		$meetingid = phpFrame::getRequest()->get('meetingid', 0);
			
		if ($meetingid != 0) {		
			// Get issue using model
			$meeting = $this->getModel('meetings')->getMeetingsDetail($this->_project->id, $meetingid);
		}
		else {
			$meeting = new stdClass();
			$meeting->access = 1;
		}
		
		// Get view
		$view = $this->getView('meetings', 'form');
		// Set view data
		$view->addData('project', $this->_project);
		$view->addData('row', $meeting);
		// Display view
		$view->display();
	}
	
	public function save_meeting() {
		if (!$this->_authorise("meetings")) return;
		
		// Check for request forgeries
		phpFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		// Get request vars
		$post = phpFrame::getRequest()->getPost();
		
		// Save file using files model
		$modelMeetings = $this->getModel('meetings');
		$row = $modelMeetings->saveMeeting($post);
		if ($row === false){
			$this->_sysevents->setSummary($modelMeetings->getLastError());
		}
		else{
			$this->_sysevents->setSummary(_LANG_MEETING_SAVED, "success");
			
			// Prepare data for activity log entry
			$action = empty($post['id']) ? _LANG_MEETINGS_ACTION_NEW : _LANG_MEETINGS_ACTION_EDIT;
			$title = $row->name;
			$description = sprintf(_LANG_MEETINGS_ACTIVITYLOG_DESCRIPTION, $row->name, $row->dtstart, $row->dtend, $row->description);
			$url = "index.php?component=com_projects&action=get_meeting_detail&projectid=".$row->projectid."&meetingid=".$row->id;
			$notify = $post['notify'] == 'on' ? true : false;
			
			// Add entry in activity log
			$modelActivityLog = $this->getModel('activitylog');
			if (!$modelActivityLog->insertRow($row->projectid, $row->created_by, 'meetings', $action, $title, $description, $url, $post['assignees'], $notify)) {
				$this->_sysevents->setSummary($modelActivityLog->getLastError());
			}	
		}
		
		$this->setRedirect('index.php?component=com_projects&action=get_meeting_detail&projectid='.$post['projectid']."&meetingid=".$row->id);
	}
	
	public function remove_meeting() {
		if (!$this->_authorise("meetings")) return;
		
		$projectid = phpFrame::getRequest()->get('projectid', 0);
		$meetingid = phpFrame::getRequest()->get('meetingid', 0);
		
		$modelMeetings = $this->getModel('meetings');
		
		if ($modelMeetings->deleteMeeting($projectid, $meetingid) === true) {
			$this->_sysevents->setSummary(_LANG_MEETING_DELETE_SUCCESS, "success");
		}
		else {
			$this->_sysevents->setSummary(_LANG_MEETING_DELETE_ERROR);
		}
		
		$this->setRedirect('index.php?component=com_projects&action=get_meetings&projectid='.$projectid);
	}
	
	public function get_slideshow_form() {
		$meetingid = phpFrame::getRequest()->get('meetingid', 0);
		$slideshowid = phpFrame::getRequest()->get('slideshowid', 0);
			
		if ($slideshowid != 0) {		
			// Get issue using model
			$slideshow = $this->getModel('meetings')->getSlideshows($this->_project->id, $meetingid, $slideshowid);
		}
		else {
			$slideshow[0] = new stdClass();
		}
		
		// Get view
		$view = $this->getView('meetings', 'slideshows_form');
		// Set view data
		$view->addData('project', $this->_project);
		$view->addData('row', $slideshow[0]);
		// Display view
		$view->display();
	}
	
	public function save_slideshow() {
		if (!$this->_authorise("meetings")) return;
		
		// Check for request forgeries
		phpFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		// Get request vars
		$post = phpFrame::getRequest()->getPost();
		
		$modelMeetings = $this->getModel('meetings');
		$row = $modelMeetings->saveSlideshow($post);
		
		$redirect_url = 'index.php?component=com_projects&action=get_slideshow_form&projectid='.$post['projectid'].'&meetingid='.$post['meetingid'];
		
		if ($row === false) {
			$this->_sysevents->setSummary($modelMeetings->getLastError());
		}
		else {
			$this->_sysevents->setSummary(_LANG_MEETINGS_SLIDESHOW_SAVE_SUCCESS, "success");
			$redirect_url .= '&slideshowid='.$row->id;
		}
		
		$this->setRedirect($redirect_url);
	}
	
	public function remove_slideshow() {
		if (!$this->_authorise("meetings")) return;
		
		$projectid = phpFrame::getRequest()->get('projectid', 0);
		$meetingid = phpFrame::getRequest()->get('meetingid', 0);
		$slideshowid = phpFrame::getRequest()->get('slideshowid', 0);
		
		$modelMeetings = $this->getModel('meetings');
		
		if (!$modelMeetings->deleteSlideshow($projectid, $slideshowid)) {
			$this->_sysevents->setSummary($modelMeetings->getLastError());
		}
		else {
			$this->_sysevents->setSummary(_LANG_MEETINGS_SLIDESHOW_DELETE_SUCCESS, "success");
		}
		
		$this->setRedirect('index.php?component=com_projects&action=get_meeting_detail&projectid='.$projectid.'&meetingid='.$meetingid);
	}
	
	public function upload_slide() {
		if (!$this->_authorise("meetings")) return;
		
		// Check for request forgeries
		phpFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		// Get request vars
		$post = phpFrame::getRequest()->getPost();
		
		$modelMeetings = $this->getModel('meetings');
		$row = $modelMeetings->uploadSlide($post);
		
		$tmpl = phpFrame::getRequest()->get('tmpl', '');
		if ($row === false) {
			if ($tmpl == 'component') {
				echo '0';
				exit;
			}
			else {
				$this->_sysevents->setSummary($modelMeetings->getLastError());
			}
		}
		else {
			if ($tmpl == 'component') {
				echo $row->id;
				exit;
			}
			else {
				$this->_sysevents->setSummary(_LANG_MEETINGS_SLIDE_UPLOAD_SUCCESS, "success");
			}
		}
		
		$this->setRedirect('index.php?component=com_projects&action=get_meetings&projectid='.$projectid);
	}
	
	public function remove_slide() {
		if (!$this->_authorise("meetings")) return;
		
		$projectid = phpFrame::getRequest()->get('projectid', 0);
		$slideid = phpFrame::getRequest()->get('slideid', 0);
		$meetingid = phpFrame::getRequest()->get('meetingid', 0);
		$slideshowid = phpFrame::getRequest()->get('slideshowid', 0);
		
		$modelMeetings = $this->getModel('meetings');
		
		if (!$modelMeetings->deleteSlide($projectid, $slideid)) {
			$this->_sysevents->setSummary($modelMeetings->getLastError());
		}
		else {
			$this->_sysevents->setSummary(_LANG_MEETINGS_SLIDE_DELETE_SUCCESS, "success");
		}
		
		$this->setRedirect('index.php?component=com_projects&action=get_slideshow_form&projectid='.$projectid.'&meetingid='.$meetingid.'&slideshowid='.$slideshowid);
	}
	
	public function get_meeting_files_form() {
		$meetingid = phpFrame::getRequest()->get('meetingid', 0);
		
		if (!empty($meetingid)) {
			$project_files = $this->getModel('files')->getFiles(new phpFrame_Database_CollectionFilter(), $this->_project->id);
			
			$meeting_files = $this->getModel('meetings')->getFiles($this->_project->id, $meetingid);
			$meeting_files_ids = array();
			for ($i=0; $i<count($meeting_files); $i++) {
				$meeting_files_ids[] = $meeting_files[$i]->id;
			}
		}
		
		// Get view
		$view = $this->getView('meetings', 'files_form');
		// Set view data
		$view->addData('project', $this->_project);
		$view->addData('meetingid', $meetingid);
		$view->addData('project_files', $project_files);
		$view->addData('meeting_files_ids', $meeting_files_ids);
		// Display view
		$view->display();
	}
	
	public function save_meetings_files() {
		if (!$this->_authorise("meetings")) return;
		
		$projectid = phpFrame::getRequest()->get('projectid', 0);
		$meetingid = phpFrame::getRequest()->get('meetingid', 0);
		$fileids = phpFrame::getRequest()->get('fileids', 0);
		
		if (is_array($fileids)) {
			foreach ($fileids as $key=>$value) {
				$fileids_array[] = $key;
			}
		}
		
		$modelMeetings = $this->getModel('meetings');
		
		if (!$modelMeetings->saveFiles($meetingid, $fileids_array)) {
			$this->_sysevents->setSummary($modelMeetings->getLastError());
		}
		else {
			$this->_sysevents->setSummary(_LANG_MEETINGS_FILES_SAVE_SUCCESS, "success");
		}
		
		$this->setRedirect('index.php?component=com_projects&action=get_meeting_detail&projectid='.$projectid.'&meetingid='.$meetingid);
	}
	
	public function get_milestones() {
		if (!$this->_authorise("milestones")) return;
		
		// Get request data
		$orderby = phpFrame::getRequest()->get('orderby', 'm.due_date');
		$orderdir = phpFrame::getRequest()->get('orderdir', 'DESC');
		$limit = phpFrame::getRequest()->get('limit', 25);
		$limitstart = phpFrame::getRequest()->get('limitstart', 0);
		$search = phpFrame::getRequest()->get('search', '');
		
		// Create list filter needed for getIssues()
		$list_filter = new phpFrame_Database_CollectionFilter($orderby, $orderdir, $limit, $limitstart, $search);
		
		// Get milestones using model
		$milestones = $this->getModel('milestones')->getMilestones($list_filter, $this->_project->id);
		
		// Get view
		$view = $this->getView('milestones', 'list');
		// Set view data
		$view->addData('project', $this->_project);
		$view->addData('rows', $milestones);
		$view->addData('page_nav', new phpFrame_HTML_Pagination($list_filter));
		// Display view
		$view->display();
	}
	
	public function get_milestone_detail() {
		if (!$this->_authorise("milestones")) return;
		
		// Get request data
		$milestoneid = phpFrame::getRequest()->get('milestoneid', 0);
		
		// Get milestone using model
		$milestone = $this->getModel('milestones')->getMilestonesDetail($this->_project->id, $milestoneid);
		
		// Get view
		$view = $this->getView('milestones', 'detail');
		// Set view data
		$view->addData('project', $this->_project);
		$view->addData('row', $milestone);
		// Display view
		$view->display();
	}
	
	public function get_milestone_form() {
		if (!$this->_authorise("milestones")) return;
		
		// Get request data
		$milestoneid = phpFrame::getRequest()->get('milestoneid', 0);
			
		if ($milestoneid != 0) {		
			// Get milestone using model
			$milestone = $this->getModel('milestones')->getMilestonesDetail($this->_project->id, $milestoneid);
		}
		else {
			$milestone = new stdClass();
		}
		
		// Get view
		$view = $this->getView('milestones', 'form');
		// Set view data
		$view->addData('project', $this->_project);
		$view->addData('row', $milestone);
		// Display view
		$view->display();
	}
	
	public function save_milestone() {
		if (!$this->_authorise("milestones")) return;
		
		// Check for request forgeries
		phpFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		// Get request vars
		$post = phpFrame::getRequest()->getPost();
		
		// Save file using files model
		$modelMilestones = $this->getModel('milestones');
		$row = $modelMilestones->saveMilestone($post);
		if ($row === false) {
			$this->_sysevents->setSummary($modelMilestones->getLastError());
		}
		else {
			$this->_sysevents->setSummary(_LANG_MILESTONE_SAVED, "success");
			
			// Prepare data for activity log entry
			$action = empty($post['id']) ? _LANG_MILESTONES_ACTION_NEW : _LANG_MILESTONES_ACTION_EDIT;
			$title = $row->title;
			$description = sprintf(_LANG_MILESTONES_ACTIVITYLOG_DESCRIPTION, $row->title, $row->due_date, $row->description);
			$url = "index.php?component=com_projects&action=get_milestone_detail&projectid=".$row->projectid."&milestoneid=".$row->id;
			$notify = $post['notify'] == 'on' ? true : false;
			
			// Add entry in activity log
			$modelActivityLog = $this->getModel('activitylog');
			if (!$modelActivityLog->insertRow($row->projectid, $row->created_by, 'milestones', $action, $title, $description, $url, $post['assignees'], $notify)) {
				$this->_sysevents->setSummary($modelActivityLog->getLastError());
			}
		}
		
		$this->setRedirect('index.php?component=com_projects&action=get_milestones&projectid='.$post['projectid']);
	}
	
	public function remove_milestone() {
		if (!$this->_authorise("milestones")) return;
		
		$projectid = phpFrame::getRequest()->get('projectid', 0);
		$milestoneid = phpFrame::getRequest()->get('milestoneid', 0);
		
		$modelMilestones = $this->getModel('milestones');
		
		if ($modelMilestones->deleteMilestone($projectid, $milestoneid) === true) {
			$this->_sysevents->setSummary(_LANG_MILESTONE_DELETE_SUCCESS, "success");
		}
		else {
			$this->_sysevents->setSummary(_LANG_MILESTONE_DELETE_ERROR);
		}
		
		$this->setRedirect('index.php?component=com_projects&action=get_milestones&projectid='.$projectid);
	}
	
	public function get_assignees_form() {
		// Get request vars
		$tool = phpFrame::getRequest()->get('tool', '');
		$itemid = phpFrame::getRequest()->get('itemid', 0);
		
		// Get model depending on selected tool
		$assignees = $this->getModel($tool)->getAssignees($itemid, false);
		$members = $this->getModel('members')->getMembers($this->_project->id);
		
		foreach ($members as $member) {
			if (is_array($assignees) && in_array($member->userid, $assignees)) {
				$selected_users[] = $member;
			}
			else {
				$unselected_users[] = $member;
			}
		}
		
		// Get view
		$view = $this->getView('assignees', '');
		// Set view data
		$view->addData('project', $this->_project);
		$view->addData('selected_users', $selected_users);
		$view->addData('unselected_users', $unselected_users);
		// Display view
		$view->display();
	}
	
	public function  save_assignees() {
	
	}
	
	public function remove_activitylog() {
		// Get request vars
		$id = phpFrame::getRequest()->get('id', 0);
		
		// Get row before we remove
		//$log = $this->getModel('activitylog')->
		echo 'i have to remove an activitylog';
	}
	
	public function process_incoming_email() {
		// Get models
		$modelComments = $this->getModel('comments');
		
		// Get mail messages with project comments
		$messages = $modelComments->fetchCommentsFromEmail();
		
		if (is_array($messages) && count($messages) > 0) {
			foreach ($messages as $message) {
				// Check whether the email address belongs to a project member
				if (!empty($message->data['p']) && !empty($message->data['fromaddress'])) {
					// Set the project id
					phpFrame::getRequest()->set('projectid', $message->data['p']);
					$projectid = $message->data['p'];
					
					// Get userid using email
					$userid = phpFrame_User_Helper::email2id($message->data['fromaddress']);
					
					// Load the project data
					$modelProjects = $this->getModel('projects');
					$this->_project = $modelProjects->getRow($projectid);
					
					$modelMembers = $this->getModel('members');
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
							$this->_sysevents->setSummary($modelComments->getLastError());
						}
						else {
							$this->_sysevents->setSummary(_LANG_COMMENT_SAVED, "success");
							
							// Prepare data for activity log entry
							$action = _LANG_COMMENTS_ACTION_NEW;
							$title = 'RE: '.$modelComments->itemid2title($row->itemid, $row->type);
							$description = sprintf(_LANG_COMMENTS_ACTIVITYLOG_DESCRIPTION, $title, $row->body);
							switch ($row->type) {
								case 'files' : 
									$url = "index.php?component=com_projects&action=get_file_detail&projectid=".$row->projectid."&fileid=".$row->itemid;
									break;
								case 'issues' : 
									$url = "index.php?component=com_projects&action=get_issue_detail&projectid=".$row->projectid."&issueid=".$row->itemid;
									break;
								case 'meetings' : 
									$url = "index.php?component=com_projects&action=get_meeting_detail&projectid=".$row->projectid."&meetingid=".$row->itemid;
									break;
								case 'messages' : 
									$url = "index.php?component=com_projects&action=get_message_detail&projectid=".$row->projectid."&messageid=".$row->itemid;
									break;
								case 'milestones' : 
									$url = "index.php?component=com_projects&action=get_milestone_detail&projectid=".$row->projectid."&milestoneid=".$row->itemid;
									break;
							}
							
							// Get assignees
							$itemModel = $this->getModel($row->type);
							$assignees = $itemModel->getAssignees($row->itemid, false);
							
							// Add entry in activity log
							$modelActivityLog = $this->getModel('activitylog');
							$modelActivityLog->project =& $this->_project;
							$delete_uids = array();
							if (!$modelActivityLog->insertRow($row->projectid, $row->userid, 'comments', $action, $title, $description, $url, $assignees, true)) {
								$this->_sysevents->setSummary($modelActivityLog->getLastError());
							}
							else {
								$delete_uids[] = $message->uid;
							}
						}
						
						// Delete message from mailbox
						$imap = new phpFrame_Mail_IMAP(config::IMAP_HOST, config::IMAP_PORT, config::IMAP_USER, config::IMAP_PASSWORD);
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
	
	private function _authorise($tool) {
		
		if (is_object($this->_project)) { 
			if (!$this->_permissions->authorise($tool, phpFrame::getSession()->getUserId(), $this->_project)) {
				$this->_sysevents->setSummary("Permission denied");
				return false;
			}
		}
		
		return true;
	}
}
