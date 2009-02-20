<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	environment
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * URI scheme Class
 * 
 * foo://username:password@example.com:8042/over/there/?name=ferret#nose
 * \ /   \________________/\_________/ \__/\_________/  \_________/ \__/
 *  |           |               |        |     |             |       |
 *  |        userinfo       hostname    port  path        query   fragment
 *  |    \_______________________________/
 * scheme              authority
 * 
 * @package		phpFrame
 * @subpackage 	environment
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class uri extends singleton {
	var $uri=null;
	var $scheme=null;
	var $userinfo=null;
	var $hostname=null;
	var $port=null;
	var $path=null;
	var $scriptname=null;
	var $query=array();
	var $fragment=null;
	
	function __construct() {
		$this->parseURI($uri);
	}
	
	function parseURI() {
		// Determine if the request was over SSL (HTTPS)
		if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) {
			$this->scheme = 'https';
		} 
		else {
			$this->scheme = 'http';
		}
			
		$this->hostname = $_SERVER['HTTP_HOST'];
		$this->port = $_SERVER['SERVER_PORT'];
			
		$this->path = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
		$this->scriptname = substr($_SERVER['SCRIPT_NAME'], (strrpos($_SERVER['SCRIPT_NAME'], '/')+1));
			
		$query_pairs = explode('&', $_SERVER["QUERY_STRING"]);
		foreach ($query_pairs as $pair) {
			$pair_array = explode('=', $pair);
			$this->query[$pair_array[0]] = $pair_array[1];
		}
		
		// Store URI string in instance
		$this->uri = $this->scheme.'://'.$this->hostname;
		if (($this->scheme == 'http' && $this->port != 80) || ($this->scheme == 'https' && $this->port != 443)) {
			$this->uri .= ':'.$this->port;	
		}
		$this->uri .= $this->path.'/'.$this->scriptname.'?'.$_SERVER["QUERY_STRING"];
	}
	
	function getBase() {
		return $this->scheme.'://'.$this->hostname.$this->path.'/';
	}
}
?>
