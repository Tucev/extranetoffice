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
 * projectsModelMeetings Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		model
 */
class projectsModelMeetings extends model {
	/**
	 * Constructor
	 *
	 * @since 1.0.1
	 */
	function __construct() {
		//TODO: Check permissions
		parent::__construct();
	}
	
	function getMeetings($projectid) {
		$filter_order = request::getVar('filter_order', 'm.created');
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
			$where[] = "m.name LIKE '%".$this->db->getEscaped($search)."%'";
		}
		
		if (!empty($projectid)) {
			$where[] = "m.projectid = ".$projectid;	
		}

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		if ($filter_order == 'm.created'){
			$orderby = ' ORDER BY m.created DESC';
		} else {
			$orderby = ' ORDER BY m.created DESC, '. $filter_order .' '. $filter_order_Dir;
		}

		// get the total number of records
		// This query groups the files by parentid so and retireves the latest revision for each file in current project
		$query = "SELECT 
				  m.*, 
				  u.username AS created_by_name, 
				  GROUP_CONCAT(um.userid) assignees
				  FROM #__meetings AS m 
				  JOIN #__users u ON u.id = m.created_by 
				  LEFT JOIN #__users_meetings um ON m.id = um.meetingid "
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
		
		// Prepare assignee data
		if (is_array($rows) && count($rows) > 0) {
			foreach ($rows as $row) {
				if (!empty($row->assignees)) {
					$assignees = explode(',', $row->assignees);
					for ($i=0; $i<count($assignees); $i++) {
						$new_assignees[$i]['id'] = $assignees[$i];
						$new_assignees[$i]['name'] = usersHelper::id2name($assignees[$i]);
					}
					$row->assignees = $new_assignees;
					unset($new_assignees);
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
	
	function getMeetingsDetail($projectid, $meetingid) {
		$query = "SELECT m.*, u.username AS created_by_name ";
		$query .= " FROM #__meetings AS m ";
		$query .= " JOIN #__users u ON u.id = m.created_by ";
		$query .= " WHERE m.id = ".$meetingid;
		$query .= " ORDER BY m.created DESC";
		$this->db->setQuery($query);
		$row = $this->db->loadObject();
		
		// Get assignees
		$row->assignees = $this->getAssignees($meetingid);
		
		// get slideshows
		$row->slideshows = $this->getSlideshows($projectid, $meetingid);
		
		// Get comments
		$modelComments =& $this->getModel('comments');
		$row->comments = $modelComments->getComments($projectid, 'meetings', $meetingid);
		
		return $row;
	}
	
	function saveMeeting($projectid, $meetingid=0) {
		require_once COMPONENT_PATH.DS."tables".DS."meetings.table.php";		
		$row =& phpFrame::getInstance("projectsTableMeetings");
		
		if (empty($meetingid)) {
			$row->created_by = $this->user->id;
			$row->created = date("Y-m-d H:i:s");
			$new_meeting = true;
		}
		else {
			$row->load($meetingid);
		}
		
		$post = request::get('post');
		$row->bind($post);
		
		if (!$row->check()) {
			error::raise(500, 'error', $row->error);
		}
		
		$row->store();
		
		// Delete existing assignees before we store new ones if editing existing issue
		if ($new_meeting !== true) {
			$query = "DELETE FROM #__users_meetings WHERE meetingid = ".$row->id;
			$this->db->setQuery($query);
			$this->db->query();
		}
		
		// Store assignees
		if (is_array($post['assignees']) && count($post['assignees']) > 0) {
			$query = "INSERT INTO #__users_meetings ";
			$query .= " (id, userid, meetingid) VALUES ";
			for ($i=0; $i<count($post['assignees']); $i++) {
				if ($i>0) { $query .= ","; }
				$query .= " (NULL, '".$post['assignees'][$i]."', '".$row->id."') ";
			}
			$this->db->setQuery($query);
			$this->db->query();
		}
		
		if (!empty($row->id)) {
			error::raise('', 'message', _LANG_MEETING_SAVED);
			return $row;
		}
		else {
			error::raise('', 'error', _LANG_MEETING_SAVE_ERROR);
			return false;
		}
	}
	
	/**
	 * Delete a project meeting
	 * 
	 * This method also deletes the entries from users_meetings and any comments associated with the meeting.
	 * 
	 * @param	int		$projectid	The project id.
	 * @param	int		$meetingid	The id of the meeting we want to delete.
	 * @return	bool
	 */
	function deleteMeeting($projectid, $meetingid) {
		//TODO: This function should allow ids as either int or array of ints.
		//TODO: This function should also check permissions before deleting
		
		// Delete meetings comments
		$query = "DELETE FROM #__comments ";
		$query .= " WHERE projectid = ".$projectid." AND type = 'meetings' AND itemid = ".$meetingid;
		$this->db->setQuery($query);
		if (!$this->db->query()) {
			$this->error[] = $this->db->getLastError();
			return false;
		}
		
		// Delete message's assignees
		$query = "DELETE FROM #__users_meetings ";
		$query .= " WHERE meetingid = ".$meetingid;
		$this->db->setQuery($query);
		if (!$this->db->query()) {
			$this->error[] = $this->db->getLastError();
			return false;
		}
		
		// Instantiate table object
		require_once COMPONENT_PATH.DS."tables".DS."meetings.table.php";
		$row =& phpFrame::getInstance("projectsTableMeetings");
		
		// Delete row from database
		if (!$row->delete($meetingid)) {
			$this->error[] = $row->getLastError();
			return false;
		}
		else {
			return true;
		}
	}
	
	function getAssignees($meetingid) {
		$query = "SELECT userid FROM #__users_meetings WHERE meetingid = ".$meetingid;
		$this->db->setQuery($query);
		$assignees = $this->db->loadResultArray();
		// Prepare assignee data
		for ($i=0; $i<count($assignees); $i++) {
			$new_assignees[$i]['id'] = $assignees[$i];
			$new_assignees[$i]['name'] = usersHelper::id2name($assignees[$i]);
		}
		return $new_assignees;
	}
	
	function getSlideshows($projectid, $meetingid, $slideshowid=0) {
		$query = "SELECT * ";
		$query .= " FROM #__slideshows ";
		$query .= " WHERE projectid = ".$projectid." AND meetingid = ".$meetingid;
		if (!empty($slideshowid)) $query .= " AND id = ".$slideshowid; 
		$this->db->setQuery($query);
		$slideshows = $this->db->loadObjectList();
		
		// Get slideshows slides
		for ($i=0; $i<count($slideshows); $i++) {
			$query = "SELECT * ";
			$query .= " FROM #__slideshows_slides ";
			$query .= " WHERE slideshowid = ".$slideshows[$i]->id;
			$this->db->setQuery($query);
			$slideshows[$i]->slides = $this->db->loadObjectList();
		}
		
		return $slideshows;
	}
}
?>