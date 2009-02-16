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
	 * The currently loaded view object.
	 *
	 * @var object
	 */
	var $view_obj=null;
	/**
	 * A string containing a url to be redirected to. Leave empty for no redirection.
	 *
	 * @var unknown_type
	 */
	var $redirect_url=null;
	
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
		
		// Check permissions
		$this->permissions = new permissions();
	}
	
    /**
     * display()
     * 
     * This method triggers the view.
     *
     * @return 	void
	 * @since	1.0
     */
	public function display() {
		if ($this->permissions->is_allowed === true) {
			$this->view_obj = $this->getView(request::getVar('view'));
			if ($this->view_obj !== false) {
				$this->view_obj->display();	
			}
		}
		else {
			error::raise('', 'error', 'Permission denied.');
		}
	}
	
	/**
	 * execute()
	 * 
	 * This method executes a given task (runs a named member method).
	 *
	 * @param 	string $task The task to be executed (default is 'display').
	 * @return 	void
	 * @since	1.0
	 */
	public function execute($task) {
		eval('$this->'.$task.'();');
	}
	
	/**
	 * cancel()
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
	 * setRedirect()
	 * 
	 * Set the redirection URL.
	 *
	 * @param string $url
	 * @return 	void
	 * @since	1.0
	 */
	public function setRedirect($url) {
		$this->redirect_url = $url;
	}
	
	/**
	 * redirect()
	 * 
	 * Redirect browser to redirect URL.
	 * @return 	void
	 * @since	1.0
	 */
	public function redirect() {
		if ($this->redirect_url) {
			header("Location: ".$this->redirect_url);
			exit;
		}
	}
	
	/**
	 * getModel()
	 * 
	 * Gets a named model within the component.
	 *
	 * @param string $name The model name. If empty the view name is used as default.
	 * @return object
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
			eval('$model =& phpFrame::getInstance('.$model_class_name.');');
			return $model;
		}
		else {
			error::raise(500, "error", "Model file ".$model_path." not found.");
			return false;
		}
	}
	
	/**
	 * getView()
	 * 
	 * Get a named view within the component.
	 *
	 * @param string $name
	 * @return object
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
			eval('$view =& phpFrame::getInstance('.$view_class_name.');');
			return $view;
		}
		else {
			error::raise(500, "error", "Model file ".$model_path." not found.");
			return false;
		}
	}
}
?>