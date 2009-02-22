<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

define("_PHPFRAME_PATH", dirname(__FILE__));

// Include base object classes
require_once _PHPFRAME_PATH.DS."base".DS."standard.php";
require_once _PHPFRAME_PATH.DS."base".DS."singleton.php";
// include database classes
require_once _PHPFRAME_PATH.DS."database".DS."db.php";
require_once _PHPFRAME_PATH.DS."database".DS."table.php";
// include environment classes
require_once _PHPFRAME_PATH.DS."environment".DS."request.php";
require_once _PHPFRAME_PATH.DS."environment".DS."response.php";
require_once _PHPFRAME_PATH.DS."environment".DS."session.php";
require_once _PHPFRAME_PATH.DS."environment".DS."uri.php";
// Include application classes
require_once _PHPFRAME_PATH.DS."application".DS."application.php";
require_once _PHPFRAME_PATH.DS."application".DS."components.php";
require_once _PHPFRAME_PATH.DS."application".DS."controller.php";
require_once _PHPFRAME_PATH.DS."application".DS."debug.php";
require_once _PHPFRAME_PATH.DS."application".DS."error.php";
require_once _PHPFRAME_PATH.DS."application".DS."factory.php";
require_once _PHPFRAME_PATH.DS."application".DS."model.php";
require_once _PHPFRAME_PATH.DS."application".DS."modules.php";
require_once _PHPFRAME_PATH.DS."application".DS."pathway.php";
require_once _PHPFRAME_PATH.DS."application".DS."permissions.php";
require_once _PHPFRAME_PATH.DS."application".DS."route.php";
require_once _PHPFRAME_PATH.DS."application".DS."view.php";
require_once _PHPFRAME_PATH.DS."application".DS."user.php";
// Include HTML classes
require_once _PHPFRAME_PATH.DS."html".DS."html.php";
require_once _PHPFRAME_PATH.DS."html".DS."pagination.php";
require_once _PHPFRAME_PATH.DS."html".DS."text.php";
// Include utils classes
require_once _PHPFRAME_PATH.DS."utils".DS."crypt.php";
require_once _PHPFRAME_PATH.DS."utils".DS."utility.php";
// include document class
require_once _PHPFRAME_PATH.DS."document".DS."document.php";
require_once _PHPFRAME_PATH.DS."document".DS."html.php";

/**
 * phpFrame Class
 * 
 * This is the framework class.
 * 
 * This class extends the "singleton" class in order to implement the singleton design pattern.
 * 
 * The class is used to access the framework. For example:
 * 
 * <code>
 * $application =& phpFrame::getInstance('application');
 * </code>
 * 
 * Before we instantiate the application we first need to set a few useful constants,
 * check for dependencies, include required framework files and then finally 
 * instantiate the application and run the apps methods in the following order:
 * 
 * <code>
 * define("_EXEC", true);
 * define('_ABS_PATH', dirname(__FILE__) );
 * define( 'DS', DIRECTORY_SEPARATOR );
 * 
 * // include config
 * require_once _ABS_PATH.DS."inc".DS."config.php";
 * 
 * // Include phpFrame
 * require_once _ABS_PATH.DS."lib".DS."phpframe".DS."phpframe.php";
 * 
 * $application =& phpFrame::getInstance('application');
 * $application->auth();
 * $application->exec();
 * $application->render();
 * $application->output();
 * </code>
 * 
 * @package		phpFrame
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class phpFrame extends singleton {
	/**
	 * The phpFrame version
	 * 
	 * @var string
	 */
	var $version='1.0 Alpha';
}
?>