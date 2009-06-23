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
        parent::display();
        
        // Append page title to document title
        if ($this->_layout != 'list') {
            $document = PHPFrame::getDocument('html');
            $document->title .= ' - '.$this->page_title;
        }
    }
    
    /**
     * Custom display method triggered by list layout.
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function displayUsersList()
    {
        $this->_data['page_title'] = _LANG_USERS;
    }
    
    /**
     * Custom display method triggered by detail layout.
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function displayUsersDetail()
    {
        $name = $this->_data['row']->firstname.' '.$this->_data['row']->lastname;
        // Set page title
        $this->_data['page_title'] = $name;
        
        // Set pathway
        $this->getPathway()->addItem($name);
    }
}
