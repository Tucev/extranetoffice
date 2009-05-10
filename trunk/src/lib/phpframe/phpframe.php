<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * phpFrame Class
 * 
 * This class provides a factory to create phpFrame objects.
 * 
 * It also provides information about the installed phpFrame version.
 * 
 * @package		phpFrame
 * @since 		1.0
 */
class phpFrame {
	/**
	 * The phpFrame version
	 * 
	 * @var string
	 */
	const VERSION='1.0 Alpha';
	
	/**
	 * Get phpFrame version
	 * 
	 * @return	string
	 * @since 	1.0
	 */
	public static function getVersion() {
		return self::VERSION;
	}
	
	/**
	 * Get application object
	 * 
	 * @return	object
	 * @since 	1.0
	 */
	public static function getFrontController() {
		return phpFrame_Base_Singleton::getInstance('phpFrame_Application_FrontController');
	}
	
	/**
	 * Get response object
	 * 
	 * @return object
	 * @since 	1.0
	 */
	public static function getResponse() {
		return phpFrame_Base_Singleton::getInstance('phpFrame_Environment_Response');
	}
	
	/**
	 * Get permissions object
	 * 
	 * @return	object
	 * @since 	1.0
	 */
	public static function getPermissions() {
		return phpFrame_Application_Permissions::getInstance();
	}
	
	/**
	 * Get component action controller object for given option
	 * 
	 * @param	string	$component_name
	 * @return	object
	 * @since 	1.0
	 */
	public static function getActionController($component_name) {
		$class_name = substr($component_name, 4)."Controller";
		return phpFrame_Base_Singleton::getInstance($class_name);
	}
	
	/**
	 * Get model
	 * 
	 * @param	$component_name
	 * @param	$model_name
	 * @param	$args
	 * @return	object
	 * @since 	1.0
	 */
	public static function getModel($component_name, $model_name, $args=array()) {
		$class_name = substr($component_name, 4)."Model";
		$class_name .= ucfirst($model_name);
		
		// make a reflection object
		$reflectionObj = new ReflectionClass($class_name);
		
		// Check if class is instantiable
		if ($reflectionObj->isInstantiable()) {
			// Try to get the constructor
			$constructor = $reflectionObj->getConstructor();
			// Check to see if we have a valid constructor method
			if ($constructor instanceof ReflectionMethod) {
				// If constructor is public we create a new instance
				if ($constructor->isPublic()) {
					return $reflectionObj->newInstanceArgs($args);
				}
			}
			// No declared constructor, so we instantiate without args
			return new $class_name;
		}
		elseif ($reflectionObj->hasMethod('getInstance')) {
			$get_instance = $reflectionObj->getMethod('getInstance');
			if ($get_instance->isPublic() && $get_instance->isStatic()) {
				return call_user_func_array(array($class_name, 'getInstance'), $args);
			}
		}
		
		// If we have not been able to return a model object we throw an exception
		throw new Exception($model_name." not supported. Could not get instance of ".$class_name);
	}
	
	/**
	 * Get a table class.
	 * 
	 * @param	string	$component_name
	 * @param	string	$table_name
	 * @return	object
	 * @since 	1.0
	 */
	public static function getTable($component_name, $table_name) {
		$class_name = substr($component_name, 4)."Table".ucfirst($table_name);
		return phpFrame_Database_Table::getInstance($class_name);
	}
	
	/**
	 * Get modules
	 * 
	 * @return	object
	 * @since 	1.0
	 */
	public static function getModules() {
		return phpFrame_Base_Singleton::getInstance('phpFrame_Application_Modules');
	}
	
	/**
	 * Get database object
	 * 
	 * @return	object
	 * @since 	1.0
	 */
	public static function getDB() {
		return phpFrame_Base_Singleton::getInstance('phpFrame_Database');
	}
	
	/**
	 * Get user object
	 * 
	 * @return 	object
	 * @since 	1.0
	 */
	public static function getUser() {
		return phpFrame_Base_Singleton::getInstance('phpFrame_User');
	}
	
	/**
	 * Get session object
	 * 
	 * @return	object
	 * @since 	1.0
	 */
	public static function getSession() {
		return phpFrame_Database_Table::getInstance('phpFrame_Environment_Session');
	}
	
	/**
	 * Get system events object
	 * 
	 * @return	object
	 * @since 	1.0
	 */
	public static function getSysevents() {
		return phpFrame_Base_Singleton::getInstance('phpFrame_Application_Sysevents');
	}
	
	/**
	 * Get document object
	 * 
	 * @param	string	$type The document type (html or xml)
	 * @return	object
	 * @since 	1.0
	 */
	public static function getDocument($type) {
		return phpFrame_Base_Singleton::getInstance('phpFrame_Document_'.strtoupper($type));
	}
	
	/**
	 * Get uri object
	 * 
	 * @return	object
	 * @since 	1.0
	 */
	public static function getURI($uri='') {
		return new phpFrame_Utils_URI($uri);
	}
	
	/**
	 * Get pathway object
	 * 
	 * @return	object
	 * @since 	1.0
	 */
	public static function getPathway() {
		return phpFrame_Base_Singleton::getInstance('phpFrame_Application_Pathway');
	}
}
