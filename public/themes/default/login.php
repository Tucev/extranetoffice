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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo config::DEFAULT_LANG; ?>" lang="<?php echo config::DEFAULT_LANG; ?>" >
<head>
<title><?php echo config::SITENAME; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php $document->printHead(); ?>
<link rel="stylesheet" href="themes/<?php echo config::TEMPLATE ?>/css/styles.css" type="text/css" />
<!--[if lte IE 6]>
<link href="templates/<?php echo config::TEMPLATE; ?>/css/ieonly.css" rel="stylesheet" type="text/css" />
<![endif]-->
</head>

<body>
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
$sys_events_obj = phpFrame::getSysevents();
$sys_events = $sys_events_obj->asString();
$sys_events_obj->clear();
echo $sys_events;
?>

<?php echo $component_output; ?>

</div><!-- close .loginbox -->

<div id="footer">
Powered by Extranet Office and phpFrame <?php echo phpFrame::getVersion(); ?><br />
&copy; 2009 E-noise.com Limited
</div>

</div><!-- close #login_wrapper -->

</center>
</body>
</html>