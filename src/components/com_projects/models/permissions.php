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
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		PHPFrame_MVC_Model
 */
class projectsModelPermissions extends PHPFrame_MVC_Model {
	/**
	 * Instance of the permission object
	 * 
	 * @var	object
	 */
	private static $_instance=null;
	/**
	 * User id
	 * 
	 * @var int
	 */
	private $_userid=null;
	/**
	 * Project id
	 * 
	 * @var int
	 */
	private $_projectid=null;
	/**
	 * The cached user's role id
	 * 
	 * @var int
	 */
	private $_roleid=null;
	
	/**
	 * Private constructor to prevent instantiation and implement the singleton pattern.
	 * @return unknown_type
	 */
	private function __construct() {
		// Get role id for session user and cache
		$this->_userid = PHPFrame::Session()->getUserId();
	}
	
	/**
	 * Get instance
	 * 
	 * @return	object of type projectsModelPermissions
	 */
	public static function getInstance() {
		if (!self::$_instance instanceof projectsModelPermissions) {
			self::$_instance = new projectsModelPermissions();
		}
		
		return self::$_instance;
	}
	
	/**
	 * Authorise access to a given tool for a given user in a given project
	 * 
	 * @param	string	$tool
	 * @param	int		$userid
	 * @param	object	$project
	 * @return	boolean
	 */
	public function authorise($tool, $userid, $project) {
		// Get role id from db or cache
		$roleid = $this->_getUserRole($userid, $project->id);
		
		// If main projects tool is selected we simply 
		// check if the user has a role in this project
		if ($tool == 'projects' && $roleid > 0) {
			return true;
		}
		
		// Check access for a specific tool
		$access_property_name = "access_".$tool;
		if ($project->$access_property_name) {
			if ($roleid !== false && $roleid <= $project->$access_property_name) {
				return true;
			}
		}
		
		return false;
	}
	
	public function getUserId() {
		return $this->_userid;
	}
	
	public function getRoleId() {
		return $this->_roleid;
	}
	
	public function isProjectAdmin() {
		return ($this->_roleid == 1);
	}
	
	/**
	 * Get user role
	 * 
	 * @param	int	$userid
	 * @param	int	$projectid
	 * @return	mixed	Returns an integer on success or FALSE on failure.
	 */
	private function _getUserRole($userid, $projectid) {
		// Get user role from database if cached one needs refreshing
		if (is_null($this->_roleid) 
			|| $userid != $this->_userid 
			|| $projectid != $this->_projectid) {
				
			$query = "SELECT roleid FROM #__users_roles ";
			$query .= " WHERE userid = ".$userid." AND projectid = ".$projectid;
			
			$this->_roleid = PHPFrame::DB()->loadResult($query);
			$this->_userid = $userid;
			$this->_projectid = $projectid;
		}
		
		return $this->_roleid;
	}
	
}
