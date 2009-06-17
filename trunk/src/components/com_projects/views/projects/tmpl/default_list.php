<?php
/**
 * @version     $Id$
 * @package        ExtranetOffice
 * @subpackage     com_projects
 * @copyright    Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license        BSD revised. See LICENSE.
 */
?>

<h2 class="componentheading"><?php echo $data['page_title']; ?></h2>

<?php if (PHPFrame::Session()->getUser()->groupid == 1) : ?>
<div class="new">
    <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL('index.php?component=com_projects&action=get_project_form'); ?>" title="<?php echo PHPFrame_Base_String::html( _LANG_PROJECTS_NEW ); ?>">
        <?php echo PHPFrame_Base_String::html( _LANG_PROJECTS_NEW ); ?>
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
<div class="list_filter_container">
<form action="index.php" id="listsearchform" name="listsearchform" method="post">
<input type="text" name="search" id="search" value="<?php echo PHPFrame::Request()->get('search'); ?>">
<button type="button" class="button" onclick="submit_filter(false);">Search</button>
<button type="button" class="button" onclick="submit_filter(true);">Reset</button>
<input type="hidden" name="component" value="com_projects" />
<input type="hidden" name="action" value="get_projects" />
</form>
</div>

<br />

<?php if ($data['rows']->countRows() > 0) : ?>
<table class="data_list" width="100%" cellpadding="0" cellspacing="0">
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
  <?php foreach($data['rows'] as $row) : ?>
  <tr class="row<?php echo $k; ?>">
    <td valign="top">
    <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&action=get_project_detail&projectid=".$row->id); ?>">
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

<?php echo $this->renderPagination($data['rows']); ?>

<?php else : ?>
<?php echo PHPFrame_Base_String::html( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>