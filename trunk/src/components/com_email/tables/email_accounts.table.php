<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_email
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * emailTableAccounts Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_email
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class emailTableAccounts extends phpFrame_Database_Table {
	/**
	 * The account id (int(11) auto_increment)
	 * 
	 * @var int
	 */
	var $id=null;
	/**
	 * The user id
	 * 
	 * @var int
	 */
	var $userid=null;
	/**
	 * Email signature
	 * 
	 * @var string
	 */
	var $email_signature=null;
	/**
	 * Incoming server type
	 * 
	 * @var string
	 */
	var $server_type='IMAP';
	/**
	 * IMAP host name or IP address
	 * 
	 * @var string
	 */
	var $imap_host=null;
	/**
	 * IMAP port
	 * 
	 * @var int
	 */
	var $imap_port=null;
	/**
	 * IMAP login username
	 * 
	 * @var string
	 */
	var $imap_user=null;
	/**
	 * IMAP password
	 * 
	 * @var string
	 */
	var $imap_password=null;
	/**
	 * From name
	 * 
	 * @var string
	 */
	var $fromname=null;
	/**
	 * Email address
	 * 
	 * @var string
	 */
	var $email_address=null;
	/**
	 * SMTP host name or IP address
	 * 
	 * @var string
	 */
	var $smtp_host=null;
	/**
	 * SMTP port
	 * 
	 * @var int
	 */
	var $smtp_port=null;
	/**
	 * SMTP Auth
	 * 
	 * @var bool
	 */
	var $smtp_auth=null;
	/**
	 * SMTP login username
	 * 
	 * @var string
	 */
	var $smtp_user=null;
	/**
	 * SMTP password
	 * 
	 * @var string
	 */
	var $smtp_password=null;
	/**
	 * Is user's default account?
	 * 
	 * @var bool
	 */
	var $default=null;
	
	/**
	 * Construct
	 * 
	 * @return void
	 */
	function __construct() {
		parent::__construct( '#__email_accounts', 'id' );
	}
}
?>