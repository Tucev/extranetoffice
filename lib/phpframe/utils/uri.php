<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	utils
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * URI scheme Class
 * 
 * <pre>
 * foo://username:password@example.com:8042/over/there/?name=ferret#nose
 * \_/ 	 \________________/\_________/ \__/\_________/  \_________/ \__/
 *  |           |               |        |     |             |       |
 *  |        userinfo       hostname    port  path        query   fragment
 *  |    \_______________________________/
 * scheme              authority
 * </pre>
 * 
 * @package		phpFrame
 * @subpackage 	utils
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class uri extends standardObject {
	/**
	 * The URI string
	 * 
	 * @var string
	 */
	var $uri=null;
	/**
	 * The URI scheme
	 * 
	 * ie: http, https, ftp, ...
	 * 
	 * @var string
	 */
	var $scheme=null;
	/**
	 * The part of the URI string containing the user and password if any
	 * 
	 * @var string
	 */
	var $userinfo=null;
	/**
	 * The host name
	 * 
	 * @var string
	 */
	var $hostname=null;
	/**
	 * Port number
	 * 
	 * @var int
	 */
	var $port=null;
	/**
	 * The server path / directory
	 * 
	 * @var string
	 */
	var $path=null;
	/**
	 * The sript name / file name
	 * 
	 * @var string
	 */
	var $scriptname=null;
	/**
	 * An array containing the query string's name/value pairs.
	 * 
	 * @var array
	 */
	var $query=array();
	/**
	 * The fragment part of the URI
	 * 
	 * @var string
	 */
	var $fragment=null;
	
	/**
	 * Constructor
	 * 
	 * This method initialises the object by invoking parseURI(). 
	 * If no URI is passed the current request's URI will be used.
	 * 
	 * @param	string	$uri The URI string.
	 * @return 	void
	 * @since	1.0
	 */
	function __construct($uri='') {
		if (empty($uri)) {
			$uri = $this->getURI();
		}
		
		$this->parseURI($uri);
	}
	
	/**
	 * Get the URI string
	 * 
	 * If the uri property has been set it returns its value, otherwise it gets the current request's URL.
	 * 
	 * @return 	string
	 * @since	1.0
	 */
	function getURI() {
		if (!empty($this->uri)) {
			return $this->uri;
		}
		else {
			// Determine if the request was over SSL (HTTPS)
			if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) {
				$scheme = 'https';
			} 
			else {
				$scheme = 'http';
			}
			
			$uri = $scheme.'://'.$_SERVER['HTTP_HOST'];
			if (($scheme == 'http' && $_SERVER['SERVER_PORT'] != 80) || ($scheme == 'https' && $_SERVER['SERVER_PORT'] != 443)) {
				$uri .= ':'.$_SERVER['SERVER_PORT'];	
			}
			$uri .= $_SERVER["REQUEST_URI"];
			
			return $uri;
		}
	}
	
	/**
	 * Parse URI
	 * 
	 * This method parses the passed URI and sets the object's properties accordingly.
	 * 
	 * @todo 	Method needs to be able to parse userinfo and fragment. At the moment it doesn't.
	 * @param	string	$uri The URI to parse
	 * @return	void
	 * @since	1.0
	 */
	function parseURI($uri) {
		// Explode URI string to get scheme
		$array = explode('://', $uri);
		$this->scheme = $array[0];
		
		// Get host from remaining part of the URI string
		$this->hostname = substr($array[1], 0, strpos($array[1], '/'));
		
		// Get port number
		if (strpos($this->hostname, ':') !== false) {
			$host_array = explode(':', $this->hostname);
			$this->hostname = $host_array[0];
			$this->port = (int) $host_array[1];
		}
		elseif ($this->scheme == 'http') {
			$this->port = (int) 80;
		}
		elseif ($this->scheme == 'https') {
			$this->port = (int) 443;
		}
		
		// Get server path
		$remaining_uri_to_parse = substr($array[1], strpos($array[1], '/'));
		$this->path = substr($remaining_uri_to_parse, 0, strrpos($remaining_uri_to_parse, '/'));
		
		// Get script name
		$remaining_uri_to_parse = substr($remaining_uri_to_parse, strrpos($remaining_uri_to_parse, '/'));
		$this->scriptname = substr($remaining_uri_to_parse, 1, (strpos($remaining_uri_to_parse, '?')-1));
		
		// Get query string
		$remaining_uri_to_parse = substr($remaining_uri_to_parse, (strpos($remaining_uri_to_parse, '?')+1));
		$query_pairs = explode('&', $remaining_uri_to_parse);
		foreach ($query_pairs as $pair) {
			$pair_array = explode('=', $pair);
			$this->query[$pair_array[0]] = $pair_array[1];
		}
		
		// Store URI string in instance
		$this->uri = $this->scheme.'://'.$this->hostname;
		if (($this->scheme == 'http' && $this->port != 80) || ($this->scheme == 'https' && $this->port != 443)) {
			$this->uri .= ':'.$this->port;	
		}
		$this->uri .= $this->path.'/'.$this->scriptname.'?'.$remaining_uri_to_parse;
	}
	
	/**
	 * Get base URL
	 * 
	 * This method retrieves the base URL for the current state of the URI object.
	 * 
	 * @return	string
	 * @since	1.0
	 */
	function getBase() {
		if ($this->scheme && $this->hostname) {
			$base = $this->scheme.'://'.$this->hostname;
			if (($this->scheme == 'http' && $this->port != 80) || ($this->scheme == 'https' && $this->port != 443)) {
				$base .= ':'.$this->port;	
			}
			$base .= $this->path.'/';
			return $base;	
		}
		else {
			return false;
		}
	}
}
?>
