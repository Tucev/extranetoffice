<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_users
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
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
class usersController extends phpFrame_Application_ActionController {
	/**
	 * Constructor
	 * 
	 * @return	void
	 * @since 	1.0
	 */
	function __construct() {
		// set default request vars
		$this->view = phpFrame_Environment_Request::getView('users');
		$this->layout = phpFrame_Environment_Request::getVar('layout', 'list');
		
		parent::__construct();
	}
	
	function save_user() {
		$modelUser = $this->getModel('users');
		
		if ($modelUser->saveUser() === false) {
			phpFrame_Application_Error::raise('', 'error', $modelUser->getLastError());
		}
		else {
			phpFrame_Application_Error::raise('', 'message', _LANG_USER_SAVE_SUCCESS);
		}
		
		$this->setRedirect($_SERVER["HTTP_REFERER"]);
	}
}
?>