<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice.Projects
 * @subpackage	models
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * projectsModelProjects Class
 * 
 * @package		ExtranetOffice.Projects
 * @subpackage 	models
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		model
 */
class projectsModelProjects extends model {
	var $config=null;
	var $user=null;
	var $db=null;
	var $view=null;
	var $layout=null;
	var $projectid=null;
	
	/**
	 * Constructor
	 *
	 * @since 1.0
	 */
	function __construct() {
		$this->init();
		
		parent::__construct();
	}
	
	function init() {
		$this->config =& factory::getConfig();
		$this->user =& factory::getUser();
		$this->db =& factory::getDB();
		
		$this->view = request::getVar('view', 'projects');
		$this->layout = request::getVar('layout', 'list');
		$this->projectid = request::getVar('projectid', 0);
		
		// Check whether project directories exists and are writable
		$projects_upload_dir = _ABS_PATH.DS.$this->config->upload_dir.DS."projects";
		//TODO: Have to catch errors here and look at file permissions
		if (!is_dir($projects_upload_dir)) {
			mkdir($projects_upload_dir, 0771);
		}
		$projects_filesystem = $this->config->filesystem.DS."projects";
		if (!is_dir($projects_filesystem)) {
			mkdir($projects_filesystem, 0771);
		}
			
		// Check whether project specific directories exists and are writable
		if (!empty($this->projectid)) {
			//TODO: Have to catch errors here and look at file permissions
			$project_specific_upload_dir = _ABS_PATH.DS.$this->config->upload_dir.DS."projects".DS.$this->projectid;
			if (!is_dir($project_specific_upload_dir)) {
				mkdir($project_specific_upload_dir, 0771);
			}
			$project_specific_filesystem = $this->config->filesystem.DS."projects".DS.$this->projectid;
			if (!is_dir($project_specific_filesystem)) {
				mkdir($project_specific_filesystem, 0771);
			}
		}
	}
	
	function getProjects($projectid=0) {
		// Only apply filtering and ordering if browsing projects list
		if (empty($projectid)) {
			$filter_order = request::getVar('filter_order', 'p.name');
			$filter_order_Dir = request::getVar('filter_order_Dir', '');
			$search = request::getVar('search', '');
			$search = strtolower( $search );
			$limitstart = request::getVar('limitstart', 0);
			$limit = request::getVar('limit', 20);
		}
		else {
			$limitstart = request::getVar('limitstart', 0);
			$limit = request::getVar('limit', 1);
		}

		$where = array();
		
		// Show only public projects or projects where user has an assigned role
		$where[] = "( p.access = '0' OR (".$this->user->id." IN (SELECT userid FROM #__users_roles WHERE projectid = p.id) ) )";
		
		if ($search) {
			$where[] = "p.name LIKE '%".$this->db->getEscaped($search)."%'";
		}
		
		if (!empty($projectid)) {
			$where[] = "p.id = ".$projectid;	
		}

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		
		if (empty($filter_order)) {
			$orderby = ' ORDER BY p.name ';
		} else {
			$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', p.name ';
		}
		
		// get the total number of records
		$query = "SELECT 
				  p.id
				  FROM #__projects AS p 
				  JOIN #__users u ON u.id = p.created_by 
				  LEFT JOIN #__project_types pt ON pt.id = p.project_type  
				  LEFT JOIN #__users_roles ur ON p.id = ur.projectid "
				  . $where . 
				  " GROUP BY p.id ";
				  
		$this->db->setQuery($query);
		$this->db->query();
		$total = $this->db->getNumRows();
		
		$pageNav = new pagination($total, $limitstart, $limit);
		
		// get the subset (based on limits) of required records
		$query = "SELECT 
				  p.*, 
				  u.username AS created_by_name, 
				  pt.name AS project_type_name, 
				  GROUP_CONCAT(ur.userid) members
				  FROM #__projects AS p 
				  JOIN #__users u ON u.id = p.created_by 
				  LEFT JOIN #__project_types pt ON pt.id = p.project_type  
				  LEFT JOIN #__users_roles ur ON p.id = ur.projectid "
				  . $where . 
				  " GROUP BY p.id ";
		$query .= $orderby." LIMIT ".$pageNav->limitstart.", ".$pageNav->limit;
		//echo str_replace('#__', 'eo_', $query); exit;
		$this->db->setQuery($query);
		$rows = $this->db->loadObjectList();
		
		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// search filter
		$lists['search'] = $search;
		
		// pack data into an array to return
		$return['rows'] = $rows;
		$return['pageNav'] = $pageNav;
		$return['lists'] = $lists;
		
		return $return;
	}

