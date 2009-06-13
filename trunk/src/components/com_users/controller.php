<?php
/**
 * @version 	$Id$
 * @package		PHPFrame
 * @subpackage	com_users
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * usersController Class
 * 
 * @package		PHPFrame
 * @subpackage 	com_users
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class usersController extends PHPFrame_Application_ActionController {
	/**
	 * Constructor
	 * 
	 * @return	void
	 * @since 	1.0
	 */
	protected function __construct() {
		// Invoke parent's constructor to set default action
		parent::__construct('get_users');
	}
	
	public function get_users() {
		// Get request data
		$orderby = PHPFrame::Request()->get('orderby', 'u.lastname');
		$orderdir = PHPFrame::Request()->get('orderdir', 'ASC');
		$limit = PHPFrame::Request()->get('limit', 25);
		$limitstart = PHPFrame::Request()->get('limitstart', 0);
		$search = PHPFrame::Request()->get('search', '');
		
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
	
	public function get_user() {
		$userid = PHPFrame::Request()->get('userid', 0);
		
		// Get users using model
		$user = $this->getModel('users')->getUsersDetail($userid);
		
		// Get view
		$view = $this->getView('users', 'detail');
		// Set view data
		$view->addData('row', $user);
		// Display view
		$view->display();
	}
	
	public function get_settings() {
		// Get request vars
		$ret_url = PHPFrame::Request()->get('ret_url', 'index.php');
		
		// Get view
		$view = $this->getView('settings', '');
		// Set view data
		$view->addData('row', PHPFrame::Session()->getUser());
		$view->addData('ret_url', $ret_url);
		// Display view
		$view->display();
	}
	
	public function save_user() {
		// Check for request forgeries
		PHPFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		// Get request vars
		$post = PHPFrame::Request()->getPost();
		
		$modelUser = $this->getModel('users');
		if ($modelUser->saveUser($post) === false) {
			$this->sysevents->setSummary($modelUser->getLastError(), "error");
		}
		else {
			$this->sysevents->setSummary(_LANG_USER_SAVE_SUCCESS, "success");
			$this->_success = true;
		}
		
		$ret_url = PHPFrame::Request()->get('ret_url', 'index.php');
		$this->setRedirect($ret_url);
	}
}
