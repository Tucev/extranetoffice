<?php
/**
 * src/components/com_projects/models/members.php
 * 
 * PHP version 5
 * 
 * @category   Project_Management
 * @package    ExtranetOffice
 * @subpackage com_projects
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @copyright  2009 E-noise.com Limited
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/extranetoffice/source/browse
 */

/**
 * projectsModelMembers Class
 * 
 * @category   Project_Management
 * @package    ExtranetOffice
 * @subpackage com_projects
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://code.google.com/p/extranetoffice/source/browse
 * @since      1.0
 */
class projectsModelMembers extends PHPFrame_MVC_Model
{
    /**
     * A reference to the project this activity log belongs to
     * 
     * @var object
     */
    private $_project=null;
    
    /**
     * Constructor
     * 
     * @param PHPFrame_Database_Row $project The project row this activity 
     *                                       log belongs to.
     *
     * @access public
     * @return void
     * @since  1.0
     */
    public function __construct(PHPFrame_Database_Row $project)
    {
        $this->_project = $project;
    }
    
    /**
     * Get project members as a row collection
     * 
     * @access public
     * @return PHPFrame_Database_RowCollection
     * @since  1.0
     */
    public function getCollection()
    {
        $rows = new PHPFrame_Database_RowCollection();
        $rows->select(array("ur.id AS id",
                            "ur.userid", 
                            "ur.roleid", 
                            "r.name AS rolename", 
                            "u.username", 
                            "u.firstname AS firstname", 
                            "u.lastname AS lastname", 
                            "u.email",
                            "u.photo"))
             ->from("#__users_roles AS ur")
             ->join("JOIN #__users u ON u.id = ur.userid")
             ->join("JOIN #__roles r ON r.id = ur.roleid")
             ->where("u.deleted = '0000-00-00 00:00:00'", "OR", "u.deleted IS NULL")
             ->where("ur.projectid", "=", ":projectid")
             ->params(":projectid", $this->_project->id)
             ->orderby("ur.roleid", "ASC");
        
        $rows->load();
        
        // Process row data before returning
        foreach ($rows as $row) {
            // Translate photo field to valid URL for frontend
            $photo = $row->photo;
            $photo_url = config::UPLOAD_DIR.'/users/';
            $photo_url .= !empty($photo) ? $photo : 'default.png';
            $row->photo = $photo_url;
            
            // Add url to detail page
            $detail_url = "index.php?component=com_users&action=get_user";
            $detail_url .= "&userid=".$row->id;
            $detail_url = PHPFrame_Utils_Rewrite::rewriteURL($detail_url);
            $row->detail_url = $detail_url;
        }
        
        return $rows;
    }
    
    /**
     * Get a single project member as a row
     * 
     * @param int $userid
     * 
     * @access public
     * @return PHPFrame_Database_Row
     * @since  1.0
     */
    public function getMember($userid)
    {
        $id_obj = new PHPFrame_Database_IdObject();
        $id_obj->select(array("ur.*", 
                              "r.name AS rolename", 
                              "u.username", 
                              "u.firstname AS firstname", 
                              "u.lastname AS lastname", 
                              "u.email", 
                              "u.photo"))
               ->from("#__users_roles AS ur")
               ->join("JOIN #__users u ON u.id = ur.userid")
               ->join("JOIN #__roles r ON r.id = ur.roleid")
               ->where("u.deleted = '0000-00-00 00:00:00'", "OR", "u.deleted IS NULL")
               ->where("ur.projectid", "=", ":projectid")
               ->params(":projectid", $this->_project->id)
               ->where("ur.userid", "=", ":userid")
               ->params(":userid", $userid);
       
        $row = new PHPFrame_Database_Row("#__users_roles");
        
        // Add array with foreign field names to allow as row columns
        $foreign_fields = array("rolename","username","firstname","lastname","email","photo");
        $row->load($id_obj, null, $foreign_fields);
        
        return $row;
    }
    
    public function saveMember($projectid, $userid, $roleid, $notify=true)
    {
        // Create new row object
        $row = new PHPFrame_Database_Row("#__users_roles");
        
        // Load existing entry before we overwrite with new values
        $id_obj = new PHPFrame_Database_IdObject();
        $id_obj->select("*")
               ->from("#__users_roles")
               ->where("projectid", "=", ":projectid")
               ->where("userid", "=", ":userid")
               ->params(":projectid", $projectid)
               ->params(":userid", $userid);
               
        $row->load($id_obj);
        
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
    
    /**
     * Invite new user to the system and make them a project member
     * 
     * @param array $post
     * @param int   $projectid
     * @param int   $roleid
     * 
     * @access public
     * @return unknown_type
     * @since  1.0
     */
    public function inviteNewUser($post, $projectid, $roleid)
    {
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
        
        $user->store();
        
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
     * @param int $userid The userid of the user to remove as member of the 
     *                    current project.
     * 
     * @access public
     * @return bool   Returns TRUE on success or FALSE on failure.
     * @since  1.0
     */
    public function deleteMember($userid)
    {
        $sql = "DELETE FROM #__users_roles ";
        $sql .= " WHERE projectid = :projectid AND userid = :userid";
        
        $params = array(":projectid"=>$this->_project->id, 
                        ":userid"=>$userid);
        
        $stmt = PHPFrame::DB()->prepare($sql);
        $stmt->execute($params);
        
        $error_info = $stmt->errorInfo();
        if (is_array($error_info) && count($error_info) > 1) {
            $this->_error[] = $error_info[2];
            return false;
        }
        
        return true;
    }
    
    /**
     * Change member's role in given project
     * 
     * @param $userid
     * @param $roleid
     * 
     * @access public
     * @return bool   Returns TRUE on success or FALSE on failure
     * @since  1.0
     */
    public function changeMemberRole($userid, $roleid)
    {
        $query = "UPDATE #__users_roles ";
        $query .= " SET roleid = :roleid";
        $query .= " WHERE projectid = :projectid AND userid = :userid";
        
        $params = array(":roleid"=>$roleid, 
                        ":projectid"=>$this->_project->id, 
                        ":userid"=>$userid);
        
        $stmt = PHPFrame::DB()->prepare($query);
        $stmt->execute($params);
        
        $error_info = $stmt->errorInfo();
        if (is_array($error_info) && count($error_info) > 1) {
            $this->_error[] = $error_info[2];
            return false;
        }
        
        return true;
    }
    
    public function isMember($projectid, $userid)
    {
        $sql = "SELECT roleid FROM #__users_roles ";
        $sql .= " WHERE projectid = :projectid AND userid = :userid";
        
        $params = array(":projectid"=>$projectid, ":userid"=>$userid);
        
        $roleid = PHPFrame::DB()->fetchColumn($sql, $params);
        
        if (!empty($roleid)) {
            return $roleid;
        } else {
            return false;
        }
    }
}
