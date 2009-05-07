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
 * projectsTableIssues Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @since 		1.0
 */
class projectsTableIssues extends phpFrame_Database_Table {
	/**
	 * The issue id
	 * 
	 * int(11) auto_increment
	 * 
	 * @var int
	 */
	var $id=null;
	/**
	 * The project id
	 * 
	 * int(11)
	 * 
	 * @var unknown_type
	 */
	var $projectid=null;
	/**
	 * Issue type id
	 * 
	 * int(11)
	 * 
	 * @var int
	 */
	var $issue_type=null;
	/**
	 * The issue title
	 * 
	 * varchar(128) NOT NULL
	 * 
	 * @var string
	 */
	var $title=null;
	/**
	 * The issue description
	 * 
	 * text NOT NULL
	 * 
	 * @var string
	 */
	var $description=null;
	/**
	 * The priority id
	 * 
	 * tinyint
	 * 
	 * @var int
	 */
	var $priority=null;
	/**
	 * Start date
	 * 
	 * date
	 * 
	 * @var string
	 */
	var $dtstart=null;
	/**
	 * End date
	 * 
	 * date
	 * 
	 * @var string
	 */
	var $dtend=null;
	/**
	 * Expected duration
	 * 
	 * float(4,2)
	 * 
	 * @var float
	 */
	var $expected_duration=null;
	/**
	 * Progress percentage
	 * 
	 * tinyint(3)
	 * 
	 * @var int
	 */
	var $progress=null;
	/**
	 * Access level
	 * 
	 * enum('0','1') 0=Public, 1=Private, only owner and assigned users
	 * 
	 * @var int
	 */
	var $access=null;
	/**
	 * User ID of issue creator
	 * 
	 * int(11)
	 * 
	 * @var int
	 */
	var $created_by=null;
	/**
	 * Date created
	 * 
	 * datetime
	 * 
	 * @var string
	 */
	var $created=null;
	/**
	 * User ID of user who closed the issue
	 * 
	 * int(11)
	 * 
	 * @var int
	 */
	var $closed_by=null;
	/**
	 * Date closed
	 * 
	 * datetime
	 * 
	 * @var string
	 */
	var $closed=null;
	/**
	 * MySQL timestamp
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
		parent::__construct( '#__issues', 'id' );
	}
}
