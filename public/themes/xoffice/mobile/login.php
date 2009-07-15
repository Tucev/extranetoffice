<?php
/**
 * @version     $Id: login.php 584 2009-06-17 04:35:09Z luis.montero@e-noise.com $
 * @package        ExtranetOffice
 * @subpackage    tmpl_default
 * @copyright    Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license        BSD revised. See LICENSE.
 * @author         Luis Montero [e-noise.com]
 */
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->lang; ?>" lang="<?php echo $this->lang; ?>" >
<head>
<title><?php echo PHPFrame::Config()->get("SITENAME"); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/<?php echo PHPFrame::Config()->get("THEME") ?>/mobile/css/styles.css" type="text/css" />
</head>

<body>

<a name="up" id="up"></a>
 
<!-- Content -->
<div id="login_wrapper">

<div id="sitename">
    <a href="index.php">
    <?php echo PHPFrame::Config()->get("SITENAME"); ?>
    </a>
</div>

<?php PHPFrame_Application_Error::display(); ?>

<?php echo $this->component_output; ?>

<div id="footer">
Powered by Extranet Office 1.0 Alpha and PHPFrame<br />
&copy; 2009 E-noise.com Limited
<?php if (PHPFrame::Config()->get("DEBUG")) : ?>
<br />
Script Execution Time: <?php echo PHPFrame_Debug_Profiler::getExecutionTime(); ?> seconds
<?php endif; ?>
</div>

</div><!-- close #login_wrapper -->

</body>
</html>