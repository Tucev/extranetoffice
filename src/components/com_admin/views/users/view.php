<?php
/**
 * src/components/com_admin/views/users/view.php
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
 * adminViewUsers Class
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_admin
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 * @see        PHPFrame_MVC_View
 * @since      1.0
 */
class adminViewUsers extends PHPFrame_MVC_View
{
    /**
     * Constructor
     * 
     * @param string $layout String to specify a specific layout.
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function __construct($layout)
    {
        // Invoke the parent to set the view name and default layout
        parent::__construct('users', $layout);
    }
    
    /**
     * Override view display method
     * 
     * This method overrides the parent display() method and appends the page 
     * title to the document title.
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function display()
    {
        parent::display();
        
        // Append page title to document title
        $document = PHPFrame::Response()->getDocument();
        $document->title .= ' - '.$this->_data['page_title'];
    }
    
    /**
     * Custom display method triggered by list layout.
     * 
     * @access protected
     * @return void
     * @since  1.0
     */
    protected function displayUsersList()
    {
        $this->_data['page_title'] = _LANG_ADMIN_USERS;
    }
    
    /**
     * Custom display method triggered by form layout.
     * 
     * @access protected
     * @return void
     * @since  1.0
     */
    protected function displayUsersForm()
    {
        if (empty($this->_data['row']->id)) {
            $this->_data['page_title'] = _LANG_ADMIN_USERS_NEW;
        } else {
            $this->_data['page_title'] = _LANG_ADMIN_USERS_EDIT;
        }
    }
}
