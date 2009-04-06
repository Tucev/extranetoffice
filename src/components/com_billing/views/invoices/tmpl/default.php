<?php
/**
 * @version 	$Id: default.php 40 2009-01-29 02:17:50Z luis.montero $
 * @package		ExtranetOffice
 * @subpackage 	com_billing
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

$this->loadTemplate(phpFrame_Environment_Request::getVar('layout', 'list'));
?>