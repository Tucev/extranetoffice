<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_admin
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * adminModelConfig Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_admin
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		model
 */
class adminModelConfig extends phpFrame_Application_Model {
	/**
	 * Update configuration file using data passed in request
	 * 
	 * Returns TRUE on success or FALSE if it fails.
	 * 
	 * @return bool
	 */
	function saveConfig($post) {
		$fname = _ABS_PATH.DS."inc".DS."config.php";
		// Open file for reading first
		if (!$fhandle = fopen($fname, "r")) {
			$this->_error[] = 'Error opening file '.$fname.'.';
			return false;
		}
		
		// Read content of file into string
		if (!$content = fread($fhandle, filesize($fname))) {
			$this->_error[] = 'Error reading file '.$fname.'.';
			return false;
		}
		
		// Use reflection API to get array of class constants
		$reflection_config = new ReflectionClass(config);
		$config_constants = $reflection_config->getConstants();
		
		// Loop through all config properties and build arrays with patterns and replacements for regex
		foreach ($config_constants as $key=>$value) {
			$lowercase_key = strtolower($key);
			if (isset($post[$lowercase_key]) && !empty($post[$lowercase_key])) {
				$patterns[] = '/const '.$key.'=(.*);/';
				$replacements[] = 'const '.$key.'="'.$post[$lowercase_key].'";';	
			}
		}
		
		// Replace config vars in config file contents
		$content = preg_replace($patterns, $replacements, $content);
		
		// Reopen file for writing
		if (!$fhandle = fopen($fname,"w")) {
			$this->_error[] = 'Error opening file '.$fname.' for writing.';
			return false;
		}
		// Write contents into file
		if (!fwrite($fhandle, $content)) {
			$this->_error[] = 'Error writing file '.$fname.'.';
			return false;
		}
		// Close file
		if (!fclose($fhandle)) {
			$this->_error[] = 'Error closing file '.$fname.' after writing.';
			return false;
		}
		else {
			return true;
		}
	}
	
}
?>