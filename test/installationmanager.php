<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	PHPUnit test suite
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

class installationManager {
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
	}
	
	public static function resetFilesystem() {
		$cmd = "rm -r ".config::FILESYSTEM.DS."*";
		passthru($cmd, $status);
		if ($status > 0) {
			throw new Exception('Could NOT delete contents of '.config::FILESYSTEM.'.');
		}
		
		$cmd = "rm -r "._ABS_PATH.DS.config::UPLOAD_DIR.DS."*";
		passthru($cmd, $status);
		if ($status > 0) {
			throw new Exception('Could NOT delete contents of '._ABS_PATH.DS.config::UPLOAD_DIR.'.');
		}
	}
}
?>