<?php
/**
 * src/components/com_users/controller.php
 * 
 * PHP version 5
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_users
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @copyright  2009 E-noise.com Limited
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 */

/**
 * usersController Class
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_users
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 * @since      1.0
 */
class usersController extends PHPFrame_MVC_ActionController
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
        parent::__construct('get_users');
    }
    
    /**
     * Get users list
     * 
     * @param string $orderby
     * @param string $orderdir
     * @param int    $limit
     * @param int    $limitstart
     * @param string $search
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function get_users(
        $orderby="u.lastname", 
        $orderdir="ASC", 
        $limit=-1, 
        $limitstart=0, 
        $search=""
    ) {
        // Get users using model
        $users = $this->getModel('users')->getCollection($orderby, 
                                                         $orderdir, 
                                                         $limit, 
                                                         $limitstart, 
                                                         $search);
        
        // Get view
        $view = $this->getView('users', 'list');
        // Set view data
        $view->addData('rows', $users);
        // Display view
        $view->display();
    }
    
    /**
     * Get user detail
     * 
     * @param int $projectid The user id
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function get_user($userid)
    {
        // Get users using model
        $user = $this->getModel('users')->getUser($userid);
        
        // Get view
        $view = $this->getView('users', 'detail');
        // Set view data
        $view->addData('row', $user);
        // Display view
        $view->display();
    }
    
    /**
     * Display user
     * 
     * @param string $ret_url A string with the URL to return to after saving user settings.
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function get_settings($ret_url='index.php')
    {
        $user = PHPFrame::Session()->getUser();
        
        // Get view
        $view = $this->getView('settings');
        // Set view data
        $view->addData('row', $user);
        $view->addData('ret_url', $ret_url);
        // Display view
        $view->display();
    }
    
    /**
     * Save user
     * 
     * @param $ret_url A string with the URL to return to after saving user settings.
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function save_user($ret_url='index.php')
    {
        // Check for request forgeries
        PHPFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
        
        // Get request vars
        $post = PHPFrame::Request()->getPost();
        
        
        // Save user using model
        $user = $this->getModel('users')->saveUser($post);
        
        if ($user instanceof PHPFrame_User && $user->get('id') > 0) {
            $this->sysevents->setSummary(_LANG_USER_SAVE_SUCCESS, "success");
            $this->_success = true;
        } else {
            $this->sysevents->setSummary(_LANG_USER_SAVE_ERROR);
        }
        
        $this->setRedirect($ret_url);
    }
}
