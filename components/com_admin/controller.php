<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_admin
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * adminController Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_admin
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class adminController extends controller {
	/**
	 * Constructor
	 * 
	 * @return	void
	 * @since 	1.0
	 */
	function __construct() {
		// set default request vars
		$this->view = request::getVar('view', 'admin');
		
		// It is important we invoke the parent's constructor before 
		// running permission check as we need the available views loaded first.
		parent::__construct();
	}
	
	/**
	 * Save global configuration
	 * 
	 * @return void
	 */
	function save_config() {
		$modelConfig =& $this->getModel('config');
		
		if ($modelConfig->saveConfig() === false) {
			error::raise('', 'error', $modelConfig->getLastError());
		}
		else {
			error::raise('', 'message', _LANG_CONFIG_SAVE_SUCCESS);
		}
		
		$this->setRedirect('index.php?option=com_admin&view=config');
	}
}
?>