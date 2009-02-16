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

$projectid = request::getVar('projectid', 0);

//$controller =& phpFrame::getInstance('projectsController');
?>

<?php if (!empty($projectid)) : ?>

<h3>Project tools</h3>
	
<ul class="ioffice_right_col_menu">
	
<li>
	<a href="<?php echo route::_("index.php?option=".request::getVar('option')."&view=".$this->view."&layout=detail&projectid=".$this->projectid); ?>">
	Project Home
	</a>
</li>
	
<?php foreach($this->tools as $tool) : ?>
<?php if ($this->roleid <= $tool[1]) : ?>
<li>
	<a href="<?php echo route::_("index.php?option=".request::getVar('option')."&view=".$this->view."&layout=".$tool[0]."&projectid=".$this->projectid); ?>">
	<?php 
	$tool_name = "_INTRANETOFFICE_".strtoupper($tool[0]);
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
	
<div class="ioffice_project_details">
	<?php echo text::_( _INTRANETOFFICE_PROJECTS_DESCRIPTION ); ?>: <br />
	<?php echo $this->project->description; ?> <br />
	<br />
	<?php echo text::_( _INTRANETOFFICE_PROJECTS_PROJECT_TYPE ); ?>: <?php echo $this->project->project_type_name; ?> <br />
	<?php echo text::_( _INTRANETOFFICE_PROJECTS_PRIORITY ); ?>: <?php echo projectsHelperProjects::priorityid2name($this->project->priority); ?> <br />
	<?php echo text::_( _INTRANETOFFICE_PROJECTS_ACCESS ); ?>: <?php echo projectsHelperProjects::global_accessid2name($this->project->access); ?> <br />
	<?php echo text::_( _INTRANETOFFICE_PROJECTS_STATUS ); ?>: <?php echo projectsHelperProjects::statusid2name($this->project->status); ?>
</div>

<?php endif; ?>