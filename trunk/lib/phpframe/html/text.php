<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage	html
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

class text {
	function _($str) {
		return $str;
	}
	
	function bytes($a) {
	    $unim = array("B","KB","MB","GB","TB","PB");
	    $c = 0;
	    while ($a>=1024) {
	        $c++;
	        $a = $a/1024;
	    }
	    return number_format($a,($c ? 2 : 0),",",".")." ".$unim[$c];
	}
	
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