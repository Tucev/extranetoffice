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
 * Controller class
 * 
 * This class is used to implement the MVC (Model/View/Controller) architecture 
 * in the components.
 * 
 * As an abstract class it has to be extended to be instantiated. This class is 
 * used as a template for creating controllers when developing components. See 
 * the built in components (dashboard, user, admin, ...) for examples.
 * 
 * Controllers processes requests and respond to events, typically user actions, 
 * and may invoke changes on using the available models.
 * 
 * This class uses the singleton design pattern, and it is therefore instantiated 
 * using the getInstance() method.
 * 
 * To make sure that the child model is instantiated using the correct run time
 * class name we pass the class name when invoking the getInstance() method.
 * 
 * For example:
 * <code>
 * class myController extends controller {
 * 		function doSomething() {
 * 			return 'something';
 * 		}
 * }
 * 
 * $myController =& phpFrame::getInstance('myController');
 * echo $myController->doSomething();
 * </code>
 * This will echo 'something'.
 * 
 * @package		phpFrame
 * @subpackage 	application
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		model, view
 * @abstract 
 */
abstract class controller extends singleton {
	/**
	 * The component (ie: com_projects)
	 * 
	 * @var string
	 */
	var $option=null;
	/**
	 * The task to be executed
	 * 
	 * @var string
	 */
	var $task=null;
	/**
	 * The view to be displayed
	 * 
	 * @var string
	 */
	var $view=null;
	/**
	 * The layout template to be used for rendering
	 * 
	 * @var string
	 */
	var $layout=null;
	/**
	 * Array containing a list of with the available views
	 * 
	 * @var array
	 */
	var $views_available=null;
	/**
	 * The currently loaded view object.
	 *
	 * @var object
	 */
	var $view_obj=null;
	/**
	 * A string containing a url to be redirected to. Leave empty for no redirection.
	 *
	 * @var string
	 */
	var $redirect_url=null;
	/**
	 * This is a flag we use to indicate whether the controller's executed task was successful or not.
	 * 
	 * @var boolean
	 */
	protected $success=false;
	
	/**
	 * Constructor
	 * 
	 * @return 	void
	 * @since	1.0
	 */
	public function __construct() {
		$this->option = request::getVar('option');
		$this->task = request::getVar('task', 'display');
		$this->view = request::getVar('view');
		$this->layout = request::getVar('layout');
		
		// Get available views
		$this->views_available = $this->getAvailableViews();
		
		// Get reference to application
		$application =& factory::getApplication();
		
		// Add pathway item
		$this->addPathwayItem(ucwords($application->component_info->name), 'index.php?option='.$this->option);
		
		// Append component name in ducument title
		$document =& factory::getDocument('html');
		if (!empty($document->title)) $document->title .= ' - ';
		$document->title .= ucwords($application->component_info->name);
	}
	
    /**
     * Display view
     * 
     * This method triggers the view.
     *
     * @return 	void
	 * @since	1.0
     */
	public function display() {
		$this->view_obj = $this->getView(request::getVar('view'));
		if (is_callable(array($this->view_obj, 'display'))) {
			$this->view_obj->display();	
		}
		else {
			error::raise('', 'error', 'display() method not found in view class.');
		}
	}
	
	/**
	 * Execute task
	 * 
	 * This method executes a given task (runs a named member method).
	 *
	 * @param 	string $task The task to be executed (default is 'display').
	 * @return 	void
	 * @since	1.0
	 */
	public function execute($task) {
		// Get reference to application to check permissions before we execute
		$application =& factory::getApplication();
		
		if ($application->permissions->is_allowed === true) {
			if (is_callable(array($this, $task))) {
				$this->$task();	
			}
			else {
				error::raise('', 'error', $task.'() method not found in controller class.');
			}
		}
		else {
			if ($application->auth == false) {
				$this->setRedirect('index.php?option=com_login');
			}
			else {
				error::raise('', 'error', 'Permission denied.');
			}
		}
	}
	
	/**
	 * Cancel
	 * 
	 * Cancel and set redirect to index.
	 *
	 * @return 	void
	 * @since	1.0
	 */
	public function cancel() {
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
	public function setRedirect($url) {
		$this->redirect_url = route::_($url);
	}
	
	/**
	 * Redirect
	 * 
	 * Redirect browser to redirect URL.
	 * @return 	void
	 * @since	1.0
	 */
	public function redirect() {
		if ($this->redirect_url && !client::isCLI()) {
			header("Location: ".$this->redirect_url);
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
	public function getModel($name='') {
		if (empty($name)) {
			$name = request::getVar('view');
		}
		
		$model_path = COMPONENT_PATH.DS."models".DS.$name.".php";
		if (file_exists($model_path)) {
			require_once $model_path;
			$model_class_name = substr(request::getVar('option'), 4).'Model'.ucfirst($name);
			$model =& phpFrame::getInstance($model_class_name);
			return $model;
		}
		else {
			error::raise(500, "error", "Model file ".$model_path." not found.");
			return false;
		}
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
	public function getView($name='') {
		if (empty($name)) {
			$name = request::getVar('view');
		}
		
		$view_path = COMPONENT_PATH.DS."views".DS.$name.DS."view.php";
		if (file_exists($view_path)) {
			require_once $view_path;
			$view_class_name = substr(request::getVar('option'), 4).'View'.ucfirst($name);
			$view =& phpFrame::getInstance($view_class_name);
			return $view;
		}
		else {
			error::raise(500, "error", "View file ".$model_path." not found.");
			return false;
		}
	}
	
	/**
	 * Get available views
	 * 
	 * This method scans the views directory for directories that may contain views 
	 * and returns an array with the directory names. If no view directories found it 
	 * returns false.
	 * 
	 * @return	array
	 * @since	1.0
	 */
	function getAvailableViews() {
		$views_path = COMPONENT_PATH.DS."views";
		$array = scandir($views_path);
		
		if (is_array($array) && count($array) > 0) {
			// Filter out files and directories starting with a "."
			foreach ($array as $item) {
				if (is_dir($views_path.DS.$item) && strpos($item, '.') !== 0) {
					$views_available[] = $item;
				}
			}
			if (is_array($views_available) && count($views_available) > 0) {
				return $views_available;	
			}
			else {
				return false;
			}
			
		}
		else {
			return false;
		}
	}
	
	/**
	 * Add item to pathway
	 * 
	 * @param	string	$title
	 * @param	string	$url
	 * @return	void
	 * @since	1.0
	 */
	function addPathwayItem($title, $url='') {
		$pathway =& factory::getPathway();
		// add item
		$pathway->addItem($title, $url);
	}
	
	public function getSuccess() {
		return $this->success;
	}
}
?>