<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_users
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

phpFrame_HTML::validate('userform');
?>


<h2 class="componentheading"><?php echo $this->page_title; ?></h2>

<form action="index.php" method="post" name="userform" id="userform" enctype="multipart/form-data">
	
<fieldset>
<legend><?php echo phpFrame_HTML_Text::_( _LANG_USER_GENERAL_SETTINGS ); ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
<tr>
	<td>
		<label for="username">
			<?php echo phpFrame_HTML_Text::_( _LANG_USERNAME ); ?>:
		</label>
	</td>
	<td>
		<input class="required" type="text" id="username" name="username" value="<?php echo $this->_user->get('username');?>" size="40" />
	</td>
</tr>
<tr>
	<td width="120">
		<label for="email">
			<?php echo phpFrame_HTML_Text::_( _LANG_EMAIL ); ?>:
		</label>
	</td>
	<td>
		<input class="required" type="text" id="email" name="email" value="<?php echo $this->_user->get('email');?>" size="40" />
	</td>
</tr>
<tr>
	<td>
		<label for="firstname">
			<?php echo phpFrame_HTML_Text::_( _LANG_FIRSTNAME ); ?>:
		</label>
	</td>
	<td>
		<input type="text" id="firstname" name="firstname" value="<?php echo $this->_user->get('firstname');?>" size="40" />
	</td>
</tr>
<tr>
	<td>
		<label for="lastname">
			<?php echo phpFrame_HTML_Text::_( _LANG_LASTNAME ); ?>:
		</label>
	</td>
	<td>
		<input type="text" id="lastname" name="lastname" value="<?php echo $this->_user->get('lastname');?>" size="40" />
	</td>
</tr>
<tr>
	<td>
		<label for="password">
			<?php echo phpFrame_HTML_Text::_( _LANG_PASSWORD ); ?>:
		</label>
	</td>
	<td>
		<input type="password" id="password" name="password" value="" size="40" />
	</td>
</tr>
<tr>
	<td>
		<label for="password2">
			<?php echo phpFrame_HTML_Text::_( _LANG_PASSWORD_VERIFY ); ?>:
		</label>
	</td>
	<td>
		<input type="password" id="password2" name="password2" size="40" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="photomsg" for="photo">
			<?php echo _LANG_USER_PHOTO; ?>:
		</label>
	</td>
	<td>
		<?php if (!empty($this->_user->photo)) : ?>
			<img src="<?php echo config::UPLOAD_DIR.'/users/'.$this->_user->photo; ?>" alt="photo" vspace="5" />
			<br />
		<?php endif; ?>
		<input type="file" name="photo" id="photo" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="notificationsmsg" for="notifications">
			<?php echo _LANG_USER_EMAIL_NOTIFICATIONS; ?>:
		</label>
	</td>
	<td>
		<select id="notifications" name="notifications">
			<option value="0" <?php if ($this->_user->notifications == 0) echo 'selected'; ?>>No</option>
			<option value="1" <?php if ($this->_user->notifications == 1) echo 'selected'; ?>>Yes</option>
		</select>
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="show_emailmsg" for="show_email">
			<?php echo _LANG_USER_SHOW_EMAIL; ?>:
		</label>
	</td>
	<td>
		<select id="show_email" name="show_email">
			<option value="0" <?php if ($this->_user->show_email == 0) echo 'selected'; ?>>No</option>
			<option value="1" <?php if ($this->_user->show_email == 1) echo 'selected'; ?>>Yes</option>
		</select>
	</td>
</tr>
</table>
</fieldset>

<div style="clear:both; margin-top:30px;"></div>

<?php if (phpFrame_Environment_Request::getVar('tmpl') != 'component') : ?>
<button type="button" onclick="Javascript:window.history.back();"><?php echo phpFrame_HTML_Text::_( _LANG_BACK ); ?></button>
<button type="submit"><?php echo phpFrame_HTML_Text::_(_LANG_SAVE); ?></button>
<?php endif; ?>

<input type="hidden" name="id" value="<?php echo $this->_user->get('id'); ?>" />
<input type="hidden" name="groupid" value="<?php echo $this->_user->get('groupid');?>" />
<input type="hidden" name="component" value="com_users" />
<input type="hidden" name="action" value="save_user" />
<input type="hidden" name="layout" value="" />
<?php echo phpFrame_HTML::_( 'form.token' ); ?>

</form>
