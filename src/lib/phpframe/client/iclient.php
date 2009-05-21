<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	client
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );
	
/**
 * Client Interface
 * 
 * @package		phpFrame
 * @subpackage 	client
 * @since 		1.0		
 */
interface phpFrame_Client_IClient {
		
	/**
	 * Check if this is the correct helper for the client being used and returns instance if so
	 * 
	 * @static
	 * @access	public
	 * @return	phpFrame_Client_IClient|boolean
	 * @since	1.0
	 */
	public static function detect();
	
	/**	
	 * Populate a Unified Request Array to return
	 * 
	 * @access	public
	 * @return	array 	generated to form unified request array
	 */
	public function populateURA();
	
	/**	
	 * Get helper name
	 * 
	 * @access	public
	 * @return	string 	name to identify helper type
	 */
	public function getName();
	
	/**
	 * Pre action hook
	 * 
	 * This method is invoked by the front controller before invoking the requested
	 * action in the action controller. It gives the client an opportunity to do 
	 * something before the component is executed.
	 * 
	 * @return	void
	 */
	public function preActionHook();
	
	/**
	 * Render component view
	 * 
	 * This method is invoked by the views and renders the ouput data in the format specified
	 * by the client.
	 * 
	 * @param	array	$data	An array containing the output data
	 * @return	void
	 */
	public function renderView($data);
	
	/**
	 * Render overall template
	 *
	 * @param	string	$str
	 * @return	void
	 */
	public function renderTemplate(&$str);
}
