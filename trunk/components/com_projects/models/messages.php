<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * projectsModelMessages Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		model
 */
class projectsModelMessages extends model {
	var $config=null;
	var $user=null;
	var $db=null;
	
	/**
	 * Constructor
	 *
	 * @since 1.0.1
	 */
	function __construct() {
		$this->init();
		parent::__construct();
	}
	
	function init() {
		$this->config =& factory::getConfig();
		$this->user =& factory::getUser();
		$this->db =& factory::getDB(); // Instantiate joomla database object
		
		//TODO: Check permissions
	}
	
	function getMessages($projectid) {
		$filter_order = request::getVar('filter_order', 'm.date_sent');
		$filter_order_Dir = request::getVar('filter_order_Dir', 'DESC');
		$search = request::getVar('search', '');
		$search = strtolower( $search );
		$limitstart = request::getVar('limitstart', 0);
		$limit = request::getVar('limit', 20);

		$where = array();
		
		// Show only public projects or projects where user has an assigned role
		//TODO: Have to apply access levels
		//$where[] = "( p.access = '0' OR (".$this->user->id." IN (SELECT userid FROM #__users_roles WHERE projectid = p.id) ) )";

		if ( $search ) {
			$where[] = "m.subject LIKE '%".$this->db->getEscaped($search)."%'";
		}
		
		if (!empty($projectid)) {
			$where[] = "m.projectid = ".$projectid;	
		}

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		if ($filter_order == 'm.date_sent'){
			$orderby = ' ORDER BY m.date_sent DESC';
		} else {
			$orderby = ' ORDER BY m.date_sent DESC, '. $filter_order .' '. $filter_order_Dir;
		}

		// get the total number of records
		// This query groups the files by parentid so and retireves the latest revision for each file in current project
		$query = "SELECT 
				  m.*, 
				  u.username AS created_by_name, 
				  GROUP_CONCAT(um.userid) assignees
				  FROM #__messages AS m 
				  JOIN #__users u ON u.id = m.userid 
				  LEFT JOIN #__users_messages um ON m.id = um.messageid "
				  . $where . 
				  " GROUP BY m.id ";
		//echo $query; exit;	  
		$this->db->setQuery( $query );
		$this->db->query();
		$total = $this->db->getNumRows();

		
		$pageNav = new pagination( $total, $limitstart, $limit );

		// get the subset (based on limits) of required records
		$query .= $orderby." LIMIT ".$pageNav->limitstart.", ".$pageNav->limit;
		//echo $query; exit;
		$this->db->setQuery($query);
		$rows = $this->db->loadObjectList();
		
		// Prepare rows and add relevant data
		if (is_array($rows) && count($rows) > 0) {
			foreach ($rows as $row) {
				// Prepare assignee data
				if (!empty($row->assignees)) {
					$assignees = explode(',', $row->assignees);
					for ($i=0; $i<count($assignees); $i++) {
						$new_assignees[$i]['id'] = $assignees[$i];
						$new_assignees[$i]['name'] = usersHelperUsers::id2name($assignees[$i]);
					}
					$row->assignees = $new_assignees;
					unset($new_assignees);
				}
				
				// get total comments
				$modelComments =& $this->getModel('comments');
				$row->comments = $modelComments->getTotalComments($row->id, 'messages');
			}
		}
		
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
	
	function getMessagesDetail($projectid, $messageid) {
		$query = "SELECT m.*, u.username AS created_by_name ";
		$query .= " FROM #__messages AS m ";
		$query .= " JOIN #__users u ON u.id = m.userid ";
		$query .= " WHERE m.id = ".$messageid;
		$query .= " ORDER BY m.date_sent DESC";
		$this->db->setQuery($query);
		$row = $this->db->loadObject();
		
		// Get assignees
		$row->assignees = $this->getAssignees($messageid);
		
		// Get comments
		$modelComments =& $this->getModel('comments');
		$row->comments = $modelComments->getComments($projectid, 'messages', $messageid);
		
		return $row;
	}
	
	function saveMessage($projectid) {
		$row = new projectsTableMessages();
		
		$post = request::get('post');
		$row->bind($post);
		
		if (empty($row->id)) {
			$row->userid = $this->user->id;
			$row->date_sent = date("Y-m-d H:i:s");
			$row->status = '1';
			$new_message = true;
		}
		
		if (!$row->check()) {
			JError::raiseError(500, $row->getError() );
		}
	
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
		}
		
		// Delete existing assignees before we store new ones if editing existing issue
		if ($new_message !== true) {
			$query = "DELETE FROM #__users_messages WHERE messageid = ".$row->id;
			$this->db->setQuery($query);
			$this->db->query();
		}
		
		// Store assignees
		if (is_array($post['assignees']) && count($post['assignees']) > 0) {
			$query = "INSERT INTO #__users_messages ";
			$query .= " (id, userid, messageid) VALUES ";
			for ($i=0; $i<count($post['assignees']); $i++) {
				if ($i>0) { $query .= ","; }
				$query .= " (NULL, '".$post['assignees'][$i]."', '".$row->id."') ";
			}
			$this->db->setQuery($query);
			$this->db->query();
		}
		
		return $row;
	}
	
	function deleteMessage($projectid, $messageid) {
		//TODO: This function should allow ids as either int or array of ints.
		//TODO: This function should also check permissions before deleting
		
		// Delete message's comments
		$query = "DELETE FROM #__comments ";
		$query .= " WHERE projectid = ".$projectid." AND type = 'messages' AND itemid = ".$messageid;
		$this->db->setQuery($query);
		$this->db->query();
		
		// Delete message's assignees
		$query = "DELETE FROM #__users_messages ";
		$query .= " WHERE messageid = ".$messageid;
		$this->db->setQuery($query);
		$this->db->query();
		
		// Instantiate table object
		$row = new projectsTableMessages();
		
		// Delete row from database
		if (!$row->delete($messageid)) {
			JError::raiseError(500, $row->getError() );
			return false;
		}
		else {
			return true;
		}
	}
	
	function getAssignees($messageid) {
		$query = "SELECT userid FROM #__users_messages WHERE messageid = ".$messageid;
		$this->db->setQuery($query);
		$assignees = $this->db->loadResultArray();
		// Prepare assignee data
		for ($i=0; $i<count($assignees); $i++) {
			$new_assignees[$i]['id'] = $assignees[$i];
			$new_assignees[$i]['name'] = usersHelperUsers::id2name($assignees[$i]);
		}
		return $new_assignees;
	}
}
?>