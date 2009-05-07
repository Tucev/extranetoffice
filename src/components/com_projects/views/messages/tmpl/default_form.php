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
phpFrame_HTML::validate('messagesform');
?>

<h2 class="componentheading"><?php echo $data['page_heading']; ?></h2>

<h2 class="subheading <?php echo strtolower($data['view']); ?>">
	<a href="<?php echo phpFrame_Application_Route::_('index.php?component=com_projects&action=get_messages&projectid='.$data['project']->id); ?>">
		<?php echo $data['view']; ?>
	</a>
</h2>

<form action="index.php" method="post" name="messagesform" id="messagesform" enctype="multipart/form-data">

<fieldset>
<legend><?php echo phpFrame_HTML_Text::_( _LANG_MESSAGES_NEW ); ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
<tr>
	<td width="30%">
		<label id="subjectmsg" for="subject">
			<?php echo _LANG_MESSAGES_SUBJECT; ?>:
		</label>
	</td>
	<td>
		<input class="required" type="text" id="subject" name="subject" size="32" maxlength="64" value="" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="bodymsg" for="body">
			<?php echo _LANG_MESSAGES_BODY; ?>:
		</label>
	</td>
	<td>
		<textarea class="required" id="body" name="body" cols="80" rows="10"></textarea>
	</td>
</tr>

<tr>
	<td>
		<label id="assigneesmsg" for="assignees">
			<?php echo _LANG_ASSIGNEES; ?>:
		</label>
	</td>
	<td>
		<?php echo phpFrame_User_Helper::assignees('', '', 'assignees[]', $data['project']->id); ?>
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

<input type="hidden" name="projectid" value="<?php echo $data['project']->id;?>" />
<input type="hidden" name="component" value="com_projects" />
<input type="hidden" name="action" value="save_message" />
<?php echo phpFrame_HTML::_( 'form.token' ); ?>

</form>