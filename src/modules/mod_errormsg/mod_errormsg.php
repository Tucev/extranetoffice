<?php
/**
 * @version		$Id$
 * @package		ExtranetOffice
 * @subpackage 	mod_errormsg
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

$sys_events_obj = phpFrame::getSysevents();
$sys_events = $sys_events_obj->asArray();
$sys_events_obj->clear();
?>

<div id="error_msg">
	<?php //phpFrame_Application_Error::display(); ?>
	<?php var_dump($sys_events); ?>
</div>