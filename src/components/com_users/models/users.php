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
 * usersModelUsers Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_users
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		model
 */
class usersModelUsers extends phpFrame_Application_Model {
	function getUsers() {
		$filter_order = phpFrame_Environment_Request::getVar('filter_order', 'u.lastname');
		$filter_order_Dir = phpFrame_Environment_Request::getVar('filter_order_Dir', '');
		$search = phpFrame_Environment_Request::getVar('search', '');
		$search = strtolower( $search );
		$limitstart = phpFrame_Environment_Request::getVar('limitstart', 0);
		$limit = phpFrame_Environment_Request::getVar('limit', 20);

		$where = array();
		
		if ($search) {
			$where[] = "u.lastname LIKE '%".$this->db->getEscaped($search)."%'";
		}
		
		$where[] = "(u.deleted = '0000-00-00 00:00:00' OR u.deleted IS NULL)";

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		
		if (empty($filter_order)) {
			$orderby = ' ORDER BY u.lastname ';
		} else {
			$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', u.lastname ';
		}
		
		// get the total number of records
		$query = "SELECT 
				  u.id
				  FROM #__users AS u "
				  . $where;
				  
		$this->db->setQuery($query);
		$this->db->query();
		$total = $this->db->getNumRows();
		
		// get the subset (based on limits) of required records
		$query = "SELECT 
				  u.*
				  FROM #__users AS u "
				  . $where;

		$pageNav = new phpFrame_HTML_Pagination($total, $limitstart, $limit);
			
		$query .= $orderby." LIMIT ".$pageNav->limitstart.", ".$pageNav->limit;
		//echo str_replace('#__', 'eo_', $query); exit;
		$this->db->setQuery($query);
		$rows = $this->db->loadObjectList();
		
		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;
	
		// search filter
		$lists['search'] = $search;
			
		// pack data into an array to return
		$return['rows'] = $rows;
		$return['pageNav'] = $pageNav;
		$return['lists'] = $lists;
			
		return $return;
	}
	
	function getUsersDetail($userid) {
		$query = "SELECT * FROM #__users WHERE id = ".$userid;
		$this->db->setQuery($query);
		return $this->db->loadObject();
	}
	
	/**
	 * Save current user
	 * 
	 * @return	bool	Returns TRUE on success or FALSE on failure.
	 */
	function saveUser() {
		$userid = phpFrame_Environment_Request::getVar('id', null);
		
		// Get reference to user object
		$user =& phpFrame_Application_Factory::getUser();
		//$user->load($userid, 'password');
		
		$post = phpFrame_Environment_Request::get('post');
		
		// Upload image if photo sent in request
		if (!empty($_FILES['photo']['name'])) {
			$dir = _ABS_PATH.DS.config::UPLOAD_DIR.DS."users";
			$accept = 'image/jpeg,image/jpg,image/png,image/gif';
			$upload = phpFrame_Utils_Filesystem::uploadFile('photo', $dir, $accept);
			if (!empty($upload['error'])) {
				$this->error[] = $upload['error'];
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
			$this->error[] = $user->getLastError();
			return false;
		}
		
		if (!$user->check()) {
			$this->error[] = $user->getLastError();
			return false;
		}
	
		if (!$user->store()) {
			$this->error[] = $user->getLastError();
			return false;
		}
		
		return true;
	}
}
?>