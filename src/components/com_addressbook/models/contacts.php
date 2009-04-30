<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_addressbook
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * addressbookModelContacts Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_addressbook
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		phpFrame_Application_Model
 */
class addressbookModelContacts extends phpFrame_Application_Model {
	/**
	 * Constructor
	 * 
	 * @return void
	 */
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Get Invoices
	 * 
	 * @param	string	$search		Search string.	
	 * @param	int		$limit		Limit the result set.
	 * @param 	int		$limitstart	When limiting results this is used to specify the entry from which the current page starts.
	 * @param 	string	$orderby	The column to use for sorting the results.
	 * @param	string	$orderdir	The direction in which to sort the results. 
	 * @return	mixed	Returns an asoc array containing the rows and pageNav or FALSE on failure.
	 */
	function getContacts($search='', $limit=25, $limitstart=0, $orderby='c.family', $orderdir='ASC') {
		// Build SQL query
		$query = "SELECT * ";
		$query .= " FROM #__contacts AS c ";
		//echo str_replace('#__','eo_', $query); exit;
		
		// get the total number of records
		$this->db->setQuery($query);
		$this->db->query();
		$total = $this->db->getNumRows();
		
		// get the subset (based on limits)
		if ($limit == -1) $limit = $total;
		$pageNav = new phpFrame_HTML_Pagination($total, $limitstart, $limit);
		$query .= " ORDER BY ".$orderby." ".$orderdir.", c.id";
		$query .= " LIMIT ".$pageNav->limitstart.", ".$pageNav->limit;
		$this->db->setQuery($query);
		$rows = $this->db->loadObjectList();
		//var_dump($rows); exit;
		
		// pack data into an array to return
		return array('rows'=>$rows, 'pageNav'=>$pageNav);
	}
	
	function getContactsDetail() {
		
	}
	
	function saveContact($post) {
		// Get instance of table class
		$row =& phpFrame_Base_Singleton::getInstance("addressbookTableContacts");
		
		if (empty($post['id'])) {
			$row->created_by = $this->user->id;
			$row->created = date("Y-m-d H:i:s");
		}
		else {
			$row->load($post['id']);
		}
		
		if (!$row->bind($post)) {
			$this->error[] = $row->getLastError();
			return false;
		}
		
		if (!$row->check()) {
			$this->error[] = $row->getLastError();
			return false;
		}
	
		if (!$row->store()) {
			$this->error[] = $row->getLastError();
			return false;
		}
		
		return $row;
	}
	
	function deleteContact() {
		
	}
	
	function importContacts() {
		
	}
	
	function exportContacts() {
		
	}
}
?>