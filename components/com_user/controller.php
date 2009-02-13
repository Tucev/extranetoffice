<?php
/**
* @package		ExtranetOffice
* @subpackage	com_user
* @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
* @license		BSD revised. See LICENSE.
* @author 		Luis Montero [e-noise.com]
* @version 		1.0.0
*/

class userController extends controller {
	function __construct() {
		parent::__construct();
		
		// set default view if none has been set
		$view = request::getVar('view', '');
		if (empty($view)) {
			request::setVar('view', 'settings');
		}
	}
	
}
?>