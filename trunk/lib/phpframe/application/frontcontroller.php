<?php
/**
 * @version		$Id$
 * @package		phpFrame_lib
 * @subpackage 	application
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * FrontController Class
 * 
 * This is the FrontController. Its main objective is to initialise the framework 
 * and decide which action controller should be run.
 * 
 * This class is still work in progress.
 * 
 * The class should be instantiated as:
 * 
 * <code>
 * $frontcontroller = phpFrame::getFrontController();
 * </code>
 * 
 * Before we instantiate the FrontController we first need to set a few useful constants,
 * include the autoloader and the config file and then finally 
 * instantiate the FrontController and run it.
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
 * $frontcontroller = phpFrame::getFrontController();
 * $frontcontroller->run();
 * </code>
 * 
 * @package		phpFrame_lib
 * @subpackage 	application
 * @since 		1.0
 * @see			phpFrame
 */
class phpFrame_Application_FrontController {
	/**
	 * Instance of itself in order to implement the singleton pattern
	 * 
	 * @var object of type phpFrame_Application_FrontController
	 */
	private static $_instance=null;
	
	/**
	 * Constructor
	 * 
	 * @access	protected
	 * @return 	void
	 * @since	1.0
	 */
	private function __construct() {
		// Set profiler milestone
		phpFrame_Debug_Profiler::setMilestone('Start');
		
		// Initialise phpFame's error and exception handlers.
		phpFrame_Exception_Handler::init();
		
		// Load language files
		$this->_loadLanguage();
		
		// Set timezone
		date_default_timezone_set(config::TIMEZONE);
		
		// Get/init session object
		$session = phpFrame::getSession();
		
		// Check dependencies
		phpFrame_Application_Dependencies::check($session);
		
		// Rewrite Request URI
		phpFrame_Utils_Rewrite::rewriteRequest();
		
		// Initialise request
		$request = phpFrame::getRequest();
		
		// Give the client a chance to do something before we move on to run
		$client = $session->getClient();
		$client->preActionHook();
		
		// Set profiler milestone
		phpFrame_Debug_Profiler::setMilestone('Front controller constructed');
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
	
	/**
	 * Run
	 * 
	 * This method executes the request and stores the component's output buffer in $this->component_output.
	 * 
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	public function run() {
		$component_name = phpFrame::getRequest()->getComponentName();
		
		// set the component path
		define("COMPONENT_PATH", _ABS_PATH.DS."src".DS."components".DS.$component_name);
		
		// Create the action controller
		$controller = phpFrame::getActionController($component_name);
		// Check that action controller is of valid type and run it if it is
		if ($controller instanceof phpFrame_Application_ActionController) {
			// Execute task
			$output = $controller->execute();
		}
		else {
			throw new phpFrame_Exception("Controller not supported.");
		}
		
		// Set profiler milestone
		phpFrame_Debug_Profiler::setMilestone('Action controller executed');
		
		// Render output using client's template
		$client = phpFrame::getSession()->getClient();
		$client->renderTemplate($output);
		
		// Set profiler milestone
		phpFrame_Debug_Profiler::setMilestone('Overall template rendered');
		
		// Build response and send it
		$response = phpFrame::getResponse();
		$response->setBody($output);
		
		// Set profiler milestone
		phpFrame_Debug_Profiler::setMilestone('Set response');
		
		$response->send();
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
		$lang_file = _ABS_PATH.DS."src".DS."lang".DS.config::DEFAULT_LANG.".php";
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
