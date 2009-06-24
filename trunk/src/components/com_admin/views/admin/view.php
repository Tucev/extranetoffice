<?php
/**
 * src/components/com_admin/views/admin/view.php
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
 * adminViewAdmin Class
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
class adminViewAdmin extends PHPFrame_MVC_View
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
        parent::__construct('admin', $layout);
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
        // Set tmpl request var in view so it can be used to append to urls when
        // view is loaded with tmpl=component (that means no overall template).
        // This is useful for views being loaded via ajax
        $tmpl = PHPFrame::Request()->get('tmpl', '');
        if (!empty($tmpl)) {
            $tmpl = "&tmpl=".$tmpl;
        }
        $this->addData('tmpl', $tmpl);
        
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
    protected function displayAdmin()
    {
        $this->_data['page_title'] = _LANG_ADMIN;
    }
}
