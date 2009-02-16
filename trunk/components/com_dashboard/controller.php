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
 * @package		ExtranetOffice
 * @subpackage 	com_dashboard
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class dashboardController extends controller {
	function __construct() {
		// set default view and layout if none has been set
		$this->view = request::getVar('view', 'dashboard');
		$this->layout = request::getVar('layout', '');
		
		parent::__construct();
	}
	
}
?>