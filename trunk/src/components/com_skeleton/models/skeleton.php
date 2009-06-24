<?php
/**
 * src/components/com_skeleton/models/skeleton.php
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
 * skeletonModelSkeleton Class
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_skeleton
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 * @see        PHPFrame_MVC_Model
 * @since      1.0
 */
class skeletonModelSkeleton extends PHPFrame_MVC_Model
{
    /**
     * Constructor
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function __construct() {}
    
    /**
     * Get a collection of row objects...
     * 
     * @access public
     * @return PHPFrame_Database_RowCollection
     * @since  1.0
     */
    public function getCollection()
    {
        /*
         * 1. One liner (sort-of)
         *
         */
        
        // Create RowCollection object
        $rows = new PHPFrame_Database_RowCollection(array("select"=>"*", 
                                                           "from"=>"#__users", 
                                                           "where"=>array("id", "=", "62")
                                                          )
                                                    );
        // Load the selection
        $rows->load();
        // Print results
        //echo $rows;
        
        /*
         * 2. Using separate options array for clarity options
         */
        
        // Create options array
        $options = array("select"=>"*", 
                         "from"=>"#__users", 
                         "where"=>array("id", "=", "62"));
        
        // Create RowCollection object
        $rows2 = new PHPFrame_Database_RowCollection($options);
        // Load the selection
        $rows2->load();
        // Print results
        //print_r($rows2);
        
        /*
         * 3. Using id object's fluent syntax
         */
        
        // Create RowCollection object
        $rows3 = new PHPFrame_Database_RowCollection();
        // Make a selection
        $rows3->select("*")->from("#__users")->where("id", "=", "62");
        // Load the selection
        $rows3->load();
        // Print results
        //print_r($rows3);
        
        return $rows;
    }
}
