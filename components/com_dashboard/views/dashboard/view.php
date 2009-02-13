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

class dashboardViewDashboard extends view {
	var $page_title=null;
	
	function display() {
		$this->page_title = _LANG_DASHBOARD;
		parent::display();
	}
}
?>