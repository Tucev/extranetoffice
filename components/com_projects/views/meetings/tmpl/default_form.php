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

// Load jQuery validation behaviour for form
html::validate('meetingsform');
?>

<script language="javascript" type="text/javascript">
function submitbutton() {
	var form = document.meetingsform;

	// do field validation
	if (form.name.value == "") {
		alert('<?php echo text::_( _LANG_MEETINGS_NAME_REQUIRED , true); ?>');
		form.name.focus();
		return;
	}
	else if (form.dtstart.value == "") {
		alert('<?php echo text::_( _LANG_MEETINGS_DTSTART_REQUIRED , true); ?>');
		form.dtstart.focus();
		return;
	}
	else if (form.dtend.value == "") {
		alert('<?php echo text::_( _LANG_MEETINGS_DTEND_REQUIRED , true); ?>');
		form.dtend.focus();
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


<form action="index.php" method="post" id="meetingsform" name="meetingsform" enctype="multipart/form-data">

<fieldset>
<legend><?php echo text::_( _LANG_MEETINGS_NEW ); ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
<tr>
	<td width="30%">
		<label id="namemsg" for="name">
			<?php echo _LANG_NAME; ?>:
		</label>
	</td>
	<td>
		<input class="required" type="text" id="name" name="name" size="32" maxlength="64" value="<?php echo $this->row->name; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="dtstartmsg" for="dtstart">
			<?php echo _LANG_START_DATE; ?>:
		</label>
	</td>
	<td>
		<?php echo html::_('calendar', 'dtstart', 'dtstart', $this->row->dtstart, 'dd/mm/yy', array('class'=>'required', 'size'=>'10', 'maxlength'=>'10')); ?>
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="dtendmsg" for="dtend">
			<?php echo _LANG_END_DATE; ?>:
		</label>
	</td>
	<td>
		<?php echo html::_('calendar', 'dtend', 'dtend', $this->row->dtend, 'dd/mm/yy', array('class'=>'required', 'size'=>'10', 'maxlength'=>'10')); ?>
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
</table>
</fieldset>

<div style="clear:both; margin-top:30px;"></div>

<button type="button" onclick="Javascript:window.history.back();"><?php echo text::_( _LANG_BACK ); ?></button>
<button type="submit"><?php echo text::_(_LANG_SAVE); ?></button>

<input type="hidden" name="projectid" value="<?php echo $this->projectid;?>" />
<input type="hidden" name="id" value="<?php echo $this->row->id;?>" />
<input type="hidden" name="option" value="com_projects" />
<input type="hidden" name="task" value="save_meeting" />
<input type="hidden" name="type" value="" />
<?php echo html::_( 'form.token' ); ?>

</form>