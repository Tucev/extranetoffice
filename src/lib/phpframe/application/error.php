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

/**
 * Error Class
 * 
 * @package		phpFrame
 * @subpackage 	application
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class error {
	/**
	 * Display error messges.
	 * 
	 * This method should be called from the template.
	 * 
	 * If no $error array is passed it displays errors stored in session.
	 * 
	 * @param	array	$error An eror array to display. Default is null. 
	 * @return	void
	 * @since 	1.0
	 */
	static function display($error=null) {
		if (!$error) {
			$session =& factory::getSession();
			if ($session->id) {
				$error = $session->getVar('error');	
			}
		}
		
		if (is_array($error) && count($error) > 0) {
			$application =& factory::getApplication();
			foreach ($error as $error_msg) {
				if ($application->client == 'CLI') {
					echo $error_msg->msg."\n";
				}
				else {
					echo '<span class="system_msg_outer">';
					echo '<span class="system_msg '.$error_msg->level.'">'.$error_msg->msg.'</span>';
					echo '</span>';
				}
			}
		}
	}
	
	/**
	 * Raise error and store it in session
	 *
	 * @param	int		$code The error code if any
	 * @param	string	$level Error level. (error, warning, notice, message)
	 * @param	string	$msg The error message
	 * @since 	1.0
	 */
	static function raise($code='', $level='error', $msg) {
		// create standard object holding error
		$error = new standardObject();
		$error->code = $code;
		$error->level = $level;
		$error->msg = $msg;
		
		// Store error in session
		$session =& factory::getSession();
		if (!$session->id) {
			error::display(array($error));
		}
		else {
			$array = $session->getVar('error', array());
			$array[] = $error;
			$session->setVar('error', $array);
		}
		
	}
	
	/**
	 * Raise fatal error, display it and stop execution
	 * 
	 * @param	string	$msg The error message to display 
	 * @return	void
	 * @since 	1.0
	 */
	function raiseFatalError($msg) {
		$error = new standardObject();
		$error->code = 500;
		$error->level = 'error';
		$error->msg = $msg;
		error::display(array($error));
		exit;
	}
}
?>