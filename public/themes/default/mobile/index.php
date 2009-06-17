<?php
/**
 * @version     $Id$
 * @package        ExtranetOffice
 * @subpackage    tmpl_default
 * @copyright    Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license        BSD revised. See LICENSE.
 * @author         Luis Montero [e-noise.com]
 */

// render module positions for output
$module_positions = array();
$module_positions['topmenu'] = $this->modules->display('topmenu', '_topmenu');
$module_positions['mainmenu'] = $this->modules->display('mainmenu', '_mainmenu');
$module_positions['right'] = $this->modules->display('right');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->lang; ?>" lang="<?php echo $this->lang; ?>" >
<head>
<title><?php echo config::SITENAME; ?> - <?php echo $document->title; ?></title>
<?php $document->printHead(); ?>
<link rel="stylesheet" href="templates/<?php echo config::THEME ?>/mobile/css/styles.css" type="text/css" />
</head>

<body>
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
            <?php PHPFrame_Application_Error::display(); ?>
            <?php echo $this->component_output; ?>
            </div><!-- close #main_col_inner -->
        </div><!-- close #main_col -->

    </div><!-- close #wrapper -->

</div><!-- close #wrapper_outer -->
<!-- End Content -->

<div id="footer">
Powered by Extranet Office 1.0 Alpha and PHPFrame<br />
&copy; 2009 E-noise.com Limited
<?php if (config::DEBUG) : ?>
<br />
Script Execution Time: <?php echo PHPFrame_Debug_Profiler::getExecutionTime(); ?> seconds
<?php endif; ?>
</div>

</body>
</html>