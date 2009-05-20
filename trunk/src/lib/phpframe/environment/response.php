<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	environment
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Response Class
 * 
 * @package		phpFrame
 * @subpackage 	environment
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class phpFrame_Environment_Response {
	private $_header=null;
	private $_body=null;
	
	function setHeader($str) {
		$this->_header = $str;
	}
	
	function setBody($str) {
		$this->_body = $str;
	}
	
	function send() {
		echo $this->_body;
		
		if (config::DEBUG) {
			echo '<pre>'.phpFrame_Debug_Profiler::getReport().'</pre>';
		}
	}
}
