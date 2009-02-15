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
 * $myController =& myController::getInstance('myController');
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
	 * The currently loaded view object.
	 *
	 * @var object
	 */
	var $view=null;
	/**
	 * A string containing a url to be redirected to. Leave empty for no redirection.
	 *
	 * @var unknown_type
	 */
	var $redirect_url=null;
	
    /**
     * This method triggers the view.
     *
     */
	public function display() {
		$this->view = $this->getView(request::getVar('view'));
		$this->view->display();
	}
	
	/**
	 * This method executes a given task (runs a named member method).
	 *
	 * @param string $task The task to be executed (default is 'display').
	 */
	public function execute($task) {
		eval('$this->'.$task.'();');
	}
	
	/**
	 * Cancel and set redirect to index.
	 *
	 */
	public function cancel() {
		$this->setRedirect( 'index.php' );
	}
	
	/**
	 * Set the redirection URL.
	 *
	 * @param string $url
	 */
	public function setRedirect($url) {
		$this->redirect_url = $url;
	}
	
	/**
	 * Redirect browser to redirect URL.
	 *
	 */
	public function redirect() {
		if ($this->redirect_url) {
			header("Location: ".$this->redirect_url);
			exit;
		}
	}
	
	/**
	 * Gets a named model within the component.
	 *
	 * @param string $name The model name. If empty the view name is used as default.
	 * @return object
	 */
	public function getModel($name='') {
		if (empty($name)) {
			$name = request::getVar('view');
		}
		require_once COMPONENT_PATH.DS."models".DS.$name.".php";
		$model_class_name = substr(request::getVar('option'), 4).'Model'.ucfirst($name);
		eval('$model =& '.$model_class_name.'::getInstance('.$model_class_name.');');
		return $model;
	}
	
	/**
	 * Get a named view within the component.
	 *
	 * @param string $name
	 * @return object
	 */
	public function getView($name) {
		require_once COMPONENT_PATH.DS."views".DS.$name.DS."view.php";
		$view_class_name = substr(request::getVar('option'), 4).'View'.ucfirst($name);
		eval('$view =& '.$view_class_name.'::getInstance('.$view_class_name.');');
		return $view;
	}
}
?>