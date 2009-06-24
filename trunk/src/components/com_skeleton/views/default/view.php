<?php
/**
 * src/components/com_skeleton/views/default/view.php
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
 * skeletonViewDefault Class
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_skeleton
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 * @see        PHPFrame_MVC_View
 * @since      1.0
 */
class skeletonViewDefault extends PHPFrame_MVC_View
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
        $document = PHPFrame::Response()->getDocument();
        $document->addScript("lib/jquery/plugins/lightbox/jquery.lightbox-0.5.js");
        
        // Invoke the parent to set the view name and default layout
        parent::__construct('default', $layout);
    }
}
