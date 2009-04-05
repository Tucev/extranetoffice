<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	environment
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );
	
/**
 * Request Class
 * 
 * @todo		This class needs to be rewritten to use php's filter extension
 * @package		phpFrame
 * @subpackage 	environment
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class phpFrame_Environment_Request {
	private static $_request=array();
	private static $_get=array();
	private static $_post=array();
	
	/**
	 * Constructor
	 * 
	 * Initialise the request, filter input and return the request array.
	 * 
	 * @return	array
	 * @since	1.0
	 */
	public static function init() {
		$inputfilter = new InputFilter();
		
		// Process incoming request arrays and store filtered data in class
		self::$_request = $inputfilter->process($_REQUEST);
		self::$_get = $inputfilter->process($_GET);
		self::$_post = $inputfilter->process($_POST);
		
		// Get arguments passed via command line and parse them as request vars
		global $argv;
		if (is_array($argv) && count($argv) > 0) {
			foreach ($argv as $pair) {
				$pair_array = explode('=', $pair);
				self::$_request[$pair_array[0]] = $pair_array[1];
				self::$_post[$pair_array[0]] = $pair_array[1];
			}
			define('CLI', true);
		} 
		else {
		    define('CLI', false); 
		}
	}
	
	/**
	 * Get get or post global array
	 * 
	 * @param	string	$global_key "get", "post" or "request"
	 * @return	array
	 */
	public static function get($global_key) {
		switch ($global_key) {
			case 'get' : 
				$array = self::$_get;
				break;
			case 'post' : 
				$array = self::$_post;
				break;
			case 'request' : 
				$array = self::$_request;
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
	 * @param	string	$name The array key.
	 * @param	mixed	$default The default value used to initialise the variable if null.
	 * @return	mixed
	 * @since	1.0
	 */
	public static function getVar($key, $default='') {
		$inputfilter = new InputFilter();
		
		// Set default value if var is empty
		if (!isset(self::$_request[$key]) || self::$_request[$key] == '') {
			self::$_request[$key] = $inputfilter->process($default);
		}
		
		return self::$_request[$key];
	}
	
	/**
	 * Set request variable.
	 * 
	 * @param	string	$name The array key.
	 * @param	mixed	$value The value to set the variable to.
	 * @return	void
	 * @since	1.0
	 */
	public static function setVar($key, $value) {
		$inputfilter = new InputFilter();
		$value = $inputfilter->process($value);
		
		self::$_request[$key] = $value;
		self::$_post[$key] = $value;
		
		return $value;
	}
	
}
?>