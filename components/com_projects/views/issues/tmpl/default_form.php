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
<legend><?php echo empty($this->issueid) ? _LANG_ISSUES_NEW : _LANG_ISSUES_EDIT; ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
<tr>
	<td width="30%">
		<label id="titlemsg" for="title">
			<?php echo _LANG_NAME; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="title" name="title" size="32" maxlength="128" value="<?php echo htmlentities($this->row->title); ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="descriptionmsg" for="description">
			<?php echo _LANG_PROJECTS_DESCRIPTION; ?>:
		</label>
	</td>
	<td>
		<textarea id="description" name="description" cols="80"><?php echo $this->row->description; ?></textarea> 
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="issue_typemsg" for="issue_type">
			<?php echo _LANG_ISSUES_TYPE; ?>:
		</label>
	</td>
	<td>
		<?php echo projectsHelperProjects::issue_type_select($this->row->issue_type); ?>
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="prioritymsg" for="priority">
			<?php echo _LANG_PROJECTS_PRIORITY; ?>:
		</label>
	</td>
	<td>
		<?php echo projectsHelperProjects::priority_select($this->row->priority); ?>
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="dtstartmsg" for="dtstart">
			<?php echo _LANG_DTSTART; ?>:
		</label>
	</td>
	<td>
		<?php echo html::_('calendar', 'dtstart', 'dtstart', $this->row->dtstart, 'dd/mm/yy', array('size'=>'10',  'maxlength'=>'10')); ?>
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="dtendmsg" for="dtend">
			<?php echo _LANG_DTEND; ?>:
		</label>
	</td>
	<td>
		<?php echo html::_('calendar', 'dtend', 'dtend', $this->row->dtend, 'dd/mm/yy', array('size'=>'10',  'maxlength'=>'10')); ?>
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="expected_durationmsg" for="expected_duration">
			<?php echo _LANG_ISSUES_EXPECTED_DURATION; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="expected_duration" name="expected_duration" size="9" maxlength="9" value="<?php echo $this->row->expected_duration; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="accessmsg" for="access">
			<?php echo _LANG_PROJECTS_ACCESS; ?>:
		</label>
	</td>
	<td>
		<?php echo projectsHelperProjects::global_access_select('access', $this->row->access); ?>
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

<div style="clear:both; margin-top:30px;"></div>

<button type="button" onclick="Javascript:window.history.back();"><?php echo text::_( _LANG_BACK ); ?></button>
<button type="submit"><?php echo text::_(_LANG_SAVE); ?></button>

<input type="hidden" name="projectid" value="<?php echo $this->projectid;?>" />
<input type="hidden" name="id" value="<?php echo $this->row->id;?>" />
<input type="hidden" name="option" value="com_projects" />
<input type="hidden" name="task" value="save_issue" />
<input type="hidden" name="type" value="" />
<?php echo html::_( 'form.token' ); ?>

</form>