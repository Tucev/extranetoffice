<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	utils
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Rewrite Class
 * 
 * @package		phpFrame
 * @subpackage 	utils
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class phpFrame_Utils_Rewrite {
	public static function rewriteRequest() {
		//var_dump($_SERVER); exit;
		// Get path to script
		$path = substr($_SERVER['SCRIPT_NAME'], 0, (strrpos($_SERVER['SCRIPT_NAME'], '/')+1));
		
		// If the script name doesnt appear in the request URI we need to rewrite
		if (strpos($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME']) === false
			&& $_SERVER['REQUEST_URI'] != $path
			&& $_SERVER['REQUEST_URI'] != $path."index.php") {
			// Remove path from request uri. 
			// This gives us the component and action expressed as directories
			if ($path != "/") {
				$params = str_replace($path, "", $_SERVER['REQUEST_URI']);
			}
			
			//preg_match('/^([a-zA-Z]+)\/?([a-zA-Z_]+)?\/?.*$/', $params, $matches);
			
			// Get component name using regex
			preg_match('/^([a-zA-Z]+)/', $params, $component_matches);
			
			// Get action name using regex
			preg_match('/^[a-zA-Z]+\/([a-zA-Z_]+)/', $params, $action_matches);
			
			if (isset($component_matches[1]) && !empty($component_matches[1])) {
				$component = "com_".$component_matches[1];
				if (isset($action_matches[1])) {
					$action = $action_matches[1];
				}

				// Prepend component and action to query string
				$rewritten_query_string = "component=".$component;
				if (!empty($action)) $rewritten_query_string .= "&action=".$action;
				if (!empty($_SERVER['QUERY_STRING'])) {
					$rewritten_query_string .= "&".$_SERVER['QUERY_STRING'];
				}
				$_SERVER['QUERY_STRING'] = $rewritten_query_string;
				
				// Update request uri
				$_SERVER['REQUEST_URI'] = $path."index.php?".$_SERVER['QUERY_STRING'];
				
				// Set vars in _REQUEST array
				if (!empty($component)) {
					$_REQUEST['component'] = $component;
					$_GET['component'] = $component;
				}
				if (!empty($action)) {
					$_REQUEST['action'] = $action;
					$_GET['action'] = $action;	
				}
			}
		}
	}
	
	public static function rewriteURL($url) {
		$uri = phpFrame::getURI();
		
		if (strpos($url, 'http') != 0) {
			$url = $uri->getBase().$url;
		}
		
		// Parse URL string
		$url_array = parse_url($url);
		parse_str($url_array['query'], $query_array);
		
		// If there are no query parameters we don't need to rewrite anything
		if (count($query_array) == 0) return $url;
		
		$rewritten_url = "";
		
		if (!empty($query_array['component'])) {
			$rewritten_url .= substr($query_array['component'], 4);
			unset($query_array['component']);
		}
		
		if (!empty($query_array['action'])) {
			$rewritten_url .= "/".$query_array['action'];
			unset($query_array['action']);
		}
		
		if (is_array($query_array) && count($query_array) > 0) {
			$rewritten_url .= "?";
			$i=0;
			foreach ($query_array as $key=>$value) {
				if ($i>0) $rewritten_url .= "&"; 
				$rewritten_url .= $key."=".$value;
				$i++;
			}
		}
		
		return $uri->getBase().$rewritten_url;
	}
}
