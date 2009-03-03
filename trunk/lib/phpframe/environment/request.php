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

require_once _ABS_PATH.DS.'lib'.DS.'phpinputfilter'.DS.'inputfilter.php';
/**
 * PHP input filter object
 * 
 * @var object
 */
$inputfilter = new InputFilter();
	
/**
 * Request Class
 * 
 * @todo		This class needs to be rewritten to use php's filter extension
 * @package		phpFrame
 * @subpackage 	environment
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class request {
	/**
	 * Constructor
	 * 
	 * Initialise the request, filter input and return the request array.
	 * 
	 * @return	array
	 * @since	1.0
	 */
	static function init() {
		global $inputfilter;
		
		// Process incoming request arrays and store filtered data in class
		$_REQUEST = $inputfilter->process($_REQUEST);
		$_GET = $inputfilter->process($_GET);
		$_POST = $inputfilter->process($_POST);
	}
	
	/**
	 * Get get or post global array
	 * 
	 * @param	string	$global_key "get" or "post"
	 * @return	array
	 */
	static function get($global_key) {
		$array_name = "\$_".strtoupper($global_key);
		eval("\$array =& ".$array_name.";");
		
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
	static function getVar($key, $default='') {
		global $inputfilter;
		
		// Set default value if var is empty
		if (empty($_REQUEST[$key])) {
			$_REQUEST[$key] = $inputfilter->process($default);
		}
		
		return $_REQUEST[$key];
	}
	
	/**
	 * Set request variable.
	 * 
	 * @param	string	$name The array key.
	 * @param	mixed	$value The value to set the variable to.
	 * @return	void
	 * @since	1.0
	 */
	static function setVar($key, $value) {
		global $inputfilter;
		
		$_REQUEST[$key] = $inputfilter->process($value);
	}
	
}
?>