<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	debug
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Log Class
 * 
 * This class still is still work in progress...
 * 
 * @package		phpFrame
 * @subpackage 	debug
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class phpFrame_Debug_Log {
	/**
	 * Write string to log file
	 * 
	 * @static
	 * @access	public
	 * @param	string	$str	The string to append to log file
	 * @return	void
	 */
	public static function write($str) {
		file_put_contents(config::LOG_FILE, $str, FILE_APPEND);
	}
}
?>