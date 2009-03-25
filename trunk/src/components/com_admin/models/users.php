<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_admin
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * adminModelUsers Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_admin
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		model
 */
class adminModelUsers extends model {
	/**
	 * Get users
	 * 
	 * This method returns an array with row objects for each user
	 * 
	 * @return array
	 */
	function getUsers($deleted=false) {
		$filter_order = request::getVar('filter_order', 'u.lastname');
		$filter_order_Dir = request::getVar('filter_order_Dir', '');
		$search = request::getVar('search', '');
		$search = strtolower( $search );
		$limitstart = request::getVar('limitstart', 0);
		$limit = request::getVar('limit', 20);

		$where = array();
		
		if ($deleted === true) {
			$where[] = "`deleted` <> '0000-00-00 00:00:00'";
			$where[] = "`deleted` IS NOT NULL";
		}
		else {
			$where[] = "(`deleted` = '0000-00-00 00:00:00' OR `deleted` IS NULL)";
		}
		
		if ($search) {
			$where[] = "(u.firstname LIKE '%".$this->db->getEscaped($search)."%' 
						OR u.lastname LIKE '%".$this->db->getEscaped($search)."%' 
						OR u.username LIKE '%".$this->db->getEscaped($search)."%')";
		}

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		
		if (empty($filter_order)) {
			$orderby = ' ORDER BY u.lastname ';
		} else {
			$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', u.lastname ';
		}
		
		// get the total number of records
		$query = "SELECT 
				  u.id
				  FROM #__users AS u 
				  LEFT JOIN #__groups g ON u.groupid = g.id "
				  . $where . 
				  " GROUP BY u.id ";
		
		//echo str_replace('#__', 'eo_', $query); exit;
		$this->db->setQuery($query);
		$this->db->query();
		$total = $this->db->getNumRows();
		
		// get the subset (based on limits) of required records
		$query = "SELECT 
				  u.*, 
				  g.id AS groupid, g.name AS group_name 
				  FROM #__users AS u 
				  LEFT JOIN #__groups g ON u.groupid = g.id "
				  . $where . 
				  " GROUP BY u.id ";

		$pageNav = new pagination($total, $limitstart, $limit);
			
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
	
	/**
	 * Get details for a single user
	 * 
	 * @param	int		$userid
	 * @return 	mixed	An object containing the user data or FALSE on failure.
	 */
	function getUsersDetail($userid=0) {
		if (!empty($userid)) {
			$query = "SELECT 
					  u.*, 
					  g.id AS groupid, g.name AS group_name 
					  FROM #__users AS u 
					  LEFT JOIN #__groups g ON u.groupid = g.id 
					  WHERE u.id = '".$userid."'";
			$this->db->setQuery($query);
			return $this->db->loadObject();
		}
		else {
			return false;
		}
	}
	
	function saveUser() {
		$userid = request::getVar('id', null);
		
		// Get reference to user object
		$user =& factory::getUser();
		
		// Create standard object to store user properties
		// We do this because we dont want to overwrite the current user object.
		// Remember the user object extends table, which in turn extends singleton.
		$row = new standardObject();
		
		// if no userid passed in request we assume it is a new user
		if (empty($userid)) {
			$row->block = '0';
			$row->created = date("Y-m-d H:i:s");
			// Generate random password and store in local variable to be used when sending email to user.
			$password = crypt::genRandomPassword();
			// Assign newly generated password to row object (this password will be encrypted when stored).
			$row->password = $password;
			$new_user = true;
		}
		// if a userid is passed in the request we assume we are updating an existing user
		else {
			$user->load($userid, 'password', $row);
			$new_user = false;
		}
		
		$post = request::get('post');
		
		// exlude password if not passed in request
		$exclude = '';
		if (empty($post['password'])) {
			$exclude = 'password';
		}
		
		// Bind the post data to the row array
		if ($user->bind($post, $exclude, $row) === false) {
			$this->error[] = $user->getLastError();
			return false;
		}
		
		if (!$user->check($row)) {
			$this->error[] = $user->getLastError();
			return false;
		}
		
		if (!$user->store($row)) {
			$this->error[] = $user->getLastError();
			return false;
		}
		
		// Send notification to new users
		if ($new_user === true) {
			$uri =& factory::getURI();
		
			$new_mail = new mail();
			$new_mail->AddAddress($row->email, usersHelper::fullname_format($row->firstname, $row->lastname));
			$new_mail->Subject = _LANG_USER_NEW_NOTIFY_SUBJECT;
			$new_mail->Body = sprintf(_LANG_USER_NEW_NOTIFY_BODY, 
									 $row->firstname, 
									 $uri->getBase(), 
									 $row->username, 
									 $password
							);
									   
			if ($new_mail->Send() !== true) {
				$this->error[] = sprintf(_LANG_EMAIL_NOT_SENT, $row->email);
				return false;
			}
		}
		
		return true;
	}
	
	function deleteUser($userid) {
		$query = "UPDATE #__users SET `deleted` = '".date("Y-m-d H:i:s")."' WHERE id = ".$userid;
		$this->db->setQuery($query);
		if ($this->db->query() === false) {
			$this->error[] = $this->db->getLastError();
			return false;
		}
		else {
			return true;
		}
	}
}
?>