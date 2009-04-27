<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

// Load jQuery validation behaviour for form
phpFrame_HTML::validate('meetingsform');
?>

<h2 class="componentheading"><?php echo $this->page_heading; ?></h2>

<h2 class="subheading <?php echo strtolower($this->current_tool); ?>">
	<a href="<?php echo phpFrame_Application_Route::_('index.php?component=com_projects&view='.phpFrame_Environment_Request::getView().'&projectid='.$this->projectid); ?>">
		<?php echo $this->current_tool; ?>
	</a>
</h2>


<form action="index.php" method="post" id="meetingsform" name="meetingsform" enctype="multipart/form-data">

<fieldset>
<legend><?php echo phpFrame_HTML_Text::_( _LANG_MEETINGS_NEW ); ?></legend>
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
		<?php echo phpFrame_HTML::_('calendar', 'dtstart', 'dtstart', substr($this->row->dtstart, 0, 10), 'dd/mm/yy', array('class'=>'required', 'size'=>'10', 'maxlength'=>'10')); ?>
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="dtendmsg" for="dtend">
			<?php echo _LANG_END_DATE; ?>:
		</label>
	</td>
	<td>
		<?php echo phpFrame_HTML::_('calendar', 'dtend', 'dtend', substr($this->row->dtend, 0, 10), 'dd/mm/yy', array('class'=>'required', 'size'=>'10', 'maxlength'=>'10')); ?>
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
		<?php echo phpFrame_User_Helper::assignees($this->row->assignees, '', 'assignees[]', $this->projectid); ?>
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

<div style="clear:left; margin-top:30px;"></div>

<button type="button" onclick="Javascript:window.history.back();"><?php echo phpFrame_HTML_Text::_( _LANG_BACK ); ?></button>
<button type="submit"><?php echo phpFrame_HTML_Text::_(_LANG_SAVE); ?></button>

<input type="hidden" name="projectid" value="<?php echo $this->projectid; ?>" />
<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
<input type="hidden" name="component" value="com_projects" />
<input type="hidden" name="action" value="save_meeting" />
<input type="hidden" name="type" value="" />
<?php echo phpFrame_HTML::_( 'form.token' ); ?>

</form>