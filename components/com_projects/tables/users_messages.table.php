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
 * projectsTableUsersMessages Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class projectsTableUsersMessages extends table {
	var $id=null; // int(11) auto_increment
	var $userid=null;
	var $messageid=null; // int(11)
  
	function __construct() {
		$db =& factory::getDB();
		parent::__construct( '#__users_messages', 'id' );
	}
}
?>