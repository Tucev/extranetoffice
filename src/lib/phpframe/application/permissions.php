<?php
/**
 * @version		$Id$
 * @package		phpFrame_lib
 * @subpackage 	application
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Permissions Class
 *
 * @package		phpFrame_lib
 * @subpackage 	application
 * @since 		1.0
 */
class phpFrame_Application_Permissions {
	/**
	 * Access level list loaded from database.
	 * 
	 * @var array
	 */
	private $_acl=array();
	
	/**
	 * Constructor
	 * 
	 * @access	public
	 * @since 	1.0
	 */
	public function __construct() {
		// Load ACL from DB
		$this->_acl = $this->_loadACL();
	}
	
	/**
	 * Authorise action in a component for a given user group
	 * 
	 * @access	public
	 * @param	string	$component
	 * @param	string	$action
	 * @param	int		$groupid
	 * @return	bool
	 * @since	1.0
	 */
	public function authorise($component, $action, $groupid) {
		foreach ($this->_acl as $acl) {
			if (
				$acl->groupid == $groupid 
				&& $acl->component == $component 
				&& ($acl->action == $action || $acl->action == '*')
			   ) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Load access levels from database
	 * 
	 * @access	private
	 * @return	array	An array ob database row objects
	 * @since	1.0
	 */
	private function _loadACL() {
		// Load access list from DB
		$query = "SELECT * FROM #__acl_groups";
		phpFrame::getDB()->setQuery($query);
		return phpFrame::getDB()->loadObjectList();
	}
}
