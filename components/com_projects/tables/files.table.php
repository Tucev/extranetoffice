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
 * projectsTableFiles Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class projectsTableFiles extends table {
	/**
	 * The row id
	 * 
	 * int(11), auto_increment, Primary Key
	 * 
	 * @var unknown_type
	 */
	var $id=null;
	/**
	 * Project ID
	 * 
	 * int(11), Foreign Key (table: #__projects)
	 * 
	 * @var int
	 */
	var $projectid=null;
	/**
	 * User ID
	 * 
	 * int(11), Foreign Key (table: #__users)
	 * 
	 * @var int
	 */
	var $userid=null;
	/**
	 * The parent fileid if any. Parent files have parentid 0.
	 * 
	 * int(11)
	 * 
	 * @var int
	 */
	var $parentid=null;
	/**
	 * The file title
	 * 
	 * varchar(64)
	 * 
	 * @var string
	 */
	var $title=null;
	/**
	 * The revision number
	 * 
	 * int(11)
	 * 
	 * @var int
	 */
	var $revision=null;
	/**
	 * Revision log message
	 * 
	 * text
	 * 
	 * @var string
	 */
	var $changelog=null;
	/**
	 * The filename
	 * 
	 * varchar(128)
	 * 
	 * @var string
	 */
	var $filename=null;
	/**
	 * The mime type
	 * 
	 * varchar(50)
	 * 
	 * @var string
	 */
	var $mimetype=null;
	/**
	 * File size in bytes
	 * 
	 * int(11)
	 * 
	 * @var int
	 */
	var $filesize=null;
	/**
	 * MySQL Timestamp
	 * 
	 * timestamp
	 * 
	 * @var string
	 */
	var $ts=null;
	
	/**
	 * Construct
	 * 
	 * @return	void
	 * @since	1.0
	 */
	function __construct() {
		parent::__construct( '#__files', 'id' );
	}
}
?>