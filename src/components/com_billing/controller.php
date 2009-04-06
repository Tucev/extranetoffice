<?php
/**
 * @version 	$Id: controller.php 40 2009-01-29 02:17:50Z luis.montero $
 * @package		ExtranetOffice
 * @subpackage	com_billing
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * billingController Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_billing
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class billingController extends phpFrame_Application_Controller {
	/**
	 * Constructor
	 * 
	 * @return	void
	 * @since 	1.0
	 */
	function __construct() {
		// set default request vars
		$this->view = phpFrame_Environment_Request::getVar('view', 'invoices');
		
		// It is important we invoke the parent's constructor before 
		// running permission check as we need the available views loaded first.
		parent::__construct();
	}
	
	function export() {
		$invoices->exportQIF();
	}
}
?>