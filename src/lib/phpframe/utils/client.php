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
 * Client Class
 * 
 * @package		phpFrame
 * @subpackage 	utils
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class phpFrame_Utils_Client {
	/**
	 * Check whether current client is PHP "Command Line Client" (CLI)
	 * 
	 * @return	boolean
	 */
	public static function isCLI() {
		// The CLI constant is set in bootstrap index.php file
		if (CLI) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Check whether current client is mobile
	 * 
	 * @return	boolean
	 * @since 	1.0
	 */
	public static function isMobile() {

		if (isset($_SERVER["HTTP_X_WAP_PROFILE"])) {
			return true;
		}
		
		if (preg_match("/wap\.|\.wap/i",$_SERVER["HTTP_ACCEPT"])) { 
			return true;
		}
		
		if (isset($_SERVER["HTTP_USER_AGENT"])) {
		
			if (preg_match("/Creative\ AutoUpdate/i",$_SERVER["HTTP_USER_AGENT"])) {
				return false;
			}
		
			$uamatches = array("midp", "j2me", "avantg", "docomo", "novarra", "palmos", "palmsource", "240x320", "opwv", "chtml", "pda", "windows\ ce", "mmp\/", "blackberry", "mib\/", "symbian", "wireless", "nokia", "hand", "mobi", "phone", "cdm", "up\.b", "audio", "SIE\-", "SEC\-", "samsung", "HTC", "mot\-", "mitsu", "sagem", "sony", "alcatel", "lg", "erics", "vx", "NEC", "philips", "mmm", "xx", "panasonic", "sharp", "wap", "sch", "rover", "pocket", "benq", "java", "pt", "pg", "vox", "amoi", "bird", "compal", "kg", "voda", "sany", "kdd", "dbt", "sendo", "sgh", "gradi", "jb", "\d\d\di", "moto");
		
			foreach ($uamatches as $uastring) {
				if (preg_match("/".$uastring."/i",$_SERVER["HTTP_USER_AGENT"])) {
					return true;
				}
			}
		
		}
		return false;
	}
}
?>