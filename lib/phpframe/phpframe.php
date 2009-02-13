<?php
/**
 * @version		$Id: phpframe.php 45 2009-02-05 11:45:04Z luis.montero $
 * @package		phpFrame
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

define("_PHPFRAME_PATH", dirname(__FILE__));

// Include object class
require_once _PHPFRAME_PATH.DS."objects".DS."singleton.php";
require_once _PHPFRAME_PATH.DS."objects".DS."standard.php";
// include database classes
require_once _PHPFRAME_PATH.DS."database".DS."db.php";
require_once _PHPFRAME_PATH.DS."database".DS."table.php";
// include environment classes
require_once _PHPFRAME_PATH.DS."environment".DS."request.php";
require_once _PHPFRAME_PATH.DS."environment".DS."session.php";
// Include application classes
require_once _PHPFRAME_PATH.DS."application".DS."application.php";
require_once _PHPFRAME_PATH.DS."application".DS."component.php";
require_once _PHPFRAME_PATH.DS."application".DS."controller.php";
require_once _PHPFRAME_PATH.DS."application".DS."debug.php";
require_once _PHPFRAME_PATH.DS."application".DS."error.php";
require_once _PHPFRAME_PATH.DS."application".DS."factory.php";
require_once _PHPFRAME_PATH.DS."application".DS."menu.php";
require_once _PHPFRAME_PATH.DS."application".DS."model.php";
require_once _PHPFRAME_PATH.DS."application".DS."pathway.php";
require_once _PHPFRAME_PATH.DS."application".DS."route.php";
require_once _PHPFRAME_PATH.DS."application".DS."view.php";
require_once _PHPFRAME_PATH.DS."application".DS."user.php";
// Include utils classes
require_once _PHPFRAME_PATH.DS."utils".DS."crypt.php";
require_once _PHPFRAME_PATH.DS."utils".DS."text.php";
require_once _PHPFRAME_PATH.DS."utils".DS."utility.php";
// include document class
require_once _PHPFRAME_PATH.DS."document".DS."document.php";
?>