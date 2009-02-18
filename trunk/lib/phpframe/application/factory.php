<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	application
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

class factory {
	function getConfig() {
		return $GLOBALS['application']->config;
	}
	
	function getRequest() {
		return $GLOBALS['application']->request;
	}
	
	function getDB() {
		$application = phpFrame::getInstance('application');
		return $application->db;
	}
	
	function getUser() {
		$application = phpFrame::getInstance('application');
		return $application->user;
	}
	
	function getSession() {
		$application = phpFrame::getInstance('application');
		return $application->session;
	}
	
	function getPathway() {
		return phpFrame::getInstance('pathway');
	}
	
	function getApplication() {
		return phpFrame::getInstance('application');
	}
}
?>