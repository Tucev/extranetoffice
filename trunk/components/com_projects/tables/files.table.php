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
	var $id=null; // int(11) auto_increment
	var $projectid=null; // int(11)
	var $userid=null; // int(11)
	var $parentid=null; // int(11)
	var $title=null; // varchar(64)
	var $revision=null; // int(11)
	var $changelog=null; // text
	var $filename=null; // varchar(128)
	var $mimetype=null; // varchar(50)
	var $filesize=null; // int(11)
	var $ts=null; // timestamp
  
	function __construct() {
		$db =& factory::getDB();
		parent::__construct( '#__files', 'id', $db );
	}
}
?>