<?php
/**
 * @version		$I$
 * @package		phpFrame
 * @subpackage 	utils
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Filter Class
 * 
 * This class requires that PHPs filter functions are available.
 * 
 * More info avout PHP Filter:
 * http://uk.php.net/manual/en/book.filter.php
 * 
 * @package		phpFrame
 * @subpackage 	utils
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class filter {
	/**
	 * Validate data stored in variable
	 * 
	 * @param $variable
	 * @param $type int, boolean, float, regexp, url, email, ip
	 * @return unknown_type
	 */
	static function validate($variable, $type='default') {
		// Check that passed type is recognised
		$allowed_types = array("default", "int", "boolean", "float", "regexp", "url", "email", "ip");
		if (!in_array($type, $allowed_types)) {
			error::raise('', 'warning', 'phpFrame Filter error: Data type not recognised by filter.');
			return false;
		}
		
		// Make filter constant using passed type
		if ($type == 'default') {
			$filter = FILTER_DEFAULT;
		}
		else {
			$filter = "FILTER_VALIDATE_".strtoupper($type);
			eval("\$filter = $filter;");	
		}
		
		return filter_var($variable, $filter);
	}
}
?>