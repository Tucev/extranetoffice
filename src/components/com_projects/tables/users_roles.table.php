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
 * projectsTableUsersRoles Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class projectsTableUsersRoles extends phpFrame_Database_Table {
	/**
	 * Id
	 * 
	 * @var int
	 */
	var $id=null;
	/**
	 * User ID
	 * 
	 * @var int
	 */
	var $userid=null;
	/**
	 * Project ID
	 * 
	 * @var int
	 */
	var $projectid=null;
	/**
	 * Role ID
	 * 
	 * @var int
	 */
	var $roleid=null;
  
	function __construct() {
		$db =& phpFrame_Application_Factory::getDB();
		parent::__construct( '#__users_roles', 'id' );
	}
	
	function load($userid, $projectid) {
		$db =& phpFrame_Application_Factory::getDB();
		$query = "SELECT * FROM #__users_roles WHERE userid = ".$userid." AND projectid = ".$projectid;
		$db->setQuery($query);	
		$row = $db->loadAssoc();
		if (is_array($row) && count($row) > 0) {
			foreach ($row as $key=>$value) {
				$this->$key = $value;
			}
			return true;
		}
		else {
			$this->id = null;
			$this->userid = null;
			$this->projectid = null;
			$this->roleid = null;
			return false;
		}
	}
}
?>