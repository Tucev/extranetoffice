<?php
/**
 * @version 	$Id$
 * @package		PHPFrame
 * @subpackage	com_admin
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * adminController Class
 * 
 * @todo		Handling of tmpl get var should be delegated to URL rewriter instead 
 * 				of appearing inside the required controler actions.
 * @package		PHPFrame
 * @subpackage 	com_admin
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class adminController extends PHPFrame_Application_ActionController {
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
		$orderby = PHPFrame::getRequest()->get('orderby', 'u.lastname');
		$orderdir = PHPFrame::getRequest()->get('orderdir', 'ASC');
		$limit = PHPFrame::getRequest()->get('limit', 25);
		$limitstart = PHPFrame::getRequest()->get('limitstart', 0);
		$search = PHPFrame::getRequest()->get('search', '');
		
		// Create list filter needed for getUsers()
		$list_filter = new PHPFrame_Database_CollectionFilter($orderby, $orderdir, $limit, $limitstart, $search);
		
		// Get users using model
		$users = $this->getModel('users')->getUsers($list_filter);
		
		// Get view
		$view = $this->getView('users', 'list');
		// Set view data
		$view->addData('rows', $users);
		$view->addData('page_nav', new PHPFrame_HTML_Pagination($list_filter));
		// Display view
		$view->display();
	}
	
	public function get_user_form() {
		$userid = PHPFrame::getRequest()->get('userid', 0);
		
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
		PHPFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		// Get request vars
		$tmpl = PHPFrame::getRequest()->get('tmpl', '');
		$post = PHPFrame::getRequest()->getPost();
		
		$modelConfig = $this->getModel('config');
		
		if ($modelConfig->saveConfig($post) === false) {
			$this->sysevents->setSummary($modelConfig->getLastError());
		}
		else {
			$this->sysevents->setSummary(_LANG_CONFIG_SAVE_SUCCESS, "success");
			$this->_success = true;
		}
		
		if (!empty($tmpl)) $tmpl = "&tmpl=".$tmpl;
		$this->setRedirect('index.php?component=com_admin&action=get_config'.$tmpl);
	}
	
	public function save_user() {
		// Check for request forgeries
		PHPFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		// Get request vars
		$tmpl = PHPFrame::getRequest()->get('tmpl', '');
		$post = PHPFrame::getRequest()->getPost();
		
		$modelUsers = $this->getModel('users');
		
		if ($modelUsers->saveUser($post) === false) {
			$this->sysevents->setSummary($modelUsers->getLastError());
		}
		else {
			$this->sysevents->setSummary(_LANG_USER_SAVE_SUCCESS, "success");
			$this->_success = true;
		}
		
		if (!empty($tmpl)) $tmpl = "&tmpl=".$tmpl;
		$this->setRedirect('index.php?component=com_admin&action=get_users'.$tmpl);
	}
	
	public function remove_user() {
		// Get request vars
		$tmpl = PHPFrame::getRequest()->get('tmpl', '');
		$userid = PHPFrame::getRequest()->get('id', 0);
		
		$modelUsers = $this->getModel('users');
		
		if ($modelUsers->deleteUser($userid) === false) {
			$this->sysevents->setSummary($modelUsers->getLastError());
		}
		else {
			$this->sysevents->setSummary(_LANG_ADMIN_USERS_DELETE_SUCCESS, "success");
		}
		
		if (!empty($tmpl)) $tmpl = "&tmpl=".$tmpl;
		$this->setRedirect('index.php?component=com_admin&action=get_users'.$tmpl);
	}
}
