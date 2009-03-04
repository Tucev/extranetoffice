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
 * projectsModelMilestones Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		model
 */
class projectsModelMilestones extends model {
	/**
	 * Constructor
	 *
	 * @since 1.0.1
	 */
	function __construct() {
		//TODO: Check permissions
		parent::__construct();
	}
	
	/**
	 * Gets milestones list info
	 *
	 * @param int $projectid
	 * @return array
	 */
	function getMilestones($projectid) {
		$filter_order = request::getVar('filter_order', 'm.due_date');
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
			$where[] = "m.title LIKE '%".$this->db->getEscaped($search)."%'";
		}
		
		if (!empty($projectid)) {
			$where[] = "m.projectid = ".$projectid;	
		}

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		if ($filter_order == 'm.due_date'){
			$orderby = ' ORDER BY m.due_date DESC';
		} else {
			$orderby = ' ORDER BY m.due_date DESC, '. $filter_order .' '. $filter_order_Dir;
		}

		// get the total number of records
		// This query groups the files by parentid so and retireves the latest revision for each file in current project
		$query = "SELECT 
				  m.*, 
				  u.username AS created_by_name, 
				  GROUP_CONCAT(um.userid) assignees
				  FROM #__milestones AS m 
				  JOIN #__users u ON u.id = m.created_by  
				  LEFT JOIN #__users_milestones um ON m.id = um.milestoneid "
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
				$row->comments = $modelComments->getTotalComments($row->id, 'milestones');
				
				// Sort out status according to due date
				if ($row->due_date < date("Y-m-d H:i:s") && $row->closed == '0000-00-00 00:00:00') {
					$row->due_date_class = 'overdue';
					$row->status = _LANG_STATUS_OVERDUE;
				}
				elseif ($row->due_date > date("Y-m-d H:i:s") && $row->closed == '0000-00-00 00:00:00') {
					$row->due_date_class = 'open';
					$row->status = _LANG_STATUS_UPCOMING;
				}
				elseif ($row->closed != '0000-00-00 00:00:00') {
					$row->due_date_class = 'closed';
					$row->status = _LANG_STATUS_CLOSED;
				}
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
	
	/**
	 * Gets detailed milestone information
	 *
	 * @param int $projectid
	 * @param int $milestoneid
	 * @return object table row containing milestone info
	 */
	function getMilestonesDetail($projectid, $milestoneid) {
		$query = "SELECT m.*, u.username AS created_by_name ";
		$query .= " FROM #__milestones AS m ";
		$query .= " JOIN #__users u ON u.id = m.created_by ";
		$query .= " WHERE m.id = ".$milestoneid;
		$query .= " ORDER BY m.due_date DESC";
		$this->db->setQuery($query);
		$row = $this->db->loadObject();
		
		// Sort out status according to due date
		if ($row->due_date < date("Y-m-d H:i:s") && $row->closed == null) {
			$row->due_date_class = 'overdue_milestone';
			$row->status = _LANG_STATUS_OVERDUE;
		}
		elseif ($row->due_date > date("Y-m-d H:i:s") && $row->closed == null) {
			$row->due_date_class = 'upcoming_milestone';
			$row->status = _LANG_STATUS_UPCOMING;
		}
		elseif ($row->closed != null) {
			$row->due_date_class = 'closed_milestone';
			$row->status = _LANG_STATUS_CLOSED;
		}
		
		// Get assignees
		$row->assignees = $this->getAssignees($milestoneid);
		
		// Get comments
		$modelComments =& $this->getModel('comments');
		$row->comments = $modelComments->getComments($projectid, 'milestones', $milestoneid);
		
		return $row;
	}
	
	/**
	 * Saves a milestone
	 *
	 * @param int $projectid
	 * @return mixed return $row or FALSE on failure
	 */
	function saveMilestone($projectid) {
		require_once COMPONENT_PATH.DS."tables".DS."milestones.table.php";
		$row =& phpFrame::getInstance("projectsTableMilestones");
		
		$post = request::get('post');
		$row->bind($post);
		
		if (empty($row->id)) {
			$row->created_by = $this->user->id;
			$row->created = date("Y-m-d H:i:s");
			$new_milestone = true;
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
		if ($new_milestone !== true) {
			$query = "DELETE FROM #__users_milestones WHERE milestoneid = ".$row->id;
			$this->db->setQuery($query);
			$this->db->query();
		}
		
		// Store assignees
		if (is_array($post['assignees']) && count($post['assignees']) > 0) {
			$query = "INSERT INTO #__users_milestones ";
			$query .= " (id, userid, milestoneid) VALUES ";
			for ($i=0; $i<count($post['assignees']); $i++) {
				if ($i>0) { $query .= ","; }
				$query .= " (NULL, '".$post['assignees'][$i]."', '".$row->id."') ";
			}
			$this->db->setQuery($query);
			$this->db->query();
		}
		
		return $row;
	}
	
	/**
	 * Delete a milestone
	 *
	 * @param int $projectid
	 * @param int $milestoneid
	 * @return bool
	 */
	function deleteMilestone($projectid, $milestoneid) {
		//TODO: This function should allow ids as either int or array of ints.
		//TODO: This function should also check permissions before deleting
		
		require_once COMPONENT_PATH.DS."tables".DS."milestones.table.php";
		
		// Delete message's comments
		$query = "DELETE FROM #__comments ";
		$query .= " WHERE projectid = ".$projectid." AND type = 'milestones' AND itemid = ".$milestoneid;
		$this->db->setQuery($query);
		$this->db->query();
		
		// Delete message's assignees
		$query = "DELETE FROM #__users_milestones ";
		$query .= " WHERE milestoneid = ".$milestoneid;
		$this->db->setQuery($query);
		$this->db->query();
		
		// Instantiate table object
		$row =& phpFrame::getInstance("projectsTableMilestones");
		
		// Delete row from database
		if (!$row->delete($milestoneid)) {
			$this->error = $row->error;
			return false;
		}
		else {
			return true;
		}
	}
	
	/**
	 * Retreives assignees as an array
	 *
	 * @param integer $milestoneid
	 * @return array signees
	 */
	function getAssignees($milestoneid) {
		$query = "SELECT userid FROM #__users_milestones WHERE milestoneid = ".$milestoneid;
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