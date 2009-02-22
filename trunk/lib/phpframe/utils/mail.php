<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	utils
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Include phpmailer
 */
require_once _PHPMAILER_PATH.DS.'phpmailer.php';

/**
 * Mail Class
 * 
 * @package		phpFrame
 * @subpackage 	utils
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class mail extends PHPMailer {
	/**
	 * Constructor
	 * 
	 * Initialise some PHPMailer default values
	 * 
	 * @return	void
	 * @since	1.0
	 */
	function __construct() {
		$config =& factory::getConfig();
		$this->Mailer = $config->mailer;
		$this->Host = $config->smtp_host;
		$this->Port = $config->smtp_port;
		$this->SMTPAuth = $config->smtp_auth;
		$this->Username = $config->smtp_user;
		$this->Password = $config->smtp_password;
	}
}
?>