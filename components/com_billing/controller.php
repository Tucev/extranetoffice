<?php
/**
 * @version 	$Id: controller.php 40 2009-01-29 02:17:50Z luis.montero $
 * @package		ExtranetOffice.Billing
 * @subpackage	controllers
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * billingController Class
 * 
 * @package		ExtranetOffice.Billing
 * @subpackage 	controllers
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class billingController extends controller {
	function __construct() {
		// set default view if none has been set
		$view = request::getVar('view', '');
		if (empty($view)) {
			request::setVar('view', 'invoices');
		}
	}
	
	function export() {
		$invoices->exportQIF();
	}
}
?>