<?php
/**
 * @version		$Id: session.php 545 2009-05-20 02:41:31Z luis.montero@e-noise.com $
 * @package		phpFrame
 * @subpackage 	registry
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Application Registry Class
 * 
 * The Session class is responsible for detecting the client (default, mobile, cli or xmlrpc)
 * 
 * @package		phpFrame
 * @subpackage 	registry
 * @since 		1.0
 */
class phpFrame_Registry_Application extends phpFrame_Registry {
	/**
	 * Instance of itself in order to implement the singleton pattern
	 * 
	 * @var object of type phpFrame_Application_FrontController
	 */
	private static $_instance=null;
	private $_cache_file=null;
	private $_readonly=array("permissions", "components", "modules");
	private $_array=array();
	
	/**
	 * Constructor
	 * 
	 * Invoke the parent (table) constructor and start the session.
	 *
	 */
	protected function __construct() {
		// Ensure that cache dir is writable
		phpFrame_Utils_Filesystem::ensureWritableDir(config::FILESYSTEM.DS."cache");
		
		$this->_cache_file = config::FILESYSTEM.DS."cache".DS."application.registry";
		// Read data from cache
		if (is_file($this->_cache_file)) {
			$serialized_array = file_get_contents($this->_cache_file);
			$this->_array = unserialize($serialized_array);
		}
		else {
			// Re-create data
			$this->_array['permissions'] = new phpFrame_Application_Permissions();
			$this->_array['components'] = new phpFrame_Application_Components();
			$this->_array['modules'] = new phpFrame_Application_Modules();
			
			// Store data in cache file
			phpFrame_Utils_Filesystem::write($this->_cache_file, serialize($this->_array));
		}
	}
	
	/**
	 * Get Instance
	 * 
	 * @return phpFrame_Application_FrontController
	 */
	public static function getInstance() {
		if (!isset(self::$_instance)) {
			self::$_instance = new self;
		}
		
		return self::$_instance;
	}
	
	public function get($key) {
		return $this->_array[$key];
	}
	
	public function set($key, $value) {
		if (array_key_exists($key, $this->_readonly)) {
			throw new phpFrame_Exception("Tried to set a read-only key (".$key.") in Application Registry.");
		}
		
		$this->_array[$key] = $value;
	}
	
	public function getPermissions() {
		return $this->_array['permissions'];
	}
	
	public function getComponents() {
		return $this->_array['components'];
	}
	
	public function getModules() {
		return $this->_array['modules'];
	}
}
