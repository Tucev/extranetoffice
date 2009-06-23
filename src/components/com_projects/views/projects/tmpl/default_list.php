<?php
/**
 * src/components/com_projects/views/projects/tmpl/default_list.php
 * 
 * PHP version 5
 * 
 * @category   MVC_Framework
 * @package    ExtranetOffice
 * @subpackage com_projects
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @copyright  2009 E-noise.com Limited
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/extranetoffice/source/browse
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

<?php echo $this->renderRowCollectionFilter($data['rows']); ?>

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