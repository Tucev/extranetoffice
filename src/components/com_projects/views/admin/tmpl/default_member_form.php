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
	var form = document.newmemberform;	
	
	// do field validation
	if (form.roleid.value == '0') {
		alert('<?php echo text::_(_LANG_PROJECTS_ROLE_REQUIRED, true); ?>');
		form.roleid.focus();
	}
	else if (form.userids.value == '' && form.email.value == '') {
		alert('<?php echo text::_(_LANG_ADMIN_USER_REQUIRED, true); ?>');
		form.email.focus();
	}
	else if (form.email.value != '' && form.username.value == '') {
		alert('<?php echo text::_(_LANG_ADMIN_NEW_USERNAME_REQUIRED, true); ?>');
		form.username.focus();
	}
	else if (form.email.value != '' && form.firstname.value == '') {
		alert('<?php echo text::_(_LANG_ADMIN_NAME_REQUIRED, true); ?>');
		form.firstname.focus();
	}
	else if (form.email.value != '' && form.groupid.value == '') {
		alert('<?php echo text::_(_LANG_ADMIN_NAME_REQUIRED, true); ?>');
		form.groupid.focus();
	}
	else {
		form.submit();
	}
}
</script>

<h2 class="componentheading"><?php echo $this->page_heading; ?></h2>

<h2 class="subheading people">
	<a href="<?php echo route::_('index.php?option=com_projects&view='.request::getVar('view').'&projectid='.$this->projectid); ?>">
		<?php echo _LANG_PEOPLE; ?>
	</a>
</h2>


<form action="index.php" method="post" name="newmemberform">
	
	<fieldset>
		<legend><?php echo text::_( _LANG_PROJECTS_ADD_EXISTING_MEMBER ); ?></legend>
		
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
		<tr>
			<td width="30%">
				<label id="useridsmsg" for="userids">
					Select existing users by username:
				</label>
			</td>
			<td>
				<?php echo projectsHelperProjects::autocompleteMembers($this->projectid, false); ?> 
				<!-- This should be added in a tooltip 
				<div class="note">
					Start typing usernames to display autocompleter. Note that the autocompleter will only
					list users that are not yet members of this project.
				</div>
				-->
			</td>
		</tr>
		</table>
		
	</fieldset>
	
	<div style="padding:10px 5px 5px 5px; font-size: 1.4em;">or</div>
	
	<fieldset>
		<legend><?php echo text::_( _LANG_PROJECTS_INVITE_NEW_MEMBER ); ?></legend>
		
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
		<tr>
			<td width="30%">
				<label id="usernamemsg" for="username">
					<?php echo _LANG_USERNAME; ?>:
				</label>
			</td>
			<td>
				<input type="text" id="username" name="username" size="32" maxlength="64" value="" />
			</td>
		</tr>
		<tr>
			<td width="30%">
				<label id="emailmsg" for="email">
					<?php echo _LANG_EMAIL; ?>:
				</label>
			</td>
			<td>
				<input type="text" id="email" name="email" size="32" maxlength="128" value="" />
			</td>
		</tr>
		<tr>
			<td width="30%">
				<label id="firstnamemsg" for="firstname">
					<?php echo _LANG_FIRSTNAME; ?>:
				</label>
			</td>
			<td>
				<input type="text" id="firstname" name="firstname" size="32" maxlength="64" value="" />
			</td>
		</tr>
		<tr>
			<td width="30%">
				<label id="lastnamemsg" for="lastname">
					<?php echo _LANG_LASTNAME; ?>:
				</label>
			</td>
			<td>
				<input type="text" id="lastname" name="lastname" size="32" maxlength="64" value="" />
			</td>
		</tr>
		<tr>
			<td><?php echo text::_( _LANG_GROUP ); ?></td>
			<td><?php echo usersHelper::selectGroup(); ?></td>
		</tr>
		</table>
	</fieldset>
	
	<br />
	
	<label id="roleidmsg" for="roleid">
		<?php echo _LANG_PROJECTS_ROLE; ?>:
	</label> 
	<?php echo projectsHelperProjects::project_role_select(); ?>
	
	<div style="clear:left; margin-top:30px;"></div>
	
	<button type="button" onclick="Javascript:window.history.back();"><?php echo text::_( _LANG_BACK ); ?></button>
	<button type="submit"><?php echo text::_(_LANG_SAVE); ?></button>
	
	<input type="hidden" name="option" value="com_projects" />
	<input type="hidden" name="task" value="save_member" />
	<input type="hidden" name="view" value="admin" />
	<input type="hidden" name="projectid" value="<?php echo $this->projectid;?>" />
	<?php html::_( 'form.token' ); ?>
	
</form>
