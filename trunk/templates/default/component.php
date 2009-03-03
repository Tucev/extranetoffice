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
<title><?php echo $this->config->sitename; ?> - <?php echo $this->document->title; ?></title>
<?php $this->document->printHead(); ?>
<link rel="stylesheet" href="templates/<?php echo $this->config->template ?>/css/styles.css" type="text/css" />
<!--[if lte IE 6]>
<link href="templates/<?php echo $this->config->template; ?>/css/ieonly.css" rel="stylesheet" type="text/css" />
<![endif]-->
</head>

<body>

<a name="up" id="up"></a>
 
<!-- Content -->
<div id="wrapper_outer">
	
	<div id="wrapper">
		
		<div id="main_col_1">
			<div id="main_col_inner">
			<?php error::display(); ?>
			<?php echo $this->component_output; ?>
			</div><!-- close #main_col_inner -->
		</div><!-- close #main_col -->

	</div><!-- close #wrapper -->

</div><!-- close #wrapper_outer -->
<!-- End Content -->

</body>
</html>