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

/**
 * Factory Class
 * 
 * @package		phpFrame
 * @subpackage 	application
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see			phpFrame
 */
class phpFrame_Application_Factory {
	/**
	 * Get application object
	 * 
	 * @return	object
	 * @since 	1.0
	 */
	function getApplication() {
		return phpFrame::getInstance('phpFrame_Application');
	}
	
	/**
	 * Get configuration object
	 * 
	 * @return	object
	 * @since 	1.0
	 */
	function getConfig() {
		$application =& phpFrame::getInstance('phpFrame_Application');
		return $application->config;
	}
	
	/**
	 * Get database object
	 * 
	 * @return	object
	 * @since 	1.0
	 */
	function getDB() {
		return phpFrame::getInstance('phpFrame_Database');
	}
	
	/**
	 * Get user object
	 * 
	 * @return 	object
	 * @since 	1.0
	 */
	function getUser() {
		return phpFrame::getInstance('phpFrame_User');
	}
	
	/**
	 * Get session object
	 * 
	 * @return	object
	 * @since 	1.0
	 */
	function getSession() {
		return phpFrame::getInstance('phpFrame_Environment_Session');
	}
	
	/**
	 * Get pathway object
	 * 
	 * @return	object
	 * @since 	1.0
	 */
	function getPathway() {
		return phpFrame::getInstance('phpFrame_Application_Pathway');
	}
	
	/**
	 * Get document object
	 * 
	 * @param	string	$type The document type (html or xml)
	 * @return	object
	 * @since 	1.0
	 */
	function getDocument($type) {
		return phpFrame::getInstance('phpFrame_Document_'.strtoupper($type));
	}
	
	/**
	 * Get uri object
	 * 
	 * @return	object
	 * @since 	1.0
	 */
	function getURI() {
		return phpFrame::getInstance('phpFrame_Utils_URI');
	}
}
?>