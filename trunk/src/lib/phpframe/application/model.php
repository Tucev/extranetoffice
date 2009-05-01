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
 * Model Class
 * 
 * This class is used to implement the MVC (Model/View/Controller) architecture 
 * in the components.
 * 
 * Models are implemented to represent information on which the application operates.
 * In most cases many models will be created for each component in order to represent
 * the different logical elements.
 * 
 * This class should be extended when creating component views as it is an 
 * abstract class. This class is used as a template for creating views when 
 * developing components. See the built in components (dashboard, user, admin, ...) 
 * for examples.
 * 
 * This class extends the phpFrame_Base_Singleton class, and it is therefore instantiated using 
 * the getInstance() method inherited from phpFrame_Base_Singleton.
 * 
 * To make sure that the child model is instantiated using the correct run time
 * class name we pass the class name when invoking the getInstance() method.
 * 
 * For example:
 * <code>
 * class myModel phpFrame_Application_Model {
 * 		function doSomething() {
 * 			return 'something';
 * 		}
 * }
 * 
 * $myModel =& phpFrame_Base_Singleton::getInstance('myModel');
 * echo $myModel->doSomething();
 * </code>
 * This will echo 'something'.
 * 
 * @package		phpFrame
 * @subpackage 	application
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		phpFrame_Application_ActionController, phpFrame_Application_View
 * @abstract 
 */
abstract class phpFrame_Application_Model extends phpFrame_Base_Singleton {
	/**
	 * A reference to the user object
	 * 
	 * @var object
	 */
	protected $_user=null;
	/**
	 * A reference to the database object
	 * 
	 * @var object
	 */
	protected $_db=null;
	/**
	 * An array containing strings with internal error messages if any
	 * 
	 * @var array
	 */
	protected $_error=array();
	
	/**
	 * Constructor
	 * 
	 * @return void
	 */
	public function __construct() {
		$this->_user = phpFrame::getUser();
		$this->_db = phpFrame::getDB();
	}
	
	/**
	 * Get a named model
	 * 
	 * @param	string	$name
	 * @return	object
	 */
	protected function getModel($name) {
		// Get current component option from request
		$option = phpFrame_Environment_Request::getComponent();
		// Figure out controller instance name
		$controller_class_name = substr($option, 4).'Controller';
		// Assign reference to controller
		$controller =& phpFrame_Base_Singleton::getInstance($controller_class_name);
		// Get model using controller's method
		$model = $controller->getModel($name);
		
		return $model;
	}
	
	/**
	 * Get table class
	 * 
	 * @param	string	$table_name
	 * @return	object
	 */
	protected function getTable($table_name) {
		return phpFrame::getTable(phpFrame_Environment_Request::getComponent(), $table_name);
	}
	
	/**
	 * Get last error in model
	 * 
	 * This method returns a string with the error message or FALSE if no errors.
	 * 
	 * @return mixed
	 */
	public function getLastError() {
		if (is_array($this->_error) && count($this->_error) > 0) {
			return end($this->_error);
		}
		else {
			return false;
		}
	}
}
?>