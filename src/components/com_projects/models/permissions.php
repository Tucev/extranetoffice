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
class projectsModelPermissions extends phpFrame_Application_Model {
	/**
	 * The current project object
	 * 
	 * @var object
	 */
	var $project=null;
	/**
	 * The current user's role id for the current project
	 * 
	 * @var int
	 */
	var $roleid=null;
	/**
	 * Array containing a list of the available views
	 * 
	 * @var array
	 */
	var $tools=null;
	/**
	 * A boolean indicating whether the permissions check was passed
	 * 
	 * @var bool
	 */
	var $is_allowed=null;
	/**
	 * The currently loaded tool/view
	 * 
	 * @var string
	 */
	var $current_tool=null;
	
	/**
	 * Check project access
	 * 
	 * This method checks access to a given project.
	 * 
	 * @param	object	$project
	 * @param	array	$views_available
	 * @return 	bool
	 */
	function checkProjectAccess(&$project, $views_available) {
		$session = phpFrame::getSession();
		if ($session->isAdmin()) {
			return $this->is_allowed = true;
		}
		
		$this->project = $project;
		
		// Load tools/views and their access levels into tools array
		foreach ($views_available as $view) {
			// Filter out "projects" view (this is not a tool)
			if ($view != 'projects') {
				$access_property_name = 'access_'.$view;
				$view_access_level = $this->project->$access_property_name;
				$this->tools[] = array($view, $view_access_level);	
			}
		}
		
		// get role id
		$this->roleid = $this->getUserRole($this->_user->id, $project->id);
		
		// Check project's global access level
		if ($this->project->access > 0 && $this->roleid < 1) {
			$this->_error[] = "You do not have access to this project";
			$this->is_allowed = false;
			return false;
		}
		
		// Check tool-specific (views) access
		if (!empty($project->id)) {
			if ($this->checkViewAccess() !== true) {
				$this->_error[] = "You do not have access to this tool in this project";
				$this->is_allowed = false;
				return false;
			}	
		}
		
		$this->is_allowed = true;
		return true;
	}
	
	/**
	 * Returns current user's role in current project
	 *
	 */
	function getUserRole($userid, $projectid) {
		$query = "SELECT roleid ";
		$query .= " FROM #__users_roles ";
		$query .= " WHERE userid = ".$userid." AND projectid = ".$projectid;
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
	
	function checkViewAccess() {
		$view = phpFrame_Environment_Request::getViewName();
		$task = phpFrame_Environment_Request::getAction();
		
		// if a task has been requested we get the tool keyword from the task
		if (!empty($task)) {
			$task_tool = substr($task, (strpos($task, '_')+1));
		}
		
		// Return true when no specific project tool has been selected (projects view)
		if ($view == 'projects' || strpos('projects', $task_tool) !== false) {
			return true;
		}
		else {
			$this->current_tool = $view;
			$access_property_name = 'access_'.$view;
			$view_access_level = $this->project->$access_property_name;
			//echo 'view_access_level: '.$view_access_level.'<br />';
			//echo 'roleid: '.$this->roleid;
			switch ($view_access_level) {
				case '1' : // Admins only
					if ($this->roleid == 1) return true;
					else return false;
					break;
				case '2' : // Admins + Project workers only
					if ($this->roleid < 3) return true;
					else return false;
					break;
				case '3' : // Admins + Project workers + Guests only
					if ($this->roleid < 4) return true;
					else return false;
					break;
				case '4' : // Admins + Project workers + Guests + Public
					return true;
					break;
			}
		}
	}
	
}
?>