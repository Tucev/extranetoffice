<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	objects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Standard Object Class
 * 
 * This class provides a standard object class with some useful generic methods. 
 * 
 * @package		phpFrame
 * @subpackage 	objects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class standardObject {
	/**
	 * Get property
	 * 
	 * @param	string	$property	The propery name to get
	 * @param	string	$value		Default value if not defined
	 * @return mixed
	 * @since	1.0
	 */
	function get($property, $value=null) {
		if (!$this->$property && $value) {
			$this->$property = $value;
		}
		return $this->$property;
	}
	
	/**
	 * Set property
	 * 
	 * Sets the named property to th given value and returns the new value stored in the property.
	 * 
	 * @param	string	$property 	The name of the property to set.
	 * @param	mixed	$value 		The new value for the property.
	 * @return	mixed
	 * @since	1.0
	 */
	function set($property, $value) {
		$this->$property = $value;
		return $this->$property;
	}
	
	/**
	 * Generates a storable string representation of the object
	 * 
	 * @return	string
	 * @since	1.0
	 */
	function toString() {
		return serialize($this);
	}
}
?>