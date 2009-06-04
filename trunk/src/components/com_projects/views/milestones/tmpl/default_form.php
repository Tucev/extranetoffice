<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

// Load jQuery validation behaviour for form
phpFrame_HTML::validate('milestonesform');
?>

<h2 class="componentheading"><?php echo $data['page_heading']; ?></h2>

<h2 class="subheading <?php echo strtolower($data['view']); ?>">
	<a href="<?php echo phpFrame_Utils_Rewrite::rewriteURL('index.php?component=com_projects&action=get_milestones&projectid='.$data['project']->id); ?>">
		<?php echo $data['view']; ?>
	</a>
</h2>


<form action="index.php" method="post" name="milestonesform" id="milestonesform">

<fieldset>
<legend><?php echo phpFrame_HTML_Text::_( $data['action'] ); ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
<tr>
	<td width="30%">
		<label id="titlemsg" for="title">
			<?php echo _LANG_NAME; ?>:
		</label>
	</td>
	<td>
		<input class="required" type="text" id="title" name="title" size="32" maxlength="50" value="<?php echo htmlentities($data['row']->title); ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="due_datemsg" for="due_date">
			<?php echo _LANG_MILESTONES_DUEDATE; ?>:
		</label>
	</td>
	<td>
		<?php echo phpFrame_HTML::_('calendar', 'due_date', 'due_date', $data['row']->due_date, 'dd/mm/yy', array('size'=>'10', 'maxlength'=>'10', 'class'=>'required')); ?>
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
		<?php echo phpFrame_User_Helper::assignees($data['row']->assignees, '', 'assignees[]', $data['project']->id); ?>
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

<?php if (!empty($data['row']->id)) : ?>
<tr>
	<td width="30%">
		<label id="created_bymsg" for="created_by">
			<?php echo _LANG_CREATED_BY; ?>:
		</label>
	</td>
	<td>
		<?php echo phpFrame_User_Helper::id2name($data['row']->created_by); ?>
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="createdmsg" for="created">
			<?php echo _LANG_CREATED; ?>:
		</label>
	</td>
	<td>
		<?php echo $data['row']->created; ?>
	</td>
</tr>
<?php endif; ?>
</table>
</fieldset>

<div style="clear:left; margin-top:30px;"></div>

<button type="button" onclick="Javascript:window.history.back();"><?php echo phpFrame_HTML_Text::_( _LANG_BACK ); ?></button>
<button type="submit"><?php echo phpFrame_HTML_Text::_(_LANG_SAVE); ?></button>

<input type="hidden" name="projectid" value="<?php echo $data['project']->id;?>" />
<input type="hidden" name="id" value="<?php echo $data['row']->id;?>" />
<input type="hidden" name="component" value="com_projects" />
<input type="hidden" name="action" value="save_milestone" />
<?php echo phpFrame_HTML::_( 'form.token' ); ?>

</form>