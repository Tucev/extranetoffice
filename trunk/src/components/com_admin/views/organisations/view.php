<?php
/**
 * src/components/com_admin/views/organisations/view.php
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
 * adminViewOrganisations Class
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
class adminViewOrganisations extends PHPFrame_MVC_View
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
        parent::__construct('organisations', $layout);
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
    }
    
    /**
     * Custom display method triggered by list layout.
     * 
     * @access protected
     * @return void
     * @since  1.0
     */
    protected function displayOrganisationsList()
    {
        $this->_data['page_title'] = _LANG_ORGANISATIONS;
    }
    
    /**
     * Custom display method triggered by form layout.
     * 
     * @access protected
     * @return void
     * @since  1.0
     */
    protected function displayOrganisationsForm()
    {
        $organisationid = $this->_data['row']->id;
        
        if (empty($organisationid)) {
            $page_title = _LANG_ADMIN_ORGANISATIONS_NEW;
        } else {
            $page_title = _LANG_ADMIN_ORGANISATIONS_EDIT;
        }
        
        $this->addData('page_title', $page_title);
    }
}
