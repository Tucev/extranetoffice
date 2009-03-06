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
	 * The client program accessing the the application (default, mobile, rss or api)
	 * 
	 * @var string
	 */
	var $client="default";
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
		
		// Get client from request
		if (client::checkMobile() == true) {
			$this->client = 'mobile';	
		}
		
		// get document object
		$this->document =& factory::getDocument('html');
		
		// If client is default (pc web browser) we add the jQuery library + jQuery UI
		if ($this->client == "default") {
			$this->document->addScript('lib/jquery/js/jquery-1.3.2.min.js');
			$this->document->addScript('lib/jquery/js/jquery-ui-1.7.custom.min.js');
			$this->document->addStyleSheet('lib/jquery/css/smoothness/jquery-ui-1.7.custom.css');	
		}
		
		// instantiate db object and store in application
		$this->db =& phpFrame::getInstance('db');
		// connect to MySQL server
		if ($this->db->connect($this->config->db_host, $this->config->db_user, $this->config->db_pass, $this->config->db_name) !== true) {
			error::raiseFatalError($this->db->error);
		}
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
		$this->session =& phpFrame::getInstance('session');
		
		// get user object
		$this->user =& phpFrame::getInstance('user');
		
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
		// Set component option in application
		$this->option =& request::getVar('option', 'com_dashboard');
		// Get component info
		$components =& phpFrame::getInstance('components');
		$this->component_info = $components->loadByOption($this->option);
		
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
		elseif (request::getVar('tmpl') == 'component') {
			$template_filename = 'component.php';
		}
		else {
			$template_filename = 'index.php';
			
			// load modules
			$this->modules =& phpFrame::getInstance('modules');
			
			// get pathway
			$this->pathway =& factory::getPathway();
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