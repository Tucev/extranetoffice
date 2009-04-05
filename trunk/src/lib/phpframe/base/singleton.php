<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage	base
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
 * @subpackage 	base
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @abstract 
 */
abstract class singleton extends standardObject {
	/**
	 * Variable holding an array of "single" instances of this classes children.
	 * 
	 * @var array
	 */
	private static $instances=array();
	
	/**
	 * Get the single instance of this sigleton class.
	 * 
	 * The $classname parameter is used in order to create the instance 
	 * using the class name from where the method is called at run level.
	 * If $classname is empty this method returns an instance of its own,
	 * not the child class that inherited this method.
	 * 
	 * @static
	 * @param string $classname The class name.
	 * @return object
	 */
	public static function &getInstance($classname) {
		// Check whether the requested class has alreay been instantiated
		if (!array_key_exists($classname, self::$instances)) {
            // instance does not exist, so create it
            if (class_exists($classname)) {
            	self::$instances[$classname] = new $classname;
            }
            else {
            	throw new Exception('Class '.$classname.' not found.');
            }
        }
        
        $instance =& self::$instances[$classname];
        
        return $instance;
    }
    
    public static function destroyInstance($classname) {
   		// Check whether the requested class has alreay been instantiated
		if (array_key_exists($classname, self::$instances)) {
            // unset instance
            unset(self::$instances[$classname]);
        }
        else {
        	throw new Exception('No instance of '.$classname.' found.');
        }
    } 
}
?>