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
	function get($property, $value=null) {
		if (!$this->$property && $value) {
			$this->$property = $value;
		}
		return $this->$property;
	}
	
	function set($property, $value) {
		$this->$property = $value;
	}
	
	function toString() {
		return serialize($this);
	}
}
?>