<?php
/**
 * @version		$Id$
 * @package		ExtranetOffice
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

class config {
	
	/////////////////////////////////////////////////
    // GENERAL PROPERTIES
    /////////////////////////////////////////////////
	
	/**
	 * The site name
	 *
	 * @var string
	 */
	var $sitename="Extranet Office 1.0";
	
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
	 * This option switched on/off the debugger for sys admins
	 *
	 * @var bool
	 */
	var $debug=false;
	/**
	 * Secret string used to generate secure hash
	 * 
	 * @var string
	 */
	var $secret="ChangeMeToSometingRandomAndComplicated";
	
	/////////////////////////////////////////////////
    // DATABASE PROPERTIES
    /////////////////////////////////////////////////
    
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
	var $db_pass="DC2YBBbnGGQzmJRR";
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
	
	/////////////////////////////////////////////////
    // FILESYSTEM PROPERTIES
    /////////////////////////////////////////////////
	
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
	var $filesystem="/var/extranetoffice_fs";
	/**
	 * List of accepted mime types as CSV.
	 *
	 * @var string
	 */
	var $upload_accept="text/plain,image/jpeg,image/pjpeg,image/jpg,image/png,image/bmp,image/gif,application/pdf,application/octet-stream,application/msword,application/excel,application/vnd.ms-excel,application/x-excel,application/x-msexcel,application/vnd.ms-powerpoint,application/mspowerpoint,application/powerpoint";
	
	/////////////////////////////////////////////////
    // MAIL RETRIEVAL PROPERTIES
    /////////////////////////////////////////////////
    
	/**
	 * The IMAP host name or IP address
	 * 
	 * @var string
	 */
	var $imap_host="mail.extranetoffice.org";
	/**
	 * The IMAP port number to use
	 * 
	 * @var int
	 */
	var $imap_port="143";
	/**
	 * The IMAP login username
	 * 
	 * @var string
	 */
	var $imap_user="notifications.will@extranetoffice.org";
	/**
	 * The IMAP password
	 * 
	 * @var string
	 */
	var $imap_password="ockCBPZ9nJDoAp9EbwmnGZiY";
	
	/////////////////////////////////////////////////
    // MAIL SENDING PROPERTIES
    /////////////////////////////////////////////////
    
	/**
	 * Method to send mail: ("mail", "sendmail", or "smtp").
	 * 
	 * @var string
	 */
	var $mailer="mail";
	/**
	 * If using SMTP mailer enable smtp authentication
	 * 
	 * @var string
	 */
	var $smtp_auth="false";
	/**
	 * The SMTP host name or IP address.
	 * 
	 * @var string
	 */
	var $smtp_host="mail.extranetoffice.org";
	/**
	 * The SMTP port to use
	 * 
	 * @var int
	 */
	var $smtp_port="25";
	/**
	 * The SMTP login name
	 * 
	 * @var string
	 */
	var $smtp_user="notifications.will@extranetoffice.org";
	/**
	 * The SMTP password
	 * 
	 * @var string
	 */
	var $smtp_password="ockCBPZ9nJDoAp9EbwmnGZiY";
	/**
	 * The email address to use for notifications
	 *  
	 * @var string
	 */
	var $fromaddress="notifications.will@extranetoffice.org";
	/**
	 * The from name for notifications
	 *  
	 * @var string
	 */
	var $fromname="notifications.will@extranetoffice.org";
	
	
	/**
	 * Get property
	 * 
	 * @todo This method has been added for compatibility with Intranet Office code and should be removed once we finish porting all the code.
	 * 
	 * @param $property
	 * @return mixed
	 */
	function get($property) {
		if ($this->$property) {
			return $this->$property;
		}
		else {
			return false;
		}
	}
}
?>