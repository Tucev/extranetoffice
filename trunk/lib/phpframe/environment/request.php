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

require_once 'lib/phpinputfilter/inputfilter.php';

/**
 * Request Class
 * 
 * @todo		This class needs to be rewritten to use php's filter extension and to extend singleton
 * @package		phpFrame
 * @subpackage 	environment
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class request {
	/**
	 * Initialise the request, filter input and return the request array.
	 * 
	 * @return	array
	 * @since	1.0
	 */
	function init() {
		$inputfilter = new InputFilter();
		return $inputfilter->process($_REQUEST);
	}
	
	/**
	 * Get get or post global array
	 * 
	 * @param	string	$global_key "get" or "post"
	 * @return	array
	 */
	function get($global_key) {
		$inputfilter = new InputFilter();
		
		switch ($global_key) {
			case 'get' : 
				return $inputfilter->process($_GET);
				break;
			case 'post' : 
				return $inputfilter->process($_POST);
				break;
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
	function getVar($key, $default='') {
		if (empty($GLOBALS['application']->request[$key])) {
			$GLOBALS['application']->request[$key] = $default;
		}
		return $GLOBALS['application']->request[$key];
	}
	
	/**
	 * Set request variable.
	 * 
	 * @param	string	$name The array key.
	 * @param	mixed	$value The value to set the variable to.
	 * @return	void
	 * @since	1.0
	 */
	function setVar($key, $value) {
		$inputfilter = new InputFilter();
		$GLOBALS['application']->request[$key] = $inputfilter->process($value);
	}
	
}
?>