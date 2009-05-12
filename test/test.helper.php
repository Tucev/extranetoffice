<?php
/**
 * @version 	$Id$
 * @package		phpFrame
 * @subpackage 	PHPUnit_test_suite
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

class testHelper {
	public static function prepareApplication() {
		// Set constants
		define("_EXEC", true);
		define('_ABS_PATH', str_replace(DS."test", DS."src",_ABS_PATH_TEST) );
		
		// Include test config
		require_once _ABS_PATH_TEST.DS."inc".DS."config.php";
		// Include autoloader
		require_once _ABS_PATH.DS."inc".DS."autoload.php";
	}
	
	public static function freshInstall() {
		self::resetDatabase();
		self::resetFilesystem();
	}
	
	public static function resetDatabase() {
		$cmd = "mysql -u ".config::DB_USER." -p".config::DB_PASS." ".config::DB_NAME." < "._ABS_PATH.DS."installation".DS."install.sql";
		passthru($cmd, $status);
		if ($status == 1) {
			throw new Exception('Could NOT reset database before running com_project controller tests.');
		}
		
		// Add system user for tests to database table
		$cmd = "mysql -u ".config::DB_USER." -p".config::DB_PASS." ".config::DB_NAME." < "._ABS_PATH_TEST.DS."testuser.sql";
		passthru($cmd, $status);
	}
	
	public static function resetFilesystem() {
		self::_emptyDir(config::FILESYSTEM);
		self::_emptyDir(_ABS_PATH.DS.config::UPLOAD_DIR.DS."projects");
		self::_emptyDir(_ABS_PATH.DS.config::UPLOAD_DIR.DS."users");
	}
	
	private static function _emptyDir($dir) {
		if (is_dir($dir)) {
			$contents = scandir($dir);
			// Ignore .svn folders from the array count.
			unset($contents[array_search('.svn', $contents)]);
			// If there are more than two items ('.' and '..') we proceed to delete
			if (sizeof($contents) > 2) {
				$cmd = "rm -r ".$dir.DS."*";
				passthru($cmd, $status);
				if ($status > 0) {
					throw new Exception('Could NOT delete contents of '.$dir.'.');
				}	
			}
		}
	}
}
