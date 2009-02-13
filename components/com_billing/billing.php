<?php
/**
 * @version 	$Id: billing.php 40 2009-01-29 02:17:50Z luis.montero $
 * @package		ExtranetOffice.Billing
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

require_once COMPONENT_PATH.DS.'controller.php';

// Create the controller
$controller =& billingController::getInstance('billingController');
// Execute task
$controller->execute(request::getVar('task', 'display'));	
// Redirect if set by the controller
$controller->redirect();
?>