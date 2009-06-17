<?php
/**
 * @version     $Id$
 * @package        ExtranetOffice
 * @subpackage    com_projects
 * @copyright    Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license        BSD revised. See LICENSE.
 */

/**
 * projectsModelMembers Class
 * 
 * @package        ExtranetOffice
 * @subpackage     com_projects
 * @author         Luis Montero [e-noise.com]
 * @since         1.0
 * @see         PHPFrame_MVC_Model
 */
class projectsModelMembers extends PHPFrame_MVC_Model {
    /**
     * Constructor
     *
     * @since    1.0
     */
    function __construct() {}
    
    function getMembers($projectid, $userid=0) {
        $query = "SELECT ur.userid, ur.roleid, r.name AS rolename";
        $query .= ", u.id AS id, u.username, u.firstname AS firstname, u.lastname AS lastname, u.email, u.photo ";
        $query .= " FROM #__users_roles AS ur, #__users AS u, #__roles AS r ";
        $query .= " WHERE u.id = ur.userid AND r.id = ur.roleid AND ur.projectid = ".$projectid;
        $query .= " AND (u.deleted = '0000-00-00 00:00:00' OR u.deleted IS NULL)";
        if (!empty($userid)) $query .= " AND ur.userid = ".$userid;
        $query .= " ORDER BY ur.roleid ASC";
        
        //echo str_replace('#__', 'eo_', $query); exit;
        
        return PHPFrame::DB()->loadObjectList($query);
    }
    
    function saveMember($projectid, $userid, $roleid, $notify=true) {
        // Create new row object
        $row = new PHPFrame_Database_Row("#__users_roles");
        
        // Load existing entry before we overwrite with new values
        $query = "SELECT * FROM #__users_roles WHERE projectid = ".$projectid." AND userid = ".$userid;
        $row->loadByQuery($query);
        
        $row->set('userid', $userid);
        $row->set('projectid', $projectid);
        $row->set('roleid', $roleid);
        
        $row->store();
        
        // Send notification via email
        if ($notify) {
            $project_name = projectsHelperProjects::id2name($projectid);
            $role_name = projectsHelperProjects::project_roleid2name($roleid);
            $site_name = config::SITENAME;
            $uri = new PHPFrame_Utils_URI();
            $new_member_email = PHPFrame_User_Helper::id2email($userid);
            
            $new_mail = new PHPFrame_Mail_Mailer();
            $new_mail->AddAddress($new_member_email, PHPFrame_User_Helper::id2name($userid));
            $new_mail->Subject = sprintf(_LANG_PROJECTS_INVITATION_SUBJECT, PHPFrame::Session()->getUser()->firstname." ".PHPFrame::Session()->getUser()->lastname, $project_name, $site_name);
            $new_mail->Body = PHPFrame_Base_String::html(sprintf(_LANG_PROJECTS_INVITATION_BODY,
                                     PHPFrame::Session()->getUser()->firstname." ".PHPFrame::Session()->getUser()->lastname, 
                                     $project_name, 
                                     $role_name, 
                                     $uri->getBase())
                              );
                                         
            if ($new_mail->Send() !== true) {
                $this->_error[] = sprintf(_LANG_EMAIL_NOT_SENT, $new_member_email);
                return false;
            }    
        }
        
        return true;
    }
    
    function inviteNewUser($post, $projectid, $roleid) {
        // Create new user object
        $user = new PHPFrame_User();
        
        $user->set('block', '0');
        $user->set('created', date("Y-m-d H:i:s"));
        // Generate random password and store in local variable to be used when sending email to user.
        $password = PHPFrame_Utils_Crypt::genRandomPassword();
        // Assign newly generated password to row object (this password will be encrypted when stored).
        $user->set('password', $password);
        
        // Bind the post data to the row array
        $user->bind($post, 'password');
        
        if (!$user->store()) {
            $this->_error[] = $user->getLastError();
            return false;
        }
        
        // add user to project
        $row = new PHPFrame_Database_Row("#__users_roles");
        
        $row->set('userid', $user->id);
        $row->set('projectid', $projectid);
        $row->set('roleid', $roleid);
        
        $row->store();
        
        // Send notification to new users
        $project_name = projectsHelperProjects::id2name($projectid);
        $role_name = projectsHelperProjects::project_roleid2name($roleid);
        $site_name = config::SITENAME;
        $uri = new PHPFrame_Utils_URI();
        
        $new_mail = new PHPFrame_Mail_Mailer();
        $new_mail->AddAddress($user->email, PHPFrame_User_Helper::fullname_format($user->firstname, $user->lastname));
        $new_mail->Subject = sprintf(_LANG_PROJECTS_INVITATION_SUBJECT, PHPFrame::Session()->getUser()->firstname." ".PHPFrame::Session()->getUser()->lastname, $project_name, $site_name);
        $new_mail->Body = sprintf(_LANG_PROJECTS_INVITATION_NEW_USER_BODY, 
                                 PHPFrame::Session()->getUser()->firstname." ".PHPFrame::Session()->getUser()->lastname,
                                 $project_name, 
                                 $role_name, 
                                 $user->username, 
                                 $password,
                                 $uri->getBase()
                        );
                                   
        if ($new_mail->Send() !== true) {
            $this->_error[] = sprintf(_LANG_EMAIL_NOT_SENT, $user->email);
            return false;
        }
        
        return true;
    }
    
    /**
     * Delete project member
     * 
     * @param    int        $projectid
     * @param    int        $userid
     * @return    bool    Returns TRUE on success or FALSE on failure.
     */
    function deleteMember($projectid, $userid) {
        $query = "DELETE FROM #__users_roles WHERE projectid = ".$projectid." AND userid = ".$userid;
        if (PHPFrame::DB()->query($query) === false) {
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
        if (PHPFrame::DB()->query($query) === false) {
            $this->_error[] = PHPFrame::DB()->getLastError();
            return false;
        }
        else {
            return true;
        }
    }
    
    function isMember($projectid, $userid) {
        $query = "SELECT roleid FROM #__users_roles WHERE projectid = ".$projectid." AND userid = ".$userid;
        $roleid = PHPFrame::DB()->loadResult($query);
        if (!empty($roleid)) {
            return $roleid;
        }
        else {
            return false;
        }
    }
}
