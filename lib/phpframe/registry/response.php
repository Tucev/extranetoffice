<?php
/**
 * @version		$Id$
 * @package		phpFrame_lib
 * @subpackage 	registry
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * Response Class
 * 
 * @package		phpFrame_lib
 * @subpackage 	registry
 * @since 		1.0
 */
class phpFrame_Registry_Response {
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
