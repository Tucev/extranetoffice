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
 * projectsTableUsersFiles Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class projectsTableUsersFiles extends table {
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
		$db =& factory::getDB();
		parent::__construct( '#__users_files', 'id' );
	}
}
?>