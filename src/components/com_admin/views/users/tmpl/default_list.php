<?php
/**
 * @version 	$Id$
 * @package		PHPFrame
 * @subpackage 	com_admin
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

PHPFrame_HTML::confirm('delete_user', _LANG_ADMIN_USERS_DELETE, _LANG_ADMIN_USERS_DELETE_CONFIRM, "div[id^='ui-tabs']:has(a.delete_user)");
?>

<h2 class="componentheading"><?php echo $data['page_title']; ?></h2>


<?php if (PHPFrame::Session()->getUser()->groupid == 1) : ?>
<div class="new">
	<?php PHPFrame_HTML::dialog(_LANG_ADMIN_USERS_NEW, 'index.php?component=com_admin&action=get_user_form', 550, 390, true, "div[id^='ui-tabs']:has(a.delete_user)"); ?>
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
<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>">
<button type="button" class="button" onclick="submit_filter(false);">Search</button>
<button type="button" class="button" onclick="submit_filter(true);">Reset</button>
<input type="hidden" name="component" value="com_admin" />
<input type="hidden" name="view" value="users" />
<input type="hidden" name="layout" value="list" />
</form>
</div>

<br />

<?php if (is_array($data['rows']) && count($data['rows']) > 0) : ?>
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
    <?php $username = PHPFrame_HTML_Text::limit_chars($row->username, 10); ?>
    <?php PHPFrame_HTML::dialog($username, 'index.php?component=com_admin&action=get_user_form&userid='.$row->id, 550, 390, true, "div[id^='ui-tabs']:has(a.delete_user)"); ?>
    </td>
    <td>
    	<?php echo $row->email; ?>
    </td>
    <td>
    	<?php echo PHPFrame_HTML_Text::limit_chars($row->firstname, 10); ?>
    </td>
    <td>
    	<?php echo PHPFrame_HTML_Text::limit_chars($row->lastname, 10); ?>
    </td>
    <td>
    	<?php echo $row->block; ?>
    </td>
    <td>
    	<?php echo $row->group_name; ?>
    </td>
    <td>
    	<a class="delete_user" title="<?php echo PHPFrame_HTML_Text::_($row->firstname.' '.$row->lastname, true); ?>" href="index.php?component=com_admin&action=remove_user&id=<?php echo $row->id; ?>">
    		<?php echo _LANG_DELETE; ?>
    	</a>
    </td>
  </tr>
  <?php $k = 1 - $k; ?>
  <?php endforeach; ?>
  </tbody>
</table>

<br />

<?php echo $data['page_nav']->getListFooter(); ?>

<?php else : ?>
<?php echo PHPFrame_HTML_Text::_( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>