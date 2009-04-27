<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_dashboard
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
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
class dashboardController extends phpFrame_Application_ActionController {
	function __construct() {
		// set default view and layout if none has been set
		$this->view = phpFrame_Environment_Request::getView('dashboard');
		$this->layout = phpFrame_Environment_Request::getVar('layout', '');
		
		parent::__construct();
	}
	
}
?>