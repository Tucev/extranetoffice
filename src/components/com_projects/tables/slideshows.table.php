<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * projectsTableSlideshows Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @since 		1.0
 */
class projectsTableSlideshows extends phpFrame_Database_Table {
	var $id=null; // int(11) auto_increment
	var $projectid=null; // int(11)
	var $meetingid=null; // int(11)
	var $name=null; // varchar(64)
	var $description=null; // text
	var $created_by=null; // int(11)
	var $created=null; // datetime
  
	function __construct() {
		$db = phpFrame::getDB();
		parent::__construct( '#__slideshows', 'id' );
	}
}
?>