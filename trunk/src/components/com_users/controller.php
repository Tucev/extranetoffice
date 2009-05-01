<?php
/**
 * @version 	$Id$
 * @package		phpFrame
 * @subpackage	com_users
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * usersController Class
 * 
 * @package		phpFrame
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
		$this->view = phpFrame_Environment_Request::getViewName('users');
		$this->layout = phpFrame_Environment_Request::getLayout('list');
		
		parent::__construct();
	}
	
	function save_user() {
		$post = phpFrame_Environment_Request::getPost();
		
		$modelUser = $this->getModel('users');
		
		if ($modelUser->saveUser($post) === false) {
			$this->_sysevents->setSummary($modelUser->getLastError(), "error");
		}
		else {
			$this->_sysevents->setSummary(_LANG_USER_SAVE_SUCCESS, "success");
		}
		
		$this->setRedirect($_SERVER["HTTP_REFERER"]);
	}
}
?>