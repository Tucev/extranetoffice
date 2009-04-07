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
	
	public static function getVersion() {
		return self::VERSION;
	}
	
	/**
	 * Get application object
	 * 
	 * @return	object
	 * @since 	1.0
	 */
	public static function getApplication() {
		return phpFrame_Base_Singleton::getInstance('phpFrame_Application');
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
	 * Get pathway object
	 * 
	 * @return	object
	 * @since 	1.0
	 */
	public static function getPathway() {
		return phpFrame_Base_Singleton::getInstance('phpFrame_Application_Pathway');
	}
	
	/**
	 * Get component controller object for given option
	 * 
	 * @param	string	$option
	 * @return	object
	 * @since 	1.0
	 */
	public static function getController($option) {
		$className = substr($option, 4)."Controller";
		return phpFrame_Base_Singleton::getInstance($className);
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
}
?>