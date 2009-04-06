<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	application
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Debug Class
 * 
 * @package		phpFrame
 * @subpackage 	application
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see			phpFrame
 */
class phpFrame_Application_Debug {
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
	 * The private constructor ensures this class is not instantiated and is alwas used statically
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
	public static function display() {
		// Get end time
		self::$_execution_end = self::_microtime_float();
		
		echo '<div style="background-color: white; padding: 20px; font-size: 0.8em; overflow: hidden;">';
		
		echo '<h1 style="font-size: 2em;">Debugger:</h1>';
		// Print execution time
		echo 'Script Execution Time: ' . round(self::$_execution_end - self::$_execution_start, 5) . ' seconds'; 
		
		echo '<h2>Application:</h2>';
		echo '<pre>'; var_dump(phpFrame_Application_Factory::getApplication()); echo '</pre>';
		echo '<hr />';
		
		echo '<h2>Request:</h2>';
		echo '<pre>'; var_dump(phpFrame_Environment_Request::get('request')); echo '</pre>';
		echo '<hr />';
		
		echo '<h2>Config:</h2>';
		echo '<pre>'; var_dump(new config); echo '</pre>';
		echo '<hr />';
		
		echo '<h2>Session:</h2>';
		echo '<pre>'; var_dump(phpFrame_Application_Factory::getSession()); echo '</pre>';
		echo '<hr />';
		
		echo '<h2>User:</h2>';
		echo '<pre>'; var_dump(phpFrame_Application_Factory::getUser()); echo '</pre>';
		echo '<hr />';
		
		global $dependencies;
		echo '<h2>Dependencies:</h2>';
		echo '<pre>'; var_dump($dependencies); echo '</pre>';
		
		echo '</div>';
	}
}
?>