<?php
/**
 * src/components/com_users/views/users/view.php
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
 * usersViewUsers Class
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_users
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 * @see        PHPFrame_MVC_View
 * @since      1.0
 */
class usersViewUsers extends PHPFrame_MVC_View
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
     * This method overrides the parent display() method and appends the page title 
     * to the document title.
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function display()
    {
        // Set title in response document
        $this->getDocument()->setTitle(_LANG_USERS);
        
        // Add pathway item
        $this->getPathway()->addItem(_LANG_USERS, "index.php?component=com_users");
        
        parent::display();
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
        // Add page title to view data
        $this->addData('page_title', _LANG_USERS);
    }
    
    /**
     * Custom display method triggered by detail layout.
     * 
     * @access protected
     * @return void
     * @since  1.0
     */
    protected function displayUsersDetail()
    {
        $user = $this->_data['row'];
        
        // Set page title
        $this->addData('page_title', $user->firstname.' '.$user->lastname);
        
        // Add pathway item
        $this->getPathway()->addItem($user->firstname.' '.$user->lastname);
        
        // Append user name to document title
        $this->getDocument()->appendTitle(" - ".$user->firstname.' '.$user->lastname);
    }
}
