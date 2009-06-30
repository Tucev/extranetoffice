<?php
/**
 * src/components/com_dashboard/views/dashboard/view.php
 * 
 * PHP version 5
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_dashboard
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @copyright  2009 E-noise.com Limited
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 */

/**
 * dashboardViewDashboard Class
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_dashboard
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 * @see        PHPFrame_MVC_View
 * @since      1.0
 */
class dashboardViewDashboard extends PHPFrame_MVC_View
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
        parent::__construct('dashboard', $layout);
    }
    
    /**
     * Override view display method
     * 
     * This method overrides the parent display() method and appends the page 
     * title to the document title.
     * 
     * @todo This method needs to be re-written to treat dashboard items as modules.
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function display()
    {
        // Add page title to view data
        $this->addData('page_title', _LANG_DASHBOARD);
        
        // Set title in response document
        $this->getDocument()->setTitle(_LANG_DASHBOARD);
        
        // Add pathway item
        $this->getPathway()->addItem(_LANG_DASHBOARD);
        
        parent::display();
    }
}
