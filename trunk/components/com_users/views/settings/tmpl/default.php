<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_users
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

html::validate('userform');
?>


<h2 class="componentheading"><?php echo $this->page_title; ?></h2>

<form action="index.php" method="post" name="userform" id="userform" enctype="multipart/form-data">
	
<fieldset>
<legend><?php echo text::_( _LANG_USER_GENERAL_SETTINGS ); ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
<tr>
	<td>
		<label for="username">
			<?php echo text::_( _LANG_USERNAME ); ?>:
		</label>
	</td>
	<td>
		<span><?php echo $this->user->get('username');?></span>
	</td>
</tr>
<tr>
	<td width="120">
		<label for="email">
			<?php echo text::_( _LANG_EMAIL ); ?>:
		</label>
	</td>
	<td>
		<input type="text" id="email" name="email" value="<?php echo $this->user->get('email');?>" size="40" />
	</td>
</tr>
<tr>
	<td>
		<label for="firstname">
			<?php echo text::_( _LANG_FIRSTNAME ); ?>:
		</label>
	</td>
	<td>
		<input type="text" id="firstname" name="firstname" value="<?php echo $this->user->get('firstname');?>" size="40" />
	</td>
</tr>
<tr>
	<td>
		<label for="lastname">
			<?php echo text::_( _LANG_LASTNAME ); ?>:
		</label>
	</td>
	<td>
		<input type="text" id="lastname" name="lastname" value="<?php echo $this->user->get('lastname');?>" size="40" />
	</td>
</tr>
<tr>
	<td>
		<label for="password">
			<?php echo text::_( _LANG_PASSWORD ); ?>:
		</label>
	</td>
	<td>
		<input type="password" id="password" name="password" value="" size="40" />
	</td>
</tr>
<tr>
	<td>
		<label for="password2">
			<?php echo text::_( _LANG_PASSWORD_VERIFY ); ?>:
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
		<?php if (!empty($this->user->photo)) : ?>
			<img src="<?php echo $this->config->get('upload_dir').'/users/'.$this->user->photo; ?>" alt="photo" vspace="5" />
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
			<option value="0" <?php if ($this->user->notifications == 0) echo 'selected'; ?>>No</option>
			<option value="1" <?php if ($this->user->notifications == 1) echo 'selected'; ?>>Yes</option>
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
			<option value="0" <?php if ($this->user->show_email == 0) echo 'selected'; ?>>No</option>
			<option value="1" <?php if ($this->user->show_email == 1) echo 'selected'; ?>>Yes</option>
		</select>
	</td>
</tr>
</table>
</fieldset>

<div style="clear:both; margin-top:30px;"></div>

<?php if (request::getVar('tmpl') != 'component') : ?>
<button type="button" onclick="Javascript:window.history.back();"><?php echo text::_( _LANG_BACK ); ?></button>
<button type="submit"><?php echo text::_(_LANG_SAVE); ?></button>
<?php endif; ?>

<input type="hidden" name="id" value="<?php echo $this->user->get('id'); ?>" />
<input type="hidden" name="username" value="<?php echo $this->user->get('username');?>" />
<input type="hidden" name="groupid" value="<?php echo $this->user->get('groupid');?>" />
<input type="hidden" name="option" value="com_users" />
<input type="hidden" name="task" value="save_user" />
<input type="hidden" name="layout" value="" />
<?php echo html::_( 'form.token' ); ?>

</form>
