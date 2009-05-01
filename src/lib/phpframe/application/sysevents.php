<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	application
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * System Events Class
 * 
 * @package		phpFrame
 * @subpackage 	application
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class phpFrame_Application_Sysevents extends phpFrame_Base_Singleton {
	/**
	 * Events summary
	 * 
	 * @var	array
	 */
	private $_summary=array();
	/**
	 * Events log
	 * 
	 * An array containig more info about what was reported to the system events.
	 * 
	 * @var	array
	 */
	private $_events_log=array();
	
	/**
	 * Constructor is protected. This class has to be instantiated using phpFrame::getSysevents().
	 * 
	 * @return	void
	 * @since 	1.0
	 */
	protected function __construct() {
		// Restore sys_events from session
		$session = phpFrame::getSession();
		if (!$session->id) {
			throw new phpFrame_Exception("Could not restore system events from session.");
		}
		else {
			$array = $session->getVar('sys_events', array());
			if (is_array($array) && array_key_exists('summary', $array)) {
				$this->_summary = $array['summary'];
			}
			
			if (is_array($array) && array_key_exists('events_log', $array)) {
				$this->_events_log = $array['events_log'];
			}
		}
	}
	
	/**
	 * Set system events summary
	 * 
	 * @param	string	$msg
	 * @param	string	$type	Possible values "error", "warning", "notice", "success", "info". Default value is "error".
	 * @return	void
	 * @since 	1.0
	 */
	public function setSummary($msg, $type=null) {
		if (is_null($type)) $type = "error";
		$this->_summary = array($type, $msg);
		$this->_storeInSession();
	}
	
	/**
	 * Add event log
	 * 
	 * @param	string	$msg	
	 * @param	string	$type	Possible values "error", "warning", "notice", "success", "info". Default value is "error".
	 * @return	void
	 * @since 	1.0
	 */
	public function addEventLog($msg, $type=null) {
		if (is_null($type)) $type = "error";
		$this->_events_log[] = array($type, $msg);
		$this->_storeInSession();
	}
	
	/**
	 * Get system events as array.
	 * 
	 * This method is used to get the system events for output.
	 * 
	 * @return array
	 * @since 	1.0
	 */
	public function asArray() {
		return array("summary" => $this->_summary, "events_log" => $this->_events_log);
	}
	
	/**
	 * Get system events as string.
	 * 
	 * @return	string
	 * @since 	1.0
	 */
	public function __toString() {
		$str = "";
		if (count($this->_summary) > 0) {
			$str .= ucfirst($this->_summary[0]).": ".$this->_summary[1];
		}
		
		if (is_array($this->_events_log) && count($this->_events_log) > 0) {
			$str .= "\n\nEvents log: \n";
			foreach ($this->_events_log as $event_log) {
				$str .= ucfirst($event_log[0]).": ".$event_log[1]."\n";
			}
		}
		
		return $str;
	}
	
	/**
	 * Clear system events from object and session.
	 * 
	 * This should be done after displaying the messages to the user.
	 * 
	 * @return	void
	 * @since 	1.0
	 */
	public function clear() {
		// Clear private vars
		$this->_summary = array();
		$this->_events_log = array();
		
		// clear system events from session
		$session = phpFrame::getSession();
		$session->setVar('sys_events', null);
	}
	
	/**
	 * Store system messages in session
	 * 
	 * @return	void
	 * @since 	1.0
	 */
	private function _storeInSession() {
		// Store sys_events in session
		$session = phpFrame::getSession();
		if (!$session->id) {
			throw new phpFrame_Exception("Could not store system message in session.");
		}
		else {
			$session->setVar('sys_events', $this->asArray());
		}
	} 
}
