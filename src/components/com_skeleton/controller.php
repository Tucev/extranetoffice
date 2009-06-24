<?php
/**
 * src/components/com_skeleton/controller.php
 * 
 * PHP version 5
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_skeleton
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @copyright  2009 E-noise.com Limited
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 */

/**
 * skeletonController Class
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_skeleton
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 * @since      1.0
 */
class skeletonController extends PHPFrame_MVC_ActionController
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
        parent::__construct('dispatch');
    }
    
    /**
     * Dispatch default view
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function dispatch()
    {
        // Get user rows using model
        $rows = $this->getModel("skeleton")->getCollection();
        
        // Get view
        $view = $this->getView('default');
        // Add rows data to view
        $view->addData('rows', $rows);
        // Display view
        $view->display();
    }
}
