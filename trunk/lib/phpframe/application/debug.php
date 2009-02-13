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

class debug {
	var $execution_start=null;
	var $execution_end=null;
	
	function __construct() {
		// Get starting time.
		$this->execution_start = $this->microtime_float(); 
	}
	
	// Function to calculate script execution time.
	function microtime_float() {
	    list ($msec, $sec) = explode(' ', microtime());
	    $microtime = (float)$msec + (float)$sec;
	    return $microtime;
	}
	
	function display() {
		// Get end time
		$this->execution_end = $this->microtime_float();
		
		echo '<hr />';
		
		echo '<h1>Debugger:</h1>';
		// Print execution time
		echo 'Script Execution Time: ' . round($this->execution_end - $this->execution_start, 5) . ' seconds'; 
		
		$application = factory::getApplication();
		echo '<h2>Application:</h2>';
		echo '<pre>'; var_dump($application); echo '</pre>';
		
		global $dependencies;
		echo '<h2>Dependencies:</h2>';
		echo '<pre>'; var_dump($dependencies); echo '</pre>';
	}
}
?>