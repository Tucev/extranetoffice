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
 * Component Class
 *
 * @package		phpFrame
 * @subpackage 	application
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class component extends table {
	var $name=null;
	var $menu_name=null;
	var $author=null;
	var $version=null;
	var $enabled=null;
	var $system=null;
	var $ordering=null;
	
	function __construct() {
		$db = factory::getDB();
		parent::__construct($db, '#__components', 'id');
	}
	
	/**
	 * This methods tests whether the specified component is installed and enabled.
	 *
	 * @access public
	 * @param string $name The component name to check (ie: dashboard, user, projects, ...)
	 * @return bool
	 */
	public static function isEnabled($name) {
		$query = "SELECT enabled FROM #__users WHERE name = '".$name."'";
		$this->db->setQuery($query);
		$enabled = $this->db->getResult();
		if ($enabled == '1') {
			return true;
		}
		else {
			return false;
		}
	}
}
?>