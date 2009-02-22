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
function submitbutton() {
	var form = document.iofficeform;

	// do field validation
	if (form.subject.value == "") {
		alert('<?php echo text::_( _LANG_MESSAGES_SUBJECT_REQUIRED , true); ?>');
		form.subject.focus();
		return;
	}
	else if (form.body.value == "") {
		alert('<?php echo text::_( _LANG_MESSAGES_BODY_REQUIRED , true); ?>');
		form.body.focus();
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

<form action="index.php" method="post" name="iofficeform" enctype="multipart/form-data">

<fieldset class="josform">
<legend><?php echo text::_( _LANG_MESSAGES_NEW ); ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
<tr>
	<td width="30%">
		<label id="subjectmsg" for="subject">
			<?php echo _LANG_MESSAGES_SUBJECT; ?>:
		</label>
	</td>
	<td>
		<input class="inputbox" type="text" id="subject" name="subject" size="32" maxlength="64" value="<?php echo $this->subject; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="bodymsg" for="body">
			<?php echo _LANG_MESSAGES_BODY; ?>:
		</label>
	</td>
	<td>
		<textarea class="inputbox" id="body" name="body" cols="80" rows="10"><?php echo $this->body; ?></textarea>
	</td>
</tr>

<tr>
	<td>
		<label id="assigneesmsg" for="assignees">
			<?php echo _LANG_ASSIGNEES; ?>:
		</label>
	</td>
	<td>
		<?php echo usersHelperUsers::assignees($this->tracker->assignees, '', 'assignees[]', $this->projectid); ?>
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

<button class="button" type="button" onclick="Javascript:window.history.back();"><?php echo text::_( _LANG_BACK ); ?></button> 	
<button class="button" type="button" onclick="submitbutton();return false;"><?php echo text::_('Save'); ?></button>

<input type="hidden" name="projectid" value="<?php echo $this->projectid;?>" />
<input type="hidden" name="option" value="com_projects" />
<input type="hidden" name="task" value="save_message" />
<input type="hidden" name="type" value="" />
<?php echo html::_( 'form.token' ); ?>

</form>