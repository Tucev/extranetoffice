<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

class projectsHelperProjects {
	/**
	 * Translate projectid to name
	 * 
	 * @param 	int		The ID to be translated
	 * @return 	string	If no id is passed returns false, otherwise returns the project name as a string
	 */
	static function id2name($id=0) {
		if (!empty($id)) { // No category has been selected
			$db =& phpFrame::getDB();
			$query = "SELECT name FROM #__projects WHERE id = '".$id."'";
			$db->setQuery($query);
			return $db->loadResult();
		}
		else {
			return false;
		}
	}
	
	/**
	 * Function to build HTML select of projects
	 * 
	 * @param	int		The selected value if any
	 * @param 	string	Attributes for the <select> tag
	 * @return 	string	A string with the HTML select
	 */
	static function select($selected=0, $attribs='') {
		// assemble projects into the array
		$options = array();
		$options[] = phpFrame_HTML::_('select.option', '0', phpFrame_HTML_Text::_( '-- Select a Project --' ) );
		
		// get projects from db
		$db =& phpFrame::getDB();
		$user =& phpFrame::getUser();
		$query = "SELECT p.id, p.name ";
		$query .= "FROM #__projects AS p ";
		$query .= " WHERE ( p.access = '0' OR (".$user->id." IN (SELECT userid FROM #__users_roles WHERE projectid = p.id) ) )";
		$query .= " ORDER BY p.name ASC";
		
		$db -> setQuery($query);
		$rows = $db->loadObjectList();
		
		foreach ($rows as $row) {
			$options[] = phpFrame_HTML::_('select.option', $row->id, $row->name );
		}
		
		$output = phpFrame_HTML::_('select.genericlist', $options, 'projectid', $attribs, $selected);
		return $output;
	}
	
	static function project_typeid2name($id=0) {
		if (!empty($id)) { // No category has been selected
			$db =& phpFrame::getDB();
			$query = "SELECT name FROM #__project_types WHERE id = '".$id."'";
			$db -> setQuery($query);
			return $db->loadResult();
		}
		else {
			return false;
		}
	}
	
	static function project_type_select($selected=0, $attribs='') {
		// assemble project types into the array
		$options = array();
		$options[] = phpFrame_HTML::_('select.option', '0', phpFrame_HTML_Text::_( '-- Select a Project Type --' ) );
		
		// get project_types from db
		$db =& phpFrame::getDB();
		$query = "SELECT id, name FROM #__project_types ";
		$query .= " ORDER BY name";
		$db -> setQuery($query);
		$rows = $db->loadObjectList();
		
		foreach ($rows as $row) {
			$options[] = phpFrame_HTML::_('select.option', $row->id, $row->name );
		}
		
		$output = phpFrame_HTML::_('select.genericlist', $options, 'project_type', $attribs, $selected);
		return $output;
	}
	
	static function priorityid2name($priorityid) {
		switch ($priorityid) {
			case '0' :
				return _LANG_PROJECTS_PRIORITY_LOW;
			case '1' :
				return _LANG_PROJECTS_PRIORITY_MEDIUM;
			case '2' :
				return _LANG_PROJECTS_PRIORITY_HIGH;
		}
	}
	
	static function priority_select($selected=0, $attribs='') {
		// assemble priorities into the array
		$options = array();
		
		$options[] = phpFrame_HTML::_('select.option', 0, _LANG_PROJECTS_PRIORITY_LOW );
		$options[] = phpFrame_HTML::_('select.option', 1, _LANG_PROJECTS_PRIORITY_MEDIUM );
		$options[] = phpFrame_HTML::_('select.option', 2, _LANG_PROJECTS_PRIORITY_HIGH );

		$output = phpFrame_HTML::_('select.genericlist', $options, 'priority', $attribs, $selected);
		return $output;
	}
	
	static function global_accessid2name($accessid) {
		switch ($accessid) {
			case '0' :
				return _LANG_PROJECTS_ACCESS_PUBLIC;
			case '1' :
				return _LANG_PROJECTS_ACCESS_PRIVATE;
		}
	}
	
	static function global_access_select($fieldname='access', $selected=1, $attribs='') {
		// assemble access into the array
		$options = array();
		//$options[] = phpFrame_HTML::_('select.option', '', phpFrame_HTML_Text::_( '-- Select an Access Level --' ) );
		
		$options[] = phpFrame_HTML::_('select.option', 0, _LANG_PROJECTS_ACCESS_PUBLIC );
		$options[] = phpFrame_HTML::_('select.option', 1, _LANG_PROJECTS_ACCESS_PRIVATE );
		
		$output = phpFrame_HTML::_('select.genericlist', $options, $fieldname, $attribs, $selected);
		return $output;
	}
	
