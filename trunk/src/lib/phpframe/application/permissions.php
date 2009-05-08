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
class phpFrame_Application_Permissions {
	/**
	 * Instance of itself
	 * 
	 * @var object
	 */
	private static $_instance=null;
	/**
	 * Access level list loaded from database.
	 * 
	 * @var array
	 */
	private static $_acl=null;
	
	/**
	 * Constructor
	 * 
	 * We declare a privae constructor to prevent multiple instantiation
	 * 
	 * @access	private
	 * @since 	1.0
	 */
	private function __construct() {}
	
	/**
	 * Get instance of the permissions object
	 * 
	 * @access	public
	 * @return	object of type phpFrame_Application_Permissions
	 * @since	1.0
	 */
	public static function getInstance() {
		if (!self::$_instance instanceof phpFrame_Application_Permissions) {
			self::$_instance = new phpFrame_Application_Permissions();
		}
		
		return self::$_instance;
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
		// Load ACL from DB if not loaded yet
		if (is_null(self::$_acl)) {
			self::$_acl = $this->_loadACL();
		}
		
		foreach (self::$_acl as $acl) {
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
