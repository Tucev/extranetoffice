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
function submitbutton(action) {
	var form = document.iofficeform;

	// do field validation
	if (form.title.value == "") {
		alert('<?php echo text::_( _LANG_NAME_REQUIRED , true); ?>');
		form.title.focus();
		return;
	}
	else if (form.due_date.value == "") {
		alert('<?php echo text::_( _LANG_MILESTONES_DUEDATE_REQUIRED , true); ?>');
		form.due_date.focus();
		return;
	}
	
	form.submit();
}
</script>

<h2 class="componentheading"><?php echo $this->page_heading; ?></h2>

<h2 class="subheading <?php echo strtolower($this->current_tool); ?>">
	<a href="<?php echo route::_('index.php?option=com_projects&view='.request::getVar('view').'&projectid='.$this->projectid); ?>">
		<?php echo $this->current_tool; ?>
	</a>
</h2>


<form action="index.php" method="post" name="iofficeform">

<fieldset>
<legend><?php echo text::_( _LANG_MILESTONES_NEW ); ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
<tr>
	<td width="30%">
		<label id="titlemsg" for="title">
			<?php echo _LANG_NAME; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="title" name="title" size="32" maxlength="50" value="<?php echo htmlentities($this->row->title); ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="due_datemsg" for="due_date">
			<?php echo _LANG_MILESTONES_DUEDATE; ?>:
		</label>
	</td>
	<td>
		<?php echo html::_('calendar', 'due_date', 'due_date', $this->row->due_date, 'dd/mm/yy', array('size'=>'10',  'maxlength'=>'10')); ?>
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="descriptionmsg" for="description">
			<?php echo _LANG_DESCRIPTION; ?>:
		</label>
	</td>
	<td>
		<textarea name="description" id="description" cols="80"><?php echo $this->row->description; ?></textarea>
	</td>
</tr>

<tr>
	<td>
		<label id="assigneesmsg" for="assignees">
			<?php echo _LANG_ASSIGNEES; ?>:
		</label>
	</td>
	<td>
		<?php echo usersHelper::assignees($this->row->assignees, '', 'assignees[]', $this->projectid); ?>
	</td>
</tr>
<tr>
	<td>
		<label id="notifymsg" for="notify">
			<?php echo _LANG_NOTIFY_ASSIGNEES; ?>:
		</label>
	</td>
	<td>
		<input type="checkbox" name="notify" checked />
	</td>
</tr>

<?php if (!empty($this->row->id)) : ?>
<tr>
	<td width="30%">
		<label id="created_bymsg" for="created_by">
			<?php echo _LANG_CREATED_BY; ?>:
		</label>
	</td>
	<td>
		<?php echo usersHelper::id2name($this->row->created_by); ?>
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="createdmsg" for="created">
			<?php echo _LANG_CREATED; ?>:
		</label>
	</td>
	<td>
		<?php echo $this->row->created; ?>
	</td>
</tr>
<?php endif; ?>
</table>
</fieldset>

<div style="clear:left; margin-top:30px;"></div>

<button type="button" onclick="Javascript:window.history.back();"><?php echo text::_( _LANG_BACK ); ?></button>
<button type="submit"><?php echo text::_(_LANG_SAVE); ?></button>

<input type="hidden" name="projectid" value="<?php echo $this->projectid;?>" />
<input type="hidden" name="id" value="<?php echo $this->row->id;?>" />
<input type="hidden" name="option" value="com_projects" />
<input type="hidden" name="task" value="save_milestone" />
<input type="hidden" name="layout" value="" />
<?php echo html::_( 'form.token' ); ?>

</form>