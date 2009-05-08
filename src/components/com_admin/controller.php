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
 * @todo		Handling of tmpl get var should be delegated to URL rewriter instead 
 * 				of appearing inside the required controler actions.
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
	protected function __construct() {
		// Invoke parent's constructor to set default action
		parent::__construct('get_admin');
	}
	
	public function get_admin() {
		// Get view
		$view = $this->getView('admin', '');
		// Display view
		$view->display();
	}
	
	public function get_config() {
		// Get view
		$view = $this->getView('config', '');
		// Display view
		$view->display();
	}
	
	public function get_users() {
		// Get request data
		$orderby = phpFrame_Environment_Request::getVar('orderby', 'u.lastname');
		$orderdir = phpFrame_Environment_Request::getVar('orderdir', 'ASC');
		$limit = phpFrame_Environment_Request::getVar('limit', 25);
		$limitstart = phpFrame_Environment_Request::getVar('limitstart', 0);
		$search = phpFrame_Environment_Request::getVar('search', '');
		
		// Create list filter needed for getUsers()
		$list_filter = new phpFrame_Database_Listfilter($orderby, $orderdir, $limit, $limitstart, $search);
		
		// Get users using model
		$users = $this->getModel('users')->getUsers($list_filter);
		
		// Get view
		$view = $this->getView('users', 'list');
		// Set view data
		$view->addData('rows', $users);
		$view->addData('page_nav', new phpFrame_HTML_Pagination($list_filter));
		// Display view
		$view->display();
	}
	
	public function get_user_form() {
		$userid = phpFrame_Environment_Request::getVar('userid', 0);
		
		// Get users using model
		$user = $this->getModel('users')->getUsersDetail($userid);
		
		// Get view
		$view = $this->getView('users', 'form');
		// Set view data
		$view->addData('row', $user);
		// Display view
		$view->display();
	}
	
	public function get_components() {
	
	}
	
	public function get_modules() {
	
	}
	
	/**
	 * Save global configuration
	 * 
	 * @return void
	 */
	public function save_config() {
		// Check for request forgeries
		phpFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		// Get request vars
		$tmpl = phpFrame_Environment_Request::getVar('tmpl', '');
		
		$modelConfig = $this->getModel('config');
		
		if ($modelConfig->saveConfig() === false) {
			$this->_sysevents->setSummary($modelConfig->getLastError());
		}
		else {
			$this->_sysevents->setSummary(_LANG_CONFIG_SAVE_SUCCESS, "success");
		}
		
		if (!empty($tmpl)) $tmpl = "&tmpl=".$tmpl;
		$this->setRedirect('index.php?component=com_admin&action=get_config'.$tmpl);
	}
	
	public function save_user() {
		// Check for request forgeries
		phpFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		// Get request vars
		$tmpl = phpFrame_Environment_Request::getVar('tmpl', '');
		$post = phpFrame_Environment_Request::getPost();
		
		$modelUsers = $this->getModel('users');
		
		if ($modelUsers->saveUser($post) === false) {
			$this->_sysevents->setSummary($modelUsers->getLastError());
		}
		else {
			$this->_sysevents->setSummary(_LANG_USER_SAVE_SUCCESS, "success");
		}
		
		if (!empty($tmpl)) $tmpl = "&tmpl=".$tmpl;
		$this->setRedirect('index.php?component=com_admin&action=get_users'.$tmpl);
	}
	
	public function remove_user() {
		// Get request vars
		$tmpl = phpFrame_Environment_Request::getVar('tmpl', '');
		$userid = phpFrame_Environment_Request::getVar('id', 0);
		
		$modelUsers = $this->getModel('users');
		
		if ($modelUsers->deleteUser($userid) === false) {
			$this->_sysevents->setSummary($modelUsers->getLastError());
		}
		else {
			$this->_sysevents->setSummary(_LANG_ADMIN_USERS_DELETE_SUCCESS, "success");
		}
		
		if (!empty($tmpl)) $tmpl = "&tmpl=".$tmpl;
		$this->setRedirect('index.php?component=com_admin&action=get_users'.$tmpl);
	}
}
