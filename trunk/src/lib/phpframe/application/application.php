<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	application
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Application Class
 * 
 * This is the mainframe application class.
 * 
 * This class extends the "phpFrame_Base_Singleton" class in order to implement the phpFrame_Base_Singleton design pattern.
 * 
 * The class should be instantiated as:
 * 
 * <code>
 * $application = phpFrame::getApplication();
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
 * // Include autoloader
 * require_once _ABS_PATH.DS."inc".DS."autoload.php";
 * 
 * $application = phpFrame::getApplication();
 * $application->auth();
 * $application->exec();
 * $application->render();
 * $application->output();
 * </code>
 * 
 * @package		phpFrame
 * @subpackage 	application
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see			phpFrame
 */
class phpFrame_Application extends phpFrame_Base_Singleton {
	/**
	 * The client program accessing the the application (default, mobile, rss or api)
	 * 
	 * @var string
	 */
	var $client="default";
	/**
	 * A boolean representing whether a session is authenticated
	 *
	 * @var bool
	 */
	var $auth=null;
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
	 * A reference to the pathway object
	 * 
	 * @var object
	 */
	var $pathway=null;
	/**
	 * The component info (data stored in components table)
	 * 
	 * @var object
	 */
	var $component_info=null;
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
	 * @access	protected
	 * @return 	void
	 * @since	1.0
	 */
	protected function __construct() {
		// Initialise phpFame's error and exception handlers.
		phpFrame_Exception_Handler::init();
		
		// Initialise debbuger
		phpFrame_Application_Debug::init();
		
		// Load language files
		$this->_loadLanguage();
		
		// Set timezone
		date_default_timezone_set(config::TIMEZONE);
		
		// Instantiate database object
		$db = phpFrame::getDB();
		
		// Check dependencies. This has to be done after connecting to the database, 
		// otherwise we can not check MySQL version.
		phpFrame_Application_Dependencies::check();
		
		// Initialise request
		phpFrame_Environment_Request::init();
		
		// Get client from request
		// This has to be done after we have initialised the request
		if (phpFrame_Utils_Client::isMobile()) {
			$this->client = 'mobile';
		}
		elseif (phpFrame_Utils_Client::isCLI()) {
			$this->client = 'CLI';
		}
	}
	
	/**
	 * Authenticate
	 * 
	 * This method should be invoked after instantiating the application class.
	 * It instantiates the session and user objects and returns a boolean depending 
	 * on whether the current user has been authenticated.
	 * 
	 * @access	public
	 * @return	bool
	 * @since	1.0
	 */
	public function auth() {
		// get session object (singeton)
		$session = phpFrame::getSession();
		// get user object
		$user = phpFrame::getUser();
		
		if (!empty($session->userid)) {
			$user->load($session->userid);
		}
		elseif ($this->client == 'CLI') {
			$user->id = 1;
			$user->groupid = 1;
			$user->username = 'system';
			$user->firstname = 'System';
			$user->lastname = 'User';
			// Store user detailt in session
			$session->userid = 1;
			$session->groupid = 1;
			$session->write();
		}
		
		// If user user is not logged on we set auth to false and option to login
		if (!$user->id) {
			$this->auth = false;
		}
		else {
			$this->auth = true;
		}
		
		return $this->auth;
	}
	
	/**
	 * Execute component
	 * 
	 * This method executes the request and stores the component's output buffer in $this->component_output.
	 * 
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	public function exec() {
		// Get component option from request
		$option = phpFrame_Environment_Request::getVar('option', 'com_dashboard');
		
		// Initialise permissions
		$this->permissions = phpFrame::getPermissions();
		
		// Get component info
		$components = phpFrame_Base_Singleton::getInstance('phpFrame_Application_Components');
		$this->component_info = $components->loadByOption($option);
		
		// load modules before we execute controller task to make modules available to components
		$this->modules = phpFrame_Base_Singleton::getInstance('phpFrame_Application_Modules');
		
		// set the component path
		define("COMPONENT_PATH", _ABS_PATH.DS."components".DS.$option);
		// Start buffering
		ob_start();
		// Create the controller
		$controller = phpFrame::getController($option);
		// Execute task
		$controller->execute(phpFrame_Environment_Request::getVar('task'));	
		// Redirect if set by the controller
		$controller->redirect();
		// save buffer
		$this->component_output = ob_get_contents();
		// clean output buffer
		ob_end_clean();
	}
	
	/**
	 * Render output in template
	 * 
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	public function render() {
		// If client is default (pc web browser) we add the jQuery library + jQuery UI
		if ($this->client == "default") {
			$document = phpFrame::getDocument('html');
			$document->addScript('lib/jquery/js/jquery-1.3.2.min.js');
			$document->addScript('lib/jquery/js/jquery-ui-1.7.custom.min.js');
			$document->addScript('lib/jquery/plugins/validate/jquery.validate.pack.js');
			$document->addScript('lib/jquery/plugins/form/jquery.form.pack.js');
			$document->addStyleSheet('lib/jquery/css/smoothness/jquery-ui-1.7.custom.css');	
		}
		
		if (!$this->auth) {
			$template_filename = 'login.php';
		}
		elseif (phpFrame_Environment_Request::getVar('tmpl') == 'component' || $this->client == 'CLI') {
			phpFrame_Application_Error::display();
			$this->output = $this->component_output;
			return;
		}
		else {
			$template_filename = 'index.php';
			
			// get pathway
			$this->pathway = phpFrame::getPathway();
		}
		
		switch ($this->client) {
			case 'api' :
				$template_path = _ABS_PATH.DS."api";
				break;
			case 'mobile' :
				$template_path = _ABS_PATH.DS.'templates'.DS.config::TEMPLATE.DS.'mobile';
				break;
			default :
				$template_path = _ABS_PATH.DS.'templates'.DS.config::TEMPLATE;
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
	 * Send output
	 * 
	 * Send the application output back to the client. This method should be invoked last, after all processing has been done.
	 * 
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	public function output() {
		echo $this->output;
		
		// Display debug output
		if (config::DEBUG) {
			phpFrame_Application_Debug::display();	
		}
		
		// clear errors after displaying
		$session = phpFrame::getSession();
		$session->setVar('error', null);
	}
	
	/**
	 * Load language files
	 * 
	 * @access	private
	 * @return	void
	 * @since	1.0
	 */
	private function _loadLanguage() {
		// load the application language file
		$lang_file = _ABS_PATH.DS."lang".DS.config::DEFAULT_LANG.".php";
		if (file_exists($lang_file)) {
			require_once $lang_file;
		}
		else {
			throw new phpFrame_Exception('Could not find language file ('.$lang_file.')');
		}
		
		// Include the phpFrame lib language file
		$lang_file = _ABS_PATH.DS."lib".DS."phpframe".DS."lang".DS.config::DEFAULT_LANG.".php";
		if (file_exists($lang_file)) {
			require_once $lang_file;
		}
		else {
			throw new phpFrame_Exception('Could not find language file ('.$lang_file.')');
		}
	}
}
?>