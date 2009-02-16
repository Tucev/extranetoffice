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
class projectsModelPermissions extends model {
	var $project=null;
	var $roleid=null;
	var $tools=null;
	var $is_allowed=null;
	var $current_tool=null;
	
	function checkProjectAccess(&$project) {
		$this->project = $project;
		
		// get role id
		$user =& factory::getUser();
		$this->roleid = $this->getUserRole($user->id, $project->id);
		
		// Check project's global access level
		if ($this->project->access > 0 && $this->roleid < 1) {
			error::raise('', 'warning', "You do not have access to this project");
			$this->is_allowed = false;
			return false;
		}
		
		// Check tool-specific access
		if (!empty($project->id)) {
			if ($this->checkViewAccess() !== true) {
				error::raise('', 'warning', "You do not have access to this tool in this project");
				$this->is_allowed = false;
				return false;
			}	
		}
		
		$this->is_allowed = true;
		return $this->current_tool;
	}
	
	/**
	 * Returns current user's role in current project
	 *
	 */
	function getUserRole($userid, $projectid) {
		$db =& factory::getDB();
		$query = "SELECT roleid ";
		$query .= " FROM #__users_roles ";
		$query .= " WHERE userid = ".$userid." AND projectid = ".$projectid;
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	function checkViewAccess() {
		$view = request::getVar('view');
		$task = request::getVar('task');
		
		if ($view == 'projects') {
			return true;
		}
		else {
			$access_property_name = 'access_'.$view;
			echo $access_property_name;
			$view_access_level = $this->project->$access_property_name;
			
			// if a task has been requested we get the tool keyword from the task
			if (!empty($task)) {
				$task_tool = substr($task, (strpos($task, '_')+1));
			}
			if (strpos($view, $task_tool) !== false) {
				$this->current_tool = $tool[0];
				switch ($tool[1]) {
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
	
	function checkToolsAccess() {
		// Build array with string to identify tools and their access level in this project
		$this->tools = array();
		$this->tools[] = array('issues', $this->project->access_issues);
		$this->tools[] = array('messages', $this->project->access_messages);
		$this->tools[] = array('milestones', $this->project->access_milestones);
		$this->tools[] = array('files', $this->project->access_files);
		$this->tools[] = array('meetings', $this->project->access_meetings);
		//TODO: Have to uncomment this lines to enable polls and reports
		//$this->tools[] = array('polls', $this->project->access_polls);
		//$this->tools[] = array('reports', $this->project->access_reports);
		$this->tools[] = array('people', $this->project->access_people);
		$this->tools[] = array('admin', $this->project->access_admin);
		// We add the rest of the options outside the object't tool array, as they are sub-tools
		$tools = $this->tools;
		$tools[] = array('member_form', $this->project->access_admin);
		$tools[] = array('remove_project', $this->project->access_admin);
		// Access levels for non-tool specific views
		// This must go after the tool-specific to deal with those view types that haven't been matched yet
		$tools[] = array('detail', 4); 
		$tools[] = array('edit', $this->project->access_admin);
		
		foreach ($tools as $tool) {
			// if a task has been requested we get the tool keyword from the task
			if (!empty($this->task)) {
				$task_tool = substr($this->task, (strpos($this->task, '_')+1));
			}
			if (strpos($tool[0], $task_tool) !== false || strpos($this->view, $tool[0]) !== false) {
				$this->current_tool = $tool[0];
				switch ($tool[1]) {
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
}
?>