<?php
/**
* @package		ExtranetOffice
* @subpackage	com_dashboard
* @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
* @license		BSD revised. See LICENSE.
* @author 		Luis Montero [e-noise.com]
* @version 		1.0.0
*/

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * dashboardController Class
 * 
 * @package		ExtranetOffice.Billing
 * @subpackage 	controllers
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class dashboardController extends controller {
	function __construct() {
		// set default view if none has been set
		$view = request::getVar('view', '');
		if (empty($view)) {
			request::setVar('view', 'dashboard');
		}
	}
	
}
?>