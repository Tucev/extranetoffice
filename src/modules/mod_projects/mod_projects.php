<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	mod_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

$projectid = PHPFrame::getRequest()->get('projectid', 0);

$controller = PHPFrame::getActionController(PHPFrame::getRequest()->getComponentName());
$project = $controller->getProject();
$tools = $controller->getTools();
$project_permissions = $controller->getProjectPermissions();
?>

<?php if (!empty($projectid)) : ?>

<h3>Project tools</h3>
	
<ul>
	
<li>
	<a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=".PHPFrame::getRequest()->getComponentName()."&action=get_project_detail&projectid=".PHPFrame::getRequest()->get('projectid')); ?>">
	Project Home
	</a>
</li>
	
<?php foreach($tools as $tool) : ?>
<?php $access_property = "access_".$tool; ?>
<?php if (!is_null($project->$access_property) && $project_permissions->getRoleId() <= $project->$access_property) : ?>
<li>
	<a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=".PHPFrame::getRequest()->getComponentName()."&action=get_".$tool."&projectid=".PHPFrame::getRequest()->get('projectid')); ?>">
	<?php 
	$tool_name = "_LANG_".strtoupper($tool);
	eval("echo $tool_name;");
	?>
	</a>
</li>
<?php endif; ?>
<?php endforeach; ?>
	
</ul>
	
<br />
<br />
	
<h3>Project details</h3>
	
<div class="project_details">
	<?php echo PHPFrame_HTML_Text::_( _LANG_DESCRIPTION ); ?>: <br />
	<?php echo $project->description; ?> <br />
	<br />
	<?php echo PHPFrame_HTML_Text::_( _LANG_PROJECTS_PROJECT_TYPE ); ?>: <?php echo $project->project_type_name; ?> <br />
	<?php echo PHPFrame_HTML_Text::_( _LANG_PROJECTS_PRIORITY ); ?>: <?php echo projectsHelperProjects::priorityid2name($project->priority); ?> <br />
	<?php echo PHPFrame_HTML_Text::_( _LANG_PROJECTS_ACCESS ); ?>: <?php echo projectsHelperProjects::global_accessid2name($project->access); ?> <br />
	<?php echo PHPFrame_HTML_Text::_( _LANG_PROJECTS_STATUS ); ?>: <?php echo projectsHelperProjects::statusid2name($project->status); ?>
</div>

<?php endif; ?>