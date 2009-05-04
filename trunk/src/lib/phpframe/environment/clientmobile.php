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
 * Client for Mobile Devices
 * 
 * @todo		
 * @package		
 * @subpackage 	
 * @author 		
 * @since 		
 */
class phpFrame_Environment_ClientMobile implements phpFrame_Environment_IClient {
		
	/**
	 * Check if this is the correct helper for the client being used
	 * 
	 * @static
	 * @access	public
	 * @return	mixed object instance of this class if correct helper for client or false otherwise
	 */
	public static function detect() {
		
		if (isset($_SERVER["HTTP_X_WAP_PROFILE"])) {
			return new self;
		}
		
		if (preg_match("/wap\.|\.wap/i",$_SERVER["HTTP_ACCEPT"])) { 
			return new self;
		}
		
		if (isset($_SERVER["HTTP_USER_AGENT"])) {
		
			if (preg_match("/Creative\ AutoUpdate/i",$_SERVER["HTTP_USER_AGENT"])) {
				return new self;
			}
		
			$uamatches = array("midp", "j2me", "avantg", "docomo", "novarra", "palmos", "palmsource", "240x320", "opwv", "chtml", "pda", "windows\ ce", "mmp\/", "blackberry", "mib\/", "symbian", "wireless", "nokia", "hand", "mobi", "phone", "cdm", "up\.b", "audio", "SIE\-", "SEC\-", "samsung", "HTC", "mot\-", "mitsu", "sagem", "sony", "alcatel", "lg", "erics", "vx", "NEC", "philips", "mmm", "xx", "panasonic", "sharp", "wap", "sch", "rover", "pocket", "benq", "java", "pt", "pg", "vox", "amoi", "bird", "compal", "kg", "voda", "sany", "kdd", "dbt", "sendo", "sgh", "gradi", "jb", "\d\d\di", "moto");
		
			foreach ($uamatches as $uastring) {
				if (preg_match("/".$uastring."/i",$_SERVER["HTTP_USER_AGENT"])) {
					return new self;
				}
			}
		
		}
		return false;
	}
	
	/**	
	 * Populate the Unified Request array
	 * 
	 * @access	public
	 * @return	Unified Request Array
	 */
	public function populateURA() {
	
		$request = array();
		
		// Get an instance of PHP Input filter
		$inputfilter = new InputFilter();
			
		// Process incoming request arrays and store filtered data in class
		$request['request'] = $inputfilter->process($_REQUEST);
		$request['get'] = $inputfilter->process($_GET);
		$request['post'] = $inputfilter->process($_POST);
			
		// Once the superglobal request arrays are processed we unset them
		// to prevent them being used from here on
		unset($_REQUEST, $_GET, $_POST);
		
		return $request;
	}
	
	/**	
	 * Get helper name
	 * 
	 * @access	public
	 * @return	string name to identify helper type
	 */
	public function getName() {
		return "mobile";
	}
	
	public function preActionHook() {}
	
	public function renderView($data) {}
	
	public function renderTemplate(&$str) {}
}
