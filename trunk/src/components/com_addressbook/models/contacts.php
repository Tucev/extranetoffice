<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_addressbook
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

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
	function __construct() {}
	
	/**
	 * Get Invoices
	 * 
	 * @param	object	$list_filter	An object of type phpFrame_Database_CollectionFilter
	 * @return	array	Returns an asoc array containing the rows.
	 */
	function getContacts(phpFrame_Database_CollectionFilter $list_filter) {
		// Build SQL query
		$query = "SELECT * ";
		$query .= " FROM #__contacts AS c ";
		
		// get the total number of records
		phpFrame::getDB()->setQuery($query);
		phpFrame::getDB()->query();
		
		// Set total number of record in list filter
		$list_filter->setTotal(phpFrame::getDB()->getNumRows());
		
		// Add order by and limit statements for subset (based on filter)
		$query .= $list_filter->getOrderByStmt();
		$query .= $list_filter->getLimitStmt();
		//echo str_replace('#__','eo_', $query); exit;
		
		// Get rows from database
		phpFrame::getDB()->setQuery($query);
		return phpFrame::getDB()->loadObjectList();
	}
	
	function getContactsDetail($id) {
		
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
	
	function deleteContact($id) {
		
	}
	
	function importContacts($file) {
		
	}
	
	function exportContacts($id) {
		// Load row from database table
		$row = $this->getTable('contacts')->load($id);
		
		// Create new instance of vCard
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
