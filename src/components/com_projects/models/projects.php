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
	public function __construct() {}
	
	/**
	 * Get projects.
	 * 
	 * @param	object	$list_filter	Object of type phpFrame_Database_CollectionFilter
	 * @return	mixed	if no parameters returns array of objects if entries exist 
	 */
	public function getCollection(phpFrame_Database_CollectionFilter $filter) {
		// Build WHERE SQL clauses
		$where = array();
		
		// Show only public projects or projects where user has an assigned role
		$userid = phpFrame::getSession()->getUserId();
		$where[] = "( p.access = '0' OR (".$userid." IN (SELECT userid FROM #__users_roles WHERE projectid = p.id) ) )";
		
		// Add search filtering
		$search = $filter->getSearchStr();
		if ($search) {
			$where[] = "p.name LIKE '%".phpFrame::getDB()->getEscaped($search)."%'";
		}
		
		// Transform where array to SQL string
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

		// Run query to get total rows before applying filter
		phpFrame::getDB()->setQuery($query)->query();
		// Set total number of record in list filter
		$filter->setTotal(phpFrame::getDB()->getNumRows());
		
		// get the subset (based on limits) of required records
		$query = "SELECT 
				  p.*, 
				  u.username AS created_by_name, 
				  pt.name AS project_type_name 
				  FROM #__projects AS p 
				  JOIN #__users u ON u.id = p.created_by 
				  LEFT JOIN #__project_types pt ON pt.id = p.project_type "
				  . $where . 
				  " GROUP BY p.id ";
		
		// Add order by and limit statements for subset (based on filter)
		$query .= $filter->getOrderByStmt();
		$query .= $filter->getLimitStmt();
		//echo str_replace('#__', 'eo_', $query); exit;
		
		return new phpFrame_Database_RowCollection($query);
	}
	
	public function getRow($projectid) {
		// Build SQL query to get row
		$userid = phpFrame::getSession()->getUserId();
		$where[] = "( p.access = '0' OR (".$userid." IN (SELECT userid FROM #__users_roles WHERE projectid = p.id) ) )";
		$where[] = "p.id = ".$projectid;
		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		
		$query = "SELECT p.*,
				  u.username AS created_by_name, 
				  pt.name AS project_type_name 
				  FROM #__projects AS p 
				  JOIN #__users u ON u.id = p.created_by 
				  LEFT JOIN #__project_types pt ON pt.id = p.project_type "
				  .$where. 
				  " GROUP BY p.id ";

		//echo str_replace("#__", "eo_", $query); exit;
		
		// Create instance of row
		$row = new phpFrame_Database_Row('#__projects');
		
		// Load row data using query and return
		return $row->loadByQuery($query, array('created_by_name', 'project_type_name'));
	}
	
	
	/**
	 * Save a project sent in the request.
	 * 
	 * @param	$post	The array to be used for binding to the row before storing it. Normally the HTTP_POST array.
	 * @return	mixed	Returns the project id or FALSE on failure.
	 */
	public function saveRow($post) {
		// Instantiate table object
		$row = new phpFrame_Database_Row('#__projects');
		
		// Bind the post data to the row array (exluding created and created_by)
		$row->bind($post, 'created,created_by');
		
		// Manually set created by and created date for new records
		if (empty($row->id)) {
			$row->set('created', date("Y-m-d H:i:s"));
			$row->set('created_by', phpFrame::getSession()->getUserId());
		}
		
		// Store row and return row object
		$row->store();
		
		// Create filesystem directories if they don't exist yet
		phpFrame_Utils_Filesystem::ensureWritableDir(config::FILESYSTEM.DS."projects");
		phpFrame_Utils_Filesystem::ensureWritableDir(config::FILESYSTEM.DS."projects".DS.$row->id);
		phpFrame_Utils_Filesystem::ensureWritableDir(config::UPLOAD_DIR.DS."projects");
		phpFrame_Utils_Filesystem::ensureWritableDir(config::UPLOAD_DIR.DS."projects".DS.$row->id);
		
		return $row;
	}
	
	/**
	 * Delete a project by id
	 * 
	 * @todo	Before deleting the project we need to delete all its tracker items, lists, files, ...
	 * @param	int	$projectid
	 * @return	void
	 */
	public function deleteRow($projectid) {
		// Instantiate table object
		$row = new phpFrame_Database_Row('#__projects');
		
		// Delete row from database
		$row->delete($projectid);
	}
}
