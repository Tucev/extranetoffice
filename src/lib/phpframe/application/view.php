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
 * This class extends the phpFrame_Base_Singleton class, and it is therefore instantiated using 
 * the getInstance() method inherited from phpFrame_Base_Singleton.
 * 
 * To make sure that the child view is instantiated using the correct run time
 * class name we pass the class name when invoking the getInstance() method.
 * 
 * For example:
 * <code>
 * class myView extends phpFrame_Application_View {
 * 		function doSomething() {
 * 			return 'something';
 * 		}
 * }
 * 
 * $myView =& phpFrame_Base_Singleton::getInstance('myView');
 * echo $myView->doSomething();
 * </code>
 * This will echo 'something'.
 * 
 * @package		phpFrame
 * @subpackage 	application
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see			phpFrame_Application_ActionController, phpFrame_Application_Model
 * @abstract 
 */
abstract class phpFrame_Application_View extends phpFrame_Base_Singleton {
	/**
	 * The view name
	 * 
	 * @var string
	 */
	var $view=null;
	/**
	 * A reference to the user object
	 * 
	 * @var object
	 */
	var $user=null;
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
    	$this->view = phpFrame_Environment_Request::getView();
    	
    	// Assign references to user object for quick access in tmpl
		$this->_user = phpFrame::getUser();
	}
	
    /**
     * Display the view
     * 
     * This method loads the template layer of the view.
     * 
     * This method  also trigger layout specific methods. 
     * For example, if we are displaying layout "list" and there is a method called 
     * displayMyviewList within the extended view class this method will be automatically invoked.
     * 
     * Method name to be triggered will be formed as follows:
     * <code>
     * $tmpl_specific_method = "display".ucfirst(phpFrame_Environment_Request::getView()).ucfirst($this->tmpl);
     * </code>
     *
     * @since	1.0
     */
    function display() {
		// If there is a layout specific method we trigger it before including the tmpl file.
    	$layout_array = explode('_', $this->layout);
    	$layout = '';
    	for ($i=0; $i<count($layout_array); $i++) {
    		$layout .= ucfirst($layout_array[$i]);
    	}
		$tmpl_specific_method = "display".ucfirst(phpFrame_Environment_Request::getView()).ucfirst($layout);
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
     * @param	string $layout The name of the view template (ie: "list" will load default_list.php)
     * @since	1.0
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
    			phpFrame_Application_Error::raise(500, "error", "Layout template file ".$tmpl_path." not found.");
    			return false;
    		}
    	}
    }
    
	/**
	 * Gets a named model within the component.
	 *
	 * @param	string $name The model name. If empty the view name is used as default.
	 * @return	object
	 * @since	1.0
	 */
	public function getModel($name='') {
		if (empty($name)) {
			$name = $this->view;
		}
		
		$model_class_name = substr(phpFrame_Environment_Request::getComponent(), 4).'Model'.ucfirst($name);
		$model =& phpFrame_Base_Singleton::getInstance($model_class_name);
		return $model;
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
		$pathway = phpFrame::getPathway();
		// add item
		$pathway->addItem($title, $url);
	}
}
?>