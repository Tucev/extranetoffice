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
 * Client Interface
 * 
 * @todo		
 * @package		
 * @subpackage 	
 * @author 		
 * @since 		
 */
interface phpFrame_Environment_IClient {
		
	/**
	 * Check if this is the correct helper for the client being used and returns instance if so
	 * 
	 * @static
	 * @access	public
	 * @return	object instance of this class
	 * @since	1.0
	 */
	public static function detect();
	
	/**	
	 * Populate a Unified Request Array to return
	 * 
	 * @access	public
	 * @return	array generated to form unified request array
	 */
	public function populateURA();
	
	/**	
	 * Get helper name
	 * 
	 * @access	public
	 * @return	string name to identify helper type
	 */
	public function getName();
}

?>