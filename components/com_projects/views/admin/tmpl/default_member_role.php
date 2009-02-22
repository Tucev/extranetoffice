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

<?php if (request::getVar('task', '') == 'admin_change_member_role') : ?>
<script language="javascript" type="text/javascript">
window.parent.location = "index.php?option=com_projects&amp;view=projects&amp;layout=admin&projectid=<?php echo $this->projectid; ?>";
window.top.setTimeout('window.parent.document.getElementById(\'sbox-window\').close()', 3000);
</script>
<?php endif; ?>

<script language="javascript" type="text/javascript">
function submitbutton() {
	var form = document.iofficeform;
	
	// do field validation
	
	form.submit();
}
</script>

<form action="index.php" method="post" name="iofficeform">

<fieldset>
<legend><?php echo $this->page_title; ?></legend>

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
<tr>
<td>
	<label id="namemsg" for="name">
		<?php echo _LANG_USERS_NAME; ?>:
	</label>
</td>
<td>
	<?php echo $this->members[0]->name; ?>
</td>
</tr>
<tr>
<td>
	<label id="roleidmsg" for="roleid">
		<?php echo _LANG_PROJECTS_ROLE; ?>:
	</label>
</td>
<td>
	<?php echo projectsHelperProjects::project_role_select($this->members[0]->roleid); ?>
</td>
</tr>
</table>

<button type="button" class="button" onclick="window.parent.document.getElementById('sbox-window').close();">
	Cancel
</button>

<button type="button" class="button" onclick="submitbutton();">
	Change role
</button>


</fieldset>

<input type="hidden" name="option" value="com_projects" />
<input type="hidden" name="task" value="admin_change_member_role" />
<input type="hidden" name="view" value="projects" />
<input type="hidden" name="type" value="admin_member_role" />
<input type="hidden" name="tmpl" value="component" />
<input type="hidden" name="projectid" value="<?php echo $this->projectid; ?>" />
<input type="hidden" name="userid" value="<?php echo $this->userid; ?>" />
<?php echo html::_( 'form.token' ); ?>

</form>
