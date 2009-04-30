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
 * projectsModelMembers Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		phpFrame_Application_Model
 */
class projectsModelMembers extends phpFrame_Application_Model {
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
		$query = "SELECT ur.userid, ur.roleid, r.name AS rolename";
		$query .= ", u.id AS id, u.username, CONCAT(u.firstname, ' ', u.lastname) AS name, u.email, u.photo ";
		$query .= " FROM #__users_roles AS ur, #__users AS u, #__roles AS r ";
		$query .= " WHERE u.id = ur.userid AND r.id = ur.roleid AND ur.projectid = ".$projectid;
		$query .= " AND (u.deleted = '0000-00-00 00:00:00' OR u.deleted IS NULL)";
		if (!empty($userid)) $query .= " AND ur.userid = ".$userid;
		$query .= " ORDER BY ur.roleid ASC";
		
		//echo str_replace('#__', 'eo_', $query); exit;

		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}
	
	function saveMember($projectid, $userid, $roleid, $notify=true) {
		// Instantiate table object
		$row =& phpFrame_Base_Singleton::getInstance('projectsTableUsersRoles');
		
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
		
		// Send notification via email
		if ($notify) {
			$project_name = projectsHelperProjects::id2name($projectid);
			$role_name = projectsHelperProjects::project_roleid2name($roleid);
			$site_name = config::SITENAME;
			$uri = phpFrame::getURI();
			$new_member_email = phpFrame_User_Helper::id2email($userid);
			
			$new_mail = new phpFrame_Mail_Mailer();
			$new_mail->AddAddress($new_member_email, phpFrame_User_Helper::id2name($userid));
			$new_mail->Subject = sprintf(_LANG_PROJECTS_INVITATION_SUBJECT, $this->user->get('name'), $project_name, $site_name);
			$new_mail->Body = phpFrame_HTML_Text::_(sprintf(_LANG_PROJECTS_INVITATION_BODY,
									 $this->user->get('name'), 
									 $project_name, 
									 $role_name, 
									 $uri->getBase())
							  );
							  		   
			if ($new_mail->Send() !== true) {
				$this->error[] = sprintf(_LANG_EMAIL_NOT_SENT, $new_member_email);
				return false;
			}	
		}
		
		return true;
	}
	
	function inviteNewUser($projectid, $roleid) {
		// Get user object
		$user = phpFrame::getUser();
		
		// Create standard object to store user properties
		// We do this because we dont want to overwrite the current user object.
		// Remember the user object extends phpFrame_Database_Table, which in turn extends phpFrame_Base_Singleton.
		$row = new phpFrame_Base_StdObject();
		
		$row->block = '0';
		$row->created = date("Y-m-d H:i:s");
		// Generate random password and store in local variable to be used when sending email to user.
		$password = phpFrame_Utils_Crypt::genRandomPassword();
		// Assign newly generated password to row object (this password will be encrypted when stored).
		$row->password = $password;
		
		$post = phpFrame_Environment_Request::getPost();
		
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
		$row_users_roles = phpFrame_Base_Singleton::getInstance('projectsTableUsersRoles');
		
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
		$site_name = config::SITENAME;
		$uri = phpFrame::getURI();
		
		$new_mail = new phpFrame_Mail_Mailer();
		$new_mail->AddAddress($row->email, phpFrame_User_Helper::fullname_format($row->firstname, $row->lastname));
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
	
	/**
	 * Delete project member
	 * 
	 * @param	int		$projectid
	 * @param	int		$userid
	 * @return	bool	Returns TRUE on success or FALSE on failure.
	 */
	function deleteMember($projectid, $userid) {
		$query = "DELETE FROM #__users_roles WHERE projectid = ".$projectid." AND userid = ".$userid;
		$this->db->setQuery($query);
		if ($this->db->query() === false) {
			return false;
		}
		else {
			return true;
		}
	}
	
	function changeMemberRole($projectid, $userid, $roleid) {
		$query = "UPDATE #__users_roles ";
		$query .= " SET roleid = ".$roleid;
		$query .= " WHERE projectid = ".$projectid." AND userid = ".$userid;
		$this->db->setQuery($query);
		if ($this->db->query() === false) {
			$this->error[] = $this->db->getLastError();
			return false;
		}
		else {
			return true;
		}
	}
	
	function isMember($projectid, $userid) {
		$query = "SELECT roleid FROM #__users_roles WHERE projectid = ".$projectid." AND userid = ".$userid;
		$this->db->setQuery($query);
		$roleid = $this->db->loadResult();
		if (!empty($roleid)) {
			return $roleid;
		}
		else {
			return false;
		}
	}
}
?>