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
 * This class should be extended when creating component models as it is an 
 * abstract class. See the built in components (dashboard, user, admin, ...) 
 * for examples.
 * 
 * @package		phpFrame
 * @subpackage 	application
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		phpFrame_Application_ActionController, phpFrame_Application_View
 * @abstract 
 */
abstract class phpFrame_Application_Model {
	/**
	 * An array containing strings with internal error messages if any
	 * 
	 * @var array
	 */
	protected $_error=array();
	
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
