<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	tmpl_default
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */
?>

<a name="up" id="up"></a>
<center>

<!-- Content -->
<div id="login_wrapper">

<div id="sitename">
	<a href="index.php">
	<?php echo config::SITENAME; ?>
	</a>
</div>

<div class="loginbox"> 

<?php 
$sys_events_obj = PHPFrame::Session()->getSysevents();
$sys_events = $sys_events_obj->asString();
$sys_events_obj->clear();
echo $sys_events;
?>

<?php echo $component_output; ?>

</div><!-- close .loginbox -->

<div id="footer">
Powered by Extranet Office and PHPFrame <?php echo PHPFrame::Version(); ?><br />
&copy; 2009 E-noise.com Limited
</div>

</div><!-- close #login_wrapper -->

</center>
