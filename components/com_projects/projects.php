<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

require_once COMPONENT_PATH.DS.'controller.php';
require_once COMPONENT_PATH.DS.'helpers'.DS.'projects.helper.php';
require_once _ABS_PATH.DS.'components'.DS.'com_user'.DS.'helpers'.DS.'users.helper.php';

// Create the controller
$controller =& phpFrame::getInstance('projectsController');
// Execute task
$controller->execute(request::getVar('task'));	
// Redirect if set by the controller
$controller->redirect();
?>