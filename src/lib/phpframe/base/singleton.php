<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage	base
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Singleton Class
 * 
 * This class is used to inherit the Singleton design pattern.
 * It restricts instantiation of a class to one object. 
 * 
 * Singleton objects are instantiated using the getInstance() method. 
 * This is enforced with the __construct() method set to protected.
 * 
 * Example:
 * <code>
 * class singletonClass extends phpFrame_Base_Singleton {
 * 		// Class code...
 * }
 * 
 * // This will fail
 * $mySingletonObject1 = new singletonClass();
 * 
 * // This will assign the singleton object to $mySingletonObject2
 * $mySingletonObject2 = phpFrame_Base_Singleton::getInstance('singletonClass');
 * 
 * // This will also work, because the getInstance() method is inherited from the phpFrame_Base_Singleton class
 * $mySingletonObject3 = singletonClass::getInstance('singletonClass');
 * </code>
 * 
 * @package		phpFrame
 * @subpackage 	base
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @abstract 
 */
abstract class phpFrame_Base_Singleton extends phpFrame_Base_StdObject {
	/**
	 * Variable holding an array of "single" instances of this classes children.
	 * 
	 * @static
	 * @access	private
	 * @var array
	 */
	private static $_instances=array();
	
	/**
	 * Constructor
	 * 
	 * The protected constructor ensures this class is not instantiated using the 'new' keyword.
	 * Singleton objects are instantiated using the getInstance() method.
	 * 
	 * @return void
	 */
	protected function __construct() {}
	
	/**
	 * Get the single instance of this sigleton class.
	 * 
	 * The $className parameter is used in order to create the instance 
	 * using the class name from where the method is called at run level.
	 * If $className is empty this method returns an instance of its own,
	 * not the child class that inherited this method.
	 * 
	 * @static
	 * @access	public
	 * @param	string $className The class name.
	 * @return	object
	 */
	public static function &getInstance($className) {
		// Check whether the requested class has alreay been instantiated
		if (!array_key_exists($className, self::$_instances)) {
            // instance does not exist, so create it
            if (class_exists($className)) {
            	self::$_instances[$className] = new $className;
            }
            else {
            	throw new Exception('Class '.$className.' not found.');
            }
        }
        
        $instance =& self::$_instances[$className];
        
        return $instance;
    }
    
    /**
     * Unset a singleton object
     * 
     * @static
     * @access	public
     * @param	string	$className
     * @return	void
     */
    public static function destroyInstance($className) {
   		// Check whether the requested class has alreay been instantiated
		if (array_key_exists($className, self::$_instances)) {
            // unset instance
            unset(self::$_instances[$className]);
        }
        else {
        	throw new Exception('No instance of '.$className.' found.');
        }
    } 
}
?>