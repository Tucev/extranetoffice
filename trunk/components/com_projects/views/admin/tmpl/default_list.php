<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );
?>

<script language="javascript" type="text/javascript">
function confirm_delete(projectid, label) {
	var answer = confirm("Are you sure you want to delete project '"+label+"'?")
	if (answer){
		window.location = "index.php?option=com_projects&task=remove_project&projectid="+projectid;
	}
}
function confirm_delete_member(projectid, userid, label) {
	var answer = confirm("Are you sure you want to delete member '"+label+"' from this project?")
	if (answer){
		window.location = "index.php?option=com_projects&task=remove_member&projectid="+projectid+"&userid="+userid;
	}
}
</script>

<h2 class="componentheading"><?php echo $this->page_heading; ?></h2>


<div class="main_col_module_half">

	<div style="float:right;" class="edit">
		<a style="float:right;" href="<?php echo route::_("index.php?option=com_projects&view=projects&layout=form&projectid=".$this->project->id); ?>" title="<?php echo text::_( _LANG_PROJECTS_NEW ); ?>">
		<?php echo text::_( _LANG_EDIT ); ?>
		</a>
	</div>
	
	<?php if ($this->project->created_by == $this->user->id) : ?>
	<div style="float:right;" class="delete">
		<a style="float:right;" href="Javascript:confirm_delete(<?php echo $this->projectid; ?>, '<?php echo text::_($this->project->name, true); ?>');" title="<?php echo text::_( _LANG_DELETE ); ?>">
			<?php echo text::_( _LANG_DELETE ); ?> &nbsp;&nbsp; 
		</a>
	</div>
	<?php endif; ?>
	
	<h3 class="project_details">Project info</h3>

	<?php echo text::_( _LANG_PROJECTS_DESCRIPTION ); ?>: <br />
	<?php echo $this->project->description; ?> <br />
	<br />
	<?php echo text::_( _LANG_PROJECTS_PROJECT_TYPE ); ?>: <?php echo $this->project->project_type_name; ?> <br />
	<?php echo text::_( _LANG_PROJECTS_PRIORITY ); ?>: <?php echo projectsHelperProjects::priorityid2name($this->project->priority); ?> <br />
	<?php echo text::_( _LANG_PROJECTS_ACCESS ); ?>: <?php echo projectsHelperProjects::global_accessid2name($this->project->access); ?> <br />
	<?php echo text::_( _LANG_PROJECTS_STATUS ); ?>: <?php echo projectsHelperProjects::statusid2name($this->project->status); ?> <br />
	<?php echo text::_( _LANG_CREATED_BY ); ?>: <?php echo usersHelperUsers::id2name($this->project->created_by); ?> <br />
	<?php echo text::_( _LANG_CREATED ); ?>: <?php echo $this->project->created; ?>
	
</div><!-- close .main_col_module_half -->

<div class="main_col_module_half">
	
	<div style="float:right;" class="edit">
		<a style="float:right;" href="<?php echo route::_("index.php?option=com_projects&view=projects&layout=form&projectid=".$this->project->id); ?>" title="<?php echo text::_( _LANG_PROJECTS_NEW ); ?>">
		<?php echo text::_( _LANG_EDIT ); ?>
		</a>
	</div>
	
	<h3 class="project_permissions">Project permissions</h3>
	
	<table class="projects_permissions_detail" width="100%">
	<thead>
	<tr>
	<th></th>
	<th>Admins</th>
	<th>Project Workers</th>
	<th>Guests</th>
	<th>Others</th>
	</tr>
	</thead>
	<tbody>
	<tr>
	<th>Project home</th>
	<td>Yes</td>
	<td>Yes</td>
	<td>Yes</td>
	<td><?php if ($this->project->access == 0) { echo 'Yes'; } else { echo 'No'; } ?></td>
	</tr>
	<?php $k = 0; ?>
	<?php foreach ($this->project_permissions->tools as $tool) : ?>
	<tr class="row<?php echo $k; ?>">
	<th><?php echo $tool[0]; ?></th>
	<td><?php if ($tool[1] >= 1) { echo 'Yes'; } else { echo 'No'; } ?></td>
	<td><?php if ($tool[1] >= 2) { echo 'Yes'; } else { echo 'No'; } ?></td>
	<td><?php if ($tool[1] >= 3) { echo 'Yes'; } else { echo 'No'; } ?></td>
	<td><?php if ($tool[1] >= 4) { echo 'Yes'; } else { echo 'No'; } ?></td>
	</tr>
	<?php $k = 1 - $k; ?>
	<?php endforeach; ?>
	</tbody>
	</table>

</div><!-- close .main_col_module_half -->

<div style="clear:left;"></div>

<div class="main_col_module">

<h3 class="people">Members</h3>

<div class="new">
<a href="<?php echo route::_("index.php?option=".request::getVar('option')."&view=".$this->view."&layout=member_form&projectid=".$this->projectid); ?>">Add new member</a>
</div>

<br />

<?php if (is_array($this->members) && count($this->members) > 0) : ?>
<table class="list" width="100%" cellpadding="0" cellspacing="0">
  <thead>
  <tr>
    <th><?php echo _LANG_PROJECTS_ROLE; ?></th>
    <th><?php echo _LANG_NAME; ?></th>
    <th><?php echo _LANG_EMAIL; ?></th>
    <th></th>
  </tr>
  </thead>
  <tbody>
  <?php $k = 0; ?>
  <?php foreach($this->members as $row) : ?>
  <tr class="row<?php echo $k; ?>">
  	<td>
    	<?php echo $row->rolename; ?>
    </td>
    <td valign="top">
    <a href="<?php echo route::_("index.php?option=com_projects&amp;view=users&amp;layout=detail&amp;userid=".$row->userid); ?>">
	<?php echo $row->name; ?>
	</a>
    </td>
    <td>
    	<?php echo $row->email; ?>
    </td>
	<td>
	<a class="thickbox" href="index.php?option=com_projects&amp;view=admin&amp;layout=member_role&amp;projectid=<?php echo $this->projectid; ?>&amp;userid=<?php echo $row->userid; ?>&amp;tmpl=component?height=200&width=300" title="<?php echo _LANG_EDIT; ?>">
		<img src="templates/<?php echo $this->config->get('template'); ?>/images/icons/generic/16x16/edit.png" alt="<?php echo text::_( _LANG_EDIT ); ?>" />
	</a>
	<a href="Javascript:confirm_delete_member(<?php echo $this->projectid; ?>, <?php echo $row->userid; ?>, '<?php echo text::_($row->name, true); ?>');" title="<?php echo text::_( _LANG_DELETE ); ?>">
		<img src="templates/<?php echo $this->config->get('template'); ?>/images/icons/generic/16x16/remove.png" alt="<?php echo text::_( _LANG_DELETE ); ?>" />
	</a>
	</td>
  </tr>
  <?php $k = 1 - $k; ?>
  <?php endforeach; ?>
  </tbody>
</table>

<?php else : ?>
<?php echo text::_( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>

</div><!-- close .dashboard_item -->

<?php //echo '<pre>'; var_dump($this->members); echo '</pre>'; ?>