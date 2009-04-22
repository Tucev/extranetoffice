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
 * @todo		This class needs to be rewritten to use php's filter extension
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
	private static $_component;
	/**
	 * A core request variable specifying controller/component functino to execute
	 * 
	 * @static
	 * @access	private
	 * @var		string
	 */
	private static $_action;
	/**
	 * A core request variable specifying which view to render
	 * 
	 * @static
	 * @access	private
	 * @var		string
	 */
	private static $_view;	
	/**
	 * A core request variable specifying which view layout to use
	 * 
	 * @static
	 * @access	private
	 * @var		string
	 */
	private static $_layout;
	
	/**
	 * Instance of PHPInputFilter
	 * 
	 * @static
	 * @access	private
	 * @var		object
	 */
	private static $_inputfilter=null;
	
	/**
	 * Private constructor prevents instantiation. This class should always be used statically.
	 * 
	 * @access	private
	 * @return	void
	 * @since	1.0
	 */
	private function __construct() {}
	
	/**
	 * Constructor
	 * 
	 * Initialise the request, filter input and return the request array.
	 * 
	 * @static
	 * @access	public
	 * @return	array
	 * @since	1.0
	 */
	public static function init($client='http') {
		// Before processing request data we check wheter the request has already been
		// initialised by testing to see if $_URA is populated
		if (!empty(self::$_URA)) {
			return;
		}
		
		//TODO create instance of appropriate request helper to populate $_URA
		//TODO stop ignoring $_SESSION and $_COOKIES
		
		if ($client == 'http') {
			// Get an instance of PHP Input filter
			self::$_inputfilter = new InputFilter();
			
			// Process incoming request arrays and store filtered data in class
			self::$_URA['request'] = self::$_inputfilter->process($_REQUEST);
			self::$_URA['get'] = self::$_inputfilter->process($_GET);
			self::$_URA['post'] = self::$_inputfilter->process($_POST);
			
			// Once the superglobal request arrays are processed we unset them
			// to prevent them being used from here on
			unset($_REQUEST, $_GET, $_POST);
		}
		
		if ($client == 'cli') {
			// Get arguments passed via command line and parse them as request vars
			global $argv;
			$request = array();
			for ($i=1; $i<count($argv); $i++) {
				if (preg_match('/^(.*)=(.*)$/', $argv[$i], $matches)) {
					$request[$matches[1]] = $matches[2];
				}
			}
			self::$_URA['request'] = $request;
		}
		
		//add other globals
		self::$_URA['env'] = $_ENV;
		self::$_URA['server'] = $_SERVER;
		self::$_URA['files'] = $_FILES;
		
		//TODO possible optimisation, see following:
		/*
		foreach (self::$_core_variables as $key) {
			$core_variable_name = '_'.$key;
			//self::$$core_variable_name = self::$_URA['request'][$key];
			self::$$core_variable_name = self::$_URA['request'][$key];
			unset(self::$_URA['request'][$key]);
		}
		*/
		
		//rip core variables from URA
		self::$_component = self::$_URA['request']['component'];
		unset(self::$_URA['request']['component']);
		self::$_action = self::$_URA['request']['action'];
		unset(self::$_URA['request']['action']);
		self::$_view = self::$_URA['request']['view'];
		unset(self::$_URA['request']['view']);
		self::$_layout = self::$_URA['request']['layout'];
		unset(self::$_URA['request']['layout']);
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
		if (!isset(self::$_URA[$key]) || self::$_URA[$key] == '') {
			// Filter value before assigning to request arrays
			self::$_URA[$key] = self::$_inputfilter->process($default);
		}
		
		return self::$_URA[$key];
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
	public static function getComponent() {
		
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
	public static function setComponent($value) {
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
		$value = self::$_inputfilter->process($value);
		
		self::$_action = $value;
		
		return $value;
	}
	/**
	 * Get $_view
	 * 
	 * @static
	 * @access	public
	 * @return	action
	 */
	public static function getView() {
		
		return self::$_view;
	}
	/**
	 * Set $_view.
	 * 
	 * @static
	 * @access	public
	 * @param	string	$value The value to set the variable to.
	 * @return	void
	 */
	public static function setView($value) {
		// Filter value before assigning to variable
		$value = self::$_inputfilter->process($value);
		
		self::$_view = $value;
		
		return $value;
	}
	/**	
	 * Get $_layout
	 * 
	 * @static
	 * @access	public
	 * @return	layout
	 */
	public static function getLayout() {
		
		return self::$_layout;
	}
	/**
	 * Set $_layout.
	 * 
	 * @static
	 * @access	public
	 * @param	string	$value The value to set the variable to.
	 * @return	void
	 */
	public static function setLayout($value) {
		// Filter value before assigning to variable
		$value = self::$_inputfilter->process($value);
		
		self::$_layout = $value;
		
		return $value;
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