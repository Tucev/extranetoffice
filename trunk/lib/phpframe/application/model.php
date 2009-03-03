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
 * This class extends the singleton class, and it is therefore instantiated using 
 * the getInstance() method inherited from singleton.
 * 
 * To make sure that the child model is instantiated using the correct run time
 * class name we pass the class name when invoking the getInstance() method.
 * 
 * For example:
 * <code>
 * class myModel extends model {
 * 		function doSomething() {
 * 			return 'something';
 * 		}
 * }
 * 
 * $myModel =& phpFrame::getInstance('myModel');
 * echo $myModel->doSomething();
 * </code>
 * This will echo 'something'.
 * 
 * @package		phpFrame
 * @subpackage 	application
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		controller, view
 * @abstract 
 */
abstract class model extends singleton {
	/**
	 * A reference to the config object
	 * 
	 * @var object
	 */
	var $config=null;
	/**
	 * A reference to the user object
	 * 
	 * @var object
	 */
	var $user=null;
	/**
	 * A reference to the database object
	 * 
	 * @var object
	 */
	var $db=null;
	
	/**
	 * Constructor
	 * 
	 * @return void
	 */
	function __construct() {
		$this->config =& factory::getConfig();
		$this->user =& factory::getUser();
		$this->db =& factory::getDB();
	}
	
	/**
	 * Get a named model
	 * 
	 * @param	string	$name
	 * @return	object
	 */
	function getModel($name) {
		// Get current component option from request
		$option =& request::getVar('option');
		// Figure out controller instance name
		$controller_class_name = substr($option, 4).'Controller';
		// Assign reference to controller
		$controller =& phpFrame::getInstance($controller_class_name);
		// Get model using controller's method
		$model =& $controller->getModel($name);
		
		return $model;
	}
}
?>