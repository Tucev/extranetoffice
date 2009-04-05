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
 * projectsTableComments Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class projectsTableComments extends phpFrame_Database_Table {
	var $id=null; // int(11) auto_increment
	var $projectid=null; // int(11) NOT NULL
	var $userid=null; // int(11) NOT NULL
	var $type=null; // varchar(50) (tracker, files, messages...)
	var $itemid=null; // int(11) the trackerid, fileid, messageid...
	var $body=null; // text
	var $created=null; // datetime NOT NULL
	
	/**
	 * Construct
	 * 
	 * @return	void
	 * @since	1.0
	 */
	function __construct() {
		parent::__construct( '#__comments', 'id' );
	}
}
?>