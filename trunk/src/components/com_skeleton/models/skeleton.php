<?php
/**
 * @version     $Id$
 * @package        PHPFrame
 * @subpackage    com_skeleton
 * @copyright    Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license        BSD revised. See LICENSE.
 */

/**
 * skeletonModelSkeleton Class
 * 
 * @package        PHPFrame
 * @subpackage     com_skeleton
 * @author         Luis Montero [e-noise.com]
 * @since         1.0
 * @see         PHPFrame_MVC_Model
 */
class skeletonModelSkeleton extends PHPFrame_MVC_Model {
    /**
     * Constructor
     * 
     * @return    void
     */
    public function __construct() {}
    
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
