<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * projectsTableUsersFiles Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @since 		1.0
 */
class projectsTableUsersFiles extends phpFrame_Database_Table {
	/**
	 * The row id
	 * 
	 * int(11), auto_increment, Primary Key
	 * 
	 * @var int
	 */
	var $id=null;
	/**
	 * The user ID
	 * 
	 * int(11), Foreign Key (table: #__users)
	 * 
	 * @var int
	 */
	var $userid=null;
	/**
	 * The file id
	 * 
	 * int(11), Foreign Key (table: #__files)
	 * 
	 * @var int
	 */
	var $fileid=null;
	
	/**
	 * Constructor
	 * 
	 * @return	void
	 * @since	1.0
	 */
	function __construct() {
		$db = phpFrame::getDB();
		parent::__construct( '#__users_files', 'id' );
	}
}
