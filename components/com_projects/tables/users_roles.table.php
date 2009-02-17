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
class projectsTableUsersRoles extends table {
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
		$db =& factory::getDB();
		parent::__construct( '#__users_roles', 'id', $db );
	}
	
	function load($userid, $projectid) {
		$db =& factory::getDB();
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
			return false;
		}
	}
}
?>