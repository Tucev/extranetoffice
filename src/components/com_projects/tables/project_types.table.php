<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * projectsTableProjectTypes Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @since 		1.0
 */
class projectsTableProjectTypes extends PHPFrame_Database_Table {
	var $id=null; // int(11) auto_increment
	var $name=null; // varchar(50) NOT NULL
	
	/**
	 * Construct
	 * 
	 * @return void
	 */
	function __construct() {
		parent::__construct( '#__project_types', 'id' );
	}
}
