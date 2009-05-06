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
		return phpFrame_Base_Singleton::getInstance('phpFrame_Application_Permissions');
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
	 * @return	object
	 * @since 	1.0
	 */
	public static function getModel($component_name, $model_name) {
		$class_name = substr($component_name, 4)."Model";
		$class_name .= ucfirst($model_name);
		return new $class_name();
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
		return phpFrame_Base_Singleton::getInstance($class_name);
	}
	
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
		return phpFrame_Base_Singleton::getInstance('phpFrame_Environment_Session');
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
?>