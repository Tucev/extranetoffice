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
 * projectsModelIssues Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		model
 */
class projectsModelIssues extends model {
	/**
	 * Constructor
	 *
	 * @since	1.0
	 */
	function __construct() {
		//TODO: Check permissions
		parent::__construct();
	}
	
	/**
	 * This method gets issues for specified project
	 *
	 * @param int $projectid
	 * @param bool $overdue	If set to true it only returns overdue issues
	 * @return array	An array containing the rows, pageNav and filter lists
	 */
	function getIssues($projectid, $overdue=false) {
		$filter_order = request::getVar('filter_order', 'i.dtstart');
		$filter_order_Dir = request::getVar('filter_order_Dir', 'DESC');
		$search = request::getVar('search', '');
		$search = strtolower( $search );
		$limitstart = request::getVar('limitstart', 0);
		$limit = request::getVar('limit', 20);
		$filter_status = request::getVar('filter_status', 'open');
		$filter_assignees = request::getVar('filter_assignees', 'me');

		$where = array();
		
		// Show only public projects or projects where user has an assigned role
		$where[] = "( i.access = '0' OR (".$this->user->id." IN (SELECT userid FROM #__users_issues WHERE issueid = i.id) ) )";

		if ( $search ) {
			$where[] = "i.title LIKE '%".$this->db->getEscaped($search)."%'";
		}
		
		if (!empty($projectid)) {
			$where[] = "i.projectid = ".$projectid;	
		}
		
		if ($overdue === true) {
			$where[] = "i.dtend < '".date("Y-m-d")." 23:59:59'";
			$where[] = "i.closed = '0000-00-00 00:00:00'";	
		}
		
		if ($filter_status == 'open' && $overdue !== true) {
			$where[] = "i.closed = '0000-00-00 00:00:00'";	
		}
		elseif ($filter_status == 'closed' && $overdue !== true) {
			$where[] = "i.closed <> '0000-00-00 00:00:00'";	
		} 
		
		if ($filter_assignees) {
			//$where[] = "i.projectid = "
		}

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		if (empty($filter_order) || $filter_order == 'i.dtstart') {
			$orderby = ' ORDER BY i.dtstart DESC';
		} else {
			$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir.', i.dtstart DESC';
		}

		// get the total number of records
		// This query groups the files by parentid so and retireves the latest revision for each file in current project
		$query = "SELECT 
				  i.*, 
				  u.username AS created_by_name, 
				  GROUP_CONCAT(ui.userid) assignees
				  FROM #__issues AS i 
				  JOIN #__users u ON u.id = i.created_by 
				  LEFT JOIN #__users_issues ui ON i.id = ui.issueid "
				  . $where . 
				  " GROUP BY i.id ";
		//echo str_replace('#__', 'eo_', $query); exit;
		$this->db->setQuery( $query );
		$this->db->query();
		$total = $this->db->getNumRows();
		
		$pageNav = new pagination($total, $limitstart, $limit);

		// get the subset (based on limits) of required records
		$query .= $orderby." LIMIT ".$pageNav->limitstart.", ".$pageNav->limit;
		//echo str_replace('#__', 'eo_', $query); exit;
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
				$row->comments = $modelComments->getTotalComments($row->id, 'issues');
				
				// set status
				if ($row->closed != "0000-00-00 00:00:00") {
					$row->status = "closed";
				}
				elseif ($row->dtend < date("Y-m-d")." 23:59:59") {
					$row->status = "overdue";
				}
				else {
					$row->status = "open";
				}
			}
		}
		
		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;
		// search filter
		$lists['search'] = $search;
		// status filter
		$lists['status'] = $filter_status;
		// assignees filter
		$lists['assignees'] = $filter_assignees;
		
		// pack data into an array to return
		$return['rows'] = $rows;
		$return['pageNav'] = $pageNav;
		$return['lists'] = $lists;
		
		return $return;
	}
	
	function getIssuesDetail($projectid, $issueid) {
		$query = "SELECT i.*, u.username AS created_by_name ";
		$query .= " FROM #__issues AS i ";
		$query .= " JOIN #__users u ON u.id = i.created_by ";
		$query .= " WHERE i.id = ".$issueid;
		$query .= " ORDER BY i.created DESC";
		$this->db->setQuery($query);
		$row = $this->db->loadObject();
		
		// Get assignees
		$row->assignees = $this->getAssignees($issueid);
		
		// Get comments
		$modelComments =& $this->getModel('comments');
		$row->comments = $modelComments->getComments($projectid, 'issues', $issueid);
		
		return $row;
	}
	
	function saveIssue($projectid, $issueid=0) {
		$row = new projectsTableIssues();
		
		if (empty($issueid)) {
			$row->created_by = $this->user->id;
			$row->created = date("Y-m-d H:i:s");
			$new_issue = true;
		}
		else {
			$row->load($issueid);
		}
		
		$post = request::get('post');
		$row->bind($post);
		
		if (!$row->check()) {
			JError::raiseError(500, $row->getError() );
		}
	
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
		}
		
		// Delete existing assignees before we store new ones if editing existing issue
		if ($new_issue !== true) {
			$query = "DELETE FROM #__users_issues WHERE issueid = ".$row->id;
			$this->db->setQuery($query);
			$this->db->query();
		}
		
		// Store assignees
		if (is_array($post['assignees']) && count($post['assignees']) > 0) {
			$query = "INSERT INTO #__users_issues ";
			$query .= " (id, userid, issueid) VALUES ";
			for ($i=0; $i<count($post['assignees']); $i++) {
				if ($i>0) { $query .= ","; }
				$query .= " (NULL, '".$post['assignees'][$i]."', '".$row->id."') ";
			}
			$this->db->setQuery($query);
			$this->db->query();
		}
		
		return $row;
	}
	
	function deleteIssue($projectid, $issueid) {
		//TODO: This function should allow ids as either int or array of ints.
		//TODO: This function should also check permissions before deleting
		
		// Delete message's comments
		$query = "DELETE FROM #__comments ";
		$query .= " WHERE projectid = ".$projectid." AND type = 'issues' AND itemid = ".$issueid;
		$this->db->setQuery($query);
		$this->db->query();
		
		// Delete message's assignees
		$query = "DELETE FROM #__users_issues ";
		$query .= " WHERE issueid = ".$issueid;
		$this->db->setQuery($query);
		$this->db->query();
		
		// Instantiate table object
		$row = new projectsTableIssues();
		
		// Delete row from database
		if (!$row->delete($issueid)) {
			JError::raiseError(500, $row->getError() );
			return false;
		}
		else {
			return true;
		}
	}
	
	function getAssignees($issueid, $asoc=true) {
		$query = "SELECT userid FROM #__users_issues WHERE issueid = ".$issueid;
		$this->db->setQuery($query);
		$assignees = $this->db->loadResultArray();
		// return plain array if asoc is false
		if ($asoc === false) {
			return $assignees;
		}
		
		// Prepare assignee data
		for ($i=0; $i<count($assignees); $i++) {
			$asoc_assignees[$i]['id'] = $assignees[$i];
			$asoc_assignees[$i]['name'] = usersHelperUsers::id2name($assignees[$i]);
		}
		
		return $asoc_assignees;
	}
	
	function closeIssue($projectid, $issueid) {
		$query = "UPDATE #__issues ";
		$query .= " SET closed = '".date("Y-m-d H:i:s")."' WHERE id = ".$issueid;
		$this->db->setQuery($query);
		$this->db->query();
		
		$row = new projectsTableIssues();
		$row->load($issueid);
		return $row;
	}
	
	function reopenIssue($projectid, $issueid) {
		$query = "UPDATE #__issues ";
		$query .= " SET closed = '0000-00-00 00:00:00' WHERE id = ".$issueid;
		$this->db->setQuery($query);
		$this->db->query();
		
		$row = new projectsTableIssues();
		$row->load($issueid);
		return $row;
	}
	
	function getTotalIssues($projectid, $overdue=false) {
		$query = "SELECT COUNT(id) FROM #__issues ";
		$query .= " WHERE projectid = ".$projectid;
		if ($overdue === true) { 
			$query .= " AND dtend < '".date("Y-m-d")." 23:59:59' AND closed = '0000-00-00 00:00:00'"; 
		}
		$this->db->setQuery($query);
		return $this->db->loadResult();
	}
}
?>