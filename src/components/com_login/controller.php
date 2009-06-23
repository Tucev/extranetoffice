<?php
/**
 * src/components/com_login/controller.php
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
 * loginController Class
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_login
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 * @since      1.0
 */
class loginController extends PHPFrame_MVC_ActionController
{
    /**
     * Constructor
     * 
     * @access protected
     * @return void
     * @since  1.0
     */
    protected function __construct()
    {
        // Invoke parent's constructor to set default action
        parent::__construct('get_login_form');
    }
    
    /**
     * Display login form
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function get_login_form()
    {
        // Get view
        $view = $this->getView('login');
        // Display view
        $view->display();
    }
    
    /**
     * Process login
     * 
     * @param string $username The username to login.
     * @param string $password The password to identify the given user.
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function login($username, $password)
    {
        // Check for request forgeries
        PHPFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
        
        // if user is not logged on we attemp to login
        $session = PHPFrame::Session();
        if (!$session->isAuth()) {
            // Get login model
            $model = $this->getModel('login');
            if (!$model->login($username, $password)) {
                $this->sysevents->setSummary($model->getLastError(), "warning");
            }
            
            $this->_success = true;
        }
        
        $this->setRedirect('index.php');
    }
    
    /**
     * Process logout
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function logout()
    {
        // Logout using model
        $this->getModel('login')->logout();
        
        // Redirect
        $this->setRedirect('index.php');
    }
    
    /**
     * Reset password
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function reset_password()
    {
        // Check for request forgeries
        PHPFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
        
        $email = PHPFrame::Request()->get('email_forgot', '');
        
        // Push model into controller
        $model = $this->getModel('login');
        if (!$model->resetPassword($email)) {
            $this->sysevents->setSummary($model->getLastError(), "warning");
        } else {
            $this->sysevents->setSummary(_LANG_RESET_PASS_SUCCESS, "success");
            $this->_success = true;
        }
        
        $this->setRedirect('index.php');
    }
    
}
