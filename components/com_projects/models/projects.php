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
 * projectsModelProjects Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		model
 */
class projectsModelProjects extends model {
	var $view=null;
	var $layout=null;
	var $projectid=null;
	
	/**
	 * Constructor
	 *
	 * @return	void
	 * @since	1.0
	 */
	function __construct() {
		parent::__construct();
		
		$this->init();
	}
	
	/**
	 * Initialise the projects model. This method is invoked in the constructor.
	 * 
	 * @return	void
	 * @since	1.0
	 */
	function init() {
		$this->view = request::getVar('view', 'projects');
		$this->layout = request::getVar('layout', 'list');
		$this->projectid = request::getVar('projectid', 0);
		
		// Check whether project directories exists and are writable
		$projects_upload_dir = _ABS_PATH.DS.$this->config->upload_dir.DS."projects";
		//TODO: Have to catch errors here and look at file permissions
		if (!is_dir($projects_upload_dir)) {
			mkdir($projects_upload_dir, 0771);
		}
		$projects_filesystem = $this->config->filesystem.DS."projects";
		if (!is_dir($projects_filesystem)) {
			mkdir($projects_filesystem, 0771);
		}
			
		// Check whether project specific directories exists and are writable
		if (!empty($this->projectid)) {
			//TODO: Have to catch errors here and look at file permissions
			$project_specific_upload_dir = _ABS_PATH.DS.$this->config->upload_dir.DS."projects".DS.$this->projectid;
			if (!is_dir($project_specific_upload_dir)) {
				mkdir($project_specific_upload_dir, 0771);
			}
			$project_specific_filesystem = $this->config->filesystem.DS."projects".DS.$this->projectid;
			if (!is_dir($project_specific_filesystem)) {
				mkdir($project_specific_filesystem, 0771);
			}
		}
	}
	
	/**
	 * Get projects.
	 * 
	 * @param $projectid
	 * @return mixed
	 */
	function getProjects($projectid=0) {
		// Only apply filtering and ordering if browsing projects list
		if (empty($projectid)) {
			$filter_order = request::getVar('filter_order', 'p.name');
			$filter_order_Dir = request::getVar('filter_order_Dir', '');
			$search = request::getVar('search', '');
			$search = strtolower( $search );
			$limitstart = request::getVar('limitstart', 0);
			$limit = request::getVar('limit', 20);
		}

		$where = array();
		
		// Show only public projects or projects where user has an assigned role
		$where[] = "( p.access = '0' OR (".$this->user->id." IN (SELECT userid FROM #__users_roles WHERE projectid = p.id) ) )";
		
		if ($search) {
			$where[] = "p.name LIKE '%".$this->db->getEscaped($search)."%'";
		}
		
		if (!empty($projectid)) {
			$where[] = "p.id = ".$projectid;	
		}

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		
		if (empty($filter_order)) {
			$orderby = ' ORDER BY p.name ';
		} else {
			$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', p.name ';
		}
		
		// get the total number of records
		$query = "SELECT 
				  p.id
				  FROM #__projects AS p 
				  JOIN #__users u ON u.id = p.created_by 
				  LEFT JOIN #__project_types pt ON pt.id = p.project_type  
				  LEFT JOIN #__users_roles ur ON p.id = ur.projectid "
				  . $where . 
				  " GROUP BY p.id ";
				  
		$this->db->setQuery($query);
		$this->db->query();
		$total = $this->db->getNumRows();
		
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
		
		if (empty($projectid)) {
			$pageNav = new pagination($total, $limitstart, $limit);
			
			$query .= $orderby." LIMIT ".$pageNav->limitstart.", ".$pageNav->limit;
			//echo str_replace('#__', 'eo_', $query); exit;
			$this->db->setQuery($query);
			$rows = $this->db->loadObjectList();
		
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
		else {
			$this->db->setQuery($query);
			return $this->db->loadObject();
		}
	}
	
	/**
	 * Save a project sent in the request.
	 * 
	 * Returns the project id or FALSE if it fails
	 * 
	 * @return int
	 */
	function saveProject() {
		// Instantiate table object
		require_once COMPONENT_PATH.DS.'tables'.DS.'projects.table.php';
		$row =& phpFrame::getInstance('projectsTableProjects');
		
		$post = request::get('post');
		$row->bind($post);
		
		if (empty($row->id)) {
			$row->created = date("Y-m-d H:i:s");
			$row->created_by = $this->user->id;
			$new_project = true;
		}
		
		if (!$row->check()) {
			error::raise('', 'error', $row->error);
			return false;
		}
		
		$row->store();
		
		// Add role for user in the new project
		if ($new_project === true) {
			if (!$this->saveMember($row->id, $this->user->id, '1')) {
				return false;
			}
		}
		
		if (!empty($row->id)) {
			error::raise('', 'message', _LANG_PROJECT_SAVED);
			return $row->id;
		}
		else {
			error::raise('', 'error', _LANG_PROJECT_SAVE_ERROR);
			return false;
		}
	}
	
	/**
	 * Delete a project by id
	 * 
	 * @todo	Before deleting the project we need to delete all its tracker items, lists, files, ...
	 * @param	int	$projectid
	 * @return	bool
	 */
	function deleteProject($projectid) {
		// Instantiate table object
		require_once COMPONENT_PATH.DS.'tables'.DS.'projects.table.php';
		$row =& phpFrame::getInstance('projectsTableProjects');
		
		// Delete row from database
		if ($row->delete($projectid) === false) {
			error::raise('', 'error', $row->error);
			return false;
		}
		else {
			return true;
		}
	}
}
?>
