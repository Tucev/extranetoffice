<?php
/**
 * @version		$Id$
 * @package		ExtranetOffice
 * @subpackage 	application
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Dependencies Class
 * 
 * @package		phpFrame
 * @subpackage 	application
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class phpFrame_Application_Dependencies {
	/**
	 * XML object with depedency data from file
	 * 
	 * @static
	 * @access	private
	 * @var		object	SimpleXMLElement
	 */
	private static $_xml=null;
	/**
	 * A string containing the installed MySQL version
	 * 
	 * @static
	 * @access	private
	 * @var		string
	 */
	private static $_mysqlversion=null;
	/**
	 * A string containing the installed PHP version
	 * 
	 * @static
	 * @access	private
	 * @var		string
	 */
	private static $_phpversion=null;
	/**
	 * A boolean indicating whether dependencies are met or not
	 * 
	 * @static
	 * @access	private
	 * @var		boolean
	 */
	private static $_status=null;
	
	/**
	 * Private constructor prevents instantiation. This class should always be used statically.
	 * 
	 * @access	private
	 * @return	void
	 * @since	1.0
	 */
	private function __construct() {}
	
	/**
	 * Check dependencies as defined in XML file
	 * 
	 * If dependencies are not met it throws an exception.
	 * 
	 * This method explicity requires a session object to work with. This is done on purpose in 
	 * order to declare such dependency and promote loose coupling.
	 * 
	 * @static
	 * @access	public
	 * @param	object	$session	The session object where to store a status flag to avoid running 
	 * 								the dependency tests more than once in the same session.
	 * @return	mixed	Returns TRUE on success or throws an exception if dependencies are not met.
	 * @since	1.0
	 */
	public static function check(phpFrame_Registry_Session $session) {
		// If we already have a status flag we dont need to run check again
		$status = $session->get('dependencies.status');
		if (self::$_status === true || $status === true) {
			return;
		}
		
		// Load the dependency requirements from XML
		if (is_null(self::$_xml)) {
			self::$_xml = simplexml_load_file(_ABS_PATH.DS."inc".DS."dependencies.xml");
		}
		
		// Get MySQL version if not loaded yet
		if (is_null(self::$_mysqlversion)) {
			self::$_mysqlversion = self::getMySQLVersion();
		}
		
		// Get PHP version if not loaded yet
		if (is_null(self::$_phpversion)) {
			self::$_phpversion = phpversion();
		}
		
		// Check MySQL version
		if (version_compare(self::$_mysqlversion, self::$_xml->mysqlversion, '<')) {
			$msg = 'MySQL version '.self::$_xml->mysqlversion.' is required. Currently installed version is '.self::$_mysqlversion.'.';
			throw new phpFrame_Exception();
		}
		
		// Check PHP version
		if (version_compare(self::$_phpversion, self::$_xml->phpversion, '<')) {
			$msg = 'PHP version '.self::$_xml->phpversion.' is required. Currently installed version is '.self::$_phpversion.'.';
			throw new phpFrame_Exception($msg);
		}
		
		// Check PHP extensions
		foreach (self::$_xml->phpextension as $ext) {
			if (!function_exists($ext->testmethod)) {
				$msg = 'Required PHP Extension '.$ext->package.' not installed. ';
				$msg .= 'For more info check '.$ext->man.'.';
				throw new phpFrame_Exception($msg);
			}
		}
		
		// If now exceptions have been thrown we set the status to true
		// This will prevent running the check twice within the same process or session
		$session->set('dependencies.status', true);
		return self::$_status = true;
	}
	
	/**
	 * Get the MySQL server version number
	 * 
	 * @static
	 * @access	public
	 * @return	string
	 * @since	1.0
	 */
	public static function getMySQLVersion() {
		if (is_null(self::$_mysqlversion)) {
			$db = phpFrame::getDB();
			$query = "SELECT version() AS ve";
			$db->setQuery($query);
			return $db->loadResult();
		}
		else {
			return self::$_mysqlversion;
		}
	}
}
