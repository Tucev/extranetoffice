<?php
/**
 * src/components/com_admin/views/users/tmpl/default_list.php
 * 
 * PHP version 5
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_admin
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @copyright  2009 E-noise.com Limited
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 */

PHPFrame_HTML::confirm(
    'delete_user', 
    _LANG_ADMIN_USERS_DELETE, 
    _LANG_ADMIN_USERS_DELETE_CONFIRM, 
    "div[id^='ui-tabs']:has(a.delete_user)"
);
?>

<h2 class="componentheading"><?php echo $data['page_title']; ?></h2>


<?php if (PHPFrame::Session()->getUser()->groupid == 1) : ?>
<div class="new">
    <?php PHPFrame_HTML::dialog(_LANG_ADMIN_USERS_NEW, 'index.php?component=com_admin&action=get_user_form', 550, 390, true, "div[id^='ui-tabs']:has(a.delete_user)"); ?>
</div>
<?php endif; ?>

<br />

<?php echo $this->renderRowCollectionFilter($data['rows']); ?>

<br />

<?php if ($data['rows']->countRows() > 0) : ?>
<table class="data_list" width="100%" cellpadding="0" cellspacing="0">
  <thead>
  <tr>
      <th></th>
    <th><?php echo _LANG_USERNAME; ?></th>
    <th><?php echo _LANG_EMAIL; ?></th>
    <th><?php echo _LANG_FIRSTNAME; ?></th>
    <th><?php echo _LANG_LASTNAME; ?></th>
    <th><?php echo _LANG_BLOCK; ?></th>
    <th><?php echo _LANG_GROUP; ?></th>
    <th></th>
  </tr>
  </thead>
  <tbody>
  <?php $k = 0; ?>
  <?php foreach($data['rows'] as $row) : ?>
  <tr class="row<?php echo $k; ?>">
      <td>
          <input type="checkbox" name="ids[]" value="<?php echo $row->id; ?>" />
      </td>
    <td valign="top">
    <?php $username = PHPFrame_Base_String::limitChars($row->username, 10); ?>
    <?php PHPFrame_HTML::dialog($username, 'index.php?component=com_admin&action=get_user_form&userid='.$row->id, 550, 390, true, "div[id^='ui-tabs']:has(a.delete_user)"); ?>
    </td>
    <td>
        <?php echo $row->email; ?>
    </td>
    <td>
        <?php echo PHPFrame_Base_String::limitChars($row->firstname, 10); ?>
    </td>
    <td>
        <?php echo PHPFrame_Base_String::limitChars($row->lastname, 10); ?>
    </td>
    <td>
        <?php echo $row->block; ?>
    </td>
    <td>
        <?php echo $row->group_name; ?>
    </td>
    <td>
        <a class="delete_user" title="<?php echo PHPFrame_Base_String::html($row->firstname.' '.$row->lastname, true); ?>" href="index.php?component=com_admin&action=remove_user&id=<?php echo $row->id; ?>">
            <?php echo _LANG_DELETE; ?>
        </a>
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