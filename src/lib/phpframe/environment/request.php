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
	 * The filtered $_REQUEST array
	 * 
	 * @static
	 * @access	private
	 * @var		array
	 */
	private static $_request=array();
	/**
	 * The filtered $_GET array
	 * 
	 * @static
	 * @access	private
	 * @var		array
	 */
	private static $_get=array();
	/**
	 * The filtered $_POST array
	 * 
	 * @static
	 * @access	private
	 * @var		array
	 */
	private static $_post=array();
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
	public static function init() {
		// Before processing request data we check wheter the request has already been
		// initialised by testing to see if we already have an instance of the input filter
		if (self::$_inputfilter instanceof InputFilter) {
			return;
		}
		
		// Get an instance of PHP Input filter
		self::$_inputfilter = new InputFilter();
		
		// Process incoming request arrays and store filtered data in class
		self::$_request = self::$_inputfilter->process($_REQUEST);
		self::$_get = self::$_inputfilter->process($_GET);
		self::$_post = self::$_inputfilter->process($_POST);
		
		// Once the superglobal request arrays are processed we unset them
		// to prevent them being used from here on
		unset($_REQUEST, $_GET, $_POST);
		
		// Get arguments passed via command line and parse them as request vars
		global $argv;
		for ($i=1; $i<count($argv); $i++) {
			if (preg_match('/^(.*)=(.*)$/', $argv[$i], $matches)) {
				self::$_request[$matches[1]] = $matches[2];
				self::$_post[$matches[1]] = $matches[2];
			}
		}
	}
	
	/**
	 * Get get or post global array
	 * 
	 * @static
	 * @access	public
	 * @param	string	$global_key "get", "post" or "request"
	 * @return	array
	 * @since	1.0
	 */
	public static function get($global_key) {
		switch ($global_key) {
			case 'request' : 
				$array = self::$_request;
				break;
			case 'get' : 
				$array = self::$_get;
				break;
			case 'post' : 
				$array = self::$_post;
				break;
		}
		
		if ($array) {
			return $array;
		}
		else {
			return false;
		}
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
		if (!isset(self::$_request[$key]) || self::$_request[$key] == '') {
			// Filter value before assigning to request arrays
			self::$_request[$key] = self::$_inputfilter->process($default);
		}
		
		return self::$_request[$key];
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
		$value = self::$_inputfilter->process($value);
		
		self::$_request[$key] = $value;
		self::$_post[$key] = $value;
		
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
		self::$_request = array();
		self::$_post = array();
		self::$_get = array();
	}
}
?>