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
 * @package		phpFrame
 * @subpackage 	application
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see			phpFrame
 */
class phpFrame_Application_FrontController extends phpFrame_Base_Singleton {
	/**
	 * Constructor
	 * 
	 * @access	protected
	 * @return 	void
	 * @since	1.0
	 */
	protected function __construct() {
		// Set profiler milestone
		phpFrame_Debug_Profiler::setMilestone('Start');
		
		// Initialise phpFame's error and exception handlers.
		phpFrame_Exception_Handler::init();
		
		// Load language files
		$this->_loadLanguage();
		
		// Set timezone
		date_default_timezone_set(config::TIMEZONE);
		
		// Check dependencies
		phpFrame_Application_Dependencies::check();
		
		// Initialise request
		phpFrame_Environment_Request::init();
		
		// get session object
		$session = phpFrame::getSession();
		if ($session->isAuth()) {
			$user = phpFrame::getUser();
			$user->load($session->getUserId());
		}
		
		// Set profiler milestone
		phpFrame_Debug_Profiler::setMilestone('Front controller constructed');
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
		// set the component path
		define("COMPONENT_PATH", _ABS_PATH.DS."components".DS.phpFrame_Environment_Request::getComponentName());
		
		$client = phpFrame_Environment_Request::getClient();
		$client->preActionHook();
		
		// Set profiler milestone
		phpFrame_Debug_Profiler::setMilestone('Delegate to action controller');
		
		// Create the action controller
		$controller = phpFrame::getActionController(phpFrame_Environment_Request::getComponentName());
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
