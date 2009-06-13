<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */
?>

<?php if (PHPFrame::Request()->get('tmpl') != 'component') : ?>
<h2 class="componentheading"><?php echo $data['page_title']; ?></h2>
<?php endif; ?>

<form action="index.php" method="post" name="iofficeform">

<fieldset>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
<tr>
<td>
	<label id="namemsg" for="name">
		<?php echo _LANG_NAME; ?>:
	</label>
</td>
<td>
	<?php echo $data['members'][0]->firstname." ".$data['members'][0]->lastname; ?>
</td>
</tr>
<tr>
<td>
	<label id="roleidmsg" for="roleid">
		<?php echo _LANG_PROJECTS_ROLE; ?>:
	</label>
</td>
<td>
	<?php echo projectsHelperProjects::project_role_select($data['members'][0]->roleid); ?>
</td>
</tr>
</table>

<?php if (PHPFrame::Request()->get('tmpl') != 'component') : ?>
<button type="submit" class="button">Change role</button>
<?php endif; ?>

</fieldset>

<input type="hidden" name="component" value="com_projects" />
<input type="hidden" name="action" value="change_member_role" />
<input type="hidden" name="projectid" value="<?php echo $data['project']->id; ?>" />
<input type="hidden" name="userid" value="<?php echo $data['members'][0]->userid; ?>" />
<?php echo PHPFrame_HTML::_( 'form.token' ); ?>

</form>
