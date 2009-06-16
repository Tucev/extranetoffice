<?php
/**
 * @version 	$Id$
 * @package		PHPFrame
 * @subpackage	com_skeleton
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * skeletonController Class
 * 
 * @package		PHPFrame
 * @subpackage 	com_skeleton
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class skeletonController extends PHPFrame_MVC_ActionController {
	/**
	 * Constructor
	 * 
	 * @return void
	 */
	protected function __construct() {
		// Invoke parent's constructor to set default action
		parent::__construct('dispatch');
	}
	
	public function dispatch() {
		// Get view
		$view = $this->getView('default', '');
		// Display view
		$view->display();
	}
}
