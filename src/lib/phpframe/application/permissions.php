<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	application
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
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
class phpFrame_Application_Permissions extends phpFrame_Base_Singleton {
	/**
	 * The userid.
	 * 
	 * @var int
	 */
	private $userid=null;
	/**
	 * The groupid.
	 * 
	 * @var int
	 */
	private $groupid=null;
	/**
	 * Access level list loaded from database.
	 * 
	 * @var array
	 */
	private $acl=null;
	/**
	 * Is super admin?
	 * 
	 * @var bool
	 */
	private $super_admin=null;
	/**
	 * Is user allowed to access task and/or view?
	 *  
	 * @var bool
	 */
	public $is_allowed=null;
	/**
	 * A string with the component's option value ie: (com_admin).
	 * 
	 * @var string
	 */
	private $component=null;
	/**
	 * The task to be executed.
	 * 
	 * @var string
	 */
	private $action=null;
	/**
	 * The view set to be displayed.
	 * 
	 * @var string
	 */
	private $view=null;
	/**
	 * The layout template to be loaded.
	 * 
	 * @var string
	 */
	private $layout=null;
	
	/**
	 * Constructor
	 *
	 * @since 1.0
	 */
	protected function __construct() {
		// Get URL vars
		$this->option = phpFrame_Environment_Request::getComponent();
		$this->task = phpFrame_Environment_Request::getVar('task', 'display');
		$this->view = phpFrame_Environment_Request::getView();
		$this->layout = phpFrame_Environment_Request::getLayout();
		
		// Check login
		$user = phpFrame::getUser();
		$this->userid = $user->id;
		if (empty($this->userid)) {
			$this->userid = 0;
			$this->groupid = 0;
		}
		else {
			$this->groupid = $user->groupid;
		}
		
		// is super admin?
		if ($user->groupid == 1) {
			$this->super_admin = true;
		}
		else {
			$this->super_admin = false;
		}
		
		// decide if user is allowed to go ahead ased on group membership
		if (!$this->checkACL($this->option, $this->task, $this->view, $this->layout)) {
			return false;
		}
		else {
			return true;
		}
		
	}
	
	/**
	 * Check Access Level
	 * 
	 * This method checks the user's access level against the access level list and sets the is_allowed property.
	 * 
	 * @return bool
	 * @since 1.0
	 */
	public function checkACL($option, $action='display', $view='', $layout='') {
		// Get group ACL
		$db = phpFrame::getDB();
		$query = "SELECT * ";
		$query .= " FROM #__acl_groups ";
		$query .= " WHERE groupid = ".$this->groupid." AND `option` = '".$option."'";
		$db->setQuery($query);
		$this->acl = $db->loadObjectList();
		
		if ($this->super_admin === true) {
			$this->is_allowed = true;
			return true;
		}
		elseif (is_array($this->acl) && count($this->acl) > 0) {
			// Check if user is allowed to execute requested task
			if (!empty($task)) {
				foreach ($this->acl as $acl) {
					if ($acl->task == '*') {
						$task_allowed = true;
						break;
					}
					elseif ($acl->task == $task && !empty($acl->value)) {
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
			if (!empty($view)) {
				foreach ($this->acl as $acl) {
					if ($acl->view == '*') {
						$view_allowed = true;
						break;
					}
					if ($acl->view == $view) {
						if ($acl->layout == '*') {
							$view_allowed = true;
							break;
						}
						elseif (!empty($acl->layout) && $acl->layout == $layout) {
							$view_allowed = true;
							break;
						}
						else {
							$view_allowed = false;
						}
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
		else {
			return false;
		}
	}
}
?>