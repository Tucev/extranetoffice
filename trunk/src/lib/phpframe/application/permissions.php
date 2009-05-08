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
	 * @since 1.0
	 */
	private function __construct() {}
	
	public static function getInstance() {
		if (!self::$_instance instanceof phpFrame_Application_Permissions) {
			self::$_instance = new phpFrame_Application_Permissions();
		}
		
		return self::$_instance;
	}
	
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
	
	private function _loadACL() {
		// Load access list from DB
		$query = "SELECT * FROM #__acl_groups";
		phpFrame::getDB()->setQuery($query);
		return phpFrame::getDB()->loadObjectList();
	}
}
