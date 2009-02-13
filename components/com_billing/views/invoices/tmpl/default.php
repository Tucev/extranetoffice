<?php
/**
 * @version 	$Id: default.php 40 2009-01-29 02:17:50Z luis.montero $
 * @package		ExtranetOffice.Billing
 * @subpackage 	viewInvoices
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

$this->loadTemplate(request::getVar('layout', 'list'));
?>