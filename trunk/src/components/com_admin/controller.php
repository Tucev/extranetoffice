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
        
        $redirect_url = 'index.php?component=com_admin&action=get_config';
        if (!empty($tmpl)) $redirect_url .= "&tmpl=".$tmpl;
        
        $this->setRedirect($redirect_url);
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
        
        $redirect_url = 'index.php?component=com_admin&action=get_users';
        if (!empty($tmpl)) $redirect_url .= "&tmpl=".$tmpl;
        
        $this->setRedirect($redirect_url);
    }
    
    /**
     * Remove user
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function remove_user($userid, $tmpl='')
    {
        $model = $this->getModel('users');
        
        try {
            $model->deleteUser($userid);
            
            $this->sysevents->setSummary(_LANG_ADMIN_USERS_DELETE_SUCCESS, "success");
            
        } catch (Exception $e) {
            $this->sysevents->setSummary(_LANG_ADMIN_USERS_DELETE_ERROR);
        }
        
        $redirect_url = 'index.php?component=com_admin&action=get_users';
        if (!empty($tmpl)) $redirect_url .= "&tmpl=".$tmpl;
        
        $this->setRedirect($redirect_url);
    }
    
    /**
     * Display organisations list
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
    public function get_organisations(
        $orderby="o.name", 
        $orderdir="ASC", 
        $limit=25, 
        $limitstart=0, 
        $search=""
    ) {
        // Get organisations using model
        $model = $this->getModel('organisations');
        $organisations = $model->getCollection(
                                     $orderby, 
                                     $orderdir, 
                                     $limit, 
                                     $limitstart, 
                                     $search
                                 );
        
        // Get view
        $view = $this->getView('organisations', 'list');
        // Set view data
        $view->addData('rows', $organisations);
        // Display view
        $view->display();
    }
    
    /**
     * Display organisation form
     * 
     * @param int $organisationid The id of the organisation used to pre-populate the form 
     *                            when editing exiting organisations.
     * 
     * @access public
     * @return void
     * @since  1.0
     */ 
    public function get_organisation_form($organisationid)
    {
        // Get users using model
        $organisation = $this->getModel('organisations')->getRow($organisationid);
        
        // Get view
        $view = $this->getView('organisations', 'form');
        // Set view data
        $view->addData('row', $organisation);
        // Display view
        $view->display();
    }
    
    /**
     * Save organisation
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function save_organisation($tmpl='')
    {
        // Check for request forgeries
        PHPFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
        
        // Get post
        $post = PHPFrame::Request()->getPost();
        
        $model = $this->getModel('organisations');
        
        try {
            $model->saveRow($post);
            
            $this->sysevents->setSummary(_LANG_ORGANISATION_SAVE_SUCCESS, "success");
            $this->_success = true;
            
        } catch (Exception $e) {
            $this->sysevents->setSummary(_LANG_ORGANISATION_SAVE_ERROR);
        }
        
        $redirect_url = 'index.php?component=com_admin&action=get_organisations';
        if (!empty($tmpl)) $redirect_url .= "&tmpl=".$tmpl;
        
        $this->setRedirect($redirect_url);
    }
    
    /**
     * Remove organisation
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function remove_organisation($organisationid, $tmpl='')
    {
        $model = $this->getModel('organisations');
        
        try {
            $model->deleteRow($organisationid);
            
            $this->sysevents->setSummary(_LANG_ORGANISATION_DELETE_SUCCESS, "success");
            
        } catch (Exception $e) {
            $this->sysevents->setSummary(_LANG_ORGANISATION_DELETE_ERROR);
        }
        
        $redirect_url = 'index.php?component=com_admin&action=get_organisations';
        if (!empty($tmpl)) $redirect_url .= "&tmpl=".$tmpl;
        
        $this->setRedirect($redirect_url);
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
}
