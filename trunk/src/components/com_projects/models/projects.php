<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * projectsModelProjects Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @since 		1.0
 * @see 		PHPFrame_MVC_Model
 */
class projectsModelProjects extends PHPFrame_MVC_Model {
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
	 * @param string $search Object of type PHPFrame_Database_CollectionFilter
	 * 
	 * @access public
	 * @return mixed if no parameters returns array of objects if entries exist
	 * @since  1.0 
	 */
	public function getCollection($search=null)
	{
	    $userid = PHPFrame::Session()->getUserId();
		
		// Create Id object used to create collection
		$id_obj = new PHPFrame_Database_IdObject();
		
		$id_obj->select("*")->from("#__projects")->where("id", "=", 1);
		//$collection
		var_dump($id_obj->__toString()); exit;
		
		$id_obj->select(array("p.*", 
		                         "u.username AS created_by_name", 
		                         "pt.name AS project_type_name"));
		
		$id_obj->from("#__projects AS p");
		
		$id_obj->join("JOIN #__users u ON u.id = p.created_by");
		$id_obj->join("LEFT JOIN #__project_types pt ON pt.id = p.project_type");
		
	    // Show only public projects or projects where user has an assigned role
		$id_obj->where("p.access = '0'", "OR", "(".$userid." IN (SELECT userid FROM #__users_roles WHERE projectid = p.id) )");
		// Add search filtering
		if ($search) {
		    $id_obj->where("p.name", "LIKE", "'%".PHPFrame::DB()->getEscaped($search)."%'");
		}
		
		$id_obj->groupBy("p.id");
		
		var_dump($id_obj->__toString()); exit;
		
		// Create list filter needed for getProjects()
		$filter = new PHPFrame_Database_CollectionFilter('p.name', 'ASC');
		
		// Transform where array to SQL string
		//$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		
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
		$query .= $filter->getOrderBySQL();
		$query .= $filter->getLimitSQL();
		//echo str_replace('#__', 'eo_', $query); exit;
		
		return new PHPFrame_Database_RowCollection($query);
	}
	
	public function getRow($projectid) {
		// Build SQL query to get row
		$userid = PHPFrame::Session()->getUserId();
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
		$row = new PHPFrame_Database_Row('#__projects');
		
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
		$row = new PHPFrame_Database_Row('#__projects');
		
		// Bind the post data to the row array (exluding created and created_by)
		$row->bind($post, 'created,created_by');
		
		// Manually set created by and created date for new records
		if (empty($row->id)) {
			$row->set('created', date("Y-m-d H:i:s"));
			$row->set('created_by', PHPFrame::Session()->getUserId());
		}
		
		// Store row and return row object
		$row->store();
		
		// Create filesystem directories if they don't exist yet
		PHPFrame_Utils_Filesystem::ensureWritableDir(config::FILESYSTEM.DS."projects");
		PHPFrame_Utils_Filesystem::ensureWritableDir(config::FILESYSTEM.DS."projects".DS.$row->id);
		PHPFrame_Utils_Filesystem::ensureWritableDir(_ABS_PATH.DS."public".DS.config::UPLOAD_DIR.DS."projects");
		PHPFrame_Utils_Filesystem::ensureWritableDir(_ABS_PATH.DS."public".DS.config::UPLOAD_DIR.DS."projects".DS.$row->id);
		
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
		$row = new PHPFrame_Database_Row('#__projects');
		
		// Delete row from database
		$row->delete($projectid);
	}
}
