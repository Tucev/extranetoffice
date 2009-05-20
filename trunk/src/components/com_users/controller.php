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
	protected function __construct() {
		// Invoke parent's constructor to set default action
		parent::__construct('get_users');
	}
	
	public function get_users() {
		// Get request data
		$orderby = phpFrame::getRequest()->get('orderby', 'u.lastname');
		$orderdir = phpFrame::getRequest()->get('orderdir', 'ASC');
		$limit = phpFrame::getRequest()->get('limit', 25);
		$limitstart = phpFrame::getRequest()->get('limitstart', 0);
		$search = phpFrame::getRequest()->get('search', '');
		
		// Create list filter needed for getUsers()
		$list_filter = new phpFrame_Database_CollectionFilter($orderby, $orderdir, $limit, $limitstart, $search);
		
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
	
	public function get_user() {
		$userid = phpFrame::getRequest()->get('userid', 0);
		
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
		$ret_url = phpFrame::getRequest()->get('ret_url', 'index.php');
		
		// Get view
		$view = $this->getView('settings', '');
		// Set view data
		$view->addData('row', phpFrame::getUser());
		$view->addData('ret_url', $ret_url);
		// Display view
		$view->display();
	}
	
	public function save_user() {
		// Check for request forgeries
		phpFrame_Utils_Crypt::checkToken() or exit( 'Invalid Token' );
		
		// Get request vars
		$post = phpFrame::getRequest()->getPost();
		
		$modelUser = $this->getModel('users');
		if ($modelUser->saveUser($post) === false) {
			$this->_sysevents->setSummary($modelUser->getLastError(), "error");
		}
		else {
			$this->_sysevents->setSummary(_LANG_USER_SAVE_SUCCESS, "success");
			$this->_success = true;
		}
		
		$ret_url = phpFrame::getRequest()->get('ret_url', 'index.php');
		$this->setRedirect($ret_url);
	}
}
