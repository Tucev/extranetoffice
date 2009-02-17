<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

class projectsHelperProjects {
	/**
	 * Translate projectid to name
	 * @param 	int		The ID to be translated
	 * @return 	string	If no id is passed returns false, otherwise returns the project name as a string
	 */
	function id2name($id=0) {
		if (!empty($id)) { // No category has been selected
			$db = factory::getDB(); // Instantiate joomla database object
			$query = "SELECT name FROM #__projects WHERE id = '".$id."'";
			$db -> setQuery($query);
			$name = $db->loadResult();
			if ($db->error) {
			  echo $db->error;
			  return false;
			}
			return $name;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Function to build HTML select of projects
	 * @param	int		The selected value if any
	 * @param 	string	Attributes for the <select> tag
	 * @return 	string	A string with the HTML select
	 */
	function select($selected=0, $attribs='') {
		// assemble projects into the array
		$options = array();
		$options[] = html::_('select.option', '0', text::_( '-- Select a Project --' ) );
		
		// get projects from db
		$db = factory::getDB(); // Instantiate joomla database object
		$user =& factory::getUser();
		$query = "SELECT p.id, p.name ";
		$query .= "FROM #__projects AS p ";
		$query .= " WHERE ( p.access = '0' OR (".$user->id." IN (SELECT userid FROM #__users_roles WHERE projectid = p.id) ) )";
		$query .= " ORDER BY p.name ASC";
		
		$db -> setQuery($query);
		$rows = $db->loadObjectList();
		if ($db->error) {
		  echo $db->error;
		  return false;
		}
		
		foreach ($rows as $row) {
			$options[] = html::_('select.option', $row->id, $row->name );
		}
		
		$attribs .= ' class="inputbox"';
		$output = html::_('select.genericlist', $options, 'projectid', $attribs, 'value', 'text', $selected);
		return $output;
	}
	
	function project_typeid2name($id=0) {
		if (!empty($id)) { // No category has been selected
			$db = factory::getDB(); // Instantiate joomla database object
			$query = "SELECT name FROM #__project_types WHERE id = '".$id."'";
			$db -> setQuery($query);
			$name = $db->loadResult();
			if ($db->error) {
			  echo $db->error;
			  return false;
			}
			return $name;
		}
		else {
			return false;
		}
	}
	
	function project_type_select($selected=0, $attribs='') {
		// assemble project types into the array
		$options = array();
		$options[] = html::_('select.option', '0', text::_( '-- Select a Project Type --' ) );
		
		// get project_types from db
		$db = factory::getDB(); // Instantiate joomla database object
		$query = "SELECT id, name FROM #__project_types ";
		$query .= " ORDER BY name";
		$db -> setQuery($query);
		$rows = $db->loadObjectList();
		if ($db->error) {
		  echo $db->error;
		  return false;
		}
		
		foreach ($rows as $row) {
			$options[] = html::_('select.option', $row->id, $row->name );
		}
		
		$attribs .= ' class="inputbox"';
		$output = html::_('select.genericlist', $options, 'project_type', $attribs, 'value', 'text', $selected);
		return $output;
	}
	
	function priorityid2name($priorityid) {
		switch ($priorityid) {
			case '0' :
				return _LANG_PROJECTS_PRIORITY_LOW;
			case '1' :
				return _LANG_PROJECTS_PRIORITY_MEDIUM;
			case '2' :
				return _LANG_PROJECTS_PRIORITY_HIGH;
		}
	}
	
	function priority_select($selected=0, $attribs='') {
		// assemble priorities into the array
		$options = array();
		
		$options[] = html::_('select.option', 0, _LANG_PROJECTS_PRIORITY_LOW );
		$options[] = html::_('select.option', 1, _LANG_PROJECTS_PRIORITY_MEDIUM );
		$options[] = html::_('select.option', 2, _LANG_PROJECTS_PRIORITY_HIGH );
		
		$attribs .= ' class="inputbox"';
		$output = html::_('select.genericlist', $options, 'priority', $attribs, 'value', 'text', $selected);
		return $output;
	}
	
	function global_accessid2name($accessid) {
		switch ($accessid) {
			case '0' :
				return _LANG_PROJECTS_ACCESS_PUBLIC;
			case '1' :
				return _LANG_PROJECTS_ACCESS_PRIVATE;
		}
	}
	
	function global_access_select($fieldname='access', $selected=1, $attribs='') {
		// assemble access into the array
		$options = array();
		//$options[] = html::_('select.option', '', text::_( '-- Select an Access Level --' ) );
		
		$options[] = html::_('select.option', 0, _LANG_PROJECTS_ACCESS_PUBLIC );
		$options[] = html::_('select.option', 1, _LANG_PROJECTS_ACCESS_PRIVATE );
		
		$attribs .= ' class="inputbox"';
		$output = html::_('select.genericlist', $options, $fieldname, $attribs, 'value', 'text', $selected);
		return $output;
	}
	
	function accessid2name($accessid) {
		switch ($accessid) {
			case '1' :
				return _LANG_PROJECTS_ACCESS_ADMINS;
			case '2' :
				return _LANG_PROJECTS_ACCESS_WORKERS;
			case '3' :
				return _LANG_PROJECTS_ACCESS_GUESTS;
			case '4' :
				return _LANG_PROJECTS_ACCESS_PUBLIC;
		}
	}
	
	function access_select($fieldname='access', $selected=0, $attribs='') {
		// assemble access into the array
		$options = array();
		$options[] = html::_('select.option', '0', text::_( '-- Select an Access Level --' ) );
		
		$options[] = html::_('select.option', 1, _LANG_PROJECTS_ACCESS_ADMINS );
		$options[] = html::_('select.option', 2, _LANG_PROJECTS_ACCESS_WORKERS );
		$options[] = html::_('select.option', 3, _LANG_PROJECTS_ACCESS_GUESTS );
		$options[] = html::_('select.option', 4, _LANG_PROJECTS_ACCESS_PUBLIC );
		
		$attribs .= ' class="inputbox"';
		$output = html::_('select.genericlist', $options, $fieldname, $attribs, 'value', 'text', $selected);
		return $output;
	}
	
	function statusid2name($statusid) {
		switch ($statusid) {
			case '0' :
				return _LANG_PROJECTS_STATUS_PLANNING;
			case '1' :
				return _LANG_PROJECTS_STATUS_IN_PROGRESS;
			case '2' :
				return _LANG_PROJECTS_STATUS_PAUSED;
			case '3' :
				return _LANG_PROJECTS_STATUS_FINISHED;
			case '-1' :
				return _LANG_PROJECTS_STATUS_ARCHIVED;
		}
	}
	
	function status_select($selected=0, $attribs='') {
		// assemble access into the array
		$options = array();
		$options[] = html::_('select.option', '0', text::_( '-- Select an Status --' ) );
		
		$options[] = html::_('select.option', '0', _LANG_PROJECTS_STATUS_PLANNING );
		$options[] = html::_('select.option', '1', _LANG_PROJECTS_STATUS_IN_PROGRESS );
		$options[] = html::_('select.option', '2', _LANG_PROJECTS_STATUS_PAUSED );
		$options[] = html::_('select.option', '3', _LANG_PROJECTS_STATUS_FINISHED );
		$options[] = html::_('select.option', '-1', _LANG_PROJECTS_STATUS_ARCHIVED );
		
		$attribs .= ' class="inputbox"';
		$output = html::_('select.genericlist', $options, 'status', $attribs, 'value', 'text', $selected);
		return $output;
	}
	
	function project_roleid2name($id=0) {
		if (!empty($id)) { // No category has been selected
			$db = factory::getDB(); // Instantiate joomla database object
			$query = "SELECT name FROM #__intranetoffice_roles WHERE id = '".$id."'";
			$db -> setQuery($query);
			$name = $db->loadResult();
			if ($db->error) {
			  echo $db->error;
			  return false;
			}
			return $name;
		}
		else {
			return false;
		}
	}
	
	function project_role_select($selected=0, $attribs='') {
		// assemble project types into the array
		$options = array();
		$options[] = html::_('select.option', '0', text::_( '-- Select a Role --' ) );
		
		// get project_types from db
		$db = factory::getDB(); // Instantiate joomla database object
		$query = "SELECT id, name FROM #__intranetoffice_roles ";
		$query .= " ORDER BY id ASC";
		$db -> setQuery($query);
		$rows = $db->loadObjectList();
		if ($db->error) {
		  echo $db->error;
		  return false;
		}
		
		foreach ($rows as $row) {
			$options[] = html::_('select.option', $row->id, $row->name );
		}
		
		$attribs .= ' class="inputbox"';
		$output = html::_('select.genericlist', $options, 'roleid', $attribs, 'value', 'text', $selected);
		return $output;
	}
	
	function issue_typeid2name($id=0) {
		if (!empty($id)) { // No category has been selected
			$db = factory::getDB(); // Instantiate joomla database object
			$query = "SELECT name FROM #__intranetoffice_issue_types WHERE id = '".$id."'";
			$db -> setQuery($query);
			$name = $db->loadResult();
			if ($db->error) {
			  echo $db->error;
			  return false;
			}
			return $name;
		}
		else {
			return false;
		}
	}
	
	function issue_type_select($selected=0, $attribs='') {
		// assemble project types into the array
		$options = array();
		$options[] = html::_('select.option', '0', text::_( '-- Select an issue type (optional) --' ) );
		
		// get project_types from db
		$db = factory::getDB(); // Instantiate joomla database object
		$query = "SELECT id, name FROM #__intranetoffice_issue_types ";
		$query .= " ORDER BY id ASC";
		$db -> setQuery($query);
		$rows = $db->loadObjectList();
		if ($db->error) {
		  echo $db->error;
		  return false;
		}
		
		foreach ($rows as $row) {
			$options[] = html::_('select.option', $row->id, $row->name );
		}
		
		$attribs .= ' class="inputbox"';
		$output = html::_('select.genericlist', $options, 'issue_type', $attribs, 'value', 'text', $selected);
		return $output;
	}
	
	function fileid2name($id=0) {
		if (!empty($id)) { // No file has been selected
			$db = factory::getDB(); // Instantiate joomla database object
			$query = "SELECT title FROM #__intranetoffice_files WHERE id = '".$id."'";
			$db -> setQuery($query);
			$name = $db->loadResult();
			if ($db->error) {
			  echo $db->error;
			  return false;
			}
			return $name;
		}
		else {
			return false;
		}
	}
	
	function activitylog_type2printable($type) {
		switch ($type) {
			case 'issues' :
				return _LANG_ISSUE;
			case 'messages' :
				return _LANG_MESSAGE;
			case 'milestones' :
				return _LANG_MILESTONE;
			case 'files' :
				return _LANG_FILE;
			case 'meetings' :
				return _LANG_MEETING;
			case 'comments' :
				return _LANG_COMMENTS;
		}
	}
	
	function mimetype2icon($mimetype) {
		switch ($mimetype) {
			case 'image/jpg' :
			case 'image/jpeg' :
			case 'image/png' :
			case 'image/gif' :
				return 'image.png';
				break;
				
			case 'application/pdf' :
				return 'pdf.png';
				break;
				
			case 'application/msword' :
			case 'application/vnd.oasis.opendocument.text' :
				return 'writer.png';
				break;
				
			default :
				return 'x.png';
				break;
		}
	}
}
?>