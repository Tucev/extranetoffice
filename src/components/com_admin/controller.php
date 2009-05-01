<?php
/**
 * @version 	$Id$
 * @package		phpFrame
 * @subpackage	com_admin
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * adminController Class
 * 
 * @package		phpFrame
 * @subpackage 	com_admin
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class adminController extends phpFrame_Application_ActionController {
	/**
	 * Constructor
	 * 
	 * @return	void
	 * @since 	1.0
	 */
	function __construct() {
		// set default request vars
		$this->view = phpFrame_Environment_Request::getViewName('admin');
		
		// It is important we invoke the parent's constructor before 
		// running permission check as we need the available views loaded first.
		parent::__construct();
	}
	
	/**
	 * Save global configuration
	 * 
	 * @return void
	 */
	function save_config() {
		// Check for request forgeries
		phpFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		$modelConfig = $this->getModel('config');
		
		if ($modelConfig->saveConfig() === false) {
			phpFrame_Application_Error::raise('', 'error', $modelConfig->getLastError());
		}
		else {
			phpFrame_Application_Error::raise('', 'message', _LANG_CONFIG_SAVE_SUCCESS);
		}
		
		$this->setRedirect('index.php?component=com_admin&view=config');
	}
	
	function save_user() {
		// Check for request forgeries
		phpFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		$modelUsers = $this->getModel('users');
		
		if ($modelUsers->saveUser() === false) {
			phpFrame_Application_Error::raise('', 'error', $modelUsers->getLastError());
		}
		else {
			phpFrame_Application_Error::raise('', 'message', _LANG_USER_SAVE_SUCCESS);
		}
		
		$this->setRedirect('index.php?component=com_admin&view=users');
	}
	
	function remove_user() {
		$userid = phpFrame_Environment_Request::getVar('id', 0);
		
		$modelUsers = $this->getModel('users');
		
		if ($modelUsers->deleteUser($userid) === false) {
			phpFrame_Application_Error::raise('', 'error', $modelUsers->getLastError());
		}
		else {
			phpFrame_Application_Error::raise('', 'message', _LANG_ADMIN_USERS_DELETE_SUCCESS);
		}
		
		$this->setRedirect('index.php?component=com_admin&view=users');
	}
}
?>