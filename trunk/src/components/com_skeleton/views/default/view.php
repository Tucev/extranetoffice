<?php
/**
 * @version 	$Id$
 * @package		PHPFrame
 * @subpackage	com_skeleton
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * skeletonViewDefault Class
 * 
 * @package		PHPFrame
 * @subpackage 	com_skeleton
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		PHPFrame_Application_View
 */
class skeletonViewDefault extends PHPFrame_Application_View {
	/**
	 * Constructor
	 * 
	 * @return 	void
	 * @since	1.0
	 */
	function __construct($layout) {
		// Invoke the parent to set the view name and default layout
		parent::__construct('default', $layout);
	}
}
