<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_users
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

class usersHelperUsers {
	/**
	 * Translate userid to username
	 * @param 	int		The ID to be translated
	 * @return 	string	If no id is passed returns false, otherwise returns the username as a string
	 */
	static function id2name($id=0) {
		if (!empty($id)) { // No user has been selected
			$db =& factory::getDB(); // Instantiate joomla database object
			$query = "SELECT firstname, lastname FROM #__users WHERE id = '".$id."'";
			$db->setQuery($query);
			$row = $db->loadObject();
			if ($row === false) {
				return false;
			}
			
			return strtoupper(substr($row->firstname, 0, 1)).'. '.$row->lastname;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Translate username to userid
	 * @param 	string	The username to be translated
	 * @return 	int		If no username is passed returns false, otherwise returns the user ID
	 */
	static function username2id($username='') {
		if (!empty($username)) { // No user has been selected
			$db =& factory::getDB(); // Instantiate joomla database object
			$query = "SELECT id FROM #__users WHERE username = '".$username."'";
			$db -> setQuery($query);
			return $db->loadResult();
		}
		else {
			return false;
		}
	}
	
	/**
	 * Translate email to userid
	 * @param 	string	The email to be translated
	 * @return 	int		If no email is passed returns false, otherwise returns the user ID
	 */
	static function email2id($email='') {
		if (!empty($email)) { // No user has been selected
			$db =& factory::getDB(); // Instantiate joomla database object
			$query = "SELECT id FROM #__users WHERE email = '".$email."'";
			$db -> setQuery($query);
			return $db->loadResult();
		}
		else {
			return false;
		}
	}
	
	static function id2email($id) {
		if (!empty($id)) { // No user has been selected
			$db =& factory::getDB(); // Instantiate joomla database object
			$query = "SELECT email FROM #__users WHERE id = '".$id."'";
			$db -> setQuery($query);
			return $db->loadResult();
		}
		else {
			return false;
		}
	}
	
	static function id2photo($id) {
		if (!empty($id)) { // No user has been selected
			$db =& factory::getDB(); // Instantiate joomla database object
			$query = "SELECT photo FROM #__intranetoffice_settings WHERE userid = '".$id."'";
			$db -> setQuery($query);
			$photo = $db->loadResult();
			if (empty($photo)) { $photo = 'default.png'; }
			return $photo;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Function to build HTML select of users
	 * @param	int		The selected value if any
	 * @param 	string	Attributes for the <select> tag
	 * @return 	string	A string with the HTML select
	 */
	static function select($selected=0, $attribs='', $fieldname='userid', $projectid=0) {
		// assemble users to the array
		$options = array();
		$options[] = JHTML::_('select.option', '0', JText::_( '-- Select a User --' ) );
		
		// get joomla users from #__users
		$db =& factory::getDB(); // Instantiate joomla database object
		$query = "SELECT u.id, u.name ";
		$query .= " FROM #__users AS u ";
		if (!empty($projectid)) {
			$query .= " LEFT JOIN #__intranetoffice_users_roles ur ON ur.userid = u.id ";
			$query .= " WHERE ur.projectid = ".$projectid;
		}
		$query .= " ORDER BY u.name";
		//echo $query; exit;
		$db -> setQuery($query);
		if (!$rows = $db->loadObjectList()) {
		  return false;
		}
		
		foreach ($rows as $row) {
			$options[] = JHTML::_('select.option', $row->id, $row->name );
		}
		
		$attribs .= ' class="inputbox"';
		$output = JHTML::_('select.genericlist', $options, $fieldname, $attribs, $selected);
		return $output;		
	}
	
	static function assignees($selected=0, $attribs='', $fieldname='assignees[]', $projectid=0) {
		// get joomla users from #__users
		$db =& factory::getDB(); // Instantiate joomla database object
		$query = "SELECT u.id, u.name ";
		$query .= " FROM #__users AS u ";
		if (!empty($projectid)) {
			$query .= " LEFT JOIN #__intranetoffice_users_roles ur ON ur.userid = u.id ";
			$query .= " WHERE ur.projectid = ".$projectid;
		}
		$query .= " ORDER BY u.name";
		//echo $query; exit;
		$db -> setQuery($query);
		if (!$rows = $db->loadObjectList()) {
		  return false;
		}
		
		// organise assignees in array for checking selected users
		$assignees = array();
		if (is_array($selected)) {
			foreach ($selected as $assignee) {
				$assignees[] = $assignee['id'];
			}
		}
		
		$attribs .= ' class="inputbox"';
		$output = '';
		for ($i=0; $i<count($rows); $i++) {
			$output .= '<input type="checkbox" name="'.$fieldname.'" ';
			$output .= ' value="'.$rows[$i]->id.'" '.$attribs;
			if (in_array($rows[$i]->id, $assignees)) { $output .= 'checked'; }
			$output .= ' /> ';
			$output .= $rows[$i]->name.'&nbsp;&nbsp;';
			// Add line break every three entries (test using modulus)
			if ((($i+1) % 3) == 0) { $output .= '<br />'; }
		}
		
		return $output;		
	}
	
	static function autocompleteUsername($form_name) {
		// get joomla users from #__users
		$db =& factory::getDB(); // Instantiate joomla database object
		$query = "SELECT id, username FROM #__users ";
		$query .= " ORDER BY username";
		$db -> setQuery($query);
		if (!$rows = $db->loadObjectList()) {
		  return false;
		}
		
		// Organise rows into array of arrays instead of array of objects
		foreach ($rows as $row) {
			$tokens[] = array($row->username, $row->id);
		}
		
		return enoiseAutocompleter::input($form_name, 'username', '', $tokens);
	}
}
?>