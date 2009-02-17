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
 * projectsTableProjectTypes Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class projectsTableProjectTypes extends table {
	var $id=null; // int(11) auto_increment
	var $name=null; // varchar(50) NOT NULL
  
	function __construct() {
		$db =& factory::getDB();
		parent::__construct( '#__project_types', 'id', $db );
	}
}
?>