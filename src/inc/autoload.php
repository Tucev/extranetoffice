<?php
/**
 * @version		$Id$
 * @package		phpFrame
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
 * @param	string	$class_name
 * @return	void
 */
function __autoload($class_name) {
	// phpFrame classes
	if (strpos($class_name, 'phpFrame') !== false) {
		$array = explode('_', $class_name);
		if (sizeof($array) == 3) {
			$file_path = "lib".DS."phpframe".DS.strtolower($array[1]).DS.strtolower($array[2]).".php";
		}
		elseif (sizeof($array) == 2) {
			$file_path = "lib".DS."phpframe".DS.strtolower($array[1]).DS.strtolower($array[1]).".php";
		}
		elseif (sizeof($array) == 1) {
			$file_path = "lib".DS."phpframe".DS."phpframe.php";
		}
	}
	// PHPMailer
	elseif ($class_name == 'PHPMailer') {
		$file_path = "lib".DS."phpmailer".DS."phpmailer.php";
	}
	// PHPInputFilter
	elseif ($class_name == 'InputFilter') {
		$file_path = "lib".DS."phpinputfilter".DS."inputfilter.php";
	}
	elseif ($class_name == 'vCard') {
		$file_path = "lib".DS."bitfolge".DS."vcard.php";
	}
	
	else {
		// Components classes
		preg_match('/^([a-z]+)([A-Z]{1}[a-z]+)([A-Z]{1}[a-z]+)?([A-Z]{1}[a-z]+)?$/', $class_name, $matches);
		if (is_array($matches) && count($matches) > 1) {
			$file_path = "components".DS."com_".strtolower($matches[1]).DS;
			
			switch ($matches[2]) {
				case 'Controller' : 
					$file_path .= "controller.php";
					break;
				case 'Model' : 
					$file_path .= "models".DS.strtolower($matches[3]).".php";
					break;
				case 'View' : 
					$file_path .= "views".DS.strtolower($matches[3]).DS."view.php";
					break;
				case 'Helper' : 
					$file_path .= "helpers".DS.strtolower($matches[3]).".helper.php";
					break;
				case 'Table' : 
					$file_path .= "tables".DS.strtolower($matches[3]);
					if (isset($matches[4])) $file_path .= "_".$matches[4];
					$file_path .=".table.php";
					break;
			}
		}
		
	}
	
	// require the file if it exists, otherwise we throw an exception
	$file_path = _ABS_PATH.DS.$file_path;
	if (is_file($file_path)) {
		require $file_path;
	}
	else {
		throw new phpFrame_Exception('Could not autoload class '.$class_name.'. File '.$file_path.' not found.');
	}
}
