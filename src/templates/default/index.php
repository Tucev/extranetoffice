<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	tmpl_default
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

// render module positions for output
$module_positions = array();
$module_positions['topmenu'] = $this->modules->display('topmenu', '_topmenu');
$module_positions['mainmenu'] = $this->modules->display('mainmenu', '_mainmenu');
$module_positions['topright'] = $this->modules->display('topright', '_topright');
$module_positions['right'] = $this->modules->display('right');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo config::DEFAULT_LANG; ?>" lang="<?php echo config::DEFAULT_LANG; ?>" >
<head>
<title><?php echo config::SITENAME; ?> - <?php echo $document->title; ?></title>
<?php $document->printHead(); ?>
<link rel="stylesheet" href="templates/<?php echo config::TEMPLATE ?>/css/styles.css" type="text/css" />
<!--[if lte IE 6]>
<link href="templates/<?php echo config::TEMPLATE; ?>/css/ie6only.css" rel="stylesheet" type="text/css" />
<![endif]-->
<!--[if lte IE 7]>
<link href="templates/<?php echo config::TEMPLATE; ?>/css/ie7only.css" rel="stylesheet" type="text/css" />
<![endif]-->
</head>

<body>
<script type="text/javascript">
$(document).ready(function() {
	window.setTimeout("$(\"#error_msg\").fadeOut(\"slow\")", 3000);
});
</script>

<a name="up" id="up"></a>

<?php echo $module_positions['topmenu']; ?>

<div id="sitename">
	<a href="index.php">
	<?php echo config::SITENAME; ?>
	</a>
</div>

<div id="breadcrumb">
	<?php $this->pathway->display(); ?>
</div>
<div style="clear:both;"></div>
 
<!-- Content -->
<div id="wrapper_outer">
	
	<?php echo $module_positions['topright']; ?>
	<?php echo $module_positions['mainmenu']; ?>
	
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
			<?php echo $this->component_output; ?>
			</div><!-- close #main_col_inner -->
		</div><!-- close #main_col -->

	</div><!-- close #wrapper -->

</div><!-- close #wrapper_outer -->
<!-- End Content -->

<div id="footer">
Powered by Extranet Office 1.0 Alpha $LastChangedRevision$<br />
&copy; 2009 E-noise.com Limited
</div>

</body>
</html>