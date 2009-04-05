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

phpFrame_HTML::confirm('delete_user', _LANG_ADMIN_USERS_DELETE, _LANG_ADMIN_USERS_DELETE_CONFIRM, "div[id^='ui-tabs']:has(a.delete_user)");
?>

<h2 class="componentheading"><?php echo $this->page_title; ?></h2>


<?php if ($this->user->groupid == 1) : ?>
<div class="new">
	<?php phpFrame_HTML::dialog(_LANG_ADMIN_USERS_NEW, 'index.php?option=com_admin&view=users&layout=form', 550, 390, true, "div[id^='ui-tabs']:has(a.delete_user)"); ?>
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
<input type="hidden" name="option" value="com_admin" />
<input type="hidden" name="view" value="users" />
<input type="hidden" name="layout" value="list" />
</form>
</div>

<br />

<?php if (is_array($this->rows) && count($this->rows) > 0) : ?>
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
  <?php foreach($this->rows as $row) : ?>
  <tr class="row<?php echo $k; ?>">
  	<td>
  		<input type="checkbox" name="ids[]" value="<?php echo $row->id; ?>" />
  	</td>
    <td valign="top">
    <?php phpFrame_HTML::dialog($row->username, 'index.php?option=com_admin&view=users&layout=form&userid='.$row->id, 550, 390, true, "div[id^='ui-tabs']:has(a.delete_user)"); ?>
    </td>
    <td>
    	<?php echo $row->email; ?>
    </td>
    <td>
    	<?php echo $row->firstname; ?>
    </td>
    <td>
    	<?php echo $row->lastname; ?>
    </td>
    <td>
    	<?php echo $row->block; ?>
    </td>
    <td>
    	<?php echo $row->group_name; ?>
    </td>
    <td>
    	<a class="delete_user" title="<?php echo phpFrame_HTML_Text::_($row->firstname.' '.$row->lastname, true); ?>" href="index.php?option=com_admin&task=remove_user&id=<?php echo $row->id; ?>">
    		<?php echo _LANG_DELETE; ?>
    	</a>
    </td>
  </tr>
  <?php $k = 1 - $k; ?>
  <?php endforeach; ?>
  </tbody>
</table>

<br />

<?php echo $this->pageNav->getListFooter(); ?>

<?php else : ?>
<?php echo phpFrame_HTML_Text::_( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>