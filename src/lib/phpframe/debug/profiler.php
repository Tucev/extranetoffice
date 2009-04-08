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
 * Profiler Class
 * 
 * This class still needs to be fleshed out.
 * 
 * @package		phpFrame
 * @subpackage 	debug
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class phpFrame_Debug_Profiler {
	/**
	 * Execution start microtime
	 * 
	 * @var float
	 */
	private static $_execution_start=null;
	/**
	 * Execution end microtime
	 * 
	 * @var float
	 */
	private static $_execution_end=null;
	
	/**
	 * Constructor
	 * 
	 * The private constructor ensures this class is not instantiated and is alwas used statically.
	 * 
	 * @return void
	 */
	private function __construct() {}
	
	/**
	 * Initialise debugger
	 * 
	 * Theis method sets the execution start time.
	 * 
	 * @return 	void
	 * @since	1.0
	 */
	static function init() {
		// Get starting time.
		self::$_execution_start = self::_microtime_float(); 
	}

	/**
	 * Calculate current microtime
	 * 
	 * @return	float
	 * @since	1.0
	 */
	private static function _microtime_float() {
	    list ($msec, $sec) = explode(' ', microtime());
	    $microtime = (float)$msec + (float)$sec;
	    return $microtime;
	}
	
	/**
	 * Display the debugger's output
	 * 
	 * @return	void
	 * @since	1.0
	 */
	public static function getExecutionTime() {
		// Get end time
		self::$_execution_end = self::_microtime_float();
		
		// Return difference between start and end times
		return round(self::$_execution_end - self::$_execution_start, 5);
	}
}
?>