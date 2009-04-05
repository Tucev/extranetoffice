<?php
/**
 * @version 	$Id: invoices.php 46 2009-02-13 01:37:49Z luis.montero $
 * @package		ExtranetOffice
 * @subpackage	com_billing
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * billingModelInvoices Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_billing
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		model
 */
class billingModelInvoices extends phpFrame_Application_Model {
	var $filter=null;
	
	function __construct() {
		
	}
	
	function listInvoices() {
		$query = "SELECT i.id as id, i.customerid as customerid, i.billdate as billdate, i.datepaid as datepaid ";
		$query .= ", i.amount as amount, i.subtotal as subtotal, i.status as paid, i.pluginused as pluginused ";
		$query .= ", u.firstname as firstname, u.lastname as lastname, u.organization as organization ";
		$query .= " FROM invoice as i, users as u ";
		$query .= " WHERE i.status = 1 ";
		$query .= " AND i.customerid = u.id ";
		$query .= " AND i.".$this->filter['basis']." BETWEEN ".$this->filter['date_range']." ";
		$query .= " ORDER BY i.datepaid ASC, i.id";
		//echo $query;
		
		$db = new db;
		$result = $db->query($query);
		
		$i=0;
		while ($row = mysql_fetch_array($result)) {
			$invoice_type = $this->getInvoiceType($row['id']);
			$invoice_pluginused = $this->getPluginUsed($row['id']);
			if ((!empty($this->filter['type']) && $invoice_type == $this->filter['type'])
				 || empty($this->filter['type'])) {
				
				if ((!empty($this->filter['pluginused']) && $invoice_pluginused == $this->filter['pluginused'])
				 	|| empty($this->filter['pluginused'])) {
				 	
				 	foreach ($row as $key=>$value) {
						$rows[$i]->$key = $value;
					}
					$rows[$i]->type = $invoice_type;
					
					if ($rows[$i]->pluginused == 'none') {
						$rows[$i]->pluginused = $this->getPluginUsed($rows[$i]->id);
					}
					
				}
			}
			$i++;
		}
		
		HTML_invoices::filterInvoice($this->filter);
		HTML_invoices::listInvoices($rows, $this->filter);
	}
	
	function exportQIF() {
		$query = "SELECT i.id as id, i.customerid as customerid, i.datepaid as datepaid ";
		$query .= ", i.amount as amount, i.subtotal as subtotal, i.status as paid, i.pluginused as pluginused ";
		$query .= " FROM invoice as i ";
		$query .= " WHERE i.status = 1 ";
		$query .= " AND i.".$this->filter['basis']." BETWEEN ".$this->filter['date_range']." ";
		$query .= " ORDER BY i.id";
		//echo $query;
		
		$db = new db;
		$result = $db->query($query);
		
		$i=0;
		while ($row = mysql_fetch_array($result)) {
			$invoice_type = $this->getInvoiceType($row['id']);
			$invoice_pluginused = $this->getPluginUsed($row['id']);
			if ((!empty($this->filter['type']) && $invoice_type == $this->filter['type'])
				 || empty($this->filter['type'])) {
				
				if ((!empty($this->filter['pluginused']) && $invoice_pluginused == $this->filter['pluginused'])
				 	|| empty($this->filter['pluginused'])) {
				 	
				 	foreach ($row as $key=>$value) {
						$rows[$i]->$key = $value;
					}
					$rows[$i]->type = $invoice_type;
					
					if ($rows[$i]->pluginused == 'none') {
						$rows[$i]->pluginused = $this->getPluginUsed($rows[$i]->id);
					}
					
				}
			}
			$i++;
		}
		
		// QIF Download
		$file_name .= 'invoices_';
		if (!empty($this->filter['type'])) {
			$file_name .= $this->filter['type'].'_';
		}
		if (!empty($this->filter['pluginused'])) {
			$file_name .= $this->filter['pluginused']."_";
		}
		$file_name_date = str_replace('\'', '', $this->filter['date_range']);
		$file_name .= str_replace(' AND ', '_', $file_name_date).'.qif';
		
		header("Content-type: application/qif");
		header("Content-Disposition: attachment; filename=\"".$file_name."\"");
		// Data
		$qif = "!Type:Bank\n"; // QIF Header
		foreach ($rows as $row) {
			$qif .= "D".$row->datepaid."\n"; // QIF Date
			$qif .= "T".$row->amount."\n"; // QIF Amount
			$qif .= "PInv. Id: ".$row->id." - Cust. Id: ".$row->customerid." - Type: ".$row->type."\n"; // QIF Payee
			// Set income category depending on invoice type
			switch ($this->filter['type']) {
				case 'hosting':
					$income_category = "Income:Sales:Hosting";
					break;						
				
				case 'development':
					$income_category = "Income:Sales:Development";
					break;
						
				case 'consultancy':
					$income_category = "Income:Sales:Consultancy";
					break;
						
				default :
					$income_category = "Income:Sales:Other";
					break;
			}
			//$qif .= "LAssets:Current Assets:".$row->pluginused."\n"; // QIF Category
			$qif .= "L".$income_category."\n"; // Income Category
			// Split transaction for VAT
			if ($row->amount != $row->subtotal) {
				// First split item
				$qif .= "S".$income_category."\n"; // Income Category
				$qif .= "$".$row->subtotal."\n"; // Excl VAT
				// Second split item
				$qif .= "SVAT:Output:Sales\n"; // VAT Output Category
				$qif .= "$".($row->amount - $row->subtotal)."\n"; // VAT amount
			}
			$qif .= "^\n"; // End of transaction
		}
		echo $qif;
	}
	
	function getInvoiceType($invoiceid) {
		$query = "SELECT invoiceentry.id as id, invoiceentry.billingtypeid as billingtypeid, billingtype.name ";
		$query .= " FROM invoiceentry, billingtype ";
		$query .= " WHERE invoiceid = ".$invoiceid;
		$query .= " AND invoiceentry.billingtypeid = billingtype.id ";
		//echo $query;
		
		$db = new db;
		$result = $db->query($query);
		// Get first result to check type
		$row = mysql_fetch_array($result);
		
		switch ($row['billingtypeid']) {
			case 1 :
			case -2 :
			case -3 :
			case -4 :
			case 6 :
			case 8 :
				return 'hosting';
			case 2 :
			case 3 :
			case 4 :
				return 'development';
			case 5 :
			case 7 :
				return 'consultancy';
			default:
				return 'other';
		}
	}
	
	function getPluginUsed($invoiceid) {
		$query = "SELECT response FROM invoicetransaction ";
		$query .= " WHERE invoiceid = ".$invoiceid;
		//echo $query;
		
		$db = new db;
		$result = $db->query($query);
		// Get first result to check type
		$row = mysql_fetch_array($result);
		
		// remove space at the begging of string (2checkout responses have a space)
		$response = trim($row['response']);
		// Explode into array using spaces to get first word
		$response = explode(' ', $response);
		// get first word
		$response = $response[0];
		
		//return $response;
		
		if ($response == 'Paypal') {
			return 'paypal';
		}
		elseif ($response == '2checkout') {
			return '2checkout';
		}
		else {
			return 'Undefined';
		}
	}
}
?>