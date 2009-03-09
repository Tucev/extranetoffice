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
 * projectsModelMembers Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		model
 */
class projectsModelMembers extends model {
	/**
	 * Constructor
	 *
	 * @since	1.0
	 */
	function __construct() {
		//TODO: Check permissions
		parent::__construct();
	}
	
	function getMembers($projectid, $userid=0) {
		$query = "SELECT ur.userid, ur.roleid, r.name AS rolename, u.username, CONCAT(firstname, ' ', lastname) AS name, u.email ";
		$query .= " FROM #__users_roles AS ur, #__users AS u, #__roles AS r ";
		$query .= " WHERE u.id = ur.userid AND r.id = ur.roleid AND ur.projectid = ".$projectid;
		if (!empty($userid)) $query .= " AND ur.userid = ".$userid;
		$query .= " ORDER BY ur.roleid ASC";
		
		//echo str_replace('#__', 'eo_', $query); exit;

		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}
	
	function saveMember($projectid, $userid, $roleid) {
		// Instantiate table object
		require_once COMPONENT_PATH.DS.'tables'.DS.'users_roles.table.php';
		$row =& phpFrame::getInstance('projectsTableUsersRoles');
		
		// Load existing entry before we overwrite with new values
		$row->load($userid, $projectid);
		
		$row->userid = $userid;
		$row->projectid = $projectid;
		$row->roleid = $roleid;
		
		if (!$row->check()) {
			$this->error[] = $row->getLastError();
			return false;
		}
		
		if (!$row->store()) {
			$this->error[] = $row->getLastError();
			return false;
		}
		
		// Send invitation via email
		$project_name = projectsHelperProjects::id2name($projectid);
		$role_name = projectsHelperProjects::project_roleid2name($roleid);
		$site_name = $this->config->sitename;
		$uri =& factory::getURI();
		$new_member_email = usersHelper::id2email($userid);
		
		$new_mail = new mail();
		$new_mail->AddAddress($new_member_email, usersHelper::id2name($userid));
		$new_mail->Subject = sprintf(_LANG_PROJECTS_INVITATION_SUBJECT, $this->user->get('name'), $project_name, $site_name);
		$new_mail->Body = text::_(sprintf(_LANG_PROJECTS_INVITATION_BODY,
								 $this->user->get('name'), 
								 $project_name, 
								 $role_name, 
								 $uri->getBase())
						  );
						  		   
		if ($new_mail->Send() !== true) {
			$this->error[] = sprintf(_LANG_EMAIL_NOT_SENT, $new_member_email);
			return false;
		}
		
		return true;
	}
	
	function inviteNewUser($projectid, $roleid) {
		// Get reference to user object
		$user =& factory::getUser();
		
		// Create standard object to store user properties
		// We do this because we dont want to overwrite the current user object.
		// Remember the user object extends table, which in turn extends singleton.
		$row = new standardObject();
		
		$row->block = '0';
		$row->created = date("Y-m-d H:i:s");
		// Generate random password and store in local variable to be used when sending email to user.
		$password = crypt::genRandomPassword();
		// Assign newly generated password to row object (this password will be encrypted when stored).
		$row->password = $password;
		
		$post = request::get('post');
		
		// Bind the post data to the row array
		if ($user->bind($post, 'password', $row) === false) {
			$this->error[] = $user->getLastError();
			return false;
		}
		
		if (!$user->check($row)) {
			$this->error[] = $user->getLastError();
			return false;
		}
		
		if (!$user->store($row)) {
			$this->error[] = $user->getLastError();
			return false;
		}
		
		// add user to project
		require_once COMPONENT_PATH.DS.'tables'.DS.'users_roles.table.php';
		$row_users_roles =& phpFrame::getInstance('projectsTableUsersRoles');
		
		$row_users_roles->userid = $row->id;
		$row_users_roles->projectid = $projectid;
		$row_users_roles->roleid = $roleid;
		
		if (!$row_users_roles->check()) {
			$this->error[] = $row_users_roles->getLastError();
			return false;
		}
		
		if (!$row_users_roles->store()) {
			$this->error[] = $row_users_roles->getLastError();
			return false;
		}
		
		// Send notification to new users
		$project_name = projectsHelperProjects::id2name($projectid);
		$role_name = projectsHelperProjects::project_roleid2name($roleid);
		$site_name = $this->config->sitename;
		$uri =& factory::getURI();
		
		$new_mail = new mail();
		$new_mail->AddAddress($row->email, usersHelper::fullname_format($row->firstname, $row->lastname));
		$new_mail->Subject = sprintf(_LANG_PROJECTS_INVITATION_SUBJECT, $this->user->get('name'), $project_name, $site_name);
		$new_mail->Body = sprintf(_LANG_PROJECTS_INVITATION_NEW_USER_BODY, 
								 $this->user->get('name'),
								 $project_name, 
								 $role_name, 
								 $row->username, 
								 $password,
								 $uri->getBase()
						);
								   
		if ($new_mail->Send() !== true) {
			$this->error[] = sprintf(_LANG_EMAIL_NOT_SENT, $row->email);
			return false;
		}
		
		return true;
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