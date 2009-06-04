<?php
/**
 * @version 	$Id$
 * @package		phpFrame
 * @subpackage	com_login
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * loginViewLogin Class
 * 
 * @package		phpFrame
 * @subpackage 	com_login
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		phpFrame_Application_View
 */
class loginViewLogin extends phpFrame_Application_View {
	/**
	 * Constructor
	 * 
	 * @return 	void
	 * @since	1.0
	 */
	function __construct($layout) {
		// Invoke the parent to set the view name and default layout
		parent::__construct('login', $layout);
	}
}
