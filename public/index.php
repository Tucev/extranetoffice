<?php
/**
 * public/index.php
 * 
 * PHP version 5
 * 
 * The web application index / bootstrap file
 * 
 * This is the file that we browse to access the web application.
 * 
 * This is reponsible for firing up the application.
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @copyright  2009 E-noise.com Limited
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 */

/**
 * Set constant containing absolute path to application
 */
define('_ABS_PATH', str_replace(DIRECTORY_SEPARATOR."public", "", dirname(__FILE__)));

/**
 * Path to configuration file
 * 
 * @var string
 */
$config_file_path = _ABS_PATH.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."config.php";

/**
 * If there is no config file we redirect to installation directory
 */
if (!file_exists($config_file_path)) {
    header("Location: installation/index.php");
}
else {
    require_once $config_file_path;    
}

// Include PHPFrame main file
require_once "PHPFrame.php";

// Include autoloader
require_once _ABS_PATH.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."autoload.php";

// Fire up PHPFrame
PHPFrame::Fire();
