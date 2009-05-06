<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	mod_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

$projectid = phpFrame_Environment_Request::getVar('projectid', 0);

$controller = phpFrame::getActionController(phpFrame_Environment_Request::getComponentName());
$views_available = $controller->getViewsAvailable();
?>

<?php if (!empty($projectid)) : ?>

<h3>Project tools</h3>
	
<ul>
	
<li>
	<a href="<?php echo phpFrame_Application_Route::_("index.php?component=".phpFrame_Environment_Request::getComponentName()."&action=get_project_detail&projectid=".phpFrame_Environment_Request::getVar('projectid')); ?>">
	Project Home
	</a>
</li>
	
<?php foreach($views_available as $tool) : ?>
<?php if ($tool != 'projects') : ?>
<?php $access_property = "access_".$tool; ?>
<?php if ($controller->project_permissions->roleid <= $controller->project->$access_property) : ?>
<li>
	<a href="<?php echo phpFrame_Application_Route::_("index.php?component=".phpFrame_Environment_Request::getComponentName()."&action=get_".$tool."&projectid=".phpFrame_Environment_Request::getVar('projectid')); ?>">
	<?php 
	$tool_name = "_LANG_".strtoupper($tool);
	eval("echo $tool_name;");
	?>
	</a>
</li>
<?php endif; ?>
<?php endif; ?>
<?php endforeach; ?>
	
</ul>
	
<br />
<br />
	
<h3>Project details</h3>
	
<div class="project_details">
	<?php echo phpFrame_HTML_Text::_( _LANG_DESCRIPTION ); ?>: <br />
	<?php echo $controller->project->description; ?> <br />
	<br />
	<?php echo phpFrame_HTML_Text::_( _LANG_PROJECTS_PROJECT_TYPE ); ?>: <?php echo $controller->project->project_type_name; ?> <br />
	<?php echo phpFrame_HTML_Text::_( _LANG_PROJECTS_PRIORITY ); ?>: <?php echo projectsHelperProjects::priorityid2name($controller->project->priority); ?> <br />
	<?php echo phpFrame_HTML_Text::_( _LANG_PROJECTS_ACCESS ); ?>: <?php echo projectsHelperProjects::global_accessid2name($controller->project->access); ?> <br />
	<?php echo phpFrame_HTML_Text::_( _LANG_PROJECTS_STATUS ); ?>: <?php echo projectsHelperProjects::statusid2name($controller->project->status); ?>
</div>

<?php endif; ?>