	static function accessid2name($accessid) {
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
	
	static function access_select($fieldname='access', $selected=0, $attribs='') {
		// assemble access into the array
		$options = array();
		$options[] = phpFrame_HTML::_('select.option', '0', phpFrame_HTML_Text::_( '-- Select an Access Level --' ) );
		
		$options[] = phpFrame_HTML::_('select.option', 1, _LANG_PROJECTS_ACCESS_ADMINS );
		$options[] = phpFrame_HTML::_('select.option', 2, _LANG_PROJECTS_ACCESS_WORKERS );
		$options[] = phpFrame_HTML::_('select.option', 3, _LANG_PROJECTS_ACCESS_GUESTS );
		$options[] = phpFrame_HTML::_('select.option', 4, _LANG_PROJECTS_ACCESS_PUBLIC );
		
		$output = phpFrame_HTML::_('select.genericlist', $options, $fieldname, $attribs, $selected);
		return $output;
	}
	
	static function statusid2name($statusid) {
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
	
	static function status_select($selected=0, $attribs='') {
		// assemble access into the array
		$options = array();
		$options[] = phpFrame_HTML::_('select.option', '0', phpFrame_HTML_Text::_( '-- Select an Status --' ) );
		
		$options[] = phpFrame_HTML::_('select.option', '0', _LANG_PROJECTS_STATUS_PLANNING );
		$options[] = phpFrame_HTML::_('select.option', '1', _LANG_PROJECTS_STATUS_IN_PROGRESS );
		$options[] = phpFrame_HTML::_('select.option', '2', _LANG_PROJECTS_STATUS_PAUSED );
		$options[] = phpFrame_HTML::_('select.option', '3', _LANG_PROJECTS_STATUS_FINISHED );
		$options[] = phpFrame_HTML::_('select.option', '-1', _LANG_PROJECTS_STATUS_ARCHIVED );
		
		$output = phpFrame_HTML::_('select.genericlist', $options, 'status', $attribs, $selected);
		return $output;
	}
	
	/**
	 * Build and display an input tag with project members autocompleter
	 * 
	 * @param	int		$projectid
	 * @param	bool	$members	If TRUE it shows project members, if FALSE it shows non-project members
	 * @return	void
	 */
	static function autocompleteMembers($projectid, $members=true) {
		$db =& phpFrame::getDB();
		$query = "SELECT u.id, u.username, u.firstname, u.lastname ";
		$query .= "FROM #__users AS u ";
		$query .= " WHERE u.id ";
		if (!$members)  $query .= " NOT ";
		$query .= " IN (SELECT u.id FROM #__users AS u LEFT JOIN #__users_roles ur ON ur.userid = u.id WHERE ur.projectid = 1)";
		$query .= " AND (u.deleted = '0000-00-00 00:00:00' OR u.deleted IS NULL)";
		$query .= " ORDER BY u.username";
		$db -> setQuery($query);
		if (!$rows = $db->loadObjectList()) {
		  return _LANG_PROJECTS_NO_EXISTING_MEMBERS;
		}
		
		// Organise rows into array of arrays instead of array of objects
		foreach ($rows as $row) {
			$tokens[] = array('id' => $row->id, 'name' => $row->firstname." ".$row->lastname." (".$row->username.")");
		}
		
		phpFrame_HTML::autocomplete('userids', 'cols="60" rows="2"', $tokens);
	}
	
	static function project_roleid2name($id=0) {
		if (!empty($id)) { // No category has been selected
			$db =& phpFrame::getDB();
			$query = "SELECT name FROM #__roles WHERE id = '".$id."'";
			$db->setQuery($query);
			return $db->loadResult();
		}
		else {
			return false;
		}
	}
	
	static function project_role_select($selected=0, $attribs='') {
		// assemble project types into the array
		$options = array();
		//$options[] = phpFrame_HTML::_('select.option', '0', phpFrame_HTML_Text::_( '-- Select a Role --' ) );
		
		// get project_types from db
		$db =& phpFrame::getDB();
		$query = "SELECT id, name FROM #__roles ";
		$query .= " ORDER BY id ASC";
		$db -> setQuery($query);
		$rows = $db->loadObjectList();
		
		foreach ($rows as $row) {
			$options[] = phpFrame_HTML::_('select.option', $row->id, $row->name );
		}
		
		$output = phpFrame_HTML::_('select.genericlist', $options, 'roleid', $attribs, $selected);
		return $output;
	}
	
	static function issue_typeid2name($id=0) {
		if (!empty($id)) { // No category has been selected
			$db =& phpFrame::getDB();
			$query = "SELECT name FROM #__issue_types WHERE id = '".$id."'";
			$db -> setQuery($query);
			return $db->loadResult();
		}
		else {
			return false;
		}
	}
	
	static function issue_type_select($selected=0, $attribs='') {
		// assemble project types into the array
		$options = array();
		$options[] = phpFrame_HTML::_('select.option', '0', phpFrame_HTML_Text::_( '-- Select an issue type (optional) --' ) );
		
		// get project_types from db
		$db =& phpFrame::getDB();
		$query = "SELECT id, name FROM #__issue_types ";
		$query .= " ORDER BY id ASC";
		$db -> setQuery($query);
		$rows = $db->loadObjectList();
		
		if (is_array($rows) && count($rows) > 0) {
			foreach ($rows as $row) {
				$options[] = phpFrame_HTML::_('select.option', $row->id, $row->name );
			}
		}
		
		$output = phpFrame_HTML::_('select.genericlist', $options, 'issue_type', $attribs, $selected);
		return $output;
	}
	
	static function fileid2name($id=0) {
		if (!empty($id)) { // No file has been selected
			$db =& phpFrame::getDB();
			$query = "SELECT title FROM #__files WHERE id = '".$id."'";
			$db -> setQuery($query);
			return $db->loadResult();
		}
		else {
			return false;
		}
	}
	
	static function activitylog_type2printable($type) {
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
	
	static function mimetype2icon($mimetype) {
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