<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

// Load jQuery validation behaviour for form
PHPFrame_HTML::validate('meetingsform');
?>

<h2 class="componentheading"><?php echo $data['page_heading']; ?></h2>

<h2 class="subheading <?php echo strtolower($data['view']); ?>">
	<a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL('index.php?component=com_projects&action=get_meetings&projectid='.$data['project']->id); ?>">
		<?php echo $data['view']; ?>
	</a>
</h2>


<form action="index.php" method="post" id="meetingsform" name="meetingsform" enctype="multipart/form-data">

<fieldset>
<legend><?php echo PHPFrame_Base_String::html( $data['action'] ); ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
<tr>
	<td width="30%">
		<label id="namemsg" for="name">
			<?php echo _LANG_NAME; ?>:
		</label>
	</td>
	<td>
		<input class="required" type="text" id="name" name="name" size="32" maxlength="64" value="<?php echo $data['row']->name; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="dtstartmsg" for="dtstart">
			<?php echo _LANG_START_DATE; ?>:
		</label>
	</td>
	<td>
		<?php echo PHPFrame_HTML::_('calendar', 'dtstart', 'dtstart', substr($data['row']->dtstart, 0, 10), 'dd/mm/yy', array('class'=>'required', 'size'=>'10', 'maxlength'=>'10')); ?>
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="dtendmsg" for="dtend">
			<?php echo _LANG_END_DATE; ?>:
		</label>
	</td>
	<td>
		<?php echo PHPFrame_HTML::_('calendar', 'dtend', 'dtend', substr($data['row']->dtend, 0, 10), 'dd/mm/yy', array('class'=>'required', 'size'=>'10', 'maxlength'=>'10')); ?>
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="descriptionmsg" for="description">
			<?php echo _LANG_DESCRIPTION; ?>:
		</label>
	</td>
	<td>
		<textarea name="description" id="description" cols="80"><?php echo $data['row']->description; ?></textarea>
	</td>
</tr>

<tr>
	<td>
		<label id="assigneesmsg" for="assignees">
			<?php echo _LANG_ASSIGNEES; ?>:
		</label>
	</td>
	<td>
		<?php echo PHPFrame_User_Helper::assignees($data['row']->assignees, '', 'assignees[]', $data['project']->id); ?>
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

<button type="button" onclick="Javascript:window.history.back();"><?php echo PHPFrame_Base_String::html( _LANG_BACK ); ?></button>
<button type="submit"><?php echo PHPFrame_Base_String::html(_LANG_SAVE); ?></button>

<input type="hidden" name="projectid" value="<?php echo $data['project']->id; ?>" />
<input type="hidden" name="id" value="<?php echo $data['row']->id; ?>" />
<input type="hidden" name="component" value="com_projects" />
<input type="hidden" name="action" value="save_meeting" />
<?php echo PHPFrame_HTML::_( 'form.token' ); ?>

</form>