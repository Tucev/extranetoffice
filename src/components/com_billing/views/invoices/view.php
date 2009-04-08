<?php
/**
 * @version 	$Id: view.php 46 2009-02-13 01:37:49Z luis.montero $
 * @package		ExtranetOffice
 * @subpackage	com_billing
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * billingViewInvoices Class
 * 
 * The methods in this class are invoked by its parent class. See display() 
 * method in 'view' class.
 * 
 * Method name to be triggered will be formed as follows:
 * 
 * <code>
 * $tmpl_specific_method = "display".ucfirst(phpFrame_Environment_Request::getVar('view')).ucfirst($this->tmpl);
 * </code>
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_billing
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		view, controller
 */
class billingViewInvoices extends phpFrame_Application_View {
	var $page_title=null;
	
	function __construct() {
		// Set the view template to load
		$this->tmpl = phpFrame_Environment_Request::getVar('tmpl', 'list');
		
		parent::__construct();
	}
	
	function displayInvoicesList() {
		$this->page_title = _LANG_BILLING_INVOICES;
		
		$current_date = date("Y-m");
		$this->filter['date_range'] = "'".$current_date."-01' AND '".$current_date."-31'";
		$this->filter['basis'] = "datepaid";
		$this->filter['type'] = "";
		$this->filter['pluginused'] = "";
		
		if (!empty(phpFrame_Environment_Request::getVar('dtstart_yyyy', ''))) {
			$this->filter['date_range'] = "'".phpFrame_Environment_Request::getVar('dtstart_yyyy')."-".phpFrame_Environment_Request::getVar('dtstart_mm')."-".phpFrame_Environment_Request::getVar('dtstart_dd')."'";
			$this->filter['date_range'] .= " AND ";
			$this->filter['date_range'] .= "'".phpFrame_Environment_Request::getVar('dtend_yyyy')."-".phpFrame_Environment_Request::getVar('dtend_mm')."-".phpFrame_Environment_Request::getVar('dtend_dd')."'";
		}
		
		$this->filter['basis'] = phpFrame_Environment_Request::getVar('basis', '');
		$this->filter['billingtype'] = phpFrame_Environment_Request::getVar('billingtype', '');
		$this->filter['pluginused'] = phpFrame_Environment_Request::getVar('pluginused', '');
		
		// get invoices
		$model = $this->getModel();
		
		$date_range = $filter['date_range'];
		$date_range_array = explode(' AND ', $date_range);
		$dtstart_array = explode('-', str_replace("'", '', $date_range_array[0]));
		$dtstart_yyyy = $dtstart_array[0];
		$dtstart_mm = $dtstart_array[1];
		$dtstart_dd = $dtstart_array[2];
		$dtend_array = explode('-', str_replace("'", '', $date_range_array[1]));
		$dtend_yyyy = $dtend_array[0];
		$dtend_mm = $dtend_array[1];
		$dtend_dd = $dtend_array[2];
	}
}
?>