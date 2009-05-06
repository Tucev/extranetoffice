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
 * usersModelUsers Class
 * 
 * @package		phpFrame
 * @subpackage 	com_users
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		phpFrame_Application_Model
 */
class usersModelUsers extends phpFrame_Application_Model {
	/**
	 * Get users list
	 * 
	 * @param	object	$list_filter	Object if type phpFrame_Database_Listfilter
	 * @return	array
	 */
	public function getUsers(phpFrame_Database_Listfilter $list_filter) {
		$where = array();
		
		if ($search) {
			$where[] = "u.lastname LIKE '%".phpFrame::getDB()->getEscaped($list_filter->getSearchStr())."%'";
		}
		
		$where[] = "(u.deleted = '0000-00-00 00:00:00' OR u.deleted IS NULL)";

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		
		// get the total number of records
		$query = "SELECT 
				  u.id
				  FROM #__users AS u "
				  . $where;
				  
		phpFrame::getDB()->setQuery($query);
		phpFrame::getDB()->query();
		
		// Set total number of record in list filter
		$list_filter->setTotal(phpFrame::getDB()->getNumRows());
		
		// get the subset (based on limits) of required records
		$query = "SELECT 
				  u.*
				  FROM #__users AS u "
				  . $where;
			
		// Add order by and limit statements for subset (based on filter)
		$query .= $list_filter->getOrderByStmt();
		$query .= $list_filter->getLimitStmt();
		//echo str_replace('#__', 'eo_', $query); exit;
		
		phpFrame::getDB()->setQuery($query);
		return phpFrame::getDB()->loadObjectList();
	}
	
	/**
	 * Get a single user's details
	 * 
	 * @param	int	$userid
	 * @return	object
	 */
	public function getUsersDetail($userid) {
		$query = "SELECT * FROM #__users WHERE id = ".$userid;
		phpFrame::getDB()->setQuery($query);
		return phpFrame::getDB()->loadObject();
	}
	
	/**
	 * Save user
	 * 
	 * @return	bool	Returns TRUE on success or FALSE on failure.
	 */
	public function saveUser($post) {
		$userid = phpFrame_Environment_Request::getVar('id', null);
		
		// Get reference to user object
		$user = phpFrame::getUser();
		//$user->load($userid, 'password');
		
		// Upload image if photo sent in request
		if (!empty($_FILES['photo']['name'])) {
			$dir = _ABS_PATH.DS.config::UPLOAD_DIR.DS."users";
			$accept = 'image/jpeg,image/jpg,image/png,image/gif';
			$upload = phpFrame_Utils_Filesystem::uploadFile('photo', $dir, $accept);
			if (!empty($upload['error'])) {
				$this->_error[] = $upload['error'];
				return false;
			}
			else {
				// resize image
				$image = new phpFrame_Utils_Image();
				$image->resize_image($dir.DS.$upload['file_name'], $dir.DS.$upload['file_name'], 80, 110);
				// Store file name in post array
				$post['photo'] = $upload['file_name'];
			}
		}
		
		// exlude password if not passed in request
		$exclude = '';
		if (empty($post['password'])) {
			$exclude = 'password';
		}
		
		// Bind the post data to the row array
		if ($user->bind($post, $exclude) === false) {
			$this->_error[] = $user->getLastError();
			return false;
		}
		
		if (!$user->check()) {
			$this->_error[] = $user->getLastError();
			return false;
		}
	
		if (!$user->store()) {
			$this->_error[] = $user->getLastError();
			return false;
		}
		
		return true;
	}
}
