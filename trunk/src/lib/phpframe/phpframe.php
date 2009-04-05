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

/**
 * Autoload magic method
 * 
 * This method is automatically called in case you are trying to use a class/interface which 
 * hasn't been defined yet. By calling this function the scripting engine is given a last 
 * chance to load the class before PHP fails with an error. 
 * 
 * @param	string	$className
 * @return	void
 */
function __autoload($className) {
	if (strpos($className, 'phpFrame') !== false) {
		$array = explode('_', $className);
		if (sizeof($array) == 3) {
			$file_path = _PHPFRAME_PATH.DS.strtolower($array[1]).DS.strtolower($array[2]).".php";
		}
		elseif (sizeof($array) == 2) {
			$file_path = _PHPFRAME_PATH.DS.strtolower($array[1]).DS.strtolower($array[1]).".php";	
		}
		
		if (file_exists($file_path)) {
			require $file_path;
		}
		else {
			die('Could not autoload class '.$className);
		}
	}
	elseif (strpos($className, 'PHPMailer') !== false) {
		$file_path = str_replace(DS.'phpframe', '', _PHPFRAME_PATH).DS."phpmailer".DS."phpmailer.php"; 
		
		if (file_exists($file_path)) {
			require $file_path;
		}
		else {
			die('Could not autoload class '.$className);
		}
	}
}

/**
 * phpFrame Class
 * 
 * This is the framework class.
 * 
 * This class extends the "phpFrame_Base_Singleton" class in order to implement the phpFrame_Base_Singleton design pattern.
 * 
 * The class is used to access the framework. For example:
 * 
 * <code>
 * $application =& phpFrame::getInstance('phpFrame_Application');
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
 * $application =& phpFrame::getInstance('phpFrame_Application');
 * $application->auth();
 * $application->exec();
 * $application->render();
 * $application->output();
 * </code>
 * 
 * @package		phpFrame
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @todo		phpFrame should be able to include whatever files i needed when getting "phpFrame_Base_Singleton" classes
 */
class phpFrame extends phpFrame_Base_Singleton {
	/**
	 * The phpFrame version
	 * 
	 * @var string
	 */
	private $_version='1.0 Alpha';
	/**
	 * phpFrame language
	 * 
	 * @var string
	 */
	private $_lang="en-GB";
	
	protected function __construct() {
		// Include language file
		$lang_file = _PHPFRAME_PATH.DS."lang".DS.$_lang.".php";
		
		if (file_exists($lang_file)) {
			require_once $lang_file;
		}
		else {
			die('phpFrame could not find its language file');
		}
	}
	
	public function getVersion() {
		return $this->_version;
	}
	
	public function getLang() {
		return $this->_lang;
	}
}
?>