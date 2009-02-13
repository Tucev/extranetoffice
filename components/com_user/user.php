<?php
/**
* @package		ExtranetOffice
* @subpackage	com_user
* @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
* @license		BSD revised. See LICENSE.
* @author 		Luis Montero [e-noise.com]
* @version 		1.0.0
*/

defined( '_EXEC' ) or die( 'Restricted access' );

require_once COMPONENT_PATH.DS.'controller.php';

// Create the controller
$controller =& userController::getInstance('userController');
// Execute task
$controller->execute(request::getVar('task', 'display'));	
// Redirect if set by the controller
$controller->redirect();
?>