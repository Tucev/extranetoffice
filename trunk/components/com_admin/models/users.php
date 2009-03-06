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
	function getUsers() {
		$filter_order = request::getVar('filter_order', 'u.lastname');
		$filter_order_Dir = request::getVar('filter_order_Dir', '');
		$search = request::getVar('search', '');
		$search = strtolower( $search );
		$limitstart = request::getVar('limitstart', 0);
		$limit = request::getVar('limit', 20);

		$where = array();
		
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
				  JOIN #__users_groups ug ON ug.userid = u.id 
				  LEFT JOIN #__groups g ON ug.groupid = g.id "
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
				  JOIN #__users_groups ug ON ug.userid = u.id 
				  LEFT JOIN #__groups g ON ug.groupid = g.id "
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
	
	function getUsersDetail($userid=0) {
		if (!empty($userid)) {
			$query = "SELECT 
					  u.*, 
					  g.id AS groupid, g.name AS group_name 
					  FROM #__users AS u 
					  JOIN #__users_groups ug ON ug.userid = u.id 
					  LEFT JOIN #__groups g ON ug.groupid = g.id 
					  WHERE u.id = '".$userid."'";
			$this->db->setQuery($query);
			return $this->db->loadObject();
		}
		else {
			return false;
		}
	}
}
?>