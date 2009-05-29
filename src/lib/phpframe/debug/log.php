<?php
/**
 * @version		$Id$
 * @package		phpFrame_lib
 * @subpackage 	debug
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Log Class
 * 
 * @package		phpFrame_lib
 * @subpackage 	debug
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
		// Add log info
		$info = "\n";
		$info .= "---";
		$info .= "\n";
		$info .= "[".date("Y-m-d H:i:s")."]";
		$info .= " [ip:".$_SERVER['REMOTE_ADDR']."]";
		$info .= " [client: ".phpFrame::getSession()->getClientName()."]";
		$info .= "\n";
		
		// Write log to filesystem using phpFrame's utility class
		phpFrame_Utils_Filesystem::write(config::LOG_FILE, $info.$str, true);
	}
}
