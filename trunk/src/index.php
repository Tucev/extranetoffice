<?php
/**
 * The web application index file
 * 
 * This is the file that we browse to access the ExtranetOffice web application.
 * 
 * In order to instantiate the application we first set a few useful constants,
 * check for dependencies, include required framework files and then finally 
 * instantiate the application and run the apps methods in the following order:
 * 
 * <code>
 * $application =& phpFrame::getInstance('phpFrame_Application');
 * $application->auth();
 * $application->exec();
 * $application->render();
 * $application->output();
 * </code>
 * 
 * @version 	$Id: index.php 46 2009-02-13 01:37:49Z luis.montero $
 * @package		ExtranetOffice
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

/**
 *  Report all PHP errors
 */
error_reporting(E_ALL ^ E_NOTICE);

/**
 *  Set flag that this is a parent file
 */
define("_EXEC", true);
/**
 * Set constant containing absolute path to application
 */
define('_ABS_PATH', dirname(__FILE__) );
/**
 * Set DS constant (directory separator depends on server operating system).
 */
define( 'DS', DIRECTORY_SEPARATOR );

/**
 * @var string
 */
$config_file_path = _ABS_PATH.DS."inc".DS."config.php";
/**
 * If there is no config file or the installation directory is present we redirect to installation directory
 * @todo	This needs to be changed once we are ready to start testing the installation process. See commented line below.
 */
//if (!file_exists($config_file_path) || is_dir(_ABS_PATH.DS."installation")) {
if (!file_exists($config_file_path)) {
	header("Location: installation/index.php");
}
else {
	require_once $config_file_path;	
}

// check dependencies
require_once _ABS_PATH.DS."inc".DS."dependencies.php";
global $dependencies;
$dependencies = new dependencies();
if ($dependencies->status === false) {
	//TODO: Here we have to print nicely the dependencies info. For now we are just dumping the whole dependencies object.
	echo 'Please check all dependencies are installed.<br />';
	echo '<pre>'; var_dump($dependencies);
	exit;
}

// Include phpFrame
require_once _ABS_PATH.DS."lib".DS."phpframe".DS."phpframe.php";

// Instantiate application
$application =& phpFrame::getInstance('phpFrame_Application');
$application->auth();
$application->exec();
$application->render();
$application->output();
?>