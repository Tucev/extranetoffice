<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
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
 * @todo		check for NULL values rather than 00-00-000 00:00
 */
class projectsModelIssues extends phpFrame_Application_Model {
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
	public function getIssues($projectid, $overdue=false) {
		$filter_order = phpFrame_Environment_Request::getVar('filter_order', 'i.dtstart');
		$filter_order_Dir = phpFrame_Environment_Request::getVar('filter_order_Dir', 'DESC');
		$search = phpFrame_Environment_Request::getVar('search', '');
		$search = strtolower( $search );
		$limitstart = phpFrame_Environment_Request::getVar('limitstart', 0);
		$limit = phpFrame_Environment_Request::getVar('limit', 20);
		$filter_status = phpFrame_Environment_Request::getVar('filter_status', 'all');
		$filter_assignees = phpFrame_Environment_Request::getVar('filter_assignees', 'me');

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
				  u.username AS created_by_name 
				  FROM #__issues AS i 
				  JOIN #__users u ON u.id = i.created_by "
				  . $where . 
				  " GROUP BY i.id ";
		//echo str_replace('#__', 'eo_', $query); exit;
		$this->db->setQuery( $query );
		$this->db->query();
		$total = $this->db->getNumRows();
		
		$pageNav = new phpFrame_HTML_Pagination($total, $limitstart, $limit);

		// get the subset (based on limits) of required records
		$query .= $orderby." LIMIT ".$pageNav->limitstart.", ".$pageNav->limit;
		//echo str_replace('#__', 'eo_', $query); exit;
		$this->db->setQuery($query);
		$rows = $this->db->loadObjectList();
		
		// Prepare rows and add relevant data
		if (is_array($rows) && count($rows) > 0) {
			foreach ($rows as $row) {
				// Get assignees
				$row->assignees = $this->getAssignees($row->id);
				
				// get total comments
				$modelComments = $this->getModel('comments');
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
	
	/**
	 * Get issues detail
	 *
	 * @param int $projectid
	 * @param int $issueid
	 * @return mixed returns $row on success and FALSE on failure
	 */
	public function getIssuesDetail($projectid, $issueid) {
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
		$modelComments = $this->getModel('comments');
		$row->comments = $modelComments->getComments($projectid, 'issues', $issueid);
		
		return $row;
	}
	
	/**
	 * Save a project issue
	 * 
	 * @param	$post	The array to be used for binding to the row before storing it. Normally the HTTP_POST array.
	 * @return	mixed	Returns the stored table row object on success or FALSE on failure
	 */
	public function saveIssue($post) {
		// Check whether a project id is included in the post array
		if (empty($post['projectid'])) {
			$this->error[] = _LANG_ERROR_NO_PROJECT_SELECTED;
			return false;
		}
			
		$row =& phpFrame_Base_Singleton::getInstance("projectsTableIssues");
		
		if (empty($post['id'])) {
			$row->created_by = $this->user->id;
			$row->created = date("Y-m-d H:i:s");
		}
		else {
			$row->load($post['id']);
		}
		
		if (!$row->bind($post)) {
			$this->error[] = $row->getLastError();
			return false;
		}
		
		if (!$row->check()) {
			$this->error[] = $row->getLastError();
			return false;
		}
	
		if (!$row->store()) {
			$this->error[] = $row->getLastError();
			return false;
		}
		
		// Delete existing assignees before we store new ones if editing existing issue
		if (!empty($post['id'])) {
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
	
	/**
	 * Delete issue
	 *
	 * @param	int		$projectid	The project id.
	 * @param	int		$issueid	The id of the issue we want to delete.
	 * @return	bool	Returns TRUE on success or FALSE on error
	 */
	public function deleteIssue($projectid, $issueid) {
		//TODO: This function should allow ids as either int or array of ints.
		//TODO: This function should also check permissions before deleting
		
		// Delete message's comments
		$query = "DELETE FROM #__comments ";
		$query .= " WHERE projectid = ".$projectid." AND type = 'issues' AND itemid = ".$issueid;
		$this->db->setQuery($query);
		if (!$this->db->query()) {
			$this->error[] = $this->db->getLastError();
			return false;
		}
		
		// Delete message's assignees
		$query = "DELETE FROM #__users_issues ";
		$query .= " WHERE issueid = ".$issueid;
		$this->db->setQuery($query);
		if (!$this->db->query()) {
			$this->error[] = $this->db->getLastError();
			return false;
		}
		
		// Instantiate table 
		$row =& phpFrame_Base_Singleton::getInstance("projectsTableIssues");
		
		// Delete row from database
		if (!$row->delete($issueid)) {
			$this->error[] = $row->getLastError();
			return false;
		}
		else {
			return true;
		}
	}
	
	/**
	 * Close an issue
	 *
	 * @param	int		$projectid
	 * @param	int		$issueid
	 * @return	mixed	Returns issues row object or FALSE on failure.
	 */
	public function closeIssue($projectid, $issueid) {
		$query = "UPDATE #__issues ";
		$query .= " SET closed = '".date("Y-m-d H:i:s")."' WHERE id = ".$issueid;
		$this->db->setQuery($query);
		if (!$this->db->query()) {
			$this->error[] = $this->db->getLastError();
			return false;
		}
		
		$row =& phpFrame_Base_Singleton::getInstance("projectsTableIssues");
		$row->load($issueid);
		return $row;
	}
	
	/**
	 * Reopen an issue
	 *
	 * @param	int		$projectid
	 * @param	int		$issueid
	 * @return	mixed	Returns issues row object or FALSE on failure.
	 */
	public function reopenIssue($projectid, $issueid) {
		$query = "UPDATE #__issues ";
		$query .= " SET closed = '0000-00-00 00:00:00' WHERE id = ".$issueid;
		$this->db->setQuery($query);
		if (!$this->db->query()) {
			$this->error[] = $this->db->getLastError();
			return false;
		}
		
		$row =& phpFrame_Base_Singleton::getInstance("projectsTableIssues");
		$row->load($issueid);
		return $row;
	}
	
	/**
	 * Get issue count
	 *
	 * @param	int		$projectid
	 * @param	bool	$overdue
	 * @return	mixed	Returns numeric resource or FALSE on failure.
	 */
	public function getTotalIssues($projectid, $overdue=false) {
		$query = "SELECT COUNT(id) FROM #__issues ";
		$query .= " WHERE projectid = ".$projectid;
		if ($overdue === true) { 
			$query .= " AND dtend < '".date("Y-m-d")." 23:59:59' AND closed = '0000-00-00 00:00:00'"; 
		}
		$this->db->setQuery($query);
		return $this->db->loadResult();
	}
	
	/**
	 * Get list of assignees
	 *
	 * @param	int		$issueid
	 * @param	bool	$asoc
	 * @return	array	Array containing assignees ids or asociative array with id, name and email if asoc is true.
	 */
	public function getAssignees($issueid, $asoc=true) {
		$query = "SELECT ui.userid, u.firstname, u.lastname, u.email";
		$query .= " FROM #__users_issues AS ui ";
		$query .= "LEFT JOIN #__users u ON u.id = ui.userid";
		$query .= " WHERE ui.issueid = ".$issueid;
		$this->db->setQuery($query);
		$assignees = $this->db->loadObjectList();
		
		// Prepare assignee data
		for ($i=0; $i<count($assignees); $i++) {
			if ($asoc === false) {
				$new_assignees[$i] = $assignees[$i]->userid;
			}
			else {
				$new_assignees[$i]['id'] = $assignees[$i]->userid;
				$new_assignees[$i]['name'] = phpFrame_User_Helper::fullname_format($assignees[$i]->firstname, $assignees[$i]->lastname);
				$new_assignees[$i]['email'] = $assignees[$i]->email;
			}
		}
		
		return $new_assignees;
	}
}
?>