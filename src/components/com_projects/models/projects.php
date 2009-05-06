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
 * projectsModelProjects Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		phpFrame_Application_Model
 */
class projectsModelProjects extends phpFrame_Application_Model {
	/**
	 * Constructor
	 *
	 * @return	void
	 * @since	1.0
	 */
	function __construct() {
		// Check whether project directories exists and are writable
		$projects_upload_dir = _ABS_PATH.DS.config::UPLOAD_DIR.DS."projects";
		//TODO: Have to catch errors here and look at file permissions
		if (!is_dir($projects_upload_dir)) {
			mkdir($projects_upload_dir, 0771);
		}
		$projects_filesystem = config::FILESYSTEM.DS."projects";
		if (!is_dir($projects_filesystem)) {
			mkdir($projects_filesystem, 0771);
		}
			
		// Check whether project specific directories exists and are writable
		$projectid = phpFrame_Environment_Request::getVar('projectid', 0);
		if (!empty($projectid)) {
			//TODO: Have to catch errors here and look at file permissions
			$project_specific_upload_dir = _ABS_PATH.DS.config::UPLOAD_DIR.DS."projects".DS.$projectid;
			if (!is_dir($project_specific_upload_dir)) {
				mkdir($project_specific_upload_dir, 0771);
			}
			$project_specific_filesystem = config::FILESYSTEM.DS."projects".DS.$projectid;
			if (!is_dir($project_specific_filesystem)) {
				mkdir($project_specific_filesystem, 0771);
			}
		}
	}
	
	/**
	 * Get projects.
	 * 
	 * @param $userid
	 * @return mixed	if no parameters returns array of objects if entries exist 
	 */
	public function getProjects(phpFrame_Database_Listfilter $list_filter, $userid=0) {
		// Build WHERE SQL clauses
		$where = array();
		
		// Show only public projects or projects where user has an assigned role
		if (empty($userid)) {
			$userid = phpFrame::getUser()->id;
		}
		$where[] = "( p.access = '0' OR (".$userid." IN (SELECT userid FROM #__users_roles WHERE projectid = p.id) ) )";
		
		$search = $list_filter->getSearchStr();
		if ($search) {
			$where[] = "p.name LIKE '%".phpFrame::getDB()->getEscaped($search)."%'";
		}

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		
		// get the total number of records
		$query = "SELECT 
				  p.id
				  FROM #__projects AS p 
				  JOIN #__users u ON u.id = p.created_by 
				  LEFT JOIN #__project_types pt ON pt.id = p.project_type  
				  LEFT JOIN #__users_roles ur ON p.id = ur.projectid "
				  . $where . 
				  " GROUP BY p.id ";
				  
		phpFrame::getDB()->setQuery($query);
		phpFrame::getDB()->query();
		
		// Set total number of record in list filter
		$list_filter->setTotal(phpFrame::getDB()->getNumRows());
		
		// get the subset (based on limits) of required records
		$query = "SELECT 
				  p.*, 
				  u.username AS created_by_name, 
				  pt.name AS project_type_name, 
				  GROUP_CONCAT(ur.userid) members
				  FROM #__projects AS p 
				  JOIN #__users u ON u.id = p.created_by 
				  LEFT JOIN #__project_types pt ON pt.id = p.project_type  
				  LEFT JOIN #__users_roles ur ON p.id = ur.projectid "
				  . $where . 
				  " GROUP BY p.id ";
		
		// Add order by and limit statements for subset (based on filter)
		//$query .= $list_filter->getOrderByStmt();
		$query .= $list_filter->getLimitStmt();
		//echo str_replace('#__', 'eo_', $query); exit;
		
		phpFrame::getDB()->setQuery($query);
		return phpFrame::getDB()->loadObjectList();
	}
	
	public function getProjectsDetail($projectid, $userid=0) {
		if (empty($projectid)) {
			$this->_error[] = _LANG_ERROR_NO_PROJECT_SELECTED;
			return false;
		}
		
		if (empty($userid)) {
			$userid = phpFrame::getUser()->id;
		}
		
		$where[] = "( p.access = '0' OR (".$userid." IN (SELECT userid FROM #__users_roles WHERE projectid = p.id) ) )";
		$where[] = "p.id = ".$projectid;
		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		
		$query = "SELECT p.*,
				  u.username AS created_by_name, 
				  pt.name AS project_type_name, 
				  GROUP_CONCAT(ur.userid) members
				  FROM #__projects AS p 
				  JOIN #__users u ON u.id = p.created_by 
				  LEFT JOIN #__project_types pt ON pt.id = p.project_type  
				  LEFT JOIN #__users_roles ur ON p.id = ur.projectid "
				  .$where. 
				  " GROUP BY p.id ";
		
		phpFrame::getDB()->setQuery($query);
		return phpFrame::getDB()->loadObject();
	}
	
	
	/**
	 * Save a project sent in the request.
	 * 
	 * @param	$post	The array to be used for binding to the row before storing it. Normally the HTTP_POST array.
	 * @return	mixed	Returns the project id or FALSE on failure.
	 */
	public function saveProject($post) {
		// Instantiate table object
		$row = $this->getTable('projects');
		
		// Bind the post data to the row array
		if ($row->bind($post, 'created,created_by') === false) {
			$this->_error[] = $row->getLastError();
			return false;
		}
	
		if (empty($row->id)) {
			$row->created = date("Y-m-d H:i:s");
			$row->created_by = phpFrame::getUser()->id;
		}
		
		if (!$row->check()) {
			$this->_error[] = $row->getLastError();
			return false;
		}
		
		if (!$row->store()) {
			$this->_error[] = $row->getLastError();
			return false;
		}
		
		return $row->id;
	}
	
	/**
	 * Delete a project by id
	 * 
	 * @todo	Before deleting the project we need to delete all its tracker items, lists, files, ...
	 * @param	int	$projectid
	 * @return	bool
	 */
	public function deleteProject($projectid) {
		// Instantiate table object
		$row = $this->getTable('projects');
		
		// Delete row from database
		if ($row->delete($projectid) === false) {
			$this->_error[] = $row->getLastError();
			return false;
		}
		else {
			return true;
		}
	}
}
?>
