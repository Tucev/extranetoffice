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
 * Exception Class
 * 
 * Extends PHP's built in Exception.
 * 
 *  class Exception
{
    protected $message = 'Unknown exception';   // exception message
    protected $code = 0;                        // user defined exception code
    protected $file;                            // source filename of exception
    protected $line;                            // source line of exception

    function __construct($message = null, $code = 0);

    final function getMessage();                // message of exception 
    final function getCode();                   // code of exception
    final function getFile();                   // source filename
    final function getLine();                   // source line
    final function getTrace();                  // an array of the backtrace()
    final function getTraceAsString();          // formatted string of trace

    // Overrideable
    function __toString();                       // formatted string for display
}
 * 
 * @package		phpFrame
 * @subpackage 	exception
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class phpFrame_Exception extends Exception {
	const FATAL_ERROR=0;
	const ERROR=1;
	const WARNING=2;
	const NOTICE=3;
	
	function __construct($message=null, $code=0, $verbose='') {
		// Construct parent class to build Exception 
		parent::__construct($message, $code);
		
		// Log the exception to file if needed
		if ($code < config::LOG_LEVEL) {
			//phpFrame_Debug_Log::write($this->__toString(true));
		}
		
		switch ($code) {
			case self::FATAL_ERROR :
				
				break;
			case self::ERROR :
				
				break;
			case self::WARNING :
				
				break;
			case self::NOTICE :
				
				break;
		}
		
	}
	
	function __toString($verbose=false) {
		if ($verbose) {
			return parent::__toString();
		}
		else {
			//return 'hello world';
			return parent::__toString();
			//return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
		}
	}
}
?>