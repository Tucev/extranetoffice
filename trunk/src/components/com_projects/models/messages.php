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
 * projectsModelMessages Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		phpFrame_Application_Model
 */
class projectsModelMessages extends phpFrame_Application_Model {
	/**
	 * Constructor
	 *
	 * @since 1.0.1
	 */
	function __construct() {}
	
	/**
	 * Get messages
	 * 
	 * @param	object	$list_filter	Object of type phpFrame_Database_CollectionFilter
	 * @param	int		$projectid
	 * @return	array
	 */
	public function getMessages(phpFrame_Database_CollectionFilter $list_filter, $projectid) {
		$where = array();
		
		// Show only public projects or projects where user has an assigned role
		//TODO: Have to apply access levels
		//$where[] = "( p.access = '0' OR (".phpFrame::getUser()->id." IN (SELECT userid FROM #__users_roles WHERE projectid = p.id) ) )";
		
		$search = $list_filter->getSearchStr();
		if ( $search ) {
			$where[] = "m.subject LIKE '%".phpFrame::getDB()->getEscaped($search)."%'";
		}
		
		if (!empty($projectid)) {
			$where[] = "m.projectid = ".$projectid;	
		}

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		// get the total number of records
		// This query groups the files by parentid so and retireves the latest revision for each file in current project
		$query = "SELECT 
				  m.*, 
				  u.username AS created_by_name 
				  FROM #__messages AS m 
				  JOIN #__users u ON u.id = m.userid "
				  . $where . 
				  " GROUP BY m.id ";
		//echo str_replace('#__', 'eo_', $query); exit;
		  
		phpFrame::getDB()->setQuery( $query );
		phpFrame::getDB()->query();
		
		$total = phpFrame::getDB()->getNumRows();

		// Set total number of records in list filter
		$list_filter->setTotal(phpFrame::getDB()->getNumRows());
		
		// Add order by and limit statements for subset (based on filter)
		$query .= $list_filter->getOrderByStmt();
		$query .= $list_filter->getLimitStmt();
		//echo str_replace('#__', 'eo_', $query); exit;
		
		phpFrame::getDB()->setQuery($query);
		$rows = phpFrame::getDB()->loadObjectList();
		
		// Prepare rows and add relevant data
		if (is_array($rows) && count($rows) > 0) {
			foreach ($rows as $row) {
				// Get assignees
				$row->assignees = $this->getAssignees($row->id);
				
				// get total comments
				$modelComments = phpFrame::getModel('com_projects', 'comments');
				$row->comments = $modelComments->getTotalComments($row->id, 'messages');
			}
		}
		
		return $rows;
	}
	
	public function getMessagesDetail($projectid, $messageid) {
		$query = "SELECT m.*, u.username AS created_by_name ";
		$query .= " FROM #__messages AS m ";
		$query .= " JOIN #__users u ON u.id = m.userid ";
		$query .= " WHERE m.id = ".$messageid;
		$query .= " ORDER BY m.date_sent DESC";
		phpFrame::getDB()->setQuery($query);
		$row = phpFrame::getDB()->loadObject();
		
		// Get assignees
		$row->assignees = $this->getAssignees($messageid);
		
		// Get comments
		$modelComments = phpFrame::getModel('com_projects', 'comments');
		$row->comments = $modelComments->getComments($projectid, 'messages', $messageid);
		
		return $row;
	}
	
	/**
	 * Save a project message
	 * 
	 * @param	$post	The array to be used for binding to the row before storing it. Normally the HTTP_POST array.
	 * @return	mixed	Returns the stored table row object on success or FALSE on failure
	 */
	public function saveMessage($post) {
		// Check whether a project id is included in the post array
		if (empty($post['projectid'])) {
			$this->_error[] = _LANG_ERROR_NO_PROJECT_SELECTED;
			return false;
		}
		
		$row = $this->getTable('messages');
		
		if (!$row->bind($post)) {
			$this->_error[] = $row->getLastError();
			return false;
		}
		
		if (empty($row->id)) {
			$row->userid = phpFrame::getUser()->id;
			$row->date_sent = date("Y-m-d H:i:s");
			$row->status = '1';
		}
		
		if (!$row->check()) {
			$this->_error[] = $row->getLastError();
			return false;
		}
	
		if (!$row->store()) {
			$this->_error[] = $row->getLastError();
			return false;
		}
		
		// Delete existing assignees before we store new ones if editing existing issue
		if (!empty($post['id'])) {
			$query = "DELETE FROM #__users_messages WHERE messageid = ".$row->id;
			phpFrame::getDB()->setQuery($query);
			phpFrame::getDB()->query();
		}
		
		// Store assignees
		if (is_array($post['assignees']) && count($post['assignees']) > 0) {
			$query = "INSERT INTO #__users_messages ";
			$query .= " (id, userid, messageid) VALUES ";
			for ($i=0; $i<count($post['assignees']); $i++) {
				if ($i>0) { $query .= ","; }
				$query .= " (NULL, '".$post['assignees'][$i]."', '".$row->id."') ";
			}
			phpFrame::getDB()->setQuery($query);
			phpFrame::getDB()->query();
		}
		
		return $row;
	}
	
	public function deleteMessage($projectid, $messageid) {
		//TODO: This function should allow ids as either int or array of ints.
		//TODO: This function should also check permissions before deleting
		
		// Delete message's comments
		$query = "DELETE FROM #__comments ";
		$query .= " WHERE projectid = ".$projectid." AND type = 'messages' AND itemid = ".$messageid;
		phpFrame::getDB()->setQuery($query);
		phpFrame::getDB()->query();
		
		// Delete message's assignees
		$query = "DELETE FROM #__users_messages ";
		$query .= " WHERE messageid = ".$messageid;
		phpFrame::getDB()->setQuery($query);
		phpFrame::getDB()->query();
		
		// Instantiate table object
		$row = $this->getTable('messages');
		
		// Delete row from database
		if (!$row->delete($messageid)) {
			$this->_error[] = $row->getLastError();
			return false;
		}
		else {
			return true;
		}
	}
	
	/**
	 * Get list of assignees
	 *
	 * @param	int		$messageid
	 * @param	bool	$asoc
	 * @return	array	Array containing assignees ids or asociative array with id, name and email if asoc is true.
	 */
	public function getAssignees($messageid, $asoc=true) {
		$query = "SELECT um.userid, u.firstname, u.lastname, u.email";
		$query .= " FROM #__users_messages AS um ";
		$query .= "LEFT JOIN #__users u ON u.id = um.userid";
		$query .= " WHERE um.messageid = ".$messageid;
		phpFrame::getDB()->setQuery($query);
		$assignees = phpFrame::getDB()->loadObjectList();
		
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
