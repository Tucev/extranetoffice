<?php
/**
 * @version		$Id$
 * @package		ExtranetOffice
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

spl_autoload_register('__autoload');

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
	// phpFrame classes
	if (strpos($className, 'phpFrame') !== false) {
		$array = explode('_', $className);
		if (sizeof($array) == 3) {
			$file_path = _ABS_PATH.DS."lib".DS."phpframe".DS.strtolower($array[1]).DS.strtolower($array[2]).".php";
		}
		elseif (sizeof($array) == 2) {
			$file_path = _ABS_PATH.DS."lib".DS."phpframe".DS.strtolower($array[1]).DS.strtolower($array[1]).".php";
		}
		elseif (sizeof($array) == 1) {
			$file_path = _ABS_PATH.DS."lib".DS."phpframe".DS."phpframe.php";
		}
	}
	// MVC classes
	elseif (strpos($className, 'Model') !== false) {
		$array = explode('Model', $className);
		if (sizeof($array) == 2) {
			$file_path = _ABS_PATH.DS."components".DS.phpFrame_Environment_Request::getVar('option').DS;
			$file_path .= "models".DS.strtolower($array[1]).".php";
		}
	}
	elseif (strpos($className, 'View') !== false) {
		$array = explode('View', $className);
		if (sizeof($array) == 2) {
			$file_path = _ABS_PATH.DS."components".DS.phpFrame_Environment_Request::getVar('option').DS;
			$file_path .= "views".DS.strtolower($array[1]).DS."view.php";
		}
	}
	elseif (strpos($className, 'Controller') !== false) {
		$file_path = _ABS_PATH.DS."components".DS.phpFrame_Environment_Request::getVar('option').DS."controller.php";
	}
	elseif (strpos($className, 'Helper') !== false) {
		$array = explode('Helper', $className);
		if (sizeof($array) == 2) {
			$file_path = _ABS_PATH.DS."components".DS.phpFrame_Environment_Request::getVar('option').DS;
			$file_path .= "helpers".DS.strtolower($array[1]).".helper.php";
		}
	}
	elseif (strpos($className, 'Table') !== false) {
		$array = explode('Table', $className);
		if (sizeof($array) == 2) {
			$file_path = _ABS_PATH.DS."components".DS.phpFrame_Environment_Request::getVar('option').DS;
			$file_path .= "tables".DS.strtolower($array[1]).".table.php";
		}
	}
	// PHPMailer
	elseif ($className == 'PHPMailer') {
		$file_path = _ABS_PATH.DS."lib".DS."phpmailer".DS."phpmailer.php"; 
	}
	// PHPInputFilter
	elseif ($className == 'InputFilter') {
		$file_path = _ABS_PATH.DS."lib".DS."phpinputfilter".DS."inputfilter.php"; 
	}
	
	if (file_exists($file_path)) {
		require $file_path;
	}
	else {
		phpFrame_Application_Error::raiseFatalError('Could not autoload class '.$className.'. File '.$file_path.' not found.');
	}
}
?>