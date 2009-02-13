<?php
/**
 * @version		$Id$
 * @package		ExtranetOffice
 * @subpackage 	framework
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

class config {
	/**
	 * The site name
	 *
	 * @var string
	 */
	var $sitename="Extranet Office 2.0";
	/**
	 * MySQL server
	 *
	 * @var string
	 */
	var $db_host="localhost";
	/**
	 * MySQL user
	 *
	 * @var string
	 */
	var $db_user="extranetoffice";
	/**
	 * MySQL password
	 *
	 * @var string
	 */
	var $db_pass="XZ85QGAAxtfGh2pc";
	/**
	 * MySQL database name
	 *
	 * @var string
	 */
	var $db_name="extranetoffice";
	/**
	 * The prefix for database tables
	 *
	 * @var string
	 */
	var $db_prefix="eo_";
	/**
	 * Site template
	 *
	 * @var string
	 */
	var $template="default";
	/**
	 * The default language to load
	 *
	 * @var string
	 */
	var $default_lang="en-GB";
	/**
	 * Relative path to folder to allow uploads. 
	 * Used for uploading images that will need to be accessible in html pages, 
	 * so it has to be within the web server root.
	 *
	 * @var string
	 */
	var $upload_dir="uploads";
	/**
	 * Path to folder outside the web server root where to store files securely.
	 *
	 * @var string
	 */
	var $filesystem="/Users/lupo/Sites/internal/extranetoffice_filesystem";
	/**
	 * This option switched on/off the debugger for sys admins
	 *
	 * @var bool
	 */
	var $debugger=true;
}
?>