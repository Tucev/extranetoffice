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
 * Client for Command Line Interface
 * 
 * @todo		
 * @package		
 * @subpackage 	
 * @author 		
 * @since 		
 */
class phpFrame_Client_Cli implements phpFrame_Client_IClient {
		
	/**
	 * Check if this is the correct helper for the client being used
	 * 
	 * @static
	 * @access	public
	 * @return	mixed object instance of this class if correct helper for client or false otherwise
	 */
	public static function detect() {
		
		global $argv;
		if (is_array($argv)) {
			return new self;
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

		// Get arguments passed via command line and parse them as request vars
		global $argv;
		$request = array();
		for ($i=1; $i<count($argv); $i++) {
			if (preg_match('/^(.*)=(.*)$/', $argv[$i], $matches)) {
				$request['request'][$matches[1]] = $matches[2];
			}
		}
		return $request;
	}
	
	/**	
	 * Get helper name
	 * 
	 * @access	public
	 * @return	string name to identify helper type
	 */
	public function getName() {
		return "cli";
	}
	
	public function preActionHook() {
		// Automatically log in as system user
		$user = phpFrame::getUser();
		$user->id = 1;
		$user->groupid = 1;
		$user->username = 'system';
		$user->firstname = 'System';
		$user->lastname = 'User';
		
		// Store user detailt in session
		$session = phpFrame::getSession();
		$session->setUser($user);
	}
	
	public function renderView($data) {
		var_dump($data);
	}
	
	public function renderTemplate(&$str) {}
}
