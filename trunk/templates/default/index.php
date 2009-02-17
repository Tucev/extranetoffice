<?php
/**
* @version 		$Id: index.php 39 2009-01-28 22:35:49Z luis.montero $
* @package		ExtranetOffice
* @subpackage 	tmpl_default
* @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
* @license		BSD revised. See LICENSE.
* @author 		Luis Montero [e-noise.com]
*/

defined( '_EXEC' ) or die( 'Restricted access' );

// render module positions for output
$module_positions = array();
$module_positions['topmenu'] = $this->modules->display('topmenu');
$module_positions['right'] = $this->modules->display('right');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->lang; ?>" lang="<?php echo $this->lang; ?>" >
<head>
<title><?php echo $this->config->sitename; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/<?php echo $this->config->template ?>/css/styles.css" type="text/css" />
<!--[if lte IE 6]>
<link href="templates/<?php echo $this->config->template; ?>/css/ieonly.css" rel="stylesheet" type="text/css" />
<![endif]-->
</head>

<body>

<a name="up" id="up"></a>

<div id="top">
	<span class="icons_16_outer">
	<span class="icons_16 users_16">
		<a href="index.php?option=com_user">Account</a>
	</span>
	</span>
	<span class="icons_16_outer">
	<span class="icons_16 sysadmin_16">
		<a href="index.php?option=com_admin">System Admin</a>
	</span>
	</span>
	<span class="icons_16_outer">
	<span class="icons_16 security_16">
		<a href="index.php?option=com_login&amp;task=logout">Logout</a>
	</span>
	</span>
</div>

<div id="sitename">
	<a href="index.php">
	<?php echo $this->config->sitename; ?>
	</a>
</div>

<div id="breadcrumb">
	Home &gt;&gt;
</div>
<div style="clear:both;"></div>
 
<!-- Content -->
<div id="wrapper_outer">

	<?php echo $module_positions['topmenu']; ?>
	
	<div id="wrapper">
		<?php $column_count = 1; ?>
		
		<?php if (!empty($module_positions['right'])) : ?>
			<div id="right_col">
				<?php echo $module_positions['right']; ?>
			</div><!-- close #right_col -->
			<?php $column_count++; ?>
		<?php endif; ?>
		
		<div id="main_col_<?php echo $column_count; ?>">
			<div id="main_col_inner">
			<?php error::display(); ?>
			<?php echo $this->component_output; ?>
			</div><!-- close #main_col_inner -->
		</div><!-- close #main_col -->

	</div><!-- close #wrapper -->

</div><!-- close #wrapper_outer -->
<!-- End Content -->

<div id="footer">
Extranet Office 2.0 Alpha<br />
&copy; 2009 E-noise.com Limited
</div>

</body>
</html>