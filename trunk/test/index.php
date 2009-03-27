<?php
/**
 * The PHPUnit test index file
 * 
 * @version 	$Id$
 * @package		ExtranetOffice
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

ini_set('xdebug.var_display_max_children', 99);
ini_set('xdebug.var_display_max_depth', 99);

/**
 *  Set flag that this is a parent file
 */
define("_EXEC", true);

/**
 * Set DS constant (directory separator depends on server operating system).
 */
define( 'DS', DIRECTORY_SEPARATOR );

/**
 * Set constant containing absolute path to unit tests
 */
define('_ABS_PATH_TEST', dirname(__FILE__) );

/**
 * Set constant containing absolute path to application
 */
define('_ABS_PATH', str_replace(DS."test",DS."src",_ABS_PATH_TEST) );

/**
 * @var string
 */
$config_file_path = _ABS_PATH_TEST.DS."inc".DS."config.php";

require_once $config_file_path;	

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
$application =& phpFrame::getInstance('application');
$application->auth();

// Include PHPUnit
require_once "PHPUnit/Framework.php";
require_once "PHPUnit/TextUI/TestRunner.php";

// Unit tests
require_once _ABS_PATH_TEST.DS."components".DS."com_projects".DS."models".DS."testProjects.php";

$suite  = new PHPUnit_Framework_TestSuite('something random');
$suite->addTestSuite('testProjects');

PHPUnit_TextUI_TestRunner::run($suite)
?>