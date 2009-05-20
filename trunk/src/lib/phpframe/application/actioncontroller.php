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
 * Action Controller class
 * 
 * This class is used to implement the MVC (Model/View/Controller) pattern 
 * in the components.
 * 
 * As an abstract class it has to be extended to be instantiated. This class is 
 * used as a template for creating controllers when developing components. See 
 * the built in components (dashboard, user, admin, ...) for examples.
 * 
 * Controllers processes requests and respond to events, typically user actions, 
 * and may invoke changes on data using the available models.
 * 
 * @package		phpFrame
 * @subpackage 	application
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		phpFrame_Application_Model, phpFrame_Application_View
 * @abstract 
 */
abstract class phpFrame_Application_ActionController extends phpFrame_Base_Singleton {
	/**
	 * Default controller action
	 * 
	 * @var	string
	 */
	protected $_default_action=null;
	/**
	 * A string containing a url to be redirected to. Leave empty for no redirection.
	 *
	 * @var string
	 */
	protected $_redirect_url=null;
	/**
	 * A reference to the System Events object.
	 * 
	 * This object is used to report system messages from the action controllers.
	 * 
	 * @var	object
	 */
	protected $_sysevents=null;
	/**
	 * This is a flag we use to indicate whether the controller's executed task was successful or not.
	 * 
	 * @var boolean
	 */
	protected $_success=false;
	
	/**
	 * Constructor
	 * 
	 * @return 	void
	 * @since	1.0
	 */
	protected function __construct($default_action) {
		$this->_default_action = (string) $default_action;
		
		// Get reference to System Events object
		$this->_sysevents = phpFrame::getSysevents();
		
		$components = phpFrame_Base_Singleton::getInstance('phpFrame_Application_Components');
		$this->component_info = $components->loadByOption(phpFrame::getRequest()->getComponentName());
		
		// Add pathway item
		phpFrame::getPathway()->addItem(ucwords($this->component_info->name), 'index.php?component='.$this->component);
		
		// Append component name in ducument title
		$document = phpFrame::getDocument('html');
		if (!empty($document->title)) $document->title .= ' - ';
		$document->title .= ucwords($frontcontroller->component_info->name);
	}
	
	/**
	 * Execute action
	 * 
	 * This method executes a given task (runs a named member method).
	 *
	 * @return 	void
	 * @since	1.0
	 */
	public function execute() {
		// Get action from the request
		$request_action = phpFrame::getRequest()->getAction();
		//echo $request_action; exit;
		// If no specific action has been requested we use default action
		if (empty($request_action)) {
			$action = $this->_default_action;
		}
		else {
			$action = $request_action;
		}
		
		// Check permissions before we execute
		$component = phpFrame::getRequest()->getComponentName();
		$groupid = phpFrame::getSession()->getGroupId();
		$permissions = phpFrame::getPermissions();
		if ($permissions->authorise($component, $action, $groupid) === true) {
			if (is_callable(array($this, $action))) {
				// Start buffering
				ob_start();
				$this->$action();
				// save buffer in response object
				$action_output = ob_get_contents();
				// clean output buffer
				ob_end_clean();
			}
			else {
				throw new phpFrame_Exception("Action ".$action."() not found in controller.");
			}
		}
		else {
			if (!phpFrame::getSession()->isAuth()) {
				$this->setRedirect('index.php?component=com_login');
			}
			else {
				$this->_sysevents->setSummary('Permission denied.');
			}
		}
		
		// Redirect if set by the controller
		$this->redirect();
		
		// Return action's output as string
		return $action_output;
	}
	
	/**
	 * Get controller's success flag
	 * 
	 * @return	boolean
	 * @since	1.0
	 */
	public function getSuccess() {
		return $this->_success;
	}
	
	/**
	 * Cancel
	 * 
	 * Cancel and set redirect to index.
	 *
	 * @return 	void
	 * @since	1.0
	 */
	protected function cancel() {
		$this->setRedirect( 'index.php' );
	}
	
	/**
	 * Set redirection url
	 * 
	 * Set the redirection URL.
	 *
	 * @param string $url
	 * @return 	void
	 * @since	1.0
	 */
	protected function setRedirect($url) {
		$this->_redirect_url = phpFrame_Utils_Rewrite::rewriteURL($url, false);
	}
	
	/**
	 * Redirect
	 * 
	 * Redirect browser to redirect URL.
	 * @return 	void
	 * @since	1.0
	 */
	protected function redirect() {
		if ($this->_redirect_url && phpFrame::getSession()->getClientName() != "cli") {
			header("Location: ".$this->_redirect_url);
			exit;
		}
	}
	
	/**
	 * Get model
	 * 
	 * Gets a named model within the component.
	 *
	 * @param	string	$name The model name. If empty the view name is used as default.
	 * @return	object
	 * @since	1.0
	 */
	protected function getModel($name, $args=array()) {
		return phpFrame::getModel(phpFrame::getRequest()->getComponentName(), $name, $args);
	}
	
	/**
	 * Get view
	 * 
	 * Get a named view within the component.
	 *
	 * @param	string	$name
	 * @return	object
	 * @since	1.0
	 */
	protected function getView($name, $layout='') {
		$class_name = strtolower(substr(phpFrame::getRequest()->getComponentName(), 4));
		$class_name .= "View".ucfirst($name);
		
		try {
			$reflectionObj = new ReflectionClass($class_name);
		}
		catch (Exception $e) {
			throw new phpFrame_Exception($e->getMessage());
		}
		
		if ($reflectionObj->isSubclassOf( new ReflectionClass("phpFrame_Application_View") )) {
			return new $class_name($layout);
		}
		else {
			throw new phpFrame_Exception("View class '".$class_name."' not supported.");
		}
	}
}
