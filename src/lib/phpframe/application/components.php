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
 * Components Class
 *
 * @package		phpFrame
 * @subpackage 	application
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class phpFrame_Application_Components extends phpFrame_Database_Table {
	var $name=null;
	var $menu_name=null;
	var $author=null;
	var $version=null;
	var $enabled=null;
	var $system=null;
	var $ordering=null;
	
	/**
	 * Construct
	 * 
	 * @return	void
	 * @since	1.0
	 */
	function __construct() {
		parent::__construct('#__components', 'id');
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
		$query = "SELECT * FROM #__components WHERE name = '".substr($option, 4)."'";
		$this->db->setQuery($query);
		$row = $this->db->loadObject();
		foreach ($this->cols as $col) {
			$col_name = $col->Field;
			$col_value = $row->$col_name;
			$this->$col_name = $col_value;
		}
		
		return $row;
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