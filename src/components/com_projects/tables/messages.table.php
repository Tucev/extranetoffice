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
 * projectsTableMessages Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class projectsTableMessages extends table {
	var $id=null; // int(11) auto_increment
	var $projectid=null; // int(11) NOT NULL
	var $userid=null; // int(11) NOT NULL
	var $date_sent=null; // datetime NOT NULL
	var $subject=null; // varchar(255)
	var $body=null; // text
	var $status=null; // enum('0', '1', '2') 0=pending; 1=active; 2=archived
	
	/**
	 * Construct
	 * 
	 * @return	void
	 * @since	1.0
	 */
	function __construct() {
		parent::__construct( '#__messages', 'id' );
	}
}
?>