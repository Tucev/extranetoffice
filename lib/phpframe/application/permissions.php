<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	application
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Permissions Class
 *
 * @package		phpFrame
 * @subpackage 	application
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class permissions {
	var $userid=null;
	var $group=null; // object containing the intranet office group id and name
	var $acl=null;
	var $super_admin=null;
	var $is_allowed=null;
	var $option=null;
	var $task=null;
	var $view=null;
	var $layout=null;
	
	/**
	 * Constructor
	 *
	 * @since 1.0
	 */
	function __construct() {
		// Get URL vars
		$this->option = request::getVar('option');
		$this->task = request::getVar('task', 'display');
		$this->view = request::getVar('view');
		$this->layout = request::getVar('layout');
		
		// Check login
		$user =& factory::getUser();
		$this->userid = $user->id;
		if (empty($this->userid)) {
			return false;
		}
		
		// is super admin?
		if ($user->groupid == 1) {
			$this->super_admin = true;
		}
		else {
			$this->super_admin = false;
		}
		
		// get intranetoffice group
		$this->group = $this->getGroup($this->userid);
		
		// decide if user is allowed to go ahead ased on group membership
		if ($this->checkACL()) {
			return false;
		}
		else {
			return true;
		}
		
	}
	
	/**
	 * Get group
	 * 
	 * This method gets the group object for the given user. 
	 * 
	 * The returned object has two properties, the group name and group id.
	 * 
	 * @param $userid
	 * @return object
	 * @since 1.0
	 */
	function getGroup($userid) {
		$db =& factory::getDB();
		$query = "SELECT ug.groupid AS id, g.name AS name ";
		$query .= " FROM #__users_groups AS ug, #__groups AS g ";
		$query .= " WHERE g.id = ug.groupid AND ug.userid = ".$userid;
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	/**
	 * Check Access Level
	 * 
	 * This method checks the user's access level against the access level list and sets the is_allowed property.
	 * 
	 * @return bool
	 * @since 1.0
	 */
	function checkACL() {
		// Get group ACL
		$db =& factory::getDB();
		$query = "SELECT * ";
		$query .= " FROM #__acl_groups ";
		$query .= " WHERE groupid = ".$this->group->id." AND `option` = '".$this->option."' AND task = '".$this->task."'";
		$db->setQuery($query);
		$this->acl = $db->loadObjectList();
		
		if ($this->super_admin === true) {
			$this->is_allowed = true;
			return true;
		}
		elseif (is_array($this->acl) && count($this->acl) > 0) {
			// Check if user is allowed to execute requested task
			if (!empty($this->task)) {
				foreach ($this->acl as $acl) {
					if ($acl->task == $this->task && !empty($acl->value)) {
						$task_allowed = true;
						break;
					}
					else {
						$task_allowed = false;
					}
				}
			}
			else {
				$task_allowed = true;
			}
			
			// Check if user is allowed to access requested view
			if (!empty($this->view)) {
				foreach ($this->acl as $acl) {
					if ($acl->view == $this->view && ($acl->layout == '*') || (!empty($acl->layout) && $acl->layout == $this->layout)) {
						$view_allowed = true;
						break;
					}
					else {
						$view_allowed = false;
					}
				}
			}
			else {
				$view_allowed = true;
			}
			
			
			if ($task_allowed === true && $view_allowed === true) {
				$this->is_allowed = true;
			}
			else {
				$this->is_allowed = false;
			}
			
			return $this->is_allowed;
		}
	}
}
?>