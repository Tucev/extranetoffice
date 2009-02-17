<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	application
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

class error {
	function display() {
		$session =& factory::getSession();
		$error = $session->getVar('error');
		if (is_array($error) && count($error) > 0) {
			foreach ($error as $error_msg) {
				echo '<span class="system_msg_outer">';
				echo '<span class="system_msg '.$error_msg->level.'">'.$error_msg->msg.'</span>';
				echo '</span>';
			}
			echo '<br />';
		}
	}
	
	/**
	 * Raise error and store it in session
	 *
	 * @param int $code The error code if any
	 * @param string $level Error level. (error, warning, notice, message)
	 * @param string $msg The error message
	 */
	function raise($code='', $level='error', $msg) {
		// create standard object holding error
		$error = new stdClass();
		$error->code = $code;
		$error->level = $level;
		$error->msg = $msg;
		
		// Store error in session
		$session =& factory::getSession();
		$array = $session->getVar('error', array());
		$array[] = $error;
		$session->setVar('error', $array);
	}
}
?>