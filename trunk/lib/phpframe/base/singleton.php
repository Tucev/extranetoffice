<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage	objects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Singleton Class
 * 
 * This class is used to implement the singleton design pattern.
 * It restricts instantiation of a class to one object.
 * 
 * @package		phpFrame
 * @subpackage 	objects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @abstract 
 */
abstract class singleton {
	/**
	 * Get the single instance of this sigleton class.
	 * 
	 * The $classname parameter is used in order to create the instance 
	 * using the class name from where the method is called at run level.
	 * If $classname is empty this method returns an instance of its own,
	 * not the child class that inherited this method.
	 *
	 * @param string $classname The class name.
	 * @return object
	 */
	public static function &getInstance($classname) {
		/**
		 * Variable holding an array of "single" instances of this classes children.
		 *
		 * @var array
		 */
		static $instances = array();
		
		if (!array_key_exists($classname, $instances)) {
            // instance does not exist, so create it
            if (class_exists($classname)) {
            	$instances[$classname] = new $classname;
            }
            else {
            	error::raise('', 'error', 'Class '.$classname.' not found.');
            }
        }
        
        $instance =& $instances[$classname];
        
        return $instance;
    }
}
?>