<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * projectsTableMilestones Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @since 		1.0
 */
class projectsTableMilestones extends phpFrame_Database_Table {
	var $id=null; // int(11) auto_increment
	var $projectid=null; // int(11)
	var $title=null; // varchar(128) NOT NULL
	var $due_date=null; // date
	var $description=null; // text
	var $created_by=null; // int(11)
	var $created=null; // datetime
	var $closed_by=null; // int(11)
	var $closed=null; // datetime
	
	/**
	 * Construct
	 * 
	 * @return	void
	 * @since	1.0
	 */
	function __construct() {
		parent::__construct( '#__milestones', 'id' );
	}
}
