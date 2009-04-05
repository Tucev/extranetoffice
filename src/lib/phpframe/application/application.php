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
 * $application =& phpFrame::getInstance('phpFrame_Application');
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
	 * A reference to the pathway object
	 * 
	 * @var object
	 */
	var $pathway=null;
	/**
	 * Document object
	 * 
	 * @var object
	 */
	var $document=null;
	/**
	 * The component option (ie: com_dashboard)
	 * 
	 * @var string
	 */
	var $option=null;
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
		// Initialise debbuger
		phpFrame_Application_Debug::init();
		
		// load the language file
		$lang_file = _ABS_PATH.DS."lang".DS.config::DEFAULT_LANG.".php";
		if (file_exists($lang_file)) {
			require_once $lang_file;
		}
		else {
			phpFrame_Application_Error::raiseFatalError('phpFrame_Application::__construct(): Could not find language file ('.$lang_file.')');
		}
		
		// Initialise request
		// when initialising the request we also detect whether this is a command line request
		phpFrame_Environment_Request::init();
		
		// Get client from request
		// This has to be done after we have initialised the request
		if (phpFrame_Utils_Client::isMobile()) {
			$this->client = 'mobile';
		}
		elseif (phpFrame_Utils_Client::isCLI()) {
			$this->client = 'CLI';
		}
		
		// get document object
		$this->document =& phpFrame_Application_Factory::getDocument('html');
		
		// If client is default (pc web browser) we add the jQuery library + jQuery UI
		if ($this->client == "default") {
			$this->document->addScript('lib/jquery/js/jquery-1.3.2.min.js');
			$this->document->addScript('lib/jquery/js/jquery-ui-1.7.custom.min.js');
			$this->document->addScript('lib/jquery/plugins/validate/jquery.validate.pack.js');
			$this->document->addScript('lib/jquery/plugins/form/jquery.form.pack.js');
			$this->document->addStyleSheet('lib/jquery/css/smoothness/jquery-ui-1.7.custom.css');	
		}
		
		// instantiate db object and store in application
		$db =& phpFrame::getInstance('phpFrame_Database');
		// connect to MySQL server
		if ($db->connect(config::DB_HOST, config::DB_USER, config::DB_PASS, config::DB_NAME) !== true) {
			phpFrame_Application_Error::raiseFatalError($db->getLastError());
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
		$this->session =& phpFrame::getInstance('phpFrame_Environment_Session');
		
		// get user object
		$this->user =& phpFrame::getInstance('phpFrame_User');
		
		if (!empty($this->session->userid)) {
			$this->user->load($this->session->userid);
		}
		elseif ($this->client == 'CLI') {
			$this->user->id = 1;
			$this->user->groupid = 1;
			$this->user->username = 'system';
			$this->user->firstname = 'System';
			$this->user->lastname = 'User';
			// Store user detailt in session
			$this->session->userid = 1;
			$this->session->groupid = 1;
			$this->session->write();
		}
		
		// If user user is not logged on we set auth to false and option to login
		if (!$this->user->id) {
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
		// Set component option in application
		$this->option =& phpFrame_Environment_Request::getVar('option', 'com_dashboard');
		
		// Initialise permissions
		$this->permissions =& phpFrame::getInstance('phpFrame_Application_Permissions');
		
		// Get component info
		$components =& phpFrame::getInstance('phpFrame_Application_Components');
		$this->component_info = $components->loadByOption($this->option);
		
		// load modules before we execute controller task to make modules available to components
		$this->modules =& phpFrame::getInstance('phpFrame_Application_Modules');
		
		// set the component path
		define("COMPONENT_PATH", _ABS_PATH.DS."components".DS.$this->option);
		// Start buffering
		ob_start();
		// Load component file
		require_once COMPONENT_PATH.DS.substr($this->option, 4).'.php';
		// save buffer
		$this->component_output = ob_get_contents();
		// clean output buffer
		ob_end_clean();
	}
	
	public function render() {
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
			$this->pathway =& phpFrame_Application_Factory::getPathway();
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
		$this->session->setVar('error', null);
	}
}
?>