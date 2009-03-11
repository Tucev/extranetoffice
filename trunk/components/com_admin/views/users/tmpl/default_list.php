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



<h2 class="componentheading"><?php echo $this->page_title; ?></h2>


<?php if ($this->user->groupid == 1) : ?>
<div class="new">
	<?php html::dialog(_LANG_ADMIN_USERS_NEW, 'index.php?option=com_admin&view=users&layout=form', 460, 390, true); ?>
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
    <a href="index.php?option=com_admin&amp;view=users&amp;layout=form&amp;userid=<?php echo $row->id; ?>">
	<?php echo $row->username; ?>
	</a>
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
  </tr>
  <?php $k = 1 - $k; ?>
  <?php endforeach; ?>
  </tbody>
</table>

<br />

<?php echo $this->pageNav->getListFooter(); ?>

<?php else : ?>
<?php echo text::_( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>