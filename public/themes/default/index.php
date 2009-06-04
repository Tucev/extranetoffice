<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	tmpl_default
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo config::DEFAULT_LANG; ?>" lang="<?php echo config::DEFAULT_LANG; ?>" >
<head>
<title><?php echo config::SITENAME; ?> - <?php echo $document->title; ?></title>
<?php $document->printHead(); ?>
<link rel="stylesheet" href="themes/<?php echo config::TEMPLATE ?>/css/styles.css" type="text/css" />
<!--[if lte IE 6]>
<link href="themes/<?php echo config::TEMPLATE; ?>/css/ie6only.css" rel="stylesheet" type="text/css" />
<![endif]-->
<!--[if lte IE 7]>
<link href="themes/<?php echo config::TEMPLATE; ?>/css/ie7only.css" rel="stylesheet" type="text/css" />
<![endif]-->
</head>

<body>
<script type="text/javascript">
$(document).ready(function() {
	window.setTimeout("$(\"#error_msg\").fadeOut(\"slow\")", 3000);
});
</script>

<a name="up" id="up"></a>

<?php echo $modules->display('topmenu', '_topmenu'); ?>

<div id="sitename">
	<a href="index.php">
	<?php echo config::SITENAME; ?>
	</a>
</div>

<div id="breadcrumb">
	<?php $pathway->display(); ?>
</div>
<div style="clear:both;"></div>
 
<!-- Content -->
<div id="wrapper_outer">
	
	<?php echo $modules->display('topright', '_topright'); ?>
	<?php echo $modules->display('mainmenu', '_mainmenu'); ?>
	
	<div id="wrapper">
		<?php $column_count = 1; ?>
		
		<?php $modules_right = $modules->display('right'); ?>
		<?php if (!empty($modules_right)) : ?>
			<div id="right_col">
				<?php echo $modules_right; ?>
			</div><!-- close #right_col -->
			<?php $column_count++; ?>
		<?php endif; ?>
		
		<div id="main_col_<?php echo $column_count; ?>">
			<?php echo $modules->display('sysevents', '_sysevents'); ?>
			<div id="main_col_inner">
			<?php echo $component_output; ?>
			</div><!-- close #main_col_inner -->
		</div><!-- close #main_col -->

	</div><!-- close #wrapper -->

</div><!-- close #wrapper_outer -->
<!-- End Content -->

<div id="footer">
Powered by Extranet Office and phpFrame <?php echo phpFrame::getVersion(); ?><br />
&copy; 2009 E-noise.com Limited
</div>

</body>
</html>