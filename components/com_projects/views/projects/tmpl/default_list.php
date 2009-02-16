<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice.Projects
 * @subpackage 	viewProjects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );
?>

<script language="javascript" type="text/javascript">
function confirm_delete(id, label) {
	var answer = confirm("Are you sure you want to delete project '"+label+"'?")
	if (answer){
		window.location = "index.php?option=com_projects&task=remove_project&layout=list&projectid="+id;
	}
}
</script>

<h2><?php echo $this->page_title; ?></h2>

<?php if ($this->user->groupid == 1) : ?>
<div class="new">
	<a href="<?php echo route::_('index.php?option=com_projects&amp;view=projects&amp;layout=new'); ?>" title="<?php echo text::_( _LANG_PROJECTS_NEW ); ?>">
		<?php echo text::_( _LANG_PROJECTS_NEW ); ?>
	</a>
</div>
<?php endif; ?>

<br />


<script language="javascript" type="text/javascript">
function submit_filter(reset) {
	var form = document.forms['listsearchform'];
	
	if (reset){
		form.search.value = '';
	}
	
	form.submit();
}
</script>
<div class="filter_container">
<form action="index.php" id="listsearchform" name="listsearchform" method="post">
<input class="inputbox" type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>">
<button type="button" class="button" onclick="submit_filter(false);">Search</button>
<button type="button" class="button" onclick="submit_filter(true);">Reset</button>
<input type="hidden" name="option" value="com_projects" />
<input type="hidden" name="view" value="projects" />
<input type="hidden" name="layout" value="list" />
</form>
</div>

<br />

<?php if (is_array($this->rows) && count($this->rows) > 0) : ?>
<table class="ioffice_list" width="100%" cellpadding="0" cellspacing="0">
  <thead>
  <tr>
    <th><?php echo _LANG_PROJECTS_NAME; ?></th>
    <th><?php echo _LANG_PROJECTS_STATUS; ?></th>
    <th><?php echo _LANG_PROJECTS_PROJECT_TYPE; ?></th>
    <th><?php echo _LANG_PROJECTS_PRIORITY; ?></th>
    <th><?php echo _LANG_PROJECTS_ACCESS; ?></th>
  </tr>
  </thead>
  <tbody>
  <?php $k = 0; ?>
  <?php foreach($this->rows as $row) : ?>
  <tr class="row<?php echo $k; ?>">
    <td valign="top">
    <a href="index.php?option=com_projects&amp;view=projects&amp;layout=detail&amp;projectid=<?php echo $row->id; ?>">
	<?php echo $row->name; ?>
	</a>
    </td>
    <td>
    	<?php echo projectsHelperProjects::statusid2name($row->status); ?>
    </td>
    <td>
    	<?php echo projectsHelperProjects::project_typeid2name($row->project_type); ?>
    </td>
    <td>
    	<?php echo projectsHelperProjects::priorityid2name($row->priority); ?>
    </td>
    <td>
    	<?php echo projectsHelperProjects::global_accessid2name($row->access); ?>
    </td>
  </tr>
  <?php $k = 1 - $k; ?>
  <?php endforeach; ?>
  </tbody>
</table>

<br />

<?php echo $this->pageNav->getListFooter(); ?>

<?php else : ?>
<?php echo text::_( _LANG_NO_PROJECTS ); ?>
<?php endif; ?>