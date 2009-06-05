<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

// Load jQuery validation behaviour for form
PHPFrame_HTML::validate('projectsform');
?>

<!-- jquery slider for show/hide project permissions -->
<script language="javascript" type="text/javascript">
$(document).ready(function() {
	$('#project_permissions').hide();
	
	$('a#toggle').click(function() {
		$('#project_permissions').slideToggle('normal');
		return false;
  	});
});
</script>

<h2 class="componentheading"><?php echo $data['page_title']; ?></h2>


<form action="index.php" method="post" id="projectsform" name="projectsform">

<fieldset>
<legend><?php echo PHPFrame_HTML_Text::_( _LANG_PROJECTS_DETAILS ); ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
<tr>
	<td width="30%">
		<label id="namemsg" for="name">
			<?php echo _LANG_PROJECTS_NAME; ?>:
		</label>
	</td>
	<td>
		<input class="required" type="text" id="name" name="name" size="32" maxlength="128" value="<?php echo $data['project']->name; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="descriptionmsg" for="description">
			<?php echo _LANG_DESCRIPTION; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="description" name="description" size="80" maxlength="255" value="<?php echo $data['project']->description; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="project_typemsg" for="project_type">
			<?php echo _LANG_PROJECTS_PROJECT_TYPE; ?>:
		</label>
	</td>
	<td>
		<?php echo projectsHelperProjects::project_type_select($data['project']->project_type); ?>
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="prioritymsg" for="priority">
			<?php echo _LANG_PROJECTS_PRIORITY; ?>:
		</label>
	</td>
	<td>
		<?php echo projectsHelperProjects::priority_select($data['project']->priority); ?>
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="statusmsg" for="status">
			<?php echo _LANG_PROJECTS_STATUS; ?>:
		</label>
	</td>
	<td>
		<?php echo projectsHelperProjects::status_select($data['project']->status); ?>
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="accessmsg" for="access">
			<?php echo _LANG_PROJECTS_ACCESS; ?>:
		</label>
	</td>
	<td>
		<?php echo projectsHelperProjects::global_access_select('access', $data['project']->access); ?>
	</td>
</tr>

<?php if (!empty($data['project']->id)) : ?>
<tr>
	<td width="30%">
		<label id="created_bymsg" for="created_by">
			<?php echo _LANG_CREATED_BY; ?>:
		</label>
	</td>
	<td>
		<?php echo PHPFrame_User_Helper::id2name($data['project']->created_by); ?>
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="createdmsg" for="created">
			<?php echo _LANG_CREATED; ?>:
		</label>
	</td>
	<td>
		<?php echo $data['project']->created; ?>
	</td>
</tr>
<?php endif; ?>

</table>
</fieldset>

<br />
<a id="toggle" href="#">Show/Hide advanced access settings</a>

<div id="project_permissions">

<fieldset>
<legend><?php echo PHPFrame_HTML_Text::_( _LANG_PROJECTS_ADVANCED_ACCESS_SETTINGS ); ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
<tr>
	<td width="30%">
		<label id="access_issuesmsg" for="access_issues">
			<?php echo _LANG_PROJECTS_ACCESS_ISSUES; ?>:
		</label>
	</td>
	<td>
		<?php echo projectsHelperProjects::access_select('access_issues', $data['project']->access_issues); ?>
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="access_messagesmsg" for="access_messages">
			<?php echo _LANG_PROJECTS_ACCESS_MESSAGES; ?>:
		</label>
	</td>
	<td>
		<?php echo projectsHelperProjects::access_select('access_messages', $data['project']->access_messages); ?>
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="access_milestonesmsg" for="access_milestones">
			<?php echo _LANG_PROJECTS_ACCESS_MILESTONES; ?>:
		</label>
	</td>
	<td>
		<?php echo projectsHelperProjects::access_select('access_milestones', $data['project']->access_milestones); ?>
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="access_filesmsg" for="access_files">
			<?php echo _LANG_PROJECTS_ACCESS_FILES; ?>:
		</label>
	</td>
	<td>
		<?php echo projectsHelperProjects::access_select('access_files', $data['project']->access_files); ?>
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="access_meetingsmsg" for="access_meetings">
			<?php echo _LANG_PROJECTS_ACCESS_MEETINGS; ?>:
		</label>
	</td>
	<td>
		<?php echo projectsHelperProjects::access_select('access_meetings', $data['project']->access_meetings); ?>
	</td>
</tr>
<!-- 
<tr>
	<td width="30%">
		<label id="access_pollsmsg" for="access_polls">
			<?php echo _LANG_PROJECTS_ACCESS_POLLS; ?>:
		</label>
	</td>
	<td>
		<?php echo projectsHelperProjects::access_select('access_polls', $data['project']->access_polls); ?>
	</td>
</tr>
 -->
<tr>
	<td width="30%">
		<label id="access_peoplemsg" for="access_people">
			<?php echo _LANG_PROJECTS_ACCESS_PEOPLE; ?>:
		</label>
	</td>
	<td>
		<?php echo projectsHelperProjects::access_select('access_people', $data['project']->access_people); ?>
	</td>
</tr>
<!-- 
<tr>
	<td width="30%">
		<label id="access_reportsmsg" for="access_reports">
			<?php echo _LANG_PROJECTS_ACCESS_REPORTS; ?>:
		</label>
	</td>
	<td>
		<?php echo projectsHelperProjects::access_select('access_reports', $data['project']->access_reports); ?>
	</td>
</tr>
 -->
<tr>
	<td width="30%">
		<label id="access_adminmsg" for="access_admin">
			<?php echo _LANG_PROJECTS_ACCESS_ADMIN; ?>:
		</label>
	</td>
	<td>
		<?php echo projectsHelperProjects::access_select('access_admin', $data['project']->access_admin); ?>
	</td>
</tr>
</table>
</fieldset>
</div><!-- close #project_permissions -->


<div style="clear:left; margin-top:30px;"></div>

<button type="button" onclick="window.history.back();"><?php echo PHPFrame_HTML_Text::_( _LANG_BACK ); ?></button>
<button type="submit"><?php echo PHPFrame_HTML_Text::_(_LANG_SAVE); ?></button>

<input type="hidden" name="id" value="<?php echo $data['project']->id;?>" />
<input type="hidden" name="component" value="com_projects" />
<input type="hidden" name="action" value="save_project" />
<input type="hidden" name="layout" value="" />
<?php echo PHPFrame_HTML::_( 'form.token' ); ?>

</form>