<?php
/**
 * The web application index or bootstrap file
 * 
 * This is the file that we browse to access the web application.
 * 
 * In order to instantiate the application we first set a few useful constants, 
 * set the global exception handler, load the config file, check for dependencies, 
 * include the autoloader and then finally instantiate the application and run its 
 * methods.
 * 
 * @version 	$Id: index.php 46 2009-02-13 01:37:49Z luis.montero $
 * @package		ExtranetOffice
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @see			phpFrame_Application
 */

/**
 *  Set flag that this is a parent file
 */
define("_EXEC", true);
/**
 * Set constant containing absolute path to application
 */
define('_ABS_PATH', dirname(__FILE__) );
/**
 * Set convenience DS constant (directory separator depends on server operating system).
 */
define( 'DS', DIRECTORY_SEPARATOR );

/**
 * Path to configuration file
 * 
 * @var string
 */
$config_file_path = _ABS_PATH.DS."inc".DS."config.php";
/**
 * If there is no config file we redirect to installation directory
 */
if (!file_exists($config_file_path)) {
	header("Location: installation/index.php");
}
else {
	require_once $config_file_path;	
}

// Include autoloader
require_once _ABS_PATH.DS."inc".DS."autoload.php";

// Instantiate application
$application = phpFrame::getApplication();
$application->auth();
$application->exec();
$application->render();
$application->output();
?>