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
 * projectsTableSlideshowsSlides Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class projectsTableSlideshowsSlides extends table {
	var $id=null; // int(11) auto_increment
	var $slideshowid=null; // int(11)
	var $title=null; // varchar(128)
	var $filename=null; // varchar(128)
	
	/**
	 * Construct
	 * 
	 * @return void
	 */
	function __construct() {
		parent::__construct( '#__slideshows_slides', 'id' );
	}
}
?>