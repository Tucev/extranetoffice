<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * projectsTableProjects Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @since 		1.0
 */
class projectsTableProjects extends phpFrame_Database_Table {
	/**
	 * The project id
	 * 
	 * int(11) auto_increment
	 * 
	 * @var int
	 */
	var $id=null;
	/**
	 * The project name
	 * 
	 * varchar(128) NOT NULL
	 * 
	 * @var string
	 */
	var $name=null;
	/**
	 * The project type id
	 * 
	 * tinyint
	 * 
	 * @var int
	 */
	var $project_type=null;
	/**
	 * Priority id
	 * 
	 * tinyint
	 * 
	 * @var int
	 */
	var $priority=null;
	/**
	 * The company id
	 * 
	 * int(11) NOT NULL
	 * 
	 * @var int
	 */
	var $company_id=null;
	/**
	 * The project description
	 * 
	 * text NOT NULL
	 * 
	 * @var string
	 */
	var $description=null;
	/**
	 * Project wide access level
	 * 
	 * tinyint(4)
	 * 
	 * @var int
	 */
	var $access=null;
	/**
	 * Status id
	 * 
	 * tinyint(1)	
	 * 
	 * @var int
	 */
	var $status=null;
	/**
	 * Issues access level
	 * 
	 * enum('1', '2', '3', '4') 1=Admins, 2=Project workers, 3=Guests, 4=Public
	 * 
	 * @var int
	 */
	var $access_issues=null;
	/**
	 * Milestones access level
	 * 
	 * enum('1', '2', '3', '4') 1=Admins, 2=Project workers, 3=Guests, 4=Public
	 * 
	 * @var int
	 */
	var $access_milestones=null;
	/**
	 * Files access level
	 * 
	 * enum('1', '2', '3', '4') 1=Admins, 2=Project workers, 3=Guests, 4=Public
	 * 
	 * @var int
	 */
	var $access_files=null;
	/**
	 * Meetings access level
	 * 
	 * enum('1', '2', '3', '4') 1=Admins, 2=Project workers, 3=Guests, 4=Public
	 * 
	 * @var int
	 */
	var $access_meetings=null;
	/**
	 * Reports access level
	 * 
	 * enum('1', '2', '3', '4') 1=Admins, 2=Project workers, 3=Guests, 4=Public
	 * 
	 * @var int
	 */
	var $access_reports=null;
	/**
	 * Polls access level
	 * 
	 * enum('1', '2', '3', '4') 1=Admins, 2=Project workers, 3=Guests, 4=Public
	 * 
	 * @var int
	 */
	var $access_polls=null;
	/**
	 * Messages access level
	 * 
	 * enum('1', '2', '3', '4') 1=Admins, 2=Project workers, 3=Guests, 4=Public
	 * 
	 * @var int
	 */
	var $access_messages=null;
	/**
	 * People access level
	 * 
	 * enum('1', '2', '3', '4') 1=Admins, 2=Project workers, 3=Guests, 4=Public
	 * 
	 * @var int
	 */
	var $access_people=null;
	/**
	 * Admin access level
	 * 
	 * enum('1', '2', '3', '4') 1=Admins, 2=Project workers, 3=Guests, 4=Public
	 * 
	 * @var int
	 */
	var $access_admin=null;
	/**
	 * User id of the project creator
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
	 * Construct
	 * 
	 * @return	void
	 * @since	1.0
	 */
	function __construct() {
		parent::__construct( '#__projects', 'id' );
	}
}
