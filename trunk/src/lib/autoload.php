<?php
/**
 * @version		$Id$
 * @package		ExtranetOffice
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

spl_autoload_register('__autoloadLib');

/**
 * Autoload libraries magic method
 * 
 * This method is automatically called in case you are trying to use a class/interface which 
 * hasn't been defined yet. By calling this function the scripting engine is given a last 
 * chance to load the class before PHP fails with an error. 
 * 
 * @param	string	$className
 * @return	void
 */
function __autoloadLib($className) {
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
		
		if (file_exists($file_path)) {
			require $file_path;
		}
		else {
			die('Could not autoload class '.$className);
		}
	}
	elseif ($className == 'PHPMailer') {
		$file_path = _ABS_PATH.DS."lib".DS."phpmailer".DS."phpmailer.php"; 
		
		if (file_exists($file_path)) {
			require $file_path;
		}
		else {
			die('Could not autoload class '.$className);
		}
	}
	elseif ($className == 'InputFilter') {
		$file_path = _ABS_PATH.DS."lib".DS."phpinputfilter".DS."inputfilter.php"; 
		
		if (file_exists($file_path)) {
			require $file_path;
		}
		else {
			die('Could not autoload class '.$className);
		}
	}
}
?>