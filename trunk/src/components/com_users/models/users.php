<?php
/**
 * @version 	$Id$
 * @package		PHPFrame
 * @subpackage	com_users
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * usersModelUsers Class
 * 
 * @package		PHPFrame
 * @subpackage 	com_users
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		PHPFrame_Application_Model
 */
class usersModelUsers extends PHPFrame_Application_Model {
	/**
	 * Get users list
	 * 
	 * @param	object	$list_filter	Object if type PHPFrame_Database_CollectionFilter
	 * @return	array
	 */
	public function getUsers(PHPFrame_Database_CollectionFilter $list_filter) {
		$where = array();
		
		// Add search filtering
		$search = $list_filter->getSearchStr();
		if ($search) {
			$where[] = "u.lastname LIKE '%".PHPFrame::getDB()->getEscaped($list_filter->getSearchStr())."%'";
		}
		
		$where[] = "(u.deleted = '0000-00-00 00:00:00' OR u.deleted IS NULL)";

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		
		// get the total number of records
		$query = "SELECT 
				  u.id
				  FROM #__users AS u "
				  . $where;
		
		// Run query to get total rows before applying filter
		$list_filter->setTotal(PHPFrame::getDB()->query($query)->rowCount());
		
		// get the subset (based on limits) of required records
		$query = "SELECT 
				  u.*
				  FROM #__users AS u "
				  . $where;
			
		// Add order by and limit statements for subset (based on filter)
		$query .= $list_filter->getOrderByStmt();
		$query .= $list_filter->getLimitStmt();
		//echo str_replace('#__', 'eo_', $query); exit;
		
		return PHPFrame::getDB()->loadObjectList($query);
	}
	
	/**
	 * Get a single user's details
	 * 
	 * @param	int	$userid
	 * @return	object
	 */
	public function getUsersDetail($userid) {
		$query = "SELECT * FROM #__users WHERE id = ".$userid;
		return PHPFrame::getDB()->loadObject($query);
	}
	
	/**
	 * Save user
	 * 
	 * @return	bool	Returns TRUE on success or FALSE on failure.
	 */
	public function saveUser($post) {
		// Get reference to user object
		$user = PHPFrame::Session()->getUser();
		
		if ($post['id']) {
			$user->load($post['id'], 'password');
		}
		
		// Upload image if photo sent in request
		if (!empty($_FILES['photo']['name'])) {
			$dir = _ABS_PATH.DS.config::UPLOAD_DIR.DS."users";
			$accept = 'image/jpeg,image/jpg,image/png,image/gif';
			$upload = PHPFrame_Utils_Filesystem::uploadFile('photo', $dir, $accept);
			if (!empty($upload['error'])) {
				$this->_error[] = $upload['error'];
				return false;
			}
			else {
				// resize image
				$image = new PHPFrame_Utils_Image();
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
		$user->bind($post, $exclude);
		// Store user in db
		if ($user->store() === false) {
			$this->_error[] = $user->getLastError();
			return false;
		}
		
		return true;
	}
}
