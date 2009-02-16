<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	application
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Application Class
 * 
 * This is the mainframe application class.
 * 
 * This class extends the "singleton" class in order to implement the singleton design pattern.
 * 
 * The class should be instantiated as:
 * 
 * <code>
 * $application =& phpFrame::getInstance('application');
 * </code>
 * 
 * @package		phpFrame
 * @subpackage 	application
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see			phpFrame
 */
class application extends singleton {
	/**
	 * Debugger object
	 * 
	 * @var object
	 */
	var $debug=null;
	/**
	 * The configuration object
	 *
	 * @var object
	 */
	var $config=null;
	/**
	 * The request array
	 *
	 * @var array
	 */
	var $request=null;
	/**
	 * The database object
	 *
	 * @var object
	 */
	var $db=null;
	/**
	 * The session object
	 *
	 * @var object
	 */
	var $session=null;
	/**
	 * A boolean representing whether a session is authenticated
	 *
	 * @var bool
	 */
	var $auth=null;
	/**
	 * The user object
	 *
	 * @var object
	 */
	var $user=null;
	/**
	 * Global application permissions object
	 * 
	 * @var object
	 */
	var $permissions=null;
	/**
	 * The modules object
	 *
	 * @var object
	 */
	var $modules=null;
	/**
	 * The output buffer produced by the executed component
	 *
	 * @var string
	 */
	var $component_output=null;
	/**
	 * The final output once content is rendered in template
	 *
	 * @var string
	 */
	var $output=null;
	
	/**
	 * Constructor
	 * 
	 * The application constructor loads the config, request and db objects.
	 * 
	 * @since	1.0
	 * @return 	void
	 */
	protected function __construct() {
		// instantiate debbuger
		$this->debug = new debug();

		// load config
		$this->config = new config;
		
		// load the language file
		// set default distro language if the one set in config doesnt exist
		if (!file_exists(_ABS_PATH.DS."lang".DS.$this->config->default_lang.".php")) {
			$this->config->default_lang = "en-GB";
		}
		require_once _ABS_PATH.DS."lang".DS.$this->config->default_lang.".php";
		
		// load request into the application
		$this->request = request::init();
		
		// instantiate db object and store in application
		$this->db =& db::getInstance('db');
		// connect to MySQL server
		$this->db->connect($this->config->db_host, $this->config->db_user, $this->config->db_pass, $this->config->db_name);
	}
	
	/**
	 * auth()
	 * 
	 * This method should be invoked after instantiating the application class.
	 * It instantiates the session and user objects and returns a boolean depending 
	 * on whether the current user has been authenticated.
	 * 
	 * @since	1.0
	 * @return	bool
	 */
	public function auth() {
		// get session object (singeton)
		$this->session =& session::getInstance('session');
		
		// get user object
		$this->user = new user();
		
		if (!empty($this->session->userid)) {
			$this->user->load($this->session->userid);
		}
		
		// If user user is not logged on we set auth to false and option to login
		if (!$this->user->id) {
			$this->auth = false;
			request::setVar('option', 'com_login');
			return $this->auth;
		}
		
		$this->auth = true;
		return $this->auth;
	}
	
	/**
	 * exec()
	 * 
	 * This method executes the request and stores the component's output buffer in $this->component_output.
	 * 
	 * @since	1.0
	 * @return	void
	 */
	public function exec() {
		// set the component path
		define("COMPONENT_PATH", _ABS_PATH.DS."components".DS.request::getVar('option', 'com_dashboard'));
		// Start buffering
		ob_start();
		// Load component file
		require_once COMPONENT_PATH.DS.substr(request::getVar('option'), 4).'.php';
		// save buffer
		$this->component_output = ob_get_contents();
		// clean output buffer
		ob_end_clean();
	}
	
	public function render() {
		if (!$this->auth) {
			$template_filename = 'login.php';
		}
		else {
			$template_filename = 'index.php';
			
			// load modules
			$this->modules = new modules();
		}
		
		switch ($this->client) {
			case 'api' :
				$template_path = _ABS_PATH.DS."api";
				break;
			case 'mobile' :
				$template_path = _ABS_PATH.DS.'templates'.DS.$this->config->template.DS.'mobile';
				break;
			default :
				$template_path = _ABS_PATH.DS.'templates'.DS.$this->config->template;
				break;
		}
		
		// Start buffering
		ob_start();
		require_once $template_path.DS.$template_filename;
		// save buffer
		$this->output = ob_get_contents();
		// clean output buffer
		ob_end_clean();
	}
	
	/**
	 * output()
	 * 
	 * Send the application output back to the client. This method should be invoked last, after all processing has been done.
	 * 
	 * @since	1.0
	 * @return	void
	 */
	public function output() {
		echo $this->output;
		
		// Display debug output
		if ($this->config->debug) {
			$this->debug->display();	
		}
		
		// clear errors after displaying
		$this->session->setVar('error', null);
	}
}
?>