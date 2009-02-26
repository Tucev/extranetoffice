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
class factory {
	/**
	 * Get application object
	 * 
	 * @return	object
	 * @since 	1.0
	 */
	function getApplication() {
		return phpFrame::getInstance('application');
	}
	
	/**
	 * Get configuration object
	 * 
	 * @return	object
	 * @since 	1.0
	 */
	function getConfig() {
		return $GLOBALS['application']->config;
	}
	
	/**
	 * Get database object
	 * 
	 * @return	object
	 * @since 	1.0
	 */
	function getDB() {
		return phpFrame::getInstance('db');
	}
	
	/**
	 * Get user object
	 * 
	 * @return 	object
	 * @since 	1.0
	 */
	function getUser() {
		return phpFrame::getInstance('user');
	}
	
	/**
	 * Get session object
	 * 
	 * @return	object
	 * @since 	1.0
	 */
	function getSession() {
		return phpFrame::getInstance('session');
	}
	
	/**
	 * Get pathway object
	 * 
	 * @return	object
	 * @since 	1.0
	 */
	function getPathway() {
		return phpFrame::getInstance('pathway');
	}
	
	/**
	 * Get document object
	 * 
	 * @param	string	$type The document type (html or xml)
	 * @return	object
	 * @since 	1.0
	 */
	function getDocument($type) {
		return phpFrame::getInstance('document'.strtoupper($type));
	}
	
	/**
	 * Get uri object
	 * 
	 * @return	object
	 * @since 	1.0
	 */
	function getURI() {
		return phpFrame::getInstance('uri');
	}
}
?>