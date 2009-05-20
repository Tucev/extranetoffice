<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	registry
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Session Class
 * 
 * The Session class is responsible for detecting the client (default, mobile, cli or xmlrpc)
 * 
 * @package		phpFrame
 * @subpackage 	registry
 * @since 		1.0
 */
class phpFrame_Registry_Session extends phpFrame_Registry {
	/**
	 * Instance of itself in order to implement the singleton pattern
	 * 
	 * @var object of type phpFrame_Application_FrontController
	 */
	private static $_instance=null;
	
	/**
	 * Constructor
	 * 
	 * Invoke the parent (table) constructor and start the session.
	 *
	 */
	protected function __construct() {
		// start php session
		session_start();
		//$this->destroy();
		
		// If new session we set the id
		if (!$_SESSION['id'] || $_SESSION['id'] != session_id()) {
			$_SESSION['id'] = session_id();
			
			$_SESSION['user'] = new phpFrame_User();
			$_SESSION['user']->set("id", 0);
			$_SESSION['user']->set("groupid", 0);
			
			$_SESSION['sysevents'] = new phpFrame_Application_Sysevents();
			
			$this->getToken(true);
			
			$this->_detectClient();
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
		return $_SESSION[$key];
	}
	
	public function set($key, $value) {
		$_SESSION[$key] = $value;
	}
	
	/**
	 * Get client object
	 * 
	 * @return	object of type phpFrame_Environment_IClient
	 */
	public static function getClient() {
		return $_SESSION['client'];
	}
	

	/**	
	 * Get $_client_helper->getName();
	 * 
	 * @static
	 * @access	public
	 * @return	client helper name
	 */
	public static function getClientName() {
		//initialise if  $_client_helper is null 
		if ($_SESSION['client'] == null) $this->_detectClient();
		
		return $_SESSION['client']->getName();
	}
	
	/**
	 * 
	 * @param	object	$user	User object of type phpFrame_User
	 * 
	 * @return	void
	 */
	public function setUser(phpFrame_User $user) {
		$_SESSION['user'] = $user;
	}
	
	public function getUser() {
		return $_SESSION['user'];
	}
	
	/**
	 * Get session user id
	 * 
	 * @return	int
	 */
	public function getUserId() {
		return $_SESSION['user']->get('id');
	}
	
	/**
	 * Get session group id
	 * 
	 * @return	int
	 */
	public function getGroupId() {
		return $_SESSION['user']->get('groupid');
	}
	
	/**
	 * Is the current session authenticated?
	 * 
	 * @access	public
	 * @return	boolean	Returns TRUE if user is authenticated and FALSE otherwise.
	 * @since	1.0
	 */
	public function isAuth() {
		if ($_SESSION['user']->get('id') > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Is the current session an admin session?
	 * 
	 * @return	boolean
	 */
	public function isAdmin() {
		if ($_SESSION['user']->get('groupid') == 1) {
			return true;
		}
		else {
			return false;
		}
	}
	
	public function getSysevents() {
		return $_SESSION['sysevents'];
	}
	
	/**
	 * Get a session token, if a token isn't set yet one will be generated.
	 * 
	 * Tokens are used to secure forms from spamming attacks. Once a token
	 * has been generated the system will check the post request to see if
	 * it is present, if not it will invalidate the session.
	 * 
	 * @access	public
	 * @param	boolean $forceNew If true, force a new token to be created
	 * @return	string
	 * @since	1.0
	 */
	public function getToken($force_new = false) {
		//create a token
		if ($_SESSION['token'] === null || $force_new) {
			$_SESSION['token'] = $this->_createToken(12);
		}

		return $_SESSION['token'];
	}
	
	public function destroy() {
		unset($_SESSION);
		unset($_COOKIE);
		self::$_instance = null;
		session_regenerate_id(true); // this destroys the session and generates a new session id
		//var_dump($_SESSION); exit;
	}
	
	/**
	 * Detect and set helper object 
	 * 
	 */
	private function _detectClient() {
		
		//scan through environment dir to find files
		$clients_path = _ABS_PATH.DS."lib".DS."phpframe".DS."client";
		$files = scandir($clients_path);
		
		//make sure the clientdefault is the last file in array as catch-all helper
		$default = array_search('default.php', $files);	//if it's there
		if ($default != false) {
			unset($files[$default]);						//unset it
			$files[] = 'default.php';						//add to end
		}
		
		//loop through files 
		foreach ($files as $file) {
			if (is_file($clients_path.DS.$file) && $file != 'iclient.php') {
				//build class names
				$className = 'phpFrame_Client_'.ucfirst(substr($file, 0, strpos($file, '.')));
				if (is_callable(array($className, 'detect'))) {
					//call class's detect() to check if this is the helper we need 
					$_SESSION['client'] = call_user_func(array($className, 'detect'));
					if ($_SESSION['client'] instanceof phpFrame_Client_IClient) {
						//break out of the function if we found our helper
						return;
					} 
				}
			}
		}
		//throw error if no helper is found
		throw new phpFrame_Exception(_PHPFRAME_LANG_SESSION_ERROR_NO_CLIENT_DETECTED);
	}
	
	/**
	 * Create a token-string
	 * 
	 * @access	private
	 * @param	int			$length Lenght of string.
	 * @return	string		$id Generated token.
	 * @since	1.0
	 */
	private function _createToken($length = 32) {
		static $chars = '0123456789abcdef';
		$max = strlen( $chars ) - 1;
		$token = '';
		$name = session_name();
		
		for($i=0; $i<$length; ++$i) {
			$token .= $chars[ (rand( 0, $max )) ];
		}

		return md5($token.$name);
	}
}
