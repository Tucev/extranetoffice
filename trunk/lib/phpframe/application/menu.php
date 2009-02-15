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

class menu {
	var $db=null;
	
	function __construct() {
		$this->db =& factory::getDB();
	}
	
	function display() {
		$option = request::getVar('option', 'com_dashboard');
		$active_component = substr($option, 4);
		
		echo '<ul id="menu">';
		
		echo '<li';
			if ($active_component == 'dashboard') echo ' class="selected"';
			echo '>';
			echo '<a href="index.php?option=com_dashboard">Dashboard</a>';
			echo '</li>';	
		
		// get non-system components
		$components = $this->getComponents();
		
		foreach ($components as $component) {
			echo '<li';
			if ($component->name == $active_component) echo ' class="selected"';
			echo '>';
			echo '<a href="index.php?option=com_'.$component->name.'">'.$component->menu_name.'</a>';
			echo '</li>';	
		}
		
		echo '</ul>';
	}
	
	function getComponents() {
		$query = "SELECT * FROM #__components WHERE system = '0' ORDER BY ordering ASC";
		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}
}
?>