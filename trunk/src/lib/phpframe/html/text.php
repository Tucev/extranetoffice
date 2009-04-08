<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage	html
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Text Class
 * 
 * @package		phpFrame
 * @subpackage 	html
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class phpFrame_HTML_Text {
	/**
	 * Format a string for html output.
	 * 
	 * @todo	This function needs to be written. It does nothing at the moment.
	 * @param	string	$str
	 * @param	bool	$javascript_safe
	 * @return	string
	 */
	public static function _($str, $javascript_safe=false) {
		return $str;
	}
	
	/**
	 * Format bytes to human readable.
	 * 
	 * @param	string	$str
	 * @return	string
	 * @since 	1.0
	 */
	function bytes($str) {
	    $unim = array("B","KB","MB","GB","TB","PB");
	    $c = 0;
	    while ($str>=1024) {
	        $c++;
	        $str = $str/1024;
	    }
	    return number_format($str,($c ? 2 : 0),",",".")." ".$unim[$c];
	}
	
	/**
	 * Limit the number of words.
	 * 
	 * @param	string	$str
	 * @param	int		$max_chars
	 * @param	bool	$add_trailing_dots
	 * @return	string
	 */
	function limit_words($str, $max_chars, $add_trailing_dots=true) {
		if (strlen($str) > $max_chars) {
			$str = substr($str, 0, $max_chars);
			$str = substr($str, 0, strrpos($str, " "));
			if ($add_trailing_dots === true) $str .= " ...";
		}
		
		return $str;
	}
}
?>