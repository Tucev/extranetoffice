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
 * <code>
 *  class Exception {
 *  protected $message = 'Unknown exception';   // exception message
 *  protected $code = 0;                        // user defined exception code
 *  protected $file;                            // source filename of exception
 *  protected $line;                            // source line of exception
 *  
 *  function __construct($message = null, $code = 0);
 *  
 *  final function getMessage();                // message of exception 
 *  final function getCode();                   // code of exception
 *  final function getFile();                   // source filename
 *  final function getLine();                   // source line
 *  final function getTrace();                  // an array of the backtrace()
 *  final function getTraceAsString();          // formatted string of trace
 *  
 *  // Overrideable
 *  function __toString();                       // formatted string for display
 *  }
 *  </code>
 * 
 * @package		phpFrame
 * @subpackage 	exception
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class phpFrame_Exception extends Exception {
	// This are PHPs error code constants
	const E_ERROR=1;
	const E_WARNING=2;
	const E_PARSE=4;
	const E_NOTICE=8;
	const E_CORE_ERROR=16;
	const E_CORE_WARNING=32;
	const E_COMPILE_ERROR=64;
	const E_COMPILE_WARNING=128;
	const E_USER_ERROR=256;
	const E_USER_WARNING=512;
	const E_USER_NOTICE=1024;
	const E_STRICT=2048;
	const E_RECOVERABLE_ERROR=4096;
	const E_DEPRECATED=8192;
	const E_USER_DEPRECATED=16384;
	
	protected $_severity;

	function __construct($message=null, $code=self::E_USER_ERROR, $verbose='') {
		// Construct parent class to build Exception 
		parent::__construct($message, $code);
		
		// Log the exception to file if needed
		if ($code < config::LOG_LEVEL) {
			//phpFrame_Debug_Log::write($this->__toString(true));
		}
		
		switch ($code) {
			case self::E_ERROR :
			case self::E_USER_ERROR :
				$this->_severity = 'error';
				echo '<pre>'.$this->__toString();
				exit;
				break;
				
			case self::E_WARNING :
			case self::E_USER_WARNING :
				$this->_severity = 'warning';
				break;
				
			case self::E_NOTICE :
			case self::E_USER_NOTICE :
				$this->_severity = 'notice';
				break;
				
			case self::E_STRICT :
			case self::E_DEPRECATED :
			case self::E_USER_DEPRECATED :
				$this->_severity = 'strict';
				break;
		}
		
	}
	
	public function getSeverity() {
		return $this->_severity;
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