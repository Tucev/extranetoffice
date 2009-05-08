<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	environment
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );
	
/**
 * Request Class
 * 
 * This class encapsulates access to the request arrays and provides input filtering.
 * 
 * All properties and methods in this class are static. Instantiation is prevented by 
 * the constructor being declared as private.
 * 
 * The request class is responsible for detecting the client (default, mobile, cli or xmlrpc)
 * and processing the incoming request accordingly.
 * 
 * @todo		This class needs to be changed to use phpFrame_Utils_Filter instead of phpinputfilter
 * @package		phpFrame
 * @subpackage 	environment
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class phpFrame_Environment_Request {
	/**
	 * A unification array of filtered global arrays
	 * 
	 * @static
	 * @access	private
	 * @var		array
	 */
	private static $_URA=array();
	/**
	 * Array list of core variable names
	 * 
	 * @static
	 * @access	private
	 * @var		array
	 */
	private static $_core_variables=array('component','action','view','layout');
	/**
	 * A core request variable specifying controller/component
	 * 
	 * @static
	 * @access	private
	 * @var		string
	 */
	private static $_component=null;
	/**
	 * A core request variable specifying controller/component functino to execute
	 * 
	 * @static
	 * @access	private
	 * @var		string
	 */
	private static $_action=null;
		
	/**
	 * Instance of PHPInputFilter
	 * 
	 * @static
	 * @access	private
	 * @var		object
	 */
	private static $_inputfilter=null;
	
	/**
	 * Private client helper object
	 *
	 * @static
	 * @access	private
	 * @var		object helper implements IClient
	 */
	private static $_client_helper=null;
	
	/**
	 * Private constructor prevents instantiation. This class should always be used statically.
	 * 
	 * @access	private
	 * @return	void
	 * @since	1.0
	 */
	private function __construct() {}
	
	/**
	 * Initialise
	 * 
	 * Initialise the request. This method detects the client using a client helper
	 * and populates the request variables accordingly.
	 * 
	 * @static
	 * @access	public
	 * @return	array
	 * @since	1.0
	 */
	public static function init() {
		// Before processing request data we check wheter the request has already been
		// initialised by testing to see if $_URA is populated
		if (!empty(self::$_URA)) {
			return;
		}
		
		self::$_inputfilter = new InputFilter();
		
		self::detectClient();
		self::$_URA = self::$_client_helper->populateURA();
		
		//TODO stop ignoring $_SESSION and $_COOKIES
		
		//add other globals
		self::$_URA['env'] = $_ENV;
		self::$_URA['server'] = $_SERVER;
		self::$_URA['files'] = $_FILES;
		
		//TODO possible optimisation, see following:
		/*
		foreach (self::$_core_variables as $key) {
			$core_variable_name = '_'.$key;
			self::$$core_variable_name = self::$_URA['request'][$key];
			unset(self::$_URA['request'][$key]);
		}
		*/
		
		//rip core variables from URA
		self::$_component = self::$_URA['request']['component'];
		unset(self::$_URA['request']['component']);
		self::$_action = self::$_URA['request']['action'];
		unset(self::$_URA['request']['action']);
	}
	
	/**
	 * Detect and set helper object 
	 * 
	 */
	private static function detectClient() {
		
		//scan through environment dir to find files
		$clients_path = _ABS_PATH.DS."lib".DS."phpframe".DS."client";
		$files = scandir($clients_path);
		
		//make sure the clientdefault is the last file in array as catch-all helper
		$default = array_search('default.php', $files);	//if it's there
		if ($default != false) {
			unset($files[$default]);						//unset it
			$files[] = 'default.php';						//add to end
		}
		
		//loop through files 
		foreach ($files as $file) {
			if (is_file($clients_path.DS.$file) && $file != 'iclient.php') {
				//build class names
				$className = 'phpFrame_Client_'.ucfirst(substr($file, 0, strpos($file, '.')));
				if (is_callable(array($className, 'detect'))) {
					//call class's detect() to check if this is the helper we need 
					self::$_client_helper = call_user_func(array($className, 'detect'));
					if (self::$_client_helper instanceof phpFrame_Client_IClient) {
						//break out of the function if we found our helper
						return;
					} 
				}
			}
		}
		//throw error if no helper is found
		throw new phpFrame_Exception(_PHPFRAME_LANG_REQUEST_ERROR_NO_CLIENT_HELPER);
	}
	
	/**
	 * Get client object
	 * 
	 * @return	object of type phpFrame_Environment_IClient
	 */
	public static function getClient() {
		return self::$_client_helper;
	}
	

	/**	
	 * Get $_client_helper->getName();
	 * 
	 * @static
	 * @access	public
	 * @return	client helper name
	 */
	public static function getClientName() {
		//initialise if  $_client_helper is null 
		if (self::$_client_helper == null) self::init();
		//return $_client_helper->getName();
		return self::$_client_helper->getName();
	}
	
	/**
	 * Get request/post array from URA
	 * 
	 * @return	array
	 */
	public static function getPost() {
		return self::$_URA['request'];
	}
	
	/**
	 * Get request variable
	 * 
	 * @static
	 * @access	public
	 * @param	string	$name The array key.
	 * @param	mixed	$default The default value used to initialise the variable if null.
	 * @return	mixed
	 * @since	1.0
	 */
	public static function getVar($key, $default='') {
		// Set default value if var is empty
		if (!isset(self::$_URA['request'][$key]) || self::$_URA['request'][$key] == '') {
			// Filter value before assigning to request arrays
			self::$_URA['request'][$key] = self::$_inputfilter->process($default);
		}
		
		return self::$_URA['request'][$key];
	}
	
	/**
	 * Set request variable.
	 * 
	 * @static
	 * @access	public
	 * @param	string	$name The array key.
	 * @param	mixed	$value The value to set the variable to.
	 * @return	void
	 * @since	1.0
	 */
	public static function setVar($key, $value) {
		// Filter value before assigning to request arrays 
		return self::$_URA['request'][$key] = self::$_inputfilter->process($value);
	}
	
	/**
	 * Get $_component.
	 * 
	 * @static
	 * @access	public
	 * @return	component
	 */
	public static function getComponentName() {
		// If component has not been set we return the default value
		if (empty(self::$_component)) {
			self::$_component = 'com_dashboard';
		}
		return self::$_component;
	}
	
	/**
	 * Set $_component.
	 * 
	 * @static
	 * @access	public
	 * @param	string	$value The value to set the variable to.
	 * @return	void
	 */
	public static function setComponentName($value) {
		// Filter value before assigning to variable
		$value = self::$_inputfilter->process($value);
		
		self::$_component = $value;
		
		return $value;
	}
	
	/**
	 * Get $_action
	 * 
	 * @static
	 * @access	public
	 * @return	action
	 */
	public static function getAction() {
		return self::$_action;
	}
	
	/**
	 * Set $_action.
	 * 
	 * @static
	 * @access	public
	 * @param	string	$value The value to set the variable to.
	 * @return	void
	 */
	public static function setAction($value) {
		// Filter value before assigning to variable
		return self::$_action = self::$_inputfilter->process($value);
	}
	
	/**
	 * Destroy the request data
	 * 
	 * @static
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	public static function destroy() {
		self::$_URA = array();
	}
}
?>