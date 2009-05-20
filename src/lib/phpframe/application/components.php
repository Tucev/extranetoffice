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
 * Components Class
 *
 * @package		phpFrame
 * @subpackage 	application
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class phpFrame_Application_Components {
	/**
	 * Array containing the installed components
	 * 
	 * @var array
	 */
	private $_array=array();
	
	/**
	 * Construct
	 * 
	 * @return	void
	 * @since	1.0
	 */
	function __construct() {
		$query = "SELECT * FROM #__components ";
		$this->_array = phpFrame::getDB()->setQuery($query)->loadObjectList();
	}
	
	/**
	 * Load component by option (ie: com_dashboard)
	 * 
	 * it loads properties from database table and returns the row object.
	 * 
	 * @param	string	$option The option string.
	 * @return	object
	 */
	public function loadByOption($option) {
		foreach ($this->_array as $component) {
			if ($component->name == substr($option, 4)) {
				return $component;
			}
		}
		
		return null;
	}
	
	/**
	 * This methods tests whether the specified component is installed and enabled.
	 *
	 * @access public
	 * @param string $name The component name to check (ie: dashboard, user, projects, ...)
	 * @return bool
	 */
	public static function isEnabled($name) {
		foreach ($this->_array as $component) {
			if ($component->name == substr($name, 4) && $component->enabled == 1) {
				return true;
			}
		}
		
		return false;
	}
}
