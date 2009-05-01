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
		$this->_db->setQuery($query);
		$this->_db->query();
		$total = $this->_db->getNumRows();
		
		// get the subset (based on limits)
		if ($limit == -1) $limit = $total;
		$pageNav = new phpFrame_HTML_Pagination($total, $limitstart, $limit);
		$query .= " ORDER BY ".$orderby." ".$orderdir.", c.id";
		$query .= " LIMIT ".$pageNav->limitstart.", ".$pageNav->limit;
		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		//var_dump($rows); exit;
		
		// pack data into an array to return
		return array('rows'=>$rows, 'pageNav'=>$pageNav);
	}
	
	function getContactsDetail() {
		
	}
	
	function saveContact($post) {
		// Get instance of table class
		$row = $this->getTable('contacts');
		//var_dump($row); exit;
		if (empty($post['id'])) {
			$row->created_by = $this->_user->id;
			$row->created = date("Y-m-d H:i:s");
		}
		else {
			$row->load($post['id']);
		}
		
		if (!$row->bind($post)) {
			$this->_error[] = $row->getLastError();
			return false;
		}
		
		if (!$row->check()) {
			$this->_error[] = $row->getLastError();
			return false;
		}
	
		if (!$row->store()) {
			$this->_error[] = $row->getLastError();
			return false;
		}
		
		return $row;
	}
	
	function deleteContact() {
		
	}
	
	function importContacts() {
		
	}
	
	function exportContacts($id=0) {
		$row = new iOfficeTableContacts();
		$row->load($id);
		
		$vCard = new vCard();
		
		// [ADR]
		$postoffice = '';
		$vCard->setAddress($postoffice, $row->home_extended, $row->home_street, $row->home_locality, $row->home_region, $row->home_postcode, $row->home_country, 'HOME;POSTAL');
		$vCard->setAddress($postoffice, $row->work_extended, $row->work_street, $row->work_locality, $row->work_region, $row->work_postcode, $row->work_country, 'WORK;POSTAL');
		$vCard->setAddress($postoffice, $row->other_extended, $row->other_street, $row->other_locality, $row->other_region, $row->other_postcode, $row->other_country, 'OTHER;POSTAL');
		
		// Birthday details
		$birthday = '';
		$vCard->setBirthday($birthday);
		
		// [EMAIL]
		$vCard->setEmail($row->home_email);
		
		// [FN]
		$vCard->setFormattedName($row->fn);
		
		// [N]
		// Need to implement additional, prefix and suffix
		$vCard->setName($row->family, $row->given, '', '', '');
		
		// Note
		$vCard->setNote($row->note);
		
		// Phone details
		// type may be PREF | WORK | HOME | VOICE | FAX | MSG | CELL | PAGER | BBS | CAR | MODEM | ISDN | VIDEO or any senseful combination, e.g. "PREF;WORK;VOICE"
		$vCard->setPhoneNumber($row->home_phone, "HOME");
		$vCard->setPhoneNumber($row->work_phone, "WORK");
		$vCard->setPhoneNumber($row->cell_phone, "CELL");
		$vCard->setPhoneNumber($row->fax, "FAX");
		
		
		// URL details
		$vCard->setURL($row->website, 'WORK');
		//$vCard->setURL($url, 'HOME');
		
		// Send output as file for download
		header("Content-type: text/x-vcard");
		header("Content-Disposition: attachment; filename=\"".$row->fn.".vcf\"");
		header("Content-Transfer-Encoding: binary");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Expires: 0");
		ob_clean();
		flush();
		
		# begin download
		echo $vCard->getVCard();
		exit;
	}
}
?>