	function saveProject() {
		$row = new iOfficeTableProjects();
		
		$post = request::get( 'post' );
		if (!$row->bind( $post )) {
			JError::raiseError(500, $row->getError() );
		}
		
		if (empty($row->id)) {
			$row->created = date("Y-m-d H:i:s");
			$row->created_by = $this->user->id;
			$new_project = true;
		}
		
		if (!$row->check()) {
			JError::raiseError(500, $row->getError() );
		}
	
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
		}
		
		// Add role for user in the new project
		if ($new_project === true) {
			$this->saveMember($row->id, $this->user->id, '1');	
		}
		
		return $row->id;
	}
	
	function deleteProject($projectid) {
		//TODO: Before deleting the project we need to delete all its tracker items, lists, files, ...
		// Instantiate table object
		$row = new iOfficeTableProjects();
		// Delete row from database
		if (!$row->delete($projectid)) {
			JError::raiseError(500, $row->getError() );
		}
	}
	
	function getMembers($projectid, $userid=0) {
		$query = "SELECT ur.userid, ur.roleid, r.name AS rolename, u.username, u.name, u.email ";
		$query .= " FROM #__users_roles AS ur, #__users AS u, #__intranetoffice_roles AS r ";
		$query .= " WHERE u.id = ur.userid AND r.id = ur.roleid AND ur.projectid = ".$projectid;
		if (!empty($userid)) $query .= " AND ur.userid = ".$userid;
		$query .= " ORDER BY ur.roleid ASC";
		
		//echo $query; exit;

		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}
	
	function saveMember($projectid, $userid, $roleid) {
		$row = new iOfficeTableUsersRoles();
		$row->load($userid, $projectid);
		
		$row->userid = $userid;
		$row->projectid = $projectid;
		$row->roleid = $roleid;
			
		if (!$row->check()) {
			JError::raiseError(500, $row->getError() );
		}
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
		}
		
		// Send invitation via email
		jimport( 'joomla.mail.mail' );
		jimport( 'joomla.mail.helper' );
		$new_mail = new JMail();
			
		$sender = $this->iOfficeConfig->get('notifications_fromaddress');
		$recipient = iOfficeHelperUsers::id2email($userid);
		$project_name = iOfficeHelperProjects::id2name($projectid);
		$role_name = iOfficeHelperProjects::project_roleid2name($roleid);
		$joomla_config = new JConfig();
		$site_name = $joomla_config->sitename;
		$site_url = JURI::Base();
			
		$subject = sprintf(_INTRANETOFFICE_ADMIN_INVITATION_SUBJECT, $this->user->get('name'), $project_name, $site_name);
		$body = JText::_(sprintf(_INTRANETOFFICE_ADMIN_INVITATION_BODY,
								 $this->user->get('name'), 
								 $project_name, 
								 $role_name, 
								 $site_url)
						);

		$recipient = trim($recipient);
		if (!JMailHelper::isEmailAddress($recipient)) {
			$error	= JText::sprintf('EMAIL_INVALID', $recipient);
			JError::raiseWarning(0, $error );
		}
		else {
			$new_mail->addRecipient($recipient);	
		}
	
		if ($error)	{
			return false;
		}
		
		// Clean the email data
		$sender = JMailHelper::cleanAddress($sender);
		$subject = JMailHelper::cleanSubject($subject);
		$body = JMailHelper::cleanBody($body);
		$new_mail->addReplyTo(array($this->user->get('email'), $this->user->get('name')));
		$new_mail->setSender($sender);
		$new_mail->FromName = $this->iOfficeConfig->get('notifications_fromname');
		$new_mail->setSubject($subject);
		$new_mail->setBody($body);
		$new_mail->useSMTP($this->iOfficeConfig->get('notifications_smtpauth'), 
						   $this->iOfficeConfig->get('notifications_smtphost'), 
						   $this->iOfficeConfig->get('notifications_smtpusername'), 
						   $this->iOfficeConfig->get('notifications_smtppassword'));
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
	
	function deleteMember($projectid, $userid) {
		$query = "DELETE FROM #__users_roles WHERE projectid = ".$projectid." AND userid = ".$userid;
		$this->db->setQuery($query);
		$this->db->query();
	}
	
	function changeMemberRole($projectid, $userid, $roleid) {
		$query = "UPDATE #__users_roles ";
		$query .= " SET roleid = ".$roleid;
		$query .= " WHERE projectid = ".$projectid." AND userid = ".$userid;
		$this->db->setQuery($query);
		$this->db->query();
	}
}
?>