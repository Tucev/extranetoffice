<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	tmpl_default
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->lang; ?>" lang="<?php echo $this->lang; ?>" >
<head>
<title><?php echo config::SITENAME; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php $document->printHead(); ?>
<link rel="stylesheet" href="templates/<?php echo config::TEMPLATE ?>/css/styles.css" type="text/css" />
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

<?php phpFrame_Application_Error::display(); ?>

<?php echo $this->component_output; ?>

<div id="footer">
Extranet Office 1.0 Alpha $LastChangedRevision$<br />
&copy; 2009 E-noise.com Limited
</div>

</div><!-- close #login_wrapper -->

</center>
</body>
</html>