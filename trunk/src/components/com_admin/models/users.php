<?php
/**
 * @version 	$Id$
 * @package		phpFrame
 * @subpackage	com_admin
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * adminModelUsers Class
 * 
 * @package		phpFrame
 * @subpackage 	com_admin
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		model
 */
class adminModelUsers extends phpFrame_Application_Model {
	/**
	 * Get users
	 * 
	 * This method returns an array with row objects for each user
	 * 
	 * @param	object	$list_filter	Object of type phpFrame_Database_Listfilter
	 * @param	boolean	$deleted		Indicates whether we want to include deleted users
	 * @return	array
	 */
	function getUsers(phpFrame_Database_Listfilter $list_filter, $deleted=false) {
		// Build SQL query
		$where = array();
		
		if ($deleted === true) {
			$where[] = "`deleted` <> '0000-00-00 00:00:00'";
			$where[] = "`deleted` IS NOT NULL";
		}
		else {
			$where[] = "(`deleted` = '0000-00-00 00:00:00' OR `deleted` IS NULL)";
		}
		
		if ($search) {
			$where[] = "(u.firstname LIKE '%".phpFrame::getDB()->getEscaped($list_filter->getSearchStr())."%' 
						OR u.lastname LIKE '%".phpFrame::getDB()->getEscaped($list_filter->getSearchStr())."%' 
						OR u.username LIKE '%".phpFrame::getDB()->getEscaped($list_filter->getSearchStr())."%')";
		}

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		
		// get the total number of records
		$query = "SELECT 
				  u.id
				  FROM #__users AS u 
				  LEFT JOIN #__groups g ON u.groupid = g.id "
				  . $where . 
				  " GROUP BY u.id ";
		
		//echo str_replace('#__', 'eo_', $query); exit;
		phpFrame::getDB()->setQuery($query);
		phpFrame::getDB()->query();
		
		// Set total number of record in list filter
		$list_filter->setTotal(phpFrame::getDB()->getNumRows());
		
		// get the subset (based on limits) of required records
		$query = "SELECT 
				  u.*, 
				  g.id AS groupid, g.name AS group_name 
				  FROM #__users AS u 
				  LEFT JOIN #__groups g ON u.groupid = g.id "
				  . $where . 
				  " GROUP BY u.id ";
			
		// Add order by and limit statements for subset (based on filter)
		$query .= $list_filter->getOrderByStmt();
		$query .= $list_filter->getLimitStmt();
		//echo str_replace('#__', 'eo_', $query); exit;
		
		phpFrame::getDB()->setQuery($query);
		return phpFrame::getDB()->loadObjectList();
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
			phpFrame::getDB()->setQuery($query);
			return phpFrame::getDB()->loadObject();
		}
		else {
			return false;
		}
	}
	
	function saveUser() {
		$userid = phpFrame_Environment_Request::getVar('id', null);
		
		// Get reference to user object
		$user = phpFrame::getUser();
		
		// Create standard object to store user properties
		// We do this because we dont want to overwrite the current user object.
		// Remember the user object extends phpFrame_Database_Table, which in turn extends phpFrame_Base_Singleton.
		$row = new phpFrame_Base_StdObject();
		
		// if no userid passed in request we assume it is a new user
		if (empty($userid)) {
			$row->block = '0';
			$row->created = date("Y-m-d H:i:s");
			// Generate random password and store in local variable to be used when sending email to user.
			$password = phpFrame_Utils_Crypt::genRandomPassword();
			// Assign newly generated password to row object (this password will be encrypted when stored).
			$row->password = $password;
			$new_user = true;
		}
		// if a userid is passed in the request we assume we are updating an existing user
		else {
			$user->load($userid, 'password', $row);
			$new_user = false;
		}
		
		$post = phpFrame_Environment_Request::getPost();
		
		// exlude password if not passed in request
		$exclude = '';
		if (empty($post['password'])) {
			$exclude = 'password';
		}
		
		// Bind the post data to the row array
		if ($user->bind($post, $exclude, $row) === false) {
			$this->_error[] = $user->getLastError();
			return false;
		}
		
		if (!$user->check($row)) {
			$this->_error[] = $user->getLastError();
			return false;
		}
		
		if (!$user->store($row)) {
			$this->_error[] = $user->getLastError();
			return false;
		}
		
		// Send notification to new users
		if ($new_user === true) {
			$uri = phpFrame::getURI();
		
			$new_mail = new phpFrame_Mail_Mailer();
			$new_mail->AddAddress($row->email, phpFrame_User_Helper::fullname_format($row->firstname, $row->lastname));
			$new_mail->Subject = _LANG_USER_NEW_NOTIFY_SUBJECT;
			$new_mail->Body = sprintf(_LANG_USER_NEW_NOTIFY_BODY, 
									 $row->firstname, 
									 $uri->getBase(), 
									 $row->username, 
									 $password
							);
									   
			if ($new_mail->Send() !== true) {
				$this->_error[] = sprintf(_LANG_EMAIL_NOT_SENT, $row->email);
				return false;
			}
		}
		
		return true;
	}
	
	function deleteUser($userid) {
		$query = "UPDATE #__users SET `deleted` = '".date("Y-m-d H:i:s")."' WHERE id = ".$userid;
		phpFrame::getDB()->setQuery($query);
		if (phpFrame::getDB()->query() === false) {
			$this->_error[] = phpFrame::getDB()->getLastError();
			return false;
		}
		else {
			return true;
		}
	}
}
