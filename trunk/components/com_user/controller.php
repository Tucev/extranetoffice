<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_user
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * userController Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_user
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class userController extends controller {
	/**
	 * Constructor
	 * 
	 * @return	void
	 * @since 	1.0
	 */
	function __construct() {
		// set default request vars
		$this->view = request::getVar('view', 'settings');
		
		parent::__construct();
	}
}
?>