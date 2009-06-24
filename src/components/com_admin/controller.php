<?php
/**
 * src/components/com_admin/controller.php
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
 * adminController Class
 * 
 * @todo Handling of tmpl get var should be delegated to URL rewriter instead 
 *       of appearing inside the required controler actions.
 *                 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_admin
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 * @since      1.0
 */
class adminController extends PHPFrame_MVC_ActionController
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
        parent::__construct('get_admin');
    }
    
    /**
     * Display admin panel
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function get_admin()
    {
        // Get view
        $view = $this->getView('admin', '');
        // Display view
        $view->display();
    }
    
    /**
     * Display config form
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function get_config()
    {
        // Get view
        $view = $this->getView('config', '');
        // Display view
        $view->display();
    }
    
    /**
     * Display users list
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
        $limit=25, 
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
     * Display user form
     * 
     * @param int $userid The user id of the user used to pre-populate the form 
     *                    when editing exiting users.
     * 
     * @access public
     * @return void
     * @since  1.0
     */ 
    public function get_user_form($userid)
    {
        // Get users using model
        $user = $this->getModel('users')->getUser($userid);
        
        // Get view
        $view = $this->getView('users', 'form');
        // Set view data
        $view->addData('row', $user);
        // Display view
        $view->display();
    }
    
    /**
     * Display components list
     * 
     * @access public
     * @return void
     * @since  1.0
     */ 
    public function get_components()
    {
    
    }

    /**
     * Display widgets list
     * 
     * @access public
     * @return void
     * @since  1.0
     */ 
    public function get_widgets()
    {
    
    }
    
    /**
     * Save global configuration
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function save_config()
    {
        // Check for request forgeries
        PHPFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
        
        // Get request vars
        $tmpl = PHPFrame::Request()->get('tmpl', '');
        $post = PHPFrame::Request()->getPost();
        
        $modelConfig = $this->getModel('config');
        
        if ($modelConfig->saveConfig($post) === false) {
            $this->sysevents->setSummary($modelConfig->getLastError());
        } else {
            $this->sysevents->setSummary(_LANG_CONFIG_SAVE_SUCCESS, "success");
            $this->_success = true;
        }
        
        if (!empty($tmpl)) $tmpl = "&tmpl=".$tmpl;
        $this->setRedirect('index.php?component=com_admin&action=get_config'.$tmpl);
    }
    
    /**
     * Save user
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function save_user()
    {
        // Check for request forgeries
        PHPFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
        
        // Get request vars
        $tmpl = PHPFrame::Request()->get('tmpl', '');
        $post = PHPFrame::Request()->getPost();
        
        $modelUsers = $this->getModel('users');
        
        if ($modelUsers->saveUser($post) === false) {
            $this->sysevents->setSummary($modelUsers->getLastError());
        } else {
            $this->sysevents->setSummary(_LANG_USER_SAVE_SUCCESS, "success");
            $this->_success = true;
        }
        
        if (!empty($tmpl)) $tmpl = "&tmpl=".$tmpl;
        $this->setRedirect('index.php?component=com_admin&action=get_users'.$tmpl);
    }
    
    /**
     * Remove user
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function remove_user()
    {
        // Get request vars
        $tmpl = PHPFrame::Request()->get('tmpl', '');
        $userid = PHPFrame::Request()->get('id', 0);
        
        $modelUsers = $this->getModel('users');
        
        if ($modelUsers->deleteUser($userid) === false) {
            $this->sysevents->setSummary($modelUsers->getLastError());
        } else {
            $this->sysevents->setSummary(_LANG_ADMIN_USERS_DELETE_SUCCESS, "success");
        }
        
        if (!empty($tmpl)) $tmpl = "&tmpl=".$tmpl;
        $this->setRedirect('index.php?component=com_admin&action=get_users'.$tmpl);
    }
}
