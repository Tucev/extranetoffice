<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	exception
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Exception and Error Handler Class
 * 
 * @package		phpFrame
 * @subpackage 	exception
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class phpFrame_Exception_Handler {
	/**
	 * Initialise the error and exception handlers
	 * 
	 * This method encapsulates set_error_handler() and set_exception_handler().
	 * 
	 * @static
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	public static function init() {
		set_error_handler(array("phpFrame_Exception_Handler", "handleError"));
		set_exception_handler(array('phpFrame_Exception_Handler', 'handleException'));
	}
	
	/**
	 * Restore error and exception handlers to PHP defaults
	 * 
	 * This method encapsulates restore_error_handler() and restore_exception_handler().
	 * 
	 * @static
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	public static function restore() {
		restore_error_handler();
		restore_exception_handler();
	}
	
	/**
	 * Error handler method
	 * 
	 * Handles PHP errors and converts them to exceptions.
	 * 
	 * @static
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	public static function handleError($errno, $errstr, $errfile, $errline, $errcontext) {
		// Throw error as custom exception
		throw new phpFrame_Exception_Error($errstr, $errno, $errfile, $errline, $errcontext);
	}
	
	/**
	 * Exceptions handler
	 * 
	 * Handles uncaught exceptions.
	 * 
	 * @static
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	public static function handleException($exception) {
		//echo 'I am an uncaught exception. Please finish me!!! You can find me in phpFrame_Exception_Handler::handleException().';
		//var_dump($exception);
		echo 'Uncaught exception: '.$exception->getMessage().'<br />';
		echo 'File: '.$exception->getFile().'<br />';
		echo 'Line: '.$exception->getLine().'<br />';
	}
}
?>