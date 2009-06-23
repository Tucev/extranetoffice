<?php
/**
 * src/components/com_login/models/login.php
 * 
 * PHP version 5
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_login
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @copyright  2009 E-noise.com Limited
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 */

/**
 * Login Model Class
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_login
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 * @see        PHPFrame_MVC_Model
 * @since      1.0
 */
class loginModelLogin extends PHPFrame_MVC_Model
{
    /**
     * Constructor
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function __construct() {}
    
    /**
     * Process log in
     * 
     * @param string $username The username to login.
     * @param string $password The password to identify the given user.
     * 
     * @access public
     * @return bool
     * @since  1.0
     */
    public function login($username, $password)
    {
        // We use an IdObject to build the query to search for the user
        // It is essential for security that the username is passed as
        // a query parameter to avoid SQL injection.
        $id_obj = new PHPFrame_Database_IdObject();
        $id_obj->select(array("id", "password"))
               ->from("#__users")
               ->where("username", "=", ":username")
               ->params(":username", $username);
               
        $credentials = new PHPFrame_Database_Row("#__users");
        $credentials->load($id_obj);
        
        // User exists
        if ($credentials instanceof PHPFrame_Database_Row 
            && !is_null($credentials->id)
        ) {
            $user = new PHPFrame_User();
            $user->load($credentials->id);
            
            // check password
            $parts = explode( ':', $credentials->password );
            $crypt = $parts[0];
            $salt = @$parts[1];
            $testcrypt = PHPFrame_Utils_Crypt::getCryptedPassword($password, $salt);
            if ($crypt == $testcrypt) {
                // Store user data in session
                $session = PHPFrame::Session();
                $session->setUser($user);
                return true;
            } else {
                // Wrong password
                $this->_error[] = "Authorisation failed: Wrong password";
                return false;
            }
        } else {
            // Username not found
            $this->_error[] = "Authorisation failed: Username not found";
            return false;
        }
    }
    
    /**
     * Log out
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function logout()
    {
        PHPFrame::Session()->destroy();
    }
    
    /**
     * Reset password for user with given email address
     * 
     * @param string $email The email address used to select the user.
     * 
     * @access public
     * @return bool   Returns TRUE on success or FALSE on failure
     * @since  1.0
     */
    public function resetPassword($email)
    {
        // First we check whether there is a user with the passed email address
        $query = "SELECT id FROM #__users WHERE email = '".$email."'";
        $userid = PHPFrame::DB()->loadResult($query);
        
        if (!empty($userid)) {
            // Create user object
            $user = new PHPFrame_User();
            $user->load($userid, 'password');
            // Generate random password and store in local variable to be used when sending email to user.
            $password = PHPFrame_Utils_Crypt::genRandomPassword();
            // Assign newly generated password to row object (this password will be encrypted when stored).
            $user->set('password', $password);
            
            if (!$user->store()) {
                $this->_error[] = $user->getLastError();
                return false;
            }
            
            // Send notification to new users
            $uri = new PHPFrame_Utils_URI();
            
            $new_mail = new PHPFrame_Mail_Mailer();
            $new_mail->AddAddress($user->email, PHPFrame_User_Helper::fullname_format($user->firstname, $user->lastname));
            $new_mail->Subject = _LANG_USER_RESET_PASS_NOTIFY_SUBJECT;
            $new_mail->Body = sprintf(_LANG_USER_RESET_PASS_NOTIFY_BODY, 
                                         $user->firstname, 
                                         $uri->getBase(), 
                                         $user->username, 
                                         $password
                                );
                                           
            if ($new_mail->Send() !== true) {
                $this->_error[] = sprintf(_LANG_EMAIL_NOT_SENT, $user->email);
                return false;
            }
            
            return true;
        } else {
            $this->_error[] = _LANG_RESET_PASS_EMAIL_NOT_FOUND;
            return false;
        }
    }
    
}
