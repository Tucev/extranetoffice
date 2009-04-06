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
 * projectsModelMilestones Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		model
 */
class projectsModelMilestones extends phpFrame_Application_Model {
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
	public function getMilestones($projectid) {
		$filter_order = phpFrame_Environment_Request::getVar('filter_order', 'm.due_date');
		$filter_order_Dir = phpFrame_Environment_Request::getVar('filter_order_Dir', 'DESC');
		$search = phpFrame_Environment_Request::getVar('search', '');
		$search = strtolower( $search );
		$limitstart = phpFrame_Environment_Request::getVar('limitstart', 0);
		$limit = phpFrame_Environment_Request::getVar('limit', 20);

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
				  u.username AS created_by_name 
				  FROM #__milestones AS m 
				  JOIN #__users u ON u.id = m.created_by "
				  . $where . 
				  " GROUP BY m.id ";
		//echo $query; exit;	  
		$this->db->setQuery( $query );
		$this->db->query();
		$total = $this->db->getNumRows();

		
		$pageNav = new phpFrame_HTML_Pagination( $total, $limitstart, $limit );

		// get the subset (based on limits) of required records
		$query .= $orderby." LIMIT ".$pageNav->limitstart.", ".$pageNav->limit;
		//echo $query; exit;
		$this->db->setQuery($query);
		$rows = $this->db->loadObjectList();
		
		// Prepare rows and add relevant data
		if (is_array($rows) && count($rows) > 0) {
			foreach ($rows as $row) {
				// Get assignees
				$row->assignees = $this->getAssignees($row->id);
				
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
	public function getMilestonesDetail($projectid, $milestoneid) {
		$query = "SELECT m.*, u.username AS created_by_name ";
		$query .= " FROM #__milestones AS m ";
		$query .= " JOIN #__users u ON u.id = m.created_by ";
		$query .= " WHERE m.id = ".$milestoneid;
		$query .= " ORDER BY m.due_date DESC";
		$this->db->setQuery($query);
		$row = $this->db->loadObject();
		
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
		
		// Get assignees
		$row->assignees = $this->getAssignees($milestoneid);
		
		// Get comments
		$modelComments =& $this->getModel('comments');
		$row->comments = $modelComments->getComments($projectid, 'milestones', $milestoneid);
		
		return $row;
	}
	
	/**
	 * Save a project milestone
	 * 
	 * @param	$post	The array to be used for binding to the row before storing it. Normally the HTTP_POST array.
	 * @return	mixed	Returns the stored table row object on success or FALSE on failure
	 */
	public function saveMilestone($post) {
		// Check whether a project id is included in the post array
		if (empty($post['projectid'])) {
			$this->error[] = _LANG_ERROR_NO_PROJECT_SELECTED;
			return false;
		}
		
		$row =& phpFrame::getInstance("projectsTableMilestones");
		
		if (!$row->bind($post)) {
			$this->error[] = $row->getLastError();
			return false;
		}
		
		if (empty($row->id)) {
			$row->created_by = $this->user->id;
			$row->created = date("Y-m-d H:i:s");
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
	public function deleteMilestone($projectid, $milestoneid) {
		//TODO: This function should allow ids as either int or array of ints.
		//TODO: This function should also check permissions before deleting
		
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
	 * Get list of assignees
	 *
	 * @param	int		$milestoneid
	 * @param	bool	$asoc
	 * @return	array	Array containing assignees ids or asociative array with id, name and email if asoc is true.
	 */
	public function getAssignees($milestoneid, $asoc=true) {
		$query = "SELECT um.userid, u.firstname, u.lastname, u.email";
		$query .= " FROM #__users_milestones AS um ";
		$query .= "LEFT JOIN #__users u ON u.id = um.userid";
		$query .= " WHERE um.milestoneid = ".$milestoneid;
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