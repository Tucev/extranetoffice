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
 * Client used by default (PC HTTP browsers or anything for which no helper exists) 
 * 
 * @todo		
 * @package		
 * @subpackage 	
 * @author 		
 * @since 		
 */
class phpFrame_Environment_ClientDefault implements phpFrame_Environment_IClient {
		
	/**
	 * Check if this is the correct helper for the client being used
	 * 
	 * @static
	 * @access	public
	 * @return	object instance of this class  
	 */
	public static function detect() {
		
		//TODO test checking for $_SERVER.HTTP_USER_AGENT
		
		//this is our last hope to find a helper, just return instance
		return new self;
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
		return "default";
	}
}

?>