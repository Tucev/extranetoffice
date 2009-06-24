<?php
/**
 * src/components/com_admin/models/users.php
 * 
 * PHP version 5
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_admin
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @copyright  2009 E-noise.com Limited
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 */

/**
 * adminModelUsers Class
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_admin
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 * @see        PHPFrame_MVC_Model
 * @since      1.0
 */
class adminModelUsers extends PHPFrame_MVC_Model
{
    /**
     * Get users
     * 
     * This method returns an array with row objects for each user
     * 
     * @param string $orderby
     * @param string $orderdir
     * @param int    $limit
     * @param int    $limitstart
     * @param string $search
     * @param bool   $deleted Indicates whether we want to include deleted users
     * 
     * @access public
     * @return PHPFrame_Database_RowCollection
     * @since  1.0
     */
    function getCollection(
        $orderby="u.lastname", 
        $orderdir="ASC", 
        $limit=-1, 
        $limitstart=0, 
        $search="",
        $deleted=false
    ) {
        $rows = new PHPFrame_Database_RowCollection();
        
        $rows->select(array("u.*", "g.id AS groupid", "g.name AS group_name"))
             ->from("#__users AS u")
             ->join("LEFT JOIN #__groups g ON u.groupid = g.id")
             ->groupby("u.id");
        
        // Add search filtering
        if ($search) {
            $rows->where("u.firstname LIKE :search", "OR", "u.lastname LIKE :search")
                 ->params(":search", "%".$search."%");
        }
        
        if ($deleted === true) {
            $rows->where("u.deleted", "<>", "'0000-00-00 00:00:00'");
            $rows->where("u.deleted", "IS NOT", "NULL");
        } else {
            $rows->where("u.deleted = '0000-00-00 00:00:00'", "OR", "u.deleted IS NULL");
        }

        $rows->load(null, '', array("groupid", "group_name"));
        
        return $rows;
    }
    
    /**
     * Get details for a single user
     * 
     * @param int $userid
     * 
     * @access public
     * @return     mixed    An object containing the user data or FALSE on failure.
     */
    public function getUser($userid=0)
    {
        // Instantiate user object
        $user = new PHPFrame_User();
        
        // Load user by id
        $user->load($userid);
        
        // Return user object
        return $user;
    }
    
    public function saveUser($post)
    {
        // Create new user object
        $user = new PHPFrame_User();
        
        // if no userid passed in request we assume it is a new user
        if (!isset($post['id']) || $post['id'] < 1) {
            $user->set('block', '0');
            $user->set('created', date("Y-m-d H:i:s"));
            // Generate random password and store in local variable to be used when sending email to user.
            $password = PHPFrame_Utils_Crypt::genRandomPassword();
            // Assign newly generated password to row object (this password will be encrypted when stored).
            $user->set('password', $password);
            $new_user = true;
        // if a userid is passed in the request we assume we are updating an existing user
        } else {
            $user->load($post['id'], 'password');
            $new_user = false;
        }
        
        // exlude password if not passed in request
        $exclude = '';
        if (empty($post['password'])) {
            $exclude = 'password';
        }
        
        // Bind the post data to the row array
        $user->bind($post, $exclude);
        
        if (!$user->store()) {
            $this->_error[] = $user->getLastError();
            return false;
        }
        
        // Send notification to new users
        if ($new_user === true) {
            $uri = new PHPFrame_Utils_URI();
        
            $new_mail = new PHPFrame_Mail_Mailer();
            $new_mail->AddAddress($user->email, PHPFrame_User_Helper::fullname_format($user->firstname, $user->lastname));
            $new_mail->Subject = _LANG_USER_NEW_NOTIFY_SUBJECT;
            $new_mail->Body = sprintf(_LANG_USER_NEW_NOTIFY_BODY, 
                                     $user->firstname, 
                                     $uri->getBase(), 
                                     $user->username, 
                                     $password
                            );
                                       
            if ($new_mail->Send() !== true) {
                $this->_error[] = sprintf(_LANG_EMAIL_NOT_SENT, $user->email);
                return false;
            }
        }
        
        return true;
    }
    
    public function deleteUser($userid)
    {
        $query = "UPDATE #__users SET `deleted` = '".date("Y-m-d H:i:s")."' WHERE id = ".$userid;
        if (PHPFrame::DB()->query($query) === false) {
            $this->_error[] = PHPFrame::DB()->getLastError();
            return false;
        } else {
            return true;
        }
    }
}
