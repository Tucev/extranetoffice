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
 * Modules Class
 * 
 * @package		phpFrame
 * @subpackage 	application
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class modules extends table {
	var $name=null;
	var $author=null;
	var $version=null;
	var $enabled=null;
	var $system=null;
	var $position=null;
	var $ordering=null;
	
	/**
	 * Constructor
	 * 
	 * @return void
	 */
	function __construct() {
		$db = factory::getDB();
		parent::__construct($db, '#__modules', 'id');
	}
	
	function countModules($position) {
		$db = factory::getDB();
		$query = "SELECT m.name AS name, mo.option AS `option` FROM #__modules AS m ";
		$query .= " LEFT JOIN #__modules_options mo ON mo.moduleid = m.id ";
		$query .= " WHERE m.position = '".$position."' AND m.enabled = '1' AND (mo.option ='".request::getVar('option')."' OR mo.option = '*') ";
		$query .= " ORDER BY m.ordering ASC";
		$db->setQuery($query);
		$db->query();
		$count = (int) $db->getNumRows();
		
		if (!$count) {
			return 0;
		}
		else {
			return $count;
		}
	}
	
	/**
	 * display()
	 * 
	 * This method displays modules assigned to a named position depending on 
	 * whether the are also assigned to the current component.
	 * 
	 * @param $position
	 * @param $class_suffix
	 * @return mixed
	 */
	function display($position, $class_suffix='') {
		$db = factory::getDB();
		$query = "SELECT m.name AS name, mo.option AS `option` FROM #__modules AS m ";
		$query .= " LEFT JOIN #__modules_options mo ON mo.moduleid = m.id ";
		$query .= " WHERE m.position = '".$position."' AND m.enabled = '1' AND (mo.option ='".request::getVar('option')."' OR mo.option = '*') ";
		$query .= " ORDER BY m.ordering ASC";
		$db->setQuery($query);
		$modules = $db->loadObjectList();
		
		if (is_array($modules) && count($modules) > 0) {
			foreach ($modules as $module) {
				$module_file_path = _ABS_PATH.DS.'modules'.DS.'mod_'.$module->name.DS.'mod_'.$module->name.'.php';
				if (file_exists($module_file_path)) {
					// Start buffering
					ob_start();
					require_once $module_file_path;
					// save buffer
					$output[] = ob_get_contents();
					// clean output buffer
					ob_end_clean();
				}
				else {
					error::raise('', 'error', 'Module file '.$module_file_path.' not found.');
				}
			}
			
			// prepare html output and filter out empty modules
			$html = '';
			for ($i=0; $i<count($output); $i++) {
				$output[$i] = trim($output[$i]);
				if (!empty($output[$i])) {
					$html .= '<div class="module'.$class_suffix.'">';
					$html .= $output[$i];
					$html .= '</div>';
				}
			}
			
			return $html;
		}
		else {
			return false;
		}
	}
}
?>