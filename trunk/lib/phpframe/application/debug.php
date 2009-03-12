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
 * Debug Class
 * 
 * @package		phpFrame
 * @subpackage 	application
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see			phpFrame
 */
class debug {
	/**
	 * Execution start microtime
	 * 
	 * @var float
	 */
	var $execution_start=null;
	/**
	 * Execution end microtime
	 * 
	 * @var float
	 */
	var $execution_end=null;
	
	/**
	 * Constructor
	 * 
	 * Theis method sets the  execution start time.
	 * 
	 * @return 	void
	 * @since	1.0
	 */
	function __construct() {
		// Get starting time.
		$this->execution_start = $this->microtime_float(); 
	}

	/**
	 * Calculate current microtime
	 * 
	 * @return	float
	 * @since	1.0
	 */
	function microtime_float() {
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
	function display() {
		// Get end time
		$this->execution_end = $this->microtime_float();
		
		echo '<hr />';
		
		echo '<h1>Debugger:</h1>';
		// Print execution time
		echo 'Script Execution Time: ' . round($this->execution_end - $this->execution_start, 5) . ' seconds'; 
		
		$application = factory::getApplication();
		
		$properties = array();
		$properties[] = array('config', 'Gloabl Configuration');
		$properties[] = array('request', 'Request');
		$properties[] = array('client', 'Client');
		$properties[] = array('db', 'Database');
		$properties[] = array('session', 'Session');
		$properties[] = array('auth', 'Auth');
		$properties[] = array('user', 'User');
		$properties[] = array('permissions', 'Permissions');
		//$properties[] = array('modules', 'Modules');
		$properties[] = array('pathway', 'Pathway');
		//$properties[] = array('document', 'Document');
		$properties[] = array('option', 'Option');
		$properties[] = array('component_info', 'Component Info');
		
		foreach ($properties as $property) {
			echo '<h2>'.$property[1].':</h2>';
			echo '<pre>'; var_dump($application->$property[0]); echo '</pre>';
			echo '<hr />';
		}
		
		global $dependencies;
		echo '<h2>Dependencies:</h2>';
		echo '<pre>'; var_dump($dependencies); echo '</pre>';
	}
}
?>