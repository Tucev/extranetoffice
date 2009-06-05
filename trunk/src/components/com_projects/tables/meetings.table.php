<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * projectsTableMeetings Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @since 		1.0
 */
class projectsTableMeetings extends PHPFrame_Database_Table {
	var $id=null; // int(11) auto_increment
	var $projectid=null; // int(11)
	var $name=null; // varchar(64)
	var $dtstart=null; // datetime
	var $dtend=null; // datetime
	var $description=null; // text
	var $created_by=null; // int(11)
	var $created=null; // datetime
	
	/**
	 * Construct
	 * 
	 * @return	void
	 * @since	1.0
	 */
	function __construct() {
		parent::__construct( '#__meetings', 'id' );
	}
}
