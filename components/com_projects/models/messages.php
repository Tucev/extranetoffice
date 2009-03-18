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
	/**
	 * Constructor
	 *
	 * @since 1.0.1
	 */
	function __construct() {
		//TODO: Check permissions
		parent::__construct();
	}
	
	public function getMessages($projectid) {
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
				  u.username AS created_by_name 
				  FROM #__messages AS m 
				  JOIN #__users u ON u.id = m.userid "
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
				// Get assignees
				$row->assignees = $this->_getAssignees($row->id);
				
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
	
	public function getMessagesDetail($projectid, $messageid) {
		$query = "SELECT m.*, u.username AS created_by_name ";
		$query .= " FROM #__messages AS m ";
		$query .= " JOIN #__users u ON u.id = m.userid ";
		$query .= " WHERE m.id = ".$messageid;
		$query .= " ORDER BY m.date_sent DESC";
		$this->db->setQuery($query);
		$row = $this->db->loadObject();
		
		// Get assignees
		$row->assignees = $this->_getAssignees($messageid);
		
		// Get comments
		$modelComments =& $this->getModel('comments');
		$row->comments = $modelComments->getComments($projectid, 'messages', $messageid);
		
		return $row;
	}
	
	public function saveMessage($projectid) {
		require_once COMPONENT_PATH.DS."tables".DS."messages.table.php";		
		$row =& phpFrame::getInstance("projectsTableMessages");
				
		$post = request::get('post');
		$row->bind($post);
		
		if (empty($row->id)) {
			$row->userid = $this->user->id;
			$row->date_sent = date("Y-m-d H:i:s");
			$row->status = '1';
			$new_message = true;
		}
		
		if (!$row->check()) {
			$this->error =& $row->error;
			return false;
		}
	
		if (!$row->store()) {
			$this->error =& $row->error;
			return false;
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
	
	public function deleteMessage($projectid, $messageid) {
		//TODO: This function should allow ids as either int or array of ints.
		//TODO: This function should also check permissions before deleting
		require_once COMPONENT_PATH.DS."tables".DS."messages.table.php";
		
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
			$this->error =& $row->error;
			return false;
		}
		else {
			return true;
		}
	}
	
	private function _getAssignees($messageid) {
		$query = "SELECT userid FROM #__users_messages WHERE messageid = ".$messageid;
		$this->db->setQuery($query);
		$assignees = $this->db->loadResultArray();
		// Prepare assignee data
		for ($i=0; $i<count($assignees); $i++) {
			$new_assignees[$i]['id'] = $assignees[$i];
			$new_assignees[$i]['name'] = usersHelper::id2name($assignees[$i]);
		}
		return $new_assignees;
	}
}
?>