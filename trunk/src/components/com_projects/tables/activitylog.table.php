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
 * projectsTableActivitylog Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class projectsTableActivitylog extends table {
	/**
	 * The activitylog row id (int(11) auto_increment)
	 * 
	 * @var int
	 */
	var $id=null;
	/**
	 * The project id
	 * 
	 * @var int
	 */
	var $projectid=null;
	/**
	 * The user id
	 * 
	 * @var int
	 */
	var $userid=null;
	/**
	 * Type of activity log entry (message, issue, file, ...)
	 * 
	 * @var string
	 */
	var $type=null;
	/**
	 * The action to be logged
	 * 
	 * @var string
	 */
	var $action=null;
	/**
	 * Title
	 * 
	 * @var string
	 */
	var $title=null;
	/**
	 * Description
	 * 
	 * @var string
	 */
	var $description=null;
	/**
	 * The relative URL to the item
	 * 
	 * @var string
	 */
	var $url=null;
	/**
	 * Timestamp (MySQL timestamp)
	 * 
	 * @var string
	 */
	var $ts=null;
	
	/**
	 * Construct
	 * 
	 * @return void
	 */
	function __construct() {
		parent::__construct( '#__activitylog', 'id' );
	}
}
?>