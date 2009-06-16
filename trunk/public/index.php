<?php
/**
 * The web application index / bootstrap file
 * 
 * This is the file that we browse to access the web application.
 * 
 * This file instantiates and runs the Front Controller object.
 * 
 * In order to instantiate the Front Controller we first set a few useful constants, 
 * load the config file and include the autoloader.
 * 
 * @version 	$Id: index.php 46 2009-02-13 01:37:49Z luis.montero $
 * @package		PHPFrame
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @see			PHPFrame_Application_FrontController
 */

//TODO: This needs to be removed. It is temporarily here to make up for the
// release of PHPFrame as a PEAR package
$PHPFrame_path = "/Users/lupomontero/Documents/workspace/PHPFrame/src";
set_include_path(get_include_path() . PATH_SEPARATOR . $PHPFrame_path);

/**
 * Set convenience DS constant (directory separator depends on server operating system).
 */
define( 'DS', DIRECTORY_SEPARATOR );
/**
 * Set constant containing absolute path to application
 */
define('_ABS_PATH', str_replace(DS."public", "", dirname(__FILE__) ));

/**
 * Path to configuration file
 * 
 * @var string
 */
$config_file_path = _ABS_PATH.DS."src".DS."config.php";
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
require_once _ABS_PATH.DS."src".DS."autoload.php";

// Fire up PHPFrame
PHPFrame::Fire();
