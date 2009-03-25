<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_email
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

require_once COMPONENT_PATH.DS.'controller.php';

// Create the controller
$controller =& phpFrame::getInstance('emailController');
// Execute task
$controller->execute(request::getVar('task'));	
// Redirect if set by the controller
$controller->redirect();
?>