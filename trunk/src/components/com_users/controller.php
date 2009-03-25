<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_users
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * usersController Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_users
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class usersController extends controller {
	/**
	 * Constructor
	 * 
	 * @return	void
	 * @since 	1.0
	 */
	function __construct() {
		// set default request vars
		$this->view = request::getVar('view', 'users');
		$this->layout = request::getVar('layout', 'list');
		
		parent::__construct();
	}
	
	function save_user() {
		$modelUser =& $this->getModel('users');
		
		if ($modelUser->saveUser() === false) {
			error::raise('', 'error', $modelUser->getLastError());
		}
		else {
			error::raise('', 'message', _LANG_USER_SAVE_SUCCESS);
		}
		
		$this->setRedirect($_SERVER["HTTP_REFERER"]);
	}
}
?>