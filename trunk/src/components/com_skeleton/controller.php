<?php
/**
 * @version 	$Id$
 * @package		phpFrame
 * @subpackage	com_skeleton
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * skeletonController Class
 * 
 * @package		phpFrame
 * @subpackage 	com_skeleton
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class skeletonController extends phpFrame_Application_ActionController {
	/**
	 * Constructor
	 * 
	 * This method overrides the parent constructor to avoid checking for permissions. 
	 * It is the login component, so we do not need to check access levels.
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
