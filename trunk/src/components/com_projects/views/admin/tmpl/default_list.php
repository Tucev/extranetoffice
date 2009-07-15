<?php
/**
 * src/components/com_projects/views/admin/tmpl/default_list.php
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

PHPFrame_HTML::confirm('delete_project', _LANG_PROJECTS_DELETE, _LANG_PROJECT_DELETE_CONFIRM);
PHPFrame_HTML::confirm('delete_member', _LANG_PROJECTS_DELETE_MEMBER, _LANG_PROJECT_MEMBER_DELETE_CONFIRM);
?>

<h2 class="componentheading">
    <a href="<?php echo $data['project_url']?>">
    <?php echo $data['page_title']; ?>
    </a>
</h2>

<div class="main_col_module_half">

    <div style="float:right;" class="edit">
        <a style="float:right;" href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&action=get_project_form&projectid=".$data['project']->id); ?>">
        <?php echo PHPFrame_Base_String::html( _LANG_EDIT ); ?>
        </a>
    </div>
    
    <?php if ($data['project']->created_by == PHPFrame::Session()->getUser()->id) : ?>
    <div style="float:right;" class="delete">
        <a class="delete_project" title="<?php echo $data['project']->name; ?>" style="float:right;" href="index.php?component=com_projects&action=remove_project&projectid=<?php echo $data['project']->id; ?>">
            <?php echo PHPFrame_Base_String::html( _LANG_DELETE ); ?> &nbsp;&nbsp; 
        </a>
    </div>
    <?php endif; ?>
    
    <h3 class="project_details">Project info</h3>

    <?php echo PHPFrame_Base_String::html( _LANG_DESCRIPTION ); ?>: <br />
    <?php echo $data['project']->description; ?> <br />
    <br />
    <?php echo PHPFrame_Base_String::html( _LANG_PROJECTS_PROJECT_TYPE ); ?>: <?php echo $data['project']->project_type_name; ?> <br />
    <?php echo PHPFrame_Base_String::html( _LANG_PROJECTS_PRIORITY ); ?>: <?php echo projectsHelperProjects::priorityid2name($data['project']->priority); ?> <br />
    <?php echo PHPFrame_Base_String::html( _LANG_PROJECTS_ACCESS ); ?>: <?php echo projectsHelperProjects::global_accessid2name($data['project']->access); ?> <br />
    <?php echo PHPFrame_Base_String::html( _LANG_PROJECTS_STATUS ); ?>: <?php echo projectsHelperProjects::statusid2name($data['project']->status); ?> <br />
    <?php echo PHPFrame_Base_String::html( _LANG_CREATED_BY ); ?>: <?php echo PHPFrame_User_Helper::id2name($data['project']->created_by); ?> <br />
    <?php echo PHPFrame_Base_String::html( _LANG_CREATED ); ?>: <?php echo $data['project']->created; ?>
    
</div><!-- close .main_col_module_half -->

<div class="main_col_module_half">
    
    <div style="float:right;" class="edit">
        <a style="float:right;" href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&action=get_project_form&projectid=".$data['project']->id); ?>" title="<?php echo PHPFrame_Base_String::html( _LANG_PROJECTS_NEW ); ?>">
        <?php echo PHPFrame_Base_String::html( _LANG_EDIT ); ?>
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
    <td><?php if ($data['project']->access == 0) { echo 'Yes'; } else { echo 'No'; } ?></td>
    </tr>
    <?php $k = 0; ?>
    <?php foreach ($data['tools'] as $tool) : ?>
    <?php if ($tool != 'projects') : ?>
    <tr class="row<?php echo $k; ?>">
    <th><?php echo $tool; ?></th>
    <?php $access_property_name = "access_".$tool; ?>
    <td><?php if ($data['project']->$access_property_name >= 1) { echo 'Yes'; } else { echo 'No'; } ?></td>
    <td><?php if ($data['project']->$access_property_name >= 2) { echo 'Yes'; } else { echo 'No'; } ?></td>
    <td><?php if ($data['project']->$access_property_name >= 3) { echo 'Yes'; } else { echo 'No'; } ?></td>
    <td><?php if ($data['project']->$access_property_name >= 4) { echo 'Yes'; } else { echo 'No'; } ?></td>
    </tr>
    <?php $k = 1 - $k; ?>
    <?php endif; ?>
    <?php endforeach; ?>
    </tbody>
    </table>

</div><!-- close .main_col_module_half -->

<div style="clear:left;"></div>

<div class="main_col_module">

<h3 class="people">Members</h3>

<div class="new">
<a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=".PHPFrame::Request()->getComponentName()."&action=get_member_form&projectid=".$data['project']->id); ?>">Add new member</a>
</div>

<br />

<?php if ($data['members']->countRows() > 0) : ?>
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
  <?php foreach($data['members'] as $row) : ?>
  <tr class="row<?php echo $k; ?>">
      <td>
        <?php echo $row->rolename; ?>
    </td>
    <td valign="top">
    <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_users&action=get_user&userid=".$row->userid); ?>">
    <?php echo $row->firstname." ". $row->lastname;?>
    </a>
    </td>
    <td>
        <?php echo $row->email; ?>
    </td>
    <td>
    <?php if ($row->userid != PHPFrame::Session()->getUserId()) : ?>
    <?php PHPFrame_HTML::dialog(_LANG_PROJECTS_CHANGE_ROLE, 'index.php?component=com_projects&action=get_member_role_form&projectid='.$data['project']->id.'&userid='.$row->userid, 300, 150, true); ?> 
    <a class="delete_member" title="<?php echo PHPFrame_Base_String::html($row->firstname." ". $row->lastname, true); ?>" href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&action=remove_member&projectid=".$data['project']->id."&userid=".$row->userid); ?>">
        <img src="templates/<?php echo PHPFrame::Config()->get("THEME"); ?>/images/icons/generic/16x16/remove.png" alt="<?php echo PHPFrame_Base_String::html( _LANG_DELETE ); ?>" />
    </a>
    <?php else : ?>
    <?php echo _LANG_PROJECTS_CANT_CHANGE_OWN_ROLE; ?>
    <?php endif; ?>
    </td>
  </tr>
  <?php $k = 1 - $k; ?>
  <?php endforeach; ?>
  </tbody>
</table>

<?php else : ?>
<?php echo PHPFrame_Base_String::html( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>

</div><!-- close .dashboard_item -->

<?php //echo '<pre>'; var_dump($data['members']); echo '</pre>'; ?>