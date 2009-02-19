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
	var form = document.iofficenewmemberform;	
	
	// do field validation
	if (form.roleid.value == '0') {
		alert('<?php echo text::_("_LANG_PROJECTS_ROLE_REQUIRED", true); ?>');
		form.roleid.focus();
	}
	else if (form.username.value == '' && form.invite_member_email.value == '') {
		alert('<?php echo text::_("_LANG_ADMIN_USER_REQUIRED", true); ?>');
		form.username.focus();
	}
	else if (form.invite_member_email.value != '' && form.name.value == '') {
		alert('<?php echo text::_("_LANG_ADMIN_NAME_REQUIRED", true); ?>');
		form.name.focus();
	}
	else if (form.invite_member_email.value != '' && form.new_username.value == '') {
		alert('<?php echo text::_("_LANG_ADMIN_NEW_USERNAME_REQUIRED", true); ?>');
		form.new_username.focus();
	}
	else {
		form.submit();
	}
}
</script>

<h2 class="componentheading"><?php echo $this->page_heading; ?></h2>

<h2 class="subheading people">
	<a href="<?php echo route::_('index.php?option=com_projects&view=projects&layout='.$this->current_tool.'&projectid='.$this->projectid); ?>">
		<?php echo _LANG_PEOPLE; ?>
	</a>
</h2>


<form action="index.php" method="post" name="iofficenewmemberform">
	
	<fieldset class="josform">
		<legend><?php echo text::_( _LANG_PROJECTS_ADD_EXISTING_MEMBER ); ?></legend>
		Select existing users by username: <?php echo usersHelperUsers::autocompleteUsername('iofficenewmemberform'); ?>
	</fieldset>
	
	<div style="padding:10px 5px 5px 5px; font-size: 1.4em;">or</div>
	
	<fieldset class="josform">
		<legend><?php echo text::_( _LANG_PROJECTS_INVITE_NEW_MEMBER ); ?></legend>
		
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="ioffice_edit">
		<tr>
			<td width="30%">
				<label id="namemsg" for="name">
					<?php echo _LANG_USERS_NAME; ?>:
				</label>
			</td>
			<td>
				<input class="inputbox" type="text" id="name" name="name" size="32" maxlength="64" value="" />
			</td>
		</tr>
		<tr>
			<td width="30%">
				<label id="new_usernamemsg" for="new_username">
					<?php echo _LANG_USERS_USERNAME; ?>:
				</label>
			</td>
			<td>
				<input class="inputbox" type="text" id="new_username" name="new_username" size="32" maxlength="64" value="" />
			</td>
		</tr>
		<tr>
			<td width="30%">
				<label id="invite_member_emailmsg" for="invite_member_email">
					<?php echo _LANG_USERS_EMAIL; ?>:
				</label>
			</td>
			<td>
				<input class="inputbox" type="text" id="invite_member_email" name="invite_member_email" size="32" maxlength="128" value="" />
			</td>
		</tr>
		</table>
	</fieldset>
	
	<br />
	
	<label id="roleidmsg" for="roleid">
		<?php echo _LANG_PROJECTS_ROLE; ?>:
	</label> 
	<?php echo projectsHelperProjects::project_role_select($this->members[0]->roleid); ?>
	
	<div style="clear:both; margin-top:30px;"></div>
	
	<button class="button" type="button" onclick="window.history.back();"><?php echo text::_('Cancel'); ?></button>
	<button class="button" type="button" onclick="submitbutton(); return false;"><?php echo text::_('Save'); ?></button>
	
	<input type="hidden" name="option" value="com_projects" />
	<input type="hidden" name="task" value="save_member" />
	<input type="hidden" name="view" value="projects" />
	<input type="hidden" name="type" value="admin" />
	<input type="hidden" name="projectid" value="<?php echo $this->projectid;?>" />
	<input type="hidden" name="userid" value="<?php echo $this->members[0]->userid;?>" />
	<?php echo html::_( 'form.token' ); ?>
	
</form>
