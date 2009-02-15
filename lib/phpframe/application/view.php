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
 * View Class
 * 
 * This class is used to implement the MVC (Model/View/Controller) architecture 
 * in the components.
 * 
 * Views are used to renders the output of a component into a form suitable for 
 * interaction, typically a user interface element. Multiple views can exist for 
 * a single component for different purposes.
 * 
 * This class should be extended when creating component views as it is an 
 * abstract class. This class is used as a template for creating views when 
 * developing components. See the built in components (dashboard, user, admin, ...) 
 * for examples.
 * 
 * This class extends the singleton class, and it is therefore instantiated using 
 * the getInstance() method inherited from singleton.
 * 
 * To make sure that the child view is instantiated using the correct run time
 * class name we pass the class name when invoking the getInstance() method.
 * 
 * For example:
 * <code>
 * class myView extends view {
 * 		function doSomething() {
 * 			return 'something';
 * 		}
 * }
 * 
 * $myView =& myView::getInstance('myView');
 * echo $myView->doSomething();
 * </code>
 * This will echo 'something'.
 * 
 * @package		ExtranetOffice
 * @subpackage 	framework
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see			controller, model
 * @abstract 
 */
abstract class view extends singleton {
	/**
	 * A reference to the global config
	 *
	 * @var object
	 */
	var $config=null;
	/**
	 * The view name
	 * 
	 * @var string
	 */
	var $view=null;
	/**
	 * The template prefix. Default value is "default", other possible values: "mobile", "xml".
	 * 
	 * @var string
	 */
	var $tmpl='default';
	/**
	 * The layout to load. Typical values: "list", "detail", "form", ...
	 * 
	 * @var string
	 */
	var $layout=null;
    
	/**
	 * Constructor
	 * 
	 * @since	1.0
	 * @return	void
	 */
	function __construct() {
		// set view name in view object
    	$this->view =& request::getVar('view');
    	
    	// Assign references to user object for quick access in tmpl
		$this->user =& factory::getUser();
	}
	
    /**
     * This method loads the template layer of the view.
     * 
     * This method will trigger layout specific methods. 
     * For example, if we are displaying layout "list" and there is a method called 
     * displayMyviewList within the extended view class this method will be automatically invoked.
     *
     * @since	1.0
     */
    function display() {
		// If there is a layout specific method we trigger it before including the tmpl file.
		$tmpl_specific_method = "display".ucfirst(request::getVar('view')).ucfirst($this->layout);
		if (method_exists($this, $tmpl_specific_method)) {
			// Invoke layout specific display method
			$this->$tmpl_specific_method();
		}
		
    	if (!empty($this->view)) {
    		$this->loadTemplate();
    	}
    }
    
    /**
     * This method loads a given view template.
     *
     * @param string $layout The name of the view template (ie: "list" will load default_list.php)
     * @todo This method should also load other layouts depending on client (pc, mobile, api)
     */
    function loadTemplate() {
    	if (!empty($this->tmpl)) {
    		$tmpl_path = COMPONENT_PATH.DS."views".DS.$this->view.DS."tmpl".DS.$this->tmpl;
    		if (!empty($this->layout)) {
    			$tmpl_path .= "_".$this->layout;
    		}
    		$tmpl_path .= ".php";
    		if (file_exists($tmpl_path)) {
    			require_once $tmpl_path;
    			return true;
    		}
    		else {
    			error::raise(500, "error", "Layout template file ".$tmpl_path." not found.");
    			return false;
    		}
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
			$name = $this->view;
		}
		
		$model_path = COMPONENT_PATH.DS."models".DS.$name.".php";
		if (file_exists($model_path)) {
			require_once $model_path;
			$model_class_name = substr(request::getVar('option'), 4).'Model'.ucfirst($name);
			eval('$model =& '.$model_class_name.'::getInstance('.$model_class_name.');');
			return $model;
		}
		else {
			error::raise(500, "error", "Model file ".$model_path." not found.");
			return false;
		}
		
		
	}
}
?>